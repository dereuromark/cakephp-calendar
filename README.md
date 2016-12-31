# CakePHP Calendar plugin

A plugin to render simple calendars.

## Features
- Simple and robust
- No JS needed, more responsive than solutions like fullcalendar
- Persistent `year/month` URL pieces (copy and paste friendly)

## Setup
```
composer require dereuromark/cakephp-calendar
```
and
```
bin/cake plugin load Calendar
```

## Usage
Load the component in your controller:
```php
$this->loadComponent('Calendar.Calendar');
```

And also your helper in the View class:
```php
$this->loadHelper('Calendar.Calendar');
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
	}
```

In your view:
```php
<?php
	foreach ($calendarItems as $item) {
		$content = $this->Html->link($item['title'], ['action' => 'view', $item['id']]);
		$this->Calendar->addRow($item['date'], $content, ['class' => 'event']);
	}

	echo $this->Calendar->render();
?>

<?php if (!$this->Calendar->isCurrentMonth()) { ?>
    <?php echo $this->Html->link(__('Jump to the current month') . '...', ['action' => 'index'])?>
<?php } ?>
```
