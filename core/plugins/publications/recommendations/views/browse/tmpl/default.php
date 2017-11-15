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

$this->js()
	->css();

?>
<div id="recommendations">
	<h3><?php echo Lang::txt('PLG_PUBLICATION_RECOMMENDATIONS_HEADER'); ?></h3>
	<div class="aside">
		<p><?php echo Lang::txt('PLG_PUBLICATION_RECOMMENDATIONS_EXPLANATION'); ?></p>
	</div>
	<div class="subject" id="recommendations-subject">
		<?php if ($this->results) { ?>
			<ul>
				<?php
				foreach ($this->results as $line)
				{
					// Get the SEF for the publication
					$sef = Route::url('index.php?option=' . $this->option . ($line->alias ? '&alias=' . $line->alias : '&id=' . $line->id) . '&rec_ref=' . $this->publication->id);
					?>
					<li>
						<a href="<?php echo $sef; ?>"><?php echo $this->escape(stripslashes($line->title)); ?></a>
					</li>
					<?php
				}
				?>
			</ul>
		<?php } else { ?>
			<p><?php echo Lang::txt('PLG_PUBLICATION_RECOMMENDATIONS_NO_RESULTS_FOUND'); ?></p>
		<?php } ?>
		<p id="credits"><a href="<?php echo Request::base(true); ?>/about/hubzero#recommendations"><?php echo Lang::txt('PLG_PUBLICATION_RECOMMENDATIONS_POWERED_BY'); ?></a></p>
	</div>
</div>
