<style>

/* System Notice Messages - just for this page */
#system-message dt.notice { display: none; }
#system-message dd.notice { margin-left: 0px; }
#system-message dd.notice ul { padding-left: 15px; color: #0055BB; background: #C3D2E5; list-style: none; border: 1px solid #0055BB;}

</style>


<?php
 
// No direct access
 
defined('_JEXEC') or die('Restricted access'); ?>

<h2>Subscribe to newsletters</h2>

<?php

$doc =& JFactory::getDocument();

if ($doc->getBuffer('message'))
{
    echo $doc->getBuffer('message');
}
    
?>


<form method=post>

	<ul style="list-style:none;">
		<?php echo $this->listhtml; ?>
	</ul>
	
	<br/>

	<input type=hidden name="task" value="save" />
	<input type="submit" value=" Save " />
</form>

