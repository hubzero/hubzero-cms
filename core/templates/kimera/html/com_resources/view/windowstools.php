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

$txt  = '';
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
?>
<div id="content-header">
	<section class="main section upperpane <?php echo $this->model->params->get('pageclass_sfx', ''); ?>">
		<div class="subject">
			<div class="grid overviewcontainer">
				<div class="col span8">
					<header>
						<h2>
							<?php echo $txt . $this->escape(stripslashes($this->model->resource->title)); ?>
							<?php if ($this->model->params->get('access-edit-resource')) { ?>
								<a class="icon-edit edit btn" href="<?php echo Route::url('index.php?option=com_resources&task=draft&step=1&id=' . $this->model->resource->id); ?>">
									<?php echo Lang::txt('COM_RESOURCES_EDIT'); ?>
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
					// Private/Public resource access check
					if (!$this->model->access('view-all'))
					{
						$ghtml = array();
						foreach ($this->model->resource->getGroups() as $allowedgroup)
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
						$lurl = Route::url('index.php?option=' . $this->option . '&task=plugin&trigger=invoke&appid=' . $this->model->resource->path);
						$html  = Components\Resources\Helpers\Html::primaryButton('', $lurl, Lang::txt('COM_RESOURCES_LAUNCH_TOOL'));
						$html .= $this->tab != 'play' ? \Components\Resources\Helpers\Html::license($this->model->params->get('license', '')) : '';
					}
					echo $html;
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
	</section><!-- / .main section -->
</div>

<?php if ($this->model->access('view')) { ?>
	<section class="main section <?php echo $this->model->params->get('pageclass_sfx', ''); ?>">
		<div class="subject tabbed">
			<?php
			$this->view('_tabs')
				->set('option', $this->option)
				->set('cats', $this->cats)
				->set('resource', $this->model->resource)
				->set('active', $this->tab)
				->display();

			$this->view('_sections')
				->set('option', $this->option)
				->set('sections', $this->sections)
				->set('resource', $this->model->resource)
				->set('active', $this->tab)
				->display();
			?>
		</div><!-- / .subject -->
		<aside class="aside extracontent">
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
		</aside><!-- / .aside extracontent -->
	</section><!-- / .main section -->
<?php } ?>
