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
 * @author    Shawn Rice <zooley@purdue.edu>
 * @copyright Copyright 2005-2011 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );
?>
<table class="results" id="orcid-results-list">
	<tbody>
		<?php if (count($this->records)) { ?>
			<?php
			foreach ($this->records as $record)
			{
				if (!$record || empty($record))
				{
					continue;
				}
				$fname     = array_key_exists('given-names', $record) ? $record['given-names'] : '';
				$lname     = array_key_exists('family-name', $record) ? $record['family-name'] : '';
				$orcid_uri = array_key_exists('uri', $record)         ? $record['uri'] : '';
				$orcid     = array_key_exists('path', $record)        ? $record['path'] : '';

				/*$scheme = JURI::getInstance()->getScheme();
				if ($orcid_uri && substr($orcid_uri, 0, strlen($scheme)) != $scheme)
				{
					$orcid_uri = preg_replace('/^([^:]+)/i', $scheme, $orcid_uri);
				}*/
				?>
				<tr>
					<td>
						<a class="title" target="_blank" rel="external" href="<?php echo $orcid_uri; ?>">
							<?php echo $fname . ' ' . $lname; ?>
						</a>
					</td>
					<td>
						<span class="orcid">
							<?php echo $orcid; ?>
						</span>
					</td>
					<td>
						<a class="btn" onclick="<?php echo $this->callbackPrefix . 'associateOrcid(\'orcid\', \'' . $orcid . '\');'; ?>">
							<?php echo JText::_('Associate this ORCID'); ?>
						</a>
					</td>
				</tr>
				<?php
			}
			?>
		<?php } else { ?>
			<tr>
				<td class="no-results">
					<?php echo JText::_('No results found.'); ?>
				</td>
			</tr>
		<?php } ?>
	</tbody>
</table>