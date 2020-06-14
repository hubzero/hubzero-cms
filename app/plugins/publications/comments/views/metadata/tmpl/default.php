<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();
?>
<p class="comment">
	<a href="<?php echo $this->url; ?>"><?php echo Lang::txt('PLG_PUBLICATIONS_COMMENTS_NUM_COMMENTS', $this->comments); ?></a>
	(<a href="<?php echo $this->url_action; ?>"><?php echo Lang::txt('PLG_PUBLICATIONS_COMMENTS_POST_A_COMMENT'); ?></a>)
</p>
