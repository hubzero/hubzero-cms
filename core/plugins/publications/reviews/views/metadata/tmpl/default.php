<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */

use Hubzero\Facades\Lang;

defined('_HZEXEC_') or die();

/**
 * What this template is given.
 *
 * @var \Hubzero\Plugin\View&object{reviews: mixed, url: mixed, url2: mixed} $this
 */
?>
<p class="review">
    <a href="<?php echo $this->url; ?>"><?php echo Lang::txt('PLG_PUBLICATIONS_REVIEWS_NUM_REVIEWS', $this->reviews); ?>
    </a>
    (<a href="<?php echo $this->url2; ?>"><?php echo Lang::txt('PLG_PUBLICATIONS_REVIEWS_REVIEW_THIS'); ?></a>)
</p>
