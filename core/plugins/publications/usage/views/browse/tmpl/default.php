<?php
/**
 * @package		HUBzero CMS
 * @author		Shawn Rice <zooley@purdue.edu>
 * @copyright	Copyright 2005-2009 HUBzero Foundation, LLC.
 * @license		http://opensource.org/licenses/MIT MIT
 *
 * Copyright 2005-2009 HUBzero Foundation, LLC.
 * All rights reserved.
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
 */

// No direct access
defined('_HZEXEC_') or die();

if ($this->publication->alias) {
	$url = 'index.php?option=' . $this->option . '&alias=' . $this->publication->alias . '&active=usage';
} else {
	$url = 'index.php?option=' . $this->option . '&id=' . $this->publication->id . '&active=usage';
}

$database = App::get('db');

?>
<h3>
	<a name="usage"></a>
	<?php echo Lang::txt('PLG_PUBLICATION_USAGE'); ?>
</h3>
<div id="sub-sub-menu">
	<ul>
		<li<?php if ($this->period == '14') { echo ' class="active"'; } ?>><a href="<?php echo Route::url($url . '&period=14&dthis=' . $this->dthis); ?>"><span><?php echo Lang::txt('PLG_PUBLICATION_USAGE_PERIOD_OVERALL'); ?></span></a></li>
		<li<?php if ($this->period == 'prior12' || $this->period == '12') { echo ' class="active"'; } ?>><a href="<?php echo Route::url($url . '&period=12&dthis=' . $this->dthis); ?>"><span><?php echo Lang::txt('PLG_PUBLICATION_USAGE_PERIOD_PRIOR12'); ?></span></a></li>
		<li<?php if ($this->period == 'month' || $this->period == '1') { echo ' class="active"'; } ?>><a href="<?php echo Route::url($url . '&period=1&dthis=' . $this->dthis); ?>"><span><?php echo Lang::txt('PLG_PUBLICATION_USAGE_PERIOD_MONTH'); ?></span></a></li>
		<li<?php if ($this->period == 'qtr' || $this->period == '3') { echo ' class="active"'; } ?>><a href="<?php echo Route::url($url . '&period=3&dthis=' . $this->dthis); ?>"><span><?php echo Lang::txt('PLG_PUBLICATION_USAGE_PERIOD_QTR'); ?></span></a></li>
		<li<?php if ($this->period == 'year' || $this->period == '0') { echo ' class="active"'; } ?>><a href="<?php echo Route::url($url . '&period=0&dthis=' . $this->dthis); ?>"><span><?php echo Lang::txt('PLG_PUBLICATION_USAGE_PERIOD_YEAR'); ?></span></a></li>
		<li<?php if ($this->period == 'fiscal' || $this->period == '13') { echo ' class="active"'; } ?>><a href="<?php echo Route::url($url . '&period=13&dthis=' . $this->dthis); ?>"><span><?php echo Lang::txt('PLG_PUBLICATION_USAGE_PERIOD_FISCAL'); ?></span></a></li>
	</ul>
</div>
<form method="get" action="<?php echo Route::url($url); ?>">

</form>
