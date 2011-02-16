<?php
/**
* @version 1.3.0
* @package RSform!Pro 1.3.0
* @copyright (C) 2007-2011 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/

defined('_JEXEC') or die('Restricted access');
?>
<div class="progressWrapper"><div class="progressBar" id="progressBar">0%</div></div>
<p><?php echo JText::sprintf('RSFP_EXPORT_START_MSG', '<strong id="exportmsg">3</strong>'); ?></p>

<input type="hidden" value="<?php echo $this->file; ?>" id="ExportFile" />
<input type="hidden" value="<?php echo $this->exportType; ?>" id="exportType" />

<script type="text/javascript">
t = setInterval(function() {
	var count = parseInt(document.getElementById('exportmsg').innerHTML);
	if (count <= 0)
		return clearTimeout(t);
	
	document.getElementById('exportmsg').innerHTML = count - 1;
}, 1000);

setTimeout(function() {
	exportProcess(0,<?php echo $this->limit; ?>,<?php echo $this->total;?>);
}, 3000);
</script>