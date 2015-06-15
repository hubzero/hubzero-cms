<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2015 Purdue University. All rights reserved.
 *
 * This file is part of: The HUBzero(R) Platform for Scientific Collaboration
 *
 * The HUBzero(R) Platform for Scientific Collaboration (HUBzero) is free
 * software: you can redistribute it and/or modify it under the terms of
 * the GNU Lesser General Public License as published by the Free Software
 * Foundation, either version 3 of the License, or (at your option) any
 * later version.
 *
 * HUBzero is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * HUBzero is a registered trademark of Purdue University.
 *
 * @package   hubzero-cms
 * @author    Sam Wilson <samwilson@purdue.edu>
 * @copyright Copyright 2005-2015 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
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
<?php $inst = \Components\Forum\Models\Post::getInstance($post->id); ?>


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