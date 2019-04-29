<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

// Directory path breadcrumbs
$bc    = \Components\Projects\Helpers\Html::buildFileBrowserCrumbs($this->subdir, $this->url, $parent, false);
$bcEnd = $this->item->isDir() ? '<span class="folder">' . $this->item->getName() . '</span>' : '<span class="file">' . $this->item->getName() . '</span>';
$lang  = $this->item->isDir() ? 'folder' : 'file';

$dubCore = [
	'Contributor',
	'Coverage',
	'Creator',
	'Date',
	'Description',
	'Format',
	'Identifier',
	'Language',
	'Publisher',
	'Relation',
	'Rights',
	'Source',
	'Subject',
	'Title',
	'Type'
];
?>

<div id="abox-content">
	<h3>
		<?php echo Lang::txt('PLG_PROJECTS_FILES_ANNOTATE') . ' ' . $lang . ' ' . $bc . ' ' . $bcEnd; ?>
	</h3>
	<?php if ($this->getError()) : ?>
		<p class="witherror"><?php $this->getError(); ?></p>
	<?php else : ?>
		<form id="hubForm-ajax" method="post" action="<?php echo Route::url($this->url); ?>">
			<fieldset>
				<input type="hidden" name="subdir" value="<?php echo $this->subdir; ?>" />
				<input type="hidden" name="action" value="annotateit" />
				<input type="hidden" name="item" value="<?php echo $this->item->getName(); ?>" />

				<ul id="metadata-entries">
					<?php $i = 0; ?>
					<?php foreach ($dubCore as $element) : ?>
						<li>
							<div class="entry key-value-pair" data-idx="<?php echo $i; ?>">
								<div class="entry-label">
									<input class="dublin" type="text" name="key[<?php echo $i; ?>]" maxlength="250" value="<?php echo $element; ?>" readonly />
								</div>
								<div class="separator">:</div>
								<div class="entry-value">
									<input type="text" name="value[<?php echo $i; ?>]" value="<?php echo (isset($this->metadata[$element])) ? $this->metadata[$element] : ''; ?>" />
								</div>
							</div>
						</li>
						<?php $i++; ?>
						<?php if (isset($this->metadata[$element])) { unset($this->metadata[$element]); } ?>
					<?php endforeach; ?>
					<?php if (count($this->metadata) > 0) : ?>
						<?php foreach ($this->metadata as $metadata => $value) : ?>
							<li>
								<div class="entry key-value-pair" data-idx="<?php echo $i; ?>">
									<div class="entry-label">
										<input type="text" name="key[<?php echo $i; ?>]" maxlength="250" value="<?php echo $metadata; ?>" />
									</div>
									<div class="separator">:</div>
									<div class="entry-value">
										<input type="text" name="value[<?php echo $i; ?>]" value="<?php echo $value; ?>" />
									</div>
								</div>
							</li>
							<?php $i++; ?>
						<?php endforeach; ?>
					<?php endif; ?>
					<li>
						<div class="entry key-value-pair" data-idx="<?php echo $i; ?>">
							<div class="entry-label">
								<input type="text" name="key[<?php echo $i; ?>]" maxlength="250" value="" />
							</div>
							<div class="separator">:</div>
							<div class="entry-value">
								<input type="text" name="value[<?php echo $i; ?>]" value="" />
							</div>
						</div>
					</li>
				</ul>

				<div class="btn icon-add add-new-annotation">
					<?php echo Lang::txt('PLG_PROJECTS_FILES_ADD_ANNOTATION'); ?>
				</div>

				<div class="buttons">
					<input type="submit" class="btn" value="<?php echo Lang::txt('PLG_PROJECTS_FILES_SAVE'); ?>" />
					<input type="reset" class="btn btn-cancel" id="cancel-action" value="<?php echo Lang::txt('JCANCEL'); ?>" />
				</div>
			</fieldset>
		</form>
	<?php endif; ?>
</div>