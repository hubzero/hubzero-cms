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

Toolbar::title(Lang::txt('COM_MEMBERS') . ': ' . Lang::txt('COM_MEMBERS_MENU_POINTS'), 'user.png' );
Toolbar::preferences('com_members', '550');

?>

<?php
	$this->view('_submenu')
	     ->display();
?>

<form action="<?php echo Route::url('index.php?option=' . $this->option); ?>" method="post" name="adminForm" id="adminForm">
	<?php if ($this->rows) { ?>
		<table class="adminlist">
			<caption>Top Earners</caption>
			<thead>
				<tr>
					<th scope="col">Name</th>
					<th scope="col">UID</th>
					<th scope="col">Lifetime Earnings</th>
					<th scope="col">Current Balance</th>
					<th scope="col">Transaction History</th>
				</tr>
			</thead>
			<tbody>
				<?php
				$k = 0;
				for ($i=0, $n=count($this->rows); $i < $n; $i++)
				{
					$row =& $this->rows[$i];
					$wuser = User::getInstance($row->uid);
					$name  = Lang::txt('COM_MEMBERS_UNKNOWN');
					if ($wuser->get('id'))
					{
						$name = $wuser->get('name');
					}
				?>
				<tr class="<?php echo "row$k"; ?>">
					<th scope="row"><?php echo $name; ?></th>
					<td><?php echo $row->uid; ?></td>
					<td><?php echo $row->earnings; ?></td>
					<td><?php echo $row->balance; ?></td>
					<td>
						<a class="icon-16-preview" href="<?php echo Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller . '&task=edit&uid=' . $row->uid); ?>">
							<span>view</span>
						</a>
					</td>
				</tr>
				<?php
					$k = 1 - $k;
				}
				?>
			</tbody>
		</table>
	<?php } else { ?>
		<p>No user information found.</p>
	<?php } ?>

	<?php if (count($this->stats) > 0) { ?>
		<table class="adminlist">
			<caption>Economy Activity Stats as of <?php echo Date::of(Date::toSql())->toLocal(Lang::txt('DATE_FORMAT_HZ1')); ?></caption>
			<thead>
				<tr>
					<th scope="col" rowspan="2">Activity</th>
					<th scope="col" colspan="3" style="background-color:#d0d0d0;">All time</th>
					<th scope="col" colspan="2" style="background-color:#e5d2c4;">Current month</th>
					<th scope="col" colspan="2">Previous month</th>
				</tr>
				<tr style="font-size:x-small">
					<th scope="col" style="background-color:#dcdddc;">Points</th>
					<th scope="col" style="background-color:#dcdddc;">Transactions</th>
					<th scope="col" style="background-color:#dcdddc;">Avg Pnt/Trans</th>
					<th scope="col" style="background-color:#f2ede9;">Points</th>
					<th scope="col" style="background-color:#f2ede9;">Transactions</th>
					<th>Points</th>
					<th>Transactions</th>
				</tr>
			</thead>
			<tbody>
				<?php
				foreach ($this->stats as $stat)
				{
					if (isset($stat['class']))
					{
						switch ($stat['class'])
						{
							case 'spendtotal':
								$class = ' style="color:red; background-color:#f2ede9;border-top:2px solid #ccc;"';
							break;
							case 'earntotal':
								$class = ' style="color:green; background-color:#ecf9e9;"';
							break;
							case 'royaltytotal':
								$class = ' style="color:#000000;border-top:2px solid #ccc;background-color:#efefef;"';
							break;
							default:
								$class = '';
							break;
						}
					}
					?>
					<tr>
						<th scope="row"<?php echo $class; ?>><?php echo $stat['memo']; ?></th>
						<td<?php echo $class; ?>><?php echo $stat['alltimepts']; ?></td>
						<td><?php echo $stat['alltimetran']; ?></td>
						<td><?php echo isset($stat['avg']) ? $stat['avg'] : ''; ?></td>
						<td><?php echo isset($stat['thismonthpts']) ? $stat['thismonthpts'] : '' ; ?></td>
						<td><?php echo isset($stat['thismonthtran']) ? $stat['thismonthtran'] : '' ; ?></td>
						<td><?php echo $stat['lastmonthpts']; ?></td>
						<td><?php echo $stat['lastmonthtran']; ?></td>
					</tr>
					<?php
				}
				?>
			</tbody>
		</table>
		<!--<p>Distribute <a href="<?php echo Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller . '&task=royalty&auto=0'); ?>">Royalties</a> for current month.</p>//-->
	<?php } else { ?>
		<p>No summary information found.</p>
	<?php } ?>

	<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
	<input type="hidden" name="controller" value="<?php echo $this->controller; ?>" />
	<input type="hidden" name="task" value="" />

	<?php echo Html::input('token'); ?>
</form>
