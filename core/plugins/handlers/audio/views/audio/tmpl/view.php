<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

$this->css('audio.css');

$source = with(new \Hubzero\Content\Moderator($this->file->getAbsolutePath()))->getUrl();
?>
<div class="file-preview video">
	<div class="file-preview-code">
		<audio src="<?php echo $source; ?>" controls>
			<p><?php echo Lang::txt('Your browser doesn\'t support HTML5 audio. Here is a <a href="%s">link to the audio</a> instead.', $source); ?></p>
		</audio>
	</div>
</div>