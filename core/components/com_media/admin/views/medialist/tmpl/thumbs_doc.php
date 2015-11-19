<?php
/**
 * @package		Joomla.Administrator
 * @subpackage	com_media
 * @copyright	Copyright (C) 2005 - 2014 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access.
defined('_HZEXEC_') or die();

$params = new \Hubzero\Config\Registry;

$this->_tmp_doc->name  = ltrim($this->_tmp_doc->name, DS);
$this->_tmp_doc->title = ltrim($this->_tmp_doc->title, DS);

Event::trigger('onContentBeforeDisplay', array('com_media.file', &$this->_tmp_doc, &$params));
?>
		<div class="imgOutline">
			<div class="imgTotal">
				<div class="imgBorder">
					<a class="doc-item <?php echo Filesystem::extension($this->_tmp_doc->name); ?>" title="<?php echo $this->_tmp_doc->name; ?>" >
						<?php echo $this->_tmp_doc->title; ?>
					</a>
				</div>
			</div>
			<div class="controls">
			<?php if (User::authorise('core.delete', 'com_media')):?>
				<a class="delete-item" target="_top" href="<?php echo Route::url('index.php?option=com_media&task=file.delete&tmpl=index&' . Session::getFormToken() . '=1&folder=' . $this->state->folder . '&rm[]=' . $this->_tmp_doc->name); ?>" rel="<?php echo $this->_tmp_doc->name; ?>">
					<?php echo Lang::txt('JACTION_DELETE'); ?>
				</a>
				<input type="checkbox" name="rm[]" value="<?php echo $this->_tmp_doc->name; ?>" />
			<?php endif;?>
			</div>
			<div class="imginfoBorder" title="<?php echo $this->_tmp_doc->name; ?>" >
				<?php echo $this->_tmp_doc->title; ?>
			</div>
		</div>
<?php
Event::trigger('onContentAfterDisplay', array('com_media.file', &$this->_tmp_doc, &$params));
