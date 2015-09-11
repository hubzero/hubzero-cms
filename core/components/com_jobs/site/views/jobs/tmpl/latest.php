<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2015 HUBzero Foundation, LLC.
 *
 * This file is part of: The HUBzero(R) Platform for Scientific Collaboration
 *
 * The HUBzero(R) Platform for Scientific Collaboration (HUBzero) is free
 * software: you can redistribute it and/or modify it under the terms of
 * the GNU Lesser General Public License as published by the Free Software
 * Foundation, either version 3 of the License, or (at your option) any
 * later version.
 *
 * HUBzero is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * HUBzero is a registered trademark of Purdue University.
 *
 * @package   hubzero-cms
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   GNU General Public License, version 2 (GPLv2)
 */

// No direct access
defined('_HZEXEC_') or die();

?>

<section class="main section">
	<h3><?php echo Lang::txt('COM_JOBS_LATEST_POSTINGS'); ?></h3>
	<form method="get" action="<?php echo Route::url('index.php?option=' . $this->option . '&task=browse'); ?>">
	<?php if (count($this->jobs) > 0) { ?>
		<?php // Display List of items
			$view = new \Hubzero\Component\View(array(
				'base_path' => PATH_CORE . DS . 'components' . DS . 'com_jobs' . DS . 'site',
				'name'      => 'jobs',
				'layout'    => '_list'
				)
			);
			$view->set('option', $this->option)
		     ->set('filters', $this->filters)
		     ->set('config', $this->config)
		     ->set('task', $this->task)
		     ->set('emp', $this->emp)
		     ->set('mini', 1)
			 ->set('jobs', $this->jobs)
		     ->set('admin', $this->admin)
		     ->display();
		?>
		<?php } else { ?>
		<p><?php echo Lang::txt('COM_JOBS_NO_JOBS_FOUND'); ?></p>
		<?php } ?>
	</form>
</section>
