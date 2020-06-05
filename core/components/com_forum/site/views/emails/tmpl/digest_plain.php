<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// Compute some counts for later use
$groups = count($this->posts);
$posts  = 0;

array_walk($this->posts, function($val, $idx) use (&$posts)
{
	$posts += count($val);
});
?>
You have <?php echo $posts; ?> new post<?php echo ($posts > 1) ? 's' : ''; ?> across <?php echo $groups; ?> of your groups

=======================
<?php foreach ($this->posts as $group => $posts) : ?>
<?php $group = Hubzero\User\Group::getInstance($group); ?>
<?php echo $group->description; ?>
<?php foreach ($posts as $post) : ?>
<?php $inst = $post; ?>

<?php
$name = Lang::txt('JANONYMOUS');
if (!$post->anonymous)
{
	$name = User::getInstance($post->created_by)->get('name');
}
?>

<?php echo $name; ?> | <?php echo Date::of($post->created)->toLocal('M j, Y g:i:s a'); ?>

<?php echo Hubzero\Utility\Sanitize::stripAll($inst->get('comment')); ?>
<?php $base = rtrim(Request::root(), '/'); ?>
<?php $sef  = Route::urlForClient('site', $inst->link()); ?>
<?php $link = $base . '/' . trim($sef, '/'); ?>
View this post on <?php echo Config::get('sitename'); ?>: <?php echo $link; ?>
<?php endforeach; ?>

-----------------------
<?php endforeach; ?>

This email was sent to you on behalf of <?php echo Request::root(); ?> because you are subscribed 
to these group discussion threads. To unsubscribe, please log in and adjust your email preferences 
for the group of interest.
