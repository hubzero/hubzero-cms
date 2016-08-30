<?php

use Hubzero\Content\Migration\Base;

/**
 * Migration script for ensuring system route plugins are in the correct order
 **/
class Migration20150630201210Core extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		if ($this->db->tableExists('#__extensions'))
		{
			$query = "SELECT * FROM `#__extensions` WHERE `folder` = 'system' ORDER BY `ordering`";
			$this->db->setQuery($query);
			$system = $this->db->loadObjectList();

			// Make sure we have results
			if (!$system || count($system) <= 0)
			{
				return;
			}

			$orderIdx = 0;
			$removals = [];
			$ordering = [
				'disablecache',
				'jquery',
				'certificate',
				'userconsent',
				'authfactors',
				'spamjail',
				'incomplete',
				'unconfirmed',
				'unapproved',
				'password',
			];

			foreach ($ordering as $order)
			{
				// Find the item that we're interested in...
				foreach ($system as $idx => $item)
				{
					if ($item->element == $order)
					{
						$result     = $item;
						$removals[] = $idx;
						break;
					}
				}

				if (isset($result))
				{
					$query = "UPDATE `#__extensions` SET `ordering` = " . $orderIdx . " WHERE `extension_id` = " . $this->db->quote($result->extension_id);
					$this->db->setQuery($query);
					$this->db->query();
					$orderIdx++;
				}
			}

			// Take out the items we've already saved
			if (count($removals) > 0)
			{
				foreach ($removals as $remove)
				{
					unset($system[$remove]);
				}
			}

			// That leaves everything else, which we'll keep in their same relative order
			foreach ($system as $plugin)
			{
				$query = "UPDATE `#__extensions` SET `ordering` = " . $orderIdx . " WHERE `extension_id` = " . $this->db->quote($plugin->extension_id);
				$this->db->setQuery($query);
				$this->db->query();
				$orderIdx++;
			}
		}
	}
}