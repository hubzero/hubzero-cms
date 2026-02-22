<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

defined('_HZEXEC_') or die;

foreach ($list as $item) : ?>
    <?php
    $catRoute = Route::url(
        \Components\Content\Site\Helpers\Route::getCategoryRoute($item->id)
    );
    ?>
    <li <?php if ($_SERVER['PHP_SELF'] == $catRoute) {
        echo ' class="active"';
        } ?>>
        <?php $levelup = $item->level - $startLevel - 1; ?>
        <h<?php echo $params->get('item_heading') + $levelup; ?>>
            <a href="<?php echo $catRoute; ?>">
                <?php echo $item->title; ?>
            </a>
        </h<?php echo $params->get('item_heading') + $levelup; ?>>

        <?php
        if ($params->get('show_description', 0)) {
            echo Html::content('prepare', $item->description, $item->getParams(), 'mod_articles_categories.content');
        }
        $showChildren = $params->get('show_children', 0);
        $maxLevel = $params->get('maxlevel', 0);
        $levelDiff = $item->level - $startLevel;
        if (
            $showChildren
            && (($maxLevel == 0) || ($maxLevel >= $levelDiff))
            && count($item->getChildren())
        ) {
            echo '<ul>';
            $temp = $list;
            $list = $item->getChildren();
            require $this->getLayoutPath($params->get('layout', 'default') . '_items');
            $list = $temp;
            echo '</ul>';
        }
        ?>
    </li>
<?php endforeach;
