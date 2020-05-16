<?php

namespace Calendar\View\Helper;

use Cake\Chronos\ChronosInterface;
use Cake\View\Helper;
use RuntimeException;

/**
 * Calendar Helper
 *
 * Copyright 2007-2008 John Elliott
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @author John Elliott
 * @author Mark Scherer
 * @copyright 2008 John Elliott
 * @link http://www.flipflops.org More Information
 * @license http://www.opensource.org/licenses/mit-license.php The MIT License
 *
 * @property \Cake\View\Helper\HtmlHelper $Html
 */
class CalendarHelper extends Helper {

	/**
	 * @var array
	 */
	public $helpers = ['Html'];

	/**
	 * @var array
	 */
	protected $monthList = ['january', 'february', 'march', 'april', 'may', 'june', 'july', 'august', 'september', 'october', 'november', 'december'];

	/**
	 * @var array
	 */
	protected $dayList = ['mon', 'tue', 'wed', 'thu', 'fri', 'sat', 'sun'];

	/**
	 * @var array
	 */
	protected $_defaultConfig = [
		'monthAsString' => false,
		'multiLabelSuffix' => ' (Day {0})',
	];

	/**
	 * Containing all rows
	 *
	 * @var array
	 */
	public $dataContainer = [];

	/**
	 * @return void
	 */
	public function init() {
		$this->dataContainer = [];
	}

	/**
	 * @param \Cake\Chronos\ChronosInterface $date
	 * @param string $content
	 * @param array $options
	 * @return void
	 */
	public function addRow(ChronosInterface $date, $content, $options = []) {
		if (!$content) {
			return;
		}
		$day = $this->retrieveDayFromDate($date);
		$this->dataContainer[$day][] = $this->Html->tag('li', $content, $options);
	}

	/**
	 * @param \Cake\I18n\Time|\Cake\I18n\FrozenTime $from
	 * @param \Cake\I18n\Time|\Cake\I18n\FrozenTime $to
	 * @param string $content
	 * @param array $options
	 * @return void
	 */
	public function addRowFromTo(ChronosInterface $from, ChronosInterface $to, $content, $options = []) {
		if (!$content) {
			return;
		}

		$from = clone $from;
		$from = $from->setTime(0, 0, 0);
		$month = $this->_View->get('_calendar')['month'];

		$days = [
		];
		$count = 0;
		while ($from <= $to) {
			if ($from->month === $month) {
				$days[$count] = $this->retrieveDayFromDate($from);
			}
			$from = $from->addDay();
			$count++;
		}

		$suffix = '';
		if ($count > 1) {
			$suffix = $this->getConfig('multiLabelSuffix');
		}
		foreach ($days as $i => $day) {
			$suffixTranslated = __($suffix, $i + 1);
			$this->dataContainer[$day][] = $this->Html->tag('li', $content . $suffixTranslated, $options);
		}
	}

	/**
	 * Generates a calendar for the specified by the month and year params and populates
	 * it with the content of the data container array
	 *
	 * @return string HTML code to display calendar in view
	 */
	public function render() {
		$str = '';

		$day = 1;
		$today = 0;

		if (empty($this->_View->get('_calendar'))) {
			throw new RuntimeException('You need to load Calendar.Calendar component for this helper to work.');
		}

		$year = $this->_View->get('_calendar')['year'];
		$month = $this->_View->get('_calendar')['month'];

		$data = $this->dataContainer;

		$currentYear = (int)date('Y');
		$currentMonth = (int)date('n');
		if ($year === $currentYear && $month === $currentMonth) {
			$today = (int)date('j');
		}

		$daysInMonth = date('t', mktime(0, 0, 0, $month, 1, $year));

		$firstDayInMonth = date('D', mktime(0, 0, 0, $month, 1, $year));
		$firstDayInMonth = strtolower($firstDayInMonth);

		$str .= '<table class="calendar">';

		$str .= '<thead>';

		$str .= '<tr><th class="cell-prev">';

		$str .= $this->previousLink();

		$str .= '</th><th colspan="5" class="cell-month">' . __(ucfirst($this->monthName($month))) . ' ' . $year . '</th><th class="cell-next">';

		$str .= $this->nextLink();

		$str .= '</th></tr>';

		$str .= '<tr>';

		for ($i = 0; $i < 7;$i++) {
				$str .= '<th class="cell-header">' . __(ucfirst($this->dayList[$i])) . '</th>'; //TODO: i18n!
		}

		$str .= '</tr>';

		$str .= '</thead>';

		$str .= '<tbody>';

		while ($day <= $daysInMonth) {
			$str .= '<tr>';

			for ($i = 0; $i < 7; $i++) {
				$cell = '&nbsp;';

				if (isset($data[$day])) {
					$cell = '<ul>' . implode(PHP_EOL, $data[$day]) . '</ul>';
				}

				$class = '';

				if ($i > 4) {
					$class = ' class="cell-weekend" ';
				}
				if ($day === $today && ($firstDayInMonth == $this->dayList[$i] || $day > 1) && ($day <= $daysInMonth)) {
					$class = ' class="cell-today" ';
				}

				if (($firstDayInMonth == $this->dayList[$i] || $day > 1) && ($day <= $daysInMonth)) {
					$str .= '<td ' . $class . '><div class="cell-number">' . $day . '</div><div class="cell-data">' . $cell . '</div></td>';
					$day++;
				} else {
					$str .= '<td class="cell-disabled">&nbsp;</td>';
				}
			}
			$str .= '</tr>';
		}

		$str .= '</tbody>';

		$str .= '</table>';

		return $str;
	}

	/**
	 * @param \Cake\Chronos\ChronosInterface $date
	 * @return int
	 */
	public function retrieveDayFromDate(ChronosInterface $date) {
		return (int)$date->format('d');
	}

	/**
	 * @param \Cake\Chronos\ChronosInterface $date
	 * @return int
	 */
	public function retrieveMonthFromDate(ChronosInterface $date) {
		return (int)$date->format('n');
	}

	/**
	 * @param \Cake\Chronos\ChronosInterface $date
	 * @return int
	 */
	public function retrieveYearFromDate(ChronosInterface $date) {
		return (int)$date->format('Y');
	}

	/**
	 * Generates a link back to the calendar from any view page.
	 *
	 * Specify action and if necessary controller, plugin, and prefix.
	 *
	 * @param array $url
	 * @param \Cake\Chronos\ChronosInterface $dateTime
	 * @return array
	 */
	public function calendarUrlArray(array $url, ChronosInterface $dateTime) {
		$year = $this->retrieveYearFromDate($dateTime);
		$month = $this->retrieveMonthFromDate($dateTime);

		$currentYear = (int)date('Y');
		$currentMonth = (int)date('n');

		if ($year === (int)$currentYear && $month === (int)$currentMonth) {
			return $url;
		}

		$url[] = $year;
		$url[] = $this->formatMonth($month);

		return $url;
	}

	/**
	 * @return string
	 */
	public function previousLink() {
		$year = $this->_View->get('_calendar')['year'];
		$month = $this->_View->get('_calendar')['month'];

		$currentYear = (int)date('Y');
		$currentMonth = (int)date('n');

		$flag = 0;
		if (!$year || !$month) { // just use current year & month
			$year = $currentYear;
			$month = $currentMonth;
		}
		if ($month > 0 && $month < 13) {
			$flag = 1;
			//$monthNum = $i + 1;
		}
		if ($flag === 0) {
			$year = $currentYear;
			$month = $currentMonth;
		}

		$prevYear = $year;
		$prevMonth = $month - 1;

		if ($prevMonth === 0) {
			$prevMonth = 12;
			$prevYear = $year - 1;
		}

		$span = $this->_View->get('_calendar')['span'];
		if ($prevYear < $currentYear - $span) {
			return '';
		}

		if ($prevYear === (int)$currentYear && $prevMonth === (int)$currentMonth) {
			$prevMonth = $prevYear = null;
		}

		return $this->Html->link(__('previous'), [$prevYear, $this->formatMonth($prevMonth)]);
	}

	/**
	 * @return string
	 */
	public function nextLink() {
		$year = $this->_View->get('_calendar')['year'];
		$month = $this->_View->get('_calendar')['month'];

		$currentYear = (int)date('Y');
		$currentMonth = (int)date('n');

		$flag = 0;
		if (!$year || !$month) { // just use current year & month
			$year = $currentYear;
			$month = $currentMonth;
		}
		if ($month > 0 && $month < 13 && (int)$year != 0) {
			$flag = 1;
			//$monthNum = $i + 1;
		}
		if ($flag === 0) {
			$year = $currentYear;
			$month = $currentMonth;
		}

		$nextYear = $year;
		$nextMonth = $month + 1;

		if ($nextMonth === 13) {
			$nextMonth = 1;
			$nextYear = $year + 1;
		}

		$span = $this->_View->get('_calendar')['span'];
		if ($nextYear > $currentYear + $span) {
			return '';
		}

		if ($nextYear === $currentYear && $nextMonth === $currentMonth) {
			$nextMonth = $nextYear = null;
		}

		return $this->Html->link(__('next'), [$nextYear, $this->formatMonth($nextMonth)]);
	}

	/**
	 * @return bool
	 */
	public function isCurrentMonth() {
		$year = $this->_View->get('_calendar')['year'];
		$month = $this->_View->get('_calendar')['month'];

		return $year === (int)date('Y') && $month === (int)date('n');
	}

	/**
	 * @param int $month
	 * @return string|null
	 */
	public function formatMonth($month) {
		if (!$month) {
			return null;
		}

		if ($this->getConfig('monthAsString')) {
			return $this->monthName($month);
		}

		return str_pad((string)$month, 2, '0', STR_PAD_LEFT);
	}

	/**
	 * @param int $month
	 * @return string|null
	 */
	public function monthName($month) {
		if (!isset($this->monthList[$month - 1])) {
			return '';
		}
		return __(ucfirst($this->monthList[$month - 1]));
	}

}
