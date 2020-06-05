<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Modules\Syndicate;

use Hubzero\Module\Module;
use Hubzero\Utility\Arr;
use Document;

/**
 * Module helper class for syndicating a feed
 */
class Helper extends Module
{
	/**
	 * Display module contents
	 *
	 * @return  void
	 */
	public function display()
	{
		// [!] Legacy comptibility
		$params = $this->params;

		$params->def('format', 'rss');

		$link = self::getLink($params);

		if (is_null($link))
		{
			return;
		}

		$moduleclass_sfx = htmlspecialchars($params->get('moduleclass_sfx'));

		$text = htmlspecialchars($params->get('text'));

		require $this->getLayoutPath($params->get('layout', 'default'));
	}

	/**
	 * Get a link
	 *
	 * @param   object  $params  Registry
	 * @return  string
	 */
	static function getLink(&$params)
	{
		$data = Document::getHeadData();

		foreach ($data['links'] as $link => $value)
		{
			$value = Arr::toString($value);
			if (strpos($value, 'application/' . $params->get('format') . '+xml'))
			{
				return $link;
			}
		}
	}
}
