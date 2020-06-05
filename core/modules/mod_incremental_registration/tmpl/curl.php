<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// no direct access
defined('_HZEXEC_') or die();
?>
<div id="curl">
	<a href="<?php echo Route::url('index.php?option=com_members&id=' . $uid . '&active=profile'); ?>" class="whole">
		<img id="curl-img" data-img-small="<?php echo $this->media('/img/smallcurl.png'); ?>" data-img-big="<?php echo $this->media('/img/bigcurl.png'); ?>" src="<?php echo $this->media('/img/smallcurl.png'); ?>" class="small" title="<?php echo Lang::txt('MOD_INCREMENTAL_REGISTRATION_EARN_AWARDS'); ?>" />
	</a>
</div>
