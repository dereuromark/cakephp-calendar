<?php

namespace Calendar\View;

use Cake\Event\EventManager;
use Cake\Http\Response;
use Cake\Http\ServerRequest;
use Cake\View\View;

/**
 * @see https://en.wikipedia.org/wiki/ICalendar
 */
class IcalView extends View {

	protected string $_ext = '.php';

	public string $subDir = 'ics';

	/**
	 * Mime-type this view class renders as.
	 *
	 * @return string The JSON content type.
	 */
	public static function contentType(): string {
		return 'text/calendar';
	}

	/**
	 * @param \Cake\Http\ServerRequest|null $request The request object.
	 * @param \Cake\Http\Response|null $response The response object.
	 * @param \Cake\Event\EventManager|null $eventManager Event manager object.
	 * @param array $viewOptions View options.
	 */
	public function __construct(
		?ServerRequest $request = null,
		?Response $response = null,
		?EventManager $eventManager = null,
		array $viewOptions = [],
	) {
		if ($response && $response instanceof Response) {
			$response = $response->withType('ics');
		}

		parent::__construct($request, $response, $eventManager, $viewOptions);

		$this->disableAutoLayout();
	}

}
