<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access.
defined('_HZEXEC_') or die();

$this->css()
     ->js();

$txt  = '';
$mode = strtolower(Request::getWord('mode', ''));

if ($mode != 'preview')
{
	switch ($this->model->published)
	{
		case 1:
			$txt .= '';
			break; // published
		case 2:
			$txt .= '<span>[' . Lang::txt('COM_RESOURCES_DRAFT_EXTERNAL') . ']</span> ';
			break;  // external draft
		case 3:
			$txt .= '<span>[' . Lang::txt('COM_RESOURCES_PENDING') . ']</span> ';
			break;  // pending
		case 4:
			$txt .= '<span>[' . Lang::txt('COM_RESOURCES_DELETED') . ']</span> ';
			break;  // deleted
		case 5:
			$txt .= '<span>[' . Lang::txt('COM_RESOURCES_DRAFT_INTERNAL') . ']</span> ';
			break;  // internal draft
		case 0:
			$txt .= '<span>[' . Lang::txt('COM_RESOURCES_UNPUBLISHED') . ']</span> ';
			break;  // unpublished
	}
}
?>
<section class="main section upperpane <?php echo $this->model->params->get('pageclass_sfx', ''); ?>">
	<div class="section-inner hz-layout-with-aside">
		<div class="subject">
			<div class="grid overviewcontainer">
				<div class="col span8">
					<header id="content-header">
						<h2>
							<?php echo $txt . $this->escape(stripslashes($this->model->title)); ?>
							<?php if ($this->model->params->get('access-edit-resource')) { ?>
								<a class="icon-edit edit btn" href="<?php echo Route::url('index.php?option=com_resources&task=draft&step=1&id=' . $this->model->id); ?>"><?php echo Lang::txt('COM_RESOURCES_EDIT'); ?></a>
							<?php } ?>
						</h2>
						<input type="hidden" name="rid" id="rid" value="<?php echo $this->model->id; ?>" />
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
					// Private/Public resource access check
					if (!$this->model->access('view-all'))
					{
						$ghtml = array();
						foreach ($this->model->groups as $allowedgroup)
						{
							$ghtml[] = '<a href="' . Route::url('index.php?option=com_groups&cn=' . $allowedgroup) . '">' . $allowedgroup . '</a>';
						}
						?>
							<p class="warning">
								<?php if (User::isGuest()): ?>
									<?php echo Lang::txt('COM_RESOURCES_ERROR_MUST_BE_LOGGED_IN', base64_encode(Request::path())); ?>
								<?php elseif ($this->get('group_owner')): ?>
									<?php echo Lang::txt('COM_RESOURCES_ERROR_MUST_BE_PART_OF_GROUP') . ' ' . implode(', ', $ghtml); ?>
								<?php else: ?>
									<?php echo Lang::txt('COM_RESOURCES_ALERTNOTAUTH'); ?>
								<?php endif; ?>
							</p>
						<?php
					}
					else
					{
						$schildren = $this->model->children()
							->whereEquals('standalone', 1)
							->whereEquals('published', Components\Resources\Models\Entry::STATE_PUBLISHED)
							->order('ordering', 'asc')
							->rows();

						$ccount = count($schildren);

						if ($ccount > 0)
						{
							$mesg = Lang::txt('COM_RESOURCES_VIEW') . ' ' . $this->model->type->get('type');

							$this->view('_primary')
								->set('option', $this->option)
								->set('class', 'download')
								->set('href', Route::url($this->model->link()) . '#series')
								->set('title', $mesg)
								->set('xtra', '')
								->set('pop', '')
								->set('action', '')
								->set('msg', $mesg)
								->display();
						}

						$html = '';

						$thumb = '/site/stats/resource_impact/resource_impact_' . $this->model->id . '_th.gif';
						$full  = '/site/stats/resource_impact/resource_impact_' . $this->model->id . '.gif';
						if (file_exists(PATH_APP . $thumb))
						{
							$html .= '<br />';
							$html .= '<a id="member-stats-graph" title="'.$this->model->id.' Impact Graph" href="' . Request::base(true) . $full . '" rel="lightbox">';
							$html .= '<img src="' . Request::base(true) . $thumb . '" alt="'.$this->model->id.' Impact Graph"/>';
							$html .= '</a>';
						}

						// Display some supporting documents
						$children = $this->model->children()
							->whereEquals('standalone', 0)
							->whereEquals('published', Components\Resources\Models\Entry::STATE_PUBLISHED)
							->order('ordering', 'asc')
							->rows();

						$firstChild = $children->first();

						// Sort out supporting docs
						$html .= $children && count($children) > 1
							   ? \Components\Resources\Helpers\Html::sortSupportingDocs($this->model, $this->option, $children)
							   : '';

						echo $html;

						$live_site = rtrim(Request::base(), '/');
						?>
						<p>
							<a class="feed" id="resource-audio-feed" href="<?php echo $live_site .'/resources/'.$this->model->id.'/feed.rss?content=audio'; ?>"><?php echo Lang::txt('Audio podcast'); ?></a><br />
							<a class="feed" id="resource-video-feed" href="<?php echo $live_site .'/resources/'.$this->model->id.'/feed.rss?content=video'; ?>"><?php echo Lang::txt('Video podcast'); ?></a><br />
							<a class="feed" id="resource-slides-feed" href="<?php echo $live_site . '/resources/'.$this->model->id.'/feed.rss?content=slides'; ?>"><?php echo Lang::txt('Slides/Notes podcast'); ?></a>
						</p>
						<?php
						if ($this->tab != 'play')
						{
							$this->view('_license')
								->set('license', $this->model->license())
								->display();
						}
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
	</div>
</section>

<?php if ($this->model->access('view-all')) { ?>
	<section class="main section noborder <?php echo $this->model->params->get('pageclass_sfx', ''); ?>">
		<div class="section-inner hz-layout-with-aside">
			<div class="subject tabbed">
				<?php
				$this->view('_tabs')
					->set('option', $this->option)
					->set('cats', $this->cats)
					->set('resource', $this->model)
					->set('active', $this->tab)
					->display();

				$this->view('_sections')
					->set('option', $this->option)
					->set('sections', $this->sections)
					->set('resource', $this->model)
					->set('active', $this->tab)
					->display();
				?>
			</div><!-- / .subject -->
			<div class="aside extracontent">
			<?php
			// Show related content
			$out = Event::trigger('resources.onResourcesSub', array($this->model, $this->option, 1));
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
		</div>
	</section>

	<?php
	// Show course listings under 'about' tab
	if ($this->tab == 'about')
	{
		// Course children
		if ($schildren->count())
		{
			$o = 'even';
			?>
			<section id="series" class="course-accordion-series">
				<div class="course-accordion">
					<?php foreach ($schildren as $child): ?>
						<?php
						$child->title = $this->escape($child->title);
						$title = $child->title;

						$o = ($o == 'odd') ? 'even' : 'odd';

						$childHref = Route::url('index.php?option=' . $this->option . '&id=' . $child->id);
						$childParams = $child->params;
						$link_action = $childParams->get('link_action', '');

						if ($child->standalone == 1)
						{
							$titleHtml = '<a href="' . $childHref . '"';
							if ($link_action == 1)
							{
								$titleHtml .= ' rel="noreferrer" target="_blank"';
							}
							elseif ($link_action == 2)
							{
								$titleHtml .= ' onclick="popupWindow(\'' . addslashes($childHref) . '\', \'' . addslashes($title) . '\', 400, 400, \'auto\'); return false;"';
							}
							$titleHtml .= '>' . $title . '</a>';
						}
						else
						{
							$titleHtml = $title;
						}

						$grandchildren = $child->children()
							->whereEquals('standalone', 0)
							->whereEquals('published', Components\Resources\Models\Entry::STATE_PUBLISHED)
							->order('ordering', 'asc')
							->rows();

						$videoi = $breeze = $hubpresenter = $youtube = $pdf = $exercises = $supp = '';

						if (count($grandchildren) > 0)
						{
							foreach ($grandchildren as $grandchild)
							{
								$grandchild->set('title', $this->escape($grandchild->title));
								$grandchild->set('path', \Components\Resources\Helpers\Html::processPath($this->option, $grandchild, $child->id));

								$alias = isset($grandchild->type->alias) ? $grandchild->type->alias : '';

								switch ($alias)
								{
									case 'player':
									case 'quicktime':
										$videoi .= (!$videoi) ? '<a href="' . $this->escape($grandchild->path) . '">' . Lang::txt('View') . '</a>' : '';
										break;
									case 'breeze':
										$breeze .= (!$breeze) ? '<a class="breeze flash" href="' . $this->escape($grandchild->path) . '&amp;no_html=1" title="' . $this->escape(stripslashes($grandchild->title)) . '">' . Lang::txt('View Flash') . '</a>' : '';
										break;
									case 'hubpresenter':
										$hubpresenter .= (!$hubpresenter) ? '<a class="hubpresenter html5" href="' . $this->escape($grandchild->path) . '" title="' . $this->escape(stripslashes($grandchild->title)) . '">' . Lang::txt('View HTML') . '</a>' : '';
										break;
									case 'elink':
									case 'youtube':
										if ($grandchild->get('logical_type') == 68)
										{
											$youtube .= (!$youtube) ? '<a class="youtube" href="' . $this->escape($grandchild->path) . '" title="' . $this->escape(stripslashes($grandchild->title)) . '">' . Lang::txt('View on YouTube') . '</a>' : '';
											break;
										}
										// fallthrough to default when elink isn't youtube
									case 'pdf':
									default:
										if ($grandchild->get('logical_type') == 14)
										{
											$ext = Filesystem::extension($grandchild->path);
											$ext = (strpos($ext, '?') ? strstr($ext, '?', true) : $ext);
											$pdf .= '<a href="' . $this->escape($grandchild->path) . '">' . Lang::txt('Notes') . ' (' . $this->escape($ext) . ')</a>' . "\n";
										}
										elseif ($grandchild->get('logical_type') == 51)
										{
											$exercises .= '<a href="' . $this->escape($grandchild->path) . '">' . $this->escape(stripslashes($grandchild->title)) . '</a>' . "\n";
										}
										else
										{
											$grandchildParams  = $grandchild->params;
											$grandchildAttribs = $grandchild->attribs;
											$linkAction = $grandchildParams->get('link_action', 0);
											$width      = $grandchildAttribs->get('width', 640) + 20;
											$height     = $grandchildAttribs->get('height', 360) + 60;

											if ($linkAction == 1)
											{
												$supp .= '<a rel="external" href="' . $this->escape($grandchild->path) . '">' . $this->escape(stripslashes($grandchild->title)) . '</a>' . "\n";
											}
											elseif ($linkAction == 2)
											{
												$url = Route::url('index.php?option=com_resources&id=' . $child->id . '&resid=' . $grandchild->id . '&task=play');
												$supp .= '<a class="play ' . $width . 'x' . $height . '" href="' . $this->escape($url) . '">' . $this->escape(stripslashes($grandchild->title)) . '</a>' . "\n";
											}
											else
											{
												$supp .= '<a href="' . $this->escape($grandchild->path) . '">' . $this->escape(stripslashes($grandchild->title)) . '</a>' . "\n";
											}
										}
										break;
								}
							}
						}

						$videoSection = trim($hubpresenter . $youtube . $breeze . $videoi);
						?>

						<div class="course-accordion-item course-<?php echo $o; ?>">
							<button
								class="course-accordion-header"
								aria-expanded="false"
								aria-controls="course-body-<?php echo $child->id; ?>"
								id="course-hdr-<?php echo $child->id; ?>">
								<span class="course-accordion-icon" aria-hidden="true">+</span>
								<span class="course-lecture-title"><?php echo $titleHtml; ?></span>
								<?php if ($child->get('type') != 31 && $child->introtext): ?>
									<span class="course-lecture-description">
										<?php echo nl2br(\Hubzero\Utility\Str::truncate(stripslashes($child->introtext), 200)); ?>
									</span>
								<?php endif; ?>
							</button>

							<div
								id="course-body-<?php echo $child->id; ?>"
								class="course-accordion-body"
								role="region"
								aria-labelledby="course-hdr-<?php echo $child->id; ?>"
								hidden>
								<?php if ($videoSection || $pdf || $supp || $exercises): ?>
									<div class="course-lecture-subsections">
										<?php if ($videoSection): ?>
											<div class="course-subsection">
												<h4><?php echo Lang::txt('Video'); ?></h4>
												<div class="course-subsection-content"><?php echo $videoSection; ?></div>
											</div>
										<?php endif; ?>

										<?php if ($pdf): ?>
											<div class="course-subsection">
												<h4><?php echo Lang::txt('Lecture Notes'); ?></h4>
												<div class="course-subsection-content"><?php echo $pdf; ?></div>
											</div>
										<?php endif; ?>

										<?php if ($supp): ?>
											<div class="course-subsection">
												<h4><?php echo Lang::txt('Supplemental Material'); ?></h4>
												<div class="course-subsection-content"><?php echo $supp; ?></div>
											</div>
										<?php endif; ?>

										<?php if ($exercises): ?>
											<div class="course-subsection">
												<h4><?php echo Lang::txt('Suggested Exercises'); ?></h4>
												<div class="course-subsection-content"><?php echo $exercises; ?></div>
											</div>
										<?php endif; ?>
									</div>
								<?php endif; ?>
							</div>
						</div>

					<?php endforeach; ?>
				</div>
			</section>
			<style>
				.course-accordion-series {
					padding: 0 50px;
				}
				.course-accordion {
					border: 2px solid #e5e5e5;
					border-radius: 1em;
					overflow: hidden;
				}
				.course-accordion-item {
					border-bottom: 1px solid #f0f0f0;
				}
				.course-accordion-header {
					display: flex;
					align-items: center;
					justify-content: space-between;
					width: 100%;
					padding: 12px 16px;
					background: #fafafa;
					border: none;
					cursor: pointer;
					font-size: 1rem;
					text-align: left;
				}
				.course-accordion-header:focus {
					outline: 2px solid #9ad0ff;
					outline-offset: 2px;
				}
				.course-lecture-title {
					font-weight: 400;
					flex: 1;
				}
				.course-accordion-icon {
					margin-left: 12px;
					margin-right: 12px;
					font-weight: 700;
					border: 2px solid #6ca3ba;
					background-color: #EEE;
					padding: 0.2em 0.5em 0.3em;
					border-radius: 50%;
				}
				.course-accordion-body {
					padding: 12px 16px;
					background: #fff;
				}
				.course-lecture-description {
					color: #444;
					max-width: 600px;
				}
				.course-lecture-subsections {
					margin-top: 8px;
				}
				.course-subsection {
					margin-bottom: 12px;
					background-color: rgba(200, 200, 200, 0.1);
					padding: 1em;
					border-radius: 1em;
				}
				.course-subsection h4 {
					margin: 0 0 6px 0;
					font-size: 0.95rem;
					color: #222;
					font-weight: 600;
				}
				.course-subsection-content {
					margin-left: 22px;
				}
				.course-subsection-content a {
					display: block;
					margin: 2px 0;
					word-break: break-word;
					border: 0;
					margin-top: 1em;
				}
				.course-odd .course-accordion-header {
					background: #fbfbfb;
				}
				.course-even .course-accordion-header {
					background: #f7f7f7;
				}
			</style>
			<script>
				jQuery(document).ready(function($) {
					$(".course-accordion-header").on("click", function() {
						var $btn = $(this);
						var panelId = $btn.attr("aria-controls");
						var $panel = $("#" + panelId);

						var expanded = $btn.attr("aria-expanded") === "true";
						if (expanded) {
							$btn.attr("aria-expanded", "false").find(".course-accordion-icon").text("+");
							$panel.slideUp(180, function() {
								$(this).attr("hidden", true);
							});
						} else {
							$btn.attr("aria-expanded", "true").find(".course-accordion-icon").text("\u2212");
							$panel.attr("hidden", false).slideDown(200);
						}
					});
				});
			</script>
			<?php
		}
	}
	?>
<?php }
