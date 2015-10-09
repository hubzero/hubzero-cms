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

// No direct access.
defined('_HZEXEC_') or die();

// Menu items
Toolbar::title(Lang::txt('COM_SYSTEM_APC_SYSTEM'), 'config.png');

$this->css('apc.css');

//$this->MYREQUEST = $this->MYREQUEST;
$this->MYREQUEST = str_replace('&amp;', '&', $this->MYREQUEST);
$this->MYREQUEST = str_replace('&', '&amp;', $this->MYREQUEST);
$this->MY_SELF_WO_SORT = str_replace('&amp;', '&', $this->MY_SELF_WO_SORT);
$this->MY_SELF_WO_SORT = str_replace('&', '&amp;', $this->MY_SELF_WO_SORT);
$MY_SELF   = str_replace('&amp;', '&', $this->MY_SELF);
$MY_SELF   = str_replace('&', '&amp;', $MY_SELF);
$cache     = $this->cache;

?>

<?php
	$this->view('_submenu')->display();
?>

<form action="<?php echo Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller); ?>" method="post" name="adminForm" id="adminForm">
<?php
	if (!isset($fieldname))
	{
		$fieldname = 'filename';
		$fieldheading = 'Script Filename';
		if (ini_get("apc.stat")) $fieldkey = 'inode';
		else $fieldkey = 'filename';
	}
	if (!empty($this->MYREQUEST['SH']))
	{
		echo '<table class="adminlist"><thead>';
		echo '<tr><th>Attribute</th><th>Value</th></tr></thead><tbody>';

		$m=0;
		foreach ($this->scope_list as $j => $list)
		{
			foreach ($cache[$list] as $i => $entry)
			{
				if (md5($entry[$fieldkey])!=$this->MYREQUEST['SH']) continue;
				foreach ($entry as $k => $value)
				{
					if ($k == "num_hits")
					{
						$value = sprintf("%s (%.2f%%)",$value,$value*100/$cache['num_hits']);
					}
					if ($k == 'deletion_time')
					{
						if (!$entry['deletion_time']) $value = "None";
					}
					echo
						"<tr class=\"tr-$m\">",
						"<td class=\"td-0\">",ucwords(preg_replace("/_/"," ",$k)),"</td>",
						"<td class=\"td-last\">",(preg_match("/time/",$k) && $value!='None') ? date(DATE_FORMAT,$value) : $value,"</td>",
						"</tr>";
					$m=1-$m;
				}
				if ($fieldkey == 'info')
				{
					echo "<tr class=\"tr-$m\"><td class=\"td-0\">Stored Value</td><td class=\"td-last\"><pre>";
					$output = var_export(apc_fetch($entry[$fieldkey]),true);
					echo htmlspecialchars($output);
					echo "</pre></td></tr>\n";
				}
				break;
			}
		}

		echo '</tbody></table>';
		echo '</div>';
	}
	else
	{
		$cols = 7;
?>
	<fieldset id="filter-bar">
		<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
		<input type="hidden" name="controller" value="<?php echo $this->controller; ?>" />
		<input type="hidden" name="task" value="<?php echo $this->task; ?>" />
		<input type="hidden" name="OB" value="<?php echo $this->MYREQUEST['OB']; ?>" />
		<div class="grid">
			<div class="col span7">
				<label for="filter-scope">Scope:</label>
				<select name="SCOPE" id="filter-scope">
					<option value="A"<?php echo $this->MYREQUEST['SCOPE']=='A' ? ' selected="selected"' : ''; ?>>Active</option>
					<option value="D"<?php echo $this->MYREQUEST['SCOPE']=='D' ? ' selected="selected"' : ''; ?>>Deleted</option>
				</select>

				<label for="filter-sort1">Sorting:</label>
				<select name="SORT1" id="filter-sort1">
					<option value="H"<?php echo $this->MYREQUEST['SORT1']=='H' ? ' selected="selected"' : ''; ?>>Hits</option>
					<option value="Z"<?php echo $this->MYREQUEST['SORT1']=='Z' ? ' selected="selected"' : ''; ?>>Size</option>
					<option value="S"<?php echo $this->MYREQUEST['SORT1']=='S' ? ' selected="selected"' : ''; ?>><?php echo $fieldheading; ?></option>
					<option value="A"<?php echo $this->MYREQUEST['SORT1']=='A' ? ' selected="selected"' : ''; ?>>Last accessed</option>
					<option value="M"<?php echo $this->MYREQUEST['SORT1']=='M' ? ' selected="selected"' : ''; ?>>Last modified</option>
					<option value="C"<?php echo $this->MYREQUEST['SORT1']=='C' ? ' selected="selected"' : ''; ?>>Created at</option>
					<option value="D"<?php echo $this->MYREQUEST['SORT1']=='D' ? ' selected="selected"' : ''; ?>>Deleted at</option>
				<?php if ($fieldname=='info') { ?>
					<option value="D"<?php echo $this->MYREQUEST['SORT1']=='T' ? ' selected="selected"' : ''; ?>>Timeout</option>
				<?php } ?>
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
			<div class="col span5">
				<label for="filter_search">Search:</label>
				<input name="SEARCH" id="filter_search" value="<?php echo $this->MYREQUEST['SEARCH']; ?>" type="text" size="25" />

				&nbsp;<input type="submit" value="GO!" />
			</div>
		</div>
	</fieldset>

<?php
		if (isset($this->MYREQUEST['SEARCH']))
		{
			// Don't use preg_quote because we want the user to be able to specify a
			// regular expression subpattern.
			$this->MYREQUEST['SEARCH'] = '/' . str_replace('/', '\\/', $this->MYREQUEST['SEARCH']) . '/i';
			if (preg_match($this->MYREQUEST['SEARCH'], 'test') === false)
			{
				echo '<p class="error">Error: enter a valid regular expression as a search query.</p>';
				break;
			}
		}
?>
	<table class="adminlist">
		<thead>
			<tr>
				<th><?php echo \Components\System\Helpers\Html::sortheader($this->MYREQUEST, $this->MY_SELF_WO_SORT, 'S',$fieldheading,   "&amp;OB=" . $this->MYREQUEST['OB']); ?></th>
				<th><?php echo \Components\System\Helpers\Html::sortheader($this->MYREQUEST, $this->MY_SELF_WO_SORT, 'H','Hits',          "&amp;OB=" . $this->MYREQUEST['OB']); ?></th>
				<th><?php echo \Components\System\Helpers\Html::sortheader($this->MYREQUEST, $this->MY_SELF_WO_SORT, 'Z','Size',          "&amp;OB=" . $this->MYREQUEST['OB']); ?></th>
				<th><?php echo \Components\System\Helpers\Html::sortheader($this->MYREQUEST, $this->MY_SELF_WO_SORT, 'A','Last accessed', "&amp;OB=" . $this->MYREQUEST['OB']); ?></th>
				<th><?php echo \Components\System\Helpers\Html::sortheader($this->MYREQUEST, $this->MY_SELF_WO_SORT, 'M','Last modified', "&amp;OB=" . $this->MYREQUEST['OB']); ?></th>
				<th><?php echo \Components\System\Helpers\Html::sortheader($this->MYREQUEST, $this->MY_SELF_WO_SORT, 'C','Created at',    "&amp;OB=" . $this->MYREQUEST['OB']); ?></th>
<?php
		if ($fieldname=='info')
		{
			$cols+=1;
?>
				<th><?php echo \Components\System\Helpers\Html::sortheader($this->MYREQUEST, $this->MY_SELF_WO_SORT, 'T','Timeout', "&amp;OB=" . $this->MYREQUEST['OB']); ?></th>
<?php
		}
?>
				<th><?php echo \Components\System\Helpers\Html::sortheader($this->MYREQUEST, $this->MY_SELF_WO_SORT, 'D','Deleted at', "&amp;OB=" . $this->MYREQUEST['OB']); ?></th>
			</tr>
		</thead>
		<tbody>
<?php
		// builds list with alpha numeric sortable keys
		//
		$list = array();
		foreach ($this->cache[$this->scope_list[$this->MYREQUEST['SCOPE']]] as $i => $entry)
		{
			switch ($this->MYREQUEST['SORT1'])
			{
				case 'A': $k = sprintf('%015d-', $entry['access_time']);   break;
				case 'H': $k = sprintf('%015d-', $entry['num_hits']);      break;
				case 'Z': $k = sprintf('%015d-', $entry['mem_size']);      break;
				case 'M': $k = sprintf('%015d-', $entry['mtime']);         break;
				case 'C': $k = sprintf('%015d-', $entry['creation_time']); break;
				case 'T': $k = sprintf('%015d-', $entry['ttl']);           break;
				case 'D': $k = sprintf('%015d-', $entry['deletion_time']); break;
				case 'S': $k = '';                                         break;
			}
			$list[$k . $entry[$fieldname]] = $entry;
		}

		if ($list)
		{
			// sort list
			switch ($this->MYREQUEST['SORT2'])
			{
				case "A": krsort($list); break;
				case "D": ksort($list);  break;
			}

			// output list
			$i=0;
			foreach ($list as $k => $entry)
			{
				if (!$this->MYREQUEST['SEARCH'] || preg_match($this->MYREQUEST['SEARCH'], $entry[$fieldname]) != 0)
				{
					$field_value = $this->escape(strip_tags($entry[$fieldname], ''));
					echo
						'<tr class="tr-',$i%2,'">',
						"<td class=\"td-0\"><a href=\"$MY_SELF&amp;OB=",$this->MYREQUEST['OB'],"&amp;SH=",md5($entry[$fieldkey]),"\">",$field_value,'</a></td>',
						'<td class="td-n center">',$entry['num_hits'],'</td>',
						'<td class="td-n right">',$entry['mem_size'],'</td>',
						'<td class="td-n center">',date(DATE_FORMAT,$entry['access_time']),'</td>',
						'<td class="td-n center">',date(DATE_FORMAT,$entry['mtime']),'</td>',
						'<td class="td-n center">',date(DATE_FORMAT,$entry['creation_time']),'</td>';

					if ($fieldname=='info')
					{
						if ($entry['ttl'])
						{
							echo '<td class="td-n center">'.$entry['ttl'].' seconds</td>';
						}
						else
						{
							echo '<td class="td-n center">None</td>';
						}
					}
					if ($entry['deletion_time'])
					{
						echo '<td class="td-last center">', date(DATE_FORMAT,$entry['deletion_time']), '</td>';
					}
					else if ($this->MYREQUEST['OB'] == OB_USER_CACHE)
					{
						echo '<td class="td-last center">';
						echo '[<a href="', $MY_SELF, '&amp;OB=', $this->MYREQUEST['OB'], '&amp;DU=', urlencode($entry[$fieldkey]), '">Delete Now</a>]';
						echo '</td>';
					}
					else
					{
						echo '<td class="td-last center"> &nbsp; </td>';
					}
					echo '</tr>';
					$i++;
					if ($i == $this->MYREQUEST['COUNT'])
					{
						break;
					}
				}
			}
		}
		else
		{
			echo '<tr class="tr-0"><td class="center" colspan="',$cols,'"><i>No data</i></td></tr>';
		}
		echo '</tbody></table>';

		if ($list && $i < count($list))
		{
			echo "<a href=\"$MY_SELF&amp;OB=",$this->MYREQUEST['OB'],"&amp;COUNT=0\"><i>",count($list)-$i,' more available...</i></a>';
		}
	}
?>
</form>