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

// No direct access
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
<div id="content-header">
	<section class="main section upperpane">
		<div class="subject">
			<div class="grid overviewcontainer">
				<div class="col span8">
					<header>
						<h2>
							<?php echo $txt . $this->escape(stripslashes($this->model->title)); ?>
						</h2>
						<input type="hidden" name="rid" id="rid" value="<?php echo $this->model->id; ?>" />
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
					<?php if ($this->model->params->get('access-edit-resource')) { ?>
						<a class="icon-edit edit btn" href="<?php echo Route::url('index.php?option=com_resources&task=draft&step=1&id=' . $this->model->id); ?>"><?php echo Lang::txt('COM_RESOURCES_EDIT'); ?></a>
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
							<?php echo Lang::txt('COM_RESOURCES_ERROR_MUST_BE_PART_OF_GROUP') . ' ' . implode(', ', $ghtml); ?>
						</p>
						<?php
					}
					else
					{
						$children = $this->model->children()
							->whereEquals('standalone', 1)
							->whereEquals('published', 1)
							->ordered()
							->rows();

						$ccount = count($children);

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
								->set('disabled', false)
								->display();
						}

						$video = 0;
						$audio = 0;
						$notes = 0;

						foreach ($children as $child)
						{
							$grandchildren = $child->children()
								->whereEquals('standalone', 0)
								->whereEquals('published', 1)
								->ordered()
								->rows();

							if (count($grandchildren) > 0)
							{
								foreach ($grandchildren as $grandchild)
								{
									switch (Filesystem::extension($grandchild->path))
									{
										case 'm4v':
										case 'mp4':
										case 'wmv':
										case 'mov':
										case 'qt':
										case 'mpg':
										case 'mpeg':
										case 'mpe':
										case 'mp2':
										case 'mpv2':
											$video++;
										break;

										case 'mp3':
										case 'm4a':
										case 'aiff':
										case 'aif':
										case 'wav':
										case 'ra':
										case 'ram':
											$audio++;
										break;

										case 'ppt':
										case 'pps':
										case 'pdf':
										case 'doc':
										case 'txt':
										case 'html':
										case 'htm':
											$notes++;
										break;
									}
								}
							}
						}

						$live_site = rtrim(Request::base(), '/');

						if ($notes || $audio || $video)
						{
							?>
							<p>
								<?php if ($audio) { ?>
									<a class="feed" id="resource-audio-feed" href="<?php echo $live_site .'/resources/'.$this->model->id.'/feed.rss?content=audio'; ?>"><?php echo Lang::txt('Audio podcast'); ?></a><br />
								<?php } ?>
								<?php if ($video) { ?>
									<a class="feed" id="resource-video-feed" href="<?php echo $live_site .'/resources/'.$this->model->id.'/feed.rss?content=video'; ?>"><?php echo Lang::txt('Video podcast'); ?></a><br />
								<?php } ?>
								<?php if ($notes) { ?>
									<a class="feed" id="resource-slides-feed" href="<?php echo $live_site . '/resources/'.$this->model->id.'/feed.rss?content=slides'; ?>"><?php echo Lang::txt('Slides/Notes podcast'); ?></a>
								<?php } ?>
							</p>
							<?php
						}

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
	</section><!-- / .main section -->
</div>

<?php if ($this->model->access('view-all')) { ?>
	<section class="main section">
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
	</section>

	<?php
	// Show course listings under 'about' tab
	if ($this->tab == 'about' && $ccount > 0)
	{
		$filters = array(
			'sortby' => Request::getString('sortby', $this->model->params->get('sort_children', 'ordering')),
			'limit'  => Request::getInt('limit', 0),
			'start'  => Request::getInt('limitstart', 0),
			'id'     => $this->model->id
		);

		// Get children
		$children = $this->model->children()
			->whereEquals('standalone', 1)
			->whereEquals('published', Components\Resources\Models\Entry::STATE_PUBLISHED)
			->order(($filters['sortby'] == 'date' ? 'created' : $filters['sortby']), 'asc')
			->limit($filters['limit'])
			->start($filters['start'])
			->rows();

		// Build the results
		$sortbys = array(
			'date'     => Lang::txt('DATE'),
			'title'    => Lang::txt('TITLE'),
			'author'   => Lang::txt('AUTHOR'),
			'ordering' => Lang::txt('ORDERING')
		);
		if ($this->model->params->get('show_ranking'))
		{
			$sortbys['ranking'] = Lang::txt('RANKING');
		}
		?>
		<form method="get" action="<?php echo Route::url($this->model->link()); ?>">
			<section class="section">
				<div class="subject">
					<h3>
						<?php echo Lang::txt('In This Workshop'); ?>
					</h3>

					<?php
					$this->view('_list', 'browse')
						->set('lines', $children)
						->set('show_edit', $this->model->access('edit'))
						->display();
					?>

					<div class="clear"></div><!-- / .clear -->

					<?php
					// Initiate paging for children
					$pageNav = $this->pagination(
						$ccount,
						$filters['start'],
						$filters['limit']
					);
					$pageNav->setAdditionalUrlParam('id', $this->model->id);
					$pageNav->setAdditionalUrlParam('sortby', $filters['sortby']);

					echo $pageNav->render();
					?>
				</div><!-- / .subject -->
				<div class="aside">
					<fieldset class="controls">
						<label for="sortby">
							<?php echo Lang::txt('COM_RESOURCES_SORT_BY'); ?>:
							<?php echo \Components\Resources\Helpers\Html::formSelect('sortby', $sortbys, $filters['sortby'], ''); ?>
						</label>
						<p class="submit">
							<input type="submit" value="<?php echo Lang::txt('COM_RESOURCES_GO'); ?>" />
						</p>
					</fieldset>
				</div><!-- / .aside -->
			</section>
		</form>
	<?php } ?>
<?php
}
