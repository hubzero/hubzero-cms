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

$this->css()
     ->js();
?>
<header id="content-header">
	<h2><?php echo $this->title; ?></h2>

	<?php if ($this->authorized) { ?>
	<div id="content-header-extra">
		<ul id="useroptions">
			<li class="last"><a class="icon-add add btn" href="<?php echo Route::url('index.php?option='.$this->option.'&task=add'); ?>"><?php echo Lang::txt('EVENTS_ADD_EVENT'); ?></a></li>
		</ul>
	</div><!-- / #content-header-extra -->
	<?php } ?>
</header><!-- / #content-header -->

<nav>
	<ul class="sub-menu">
		<li<?php if ($this->task == 'year') { echo ' class="active"'; } ?>><a href="<?php echo Route::url('index.php?option='.$this->option.'&year='.$this->year); ?>"><span><?php echo Lang::txt('EVENTS_CAL_LANG_REP_YEAR'); ?></span></a></li>
		<li<?php if ($this->task == 'month') { echo ' class="active"'; } ?>><a href="<?php echo Route::url('index.php?option='.$this->option.'&year='.$this->year.'&month='.$this->month); ?>"><span><?php echo Lang::txt('EVENTS_CAL_LANG_REP_MONTH'); ?></span></a></li>
		<li<?php if ($this->task == 'week') { echo ' class="active"'; } ?>><a href="<?php echo Route::url('index.php?option='.$this->option.'&year='.$this->year.'&month='.$this->month.'&day='.$this->day.'&task=week'); ?>"><span><?php echo Lang::txt('EVENTS_CAL_LANG_REP_WEEK'); ?></span></a></li>
		<li<?php if ($this->task == 'day') { echo ' class="active"'; } ?>><a href="<?php echo Route::url('index.php?option='.$this->option.'&year='.$this->year.'&month='.$this->month.'&day='.$this->day); ?>"><span><?php echo Lang::txt('EVENTS_CAL_LANG_REP_DAY'); ?></span></a></li>
	</ul>
</nav>

<section class="main section">
	<h3><?php echo stripslashes($this->event->title); ?></h3>
	<?php
		$html  = '<div id="sub-sub-menu">'."\n";
		$html .= '<ul>'."\n";
		$html .= "\t".'<li';
		if ($this->page->alias == '') {
			$html .= ' class="active"';
		}
		$html .= '><a class="tab" href="'. Route::url('index.php?option='.$this->option.'&task=details&id='.$this->event->id) .'"><span>'.Lang::txt('EVENTS_OVERVIEW').'</span></a></li>'."\n";
		if ($this->pages) {
			foreach ($this->pages as $p)
			{
				$html .= "\t".'<li';
				if ($this->page->alias == $p->alias) {
					$html .= ' class="active"';
				}
				$html .= '><a class="tab" href="'. Route::url('index.php?option='.$this->option.'&task=details&id='.$this->event->id.'&page='.$p->alias) .'"><span>'.trim(stripslashes($p->title)).'</span></a></li>'."\n";
			}
		}
		$html .= "\t".'<li';
		if ($this->page->alias == 'register') {
			$html .= ' class="active"';
		}
		$html .= '><a class="tab" href="'. Route::url('index.php?option='.$this->option.'&task=details&id='.$this->event->id.'&page=register') .'"><span>'.Lang::txt('EVENTS_REGISTER').'</span></a></li>'."\n";
		$html .= '</ul>'."\n";
		$html .= '<div class="clear"></div>'."\n";
		$html .= '</div>'."\n";
		echo $html;
	?>

	<?php if ($this->getError()) { ?>
		<p class="error"><?php echo $this->getError(); ?></p>
	<?php } ?>

	<form method="post" action="index.php" id="hubForm">
		<div class="explaination">
			<p><?php echo Lang::txt('EVENTS_PROVIDE_PASSWORD'); ?></p>
		</div>
		<fieldset>
			<legend><?php echo Lang::txt('EVENTS_LIMITED_REGISTRATION'); ?></legend>
			<label>
				<?php echo Lang::txt('EVENTS_PASSWORD'); ?>
				<input type="password" name="passwrd" />
			</label>
			<input type="hidden" name="id" value="<?php echo $this->event->id; ?>" />
			<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
			<input type="hidden" name="task" value="register" />
		</fieldset>
		<div class="clear"></div>
		<p class="submit"><input type="submit" value="<?php echo Lang::txt('EVENTS_SUBMIT'); ?>" /></p>
	</form>
</section><!-- / .main section -->
