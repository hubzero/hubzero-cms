<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Form\Fields;

use Hubzero\Form\Fields\Select;
use Plugin;
use Html;
use Lang;

/**
 * Renders a list of support ticket statuses
 */
class Mailers extends Select
{
	/**
	 * Element name
	 *
	 * @var  string
	 */
	public $type = 'Mailers';

	/**
	 * Method to get the field options for category
	 *
	 * @return  array
	 */
	protected function getOptions()
	{
		$options   = [];
		$options[] = Html::select('option', '', Lang::txt('Site default'));

		foreach (Plugin::byType('mail') as $plugin)
		{
			$options[] = Html::select('option', $plugin->name, ucfirst($plugin->name));
		}

		return $options;
	}
}
