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
 * @author    Shawn Rice <zooley@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// No direct access
defined('_HZEXEC_') or die();

$this->css('jquery.datepicker.css', 'system')
	 ->css('jquery.timepicker.css', 'system')
	 ->css()
	 ->css('todo.css', 'projects', 'todo')
	 ->js()
	 ->js('jquery.timepicker', 'system');

$filters = array('projects' => $this->projects);
$url = 'index.php?option=com_members&id=' . $this->member->get('uidNumber') . '&active=todo';

$cfilters = array(
	'mine'  => 1,
	'active'=> 1,
	'editor'=> 1
);

?>
<h3 class="section-header">
	<?php echo Lang::txt('PLG_MEMBERS_TODO'); ?>
</h3>
<?php if ($this->model->entries('count', $cfilters)) { ?>
	<ul id="page_options" class="pluginOptions">
		<li>
			<a class="icon-add add btn showinbox"  href="<?php echo Route::url($url . '&action=new'); ?>">
				<?php echo Lang::txt('PLG_MEMBERS_TODO_ADD_TODO'); ?>
			</a>
		</li>
	</ul>
<?php } ?>
<?php if (User::get('id') == $this->member->get('uidNumber') && empty($this->projects) || !$this->todo->entries('count', $filters)) { ?>

	<div class="introduction">
		<div class="introduction-message">
			<p><?php echo Lang::txt('PLG_MEMBERS_TODO_INTRO_EMPTY'); ?></p>
		</div>
		<div class="introduction-questions">
			<p><strong><?php echo Lang::txt('PLG_MEMBERS_TODO_INTRO_HOW_TO_START'); ?></strong></p>
			<p><?php echo Lang::txt('PLG_MEMBERS_TODO_INTRO_HOW_TO_START_EXPLANATION', Route::url('index.php?option=com_projects')); ?></p>
		</div>
	</div><!-- / .introduction -->

<?php } else { ?>
<div class="container">
	<?php if ($this->getError()) : ?>
		<p class="error"><?php echo $this->getError(); ?></p>
	<?php endif; ?>
	<?php 
	// Get shared todo items from blog plugin
	$results = Event::trigger( 'projects.onShared', array(
		'todo',
		$this->model,
		$this->projects,
		$this->member->get('uidNumber'),
		$this->filters
	));
	echo !empty($results) && isset($results[0]) ? $results[0] : NULL;
	 ?>
</div>
<?php } ?>