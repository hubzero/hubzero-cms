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
 * @author    Sam Wilson <samwilson@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

$this->css()
     ->css('connections')
     ->js()
     ->js('connections');

$layout = Request::getCmd('layout', 'list');
$hasPrivate = false;
?>

<ul id="page_options" class="layout">
	<li>
		<a class="layout-control layout-large-icon first<?php echo ($layout == 'large-icon') ? ' active' : ''; ?>" data-class="large-icon" href="#"></a>
		<a class="layout-control layout-small-icon<?php echo ($layout == 'small-icon') ? ' active' : ''; ?>" data-class="small-icon" href="#"></a>
		<a class="layout-control layout-list last<?php echo ($layout == 'list') ? ' active' : ''; ?>" data-class="list" href="#"></a>
	</li>
</ul>

<div class="connections">
	<a class="connection default <?php echo $layout; ?>" href="<?php echo Route::url($this->model->link('files') . '&action=browse'); ?>">
		<img src="/core/plugins/filesystem/local/assets/img/icon.png" alt="">
		<div class="name"><?php echo $this->model->get('title'); ?> Master Repository</div>
	</a>

	<?php foreach ($this->connections as $connection) : ?>
		<?php $imgRel = '/plugins/filesystem/' . $connection->provider->alias . '/assets/img/icon.png'; ?>
		<?php $img = (is_file(PATH_APP . DS . $imgRel)) ? '/app' . $imgRel : '/core' . $imgRel; ?>
		<a class="connection <?php echo $layout; ?>" href="<?php echo Route::url($this->model->link('files') . '&action=browse&connection=' . $connection->id); ?>">
			<?php if (!$connection->isShared()) : ?>
				<?php $hasPrivate = true; ?>
				<div class="private-connection"></div>
			<?php endif; ?>
			<img src="<?php echo $img; ?>" alt="" />
			<div class="name"><?php echo $connection->name; ?></div>
		</a>
	<?php endforeach; ?>

	<!--<form class="connection new-connection <?php //echo $layout; ?>" action="<?php //echo Route::url($this->model->link('files') . '&action=newconnection'); ?>" method="POST">
		<div class="new"></div>
		<div class="name">
			<select name="provider_id" class="connection-type">
				<option value="">New Connection</option>
				<?php //foreach (\Components\Projects\Models\Orm\Provider::all() as $provider) : ?>
					<option value="<?php //echo $provider->id; ?>"><?php //echo $provider->name; ?></option>
				<?php //endforeach; ?>
			</select>
		</div>
	</form>-->

	<?php if ($hasPrivate) : ?>
		<div class="info private-explanation clear">
			<?php echo Lang::txt('PLG_PROJECTS_FILES_CONNECTIONS_PRIVATE_EXPLANATION'); ?>
		</div>
	<?php endif; ?>
</div>