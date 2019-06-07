<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();
?>

Instructor Digest Course Update: <?php echo $this->course->get('title'); ?>

=======================

Enrollments
<?php echo $this->enrollments; ?> total
<?php echo $this->passing; ?> passing
<?php echo $this->failing; ?> failing

=======================

Discussion Topics
<?php echo $this->posts_cnt; ?> total
<?php echo $this->latest_cnt; ?> new

=======================

Latest Discussions:
<?php if (count($this->latest) > 0) : ?>
<?php foreach ($this->latest as $post) : ?>
----------------------------------------
<?php $postObj = \Components\Forum\Models\Post::getInstance($post->id); ?>
<?php echo User::getInstance($post->created_by)->get('name'); ?> | created: <?php echo Date::of($post->created)->toLocal('M j, Y g:i:s a') . "\n"; ?>
<?php echo $postObj->content('raw') . "\n"; ?>
----------------------------------------

<?php endforeach; ?>
<?php else : ?>
No new comments to display

<?php endif; ?>
<?php echo Request::root(); ?> sent this email because you are the primary instructor of a course. Visit our <?php echo Request::root(); ?>legal/privacy and our <?php echo Request::root(); ?>support pages if you have any questions.