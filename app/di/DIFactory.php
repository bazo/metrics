<?php

namespace DI;

use Controllers\EventsController;
use Models\Events\EventsManager;
use MongoClient;
use Phalcon\DI\FactoryDefault;
use Phalcon\Mvc\Collection\Manager;
use Phalcon\Mvc\Url;

/**
 * @author Martin Bažík <martin@bazo.sk>
 */
class DIFactory
{

	/** @return FactoryDefault */
	public static function create($config)
	{
		$di = new FactoryDefault();

		$di->set('url', function() {
			$url = new Url();
			$url->setBaseUri('/');
			return $url;
		});

		$di->set('mongo', function() {
			$mongo = new MongoClient();
			return $mongo->selectDB('metrics');
		}, true);

		$di->set('collectionManager', function() {
			return new Manager();
		}, true);

		$di->set('eventsManager', function() use ($di) {
			$service = new EventsManager;
			return $service;
		});

		$di->set('eventsController', function() use ($di) {
			$service = new EventsController($di->get('eventsManager'));
			return $service;
		});

		return $di;
	}


}
