<?php
declare(strict_types=1);

namespace Calendar\Utility;

use DateTimeImmutable;
use DateTimeInterface;
use DateTimeZone;
use InvalidArgumentException;

/**
 * Formats event arrays into RFC 5545 (`text/calendar`) output suitable
 * for direct emission from an `IcalView`-rendered controller action.
 *
 * Handles the easy-to-get-wrong parts of the spec — value escaping per
 * §3.3.11, line folding at 75 octets per §3.1, UID generation, DTSTAMP,
 * and the all-day vs timed event distinction.
 *
 * Typical usage from inside `templates/ics/<action>.php`:
 *
 * ```php
 * use Calendar\Utility\IcalFormatter;
 *
 * echo IcalFormatter::formatCalendar($events, ['prodid' => '-//Acme//Events 1.0//EN']);
 * ```
 *
 * Each event in `$events` is an associative array with at least `start`.
 * Recognized keys: `uid`, `summary`, `description`, `location`, `url`,
 * `start`, `end`, `allDay`, `created`, `modified`, `categories`.
 */
class IcalFormatter {

	/**
	 * @var string
	 */
	protected const CRLF = "\r\n";

	/**
	 * Maximum content-line length in octets per RFC 5545 §3.1 before
	 * folding (`75 octets, excluding the line break`).
	 *
	 * @var int
	 */
	protected const FOLD_LIMIT = 75;

	/**
	 * Build a complete VCALENDAR document wrapping one or more events.
	 *
	 * @param array<int, array<string, mixed>> $events
	 * @param array<string, mixed> $options Supported keys:
	 *   - `prodid` (string) PRODID property value (default `-//cakephp-calendar//Events//EN`).
	 *   - `method` (string|null) Optional METHOD (e.g. `PUBLISH`, `REQUEST`).
	 *   - `calscale` (string) CALSCALE property (default `GREGORIAN`).
	 *
	 * @return string The full `.ics` payload terminated with CRLF.
	 */
	public static function formatCalendar(array $events, array $options = []): string {
		$options += [
			'prodid' => '-//cakephp-calendar//Events//EN',
			'method' => null,
			'calscale' => 'GREGORIAN',
		];

		$lines = [
			'BEGIN:VCALENDAR',
			'VERSION:2.0',
			'PRODID:' . static::escape((string)$options['prodid']),
			'CALSCALE:' . (string)$options['calscale'],
		];
		if ($options['method'] !== null) {
			$lines[] = 'METHOD:' . (string)$options['method'];
		}

		foreach ($events as $event) {
			$lines[] = static::formatEvent($event);
		}

		$lines[] = 'END:VCALENDAR';

		return implode(static::CRLF, $lines) . static::CRLF;
	}

	/**
	 * Build a single VEVENT block.
	 *
	 * Required key: `start`. Everything else is optional. `uid` is auto-
	 * generated from a hash of the event payload when absent so that
	 * re-emitting the same data produces the same UID (idempotent feeds).
	 *
	 * @param array<string, mixed> $event
	 *
	 * @return string Folded, CRLF-joined block of VEVENT lines.
	 */
	public static function formatEvent(array $event): string {
		if (!isset($event['start'])) {
			throw new InvalidArgumentException('Event "start" is required.');
		}

		$allDay = !empty($event['allDay']);
		$start = static::toDateTime($event['start']);
		$end = isset($event['end']) ? static::toDateTime($event['end']) : null;

		$uid = isset($event['uid']) && $event['uid'] !== ''
			? (string)$event['uid']
			: static::generateUid($event);

		$lines = ['BEGIN:VEVENT'];
		$lines[] = 'UID:' . static::escape($uid);
		$lines[] = 'DTSTAMP:' . static::formatDateTime(new DateTimeImmutable('now', new DateTimeZone('UTC')));
		$lines[] = static::formatDateTimeProperty('DTSTART', $start, $allDay);

		if ($end !== null) {
			$lines[] = static::formatDateTimeProperty('DTEND', $end, $allDay);
		}
		if (isset($event['created'])) {
			$lines[] = 'CREATED:' . static::formatDateTime(static::toDateTime($event['created']));
		}
		if (isset($event['modified'])) {
			$lines[] = 'LAST-MODIFIED:' . static::formatDateTime(static::toDateTime($event['modified']));
		}
		if (isset($event['summary']) && $event['summary'] !== '') {
			$lines[] = 'SUMMARY:' . static::escape((string)$event['summary']);
		}
		if (isset($event['description']) && $event['description'] !== '') {
			$lines[] = 'DESCRIPTION:' . static::escape((string)$event['description']);
		}
		if (isset($event['location']) && $event['location'] !== '') {
			$lines[] = 'LOCATION:' . static::escape((string)$event['location']);
		}
		if (isset($event['url']) && $event['url'] !== '') {
			$lines[] = 'URL:' . (string)$event['url'];
		}
		if (isset($event['categories']) && $event['categories'] !== []) {
			$categories = is_array($event['categories']) ? $event['categories'] : [$event['categories']];
			$lines[] = 'CATEGORIES:' . implode(',', array_map(
				static fn (mixed $c): string => static::escape((string)$c),
				$categories,
			));
		}

		$lines[] = 'END:VEVENT';

		return implode(static::CRLF, array_map(static::foldLine(...), $lines));
	}

	/**
	 * Escape a TEXT value per RFC 5545 §3.3.11. Backslashes, semicolons,
	 * and commas must be backslash-escaped; literal newlines become the
	 * two-character escape `\n`.
	 *
	 * @param string $value
	 *
	 * @return string
	 */
	public static function escape(string $value): string {
		return strtr($value, [
			'\\' => '\\\\',
			';' => '\\;',
			',' => '\\,',
			"\r\n" => '\\n',
			"\n" => '\\n',
			"\r" => '\\n',
		]);
	}

	/**
	 * Fold a content line at {@see self::FOLD_LIMIT} octets per RFC 5545
	 * §3.1. Continuation lines are prefixed with a single space.
	 *
	 * @param string $line
	 *
	 * @return string
	 */
	public static function foldLine(string $line): string {
		if (strlen($line) <= static::FOLD_LIMIT) {
			return $line;
		}

		$folded = '';
		$offset = 0;
		$length = strlen($line);
		while ($offset < $length) {
			$chunk = substr($line, $offset, $offset === 0 ? static::FOLD_LIMIT : static::FOLD_LIMIT - 1);
			$folded .= ($offset === 0 ? '' : static::CRLF . ' ') . $chunk;
			$offset += strlen($chunk);
		}

		return $folded;
	}

	/**
	 * Format a DateTime for a generic time-stamp property (UTC,
	 * `YYYYMMDDTHHMMSSZ`).
	 *
	 * @param \DateTimeInterface $dt
	 *
	 * @return string
	 */
	public static function formatDateTime(DateTimeInterface $dt): string {
		return DateTimeImmutable::createFromInterface($dt)
			->setTimezone(new DateTimeZone('UTC'))
			->format('Ymd\\THis\\Z');
	}

	/**
	 * Emit a DTSTART / DTEND / EXDATE-style property with the right
	 * value-type parameter:
	 *  - `VALUE=DATE` for all-day events (no time component, no Z),
	 *  - UTC instant with `Z` suffix for timed events.
	 *
	 * @param string $name
	 * @param \DateTimeInterface $dt
	 * @param bool $allDay
	 *
	 * @return string
	 */
	protected static function formatDateTimeProperty(string $name, DateTimeInterface $dt, bool $allDay): string {
		if ($allDay) {
			return $name . ';VALUE=DATE:' . $dt->format('Ymd');
		}

		return $name . ':' . static::formatDateTime($dt);
	}

	/**
	 * Coerce a value to DateTimeImmutable. Accepts DateTimeInterface,
	 * timestamps (int), and parseable date strings.
	 *
	 * @param mixed $value
	 *
	 * @return \DateTimeImmutable
	 */
	protected static function toDateTime(mixed $value): DateTimeImmutable {
		if ($value instanceof DateTimeImmutable) {
			return $value;
		}
		if ($value instanceof DateTimeInterface) {
			return DateTimeImmutable::createFromInterface($value);
		}
		if (is_int($value)) {
			return (new DateTimeImmutable('@' . $value))->setTimezone(new DateTimeZone(date_default_timezone_get()));
		}
		if (is_string($value) && $value !== '') {
			return new DateTimeImmutable($value);
		}

		throw new InvalidArgumentException('Cannot coerce value to DateTime: ' . get_debug_type($value));
	}

	/**
	 * Generate a stable UID from the event payload so re-emitting the
	 * same data yields the same UID (clients then update instead of
	 * duplicating). Callers wanting a fresh UID per emission should
	 * supply `uid` themselves.
	 *
	 * @param array<string, mixed> $event
	 *
	 * @return string
	 */
	protected static function generateUid(array $event): string {
		$stable = [
			'start' => isset($event['start']) ? (string)$event['start'] : '',
			'summary' => (string)($event['summary'] ?? ''),
			'location' => (string)($event['location'] ?? ''),
		];

		return substr(sha1((string)json_encode($stable)), 0, 32) . '@cakephp-calendar';
	}

}
