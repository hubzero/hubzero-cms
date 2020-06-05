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
								<a class="icon-edit edit btn" href="<?php echo Route::url('index.php?option=com_resources&task=draft&step=1&id=' . $this->model->id); ?>">
									<?php echo Lang::txt('COM_RESOURCES_EDIT'); ?>
								</a>
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
				</div><!-- / .col span8 -->

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
							<?php echo Lang::txt('COM_RESOURCES_ERROR_MUST_BE_PART_OF_GROUP') . ' ' . implode(', ', $ghtml); ?>
						</p>
						<?php
					}
					else
					{
						// Get summary usage data
						$startdate = new DateTime('midnight first day of this month');
						$enddate   = new DateTime('midnight first day of next month');
						$db = App::get('db');
						$sql  = "SELECT truncate(sum(walltime)/60/60,3) as totalhours FROM `sessionlog`";
						$sql .= " WHERE start > " . $db->quote($startdate->format('Y-m-d H:i:s'));
						$sql .= " AND start < " . $db->quote($enddate->format('Y-m-d H:i:s'));
						$db->setQuery($sql);
						$totalhours = $db->loadResult();

						$params = Component::params('com_tools');
						$maxhours = $params->get('windows_monthly_max_hours', '100');

						if (floatval($totalhours) < floatval($maxhours))
						{
							$lurl = Route::url('index.php?option=' . $this->option . '&task=plugin&trigger=invoke&appid=' . $this->model->path);
							$html = Components\Resources\Helpers\Html::primaryButton('', $lurl, Lang::txt('COM_RESOURCES_LAUNCH_TOOL'));
							$html .= $this->tab != 'play' ? \Components\Resources\Helpers\Html::license($this->model->params->get('license', '')) : '';
							$html .= '<p class="info">Read the <a href="' . Route::url($this->model->link() . '&active=windowstools') . '">setup/instructions</a>.</p>';

							$this->js('
								jQuery(document).ready(function($){
									var primary = $(".btn-primary"),
										url = "' . str_replace('&amp;', '&', $lurl) . '";

									if (primary.length) {
										primary.on("click", function (e){
											e.preventDefault();

											$.get(url.nohtml(), function(data){
												var returned = JSON.parse(data);

												if (returned.success) {
													window.open(returned.message);
												} else {
													if (returned.message) {
														var msg = $("#system-message-container");
														if (msg.length) {
															msg.append(
																\'<dl id="system-message">\' +
																\'<dt class="warning">Warning</dt>\' +
																\'<dd class="warning message"><ul><li>\' + returned.message + \'</li></ul></dd>\' +
																\'</dl>\'
															);
														}
													}
												}
											});
										});
									}
								});
							');
						}
						else
						{
							$html  = Components\Resources\Helpers\Html::primaryButton('', '', Lang::txt('COM_RESOURCES_LAUNCH_TOOL'));
							$html .= 'AppStream tool usage over limit. Please contact the system administrator.';
							$html .= "<br/>" . $totalUsageFigure[0]->totalhours . "/" . $maxhours;
						}

						echo $html;
					}
					?>
				</div><!-- / .col span4 -->
			</div><!-- / .grid -->

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
</section><!-- / .main section -->

<?php if ($this->model->access('view')) { ?>
	<section class="main section <?php echo $this->model->params->get('pageclass_sfx', ''); ?>">
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
			<aside class="aside extracontent">
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
		</aside><!-- / .aside extracontent -->
		</div>
	</section><!-- / .main section -->
<?php }
