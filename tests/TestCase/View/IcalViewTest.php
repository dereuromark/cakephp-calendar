<?php

namespace Calendar\Test\TestCase\View;

use Cake\Http\Response;
use Cake\Http\ServerRequest;
use Cake\Routing\Route\DashedRoute;
use Cake\Routing\Router;
use Cake\TestSuite\TestCase;
use Calendar\View\IcalView;

class IcalViewTest extends TestCase {

	protected IcalView $icalView;

	protected ServerRequest $request;

	protected Response $response;

	/**
	 * setUp method
	 *
	 * @return void
	 */
	public function setUp(): void {
		parent::setUp();

		$this->request = (new ServerRequest())
			->withParam('controller', 'Events')
			->withParam('action', 'index')
			->withParam('_ext', 'ics');
		$this->response = new Response();

		$this->icalView = new IcalView($this->request, $this->response);

		Router::defaultRouteClass(DashedRoute::class);
		$builder = Router::createRouteBuilder('/');
		$builder->fallbacks(DashedRoute::class);
		$builder->connect('/{controller}/{action}/*');
	}

	/**
	 * @return void
	 */
	public function tearDown(): void {
		parent::tearDown();

		unset($this->icalView);
	}

	/**
	 * @return void
	 */
	public function testRenderEmpty() {
		$this->icalView->set('events', []);

		$result = $this->icalView->render('view');
		$this->assertStringContainsString("BEGIN:VCALENDAR\r\n", $result);
		$this->assertStringContainsString("END:VCALENDAR\r\n", $result);
		$this->assertStringNotContainsString('BEGIN:VEVENT', $result);

		$type = $this->icalView->getResponse()->getType();
		$this->assertSame('text/calendar', $type);
	}

	/**
	 * @return void
	 */
	public function testRenderWithEvents() {
		$this->icalView->set('events', [
			[
				'uid' => 'demo-1',
				'summary' => 'Demo event',
				'location' => 'Room A',
				'start' => '2026-05-12 12:00:00',
				'end' => '2026-05-12 13:00:00',
			],
		]);

		$result = $this->icalView->render('view');

		$this->assertStringContainsString('BEGIN:VEVENT', $result);
		$this->assertStringContainsString('UID:demo-1', $result);
		$this->assertStringContainsString('SUMMARY:Demo event', $result);
		$this->assertStringContainsString('LOCATION:Room A', $result);
		$this->assertStringContainsString('END:VEVENT', $result);
	}

}
