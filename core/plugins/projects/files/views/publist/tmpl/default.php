<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

use Hubzero\Facades\App;
use Hubzero\Facades\Lang;
use Hubzero\Facades\Route;

// @phpcs:disable PSR1.Files.SideEffects

// No direct access
defined('_HZEXEC_') or die();


$database = App::get('db');
$objSt = new \Components\Projects\Tables\Stamp($database);

// Get listed public files
$items = $objSt->getPubList($this->model->get('id'), 'files');

if ($items) {
    ?>
<div class="public-list-header">
    <h3><?php echo ucfirst(Lang::txt('COM_PROJECTS_PUBLIC')); ?> <?php echo Lang::txt('COM_PROJECTS_FILES'); ?></h3>
</div>
<div class="public-list-wrap">
    <ul>
        <?php foreach ($items as $item) {
            $ref = json_decode($item->reference);
            $file = new \Components\Projects\Models\File($e);
            ?>
        <li><a href="<?php echo Route::url($this->model->link('stamp') . '&s=' . $item->stamp); ?>
        "><?php echo $file::drawIcon($file->get('ext'));?> <?php echo basename($ref->file); ?></li>
            <?php
        } ?>
    </ul>
</div>
<?php }
