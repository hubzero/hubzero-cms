<?php
/**
 * @package     hubzero.cms.admin
 * @subpackage  com_dataviewer
 *
 * @author      Sudheera R. Fernando sudheera@xconsole.org
 * @copyright   Copyright 2010-2015 HUBzero Foundation, LLC.
 * @license     http://opensource.org/licenses/MIT MIT or later; see LICENSE.txt
 */

defined('_HZEXEC_') or die();

function dv_config()
{
	global $com_name, $conf;
	$base = $conf['dir_base'];

	$document = App::get('document');
	$document->addScript(DB_PATH . DS . 'html' . DS . 'ace/ace.js');

	$db_id = Request::getString('db', false);
	$db_conf_file = $base . DS . $db_id . DS . 'database.json';
	$db_conf = json_decode(file_get_contents($db_conf_file), true);


	Toolbar::title('Dataviewer configuration editor for "' . $db_conf['name'] . '" database' , 'databases');
	Toolbar::custom('back', 'back', 'back', 'Go back', false );


	if (isset($_SESSION['dataviewer']['conf_file_updated'])) {
		print "<p class=\"message\">Configuration file updated.</p>";
		unset($_SESSION['dataviewer']['conf_file_updated']);
	}

	$dv_conf_text = '';
	$dv_conf_file = $base . DS . $db_id . DS . 'applications/dataviewer/config.json';
	if (file_exists($dv_conf_file)) {
		$dv_conf_text = file_get_contents($dv_conf_file);
	}
?>
<style>
	#db-conf-editor {
		margin: 0;
	}

	div.single {
		display: inline-block;
		margin-right: 30px;
		width: 43%;
		min-width: 300px;
	}
</style>
<script>
	var com_name = '<?php echo $com_name; ?>';
	var db = '<?php echo $db_id; ?>';
	var db_rid = '<?php echo DB_RID; ?>';
	var db_back_link = "/administrator/index.php?option=com_<?php echo $conf['com_name']; ?>";
</script>

<div style="font-size: 1.5em;">Editing configuration file : <strong><?php echo $dv_conf_file; ?></strong></div>
<br />


<div id="db-conf-editor" style="height: 400px; width: 900px; display: inline-block;"><?php echo $dv_conf_text; ?></div>

<div style="height: 400px; width: 400px; display: inline-block; vertical-align: top; margin-left: 30px; border: 1px solid #DDD; background: #FAFAFA; padding: 5px;">

<pre style="font-size: 0.8em; line-height: 1em; white-space: pre">
<strong>Set ACL: specify users and groups.</strong>

e.g. both user and group lists
{
    "acl": {
    	"allowed_users": ["user1", "user2"],
    	"allowed_groups": ["group1", "group2"]
    }
}

e.g. one list (users) only
{
    "acl": {
        "allowed_users": ["user1", "user2"],
        "allowed_groups": false
    }
}

e.g. make the data views publicly accessible
{
    "acl": {
        "allowed_users": false,
        "allowed_groups": false
    }
}

<strong>You can also set multiple config settings.</strong>
{
    "settings": {
        "limit": 50
    },
    "null_desc": "No data available for this field",
    "acl": {
        "allowed_users": false,
        "allowed_groups": ["test"]
    }
}

</pre>

</div>

<br />
<input id="db-config-update" type="button" value="Update" style="color: red; font-weight: bold; font-size: 1.2em;" />
<input id="db-config-view" data-link="/administrator/index.php?option=com_<?php echo $conf['com_name']; ?>&db=<?php echo $db_id; ?>&task=config_current" type="button" value="View Combined Config" style="color: blue; font-weight: bold; font-size: 1.2em;" />

<form id="db-conf-update-form" method="post" action="/administrator/index.php?option=com_<?php echo $conf['com_name']; ?>&task=config_update">
	<input name="<?php echo DB_RID; ?>" type="hidden" value="<?php echo DB_RID; ?>" />
	<input name="db" type="hidden" value="<?php echo $db_id; ?>" />
	<input name="update" type="hidden" value="true" />
	<input name="conf_text" type="hidden" value="" />
</form>

<div id="dv-view-conf" style="display: none;">
	<div id="db-conf-viewer" style="height: 100%; width: 100%;"></div>
</div>
<?php
}
?>
