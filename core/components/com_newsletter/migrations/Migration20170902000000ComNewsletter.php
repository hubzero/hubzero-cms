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
 * Migration script for installing default newsletter content
 **/
class Migration20170902000000ComNewsletter extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		if ($this->db->tableExists('#__newsletter_templates'))
		{
			$query = "SELECT `id` FROM `#__newsletter_templates` WHERE `name`='Default HTML Email Template';";
			$this->db->setQuery($query);
			$id = $this->db->loadResult();

			if (!$id)
			{
				$query = "INSERT INTO `#__newsletter_templates` (`editable`, `name`, `template`, `primary_title_color`, `primary_text_color`, `secondary_title_color`, `secondary_text_color`, `deleted`) VALUES
	(0, 'Default HTML Email Template', '<html>\n <head>\n    <title>{{TITLE}}</title>\n  </head>\n <body>\n    <table width=\"100%\" border=\"0\" cellspacing=\"0\">\n     <tr>\n        <td align=\"center\">\n         \n          <table width=\"700\" border=\"0\" cellpadding=\"20\" cellspacing=\"0\">\n           <tr class=\"display-browser\">\n              <td colspan=\"2\" style=\"font-size:10px;padding:0 0 5px 0;\" align=\"center\">\n               Email not displaying correctly? <a href=\"{{LINK}}\">View in a Web Browser</a>\n              </td>\n           </tr>\n           <tr>\n              <td colspan=\"2\" style=\"background:#000000;\">\n                <h1 style=\"color:#FFFFFF;\">HUB Campaign Template</h1>\n               <h3 style=\"color:#888888;\">{{TITLE}}</h3>\n             </td>\n           <tr>\n              <td width=\"500\" valign=\"top\" style=\"font-size:14px;color:#222222;border-left:1px solid #000000;\">\n               <span style=\"display:block;color:#CCCCCC;margin-bottom:20px;\">Issue {{ISSUE}}</span>\n                {{PRIMARY_STORIES}}\n             </td>\n             <td width=\"200\" valign=\"top\" style=\"font-size:12px;color:#555555;border-left:1px solid #AAAAAA;border-right:1px solid #000000;\">\n                {{SECONDARY_STORIES}}\n             </td>\n           </tr>\n           <tr>\n              <td colspan=\"2\" align=\"center\" style=\"background:#000000;color:#FFFFFF;\">\n               Copyright &copy; {{COPYRIGHT}} HUB. All Rights reserved.\n              </td>\n           </tr>\n         </table>\n        \n        </td>\n     </tr>\n   </table>\n  </body>\n</html>  ', '', '', '', '', 0),
	(0, 'Default Plain Text Email Template', 'View In Browser - {{LINK}}\n=====================================\n{{TITLE}} - {{ISSUE}}\n=====================================\n\n{{PRIMARY_STORIES}}\n\n--------------------------------------------------\n\n{{SECONDARY_STORIES}}\n\n--------------------------------------------------\n\nUnsubscribe - {{UNSUBSCRIBE_LINK}}\nCopyright - {{COPYRIGHT}}', NULL, NULL, NULL, NULL, 0);";

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
		if ($this->db->tableExists('#__newsletter_templates'))
		{
			$query = "DELETE FROM `#__newsletter_templates` WHERE `name`='Default HTML Email Template';";
			$this->db->setQuery($query);
			$this->db->query();
		}
	}
}
