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

$this->css();

$sef = JRoute::_('index.php?option=' . $this->option . '&' . ($this->model->resource->alias ? 'alias=' . $this->model->resource->alias : 'id=' . $this->model->resource->id));

// Set the display date
switch ($this->model->params->get('show_date'))
{
	case 0: $thedate = ''; break;
	case 1: $thedate = $this->model->resource->created;    break;
	case 2: $thedate = $this->model->resource->modified;   break;
	case 3: $thedate = $this->model->resource->publish_up; break;
}
/*
if ($this->curtool)
{
	$thedate = $this->curtool->released;
}
*/

$this->model->resource->introtext = stripslashes($this->model->resource->introtext);
$this->model->resource->fulltxt = stripslashes($this->model->resource->fulltxt);
$this->model->resource->fulltxt = ($this->model->resource->fulltxt) ? trim($this->model->resource->fulltxt) : trim($this->model->resource->introtext);

// Parse for <nb:field> tags
$type = new ResourcesType($this->database);
$type->load($this->model->resource->type);

$data = array();

preg_match_all("#<nb:(.*?)>(.*?)</nb:(.*?)>#s", $this->model->resource->fulltxt, $matches, PREG_SET_ORDER);
if (count($matches) > 0)
{
	foreach ($matches as $match)
	{
		$data[$match[1]] = $match[2];
	}
}
$this->model->resource->fulltxt = preg_replace("#<nb:(.*?)>(.*?)</nb:(.*?)>#s", '', $this->model->resource->fulltxt);
$this->model->resource->fulltxt = trim($this->model->resource->fulltxt);

include_once(JPATH_ROOT . DS . 'components' . DS . 'com_resources' . DS . 'models' . DS . 'elements.php');
$elements = new ResourcesElements($data, $this->model->type->customFields);
$schema = $elements->getSchema();

// Set the document description
if ($this->model->resource->introtext)
{
	$document = JFactory::getDocument();
	$document->setDescription(strip_tags($this->model->resource->introtext));
}

// Check if there's anything left in the fulltxt after removing custom fields
// If not, set it to the introtext
$maintext = $this->model->description('parsed');
?>
<div class="subject abouttab">
	<?php
	if ($this->model->resource->revision == 'dev' or !$this->model->resource->toolpublished) {
		//$shots = null;
	} else {
		// Screenshots
		$ss = new ResourcesScreenshot($this->database);

		$this->view('_screenshots')
		     ->set('id', $this->model->resource->id)
		     ->set('created', $this->model->resource->created)
		     ->set('upath', $this->model->params->get('uploadpath'))
		     ->set('wpath', $this->model->params->get('uploadpath'))
		     ->set('versionid', $this->model->resource->versionid)
		     ->set('sinfo', $ss->getScreenshots($this->model->resource->id, $this->model->resource->versionid))
		     ->set('slidebar', 1)
		     ->display();
		?>
	<?php } ?>

	<div class="resource">
	<?php if ($thedate) { ?>
		<div class="grid">
			<div class="col span-half">
	<?php } ?>
				<h4><?php echo JText::_('PLG_RESOURCES_ABOUT_CATEGORY'); ?></h4>
				<p class="resource-content">
					<a href="<?php echo JRoute::_('index.php?option=' . $this->option . '&type=' . $this->model->type->alias); ?>">
						<?php echo $this->escape(stripslashes($this->model->type->type)); ?>
					</a>
				</p>
	<?php if ($thedate) { ?>
		</div>
			<div class="col span-half omega">
				<h4><?php echo JText::_('PLG_RESOURCES_ABOUT_PUBLISHED_ON'); ?></h4>
				<p class="resource-content">
					<time datetime="<?php echo $thedate; ?>"><?php echo JHTML::_('date', $thedate, JText::_('DATE_FORMAT_HZ1')); ?></time>
				</p>
			</div>
		</div>
	<?php } ?>

	<?php if (!$this->model->resource->toolpublished) { ?>
			<h4><?php echo JText::_('PLG_RESOURCES_ABOUT_ABSTRACT'); ?></h4>
			<div class="resource-content">
				<?php echo $maintext; ?>
			</div>
	<?php } else if ($this->model->resource->access == 3 && (!in_array($this->model->resource->group_owner, $usersgroups) || !$this->authorized)) { // Protected - only show the introtext ?>
			<h4><?php echo JText::_('PLG_RESOURCES_ABOUT_ABSTRACT'); ?></h4>
			<div class="resource-content">
				<?php echo $this->escape($introtext); ?>
			</div>
	<?php } else { ?>
		<?php if (trim($maintext)) { ?>
			<h4><?php echo JText::_('PLG_RESOURCES_ABOUT_ABSTRACT'); ?></h4>
			<div class="resource-content">
				<?php echo $maintext; ?>
			</div>
		<?php } ?>

		<?php
		$citations = '';
		if (!isset($schema->fields) || !is_array($schema->fields))
		{
			$schema->fields = array();
		}
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

		if ($this->model->params->get('show_citation'))
		{
			if ($this->model->params->get('show_citation') == 1 || $this->model->params->get('show_citation') == 2)
			{
				// Citation instructions
				//$this->helper->getUnlinkedContributors();

				// Build our citation object
				$juri = JURI::getInstance();

				$cite = new stdClass();
				$cite->title = $this->model->resource->title;
				$cite->year = JHTML::_('date', $thedate, 'Y');
				$cite->location = $juri->base() . ltrim($sef, DS);
				$cite->date = JFactory::getDate()->toSql();

				$cite->url = '';
				$cite->type = '';
				$authors = array();
				$contributors = $this->model->contributors('tool');
				if ($contributors)
				{
					foreach ($contributors as $contributor)
					{
						$authors[] = $contributor->name ? $contributor->name : $contributor->xname;
					}
				}
				$cite->author = implode(';', $authors);

				// Get contribtool params
				$tconfig = JComponentHelper::getParams( 'com_tools' );
				$doi = '';

				if (isset($this->model->resource->doi) && $this->model->resource->doi && $tconfig->get('doi_shoulder'))
				{
					$doi = $tconfig->get('doi_shoulder') . DS . strtoupper($this->model->resource->doi);
				}
				else if (isset($this->model->resource->doi_label) && $this->model->resource->doi_label)
				{
					$doi = '10254/' . $tconfig->get('doi_prefix') . $this->model->resource->id . '.' . $this->model->resource->doi_label;
				}

				if ($doi)
				{
					$cite->doi = $doi;
				}

				if ($this->model->params->get('show_citation') == 2) {
					$citations = '';
				}
			}
			else
			{
				$cite = null;
			}

			//$this->helper->getUnlinkedContributors();

			$revision = isset($this->model->resource->revision) ? $this->model->resource->revision : '';
			$citeinstruct  = ResourcesHtml::citation($this->option, $cite, $this->model->resource->id, $citations, $this->model->resource->type, $revision);
			$citeinstruct .= ResourcesHtml::citationCOins($cite, $this->model); //->resource, $this->model->params, $this->helper);
			?>
			<h4><?php echo JText::_('PLG_RESOURCES_ABOUT_CITE_THIS'); ?></h4>
			<div class="resource-content">
				<?php echo $citeinstruct; ?>
			</div>
			<?php
		}
		?>
	<?php } ?>

	<?php if ($this->model->contributors('submitter')) { ?>
		<h4><?php echo JText::_('PLG_RESOURCES_ABOUT_CONTRIBUTOR'); ?></h4>
		<div class="resource-content">
			<div id="submitterlist">
				<?php
				$view = new \Hubzero\Component\View(array(
					'base_path' => JPATH_ROOT . DS . 'components' . DS . 'com_resources',
					'name'      => 'view',
					'layout'    => '_submitters',
				));
				$view->option       = $this->option;
				$view->contributors = $this->model->contributors('submitter');
				$view->badges       = $this->params->get('badges', 0);
				$view->showorgs     = 1;
				$view->display();
				?>
			</div>
		</div>
	<?php } ?>

	<?php
	if ($this->model->params->get('show_assocs'))
	{
		if ($tags = $this->model->tags())
		{
			$tagger = new ResourcesTags($this->database);
			?>
			<h4><?php echo JText::_('PLG_RESOURCES_ABOUT_TAGS'); ?></h4>
			<div class="resource-content">
				<?php echo $tagger->buildCloud($tags); ?>
			</div>
			<?php
		}
	}
	?>
	</div><!-- / .resource -->
</div><!-- / .subject -->