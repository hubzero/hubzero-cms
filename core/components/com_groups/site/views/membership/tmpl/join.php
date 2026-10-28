<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright © 2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

$base = 'index.php?option=' . $this->option . '&cn=' . $this->group->get('cn');
?>
<header id="content-header">
    <h2><?php echo $this->escape($this->title); ?></h2>

    <div id="content-header-extra">
        <ul id="useroptions">
            <li class="last">
                <a class="group btn" href="<?php echo Route::url('index.php?option=' . $this->option); ?>">
                    <?php echo Lang::txt('COM_GROUPS_ACTION_BACK_TO_ALL_GROUPS'); ?>
                </a>
            </li>
        </ul>
    </div><!-- / #content-header-extra -->
</header>

<section class="main section">
    <div class="section-inner">
        <p class="info"><?php echo Lang::txt('COM_GROUPS_JOIN_CONFIRM', $this->escape($this->group->get('description'))); ?></p>
        <p>
            <button type="submit" form="hz-post-action" formaction="<?php echo Route::url($base . '&task=join'); ?>" class="btn btn-success">
                <?php echo Lang::txt('COM_GROUPS_TOOLBAR_JOIN'); ?>
            </button>
            <a class="btn btn-secondary" href="<?php echo Route::url($base); ?>">
                <?php echo Lang::txt('JCANCEL'); ?>
            </a>
        </p>
    </div>
</section>
