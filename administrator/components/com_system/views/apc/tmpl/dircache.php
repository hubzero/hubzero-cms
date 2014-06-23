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
 * @copyright Copyright 2005-2011 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

// Menu items
JToolBarHelper::title(JText::_('COM_SYSTEM_APC_DIR'), 'config.png');

$this->MYREQUEST = $this->MYREQUEST;
$MY_SELF   = $this->MY_SELF;

?>

<?php
	$this->view('_submenu')->display();
?>

<form action="index.php?option=<?php echo $this->option ?>&amp;controller=<?php echo $this->controller; ?>" method="post" name="adminForm" id="adminForm">
	<fieldset id="filter-bar">
		<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
		<input type="hidden" name="controller" value="<?php echo $this->controller; ?>" />
		<input type="hidden" name="task" value="<?php echo $this->task; ?>" />
		<input type="hidden" name="OB" value="<?php echo $this->MYREQUEST['OB']; ?>" />
		<div class="col width-60 fltlft">
		<label for="filter-scope">Scope:</label>
		<select name="SCOPE" id="filter-scope">
			<option value="A"<?php echo $this->MYREQUEST['SCOPE']=='A' ? ' selected="selected"' : ''; ?>>Active</option>
			<option value="D"<?php echo $this->MYREQUEST['SCOPE']=='D' ? ' selected="selected"' : ''; ?>>Deleted</option>
		</select>

		<label for="filter-sort1">Sorting:</label>
		<select name="SORT1" id="filter-sort1">
			<option value="H"<?php echo $this->MYREQUEST['SORT1']=='H' ? ' selected="selected"' : ''; ?>>Total Hits</option>
			<option value="Z"<?php echo $this->MYREQUEST['SORT1']=='Z' ? ' selected="selected"' : ''; ?>>Total Size</option>
			<option value="T"<?php echo $this->MYREQUEST['SORT1']=='T' ? ' selected="selected"' : ''; ?>>Number of Files</option>
			<option value="S"<?php echo $this->MYREQUEST['SORT1']=='S' ? ' selected="selected"' : ''; ?>>Directory Name</option>
			<option value="A"<?php echo $this->MYREQUEST['SORT1']=='A' ? ' selected="selected"' : ''; ?>>Avg. Size</option>
			<option value="C"<?php echo $this->MYREQUEST['SORT1']=='C' ? ' selected="selected"' : ''; ?>>Avg. Hits</option>
		</select>

		<select name="SORT2">
			<option value="D"<?php echo $this->MYREQUEST['SORT2']=='D' ? ' selected="selected"' : ''; ?>>DESC</option>
			<option value="A"<?php echo $this->MYREQUEST['SORT2']=='A' ? ' selected="selected"' : ''; ?>>ASC</option>
		</select>

		<select name="COUNT" onChange="form.submit()">
			<option value="10" <?php echo $this->MYREQUEST['COUNT']=='10' ? ' selected="selected"' : ''; ?>>Top 10</option>
			<option value="20" <?php echo $this->MYREQUEST['COUNT']=='20' ? ' selected="selected"' : ''; ?>>Top 20</option>
			<option value="50" <?php echo $this->MYREQUEST['COUNT']=='50' ? ' selected="selected"' : ''; ?>>Top 50</option>
			<option value="100"<?php echo $this->MYREQUEST['COUNT']=='100'? ' selected="selected"' : ''; ?>>Top 100</option>
			<option value="150"<?php echo $this->MYREQUEST['COUNT']=='150'? ' selected="selected"' : ''; ?>>Top 150</option>
			<option value="200"<?php echo $this->MYREQUEST['COUNT']=='200'? ' selected="selected"' : ''; ?>>Top 200</option>
			<option value="500"<?php echo $this->MYREQUEST['COUNT']=='500'? ' selected="selected"' : ''; ?>>Top 500</option>
			<option value="0"  <?php echo $this->MYREQUEST['COUNT']=='0'  ? ' selected="selected"' : ''; ?>>All</option>
		</select>
		</div>
		<div class="col width-40 fltrt">
		<label for="AGGR">Group By Dir Level:</label>
		<select name="AGGR" id="AGGR">
			<option value="" selected="selected">None</option>
		<?php for ($i = 1; $i < 10; $i++) { ?>
			<option value="<?php echo $i; ?>"<?php echo $this->MYREQUEST['AGGR']==$i ? ' selected="selected"' : ''; ?>><?php echo $i; ?></option>
		<?php } ?>
		</select>
		&nbsp;<input type="submit" value="GO!" />
		</div>
	</fieldset>
	<div class="clr"></div>

	<table class="adminlist">
		<thead>
			<tr>
				<th><?php echo SystemHtml::sortheader($this->MYREQUEST, $this->MY_SELF_WO_SORT, 'S','Directory Name',  "&OB=" . $this->MYREQUEST['OB']); ?></th>
				<th><?php echo SystemHtml::sortheader($this->MYREQUEST, $this->MY_SELF_WO_SORT, 'T','Number of Files', "&OB=" . $this->MYREQUEST['OB']); ?></th>
				<th><?php echo SystemHtml::sortheader($this->MYREQUEST, $this->MY_SELF_WO_SORT, 'H','Total Hits',      "&OB=" . $this->MYREQUEST['OB']); ?></th>
				<th><?php echo SystemHtml::sortheader($this->MYREQUEST, $this->MY_SELF_WO_SORT, 'Z','Total Size',      "&OB=" . $this->MYREQUEST['OB']); ?></th>
				<th><?php echo SystemHtml::sortheader($this->MYREQUEST, $this->MY_SELF_WO_SORT, 'C','Avg. Hits',       "&OB=" . $this->MYREQUEST['OB']); ?></th>
				<th><?php echo SystemHtml::sortheader($this->MYREQUEST, $this->MY_SELF_WO_SORT, 'A','Avg. Size',       "&OB=" . $this->MYREQUEST['OB']); ?></th>
			</tr>
		</thead>
		<tbody>
<?php
	// builds list with alpha numeric sortable keys
	$tmp = $list = array();
	foreach ($this->cache[$this->scope_list[$this->MYREQUEST['SCOPE']]] as $entry)
	{
		$n = dirname($entry['filename']);
		if ($this->MYREQUEST['AGGR'] > 0)
		{
			$n = preg_replace("!^(/?(?:[^/\\\\]+[/\\\\]){".($this->MYREQUEST['AGGR']-1)."}[^/\\\\]*).*!", "$1", $n);
		}
		if (!isset($tmp[$n]))
		{
			$tmp[$n] = array('hits'=>0,'size'=>0,'ents'=>0);
		}
		$tmp[$n]['hits'] += $entry['num_hits'];
		$tmp[$n]['size'] += $entry['mem_size'];
		++$tmp[$n]['ents'];
	}

	foreach ($tmp as $k => $v)
	{
		switch ($this->MYREQUEST['SORT1'])
		{
			case 'A': $kn = sprintf('%015d-', $v['size'] / $v['ents']); break;
			case 'T': $kn = sprintf('%015d-', $v['ents']);              break;
			case 'H': $kn = sprintf('%015d-', $v['hits']);              break;
			case 'Z': $kn = sprintf('%015d-', $v['size']);              break;
			case 'C': $kn = sprintf('%015d-', $v['hits'] / $v['ents']); break;
			case 'S': $kn = $k;                                         break;
		}
		$list[$kn . $k] = array($k, $v['ents'], $v['hits'], $v['size']);
	}

	if ($list)
	{
		// sort list
		switch ($this->MYREQUEST['SORT2'])
		{
			case "A":	krsort($list);	break;
			case "D":	ksort($list);	break;
		}
		// output list
		$i = 0;
		foreach ($list as $entry)
		{
			echo
				'<tr class="row' . $i%2 . '">' .
				'<td class="td-0">' . $entry[0] . '</a></td>' .
				'<td class="td-n center">' . $entry[1] . '</td>' .
				'<td class="td-n center">' . $entry[2] . '</td>' .
				'<td class="td-n center">' . $entry[3] . '</td>' .
				'<td class="td-n center">' . round($entry[2] / $entry[1]) . '</td>' .
				'<td class="td-n center">' . round($entry[3] / $entry[1]) . '</td>' .
				'</tr>';

			if (++$i == $this->MYREQUEST['COUNT'])
			{
				break;
			}
		}
	}
	else
	{
		echo '<tr class="row0"><td class="center" colspan="6"><i>No data</i></td></tr>';
	}

	echo "</tbody></table>";

	if ($list && $i < count($list))
	{
		echo "<a href=\"$MY_SELF&OB=",$this->MYREQUEST['OB'],"&COUNT=0\"><i>",count($list)-$i,' more available...</i></a>';
	}
?>
</form>