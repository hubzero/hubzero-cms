<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

?>
<br />
	<ul class="key">
		<li class="draft"><?php echo Lang::txt('COM_PUBLICATIONS_VERSION_DRAFT'); ?></li>
		<li class="ready"><?php echo Lang::txt('COM_PUBLICATIONS_VERSION_READY'); ?></li>
		<li class="new"><?php echo Lang::txt('COM_PUBLICATIONS_VERSION_PENDING'); ?></li>
		<li class="preserving"><?php echo Lang::txt('COM_PUBLICATIONS_VERSION_PRESERVING'); ?></li>
		<li class="wip"><?php echo Lang::txt('COM_PUBLICATIONS_VERSION_WIP'); ?></li>
		<li class="published"><?php echo Lang::txt('COM_PUBLICATIONS_VERSION_PUBLISHED'); ?></li>
		<li class="unpublished"><?php echo Lang::txt('COM_PUBLICATIONS_VERSION_UNPUBLISHED'); ?></li>
		<li class="deleted"><?php echo Lang::txt('COM_PUBLICATIONS_VERSION_DELETED'); ?></li>
	</ul>