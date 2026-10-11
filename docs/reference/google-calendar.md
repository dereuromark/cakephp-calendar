# Google Calendar links

Load the helper in your view initialization:

```php
$this->addHelper('Calendar.GoogleCalendar');
```

Create a link in a template:

```php
echo $this->GoogleCalendar->link('Team meeting', [
    'from' => $event->beginning,
    'to' => $event->end,
], [
    'details' => $event->description,
    'location' => $event->location,
]);
```

The `from` value is required. CakePHP datetime values are converted to UTC. Date-only values use `Ymd`; when `from` is a CakePHP `Date` and `to` is omitted, the helper supplies the next day for a one-day event. Supply an explicit end for timed events. Strings are passed through without date conversion, so format them as `YYYYMMDD` or `YYYYMMDDTHHMMSSZ` before calling the helper.

`url()` takes the same arguments and returns a URL instead of an HTML link. Optional details are `details`, `location`, and `ctz` (calendar timezone). The helper's `url` configuration defaults to `https://calendar.google.com/calendar/render`.

The link opens Google Calendar's event form. It does not save an event automatically, synchronize changes, or provide a subscription feed.
