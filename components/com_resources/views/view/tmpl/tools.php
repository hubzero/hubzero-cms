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
 * @copyright Copyright 2005-2011 Purdue University. All rights reserved.
 * @license   GNU General Public License, version 2 (GPLv2) 
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

$dateFormat = '%d %b %Y';
$tz = null;

if (version_compare(JVERSION, '1.6', 'ge'))
{
	$dateFormat = 'd M Y';
	$tz = false;
}

$txt = '';
$mode = strtolower(JRequest::getWord('mode', ''));

if ($mode != 'preview')
{
	switch ($this->model->resource->published)
	{
		case 1: $txt .= ''; break; // published
		case 2: $txt .= '<span>[' . JText::_('COM_RESOURCES_DRAFT_EXTERNAL') . ']</span> '; break;  // external draft
		case 3: $txt .= '<span>[' . JText::_('COM_RESOURCES_PENDING') . ']</span> ';        break;  // pending
		case 4: $txt .= '<span>[' . JText::_('COM_RESOURCES_DELETED') . ']</span> ';        break;  // deleted
		case 5: $txt .= '<span>[' . JText::_('COM_RESOURCES_DRAFT_INTERNAL') . ']</span> '; break;  // internal draft
		case 0; $txt .= '<span>[' . JText::_('COM_RESOURCES_UNPUBLISHED') . ']</span> ';    break;  // unpublished
	}
}

	/* Tool page view  */

	$tconfig 		= $this->tconfig;

	//$usersgroups 	= $this->usersgroups;
	$helper 		= $this->helper;
	$thistool 		= $this->thistool;
	$curtool 		= $this->curtool;
	$alltools 		= $this->alltools;
	$revision 		= $this->revision;

	$juser =& JFactory::getUser();

?>
		<div class="main section upperpane">
			<div class="aside rankarea">
<?php 
	// Show resource ratings
	if (!$thistool) 
	{
		if ($this->model->params->get('show_metadata', 1)) 
		{
			$view = new JView(array(
				'name'   => 'view',
				'layout' => '_metadata',
			));
			$view->option   = $this->option;
			$view->sections = $this->sections;
			$view->model    = $this->model;
			$view->display();
		}
	} 
	else if ($revision == 'dev' or !$this->model->resource->toolpublished) 
	{
?>
				<div class="metaplaceholder">
					<p>
						<?php echo ($revision=='dev') 
								? JText::_('This section will be filled when this tool version gets published.') 
								: JText::_('This section is unavailable in an archive version of a tool.');

						if (isset($this->model->resource->curversion) && $this->model->resource->curversion) 
						{
							echo ' '.JText::_('Consult the latest published version').' <a href="'.JRoute::_('index.php?option='.$this->option.'&id='.$this->model->resource->id.'&rev='.$curtool->revision).'">'.$this->model->resource->curversion.'</a> '.JText::_('for most current information.');
						}
						?>
					</p>
				</div>
<?php
	}
?>
			</div><!-- / .aside -->

			<div class="subject">
				<div class="overviewcontainer">
					<div id="content-header">
						<h2>
							<?php echo $txt . $this->escape(stripslashes($this->model->resource->title)); ?>
							<?php 
								if ($this->model->params->get('access-edit-resource')) 
								{ 
							?>
								<a class="edit button" href="<?php echo JRoute::_('index.php?option=com_tools&task=resource&step=1&app=' . $this->model->resource->alias); ?>"><?php echo JText::_('EDIT'); ?></a>
							<?php 
								} // if ($this->model->params->get('access-edit-resource')) 
							?>
						</h2>
						<input type="hidden" name="rid" id="rid" value="<?php echo $this->model->resource->id; ?>" />
					</div>
<?php
	// Display authors
	if ($this->model->params->get('show_authors', 1)) 
	{
			// Get contributors of this version		
			/*if ($alltools && $this->model->resource->revision!='dev') {
				$helper->getToolAuthors($this->model->resource->alias, $this->model->resource->revision);
			}*/
?>
					<div id="authorslist">
						<?php
						$view = new JView(array(
							'name'   => 'view',
							'layout' => '_contributors',
						));
						$view->option = $this->option;
						$view->contributors = $this->model->contributors('tool');
						$view->display();
						?>
					</div>
<?php
	}
?>
					<p class="ataglance">
						<?php echo $this->model->resource->introtext 
								? Hubzero_View_Helper_Html::shortenText(stripslashes($this->model->resource->introtext), 255, 0) 
								: Hubzero_View_Helper_Html::shortenText(stripslashes($this->model->resource->fulltxt), 255, 0);
						?>
					</p>

				</div><!-- / .overviewcontainer -->

				<div class="aside launcharea">
<?php

	// Private/Public resource access check
	if (!$this->model->access('view-all')) 
	{
		$ghtml = array();
		foreach ($this->model->resource->getGroups() as $allowedgroup)
		{
			$ghtml[] = '<a href="' . JRoute::_('index.php?option=com_groups&cn=' . $allowedgroup) . '">' . $allowedgroup . '</a>';
		}
?>
					<p class="warning">
						<?php echo JText::_('COM_RESOURCES_ERROR_MUST_BE_PART_OF_GROUP') . ' ' . implode(', ', $ghtml); ?>
					</p>
<?php
	} 
	else 
	{
		// get launch button
		//$helper->getFirstChild();
		$firstChild = $this->model->children(0);
		echo ResourcesHtml::primary_child($this->option, $this->model->resource, $firstChild, '');

		$html = '';

		// Display version info
		$versiontext = '<strong>';
		if ($revision && $thistool) {
			$versiontext .= $thistool->version.'</strong>';
			if ($this->model->resource->revision!='dev') {
				$versiontext .=  '<br /> '.ucfirst(JText::_('COM_RESOURCES_PUBLISHED_ON')).' ';
				$versiontext .= ($thistool->released && $thistool->released != '0000-00-00 00:00:00') ? JHTML::_('date', $thistool->released, $dateFormat, $tz): JHTML::_('date', $this->model->resource->publish_up, $dateFormat, $tz);
				$versiontext .= ($thistool->unpublished && $thistool->unpublished != '0000-00-00 00:00:00') ? ', '.JText::_('COM_RESOURCES_UNPUBLISHED_ON').' '.JHTML::_('date', $thistool->unpublished, $dateFormat, $tz): '';
			} else {
				$versiontext .= ' ('.JText::_('COM_RESOURCES_IN_DEVELOPMENT').')';
			}
		} else if ($curtool) {
			$versiontext .= $curtool->version.'</strong> - '.JText::_('COM_RESOURCES_PUBLISHED_ON').' ';
			$versiontext .= ($curtool->released && $curtool->released != '0000-00-00 00:00:00') ? JHTML::_('date', $curtool->released, $dateFormat, $tz): JHTML::_('date', $this->model->resource->publish_up, $dateFormat, $tz);
		}

		if (!$thistool) 
		{
			$html .= "\t\t\t\t".'<p class="curversion">'.JText::_('COM_RESOURCES_VERSION').' '.$versiontext.'</p>'."\n";
		} 
		else if ($revision == 'dev') 
		{
			$html .= "\t\t\t\t".'<p class="devversion">'.JText::_('COM_RESOURCES_VERSION').' '.$versiontext;
			$html .= $this->model->resource->toolpublished ? ' <span>'.JText::_('View').' <a href="'.JRoute::_('index.php?option='.$this->option.'&id='.$this->model->resource->id.'&active=versions').'">'.JText::_('other versions').'</a></span>' : '';
			$html .='</p>'."\n";
		} 
		else 
		{
			// Show archive message		
			$msg = '<strong>'.JText::_('COM_RESOURCES_ARCHIVE').'</strong> '.JText::_('COM_RESOURCES_VERSION').' '.$versiontext;
			if (isset($this->model->resource->curversion) && $this->model->resource->curversion) {
				$msg .= ' <br />'.JText::_('COM_RESOURCES_LATEST_VERSION').': <a href="'.JRoute::_('index.php?option='.$this->option.'&id='.$this->model->resource->id.'&rev='.$curtool->revision).'">'.$this->model->resource->curversion.'</a>.';
			}
			$msg .= ' <a href="'.JRoute::_('index.php?option='.$this->option.'&id='.$this->model->resource->id.'&active=versions').'">'.JText::_('All versions').'</a>';
			$html .= ResourcesHtml::archive($msg)."\n";
		}

		// doi message
		if ($revision != 'dev' && ($this->model->resource->doi || $this->model->resource->doi_label)) {
			if($this->model->resource->doi && $tconfig->get('doi_shoulder'))
			{
				$doi = 'doi:' . $tconfig->get('doi_shoulder') . DS . strtoupper($this->model->resource->doi);
			}
			else
			{
				$doi = 'doi:10254/' . $tconfig->get('doi_prefix') . $this->model->resource->id . '.' . $this->model->resource->doi_label;
			}
			
			$html .= "\t\t".'<p class="doi">'.$doi.' <span><a href="'.JRoute::_('index.php?option='.$this->option.'&id='.$this->model->resource->id.'&active=about').'#citethis">'.JText::_('cite this').'</a></span></p>'."\n";
		}

		// Open/closed source
		if (isset($this->model->resource->toolsource) && $this->model->resource->toolsource == 1 && isset($this->model->resource->tool)) { // open source
			$html .= '<p class="opensource_license">'.JText::_('Open source').': <a class="popup" href="index.php?option='.$this->option.'&task=license&tool='.$this->model->resource->tool.'&no_html=1">license</a> ';
			$html .= ($this->model->resource->taravailable) ? ' |  <a href="index.php/'.$this->model->resource->tarname.'?option='.$this->option.'&task=sourcecode&tool='.$this->model->resource->tool.'">'.JText::_('download').'</a> '."\n" : ' | <span class="unavail">'.JText::_('code unavaialble').'</span>'."\n";
			$html .= '</p>'."\n";
		} elseif (isset($this->model->resource->toolsource) && !$this->model->resource->toolsource) { // closed source, archive page
			$html .= '<p class="closedsource_license">'.JText::_('COM_RESOURCES_TOOL_IS_CLOSED_SOURCE').'</p>'."\n";
		}
		// do we have a first-time user guide?
		$helper->getChildren($this->model->resource->id, 0, 'all');
		$children = $helper->children;

		if (!$thistool) {
			$guide = 0;
			foreach ($children as $child)
			{
				$title = ($child->logicaltitle)
						? $child->logicaltitle
						: stripslashes($child->title);
				if ($child->access == 0 || ($child->access == 1 && !$juser->get('guest'))) {
					if (strtolower($title) !=  preg_replace('/user guide/', '', strtolower($title))) {
						$guide = $child;
					}
				}
			}
			$url = $guide ? ResourcesHtml::processPath($this->option, $guide, $this->model->resource->id) : '';
			$html .= "\t\t".'<p class="supdocs">'."\n";
			if ($url) {
				$html .= "\t\t\t".'<span><span class="guide"><a href="'.$url.'">'.JText::_('First-Time User Guide').'</a></span></span>'."\n";
			}
			$html .= "\t\t\t".'<span class="viewalldocs"><a href="'.JRoute::_('index.php?option='.$this->option.'&id='.$this->model->resource->id.'&active=supportingdocs').'">'.JText::_('View All Supporting Documents').'</a></span>'."\n";
			$html .= "\t\t".'</p>'."\n";
		}
		
		echo $html;
	} // --- end else (if group check passed)
?>
				</div><!-- / .aside launcharea -->
			</div><!-- / .subject -->
<?php
	if (!$this->model->access('view-all')) 
	{ // show nothing else 
?>
		
		</div><!-- / .main section -->
<?php 
	} 
	else 
	{
?>
			<div class="clear sep"></div>
		</div><!-- / .main section -->
		
		<div class="main section noborder">
			<div class="aside extracontent">

<?php
		// Get Releated Resources plugin
		JPluginHelper::importPlugin('resources', 'related');
		$dispatcher =& JDispatcher::getInstance();

		// Show related content
		$out = $dispatcher->trigger('onResourcesSub', array($this->model->resource, $this->option, 1));
		if (count($out) > 0) 
		{
			foreach ($out as $ou)
			{
				if (isset($ou['html'])) 
				{
					echo $ou['html'];
				}
			}
		}

		// Show what's popular
		if ($this->tab == 'about') 
		{
			ximport('Hubzero_Module_Helper');
			echo Hubzero_Module_Helper::renderModules('extracontent');
		}
?>
			</div><!-- / .aside extracontent -->

			<div class="subject tabbed">
				<?php echo ResourcesHtml::tabs($this->option, $this->model->resource->id, $this->cats, $this->tab, $this->model->resource->alias); ?>
				<?php echo ResourcesHtml::sections($this->sections, $this->cats, $this->tab, 'hide', 'main'); ?>
			</div><!-- / .subject -->
			<div class="clear"></div>
		</div><!-- / .main section -->
<?php
	}
?>
		<div class="clear"></div>
