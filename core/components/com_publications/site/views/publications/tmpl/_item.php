<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

?>
<li>
	<span class="pub-thumb"><img width="40" height="40" src="<?php echo Route::url($this->row->link('thumb')); ?>" alt="" /></span>
	<span class="pub-details">
		<a href="<?php echo Route::url('index.php?option=com_publications&id=' . $this->row->get('id')); ?>" title="<?php echo stripslashes($this->row->get('abstract')); ?>"><?php echo \Hubzero\Utility\Str::truncate(stripslashes($this->row->get('title')), 100); ?></a>
		<span class="block details"><?php echo implode(' <span>|</span> ', $this->info); ?></span>
	</span>
</li>
