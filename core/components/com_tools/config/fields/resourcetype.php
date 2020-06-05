<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Form\Fields;

use Hubzero\Form\Fields\Select;
use Html;

/**
 * Renders a list of resource types
 */
class Resourcetype extends Select
{
	/**
	 * The form field type.
	 *
	 * @var  string
	 */
	protected $type = 'resourcetype';

	/**
	 * Method to get the field options.
	 *
	 * @return  array  The field option objects.
	 */
	protected function getOptions()
	{
		include_once \Component::path('com_resources') . DS . 'models' . DS . 'type.php';

		$types = \Components\Resources\Models\Type::getMajorTypes();

		$options = array();

		foreach ($types as $type)
		{
			$options[] = Html::select('option', $type->id, stripslashes($type->type), 'value', 'text');
		}

		$options = array_merge(parent::getOptions(), $options);

		return $options;
	}
}
