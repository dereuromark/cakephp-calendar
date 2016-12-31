<?php
namespace TestApp\Controller;

use Cake\Controller\Controller;

/**
 * Use Controller instead of AppController to avoid conflicts
 *
 * @property \Calendar\Controller\Component\CalendarComponent $Calendar
 */
class CalendarComponentTestController extends Controller {

	/**
	 * @var array
	 */
	public $components = ['Calendar.Calendar'];

}
