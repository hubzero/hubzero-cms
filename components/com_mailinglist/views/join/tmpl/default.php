<?php
 
// No direct access
 
defined('_JEXEC') or die('Restricted access'); ?>




<h2>Your mailing lists</h2>

<form method=post action="/mailinglist">

	<?php 
		if( $this->get('confirmationMessage', '') != '')
		{
			echo '<p class="passed">Your changes have been saved</p>';
		}
	?>


	<ul style="list-style:none;">
		<?php echo $this->listhtml; ?>
	</ul>
	
	<br/>

	<input type=hidden name="task" value="dojoin" />
	<input type="submit" value=" Update " />
</form>



</div>


