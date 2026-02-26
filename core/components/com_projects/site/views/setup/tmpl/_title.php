<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

use Hubzero\Facades\Lang;
use Hubzero\Facades\Route;

// No direct access
defined('_HZEXEC_') or die();

$title = $this->model->get('title') ? Lang::txt('COM_PROJECTS_NEW_PROJECT')
    . ': '
    . $this->model->get('title') : $this->title;

?>
<header id="content-header">
    <h2><?php echo $title; ?>
    <?php
    if (
        $this->model->groupOwner()
        && $cn = $this->model->groupOwner('cn')
    ) {
        $forGroup = Lang::txt('COM_PROJECTS_FOR') . ' '
            . ucfirst(Lang::txt('COM_PROJECTS_GROUP'));
        $groupUrl = Route::url(
            'index.php?option=com_groups&cn=' . $cn
        );
        $groupDesc = \Hubzero\Utility\Str::truncate(
            $this->model->groupOwner('description'),
            50
        );
        ?>
        <?php echo $forGroup; ?>
        <a href="<?php echo $groupUrl; ?>"><?php
            echo $groupDesc;
        ?></a>
    <?php } ?></h2>
</header><!-- / #content-header -->
