<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

$unit = $this->course->offering()->unit($this->unit);

if (!$unit):
	throw new Exception(Lang::txt('uh-oh'), 404);
endif;

if (!$this->course->offering()->access('view')): ?>
	<p class="info"><?php echo Lang::txt('Access to the "Syllabus" section of this course is restricted to members only. You must be a member to view the content.'); ?></p>
<?php else: ?>
	<?php echo $unit->get('title'); ?>
<?php endif;
