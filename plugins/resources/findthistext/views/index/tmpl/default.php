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

$this->css();

// parse the custom fields out of the resource
$resourceFields = array();
preg_match_all("#<nb:(.*?)>(.*?)</nb:(.*?)>#s", $this->model->resource->fulltxt, $matches, PREG_SET_ORDER);
if (count($matches) > 0)
{
	foreach ($matches as $match)
	{
		$resourceFields[$match[1]] = $match[2];
	}
}
?>

<h3><?php echo JText::_('PLG_RESOURCES_FINDTHISTEXT'); ?></h3>
<p><?php echo JText::_('PLG_RESOURCES_FINDTHISTEXT_DESC'); ?></p>

<table class="find">
	<tbody>
		<?php if (isset($resourceFields['doi']) && $resourceFields['doi'] != '') : ?>
			<tr>
				<th>
					<?php echo JText::_('PLG_RESOURCES_FINDTHISTEXT_FIELD_DOI_LABEL'); ?>
				</th>
				<td>
					<?php
						// make sure have a valid url
						$doiUrl = 'http://dx.doi.org/' . $resourceFields['doi'];
					?>
					<a rel="external" href="<?php echo $doiUrl; ?>">
						<?php echo $doiUrl; ?>
					</a>
				</td>
			</tr>
		<?php endif; ?>
		<?php if ($this->openurl) : ?>
			<tr>
				<th>
					<?php echo JText::_('PLG_RESOURCES_FINDTHISTEXT_FIELD_LOCALLIBRARY_LABEL'); ?>
				</th>
				<td>
					<?php echo JText::_('PLG_RESOURCES_FINDTHISTEXT_FIELD_LOCALLIBRARY_DESC'); ?>
					<?php
						$text  = $this->openurl->text;
						$image = "<img src=\"{$this->openurl->icon}\" alt=\"\" />";

						// add field data to local library link
						$fields   = array('doi','isbn','issn');
						$linkData = array(
							'title' => $this->model->resource->title
						);
						foreach ($fields as $field)
						{
							if (isset($resourceFields[$field]) && $resourceFields[$field] != '')
							{
								$linkData[$field] = $resourceFields[$field];
							}
						}

						// build link
						$link  = rtrim($this->openurl->link, '?') . '?' . http_build_query($linkData);
					?>
					<a rel="external" href="<?php echo $link; ?>">
						<?php echo ($this->openurl->icon) ? $image : $text; ?>
					</a>
				</td>
			</tr>
		<?php endif; ?>
		<tr>
			<th>
				<?php echo JText::_('PLG_RESOURCES_FINDTHISTEXT_FIELD_GOOGLESCHOLAR_LABEL'); ?>
			</th>
			<td>
				<?php
					$query = '';
					if (isset($resourceFields['doi']) && $resourceFields['doi'] != '')
					{
						$query .= $resourceFields['doi'];
					}
					elseif ($this->model->resource->title)
					{
						$query .= $this->model->resource->title;
					}
					?>
				<a rel="external" title="Google Scholar Search Results" href="http://scholar.google.com/scholar?q=<?php echo $query; ?>">
					<img src="http://scholar.google.com/intl/en/scholar/images/scholar_logo_lg_2011.gif" alt="Google Scholar Search Results" width="100" />
				</a>
			</td>
		</tr>
		<tr>
			<th>
				<?php echo JText::_('PLG_RESOURCES_FINDTHISTEXT_FIELD_OTHERSOURCES_LABEL'); ?>
			</th>
			<td>
				<ul>
					<li>
						<?php
							$url = 'http://www.deepdyve.com/search?query=' . str_replace(' ', '+',  $this->model->resource->title);
							echo JText::sprintf('PLG_RESOURCES_FINDTHISTEXT_SOURCES_DEEPDYVE', $url);
						?>
					</li>
				</ul>
			</td>
		</tr>
	</tbody>
</table>