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

// No direct access
defined('_HZEXEC_') or die();

require_once PATH_CORE . DS . 'components' . DS . 'com_forum' . DS . 'models' . DS . 'post.php';
require_once PATH_CORE . DS . 'components' . DS . 'com_forum' . DS . 'models' . DS . 'category.php';
require_once PATH_CORE . DS . 'components' . DS . 'com_forum' . DS . 'models' . DS . 'section.php';

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