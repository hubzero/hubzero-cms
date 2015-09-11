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

defined('_HZEXEC_') or die();

?>
<header id="content-header">
	<h2><?php echo Lang::txt('COM_DEVELOPER_API_OAUTH_AUTHORIZATION_NEEDED'); ?></h2>
</header>

<section class="main section">
	<div class="section-inner">
		<p><?php echo Lang::txt('COM_DEVELOPER_API_OAUTH_AUTHORIZATION_NEEDED_DESC', $this->application->get('name')); ?></p>
		<form action="<?php echo Route::url('index.php?option=' . $this->option); ?>" id="oauth_form" method="post">
			<fieldset class="buttons">
				<button type="submit" name="authorize" value="1" class="btn btn-success"><?php echo Lang::txt('Authorize'); ?></button>
				<button type="submit" name="authorize" value="0" class="btn btn-danger btn-secondary"><?php echo Lang::txt('No Thanks'); ?></button>
			</fieldset>
			<input type="hidden" name="option" value="com_developer" />
			<input type="hidden" name="controller" value="oauth" />
			<input type="hidden" name="task" value="doauthorize" />
			<input type="hidden" name="client_id" value="<?php echo $this->application->get('client_id'); ?>" />
			<input type="hidden" name="response_type" value="<?php echo $this->escape(Request::getWord('response_type', '')); ?>" />
			<input type="hidden" name="redirect_uri" value="<?php echo $this->escape(Request::getVar('redirect_uri', '')); ?>" />
			<input type="hidden" name="state" value="<?php echo $this->escape(Request::getCmd('state', '')); ?>" />
		</form>
	</div>
</section>