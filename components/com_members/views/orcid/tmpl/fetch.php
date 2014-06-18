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

$orcid_html = '';


$orcid_html .= "<ol class=\"results\" id=\"orcid-results-list\">\n";
foreach($this->records as $record) {

  	$fname = array_key_exists ('given-names', $record) ? $record['given-names'] : '';
	$lname = array_key_exists ('family-name', $record) ? $record['family-name'] : '';
	$orcid_url = array_key_exists ('orcid-id', $record) ? $record['orcid-id'] : '';
	$orcid = array_key_exists ('orcid', $record) ? $record['orcid'] : '';

	$orcid_html .= "<li class=\"public\">";
		$orcid_html .= "<div class=\"grid\">";
			$orcid_html .= "<div class=\"col span4\">";
				$orcid_html .= "<p class=\"title\">";
					$orcid_html .= "<a target=\"_blank\" href=\"" . $orcid_url . "\">";
							$orcid_html .= $fname . " " . $lname;
					$orcid_html .= "</a>";
				$orcid_html .= "</p>";
			$orcid_html .= "</div>";
			$orcid_html .= "<div class=\"col span4\">";
				$orcid_html .= $orcid;
			$orcid_html .= "</div>";
			$orcid_html .= "<div class=\"col span4 omega\">";
			$orcid_html .= "<a style=\"text-decoration: none;\" class=\"btn\" onclick=\"HUB.Members.Profile.associateOrcid('', '" . $orcid ."')\"> " . JText::_('Associate this ORCID') . "</a>";
			$orcid_html .= "</div>";
		$orcid_html .= "</div>";
	$orcid_html .= "</li>";
}

$orcid_html .= "</ol>\n";

echo json_encode($orcid_html);
exit();

?>
