<?php

namespace Components\Supportstats\Helpers;

class ArrayHelper
{

	public static function flatten($original, $return = array())
	{
		foreach ($original as $element)
		{
			if (is_array($element))
			{
				$return = self::flatten($element, $return);
			}
			else
			{
				$return[] = $element;
			}
		}
		return $return;
	}

	public static function mapByAttribute($array, $attribute)
	{
		$mappedArray = array();

		foreach ($array	as $element)
		{
			$attributeValue = $element->get($attribute);
			$mappedArray[$attributeValue] = $element;
		}

		return $mappedArray;
	}

}
