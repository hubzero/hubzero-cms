<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

$orcid_html = '';


$orcid_html .= "<ol class=\"results\" id=\"orcid-results-list\">\n";
foreach ($this->records as $record)
{
	$fname     = array_key_exists('given-names', $record) ? $record['given-names'] : '';
	$lname     = array_key_exists('family-name', $record) ? $record['family-name'] : '';
	$orcid_url = array_key_exists('orcid-id', $record) ? $record['orcid-id'] : '';
	$orcid     = array_key_exists('orcid', $record) ? $record['orcid'] : '';

	$orcid_html .= "<li class=\"public\">";
		$orcid_html .= "<div class=\"grid\">";
			$orcid_html .= "<div class=\"col span4\">";
				$orcid_html .= "<p class=\"title\">";
					$orcid_html .= "<a rel=\"nofollow external\" href=\"" . $orcid_url . "\">";
							$orcid_html .= $fname . " " . $lname;
					$orcid_html .= "</a>";
				$orcid_html .= "</p>";
			$orcid_html .= "</div>";
			$orcid_html .= "<div class=\"col span4\">";
				$orcid_html .= $orcid;
			$orcid_html .= "</div>";
			$orcid_html .= "<div class=\"col span4 omega\">";
			$orcid_html .= "<a class=\"btn\" onclick=\"HUB.Members.Profile.associateOrcid('', '" . $orcid ."')\"> " . Lang::txt('Associate this ORCID') . "</a>";
			$orcid_html .= "</div>";
		$orcid_html .= "</div>";
	$orcid_html .= "</li>";
}

$orcid_html .= "</ol>\n";

echo json_encode($orcid_html);
exit();
