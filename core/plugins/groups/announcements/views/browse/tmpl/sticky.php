<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

/**
 * What this template is given.
 *
 * @var \Hubzero\Plugin\View&object{authorized: mixed, group: mixed, option: mixed, rows: mixed} $this
 */

//add styles and scripts
$this
     ->css()
     ->js();
?>
<?php if ($this->rows->count() > 0) : ?>
    <div class="scontainer">
        <?php foreach ($this->rows as $row) : ?>
            <?php
                $this
                     ->view('item')
                     ->set('option', $this->option)
                     ->set('group', $this->group)
                     ->set('authorized', $this->authorized)
                     ->set('announcement', $row)
                     ->set('showClose', true)
                     ->display();
            ?>
        <?php endforeach; ?>
    </div>
<?php endif;
