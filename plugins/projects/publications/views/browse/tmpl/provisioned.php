<?php
/**
 * @package		HUBzero CMS
 * @author		Alissa Nedossekina <alisa@purdue.edu>
 * @copyright	Copyright 2005-2009 by Purdue Research Foundation, West Lafayette, IN 47906
 * @license		http://www.gnu.org/licenses/gpl-2.0.html GPLv2
 *
 * Copyright 2005-2009 by Purdue Research Foundation, West Lafayette, IN 47906.
 * All rights reserved.
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License,
 * version 2 as published by the Free Software Foundation.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );

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

		<div class="contrib-start simple">
			<p><?php echo Lang::txt('PLG_PROJECTS_PUBLICATIONS_CONTRIB_FROM_OUTSIDE'); ?></p>
			<p class="submitarea">
				<span><a href="<?php echo Route::url('index.php?option=com_publications&task=submit&action=publication&base=files'); ?>" class="btn btn-primary"><?php echo Lang::txt('PLG_PROJECTS_PUBLICATIONS_PUBLISH_FILES'); ?></a></span>
			</p>
		</div>
	</div>
	<div class="col span8 omega">
		<?php if (User::isGuest())
		{
			$this->view('intro')
			     ->set('project', $this->project)
			     ->set('pub', $this->pub)
			     ->display();
		} else
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