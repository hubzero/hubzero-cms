<?php

class plgYSearchWeightTools
{
	public static function onYSearchWeightResources($_terms, $res)
	{
		return $res->get_plugin() == 'resources' && $res->get_section() == 'Tools' ? 1 : 0.5;
	}
}
