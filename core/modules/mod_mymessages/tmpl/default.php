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

// no direct access
defined('_HZEXEC_') or die();

if ($this->getError())
{
	echo '<p class="error">' . $this->getError() . '</p>' . "\n";
}
else
{
	// Push the module CSS to the template
	$this->css();
	?>
	<div<?php echo ($this->moduleclass) ? ' class="' . $this->moduleclass . '"' : ''; ?>>
		<ul class="module-nav">
			<li><a class="icon-email-alt" href="<?php echo Route::url('index.php?option=com_members&id=' . User::get('id') . '&active=messages'); ?>"><?php echo Lang::txt('MOD_MYMESSAGES_ALL_MESSAGES'); ?></a></li>
			<li><a class="icon-plus" href="<?php echo Route::url('index.php?option=com_members&id=' . User::get('id') . '&active=messages&task=settings'); ?>"><?php echo Lang::txt('MOD_MYMESSAGES_MESSAGE_SETTINGS'); ?></a></li>
		</ul>

		<?php if (count($this->rows) <= 0) { ?>
			<p><em><?php echo Lang::txt('MOD_MYMESSAGES_NO_MESSAGES'); ?></em></p>
		<?php } else { ?>
			<ul class="expandedlist">
				<?php
				foreach ($this->rows as $row)
				{
					$cls = 'box';
					if ($row->actionid)
					{
						$cls = 'actionitem';
					}
					if ($row->component == 'support' || $row->component == 'com_support')
					{
						$fg = explode(' ', $row->subject);
						$fh = array_pop($fg);
						$row->subject = implode(' ', $fg);
					}
					?>
					<li class="<?php echo $cls; ?>">
						<a href="<?php echo Route::url('index.php?option=com_members&id=' . User::get('id') . '&active=messages&msg=' . $row->id); ?>">
							<?php echo $this->escape(stripslashes($row->subject)); ?>
						</a>
						<span>
							<span>
								<time datetime="<?php echo $this->escape($row->created); ?>"><?php echo Date::of($row->created)->toLocal(Lang::txt('DATE_FORMAT_HZ1')); ?></time>
							</span>
						</span>
					</li>
					<?php
				}
				?>
			</ul>
		<?php } ?>
		<?php if ($this->total > $this->limit) { ?>
			<p class="note"><?php echo Lang::txt('MOD_MYMESSAGES_YOU_HAVE_MORE', $this->limit, $this->total, Route::url('index.php?option=com_members&id=' . User::get('id') . '&active=messages')); ?></p>
		<?php } ?>
	</div>
	<?php
}