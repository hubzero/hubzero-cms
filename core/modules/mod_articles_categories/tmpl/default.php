<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

defined('_HZEXEC_') or die;

/**
 * What this template is given.
 *
 * @var \Hubzero\Module\Module $this
 */

/**
 * Set by the code that includes this file.
 *
 * @var mixed $moduleclass_sfx
 * @var mixed $params
 */
?>
<ul class="categories-module<?php echo $moduleclass_sfx; ?>">
    <?php require $this->getLayoutPath($params->get('layout', 'default') . '_items'); ?>
</ul>
