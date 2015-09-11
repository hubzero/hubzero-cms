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
