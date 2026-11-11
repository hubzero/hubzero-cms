<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

use Hubzero\Facades\Lang;
use Hubzero\Facades\Route;
use Hubzero\Utility\Str;

// No direct access
defined('_HZEXEC_') or die();

/**
 * What this template is given.
 *
 * @var \Hubzero\Component\View&object{model: mixed, title: mixed} $this
 */

$title = $this->model->get('title')
    ? Lang::txt('COM_PROJECTS_NEW_PROJECT') . ': ' . $this->escape($this->model->get('title'))
    : $this->title;

?>
<header id="content-header">
    <h2><?php echo $title; ?> <?php if ($this->model->groupOwner() && $cn = $this->model->groupOwner('cn')) {
        $linkText = $this->escape(Str::truncate($this->model->groupOwner('description'), 50));
        ?> <?= Lang::txt('COM_PROJECTS_FOR') . ' ' . ucfirst(Lang::txt('COM_PROJECTS_GROUP')) ?> <a
            href="<?= Route::url('index.php?option=com_groups&cn=' . $cn) ?>"><?= $linkText ?></a><?php
        } ?></h2>
</header><!-- / #content-header -->
