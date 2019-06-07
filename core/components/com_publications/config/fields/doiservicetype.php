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
 * Renders a list of DOI service types
 */
class DOIServicetype extends Select
{
	/**
	 * Element name
	 *
	 * @var  string
	 */
	public $type = 'doiservicetype';

	/**
	 * Method to get the field options for DOI Service
	 *
	 * @return  array  The field options.
	 */
	protected function getOptions()
	{
		$options   = array();

		$options[] =  Html::select('option', '0', Lang::txt('None'));
		$options[] =  Html::select('option', '1', Lang::txt('EZID'));
		$options[] =  Html::select('option', '2', Lang::txt('DataCite'));

		Document::addScript('/core/components/com_publications/admin/assets/js/doiservice.js');

		return $options;
	}
}
