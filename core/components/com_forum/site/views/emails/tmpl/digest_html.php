<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

// Compute some counts for later use
$groups = count($this->posts);
$posts  = 0;

array_walk($this->posts, function ($val, $idx) use (&$posts) {
    $posts += $val->count();
});
?>
<!-- Start Header -->
<table class="tbl-header" width="100%" cellpadding="0" cellspacing="0" border="0">
    <tbody>
        <tr>
            <td width="10%" align="left" valign="bottom" nowrap="nowrap" class="sitename">
                <?php echo Config::get('sitename'); ?>
            </td>
            <td width="80%" align="left" valign="bottom" class="tagline mobilehide">
                <span class="home">
                    <a href="<?php echo Request::base(); ?>">
                        <?php echo Request::base(); ?>
                    </a>
                </span>
                <br />
                <span class="description">
                    <?php echo Config::get('MetaDesc'); ?>
                </span>
            </td>
            <td width="10%" align="right" valign="bottom" nowrap="nowrap" class="component">
                Group Digest
            </td>
        </tr>
    </tbody>
</table>
<!-- End Header -->

<!-- Start Spacer -->
<table class="tbl-spacer" width="100%" cellpadding="0" cellspacing="0" border="0">
    <tbody>
        <tr>
            <td height="30"></td>
        </tr>
    </tbody>
</table>
<!-- End Spacer -->

<?php
$groupInfoStyle = 'border-collapse: collapse; background-color: #F3F3F3;'
    . ' border: 1px solid #DDDDDD;';
$imgSrc = Request::root()
    . '/core/components/com_forum/site/assets/img/group.png';
?>
<table id="group-info" width="650" cellpadding="0" cellspacing="0" border="0"
    style="<?php echo $groupInfoStyle; ?>">
    <tr>
        <td width="85" style="padding: 0 0 0 15px; opacity: 0.8">
            <img width="80" src="<?php echo $imgSrc; ?>" />
        </td>
        <td width="565" style="padding: 14px;">
            <span style="font-weight: bold; font-size:14px;">
                Your <?php echo $this->interval; ?> group discussion digest
            </span>
            <hr />
            <span>You have <?php echo $posts; ?> new post<?php if ($posts > 1) {
                echo 's';
                           } ?> across <?php echo $groups; ?> of your groups</span>
        </td>
    </tr>
</table>

<!-- Start Spacer -->
<table class="tbl-spacer" width="100%" cellpadding="0" cellspacing="0" border="0">
    <tbody>
        <tr>
            <td height="30"></td>
        </tr>
    </tbody>
</table>
<!-- End Spacer -->

<?php
$discussionStyle = 'border-collapse: collapse;';
?>
<table id="course-discussions" width="650" cellpadding="0" cellspacing="0" border="0"
    style="<?php echo $discussionStyle; ?>">
    <tr style="border-bottom: 1px solid #DDDDDD;">
        <td style="font-size: 13px; font-weight: bold; padding: 4px 0;">
            Latest Discussions
        </td>
    </tr>
</table>

<?php foreach ($this->posts as $group => $posts) : ?>
    <?php
    $groupTableStyle = 'border-collapse: collapse;';
    $postTableStyle = 'border-collapse: collapse;';
    ?>
    <table id="group-discussions" width="650" cellpadding="0" cellspacing="0" border="0"
        style="<?php echo $groupTableStyle; ?>">
        <tr>
            <td colspan="2" style="text-align:center;font-size:14px;">
                <?php $group = Hubzero\User\Group::getInstance($group); ?>
                <?php echo $group->description; ?>
            </td>
        </tr>
        <tr>
            <td>
                <?php foreach ($posts as $post) : ?>
                    <?php
                    $thumbSrc = Request::root()
                        . '/members/' . $post->created_by
                        . '/Image:thumb.png';
                    $bubbleStyle = 'position: relative;'
                        . ' border: 1px solid #CCCCCC;'
                        . ' padding: 12px;'
                        . ' -webkit-border-radius: 7px;'
                        . ' -moz-border-radius: 7px;'
                        . ' border-radius: 7px;';
                    $arrowStyle = 'background: #FFFFFF;'
                        . ' border: 1px solid #CCCCCC;'
                        . ' width: 15px; height: 15px;'
                        . ' position: absolute;'
                        . ' top: 50%; left: -10px;'
                        . ' margin-top: -7px;'
                        . ' transform:rotate(45deg);'
                        . ' -ms-transform:rotate(45deg);'
                        . ' -webkit-transform:rotate(45deg);';
                    $arrowCoverStyle = 'background: #FFFFFF;'
                        . ' width: 11px; height: 23px;'
                        . ' position: absolute;'
                        . ' top: 50%; left: -1px;'
                        . ' margin-top: -10px;';
                    ?>
                    <table id="course-discussions" width="650"
                        cellpadding="0" cellspacing="0" border="0"
                        style="<?php echo $postTableStyle; ?>">
                        <tr>
                            <td width="75" style="padding: 10px 0;">
                                <img width="50"
                                    src="<?php echo $thumbSrc; ?>" />
                            </td>
                            <td style="padding: 10px 0;">
                                <div style="<?php echo $bubbleStyle; ?>">
                                    <div style="<?php echo $arrowStyle; ?>"></div>
                                    <div style="<?php echo $arrowCoverStyle; ?>"></div>
                                    <div style="color: #AAAAAA; font-size: 11px;">
                                        <?php
                                        $name = Lang::txt('JANONYMOUS');
                                        if (!$post->anonymous) {
                                            $postCreator = $post->created_by;
                                            $name = User::getInstance($postCreator)
                                                ->get('name');
                                        }
                                        $postDate = Date::of($post->created)
                                            ->toLocal('M j, Y g:i:s a');
                                        ?>

                                        <?php echo $name; ?> | <?php echo $postDate; ?>
                                    </div>
                                    <div>
                                        <?php echo $post->comment; ?>
                                    </div>
                                    <div style="color: #AAAAAA; font-size: 11px;">
                                        <?php $base = rtrim(Request::root(), '/'); ?>
                                        <?php
                                        $postModel = \Components\Forum\Models\Post::one(
                                            $post->id
                                        );
                                        $sef = Route::urlForClient(
                                            'site',
                                            $postModel->link()
                                        );
                                        ?>
                                        <?php $link = $base . '/' . trim($sef, '/'); ?>
                                        <?php $siteName = Config::get('sitename'); ?>
                                        <a href="<?php echo $link; ?>">
                                            View this post on <?php echo $siteName; ?>
                                        </a>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    </table>
                <?php endforeach; ?>
            </td>
        </tr>
    </table>
<?php endforeach; ?>

<!-- Start Spacer -->
<table class="tbl-spacer" width="100%" cellpadding="0" cellspacing="0" border="0">
    <tbody>
        <tr>
            <td height="30"></td>
        </tr>
    </tbody>
</table>
<!-- End Spacer -->
