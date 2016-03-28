<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2015 HUBzero Foundation, LLC.
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 *
 * HUBzero is a registered trademark of Purdue University.
 *
 * @package   hubzero-cms
 * @author    Sam Wilson <samwilson@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

// Compute some counts for later use
$groups = count($this->posts);
$posts  = 0;

array_walk($this->posts, function($val, $idx) use (&$posts)
{
	$posts += count($val);
});
?>
You have <?php echo $posts; ?> new post<?php if ($posts > 1) echo 's'; ?> across <?php echo $groups; ?> of your groups

=======================
<?php foreach ($this->posts as $group => $posts) : ?>
<?php $group = Hubzero\User\Group::getInstance($group); ?>
<?php echo $group->description; ?>
<?php foreach ($posts as $post) : ?>
<?php $inst = $post; ?>


<?php echo User::getInstance($post->created_by)->get('name'); ?> | <?php echo Date::of($post->created)->toLocal('M j, Y g:i:s a'); ?>

<?php echo $inst->content('clean'); ?>
<?php $base = rtrim(Request::root(), '/'); ?>
<?php $sef  = Route::urlForClient('site', $inst->link()); ?>
<?php $link = $base . '/' . trim($sef, '/'); ?>
View this post on <?php echo Config::get('sitename'); ?>: <?php echo $link; ?>
<?php endforeach; ?>

-----------------------
<?php endforeach; ?>

This email was sent to you on behalf of <?php echo Request::root(); ?> becuase you are subscribed 
to these group discussion threads. To unsubscribe, please log in and adjust your email preferences 
for the group of interest.