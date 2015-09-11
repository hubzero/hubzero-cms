<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2015 HUBzero Foundation, LLC.
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 *
 * HUBzero is a registered trademark of Purdue University.
 *
 * @package   hubzero-cms
 * @author    Ahmed Abdel-Gawad <aabdelga@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
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
					$orcid_html .= "<a target=\"_blank\" href=\"" . $orcid_url . "\">";
							$orcid_html .= $fname . " " . $lname;
					$orcid_html .= "</a>";
				$orcid_html .= "</p>";
			$orcid_html .= "</div>";
			$orcid_html .= "<div class=\"col span4\">";
				$orcid_html .= $orcid;
			$orcid_html .= "</div>";
			$orcid_html .= "<div class=\"col span4 omega\">";
			$orcid_html .= "<a style=\"text-decoration: none;\" class=\"btn\" onclick=\"HUB.Members.Profile.associateOrcid('', '" . $orcid ."')\"> " . Lang::txt('Associate this ORCID') . "</a>";
			$orcid_html .= "</div>";
		$orcid_html .= "</div>";
	$orcid_html .= "</li>";
}

$orcid_html .= "</ol>\n";

echo json_encode($orcid_html);
exit();
