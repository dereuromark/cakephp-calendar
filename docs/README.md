# CakePHP Calendar plugin

## Usage
Make sure your calendar items table (e.g. EventsTable) has added the Calendar behavior in `initialize()` method:
```php
// If needed, also provide your config
$this->addBehavior('Calendar.Calendar', [
    'field' => 'beginning',
    'endField' => 'end',
    'scope' => ['invisible' => false],
]);
```
Now the `find('calendar')` custom finder is available on this table class.

Load the component in your controller:
```php
$this->loadComponent('Calendar.Calendar');
```

And also your helper in the View class:
```php
$this->addHelper('Calendar.Calendar');
```

Your action:
```php
    /**
     * @param string|null $year
     * @param string|null $month
     * @return void
     */
    public function calendar($year = null, $month = null) {
        $this->Calendar->init($year, $month);

        // Fetch calendar items (like events, birthdays, ...)
        $options = [
            'year' => $this->Calendar->year(),
            'month' => $this->Calendar->month(),
        ];
        $events = $this->Events->find('calendar', ...$options);

        $this->set(compact('events'));
    }
```

In your index template:
```php
<?php
    foreach ($events as $event) {
        $content = $this->Html->link($event->title, ['action' => 'view', $event->id]);
        $this->Calendar->addRow($event->date, $content, ['class' => 'event']);
    }

    echo $this->Calendar->render();
?>

<?php if (!$this->Calendar->isCurrentMonth()) { ?>
    <?php echo $this->Html->link(__('Jump to the current month') . '...', ['action' => 'index'])?>
<?php } ?>
```

And in your view template you can have a backlink as easy as:
```php
<?php echo $this->Html->link(
    __('List {0}', __('Events')),
    $this->Calendar->calendarUrlArray(['action' => 'index'], $event->date)
); ?>
```

It will redirect back to the current year and month this calendar item has been linked from.
So you have a persistent calendar - even with some clicking around, the user will still be able to navigate very easily through the calendar items.

#### Multi-day events
In case you have a beginning and end for dates, and those can span over multiple days, use:
```php
<?php
    foreach ($events as $event) {
        $content = ...;
        $attr = [...];
        $this->Calendar->addRowFromTo($event->beginning, $event->end, $content, $attr);
    }

    echo $this->Calendar->render();
?>
```

### Configuration

#### Integrity
The component validates both year and month input and throws 404 exception for invalid ones.

The component has a max limit in each direction, defined by init() call:
```php
$this->Calendar->init($year, $month, 5);
```
This will allow the calendar to work 5 years in both directions. Out of bounds are 404 exceptions.
The helper knows not to generate links for over the limit dates.

#### Presentation
You can configure the URL elements to contain the month either as number (default) or text.
```
/controller/action/2017/08
/controller/action/2017/august
```
When loading the helper, pass `'monthAsString' => true` for the textual option.


## ICalendar files (ical/ics)
You can easily render out machine readable calendar events using the [Ical](https://en.wikipedia.org/wiki/ICalendar) format.

In your routes.php file you need to enable `ics` as extension:
```php
Router::extensions([..., 'ics']);
```

Then inside your controller just set a custom view class for this extension:
```php
    use Calendar\View\IcalView;

    /**
     * @return array<string>
     */
    public function viewClasses(): array {
        if (!$this->request->getParam('_ext')) {
            return [];
        }

        return [IcalView::class];
    }
```

Let's say we want to render `/events/view/1.ics` now.
Now it will look inside a subfolder for a PHP file here: `Template/Events/ics/view.php`.
Inside this template just use any Ical library of your choice to output this event:

```php
<?php
/**
 * @var \Calendar\View\IcalView|\App\View\AppView $this !
 * @var \App\Model\Entity\Event $event
 */

$vcalendar = new \Sabre\VObject\Component\VCalendar([
    'VEVENT' => [
        'SUMMARY' => $event->name,
        'DTSTART' => $event->beginning,
        'DTEND' => $event->end,
        'DESCRIPTION' => $event->description,
        'GEO' => $event->lat . ';' . $event->lng,
        'URL' => $event->url,
    ],
]);
echo $vcalendar->serialize();
```
This uses the [sabre-io/vobject](https://github.com/sabre-io/vobject) library (that you need to composer install then).

You could also make your own helper and use that instead:
```php
$calendarEvent = $this->Ical->newEvent();
$calendarEvent->set...();
$this->Ical->addEvent($calendarEvent);

echo $this->Ical->render();
```

I didn't want to hard-link this plugin to a specific renderer. This way you keep complete flexibility here while being able to use the view class as convenience wrapper.

For a larger list of events, you can also look into e.g.
- https://github.com/spatie/icalendar-generator
