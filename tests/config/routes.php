<?php

use Cake\Routing\Route\DashedRoute;
use Cake\Routing\RouteBuilder;
use Cake\Routing\Router;

Router::reload();

Router::scope('/', function(RouteBuilder $routes): void {
	$routes->fallbacks(DashedRoute::class);
});
