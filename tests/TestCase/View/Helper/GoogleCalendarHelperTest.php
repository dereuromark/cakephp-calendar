<?php

namespace Calendar\Test\TestCase\View\Helper;

use Cake\I18n\Date;
use Cake\I18n\DateTime;
use Cake\TestSuite\TestCase;
use Cake\View\View;
use Calendar\View\Helper\GoogleCalendarHelper;

class GoogleCalendarHelperTest extends TestCase {

	protected GoogleCalendarHelper $GoogleCalendar;

	protected View $View;

	/**
	 * setUp method
	 *
	 * @return void
	 */
	public function setUp(): void {
		parent::setUp();

		$this->View = new View();
		$this->GoogleCalendar = new GoogleCalendarHelper($this->View);
	}

	/**
	 * tearDown method
	 *
	 * @return void
	 */
	public function tearDown(): void {
		parent::tearDown();

		unset($this->GoogleCalendar);
	}

	/**
	 * @return void
	 */
	public function testLink() {
		$details = [
			'details' => 'My details',
			'location' => 'My location',
			//'ctz' => 'Europe/Berlin',
		];
		$fromTo = [
			'from' => new DateTime('2023-12-02 15:00:00'),
			'to' => new DateTime('2023-12-02 18:00:00'),
		];

		$result = $this->GoogleCalendar->link('My title', $fromTo, $details);
		$expected = 'https://calendar.google.com/calendar/render?action=TEMPLATE&text=My+title&dates=20231202T15%3A00%3A00%2B00%3A00%2F20231202T18%3A00%3A00%2B00%3A00&details=My+details&location=My+location';
		$this->assertSame($expected, $result);
	}

	/**
	 * @return void
	 */
	public function testLinkDate() {
		$fromTo = [
			'from' => new Date('2023-12-02'),
		];

		$result = $this->GoogleCalendar->link('My title', $fromTo);
		$expected = 'https://calendar.google.com/calendar/render?action=TEMPLATE&text=My+title&dates=2023122%2F2023123';
		$this->assertSame($expected, $result);
	}

}
