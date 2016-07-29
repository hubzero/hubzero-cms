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
$defaultName = $this->params->get('default_connection_name', '%s Master Repository');
?>

<ul id="page_options" class="layout">
	<li>
		<a class="layout-control layout-large-icon first<?php echo ($layout == 'large-icon') ? ' active' : ''; ?>" data-class="large-icon" href="#"></a>
		<a class="layout-control layout-small-icon<?php echo ($layout == 'small-icon') ? ' active' : ''; ?>" data-class="small-icon" href="#"></a>
		<a class="layout-control layout-list last<?php echo ($layout == 'list') ? ' active' : ''; ?>" data-class="list" href="#"></a>
	</li>
</ul>

<div class="connections">
	<div class="connection-wrap default <?php echo $layout; ?>">
		<a class="connection" href="<?php echo Route::url($this->model->link('files') . '&action=browse'); ?>">
			<img src="/core/plugins/filesystem/local/assets/img/icon.png" alt="">
			<div class="name"><?php echo Lang::txt($defaultName, $this->model->get('title')); ?></div>
		</a>
	</div>

	<?php foreach ($this->connections as $connection) : ?>
		<?php $imgRel = '/plugins/filesystem/' . $connection->provider->alias . '/assets/img/icon.png'; ?>
		<?php $img = (is_file(PATH_APP . DS . $imgRel)) ? '/app' . $imgRel : '/core' . $imgRel; ?>
		<div class="connection-wrap <?php echo $layout; ?>">
			<a class="connection" href="<?php echo Route::url($this->model->link('files') . '&action=browse&connection=' . $connection->id); ?>">
				<?php if (!$connection->isShared()) : ?>
					<?php $hasPrivate = true; ?>
					<div class="private-connection"></div>
				<?php endif; ?>
				<img src="<?php echo $img; ?>" alt="" />
				<div class="name"><?php echo $connection->name; ?></div>
			</a>
			<div class="connection-actions">
				<a class="connection-edit icon-edit" title="<?php echo Lang::txt('Edit Connection'); ?>" href="<?php echo Route::url($this->model->link('files') . '&action=editconnection&connection=' . $connection->id); ?>">
					<?php echo Lang::txt('Edit'); ?>
				</a>
				<a class="connection-delete icon-delete" title="<?php echo Lang::txt('Delete Connection'); ?>" data-confirm="<?php echo Lang::txt('Are you sure you want to delete this connection?'); ?>" href="<?php echo Route::url($this->model->link('files') . '&action=deleteconnection&connection=' . $connection->id); ?>">
					<?php echo Lang::txt('Delete'); ?>
				</a>
			</div>
		</div>
	<?php endforeach; ?>

	<form class="connection-wrap new-connection <?php echo $layout; ?>" action="<?php echo Route::url($this->model->link('files') . '&action=newconnection'); ?>" method="post">
		<fieldset class="connection">
			<div class="new"></div>
			<div class="name">
				<select name="provider_id" class="connection-type">
					<option value=""><?php echo Lang::txt('New Connection'); ?></option>
					<?php foreach (\Components\Projects\Models\Orm\Provider::all() as $provider) : ?>
						<option value="<?php echo $provider->id; ?>"><?php echo $this->escape($provider->name); ?></option>
					<?php endforeach; ?>
				</select>
			</div>
		</fieldset>
	</form>

	<?php if ($hasPrivate) : ?>
		<div class="info private-explanation clear">
			<?php echo Lang::txt('PLG_PROJECTS_FILES_CONNECTIONS_PRIVATE_EXPLANATION'); ?>
		</div>
	<?php endif; ?>
</div>