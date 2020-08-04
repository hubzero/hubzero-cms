<?php
/**
 * @package    framework
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Content\Migration\Macros;

use Hubzero\Content\Migration\Macro;
use Hubzero\Config\Registry;
use Hubzero\Access\Asset;

/**
 * Migration macro to sets the asset rules for an element
 **/
class SetAssetRules extends Macro
{
	/**
	 * Sets the asset rules
	 *
	 * @param   string  $element  The element to which the rules apply
	 * @param   array   $rules    The incoming rules to set
	 * @return  void
	 **/
	public function __invoke($element, $rules)
	{
		if ($this->db->tableExists('#__assets'))
		{
			$asset = Asset::oneByName($element);

			if (!$asset || !$asset->get('id'))
			{
				return false;
			}

			// Loop through and map textual groups to ids (if applicable)
			foreach ($rules as $idx => $rule)
			{
				foreach ($rule as $group => $value)
				{
					if (!is_numeric($group))
					{
						$query = $this->db->getQuery()
							->select('id')
							->from('#__usergroups')
							->whereEquals('title', $group)
							->toString();

						$this->db->setQuery($query);

						if ($id = $this->db->loadResult())
						{
							unset($rules[$idx][$group]);

							$rules[$idx][$id] = $value;
						}
					}
				}
			}

			$asset->set('rules', json_encode($rules));
			$asset->save();

			return true;
		}

		return false;
	}
}
