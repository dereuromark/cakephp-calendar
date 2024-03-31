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
		//$this->icalView->setviewVars[] = [];

		$result = $this->icalView->render('view');
		$this->assertSame('', $result);

		$type = $this->icalView->getResponse()->getType();
		$this->assertSame('text/calendar', $type);
	}

}
