<?php

/**
 * Data definition editor task for the admin Dataviewer component.
 *
 * Shows the data definition with tabs for preview, PHP editor,
 * and JSON output.
 *
 * @package    hubzero-cms
 * @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Dataviewer\Admin\Tasks;

use Components\Dataviewer\Admin\DvConfig;

class DataDefinition
{
    public static function execute()
    {
        $base = DvConfig::$conf['dir_base'];

        $document = \Hubzero\Facades\App::get('document');
        $document->addScript(DB_PATH . DS . 'html' . DS . 'ace/ace.js');

        $dbId = \Hubzero\Facades\Request::getString('db', false);
        $dbConfFile = $base . DS . $dbId . DS . 'database.json';
        $dbConf = json_decode(file_get_contents($dbConfFile), true);

        $ddName = \Hubzero\Facades\Request::getString('dd', false);
        $fullScreen = \Hubzero\Facades\Request::getString('tmpl', false);

        $ddFile = $base . '/' . $dbId . '/applications/'
            . DvConfig::$com_name . '/datadefinitions/' . $ddName . '.json';
        $ddJson = file_get_contents($ddFile);
        $dd = json_decode($ddJson, true);

        \Hubzero\Facades\Toolbar::title(
            htmlspecialchars($dbConf['name']) . ' >> <small>'
                . htmlspecialchars($dd['title']) . '</small>',
            'databases'
        );
        \Hubzero\Facades\Toolbar::custom(
            'back', 'back', 'back', 'Go back', false
        );

        $ddFilePhp = $base . '/' . $dbId . '/applications/'
            . DvConfig::$com_name . '/datadefinitions-php/' . $ddName . '.php';
        $ddPhp = file_get_contents($ddFilePhp);

        $comName = htmlspecialchars(DvConfig::$com_name, ENT_QUOTES, 'UTF-8');
        $safeDbId = htmlspecialchars($dbId, ENT_QUOTES, 'UTF-8');
        $safeDdName = htmlspecialchars($ddName, ENT_QUOTES, 'UTF-8');
        $dvLink = '/' . urlencode(DvConfig::$com_name) . '/view/'
            . urlencode($dbId) . ':db/' . urlencode($ddName) . '/';

        $backLink = '/administrator/index.php?option=com_'
            . urlencode(DvConfig::$com_name) . '&task=dataview_list&db='
            . urlencode($dbId);
        $formAction = '/administrator/index.php?option=com_'
            . urlencode(DvConfig::$com_name)
            . '&task=data_definition_update';
        ?>

<style>
    #db-dd-editor {
        margin: 0;
    }
</style>

<script>
    var com_name = <?php echo json_encode(DvConfig::$com_name); ?>;
    var db_rid = <?php echo json_encode(DB_RID); ?>;
    var db_back_link = <?php echo json_encode($backLink); ?>;
</script>

<div id="tabs">
    <ul>
        <li><a href="#tabs-1">Dataview</a></li>
        <li><a href="#tabs-2">Editor</a></li>
        <li><a href="#tabs-3">[JSON Output]</a></li>
    </ul>

    <div id="tabs-1">
        <iframe seamless
            src="<?php echo htmlspecialchars($dvLink) . '?tmpl=component'; ?>"
            id="db-tables-dv-iframe"
            style="width: 100%; min-width: 1000px;" height="650px;"
            marginWidth="0" marginHeight="0" frameBorder="0"
            scrolling="auto">
            IFRAMES not supported by the browser</iframe>
    </div>

    <div id="tabs-2">
        <textarea id="db-dd-source-php" style="display: none;"><?php
            echo htmlspecialchars($ddPhp);
        ?></textarea>
        <?php $editorHeight = $fullScreen ? 600 : 500; ?>
        <div id="db-dd-editor-php"
            style="height: <?php echo (int)$editorHeight; ?>px;
                width: 100%;"></div>
        <input id="db-dd-update" type="button" value="Update Dataview"
            style="color: blue; position: absolute;
                top: 60px; right: 60px;" />
        <br />
    </div>

    <div id="tabs-3">
        <?php $editorHeight = $fullScreen ? 600 : 500; ?>
        <div id="db-dd-editor"
            style="height: <?php echo (int)$editorHeight; ?>px;
                width: 100%;">
            <?php echo htmlspecialchars($ddJson); ?>
        </div>
    </div>

    <div style="position: absolute; top: 5px; right: 10px;">
        <?php if ($fullScreen) :
            $closeLink = '/administrator/index.php?option=com_'
                . urlencode(DvConfig::$com_name)
                . '&task=data_definition&db=' . urlencode($dbId)
                . '&dd=' . urlencode($ddName);
            ?>
            [<a href="<?php echo htmlspecialchars($backLink); ?>"
                title="Go back to Dataview list">
                &nbsp;<span class="ui-icon ui-icon-arrowthick-1-w"
                    style="display: inline-block;
                        margin-bottom: -4px;"></span>&nbsp;
            </a>] &nbsp;
            [<a href="<?php echo htmlspecialchars($closeLink); ?>"
                title="Leave Full Screen mode">
                &nbsp;<span class="ui-icon ui-icon-close"
                    style="display: inline-block;
                        margin-bottom: -4px;"></span>&nbsp;
            </a>]
        <?php else :
            $fsLink = '/administrator/index.php?option=com_'
                . urlencode(DvConfig::$com_name)
                . '&task=data_definition&db=' . urlencode($dbId)
                . '&dd=' . urlencode($ddName) . '&tmpl=component';
            ?>
            [<a href="<?php echo htmlspecialchars($fsLink); ?>"
                title="Switch to Full Screen mode">
                &nbsp;<span class="ui-icon ui-icon-arrow-4-diag"
                    style="display: inline-block;
                        margin-bottom: -4px;"></span>&nbsp;
            </a>]
        <?php endif; ?>
    </div>
</div>

<form id="db-dd-update-form" method="post"
    action="<?php echo htmlspecialchars($formAction); ?>">
    <input name="<?php echo htmlspecialchars(DB_RID); ?>" type="hidden"
        value="<?php echo htmlspecialchars(DB_RID); ?>" />
    <input name="db" type="hidden" value="<?php echo $safeDbId; ?>" />
    <input name="dd" type="hidden" value="<?php echo $safeDdName; ?>" />
    <input name="update" type="hidden" value="true" />
    <input name="dd_text" type="hidden" value="" />
</form>
        <?php
    }
}
