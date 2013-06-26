<?php
/**
 * @package		HUBzero CMS
 * @author		Sudheera R. Fernando <sudheera@xconsole.org>
 * @copyright	Copyright 2012 by Purdue Research Foundation, West Lafayette, IN 47906
 * @license		http://www.gnu.org/licenses/gpl-2.0.html GPLv2
 *
 * Copyright 2012 by Purdue Research Foundation, West Lafayette, IN 47906.
 * All rights reserved.
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License,
 * version 2 as published by the Free Software Foundation.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
 */

// No direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

error_reporting(E_ALL);
ini_set('display_errors', TRUE);
ini_set('display_startup_errors', TRUE);

// ACL
$jacl =& JFactory::getACL();
$jacl->addACL($option, 'manage', 'users', 'super administrator');
$jacl->addACL($option, 'manage', 'users', 'administrator');

$user = & JFactory::getUser();
if (!$user->authorize($option, 'manage')) {
	$mainframe->redirect('index.php', JText::_('ALERTNOTAUTH'));
}

mb_internal_encoding('UTF-8');

jimport('joomla.application.component.helper');

JToolBarHelper::title( JText::_( 'Dataview List' ), 'generic.png' );
JToolBarHelper::preferences('com_prj_dv', '400');


global $com_name, $html_path;
$document = &JFactory::getDocument();

$html_path = str_replace(JPATH_BASE, '/administrator', JPATH_COMPONENT) . '/html';
$com_name = str_replace(JPATH_BASE.'/components/', '', JPATH_COMPONENT);
$com_name = str_replace('com_', '' , $com_name);

$document->addScript($html_path . '/util.js');
$document->addScript($html_path . '/jquery.min.js');
$document->addScript($html_path . '/jquery.dv.js');
$document->addScript($html_path . '/ui/jquery-ui.min.js');
$document->addStyleSheet($html_path . '/ui/themes/smoothness/jquery-ui.css');
$document->addScript($html_path . '/dv.js');


$task = JRequest::getString('task', false, 'POST');
if ($task === 'get_dd') {
	$dd_name = JRequest::getString('name', '', 'POST');
	$path = str_replace('administrator/', '', JPATH_COMPONENT);
	print json_encode(file_get_contents($path . '/data/' . $dd_name . '.php'));
	exit();
} else {
	dv_get_list();
}

function dv_get_list($path = false) {
	global $com_name, $html_path;

	if (!$path) {
		$path = str_replace('administrator/', '', JPATH_COMPONENT);
	}
	return;

	$path = $path . '/data';
	$files = scandir($path);
?>

	<script>
		var com_name = '<?=$com_name?>';
	</script>
	<table class="adminlist" summary="">
		<thead>
		 	<tr>
				<th>Title</th>
				<th>File</th>
				<th>Last Updated</th>
				<th>View</th>
				<th>Data Definition</th>
			</tr>
		</thead>

		<tbody>
<?php
	asort($files);
	foreach ($files as $file) {
		if (substr($file, -4) === '.php') {
			require_once($path . '/' . $file);
			$last_mod = date("Y-m-d H:i:s", filemtime($path . '/' . $file));
			$get_func = 'get_' . substr($file, 0, -4);
			$dd = $get_func();

			print '<tr>';
			print '<td >' . $dd['title'] . '</td>';
			print '<td>' . $file . '</td>';
			print '<td>' . $last_mod . '</td>';
			print '<td><a target="_blank" href="/' . $com_name . '/spreadsheet/' . substr($file, 0, -4) . '/">View</a></td>';
			print '<td><a href="#" class="dv-admin-view-dd" data-dd="' . substr($file, 0, -4) . '">View</a></td>';
			print '</tr>';
		}
	}
	print '</pre>';
}
?>
		<tbody>
	</table>

	<div id="dv-admin-dd"></div>
