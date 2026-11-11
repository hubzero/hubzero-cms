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

/**
 * What this template is given.
 *
 * @var \Hubzero\Module\Module&object{charlimit: mixed, quote: mixed} $this
 */

$base = rtrim(Request::base(true), '/');

if ($this->quote) {
    ?>
    <?php
    $idAttr = $this->params->get('moduleid') ? ' id="' . $this->params->get('moduleid') . '"' : '';
    ?>
    <div class="<?= $this->module->module ?>"<?= $idAttr ?>>
        <blockquote cite="<?php echo $this->escape(stripslashes($this->quote->get('fullname'))); ?>">
            <p>
                <?php
                $text = stripslashes($this->escape($this->quote->get('quote'))) . ' ';
                $text = substr($text, 0, $this->charlimit);
                $text = substr($text, 0, strrpos($text, ' '));

                echo $text;
                ?>
                <?php if (strlen($this->quote->get('quote')) > $this->charlimit) { ?>
                    <?php
                    $quoteUrl = $base . '/about/quotes/?quoteid='
                        . $this->quote->get('id');
                    $quoteTitle = Lang::txt(
                        'MOD_RANDOMQUOTE_VIEW_FULL',
                        $this->escape(stripslashes($this->quote->get('fullname'))),
                    );
                    ?>
                    <a href="<?php echo $quoteUrl; ?>"
                        title="<?php echo $quoteTitle; ?>"
                        class="showfullquote"
                    >
                        <?php echo Lang::txt('MOD_RANDOMQUOTE_VIEW'); ?>
                    </a>
                <?php } ?>
            </p>
        </blockquote>
        <p class="cite">
            <cite><?php echo $this->escape(stripslashes($this->quote->get('fullname'))); ?></cite>,
            <?php echo $this->escape(stripslashes($this->quote->get('org'))); ?>
            <span>-</span>
            <span><?php echo Lang::txt('MOD_RANDOMQUOTE_IN', $base . '/about/quotes'); ?></span>
        </p>
    </div>
    <?php
}
