<?php

namespace TestApp\Model\Behavior;

use Cake\ORM\Query\SelectQuery;
use Calendar\Model\Behavior\CalendarBehavior;

class TypedCalendarBehavior extends CalendarBehavior {

	public int $calls = 0;

	/**
	 * @param \Cake\ORM\Query\SelectQuery $query Query
	 * @param int $year Calendar year
	 * @param int $month Calendar month
	 * @return \Cake\ORM\Query\SelectQuery
	 */
	public function findCalendar(SelectQuery $query, int $year, int $month): SelectQuery {
		$this->calls++;

		return parent::findCalendar($query, $year, $month);
	}

}
