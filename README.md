# CakePHP Calendar plugin

[![Build Status](https://api.travis-ci.org/dereuromark/cakephp-calendar.png?branch=master)](https://travis-ci.org/dereuromark/cakephp-calendar)
[![Latest Stable Version](https://poser.pugx.org/dereuromark/cakephp-calendar/v/stable.svg)](https://packagist.org/packages/dereuromark/cakephp-calendar)
[![Minimum PHP Version](http://img.shields.io/badge/php-%3E%3D%205.5-8892BF.svg)](https://php.net/)
[![License](https://poser.pugx.org/dereuromark/cakephp-calendar/license.png)](https://packagist.org/packages/dereuromark/cakephp-calendar)
[![Total Downloads](https://poser.pugx.org/dereuromark/cakephp-calendar/d/total.png)](https://packagist.org/packages/dereuromark/cakephp-calendar)

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
