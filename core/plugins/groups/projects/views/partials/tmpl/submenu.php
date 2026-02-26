<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

use Hubzero\Facades\Lang;
use Hubzero\Facades\Route;
use Hubzero\Facades\Event;

// No direct access
defined('_HZEXEC_') or die();

$groupProjectPlugins = Event::trigger('groups.onGroupProjects', array($this->group));
?>

<ul class="sub-menu">
    <li <?php if ($this->tab == 'all') {
        echo 'class="active"';
        } ?> >
        <?php
        $allUrl = Route::url(
            'index.php?option=com_groups&cn='
            . $this->group->get('cn')
            . '&active=projects&action=all'
        );
        ?>
        <a href="<?php echo $allUrl; ?>">
            <?php echo Lang::txt('PLG_GROUPS_PROJECTS_LIST') . ' (' . $this->projectcount . ')'; ?>
        </a>
    </li>
    <li <?php if ($this->tab == 'updates') {
        echo 'class="active"';
        } ?> >
        <?php
        $updatesUrl = Route::url(
            'index.php?option=com_groups&cn='
            . $this->group->get('cn')
            . '&active=projects&action=updates'
        );
        ?>
        <a href="<?php echo $updatesUrl; ?>">
            <?php echo Lang::txt('PLG_GROUPS_PROJECTS_UPDATES_FEED'); ?> <?php if ($this->newcount) {
                echo '<span class="s-new">' . $this->newcount . '</span>';
            } ?>
        </a>
    </li>
    <?php foreach ($groupProjectPlugins as $plugin) { ?>
        <li <?php if ($this->tab == $plugin->name) {
            echo 'class="active"';
            } ?> >
            <a href="<?php echo $plugin->pathRoute ?>">
                <?php echo $plugin->title ?> <?php if ($plugin->newcount) {
                    echo '<span class="s-new">' . $plugin->newcount . '</span>';
                } ?>
            </a>
        </li>
    <?php } //endforeach ?>
</ul>
