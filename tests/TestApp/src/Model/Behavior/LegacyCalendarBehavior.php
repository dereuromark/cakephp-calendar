<?php

namespace TestApp\Model\Behavior;

use Cake\ORM\Query\SelectQuery;
use Calendar\Model\Behavior\CalendarBehavior;

class LegacyCalendarBehavior extends CalendarBehavior {

	public int $calls = 0;

	/**
	 * @param \Cake\ORM\Query\SelectQuery $query Query
	 * @param array<string, mixed> $options Options
	 * @return \Cake\ORM\Query\SelectQuery
	 */
	public function findCalendar(SelectQuery $query, array $options): SelectQuery {
		$this->calls++;

		return parent::findCalendar($query, $options);
	}

}
