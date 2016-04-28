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
 * @author	Shawn Rice <zooley@purdue.edu>, Kevin Wojkovich <kevinw@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

$this->css('citations.css')
	 ->js();

$base = 'index.php?option=com_members&id=' . $this->member->get('id') . '&active=citations';

if (isset($this->messages))
{
	foreach ($this->messages as $message)
	{
		echo "<p class=\"{$message['type']}\">" . $message['message'] . "</p>";
	}
}
?>

<div id="content-header-extra"><!-- Citation management buttons -->
	<?php if ($this->isAdmin) : ?>
		<a class="btn icon-add" href="<?php echo Route::url($base. '&action=add'); ?>">
			<?php echo Lang::txt('PLG_MEMBERS_CITATIONS_SUBMIT_CITATION'); ?>
		</a>
		<a class="btn icon-upload" href="<?php echo Route::url($base. '&action=import'); ?>">
			<?php echo Lang::txt('PLG_MEMBERS_CITATIONS_IMPORT_CITATION'); ?>
		</a>
		<a class="btn icon-settings" href="<?php echo Route::url($base. '&action=settings'); ?>">
			<?php echo Lang::txt('PLG_MEMBERS_CITATIONS_SET_FORMAT'); ?>
		</a>
	<?php endif; ?>
</div><!-- / Citations management buttons -->

<div id="intro-container">
<div id="citations-introduction">
	<div class="instructions">
	<h2 id="instructions-title"><?php echo Lang::txt('PLG_MEMBERS_CITATIONS'); ?></h2>
	<p id="noCitations"> <?php echo Lang::txt('PLG_MEMBERS_CITATIONS_NO_CITATIONS_FOUND'); ?></p>
	<?php if ($this->isAdmin): ?>
	<p id="who"><?php echo Lang::txt('PLG_MEMBERS_CITATIONS_MEMBER_MAY'); ?></p>
	<ul>
		<li>
			<div class="instruction">
			<a class="btn icon-add" href="<?php echo Route::url($base. '&action=add'); ?>">
				<?php echo Lang::txt('PLG_MEMBERS_CITATIONS_SUBMIT_CITATION'); ?>
		</a>
		<span class="description"><?php echo Lang::txt('PLG_MEMBERS_CITATIONS_MANUALLY_ENTER'); ?></span>
		</div>
	</li>
 		<li>
		<div class="instruction">
		<a class="btn icon-upload" href="<?php echo Route::url($base. '&action=import'); ?>">
			<?php echo Lang::txt('PLG_MEMBERS_CITATIONS_IMPORT_CITATION'); ?>
		</a>
		<span class="description">  Import a list of citations.</span>
		</div>
		</li>
		<li>
		<span class="or">or</span>
		</li>
 		<li>
		<div class="instruction">
			<a class="btn icon-settings" href="<?php echo Route::url($base. '&action=settings'); ?>">
			<?php echo Lang::txt('PLG_MEMBERS_CITATIONS_SET_FORMAT'); ?>
		 </a>
		<span class="description"><?php echo Lang::txt('PLG_MEMBERS_CITATIONS_SETTINGS_DESCRIPTION'); ?></span>
		</div>
			</li>
 </ul>
 <?php endif; ?>
</div><!-- / .instructions -->
	<div class="questions">
	<p><strong><?php echo Lang::txt('PLG_MEMBERS_CITATIONS_INTRO_WHAT_IS_THIS'); ?></strong></p>
	<p><?php echo Lang::txt('PLG_MEMBERS_CITATIONS_INTRO_WHAT_IS_THIS_EXPLANATION'); ?></p>
	</div>
	</div>

</div> <!-- /#intro-container --> 
