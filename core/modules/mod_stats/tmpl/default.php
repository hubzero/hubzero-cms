<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// no direct access
defined('_HZEXEC_') or die;

/**
 * What this template is given.
 *
 * @var \Hubzero\Module\Module $this
 */

/**
 * Set by the code that includes this file.
 *
 * @var mixed $list
 * @var mixed $moduleclass_sfx
 */
?>
<dl class="stats-module<?php echo $moduleclass_sfx; ?>">
<?php foreach ($list as $item) : ?>
    <dt><?php echo $item->title; ?></dt>
    <dd><?php echo $item->data; ?></dd>
<?php endforeach; ?>
</dl>
