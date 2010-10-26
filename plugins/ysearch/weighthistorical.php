<?php

class plgYSearchWeightHistorical
{
	public static function onYSearchWeightAll($terms, $res)
	{
		return $res->get_plugin() == 'resources' && strtolower($res->get('section')) == 'historical documents' ? 0.1 : 1;
	}
}
