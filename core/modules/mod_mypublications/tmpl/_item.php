<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// no direct access
defined('_HZEXEC_') or die();

$link = $item->link('version');

if ($item->get('state') == Components\Publications\Models\Orm\Version::STATE_DRAFT) :
    $link = $item->link('edit');
endif;

?>
<li class="publication">
    <a href="<?php echo Route::url($link); ?>">
        <?php echo $this->escape($item->get('title')); ?>
    </a>

    <span>
        <span class="version">
            <?php echo Lang::txt('MOD_MYPUBLICATIONS_VERSION', $item->get('version_label')); ?>
        </span>

        <?php if (!$item->publication->project->isProvisioned()) : ?>
            <span class="project">
                <?php
                $projectTitle = Hubzero\Utility\Str::truncate(
                    $item->publication->project->get('title'),
                    100
                );
                echo Lang::txt('MOD_MYPUBLICATIONS_PROJECT', $projectTitle);
                ?>
            </span>
        <?php endif; ?>
    </span>
</li>
