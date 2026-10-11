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
		$this->Controller->Calendar->init((string)date('Y'), '02');

		$this->assertSame((int)date('Y'), $this->Controller->Calendar->year());
		$this->assertSame(2, $this->Controller->Calendar->month());
	}

	/**
	 * @return void
	 */
	public function testInitFromString() {
		$this->Controller->Calendar->init((string)date('Y'), 'february');

		$this->assertSame((int)date('Y'), $this->Controller->Calendar->year());
		$this->assertSame(2, $this->Controller->Calendar->month());
	}

	/**
	 * @return void
	 */
	public function testInitInvalid() {
		$this->expectException(NotFoundException::class);

		$this->Controller->Calendar->init((string)date('Y'), '');
	}

	public function testInitWithoutDateUsesCurrentMonth(): void {
		$this->Controller->Calendar->init(null, null);

		$this->assertSame((int)date('Y'), $this->Controller->Calendar->year());
		$this->assertSame((int)date('n'), $this->Controller->Calendar->month());
	}

	public function testInitRejectsYearWithTrailingText(): void {
		$this->expectException(NotFoundException::class);
		$this->Controller->Calendar->init(date('Y') . 'invalid', '02');
	}

	public function testInitRejectsFractionalMonth(): void {
		$this->expectException(NotFoundException::class);
		$this->Controller->Calendar->init(date('Y'), '2.5');
	}

	public function testInitRejectsUnknownDateInsteadOfDefaulting(): void {
		$this->expectException(NotFoundException::class);
		$this->Controller->Calendar->init('invalid', 'unknown');
	}

	/**
	 * @return void
	 */
	public function testZeroDateSentinelsStillUseCurrentMonth(): void {
		foreach ([[0, 0], ['0', '0'], ['00', '00']] as [$year, $month]) {
			$this->Controller->Calendar->init($year, $month);
			$this->assertSame((int)date('Y'), $this->Controller->Calendar->year());
			$this->assertSame((int)date('n'), $this->Controller->Calendar->month());
		}
	}

}
