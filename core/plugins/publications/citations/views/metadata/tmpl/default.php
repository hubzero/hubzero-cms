<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();
?>

<p class="citation">
	<a href="<?php echo $this->url; ?>"><?php echo Lang::txt('PLG_PUBLICATION_CITATIONS_COUNT', count($this->citations)); ?></a>
</p>