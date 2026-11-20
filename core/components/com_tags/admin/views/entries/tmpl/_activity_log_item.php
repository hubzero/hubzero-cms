<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access.
defined('_HZEXEC_') or die();

/**
 * What this template is given.
 *
 * @var \Hubzero\Component\View&object{log: mixed} $this
 */

use Components\Tags\Helpers\ActivityLogPresenter;

$log = $this->log;
$id = $log->get('id');

$parser = new ActivityLogPresenter();
$parsedLog = $parser->parse($log);

if ($parsedLog->activityDescription) : ?>
    <li class="<?php echo $parsedLog->class; ?>"
        data-id="<?php echo $id; ?>">
        <span class="entry-log-data">
            <?php echo $this->escape($parsedLog->activityDescription); ?>
        </span>
    </li>
<?php endif;
