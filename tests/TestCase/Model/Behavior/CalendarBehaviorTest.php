<?php

namespace Calendar\Test\TestCase\Model\Behavior;

use Cake\ORM\Entity;
use Cake\ORM\Table;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

class CalendarBehaviorTest extends TestCase {

	/**
	 * @var array<string>
	 */
	protected array $fixtures = [
		'plugin.Calendar.Events',
	];

	/**
	 * @var \Cake\ORM\Table;
	 */
	protected Table $Events;

	/**
	 * @var array
	 */
	protected array $config = [
		'field' => 'beginning',
	];

	/**
	 * setUp
	 *
	 * @return void
	 */
	public function setUp(): void {
		parent::setUp();

		$this->Events = TableRegistry::getTableLocator()->get('Calendar.Events');
		$this->Events->addBehavior('Calendar.Calendar', $this->config);

		$this->truncate();
		$this->_addFixtureData();
	}

	/**
	 * @return void
	 */
	protected function truncate() {
		/** @var \Cake\Database\Schema\SqlGeneratorInterface $schema */
		$schema = $this->Events->getSchema();
		$sql = $schema->truncateSql($this->Events->getConnection());
		foreach ($sql as $snippet) {
			$this->Events->getConnection()->execute($snippet);
		}
	}

	/**
	 * @return void
	 */
	public function tearDown(): void {
		parent::tearDown();

		unset($this->Events);
		TableRegistry::getTableLocator()->clear();
	}

	/**
	 * @return void
	 */
	public function testFind() {
		$options = [
			'month' => 12,
			'year' => (int)date('Y'),
		];

		$events = $this->Events->find('calendar', ...$options);

		$eventList = array_values($events->find('list')->toArray());
		$expected = [
			'One',
			'4 days',
			'Over new years eve',
		];
		$this->assertEquals($expected, $eventList);
	}

	/**
	 * Gets a new Entity
	 *
	 * @param array $data
	 * @return \Cake\ORM\Entity
	 */
	protected function _getEntity($data) {
		return new Entity($data);
	}

	/**
	 * @throws \Cake\Http\Exception\InternalErrorException
	 * @return void
	 */
	protected function _addFixtureData() {
		$data = [
			[
				'title' => 'Wrong',
				'beginning' => date('Y') . '-11-30',
			],
			[
				'title' => 'One',
				'beginning' => date('Y') . '-12-28',
			],
			[
				'title' => '4 days',
				'beginning' => date('Y') . '-12-14',
				'end' => date('Y') . '-12-18',
			],
			[
				'title' => 'Over new years eve',
				'beginning' => date('Y') . '-12-29',
				'end' => ((int)date('Y') + 1) . '-01-02',
			],
		];

		foreach ($data as $row) {
			$entity = $this->_getEntity($row);
			$this->Events->saveOrFail($entity);
		}
	}

}
