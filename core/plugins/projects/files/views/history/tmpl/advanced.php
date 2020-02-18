<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

$this->css()
     ->js();

$v = count($this->versions) + 1;

// Directory path breadcrumbs
$bc = \Components\Projects\Helpers\Html::buildFileBrowserCrumbs($this->subdir, $this->url, $parent);

$i       = 0;
$shown   = 0;
$skipped = 0;
$locals  = 0;
$sLocals = 0;

$candiff = count($this->versions);
foreach ($this->versions as $version)
{
	if ($version['hide'] == 1 || $version['commitStatus'] == 'D' || $version['remote'])
	{
		$candiff--;
	}
	if (!$version['remote'])
	{
		$locals++;
	}
}

$endPath = ' &raquo; <span class="subheader">' . Lang::txt('PLG_PROJECTS_FILES_SHOW_REV_HISTORY_FOR') . ' <span class="italic">' . \Components\Projects\Helpers\Html::shortenFileName($this->file->get('name'), 40) . '</span></span>';

$allowDiff = ($this->file->isBinary() || $this->file->get('converted') || $candiff <= 1 ) ? 0 : 1;

?>
<?php if ($this->ajax) { ?>
<div id="abox-content">
<h3><?php echo Lang::txt('PLG_PROJECTS_FILES_SHOW_HISTORY'); ?></h3>
<?php
// Display error
if ($this->getError()) {
	echo '<p class="witherror">' . $this->getError() . '</p>';
}
?>
<?php } ?>

<form id="<?php echo $this->ajax ? 'hubForm-ajax' : 'plg-form'; ?>" method="get" action="<?php echo $this->url; ?>">
	<?php if (!$this->ajax) { ?>
		<div id="plg-header">
			<h3 class="files">
				<a href="<?php echo $this->url; ?>"><?php echo $this->title; ?></a><?php if ($this->subdir) { ?> <?php echo $bc; ?><?php } ?>
			<?php echo $endPath; ?>
			</h3>
		</div>
	<?php } ?>

	<fieldset >
		<input type="hidden" name="subdir" value="<?php echo $this->subdir; ?>" />
		<input type="hidden" name="file" value="<?php echo $this->file->get('name'); ?>" />
		<input type="hidden" name="action" value="diff" />
			<ul class="sample">
				<?php
					$extras =  ($allowDiff && !$this->getError()) ? '<input type="submit" id="rundiff" value="' . Lang::txt('PLG_PROJECTS_FILES_DIFF_REVISIONS') . '" class="btn rightfloat" />' : '';

					// Display list item with file data
					$this->view('default', 'selected')
					     ->set('skip', false)
					     ->set('file', $this->file)
					     ->set('action', 'history')
					     ->set('multi', 'multi')
					     ->set('extras', $extras)
					     ->display();
				?>
			</ul>

			<?php if (!$this->getError()) { ?>
			<table class="revisions">
				<thead>
					<tr>
						<?php if ($allowDiff) { ?>
						<th>Rev</th>
						<?php } ?>
						<th><?php echo Lang::txt('PLG_PROJECTS_FILES_REVISION_OWNER'); ?></th>
						<?php if ($allowDiff) { ?>
						<th class="diffing">Diff</th>
						<?php } ?>
						<th><?php echo Lang::txt('PLG_PROJECTS_FILES_REVISION_DIFF'); ?></th>
						<th><?php echo Lang::txt('PLG_PROJECTS_FILES_REVISION_OPTIONS'); ?></th>
					</tr>
				</thead>
				<tbody>
			<?php foreach ($this->versions as $version) {

				if ($version['hide'] == 1)
				{
					$skipped++;
					continue;
				}
				$last = $i == 0 ? true : false;

				$origin = $version['remote']
					? Lang::txt('PLG_PROJECTS_FILES_FILE_STATUS_REMOTE')
					: Lang::txt('PLG_PROJECTS_FILES_FILE_STATUS_LOCAL');
				if (!$version['remote'] && preg_match("/SFTP/", $version['message']))
				{
					$origin = 'SFTP';
				}
				$status = '<span class="commit-type">[' . $origin . ']</span> ';
				$name   = $version['remote'] && $this->file->get('remote') ? $this->file->get('remoteTitle') : $version['name'];

				// Get url, name and status
				if ($version['remote'])
				{
					$url = $this->url
						. '/?action=open&amp;subdir=' . urlencode($this->subdir)
						. '&amp;file=' . urlencode($version['file']);

					if ($this->connected && $last == true)
					{
						$action  = '<a href="' . $url . '" class="open_file" title="'
							. Lang::txt('PLG_PROJECTS_FILES_REMOTE_OPEN') . '" target="_blank" rel="noopener noreferrer external">&nbsp;</a>';
					}
					else
					{
						$action  = '';
					}
				}
				else
				{
					$url = $this->url
						.'/?asset=' . urlencode($version['name'])
						. '&amp;action=download&amp;hash=' . $version['hash'];
					$action = (in_array($version['commitStatus'], array('A', 'M', 'R', 'W')))
						? '<a href="' . $url .'" class="download_file" title="' . Lang::txt('PLG_PROJECTS_FILES_DOWNLOAD') . '" >&nbsp;</a>'
						: '';
				}

				if ($version['change'])
				{
					// Other type of change
					$status .= ' ' . $version['change'];
				}

				if ($last)
				{
					$status .= ' <span class="crev">' . Lang::txt('PLG_PROJECTS_FILES_FILE_STATUS_CURRENT') . '</span>';
				}

				$charLimit = $last == true ? 400 : 400;

				$trclass = $last ? 'current-revision' : '';
				$trclass = $version['commitStatus'] == 'D' ? 'deleted-revision' : $trclass;

				$v--;

				if ($version['commitStatus'] == 'D')
				{
					$skipped++;
					continue;
				}

				$shown++;

				if (!$version['remote'])
				{
					$sLocals++;
				}

				// Oldest local shown?
				$oldest = (!$version['remote'] && ((($skipped + $shown) == count($this->versions))
							|| $sLocals == $locals)) ? true : false;

				?>
				<tr <?php if ($trclass) { echo 'class="' . $trclass . '"'; } ?>>
					<?php if ($allowDiff) { ?>
					<td><?php echo '@'.$v; ?></td>
					<?php } ?>
					<td class="commit-actor"><span class="prominent"><?php echo \Components\Projects\Helpers\Html::formatTime($version['date'], true); ?></span>
						<span class="block"><?php echo $version['author'] ? $version['author'] : $version['email']; ?></span>
					</td>
					<?php if ($allowDiff) { ?>
					<td class="diffing">
						<?php if (!$version['remote'] && count($this->versions) > 1) { ?>
						<input type="radio" value="<?php echo urlencode($v . '@' . substr($version['hash'], 0, 10) . '@' . $version['name'] ); ?>" name="old" <?php if ($oldest) { echo 'checked="checked"'; } ?> <?php if ($last) { echo 'disabled="disabled"'; } ?> class="diff-old" />
						<input type="radio" value="<?php echo urlencode($v . '@' . substr($version['hash'], 0, 10) . '@' . $version['name'] ); ?>" name="new" <?php if ($last) { echo 'checked="checked"'; } ?> <?php if ($oldest) { echo 'disabled="disabled"'; } ?> class="diff-new" />
						<?php } ?>
					</td>
					<?php } ?>
					<td class="commit-details">
							<?php if ($version['movedTo']) { ?>
								<span class="moved"><span class="<?php echo $version['movedTo'] == 'remote' ? 'send_to_remote' : 'send_to_local'; ?>"><span>&nbsp;</span></span></span>
							<?php } ?>
						<span class="commitstatus"><?php echo $status; ?></span>
						<span class="block italic faded">
							<?php echo $version['name']; ?>
							<?php echo $version['size'] ? ', ' . $version['size'] : '';  ?>
						</span>
						<div class="commitcontent"><?php if ($version['content'] && in_array($version['commitStatus'], array('A', 'M')))
						{
							$over = strlen($version['content']) >= $charLimit ? 1 : 0;
							$content = $over ? \Hubzero\Utility\Str::truncate($version['content'], $charLimit) : $version['content'];

							echo '<div class="short-txt" id="short-' . $i . '"><pre>' . $content . '</pre>';
							if ($over)
							{
								echo '<p class="showaslink showmore js">' . Lang::txt('PLG_PROJECTS_FILES_SHOW_MORE') . '</p>';
							}
							echo '</div>';
							if ($over)
							{
								echo '<div class="long-txt hidden" id="long-' . $i . '"><pre>' . $version['content'] . '</pre>';
								echo '<p class="showaslink showless">' . Lang::txt('PLG_PROJECTS_FILES_SHOW_LESS') . '</p>';
								echo '</div>';
							}
						}
						?>
						<?php if ($version['preview'] && $version['commitStatus'] != 'D') { ?>
							<div id="preview-image">
								<img src="<?php echo $version['preview']; ?>" alt="<?php echo Lang::txt('PLG_PROJECTS_FILES_LOADING_PREVIEW'); ?>" />
							</div>
						<?php } ?>
						</div>
					</td>
					<td class="commit-options">
						<?php echo $action; ?>
					</td>
				</tr>
			<?php $i++; } ?>
				</tbody>
			</table>
			<?php } ?>
		</fieldset>
</form>
<?php if ($this->ajax) { ?>
</div>
<?php }
