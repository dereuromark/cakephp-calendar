<?php
namespace Calendar\Test\TestCase\View\Helper;

use Cake\I18n\Time;
use Cake\Network\Request;
use Cake\Routing\Router;
use Cake\TestSuite\TestCase;
use Cake\View\View;
use Calendar\View\Helper\CalendarHelper;

/**
 *
 */
class CalendarHelperTest extends TestCase {

	/**
	 * @var \Calendar\View\Helper\CalendarHelper
	 */
	public $Calendar;

	/**
	 * @var \Cake\View\View
	 */
	public $View;

	/**
	 * setUp method
	 *
	 * @return void
	 */
	public function setUp() {
		parent::setUp();

		$this->request = new Request();// $this->getMockBuilder(Request::class)->getMock();
		$this->request->params['action'] = 'index';
		$this->request->params['controller'] = 'Events';

		$this->View = new View($this->request);
		$this->Calendar = new CalendarHelper($this->View);

		Router::reload();

		Router::connect('/:controller', ['action' => 'index']);
		Router::connect('/:controller/:action/*');
	}

	/**
	 * tearDown method
	 *
	 * @return void
	 */
	public function tearDown() {
		parent::tearDown();

		unset($this->Calendar);
	}

	/**
	 * @return void
	 */
	public function testRenderEmpty() {
		$this->View->viewVars['_calendar'] = [
			'year' => 2010,
			'month' => 12,
		];

		$result = $this->Calendar->render();
		$this->assertContains('<table class="calendar">', $result);
	}

	/**
	 * @return void
	 */
	public function testRender() {
		$this->View->viewVars['_calendar'] = [
			'year' => 2010,
			'month' => 12,
		];

		$this->Calendar->addRow(new Time('2010-12-02 11:12:13'), 'Foo Bar', ['class' => 'event']);

		$result = $this->Calendar->render();

		$expected = '<td ><div class="cell-number">2</div><div class="cell-data"><ul><li class="event">Foo Bar</li></ul></div></td>';
		$this->assertContains($expected, $result);
	}

}
