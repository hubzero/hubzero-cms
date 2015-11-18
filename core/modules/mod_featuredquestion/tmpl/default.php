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
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

// no direct access
defined('_HZEXEC_') or die();

if ($this->getError()) { ?>
	<p class="error"><?php echo Lang::txt('MOD_FEATUREDQUESTION_MISSING_CLASS'); ?></p>
<?php } else {
	if ($this->row) {
		$name = Lang::txt('MOD_FEATUREDQUESTION_ANONYMOUS');
		if (!$this->row->get('anonymous'))
		{
			$name = $row->creator()->get('name');
		}

		$rcount = $this->row->responses()->where('state', '<', 2)->count();
		$when = Date::of($this->row->get('created'))->relative();
	?>
	<div class="<?php echo $this->cls; ?>">
		<h3><?php echo Lang::txt('MOD_FEATUREDQUESTION'); ?></h3>
		<?php if (is_file(PATH_APP . $this->thumb)) { ?>
			<p class="featured-img">
				<a href="<?php echo Route::url($this->row->link()); ?>">
					<img width="50" height="50" src="<?php echo $this->thumb; ?>" alt="" />
				</a>
			</p>
		<?php } ?>
		<p>
			<a href="<?php echo Route::url($this->row->link()); ?>">
				<?php echo $this->escape(strip_tags($this->row->subject)); ?>
			</a>
			<?php if ($this->row->get('question')) { ?>
				: <?php echo \Hubzero\Utility\String::truncate($this->escape(strip_tags($this->row->question)), $this->txt_length); ?>
			<?php } ?>
			<br />
			<span><?php echo Lang::txt('MOD_FEATUREDQUESTION_ASKED_BY', $name); ?></span> -
			<span><?php echo Lang::txt('MOD_FEATUREDQUESTION_AGO', $when); ?></span> -
			<span><?php echo ($rcount == 1) ? Lang::txt('MOD_FEATUREDQUESTION_RESPONSE', $rcount) : Lang::txt('MOD_FEATUREDQUESTION_RESPONSES', $rcount); ?></span>
		</p>
	</div>
	<?php
	}
}
