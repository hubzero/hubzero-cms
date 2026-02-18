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

<form method="get" action="<?php echo Route::url($base . '&task=followers'); ?>" id="collections">
    <?php

    $this->view('_submenu', 'collection')
        ->set('option', $this->option)
        ->set('member', $this->member)
        ->set('params', $this->params)
        ->set('name', $this->name)
        ->set('active', 'followers')
        ->set('collections', $this->collections)
        ->set('posts', $this->posts)
        ->set('followers', $this->total)
        ->set('following', $this->following)
        ->display();
    ?>

    <?php if ($this->rows->total() > 0) { ?>
        <div class="container">
            <table class="followers entries">
                <caption>
                    <?php echo Lang::txt('PLG_MEMBERS_COLLECTIONS_FOLLOWING_YOU'); ?>
                </caption>
                <tbody>
                <?php foreach ($this->rows as $row) {
                    $followerObj = $row->follower();
                    $followerImg = $followerObj->image();
                    $followerTitle = $this->escape(
                        stripslashes($followerObj->title() ?: '')
                    );
                    $profilePicAlt = Lang::txt(
                        'PLG_MEMBERS_COLLECTIONS_PROFILE_PICTURE',
                        $followerTitle
                    );
                    $followerLink = Route::url(
                        $followerObj->link()
                    );
                    $followerCount = Lang::txt(
                        'PLG_MEMBERS_COLLECTIONS_NUM_FOLLOWERS',
                        $row->count('followers')
                    );
                    $followingCount = Lang::txt(
                        'PLG_MEMBERS_COLLECTIONS_NUM_FOLLOWING',
                        $row->count('following')
                    );
                    $createdDate = $row->get('created');
                    $formattedDate = Date::of($createdDate)
                        ->toLocal(Lang::txt('DATE_FORMAT_HZ1'));
                    ?>
                    <tr class="<?php echo $row->get('follower_type'); ?>">
                        <th class="entry-img">
                            <img
                                src="<?php echo $followerImg; ?>"
                                width="40"
                                height="40"
                                alt="<?php echo $profilePicAlt; ?>"
                            />
                        </th>
                        <td>
                            <a class="entry-title"
                                href="<?php echo $followerLink; ?>">
                                <?php echo $followerTitle; ?>
                            </a>
                            <br />
                            <span class="entry-details">
                                <span class="follower count">
                                    <?php echo $followerCount; ?>
                                </span>
                                <span class="following count">
                                    <?php echo $followingCount; ?>
                                </span>
                            </span>
                        </td>
                        <td>
                            <time datetime="<?php echo $createdDate; ?>">
                                <?php echo $formattedDate; ?>
                            </time>
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
            $pageNav->setAdditionalUrlParam('task', 'followers');
            echo $pageNav->render();
            ?>
            <div class="clear"></div>
        </div><!-- / .container -->
    <?php } else { ?>
        <div id="collection-introduction">
            <?php if ($this->params->get('access-manage-collection')) { ?>
                <div class="instructions">
                    <p><?php echo Lang::txt('PLG_MEMBERS_COLLECTIONS_FOLLOWING_YOU_NONE'); ?></p>
                </div><!-- / .instructions -->
                <div class="questions">
                    <?php
                    $whatAreFollowers = Lang::txt(
                        'PLG_MEMBERS_COLLECTIONS_FOLLOW_WHAT_ARE_FOLLOWERS'
                    );
                    ?>
                    <p><strong><?php echo $whatAreFollowers; ?></strong></p>
                    <p><?php echo Lang::txt('PLG_MEMBERS_COLLECTIONS_FOLLOW_WHAT_ARE_FOLLOWERS_EXPLANATION'); ?><p>
                </div>
            <?php } else { ?>
                <div class="instructions">
                    <p>
                        <?php echo Lang::txt('PLG_MEMBERS_COLLECTIONS_FOLLOW_MEMBER_HAS_NO_FOLLOWERS'); ?>
                    </p>
                </div><!-- / .instructions -->
            <?php } ?>
        </div><!-- / #collection-introduction -->
    <?php } ?>
    <div class="clear"></div>
</form>
