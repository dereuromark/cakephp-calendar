<?php

namespace Calendar\Test\TestCase\View\Helper;

use Cake\Http\ServerRequest;
use Cake\I18n\I18n;
use Cake\I18n\Time;
use Cake\Routing\Router;
use Cake\TestSuite\TestCase;
use Cake\View\View;
use Calendar\View\Helper\CalendarHelper;

class CalendarHelperTest extends TestCase {

	/**
	 * @var \Calendar\View\Helper\CalendarHelper
	 */
	protected $Calendar;

	/**
	 * @var \Cake\View\View
	 */
	protected $View;

	/**
	 * setUp method
	 *
	 * @return void
	 */
	public function setUp(): void {
		parent::setUp();

		$request = (new ServerRequest(['url' => '/events']))
			->withParam('controller', 'Events')
			->withParam('action', 'index');

		$this->View = new View($request);
		$this->Calendar = new CalendarHelper($this->View);
		Router::setRequest($request);

		Router::reload();
		Router::connect('/:controller', ['action' => 'index']);
		Router::connect('/:controller/:action/*');
	}

	/**
	 * tearDown method
	 *
	 * @return void
	 */
	public function tearDown(): void {
		parent::tearDown();

		unset($this->Calendar);
	}

	/**
	 * @return void
	 */
	public function testRenderEmpty() {
		$this->View->set('_calendar', [
			'span' => 3,
			'year' => 2010,
			'month' => 12,
		]);

		$result = $this->Calendar->render();
		$this->assertStringContainsString('<table class="calendar">', $result);
	}

	/**
	 * @return void
	 */
	public function testRenderEnUs() {
		I18n::setLocale('en-us');
		$this->Calendar = new CalendarHelper($this->View);

		$this->View->set('_calendar', [
			'span' => 3,
			'year' => 2010,
			'month' => 12,
		]);

		$result = $this->Calendar->render();
		$this->assertStringContainsString('<th colspan="5" class="cell-month">December 2010</th>', $result);
		// Sunday is the first day of the week
		$this->assertStringContainsString('<tr><th class="cell-header">Sun</th>', $result);
	}

	/**
	 * @return void
	 */
	public function testRenderDeDe() {
		I18n::setLocale('de-de');
		$this->Calendar = new CalendarHelper($this->View);

		$this->View->set('_calendar', [
			'span' => 3,
			'year' => 2010,
			'month' => 12,
		]);

		$result = $this->Calendar->render();
		$this->assertStringContainsString('<th colspan="5" class="cell-month">Dezember 2010</th>', $result);
		// Monday is the first day of the week
		$this->assertStringContainsString('<tr><th class="cell-header">Mo</th>', $result);
	}

	/**
	 * @return void
	 */
	public function testRender() {
		$this->View->set('_calendar', [
			'span' => 3,
			'year' => date('Y'),
			'month' => 12,
		]);

		$this->Calendar->addRow(new Time(date('Y') . '-12-02 11:12:13'), 'Foo Bar', ['class' => 'event']);

		$result = $this->Calendar->render();

		$expected = '<div class="cell-number">2</div><div class="cell-data"><ul><li class="event">Foo Bar</li></ul></div>';
		$this->assertStringContainsString($expected, $result);

		$this->assertStringContainsString('<th class="cell-prev"><a', $result);
		$this->assertStringContainsString('<th class="cell-next"><a', $result);
	}

	/**
	 * @return void
	 */
	public function testRenderNoPref() {
		$this->View->set('_calendar', [
			'span' => 3,
			'year' => date('Y') - 4,
			'month' => 12,
		]);

		$result = $this->Calendar->render();

		$expected = '><th class="cell-prev"></th>';
		$this->assertStringContainsString($expected, $result);

		$this->assertStringContainsString('<th class="cell-next"><a', $result);
	}

	/**
	 * @return void
	 */
	public function testRenderNoNext() {
		$this->View->set('_calendar', [
			'span' => 3,
			'year' => date('Y') + 4,
			'month' => 12,
		]);

		$result = $this->Calendar->render();

		$expected = '><th class="cell-next"></th>';
		$this->assertStringContainsString($expected, $result);

		$this->assertStringContainsString('<th class="cell-prev"><a', $result);
	}

}
