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

$this->css()
     ->js();

// Check who suspended project
$log = \Hubzero\Activity\Log::all();

$l = $log->getTableName();
$r = \Hubzero\Activity\Recipient::blank()->getTableName();

$result = $log
	->join($r, $r . '.log_id', $l . '.id', 'inner')
	->whereEquals($r . '.scope', 'project')
	->whereEquals($r . '.scope_id', $this->model->get('id'))
	->whereEquals($l . '.description', Lang::txt('COM_PROJECTS_ACTIVITY_PROJECT_SUSPENDED'))
	->order($l . '.created', 'desc')
	->row();

$suspended = null;
if ($result)
{
	$suspended = $result->details->get('admin');
}
?>
<div id="project-wrap">
	<section class="main section">
		<form method="post" action="<?php echo Route::url('index.php?option=' . $this->option . '&alias=' . $this->model->get('alias')); ?>">
			<fieldset >
				<input type="hidden" name="id" value="<?php echo $this->model->get('id'); ?>" />
				<input type="hidden" name="task" value="reinstate" />
				<input type="hidden" name="option" value="<?php echo $this->option; ?>" />

				<?php
					$this->view('_header')
					     ->set('model', $this->model)
					     ->set('showPic', 1)
					     ->set('showPrivacy', 0)
					     ->set('goBack', 0)
					     ->set('showUnderline', 1)
					     ->set('option', $this->option)
					     ->display();
				?>

				<p class="warning">
					<?php echo $suspended == 2 ? Lang::txt('COM_PROJECTS_CANCEL_SUSPENDED_PROJECT') : Lang::txt('COM_PROJECTS_CANCEL_SUSPENDED_PROJECT_ADMIN'); ?> <?php if (!$this->model->access('manager') && $suspended == 2) { ?><?php echo Lang::txt('COM_PROJECTS_CANCEL_SUSPENDED_PROJECT_NO_MANAGER'); ?><?php } ?>
				</p>

				<?php if ($this->model->access('manager') && $suspended == 2) { ?>
					<h4><?php echo Lang::txt('COM_PROJECTS_CANCEL_WANT_TO_REINSTATE'); ?></h4>
					<p>
						<span><input type="submit" class="confirm" value="<?php echo Lang::txt('COM_PROJECTS_CANCEL_YES_REINSTATE'); ?>" /></span>
					</p>
					<p>
						<?php echo ucfirst(Lang::txt('COM_PROJECTS_CANCEL_PERMANENTLY')); ?>, <?php echo Lang::txt('COM_PROJECTS_CANCEL_YOU_CAN_ALSO'); ?> <a href="<?php echo Route::url('index.php?option=com_support&controller=tickets&task=new'); ?>"><?php echo Lang::txt('COM_PROJECTS_CANCEL_CONTACT_ADMIN'); ?></a>
					</p>
				<?php } ?>
			</fieldset>
		</form>
	</section><!-- / .main section -->
</div>