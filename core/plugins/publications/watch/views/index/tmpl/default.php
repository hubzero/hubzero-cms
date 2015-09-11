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
 * @author    Alissa Nedossekina <alisa@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

$this->css();

?>

<div class="watch<?php echo $this->watched ? ' watching' : ' '; ?>">
	<span class="pub-info-pop tooltips" title="<?php echo Lang::txt('PLG_PUBLICATIONS_WATCH_TITLE') . ' :: ' . Lang::txt('PLG_PUBLICATIONS_WATCH_EXPLAIN'); ?>">&nbsp;</span>
	<?php if ($this->watched) { ?>
		<p><?php echo Lang::txt('PLG_PUBLICATIONS_WATCH_WATCHED'); ?> <a href="<?php echo Route::url($this->publication->link() . '&active=watch&action=unsubscribe&confirm=1'); ?>"><?php echo Lang::txt('PLG_PUBLICATIONS_WATCH_UNSUBSCRIBE'); ?></a></p>
	<?php } else { ?>
		<p><?php echo Lang::txt('PLG_PUBLICATIONS_WATCH_WANT_TO_FOLLOW'); ?>
			<a href="<?php echo Route::url($this->publication->link() . '&active=watch&action=subscribe&confirm=1'); ?>"><?php echo Lang::txt('PLG_PUBLICATIONS_WATCH_SUBSCRIBE'); ?></a>
		</p>
	<?php } ?>
</div>