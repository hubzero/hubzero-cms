<?php
/**
* @version 1.3.0
* @package RSform!Pro 1.3.0
* @copyright (C) 2007-2011 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/

defined('_JEXEC') or die('Restricted access');
?>
<form action="index.php?option=com_rsform" method="post" name="adminForm">
<table class="adminform">
	<tr>
		<th>Checking for updates</th>
	</tr>
	<tr>
		<td>
			<iframe src="http://www.rsjoomla.com/index2.php?option=com_rslicense&task=checkUpdate&amp;sess=<?php echo $this->code;?>&amp;revision=<?php echo _RSFORM_REVISION;?>&amp;version=1.5" style="border:0px solid;width:100%;height:18px;" scrolling="no" frameborder="no"></iframe>
		</td>
	</tr>
	<tr>
		<td>&nbsp;</td>
	</tr>
	<tr>
		<td><iframe src="http://www.rsjoomla.com/latest.html?tmpl=component" style="border:0px solid;width:100%;height:380px;" scrolling="no" frameborder="no"></iframe></td>
	</tr>
</table>

<input type="hidden" name="task" value=""/>
<input type="hidden" name="option" value="com_rsform"/>
</form>