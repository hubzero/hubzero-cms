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
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

// No direct access.
defined('_HZEXEC_') or die();

$this->css()
     ->js();

$txt = '';
$html = '';
$mode = strtolower(Request::getWord('mode', ''));

if ($mode != 'preview')
{
	switch ($this->model->resource->published)
	{
		case 1: $txt .= ''; break; // published
		case 2: $txt .= '<span>[' . Lang::txt('COM_RESOURCES_DRAFT_EXTERNAL') . ']</span> '; break;  // external draft
		case 3: $txt .= '<span>[' . Lang::txt('COM_RESOURCES_PENDING') . ']</span> ';        break;  // pending
		case 4: $txt .= '<span>[' . Lang::txt('COM_RESOURCES_DELETED') . ']</span> ';        break;  // deleted
		case 5: $txt .= '<span>[' . Lang::txt('COM_RESOURCES_DRAFT_INTERNAL') . ']</span> '; break;  // internal draft
		case 0; $txt .= '<span>[' . Lang::txt('COM_RESOURCES_UNPUBLISHED') . ']</span> ';    break;  // unpublished
	}
}

$thumb = "/site/stats/resource_impact/resource_impact_".$this->model->resource->id."_th.gif";
$full = "/site/stats/resource_impact/resource_impact_".$this->model->resource->id.".gif";
if (file_exists(PATH_APP . $thumb))
{
	$html .= '</br>';
	$html .= '<a id="member-stats-graph" title="'.$resource->id.' Impact Graph" href="' . Request::base(true) . $full . '" rel="lightbox">';
	$html .= '<img src="' . Request::base(true) . $thumb . '" alt="'.$resource->id.' Impact Graph"/></a>';
}
?>
<section class="main section upperpane <?php echo $this->model->params->get('pageclass_sfx', ''); ?>">
	<div class="subject">
		<div class="grid overviewcontainer">
			<div class="col span8">
				<header id="content-header">
					<h2>
						<?php echo $txt . $this->escape(stripslashes($this->model->resource->title)); ?>
						<?php if ($this->model->params->get('access-edit-resource')) { ?>
							<a class="icon-edit edit btn" href="<?php echo Route::url('index.php?option=com_resources&task=draft&step=1&id=' . $this->model->resource->id); ?>"><?php echo Lang::txt('COM_RESOURCES_EDIT'); ?></a>
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
							$ghtml[] = '<a href="' . Route::url('index.php?option=com_groups&gid=' . $allowedgroup) . '">' . $allowedgroup . '</a>';
						}
				?>
				<p class="warning">
					<?php echo Lang::txt('COM_RESOURCES_ERROR_MUST_BE_PART_OF_GROUP') . ' ' . implode(', ', $ghtml); ?>
				</p>
				<?php
				}
				else
				{
					$ccount = count($this->model->children('standalone'));

					if ($ccount > 0)
					{
						echo \Components\Resources\Helpers\Html::primary_child($this->option, $this->model->resource, '', '');
					}

					// get launch button
					$firstChild = $this->model->children(0);

					// Display some supporting documents
					$children = $this->model->children('!standalone');

					// Sort out supporting docs
					$html .= $children && count($children) > 1
						   ? \Components\Resources\Helpers\Html::sortSupportingDocs( $this->model->resource, $this->option, $children )
						   : '';

					echo $html;

					$live_site = rtrim(Request::base(),'/');
					?>
					<p>
						<a class="feed" id="resource-audio-feed" href="<?php echo $live_site .'/resources/'.$this->model->resource->id.'/feed.rss?content=audio'; ?>"><?php echo Lang::txt('Audio podcast'); ?></a><br />
						<a class="feed" id="resource-video-feed" href="<?php echo $live_site .'/resources/'.$this->model->resource->id.'/feed.rss?content=video'; ?>"><?php echo Lang::txt('Video podcast'); ?></a><br />
						<a class="feed" id="resource-slides-feed" href="<?php echo $live_site . '/resources/'.$this->model->resource->id.'/feed.rss?content=slides'; ?>"><?php echo Lang::txt('Slides/Notes podcast'); ?></a>
					</p>
					<?php
					echo $this->tab != 'play' ? \Components\Resources\Helpers\Html::license( $this->model->params->get( 'license', '' ) ) : '';
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
	<section class="main section noborder <?php echo $this->model->params->get('pageclass_sfx', ''); ?>">
		<div class="subject tabbed">
			<?php echo \Components\Resources\Helpers\Html::tabs($this->option, $this->model->resource->id, $this->cats, $this->tab, $this->model->resource->alias); ?>
			<?php echo \Components\Resources\Helpers\Html::sections($this->sections, $this->cats, $this->tab, 'hide', 'main'); ?>
		</div><!-- / .subject -->
		<div class="aside extracontent">
			<?php
			// Show related content
			$out = Event::trigger('resources.onResourcesSub', array($this->model->resource, $this->option, 1));
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
					<th><?php echo Lang::txt('Lecture Number/Topic'); ?></th>
					<th width="12%"><?php echo Lang::txt('Online Lecture'); ?></th>
					<th><?php echo Lang::txt('Video'); ?></th>
					<th><?php echo Lang::txt('Lecture Notes'); ?></th>
					<th><?php echo Lang::txt('Supplemental Material'); ?></th>
					<th><?php echo Lang::txt('Suggested Exercises'); ?></th>
				</tr>
			</thead>
			<tbody>
			<?php
				$html = '';
				foreach ($schildren as $child)
				{
					// Retrieve the grandchildren
					$this->helper = new \Components\Resources\Helpers\Helper($child->id, $this->database);
					$this->helper->getChildren();

					$child_params = new \Hubzero\Config\Registry($child->params);
					$link_action = $child_params->get( 'link_action', '' );

					$child->title = $this->escape($child->title);

					$o = ($o == 'odd') ? 'even' : 'odd';

					$html .= "\t\t".'<tr class="'.$o.'">'."\n";
					$html .= "\t\t\t".'<td>';
					if ($child->standalone == 1) {
						$html .= '<a href="'.Route::url('index.php?option='.$this->option.'&id='.$child->id).'"';
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
						$videoi       = '';
						$breeze       = '';
						$hubpresenter = '';
						$pdf          = '';
						$video        = '';
						$exercises    = '';
						$supp         = '';
						$grandchildren = $this->helper->children;
						foreach ($grandchildren as $grandchild)
						{
							$grandchild->title = $this->escape($grandchild->title);
							$grandchild->path = \Components\Resources\Helpers\Html::processPath($this->option, $grandchild, $child->id);

							$grandchild_rt = new \Components\Resources\Tables\Type($this->database);
							$grandchild_rt->load($grandchild->type);
							$alias = $grandchild_rt->alias;

							switch ($alias)
							{
								case "player":
								case "quicktime":
									$videoi .= (!$videoi) ? '<a href="'.$grandchild->path.'">'.Lang::txt('View').'</a>' : '';
									break;
								case "breeze":
									$breeze .= (!$breeze) ? '<a title="View Presentation - Flash Version" class="breeze flash" href="'.$grandchild->path.'&amp;no_html=1" title="'.$this->escape(stripslashes($grandchild->title)).'">'.Lang::txt('View Flash').'</a>' : '';
									break;
								case "hubpresenter":
									$hubpresenter .= (!$hubpresenter) ? '<a title="View Presentation - HTML5 Version" class="hubpresenter html5" href="'.$grandchild->path.'" title="'.$this->escape(stripslashes($grandchild->title)).'">'.Lang::txt('View HTML').'</a>' : '';
									break;
								case "pdf":
								default:
									if ($grandchild->logicaltype == 14) {
										$pdf .= '<a href="'.$grandchild->path.'">'.Lang::txt('Notes').'</a>'."\n";
									} elseif ($grandchild->logicaltype == 51) {
										$exercises .= '<a href="'.$grandchild->path.'">'.stripslashes($grandchild->title).'</a>'."\n";
									} else {
										$grandchildParams  = new \Hubzero\Config\Registry($grandchild->params);
										$grandchildAttribs = new \Hubzero\Config\Registry($grandchild->attribs);
										$linkAction = $grandchildParams->get( 'link_action', 0 );
										$width      = $grandchildAttribs->get('width', 640) + 20;
										$height     = $grandchildAttribs->get('height', 360) + 60;

										if ($linkAction == 1)
										{
											$supp .= '<a rel="external" href="'.$grandchild->path.'">'.stripslashes($grandchild->title).'</a><br />'."\n";
										}
										elseif ($linkAction == 2)
										{
											$url = Route::url('index.php?option=com_resources&id=' . $child->id . '&resid=' . $grandchild->id . '&task=play');
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
						//$html .= "\t\t\t".'<td colspan="5">'.Lang::txt('Currently unavilable').'</td>'."\n";
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