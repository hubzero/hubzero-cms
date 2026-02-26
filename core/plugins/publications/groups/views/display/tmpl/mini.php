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
$groupDesc = $this->escape(stripslashes($this->group->get('description')));
$groupUrl = Route::url(
    'index.php?option=com_groups&cn=' . $this->group->get('cn')
);
$imgAlt = Lang::txt('PLG_PUBLICATIONS_GROUPS_IMAGE', $groupDesc);
$groupLink = '<a href="' . $groupUrl . '">' . $groupDesc . '</a>';
$belongsText = Lang::txt(
    'PLG_PUBLICATIONS_GROUPS_BELONGS_TO_GROUP',
    $groupLink
);
?>
<div id="group-owner">
    <h3><?php echo $groupDesc; ?></h3>
    <div class="group-content">
    <?php if ($logo) { ?>
        <p class="group-img">
            <a href="<?php echo $groupUrl; ?>">
                <img src="<?php echo $logo; ?>"
                    width="50"
                    alt="<?php echo $imgAlt; ?>" />
            </a>
        </p>
        <p class="group-description group-withlogo">
            <?php echo $belongsText; ?>
        </p>
    <?php } else { ?>
        <p class="group-description">
            <?php echo $belongsText; ?>
        </p>
    <?php } ?>
    </div>
</div>