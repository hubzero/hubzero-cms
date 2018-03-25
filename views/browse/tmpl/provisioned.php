<?php
/**
 * @package		HUBzero CMS
 * @author		Alissa Nedossekina <alisa@purdue.edu>
 * @copyright	Copyright 2005-2009 HUBzero Foundation, LLC.
 * @license		http://opensource.org/licenses/MIT MIT
 *
 * Copyright 2005-2009 HUBzero Foundation, LLC.
 * All rights reserved.
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
 */

// No direct access
defined('_HZEXEC_') or die();

$pubconfig = Component::params('com_publications');
?>
<div class="grid contribute">
	<div class="col span4">
		<h3><?php echo Lang::txt('PLG_PROJECTS_PUBLICATIONS_WHERE_TO_START'); ?></h3>
		<div class="contrib-start">
			<p><span class="project-icon"></span><?php echo Lang::txt('PLG_PROJECTS_PUBLICATIONS_CONTRIB_FROM_PROJECTS'); ?></p>
			<p class="submitarea">
				<span><a href="/projects/start" class="btn btn-success"><?php echo Lang::txt('PLG_PROJECTS_PUBLICATIONS_START_PROJECT'); ?></a></span>
				<span><a href="/members/myaccount/projects" class="btn"><?php echo Lang::txt('PLG_PROJECTS_PUBLICATIONS_MY_PROJECTS'); ?></a></span>
			</p>
		</div>
<?php if ($pubconfig->get('contribute', 0)) { ?>
		<div class="contrib-start simple">
			<p><?php echo Lang::txt('PLG_PROJECTS_PUBLICATIONS_CONTRIB_FROM_OUTSIDE'); ?></p>
			<?php
			if (count($this->choices) > 0)
			{
				foreach ($this->choices as $current)
				{
					?>
					<p class="submitarea">
						<span>
							<a class="btn btn-primary<?php echo ($current->description) ? ' tooltips" title="' . $this->escape($current->description) . '"' : ''; ?>" href="<?php echo Route::url('index.php?option=com_publications&task=submit&action=publication&base=' . $current->alias); ?>">
								<?php echo $this->escape($current->type); ?>
							</a>
						</span>
					</p>
					<?php
				}
			}
			else
			{
				?>
				<p class="submitarea">
					<span>
						<a class="btn btn-primary" href="<?php echo Route::url('index.php?option=com_publications&task=submit&action=publication&base=files'); ?>">
							<?php echo Lang::txt('PLG_PROJECTS_PUBLICATIONS_PUBLISH_FILES'); ?>
						</a>
					</span>
				</p>
				<?php
			}
			?>
		</div>
<?php } ?>
	</div>
	<div class="col span8 omega">
		<?php if (User::isGuest())
		{
			$this->view('intro')
			     ->set('project', $this->project)
			     ->set('pub', $this->pub)
			     ->display();
		}
		else
		{
			$filters = array();

			// Get user projects
			$filters['projects']  = $this->project->table()->getUserProjectIds(User::get('id'), 0, 1);

			$filters['mine']	= User::get('id');
			$filters['dev']		= 1;
			$filters['sortby']	= 'mine';
			$filters['limit'] 	= Request::getInt('limit', Config::get('list_limit'));
			$filters['start'] 	= Request::getInt('limitstart', 0);

			// Get publications created by user
			$mypubs = $this->pub->entries( 'list', $filters );
			$total  = $this->pub->entries( 'count', $filters );
			?>
			<form action="<?php echo Route::url('index.php?option=com_publications&task=submit'); ?>" method="post" id="browseForm" >
			<h3><?php echo Lang::txt('PLG_PROJECTS_PUBLICATIONS_MY_PUBLICATIONS'); ?></h3>
			<?php if (!empty($mypubs)) { ?>
			<ul class="mypubs">
				<?php

				foreach ($mypubs as $row)
				{
					$this->view('_item')
					     ->set('row', $row)
					     ->set('project', $this->project)
					     ->set('pub', $this->pub)
					     ->display();
				}
				?>
			</ul>
			<?php // Pagination
			$pageNav = new \Hubzero\Pagination\Paginator(
				$total,
				$filters['start'],
				$filters['limit']
			);
			echo $pageNav->render();
			?>
			</form>
			<?php } else { ?>
				<p class="noresults"><?php echo Lang::txt('PLG_PROJECTS_PUBLICATIONS_NO_RELEVANT_PUBS_FOUND'); ?></p>
			<?php } ?>
		<?php } ?>
	</div>
</div>