<?php
/**
 * @package     hubzero.cms.site
 * @subpackage  com_dataviewer
 *
 * @author      Sudheera R. Fernando sudheera@xconsole.org
 * @copyright   Copyright 2005-2011,2013 Purdue University. All rights reserved.
 * @license     http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

function dv_list()
{
	global $com_name, $conf;

	JToolBarHelper::title( JText::_('Database List' ), 'databases');
	JToolBarHelper::preferences('com_' . $com_name, '500');


	$base = $conf['dir_base'];
	$arry = array();
	$list = `cd $base; ls ./*/database.json`;

	$list = explode("\n", $list);
	array_pop($list);

?>
	<table class="adminlist" summary="">
		<thead>
		 	<tr>
		 		<th width="50px">#</th>
				<th width="40%">Name</th>
				<th>Config</th>
				<th width="30%">Data Views</th>
			</tr>
		</thead>

		<tbody>
<?php

	$c = 0;
	
	foreach ($list as $item) {
		chdir($base);
		$id = explode('/', $item);
		$id = $id[1];
		$db = json_decode(file_get_contents($item), true);
	
		print '<tr>';
		print '<td >' . ++$c . '</td>';
		print '<td >' . $db['name'] . '</td>';
		print '<td><a href="/administrator/index.php?option=com_dataviewer&task=config&db=' . $id . '">Edit Config</a></td>';
		print '<td><a href="/administrator/index.php?option=com_dataviewer&task=dataview_list&db=' . $id . '" target="_blank">' . 'Dataviews' . '</a></td>';
		print '</tr>';
	}
?>
		<tbody>
	</table>
<?php
}
?>
