<?php
/**
 * @package     hubzero.cms.admin
 * @subpackage  com_dataviewer
 *
 * @author      Sudheera R. Fernando sudheera@xconsole.org
 * @copyright   Copyright 2005-2011,2013 Purdue University. All rights reserved.
 * @license     http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

function dv_data_definition()
{
	global $com_name, $conf;
	$base = $conf['dir_base'];

	$document =  JFactory::getDocument();
	$document->addScript(DB_PATH . DS . 'html' . DS . 'ace/ace.js');

	$db_id = JRequest::getString('db', false);
	$db_conf_file = $base . DS . $db_id . DS . 'database.json';
	$db_conf = json_decode(file_get_contents($db_conf_file), true);

	$dd_name = JRequest::getString('dd', false);


	$full_screen = JRequest::getString('tmpl', false);


	$dd_file = "$base/$db_id/applications/$com_name/datadefinitions/$dd_name.json";
	$dd_json = file_get_contents($dd_file);
	$dd = json_decode($dd_json, true);


	JToolBarHelper::title($db_conf['name'] . ' >> <small>' . $dd['title'] . '</small>', 'databases');
	JToolBarHelper::custom('back', 'back', 'back', 'Go back', false );


	$dd_file_php = "$base/$db_id/applications/$com_name/datadefinitions-php/$dd_name.php";
	$dd_php = file_get_contents($dd_file_php);

	$dv_link = '/' . $com_name . "/view/$db_id:db/$dd_name/";
?>

	<style>
		#db-dd-editor {
			margin: 0;
		}
	</style>

	<script>
		var com_name = '<?php echo $com_name; ?>';
		var db_rid = '<?php echo DB_RID; ?>';
		var db_back_link = '/administrator/index.php?option=com_<?php echo $com_name; ?>&task=dataview_list&db=<?php echo $db_id; ?>';
	</script>

	<div id="tabs">
		<ul>
			<li><a href="#tabs-1">Dataview</a></li>
			<li><a href="#tabs-2">Editor</a></li>
			<li><a href="#tabs-3">[JSON Output]</a></li>
		</ul>

		<div id="tabs-1">
			<iframe seamless src="<?php echo $dv_link . '?tmpl=component'; ?>" id="db-tables-dv-iframe" style="width: 100%; min-width: 1000px;" height="650px;" marginWidth="0" marginHeight="0" frameBorder="0" scrolling="auto">IFRAMES not supported by the browser</iframe>
		</div>

		<div id="tabs-2">
			<textarea id="db-dd-source-php" style="display: none;"><?php echo $dd_php; ?></textarea>
			<div id="db-dd-editor-php" style="height: <?php echo $full_screen ? 600 : 500; ?>px; width: 100%;"></div>
			<input id="db-dd-update" type="button" value="Update Dataview" style="color: blue; position: absolute; top: 60px; right: 60px;" />
			<br />
		</div>

		<div id="tabs-3">
			<div id="db-dd-editor" style="height: <?php echo $full_screen ? 600 : 500; ?>px; width: 100%;"><?php echo htmlspecialchars($dd_json); ?></div>
		</div>


		<div style="position: absolute; top: 5px; right: 10px;">
<?php
		$host = str_replace($_SERVER['SCRIPT_URL'], '', $_SERVER['SCRIPT_URI']);
		if ($full_screen) {
			$close = $host . "/administrator/index.php?option=com_$com_name&task=data_definition&db=$db_id&dd=$dd_name";
?>
			[<a href="<?php echo $host . '/administrator/index.php?option=com_' . $com_name . '&task=dataview_list&db=' . $db_id; ?>" title="Go back to Dataview list">
				&nbsp;<span class="ui-icon ui-icon-arrowthick-1-w" style="display: inline-block; margin-bottom: -4px;"></span>&nbsp;
			</a>] &nbsp;
			[<a href="<?php echo $close; ?>" title="Leave Full Screen mode">
				&nbsp;<span class="ui-icon ui-icon-close" style="display: inline-block; margin-bottom: -4px;"></span>&nbsp;
			</a>]
<?php
		} else {
			$fs = $host . "/administrator/index.php?option=com_$com_name&task=data_definition&db=$db_id&dd=$dd_name&tmpl=component";
?>
			[<a href="<?php echo $fs; ?>" title="Switch to Full Screen mode">
				&nbsp;<span class="ui-icon ui-icon-arrow-4-diag" style="display: inline-block; margin-bottom: -4px;"></span>&nbsp;
			</a>]
<?php
		}
?>
		</div>
	</div>

	<form id="db-dd-update-form" method="post" action="/administrator/index.php?option=com_<?php echo $com_name; ?>&task=data_definition_update">
		<input name="<?php echo DB_RID; ?>" type="hidden" value="<?php echo DB_RID; ?>" />
		<input name="db" type="hidden" value="<?php echo $db_id; ?>" />
		<input name="dd" type="hidden" value="<?php echo $dd_name; ?>" />
		<input name="update" type="hidden" value="true" />
		<input name="dd_text" type="hidden" value="" />
	</form>
<?php
}
?>
