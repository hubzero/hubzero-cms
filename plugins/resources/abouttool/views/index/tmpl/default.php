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
defined('_JEXEC') or die('Restricted access');

if ($this->resource->alias) {
	$sef = JRoute::_('index.php?option=' . $this->option . '&alias=' . $this->resource->alias);
} else {
	$sef = JRoute::_('index.php?option=' . $this->option . '&id=' . $this->resource->id);
}

// Set the display date
switch ($this->params->get('show_date'))
{
	case 0: $thedate = ''; break;
	case 1: $thedate = $this->resource->created;    break;
	case 2: $thedate = $this->resource->modified;   break;
	case 3: $thedate = $this->resource->publish_up; break;
}
if ($this->curtool) 
{
	$thedate = $this->curtool->released;
}

$dateFormat = '%d %b %Y';
$yearFormat = '%Y';
$timeFormat = '%I:%M %p';
$tz = 0;
if (version_compare(JVERSION, '1.6', 'ge'))
{
	$dateFormat = 'd M Y';
	$yearFormat = 'Y';
	$timeFormat = 'h:M a';
	$tz = true;
}

$this->resource->introtext = stripslashes($this->resource->introtext);
//$this->resource->fulltxt = stripslashes($this->resource->fulltxt);
$this->resource->fulltxt = ($this->resource->fulltxt) ? trim($this->resource->fulltxt) : trim($this->resource->introtext);

// Parse for <nb: > tags
$type = new ResourcesType($this->database);
$type->load($this->resource->type);

$data = array();
preg_match_all("#<nb:(.*?)>(.*?)</nb:(.*?)>#s", $this->resource->fulltxt, $matches, PREG_SET_ORDER);
if (count($matches) > 0) 
{
	foreach ($matches as $match)
	{
		$data[$match[1]] = $match[2];
	}
}
$this->resource->fulltxt = preg_replace("#<nb:(.*?)>(.*?)</nb:(.*?)>#s", '', $this->resource->fulltxt);
$this->resource->fulltxt = trim($this->resource->fulltxt);

include_once(JPATH_ROOT . DS . 'components' . DS . 'com_resources' . DS . 'models' . DS . 'elements.php');
$elements = new ResourcesElements($data, $type->customFields);
$schema = $elements->getSchema();

// Set the document description
if ($this->resource->introtext) 
{
	$document =& JFactory::getDocument();
	$document->setDescription(ResourcesHtml::encode_html(strip_tags($this->resource->introtext)));
}

// Check if there's anything left in the fulltxt after removing custom fields
// If not, set it to the introtext
$maintext = $this->resource->fulltxt;
$maintext = preg_replace('/&(?!(?i:\#((x([\dA-F]){1,5})|(104857[0-5]|10485[0-6]\d|1048[0-4]\d\d|104[0-7]\d{3}|10[0-3]\d{4}|0?\d{1,6}))|([A-Za-z\d.]{2,31}));)/i',"&amp;",$maintext);
$maintext = str_replace('<blink>', '', $maintext);
$maintext = str_replace('</blink>', '', $maintext);

if (preg_match("/([\<])([^\>]{1,})*([\>])/i", $maintext)) {
		// Do nothing
} else {
		// Get the wiki parser and parse the full description
		$wikiconfig = array(
			'option'   => $this->option,
			'scope'    => 'resources' . DS . $this->resource->id,
			'pagename' => 'resources',
			'pageid'   => $this->resource->id,
			'filepath' => $this->config->get('uploadpath'),
			'domain'   => ''
		);
		ximport('Hubzero_Wiki_Parser');
		$p =& Hubzero_Wiki_Parser::getInstance();
		$maintext = $p->parse($maintext, $wikiconfig);
}
?>
<div class="subject abouttab">
<?php
if ($this->resource->revision == 'dev' or !$this->resource->toolpublished) {
	$shots = null;
} else {
	// Screenshots
	$ss = new ResourcesScreenshot($this->database);
	$shots = ResourcesHtml::screenshots($this->resource->id, $this->resource->created, $this->config->get('uploadpath'), $this->config->get('uploadpath'), $this->resource->versionid, $ss->getScreenshots($this->resource->id, $this->resource->versionid), 1);
}
if ($shots) {
?>
	<div class="sscontainer">
		<?php echo $shots; ?>
	</div><!-- / .sscontainer -->
<?php
}
?>
	<div class="resource">
		<div class="two columns first">
			<h4><?php echo JText::_('Category'); ?></h4>
			<p class="resource-content">
				<a href="<?php echo JRoute::_('index.php?option=' . $this->option . '&type=' . $this->resource->_type->alias); ?>">
					<?php echo $this->escape(stripslashes($this->resource->_type->type)); ?>
				</a>
			</tp>
		</div>
		<div class="two columns second">
			<h4><?php echo JText::_('Published on'); ?></h4>
			<p class="resource-content">
				<time datetime="<?php echo $thedate; ?>"><?php echo JHTML::_('date', $thedate, $dateFormat, $tz); ?></time>
			</p>
		</div>
		<div class="clearfix"></div>

<?php if ($this->resource->revision == 'dev' or !$this->resource->toolpublished) { ?>
			<h4><?php echo JText::_('PLG_RESOURCES_ABOUT_ABSTRACT'); ?></h4>
			<div class="resource-content">
				<?php echo $maintext; ?>
			</div>
<?php } else if ($this->resource->access == 3 && (!in_array($this->resource->group_owner, $usersgroups) || !$this->authorized)) {
	// Protected - only show the introtext
?>
			<h4><?php echo JText::_('PLG_RESOURCES_ABOUT_ABSTRACT'); ?></h4>
			<div class="resource-content">
				<?php echo $this->escape($introtext); ?>
			</div>
<?php
} else {
	if (trim($maintext)) {
?>
			<h4><?php echo JText::_('PLG_RESOURCES_ABOUT_ABSTRACT'); ?></h4>
			<div class="resource-content">
				<?php echo $maintext; ?>
			</div>
<?php
	}
	$this->helper->getSubmitters(true, 1, $this->plugin->get('badges', 0));
	if ($this->helper->contributors && $this->helper->contributors != '<br />') {
?>
			<h4><?php echo JText::_('PLG_RESOURCES_ABOUT_CONTRIBUTOR'); ?></th>
			<div class="resource-content">
				<span id="authorslist">
					<?php echo $this->helper->contributors; ?>
				</span>
			</div>
<?php
	}
	$citations = '';
	foreach ($schema->fields as $field)
	{
		if (isset($data[$field->name])) {
			if ($field->name == 'citations') {
				$citations = $data[$field->name];
			} else if ($value = $elements->display($field->type, $data[$field->name])) {
?>
			<h4><?php echo $field->label; ?></h4>
			<div class="resource-content">
				<?php echo $value; ?>
			</div>
<?php
			}
		}
	}

	if ($this->params->get('show_citation')) {
		if ($this->params->get('show_citation') == 1 || $this->params->get('show_citation') == 2) {
			// Citation instructions
			$this->helper->getUnlinkedContributors();

			// Build our citation object
			$juri =& JURI::getInstance();
			
			$cite = new stdClass();
			$cite->title = $this->resource->title;
			$cite->year = JHTML::_('date', $thedate, $yearFormat, $tz);
			$cite->location = $juri->base() . ltrim($sef, DS);
			$cite->date = date("Y-m-d H:i:s");

			$cite->url = '';
			$cite->type = '';
			$cite->author = $this->helper->ul_contributors;
			
			// Get contribtool params
			$tconfig =& JComponentHelper::getParams( 'com_tools' );
			$doi = '';

			if (isset($this->resource->doi) && $this->resource->doi && $tconfig->get('doi_shoulder'))
			{
				$doi = $tconfig->get('doi_shoulder') . DS . strtoupper($this->resource->doi);
			}
			else if (isset($this->resource->doi_label) && $this->resource->doi_label)
			{
				$doi = '10254/' . $tconfig->get('doi_prefix') . $this->resource->id . '.' . $this->resource->doi_label;
			}

			if ($doi)
			{
				$cite->doi = $doi;
			}
			
			if ($this->params->get('show_citation') == 2) {
				$citations = '';
			}
		} else {
			$cite = null;
		}

		$this->helper->getUnlinkedContributors();
		
		$revision = isset($this->resource->revision) ? $this->resource->revision : '';
		$citeinstruct  = ResourcesHtml::citation($this->option, $cite, $this->resource->id, $citations, $this->resource->type, $revision);
		$citeinstruct .= ResourcesHtml::citationCOins($cite, $this->resource, $this->config, $this->helper);
?>
			<h4><a name="citethis"></a><?php echo JText::_('PLG_RESOURCES_ABOUT_CITE_THIS'); ?></h4>
			<div class="resource-content">
				<?php echo $citeinstruct; ?>
			</div>
<?php
	}
}
// If the resource had a specific event date/time
/*if ($this->attribs->get('timeof', '')) {
	if (substr($this->attribs->get('timeof', ''), -8, 8) == '00:00:00') {
		$exp = $dateFormat; //'%B %d %Y';
	} else {
		$exp = $timeFormat . ', ' . $dateFormat; //'%I:%M %p, %B %d %Y';
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
}*/
// Tags
if (!$this->thistool && $this->revision != 'dev') {
	if ($this->params->get('show_assocs')) {
		$tagCloud = $this->helper->getTagCloud($this->authorized);

		if ($tagCloud) {
?>
			<h4><?php echo JText::_('PLG_RESOURCES_ABOUT_TAGS'); ?></h4>
			<div class="resource-content">
				<?php echo $tagCloud; ?>
			</div>
<?php
		}
	}
}
?>
	</div><!-- / .resource -->
</div><!-- / .subject -->
<div class="clear"></div>