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
 * @author    Christopher Smoak <csmoak@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

$this->css('introduction', 'system')
     ->css('intro')
     ->css();
?>

<header id="content-header">
	<h2><?php echo Lang::txt('COM_DEVELOPER_API_HOME'); ?></h2>

	<div id="content-header-extra">
		<p>
			<a class="btn icon-code" href="<?php echo Route::url('index.php?option=com_developer'); ?>">
				<?php echo Lang::txt('COM_DEVELOPER'); ?>
			</a>
		</p>
	</div>
</header>

<section id="introduction" class="section api">
	<div class="section-inner">
		<div class="grid">
			<div class="col span8">
				<h3><?php echo Lang::txt('COM_DEVELOPER_API_GETSTARTED'); ?></h3>
				<p><?php echo Lang::txt('COM_DEVELOPER_API_GETSTARTED_DESC'); ?></p>
			</div>
			<div class="col span4 hasOnlyButton omega">
				<p>
					<a class="btn icon-docs" href="<?php echo Route::url('index.php?option=com_developer&controller=api&task=docs'); ?>">
						<?php echo Lang::txt('COM_DEVELOPER_API_LINK_DOCUMENTATION'); ?>
					</a>
				</p>
			</div>
		</div>
	</div>
</section>

<section class="main section">
	<div class="section-inner">
		<div class="grid">
			<div class="col span3">
				<h2><?php echo Lang::txt('COM_DEVELOPER_API_APPLICATIONS'); ?></h2>
			</div>
			<div class="col span3">
				<h3><a href="<?php echo Route::url('index.php?option=com_developer&controller=applications'); ?>"><?php echo Lang::txt('COM_DEVELOPER_API_MY_APPLICATIONS'); ?></a></h3>
				<p><?php echo Lang::txt('COM_DEVELOPER_API_MY_APPLICATIONS_DESC'); ?></p>
				<p><a href="<?php echo Route::url('index.php?option=com_developer&controller=applications'); ?>"><?php echo Lang::txt('COM_DEVELOPER_API_MANAGE'); ?></a></p>
			</div>
			<div class="col span3">
				<h3><a href="<?php echo Route::url('index.php?option=com_developer&controller=applications#authorized'); ?>"><?php echo Lang::txt('COM_DEVELOPER_API_AUTHORIZED_APPLICATIONS'); ?></a></h3>
				<p><?php echo Lang::txt('COM_DEVELOPER_API_AUTHORIZED_APPLICATIONS_DESC'); ?></p>
				<p><a href="<?php echo Route::url('index.php?option=com_developer&controller=applications#authorized'); ?>"><?php echo Lang::txt('COM_DEVELOPER_API_MANAGE'); ?></a></p>
			</div>
			<div class="col span3 omega">
				<h3><a href="<?php echo Route::url('index.php?option=com_developer&controller=applications&task=new'); ?>"><?php echo Lang::txt('COM_DEVELOPER_API_NEW_APPLICATION'); ?></a></h3>
				<p><?php echo Lang::txt('COM_DEVELOPER_API_NEW_APPLICATION_DESC'); ?></p>
				<p><a href="<?php echo Route::url('index.php?option=com_developer&controller=applications&task=new'); ?>"><?php echo Lang::txt('COM_DEVELOPER_API_CREATE'); ?></a></p>
			</div>
		</div>
		<?php /*<div class="grid">
			<div class="col span3">
				<h2><?php echo Lang::txt('COM_DEVELOPER_API_LEARN'); ?></h2>
			</div>
			<div class="col span3">
				<h3><a href="<?php echo Route::url('index.php?option=com_developer&controller=api&task=docs'); ?>"><?php echo Lang::txt('COM_DEVELOPER_API_DOCS'); ?></a></h3>
				<p><?php echo Lang::txt('COM_DEVELOPER_API_DOCS_DESC'); ?></p>
				<p><a href="<?php echo Route::url('index.php?option=com_developer&controller=api&task=docs'); ?>"><?php echo Lang::txt('COM_DEVELOPER_API_MANAGE'); ?></a></p>
			</div>
			<div class="col span3">
				<h3><a href="<?php echo Route::url('index.php?option=com_developer&controller=api&task=console'); ?>"><?php echo Lang::txt('COM_DEVELOPER_API_CONSOLE'); ?></a></h3>
				<p><?php echo Lang::txt('COM_DEVELOPER_API_CONSOLE_DESC'); ?></p>
				<p><a href="<?php echo Route::url('index.php?option=com_developer&controller=api&task=console'); ?>"><?php echo Lang::txt('COM_DEVELOPER_API_MANAGE'); ?></a></p>
			</div>
			<div class="col span3 omega">
				<h3><a href="<?php echo Route::url('index.php?option=com_developer&controller=api&task=status'); ?>"><?php echo Lang::txt('COM_DEVELOPER_API_STATUS'); ?></a></h3>
				<p><?php echo Lang::txt('COM_DEVELOPER_API_STATUS_DESC'); ?></p>
				<p><a href="<?php echo Route::url('index.php?option=com_developer&controller=api&task=status'); ?>"><?php echo Lang::txt('COM_DEVELOPER_API_CREATE'); ?></a></p>
			</div>
		</div>*/ ?>
	</div>
</section>