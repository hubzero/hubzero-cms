<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
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
			<a class="advertise-popup" href="<?php echo Route::url('index.php?option=com_help&component=courses&page=basics#why_enroll'); ?>">
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