<?php

// @phpcs:disable PSR1.Files.SideEffects

/**
 * @package   hubzero-cms
 * @copyright Copyright (c) 2005-2020 The Regents of the University of California.
 * @license   http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

$base = $this->member->link() . '&active=' . $this->name;

$this->css()
    ->js();
?>

<ul id="page_options">
    <li>
        <a class="icon-info btn popup"
            href="<?php echo Route::url('index.php?option=com_help&component=collections&page=index'); ?>">
            <span><?php echo Lang::txt('PLG_MEMBERS_COLLECTIONS_GETTING_STARTED'); ?></span>
        </a>
    </li>
</ul>

<form method="get" action="<?php echo Route::url($base . '&task=following'); ?>" id="collections">
    <?php

    $this->view('_submenu', 'collection')
        ->set('option', $this->option)
        ->set('member', $this->member)
        ->set('params', $this->params)
        ->set('name', $this->name)
        ->set('active', 'following')
        ->set('collections', $this->collections)
        ->set('posts', $this->posts)
        ->set('followers', $this->followers)
        ->set('following', $this->rows->total())
        ->display();
    ?>

    <?php if ($this->rows->total() > 0) { ?>
        <div class="container">
            <table class="following entries">
                <tbody>
                    <?php foreach ($this->rows as $row) {
                        $followingObj = $row->following();
                        $followingImg = $followingObj->image();
                        $followingTitle = $this->escape(
                            stripslashes($followingObj->title() ?: '')
                        );
                        $profilePicAlt = Lang::txt(
                            'PLG_MEMBERS_COLLECTIONS_PROFILE_PICTURE',
                            $followingTitle
                        );
                        $followingLink = Route::url($followingObj->link());
                        $followerCount = Lang::txt(
                            'PLG_MEMBERS_COLLECTIONS_NUM_FOLLOWERS',
                            $row->count('followers')
                        );
                        $followingCount = Lang::txt(
                            'PLG_MEMBERS_COLLECTIONS_NUM_FOLLOWING',
                            $row->count('following')
                        );
                        $followType = $row->get('following_type');
                        $followId = $row->get('following_id');
                        ?>
                        <tr class="<?php echo $followType; ?>">
                            <th>
                                <?php if ($followingImg) { ?>
                                    <img
                                        src="<?php echo $followingImg; ?>"
                                        width="40"
                                        height="40"
                                        alt="<?php echo $profilePicAlt; ?>"
                                    />
                                <?php } else { ?>
                                    <span class="entry-id">
                                        <?php echo $followId; ?>
                                    </span>
                                <?php } ?>
                            </th>
                            <td>
                                <a class="entry-title"
                                    href="<?php echo $followingLink; ?>">
                                    <?php echo $followingTitle; ?>
                                </a>
                                <?php if ($followType == 'collection') {
                                    $creatorName = $this->escape(
                                        stripslashes(
                                            $followingObj->creator('name') ?: ''
                                        )
                                    );
                                    echo Lang::txt('by %s', $creatorName);
                                } ?>
                                <br />
                                <span class="entry-details">
                                    <span class="follower count">
                                        <?php echo $followerCount; ?>
                                    </span>
                                    <?php if ($followType != 'collection') { ?>
                                        <span class="following count">
                                            <?php echo $followingCount; ?>
                                        </span>
                                    <?php } ?>
                                </span>
                            </td>
                            <td>
                                <?php if ($this->params->get('access-manage-collection')) {
                                    $unfollowUrl = Route::url(
                                        $followingObj->link('unfollow')
                                    );
                                    $followTxt = Lang::txt(
                                        'PLG_MEMBERS_COLLECTIONS_FOLLOW'
                                    );
                                    $unfollowTxt = Lang::txt(
                                        'PLG_MEMBERS_COLLECTIONS_UNFOLLOW'
                                    );
                                    ?>
                                    <a class="icon-unfollow unfollow btn"
                                        data-id="<?php echo $followId; ?>"
                                        data-text-follow="<?php echo $followTxt; ?>"
                                        data-text-unfollow="<?php echo $unfollowTxt; ?>"
                                        href="<?php echo $unfollowUrl; ?>">
                                        <span><?php echo $unfollowTxt; ?></span>
                                    </a>
                                <?php } ?>
                            </td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
            <?php

            $pageNav = $this->pagination(
                $this->total,
                $this->filters['start'],
                $this->filters['limit']
            );
            $pageNav->setAdditionalUrlParam('id', $this->member->get('id'));
            $pageNav->setAdditionalUrlParam('active', 'collections');
            $pageNav->setAdditionalUrlParam('task', 'following');
            echo $pageNav->render();
            ?>
            <div class="clear"></div>
        </div><!-- / .container -->
    <?php } else { ?>
        <div id="collection-introduction">
            <?php if ($this->params->get('access-manage-collection')) { ?>
                <div class="instructions">
                    <ol>
                        <?php
                        $collectionsUrl = Route::url(
                            'index.php?option=com_collections'
                        );
                        $step1 = Lang::txt(
                            'PLG_MEMBERS_COLLECTIONS_FEED_INSTRUCTIONS_STEP1',
                            $collectionsUrl
                        );
                        ?>
                        <li><?php echo $step1; ?></li>
                        <li><?php echo Lang::txt('PLG_MEMBERS_COLLECTIONS_FEED_INSTRUCTIONS_STEP2'); ?></li>
                        <li><?php echo Lang::txt('PLG_MEMBERS_COLLECTIONS_FEED_INSTRUCTIONS_STEP3'); ?></li>
                    </ol>
                </div><!-- / .instructions -->
                <div class="questions">
                    <p><strong><?php echo Lang::txt('PLG_MEMBERS_COLLECTIONS_FOLLOW_WHAT_IS_FOLLOWING'); ?></strong></p>
                    <p><?php echo Lang::txt('PLG_MEMBERS_COLLECTIONS_WHAT_IS_FOLLOWING_EXPLANATION'); ?></p>
                </div>
            <?php } else { ?>
                <div class="instructions">
                    <p>
                        <?php echo Lang::txt('PLG_MEMBERS_COLLECTIONS_FOLLOW_NOT_FOLLOWING_ANYONE'); ?>
                    </p>
                </div><!-- / .instructions -->
            <?php } ?>
        </div><!-- / #collection-introduction -->
    <?php } ?>
    <div class="clear"></div>
</form>
