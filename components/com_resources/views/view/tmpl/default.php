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

$this->css()
     ->js();

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

$juser = JFactory::getUser();
?>
<section class="main section upperpane">
	<div class="subject">
		<div class="grid overviewcontainer">
			<div class="col span8">
				<header id="content-header">
					<h2>
						<?php echo $txt . $this->escape(stripslashes($this->model->resource->title)); ?>
						<?php if ($this->model->params->get('access-edit-resource')) { ?>
							<a class="icon-edit edit btn" href="<?php echo JRoute::_('index.php?option=com_resources&task=draft&step=1&id=' . $this->model->resource->id); ?>">
								<?php echo JText::_('COM_RESOURCES_EDIT'); ?>
							</a>
						<?php } ?>
					</h2>
					<input type="hidden" name="rid" id="rid" value="<?php echo $this->model->resource->id; ?>" />
				</header>

				<?php if ($this->model->params->get('show_authors', 1)) { ?>
					<div id="authorslist">
						<?php
						// Display authors
						$this->view('_contributors')
						     ->set('option', $this->option)
						     ->set('contributors', $this->model->contributors('!submitter'))
						     ->display();
						?>
					</div><!-- / #authorslist -->
				<?php } ?>
			</div><!-- / .overviewcontainer -->

			<div class="col span4 omega launcharea">
				<?php
				$html = '';
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
					$firstchild = $this->model->children(0);

					$html .= $this->tab != 'play' && is_object($firstchild) ? ResourcesHtml::primary_child($this->option, $this->model->resource, $firstchild, '') : '';

					// Display some supporting documents
					$children = $this->model->children();

					// Sort out supporting docs
					$html .= $children && count($children) > 1
						   ? ResourcesHtml::sortSupportingDocs( $this->model->resource, $this->option, $children )
						   : '';

					//$html .= $feeds ? $feeds : '';
					$html .= $this->tab != 'play' ? ResourcesHtml::license($this->model->params->get('license', '')) : '';
				} // --- end else (if group check passed)
				echo $html;
				?>
			</div><!-- / .aside launcharea -->
		</div>
	</div><!-- / .subject -->
	<aside class="aside rankarea">
		<?php
		// Show metadata
		if ($this->model->params->get('show_metadata', 1))
		{
			$this->view('_metadata')
			     ->set('option', $this->option)
			     ->set('sections', $this->sections)
			     ->set('model', $this->model)
			     ->display();
		}
		?>
	</aside><!-- / .aside -->
</section><!-- / .main section -->

<?php if ($this->model->access('view')) { ?>
	<section class="main section">
		<div class="subject tabbed">
			<?php echo ResourcesHtml::tabs($this->option, $this->model->resource->id, $this->cats, $this->tab, $this->model->resource->alias); ?>
			<?php echo ResourcesHtml::sections($this->sections, $this->cats, $this->tab, 'hide', 'main'); ?>
		</div><!-- / .subject -->
		<aside class="aside extracontent">
			<?php
			// Get Releated Resources plugin
			JPluginHelper::importPlugin('resources', 'related');
			$dispatcher = JDispatcher::getInstance();

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
				echo \Hubzero\Module\Helper::renderModules('extracontent');
			}
			?>
		</aside><!-- / .aside extracontent -->
	</section><!-- / .main section -->
<?php } ?>