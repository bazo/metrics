<?php

namespace Models\Events;


/**
 * @author Martin Bažík <martin@bazo.sk>
 */
class Events extends \Phalcon\Mvc\Collection
{

	const GRANULARITY_YEAR = 'year';
	const GRANULARITY_MONTH = 'month';
	const GRANULARITY_DAY = 'day';
	const GRANULARITY_HOUR = 'hour';
	const GRANULARITY_MINUTE = 'minute';
	const GRANULARITY_SECOND = 'second';

	public function getSource()
	{
		return 'events';
	}


}
