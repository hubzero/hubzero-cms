<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

use Hubzero\Content\Migration\Base;
// No direct access  
defined('_HZEXEC_') or die();  

/**
 * Migration script for ...
 **/
class Migration20220624143229PlgAuthenticationShibboleth extends Base
{
	/**
	* Up
	**/
	public function up()
	{
		if ($this->db->tableExists('#__extensions'))
		{ 
			if ($this->db->tableHasField('#__extensions', 'params'))  
        		{
                                $query = "ALTER TABLE `#__extensions` MODIFY COLUMN params LONGTEXT not null";
                                $this->db->setQuery($query);
                                $this->db->query();
			}
		}
	}

	/**
	 * Down
	 **/
	public function down()
	{
                if ($this->db->tableExists('#__extensions'))
                { 
                        if ($this->db->tableHasField('#__extensions', 'params'))    
                        {
                                /* this is dangerous as contents can be clipped and become unparseable then GUI fails
				$query = "ALTER TABLE `#__extensions` MODIFY COLUMN params TEXT not null";
                                $this->db->setQuery($query);
                                $this->db->query();
				*/
                        }
                }
	}
}
