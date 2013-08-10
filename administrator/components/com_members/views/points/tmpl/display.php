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
defined('_JEXEC') or die( 'Restricted access' );

$dateFormat = '%d %b, %Y';
$tz = null;

if (version_compare(JVERSION, '1.6', 'ge'))
{
	$dateFormat = 'd M, Y';
	$tz = false;
}

JToolBarHelper::title( JText::_( 'MEMBERS' ).': <small><small>[ Manage Points ]</small></small>', 'user.png' );
JToolBarHelper::preferences('com_members', '550');

?>
<div role="navigation" class="sub-navigation">
	<ul id="subsubmenu">
		<li><a href="index.php?option=<?php echo $this->option; ?>&amp;controller=<?php echo $this->controller; ?>" class="active">Summary</a></li> 
		<li><a href="index.php?option=<?php echo $this->option; ?>&amp;controller=<?php echo $this->controller; ?>&amp;task=edit">Look up User Balance</a></li>
		<li><a href="index.php?option=<?php echo $this->option; ?>&amp;controller=<?php echo $this->controller; ?>&amp;task=config">Configuration</a></li> 
		<li><a href="index.php?option=<?php echo $this->option; ?>&amp;controller=<?php echo $this->controller; ?>&amp;task=batch">Batch Transaction</a></li>
	</ul>
</div>

<form action="index.php" method="post" name="adminForm" id="adminForm">
<?php if ($this->rows) { ?>
		<table class="adminlist" summary="A list of Top 15 earners and their points">
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
for ($i=0, $n=count( $this->rows ); $i < $n; $i++)
{
	$row = &$this->rows[$i];
	$wuser =& Hubzero_User_Profile::getInstance( $row->uid );
	if (is_object($wuser)) {
		$name = $wuser->get('name');
	} else {
		$name = JText::_('UNKNOWN');
	}
?>
				<tr class="<?php echo "row$k"; ?>">
					<th scope="row"><?php echo $name; ?></th>
					<td><?php echo $row->uid; ?></td>
					<td><?php echo $row->earnings; ?></td>
					<td><?php echo $row->balance; ?></td>
					<td>
						<a class="icon-16-preview" href="index.php?option=<?php echo $this->option; ?>&amp;controller=<?php echo $this->controller; ?>&amp;task=edit&amp;uid=<?php echo $row->uid; ?>">
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
		<table class="adminlist" summary="Summary of user point activity">
			<caption>Economy Activity Stats as of <?php echo JHTML::_('date', date( "Y-m-d H:i:s" ), $dateFormat, $tz); ?></caption>
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
	if (isset($stat['class'])) {
		switch ( $stat['class'] )
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
		<!--<p>Distribute <a href="index.php?option=<?php echo $this->option; ?>&amp;task=royalty&amp;auto=0">Royalties</a> for current month.</p>//-->
<?php } else { ?>
		<p>No summary information found.</p>
<?php } ?>

	<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
	<input type="hidden" name="controller" value="<?php echo $this->controller; ?>" />
	<input type="hidden" name="task" value="" />
	
	<?php echo JHTML::_( 'form.token' ); ?>
</form>
