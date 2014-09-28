<?php

namespace Models\Events;


/**
 * @author Martin Bažík <martin@bazo.sk>
 */
class EventsManager
{

	public function add($data)
	{
		$event = new Events;

		foreach ($data as $property => $value) {
			$event->$property = $value;
		}

		$event->save();
	}


	public function findAll()
	{
		return Events::find();
	}


	public function aggregate($type, $granularity = Events::GRANULARITY_HOUR, \DateTime $minDate = NULL, \DateTime $maxDate = NULL)
	{
		$pipeline = [];

		if (!is_null($minDate) or ! is_null($maxDate)) {

			$time = [];
			if (!is_null($minDate)) {
				$minDate->setTime(0, 0, 0);
				$time['$gte'] = new \MongoDate($minDate->getTimestamp());
			}

			if (!is_null($maxDate)) {
				$maxDate->setTime(23, 59, 59);
				$time['$lte'] = new \MongoDate($maxDate->getTimestamp());
			}

			$query = [
				'$match' => [
					"time" => $time
				]
			];
		}

		$project = [
			'$project' => [
				'_id'	 => 0,
				'type'	 => 1,
				'value'	 => 1,
				'time'	 => 1
			]
		];

		$id			 = [];
		$sortColumns = [];

		switch ($granularity) {
			case Events::GRANULARITY_SECOND:
				$id['second']				 = [
					'$second' => '$time'
				];
				$sortColumns['_id.second']	 = 1;
			case Events::GRANULARITY_MINUTE:
				$id['minute']				 = [
					'$minute' => '$time'
				];
				$sortColumns['_id.minute']	 = 1;
			case Events::GRANULARITY_HOUR:
				$id['hour']					 = [
					'$hour' => '$time'
				];
				$sortColumns['_id.hour']	 = 1;
			case Events::GRANULARITY_DAY:
				$id['day']					 = [
					'$dayOfMonth' => '$time'
				];
				$sortColumns['_id.day']		 = 1;
			case Events::GRANULARITY_MONTH:
				$id['month']				 = [
					'$month' => '$time'
				];
				$sortColumns['_id.month']	 = 1;
			case Events::YEAR:
				$id['year']					 = [
					'$year' => '$time'
				];
				$sortColumns['_id.year']	 = 1;
		}

		$group = ['$group' => [
				'_id'	 => $id,
				'count'	 => [
					'$sum' => 1
				],
				'avg'	 => [
					'$avg' => '$value'
				],
				'min'	 => [
					'$min' => '$value'
				],
				'max'	 => [
					'$max' => '$value'
				]
			]
		];

		$sort = [
			'$sort' => $sortColumns
		];

		if (!empty($query)) {
			array_push($pipeline, $query);
		}
		array_push($pipeline, $project);
		array_push($pipeline, $group);
		array_push($pipeline, $sort);

		$data = \Models\Events\Events::aggregate($pipeline);
		return $data;
	}


}
