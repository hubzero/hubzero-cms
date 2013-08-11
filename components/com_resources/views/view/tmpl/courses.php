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

	$juser =& JFactory::getUser();
?>
		<div class="main section upperpane">
			<div class="aside rankarea">
<?php
	// Show metadata
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
	} // if ($this->model->params->get('show_metadata', 1)) 
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
								<a class="edit button" href="<?php echo JRoute::_('index.php?option=com_resources&task=draft&step=1&id=' . $this->model->resource->id); ?>"><?php echo JText::_('COM_RESOURCES_EDIT'); ?></a>
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
?>
					<div id="authorslist">
						<?php
						$view = new JView(array(
							'name'   => 'view',
							'layout' => '_contributors',
						));
						$view->option = $this->option;
						$view->contributors = $this->model->contributors('!submitter');
						$view->display();
						?>
					</div><!-- / #authorslist -->
<?php
	} // if ($this->model->params->get('show_authors', 1)) 
?>
				</div><!-- / .overviewcontainer -->

				<div class="aside launcharea">
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

		$children = $this->model->children('standalone');

		$iTunes = 0;
		$supdocs = 0;
		$totaldocs = 0;
		$realdocs = 0;
		$fctype = is_object($firstChild) ? ResourcesHtml::getFileExtension($firstChild->path) : '';

		$html = '';

		// Single out featured children resources
		if ($children) 
		{
			$supln  = '<ul class="supdocln">'."\n";
			$supli  = array();

			foreach ($children as $child)
			{
				if ($child->access == 0 
				 || ($child->access == 1 && !$juser->get('guest'))) 
				{
					$totaldocs++;

					// exclude first child
					$realdocs = $totaldocs;

					$ftype = ResourcesHtml::getFileExtension($child->path);
					$url = ResourcesHtml::processPath($this->option, $child, $this->model->resource->id);

					$title = ($child->logicaltitle)
							? stripslashes($child->logicaltitle)
							: stripslashes($child->title);

					$child->title = $this->escape(stripslashes($child->title));
					//$child->title = str_replace('"', '&quot;', $child->title);
					//$child->title = str_replace('&amp;', '&', $child->title);
					//$child->title = str_replace('&', '&amp;', $child->title);
					//$child->title = str_replace('&amp;quot;', '&quot;', $child->title);

					$linktitle = ($child->title == $title) ? $title : $title . ' - ' . $child->title;

				 	//if (strtolower($fctype) != strtolower($ftype) or $this->resource->type == 6) 
					//{
						// iTunes?
						if (strtolower($child->title) !=  preg_replace('/itunes u/', '', strtolower($child->title))) 
						{
							$supli[] = ' <li><a class="itunes" href="'.$url.'" title="'.$linktitle.'">'.JText::_('iTunes U').'</a></li>'."\n";
						}

						// PDF slides?
						if (strtolower($ftype) == 'pdf' && $title == 'Presentation Slides') 
						{
							$supli[] = ' <li><a class="pdf" href="'.$url.'" title="'.$linktitle.'">'.JText::_('Slides').'</a></li>'."\n";
						}

						// Audio podcast?
						if (strtolower($ftype) == 'mp3' && strtolower($title) !=  preg_replace('/audio/', '', strtolower($title))) 
						{
							$supli[] = ' <li><a class="mp3" href="'.$url.'" title="'.$linktitle.'">'.JText::_('Audio').'</a></li>'."\n";
						}

						// Video podcast?
						if (strtolower($ftype) == 'mp4' && strtolower($title) !=  preg_replace('/video/', '', strtolower($title))) 
						{
							$supli[] = ' <li><a class="mp4" href="'.$url.'" title="'.$linktitle.'">'.JText::_('Video').'</a></li>'."\n";
						}

						// High Res video?
						if (strtolower($ftype) == 'mov' && strtolower($title) !=  preg_replace('/video/', '', strtolower($title))) 
						{
							$supli[] = ' <li><a class="mov" href="'.$url.'" title="'.$linktitle.'">'.JText::_('Video').'</a></li>'."\n";
						}

						// Syllabus?
						if (strtolower($ftype) == 'pdf' && strtolower($title) !=  preg_replace('/syllabus/', '', strtolower(stripslashes($title)))) 
						{
							$supli[] = ' <li><a class="pdf" href="'.$url.'" title="'.$linktitle.'">'.JText::_('Syllabus').'</a></li>'."\n";
						}
					//}
				}
			}

			$supdocs = count( $supli ) > 2 ? 2 : count( $supli );
			$otherdocs = $realdocs - $supdocs;
			$otherdocs = ($supdocs + $otherdocs) == 3  ? 0 : $otherdocs;

			for ($i=0; $i < count( $supli ); $i++)
			{
				$supln .=  $i < 2 ? $supli[$i] : '';
				$supln .=  $i == 2 && !$otherdocs ? $supli[$i] : '';
			}

			// View more link?
			if ($supdocs > 0 && $otherdocs > 0) 
			{
				$supln .= ' <li class="otherdocs"><a href="'.JRoute::_('index.php?option='.$this->option.'&id='.$this->model->resource->id.'&active=supportingdocs').'" title="'.JText::_('View All').' '.$realdocs.' '.JText::_('Supporting Documents').' ">'.$otherdocs.' '.JText::_('more').' &rsaquo;</a></li>'."\n";
			} 
			else if (!$supdocs && $realdocs > 0 && $this->tab != 'play' && is_object($firstChild)) 
			{
				$html .= "\t\t".'<p class="supdocs"><span class="viewalldocs"><a href="'.JRoute::_('index.php?option='.$this->option.'&id='.$this->model->resource->id.'&active=supportingdocs').'">'.JText::_('Additional materials available').' ('.$realdocs.')</a></span></p>'."\n";
			}

			$supln .= '</ul>'."\n";
			$supdocs = $supdocs && $this->tab != 'play'  ? $supln : 0;
		}

		// Show icons of other available formats
		if ($supdocs) {
			$html .= "\t\t\t".$supdocs."\n";
		}
		
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
		

		<?php
		// Show course listings under 'about' tab
		if ($this->tab == 'about') 
		{
			// Course children
			$schildren = $this->model->children('standalone');
			if ($schildren) 
			{
				//$html .= ResourcesHtml::writeResultsTable( $this->database, $this->model->resource, $schildren, $this->option );
				$o = 'even';
		?>
		<a name="series"></a>
		<table class="child-listing" summary="<?php echo JText::_('A table of resources associated to this resource'); ?>">
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
				$this->model->paramsClass = 'JParameter';
				if (version_compare(JVERSION, '1.6', 'ge'))
				{
					$this->model->paramsClass = 'JRegistry';
				}
				$html = '';
				foreach ($schildren as $child)
				{
					// Retrieve the grandchildren
					$this->helper = new ResourcesHelper($child->id, $this->database);
					$this->helper->getChildren();

					$child_params = new $this->model->paramsClass($child->params);
					$link_action = $child_params->get( 'link_action', '' );

					$child->title = ResourcesHtml::encode_html($child->title);

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
							//$html .= ($child->introtext) ? '<br />'.Hubzero_View_Helper_Html::shortenText(stripslashes($child->introtext),200,0) : '';
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
							$grandchild->title = ResourcesHtml::encode_html($grandchild->title);
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
									$breeze .= (!$breeze) ? '<a title="View Presentation - Flash Version" class="breeze flash" href="'.$grandchild->path.'&amp;no_html=1" title="'.htmlentities(stripslashes($grandchild->title)).'">'.JText::_('View Flash').'</a>' : '';
									break;
								case "hubpresenter":
									$hubpresenter .= (!$hubpresenter) ? '<a title="View Presentation - HTML5 Version" class="hubpresenter html5" href="'.$grandchild->path.'" title="'.htmlentities(stripslashes($grandchild->title)).'">'.JText::_('View HTML').'</a>' : '';
									break;
								case "pdf":
								default:
									if ($grandchild->logicaltype == 14) {
										$pdf .= '<a href="'.$grandchild->path.'">'.JText::_('Notes').'</a>'."\n";
									} elseif ($grandchild->logicaltype == 51) {
										$exercises .= '<a href="'.$grandchild->path.'">'.stripslashes($grandchild->title).'</a>'."\n";
									} else {
										$supp .= '<a href="'.$grandchild->path.'">'.stripslashes($grandchild->title).'</a><br />'."\n";
									}
									break;
							}
						}

						if($hubpresenter) {
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
							$html .= Hubzero_View_Helper_Html::shortenText(stripslashes($child->introtext),200,0) . '<br /><br />';
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
		</div><!-- / .main section -->
<?php
	}
?>
	<div class="clear"></div>

