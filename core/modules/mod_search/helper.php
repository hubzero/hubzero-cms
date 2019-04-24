<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Modules\Search;

use Hubzero\Module\Module;
use Document;
use Request;
use Config;
use Route;
use Lang;

/**
 * Module class for displaying a search form
 */
class Helper extends Module
{
	/**
	 * Number of instances of the module
	 *
	 * @var  integer
	 */
	public static $instances = 0;

	/**
	 * Display the search form
	 *
	 * @return  void
	 */
	public function display()
	{
		self::$instances++;

		if ($this->params->get('opensearch', 0))
		{
			$ostitle = $this->params->get('opensearch_title', Lang::txt('MOD_SEARCH_SEARCHBUTTON_TEXT') . ' ' . Config::get('sitename'));

			Document::addHeadLink(
				Request::base() . Route::url('&option=com_search&format=opensearch'),
				'search',
				'rel',
				array('title' => htmlspecialchars($ostitle), 'type' => 'application/opensearchdescription+xml')
			);
		}

		//$upper_limit = Lang::getUpperLimitSearchWord();
		//$maxlength   = $upper_limit;

		$params          = $this->params;
		$button          = $this->params->get('button', '');
		$button_pos      = $this->params->get('button_pos', 'right');
		$button_text     = htmlspecialchars($this->params->get('button_text', Lang::txt('MOD_SEARCH_SEARCHBUTTON_TEXT')));
		$width           = intval($this->params->get('width', 20));
		$text            = htmlspecialchars($this->params->get('text', Lang::txt('MOD_SEARCH_SEARCHBOX_TEXT')));
		$label           = htmlspecialchars($this->params->get('label', Lang::txt('MOD_SEARCH_LABEL_TEXT')));
		$moduleclass_sfx = htmlspecialchars($this->params->get('moduleclass_sfx'));

		require $this->getLayoutPath($this->params->get('layout', 'default'));
	}
}
