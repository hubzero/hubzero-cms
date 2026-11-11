<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright © 2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

/**
 * What this template is given.
 *
 * @var \Hubzero\Plugin\View&object{action: mixed, email: mixed, publication: mixed} $this
 */

$this->css();

$link = Route::url($this->publication->link('version') . '&active=watch&confirm=1&action=' . $this->action);

// The router decodes query values, so the address is appended afterwards
if ($this->email) {
    $link .= (strstr($link, '?') ? '&amp;' : '?') . 'email=' . urlencode($this->email);
}
?>
<div class="item-watch">
    <p>
        <?php echo Lang::txt('PLG_PUBLICATIONS_WATCH_CONFIRM_' . strtoupper($this->action)); ?>
    </p>
    <p>
        <?php
        $buttonText = Lang::txt('PLG_PUBLICATIONS_WATCH_' . strtoupper($this->action));
        ?>
        <button type="submit" form="hz-post-action" formaction="<?= $link ?>"
            class="btn <?= $this->action ?>"><?= $buttonText ?></button>
    </p>
</div>
