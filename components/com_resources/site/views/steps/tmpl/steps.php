<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2011 Purdue University. All rights reserved.
 *
 * This file is part of: The HUBzero(R) Platform for Scientific Collaboration
 *
 * The HUBzero(R) Platform for Scientific Collaboration (HUBzero) is free
 * software: you can redistribute it and/or modify it under the terms of
 * the GNU Lesser General Public License as published by the Free Software
 * Foundation, either version 3 of the License, or (at your option) any
 * later version.
 *
 * HUBzero is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * HUBzero is a registered trademark of Purdue University.
 *
 * @package   hubzero-cms
 * @author    Shawn Rice <zooley@purdue.edu>
 * @copyright Copyright 2005-2011 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

$attachments = 0;
$authors = 0;
$tags = array();
$state = 'draft';
if ($this->resource->id)
{
	$database = JFactory::getDBO();
	$ra = new \Components\Resources\Tables\Assoc($database);
	$rc = new \Components\Resources\Tables\Contributor($database);
	$rt = new \Components\Resources\Helpers\Tags($this->resource->id);

	switch ($this->resource->published)
	{
		case 1: $state = 'published';  break;  // published
		case 2: $state = 'draft';      break;  // draft
		case 3: $state = 'pending';    break;  // pending
	}

	$attachments = $ra->getCount($this->resource->id);

	$authors = $rc->getCount($this->resource->id, 'resources');

	$tags = $rt->tags('count');
}
?>
<div class="meta-container">
	<table class="meta">
		<thead>
			<tr>
				<th scope="col"><?php echo Lang::txt('Type'); ?></th>
				<th scope="col"><?php echo Lang::txt('Title'); ?></th>
				<th scope="col" colspan="3"><?php echo Lang::txt('Associations'); ?></th>
				<th scope="col"><?php echo Lang::txt('Status'); ?></th>
			<?php if ($this->progress['submitted'] != 1) { ?>
				<th></th>
			<?php } ?>
			</tr>
		</thead>
		<tbody>
			<tr>
				<td>
					<?php echo ($this->resource->getTypeTitle()) ? $this->escape(stripslashes($this->resource->getTypeTitle())) : Lang::txt('(none)'); ?>
				</td>
				<td>
					<?php echo ($this->resource->title) ? $this->escape(\Hubzero\Utility\String::truncate(stripslashes($this->resource->title), 150)) : Lang::txt('(none)'); ?>
				</td>
				<td>
					<?php echo $attachments; ?> attachment(s)
				</td>
				<td>
					<?php echo $authors; ?> author(s)
				</td>
				<td>
					<?php echo $tags; ?> tag(s)
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
for ($i=1, $n=count( $this->steps ); $i < $n; $i++)
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
		$html .= '<strong>' . $this->steps[$i] . '</strong>';
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
