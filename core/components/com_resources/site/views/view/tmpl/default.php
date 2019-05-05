<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access.
defined('_HZEXEC_') or die();

$this->css()
     ->js();

$txt = '';
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
								<?php else: ?>
									<?php echo Lang::txt('COM_RESOURCES_ERROR_MUST_BE_PART_OF_GROUP') . ' ' . implode(', ', $ghtml); ?>
								<?php endif; ?>
							</p>
						<?php
					}
					else
					{
						// Display some supporting documents
						$children = $this->model->children()
							->whereEquals('standalone', 0)
							->whereEquals('published', \Components\Resources\Models\Entry::STATE_PUBLISHED)
							->order('ordering', 'asc')
							->rows();

						// get launch button
						$firstchild = $children->first();

						$html  = $this->tab != 'play' && is_object($firstchild) ? \Components\Resources\Helpers\Html::primary_child($this->option, $this->model, $firstchild, '') : '';

						// Sort out supporting docs
						$html .= $children && count($children) > 1
							   ? \Components\Resources\Helpers\Html::sortSupportingDocs($this->model, $this->option, $children)
							   : '';

						echo $html;

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