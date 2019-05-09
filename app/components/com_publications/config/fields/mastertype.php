<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Form\Fields;

use Hubzero\Form\Fields\Select;
use Html;
use Lang;

/**
 * Renders a list of master types
 */
class Mastertype extends Select
{
	/**
	 * The form field type.
	 *
	 * @var  string
	 */
	protected $type = 'mastertype';

	/**
	 * Method to get the field options.
	 *
	 * @return  array  The field option objects.
	 */
	protected function getOptions()
	{
		$db = \App::get('db');

		include_once dirname(dirname(__DIR__)) . DS . 'tables' . DS . 'master.type.php';

		$mt = new \Components\Publications\Tables\MasterType($db);
		$types = $mt->getTypes('*', 0, 0, 'ordering');

		$options = array();
		$options[] = Html::select('option', '*', Lang::txt('- All contributable -'), 'value', 'text');

		foreach ($types as $type)
		{
			$options[] = Html::select('option', $type->alias, stripslashes($type->type), 'value', 'text');
		}

		$options = array_merge(parent::getOptions(), $options);

		return $options;
	}
}
