<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

$this->css('video.css');

$source = with(new \Hubzero\Content\Moderator($this->file->getAbsolutePath()))->getUrl();
?>
<div class="file-preview video">
	<div class="file-preview-code">
		<video src="<?php echo $source; ?>" controls>
			<p><?php echo Lang::txt('Your browser doesn\'t support HTML5 video. Here is a <a href="%s">link to the video</a> instead.', $source); ?></p>
		</video>
	</div>
</div>