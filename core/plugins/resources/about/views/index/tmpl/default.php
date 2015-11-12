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
 * @author    Shawn Rice <zooley@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

$this->css();

$sef = Route::url('index.php?option=' . $this->option . '&' . ($this->model->resource->alias ? 'alias=' . $this->model->resource->alias : 'id=' . $this->model->resource->id));

// Set the display date
switch ($this->model->params->get('show_date'))
{
	case 0: $thedate = ''; break;
	case 1: $thedate = $this->model->resource->created;    break;
	case 2: $thedate = $this->model->resource->modified;   break;
	case 3: $thedate = $this->model->resource->publish_up; break;
}
if ($this->model->isTool() && $this->model->curtool)
{
	$thedate = $this->model->curtool->released;
}

$this->model->resource->introtext = stripslashes($this->model->resource->introtext);
$this->model->resource->fulltxt = stripslashes($this->model->resource->fulltxt);
$this->model->resource->fulltxt = ($this->model->resource->fulltxt) ? trim($this->model->resource->fulltxt) : trim($this->model->resource->introtext);

// Parse for <nb:field> tags
$type = new \Components\Resources\Tables\Type($this->database);
$type->load($this->model->resource->type);

$data = array();
preg_match_all("#<nb:(.*?)>(.*?)</nb:(.*?)>#s", $this->model->resource->fulltxt, $matches, PREG_SET_ORDER);
if (count($matches) > 0)
{
	foreach ($matches as $match)
	{
		$data[$match[1]] = str_replace('="/site', '="' . substr(PATH_APP, strlen(PATH_ROOT)) . '/site', $match[2]);
	}
}
$this->model->resource->fulltxt = preg_replace("#<nb:(.*?)>(.*?)</nb:(.*?)>#s", '', $this->model->resource->fulltxt);
$this->model->resource->fulltxt = trim($this->model->resource->fulltxt);
$this->model->resource->fulltxt = str_replace('="/site', '="' . substr(PATH_APP, strlen(PATH_ROOT)) . '/site', $this->model->resource->fulltxt);

include_once(PATH_CORE . DS . 'components' . DS . 'com_resources' . DS . 'models' . DS . 'elements.php');
$elements = new \Components\Resources\Models\Elements($data, $this->model->type->customFields);
$schema = $elements->getSchema();

// Set the document description
if ($this->model->resource->introtext)
{
	Document::setDescription(strip_tags($this->model->resource->introtext));
}

// Check if there's anything left in the fulltxt after removing custom fields
// If not, set it to the introtext
$maintext = $this->model->description('parsed');
?>
<div class="subject abouttab">
	<?php if ($this->model->isTool()) { ?>
		<?php
		if ($this->model->resource->revision == 'dev' or !$this->model->resource->toolpublished) {
			//$shots = null;
		} else {
			// Screenshots
			$ss = new \Components\Resources\Tables\Screenshot($this->database);

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
	<?php } ?>

	<div class="resource">
		<?php if ($thedate) { ?>
			<div class="grid">
				<div class="col span-half">
		<?php } ?>
					<h4><?php echo Lang::txt('PLG_RESOURCES_ABOUT_CATEGORY'); ?></h4>
					<p class="resource-content">
						<a href="<?php echo Route::url('index.php?option=' . $this->option . '&type=' . $this->model->type->alias); ?>">
							<?php echo $this->escape(stripslashes($this->model->type->type)); ?>
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
					$cite->title    = $this->model->resource->title;
					$cite->year     = Date::of($thedate)->toLocal('Y');
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
							$authors[] = $contributor->name ? $contributor->name : $contributor->xname;
						}
					}
					$cite->author = implode(';', $authors);

					if ($this->model->isTool())
					{
						// Get contribtool params
						$tconfig = Component::params( 'com_tools' );
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

						$revision = isset($this->model->resource->revision) ? $this->model->resource->revision : '';
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

				$citeinstruct  = \Components\Resources\Helpers\Html::citation($this->option, $cite, $this->model->resource->id, $citations, $this->model->resource->type, $revision);
				$citeinstruct .= \Components\Resources\Helpers\Html::citationCOins($cite, $this->model);
				?>

				<?php if ($this->model->params->get('show_citation') == 3): ?>
				<h4><?php echo (isset($citations) && ($citations != NULL || $citations != '') ? Lang::txt('PLG_RESOURCES_ABOUT_CITE_THIS') : ''); ?></h4>

				<div class="resource-content">
					<?php echo (isset($citations) && ($citations != NULL || $citations != '') ? $citeinstruct : ''); ?>
				</div>
				<?php else: ?>
					<h4><?php echo (isset($cite) && ($cite != NULL || $cite != '') ? Lang::txt('PLG_RESOURCES_ABOUT_CITE_THIS') : ''); ?></h4>
					<div class="resource-content">
						<?php echo (isset($cite) && ($cite != NULL || $cite != '') ? $citeinstruct : ''); ?>
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
					$seminarTime = ($this->model->attribs->get('timeof', '') != '0000-00-00 00:00:00' || $this->model->attribs->get('timeof', '') != '')
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
						'base_path' => PATH_CORE . DS . 'components' . DS . 'com_resources' . DS . 'site',
						'name'   => 'view',
						'layout' => '_submitters',
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

		<?php if ($this->model->params->get('show_assocs')) { ?>
			<?php
			$tagger = new \Components\Resources\Helpers\Tags($this->model->resource->id);
			if ($tags = $tagger->render('cloud', ($this->model->access('edit') ? array() : array('admin' => 0)))) { ?>
				<h4><?php echo Lang::txt('PLG_RESOURCES_ABOUT_TAGS'); ?></h4>
				<div class="resource-content">
					<?php
					echo $tags;
					?>
				</div>
			<?php } ?>
		<?php } ?>
	</div><!-- / .resource -->
</div><!-- / .subject -->