<?php defined('_JEXEC') or die('Restricted access'); 
if ($this->user->guest) {?>


<p>You must login to see this content.</p>
<script type="text/javascript">
document.location="/login";
</script>




<?php  } else { ?>
<iframe width="60%" height="600" style="float:left;" src="/index.php?option=com_livefeed&task=justintv&tmpl=component"></iframe>
<iframe width="40%" height="600" style="float:left;" src="/index.php?option=com_livefeed&task=chat&area=NEESlive&tmpl=component"></iframe>
<?php }?>