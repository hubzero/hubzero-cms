<?php
/**
 * $Id: admin.xmap.html.php 108 2010-04-20 22:20:43Z guilleva $
 * $LastChangedDate: 2010-04-20 16:20:43 -0600 (mar, 20 abr 2010) $
 * $LastChangedBy: guilleva $
 * Xmap by Guillermo Vargas
 * a sitemap component for Joomla! CMS (http://www.joomla.org)
 * Author Website: http://joomla.vargas.co.cr
 * Project License: GNU/GPL http://www.gnu.org/copyleft/gpl.html
*/

defined( '_JEXEC' ) or die( 'Direct Access to this location is not allowed.' );

jimport( 'joomla.html.pane' );

/** HTML class for all Xmap administration output */
class XmapAdminHtml {

	/* Show the configuration options and menu ordering */
	function show ( &$config, &$menus, &$lists, &$pluginList, &$xmlfile ) {
		global $xmapSiteURL,$xmapComponentURL,$xmapAdministratorURL,$xmapAdministratorPath,$mainframe;
		$mainframe->addCustomHeadTag("<script type=\"text/javascript\" src=\"$xmapComponentURL/admin.js\"></script>");
		$mainframe->addCustomHeadTag("<link type=\"text/css\" rel=\"stylesheet\"  href=\"$xmapComponentURL/admin.css\" />");
		JHTML::_('behavior.tooltip');
?>
		<script type="text/javascript">
			var ajaxURL = '<?php echo preg_replace('#http.?://[^/]+/+#','/',$xmapAdministratorURL) . '/index2.php?option=com_xmap&task=ajax_request&no_html=1' ?>';
			var loadingMessage = '<?php echo str_replace("''","\\",_XMAP_MSG_LOADING_SETTINGS); ?>';
			var mosConfigLiveSite = '<?php echo $xmapSiteURL; ?>';
			var sitemapdefault = <?php echo ($config->sitemap_default? $config->sitemap_default: 0);?>;
			var editMenuOptionsMessage = '<?php echo str_replace("'","\\'",_XMAP_EDIT_MENU); ?>';
			var deleteSitemapConfirmMessage = '<?php echo str_replace("'","\\'",_XMAP_CONFIRM_DELETE_SITEMAP); ?>';
			var unistallPluginConfirmMessage = '<?php echo str_replace("'","\\'",_XMAP_CONFIRM_UNINSTALL_PLUGIN); ?>';
			var deleteMenuMessage = '<?php echo str_replace("'","\\'",_XMAP_DELETE_MENU); ?>';
			var moveDMenuMessage = '<?php echo str_replace("'","\\'",_XMAP_MOVEDOWN_MENU); ?>';
			var moveUMenuMessage = '<?php echo str_replace("'","\\'",_XMAP_MOVEUP_MENU); ?>';
			var addMessage='<?php echo str_replace("'","\\'",_XMAP_ADD); ?>';
			var cancelMessage='<?php echo str_replace("'","\\'",_XMAP_CANCEL); ?>';
			var menus = [<?php $coma=''; foreach ($menus as $menutype => $menu) { echo "$coma'$menutype'";$coma=',';} ?>];
			var joomla = '<?php echo (defined('JPATH_ADMINISTRATOR')? '1.5':'1.0'); ?>';
		</script>
<?php if ($lists['msg_success'] ) { ?>
		<table class="adminheading">
			<tr>
				<th class="menus">
					<small style="margin-left:50px;">
					<?php echo $lists['msg_success']; ?>
					</small>
				</th>
			</tr>
		</table>
<?php } ?>
		<div id="sitemapsouter">
		<?php

		$pane = &JPane::getInstance('Tabs');
	        echo $pane->startPane( 'xmap-pane' );
		/**********************************************************************************************
		 * Menu Selection Tab
		 **********************************************************************************************/
        	echo $pane->startPanel( _XMAP_TAB_SITEMAPS, 'sitemaps-tab' );
?>
		<div id="sitemapstoolbar">
		   <div class="toolbaroption"><a href="#" onClick="addSitemap();return false;"><?php echo _XMAP_ADD_SITEMAP; ?></a></div>
		</div>
		<div id="sitemaps" onclick="handleClick();">
<?php
 		$sitemaps = $config->getSitemaps();
                if (count($sitemaps)) {
					foreach ($sitemaps as $sitemap) {
						XmapAdminHtml::showSitemapInfo($sitemap,($config->sitemap_default == $sitemap->id));
					}
		} else {
			echo _XMAP_MSG_NO_SITEMAPS;
                }
        ?>
		</div>
        <?php
		echo $pane->endPanel();
		echo $pane->startPanel( 'CSS', 'css-tab' );
        ?>
		<form action="index2.php" method="post" name="adminForm" class="adminForm">
		<?php
			/**********************************************************************************************
			 * Style Editor Tab
			 **********************************************************************************************/

			$template_path = JPATH_COMPONENT_SITE.DS.'css'.DS.'xmap.css';

			if ( $fp = @fopen( $template_path, 'r' )) {
				$csscontent = JFile::read($template_path);
				$csscontent = htmlspecialchars( $csscontent );
			}
		?>
		<table cellpadding="1" cellspacing="1" border="0" width="100%">
			<tr>
				<td width="290">
					<table class="adminheading">
						<tr>
							<th class="templates" width="100%">
								<?php echo _XMAP_CSS_EDIT; ?>
							</th>
							<th style="text-align:right;">
								<button><?php echo _XMAP_TOOLBAR_SAVE; ?></button>
							</th>
						</tr>
					</table>
				</td>
			</tr>
			<tr>
				<td width="220">
					<span class="componentheading"><?php echo _XMAP_CSS; ?>:
						<?php
							echo is_writable($template_path) ?
							'<strong style="color:green;">'._XMAP_CFG_WRITEABLE.'</strong>' :
							'<strong style="color:red;">'._XMAP_CFG_UNWRITEABLE.'</strong>';
						?>
					</span>
				</td>
			</tr>
			<tr>
			<td>
				<input type="checkbox" id="exclude_css" name="exclude_css" value="1"<?php echo ($config->exclude_css ? ' checked="checked"':''); ?> />
				<label for="exclude_css"><?php echo _XMAP_MSG_EXCLUDE_CSS_SITEMAP; ?></label>
			</td>
			</tr>
			<tr>
			<td>
				<input type="checkbox" id="exclude_xsl" name="exclude_xsl" value="1"<?php echo ($config->exclude_xsl ? ' checked="checked"':''); ?> />
				<label for="exclude_xsl"><?php echo _XMAP_MSG_EXCLUDE_XSL_SITEMAP; ?></label>
			</td>
			</tr>
		</table>

		<table class="adminform">
			<tr>
			  <th><?php echo $template_path; ?></th>
			</tr>
			<tr>
			  <td>
				<textarea style="width:100%;height:500px" cols="80" rows="25" name="csscontent" class="inputbox"><?php echo $csscontent; ?></textarea>
			  </td>
			</tr>
		</table>

		<input type="hidden" name="option" value="com_xmap" />
		<input type="hidden" name="task" value="save" />
		<input type="hidden" name="boxchecked" value="0" />
		<input type="hidden" name="hidemainmenu" value="0" />
		</form>
        <?php
			echo $pane->endPanel();
			echo $pane->startPanel(_XMAP_TAB_EXTENSIONS,'ext-tab');
		?>
<?php
        /*********************************************************
		*                       Plugins section                  *
        *********************************************************/
?>
        <div id="pluginstoolbar"><?php
			// require_once($xmapAdministratorPath.'/components/com_installer/admin.installer.html.php');
			$extpane = &JPane::getInstance('sliders');
        		echo $extpane->startPane( 'xmapplugin-pane' );
			echo $extpane->startPanel( _XMAP_TAB_INSTALL_PLUGIN, 'installplugin-tab' );
			XmapAdminHtml::showInstallForm( _XMAP_INSTALL_NEW_PLUGIN ,dirname(__FILE__));
			echo $extpane->endPanel();
			echo $extpane->startPanel( _XMAP_TAB_INSTALLED_EXTENSIONS, 'plugins-tab' );
?>
		  <div id="plugins">
				<?php XmapAdminHtml::showInstalledPlugins($pluginList, 'com_xmap', $xmlfile, $lists); ?>
		  </div>
<?php
			echo $extpane->endPanel();
			echo $extpane->endPane();

        ?></div>
        <?php
			echo $pane->endPanel();
			echo $pane->endPane();
       ?>
        </div>
		<div id="divloading" style="display:none;"><?php echo _XMAP_LOADING; ?></div>
		<div id="divoptions"></div>
		<div id="divbg" style="display:none;"></div>
		<div id="optionsmenu" style="display:none;">
		   <div onclick="settingsSitemap();"><?php echo _XMAP_SETTINGS_SITEMAP; ?></div>
		   <div onclick="setAsDefault();"><?php echo _XMAP_SITEMAP_SET_DEFAULT; ?></div>
		   <div onclick="copySitemap();"><?php echo _XMAP_COPY_SITEMAP; ?></div>
		   <div onclick="deleteSitemap();"><?php echo _XMAP_DELETE_SITEMAP; ?></div>
		   <div onclick="clearCacheSitemap();"><?php echo _XMAP_CLEAR_CACHE; ?></div>
		</div>
		<script type="text/javascript">
		if (typeof addSitemap != 'function') {
			document.write('<' + 'script src="<?php echo $xmapAdministratorURL; ?>/components/com_xmap/admin.js" language=javascript><' + '/script' + '>');
			document.write('<' + 'link href="<?php echo $xmapAdministratorURL; ?>/components/com_xmap/admin.css" rel="stylesheet" type="text/css"' + ' />');
		}
		</script>
		<?php
	}

	function showSitemapInfo( &$sitemap,$default=false ) {
		global $xmapComponentURL;
?>
		<form name="sitemapform<?php echo $sitemap->id; ?>" onsubmit="return false;">
		<div id="sitemap<?php echo $sitemap->id; ?>" class="sitemap">
                   <div class="sitemaptop">
                      <div class="tl"><div class="tr"><div class="tm"><div class="smname" id="sitemapname<?php echo $sitemap->id; ?>" onClick="editTextField(this,<?php echo $sitemap->id; ?>,'name');"><?php echo $sitemap->name; ?></div><div class="divimgdefault"><?php echo '<img src="',$xmapComponentURL,'/images/',($default? 'default.gif':'no_default.gif'),'" id="imgdefault',$sitemap->id,'" />'; ?></div><div class="optionsbut" id="optionsbut<?php echo $sitemap->id; ?>" onClick="optionsMenu(<?php echo $sitemap->id; ?>);"><span><?php echo _XMAP_EDIT_MENU; ?></span></div></div></div></div>
		   </div>
                   <div class="mr"><div class="mm">
			<div class="menulistouter">
                        <div id="menulist<?php echo $sitemap->id; ?>" class="menulist"><?php
				XmapAdminHtml::printMenusList($sitemap);
                        ?></div><div class="add_menu_link" onClick="showMenusList(<?php echo $sitemap->id ?>,this);" /><span class="plussign">+</span><?php echo _XMAP_ADD_MENU; ?></div></div>
                        <div class="sitemapinfo">
			   <div><?php echo _XMAP_SITEMAP_ID .': '. $sitemap->id; ?></div>
			   <div><table cellspacing="2" cellpadding="2" class="sitemapstats">
                             <tr>
                               <td>&nbsp;</td>
                               <td>HTML</td>
                               <td>XML</td>
                             </tr>
                             <tr>
                               <td><?php echo _XMAP_INFO_LAST_VISIT; ?></td>
                               <td><?php echo $sitemap->lastvisit_html? strftime("%b/%d/%Y",$sitemap->lastvisit_html) : _XMAP_NEVER_VISITED; ?></td>
                               <td><?php echo $sitemap->lastvisit_xml? strftime("%b/%d/%Y",$sitemap->lastvisit_xml) : _XMAP_NEVER_VISITED; ?></td>
                             </tr>
                             <tr>
                               <td><?php echo _XMAP_INFO_COUNT_VIEWS; ?></td>
                               <td><?php echo $sitemap->lastvisit_html? $sitemap->views_html: "--"; ?></td>
                               <td><?php echo $sitemap->lastvisit_xml? $sitemap->views_xml:"--"; ?></td>
                             </tr>
                             <tr>
                               <td><?php echo _XMAP_INFO_TOTAL_LINKS; ?></td>
                               <td><?php echo $sitemap->lastvisit_html? $sitemap->count_html: "--"; ?></td>
                               <td><?php echo $sitemap->lastvisit_xml? $sitemap->count_xml : "--"; ?></td>
                             </tr>
                           </table></div>
                        </div>
		   <div class="spacer"></div>
                   </div></div>
                   <div class="bm"><div class="bl"><div class="br"></div></div></div>
		</div>
		<div class="spacer"></div>
		</form>
<?php
	}

	function showSitemapSettings(&$sitemap,&$lists) {
		global $xmapSiteURL;

?>
	<div class="settingstop"><?php echo sprintf (_XMAP_TIT_SETTINGS_OF,$sitemap->name); ?><div class="settingstoptool"></div></div>
	<form name="frmSettings" id="frmSettings<?php echo $sitemap->id; ?>">
        <table width="100%" border="0" cellpadding="2" cellspacing="0" class="adminForm" style="table-layout: auto; white-space: nowrap;">
	<tr>
	<td>
		<fieldset>
			<legend><?php echo _XMAP_CFG_OPTIONS; ?></legend>
			<table>
				<tr>
					<td style="width:1%">
						<label for="classname"><?php echo _XMAP_CFG_CSS_CLASSNAME; ?></label>:
					</td>
					<td style="width:32%">
						<input type="text" name="classname" id="classname" value="<?php echo @$sitemap->classname; ?>"/>
					</td>

					<td style="width:1%">
						<label for="show_menutitle"><?php echo _XMAP_CFG_SHOW_MENU_TITLES; ?></label>:
					</td>
					<td style="width:32%">
						<input type="checkbox" name="show_menutitle" id="show_menutitle" value="1"<?php echo @$sitemap->show_menutitle ? ' checked="checked"' : ''; ?> />
					</td>

				</tr>
			<tr>
				<td style="width:1%">
					<label for="columns"><?php echo _XMAP_CFG_NUMBER_COLUMNS; ?></label>:
				</td>
				<td style="width:32%">
					<?php echo $lists['columns']; ?>
				</td>
				<td>
					<label for="include_link"><?php echo _XMAP_CFG_INCLUDE_LINK; ?></label>:
				</td>
				<td>
					<input type="checkbox" name="includelink" id="include_link" value="1"<?php echo @$sitemap->includelink ? ' checked="checked"' : ''; ?> />
				</td>
			</tr>

				<?php
					// currently selected external link marker image
					if( eregi( 'gif|jpg|jpeg|png', @$sitemap->ext_image )) {
						$ext_imgurl = $xmapSiteURL.'/components/com_xmap/images/'.$sitemap->ext_image;
					} else {
						$ext_imgurl = $xmapSiteURL.'/images/blank.png';
					}
				?>
				<tr>
					<td>
						<label for="exlinks"><?php echo _XMAP_EX_LINK; ?></label>:
					</td>
					<td colspan="4">
						<input type="checkbox" name="exlinks" id="exlinks" value="1"<?php echo @$sitemap->exlinks ? ' checked="checked"' : ''; ?> />
						&nbsp;
						<?php echo $lists['ext_image']; ?>
						&nbsp;
						<img src="<?php echo $ext_imgurl; ?>" name="imagelib" alt="" />
					</td>
				</tr>
			</table>
		</fieldset>
	</td>
	</tr>

	<tr>
	<td>
		<fieldset>
			<legend><?php echo _XMAP_CFG_URLS; ?></legend>
			<table>
				<?php
					$xml_link = $xmapSiteURL . '/index.php?option=com_xmap&amp;sitemap='.$sitemap->id.'&amp;view=xml';
					$news_link = $xmapSiteURL . '/index.php?option=com_xmap&amp;sitemap='.$sitemap->id.'&amp;view=xml&amp;news=1';
					$html_link = $xmapSiteURL . '/index.php?option=com_xmap&amp;sitemap='.$sitemap->id;
				?>
				<tr>
					<td>
						<?php echo _XMAP_CFG_XML_MAP; ?>:
					</td>
					<td>
						<span id="xmllink" style="background:#FFFFCC; padding:1px; border:1px inset;">
						<a href="<?php echo $xml_link; ?>" target="_blank" title="XML Sitemap Link">
						<?php echo $xml_link; ?>
						</a>
						</span>
						&nbsp;
						<?php
							echo JHTML::_('tooltip', _XMAP_XML_LINK_TIP);
						?>
					</td>
				</tr>
				<tr>
					<td>
						<?php echo _XMAP_CFG_HTML_MAP; ?>:
					</td>
					<td>
						<span id="xmllink" style="background:#FFFFCC; padding:1px; border:1px inset;">
						<a href="<?php echo $html_link; ?>" target="_blank" title="HTML Sitemap Link">
						<?php echo $html_link; ?>
						</a>
						</span>
						&nbsp;
						<?php
							echo JHTML::_('tooltip', _XMAP_HTML_LINK_TIP);
						?>
					</td>
				</tr>
				<tr>
					<td>
						<?php echo _XMAP_CFG_NEWS_MAP; ?>:
					</td>
					<td>
						<span id="newsink" style="background:#FFFFCC; padding:1px; border:1px inset;">
						<a href="<?php echo $news_link; ?>" target="_blank" title="News Sitemap Link">
						<?php echo $news_link; ?>
						</a>
						</span>
						&nbsp;
						<?php
							echo JHTML::_('tooltip', _XMAP_HTML_LINK_TIP);
						?>
					</td>
				</tr>
			</table>
		</fieldset>
	</td>
	</tr>

	<tr>
	<td>
		<fieldset>
			<legend><?php echo _XMAP_EXCLUDE_MENU; ?></legend>
			<table>
			<tr>
				<td>
					<?php echo _XMAP_EXCLUDE_MENU; ?>:
				</td>
				<td>
					<input type="text" name="exclmenus" id="exclmenus" size="40" value="<?php echo $sitemap->exclmenus; ?>" />
					&nbsp;
					<input type="button" onclick="addExclude(<?php echo $sitemap->id; ?>); return false;" value="&larr;" />&nbsp;
				</td>
				<td>
					<?php echo $lists['exclmenus']; ?>
					&nbsp;
					<?php
						echo JHTML::_('tooltip', _XMAP_EXCLUDE_MENU_TIP);
					?>
				</td>
			</tr>
			</table>
		</fieldset>
		<table width="100%">
		<tr>
		<td>
		<fieldset>
			<legend><?php echo _XMAP_CACHE; ?></legend>
			<table>
			<tr>
				<td>
					<label for="usecache"><?php echo _XMAP_USE_CACHE; ?></label>:
				</td>
				<td>
					<input type="checkbox" name="usecache" id="usecache" value="1" <?php echo ($sitemap->usecache == 1? 'checked="checked" ': ''); ?> />
				</td>
				<td>
					<?php echo _XMAP_CACHE_LIFE_TIME; ?>:
				</td>
				<td>
					<input type="text" size="10" name="cachelifetime" id="cachelifetime" value="<?php echo $sitemap->cachelifetime; ?>" />
				</td>
			</tr>
			</table>
		</fieldset>
		</td>
		<td>
		<fieldset>
			<legend><?php echo _XMAP_COMPRESSION; ?></legend>
			<table>
			<tr>
				<td>
					<input type="checkbox" name="compress_xml" id="compress_xml" value="1" <?php echo ($sitemap->compress_xml == 1? 'checked="checked" ': ''); ?> />
				</td>
				<td>
					<label for="compress_xml"><?php echo _XMAP_USE_COMPRESSION; ?></label>:
				</td>
			</tr>
			</table>
		</fieldset>
		</td>
		</tr>
		</table>
	</td>
	</tr>
	<tr>
	<td align="center">
           <input type="hidden" name="id" value="<?php echo $sitemap->id; ?>" />
           <input type="hidden" name="name" value="<?php echo $sitemap->name; ?>" />
           <input type="button" name="cancelsettings" value="<?php echo _XMAP_TOOLBAR_CANCEL; ?>"  onclick="closeSettings('sitemapsettings');" />
           <input type="button" name="savesettings" value="<?php echo _XMAP_TOOLBAR_SAVE; ?>"  onclick="saveSettings(<?php echo $sitemap->id; ?>,'save_sitemap_settings','sitemapsettings');" />
	</td>
	</tr>
        </table>
        </form>
<?php
	}
	function printMenusList( &$sitemap ) {
		$menus = $sitemap->getMenus();
		$i = 0;
		foreach ($menus as $name => $menu) {
		    echo '<div id="'.$name.$sitemap->id.'" onmouseover="showMenuOptions(\''.str_replace("'","\\'",$name).$sitemap->id.'\',\'',str_replace("'","\\'",$name),'\','. $sitemap->id. ');" onmouseout="hideOptions(this.menu);"><span>',$i,'. ', $name,'</span></div>';
		    $i++;
		}
	}

	function showMenuOptions (&$sitemap,&$menu,&$lists) {
		if (is_object($menu) ) {
		?>
	<form name="frmMenuOptions" id="frmMenuOptions">
		<input type="hidden" name="sitemap" value="<?php echo $sitemap->id; ?>" />
		<input type="hidden" name="menutype" value="<?php echo $menu->menutype; ?>" />
        <div class="settingstop"><?php echo sprintf (_XMAP_TIT_SETTINGS_OF,$menu->menutype); ?><div class="settingstoptool"></div></div>
		<table>
			<tr>
				<td>&nbsp;</td>
				<td><label for="module"><?php echo _XMAP_CFG_MENU_MODULE; ?>: </label><input type="text" name="module" value="<?php echo $menu->module; ?>" size="30" /><?php echo JHTML::_('tooltip', _XMAP_CFG_MENU_MODULE_TIP); ?>
</td>
			</tr>
			<tr>
				<td><input type="checkbox" name="show" id="show" <?php echo ($menu->show? " checked=\"checked\"":""); ?> /></td>
				<td><label for="show"><?php echo _XMAP_CFG_MENU_SHOW_HTML; ?></label></td>
			</tr>
			<tr>
				<td style="vertical-align:top;"><input type="checkbox" name="showXML" id="showXML" <?php echo ($menu->showXML? " checked=\"checked\"":""); ?> /></td>
				<td><label for="showXML"><?php echo _XMAP_CFG_MENU_SHOW_XML; ?></label>
				    <div id="menu_options_xml">
					<table>
					<tr>
						<td><?php echo _XMAP_CFG_MENU_CHANGEFREQ; ?></td>
						<td><?php echo $lists['changefreq']; ?></td>
					</tr>
					<tr>
						<td><?php echo _XMAP_CFG_MENU_PRIORITY; ?></td>
						<td><?php echo $lists['priority']; ?></td>
					</tr>
					</table>
				    </div>
				</td>
			</tr>
			<tr>
				<td colspan="2">
					<input type="button" value="<?php echo _XMAP_TOOLBAR_SAVE; ?>" onclick="saveMenuOptions();" />&nbsp;&nbsp;&nbsp;
					<input type="button" value="<?php echo _XMAP_TOOLBAR_CANCEL; ?>" onclick="closeSettings('menuoptions');" />
				</td>
			</tr>
	</form>
<?php
		}
	}

	function showInstalledPlugins( &$rows, $option, &$xmlfile, &$lists ) {

		if (count($rows)) {
			?>
			<form action="index2.php" method="post" name="installedPlugins">
			<?php
			$rc = 0;
			for ($i = 0, $n = count( $rows ); $i < $n; $i++) {
				XmapAdminHtml::printPluginInfo ($rows[$i]);
			}
		} else {
			?>
			<div><?php echo _XMAP_NO_PLUGINS_INSTALLED; ?></div>
			<?php
		}
		?>
		</table>
		<input type="hidden" name="task" value="plugins" />
		<input type="hidden" name="boxchecked" value="0" />
		<input type="hidden" name="option" value="<?php echo $option; ?>" />
		</form>
		<?php
	}

	function printPluginInfo (&$row) {
		$mosConfig_live_site = substr_replace(JURI::root(), "", -1, 1);
?>
		<div id="plugin<?php echo $row->id; ?>" class="plugin <?php echo $row->published? 'published':'unpublished'; ?>">
                    <div class="titlebar">
	    <div class="tl tl1"><img src="<?php echo $mosConfig_live_site; ?>/images/blank.png" width="1" height=1" alt=" " /></div><div class="tl tl2"><img src="<?php echo $mosConfig_live_site; ?>/images/blank.png" width="1" height=1" alt=" " /></div><div class="tl tl3"><img src="<?php echo $mosConfig_live_site; ?>/images/blank.png" width="1" height=1" alt=" " /></div>
			 <div class="tl">
                          <div class="pluginname"><?php echo $row->name; ?></div>
			  <div class="pluginversion"><?php echo @$row->version != "" ? $row->version : "&nbsp;"; ?></div>
                          <div class="spacer"></div>
                      </div>
                    </div>
                    <div class="insidebox">
					  <div class="plugindate"><?php echo @$row->creationdate != "" ? $row->creationdate : "&nbsp;"; ?></div>
					  <div class="pluginauthor"><?php echo _XMAP_AUTHOR .': ' . (@$row->author != "" ? $row->author : _XMAP_UNKNOWN_AUTHOR) . (@$row->authorEmail != "" ? ' &lt;'.$row->authorEmail.'&gt;' : "&nbsp;"); ?></div>
					  <div class="pluginauthorurl"><?php echo @$row->authorUrl != "" ? "<a href=\"" .(substr( $row->authorUrl, 0, 7) == 'http://' ? $row->authorUrl : 'http://'.$row->authorUrl) ."\" target=\"_blank\">$row->authorUrl</a>" : "&nbsp;"; ?></div>
                      <div class="plugintaskbar"><a href="javascript:uninstallPlugin(<?php echo $row->id; ?>);"><?php echo _XMAP_UNINSTALL; ?></a><a href="javascript:settingsPlugin(<?php echo $row->id; ?>);"><?php echo _XMAP_PLUGIN_OPTIONS; ?></a>
			  <a href="javascript:changePluginState(<?php echo $row->id; ?>)"><img id="pluginstate<?php echo $row->id; ?>" src="images/<?php echo $row->published?'publish_g.png" title="'._XMAP_EXT_PUBLISHED.'"':'publish_x.png" title="'._XMAP_EXT_UNPUBLISHED.'"'; ?>" border="0" /></a></div>
                    </div>
                </div>
<?php
	}

	function writableCell( $folder ) {
		echo '<tr>';
		echo '<td class="item">' . $folder . '/</td>';
		echo '<td align="left">';
		echo is_writable( JPATH_COMPONENT_SITE.DS.$folder ) ? '<b><font color="green">'._XMAP_WRITEABLE.'</font></b>' : '<b><font color="red">'._XMAP_UNWRITEABLE.'</font></b>' . '</td>';
		echo '</tr>';
	}

	function showInstallForm( $title,$p_startdir ) {
		?>
		<table class="content">
<?php
		XmapAdminHtml::writableCell( '/administrator/components/com_xmap/extensions' );
?>
		</table>
		<div style="margin: 10px 0px; padding: 5px 15px 5px 35px; min-height: 25px; border: 1px solid #cc0000; background: #ffffcc; text-align: left; color: red; font-weight: bold; background-image: url(../includes/js/ThemeOffice/warning.png); background-repeat: no-repeat; background-position: 10px 50%;">
			<?php echo _XMAP_INSTALL_3PD_WARN; ?>
		</div>
		<script language="javascript" type="text/javascript">
		function submitbutton3(pressbutton) {
			var form = document.adminForm_dir;

			// do field validation
			if (form.install_directory.value == ""){
				alert( "<?php echo str_replace('"','\\"',_XMAP_MSG_SELECT_FOLDER); ?>" );
			} else {
				form.submit();
			}
		}
		</script>
		<form enctype="multipart/form-data" action="index2.php" method="post" name="filename">
		<table class="adminheading">
		<tr>
			<th class="install">
			<?php echo $title;?>
			</th>
		</tr>
		</table>

		<table class="adminform">
		<tr>
			<th>
			<?php echo _XMAP_UPLOAD_PKG_FILE; ?>
			</th>
		</tr>
		<tr>
			<td align="left">
			Package File:
			<input class="text_area" name="install_package" type="file" size="40"/>
			<input class="button" type="submit" value="<?php echo _XMAP_UPLOAD_AND_INSTALL; ?>" />
			</td>
		</tr>
		</table>

		<input type="hidden" name="task" value="uploadfile" />
		<input type="hidden" name="installtype" value="upload" />
		<input type="hidden" name="option" value="com_xmap" />
		</form>
		<br />

		<form enctype="multipart/form-data" action="index2.php" method="post" name="adminForm_dir">
		<table class="adminform">
		<tr>
			<th>
			<?php echo _XMAP_INSTALL_F_DIRECTORY; ?>
			</th>
		</tr>
		<tr>
			<td align="left">
			<?php echo _XMAP_INSTALL_DIRECTORY; ?>:&nbsp;
			<input type="text" name="install_directory" class="text_area" size="60" value="<?php echo $p_startdir; ?>"/>&nbsp;
			<input type="button" class="button" value="<?php echo _XMAP_INSTALL; ?>" onclick="submitbutton3()" />
			</td>
		</tr>
		</table>

		<input type="hidden" name="task" value="installfromdir" />
		<input type="hidden" name="installtype" value="folder" />
		<input type="hidden" name="option" value="com_xmap"/>
		</form>
		<?php
	}

	/**
	* @param string
	* @param string
	* @param string
	* @param string
	*/
	function showInstallMessage( $message, $title, $url ) {
		global $PHP_SELF;
		?>
		<table class="adminheading">
		<tr>
			<th class="install">
			<?php echo $title; ?>
			</th>
		</tr>
		</table>

		<table class="adminform">
		<tr>
			<td align="left">
			<strong><?php echo $message; ?></strong>
			</td>
		</tr>
		<tr>
			<td colspan="2" align="center">
			[&nbsp;<a href="<?php echo $url;?>" style="font-size: 16px; font-weight: bold"><?php echo _XMAP_CONTINUE; ?> ...</a>&nbsp;]
			</td>
		</tr>
		</table>
<?php
	}

	function showInstallFailedMessage(  ) {
?>
		<table class="adminheading">
		<tr>
			<td colspan="2" align="center">
			[&nbsp;<a href="index.php?option=com_xmap" style="font-size: 16px; font-weight: bold"><?php echo _XMAP_CONTINUE; ?>...</a>&nbsp;]
			</td>
		</table>
<?php
	}

	function showPluginSettings (&$extension,$itemid='-1') {
		// get params definitions
		$xmlfile = $extension->getXmlPath();
		$params = new JParameter( $extension->getParams($itemid,true), $xmlfile, 'xmap_ext' );
?>
<form name="frmSettings" id="frmSettings<?php echo $extension->id; ?>">
	<input type="hidden" name="id" value="<?php echo $extension->id; ?>" />
	<?php echo $params->render(); ?>
	<div style="text-align: center;padding: 5px;">
	   <input type="button" name="cancel" onclick="closeSettings('pluginsettings');" value="<?php echo _XMAP_TOOLBAR_CANCEL; ?>" />&nbsp;&nbsp;&nbsp;
	   <input type="button" name="save" onclick="saveSettings(<?php echo $extension->id; ?>,'save_plugin_settings','pluginsettings');" value="<?php echo _XMAP_TOOLBAR_SAVE; ?>" />
	</div>
</form>
<?php
	}
	function showNavigator($sitemapid,$name)
	{
		$doc =& JFactory::getDocument();
		$doc->addScriptDeclaration('
			var tree;
			var autotext = \'\';
			insertLink = function (){
				var link = $(\'f_link\').getValue();
				var text = $(\'f_text\').getValue();
				var title = $(\'f_title\').getValue();
				var cssstyle = $(\'f_cssstyle\').getValue();
				var cssclass = $(\'f_cssclass\').getValue();
				if (link != \'\' && text != \'\') {
					var extra =\'\';
					if (title != \'\') {
						extra = extra + \' title="\'+title.replace(\'"\',\'&quot;\')+\'"\';
					}
					if (cssclass != \'\') {
						extra = extra + \' class="\'+cssclass.replace(\'"\',\'&quot;\')+\'"\';
					}
					if (cssstyle != \'\') {
						extra = extra + \' style="\'+cssstyle.replace(\'"\',\'&quot;\')+\'"\';
					}
					var tag = "<a href=\""+link+"\" "+extra+">"+text+"</a>";
					window.parent.jInsertEditorText(tag, "'.htmlspecialchars($name).'");
				}
				window.parent.document.getElementById(\'sbox-window\').close();
			};
			window.addEvent("domready",function(){
				tree =  new MooTreeControl({
				div: \'xmap-nav_tree\',
				mode: \'files\',
				grid: true,
				theme: \''.JURI::base().(JPATH_COMPONENT == JPATH_COMPONENT_SITE? 'administrator/':'').'components/com_media/assets/mootree.gif\',
				onSelect: function (node,state) {
					if (typeof node.data.link != \'undefined\' && node.data.selectable == \'true\') {
						document.adminForm.link.value = node.data.link;
						if (document.adminForm.text.value == autotext ) {
							document.adminForm.text.value = node.text;
							autotext =  node.text;
						}
					}
				}
			},{
				text: \'Home\',
				open: true
			});
			tree.root.load(\'index.php?option=com_xmap&task=navigator-links&sitemap='.$sitemapid.'&e_name='.$name.'&tmpl=component\');
			});
			');

		echo '<div id="xmap-nav_tree" style="height:250px;overflow:auto;border:1px solid #CCC;"></div>';
		echo '
		      <div id="xmap-nav_linkinfo" style="margin-top:3px;border:1px solid #CCC;height:120px;">
			    <form name="adminForm" action="#" onSubmit="return false;">
			    <table width="100%">
				   <tr>
				       <td>'._XMAP_TEXT.'</td>
				       <td colspan="3"><input type="text" name="text" id="f_text" value="" size="30" /></td>
				   </tr>
				   <tr>
				       <td>'._XMAP_TITLE.'</td>
				       <td colspan="3"><input type="text" name="title" id="f_title"  value="" size="30" /></td>
				   </tr>
				   <tr>
				       <td>'._XMAP_LINK.'</td>
				       <td colspan="3"><input type="text" name="link" id="f_link"  value="" size="50" /></td>
				   </tr>
				   <tr>
				       <td>'._XMAP_CSS_STYLE.'</td>
				       <td><input type="text" name="cssstyle" id="f_cssstyle"  value="" /></td>
				       <td>'._XMAP_CSS_CLASS.'</td>
				       <td><input type="text" name="cssclass" id="f_cssclass"  value="" /></td>
				   </tr>
				   <tr>
				       <td colspan="4" align="right"><button name="cssstyle" id="f_cssstyle" onclick="insertLink();">'._XMAP_OK.'</button>
					     <button name="cssstyle" id="f_cssstyle" onclick="window.parent.document.getElementById(\'sbox-window\').close();">'._XMAP_CANCEL.'</button></td>
				   </tr>
				</table>
				</form>
		      </div>';
		echo '<ul id="xmap-nav"></ul>';
	}
	function showNavigatorLinks($sitemapid,$list,$name)
	{
		header('Content-type: text/xml');
		echo '<?xml version="1.0" encoding="UTF-8" ?>',"\n";
		echo "<nodes>\n";
		foreach ($list as $node) {
			$load = 'index.php?option=com_xmap&amp;task=navigator-links&amp;sitemap='.$sitemapid.'&amp;e_name='.$name.(isset($node->id)?'&amp;Itemid='.$node->id:'').(isset($node->link)?'&amp;link='.urlencode($node->link):'').'&amp;tmpl=component';
			echo "<node text=\"".htmlspecialchars($node->name)."\" ".($node->expandible?" openicon=\"_open\" icon=\"_closed\" load=\"$load\"":' icon="_doc"')." uid=\"".$node->uid."\" link=\"".str_replace(array('&amp;','&'),array('&','&amp;'),$node->link)."\" selectable=\"".($node->selectable?'true':'false')."\">\n";
			echo "</node>\n";
		}
		echo "</nodes>";
		exit;
	}
}
