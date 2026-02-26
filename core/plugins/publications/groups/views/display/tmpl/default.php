<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

use Hubzero\Facades\Lang;
use Hubzero\Facades\Route;

$this->css();

$logo = $this->group->getLogo();
?>
<div id="group-owner" class="container">
    <div class="group-content">
        <h3><?php echo $this->escape(stripslashes($this->group->get('description'))); ?></h3>
        <p class="group-img">
            <?php
            $groupDesc = $this->escape(stripslashes($this->group->get('description')));
            $imgAlt = Lang::txt('PLG_PUBLICATIONS_GROUPS_IMAGE', $groupDesc);
            ?>
            <img src="<?php echo $logo; ?>"
                width="50"
                alt="<?php echo $imgAlt; ?>" />
        </p>
        <?php
        $groupUrl = Route::url('index.php?option=com_groups&cn=' . $this->group->get('cn'));
        $groupName = $this->escape(stripslashes($this->group->get('description')));
        $groupLink = '<a href="' . $groupUrl . '">' . $groupName . '</a>';
        ?>
        <p class="group-descripion"><?php
            echo Lang::txt('PLG_PUBLICATIONS_GROUPS_BELONGS_TO_GROUP', $groupLink);
        ?></p>
    </div>
</div>