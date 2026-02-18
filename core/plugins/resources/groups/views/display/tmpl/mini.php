<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

$this->css();

if ($this->group) :
    $logo = $this->group->getLogo();
    ?>

<div id="group-owner" class="container">
    <h4>Owned by</h4>
    <div class="group-content">
    <?php
    $groupDesc = $this->escape(stripslashes($this->group->get('description')));
    $groupUrl = Route::url(
        'index.php?option=com_groups&cn=' . $this->group->get('cn')
    );
    $groupImgAlt = Lang::txt('PLG_RESOURCES_GROUPS_IMAGE', $groupDesc);
    $groupLink = '<a href="' . $groupUrl . '">' . $groupDesc . '</a>';
    $belongsText = Lang::txt(
        'PLG_RESOURCES_GROUPS_BELONGS_TO_GROUP',
        $groupLink
    );
    ?>
    <?php if ($logo) { ?>
        <p class="group-img">
            <a href="<?php echo $groupUrl; ?>">
                <img
                    src="<?php echo $logo; ?>"
                    width="50"
                    alt="<?php echo $groupImgAlt; ?>"
                />
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
    <a class="owner"
        href="<?php echo Route::url('index.php?option=com_groups&cn=' . $this->group->get('cn')); ?>"
        ><?php echo $this->escape(stripslashes($this->group->get('description'))); ?></a>
</div>
<?php endif; ?>

<?php if ($this->aclgroups) : ?>
    <div id="group-shared" class="container">
        
        <h4>Shared with</h4>
    <?php foreach ($this->aclgroups as $group) :
        $logo = $group->getLogo();
        $aclDesc = $this->escape(stripslashes($group->get('description')));
        $aclAlt = Lang::txt('PLG_RESOURCES_GROUPS_IMAGE', $aclDesc);
        $aclUrl = Route::url(
            'index.php?option=com_groups&cn=' . $group->get('cn')
        );
        ?>
    <a href="<?php echo $aclUrl; ?>"
        class="shared-with-group">
        <div class="inner">
            <?php if ($logo) : ?>
            <div class="img">
                <img
                    src="<?php echo $logo; ?>"
                    alt="<?php echo $aclAlt; ?>"
                />
            </div>
            <?php endif; ?>
            <p class="group-description"><?php echo $this->escape(stripslashes($group->get('description'))); ?></p>
        </div>
    </a>

    <?php endforeach; ?>
</div>
<?php endif; ?>