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
JToolBarHelper::title(JText::_('APC Host Information'), 'addedit.png');

$time = $this->time;

?>

<script type="text/javascript">
window.addEvent('domready', function() {
	var clrcache = $('clearcache');
	
	clrcache.addEvent('click', function(e) {
		var mes = confirm('Are you sure?');
		if(!mes) {
			new Event(e).stop();
		}
		return res;
	});
});
</script>

<div id="clearcache" style="float:right;margin-top:-61px;">
	<a href="/administrator/index.php?option=com_apc&task=clrcache">Clear <?php echo $this->cache_mode; ?> cache</a>
</div>

<div class=content>
<div class="leftcol">
	<div class="info div1">
		<h2>General Cache Information</h2>
		<table cellspacing=0>
			<tbody>
				<tr class=tr-0><td class=td-0>APC Version</td><td><?php echo $this->apcversion; ?></td></tr>
				<tr class=tr-1><td class=td-0>PHP Version</td><td><?php echo $this->phpversion; ?></td></tr>
				<tr class=tr-0><td class=td-0>APC Host</td><td><?php echo $_SERVER['SERVER_NAME'] . ' ' . $this->host; ?></td></tr>
				<tr class=tr-1><td class=td-0>Server Software</td><td><?php echo $_SERVER['SERVER_SOFTWARE']; ?></td></tr>
				<tr class=tr-0>
					<td class=td-0>Shared Memory</td>
					<td>
						<?php echo "{$this->mem['num_seg']} Segment(s) with {$this->seg_size}
							<br />
						({$this->cache['memory_type']} memory, {$this->cache['locking_type']} locking)"; ?>
					</td>
				</tr>
				<tr class=tr-1>
					<td class=td-0>Start Time</td>
					<td><?php echo date(DATE_FORMAT, $this->cache['start_time']); ?></td>
				</tr>
				<tr class=tr-0>
					<td class=td-0>Uptime</td>
					<td><?php echo $this->duration; ?></td>
				</tr>
				<tr class=tr-1>
					<td class=td-0>File Upload Support</td>
					<td><?php echo $this->cache['file_upload_progress']; ?></td>
				</tr>
			</tbody>
		</table>
	</div>
	<div class="info div1">
		<h2>File Cache Information</h2>
		<table cellspacing=0>
			<tbody>
				<tr class=tr-0><td class=td-0>Cached Files</td><td><?php echo "$this->number_files ($this->size_files)"; ?></td></tr>
				<tr class=tr-1><td class=td-0>Hits</td><td><?php echo "{$this->cache['num_hits']}"; ?></td></tr>
				<tr class=tr-0><td class=td-0>Misses</td><td><?php echo "{$this->cache['num_misses']}"; ?></td></tr>
				<tr class=tr-1><td class=td-0>Request Rate (hits, misses)</td><td><?php echo "$this->req_rate cache requests/second"; ?></td></tr>
				<tr class=tr-0><td class=td-0>Hit Rate</td><td><?php echo "$this->hit_rate cache requests/second"; ?></td></tr>
				<tr class=tr-1><td class=td-0>Miss Rate</td><td><?php echo "$this->miss_rate cache requests/second"; ?></td></tr>
				<tr class=tr-0><td class=td-0>Insert Rate</td><td><?php echo "$this->insert_rate cache requests/second"; ?></td></tr>
				<tr class=tr-1><td class=td-0>Cache full count</td><td><?php echo "{$this->cache['expunges']}"; ?></td></tr>
			</tbody>
		</table>
	</div>
	<div class="info div1">
		<h2>User Cache Information</h2>
		<table cellspacing=0>
			<tbody>
				<tr class=tr-0><td class=td-0>Cached Variables</td><td><?php echo "$this->number_vars ($this->size_vars)"; ?></td></tr>
				<tr class=tr-1><td class=td-0>Hits</td><td><?php echo "{$this->cache_user['num_hits']}"; ?></td></tr>
				<tr class=tr-0><td class=td-0>Misses</td><td><?php echo "{$this->cache_user['num_misses']}"; ?></td></tr>
				<tr class=tr-1><td class=td-0>Request Rate (hits, misses)</td><td><?php echo "$this->req_rate_user cache requests/second"; ?></td></tr>
				<tr class=tr-0><td class=td-0>Hit Rate</td><td><?php echo "$this->hit_rate_user cache requests/second"; ?></td></tr>
				<tr class=tr-1><td class=td-0>Miss Rate</td><td><?php echo "$this->miss_rate_user cache requests/second"; ?></td></tr>
				<tr class=tr-0><td class=td-0>Insert Rate</td><td><?php echo "$this->insert_rate_user cache requests/second"; ?></td></tr>
				<tr class=tr-1><td class=td-0>Cache full count</td><td><?php echo "{$this->cache_user['expunges']}"; ?></td></tr>
			</tbody>
		</table>
	</div>
	<div class="info div2">
		<h2>Runtime Settings</h2>
		<table cellspacing=0>
			<tbody>
<?php
	$j = 0;
	foreach (ini_get_all('apc') as $k => $v)
	{
		echo "<tr class=tr-$j><td class=td-0>",$k,"</td><td>",str_replace(',',',<br />',$v['local_value']),"</td></tr>\n";
		$j = 1 - $j;
	}

	if($this->mem['num_seg'] > 1 || $this->mem['num_seg'] == 1 && count($this->mem['block_lists'][0]) > 1)
	{
		$mem_note = "Memory Usage<br /><font size=-2>(multiple slices indicate fragments)</font>";
	}
	else
	{
		$mem_note = "Memory Usage";
	}
?>
			</tbody>
		</table>
	</div>
</div>
<div class="rightcol">
	<div class="graph div3">
		<h2>Host Status Diagrams</h2>
		<table cellspacing=0>
			<tbody>
<?php $size='width='.(GRAPH_SIZE+50).' height='.(GRAPH_SIZE+10); ?>
				<tr>
					<td class=td-0><?php echo $mem_note; ?></td>
					<td class=td-1>Hits &amp; Misses</td>
				</tr>
<?php if ($this->graphics_avail) { ?>
				<tr>
					<td class=td-0><img alt="" <?php echo "$size src=\"/administrator/index.php?option={$this->option}&task=mkimage&IMG=1&time=$time\""; ?>></td>
					<td class=td-1><img alt="" <?php echo "$size src=\"/administrator/index.php?option={$this->option}&task=mkimage&IMG=2&time=$time\""; ?>></td>
				</tr>
<?php } else { ?>
				<tr>
					<td class=td-0><span class="green box">&nbsp;</span>
						<?php echo "Free: $this->bmem_avail " . sprintf(" (%.1f%%)", $this->mem_avail*100/$this->mem_size); ?>
					</td>
					<td class=td-1><span class="green box">&nbsp;</span>
						<?php echo "Hits: {$this->cache['num_hits']} " . sprintf(" (%.1f%%)", $this->cache['num_hits']*100/($this->cache['num_hits']+$this->cache['num_misses'])); ?>
					</td>
				</tr>
				<tr>
					<td class=td-0><span class="red box">&nbsp;</span>
						<?php echo "Used: $this->bmem_used " . sprintf(" (%.1f%%)", $this->mem_used*100/$this->mem_size); ?>
					</td>
					<td class=td-1><span class="red box">&nbsp;</span>
						<?php echo "Misses: {$this->cache['num_misses']} " . sprintf(" (%.1f%%)",$this->cache['num_misses']*100/($this->cache['num_hits']+$this->cache['num_misses'])); ?>
					</td>
				</tr>
<?php } ?>
			</tbody>
		</table>
		<br/>
		<h2>Detailed Memory Usage and Fragmentation</h2>
			<table cellspacing=0>
				<tbody>
					<tr>
						<td class=td-0 colspan=2><br/>
<?php
	// Fragementation: (freeseg - 1) / total_seg
	$nseg = $freeseg = $fragsize = $freetotal = 0;
	for($i = 0; $i < $this->mem['num_seg']; $i++) {
		$ptr = 0;
		foreach($this->mem['block_lists'][$i] as $block)
		{
			if ($block['offset'] != $ptr)
			{
				++$nseg;
			}
			$ptr = $block['offset'] + $block['size'];
			// Only consider blocks <5M for the fragmentation %
			if($block['size'] < (5*1024*1024)) $fragsize+=$block['size'];
			$freetotal+=$block['size'];
		}
		$freeseg += count($this->mem['block_lists'][$i]);
	}

	if ($freeseg > 1)
	{
		$frag = sprintf("%.2f%% (%s out of %s in %d fragments)", ($fragsize/$freetotal)*100,ApcHTML::bsize($fragsize),ApcHTML::bsize($freetotal),$freeseg);
	}
	else
	{
		$frag = "0%";
	}

	if ($this->graphics_avail)
	{
		$size='width='.(2*GRAPH_SIZE+150).' height='.(GRAPH_SIZE+10);
		echo "<img alt=\"\" $size src=\"/administrator/index.php?option={$this->option}&task=mkimage&IMG=3&time=$time\">";
	}
	echo "</br>Fragmentation: $frag";
	echo "</td>";
	echo "</tr>";
	if(isset($this->mem['adist']))
	{
		foreach($this->mem['adist'] as $i=>$v)
		{
			$cur = pow(2,$i); $nxt = pow(2,$i+1)-1;
			if($i==0) $range = "1";
			else $range = "$cur - $nxt";
			echo "<tr><th align=right>$range</th><td align=right>$v</td></tr>\n";
		}
	}
?>
			</tbody>
		</table>
	</div>
</div>
</div>