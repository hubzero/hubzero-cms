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

$html  = '';

$rtrn = Request::getString('REQUEST_URI', Route::url('index.php?option=' . $this->option . '&task=' . $this->task), 'server');

?>
<div id="project-wrap">
	<section class="main section">
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
		<h3><?php echo Lang::txt('COM_PROJECTS_INVITED_CONFIRM'); ?></h3>
		<div id="confirm-invite" class="invitation">
			<div class="grid">
				<div class="col span6">
					<p>
						<?php echo Lang::txt('COM_PROJECTS_INVITED_CONFIRM_SCREEN') . ' "' . $this->model->get('title') . '". ' . Lang::txt('COM_PROJECTS_INVITED_NEED_ACCOUNT_TO_JOIN'); ?>
					</p>
				</div>
				<div class="col span6 omega">
					<p>
						<?php echo Lang::txt('COM_PROJECTS_INVITED_HAVE_ACCOUNT') . ' <a href="' . Route::url('index.php?option=com_users&view=login&return=' . base64_encode($rtrn)) .  '">' . Lang::txt('COM_PROJECTS_INVITED_PLEASE_LOGIN') . '</a>'; ?>
					</p>
					<p>
						<?php echo Lang::txt('COM_PROJECTS_INVITED_DO_NOT_HAVE_ACCOUNT') . ' <a href="' . Route::url('index.php?option=com_members&controller=register&return=' . base64_encode($rtrn)) .  '">' . Lang::txt('COM_PROJECTS_INVITED_PLEASE_REGISTER') . '</a>'; ?>
					</p>
				</div>
			</div>
		</div>
	</section><!-- / .main section -->
</div>