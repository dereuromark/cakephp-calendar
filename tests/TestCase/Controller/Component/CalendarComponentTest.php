<?php

namespace Calendar\Test\TestCase\Controller\Component;

use Cake\Network\Request;
use Cake\TestSuite\TestCase;
use TestApp\Controller\CalendarComponentTestController;

/**
 */
class CalendarComponentTest extends TestCase {

	/**
	 * @var \TestApp\Controller\CalendarComponentTestController
	 */
	protected $Controller;

	/**
	 * @return void
	 */
	public function setUp() {
		parent::setUp();

		$this->Controller = new CalendarComponentTestController(new Request());
		$this->Controller->startupProcess();
	}

	/**
	 * @return void
	 */
	public function tearDown() {
		parent::tearDown();

		unset($this->Controller->Calendar);
		unset($this->Controller);
	}

	/**
	 * @return void
	 */
	public function testInit() {
		$this->Controller->Calendar->init('2016', '02');

		$this->assertSame(2016, $this->Controller->Calendar->year());
		$this->assertSame(2, $this->Controller->Calendar->month());
	}

	/**
	 * @return void
	 */
	public function testInitFromString() {
		$this->Controller->Calendar->init('2016', 'february');

		$this->assertSame(2016, $this->Controller->Calendar->year());
		$this->assertSame(2, $this->Controller->Calendar->month());
	}

	/**
	 * @expectedException \Cake\Network\Exception\NotFoundException
	 * @return void
	 */
	public function testInitInvalid() {
		$this->Controller->Calendar->init('2016', '');
	}

}
