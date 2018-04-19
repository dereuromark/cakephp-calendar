# CakePHP Calendar plugin

[![Build Status](https://api.travis-ci.org/dereuromark/cakephp-calendar.png?branch=master)](https://travis-ci.org/dereuromark/cakephp-calendar)
[![Latest Stable Version](https://poser.pugx.org/dereuromark/cakephp-calendar/v/stable.svg)](https://packagist.org/packages/dereuromark/cakephp-calendar)
[![Minimum PHP Version](http://img.shields.io/badge/php-%3E%3D%205.6-8892BF.svg)](https://php.net/)
[![License](https://poser.pugx.org/dereuromark/cakephp-calendar/license.png)](https://packagist.org/packages/dereuromark/cakephp-calendar)
[![Total Downloads](https://poser.pugx.org/dereuromark/cakephp-calendar/d/total.png)](https://packagist.org/packages/dereuromark/cakephp-calendar)
[![Coding Standards](https://img.shields.io/badge/cs-PSR--2--R-yellow.svg)](https://github.com/php-fig-rectified/fig-rectified-standards)

A plugin to render simple calendars.

This branch is for CakePHP 3.5+.

## Features
- Simple and robust
- No JS needed, more responsive than solutions like fullcalendar
- Persistent `year/month` URL pieces (copy-paste and link/redirect friendly)

## Demo
See the demo [Calendar example](http://sandbox.dereuromark.de/sandbox/calendar) at the sandbox.

## Setup
```
composer require dereuromark/cakephp-calendar
```
Then make sure the plugin is loaded in bootstrap:
```
bin/cake plugin load Calendar
```
You can also just manually put this in:
```
Plugin::load('Calendar');
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
		$options = [
			'year' => $this->Calendar->year(),
			'month' => $this->Calendar->month(),
		];
		$events = $this->Events->find('calendar', $options);
		
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

