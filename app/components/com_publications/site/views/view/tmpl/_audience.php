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

// No direct access
defined('_HZEXEC_') or die();

$levels   = array();
$labels   = array();
$selected = array();
$txtlabel = '';

$this->audience = !empty($this->audience[0]) ? $this->audience[0] : $this->audience;
$hideEmpty = isset($this->hideEmpty) ? $this->hideEmpty : true;

if ($this->audience)
{
	for ($i = 0, $n = $this->numlevels; $i <= $n; $i++)
	{
		$lb = 'label' . $i;
		$lv = 'level' . $i;
		$ds = 'desc' . $i;
		$levels[$lv] = $this->audience->$lv;
		$labels[$lv]['title'] = $this->audience->$lb;
		$labels[$lv]['desc']  = $this->audience->$ds;
		if ($this->audience->$lv)
		{
			$selected[] = $lv;
		}
	}
	if (empty($selected) && $hideEmpty == true)
	{
		return;
	}

	?>
	<div class="usagescale">
		<div class="showscale">
			<ul class="audiencelevel">
				<?php
				// colored circles
				foreach ($levels as $key => $value)
				{
					$class = (!$value) ? ' isoff' : '';
					$class = (!$value && $key == 'level0') ? '_isoff' : $class;
					?>
					<li class="<?php echo $key . $class; ?>"><span>&nbsp;</span></li>
					<?php
				}

				// figure out text label
				if (count($selected) == 1)
				{
					$txtlabel = $labels[$selected[0]]['title'];
				}
				else if (count($selected) > 1)
				{
					$first 	    = array_shift($selected);
					$first		= $labels[$first]['title'];
					$firstbits  = explode("-", $first);
					$first 	    = array_shift($firstbits);

					$last     = end($selected);
					$last     = $labels[$last]['title'];
					$lastbits = explode("-", $last);
					$last     = end($lastbits);

					$txtlabel = $first . '-' . $last;
				}
				else
				{
					$txtlabel = Lang::txt('Tool Audience Unrated');
				}
				?>
				<li class="txtlabel"><?php echo $txtlabel; ?></li>
			</ul>
		</div>

		<?php if ($this->showtips) { ?>
			<div class="explainscale">
				<table class="skillset">
					<thead>
						<tr>
							<td colspan="2" class="combtd"><?php echo Lang::txt('Difficulty Level'); ?></td>
							<td><?php echo Lang::txt('Target Audience'); ?></td>
						</tr>
					</thead>
					<tbody>
					<?php foreach ($labels as $key => $label) { ?>
						<tr>
							<th>
								<ul class="audiencelevel">
									<?php foreach ($labels as $ky => $val) { ?>
										<li class="<?php
											$class = ($ky != $key) ? ' isoff' : '';
											$class = ($ky != $key && $ky == 'level0') ? '_isoff' : $class;
											echo $ky . $class;
											?>"><span>&nbsp;</span></li>
									<?php } ?>
								</ul>
							</th>
							<td><?php echo $label['title']; ?></td>
							<td class="secondcol"><?php echo $label['desc']; ?></td>
						</tr>
					<?php } ?>
					</tbody>
				</table>
				<p class="learnmore"><a href="<?php echo $this->audiencelink; ?>"><?php echo Lang::txt('Learn more'); ?> &rsaquo;</a></p>
			</div>
		<?php } ?>
	</div>
<?php } ?>