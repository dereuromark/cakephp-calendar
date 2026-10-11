<?php

namespace Calendar\Model\Behavior;

use Cake\Core\Configure;
use Cake\I18n\DateTime;
use Cake\ORM\Behavior;
use Cake\ORM\Query\SelectQuery;
use Cake\ORM\Table;
use InvalidArgumentException;

/**
 * A convenience wrapper for calendar querying.
 *
 * Use as find('calendar') after attaching the behavior to a Table class.
 *
 * @author Mark Scherer
 * @license MIT
 */
class CalendarBehavior extends Behavior {

	/**
	 * @var string
	 */
	public const YEAR = 'year';

	/**
	 * @var string
	 */
	public const MONTH = 'month';

	/**
	 * @var array<string, mixed>
	 */
	protected array $_defaultConfig = [
		'field' => 'date',
		'endField' => null,
		'implementedFinders' => [
			'calendar' => 'findCalendar',
		],
		'scope' => [],
	];

	/**
	 * Constructor
	 *
	 * Merges config with the default and store in the config property
	 *
	 * The parent behavior retains the Table reference.
	 *
	 * @param \Cake\ORM\Table $table The table this behavior is attached to.
	 * @param array<string, mixed> $config The config for this behavior.
	 */
	public function __construct(Table $table, array $config = []) {
		$defaults = (array)Configure::read('Calendar');
		parent::__construct($table, $config + $defaults);
	}

	/**
	 * Custom finder for Calendars field.
	 *
	 * @param \Cake\ORM\Query\SelectQuery $query Query.
	 * @param array<string, mixed> $options Calendar year and month (1..12)
	 * @return \Cake\ORM\Query\SelectQuery
	 */
	public function findCalendar(SelectQuery $query, array $options): SelectQuery {
		$year = $options[static::YEAR] ?? null;
		$month = $options[static::MONTH] ?? null;
		if ((!is_int($year) && !is_string($year)) || !ctype_digit((string)$year)
			|| (!is_int($month) && !is_string($month)) || !ctype_digit((string)$month)) {
			throw new InvalidArgumentException('Invalid calendar year or month');
		}
		$year = (int)$year;
		$month = (int)$month;
		$field = $this->getConfig('field');

		if ($year < 1 || $year > 9999 || !checkdate($month, 1, $year)) {
			throw new InvalidArgumentException('Invalid calendar year or month');
		}

		$from = new DateTime(sprintf('%04d-%02d-01', $year, $month));
		$to = $from->addMonths(1);
		if ($to->year > 9999) {
			throw new InvalidArgumentException('Calendar month exceeds the supported date range');
		}

		$conditions = [
			$field . ' >=' => $from,
			$field . ' <' => $to,
		];
		if ($this->getConfig('endField')) {
			$endField = $this->getConfig('endField');

			$conditions = [
				'OR' => [
					[
						$field . ' <' => $to,
						$endField . ' >' => $from,
					],
					$conditions,
				],
			];
		}

		$query->where($conditions);
		if ($this->getConfig('scope')) {
			$query->andWhere($this->getConfig('scope'));
		}

		return $query;
	}

}
