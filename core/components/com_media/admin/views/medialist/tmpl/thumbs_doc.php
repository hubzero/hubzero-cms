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

//$this->_tmp_doc->name  = ltrim($this->_tmp_doc->name, DS);
//$this->_tmp_doc->title = ltrim($this->_tmp_doc->title, DS);

Event::trigger('onContentBeforeDisplay', array('com_media.file', &$this->_tmp_doc, &$params));
?>
		<div class="imgOutline">
			<div class="imgTotal">
				<div class="imgBorder">
					<a class="doc-item <?php echo Filesystem::extension($this->currentDoc['name']); ?>" title="<?php echo $this->currentDoc['name']; ?>" >
						<?php echo $this->currentDoc['name']; ?>
					</a>
				</div>
			</div>
			<div class="imginfoBorder" title="<?php echo $this->currentDoc['name']; ?>" >
				<?php if (User::authorise('core.delete', 'com_media')):?>
					<input type="checkbox" name="rm[]" value="<?php echo $this->currentDoc['name']; ?>" />
				<?php endif;?>
				<?php echo $this->currentDoc['name']; ?>
			</div>
		</div>
<?php
Event::trigger('onContentAfterDisplay', array('com_media.file', &$this->_tmp_doc, &$params));
