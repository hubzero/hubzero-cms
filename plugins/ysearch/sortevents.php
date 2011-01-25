<?php

class plgYSearchSortEvents extends YSearchPlugin
{
	public static function onYSearchSort($a, $b)
	{
		if (!isset($_GET['dbg']))
			return 0;

		if ($a->get_plugin() !== 'events' || $b->get_plugin() !== 'events' || $a->get_date() === $b->get_date())
			return 0;
	
		return $a->get_date() > $b->get_date() ? 1 : -1;
	}
}
