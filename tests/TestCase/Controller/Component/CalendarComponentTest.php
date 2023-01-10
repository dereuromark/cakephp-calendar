<?php

namespace Calendar\Test\TestCase\Controller\Component;

use Cake\Http\Exception\NotFoundException;
use Cake\Http\ServerRequest;
use Cake\TestSuite\TestCase;
use TestApp\Controller\CalendarComponentTestController;

class CalendarComponentTest extends TestCase {

	protected CalendarComponentTestController $Controller;

	/**
	 * @return void
	 */
	public function setUp(): void {
		parent::setUp();

		$this->Controller = new CalendarComponentTestController(new ServerRequest());
		$this->Controller->startupProcess();
	}

	/**
	 * @return void
	 */
	public function tearDown(): void {
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
	 * @return void
	 */
	public function testInitInvalid() {
		$this->expectException(NotFoundException::class);

		$this->Controller->Calendar->init('2016', '');
	}

}
