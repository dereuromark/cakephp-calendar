# Overview

The plugin provides three parts for month calendars:

- `CalendarBehavior` selects records for a year and month.
- `CalendarComponent` validates the requested date and passes it to the view.
- `CalendarHelper` renders an HTML table with event content and navigation links.

You provide the events table, routes, templates, and CSS. No JavaScript is required to render the calendar. The plugin does not provide an event editor or drag-and-drop scheduling.

For exports, `IcalView` renders templates with a calendar content type. `IcalFormatter` generates basic event payloads. `GoogleCalendarHelper` builds links that let visitors add events to their own calendars.

Start with [installation](./installation), then build a [month calendar](./getting-started).
