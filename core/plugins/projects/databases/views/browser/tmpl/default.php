<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

use Hubzero\Facades\Lang;
use Hubzero\Facades\Route;

// @phpcs:disable PSR1.Files.SideEffects

// No direct access
defined('_HZEXEC_') or die();

/**
 * What this template is given.
 *
 * @var \Hubzero\Plugin\View&object{attachments: mixed, database: mixed, model: mixed, primary: mixed} $this
 */

// What takes the database below takes it by reference
$database = $this->database;

// Get databases to choose from
$objPD = new \Components\Projects\Tables\Database($database);
$items = $objPD->getItems($this->model->get('id'), []);

$missing = [];
$shown = [];

// Attached item
$selected = null;
if ($this->primary && !empty($this->attachments)) {
    $selected = $this->attachments[0]->object_name;
}

// Build url
$route = $this->model->isProvisioned()
    ? 'index.php?option=com_publications' . '&task=submit'
    : 'index.php?option=com_projects' . '&alias=' . $this->model->get('alias');
$p_url = Route::url($route . '&active=databases');

?>
<ul id="c-browser"
    <?= count($items) == 0 && isset($this->attachments) && count($this->attachments) == 0 ? 'class="hidden"' : '' ?> >
<?php
if (count($items) > 0) { ?>
    <?php
    foreach ($items as $item) {
        if ($item->revision == null || ($selected && $selected == $item->database_name)) {
            ?>
        <li class="c-click databases"
            id="data::<?= $this->escape($item->database_name) ?>"><?= $this->escape($item->title) ?></li>
            <?php
            $shown[] = $item->database_name;
        }
    } ?>

        <?php

        // Check for missing items
        // Primary content / Supporting docs
        if (isset($this->attachments)) {
            if (count($this->attachments) > 0) {
                foreach ($this->attachments as $attachment) {
                    if (!in_array($attachment->object_name, $shown)) {
                        // Found missing
                        $miss = [];
                        $miss['id'] = $attachment->object_name;
                        $miss['title'] = $attachment->title;
                        $missing[] = $miss;
                    }
                }
            }
        }

        // Add missing items
        if (count($missing) > 0) {
            foreach ($missing as $miss) { ?>
                <li class="c-click databases i-missing"
                    id="data::<?= $this->escape($miss['id']) ?>"><?= $this->escape($miss['title']) ?><span
                    class="c-missing"><?= Lang::txt('PLG_PROJECTS_DATA_MISSING_DATABASE') ?></span></li>
            <?php }
        }
}
?>
</ul>

<?php if ((count($shown) + count($missing)) == 0) { ?>
    <p class="noresults"><?php echo Lang::txt('PLG_PROJECTS_DATA_NO_SELECTION_ITEMS_FOUND_DATA'); ?></p>
<?php } ?>

<?php if (!$this->model->isProvisioned()) { ?>
    <?php
    $linkUrl = Route::url($route . '&active=databases');
    $paragraphText = Lang::txt('PLG_PROJECTS_DATABASES_TO_CREATE');
    ?>
    <p class="addnew"><?= Lang::txt('PLG_PROJECTS_DATABASES_GO_TO') ?> <a
        href="<?= $linkUrl ?>"><?= Lang::txt('PLG_PROJECTS_DATABASES') ?></a> <?= $paragraphText ?></p>
<?php }
