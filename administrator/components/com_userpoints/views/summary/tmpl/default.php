<?php
// No direct access
defined('_JEXEC') or die( 'Restricted access' );

JToolBarHelper::title( JText::_( 'Manage Points' ), 'addedit.png' );
JToolBarHelper::preferences('com_userpoints', '550');

?>
<div id="submenu-box">
	<div class="t">
		<div class="t">
			<div class="t"></div>
	 	</div>
 	</div>
	<div class="m">
		<ul id="submenu">
			<li><a href="index.php?option=<?php echo $this->option; ?>" class="active">Summary</a></li> 
			<li><a href="index.php?option=<?php echo $this->option; ?>&amp;task=edit">Look up User Balance</a></li>
			<li><a href="index.php?option=<?php echo $this->option; ?>&amp;task=config">Configuration</a></li> 
			<li><a href="index.php?option=<?php echo $this->option; ?>&amp;task=batch">Batch Transaction</a></li>
		</ul>
		<div class="clr"></div>
	</div>
	<div class="b">
		<div class="b">
			<div class="b"></div>
		</div>
	</div>
</div>

<div id="element-box">
	<div class="t">
		<div class="t">
			<div class="t"></div>
		</div>
	</div>
	<div class="m">

		<h3>Top Earners</h3>
<?php if ($this->rows) { ?>
		<table class="adminlist" summary="A list of Top 15 earners and their points">
			<thead>
				<tr>
					<th>Name</th>
					<th>UID</th>
					<th>Lifetime Earnings</th>
					<th>Current Balance</th>
					<th>Transaction History</th>
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
					<th><?php echo $name; ?></th>
					<th><?php echo $row->uid; ?></th>
					<th><?php echo $row->earnings; ?></th>
					<th><?php echo $row->balance; ?></th>
					<th><a href="index.php?option=<?php echo $this->option; ?>&amp;task=edit&amp;uid=<?php echo $row->uid; ?>">view</a></th>
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

		<h3>Economy Activity Stats as of <?php echo JHTML::_('date', date( "Y-m-d H:i:s" ), '%d %b, %Y'); ?></h3>
<?php if (count($this->stats) > 0) { ?>
		<table class="adminlist" summary="Summary of user point activity">
			<thead>
				<tr>
					<th rowspan="2">Activity</th>
					<th colspan="3" style="background-color:#d0d0d0;">All time</th>
					<th colspan="2" style="background-color:#e5d2c4;">Current month</th>
					<th colspan="2">Previous month</th>
				</tr>
				<tr style="font-size:x-small">
					<th style="background-color:#dcdddc;">Points</th>
					<th style="background-color:#dcdddc;">Transactions</th>
					<th style="background-color:#dcdddc;">Avg Pnt/Trans</th>
					<th style="background-color:#f2ede9;">Points</th>
					<th style="background-color:#f2ede9;">Transactions</th>
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
					<th<?php echo $class; ?>><?php echo $stat['memo']; ?></th>
					<th<?php echo $class; ?>><?php echo $stat['alltimepts']; ?></th>
					<th><?php echo $stat['alltimetran']; ?></th>			  
					<th><?php echo isset($stat['avg']) ? $stat['avg'] : ''; ?></th>
					<th><?php echo isset($stat['thismonthpts']) ? $stat['thismonthpts'] : '' ; ?></th>
					<th><?php echo isset($stat['thismonthtran']) ? $stat['thismonthtran'] : '' ; ?></th>
					<th><?php echo $stat['lastmonthpts']; ?></th>
					<th><?php echo $stat['lastmonthtran']; ?></th>
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

		<div class="clr"></div>
	</div>
	<div class="b">
		<div class="b">
			<div class="b"></div>
		</div>
	</div>
</div>