<?php

namespace Calendar\Test\TestCase\View\Helper;

use Cake\Http\ServerRequest;
use Cake\I18n\DateTime;
use Cake\I18n\I18n;
use Cake\Routing\Route\DashedRoute;
use Cake\Routing\Router;
use Cake\TestSuite\TestCase;
use Cake\View\View;
use Calendar\View\Helper\CalendarHelper;

class CalendarHelperTest extends TestCase {

	protected CalendarHelper $Calendar;

	protected View $View;

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
		$this->Calendar->getView()->setRequest($request);

		Router::reload();
		Router::defaultRouteClass(DashedRoute::class);
		$builder = Router::createRouteBuilder('/');
		$builder->fallbacks(DashedRoute::class);
		$builder->connect('/{controller}/{action}/*');
		Router::setRequest($this->Calendar->getView()->getRequest());
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
		I18n::setLocale('en-us'); // English - United States
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

		// Saturday and Sunday are "weekend" days
		$this->assertStringContainsString('<td class="cell-weekend"><div class="cell-number">4', $result);
		$this->assertStringContainsString('<td class="cell-weekend"><div class="cell-number">5', $result);
	}

	/**
	 * @return void
	 */
	public function testRenderDeDe() {
		I18n::setLocale('de-de'); // German - Germany
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

		// Saturday and Sunday are "weekend" days
		$this->assertStringContainsString('<td class="cell-weekend"><div class="cell-number">4', $result);
		$this->assertStringContainsString('<td class="cell-weekend"><div class="cell-number">5', $result);
	}

	/**
	 * @return void
	 */
	public function testRenderArDz() {
		I18n::setLocale('ar-dz'); // Arabic - Algeria
		$this->Calendar = new CalendarHelper($this->View);

		$this->View->set('_calendar', [
			'span' => 3,
			'year' => 2010,
			'month' => 12,
		]);

		$result = $this->Calendar->render();
		$this->assertStringContainsString('<th colspan="5" class="cell-month">ديسمبر 2010</th>', $result);

		// Saturday is the first day of the week
		$this->assertStringContainsString('<tr><th class="cell-header">السبت</th>', $result);

		// Friday and Saturday are "weekend" days
		$this->assertStringContainsString('<td class="cell-weekend"><div class="cell-number">3', $result);
		$this->assertStringContainsString('<td class="cell-weekend"><div class="cell-number">4', $result);
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

		$this->Calendar->addRow(new DateTime(date('Y') . '-12-02 11:12:13'), 'Foo Bar', ['class' => 'event']);

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
