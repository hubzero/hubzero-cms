<?php
// No direct access
defined('_JEXEC') or die( 'Restricted access' );

JToolBarHelper::title( JText::_( 'Manage Points' ), 'addedit.png' );
JToolBarHelper::save( 'saveconfig', 'Save Configuration' );
JToolBarHelper::cancel();

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

		<form action="index.php" method="post" name="adminForm">
		<table class="adminform">
		 <thead>
		  <tr>
		   <th>#</th>
		   <th>Points</th>
		   <th>Alias</th>
		   <th>Description</th>
		  </tr>
		 </thead>
		 <tbody>
<?php
		$rows = 50;
		$i = 1;
		for ( $r = 0; $r < $rows; $r++ ) {
?>
		  <tr>
		   <td>(<?php echo $i; ?>)</td>
		   <td><input type="text" name="points[<?php echo $i; ?>]" value="<?php echo @$this->params[$i-1]->points; ?>" size="10" maxlength="10" /></td>
		   <td><input type="text" name="alias[<?php echo $i; ?>]" value="<?php echo htmlspecialchars( @$this->params[$i-1]->alias ); ?>" size="20" maxlength="50" /></td>
		   <td><input type="text" name="description[<?php echo $i; ?>]" value="<?php echo htmlspecialchars( @$this->params[$i-1]->description ); ?>" size="50" maxlength="255" /></td>
<?php
				$i++;
?>
		  </tr>
<?php } ?>
		 </tbody>
		</table>
		<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
		<input type="hidden" name="task" value="" />
			<?php echo JHTML::_( 'form.token' ); ?>
		</form>

		<div class="clr"></div>
	</div>
	<div class="b">
		<div class="b">
			<div class="b"></div>
		</div>
	</div>
</div>