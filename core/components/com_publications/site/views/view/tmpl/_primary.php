<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

use Hubzero\Facades\Lang;

// No direct access
defined('_HZEXEC_') or die();

/**
 * What this template is given.
 *
 * @var \Hubzero\Component\View&object{
 *     action: mixed,
 *     class: mixed,
 *     disabled: mixed,
 *     ftpDoc: mixed,
 *     href: mixed,
 *     msg: mixed,
 *     options: mixed,
 *     pop: mixed,
 *     showDoc: mixed,
 *     title: mixed,
 * } $this
 */

if ($this->disabled) : ?>
    <p id="primary-document">
        <span class="btn disabled <?php echo $this->class; ?>"><?php echo $this->msg; ?></span>
    </p>
<?php else : ?>
    <?php if (isset($this->options) && !empty($this->options)) : ?>
        <div class="btn-group btn-primary" id="primary-document">
            <a class="btn <?php echo ($this->class) ? ' ' . $this->class : ''; ?>" <?php
                    echo ($this->href) ? ' href="' . $this->href . '"' : '';
                    echo ($this->title) ? ' title="' . $this->escape($this->title) . '"' : '';
                    echo ($this->action) ? ' ' . $this->action : '';
            ?>><?php echo $this->msg; ?></a>
            <span class="btn dropdown-toggle"></span>
            <ul class="dropdown-menu">
                <?php foreach ($this->options as $option) : ?>
                    <li>
                        <a <?= $option->class ? 'class="' . $option->class . '"' : '' ?>
                            <?= isset($option->attrs) ? $option->attrs : '' ?>
                            href="<?= $option->href ?>"><?= $this->escape($option->title) ?></a>
                    </li>
                <?php endforeach; ?>
            </ul>
        </div>
        <?php
        if ($this->showDoc && !empty($this->ftpDoc)) : ?>     
            <p class="ftpDoc"><a href="<?= $this->ftpDoc ?>" target="_blank"><?= Lang::txt('Download Guide') ?></a></p>
        <?php endif; ?>
    <?php else : ?>
        <p id="primary-document">
            <a class="btn btn-primary<?php echo ($this->class) ? ' ' . $this->class : ''; ?>" <?php
                    echo ($this->href) ? ' href="' . $this->href . '"' : '';
                    echo ($this->title) ? ' title="' . $this->escape($this->title) . '"' : '';
                    echo ($this->action) ? ' ' . $this->action : '';
            ?>><?php echo $this->msg; ?></a>
        </p>
    <?php endif; ?>
<?php endif; ?>

<?php if ($this->pop) : ?>
    <div id="primary-document_pop">
        <div><?php echo $this->pop; ?></div>
    </div>
<?php endif;
