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

require_once Component::path('com_tools') . '/models/orm/handler.php';

use \Components\Tools\Models\Orm\Handler;

$handlerBase = DS . trim($this->fileparams->get('handler_base_path', 'srv/projects'), DS) . DS;


$me = ($this->item->get('email') == User::get('email')
	|| $this->item->get('author') == User::get('name'))  ? 1 : 0;
$when = $this->item->get('date') ? \Components\Projects\Helpers\Html::formatTime($this->item->get('date')) : 'N/A';
$subdirPath = $this->subdir ? '&subdir=' . urlencode($this->subdir) : '';

$link = Route::url($this->model->link('files') . '&action=' . (($this->item->get('converted')) ? 'open' : 'download') . $subdirPath . '&asset=' . urlencode($this->item->get('name')));

// Do not display Google native extension
$name = $this->item->get('name');
if ($this->item->get('remote'))
{
	$native = \Components\Projects\Helpers\Google::getGoogleNativeExts();
	if (in_array($this->item->get('ext'), $native))
	{
		$name = preg_replace("/." . $this->item->get('ext') . "\z/", "", $this->item->get('name'));

		// Attempt to build external URLs to Google services
		if (isset($this->params['remoteConnections']))
		{
			if (isset($this->params['remoteConnections'][$this->item->get('localPath')]))
			{
				$remote = $this->params['remoteConnections'][$this->item->get('localPath')];

				if ($remote->service == 'google')
				{
					switch ($this->item->get('ext'))
					{
						case 'gdoc':
							$link = 'https://docs.google.com/document/d/' . $remote->remote_id;
							break;
						case 'gslides':
							$link = 'https://docs.google.com/presentation/d/' . $remote->remote_id;
							break;
						case 'gsheet':
							$link = 'https://docs.google.com/spreadsheets/d/' . $remote->remote_id;
							break;
						default:
							break;
					}
				}
			}
		}
	}
}
$ext = $this->item->get('type') == 'file' ? $this->item->get('ext') : 'folder';
?>
<tr class="mini faded mline">
	<?php 
	if ($this->model->access('content'))
	{
	?>
	<td>
		<?php 
			$checkasset = "";
			if ($this->item->get('type') == 'folder')
			{
				$checkasset = ' dir';
			}
			else
			{
				if ($this->item->get('untracked'))
				{
					$checkasset .= ' untracked';
				}
				if ($this->item->get('converted'))
				{
					$checkasset .= ' remote service-google';
				}
			}
		?>
		<input type="checkbox" value="<?php echo urlencode($this->item->get('name')); ?>" name="<?php echo $this->item->get('type') == 'file' ? 'asset[]' : 'folder[]'; ?>" class="checkasset js<?php echo $checkasset ?>" />
	</td>
	<?php } ?>
	<td class="middle_valign nobsp is-relative">
		<?php echo $this->item->drawIcon($ext); ?>
		<?php if ($this->item->get('type') == 'file') { ?>
			<div class="file-action-dropdown<?php echo ($handlers = Handler::getLaunchUrlsForFile($handlerBase . $this->model->get('alias') . '/' . $this->item->get('name'))) ? ' hasMultiple' : ''; ?>">
				<a href="<?php echo $link; ?>" class="preview file:<?php echo urlencode($this->item->get('name')); ?>"<?php echo $this->item->get('converted') ? ' target="_blank"' : ''; ?>>
					<?php echo \Components\Projects\Helpers\Html::shortenFileName($name, 60); ?>
				</a>
				<?php if ($handlers && count($handlers) > 0) : ?>
					<?php foreach ($handlers as $handler) : ?>
					<a href="<?php echo Route::url($handler['url']); ?>">
						<?php echo $handler['prompt']; ?>
					</a>
					<?php endforeach; ?>
				<?php endif; ?>
			</div>
		<?php } else { ?>
			<a href="<?php echo Route::url($this->model->link('files') . '/&action=browse&subdir=' . urlencode($this->item->get('localPath'))); ?>" class="dir:<?php echo urlencode($this->item->get('name')); ?>" title="<?php echo Lang::txt('PLG_PROJECTS_FILES_GO_TO_DIR') . ' ' . $this->item->get('name'); ?>"><?php echo \Components\Projects\Helpers\Html::shortenFileName($this->item->get('name'), 60); ?></a>
		<?php } ?>
	</td>
	<td class="shrinked middle_valign"></td>
	<td class="shrinked middle_valign"><?php echo $this->item->getSize(true); ?></td>
	<td class="shrinked middle_valign">
	<?php if (!$this->item->get('untracked')) { ?>
		<?php if ($this->item->get('type') == 'file' && $this->params['versionTracking'] == '1') { ?>
			<a href="<?php echo Route::url($this->model->link('files') . '&action=history' . $subdirPath . '&asset=' . urlencode($this->item->get('name'))); ?>" title="<?php echo Lang::txt('PLG_PROJECTS_FILES_HISTORY_TOOLTIP'); ?>"><?php echo $when; ?></a>
		<?php } else { ?>
			<?php echo $when; ?>
		<?php } ?>
	<?php } elseif ($this->item->get('untracked')) { echo Lang::txt('PLG_PROJECTS_FILES_UNTRACKED'); } ?>
	</td>
	<td class="shrinked middle_valign"><?php echo $me ? Lang::txt('PLG_PROJECTS_FILES_ME') : $this->item->get('author'); ?></td>
	<td class="shrinked middle_valign nojs">
		<?php if ($this->model->access('content')) { ?>
			<a href="<?php echo Route::url($this->model->link('files') . '&action=delete' . $subdirPath . '&asset=' . urlencode($this->item->get('name'))); ?>" title="<?php echo Lang::txt('PLG_PROJECTS_FILES_DELETE_TOOLTIP'); ?>" class="i-delete">&nbsp;</a>
			<a href="<?php echo Route::url($this->model->link('files') . '&action=move' . $subdirPath . '&asset=' . urlencode($this->item->get('name'))); ?>" title="<?php echo Lang::txt('PLG_PROJECTS_FILES_MOVE_TOOLTIP'); ?>" class="i-move">&nbsp;</a>
		<?php } ?>
	</td>
	<?php if ($this->publishing) { ?>
		<td class="shrinked"></td>
	<?php } ?>
</tr>
