<?php
/**
 * $Id: install.xmap.php 82 2010-01-01 11:18:15Z guilleva $
 * $LastChangedDate: 2010-01-01 05:18:15 -0600 (vie, 01 ene 2010) $
 * $LastChangedBy: guilleva $
 * Xmap by Guillermo Vargas
 * a sitemap component for Joomla! CMS (http://www.joomla.org)
 * Author Website: http://joomla.vargas.co.cr
 * Project License: GNU/GPL http://www.gnu.org/copyleft/gpl.html
*/

defined( '_JEXEC' ) or die( 'Direct Access to this location is not allowed.' );

// load language file
$pathLangFile	= JPATH_ADMINISTRATOR.DS.'components'.DS.'com_xmap'.DS.'language'.DS;
$lang =& JFactory::getLanguage();
$tmp_lng = strtolower($lang->getBackwardLang());
	?>	
	<div align="center">
    <table border="0" width="90%">
	    <tr>
		    <td width="18%"><u><b>Name:</b></u></td>
		    <td width="80%"><b>Xmap 1.2.7 Stable</b></td>
	    </tr>
	    <tr>
		    <td width="18%"><u><b>Description:</b></u></td>
		    <td width="80%"><b>Xmap is a sitemap component for Joomla! Based on <a target="_blank" href="http://www.ko-ca.com">Joomap</a> by Daniel Grothe.</b></td>
	    </tr>
	    
		    <td width="18%"><u><b>Author:</b></u></td>
		    <td width="80%"><b>2010 by <a target="_blank" href="http://www.joomla.vargas.co.cr/">Guillermo Vargas</a>.</b></td>
	    </tr>
		<tr>
		    <td width="18%"><u><b>License:</b></u></td>
		    <td width="80%"><b>Released under the terms and conditions of the <a href="http://www.gnu.org/licenses/gpl-1.0.html">GNU General Public License</a>.</b></td>
	    </tr>
	    <tr>
	    <tr>
		    <td width="18%"><u><b>Website:</b></u></td>
		    <td width="80%"><b><a target="_blank" href="http://joomla.vargas.co.cr">http://joomla.vargas.co.cr/</a></b></td>
	    </tr>
		<tr>
		    <td width="18%"><u><b>Installation Process:</b></u></td>
		    <td width="80%" rowspan="2"><b>
			<?php
			if ( file_exists( $pathLangFile . $tmp_lng . '.php' )){
				include_once( $pathLangFile . $tmp_lng . '.php' );
			}else{
				include_once( $pathLangFile . $tmp_lng . '.php' );
			}

			$live_site = substr_replace(JURI::root(), "", -1, 1);

			include( JPATH_ADMINISTRATOR.DS.'components'.DS.'com_xmap'.DS.'classes'.DS.'XmapConfig.php' );

			echo '<table>';
			echo '<tr><td><img src="',$live_site,'/administrator/components/com_xmap/images/logo.jpg" /></td>';
			echo '<td>';
			echo '<table class="adminlist" style="width:auto"><tr class="row0"><td>&rarr;</td><td>'."\n";

			XmapConfig::create();

			echo '</td></tr>'."\n";

			echo "</table></td>\n";
			echo "</tr>";
			?> </b>
			<font color="green"><b>Installation completed.</b><br />
			<br> </br>
			<font color="green"><b>Xmap 1.2.7 has been installed successfully! Thank you for using Xmap! Settings can be configured in the <a href="index2.php?option=com_xmap">&rarr; component menu</a>!<br /></b><br />
			<br> </br></td>
		</tr>
	    <tr>
	      <td>&nbsp;</td>
      </tr>
		<tr>
		    <td width="100%" colspan="2">
		      <p><b><font style="color:#008000;">Please periodically review our project's site for changes or new versions for this program.</font></b>
        </td>
	    </tr>
    </table>
	</div>