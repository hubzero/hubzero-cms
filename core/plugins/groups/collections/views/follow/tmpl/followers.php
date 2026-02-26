<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

use Hubzero\Facades\Date;
use Hubzero\Facades\Lang;
use Hubzero\Facades\Route;
use Hubzero\Facades\User;

// No direct access
defined('_HZEXEC_') or die();

$this->js();

$base = 'index.php?option=' . $this->option . '&cn=' . $this->group->get('cn') . '&active=' . $this->name;
?>

<ul id="page_options">
    <li>
        <a class="icon-info btn popup"
            href="<?php echo Route::url('index.php?option=com_help&component=collections&page=index'); ?>">
            <span><?php echo Lang::txt('PLG_GROUPS_COLLECTIONS_GETTING_STARTED'); ?></span>
        </a>
    </li>
</ul>

<form method="get" action="<?php echo Route::url($base . '&scope=followers'); ?>" id="collections">
    <?php
    $this->view('_submenu', 'collection')
         ->set('option', $this->option)
         ->set('group', $this->group)
         ->set('params', $this->params)
         ->set('name', $this->name)
         ->set('active', 'followers')
         ->set('collections', $this->collections)
         ->set('posts', $this->posts)
         ->set('followers', $this->total)
         ->set('following', ($this->params->get('access-can-follow') ? $this->following : 0))
         ->display();
    ?>

    <?php if (!User::isGuest() && $this->params->get('access-manage-collection')) { ?>
        <p class="guest-options">
            <a class="icon-config config btn" href="<?php echo Route::url($base . '&scope=settings'); ?>">
                <span><?php echo Lang::txt('PLG_GROUPS_COLLECTIONS_SETTINGS'); ?></span>
            </a>
        </p>
    <?php } ?>

    <?php if ($this->rows->total() > 0) { ?>
        <div class="container">
            <table class="followers entries">
                <caption>
                    <?php echo Lang::txt('People following this group'); ?>
                </caption>
                <tbody>
                    <?php foreach ($this->rows as $row) {
                        $followerImg = $row->follower()->image();
                        $followerTitle = $this->escape(
                            stripslashes($row->follower()->title())
                        );
                        $followerLink = Route::url(
                            $row->follower()->link()
                        );
                        $followersCount = Lang::txt(
                            '<strong>%s</strong> followers',
                            $row->count('followers')
                        );
                        $followingCount = Lang::txt(
                            '<strong>%s</strong> following',
                            $row->count('following')
                        );
                        $createdDate = $row->get('created');
                        $createdLocal = Date::of($createdDate)
                            ->toLocal(Lang::txt('DATE_FORMAT_HZ1'));
                        ?>
                        <tr class="<?php echo $row->get('follower_type'); ?>">
                            <th class="entry-img">
                                <img
                                    src="<?php echo $followerImg; ?>"
                                    width="40"
                                    height="40"
                                    alt="Profile picture of <?php echo $followerTitle; ?>" />
                            </th>
                            <td>
                                <a class="entry-title"
                                    href="<?php echo $followerLink; ?>">
                                    <?php echo $followerTitle; ?>
                                </a>
                                <br />
                                <span class="entry-details">
                                    <span class="follower count">
                                        <?php echo $followersCount; ?>
                                    </span>
                                    <span class="following count">
                                        <?php echo $followingCount; ?>
                                    </span>
                                </span>
                            </td>
                            <td>
                                <time datetime="<?php echo $createdDate; ?>">
                                    <?php echo $createdLocal; ?>
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
            $pageNav->setAdditionalUrlParam('cn', $this->group->get('cn'));
            $pageNav->setAdditionalUrlParam('active', 'collections');
            $pageNav->setAdditionalUrlParam('scope', 'followers');
            echo $pageNav->render();
            ?>
            <div class="clear"></div>
        </div><!-- / .container -->
    <?php } else { ?>
        <div id="collection-introduction">
            <?php if ($this->params->get('access-manage-collection')) {
                $noFollowersMsg = Lang::txt(
                    'This group currently does not have anyone'
                    . ' following it or any of its collections.'
                );
                $followersDesc = Lang::txt(
                    '"Followers" are members that have decided'
                    . ' to receive all public posts this group'
                    . ' makes or all posts in one of this'
                    . ' group\'s collections.'
                );
                $followersPrivacy = Lang::txt(
                    'Followers cannot see of your private'
                    . ' collections or posts made to private'
                    . ' collections.'
                );
                ?>
                <div class="instructions">
                    <p><?php echo $noFollowersMsg; ?></p>
                </div><!-- / .instructions -->
                <div class="questions">
                    <p>
                        <strong>
                            <?php echo Lang::txt('What are followers?'); ?>
                        </strong>
                    </p>
                    <p><?php echo $followersDesc; ?><p>
                    <p><?php echo $followersPrivacy; ?><p>
                </div>
            <?php } else { ?>
                <div class="instructions">
                    <p>
                        <?php echo Lang::txt('This group is not following anyone or any collections.'); ?>
                    </p>
                </div><!-- / .instructions -->
            <?php } ?>
        </div><!-- / #collection-introduction -->
    <?php } ?>
    <div class="clear"></div>
</form>