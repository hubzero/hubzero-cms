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
?>

<header id="content-header">
    <h2><?php echo $this->asset->title ?></h2>

    <div id="content-header-extra">
        <p>
            <?php $routeUrl = Route::url($this->course->offering()->link() . '&active=outline'); ?>
            <a class="icon-prev back btn" href="<?php echo $routeUrl; ?>">
                <?php echo Lang::txt('Back to course'); ?>
            </a>
        </p>
    </div>
</header>

<object type="application/x-shockwave-flash" width="100%" height="100%">
    <param name="movie" value="<?php echo Route::url($this->model->path($this->course->get('id'))); ?>"></param>
    <param name="wmode" value="opaque"></param>
</object>