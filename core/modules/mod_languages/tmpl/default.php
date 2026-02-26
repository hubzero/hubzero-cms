<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

use Hubzero\Facades\Lang;
use Hubzero\Facades\Request;

// no direct access
defined('_HZEXEC_') or die();

$this->css('template.css');
?>
<div class="mod-languages<?php echo $moduleclass_sfx ?>">
<?php if ($headerText) : ?>
    <div class="pretext"><p><?php echo $headerText; ?></p></div>
<?php endif; ?>

<?php if ($params->get('dropdown', 1)) : ?>
    <form name="lang" method="post" action="<?php echo htmlspecialchars(Request::current()); ?>">
        <select class="inputbox" onchange="document.location.replace(this.value);">
            <?php foreach ($list as $language) : ?>
                <?php
                $dir = Lang::getInstance($language->lang_code)->isRTL()
                    ? 'rtl' : 'ltr';
                $selected = $language->active
                    ? 'selected="selected"' : '';
                ?>
                <option dir="<?php echo $dir; ?>"
                    value="<?php echo $language->link; ?>"
                    <?php echo $selected; ?>
                >
                <?php echo $language->title_native;?></option>
            <?php endforeach; ?>
        </select>
    </form>
<?php else : ?>
    <ul class="<?php echo $params->get('inline', 1) ? 'lang-inline' : 'lang-block';?>">
        <?php foreach ($list as $language) : ?>
            <?php if ($params->get('show_active', 0) || !$language->active) :?>
                <?php
                $activeClass = $language->active ? 'lang-active' : '';
                $langDir = Lang::getInstance($language->lang_code)->isRTL()
                    ? 'rtl' : 'ltr';
                ?>
                <li class="<?php echo $activeClass; ?>"
                    dir="<?php echo $langDir; ?>"
                >
                    <a href="<?php echo $language->link;?>">
                        <?php if ($params->get('image', 1)) :?>
                            <img src="<?php echo $this->img($language->image . '.gif'); ?>"
                                alt="<?php echo $language->title_native; ?>"
                            />
                        <?php else : ?>
                            <?php
                            echo $params->get('full_name', 1)
                                ? $language->title_native
                                : strtoupper($language->sef);
                            ?>
                        <?php endif; ?>
                    </a>
                </li>
            <?php endif;?>
        <?php endforeach;?>
    </ul>
<?php endif; ?>

<?php if ($footerText) : ?>
    <div class="posttext"><p><?php echo $footerText; ?></p></div>
<?php endif; ?>
</div>
