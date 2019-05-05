<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

Toolbar::title(Lang::txt('COM_MEMBERS') . ': ' . Lang::txt('COM_MEMBERS_MENU_POINTS'), 'user.png' );
Toolbar::preferences('com_members', '550');

$this->css();
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
					<th scope="col" colspan="3" class="all-time">All time</th>
					<th scope="col" colspan="2" class="current-month">Current month</th>
					<th scope="col" colspan="2">Previous month</th>
				</tr>
				<tr>
					<th scope="col" class="all-time-col">Points</th>
					<th scope="col" class="all-time-col">Transactions</th>
					<th scope="col" class="all-time-col">Avg Pnt/Trans</th>
					<th scope="col" class="current-month-col">Points</th>
					<th scope="col" class="current-month-col">Transactions</th>
					<th>Points</th>
					<th>Transactions</th>
				</tr>
			</thead>
			<tbody>
				<?php
				foreach ($this->stats as $stat)
				{
					if (!isset($stat['class']))
					{
						$stat['class'] = '';
					}
					?>
					<tr>
						<th scope="row" class="<?php echo $stat['class']; ?>"><?php echo $stat['memo']; ?></th>
						<td class="<?php echo $stat['class']; ?>"><?php echo $stat['alltimepts']; ?></td>
						<td><?php echo $stat['alltimetran']; ?></td>
						<td><?php echo isset($stat['avg']) ? $stat['avg'] : ''; ?></td>
						<td><?php echo isset($stat['thismonthpts']) ? $stat['thismonthpts'] : ''; ?></td>
						<td><?php echo isset($stat['thismonthtran']) ? $stat['thismonthtran'] : ''; ?></td>
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
