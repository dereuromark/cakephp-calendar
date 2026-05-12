<?php
/**
 * Example IcalView template — demonstrates the formatter contract.
 *
 * The controller is expected to set a view variable `$events` (an
 * array of associative arrays). The formatter handles UID generation,
 * RFC 5545 escaping, line folding, and CRLF.
 *
 * @var \Calendar\View\IcalView $this
 * @var array<int, array<string, mixed>> $events
 */

use Calendar\Utility\IcalFormatter;

echo IcalFormatter::formatCalendar($events ?? [], [
	'prodid' => '-//cakephp-calendar//Test Events//EN',
]);
