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
$html = '';
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

$thumb = "/site/stats/resource_impact/resource_impact_".$this->model->resource->id."_th.gif";
$full = "/site/stats/resource_impact/resource_impact_".$this->model->resource->id.".gif";
if (file_exists(JPATH_ROOT . $thumb))
{
	$html .= '</br>';
	$html .= '<a id="member-stats-graph" title="'.$resource->id.' Impact Graph" href="'.$full.'" rel="lightbox">';
	$html .= '<img src="'.$thumb.'" alt="'.$resource->id.' Impact Graph"/></a>';
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
							<a class="icon-edit edit btn" href="<?php echo JRoute::_('index.php?option=com_resources&task=draft&step=1&id=' . $this->model->resource->id); ?>"><?php echo JText::_('COM_RESOURCES_EDIT'); ?></a>
						<?php } ?>
					</h2>
					<input type="hidden" name="rid" id="rid" value="<?php echo $this->model->resource->id; ?>" />
				</header>

				<?php if ($this->model->params->get('show_authors', 1)) { ?>
					<div id="authorslist">
						<?php
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
					// Private/Public resource access check
					if (!$this->model->access('view-all'))
					{
						$ghtml = array();
						foreach ($this->model->resource->getGroups() as $allowedgroup)
						{
							$ghtml[] = '<a href="' . JRoute::_('index.php?option=com_groups&gid=' . $allowedgroup) . '">' . $allowedgroup . '</a>';
						}
				?>
				<p class="warning">
					<?php echo JText::_('COM_RESOURCES_ERROR_MUST_BE_PART_OF_GROUP') . ' ' . implode(', ', $ghtml); ?>
				</p>
				<?php
				}
				else
				{
					$ccount = count($this->model->children('standalone'));

					if ($ccount > 0)
					{
						echo ResourcesHtml::primary_child($this->option, $this->model->resource, '', '');
					}

					// get launch button
					$firstChild = $this->model->children(0);

					// Display some supporting documents
					$children = $this->model->children('!standalone');

					// Sort out supporting docs
					$html .= $children && count($children) > 1
						   ? ResourcesHtml::sortSupportingDocs( $this->model->resource, $this->option, $children )
						   : '';

					echo $html;

					$live_site = rtrim(JURI::base(),'/');
					?>
					<p>
						<a class="feed" id="resource-audio-feed" href="<?php echo $live_site .'/resources/'.$this->model->resource->id.'/feed.rss?format=audio'; ?>"><?php echo JText::_('Audio podcast'); ?></a><br />
						<a class="feed" id="resource-video-feed" href="<?php echo $live_site .'/resources/'.$this->model->resource->id.'/feed.rss?format=video'; ?>"><?php echo JText::_('Video podcast'); ?></a><br />
						<a class="feed" id="resource-slides-feed" href="<?php echo $live_site . '/resources/'.$this->model->resource->id.'/feed.rss?format=slides'; ?>"><?php echo JText::_('Slides/Notes podcast'); ?></a>
					</p>
					<?php
					echo $this->tab != 'play' ? ResourcesHtml::license( $this->model->params->get( 'license', '' ) ) : '';
				} // --- end else (if group check passed)
				?>
			</div><!-- / .aside launcharea -->
		</div>

		<?php
		// Display canonical
		$this->view('_canonical')
		     ->set('option', $this->option)
		     ->set('model', $this->model)
		     ->display();
		?>
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
</section>

<?php if ($this->model->access('view-all')) { ?>
	<section class="main section noborder">
		<div class="subject tabbed">
			<?php echo ResourcesHtml::tabs($this->option, $this->model->resource->id, $this->cats, $this->tab, $this->model->resource->alias); ?>
			<?php echo ResourcesHtml::sections($this->sections, $this->cats, $this->tab, 'hide', 'main'); ?>
		</div><!-- / .subject -->
		<div class="aside extracontent">
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
		</div><!-- / .aside extracontent -->
	</section>

	<?php
	// Show course listings under 'about' tab
	if ($this->tab == 'about')
	{
		// Course children
		$schildren = $this->model->children('standalone');
		if ($schildren)
		{
			$o = 'even';
	?>
	<section class="section" id="series">
		<table class="child-listing">
			<colgroup class="lecture_name"></colgroup>
			<colgroup class="lecture_online"></colgroup>
			<colgroup class="lecture_video"></colgroup>
			<colgroup class="lecture_notes"></colgroup>
			<colgroup class="lecture_supp"></colgroup>
			<colgroup class="lecture_exercises"></colgroup>
			<thead>
				<tr>
					<th><?php echo JText::_('Lecture Number/Topic'); ?></th>
					<th width="12%"><?php echo JText::_('Online Lecture'); ?></th>
					<th><?php echo JText::_('Video'); ?></th>
					<th><?php echo JText::_('Lecture Notes'); ?></th>
					<th><?php echo JText::_('Supplemental Material'); ?></th>
					<th><?php echo JText::_('Suggested Exercises'); ?></th>
				</tr>
			</thead>
			<tbody>
			<?php
				$html = '';
				foreach ($schildren as $child)
				{
					// Retrieve the grandchildren
					$this->helper = new ResourcesHelper($child->id, $this->database);
					$this->helper->getChildren();

					$child_params = new JRegistry($child->params);
					$link_action = $child_params->get( 'link_action', '' );

					$child->title = $this->escape($child->title);

					$o = ($o == 'odd') ? 'even' : 'odd';

					$html .= "\t\t".'<tr class="'.$o.'">'."\n";
					$html .= "\t\t\t".'<td>';
					if ($child->standalone == 1) {
						$html .= '<a href="'.JRoute::_('index.php?option='.$this->option.'&id='.$child->id).'"';
						if ($link_action == 1) {
							$html .= ' target="_blank"';
						} elseif ($link_action == 2) {
							$html .= ' onclick="popupWindow(\''.$url.'\', \''.$child->title.'\', 400, 400, \'auto\');"';
						}
						$html .= '>'.$child->title.'</a>';
						if ($child->type != 31) {
							//$html .= ($child->introtext) ? '<br />'.\Hubzero\Utility\String::truncate(stripslashes($child->introtext),200) : '';
						}
					}
					$html .= '</td>'."\n";
					if ($this->helper->children && count($this->helper->children) > 0)
					{
						$videoi   		= '';
						$breeze    		= '';
						$hubpresenter 	= '';
						$pdf       		= '';
						$video     		= '';
						$exercises 		= '';
						$supp      		= '';
						$grandchildren 	= $this->helper->children;
						foreach ($grandchildren as $grandchild)
						{
							$grandchild->title = $this->escape($grandchild->title);
							$grandchild->path = ResourcesHtml::processPath($this->option, $grandchild, $child->id);

							$grandchild_rt = new ResourcesType( $this->database );
							$grandchild_rt->load($grandchild->type);
							$alias = $grandchild_rt->alias;

							switch ($alias)
							{
								case "player":
								case "quicktime":
									$videoi .= (!$videoi) ? '<a href="'.$grandchild->path.'">'.JText::_('View').'</a>' : '';
									break;
								case "breeze":
									$breeze .= (!$breeze) ? '<a title="View Presentation - Flash Version" class="breeze flash" href="'.$grandchild->path.'&amp;no_html=1" title="'.$this->escape(stripslashes($grandchild->title)).'">'.JText::_('View Flash').'</a>' : '';
									break;
								case "hubpresenter":
									$hubpresenter .= (!$hubpresenter) ? '<a title="View Presentation - HTML5 Version" class="hubpresenter html5" href="'.$grandchild->path.'" title="'.$this->escape(stripslashes($grandchild->title)).'">'.JText::_('View HTML').'</a>' : '';
									break;
								case "pdf":
								default:
									if ($grandchild->logicaltype == 14) {
										$pdf .= '<a href="'.$grandchild->path.'">'.JText::_('Notes').'</a>'."\n";
									} elseif ($grandchild->logicaltype == 51) {
										$exercises .= '<a href="'.$grandchild->path.'">'.stripslashes($grandchild->title).'</a>'."\n";
									} else {
										$grandchildParams  = new JRegistry($grandchild->params);
										$grandchildAttribs = new JRegistry($grandchild->attribs);
										$linkAction = $grandchildParams->get( 'link_action', 0 );
										$width      = $grandchildAttribs->get('width', 640) + 20;
										$height     = $grandchildAttribs->get('height', 360) + 60;

										if ($linkAction == 1)
										{
											$supp .= '<a rel="external" href="'.$grandchild->path.'">'.stripslashes($grandchild->title).'</a><br />'."\n";
										}
										elseif ($linkAction == 2)
										{
											$url = JRoute::_('index.php?option=com_resources&id=' . $child->id . '&resid=' . $grandchild->id . '&task=play');
											$supp .= '<a class="play '.$width.'x'.$height.'" href="'.$url.'">'.stripslashes($grandchild->title).'</a><br />'."\n";
										}
										else
										{
											$supp .= '<a href="'.$grandchild->path.'">'.stripslashes($grandchild->title).'</a><br />'."\n";
										}
									}
									break;
							}
						}

						if ($hubpresenter) {
							$html .= "\t\t\t".'<td>'.$hubpresenter.'<br>'.$breeze.'</td>'."\n";
						} else {
							$html .= "\t\t\t".'<td>'.$breeze.'</td>'."\n";
						}
						$html .= "\t\t\t".'<td>'.$videoi.'</td>'."\n";
						$html .= "\t\t\t".'<td>'.$pdf.'</td>'."\n";
						$html .= "\t\t\t".'<td>'.$supp.'</td>'."\n";
						$html .= "\t\t\t".'<td>'.$exercises.'</td>'."\n";
					} else {
						//$html .= "\t\t\t".'<td colspan="5">'.JText::_('Currently unavilable').'</td>'."\n";
						$html .= "\t\t\t".'<td colspan="5"> </td>'."\n";
					}
					$html .= "\t\t".'</tr>'."\n";
					if ($child->standalone == 1) {
						if ($child->type != 31 && $child->introtext) {
							$html .= "\t\t".'<tr class="'.$o.'">'."\n";
							$html .= "\t\t\t".'<td colspan="6">';
							$html .= \Hubzero\Utility\String::truncate(stripslashes($child->introtext),200) . '<br /><br />';
							$html .= "\t\t\t".'</td>'."\n";
							$html .= "\t\t".'</tr>'."\n";
						}
					}
				}
				echo $html;
				?>
				</tbody>
			</table>
			<?php
			}
		}
		?>
	</section><!-- / .main section -->
<?php } ?>