# iCalendar exports

`IcalView` sets the `text/calendar` content type, disables the application layout, and looks for templates in an `ics` subdirectory. It does not generate events itself.

## Enable an export route

In your existing application route scope, enable the extension before connecting routes or calling `fallbacks()`:

```php
$routes->setExtensions(['ics']);
```

If you already support other extensions, include them in the array. With standard fallback routes, an action such as `/events/view/1.ics` then receives `_ext = ics`.

In the action, select the export view for that extension:

```php
if ($this->request->getParam('_ext') === 'ics') {
    $this->viewBuilder()->setClassName(\Calendar\View\IcalView::class);
}
```

Keep the action's existing record lookup and access checks. Create `templates/Events/ics/view.php` alongside your normal HTML template:

```php
<?php
use Calendar\Utility\IcalFormatter;

echo IcalFormatter::formatCalendar([
    [
        'uid' => 'event-' . $event->id . '@events.example.com',
        'summary' => $event->title,
        'start' => $event->date instanceof \Cake\I18n\Date
            ? $event->date->format('Y-m-d')
            : $event->date,
        'allDay' => $event->date instanceof \Cake\I18n\Date,
    ],
]);
```

Use your own domain in the UID. A persistent UID tied to the record lets calendar clients identify the same event after its title or date changes. The generated fallback UID hashes the normalized start, all-day flag, summary, and location, so it changes when those values change. Its date normalization corrects the older locale-dependent algorithm; existing feeds using fallback UIDs will receive new identifiers after this change. Supply persistent UIDs to avoid this dependency on formatter internals.

## Formatter inputs

`formatCalendar($events, $options)` wraps an array of event arrays in a calendar document. Each event requires `start`; supported optional keys are `uid`, `summary`, `description`, `location`, `url`, `end`, `allDay`, `created`, `modified`, and `categories`.

Dates accept `DateTimeInterface` objects, integer Unix timestamps, or parseable nonempty date strings. CakePHP `DateTime` is supported. Convert CakePHP date-only objects to a `Y-m-d` string for the formatter. Use explicit timezones for timed events because timezone-free strings use the PHP default timezone.

```php
$payload = [
    [
        'uid' => 'holiday-42@events.example.com',
        'summary' => 'Office closed',
        'start' => '2026-12-24',
        'end' => '2026-12-26',
        'allDay' => true,
        'categories' => ['Holiday'],
    ],
];
echo \Calendar\Utility\IcalFormatter::formatCalendar($payload, [
    'prodid' => '-//Example App//Events//EN',
]);
```

The all-day example covers December 24 and 25: iCalendar `DTEND` is exclusive, as defined in [RFC 5545](https://www.rfc-editor.org/rfc/rfc5545#section-3.6.1). Timed values are emitted in UTC with a `Z` suffix; all-day values use `VALUE=DATE`. The formatter escapes text, folds content lines at 75 bytes, and uses CRLF line endings. Pass only trusted values for protocol options such as `method` and `calscale`, and valid event URLs.

Options are `prodid` (default `-//cakephp-calendar//Events//EN`), `method` (default `null`, omitted), and `calscale` (default `GREGORIAN`). The formatter handles basic events rather than recurrence rules, attendees, attachments, or timezone definitions. Use another iCalendar library when those are required; its output can still be rendered by `IcalView`.
