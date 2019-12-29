<?php

namespace Calendar\Test\TestCase\View\Helper;

use Cake\Http\Response;
use Cake\Http\ServerRequest;
use Cake\Routing\Router;
use Cake\TestSuite\TestCase;
use Calendar\View\IcalView;

class IcalViewTest extends TestCase {

	/**
	 * @var \Calendar\View\IcalView
	 */
	protected $icalView;

	/**
	 * @var \Cake\Http\ServerRequest
	 */
	protected $request;

	/**
	 * @var \Cake\Http\Response
	 */
	protected $response;

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

		Router::reload();

		Router::connect('/:controller', ['action' => 'index']);
		Router::connect('/:controller/:action/*');
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
