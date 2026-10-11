# Getting started

After [installation](./installation), this example adds a month calendar to an existing `EventsController`. It assumes an `Events` table with `id`, `title`, and a `date` column mapped to a CakePHP date or datetime type.

## Select events by month

Add the behavior in your existing `EventsTable::initialize()` method, keeping its other setup:

```php
$this->addBehavior('Calendar.Calendar', ['field' => 'date']);
```

In `EventsController::initialize()`, after the existing parent call, load the component:

```php
$this->loadComponent('Calendar.Calendar');
```

Add this action to `EventsController`:

```php
public function calendar(?string $year = null, ?string $month = null): void
{
    /** @var \Calendar\Controller\Component\CalendarComponent $calendar */
    $calendar = $this->components()->get('Calendar');
    $calendar->init($year, $month);
    $events = $this->fetchTable('Events')->find(
        'calendar',
        year: $calendar->year(),
        month: $calendar->month(),
    );
    $this->set(compact('events'));
}
```

With your application's standard fallback routes, visit `/events/calendar`. Both missing date parameters select the current month; `/events/calendar/2026/02` selects February 2026. A supplied date must fall within ten years of the current year by default. If you use explicit routes, connect the action and its optional year/month parameters in your application routes.

Keep your application's authentication and authorization checks for this action.

## Render the calendar

Add the helper in your existing `AppView::initialize()` method:

```php
$this->addHelper('Calendar.Calendar');
```

Create `templates/Events/calendar.php`:

```php
<?php
foreach ($events as $event) {
    $content = $this->Html->link($event->title, [
        'action' => 'view',
        $event->id,
    ]);
    $this->Calendar->addRow($event->date, $content, ['class' => 'event']);
}

echo $this->Calendar->render();
```

The links assume an existing `view` action. Replace them with `h($event->title)` if you only need labels.

The helper renders a table and previous/next links. It supplies CSS classes rather than a stylesheet. Add styles to your application's stylesheet, for example:

```css
.calendar { width: 100%; border-collapse: collapse; }
.calendar th, .calendar td { border: 1px solid #ccc; padding: 0.5rem; }
.calendar td { vertical-align: top; }
.calendar ul { padding-left: 1rem; margin: 0.5rem 0; }
```

Query the selected month before calling `addRow()`: it places content by day number and does not filter dates by month or year. Build content with escaping helpers because the helper renders the supplied content as HTML.

See the [calendar reference](../reference/calendar) for multi-day events, date limits, and navigation options.
