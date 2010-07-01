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
			<li><a href="index.php?option=<?php echo $this->option; ?>">Summary</a></li> 
			<li><a href="index.php?option=<?php echo $this->option; ?>&amp;task=edit">Look up User Balance</a></li>
			<li><a href="index.php?option=<?php echo $this->option; ?>&amp;task=config">Configuration</a></li> 
			<li><a href="index.php?option=<?php echo $this->option; ?>&amp;task=batch" class="active">Batch Transaction</a></li>
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

		<form action="index2.php" method="post" name="adminForm">
		<table class="adminform">
				 <thead>
				  <tr>
				   <th colspan="2">Process batch transaction</th>
				  </tr>
				 </thead>
				 <tbody>
                  <tr>
				   <td><label for="type">Transaction Type:</label></td>
				   <td><select name="type" id="type">
						<option>deposit</option>
						<option>withdraw</option>
				   </select></td>
				  </tr>
				  <tr>
				   <td><label for="amount">Amount:</label></td>
				   <td><input type="text" name="amount" id="amount"  maxlength="11" value="" /></td>
				  </tr>
				  <tr>
				   <td><label for="description">Description:</label></td>
				   <td><input type="text" name="description" id="description"  maxlength="250"style="width:100%"  value="" /></td>
				  </tr>
                    <tr>
				   <td><label for="users">User list</label></td>
				   <td><textarea name="users" id="users" rows="10" style="width:100%"></textarea>
                   <br /> Enter a comma-separated list of userids.</td>
				  </tr>
                  <thead>
				  <tr>
				   <th colspan="2">Transaction log details</th>
				  </tr>
				 </thead>
                  <tr>
				   <td><label for="com">Category / Component</label></td>
				   <td><input type="text" name="com" id="com" size="30" maxlength="250" value="" />
                   <br />E.g. answers, survey, etc.</td>
				  </tr>
                  <tr>
				   <td><label for="action">Action type</label></td>
				   <td><input type="text" name="action" id="action" size="30" maxlength="250" value="" />
                   <br /> E.g. royalty, setup, etc.</td>
				  </tr>
                  <tr>
				   <td><label for="ref">Reference id (optional)</label></td>
				   <td><input type="text" name="ref" id="ref" size="30" maxlength="250" value="" /></td>
				  </tr>
                 
				 </tbody>
		</table>
        <input type="hidden" name="option" value="<?php echo $option;?>" />
		<input type="hidden" name="task" value="process_batch" />
        <input type="submit" name="submit" value="Process batch transaction" />	
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