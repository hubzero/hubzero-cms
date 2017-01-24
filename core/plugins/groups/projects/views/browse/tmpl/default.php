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
 * @author    Alissa Nedossekina <alisa@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

$this->css();
?>

<h3 class="section-header"><?php echo Lang::txt('PLG_GROUPS_PROJECTS'); ?></h3>

<?php if ($this->getError()) { ?>
	<p class="error"><?php echo $this->getError(); ?></p>
<?php } ?>

<?php if ($this->group->published == 1 && User::authorise('core.create', 'com_projects')) { ?>
	<ul id="page_options" class="pluginOptions">
		<li>
			<a class="icon-add add btn showinbox"  href="<?php echo Route::url('index.php?option=com_projects&task=start&gid=' . $this->group->get('gidNumber')); ?>">
				<?php echo Lang::txt('PLG_GROUPS_PROJECTS_ADD'); ?>
			</a>
		</li>
	</ul>
<?php } ?>

<?php 
// Output the submenu view
$view = $this->view('submenu', 'partials')
	->set('group', $this->group)
	->set('projectcount', $this->projectcount)
	->set('newcount', $this->newcount)
	->set('tab', 'all')
	->display();
?>

<section class="main section" id="s-projects">
	<div class="container">
		<!-- Placeholder for Group Dashboard -->
		<?php 
		$dashboards = Event::trigger('groups.onGroupClassroomprojects', array($this->group));
		foreach ($dashboards as $dashboard)
		{
			echo $dashboard;
		}
		?>
		<!-- End placeholder for Group Dashboard -->

		<?php
		if ($this->which == 'all')
		{
			// Show owned projects first
			$this->view('list')
			     ->set('option', $this->option)
			     ->set('rows', $this->owned)
			     ->set('config', $this->config)
			     ->set('which', 'owned')
			     ->display();

			echo '</div><div class="container">';
		}
		// Show rows
		$this->view('list')
		     ->set('option', $this->option)
		     ->set('rows', $this->rows)
		     ->set('config', $this->config)
		     ->set('which', $this->filters['which'])
		     ->display();
		?>
		</div>
</section><!-- /.main section -->
