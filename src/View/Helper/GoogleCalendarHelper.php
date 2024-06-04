<?php

namespace Calendar\View\Helper;

use Cake\I18n\Date;
use Cake\View\Helper;
use InvalidArgumentException;

/**
 * GoogleCalendar integration
 *
 * @author Mark Scherer
 * @license http://www.opensource.org/licenses/mit-license.php The MIT License
 *
 * @property \Cake\View\Helper\HtmlHelper $Html
 */
class GoogleCalendarHelper extends Helper {

	public array $helpers = ['Html'];

	/**
	 * @var array<string, mixed>
	 */
	protected array $_defaultConfig = [
		'url' => 'https://calendar.google.com/calendar/render',
	];

	/**
	 * Generates a calendar URL for google.
	 *
	 * @see https://github.com/InteractionDesignFoundation/add-event-to-calendar-docs/blob/main/services/google.md
	 *
	 * @param string $title
	 * @param array $dateFromTo
	 * @param array<string, mixed> $details
	 *
	 * @return string HTML code to display calendar in view
	 */
	public function url(string $title, array $dateFromTo, array $details = []): string {
		$url = $this->getConfig('url');
		$url .= '?action=TEMPLATE';

		$query = [];
		$query[] = 'text=' . urlencode($title);

		$dates = [];
		if (!empty($dateFromTo['from'])) {
			/** @var \Cake\I18n\DateTime|\Cake\I18n\Date|string $from */
			$from = $dateFromTo['from'];
			if ($from instanceof Date) {
				$from = $from->year . $from->month . $from->day;
			} elseif (!is_string($from)) {
				$from = $from->toIso8601String();
				$from = str_replace('-', '', $from);
			}
			$dates[] = $from;
		}
		if (!empty($dateFromTo['to'])) {
			/** @var \Cake\I18n\DateTime|\Cake\I18n\Date|string $to */
			$to = $dateFromTo['to'];
			if ($to instanceof Date) {
				$to = $to->year . $to->month . $to->day;
			} elseif (!is_string($to)) {
				$to = $to->toIso8601String();
				$to = str_replace('-', '', $to);
			}
			$dates[] = $to;
		} elseif (!empty($dateFromTo['from']) && $dateFromTo['from'] instanceof Date) {
			$to = $dateFromTo['from']->addDays(1);
			$dates[] = $to->year . $to->month . $to->day;

		}

		if (!$dates) {
			throw new InvalidArgumentException('Missing required input for date (from)');
		}
		$query[] = 'dates=' . urlencode(implode('/', $dates));

		if (!empty($details['details'])) {
			$query[] = 'details=' . urlencode($details['details']);
		}
		if (!empty($details['location'])) {
			$query[] = 'location=' . urlencode($details['location']);
		}
		if (!empty($details['ctz'])) {
			$query[] = 'ctz=' . urlencode($details['ctz']);
		}

		//TODO: sprop etc

		$url .= '&' . implode('&', $query);

		return $url;
	}

	/**
	 * Generates a calendar link for google.
	 *
	 * @param string $title
	 * @param array $dateFromTo
	 * @param array<string, mixed> $details
	 *
	 * @return string HTML code to display calendar in view
	 */
	public function link(string $title, array $dateFromTo, array $details = []): string {
		return $this->Html->link($title, $this->url($title, $dateFromTo, $details));
	}

}
