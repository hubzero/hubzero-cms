<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();
?>
<p class="review">
	<a href="<?php echo $this->url; ?>"><?php echo Lang::txt('PLG_PUBLICATIONS_REVIEWS_NUM_REVIEWS', $this->reviews); ?></a>
	(<a href="<?php echo $this->url2; ?>"><?php echo Lang::txt('PLG_PUBLICATIONS_REVIEWS_REVIEW_THIS'); ?></a>)
</p>
