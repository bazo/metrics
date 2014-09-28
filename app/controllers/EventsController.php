<?php

namespace Controllers;


use Models\Events\EventsManager;
use Phalcon\DI\Injectable;

/**
 * @author Martin Bažík <martin@bazo.sk>
 */
class EventsController extends Injectable
{

	/** @var EventsManager */
	private $eventsManager;

	function __construct(EventsManager $eventsManager)
	{
		$this->eventsManager = $eventsManager;
	}


	public function create()
	{
		$json	 = $this->request->getRawBody();
		$data	 = json_decode($json, TRUE);

		$data['time'] = new \MongoDate($data['timestamp']);
		$this->eventsManager->add($data);
		exit;
	}


	public function show($type)
	{
		$granularity = $this->request->getQuery('granularity');

		$minDate = $this->request->getQuery('minDate');
		$maxDate = $this->request->getQuery('maxDate');

		$minDateTime = !is_null($minDate) ? new \DateTime($minDate) : NULL;
		$maxDateTime = !is_null($maxDate) ? new \DateTime($maxDate) : NULL;

		$data = $this->eventsManager->aggregate($type, $granularity, $minDateTime, $maxDateTime);

		$this->response->setJsonContent($data);
		$this->response->send();
	}


}
