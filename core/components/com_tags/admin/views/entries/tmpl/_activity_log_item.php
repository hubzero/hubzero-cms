<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access.
defined('_HZEXEC_') or die();

use Components\Tags\Helpers\ActivityLogPresenter;

$log = $this->log;
$id = $log->get('id');

$parser = new ActivityLogPresenter();
$parsedLog = $parser->parse($log);

if ($parsedLog->activityDescription): ?>
	<li class="<?php echo $parsedLog->class; ?>"
		data-id="<?php echo $id; ?>">
		<span class="entry-log-data">
			<?php echo $parsedLog->activityDescription; ?>
		</span>
	</li>
<?php endif;
