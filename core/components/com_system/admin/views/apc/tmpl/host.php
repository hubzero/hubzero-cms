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
Toolbar::title(Lang::txt('COM_SYSTEM_APC_HOST'), 'config.png');

$this->css('apc.css');

$time = $this->time;

?>

<?php
	$this->view('_submenu')->display();
?>

<script type="text/javascript">
jQuery(document).ready(function($){
	$('#clearcache').on('click', function(e) {
		var mes = confirm('<?php echo Lang::txt('COM_SYSTEM_APC_CONFIRM'); ?>');
		if (!mes) {
			e.preventDefault();
		}
		return res;
	});
});
</script>

<div id="clearcache">
	<a class="button" href="<?php echo Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller . '&task=clrcache'); ?>">Clear <?php echo $this->cache_mode; ?> cache</a>
</div>

<form action="<?php echo Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller); ?>" method="post" name="adminForm" id="adminForm">
	<div class="col width-50 fltlft">
		<table class="adminlist">
			<thead>
				<tr>
					<th colspan="2">
						General Cache Information
					</th>
				</tr>
			</thead>
			<tbody>
				<tr class="row0">
					<th scope="row">APC Version</th>
					<td><?php echo $this->apcversion; ?></td>
				</tr>
				<tr class="row1">
					<th scope="row">PHP Version</th>
					<td><?php echo $this->phpversion; ?></td>
				</tr>
				<tr class="row0">
					<th scope="row">APC Host</th>
					<td><?php echo $_SERVER['SERVER_NAME'] . ' ' . $this->host; ?></td>
				</tr>
				<tr class="row1">
					<th scope="row">Server Software</th>
					<td><?php echo $_SERVER['SERVER_SOFTWARE']; ?></td>
				</tr>
				<tr class="row0">
					<th scope="row">Shared Memory</th>
					<td>
						<?php echo "{$this->mem['num_seg']} Segment(s) with {$this->seg_size}
							<br />
						({$this->cache['memory_type']} memory, {$this->cache['locking_type']} locking)"; ?>
					</td>
				</tr>
				<tr class="row1">
					<th scope="row">Start Time</th>
					<td><?php echo date(DATE_FORMAT, $this->cache['start_time']); ?></td>
				</tr>
				<tr class="row0">
					<th scope="row">Uptime</th>
					<td><?php echo $this->duration; ?></td>
				</tr>
				<tr class="row1">
					<th scope="row">File Upload Support</th>
					<td><?php echo $this->cache['file_upload_progress']; ?></td>
				</tr>
			</tbody>
		</table>

		<table class="adminlist">
			<thead>
				<tr>
					<th colspan="2">
						File Cache Information
					</th>
				</tr>
			</thead>
			<tbody>
				<tr class="row0"><th scope="row">Cached Files</th><td><?php echo "$this->number_files ($this->size_files)"; ?></td></tr>
				<tr class="row1"><th scope="row">Hits</th><td><?php echo "{$this->cache['num_hits']}"; ?></td></tr>
				<tr class="row0"><th scope="row">Misses</th><td><?php echo "{$this->cache['num_misses']}"; ?></td></tr>
				<tr class="row1"><th scope="row">Request Rate (hits, misses)</th><td><?php echo "$this->req_rate cache requests/second"; ?></td></tr>
				<tr class="row0"><th scope="row">Hit Rate</th><td><?php echo "$this->hit_rate cache requests/second"; ?></td></tr>
				<tr class="row1"><th scope="row">Miss Rate</th><td><?php echo "$this->miss_rate cache requests/second"; ?></td></tr>
				<tr class="row0"><th scope="row">Insert Rate</th><td><?php echo "$this->insert_rate cache requests/second"; ?></td></tr>
				<tr class="row1"><th scope="row">Cache full count</th><td><?php echo "{$this->cache['expunges']}"; ?></td></tr>
			</tbody>
		</table>

		<table class="adminlist">
			<thead>
				<tr>
					<th colspan="2">
						User Cache Information
					</th>
				</tr>
			</thead>
			<tbody>
				<tr class="row0"><th scope="row">Cached Variables</th><td><?php echo "$this->number_vars ($this->size_vars)"; ?></td></tr>
				<tr class="row1"><th scope="row">Hits</th><td><?php echo "{$this->cache_user['num_hits']}"; ?></td></tr>
				<tr class="row0"><th scope="row">Misses</th><td><?php echo "{$this->cache_user['num_misses']}"; ?></td></tr>
				<tr class="row1"><th scope="row">Request Rate (hits, misses)</th><td><?php echo "$this->req_rate_user cache requests/second"; ?></td></tr>
				<tr class="row0"><th scope="row">Hit Rate</th><td><?php echo "$this->hit_rate_user cache requests/second"; ?></td></tr>
				<tr class="row1"><th scope="row">Miss Rate</th><td><?php echo "$this->miss_rate_user cache requests/second"; ?></td></tr>
				<tr class="row0"><th scope="row">Insert Rate</th><td><?php echo "$this->insert_rate_user cache requests/second"; ?></td></tr>
				<tr class="row1"><th scope="row">Cache full count</th><td><?php echo "{$this->cache_user['expunges']}"; ?></td></tr>
			</tbody>
		</table>
	</div>
	<div class="col width-50 fltrt">
		<table class="adminlist">
			<thead>
				<tr>
					<th colspan="2">
						Runtime Settings
					</th>
				</tr>
			</thead>
			<tbody>
				<?php
					$j = 0;
					foreach (ini_get_all('apc') as $k => $v)
					{
						echo "<tr class=\"row$j\"><th>",$k,"</th><td>",str_replace(',',',<br />',$v['local_value']),"</td></tr>\n";
						$j = 1 - $j;
					}

					if ($this->mem['num_seg'] > 1 || $this->mem['num_seg'] == 1 && count($this->mem['block_lists'][0]) > 1)
					{
						$mem_note = 'Memory Usage<br /><span style="font-size: 0.85em">(multiple slices indicate fragments)</span>';
					}
					else
					{
						$mem_note = 'Memory Usage';
					}
				?>
			</tbody>
		</table>
	</div>
	<div class="clr"></div>

	<div class="col width-50 fltlft">
		<table class="adminlist">
			<thead>
				<tr>
					<th colspan="2">
						Host Status Diagrams
					</th>
				</tr>
			</thead>
			<tbody>
				<tr>
					<th scope="col"><?php echo $mem_note; ?></th>
					<th scope="col">Hits &amp; Misses</th>
				</tr>
			<?php $size = 'width=' . (GRAPH_SIZE+50) . ' height=' . (GRAPH_SIZE+10); ?>
			<?php if ($this->graphics_avail) : ?>
				<tr class="row0">
					<td><img alt="" <?php echo $size; ?> src="<?php echo Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller . '&task=mkimage&IMG=1&time=' . $time); ?>" /></td>
					<td><img alt="" <?php echo $size; ?> src="<?php echo Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller . '&task=mkimage&IMG=2&time=' . $time); ?>" /></td>
				</tr>
			<?php endif; ?>
				<tr class="row0">
					<td>
						<span class="green box">&nbsp;</span>
						<?php echo "Free: $this->bmem_avail " . sprintf(" (%.1f%%)", $this->mem_avail*100/$this->mem_size); ?>
					</td>
					<td>
						<span class="green box">&nbsp;</span>
						<?php echo "Hits: {$this->cache['num_hits']} " . sprintf(" (%.1f%%)", $this->cache['num_hits']*100/($this->cache['num_hits']+$this->cache['num_misses'])); ?>
					</td>
				</tr>
				<tr class="row1">
					<td>
						<span class="red box">&nbsp;</span>
						<?php echo "Used: $this->bmem_used " . sprintf(" (%.1f%%)", $this->mem_used*100/$this->mem_size); ?>
					</td>
					<td>
						<span class="red box">&nbsp;</span>
						<?php echo "Misses: {$this->cache['num_misses']} " . sprintf(" (%.1f%%)", $this->cache['num_misses']*100/($this->cache['num_hits']+$this->cache['num_misses'])); ?>
					</td>
				</tr>
			</tbody>
		</table>
	</div>
	<div class="col width-50 fltrt">
		<table class="adminlist">
			<thead>
				<tr>
					<th<?php if (isset($this->mem['adist'])) { echo ' colspan="2"'; } ?>>
						Detailed Memory Usage and Fragmentation
					</th>
				</tr>
			</thead>
			<tbody>
				<tr>
					<th<?php if (isset($this->mem['adist'])) { echo ' colspan="2"'; } ?>>
				<?php
					// Fragementation: (freeseg - 1) / total_seg
					$nseg = $freeseg = $fragsize = $freetotal = 0;
					for ($i = 0; $i < $this->mem['num_seg']; $i++)
					{
						$ptr = 0;
						foreach ($this->mem['block_lists'][$i] as $block)
						{
							if ($block['offset'] != $ptr)
							{
								++$nseg;
							}
							$ptr = $block['offset'] + $block['size'];
							// Only consider blocks <5M for the fragmentation %
							if ($block['size'] < (5*1024*1024)) $fragsize+=$block['size'];
							$freetotal+=$block['size'];
						}
						$freeseg += count($this->mem['block_lists'][$i]);
					}

					if ($freeseg > 1)
					{
						$frag = sprintf("%.2f%% (%s out of %s in %d fragments)", ($fragsize/$freetotal)*100, \Components\System\Helpers\Html::bsize($fragsize), \Components\System\Helpers\Html::bsize($freetotal), $freeseg);
					}
					else
					{
						$frag = "0%";
					}

					if ($this->graphics_avail)
					{
						$size='width='.(2*GRAPH_SIZE+150).' height='.(GRAPH_SIZE+10);
						echo "<img alt=\"\" $size src=\"index.php?option={$this->option}&amp;controller={$this->controller}&amp;task=mkimage&amp;IMG=3&amp;time=$time\" />";
					}
					echo "<br />Fragmentation: $frag";
					echo "</th>";
					echo "</tr>";
					if (isset($this->mem['adist']))
					{
						foreach ($this->mem['adist'] as $i=>$v)
						{
							$cur = pow(2,$i); $nxt = pow(2,$i+1)-1;
							if ($i==0) $range = "1";
							else $range = "$cur - $nxt";
							echo "<tr><th>$range</th><td>$v</td></tr>\n";
						}
					}
				?>
			</tbody>
		</table>
	</div>
	<div class="clr"></div>
</form>
