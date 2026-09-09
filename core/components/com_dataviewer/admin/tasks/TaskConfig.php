<?php

/**
 * Dataviewer configuration editor task for a database.
 *
 * @package    hubzero-cms
 * @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Dataviewer\Admin\Tasks;

use Components\Dataviewer\Admin\DvConfig;

class TaskConfig
{
    public static function execute()
    {
        $base = DvConfig::$conf['dir_base'];

        $document = \Hubzero\Facades\App::get('document');
        $document->addScript(DB_PATH . DS . 'html' . DS . 'ace/ace.js');

        $dbId = \Hubzero\Facades\Request::getString('db', false);
        $dbConfFile = $base . DS . $dbId . DS . 'database.json';
        $dbConf = json_decode(file_get_contents($dbConfFile), true);

        \Hubzero\Facades\Toolbar::title(
            'Dataviewer configuration editor for "'
                . htmlspecialchars($dbConf['name']) . '" database',
            'databases'
        );
        \Hubzero\Facades\Toolbar::custom(
            'back', 'back', 'back', 'Go back', false
        );

        if (\Hubzero\Facades\Session::get('dv.admin.conf_updated', false)) {
            print '<p class="message">'
                . \Hubzero\Facades\Lang::txt('COM_DATAVIEWER_CONFIG_UPDATED')
                . '</p>';
            \Hubzero\Facades\Session::clear('dv.admin.conf_updated');
        }

        $dvConfText = '';
        $dvConfFile = $base . DS . $dbId
            . DS . 'applications/dataviewer/config.json';
        if (file_exists($dvConfFile)) {
            $dvConfText = file_get_contents($dvConfFile);
        }

        $comName = htmlspecialchars(DvConfig::$com_name, ENT_QUOTES, 'UTF-8');
        $safeDbId = htmlspecialchars($dbId, ENT_QUOTES, 'UTF-8');
        $backLink = '/administrator/index.php?option=com_'
            . urlencode(DvConfig::$conf['com_name']);
        $configLink = '/administrator/index.php?option=com_'
            . urlencode(DvConfig::$conf['com_name'])
            . '&db=' . urlencode($dbId) . '&task=config_current';
        $formAction = '/administrator/index.php?option=com_'
            . urlencode(DvConfig::$conf['com_name'])
            . '&task=config_update';
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
    var com_name = <?php echo json_encode(DvConfig::$com_name); ?>;
    var db = <?php echo json_encode($dbId); ?>;
    var db_rid = <?php echo json_encode(DB_RID); ?>;
    var db_back_link = <?php echo json_encode($backLink); ?>;
</script>

<div style="font-size: 1.5em;">Editing configuration file :
    <strong><?php echo htmlspecialchars($dvConfFile); ?></strong></div>
<br />

<div id="db-conf-editor"
    style="height: 400px; width: 900px; display: inline-block;">
    <?php echo htmlspecialchars($dvConfText); ?>
</div>

<div style="height: 400px; width: 400px; display: inline-block;
    vertical-align: top; margin-left: 30px; border: 1px solid #DDD;
    background: #FAFAFA; padding: 5px;">

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
<input id="db-config-update" type="button" value="Update"
    style="color: red; font-weight: bold; font-size: 1.2em;" />
<input id="db-config-view"
    data-link="<?php echo htmlspecialchars($configLink); ?>"
    type="button" value="View Combined Config"
    style="color: blue; font-weight: bold; font-size: 1.2em;" />

<form id="db-conf-update-form" method="post"
    action="<?php echo htmlspecialchars($formAction); ?>">
    <input name="<?php echo htmlspecialchars(DB_RID); ?>" type="hidden"
        value="<?php echo htmlspecialchars(DB_RID); ?>" />
    <input name="db" type="hidden"
        value="<?php echo $safeDbId; ?>" />
    <input name="update" type="hidden" value="true" />
    <input name="conf_text" type="hidden" value="" />
</form>

<div id="dv-view-conf" style="display: none;">
    <div id="db-conf-viewer" style="height: 100%; width: 100%;"></div>
</div>
        <?php
    }
}
