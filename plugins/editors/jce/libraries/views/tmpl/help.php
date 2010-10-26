<?php
/**
* $Id: help.php 26 2009-05-25 10:21:53Z happynoodleboy $
* @package      JCE
* @copyright    Copyright (C) 2005 - 2009 Ryan Demmer. All rights reserved.
* @author		Ryan Demmer
* @license      GNU/GPL
* JCE is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
*/
defined( '_JEXEC' ) or die( 'Restricted access' );

$version = "1.5.0";

require_once( JCE_LIBRARIES . DS. 'classes' .DS. 'editor.php' );
require_once( JCE_LIBRARIES . DS. 'classes' .DS. 'plugin.php' );
require_once( JCE_LIBRARIES . DS. 'classes' .DS. 'utils.php' );

$help 	=& JContentEditorPlugin::getInstance();
$plugin = $help->getPlugin('name');

$params = $help->getEditorParams();	
$help->loadLanguages();

$help->script( array( 
	'tiny_mce_popup'
), 'tiny_mce' );
$help->css( array(
	'plugin',
	'help'
) );

$url = $params->get( 'help', 'http://www.joomlacontenteditor.net/index2.php?option=com_content' ) . '&task=findkey&lang=' . $help->getLanguageTag() . '&keyref=';

?>
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="<?php echo $help->getLanguageTag();?>" lang="<?php echo $help->getLanguageTag();?>" dir="<?php echo $help->getLanguageDir();?>" >
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<title><?php echo JText::_('HELP');?></title>
<?php
$help->printScripts();
$help->printCss();	
?>
	<script type="text/javascript">
		var helpDialog = {		
			url : '<?php echo $url;?>',
			plugin : '<?php echo $plugin;?>',
			node : null,
			init : function(){
				this.loadFrame();
			},
			loadFrame : function(node){
				var d = document;
				this.node = node || this.plugin + '.about';
				d.getElementById(this.node).className = 'loading';
				d.getElementById('help-iframe').src = this.url + this.node;
			},
			frameLoaded : function(){
				var d = document;
				if(this.node && d.getElementById(this.node)){
					d.getElementById(this.node).className = '';
				}
			}
		};
    </script>
</head>
<body onLoad="helpDialog.init();">
	<fieldset>
    <legend><?php echo JText::_('HELP');?></legend>
    <table border="0" cellpadding="0" cellspacing="0">
    	<tr>
        	<td id="help-left"><div><?php echo $help->getHelpTopics();?></div></td>
            <td id="help-middle">&nbsp;</td>
            <td id="help-right"><iframe id="help-iframe" onload="helpDialog.frameLoaded();" src="javascript:;" scrolling="auto" frameborder="0"></iframe></td>
        </tr>
    </table>
    </fieldset>
    <div class="mceActionPanel">
		<div style="float: right">
			<input type="button" id="cancel" name="cancel" value="<?php echo JText::_('Cancel');?>" onClick="tinyMCEPopup.close();" />
		</div>
	</div>
</body>
</html>
