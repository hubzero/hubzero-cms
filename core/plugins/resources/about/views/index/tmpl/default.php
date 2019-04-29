<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

$this->css();

$sef = Route::url($this->model->link());

// Set the display date
$thedate = $this->model->date;
if ($this->model->isTool() && $this->model->curtool)
{
	$thedate = $this->model->curtool->released;
}
if ($thedate == '0000-00-00 00:00:00')
{
	$thedate = '';
}

$this->model->introtext = stripslashes($this->model->introtext);
$this->model->fulltxt = stripslashes($this->model->fulltxt);
$this->model->fulltxt = ($this->model->fulltxt) ? trim($this->model->fulltxt) : trim($this->model->introtext);

// Parse for <nb:field> tags
$type = $this->model->type;

$data = array();
preg_match_all("#<nb:(.*?)>(.*?)</nb:(.*?)>#s", $this->model->fulltxt, $matches, PREG_SET_ORDER);
if (count($matches) > 0)
{
	foreach ($matches as $match)
	{
		$data[$match[1]] = str_replace('="/site', '="' . substr(PATH_APP, strlen(PATH_ROOT)) . '/site', $match[2]);
	}
}
$this->model->fulltxt = preg_replace("#<nb:(.*?)>(.*?)</nb:(.*?)>#s", '', $this->model->fulltxt);
$this->model->fulltxt = trim($this->model->fulltxt);
$this->model->fulltxt = str_replace('="/site', '="' . substr(PATH_APP, strlen(PATH_ROOT)) . '/site', $this->model->fulltxt);

include_once Component::path('com_resources') . DS . 'models' . DS . 'elements.php';
$elements = new \Components\Resources\Models\Elements($data, $this->model->type->customFields);
$schema = $elements->getSchema();

// Set the document description
if ($this->model->introtext)
{
	Document::setDescription(strip_tags($this->model->introtext));
}

// Check if there's anything left in the fulltxt after removing custom fields
// If not, set it to the introtext
$maintext = $this->model->description;
?>
<div class="subject abouttab">
	<?php if ($this->model->isTool()) { ?>
		<?php
		if ($this->model->revision == 'dev' or !$this->model->toolpublished) {
			//$shots = null;
		} else {
			// Screenshots
			$ss = $this->model->screenshots()
				->whereEquals('versionid', $this->model->versionid)
				->ordered()
				->rows();

			$this->view('_screenshots')
			     ->set('id', $this->model->id)
			     ->set('created', $this->model->created)
			     ->set('upath', $this->model->params->get('uploadpath'))
			     ->set('wpath', $this->model->params->get('uploadpath'))
			     ->set('versionid', $this->model->versionid)
			     ->set('sinfo', $ss)
			     ->set('slidebar', 1)
			     ->display();
			?>
		<?php } ?>
	<?php } ?>

	<div class="resource">
		<?php if ($thedate) { ?>
			<div class="grid">
				<div class="col span-half">
		<?php } ?>
					<h4><?php echo Lang::txt('PLG_RESOURCES_ABOUT_CATEGORY'); ?></h4>
					<p class="resource-content">
						<a href="<?php echo Route::url('index.php?option=' . $this->option . '&type=' . $this->model->type->get('alias')); ?>">
							<?php echo $this->escape(stripslashes($this->model->type->get('type'))); ?>
						</a>
					</p>
		<?php if ($thedate) { ?>
				</div>
				<div class="col span-half omega">
					<h4><?php echo Lang::txt('PLG_RESOURCES_ABOUT_PUBLISHED_ON'); ?></h4>
					<p class="resource-content">
						<time datetime="<?php echo $thedate; ?>"><?php echo Date::of($thedate)->toLocal(Lang::txt('DATE_FORMAT_HZ1')); ?></time>
					</p>
				</div>
			</div>
		<?php } ?>

		<?php if (!$this->model->access('view-all')) { // Protected - only show the introtext ?>
			<h4><?php echo Lang::txt('PLG_RESOURCES_ABOUT_ABSTRACT'); ?></h4>
			<div class="resource-content">
				<?php echo $maintext; ?>
			</div>
		<?php } else { ?>
			<?php if (trim($maintext)) { ?>
				<h4><?php echo Lang::txt('PLG_RESOURCES_ABOUT_ABSTRACT'); ?></h4>
				<div class="resource-content">
					<?php echo $maintext; ?>
				</div>
			<?php } ?>

			<?php
			$citations = '';
			if (is_object($schema))
			{
				if (!isset($schema->fields) || !is_array($schema->fields))
				{
					$schema->fields = array();
				}
				foreach ($schema->fields as $field)
				{
					if (isset($data[$field->name]))
					{
						if ($field->name == 'citations')
						{
							$citations = $data[$field->name];
						}
						else if ($value = $elements->display($field->type, $data[$field->name]))
						{
							?>
							<h4><?php echo $field->label; ?></h4>
							<div class="resource-content">
								<?php echo $value; ?>
							</div>
							<?php
						}
					}
				}
			}
			?>

			<?php if ($this->model->params->get('show_citation')) { ?>
				<?php
				$revision = 0;

				//auto generated
				if ($this->model->params->get('show_citation') == 1 || $this->model->params->get('show_citation') == 2)
				{
					// Build our citation object
					$cite = new stdClass();
					$cite->title    = $this->model->title;
					$cite->year     = ($thedate ? Date::of($thedate)->toLocal('Y') : Date::of('now')->toLocal('Y'));
					$cite->location = Request::base() . ltrim($sef, '/');
					$cite->date     = Date::toSql();
					$cite->url      = '';
					$cite->type     = '';
					$authors = array();
					$contributors = ($this->model->isTool() ? $this->model->contributors('tool') : $this->model->contributors('!submitter'));
					if ($contributors)
					{
						foreach ($contributors as $contributor)
						{
							if ($contributor->role == 'submitter')
							{
								continue;
							}
							$authors[] = $contributor->name;
						}
					}
					$cite->author = implode(';', $authors);

					if ($this->model->isTool())
					{
						// Get contribtool params
						$tconfig = Component::params('com_tools');
						$doi = '';

						if ($this->model->doi && $tconfig->get('doi_shoulder'))
						{
							$doi = $tconfig->get('doi_shoulder') . '/' . strtoupper($this->model->doi);
						}
						else if ($this->model->doi_label)
						{
							$doi = '10254/' . $tconfig->get('doi_prefix') . $this->model->id . '.' . $this->model->doi_label;
						}

						if ($doi)
						{
							$cite->doi = $doi;
						}

						$revision = $this->model->revision ? $this->model->revision : '';
					}

					if ($this->model->params->get('show_citation') == 2)
					{
						$citations = '';
					}
				}
				else
				{
					$cite = null;
				}

				$citeinstruct = \Components\Resources\Helpers\Html::citation($this->option, $cite, $this->model->id, $citations, $this->model->type, $revision);
				?>

				<?php if ($this->model->params->get('show_citation') == 3): ?>
				<h4><?php echo (isset($citations) && ($citations != null || $citations != '')) ? Lang::txt('PLG_RESOURCES_ABOUT_CITE_THIS') : ''; ?></h4>

				<div class="resource-content">
					<?php echo (isset($citations) && ($citations != null || $citations != '')) ? $citeinstruct : ''; ?>
				</div>
				<?php else: ?>
					<h4><?php echo (isset($cite) && ($cite != null || $cite != '')) ? Lang::txt('PLG_RESOURCES_ABOUT_CITE_THIS') : ''; ?></h4>
					<div class="resource-content">
						<?php echo (isset($cite) && ($cite != null || $cite != '')) ? $citeinstruct : ''; ?>
					</div>
				<?php endif; ?>
			<?php } ?>
		<?php } ?>

		<?php if ($this->model->attribs->get('timeof', '')) { ?>
			<h4><?php echo Lang::txt('PLG_RESOURCES_ABOUT_TIME'); ?></h4>
			<p class="resource-content"><time><?php
				// If the resource had a specific event date/time
				if (substr($this->model->attribs->get('timeof', ''), -8, 8) == '00:00:00')
				{
					$exp = Lang::txt('DATE_FORMAT_HZ1'); //'%B %d %Y';
				}
				else
				{
					$exp = Lang::txt('TIME_FORMAT_HZ1') . ', ' . Lang::txt('DATE_FORMAT_HZ1'); //'%I:%M %p, %B %d %Y';
				}
				if (substr($this->model->attribs->get('timeof', ''), 4, 1) == '-')
				{
					$seminarTime = ($this->model->attribs->get('timeof', '') != '0000-00-00 00:00:00' && $this->model->attribs->get('timeof', '') != '')
								  ? Date::of($this->model->attribs->get('timeof', ''))->toLocal($exp)
								  : '';
				}
				else
				{
					$seminarTime = $this->model->attribs->get('timeof', '');
				}

				echo $this->escape($seminarTime);
				?></time></p>
		<?php } ?>

		<?php if ($this->model->attribs->get('location', '')) { ?>
			<h4><?php echo Lang::txt('PLG_RESOURCES_ABOUT_LOCATION'); ?></h4>
			<p class="resource-content"><?php echo $this->escape($this->model->attribs->get('location', '')); ?></p>
		<?php } ?>

		<?php if ($this->model->contributors('submitter')) { ?>
			<h4><?php echo Lang::txt('PLG_RESOURCES_ABOUT_SUBMITTER'); ?></h4>
			<div class="resource-content">
				<div id="submitterlist">
					<?php
					$view = new \Hubzero\Component\View(array(
						'base_path' => Component::path('com_resources') . DS . 'site',
						'name'      => 'view',
						'layout'    => '_submitters',
					));
					$view->option       = $this->option;
					$view->contributors = $this->model->contributors('submitter');
					$view->badges       = $this->plugin->get('badges', 0);
					$view->showorgs     = 1;
					$view->display();
					?>
				</div>
			</div>
		<?php } ?>

		<?php if ($this->model->params->get('show_assocs')): ?>
			<?php if ($this->tags->count()): ?>
				<h4><?php echo Lang::txt('PLG_RESOURCES_ABOUT_TAGS'); ?></h4>
				<div class="resource-content">
					<?php
					$view = new Hubzero\Component\View(array(
						'base_path' => Component::path('com_tags') . '/site',
						'name'      => 'tags',
						'layout'    => '_cloud'
					));
					$view->set('config', Component::params('com_tags'));
					$view->set('tags', $this->tags);
					$view->display();
					?>
				</div>
			<?php endif; ?>
		<?php endif; ?>

		<?php
		$cite = null; // Not used
		echo \Components\Resources\Helpers\Html::citationCOins($cite, $this->model);
		?>
	</div><!-- / .resource -->
</div><!-- / .subject -->
