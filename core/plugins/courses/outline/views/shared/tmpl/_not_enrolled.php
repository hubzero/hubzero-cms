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
 * @author    Shawn Rice <zooley@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

$this->js('_not_enrolled.js')
     ->css('_not_enrolled.css');

$oparams = new \Hubzero\Config\Registry($this->course->offering()->get('params'));

$price = 'free';
if ($cost = $oparams->get('store_price', false))
{
	$price = 'only $' . $cost;
}
?>

<div id="offering-introduction">
	<div class="instructions">
		<p class="warning"><?php echo Lang::txt($this->message); ?></p>
	</div>
	<div class="enroll-now">
		<a class="enroll btn" href="<?php echo Route::url($this->course->offering()->link('enroll')); ?>">Enroll for <?php echo $price; ?>!</a>
	</div>
	<div class="questions">
		<p>
			In order to access this part of the course, you need to enroll.
			If you're enrolled, you're not obligated to complete the course.
			But enrollment lets you:
		</p>
		<ul>
			<li>Take quizzes and exams</li>
			<li>Track your progress</li>
			<li>Add notes to lectures</li>
			<li>Participate in discussions</li>
		</ul>
		<p>
			For more details, check out our 
			<a class="advertise-popup" target="_blank" href="<?php echo Route::url('index.php?option=com_help&component=courses&page=basics#why_enroll'); ?>">
				enrollment benefits
			</a>.
		</p>
		<p>
			<strong><?php echo Lang::txt('I\'m convinced...now what?'); ?></strong>
		</p>
		<p>
			<a href="<?php echo Route::url($this->course->offering()->link('enroll')); ?>">Enroll for <?php echo $price; ?>!</a>
		</p>
		<p>
			<strong><?php echo Lang::txt('Want more details about this and similar courses?'); ?></strong>
			</p>
		<p>
			<?php echo Lang::txt(
				'To learn more, either visit the <a href="%s">course overview page</a> or browse the <a href="%s">course listing</a>.',
				Route::url($this->course->link()),
				Route::url('index.php?option=' . $this->option . '&controller=courses&task=browse'));
			?>
		</p>
	</div>
</div>