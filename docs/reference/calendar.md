# Calendar reference

## Behavior

Attach `Calendar.Calendar` to the table containing events:

```php
$this->addBehavior('Calendar.Calendar', [
    'field' => 'beginning',
    'endField' => 'end',
    'scope' => ['invisible' => false],
]);
```

`field` defaults to `date`. `endField` defaults to `null`; `scope` defaults to an empty array. Global `Configure::read('Calendar')` values supply behavior defaults, and explicit behavior settings take precedence.

Use named finder arguments with integers. Convert request strings to integers first, or use the validated values from the component:

```php
$events = $this->fetchTable('Events')->find('calendar', year: 2026, month: 2);
```

Without an end field, the finder selects starts at or after the first instant of the month and before the first instant of the next month. With an end field, it also selects events starting before the next month whose end is later than the selected month's start. Events that end exactly at the month's start do not overlap it. Invalid month/year combinations throw `InvalidArgumentException`. Years must fit four digits; December 9999 is rejected because its exclusive next-month boundary exceeds that range.

The finder adds conditions and returns a query; it does not sort, paginate, or enforce access permissions. Chain your application's filters and ordering as needed. Date boundaries use the application's default timezone; configure database date handling consistently.

## Component

```php
/** @var \Calendar\Controller\Component\CalendarComponent $calendar */
$calendar = $this->components()->get('Calendar');
$calendar->init($year, $month, 5, ['url' => ['action' => 'calendar']]);
```

Both missing date values select the current month. Otherwise, supply an integer year and a month number from 1 to 12, or an English month name such as `february`. Zero-padded strings such as `02` work. Invalid input throws `NotFoundException`.

The third argument limits years around the current year, inclusively. Its default is ten. The example permits five years in each direction. The fourth argument supplies view options; `url` can override the navigation route. Avoid overriding the generated `year`, `month`, or `span` values.

Call `year()` and `month()` after initialization to read integers. `monthAsString()` returns the zero-padded numeric month, such as `02`; it does not return a month name. `months()` and `days()` return the component's English label lists.

## Helper

```php
$this->addHelper('Calendar.Calendar', [
    'monthAsString' => true,
    'multiLabelSuffix' => ' (Day {0})',
    'timezone' => 'Europe/Berlin',
]);
```

`monthAsString` changes month URL segments from numbers to translated month names. The component accepts English names only, so keep month translations in the `calendar` domain in English when enabling this option. It defaults to `false`. Headings and weekday labels use the current locale. The locale also determines the first weekday and weekend markings. The `timezone` setting controls the helper's current-date highlighting; it does not convert event dates or query boundaries.

`addRow($date, $content, $options)` places HTML content on that day number. Pass only records from the selected month. The helper accepts CakePHP `Date`, `DateTime`, or Chronos values; raw date strings must first be converted. Escape user content with `h()` or use HTML helpers.

For multi-day events:

```php
foreach ($events as $event) {
    $content = $this->Html->link($event->title, ['action' => 'view', $event->id]);
    $this->Calendar->addRowFromTo($event->beginning, $event->end, $content);
}
echo $this->Calendar->render();
```

Use matching endpoint types, such as two CakePHP `DateTime` values or two `Date` values. This method includes both endpoint dates when their times permit, and filters rendered days to the selected year and month. The day suffix counts from the event's original start, including days outside the selected month. Its date loop runs across the supplied range, so avoid feeding unnecessarily long ranges. This display method's endpoint handling differs from the finder's strict overlap boundary and iCalendar's exclusive end date.

`render()` needs the component's `_calendar` view variable. It renders a table with previous/next links, hides navigation beyond the year limit, and adds event list items. Add your own CSS for `.calendar`, `.cell-today`, `.cell-weekend`, and event classes.

## Links back to a calendar

```php
echo $this->Html->link(
    'Back to calendar',
    $this->Calendar->calendarUrlArray(['action' => 'calendar'], $event->date),
);
```

The method derives the year/month from the supplied event date. It omits them for the current month. It does not remember which calendar page the visitor came from; preserve that context separately if an event spans several months.

## Finder API on the cake6 branch

The finder accepts explicit year and month arguments:

```php
public function findCalendar(SelectQuery $query, int $year, int $month): SelectQuery
```

Use `$table->find('calendar', year: 2026, month: 12)` or call the behavior with a query and two integers. Named finder calls require integer values; strings raise `TypeError`, and missing arguments raise `ArgumentCountError`. Update custom finder overrides to match this declaration. The CakePHP 5 branch retains the released options-array declaration.

This branch prepares the breaking finder contract for the CakePHP 6 upgrade. The framework dependency and CI still use CakePHP 5 until the framework migration is completed.
