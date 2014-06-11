<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2011 Purdue University. All rights reserved.
 *
 * This file is part of: The HUBzero(R) Platform for Scientific Collaboration
 *
 * The HUBzero(R) Platform for Scientific Collaboration (HUBzero) is free
 * software: you can redistribute it and/or modify it under the terms of
 * the GNU Lesser General Public License as published by the Free Software
 * Foundation, either version 3 of the License, or (at your option) any
 * later version.
 *
 * HUBzero is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * HUBzero is a registered trademark of Purdue University.
 *
 * @package   hubzero-cms
 * @author    Ahmed Abdel-Gawad <aabdelga@purdue.edu>
 * @copyright Copyright 2005-2011 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );


jimport( 'joomla.application.component.view');  
   
 
   
class MembersViewOrcid extends JView  
{  
    public function display($tpl = null)  
    {  
		echo "<h4>WERTY</h4>";
		$model = &$this->getModel();  
		$html = $model->getORCIDs();
		dump($html, "ORCID");
		echo $html;
	}
	
	function fillData($root, &$orcid, &$orcid_url)
	{
		foreach($root->children() as $ch) {
			if($ch->count() == 0) {
				
				echo "<tr><td style=\"background-color: #0F0F0F;\"><b>" . $ch->getName() . "</b></td><td>";
				if($ch->getName() == "orcid-id") {
					echo "<a target=\"_blank\" href=\"" . $ch . "\">" . $ch . "</a>";
					$orcid_url = $ch;
				}
				else {
					if($ch->getName() == "orcid")
						$orcid = $ch;
					echo $ch;
				}
				echo "</td></tr>";
			} else {
				$orcid_url_param = '';
				fillData($ch, $orcid, $orcid_url_param);
			}
		}
	}

}


?>