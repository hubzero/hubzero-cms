<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// no direct access
defined('_HZEXEC_') or die;
?>

<form action="<?php echo Route::url('index.php?option=com_search'); ?>" method="get" id="searchform<?php echo (self::$instances > 1) ? $this->module->id : ''; ?>" class="<?php echo $moduleclass_sfx; ?>searchform">
	<input type="text" name="terms" class="searchword">
	<input type="submit" class="searchsubmit" value="Search ›" />
	<div class="icon">
		<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 475.1 475.1"><path d="M464.5 412.8l-97.9-97.9c23.6-34.1 35.4-72 35.4-113.9 0-27.2-5.3-53.2-15.9-78.1-10.6-24.8-24.8-46.3-42.8-64.2s-39.4-32.3-64.2-42.8C254.2 5.3 228.2 0 201 0c-27.2 0-53.2 5.3-78.1 15.8-24.8 10.6-46.2 24.9-64.2 42.9s-32.3 39.4-42.8 64.2C5.3 147.8 0 173.8 0 201c0 27.2 5.3 53.2 15.8 78.1 10.6 24.8 24.8 46.2 42.8 64.2 18 18 39.4 32.3 64.2 42.8 24.8 10.6 50.9 15.8 78.1 15.8 41.9 0 79.9-11.8 113.9-35.4l97.9 97.6c6.9 7.2 15.4 10.8 25.7 10.8 9.9 0 18.5-3.6 25.7-10.8 7.2-7.2 10.8-15.8 10.8-25.7.2-9.9-3.3-18.5-10.4-25.6zM291.4 291.4c-25 25-55.1 37.5-90.4 37.5-35.2 0-65.3-12.5-90.4-37.5-25-25-37.5-55.1-37.5-90.4 0-35.2 12.5-65.3 37.5-90.4 25-25 55.1-37.5 90.4-37.5 35.2 0 65.3 12.5 90.4 37.5 25 25 37.5 55.1 37.5 90.4 0 35.2-12.5 65.3-37.5 90.4z"/></svg>
	</div>
</form>
