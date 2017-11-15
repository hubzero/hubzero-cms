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

$google = $this->connect->getConfigs('google');
$dropbox = $this->connect->getConfigs('dropbox');

// Some connection active
$active = ($google['active'] || $dropbox['active']) ? 1 : 0;
$on = ($google['on'] || $dropbox['on']) ? 1 : 0;

// Project creator
$creator = ($this->model->access('owner')) ? 1 : 0;

$limited = $this->params->get('connectedProjects') ? \Components\Projects\Helpers\Html::getParamArray($this->params->get('connectedProjects')) : array();

$authorized = (empty($limited) || (!empty($limited) && in_array($this->model->get('alias'), $limited))) ? true : false;

$connected = (($google && $this->oparams->get('google_token')) || ($dropbox && $this->oparams->get('dropbox_token'))) ? 1 : 0;
?>
<?php if ($on && (($google || $dropbox) && $active || (!$active && $creator && $authorized))) { ?>
<p id="connector">
	<span>
		<?php if (!$active || !$connected) {  ?>
		<?php if ($google) { ?>
		<span class="google"></span>
		<?php } ?>
		<?php if ($dropbox) { ?>
		<span class="dropbox"></span>
		<?php } ?>
		<a href="<?php echo Route::url('index.php?option=' . $this->option . '&alias=' . $this->model->get('alias') . '&active=files&action=connect'); ?>"><?php echo Lang::txt('PLG_PROJECTS_FILES_CONNECT'); ?></a>
		<?php }
			// Connected to Google
			if ($this->oparams->get('google_token') && $active) {  ?>
				<span class="connect-email"><span class="google"></span> <?php echo $this->oparams->get('google_email'); ?> <a href="<?php echo Route::url('index.php?option=' . $this->option . '&alias=' . $this->model->get('alias') . '&active=files') . '?action=connect'; ?>">[&raquo;]</a></span>
		<?php } ?>
	</span>
</p>
<?php } else { ?>
	<p class="editing mini pale"><?php echo Lang::txt('PLG_PROJECTS_FILES_MAX_UPLOAD') . ' ' . $this->sizelimit; ?></p>
<?php }
