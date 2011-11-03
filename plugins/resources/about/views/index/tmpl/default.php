<?php
/**
 * @package     hubzero-cms
 * @author      Shawn Rice <zooley@purdue.edu>
 * @copyright   Copyright 2005-2011 Purdue University. All rights reserved.
 * @license     http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
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
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

$sef = JRoute::_('index.php?option=' . $this->option . '&id=' . $this->resource->id);

// Set the display date
switch ($this->params->get('show_date'))
{
	case 0: $thedate = ''; break;
	case 1: $thedate = $this->resource->created;    break;
	case 2: $thedate = $this->resource->modified;   break;
	case 3: $thedate = $this->resource->publish_up; break;
}

// Prepare/parse text
$introtext = stripslashes($this->resource->introtext);
$maintext  = ($this->resource->fulltext)
		   ? stripslashes($this->resource->fulltext)
		   : stripslashes($this->resource->introtext);

//$maintext = stripslashes($maintext);

if ($introtext) {
	$document =& JFactory::getDocument();
	$document->setDescription(ResourcesHtml::encode_html(strip_tags($introtext)));
}

// Parse for <nb: > tags
$type = new ResourcesType($this->database);
$type->load($this->resource->type);

$fields = array();
if (trim($type->customFields) != '') {
	$fs = explode("\n", trim($type->customFields));
	foreach ($fs as $f)
	{
		$fields[] = explode('=', $f);
	}
} else {
	$flds = $this->config->get('tagstool');
	$flds = explode(',', $flds);
	foreach ($flds as $fld)
	{
		$fields[] = array($fld, $fld, 'textarea', 0);
	}
}

if (!empty($fields)) {
	for ($i=0, $n=count($fields); $i < $n; $i++)
	{
		// Explore the text and pull out all matches
		array_push($fields[$i], ResourcesHtml::parseTag($maintext, $fields[$i][0]));

		// Clean the original text of any matches
		$maintext = str_replace('<nb:' . $fields[$i][0] . '>' . end($fields[$i]) . '</nb:' . $fields[$i][0] . '>', '', $maintext);
	}
	$maintext = trim($maintext);
}

$maintext = ($maintext) ? stripslashes($maintext) : stripslashes(trim($this->resource->introtext));
$maintext = preg_replace('/&(?!(?i:\#((x([\dA-F]){1,5})|(104857[0-5]|10485[0-6]\d|1048[0-4]\d\d|104[0-7]\d{3}|10[0-3]\d{4}|0?\d{1,6}))|([A-Za-z\d.]{2,31}));)/i',"&amp;",$maintext);
$maintext = str_replace('<blink>', '', $maintext);
$maintext = str_replace('</blink>', '', $maintext);
?>
<div class="subject abouttab">
	<table class="resource" summary="<?php echo JText::_('RESOURCE_TBL_SUMMARY'); ?>">
		<tbody>
			<tr>
				<th><?php echo JText::_('Category'); ?></th>
				<td><a href="<?php echo JRoute::_('index.php?option=' . $this->option . '&type=' . $this->resource->_type->alias); ?>"><?php echo $this->resource->_type->type; ?></a></td>
			</tr>
<?php
// Check how much we can display
if ($this->resource->access == 3 && (!in_array($this->resource->group_owner, $this->usersgroups) || !$this->authorized)) {
	// Protected - only show the introtext
?>
			<tr>
				<th><?php echo ''; ?></th>
				<td><?php echo $this->escape($introtext); ?></td>
			</tr>
<?php
} else {
?>
			<tr>
				<th><?php echo JText::_('PLG_RESOURCES_ABOUT_ABSTRACT'); ?></th>
				<td><?php echo $maintext; ?></td>
			</tr>
<?php
$name = JText::_('PLG_RESOURCES_ABOUT_ANONYMOUS');
if ($this->resource->created_by) {
	$xuser =& JUser::getInstance($this->resource->created_by);
	if (is_object($xuser) && $xuser->get('name')) {
		$name  = '<a href="' . JRoute::_('index.php?option=com_members&id=' . $this->resource->created_by) . '">' . stripslashes($xuser->get('name')) . '</a>';
		/*$types = array('manager', 'administrator', 'super administrator', 'publisher', 'editor');
		if (in_array(strtolower($xuser->usertype), $types)) {
			$name .= ' <span class="user-badges"><span>' . str_replace(' ', '-', strtolower($xuser->usertype)) . '</span></span>';
		}*/
		$types = array(23 => 'manager', 24 => 'administrator', 25 => 'super administrator', 21 => 'publisher', 20 => 'editor');
		if (isset($types[$xuser->gid])) {
			$name .= ' <ul class="badges"><li>' . str_replace(' ', '-', $types[$xuser->gid]) . '</li></ul>';
		}
	}
}
?>
			<tr>
				<th><?php echo JText::_('PLG_RESOURCES_ABOUT_CONTRIBUTOR'); ?></th>
				<td><?php echo $name; ?></td>
			</tr>
<?php
	$citations = '';
	foreach ($fields as $field)
	{
		if (end($field) != NULL) {
			if ($field[0] == 'citations') {
				$citations = end($field);
			} else {
?>
			<tr>
				<th><?php echo $field[1]; ?></th>
				<td><?php echo end($field); ?></td>
			</tr>
<?php
			}
		}
	}

	if ($this->params->get('show_citation')) {
		if ($this->params->get('show_citation') == 1 || $this->params->get('show_citation') == 2) {
			// Citation instructions
			$this->helper->getUnlinkedContributors();

			// Build our citation object
			$cite = new stdClass();
			$cite->title = $this->resource->title;
			$cite->year = JHTML::_('date', $thedate, '%Y');
			$juri =& JURI::getInstance();
			if (substr($sef,0,1) == '/') {
				$sef = substr($sef, 1, strlen($sef));
			}
			$cite->location = $juri->base() . $sef;
			$cite->date = date( "Y-m-d H:i:s" );
			$cite->url = '';
			$cite->type = '';
			$cite->author = $this->helper->ul_contributors;
			if (isset($this->resource->doi)) {
				$cite->doi = $this->config->get('doi').'r'.$this->resource->id.'.'.$this->resource->doi;
			}

			if ($this->params->get('show_citation') == 2) {
				$citations = '';
			}
		} else {
			$cite = null;
		}

		$citeinstruct  = ResourcesHtml::citation( $this->option, $cite, $this->resource->id, $citations, $this->resource->type );
		$citeinstruct .= ResourcesHtml::citationCOins($cite, $this->resource, $this->config, $this->helper);
?>
			<tr>
				<th><a name="citethis"></a><?php echo JText::_('PLG_RESOURCES_ABOUT_CITE_THIS'); ?></th>
				<td><?php echo $citeinstruct; ?></td>
			</tr>
<?php
	}
}
// If the resource had a specific event date/time
if ($this->attribs->get('timeof', '')) {
	if (substr($this->attribs->get('timeof', ''), -8, 8) == '00:00:00') {
		$exp = '%B %d, %Y';
	} else {
		$exp = '%I:%M %p, %B %d, %Y';
	}
	if (substr($this->attribs->get('timeof', ''), 4, 1) == '-') {
		$seminarTime = ($this->attribs->get('timeof', '') != '0000-00-00 00:00:00' || $this->attribs->get('timeof', '') != '')
					  ? JHTML::_('date', $this->attribs->get('timeof', ''), $exp)
					  : '';
	} else {
		$seminarTime = $this->attribs->get('timeof', '');
	}
?>
			<tr>
				<th><?php echo JText::_('PLG_RESOURCES_ABOUT_TIME'); ?></th>
				<td><?php echo $this->escape($seminarTime); ?></td>
			</tr>
<?php
}
// If the resource had a specific location
if ($this->attribs->get('location', '')) {
?>
			<tr>
				<th><?php echo JText::_('PLG_RESOURCES_ABOUT_LOCATION'); ?></th>
				<td><?php echo $this->escape($this->attribs->get('location', '')); ?></td>
			</tr>
<?php
}
// Tags
if ($this->params->get('show_assocs')) {
	$tagCloud = $this->helper->getTagCloud($this->authorized);

	if ($tagCloud) {
?>
			<tr>
				<th><?php echo JText::_('PLG_RESOURCES_ABOUT_TAGS'); ?></th>
				<td><?php echo $tagCloud; ?></td>
			</tr>
<?php
	}
}
?>
		</tbody>
	</table>
</div><!-- / .subject -->
<div class="clear"></div>
<input type="hidden" name="rid" id="rid" value="<?php echo $this->resource->id; ?>" />