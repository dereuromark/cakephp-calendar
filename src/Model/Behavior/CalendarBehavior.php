<?php

namespace Calendar\Model\Behavior;

use Cake\Core\Configure;
use Cake\I18n\DateTime;
use Cake\ORM\Behavior;
use Cake\ORM\Query;
use Cake\ORM\Table;

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

	protected ?Table $_table;

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
	 * Does not retain a reference to the Table object. If you need this
	 * you should override the constructor.
	 *
	 * @param \Cake\ORM\Table $table The table this behavior is attached to.
	 * @param array $config The config for this behavior.
	 */
	public function __construct(Table $table, array $config = []) {
		$defaults = (array)Configure::read('Calendar');
		parent::__construct($table, $config + $defaults);

		$this->_table = $table;
	}

	/**
	 * Custom finder for Calendars field.
	 *
	 * Options:
	 * - year (required), best to use CalendarBehavior::YEAR constant
	 * - month (required), best to use CalendarBehavior::MONTH constant
	 *
	 * @param \Cake\ORM\Query $query Query.
	 * @param array<string, mixed> $options Array of options as described above
	 * @return \Cake\ORM\Query
	 */
	public function findCalendar(Query $query, array $options) {
		$field = $this->getConfig('field');

		$year = $options[static::YEAR];
		$month = $options[static::MONTH];

		$from = new DateTime($year . '-' . $month . '-01');
		$lastDayOfMonth = $from->daysInMonth;

		$to = new DateTime($year . '-' . $month . '-' . $lastDayOfMonth . ' 23:59:59');

		$conditions = [
			$field . ' >=' => $from,
			$field . ' <=' => $to,
		];
		if ($this->getConfig('endField')) {
			$endField = $this->getConfig('endField');

			$conditions = [
				'OR' => [
					[
						$field . ' <=' => $to,
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
