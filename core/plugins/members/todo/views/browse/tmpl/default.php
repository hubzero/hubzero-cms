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

$this->css('jquery.datepicker.css', 'system')
	 ->css('jquery.timepicker.css', 'system')
	 ->css()
	 ->css('todo.css', 'plg_projects_todo')
	 ->js()
	 ->js('jquery.timepicker', 'system');

$filters = array('projects' => $this->projects);
$url = 'index.php?option=com_members&id=' . $this->member->get('id') . '&active=todo';

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
<?php if (User::get('id') == $this->member->get('id') && empty($this->projects) || !$this->todo->entries('count', $filters)) { ?>

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
		$this->member->get('id'),
		$this->filters
	));
	echo !empty($results) && isset($results[0]) ? $results[0] : NULL;
	 ?>
</div>
<?php } ?>