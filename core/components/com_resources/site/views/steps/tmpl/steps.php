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

// No direct access.
defined('_HZEXEC_') or die();

$attachments = 0;
$authors     = 0;
$tags        = array();
$state       = 'draft';

if ($this->resource->get('id'))
{
	switch ($this->resource->get('published'))
	{
		case 1: $state = 'published';  break;  // published
		case 2: $state = 'draft';      break;  // draft
		case 3: $state = 'pending';    break;  // pending
		case 0:
		default: $state = 'unpublished';  break;  // unpublished
	}

	$attachments = $this->resource->children()->total();

	$authors =  $this->resource->authors()->total();

	$tags = $this->resource->tags()->count();
}
?>
<div class="meta-container">
	<table class="meta">
		<thead>
			<tr>
				<th scope="col"><?php echo Lang::txt('COM_CONTRIBUTE_STEP_TYPE'); ?></th>
				<th scope="col"><?php echo Lang::txt('COM_CONTRIBUTE_TITLE'); ?></th>
				<th scope="col" colspan="3"><?php echo Lang::txt('COM_CONTRIBUTE_ASSOCIATIONS'); ?></th>
				<th scope="col"><?php echo Lang::txt('COM_CONTRIBUTE_STATUS'); ?></th>
			<?php if ($this->progress['submitted'] != 1) { ?>
				<th></th>
			<?php } ?>
			</tr>
		</thead>
		<tbody>
			<tr>
				<td>
					<?php echo $this->resource->type()->get('title', Lang::txt('COM_CONTRIBUTE_NONE')); ?>
				</td>
				<td>
					<?php echo ($this->resource->get('title') ? $this->escape(\Hubzero\Utility\String::truncate(stripslashes($this->resource->get('title')), 150)) : Lang::txt('COM_CONTRIBUTE_NONE')); ?>
				</td>
				<td>
					<?php echo Lang::txt('%s attachment(s)', $attachments); ?>
				</td>
				<td>
					<?php echo Lang::txt('%s author(s)', $authors); ?>
				</td>
				<td>
					<?php echo Lang::txt('%s tag(s)', $tags); ?>
				</td>
				<td>
					<span class="<?php echo $state; ?> status"><?php echo $state; ?></span>
				</td>
			<?php if ($this->progress['submitted'] != 1) { ?>
				<td>
				<?php if ($this->step == 'discard') { ?>
					<strong><?php echo Lang::txt('COM_CONTRIBUTE_CANCEL'); ?></strong>
				<?php } else { ?>
					<a class="icon-delete" href="<?php echo Route::url('index.php?option='.$this->option.'&task=discard&id='.$this->id); ?>"><?php echo Lang::txt('COM_CONTRIBUTE_CANCEL'); ?></a>
				<?php } ?>
				</td>
			<?php } ?>
			</tr>
		</tbody>
	</table>
</div>

<ol id="steps">
	<li id="start">
		<a href="<?php echo ($this->progress['submitted'] == 1) ? Route::url('index.php?option=com_resources&id=' . $this->id) : Route::url('index.php?option=' . $this->option . '&task=new'); ?>">
			<?php echo Lang::txt('COM_CONTRIBUTE_START'); ?>
		</a>
	</li>
	<?php
	$laststep = (count($this->steps) - 1);

	$html  = '';
	for ($i=1, $n=count($this->steps); $i < $n; $i++)
	{
		$html .= "\t".'<li';
		if ($this->step == $i) {
			$html .= ' class="active"';
		} elseif ($this->progress[$this->steps[$i]] == 1) {
			$html .= ' class="completed"';
		}
		$html .= '>';
		if ($this->step == $i)
		{
			$html .= '<strong>' . Lang::txt('COM_CONTRIBUTE_STEP_'.strtoupper($this->steps[$i])) . '</strong>';
		}
		elseif ($this->progress[$this->steps[$i]] == 1 || $this->step > $i)
		{
			$html .= '<a href="'. Route::url('index.php?option='.$this->option.'&task=draft&step='.$i.'&id='.$this->id) .'">'.Lang::txt('COM_CONTRIBUTE_STEP_'.strtoupper($this->steps[$i])).'</a>';
		}
		else
		{
			if ($this->progress['submitted'] == 1)
			{
				$html .= '<a href="'. Route::url('index.php?option='.$this->option.'&task=draft&step='.$i.'&id='.$this->id) .'">'.Lang::txt('COM_CONTRIBUTE_STEP_'.strtoupper($this->steps[$i])).'</a>';
			}
			else
			{
				$html .= '<span>' . $this->steps[$i] . '</span>';
			}
		}
		$html .= '</li>'."\n";
	}
	echo $html;
	?>
</ol>
<div class="clear"></div>
