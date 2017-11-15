<?php
class Utility
{
	/**
	 * Find the URL of the provided object
	 * @static
	 * @param mixed $item
	 * @return string
	 */
	public static function findUrl($item)
	{
		$return = null;
		try {
			if (is_string($item))
			{
				$return = $item;
			}
			elseif (is_object($item))
			{
				$return = 'https://api.constantcontact.com'.$item->link;
			}
			if ($return == null)
			{
				throw new CTCTException('Constant Contact Error: Unable to determine which url to access');
			}
		} catch (CTCTException $e) {
			$e->generateError();
		}
		return $return;
	}

	/**
	 * Find the next link from collection XML
	 *
	 * @static
	 * @param SimpleXMLElement $item
	 * @return string - valid nextlink to be used, else false if none could be found
	 */
	public static function findNextLink($item)
	{
		$nextLink = $item->xpath("//*[@rel='next']");
		return ($nextLink) ? (string) $nextLink[0]->Attributes()->href : false;
	}
}
