<?php

/**
 * Calendar Example Configuration
 *
 * Merge the keys below into your application's config/app.php (or
 * config/app_local.php) — do not replace the whole file, since this snippet
 * only contains this plugin's configuration. When copying entries that
 * reference imported classes, use fully-qualified class names or move the
 * `use` imports to the top of the target file. Customize the values as needed.
 *
 * The `Calendar` namespace is read by Calendar\Model\Behavior\CalendarBehavior and merged
 * as config defaults for every table using the behavior. Per-table behavior options still
 * override these.
 */
return [
	'Calendar' => [
		// Date(time) column used for the calendar range query (find('calendar')).
		// Default: 'date'.
		'field' => 'date',

		// Optional end-date column for records that span a range. When set, the finder also
		// matches records whose end field falls within the queried month. null = single
		// date column only. Default: null.
		'endField' => null,

		// Additional conditions/scope merged into the calendar query (array of ORM
		// conditions). Default: [] (no extra scope).
		'scope' => [],
	],
];
