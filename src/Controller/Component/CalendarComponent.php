<?php

namespace Calendar\Controller\Component;

use Cake\Controller\Component;
use Cake\Http\Exception\NotFoundException;
use RuntimeException;

/**
 * Calendar Component
 *
 * inspired by http://www.flipflops.org/2007/09/21/a-simple-php-calendar-function/
 *
 * @author Mark Scherer
 * @copyright 2012 Mark Scherer
 * @license http://opensource.org/licenses/mit-license.php MIT
 */
class CalendarComponent extends Component {

	/**
	 * @var array<string>
	 */
	public array $monthList = [
		'january', 'february', 'march', 'april', 'may', 'june', 'july', 'august', 'september', 'october',
		'november', 'december',
	];

	/**
	 * @var array<string>
	 */
	public array $dayList = [
		'mon', 'tue', 'wed', 'thu', 'fri', 'sat', 'sun',
	];

	public ?int $year = null;

	public ?int $month = null;

	public ?int $day = null;

	/**
	 * @param string|int|null $year Year
	 * @param string|int|null $month Month
	 * @param int $span Years in both directions
	 * @param array<string, mixed> $options To be passed to previous and next methods
	 * @throws \Cake\Http\Exception\NotFoundException
	 * @return void
	 */
	public function init($year, $month, int $span = 10, array $options = []): void {
		if (($year === null || $year === '') && ($month === null || $month === '')) {
			$year = (int)date('Y');
			$month = (int)date('n');
		} else {
			if ((!is_int($year) && !is_string($year)) || !preg_match('/^[0-9]+$/D', (string)$year)) {
				throw new NotFoundException('Invalid date');
			}
			if (is_string($month) && !ctype_digit($month)) {
				$month = $this->retrieveMonth($month);
			} elseif (!is_int($month) && !is_string($month)) {
				throw new NotFoundException('Invalid date');
			}
			$year = (int)$year;
			$month = (int)$month;
		}

		$current = (int)date('Y');

		if (!$month || $year < $current - $span || $year > $current + $span) {
			throw new NotFoundException('Invalid date');
		}

		$this->year = $year;
		$this->month = $month;

		if ($month < 1 || $month > 12) {
			throw new NotFoundException('Invalid date');
		}

		$viewVars = $options + [
			'year' => $this->year,
			'month' => $this->month,
			'span' => $span,
		];

		$this->getController()->set('_calendar', $viewVars);
	}

	/**
	 * @return int
	 */
	public function year(): int {
		if (!$this->year) {
			throw new RuntimeException('Make sure to first call init().');
		}

		return $this->year;
	}

	/**
	 * @return int
	 */
	public function month(): int {
		if (!$this->month) {
			throw new RuntimeException('Make sure to first call init().');
		}

		return $this->month;
	}

	/**
	 * @return string
	 */
	public function monthAsString(): string {
		if (!$this->month) {
			throw new RuntimeException('Make sure to first call init().');
		}

		return $this->asString($this->month);
	}

	/**
	 * Month as integer value 1..12 or 0 on error
	 * february => 2
	 *
	 * @param string $month
	 * @return int
	 */
	public function retrieveMonth($month) {
		if (!$month) {
			return 0;
		}
		$month = mb_strtolower($month);
		if (in_array($month, $this->monthList, true)) {
			$keys = array_keys($this->monthList, $month);

			return $keys[0] + 1;
		}

		return 0;
	}

	/**
	 * Day as integer value 1..31 or 0 on error
	 * february => 2
	 *
	 * @param string $day
	 * @param int|null $month
	 * @return int
	 */
	public function retrieveDay($day, $month = null) {
		$day = (int)$day;
		if ($day < 1 || $day > 31) {
			return 0;
		}

		// TODO check on month days!

		return $day;
	}

	/**
	 * @return array
	 */
	public function months(): array {
		return $this->monthList;
	}

	/**
	 * @return array
	 */
	public function days(): array {
		return $this->dayList;
	}

	/**
	 * Converts integer to x-digit string
	 * 1 => 01, 12 => 12
	 *
	 * @param int $number
	 * @param int $digits
	 * @return string
	 */
	protected function asString($number, $digits = 2) {
		$number = (string)$number;
		$count = mb_strlen($number);
		while ($count < $digits) {
			$number = '0' . $number;
			$count++;
		}

		return $number;
	}

}
