<?php
/**
* $Id: advlink.php 26 2009-05-25 10:21:53Z happynoodleboy $
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

$version = "1.5.7.4";

require_once( dirname( __FILE__ ) .DS. 'classes' .DS. 'advlink.php' );

$advlink =& AdvLink::getInstance();
// Process Requests
$advlink->processXHR( true );
// Load Plugin Parameters
$params	= $advlink->getPluginParams();

$advlink->_debug = false;
$version .= $advlink->_debug ? ' - debug' : '';
?>
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="<?php echo $advlink->getLanguageTag();?>" lang="<?php echo $advlink->getLanguageTag();?>" dir="<?php echo $advlink->getLanguageDir();?>" >
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<title><?php echo JText::_('PLUGIN TITLE');?> : <?php echo $version;?></title>
<?php
$advlink->printScripts();
$advlink->printCss();
?>
	<script type="text/javascript">
		function initAdvLink(){
			return new AdvLink({
				lang: '<?php echo $advlink->getLanguage(); ?>',
				alerts: <?php echo $advlink->getAlerts();?>,
				params: {
					'defaults': {
						'targetlist': "<?php echo $params->get('advlink_target', 'default');?>"
					}
				}
			});
		}
	</script>
    <?php echo $advlink->printHead();?>
</head>
<body lang="<?php echo $advlink->getLanguage();?>" id="advlink">
	<form onSubmit="insertAction();return false;" action="#">
	<div class="tabs">
		<ul>
			<li id="general_tab" class="current"><span><a href="javascript:mcTabs.displayTab('general_tab','general_panel');" onMouseDown="return false;"><?php echo JText::_('LINK');?></a></span></li>
			<li id="advanced_tab"><span><a href="javascript:mcTabs.displayTab('advanced_tab','advanced_panel');" onMouseDown="return false;"><?php echo JText::_('Advanced');?></a></span></li>
		</ul>
	</div>
	<div class="panel_wrapper">
		<div id="general_panel" class="panel current">
			<fieldset>
				<legend><?php echo JText::_('Link');?></legend>
				<table border="0" cellpadding="4" cellspacing="0">
					<tr>
						<td nowrap="nowrap"><label for="href" class="hastip" title="<?php echo JText::_('URL DESC');?>"><?php echo JText::_('URL');?></label></td>
						<td><input id="href" type="text" value="" size="150" class="required" /></td>
                        <td id="hrefbrowsercontainer">&nbsp;</td>
                        <td><a class="email" href="javascript:AdvLinkDialog.createEmail();"><span title="<?php echo JText::_('Email');?>"/></a></td>
					</tr>
                    <tr>
                        <td colspan="4"><fieldset><legend><?php echo JText::_('Link Browser');?></legend><div id="link-options" class="tree"><?php echo $advlink->getLists();?></div></fieldset></td>
                    </tr>
               </table>
               </fieldset>
               <fieldset>
				<legend><?php echo JText::_('Attributes');?></legend>
               <table>
					<tr>
						<td><label for="anchorlist" class="hastip" title="<?php echo JText::_('ANCHORS DESC');?>"><?php echo JText::_('ANCHORS');?></label></td>
						<td colspan="3" id="anchorlistcontainer">&nbsp;</td>
					</tr>
					<tr>
						<td><label for="targetlist" class="hastip" title="<?php echo JText::_('TARGET DESC');?>"><?php echo JText::_('TARGET');?></label></td>
						<td colspan="3">
                        	<select id="targetlist">
								<option value=""><?php echo JText::_('NOT SET');?></option>
								<option value="_self"><?php echo JText::_('TARGET SELF');?></option>
								<option value="_blank"><?php echo JText::_('TARGET BLANK');?></option>
								<option value="_parent"><?php echo JText::_('TARGET PARENT');?></option>
								<option value="_top"><?php echo JText::_('TARGET TOP');?></option>								
							</select>
						</td>
					</tr>
					<tr>
						<td nowrap="nowrap"><label for="title" class="hastip" title="<?php echo JText::_('TITLE DESC');?>"><?php echo JText::_('TITLE');?></label></td>
						<td colspan="3"><input id="title" type="text" value="" /></td>
					</tr>
				</table>
				</fieldset>
			</div>
			<div id="advanced_panel" class="panel">
			<fieldset>
					<legend><?php echo JText::_('ADVANCED TAB');?></legend>
					<table border="0" cellpadding="0" cellspacing="4" class="properties">
						<tr>
							<td><label for="id" class="hastip" title="<?php echo JText::_('ID DESC');?>"><?php echo JText::_('ID');?></label></td>
							<td><input id="id" type="text" value="" /></td> 
						</tr>
						<tr>
							<td><label for="style" class="hastip" title="<?php echo JText::_('STYLE DESC');?>"><?php echo JText::_('STYLE');?></label></td>
							<td><input type="text" id="style" value="" /></td>
						</tr>
                        <tr>
                            <td><label for="classlist" class="hastip" title="<?php echo JText::_('CLASS LIST DESC');?>"><?php echo JText::_('CLASS LIST');?></label></td>
                            <td colspan="3">
                                <select id="classlist" onChange="AdvLinkDialog.setClasses(this.value);">
                                    <option value=""><?php echo JText::_('NOT SET');?></option>
                                </select>
                           </td>
                        </tr>
						<tr>
							<td><label for="classes" class="hastip" title="<?php echo JText::_('CLASSES DESC');?>"><?php echo JText::_('CLASSES');?></label></td>
							<td><input type="text" id="classes" value="" /></td>
						</tr>
						<tr>
							<td><label for="dir" class="hastip" title="<?php echo JText::_('LANGUAGE DIRECTION DESC');?>"><?php echo JText::_('LANGUAGE DIRECTION');?></label></td>
							<td>
                            	<select id="dir"> 
									<option value=""><?php echo JText::_('NOT SET');?></option>
									<option value="ltr"><?php echo JText::_('LTR');?></option>
									<option value="rtl"><?php echo JText::_('RTL');?></option>
								</select>
							</td> 
						</tr>
						<tr>
							<td><label for="hreflang" class="hastip" title="<?php echo JText::_('TARGET LANGUAGE CODE DESC');?>"><?php echo JText::_('TARGET LANGUAGE CODE');?></label></td>
							<td><input type="text" id="hreflang" value="" /></td>
						</tr>
						<tr>
							<td><label for="lang" class="hastip" title="<?php echo JText::_('LANGUAGE CODE DESC');?>"><?php echo JText::_('LANGUAGE CODE');?></label></td>
							<td><input id="lang" type="text" value="" /></td> 
						</tr>
						<tr>
							<td><label for="charset" class="hastip" title="<?php echo JText::_('CHARSET DESC');?>"><?php echo JText::_('CHARSET');?></label></td>
							<td><input type="text" id="charset" value="" /></td>
						</tr>
						<tr>
							<td><label for="type" class="hastip" title="<?php echo JText::_('MIMETYPE DESC');?>"><?php echo JText::_('MIMETYPE');?></label></td>
							<td><input type="text" id="type" value="" /></td>
						</tr>
						<tr>
							<td><label for="rel" class="hastip" title="<?php echo JText::_('REL DESC');?>"><?php echo JText::_('REL');?></label></td>
							<td><select id="rel" class="mceEditableSelect"> 
									<option value=""><?php echo JText::_('NOT SET');?></option>
									<option value="lightbox">Lightbox</option>
                                    <option value="lytebox">Lytebox</option>
                                    <option value="lyteframe">Lyteframe</option>
                                    <option value="nofollow">No Follow</option>
                                    <option value="alternate">Alternate</option> 
									<option value="designates">Designates</option> 
									<option value="stylesheet">Stylesheet</option> 
									<option value="start">Start</option> 
									<option value="next">Next</option> 
									<option value="prev">Prev</option> 
									<option value="contents">Contents</option> 
									<option value="index">Index</option> 
									<option value="glossary">Glossary</option> 
									<option value="copyright">Copyright</option> 
									<option value="chapter">Chapter</option> 
									<option value="subsection">Subsection</option> 
									<option value="appendix">Appendix</option> 
									<option value="help">Help</option> 
									<option value="bookmark">Bookmark</option> 
								</select> 
							</td>
						</tr>
						<tr>
							<td><label for="rev" class="hastip" title="<?php echo JText::_('REV DESC');?>"><?php echo JText::_('REV');?></label></td>
							<td><select id="rev"> 
									<option value=""><?php echo JText::_('NOT SET');?></option>
									<option value="alternate">Alternate</option> 
									<option value="designates">Designates</option> 
									<option value="stylesheet">Stylesheet</option> 
									<option value="start">Start</option> 
									<option value="next">Next</option> 
									<option value="prev">Prev</option> 
									<option value="contents">Contents</option> 
									<option value="index">Index</option> 
									<option value="glossary">Glossary</option> 
									<option value="copyright">Copyright</option> 
									<option value="chapter">Chapter</option> 
									<option value="subsection">Subsection</option> 
									<option value="appendix">Appendix</option> 
									<option value="help">Help</option> 
									<option value="bookmark">Bookmark</option> 
								</select> 
							</td>
						</tr>
						<tr>
							<td><label for="tabindex" class="hastip" title="<?php echo JText::_('TABINDEX DESC');?>"><?php echo JText::_('TABINDEX');?></label></td>
							<td><input type="text" id="tabindex" value="" /></td>
						</tr>
						<tr>
							<td><label for="accesskey" class="hastip" title="<?php echo JText::_('ACCESSKEY DESC');?>"><?php echo JText::_('ACCESSKEY');?></label></td>
							<td><input type="text" id="accesskey" value="" /></td>
						</tr>
					</table>
				</fieldset>
			</div>
		</div>
		<div class="mceActionPanel">
			<div style="float: left">
				<input type="button" id="insert" name="insert" value="<?php echo JText::_('Insert');?>" onClick="AdvLinkDialog.insert();" />
			</div>
			<div style="float: right">
				<input type="button" class="button" id="help" name="help" value="<?php echo JText::_('Help');?>" onClick="AdvLinkDialog.openHelp();" />
				<input type="button" id="cancel" name="cancel" value="<?php echo JText::_('Cancel');?>" onClick="tinyMCEPopup.close();" />
			</div>
		</div>
    </form>
</body>
</html>
