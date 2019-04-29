<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Form\Fields;

use Hubzero\Form\Fields\Select;
use Components\Resources\Models\Type;
use Html;
use Lang;

/**
 * Renders a list of support ticket statuses
 */
class Resourcetype extends Select
{
	/**
	 * Element name
	 *
	 * @var  string
	 */
	public $type = 'Resourcetype';

	/**
	 * Method to get the field options for category
	 * Use the extension attribute in a form to specify the.specific extension for
	 * which categories should be displayed.
	 * Use the show_root attribute to specify whether to show the global category root in the list.
	 *
	 * @return  array  The field option objects.
	 */
	protected function getOptions()
	{
		$options   = array();
		$options[] =  Html::select('option', '0', Lang::txt('All'));

		include_once \Component::path('com_resources') . '/models/type.php';

		$types = Type::getMajorTypes();

		foreach ($types as $anode)
		{
			$options[] = Html::select('option', $anode->id, stripslashes($anode->type));
		}

		return $options;
	}
}
