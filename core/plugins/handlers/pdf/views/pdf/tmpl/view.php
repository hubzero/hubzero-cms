<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

$width  = '724';
$height = '1024';

$source = with(new \Hubzero\Content\Moderator($this->file->getAbsolutePath()))->getUrl();
?>
<div class="file-preview pdf">
	<div class="compiled-doc" embed-src="<?php echo $source; ?>" embed-width="<?php echo $width; ?>" embed-height="<?php echo $height; ?>">
		<object class="width-container" width="<?php echo $width; ?>" height="<?php echo $height; ?>" type="<?php echo $this->file->getMimetype(); ?>" data="<?php echo $source; ?>" id="pdf_content">
			<embed src="<?php echo $source; ?>" type="<?php echo $this->file->getMimetype(); ?>" />
		</object>
	</div>
</div>