<?php

use Hubzero\Content\Migration\Base;

// No direct access
defined('_HZEXEC_') or die();

/**
 * Migration script for installing default content for the DrWho component
 **/
class Migration20170703150500ComPartners extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		if ($this->db->tableExists('#__partner_partners'))
		{
			$query = "INSERT INTO `jos_partner_partners` (`id`, `name`, `date_joined`, `partner_type`, `site_url` , `twitter_handle`, `groups_cn`, `logo_img`, `QUBES_liason_primary`,`QUBES_liason_secondary`,`partner_liason_primary`,`partner_liason_secondary`, `activities`, `state`, `featured`, `about`) VALUES
					(1,'National Science Foundation','2014-09-17',2,'www.nsf.gov','NSF','nsf', '', '','','', '','activites', 1, 1,
                    '<!-- {FORMAT:HTML} --><p>The National Science Foundation (NSF) is an independent federal agency created by Congress in 1950 to promote the
		             progress of science; to advance the national health, prosperity, and welfare; to secure the national defense…’\n</p>');";

			$this->db->setQuery($query);
			$this->db->query();
		}

		if ($this->db->tableExists('#__partner_type_partner_types'))
		{
			$query = "INSERT INTO `jos_partner_type_partner_types`(`id`,`internal`,`external`,`description`) VALUES
			(1,'Collaborators', 'Consortium Members', '<!-- {FORMAT:HTML} --><p>Sharing information, coordinating efforts \n</p>'),
			(2,'Alliance Partners', 'Partners', '<!-- {FORMAT:HTML} --><p>Joint programming (decision-making power is shared or transferred) \n</p>' ),
			(3,'Venture Partners', 'Featured Partners', '<!-- {FORMAT:HTML} --><p>Joint ventures \n</p>' ),
			(4,'Funding Partners', 'Sponsors', '<!-- {FORMAT:HTML} --><p>Recipient-donor relationship - determination for allocating funds \n</p>' ),
			(5,'Host Partners', 'Leadership Team', '<!-- {FORMAT:HTML} --><p>Cost-sharing; grant match; sharing of benefits and costs \n</p>' );";

			$this->db->setQuery($query);
			$this->db->query();
		}

		
	}

	/**
	 * Down
	 **/
	public function down()
	{
		

		if ($this->db->tableExists('#__partner_partners'))
		{
			$query = "DELETE FROM `#__partner_partners`";
			$this->db->setQuery($query);
			$this->db->query();
		}

		if ($this->db->tableExists('#__partner_type_partner_types'))
		{
			$query = "DELETE FROM `#__partner_type_partners_types`";
			$this->db->setQuery($query);
			$this->db->query();
		}
	}
}
