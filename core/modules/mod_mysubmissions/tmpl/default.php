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

if (User::isGuest()) { ?>
	<p class="warning"><?php echo Lang::txt('MOD_MYSUBMISSIONS_WARNING'); ?></p>
<?php } else {
	$steps = $this->steps;

	if ($this->rows)
	{
		$stepchecks = array();
		$laststep = (count($steps) - 1);

		foreach ($this->rows as $row)
		{
			?>
			<div class="submission">
				<h4>
					<?php echo $this->escape(stripslashes($row->title)); ?>
					<a class="edit" href="<?php echo Route::url('index.php?option=com_resources&task=draft&step=1&id=' . $row->id); ?>">
						<?php echo Lang::txt('MOD_MYSUBMISSIONS_EDIT'); ?>
					</a>
				</h4>
				<table>
					<tbody>
						<tr>
							<th><?php echo Lang::txt('MOD_MYSUBMISSIONS_TYPE'); ?></th>
							<td colspan="2"><?php echo $this->escape($row->typetitle); ?></td>
						</tr>
						<?php
						for ($i=1, $n=count($steps); $i < $n; $i++)
						{
							if ($i != $laststep)
							{
								$check = 'step_' . $steps[$i] . '_check';
								$stepchecks[$steps[$i]] = $this->$check($row->id);

								if ($stepchecks[$steps[$i]])
								{
									$completed = '<span class="yes">' . Lang::txt('MOD_MYSUBMISSIONS_COMPLETED') . '</span>';
								}
								else
								{
									$completed = '<span class="no">' . Lang::txt('MOD_MYSUBMISSIONS_NOT_COMPLETED') . '</span>';
								}
								?>
								<tr>
									<th><?php echo $steps[$i]; ?></th>
									<td><?php echo $completed; ?></td>
									<td>
										<a href="<?php echo Route::url('index.php?option=com_resources&task=draft&step=' . $i . '&id=' . $row->id); ?>">
											<?php echo Lang::txt('MOD_MYSUBMISSIONS_EDIT'); ?>
										</a>
									</td>
								</tr>
								<?php
							}
						}
						?>
				</table>
				<p class="discrd">
					<a href="<?php echo Route::url('index.php?option=com_resources&task=discard&id=' . $row->id); ?>">
						<?php echo Lang::txt('MOD_MYSUBMISSIONS_DELETE'); ?>
					</a>
				</p>
				<p class="review">
					<a href="<?php echo Route::url('index.php?option=com_com_resources&task=draft&step=' . $laststep . '&id=' . $row->id); ?>">
						<?php echo Lang::txt('MOD_MYSUBMISSIONS_REVIEW_SUBMIT'); ?>
					</a>
				</p>
				<div class="clear"></div>
			</div>
			<?php
		}
	}
	else
	{
		?>
		<p><?php echo Lang::txt('MOD_MYSUBMISSIONS_NONE'); ?></p>
		<?php
	}
}
