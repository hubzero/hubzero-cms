<?php
/**
 * plugin_googlemap2.php,v 2.12 2008/07/29 13:34:11
 * @copyright (C) Reumer.net
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 */
 /* ----------------------------------------------------------------
 * 2008-07-29 version 2.12: Improved by Mike Reumer
 * - use no javascript message of the language defined in Joomla.
 * - Changed proxy so local file are read local and also use file gets too
 * - Added domready instead of timeout, so in IE less problems with other modules
 * - Added multiple kml files
 * - Changed when to place marker and text
 * - Better solution for placing marker or no marker
 * - Simple directions on map
 * - Sorting of items inside top items of kml-file
 * - Directions in lightbox and possibility to use print version of directions
 * - Streetview with button!
 * - On map directions
 * - 3D and automatic control
 * - lightbox out of infowindow
 * - navigation label
 * - Wiki/panoramio layer
 * - Changed adsmanager
 /* ----------------------------------------------------------------

/** ensure this file is being included by a parent file */

if (defined( '_JEXEC' )) {
	$mainframe->registerEvent( 'onPrepareContent', 'Pre15x_PluginGoogleMap2' );
	$mainframe->registerEvent( 'onMap', 'Pre15x_PluginGoogleMap2' );
} elseif (defined( '_VALID_MOS' )) {
	global $mainframe;
	$_MAMBOTS->registerFunction( 'onPrepareContent', 'Pre10x_PluginGoogleMap2' );
	$_MAMBOTS->registerFunction( 'onMap', 'Pre10xonMap_PluginGoogleMap2' );
} else {
	die( 'Restricted access' );
}

if (!defined('_CMN_JAVASCRIPT')) define('_CMN_JAVASCRIPT', "<b>JavaScript must be enabled in order for you to use Google Maps.</b> <br/>However, it seems JavaScript is either disabled or not supported by your browser. <br/>To view Google Maps, enable JavaScript by changing your browser options, and then try again.");

/* Switch call to function of 1.5 to the real module 
 */
function Pre15x_PluginGoogleMap2( &$row, &$params, $page=0 ) {
	$database = &JFactory::getDBO();

	// Get Plugin info
	$plugin =& JPluginHelper::getPlugin('content', 'plugin_googlemap2'); 

	$plugin_params = new JParameter( $plugin->params );
	$joomla_version = 1.5;

	//$published = $plugin->published;
	// Solve bug in Joomal 1.5 that when plugin is unpublished that the tag is not removed
	// So use a parameter of plugin to set published for Joomla 1.5
	$published = $plugin_params->get( 'publ', '0' );
	// If format=feed then remove plugin so not published
	$format = JRequest::getVar('format', '');	
	if ($format=="feed"||$format=="pdf") // PDF is not working and {mosmap} shows in pfd
		$published=0;

	$option = JRequest::getVar('option', '');	
	$view = JRequest::getVar('view', '');	
	$task = JRequest::getVar('task', '');	

	if (!($option=='com_content'&&$view=='article'&&$task=="edit")) {
		$id = intval( JRequest::getVar('id', null) );	
		$id = explode(":", $id);
		$id = $id[0];
		$pluginmap = new PluginGoogleMap2();
	
		if( !$pluginmap->core($published, $row, $params, $page, $plugin_params, $id, $joomla_version) ){
			echo "problem";
		}
		unset ($id, $pluginmap);
	}

	unset($database, $plugin, $plugin_params, $joomla_version, $published, $format, $option, $view, $task);

	return true;
}

/* Switch call to function of 1.0.x to the real module
 */
function Pre10x_PluginGoogleMap2( $published, &$row, $mask=0, $page=0 ) {
	global $database;

	// load plugin parameters
	$query = "SELECT id"
		. "\n FROM #__mambots"
		. "\n WHERE element = 'plugin_googlemap2'"
		. "\n AND folder = 'content'"
		;
	$database->setQuery( $query );
	$id = $database->loadResult();
	$plugin = new mosMambot( $database );
	$plugin->load( $id );
	$plugin_params = new mosParameters( $plugin->params );
	$joomla_version = 1.0;

	$id = intval( mosGetParam( $_REQUEST, 'id', null ) );

	$pluginmap = new PluginGoogleMap2();
	
	if( !$pluginmap->core($published, $row, $mask, $page, $plugin_params, $id, $joomla_version) ){
		echo "problem";
	}

	unset($query, $id, $plugin, $plugin_params, $joomla_version, $pluginmap);
	return true;
}

function Pre10xonMap_PluginGoogleMap2( $published, &$row, $mask=0, $page=0 ) {
	global $database;

	// load plugin parameters
	$query = "SELECT id"
		. "\n FROM #__mambots"
		. "\n WHERE element = 'plugin_googlemap2'"
		. "\n AND folder = 'content'"
		;
	$database->setQuery( $query );
	$id = $database->loadResult();
	$plugin = new mosMambot( $database );
	$plugin->load( $id );
	$plugin_params = new mosParameters( $plugin->params );
	$joomla_version = 1.0;

	$id = intval( mosGetParam( $_REQUEST, 'id', null ) );

	$pluginmap = new PluginGoogleMap2();
	$pluginmap->event = '10xonMap';

	if( !$pluginmap->core($published, $row, $mask, $page, $plugin_params, $id, $joomla_version) ){
		echo "problem";
	}

	unset($query, $id, $plugin, $plugin_params, $joomla_version, $pluginmap);
	return true;
}

class PluginGoogleMap2 {
	var $debug_plugin = '0';
	var $debug_text = '';
	var $event = '';

	/* If PHP < 5 then htmlspecialchars_decode doesn't exists
	 */
	
	function _htsdecode($string, $options=0) {
		if (function_exists('htmlspecialchars_decode')) {
			return htmlspecialchars_decode($string, $options);
		} else {
			return strtr($string,array_flip(get_html_translation_table(HTML_SPECIALCHARS, $options)));
		}
	}
	
	function debug_log($text)
	{
		if ($this->debug_plugin =='1')
			$this->debug_text .= "\n// ".$text." (".round($this->memory_get_usage()/1024)." KB)";
	
		return;
	}

	function get_index($string, $brackets)
	{
		if ($brackets=='[') {
			$string = preg_replace("/^.*\[/", '', $string);
			$string = preg_replace("/\].*$/", '', $string);
		} else {
			$string = preg_replace("/^.*\(/", '', $string);
			$string = preg_replace("/\).*$/", '', $string);
		}		
		return $string;
	}
    // Only define function if it doesn't exist
    function memory_get_usage()
    {
		if ( function_exists( 'memory_get_usage' ) )
			return memory_get_usage(); 
		else
			return 0;
    }

	function injectCustomHeadTags($html, $check, &$row) {
		global $mainframe;

		// Get buffer
		// Is there a difference between J15/J10
		$buf = &$row;
		if (!function_exists('jimport')) {
			// version 1.0.x
			$screen = ob_get_contents();
			$header = $mainframe->getHead();
		} else {
			$screen = '';
			$header = '';
			$header = $mainframe->getHead();
		}
			
		// Check if code already is inserted?
		$check = str_replace("/", "\/",$check);
		$check = str_replace(".", "\.",$check);
		$check = str_replace("?", "\?",$check);
		$check = "/".$check."/is";
		$chk = preg_match($check, $buf) + preg_match($check, $screen) + preg_match($check, $header);
		if ($chk==0) {
			// Check for head
			$head = preg_match("/<head>/is", $buf);
			$hd = preg_match("/<head>/is", $screen);
			// if no head do mainframe replace
			if ($head==0) {
				// With Joomla 10x onMap add header doesn't work
				if ($hd==0) {
					$this->debug_log("Mainframe header replace");
					$mainframe->addCustomHeadTag($html);
				}
				else {
					$this->debug_log("With Joomla 10x onMap add header doesn't work and header not available so place it in body");
					echo $html;
				}
			} else {
				// if head then place in head the scripts
				$buf = preg_replace("/<head(| .*?)>(.*?)<\/head>/is", "<head$1>$2".$html."</head>", $buf);						
			}
		} else
			$this->debug_log("No replace script already available");

		unset($buf, $screen, $header, $check, $chk, $head, $hd);
	}
	
	/* If PHP < 5 then SimpleXMLElement doesn't exists
	 */
	function get_geo($protocol, $googlewebsite, $address, $key)
	{
		$this->debug_log("get_geo(".$address.")");
	
		$coords = '';
		$getpage='';
		$replace = array("\n", "\r", "&lt;br/&gt;", "&lt;br /&gt;", "&lt;br&gt;", "<br>", "<br />", "<br/>");
		$address = str_replace($replace, '', $address);

		// Convert address to utf-8 encoding
		if (function_exists('mb_detect_encoding')) {
			$enc = mb_detect_encoding($address);
			if (!empty($enc))
				$address = mb_convert_encoding($address, "utf-8", $enc);
			else
				$address = mb_convert_encoding($address, "utf-8");
		}

		$this->debug_log("Address: ".$address);
		
		$uri = $protocol.$googlewebsite."/maps/geo?q=".urlencode($address)."&output=xml&key=".$key;
		$this->debug_log("get_geo(".$uri.")");
		
		if ( !class_exists('SimpleXMLElement') )
		{
			// PHP4
			$ok = false;
			$this->debug_log("SimpleXMLElement doesn't exists so probably PHP 4.x");
			if (ini_get('allow_url_fopen'))
				if (($getpage = file_get_contents($uri)))
					$ok = true;

			if (!$ok) {
				$this->debug_log("URI couldn't be opened probably ALLOW_URL_FOPEN off");
				if (function_exists('curl_init')) {
					$this->debug_log("curl_init does exists");
					$ch = curl_init();
					$timeout = 5; // set to zero for no timeout
					curl_setopt ($ch, CURLOPT_URL, $uri);
					curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1);
					curl_setopt ($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
					$getpage = curl_exec($ch);
					curl_close($ch);
				} else
					$this->debug_log("curl_init doesn't exists");
			}
	
			$this->debug_log("Returned page: ".$getpage);
	
			if (function_exists('mb_detect_encoding')) {
				$enc = mb_detect_encoding($getpage);
				if (!empty($enc))
					$getpage = mb_convert_encoding($getpage, "utf-8", $enc);
			}
				
			if (function_exists('domxml_open_mem')&&($getpage<>'')) {
				$responsedoc = domxml_open_mem($getpage);
				if ($responsedoc !=null) {				
					$response = $responsedoc->get_elements_by_tagname("Response");
					if ($response!=null) {
						$placemark = $response[0]->get_elements_by_tagname("Placemark");
						if ($placemark!=null) {
							$point = $placemark[0]->get_elements_by_tagname("Point");
							if ($point!=null) {
								$coords = $point[0]->get_content();
								$this->debug_log("Coordinates: ".join(", ", explode(",", $coords)));
								return $coords;
							}
						}
					}
				}
			}
			$this->debug_log("Coordinates: null");
			return null;
		}
		else
		{
			// PHP5
			$this->debug_log("SimpleXMLElement does exists so probably PHP 5.x");
			$ok = false;
			if (ini_get('allow_url_fopen')) { 
				if (file_exists($uri)) {
					$getpage = file_get_contents($uri);
					$ok = true;
				}
			} 
			
			if (!$ok) { 
				$this->debug_log("URI couldn't be opened probably ALLOW_URL_FOPEN off");
				if (function_exists('curl_init')) {
					$this->debug_log("curl_init does exists");
					$ch = curl_init();
					$timeout = 5; // set to zero for no timeout
					curl_setopt ($ch, CURLOPT_URL, $uri);
					curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1);
					curl_setopt ($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
					$getpage = curl_exec($ch);
					curl_close($ch);
				} else
					$this->debug_log("curl_init doesn't exists");
			}
	
			$this->debug_log("Returned page: ".$getpage);
			if (function_exists('mb_detect_encoding')) {
				$enc = mb_detect_encoding($getpage);
				if (!empty($enc))
					$getpage = mb_convert_encoding($getpage, "utf-8", $enc);
			}
	
			if ($getpage <>'') {
				$expr = '/xmlns/';
				$getpage = preg_replace($expr, 'id', $getpage);
				$xml = new SimpleXMLElement($getpage);
				foreach($xml->xpath('//coordinates') as $coordinates) {
					$coords = $coordinates;
					break;
				}
				if ($coords=='') {
					$this->debug_log("Coordinates: null");
					return null;
				}
				$this->debug_log("Coordinates: ".join(", ", explode(",", $coords)));
				return $coords;
			}
		}
		$this->debug_log("get_geo totally wrong end!");
	}
	
	function randomkeys($length)
	{
		$key = "";
		$pattern = "1234567890abcdefghijklmnopqrstuvwxyz";
		for($i=0;$i<$length;$i++)
		{
			$key .= $pattern{rand(0,35)};
		}
		unset($i, $pattern);
		return $key;
	}
	
	function translate($orgtext, $lang) {
		$langtexts = preg_split("/[\n\r]+/", $orgtext);
		$text = "";

		if (is_array($langtexts)) {
			$replace = array("\n", "\r", "<br/>", "<br />", "<br>");
			$firsttext = "";
			foreach($langtexts as $langtext)
			{
				$values = explode(";",$langtext, 2);
				if (count($values)>1) {
					$values[0] = trim(str_replace($replace, '', $values[0]));
					if ($firsttext == "")
						$firsttext = $values[1];
						
					if (trim($lang)==$values[0])
					{
						$text = $values[1];
						break;
					}
				}
			}
			// Not found
			if ($text=="")
				$text = $firsttext;
		}	
		
		if ($text=="")
			$text = $orgtext;
	
		$text = $this->_htsdecode($text, ENT_NOQUOTES);
	
		unset($langtexts, $replace, $langtext, $values);
		return $text;
	}

	function get_API_key ($params, $url) {
		$url = trim($url);
		$replace = array('http://', 'https://');
		$url = str_replace($replace, '', $url);
		$this->debug_log("url: ".$url);
		$key = '';
		$multikey = trim($params->get( 'Google_Multi_API_key', '' ));
		if ($multikey!='') {
			$this->debug_log("multikey: ".$multikey);
			$replace = array("\n", "\r", "<br/>", "<br />", "<br>");
			$sites = preg_split("/[\n\r]+/", $multikey);
			foreach($sites as $site)
			{
				$values = explode(";",$site, 2);
				$values[0] = trim(str_replace($replace, '', $values[0]));
				$values[1] = str_replace($replace, '', $values[1]);
				$this->debug_log("values[0]: ".$values[0]);
				$this->debug_log("values[1]: ".$values[1]);
				if ($url==$values[0])
				{
					$key = trim($values[1]);
					break;
				}
			}
		}
		
		if ($key=='')
			$key = trim($params->get( 'Google_API_key', '' ));
			
		unset($replace, $multikey, $sites, $site, $values);
		$this->debug_log("key: ".$key);
		return $key;
	}

	function check_google_api_version($version, $checkversion) {
		if ($version=='2.x')
			return true;
		if ($version=='2.s')
			return true;
		if ($version=='2')
			return true;
			
		$ver1 = explode(".", $version);
		$ver2 = explode(".", $checkversion);
		$cont = true;
		$x = 0;
		while ($cont&&(count($ver1)>$x)&&(count($ver2)>$x)) {
			if (is_numeric($ver2[$x])&&is_numeric($ver1[$x])) {
				if (intval($ver1[$x]) > intval($ver2[$x]))
					return true;
				if (($ver1[$x]!='x')&&(intval($ver2[$x]) > intval($ver1[$x]))) {
					$cont = false;
				}
			} elseif (($ver1[$x]!='x')&&($ver2[$x] > $ver1[$x])) {
				if ($ver1[$x] > $ver2[$x])
					return true;

				$cont = false;
			}

			$x++;
		}
		if ((count($ver1)<=$x)&&(count($ver2)>$x)&&$cont)
			$cont = false;
			
		return $cont;			
	}
	
	function remove_html_tags($text) {
		$reg[] = "/<span[^>]*?>/si";
		$repl[] = '';
		$reg[] = "/<\/span>/si";
		$repl[] = '';
		return preg_replace( $reg, $repl, $text );
	}
	
	/** Real module
	 */
	function core( $published, &$row, $mask=0, $page=0, &$params, $id, $joomla_version=1.0 ) {
		global $mainframe, $mosConfig_locale;
		global $iso_client_lang; // This is a global of Joomfish!

		if ($joomla_version< 1.5) {
			global $mosConfig_live_site, $mosConfig_locale, $iso_client_lang;
			$plugin_path = "mambots";
			$geoiso = "utf-8";
			$iso=trim(str_replace('charset=','',_ISO));
			$no_javascript = _CMN_JAVASCRIPT;
			$pagebreak = '/{mospagebreak}/';
		} else {
			$plugin_path = "plugins";
			$mosConfig_live_site = preg_replace('/\/$/', '', JURI::base());
			$lang = JFactory::getLanguage();
			$mosConfig_locale = $lang->getTag();
			$iso = "utf-8";
			$geoiso=$iso;
			$no_javascript = JText::_( '_CMN_JAVASCRIPT' );
			$document =& JFactory::getDocument();
			$pagebreak = '/<hr\s(title=".*"\s)?class="system-pagebreak"(\stitle=".*")?\s\/>/si';
		}

		// get the parameter on what code should plugin trigger!
		$plugincode = $params->get( 'plugincode', 'mosmap' );
		$brackets = $params->get( 'brackets', 'mosmap' );
	
		if ($brackets=="both") {
			$singleregex='/(\{|\[)('.$plugincode.'\s*)(.*?)(\}|\])/si';
			$regex='/(\{|\[)'.$plugincode.'\s*.*?(\}|\])/si';
			$countmatch = 3;
		} elseif ($brackets=="[") {
			$singleregex='/(\['.$plugincode.'\s*)(.*?)(\])/si';
			$regex='/\['.$plugincode.'\s*.*?\]/si';
			$countmatch = 2;
		} else {
			$singleregex='/({'.$plugincode.'\s*)(.*?)(})/si';
			$regex='/{'.$plugincode.'\s*.*?}/si';
			$countmatch = 2;
		}
	
		$cnt=preg_match_all($regex,$row->text,$matches,PREG_OFFSET_CAPTURE | PREG_PATTERN_ORDER);
		$first=true;
		$first_mootools=true;
		$first_modalbox=true;
		$first_localsearch=true;
		$first_kmlrenderer=true;
		$first_kmlelabel=true;
		$first_svcontrol=true;
		$first_animdir= true;
		$first_arcgis=true;
		$first_panoramiolayer = true;
		
		for($counter = 0; $counter < $cnt; $counter ++)
		{
			// Parameters can get the default from the plugin if not empty or from the administrator part of the plugin
			$this->debug_plugin = $params->get( 'debug', '0' );
			$google_API_version = $params->get( 'Google_API_version', '2.x' );

			$timeinterval = $params->get( 'timeinterval', '500' );
			$loadmootools = $params->get( 'loadmootools', '1' );
			$urlsetting = $params->get( 'urlsetting', 'http_host' );
			$googlewebsite = $params->get( 'googlewebsite', 'maps.google.com' );
			$googleindexing = $params->get( 'googleindexing', '1' );
			$width = $params->get( 'width', '100%' );
			$height = $params->get( 'height', '400px' );
			$deflatitude = $params->get( 'lat', '52.075581' );
			$deflongitude = $params->get( 'lon', '4.541513' );
			$centerlat = $params->get( 'centerlat', '' );
			$centerlon = $params->get( 'centerlon', '' );
			$address = $params->get( 'address', '' );
			$zoom = $params->get( 'zoom', '10' );
			$ovzoom = $params->get( 'ovzoom', '' );
			$controltype = $params->get( 'controlType', 'user' );
			$showmaptype = $params->get( 'showMaptype', '1' );
			$showterrainmaptype = $params->get( 'showTerrainMaptype', '1' );
			$showearthmaptype = $params->get( 'showEarthMaptype', '1' );
			$showscale = $params->get( 'showScale', '0' );
			$zoom_new = $params->get( 'zoomNew', '0' );
			$zoom_wheel = $params->get( 'zoomWheel', '0' );
			$keyboard = $params->get( 'keyboard', '0' );
			$overview = $params->get( 'overview', '0' );
			$navlabel = $params->get( 'navlabel', '0' );
			$dragging = $params->get( 'dragging', '1' );
			$rotation = $params->get( 'rotation', '0' );
			$marker = $params->get( 'marker', '1' );
			$icon = $params->get( 'icon', '' );
			$iconwidth = $params->get( 'iconwidth', '' );
			$iconheight = $params->get( 'iconheight', '' );
			$iconshadow = $params->get( 'iconshadow', '' );
			$iconshadowwidth = $params->get( 'iconshadowwidth', '' );
			$iconshadowheight = $params->get( 'iconshadowheight', '' );
			$iconshadowanchorx = $params->get( 'iconshadowanchorx', '' );
			$iconshadowanchory = $params->get( 'iconshadowanchory', '' );
			$iconanchorx = $params->get( 'iconanchorx', '' );
			$iconanchory = $params->get( 'iconanchory', '' );
			$iconinfoanchorx = $params->get( 'iconinfoanchorx', '' );
			$iconinfoanchory = $params->get( 'iconinfoanchory', '' );
			$icontransparent = $params->get( 'icontransparent', '' );
			$iconimagemap = $params->get( 'iconimagemap', '' );
			$gotoaddr = $params->get( 'gotoaddr', '0' );
			$gotoaddrzoom = $params->get( 'gotoaddrzoom', '0' );
			$erraddr = $params->get( 'erraddr', 'Address ## not found!' );
			$txtaddr = $params->get( 'txtaddr', 'Address: <br />##' );
			$clientgeotype = $params->get( 'clientgeotype', 'google' );
			$align = $params->get( 'align', 'center' );
			$langtype = $params->get( 'langtype', '' );
			$dir = $params->get( 'dir', '0' );
			$animdir = $params->get( 'animdir', '0' );
			$animspeed = $params->get( 'animspeed', 1 );
			$formspeed = $params->get( 'formspeed', 0 );
			$formdirtype = $params->get( 'formdirtype', 0 );
			$formaddress = $params->get( 'formaddress', 0 );
			$animautostart = $params->get( 'animautostart', '0' );
			$animunit = $params->get( 'animunit', 'miles' );
			$langanim = $params->get( 'langanim', 'en;The requested panorama could not be displayed|Could not generate a route for the current start and end addresses|Street View coverage is not available for this route|You have reached your destination|miles|miles|ft|kilometers|kilometer|meters|In|You will reach your destination|Stop|Drive|Press Drive to follow your route|Route|Speed|Fast|Medium|Slow' );
			$dirtype = $params->get( 'dirtype', 'D' );
			$avoidhighways = $params->get( 'avoidhighways', '0' );
			$traffic = $params->get( 'traffic', '0' );
			$panoramio = $params->get( 'panoramio', 'none' );
			$panotype = $params->get( 'panotype', 'none' );
			$panoorder = $params->get( 'panoorder', 'popularity' );
			$panomax = $params->get( 'panomax', '50' );
			$youtube = $params->get( 'youtube', 'none' );
			$wiki = $params->get( 'wiki', 'none' );
			$adsmanager = $params->get( 'adsmanager', '0' );
			$maxads = $params->get( 'maxads', '3' );
			$localsearch = $params->get( 'localsearch', '0' );
			$adsense = $params->get( 'adsense', '' );
			$channel = $params->get( 'channel', '' );
			$googlebar = $params->get( 'googlebar', '0' );
			$searchlist = $params->get( 'searchlist', '0' );
			$searchtarget = $params->get( 'searchtarget', '0' );
			$searchzoompan = $params->get( 'searchzoompan', '1' );
			$txt_get_dir = $params->get( 'txtgetdir', 'Get Directions' );
			$txt_from = $params->get( 'txtfrom', '' );
			$txt_to = $params->get( 'txtto', '' );
			$txt_diraddr = $params->get( 'txtdiraddr', 'Address: ' );
			$txt_dir = $params->get( 'txtdir', 'Directions: ' );
			$txt_driving = $params->get( 'txt_driving', '' );
			$txt_avhighways = $params->get( 'txt_avhighways', '' );
			$txt_walking = $params->get( 'txt_walking', '' );
			$dirdef = $params->get( 'dirdefault', '0' );
			$showdir = $params->get( 'showdir', '1' );
			$lightbox = $params->get( 'lightbox', '0' );
			$lbxwidth = $params->get( 'lbxwidth', '100%' );
			$lbxheight = $params->get( 'lbxheight', '700px' );
			$txtlightbox = $params->get( 'txtlightbox', '0' );
			$lbxcaption =  $params->get( 'lbxcaption', '' );
			$lbxzoom =  $params->get( 'lbxzoom', '' );
			$effect = $params->get( 'effect', 'none' );
			$kmlrenderer = $params->get( 'kmlrenderer', 'google' );
			$kmlsidebar = $params->get( 'kmlsidebar', 'none' );
			$kmllightbox = $params->get( 'kmllightbox', '0' );
			$kmlsbwidth = $params->get( 'kmlsbwidth', '200' );
			$kmlfoldersopen = $params->get( 'kmlfoldersopen', '0' );
			$kmlopenmethod = $params->get( 'kmlopenmethod', 'click' );
			$kmlsbsort = $params->get( 'kmlsbsort', 'none' );
			$kmlmessshow = $params->get( 'kmlmessshow', '0' );
			$kmlclickablemarkers = $params->get( 'kmlclickablemarkers', '1' );			
			$kmlcontentlinkmarkers = $params->get( 'kmlcontentlinkmarkers', '0' );			
			$kmllinkablemarkers = $params->get( 'kmllinkablemarkers', '0' );
			$kmllinktarget = $params->get( 'kmllinktarget', '' );
			$kmllinkmethod = $params->get( 'kmllinkmethod', '' );
			$kmlmarkerlabel = $params->get( 'kmlmarkerlabel', '' );
			$kmlmarkerlabelclass = $params->get( 'kmlmarkerlabelclass', '' );
			$kmlpolylabel = $params->get( 'kmlpolylabel', '' );
			$kmlpolylabelclass = $params->get( 'kmlpolylabelclass', '' );
			$proxy = $params->get( 'proxy', '1' );
			$sv = $params->get( 'sv', 'none' );
			$svwidth = $params->get( 'svwidth', '100%' );
			$svheight = $params->get( 'svheight', '300' );
			$svyaw = $params->get( 'svyaw', '0' );
			$svpitch = $params->get( 'svpitch', '0' );
			$svzoom = $params->get( 'svzoom', '' );
			$earthtimeout = $params->get( 'earthtimeout', '100' );
			$earthborders = $params->get( 'earthborders', '0' );
			$earthbuildings = $params->get( 'earthbuildings', '0' );
			$earthroads = $params->get( 'earthroads', '0' );
			$earthterrain = $params->get( 'earthterrain', '0' );
			
			// Key should be filled in the administrtor part or as parameter with the plugin out of content item
			$startmem = round($this->memory_get_usage()/1024);
			$this->debug_log("Memory Usage Start: " . $startmem . " KB");
			$this->debug_log("HTTP_HOST: ".$_SERVER['HTTP_HOST']);
			$this->debug_log("SERVER_PORT: ".$_SERVER['SERVER_PORT']);
			$this->debug_log("mosConfig_live_site: ".$mosConfig_live_site);
			if ($urlsetting=='mosconfig')
				$key = $this->get_API_key($params, $mosConfig_live_site);
			else 
				$key = $this->get_API_key($params, $_SERVER['HTTP_HOST']);
			
			// get default lang from $mosConfig_locale
			$this->debug_log("langtype: ".$langtype);
			$this->debug_log("mosConfig_locale: ".$mosConfig_locale);
			$this->debug_log("iso_client_lang: ".$iso_client_lang);
		
			if ($langtype == 'site') 
			{
				if ($joomla_version< 1.5) 
					$locale_parts = explode('_', $mosConfig_locale);
				else
					$locale_parts = explode('-', $mosConfig_locale);
				$lang = $locale_parts[0];
				// Chinese and portugal use full iso code to indicate language
				if ($lang=='zh'||$lang=='pt')
					$lang = $mosConfig_locale;
			} else if ($langtype == 'config') 
			{
				$lang = $params->get( 'lang', '' );
			} else if ($langtype == 'joomfish')
			{
				$lang = $iso_client_lang;
			} else {
				$lang = '';
			} 
	
			$this->debug_log("lang : ".$lang);
			
			//Translate parameters
			$erraddr = $this->translate($erraddr, $lang);
			$txtaddr = $this->translate($txtaddr, $lang);
			$txtaddr = str_replace(array("\r\n", "\r", "\n"), '', $txtaddr );
			$txt_get_dir = $this->translate($txt_get_dir, $lang);
			$txt_from = $this->translate($txt_from, $lang);
			$txt_to = $this->translate($txt_to, $lang);
			$txt_diraddr = $this->translate($txt_diraddr, $lang);
			$txt_dir = $this->translate($txt_dir, $lang);
			$txtlightbox = $this->translate($txtlightbox, $lang);
			$txt_driving = $this->translate($txt_driving, $lang);
			$txt_avhighways = $this->translate($txt_avhighways, $lang);
			$txt_walking = $this->translate($txt_walking, $lang);
			$langanim = $this->translate($langanim, $lang);
			$langanim = explode("|", $langanim);
	
			// Next parameters can be set as default out of the administrtor module or stay empty and the plugin-code decides the default. 
			$zoomType = $params->get( 'zoomType', '' );
			$mapType = $params->get( 'mapType', '' );
	
			// default empty and should be filled as a parameter with the plugin out of the content item
			$code='';
			$lbcode='';
			$mapclass='';
			$tolat='';
			$tolon='';
			$toaddress='';
			$text='';
			$tooltip='';
			$kml = array();
			$layer = array();
			$lookat = array();
			$msid='';
			$client_geo = 0;
			$show = 1;
			$imageurl='';
			$imagex='';
			$imagey='';
			$imagexyunits='';
			$imagewidth='';
			$imageheight='';
			$imageanchorx='';
			$imageanchory='';
			$imageanchorunits='';
			$searchtext='';
			$latitude='';
			$longitude='';
			$waypoints = array();

			// Give the map a random name so it won't interfere with another map
			$mapnm = $id."_".$this->randomkeys(5)."_".$counter;
			// Protocol not working with maps.google.com only with enterprise account
//			if ($_SERVER['SERVER_PORT'] == 443)
//				$protocol = "https://";
//			else
				$protocol = "http://";
			
			$mosmap=$matches[0][$counter][0];
	
			//track if coordinates different from config
			$inline_coords = 0;
			$inline_tocoords = 0;

			// Match the field details to build the html
			preg_match($singleregex,$mosmap,$mosmapparsed);

			$fields = explode("|", $mosmapparsed[$countmatch]);

			foreach($fields as $value)
			{
				$value=trim($value);
				$values = explode("=",$value, 2);
				$values[0] = trim(strtolower($values[0]));
				$values=preg_replace("/^'/", '', $values);
				$values=preg_replace("/'$/", '', $values);
				$values=preg_replace("/^&#0{0,2}39;/",'',$values);
				$values=preg_replace("/&#0{0,2}39;$/",'',$values);

				if (count($values)>1)
					$values[1] = trim($values[1]);

				if($values[0]=='debug'){
					$this->debug_plugin=$values[1];
				}else if($values[0]=='publ'){
					$published=$values[1];
				}else if($values[0]=='mapname'){
					$mapnm=$values[1];
				}else if($values[0]=='mapclass'){
					$mapclass=$values[1];
				}else if($values[0]=='googleindexing'){
					$googleindexing=$values[1];
				}else if($values[0]=='width'){
					$width=$values[1];
				}else if($values[0]=='height'){
					$height=$values[1];
				}else if($values[0]=='lat'&&$values[1]!=''){
					$latitude=$this->remove_html_tags($values[1]);
					$inline_coords = 1;
				}else if($values[0]=='lon'&&$values[1]!=''){
					$longitude=$this->remove_html_tags($values[1]);
					$inline_coords = 1;
				}else if($values[0]=='centerlat'){
					$centerlat=$this->remove_html_tags($values[1]);
					$inline_coords = 1;
				}else if($values[0]=='centerlon'){
					$centerlon=$this->remove_html_tags($values[1]);
					$inline_coords = 1;
				}else if($values[0]=='tolat'){
					$tolat=$this->remove_html_tags($values[1]);
					$inline_tocoords = 1;
				}else if($values[0]=='tolon'){
					$tolon=$this->remove_html_tags($values[1]);
					$inline_tocoords = 1;
				}else if($values[0]=='zoom'){
					$zoom=$values[1];
				}else if($values[0]=='key'){
					$key=$values[1];
				}else if($values[0]=='controltype'){
					$controltype=$values[1];
				}else if($values[0]=='keyboard'){
					$keyboard=$values[1];
				}else if($values[0]=='zoomtype'){
					$zoomType=$values[1];
				}else if($values[0]=='rotation'){
					$rotation=$values[1];
				}else if($values[0]=='text'){
					$text=html_entity_decode(html_entity_decode(trim($values[1])));
					$text=str_replace("\"","\\\"", $text);
					$text=str_replace("&#0{0,2}39;","'", $text);
				}else if($values[0]=='tooltip'){
					$tooltip=trim($values[1]);
					$tooltip=str_replace("&amp;","&", $tooltip);
				}else if($values[0]=='maptype'){
					$mapType=$values[1];
				}else if($values[0]=='showmaptype'){
					$showmaptype=$values[1];
				}else if($values[0]=='showterrainmaptype'){
					$showterrainmaptype=$values[1];
				}else if($values[0]=='showearthmaptype'){
					$showearthmaptype=$values[1];
				}else if($values[0]=='showscale'){
					$showscale=$values[1];
				}else if($values[0]=='zoomnew'){
					$zoom_new=$values[1];
				}else if($values[0]=='zoomwheel'){
					$zoom_wheel=$values[1];
				}else if($values[0]=='overview'){
					$overview=$values[1];
				}else if($values[0]=='navlabel'){
					$navlabel=$values[1];
				}else if($values[0]=='dragging'){
					$dragging=$values[1];
				}else if($values[0]=='marker'){
					$marker=$values[1];
				}else if($values[0]=='icon'){
					$icon=$values[1];
				}else if($values[0]=='iconwidth'){
					$iconwidth=$values[1];
				}else if($values[0]=='iconheight'){
					$iconheight=$values[1];
				}else if($values[0]=='iconshadow'){
					$iconshadow=$values[1];
				}else if($values[0]=='iconshadowwidth'){
					$iconshadowwidth=$values[1];
				}else if($values[0]=='iconshadowheight'){
					$iconshadowheight=$values[1];
				}else if($values[0]=='iconshadowanchorx'){
					$iconshadowanchorx=$values[1];
				}else if($values[0]=='iconshadowanchory'){
					$iconshadowanchory=$values[1];
				}else if($values[0]=='iconanchorx'){
					$iconanchorx=$values[1];
				}else if($values[0]=='iconanchory'){
					$iconanchory=$values[1];
				}else if($values[0]=='iconinfoanchorx'){
					$iconinfoanchorx=$values[1];
				}else if($values[0]=='iconinfoanchory'){
					$iconinfoanchory=$values[1];
				}else if($values[0]=='icontransparent'){
					$icontransparent=$values[1];
				}else if($values[0]=='iconimagemap'){
					$iconimagemap=$values[1];
				}else if($values[0]=='address'){
					$address=trim($values[1]);
				}else if($values[0]=='toaddress'){
					$toaddress=trim($values[1]);
				}else if(($brackets=='both'||$brackets=='[')&&preg_match("/waypoint\([0-9]+\)/", $values[0])){
					$waypoints[$this->get_index($values[0], '(')] = $values[1];
				}else if(($brackets=='both'||$brackets=='{')&&preg_match("/waypoint\[[0-9]+\]/", $values[0])){
					$waypoints[$this->get_index($values[0], '[')] = $values[1];
				}else if($values[0]=='gotoaddr'){
					$gotoaddr=$values[1];
				}else if($values[0]=='gotoaddrzoom'){
					$gotoaddrzoom=$values[1];
				}else if($values[0]=='align'){
					$align=$values[1];
				}else if($values[0]=='lang'){
					$lang=$values[1];
				}else if($values[0]=='kml'){
					$kml[0]=$this->remove_html_tags($values[1]);
				}else if(($brackets=='both'||$brackets=='[')&&preg_match("/kml\([0-9]+\)/", $values[0])){
					$kml[$this->get_index($values[0], '(')] = $this->remove_html_tags($values[1]);
				}else if(($brackets=='both'||$brackets=='{')&&preg_match("/kml\[[0-9]+\]/", $values[0])){
					$kml[$this->get_index($values[0], '[')] = $this->remove_html_tags($values[1]);
				}else if($values[0]=='layer'){
					$layer[0]=$this->remove_html_tags($values[1]);
				}else if(($brackets=='both'||$brackets=='[')&&preg_match("/layer\([0-9]+\)/", $values[0])){
					$layer[$this->get_index($values[0], '(')] = $this->remove_html_tags($values[1]);
				}else if(($brackets=='both'||$brackets=='{')&&preg_match("/layer\[[0-9]+\]/", $values[0])){
					$layer[$this->get_index($values[0], '[')] = $this->remove_html_tags($values[1]);
				}else if($values[0]=='msid'){
					$msid=$values[1];
				}else if($values[0]=='traffic'){
					$traffic=$values[1];
				}else if($values[0]=='panoramio'){
					$panoramio=$values[1];
				}else if($values[0]=='panotype'){
					$panotype=$values[1];
				}else if($values[0]=='panoorder'){
					$panoorder=$values[1];
				}else if($values[0]=='panomax'){
					$panomax=$values[1];
				}else if($values[0]=='youtube'){
					$youtube=$values[1];
				}else if($values[0]=='wiki'){
					$wiki=$values[1];
				}else if($values[0]=='adsmanager'){
					$adsmanager=$values[1];
				}else if($values[0]=='maxads'){
					$maxads=$values[1];
				}else if($values[0]=='localsearch'){
					$localsearch=$values[1];
				}else if($values[0]=='adsense'){
					$adsense=$values[1];
				}else if($values[0]=='channel'){
					$channel=$values[1];
				}else if($values[0]=='googlebar'){
					$googlebar=$values[1];
				}else if($values[0]=='searchtext'){
					$searchtext=$values[1];
				}else if($values[0]=='searchlist'){
					$searchlist=$values[1];
				}else if($values[0]=='searchtarget'){
					$searchtarget=$values[1];
				}else if($values[0]=='searchzoompan'){
					$searchzoompan=$values[1];
				}else if($values[0]=='dir'){
					$dir=$values[1];
				}else if($values[0]=='dirtype'){
					$dirtype=$values[1];
				}else if($values[0]=='avoidhighways'){
					$avoidhighways=$values[1];
				}else if($values[0]=='showdir'){
					$showdir=$values[1];
				}else if($values[0]=='animdir'){
					$animdir=$values[1];
				}else if($values[0]=='animspeed'){
					$animspeed=$values[1];
				}else if($values[0]=='animautostart'){
					$animautostart=$values[1];
				}else if($values[0]=='animunit'){
					$animunit=$values[1];
				}else if($values[0]=='formspeed'){
					$formspeed=$values[1];
				}else if($values[0]=='formdirtype'){
					$formdirtype=$values[1];
				}else if($values[0]=='formaddress'){
					$formaddress=$values[1];
				}else if($values[0]=='txt_get_dir'){
					$txt_get_dir=$values[1];
				}else if($values[0]=='txt_from'){
					$txt_from=$values[1];
				}else if($values[0]=='txt_to'){
					$txt_to=$values[1];
				}else if($values[0]=='txt_diraddr'){
					$txt_diraddr=$values[1];
				}else if($values[0]=='txt_dir'){
					$txt_dir=$values[1];
				}else if($values[0]=='lightbox'){
					$lightbox=$values[1];
				}else if($values[0]=='lbxwidth'){
					$lbxwidth=$values[1];
				}else if($values[0]=='lbxheight'){
					$lbxheight=$values[1];
				}else if($values[0]=='lbxcaption'){
					$lbxcaption=$values[1];
				}else if($values[0]=='txtlightbox'){
					$txtlightbox=$values[1];
				}else if($values[0]=='lbxcenterlat'){
					$lbxcenterlat=$values[1];
				}else if($values[0]=='lbxcenterlon'){
					$lbxcenterlon=$values[1];
				}else if($values[0]=='lbxzoom'){
					$lbxzoom=$values[1];
				}else if($values[0]=='show'){
					$show=$values[1];
				}else if($values[0]=='imageurl'){
					$imageurl=$values[1];
				}else if($values[0]=='imagex'){
					$imagex=$values[1];
				}else if($values[0]=='imagey'){
					$imagey=$values[1];
				}else if($values[0]=='imagexyunits'){
					$imagexyunits=$values[1];
				}else if($values[0]=='imagewidth'){
					$imagewidth=$values[1];
				}else if($values[0]=='imageheight'){
					$imageheight=$values[1];
				}else if($values[0]=='imageanchorx'){
					$imageanchorx=$values[1];
				}else if($values[0]=='imageanchory'){
					$imageanchory=$values[1];
				}else if($values[0]=='imageanchorunits'){
					$imageanchorunits=$values[1];
				}else if($values[0]=='kmlrenderer'){
					$kmlrenderer=$values[1];
				}else if($values[0]=='kmlsidebar'){
					$kmlsidebar=$values[1];
				}else if($values[0]=='kmllightbox'){
					$kmllightbox=$values[1];
				}else if($values[0]=='kmlsbwidth'){
					$kmlsbwidth=$values[1];
				}else if($values[0]=='kmlfoldersopen'){
					$kmlfoldersopen=$values[1];
				}else if($values[0]=='kmlopenmethod'){
					$kmlopenmethod=$values[1];
				}else if($values[0]=='kmlsbsort'){
					$kmlsbsort=$values[1];
				}else if($values[0]=='kmlmessshow'){
					$kmlmessshow =$values[1];
				}else if($values[0]=='kmlclickablemarkers'){
					$kmlclickablemarkers =$values[1];
				}else if($values[0]=='kmlcontentlinkmarkers'){
					$kmlcontentlinkmarkers =$values[1];
				}else if($values[0]=='kmllinkablemarkers'){
					$kmllinkablemarkers =$values[1];
				}else if($values[0]=='kmllinktarget'){
					$kmllinktarget =$values[1];
				}else if($values[0]=='kmllinkmethod'){
					$kmllinkmethod =$values[1];
				}else if($values[0]=='kmlmarkerlabel'){
					$kmlmarkerlabel =$values[1];
				}else if($values[0]=='kmlmarkerlabelclass'){
					$kmlmarkerlabelclass =$values[1];
				}else if($values[0]=='kmlpolylabel'){
					$kmlpolylabel =$values[1];
				}else if($values[0]=='kmlpolylabelclass'){
					$kmlpolylabelclass =$values[1];
				}else if($values[0]=='sv'){
					$sv =$values[1];
				}else if($values[0]=='svwidth'){
					$svwidth =$values[1];
				}else if($values[0]=='svheight'){
					$svheight=$values[1];
				}else if($values[0]=='svyaw'){
					$svyaw=$values[1];
				}else if($values[0]=='svpitch'){
					$svpitch=$values[1];
				}else if($values[0]=='svzoom'){
					$svzoom=$values[1];
				}else if($values[0]=='lookat'){
					$lookat[0]=$values[1];
				}else if(($brackets=='both'||$brackets=='[')&&preg_match("/lookat\([0-9]+\)/", $values[0])){
					$lookat[$this->get_index($values[0], '(')] = $values[1];
				}else if(($brackets=='both'||$brackets=='{')&&preg_match("/lookat\[[0-9]+\]/", $values[0])){
					$lookat[$this->get_index($values[0], '[')] = $values[1];
				}else if($values[0]=='earthbuildings'){
					$earthbuildings=$values[1];
				}else if($values[0]=='earthborders'){
					$earthborders=$values[1];
				}else if($values[0]=='earthroads'){
					$earthroads=$values[1];
				}else if($values[0]=='earthterrain'){
					$earthterrain=$values[1];
				}
			}
			
			if (!$published )
			{
				$row->text = str_replace($mosmap, $code, $row->text);
			} else {
				$this->debug_log("Parameters: ");
				$this->debug_log("- debug: ".$this->debug_plugin);
				$this->debug_log("- dir: ".$dir);
				$this->debug_log("- text: ".$text);
				$this->debug_log("- icon: ".$icon);
				$this->debug_log("- iconwidth: ".$iconwidth);
				$this->debug_log("- iconheight: ".$iconheight);
				$this->debug_log("- iconinfoanchory: ".$iconinfoanchory);
				$this->debug_log("- searchlist: ".$searchlist);
				$this->debug_log("- searchzoompan: ".$searchzoompan);
				$this->debug_log("- kmlrenderer: ".$kmlrenderer);
				$this->debug_log("- kmlmessshow: ".$kmlmessshow);
				
				if($inline_coords == 0 && !empty($address))
				{
					if ($clientgeotype=="local")
						$coord = "";
					else
						$coord = $this->get_geo($protocol, $googlewebsite, $address, $key);
						
					if ($coord=='') {
						$client_geo = 1;
					} else {
						list ($longitude, $latitude, $altitude) = explode(",", $coord);
						$inline_coords = 1;
					}
				}

				if($inline_tocoords == 0 && !empty($toaddress))
				{
					$tocoord = $this->get_geo($protocol, $googlewebsite, $toaddress, $key);
					if ($tocoord=='') {
						$client_togeo = 1;
					} else {
						list ($tolon, $tolat, $altitude) = explode(",", $tocoord);
						$inline_tocoords = 1;
					}
				}
	
				if (is_numeric($svwidth))
				{
					$svwidth .= "px";
				}
				if (is_numeric($svheight))
				{
					$svheight.= "px";
				}
				if (is_numeric($kmlsbwidth))
				{
					$kmlsbwidthorig = $kmlsbwidth;
					$kmlsbwidth .= "px";
				} else 
					$kmlsbwidthorig = 0;
				$lbxwidthorig = $lbxwidth;
				if (is_numeric($lbxwidth))
				{
					$lbxwidth .= "px";
				}
				
				if (is_numeric($lbxheight))
				{
					$lbxheight .= "px";
				}
				if (is_numeric($width))
				{
					$width .= "px";
				}
				if (is_numeric($height))
				{
					$height .= "px";
				}
				if (!is_numeric($panomax))
				{
					$panomax= "50";
				}
				
				if ($msid!=''&&count($kml)==0) {
					$kml[0]=$protocol.$googlewebsite.'/maps/ms?';
					if ($lang!='')
						$kml[0] .= "hl=".$lang."&amp;";
					$kml[0].='ie='.$iso.'&amp;msa=0&amp;msid='.$msid.'&amp;output=kml';
					$this->debug_log("- msid: ".$kml[0]);
				}
				
				if ($googlebar=='1'||$localsearch=='1') {
					$searchoption = array();
	
					switch ($searchlist) {
					case "suppress":
						$searchoption[] ="resultList : G_GOOGLEBAR_RESULT_LIST_SUPPRESS";
						break;
					
					case "inline":
						$searchoption[] ="resultList : G_GOOGLEBAR_RESULT_LIST_INLINE";
						break;

					case "div":
						$searchoption[] ="resultList : document.getElementById('searchresult".$mapnm."')";
						break;
	
					default:
						if(empty($searchlist))
							$searchoption[] ="resultList : G_GOOGLEBAR_RESULT_LIST_INLINE";
						else {
							$searchoption[] ="resultList : document.getElementById('".$searchlist."')";
							$extsearchresult= true;
						}
						break;
					}
					
					switch ($searchtarget) {
					case "_self":
						$searchoption[] ="linkTarget : G_GOOGLEBAR_LINK_TARGET_SELF";
						break;
					
					case "_blank":
						$searchoption[] ="linkTarget : G_GOOGLEBAR_LINK_TARGET_BLANK";
						break;
	
					case "_top":
						$searchoption[] ="linkTarget : G_GOOGLEBAR_LINK_TARGET_TOP";
						break;
	
					case "_parent":
						$searchoption[] ="linkTarget : G_GOOGLEBAR_LINK_TARGET_PARENT";
						break;
	
					default:
						$searchoption[] ="linkTarget : G_GOOGLEBAR_LINK_TARGET_BLANK";
						break;
					}
					
					if ($searchzoompan=="1")
						$searchoption[] ="suppressInitialResultSelection : false
										  , suppressZoomToBounds : false";
					else

						$searchoption[] ="suppressInitialResultSelection : true
										  , suppressZoomToBounds : true";
										  
					$searchoptions = implode(', ', $searchoption);
				} else 
					$searchoptions = "";

				if ($icon!='') {
					$code .= "\n<img src='".$icon."' style='display:none' alt='icon' />";
					if ($iconshadow!='')
						$code .= "\n<img src='".$iconshadow."' style='display:none' alt='icon shadow' />";
					if ($icontransparent!='')
						$code .= "\n<img src='".$icontransparent."' style='display:none' alt='icon transparent' />";
				} 
				if ($sv!='none'&&$animdir=='0') {
					$code .= "\n<img src='http://maps.gstatic.com/mapfiles/cb/man_arrow-0.png' style='display:none' alt='streetview icon' />";
					$code .= "\n<img src='http://maps.gstatic.com/mapfiles/cb/man_arrow-1.png' style='display:none' alt='streetview icon' />";
					$code .= "\n<img src='http://maps.gstatic.com/mapfiles/cb/man_arrow-2.png' style='display:none' alt='streetview icon' />";
					$code .= "\n<img src='http://maps.gstatic.com/mapfiles/cb/man_arrow-3.png' style='display:none' alt='streetview icon' />";
					$code .= "\n<img src='http://maps.gstatic.com/mapfiles/cb/man_arrow-4.png' style='display:none' alt='streetview icon' />";
					$code .= "\n<img src='http://maps.gstatic.com/mapfiles/cb/man_arrow-5.png' style='display:none' alt='streetview icon' />";
					$code .= "\n<img src='http://maps.gstatic.com/mapfiles/cb/man_arrow-6.png' style='display:none' alt='streetview icon' />";
					$code .= "\n<img src='http://maps.gstatic.com/mapfiles/cb/man_arrow-7.png' style='display:none' alt='streetview icon' />";
					$code .= "\n<img src='http://maps.gstatic.com/mapfiles/cb/man_arrow-8.png' style='display:none' alt='streetview icon' />";
					$code .= "\n<img src='http://maps.gstatic.com/mapfiles/cb/man_arrow-9.png' style='display:none' alt='streetview icon' />";
					$code .= "\n<img src='http://maps.gstatic.com/mapfiles/cb/man_arrow-10.png' style='display:none' alt='streetview icon' />";
					$code .= "\n<img src='http://maps.gstatic.com/mapfiles/cb/man_arrow-11.png' style='display:none' alt='streetview icon' />";
					$code .= "\n<img src='http://maps.gstatic.com/mapfiles/cb/man_arrow-12.png' style='display:none' alt='streetview icon' />";
					$code .= "\n<img src='http://maps.gstatic.com/mapfiles/cb/man_arrow-13.png' style='display:none' alt='streetview icon' />";
					$code .= "\n<img src='http://maps.gstatic.com/mapfiles/cb/man_arrow-14.png' style='display:none' alt='streetview icon' />";
					$code .= "\n<img src='http://maps.gstatic.com/mapfiles/cb/man_arrow-15.png' style='display:none' alt='streetview icon' />";
				}
				// Generate the map position prior to any Google Scripts so that these can parse the code
				$code.= "<!-- fail nicely if the browser has no Javascript -->
						<noscript><blockquote class='warning'><p>".$no_javascript."</p></blockquote></noscript>";
						
				if ($align!='none')
					$code.="<div style=\"text-align:".$align."\">";

				if ($lightbox=='1') {
					$lboptions = array();
					if (!empty($lbxzoom))
						$lboptions[] = "zoom : ".$lbxzoom;
					if (!empty($lbxcenterlat)&&!empty($lbxcenterlon))
						$lboptions[] = "mapcenter : \"".$lbxcenterlat." ".$lbxcenterlon."\"";

					$lbxwidthorig = (is_numeric($lbxwidthorig)?(($kmlsidebar=="left"||$kmlsidebar=="right")?$lbxwidthorig+$kmlsbwidthorig+5:$lbxwidthorig)."px":$lbxwidthorig);
					$lbname = (($gotoaddr=='1'||$kmlsidebar=="left"||$kmlsidebar=="right"||$animdir!='0'||$sv=='top'||$sv=='bottom'||$searchlist=='div'||$dir=='5')?"lightbox":"googlemap");
					
					if ($show==1) {
						$code.="<a href='javascript:void(0)' onclick='javascript:MOOdalBox.open(\"".$lbname.$mapnm."\", \"".$lbxcaption."\", \"".$lbxwidthorig." ".$lbxheight."\", map".$mapnm.", {".implode(",",$lboptions)."});return false;' class='lightboxlink'>".$txtlightbox."</a>";
					} else {
						$lbcode.="<a href='javascript:void(0)' onclick='javascript:MOOdalBox.open(\"".$lbname.$mapnm."\", \"".$lbxcaption."\", \"".$lbxwidthorig." ".$lbxheight."\", map".$mapnm.", {".implode(",",$lboptions)."});return false;' class='lightboxlink'>".$txtlightbox."</a>";
					}
					$code .= "<div id='lightbox".$mapnm."'>";
				}
				
				if ($gotoaddr=='1')
				{
					$code.="<form name=\"gotoaddress".$mapnm."\" class=\"gotoaddress\" onSubmit=\"javascript:gotoAddress".$mapnm."();return false;\">";
					$code.="	<input id=\"txtAddress".$mapnm."\" name=\"txtAddress".$mapnm."\" type=\"text\" size=\"25\" value=\"\">";
					$code.="	<input name=\"goto\" type=\"button\" class=\"button\" onClick=\"gotoAddress".$mapnm."();return false;\" value=\"Goto\">";
					$code.="</form>";
				}

				if ($kmlrenderer!="google"&&($kmlsidebar=="left"||$kmlsidebar=="right"))
					$code.="<table style=\"width:100%;border-spacing:0px;\">
							<tr>";

				if ($kmlrenderer!="google"&&$kmlsidebar=="left")
					$code.="<td style=\"width:".$kmlsbwidth.";height:".$height.";vertical-align:top;\"><div id=\"kmlsidebar".$mapnm."\" style=\"align:left;width:".$kmlsbwidth.";height:".$height.";overflow:auto;\"></div></td>";

				if ($kmlrenderer!="google"&&($kmlsidebar=="left"||$kmlsidebar=="right"))
					$code.="<td>";
					
				if ($sv=='top'||($animdir!='0'&&$animdir!='3')) {
					$code.="<div id='svpanel".$mapnm."' class='svPanel' style='width:".$svwidth."; height:".$svheight."'><div id='svpanorama".$mapnm."' class='streetview' style='width:".$svwidth."; height:".$svheight.(($kmlsidebar=="right")?"float:left;":"").";'></div>";

					if ($animdir!='0') {
						$code.="<div id='status".$mapnm."' class='status' style='top: -".floor($svheight/2)."px'><b>Loading</b></div><div id='instruction".$mapnm."' class='instruction'></div></div><div id='progressBorder".$mapnm."' class='progressBorder'><div id='progressBar".$mapnm."' class='progressBar'></div></div>";
						$code.= "<div class='animforms'>";
						$code.= "<div class='animbuttonforms'><input type='button' value='Drive' id='stopgo".$mapnm."'  onclick='route".$mapnm.".startDriving()'  disabled='disabled' /></div>";

						if ($formspeed==1)
							$code.= "<div class='animformspeed'>
										<div class='animlabel'>".((array_key_exists(16, $langanim))?$langanim[16]:"Drive")."</div>
										<select id='speed".$mapnm."' onchange='route".$mapnm.".setSpeed()'>
											<option value='0'>".((array_key_exists(17, $langanim))?$langanim[17]:"Fast")."</option>
											<option value='1' selected='selected'>".((array_key_exists(18, $langanim))?$langanim[18]:"Normal")."</option>
											<option value='2'>".((array_key_exists(19, $langanim))?$langanim[19]:"Slow")."</option>
										</select>
									</div>";

						if ($formdirtype==1)
							$code.= "<div class='animformdirtype'>
										<input ".(($txt_driving=='')?"type='hidden' ":"type='radio' ")."class='radio' name='dirflg".$mapnm."' value='' ".(($dirtype=="D")?"checked='checked'":"")." />".$txt_driving.(($txt_driving!='')?"&nbsp;":"")."<br />
										<input ".(($txt_avhighways=='')?"type='hidden' ":"type='radio' ")."class='radio' name='dirflg".$mapnm."' value='h' ".(($avoidhighways=='1')?"checked='checked'":"")." />".$txt_avhighways.(($txt_avhighways!='')?"&nbsp;":"")."<br />
										<input ".(($txt_walking=='')?"type='hidden' ":"type='radio' ")."class='radio' name='dirflg".$mapnm."' value='w' ".(($dirtype=="W")?"checked='checked'":"")." />".$txt_walking.(($txt_walking!='')?"&nbsp;":"")."<br />
									</div>";

						if ($formaddress==1)
							$code.= "<div class='animformaddress'>
										".(($txt_from=='')?"":"<div class='animlabel'>".$txt_from."</div>")."
										<div class='animinput'><input id='from".$mapnm."' ".(($txt_from=='')?"type='hidden' ":"")." size='30' value='".$address."'/></div>
										<div style='clear: both;'></div>
										".(($txt_to=='')?"":"<div class='animlabel'>".$txt_to."</div>")."
										<div class='animinput'><input id='to".$mapnm."' ".(($txt_to=='')?"type='hidden' ":"")." size='30' value='".$toaddress."'/></div>
									</div>
									<div class='animbuttons'>
										<input type='button' value='".((array_key_exists(15, $langanim))?$langanim[15]:"Route")."' class='animroute' onclick='route".$mapnm.".generateRoute()' />
									</div>
									";
					}
					$code.="<div style=\"clear: both;\"></div>";
					$code.="</div>";
				}

				if (($animdir=='2'||$animdir=='3')&&$showdir!='0') {
					$code.="<table style=\"width:".$width.";\"><tr>";
					$code.="<td style='width:50%;'><div id=\"googlemap".$mapnm."\" ".((!empty($mapclass))?"class=\"".$mapclass."\"" :"class=\"map\"")." style=\"" . ($align != 'none' ? ($align == 'center' || $align == 'left' ? 'margin-right: auto; ' : '') . ($align == 'center' || $align == 'right' ? 'margin-left: auto; ' : '') : '') . "width:100%; height:".$height.";".(($show==0)?"display:none;":"").(($kmlsidebar=="right"||$animdir=='2')?"float:left;":"")."\"></div></td>";
					$code.= "<td style='width:50%;'><div id=\"dirsidebar".$mapnm."\" class='directions' style='float:left;width:100%;height: ".$height.";overflow:auto; '></div></td>";				
					$code.="</tr></table>";
				} else {
					$code.="<div id=\"googlemap".$mapnm."\" ".((!empty($mapclass))?"class=\"".$mapclass."\"" :"class=\"map\"")." style=\"" . ($align != 'none' ? ($align == 'center' || $align == 'left' ? 'margin-right: auto; ' : '') . ($align == 'center' || $align == 'right' ? 'margin-left: auto; ' : '') : '') . "width:".$width."; height:".$height.";".(($show==0)?"display:none;":"").(($kmlsidebar=="right"||$animdir=='2')?"float:left;":"")."\"></div>";
				}
							
				if ($sv=='bottom'||$animdir=="3") {
					if ($animdir=='3') {
						$code.="<div id='progressBorder".$mapnm."' class='progressBorder'><div id='progressBar".$mapnm."' class='progressBar'></div></div>";
						$code.= "<div class='animforms'>";
						$code.= "<div class='animbuttonforms'><input type='button' value='Drive' id='stopgo".$mapnm."'  onclick='route".$mapnm.".startDriving()'  disabled='disabled' /></div>";

						if ($formspeed==1)
							$code.= "<div class='animformspeed'>
										<div class='animlabel'>".((array_key_exists(16, $langanim))?$langanim[16]:"Drive")."</div>
										<select id='speed".$mapnm."' onchange='route".$mapnm.".setSpeed()'>
											<option value='0'>".((array_key_exists(17, $langanim))?$langanim[17]:"Fast")."</option>
											<option value='1' selected='selected'>".((array_key_exists(18, $langanim))?$langanim[18]:"Normal")."</option>
											<option value='2'>".((array_key_exists(19, $langanim))?$langanim[19]:"Slow")."</option>
										</select>
									</div>";

						if ($formdirtype==1)
							$code.= "<div class='animformdirtype'>
										<input ".(($txt_driving=='')?"type='hidden' ":"type='radio' ")."class='radio' name='dirflg".$mapnm."' value='' ".(($dirtype=="D")?"checked='checked'":"")." />".$txt_driving.(($txt_driving!='')?"&nbsp;":"")."<br />
										<input ".(($txt_avhighways=='')?"type='hidden' ":"type='radio' ")."class='radio' name='dirflg".$mapnm."' value='h' ".(($avoidhighways=='1')?"checked='checked'":"")." />".$txt_avhighways.(($txt_avhighways!='')?"&nbsp;":"")."<br />
										<input ".(($txt_walking=='')?"type='hidden' ":"type='radio' ")."class='radio' name='dirflg".$mapnm."' value='w' ".(($dirtype=="W")?"checked='checked'":"")." />".$txt_walking.(($txt_walking!='')?"&nbsp;":"")."<br />
									</div>";

						if ($formaddress==1)
							$code.= "<div class='animformaddress'>
										".(($txt_from=='')?"":"<div class='animlabel'>".$txt_from."</div>")."
										<div class='animinput'><input id='from".$mapnm."' ".(($txt_from=='')?"type='hidden' ":"")." size='30' value='".$address."'/></div>
										<div style='clear: both;'></div>
										".(($txt_to=='')?"":"<div class='animlabel'>".$txt_to."</div>")."
										<div class='animinput'><input id='to".$mapnm."' ".(($txt_to=='')?"type='hidden' ":"")." size='30' value='".$toaddress."'/></div>
									</div>
									<div class='animbuttons'>
										<input type='button' value='".((array_key_exists(15, $langanim))?$langanim[15]:"Route")."' class='animroute' onclick='route".$mapnm.".generateRoute()' />
									</div>
									";
					}
					$code.="<div style=\"clear: both;\"></div>";
					$code.="</div>";
					$code.="<div id='svpanel".$mapnm."' class='svPanel' style='width:".$svwidth."; height:".$svheight."'><div id='svpanorama".$mapnm."' class='streetview' style='width:".$svwidth."; height:".$svheight.(($kmlsidebar=="right")?"float:left;":"").";'></div>";
					if ($animdir!='0')
						$code.="<div id='status".$mapnm."' class='status' style='top: -".floor($svheight/2)."px'><b>Loading</b></div><div id='instruction".$mapnm."' class='instruction'></div></div>";
				}

				if ($kmlrenderer!="google"&&($kmlsidebar=="left"||$kmlsidebar=="right"))
					$code.="</td>";
				
				if ($kmlrenderer!="google"&&$kmlsidebar=="right")
					$code.="<td style=\"width:".$kmlsbwidth.";height:".$height.";vertical-align:top;\"><div id=\"kmlsidebar".$mapnm."\" style=\"align:left;width:".$kmlsbwidth.";height:".$height.";overflow:auto;\"></div></td>";
					
				if ($kmlrenderer!="google"&&($kmlsidebar=="left"||$kmlsidebar=="right"))
					$code.="</tr>
							</table>";

				if ($searchlist=='div') {
					$code.="<div id=\"searchresult".$mapnm."\"></div>";
				}
				if ($kmlsidebar=="left"||$kmlsidebar=="right")
					$code.="<div style=\"clear: both;\"></div>";
				
				if (((!empty($tolat)&&!empty($tolon))||!empty($address)||($dir=='5'))&&($animdir!='2'||($animdir=='2'&&$showdir=='0')))
					$code.= "<div id=\"dirsidebar".$mapnm."\" class='directions' ".(($showdir=='0')?"style='display:none'":"")."></div>";

				if ($lightbox=='1') {
					$code .= "</div>";
				}


				if ($align!='none')
					$code.="</div>";
	
				// Only add the google javascript once
				if($first)
				{
					$head_pre = "<script src=\"";
					$url = $protocol.$googlewebsite."/maps?file=api&amp;v=".$google_API_version."&amp;oe=".$iso;
					if ($lang!='') 
						$url .= "&amp;hl=".$lang;
	
					$url .= "&amp;key=".$key;
					$url .= "&amp;sensor=false";
					$url .= "&amp;indexing=".(($googleindexing)?"true":"false");
					
					$head_post = "\" type=\"text/javascript\"></script>";
					$this->debug_log('Google API script');
					$this->injectCustomHeadTags($head_pre.$url.$head_post, "maps.google.com/maps?file=api", $row->text);
					$first=false;
				}
	
				if (($kmllightbox=="1"||$lightbox=="1"||$effect!="none"||$dir=="3"||$dir=="4"||strpos($text, "MOOdalBox"))&&$first_mootools) {
					if ($joomla_version< 1.5) {
						$head ="<script src='".$mosConfig_live_site."/".$plugin_path."/content/mootools/mootools-release-1.11.js' type='text/javascript'></script>";
						$this->debug_log('mootools');
						$this->injectCustomHeadTags($head, "/mootools", $row->text);
					} else 
						JHTML::_('behavior.mootools');
					$first_mootools = false;
				}
				if (($kmllightbox=="1"||$lightbox=="1"||$dir=="3"||$dir=="4"||strpos($text, "MOOdalBox"))&&$first_modalbox)	{
					$head = "<link rel='stylesheet' href='".$mosConfig_live_site."/".$plugin_path."/content/moodalbox121/css/moodalbox.css' type='text/css' /><script src='".$mosConfig_live_site."/".$plugin_path."/content/moodalbox121/js/modalbox1.2hack.js' type='text/javascript'></script>";	
					$this->debug_log('modalbox');
					$this->injectCustomHeadTags($head, "modalbox1.2hack.js", $row->text);
					$first_modalbox = false;
				}
				if (($localsearch=="1"||$client_geo==1)&&$first_localsearch) {
					$head = "<script src='".$protocol."www.google.com/uds/api?file=uds.js&amp;v=1.0&amp;key=".$key."' type='text/javascript'></script>
							<script src='".$protocol."www.google.com/uds/solutions/localsearch/gmlocalsearch.js".((!empty($adsense))?"?adsense=".$adsense:"").((!empty($channel)&&!empty($adsense))?"&amp;channel=".$channel:"")."' type='text/javascript'></script>
							<style type='text/css'>
							  @import url('".$protocol."www.google.com/uds/css/gsearch.css');
							  @import url('".$protocol."www.google.com/uds/solutions/localsearch/gmlocalsearch.css');
							</style>";
					$this->debug_log('localsearch');
					$this->injectCustomHeadTags($head, "gmlocalsearch.js", $row->text);
					$first_localsearch = false;
				}
				if ($first_kmlelabel&&(($kmlpolylabel!=""&&$kmlpolylabelclass!="")||($kmlmarkerlabel!=""&&$kmlmarkerlabelclass!=""))) {
					$head = "<script src='".$mosConfig_live_site."/".$plugin_path."/content/elabel/elabel.js' type='text/javascript'></script>";
					$this->debug_log('elabel');
					$this->injectCustomHeadTags($head, "elabel.js", $row->text);
					$first_kmlelabel = false;
				}
				if ($kmlrenderer=='geoxml'&&$first_kmlrenderer) {
					$head = "<script src='".$mosConfig_live_site."/".$plugin_path."/content/geoxml/geoxml.js' type='text/javascript'></script>";
					$this->debug_log('geoxml');
					$this->injectCustomHeadTags($head, "geoxml.js", $row->text);
					$first_kmlrenderer = false;
				}
				if ($zoomType=='3D-largeSV'&&$first_svcontrol) {
					$head = "<script src='".$mosConfig_live_site."/".$plugin_path."/content/StreetViewControl/StreetViewControl.js' type='text/javascript'></script>";
					$this->debug_log('streeviewcontrol');
					$this->injectCustomHeadTags($head, "StreetViewControl.js", $row->text);
					$first_svcontrol = false;
				}
				if ($animdir!='0'&&$first_animdir) {
					$head = "<link rel='stylesheet' href='".$mosConfig_live_site."/".$plugin_path."/content/directions/directions.css' type='text/css' /><script src='".$mosConfig_live_site."/".$plugin_path."/content/directions/directions.js' type='text/javascript'></script>";
					$this->debug_log('directions');
					$this->injectCustomHeadTags($head, "directions.js", $row->text);
					$first_animdir = false;
				}
				if ($kmlrenderer=='arcgis'&&$first_arcgis) {
					$head = "<script src='http://serverapi.arcgisonline.com/jsapi/gmaps/?v=1.4' type='text/javascript' ></script>";
					$this->debug_log('arcgis');
					$this->injectCustomHeadTags($head, "serverapi.arcgisonline.com", $row->text);
					$first_arcgis = false;
				}
				if ($panotype!='none'&&$first_panoramiolayer) {
					$head = "<script src='".$mosConfig_live_site."/".$plugin_path."/content/panoramiolayer/panoramiolayer.js' type='text/javascript' ></script>";
					$this->debug_log('panoramiolayer');
					$this->injectCustomHeadTags($head, "panoramiolayer.js", $row->text);
					$first_panoramiolayer = false;
				}
				
				$code.="<script type='text/javascript'>//<![CDATA[\n";
				if ($this->debug_plugin=="1")
					$code.="function VersionControl(opt_no_style){
							  this.noStyle = opt_no_style;
							};
							VersionControl.prototype = new GControl();
							VersionControl.prototype.initialize = function(map) {
							  var display = document.createElement('div');
							  map.getContainer().appendChild(display);
							  display.innerHTML = '2.'+G_API_VERSION;
							  display.className = 'api-version-display';
							  if(!this.noStyle){
								display.style.fontFamily = 'Arial, sans-serif';
								display.style.fontSize = '11px';
							  }
							  this.htmlElement = display;
							  return display;
							}
							VersionControl.prototype.getDefaultPosition = function() {
							  return new GControlPosition(G_ANCHOR_BOTTOM_LEFT, new GSize(3, 38));
							}
						";

				// Globale map variable linked to the div
				$code.="var tst".$mapnm."=document.getElementById('googlemap".$mapnm."');
				var tstint".$mapnm.";
				var map".$mapnm.";
				var mySlidemap".$mapnm.";
				var overviewmap".$mapnm.";
				var overmap".$mapnm.";
				var xml".$mapnm.";
				var imageovl".$mapnm.";
				var directions".$mapnm.";
				";
				
				if ($proxy=="1")
					$code .= "\nvar proxy = '".$mosConfig_live_site."/".$plugin_path."/content/plugin_googlemap2_proxy.php?';";

				if ($traffic=='1') 
					$code.="\nvar trafficInfo".$mapnm.";";
				if ($localsearch=='1') 
					$code.="\nvar localsearch".$mapnm.";";
				if ($adsmanager=='1') 
					$code.="\nvar adsmanager".$mapnm.";";
				if ($kmlrenderer=='geoxml') {
					$code.="\nvar exml".$mapnm.";";
					$code.="\ntop.publishdirectory = '".$mosConfig_live_site."/".$plugin_path."/content/geoxml/';";
				}
				if (count($lookat)>0)
					$code.="\nvar geplugin".$mapnm.";";
				if ($panotype!='none')
					$code.="\nvar panoLayer".$mapnm.";";

				if ($icon!='') {
					$code.="\nmarkericon".$mapnm." = new GIcon(G_DEFAULT_ICON);";
					$code.="\nmarkericon".$mapnm.".image = '".$icon."';";
					if ($iconwidth!=''&&$iconheight!='')
						$code.="\nmarkericon".$mapnm.".iconSize = new GSize(".$iconwidth.", ".$iconheight.");";
					if ($iconshadow !='') {
						$code.="\nmarkericon".$mapnm.".shadow = '".$iconshadow."';";
		
						if ($iconshadowwidth!=''&&$iconshadowheight!='') 
							$code.="\nmarkericon".$mapnm.".shadowSize = new GSize(".$iconshadowwidth.", ".$iconshadowheight.");";
						if ($iconshadowanchorx!=''&&$iconshadowanchory!='')
							$code.="\nmarkericon".$mapnm.".infoShadowAnchor = new GPoint(".$iconshadowanchorx.", ".$iconshadowanchory.");";
					}
					if ($iconanchorx!=''&&$iconanchory!='')
						$code.="\nmarkericon".$mapnm.".iconAnchor = new GPoint(".$iconanchorx.", ".$iconanchory.");";
					if ($iconinfoanchorx!=''&&$iconinfoanchory!='')
						$code.="\nmarkericon".$mapnm.".infoWindowAnchor = new GPoint(".$iconinfoanchorx.", ".$iconinfoanchory.");";
					if ($icontransparent!='') 			
						$code.="\nmarkericon".$mapnm.".transparent = '".$icontransparent."';";
					if ($iconimagemap!='')
						$code.="\nmarkericon".$mapnm.".imageMap = [".$iconimagemap."];";
				}
				
				if ($sv!='none'||$animdir!='0') {
					$code.="\nvar svclient".$mapnm.";
							var svmarker".$mapnm.";
							var svlastpoint".$mapnm.";
							var svpanorama".$mapnm.";
							";
				}

				if ($animdir!='0')				
					$code.="\nvar route".$mapnm.";
							";
				
				if ($sv!='none'&&$animdir=='0') {
					$code.="\nvar guyIcon".$mapnm." = new GIcon(G_DEFAULT_ICON);
							guyIcon".$mapnm.".image = 'http://maps.gstatic.com/mapfiles/cb/man_arrow-0.png';
							guyIcon".$mapnm.".transparent = 'http://maps.gstatic.com/mapfiles/cb/man-pick.png';
							guyIcon".$mapnm.".imageMap = [26,13, 30,14, 32,28, 27,28, 28,36, 18,35, 18,27, 16,26, 16,20, 16,14, 19,13, 22,8];
							guyIcon".$mapnm.".iconSize = new GSize(49, 52);
							guyIcon".$mapnm.".iconAnchor = new GPoint(25, 35);
							guyIcon".$mapnm.".infoWindowAnchor = new GPoint(25, 5);
							";
				}
	
				if ( strpos(" ".$_SERVER['HTTP_USER_AGENT'], 'Opera') )
				{
					$code.="var _mSvgForced = true;
							var _mSvgEnabled = true; ";
				}
	
				if($zoom_wheel=='1')
				{
					$code.="function CancelEvent".$mapnm."(event) { 
								var e = event; 
								if (typeof e.preventDefault == 'function') e.preventDefault(); 
									if (typeof e.stopPropagation == 'function') e.stopPropagation(); 
		
								if (window.event) { 
									window.event.cancelBubble = true; // for IE 
									window.event.returnValue = false; // for IE 
								} 
							}
						";
				}
	
				if ($gotoaddr=='1')
				{
					$code.="function gotoAddress".$mapnm."() {
								var address = document.getElementById('txtAddress".$mapnm."').value;
	
								if (address.length > 0) {
									var geocoder = new GClientGeocoder();
									geocoder.setViewport(map".$mapnm.".getBounds());
	
									geocoder.getLatLng(address,
									function(point) {
										if (!point) {
											var erraddr = '{$erraddr}';
											erraddr = erraddr.replace(/##/, address);
										  alert(erraddr);
										} else {
										  var txtaddr = '{$txtaddr}';
										  txtaddr = txtaddr.replace(/##/, address);
										  map".$mapnm.".setCenter(point".(($gotoaddrzoom!=0)?",".$gotoaddrzoom:"").");
										  map".$mapnm.".openInfoWindowHtml(point,txtaddr);
										  setTimeout('map".$mapnm.".closeInfoWindow();', 5000);
										}
									  });
								  }
								  return false;
								  
							}";
				}
	
				if (($dir!='0')||((!empty($tolat)&&!empty($tolon))||!empty($toaddress))&&$animdir=='0') {
				    $code .="function handleErrors".$mapnm."(){
								var dirsidebar".$mapnm." = document.getElementById('dirsidebar".$mapnm."');
								var newelem = document.createElement('p');
								if (directions".$mapnm.".getStatus().code == G_GEO_UNKNOWN_ADDRESS)
									newelem.innerHTML = 'No corresponding geographic location could be found for one of the specified addresses. This may be due to the fact that the address is relatively new, or it may be incorrect.<br />Error code: ' + directions".$mapnm.".getStatus().code;
								else if (directions".$mapnm.".getStatus().code == G_GEO_SERVER_ERROR)
									newelem.innerHTML = 'A geocoding or directions request could not be successfully processed, yet the exact reason for the failure is not known.<br />Error code: ' + directions".$mapnm.".getStatus().code;
							    else if (directions".$mapnm.".getStatus().code == G_GEO_MISSING_QUERY)
									 newelem.innerHTML = 'The HTTP q parameter was either missing or had no value. For geocoder requests, this means that an empty address was specified as input. For directions requests, this means that no query was specified in the input.<br />Error code: ' + directions".$mapnm.".getStatus().code;
								//   else if (directions".$mapnm.".getStatus().code == G_UNAVAILABLE_ADDRESS)  <--- Doc bug... this is either not defined, or Doc is wrong
								//     newelem.innerHTML = 'The geocode for the given address or the route for the given directions query cannot be returned due to legal or contractual reasons.<br />Error code: ' + directions".$mapnm.".getStatus().code;
								   else if (directions".$mapnm.".getStatus().code == G_GEO_BAD_KEY)
									 newelem.innerHTML = 'The given key is either invalid or does not match the domain for which it was given.<br />Error code: ' + directions".$mapnm.".getStatus().code;
								
								   else if (directions".$mapnm.".getStatus().code == G_GEO_BAD_REQUEST)
									 newelem.innerHTML = 'A directions request could not be successfully parsed.<br />Error code: ' + directions".$mapnm.".getStatus().code;
								   else newelem.innerHTML = 'An unknown error occurred.';
								dirsidebar".$mapnm.".appendChild(newelem); 
							}
								";
					}
					
				if ($dir!='0'&&$animdir=='0') {
					$code.="\nDirectionMarkersubmit".$mapnm." = function( formObj ){
								if(formObj.dir[1].checked ){
									tmp = formObj.daddr.value;
									formObj.daddr.value = formObj.saddr.value;
									formObj.saddr.value = tmp;
								}";
					if ($dir=='1')
						$code.="\nformObj.submit();";
					elseif ($dir=='2')
						$code.="\nformObj.submit();";
					elseif ($dir=='3')
						$code.="\nfor (var i=0; i < formObj.dirflg.length; i++) {
								   if (formObj.dirflg[i].checked) {
									  var dirflg= formObj.dirflg[i].value;
									  break;
								   }
								}
								MOOdalBox.open('".$protocol."maps.google.com/maps?dir=to&dirflg='+dirflg+'&saddr='+formObj.saddr.value+'&hl=en&daddr='+formObj.daddr.value+'".(($lang!='')?"&amp;hl=".$lang:"")."&pw=2', '".$lbxcaption."', '".$lbxwidth." ".$lbxheight."', null, 16);";
					elseif ($dir=='5') 
  							$code .= "\nfor (var i=0; i < formObj.dirflg.length; i++) {
										   if (formObj.dirflg[i].checked) {
											  var dirflg= formObj.dirflg[i].value;
											  break;
										   }
										}
										var dirsidebar".$mapnm." = document.getElementById('dirsidebar".$mapnm."');
										if (directions".$mapnm.") {
											directions".$mapnm.".clear();
											if ( dirsidebar".$mapnm.".hasChildNodes() )
												{
													while ( dirsidebar".$mapnm.".childNodes.length >= 1 )
													{
														dirsidebar".$mapnm.".removeChild( dirsidebar".$mapnm.".firstChild );       
													} 
												}
										} else {
											directions".$mapnm." = new GDirections(map".$mapnm.", dirsidebar".$mapnm.");
									        GEvent.addListener(directions".$mapnm.", 'error', handleErrors".$mapnm.");
										}
										options = Array();
										if (dirflg=='w')
											options.travelMode = G_TRAVEL_MODE_WALKING;
										if (dirflg=='h')
											options.avoidHighways = true;
										directions".$mapnm.".load('from: '+formObj.saddr.value+' to: '+formObj.daddr.value, options);
									";
					else
						$code.="\nfor (var i=0; i < formObj.dirflg.length; i++) {
								   if (formObj.dirflg[i].checked) {
									  var dirflg= formObj.dirflg[i].value;
									  break;
								   }
								}
								MOOdalBox.open('".$protocol."maps.google.com/maps?dir=to&dirflg='+dirflg+'&saddr='+formObj.saddr.value+'&hl=en&daddr='+formObj.daddr.value+'".(($lang!='')?"&amp;hl=".$lang:"")."', '".$lbxcaption."', '".$lbxwidth." ".$lbxheight."', null, 16);";
						
					$code.="\nif(formObj.dir[1].checked )
								setTimeout('DirectionRevert".$mapnm."()',100);
							};";
					
					$code.="\nDirectionRevert".$mapnm." = function(){
								formObj = document.getElementById('directionform".$mapnm."');
								tmp = formObj.daddr.value;
								formObj.daddr.value = formObj.saddr.value;
								formObj.saddr.value = tmp;
							};";
				}
				
				// Function for overview
				if(!$overview==0&&$this->check_google_api_version($google_API_version, "2.93"))
				{
					$code.="\nfunction checkOverview".$mapnm."() {
						        overmap".$mapnm." = overviewmap".$mapnm.".Aa;
								if (overmap".$mapnm.") {
							";
								  
					if($overview==2)

					{
						$code.="\n		overviewmap".$mapnm.".hide(true);";
					}

					switch ($mapType) {
					case "Satellite":
					
						$code.="\n		overmap".$mapnm.".setMapType(G_SATELLITE_MAP);";
						break;
					
					case "Hybrid":
						$code.="\n		overmap".$mapnm.".setMapType(G_HYBRID_MAP);";
						break;

					case "Terrain":
						$code.="\n		overmap".$mapnm.".setMapType(G_PHYSICAL_MAP);";
						break;
					
					case "Earth":
						break;

					default:
						$code.="\n		overmap".$mapnm.".setMapType(G_NORMAL_MAP);";
						break;
					}
					
					if ($ovzoom!="") {
						$code.="\n		setTimeout('overmap".$mapnm.".setCenter(map".$mapnm.".getCenter(), map".$mapnm.".getZoom()+".$ovzoom.")', 100);";
						$code.="\n		GEvent.addListener(map".$mapnm.",'move',function() {
	  var c = Math.min(Math.max(0, map".$mapnm.".getZoom()+".$ovzoom."), 19);
	  overmap".$mapnm.".setCenter(map".$mapnm.".getCenter(), c);
        });";
						$code.="\n		GEvent.addListener(map".$mapnm.",'moveend',function() {
	  var c = Math.min(Math.max(0, map".$mapnm.".getZoom()+".$ovzoom."), 19);
	  overmap".$mapnm.".setCenter(map".$mapnm.".getCenter(), c);

        });";
					}
					$code.= "\n	} else {
								  setTimeout('checkOverview".$mapnm."()',100);
								}
							  }";
				}
		
				$code.="\nfunction initearth".$mapnm."(geplugin) {
							geplugin".$mapnm." = geplugin;";

				// Add layers
				if ($earthborders=="1")
					$code.="\n	geplugin".$mapnm.".getLayerRoot().enableLayerById(geplugin".$mapnm.".LAYER_BORDERS, true);";
				if ($earthbuildings=="1")
					$code.="\n	geplugin".$mapnm.".getLayerRoot().enableLayerById(geplugin".$mapnm.".LAYER_BUILDINGS, true);";
				else
					$code.="\n	geplugin".$mapnm.".getLayerRoot().enableLayerById(geplugin".$mapnm.".LAYER_BUILDINGS, false);";
				if ($earthroads=="1")
					$code.="\n	geplugin".$mapnm.".getLayerRoot().enableLayerById(geplugin".$mapnm.".LAYER_ROADS, true);";
				if ($earthterrain=="1")
					$code.="\n	geplugin".$mapnm.".getLayerRoot().enableLayerById(geplugin".$mapnm.".LAYER_TERRAIN, true);";
				else
					$code.="\n	geplugin".$mapnm.".getLayerRoot().enableLayerById(geplugin".$mapnm.".LAYER_TERRAIN, false);";
								
				if (count($lookat)>0)
					$code.="\n	setTimeout('setearth".$mapnm."()', ".$earthtimeout.");";
					
				$code.="\n}";
				if (count($lookat)>0) {
					$la = false;
					$cam = false;
					$code.="\nfunction setearth".$mapnm."() {
								var lookat = geplugin".$mapnm.".getView().copyAsLookAt(geplugin".$mapnm.".ALTITUDE_RELATIVE_TO_GROUND);
								var camera = geplugin".$mapnm.".getView().copyAsCamera(geplugin".$mapnm.".ALTITUDE_RELATIVE_TO_GROUND);";
								
					$values = explode(',', $lookat[0]);
					if (count($values)>0&&$values[0]!='') { // Latitude
						$code.="\nlookat.setLatitude(".$values[0].");";
						$la = true;
					}
					if (count($values)>1&&$values[1]!='') { // Longitude
						$code.="\nlookat.setLongitude(".$values[1].");";
						$la = true;
					}
					if (count($values)>2&&$values[2]!='') { // Range
						$code.="\nlookat.setRange(".$values[2].");";
						$la = true;
					}
					if (count($values)>3&&$values[3]!='') { // tilt
						$code.="\nlookat.setTilt(".$values[3].");";
						$la = true;
					}
					if (count($values)>4&&$values[4]!='') { // camera tilt
						$code.="\ncamera.setTilt(".$values[4].");";
						$cam = true;
					}
					if (count($values)>5&&$values[5]!='') { // camera roll
						$code.="\ncamera.setRoll(".$values[5].");";
						$cam = true;
					}
					if (count($values)>6&&$values[6]!='') {// flyspeed
						if ($values[6]=='teleport')
							$code.="\ngeplugin".$mapnm.".getOptions().setFlyToSpeed(geplugin".$mapnm.".SPEED_TELEPORT);";
						else
							$code.="\ngeplugin".$mapnm.".getOptions().setFlyToSpeed(".$values[6].");";
					}
							
					if ($la)
						$code.="\n	geplugin".$mapnm.".getView().setAbstractView(lookat);";
					if ($cam&&!$la)
						$code.="\n	geplugin".$mapnm.".getView().setAbstractView(camera);";
						
					$code.="\n}";
				}

				if ($kmlrenderer=='arcgis') {
					$code .="\nfunction dynmapcallback".$mapnm."(mapservicelayer) {
							      map".$mapnm.".addOverlay(mapservicelayer);
								    }";	
				}
				
				if ($kmlrenderer=='google') {
					$code .= "\nfunction savePositionKML".$mapnm."() {
									ok = true;
									for (x=0;x<xml".$mapnm.".length;x++) {
										if (!xml".$mapnm."[x].hasLoaded())
											ok = false;
									}
									if (ok)
										map".$mapnm.".savePosition();
									else
										setTimeout('savePositionKML".$mapnm."()',100);
								}
							";
				}
				
				// Functions to wacth if the map has changed
				$code.="\nfunction checkMap".$mapnm."()
				{
					if (tst".$mapnm.") {
					";
					
				if ($show!=0)
					$code.="\n			if (tst".$mapnm.".offsetWidth != tst".$mapnm.".getAttribute(\"oldValue\"))
							{
								tst".$mapnm.".setAttribute(\"oldValue\",tst".$mapnm.".offsetWidth);
								if (tst".$mapnm.".offsetWidth > 0) {
							";
	
				$code.="\n				if (tst".$mapnm.".getAttribute(\"refreshMap\")==0)
									clearInterval(tstint".$mapnm.");";
				if ($effect !='none') 
					$code .="\n					mySlidemap".$mapnm." = new Fx.Slide('googlemap".$mapnm."',{wait:true, duration: 1500, transition:Fx.Transitions.Bounce.easeOut, mode: '".$effect."'})
									mySlidemap".$mapnm.".hide();
									mySlidemap".$mapnm.".slideIn();
									mySlidemap".$mapnm.".slideOut().chain(function(){
											mySlidemap".$mapnm.".slideIn();
										});";
		
				$code .="\n					getMap".$mapnm."();
									tst".$mapnm.".setAttribute(\"refreshMap\", 1);";
				if ($show!=0)
					$code .="\n				} 
							}";
				$code .="\n	}
				}
				";

				if ($sv!="none"&&$animdir=='0') {
					$code .="function onYawChange".$mapnm."(newYaw) {
								var GUY_NUM_ICONS = 16;
								var GUY_ANGULAR_RES = 360/GUY_NUM_ICONS;
								if (newYaw < 0) {
									newYaw += 360;
								}
								var guyImageNum = Math.round(newYaw/GUY_ANGULAR_RES) % GUY_NUM_ICONS;
								var guyImageUrl = 'http://maps.gstatic.com/mapfiles/cb/man_arrow-' + guyImageNum + '.png';
								svmarker".$mapnm.".setImage(guyImageUrl);
							}

							function onNewLocation".$mapnm."(point) {
								// Get the original x + y coordinates
								svmarker".$mapnm.".setLatLng(point.latlng);
								map".$mapnm.".panTo(point.latlng);
								svlastpoint".$mapnm." = point.latlng;
							}

							function onDragEnd".$mapnm."() {
								var latlng = svmarker".$mapnm.".getLatLng();
								if (svpanorama".$mapnm.") {
									svclient".$mapnm.".getNearestPanorama(latlng, svonResponse".$mapnm.");
								}
							}

							function svonResponse".$mapnm."(response) {
								if (response.code != 200) {
									svmarker".$mapnm.".setLatLng(svlastpoint".$mapnm.");
									map".$mapnm.".setCenter(svlastpoint".$mapnm.");
								} else {
									var latlng = new GLatLng(response.Location.lat, response.Location.lng);
									svmarker".$mapnm.".setLatLng(latlng);
									svlastpoint".$mapnm." = latlng;
									svpanorama".$mapnm.".setLocationAndPOV(latlng, null);
								}
							}
							";
				}
	
				// Function for displaying the map and marker
				$code.="\nfunction getMap".$mapnm."(){";

				if ($show!=0)
					$code.="\n	if (tst".$mapnm.".offsetWidth > 0) {";
				
				$code.="\n	map".$mapnm." = new GMap2(document.getElementById('googlemap".$mapnm."')".(($googlebar=='1'&&!empty($searchoptions))?", { googleBarOptions: {".$searchoptions." } }":"").");
						map".$mapnm.".getContainer().style.overflow='hidden';
						";
				
				if ($sv!="none"||$animdir!='0')
					$code.="svclient".$mapnm." = new GStreetviewClient();";
					
				if($keyboard=='1'&&$controltype=='user')
				{
					$code.="new GKeyboardHandler(map".$mapnm.");
					";
				} 
				if($dragging=="0")
					$code.="map".$mapnm.".disableDragging();";

				if ($showterrainmaptype=="1"&&$this->check_google_api_version($google_API_version, "2.93"))
					$code.="map".$mapnm.".addMapType(G_PHYSICAL_MAP);";
				if ($showearthmaptype=="1"&&$this->check_google_api_version($google_API_version, "2.113"))
					$code.="map".$mapnm.".addMapType(G_SATELLITE_3D_MAP);";

				if(!$overview==0&&$this->check_google_api_version($google_API_version, "2.93"))
				{
					$code.="overviewmap".$mapnm." = new GOverviewMapControl();";
					$code.="map".$mapnm.".addControl(overviewmap".$mapnm.", new GControlPosition(G_ANCHOR_BOTTOM_RIGHT));";
					$code.="setTimeout('checkOverview".$mapnm."()',100);";

				} elseif (!$overview==0) {
					$code.="overviewmap".$mapnm." = new GOverviewMapControl();";
					$code.="map".$mapnm.".addControl(overviewmap".$mapnm.", new GControlPosition(G_ANCHOR_BOTTOM_RIGHT));";
					
					if($overview==2)
					{
						$code.="overviewmap".$mapnm.".hide(true);";
					}
				}

				if($navlabel == 1)
					$code.="map".$mapnm.".addControl(new GNavLabelControl(), new GControlPosition(G_ANCHOR_TOP_RIGHT, new GSize(7, 30)));";

				if($client_geo == 1) {
					if ($clientgeotype=="local") {
						$code.="\nvar localSearch = new GlocalSearch();";
						$replace = array("\n", "\r", "&lt;br/&gt;", "&lt;br /&gt;", "&lt;br&gt;");
						$addr = str_replace($replace, '', $address);
	
						$code.="\nvar address = \"".$addr."\";";
						$code.="\nlocalSearch.setSearchCompleteCallback(null,	function() {
								if (localSearch.results[0]) {
									var resultLat = localSearch.results[0].lat;
									var resultLng = localSearch.results[0].lng;
									var point = new GLatLng(resultLat,resultLng);
								} else 
								";
						if ($latitude !=''&&$longitude!='')
							$code.="var point = new GLatLng( $latitude, $longitude);";
						else
							$code.="var point = new GLatLng( $deflatitude, $deflongitude);";
					} else {
						$code.="var geocoder = new GClientGeocoder();";
						$replace = array("\n", "\r", "&lt;br/&gt;", "&lt;br /&gt;", "&lt;br&gt;");
						$addr = str_replace($replace, '', $address);
	
						$code.="var address = \"".$addr."\";";
						$code.="geocoder.getLatLng(address, function(point) {
									if (!point)";
									
						if ($latitude !=''&&$longitude!='')
							$code.="var point = new GLatLng( $latitude, $longitude);";
						else
							$code.="var point = new GLatLng( $deflatitude, $deflongitude);";
					}
				} else { 
					if ($latitude !=''&&$longitude!='')
						$code.="\nvar point = new GLatLng( $latitude, $longitude);";
					else
						$code.="\nvar point = new GLatLng( $deflatitude, $deflongitude);";
				}
				if (!empty($centerlat)&&!empty($centerlon))
					$code.="\nvar centerpoint = new GLatLng( $centerlat, $centerlon);";
				else
					$code.="\nvar centerpoint = point;";

				if ($inline_coords == 0 && count($kml)>0)
					$code.="map".$mapnm.".setCenter(new GLatLng(0, 0), 0);
					";					
				else
					$code.="map".$mapnm.".setCenter(centerpoint, ".$zoom.");
					";					
					
				if ($controltype=='user') {
					switch ($zoomType) {
						case "Large":
							$code.="map".$mapnm.".addControl(new GLargeMapControl());";
							break;
						case "Small":
							$code.="map".$mapnm.".addControl(new GSmallMapControl());";
							break;
						case "3D-large":
							$code.="map".$mapnm.".addControl(new GLargeMapControl3D());";
							if ($rotation)
								$code.="map".$mapnm.".enableRotation();";
							break;
						case "3D-largeSV":
							$code.="map".$mapnm.".addControl(new StreetViewControl());";
							if ($rotation)
								$code.="map".$mapnm.".enableRotation();";
							break;
						case "3D-small":
							$code.="map".$mapnm.".addControl(new GSmallZoomControl3D());";
							if ($rotation)
								$code.="map".$mapnm.".enableRotation();";
							break;
						default:
							break;
					}
					
					if($showmaptype!='0')
					{
						$code.="map".$mapnm.".addControl(new GMapTypeControl());";
					} 
	
					if ($showscale==1)
						$code.="map".$mapnm.".addControl(new GScaleControl());";
				} else {
					$code.="map".$mapnm.".setUIToDefault();";
					if ($rotation)
						$code.="map".$mapnm.".enableRotation();";
				}
					
					
				if (count($kml)>0) {
					switch ($kmlrenderer) {
						case "google":
						default:
							$code .= "xml".$mapnm." = [];";
							foreach ($kml as $idx => $val) {
								$code .= "var kmlurl = '".$kml[$idx]."';";
								$code .= "kmlurl = kmlurl.replace(/&amp;/g, String.fromCharCode(38));";
								$code .= "\nxml".$mapnm."[".$idx."] = new GGeoXml(kmlurl);";
								$code .= "\nmap".$mapnm.".addOverlay(xml".$mapnm."[".$idx."]);";
							}
							if ($inline_coords==0) {
								$code .= "\nxml".$mapnm."[0].gotoDefaultViewport(map".$mapnm.");";
								$code .= "\nsetTimeout('savePositionKML".$mapnm."()',100);"; 
							}

							break;
						case "arcgis":
							$code .= "var xml = [];";
							foreach ($kml as $idx => $val) {
								$code .= "var kmlurl = '".$kml[$idx]."';";
								$code .= "\nkmlurl = kmlurl.replace(/&amp;/g, String.fromCharCode(38));";
								$code .= "\nxml[".$idx."] = new esri.arcgis.gmaps.DynamicMapServiceLayer(kmlurl, null, 0.75, dynmapcallback".$mapnm.");";
							}

							break;
						case "geoxml":
							$code .= "\nvar kml".$mapnm." = [];";
							foreach ($kml as $idx => $val) {
								$code .= "\nvar kmlurl = '".$kml[$idx]."';";
								$code .= "\nkmlurl = escape(kmlurl.replace(/&amp;/g, String.fromCharCode(38)));";
								$code .= "\nkml".$mapnm.".push(kmlurl);";
							}
							$xmloptions = array();
							if ($kmlsidebar=="left"||$kmlsidebar=="right") {
								$xmloptions[] = "sidebarid: 'kmlsidebar".$mapnm."'";
							} else {
								if ($kmlsidebar!="none")
									$xmloptions[] = "sidebarid: '".$kmlsidebar."'";
							}
							if ($kmlmessshow=='1')
								$xmloptions[] = "messshow: true";
							
							if ($inline_coords==1)
								$xmloptions[] = "nozoom: true";

							if ($dir!='0') {
								$xmloptions[] = "directions: true";
							}
							if ($kmlfoldersopen!='0') {
								$xmloptions[] = "allfoldersopen: true";
							}
							if ($kmlopenmethod!='0') {
								$xmloptions[] = "iwmethod: '".$kmlopenmethod."'";
							}
							
							if ($kmlsbsort=='asc') {
								$xmloptions[] = "sortbyname: 'asc'";
							}elseif ($kmlsbsort=='desc') {
								$xmloptions[] = "sortbyname: 'desc'";
							} else 	
								$xmloptions[] = "sortbyname: 'none'";

							if ($kmlclickablemarkers!='1') {
								$xmloptions[] = "clickablemarkers: false";
							}
							if ($kmlcontentlinkmarkers!='0') {
								$xmloptions[] = "contentlinkmarkers: true";
							}
							if ($kmllinkablemarkers!='0') {
								$xmloptions[] = "linkablemarkers: true";
							}
							if ($kmllinktarget!='') {
								$xmloptions[] = "linktarget: '".$kmllinktarget."'";
							}
							if ($kmllinkmethod!='') {
								$xmloptions[] = "linkmethod: '".$kmllinkmethod."'";
							}

							if (($kmlpolylabel!=""&&$kmlpolylabelclass!="")) {
								$xmloptions[] = "polylabelopacity: '".$kmlpolylabel."'";
								$xmloptions[] = "polylabelclass: '".$kmlpolylabelclass."'";
							}

							if (($kmlmarkerlabel!=""&&$kmlmarkerlabelclass!="")) {
								$xmloptions[] = "pointlabelopacity: '".$kmlmarkerlabel."'";
								$xmloptions[] = "pointlabelclass: '".$kmlmarkerlabelclass."'";
							}
							
							$xmloptions[] = "titlestyle: ' '";
								
							$code .= "\nexml".$mapnm." = new GeoXml(\"exml".$mapnm."\", map".$mapnm.", kml".$mapnm.", {".implode(",",$xmloptions)."});";
							$code .= "\nexml".$mapnm.".parse(); ";
							break;
					}
				}

				if ($traffic=='1') {
					$code .= "\ntrafficInfo".$mapnm." = new GTrafficOverlay();";
					$code .= "\nmap".$mapnm.".addOverlay(trafficInfo".$mapnm.");";
				}

				if ($panoramio!="none") {
					$code .= "\nmap".$mapnm.".addOverlay(new GLayer('com.panoramio.".$panoramio."'));";
				}
				if ($panotype!="none") {
					$code .= "\n  var options = {
    								order: '".$panoorder."',
								    set: '".$panotype."', 
								    to: '".$panomax."' };
								panoLayer".$mapnm." = new PanoramioLayer(map".$mapnm.", options);
								panoLayer".$mapnm.".enable();";
				}
				
				if ($youtube!="none") {
					$code .= "\nmap".$mapnm.".addOverlay(new GLayer('com.youtube.".$youtube."'));";
				}

				if ($wiki!="none") {
					$code .= "\nmap".$mapnm.".addOverlay(new GLayer('org.wikipedia.".$wiki."'));";
				}
				
				if (count($layer)>0) {
					foreach ($layer as $lay) {
						$code .= "\nmap".$mapnm.".addOverlay(new GLayer('".$lay."'));";
					}
				}
				
				if ($localsearch=='1') {
					$code .= "localsearch".$mapnm." = new google.maps.LocalSearch(".((!empty($searchoptions))?"{ ".$searchoptions." }":"").");";
					$code .= "map".$mapnm.".addControl(localsearch".$mapnm.", new GControlPosition(G_ANCHOR_BOTTOM_RIGHT, new GSize(10,20)));";
					if (!empty($searchtext))
						$code .= "localsearch".$mapnm.".execute('".$searchtext."');";
				}
				
				if ($googlebar=='1') {
					$code .= "map".$mapnm.".enableGoogleBar();";
				}

				if ($adsmanager=='1') {
					$code .= "adsmanager".$mapnm." = new GAdsManager(map".$mapnm.", ".((!empty($adsense))?"'".$adsense."'":"''").", { style: 'adunit', maxAdsOnMap: ".$maxads.((!empty($searchtext))?", keywords: '".$searchtext."'":"").((!empty($channel)&&!empty($adsense))?", channel: '".$channel."'":"").(($localsearch=='1')?", position: new GControlPosition(G_ANCHOR_BOTTOM_LEFT, new GSize(20,20))":"")."}); ";
					$code .= "adsmanager".$mapnm.".enable();";
				}

				if ($this->debug_plugin=="1")
					$code.="map".$mapnm.".addControl(new VersionControl());";

				if (((!empty($tolat)&&!empty($tolon))||!empty($toaddress))&&$animdir=='0') {
					// Route
					$xmloptions = array();
					if ($dirtype=='W')
						$xmloptions[] = "travelMode : G_TRAVEL_MODE_WALKING";
					else
						$xmloptions[] = "travelMode : G_TRAVEL_MODE_DRIVING";
					
					if ($avoidhighways=='1')
						$xmloptions[] = "avoidHighways : true";
					else
						$xmloptions[] = "avoidHighways : false";
					
					$code .= "var dirsidebar".$mapnm." = document.getElementById('dirsidebar".$mapnm."');";
					$code .= "if (directions".$mapnm.") {
									directions".$mapnm.".clear();
									if ( dirsidebar".$mapnm.".hasChildNodes() )
									{
										while ( dirsidebar".$mapnm.".childNodes.length >= 1 )
										{
											dirsidebar".$mapnm.".removeChild( dirsidebar".$mapnm.".firstChild );       
										} 
									}
							} else {
									directions".$mapnm." = new GDirections(map".$mapnm.", dirsidebar".$mapnm.");
									GEvent.addListener(directions".$mapnm.", 'error', handleErrors".$mapnm.");
								}
						";
						
					if (is_array($waypoints)&&count($waypoints)>0) {
						if ($address!="")
							array_unshift($waypoints, $address);
						if ($toaddress!="")
							array_push($waypoints, $toaddress);
						$wpstring="";
						foreach ($waypoints as $wp) {
							if ($wpstring!="")
								$wpstring.= ", ";
							$wpstring .= "'".$wp."'";
						}
						$code.="\ndirections".$mapnm.".loadFromWaypoints([".$wpstring."], {".implode(",",$xmloptions)."});";
					} else
						$code.="\ndirections".$mapnm.".load('from: ".$address." to: ".$toaddress."', {".implode(",",$xmloptions)."});";
						
					// Old
					// $code .= "directions".$mapnm.".load('from: ".(($latitude!='')?$latitude:$deflatitude).", ".(($longitude!='')?$longitude:$deflongitude)." to: ".$tolat.", ".$tolon."', {".implode(",",$xmloptions)."});";
				}
				
				switch ($mapType) {
				case "Satellite":
					$code.="\nmap".$mapnm.".setMapType(G_SATELLITE_MAP);";
					break;
				
				case "Hybrid":
					$code.="\nmap".$mapnm.".setMapType(G_HYBRID_MAP);";
					break;

				case "Terrain":
					if ($this->check_google_api_version($google_API_version, "2.93"))
						$code.="\nmap".$mapnm.".setMapType(G_PHYSICAL_MAP);";
					else 
						$code.="\nmap".$mapnm.".setMapType(G_NORMAL_MAP);";
					break;

				case "Earth":
					if ($this->check_google_api_version($google_API_version, "2.113")) {
						$code.="\nmap".$mapnm.".setMapType(G_SATELLITE_3D_MAP);";
						$code.="\nmap".$mapnm.".getEarthInstance(initearth".$mapnm.");";
					} else 
						$code.="\nmap".$mapnm.".setMapType(G_NORMAL_MAP);";
					break;
				
				default:
					$code.="\nmap".$mapnm.".setMapType(G_NORMAL_MAP);";
					break;
				}

				if($zoom_new=='1'&&$controltype=='user')
				{
					$code.="
					map".$mapnm.".enableContinuousZoom();
					map".$mapnm.".enableDoubleClickZoom();
					";
				} else {
					$code.="
					map".$mapnm.".disableContinuousZoom();
					map".$mapnm.".disableDoubleClickZoom();
					";
				}

				if($zoom_wheel=='1'&&$controltype=='user')
				{
					$code.="map".$mapnm.".enableScrollWheelZoom();
					";
				} 

				if (($inline_coords == 0 && count($kml)==0) // No inline coordinates and no kml => standard configuration
					||($latitude !=''&&$longitude!='')) { // Inline coordinates and text is not empty

//					previous:  ||($inline_coords == 1 && $text !='')) { // Inline coordinates and text is not empty
//					previous: if (($inline_coords == 1&&!(count($kml)>0&&$text==''))||($inline_coords == 0 && count($kml)==0)) {

					$options = '';
					
					if ($tooltip!='') 
						$options .= (($options!='')?', ':'')."title:\"".$tooltip."\"";
					if ($icon!='')
						$options .= (($options!='')?', ':'')."icon:markericon".$mapnm;
					
					$code.="var marker".$mapnm." = new GMarker(point".(($options!='')?', {'.$options.'}':'').");";
					
					$code.="map".$mapnm.".addOverlay(marker".$mapnm.");
					";

					if ($text!=''||$dir!='0') {
						// convert $text to maybe tabs?
						// Check <tab> tag
						$reg='/(<tab\s*?(title=\'(.*?)\')?>)(.*?)(<\/tab>)/si';
						$c=preg_match_all($reg,$text,$m);

						// if <tab> then make array of $text
						if ($c>0) {
							$text= array();
							for ($z=0;$z<$c;$z++) {
								// transform attribute title to title of tab
								$text[$z]->title = $this->_htsdecode($m[3][$z], ENT_NOQUOTES);
								$text[$z]->text = $this->_htsdecode($m[4][$z], ENT_NOQUOTES);
							}
						}
						if ($dir!='0') {
							$dirform="<form id='directionform".$mapnm."' action='".$protocol."maps.google.com/maps' method='get' target='_blank' onsubmit='DirectionMarkersubmit".$mapnm."(this);return false;' class='mapdirform'>";
								
							$dirform.="<br />".$txt_dir."<input ".(($txt_to=='')?"type='hidden' ":"type='radio' ")." ".(($dirdef=='0')?"checked='checked'":"")." name='dir' value='to'>".(($txt_to!='')?$txt_to."&nbsp;":"")."<input ".(($txt_from=='')?"type='hidden' ":"type='radio' ").(($dirdef=='1')?"checked='checked'":"")." name='dir' value='from'>".(($txt_from!='')?$txt_from:"");
							$dirform.="<br />".$txt_diraddr."<input type='text' class='inputbox' size='20' name='saddr' id='saddr' value='' /><br />";

							if ($txt_driving!=''||$dirtype=="D")

								$dirform.="<input ".(($txt_driving=='')?"type='hidden' ":"type='radio' ")."class='radio' name='dirflg' value='' ".(($dirtype=="D")?"checked='checked'":"")." />".$txt_driving.(($txt_driving!='')?"&nbsp;":"");
							if ($txt_avhighways!=''||$dirtype=="1")
								$dirform.="<input ".(($txt_avhighways=='')?"type='hidden' ":"type='radio' ")."class='radio' name='dirflg' value='h' ".(($avoidhighways=='1')?"checked='checked'":"")." />".$txt_avhighways.(($txt_avhighways!='')?"&nbsp;":"");
							if ($txt_walking!=''||$dirtype=="W")
								$dirform.="<input ".(($txt_walking=='')?"type='hidden' ":"type='radio' ")."class='radio' name='dirflg' value='w' ".(($dirtype=="W")?"checked='checked'":"")." />".$txt_walking.(($txt_walking!='')?"&nbsp;":"");
							if ($txt_driving!=''||$txt_avhighways!=''||$txt_walking!='')
								$dirform.="<br />";	
							$dirform.="<input value='".$txt_get_dir."' class='button' type='submit' style='margin-top: 2px;'>";
							
							if ($dir=='2')
								$dirform.= "<input type='hidden' name='pw' value='2'/>";

							if ($lang!='') 
								$dirform.= "<input type='hidden' name='hl' value='".$lang."'/>";

							if (!empty($address))
								$dirform.="<input type='hidden' name='daddr' value='".$address." (".(($latitude!='')?$latitude:$deflatitude).", ".(($longitude!='')?$longitude:$deflongitude).")'/></form>";
							else
								$dirform.="<input type='hidden' name='daddr' value='".(($latitude!='')?$latitude:$deflatitude).", ".(($longitude!='')?$longitude:$deflongitude)."'/></form>";
							
							// Add form before div or at the end of the html.
							if (is_array($text)) {
								$text[$z+1]->title = $txt_dir;
								$text[$z+1]->text = $this->_htsdecode($dirform, ENT_NOQUOTES);
							} else {
								$pat="/&lt;\/div&gt;$/";
								if (preg_match($pat, $text))
									$text = preg_replace($pat, $dirform."</div>", $text);
								else {
									$pat="/<\/div>$/";
									if (preg_match($pat, $text))
										$text = preg_replace($pat, $dirform."</div>", $text);
									else
										$text.=$dirform;
								}
							}
						}
						
						if (!is_array($text))
							$text = $this->_htsdecode($text, ENT_NOQUOTES);

						// If marker 
						if ($marker==1) {
							if (is_array($text)) {
								$code .= "marker".$mapnm.".openInfoWindowTabsHtml([";
								$first = true;
								foreach ($text as $tab) {
									if ($first) 
										$first = false;
									else 
										$code.=",  ";
										
									$code.= "new GInfoWindowTab(\"".$tab->title."\", \"".$tab->text."\")";
								}
								
								$code .= "]);";  
								
							} else
								$code.="marker".$mapnm.".openInfoWindowHtml(\"".$text."\");"; 
						}
						
						$code.="GEvent.addListener(marker".$mapnm.", 'click', function() {
								marker".$mapnm;
						if (is_array($text)) {
							$code .=".openInfoWindowTabsHtml([";
							$first = true;
							foreach ($text as $tab) {
								if ($first) 
									$first = false;
								else 
									$code.=",  ";
									
								$code.= "new GInfoWindowTab(\"".$tab->title."\", \"".$tab->text."\")";
							}
							
							$code .= "]);";  
							
						} else
							$code.=".openInfoWindowHtml(\"".$text."\");";
							
						$code.="});
						";
					}
				}
				
				if ($imageurl!='') {
					$code .= "imageovl".$mapnm." = new GScreenOverlay('$imageurl',
											new GScreenPoint($imagex, $imagey, '$imagexyunits', '$imagexyunits'),  // screenXY
											new GScreenPoint($imageanchorx, $imageanchory, '$imageanchorunits', '$imageanchorunits'),  // overlayXY
											new GScreenSize($imagewidth, $imageheight)  // size on screen
										);
								map".$mapnm.".addOverlay(imageovl".$mapnm.");
						";
				}
				if ($animdir=='0'&&($sv=='top'||$sv=='bottom'||($sv!='none'&&$sv!='top'&&$sv!='bottom'))) {
					if ($sv!='none'&&$sv!='top'&&$sv!='bottom')
						$code.="\nvar panobj = document.getElementById('".$sv."');
								";
					else
						$code.="\nvar panobj = document.getElementById('svpanorama".$mapnm."');
								";
					$svopt = "";
					if ($svyaw!='0')
						$svopt .= "yaw:".$svyaw;
					if ($svpitch!='0')
						$svopt .= (($svopt=="")?"":", ")."pitch:".$svpitch;
					if ($svzoom!='')
						$svopt .= (($svopt=="")?"":", ")."zoom:".$svzoom;
						
					$code.="\nsvpanorama".$mapnm." = new GStreetviewPanorama(panobj);
							svlastpoint".$mapnm." = map".$mapnm.".getCenter();
							svpanorama".$mapnm.".setLocationAndPOV(svlastpoint".$mapnm.", ".(($svopt!='')?"{".$svopt."}":'null').");
							svmarker".$mapnm." = new GMarker(svlastpoint".$mapnm.", {icon: guyIcon".$mapnm." , draggable: true});
							map".$mapnm.".addOverlay(svmarker".$mapnm.");
							GEvent.addListener(svmarker".$mapnm.", 'dragend', onDragEnd".$mapnm.");
							GEvent.addListener(svpanorama".$mapnm.", 'initialized', onNewLocation".$mapnm.");
							GEvent.addListener(svpanorama".$mapnm.", 'yawchanged', onYawChange".$mapnm."); 
							";
				}

				if ($animdir!="0") {
					$xmloptions = array();
					$xmloptions[] = "preserveViewport: false";
					$xmloptions[] = "getSteps: true";
					
					if ($dirtype=='W')
						$xmloptions[] = "travelMode : G_TRAVEL_MODE_WALKING";
					else
						$xmloptions[] = "travelMode : G_TRAVEL_MODE_DRIVING";
					
					if ($avoidhighways=='1')
						$xmloptions[] = "avoidHighways : true";
					else
						$xmloptions[] = "avoidHighways : false";
						
					$opts = array();
					if ($animspeed!=1)
						$opts[] = "Speed : ".$animspeed;
					if ($animautostart!=0)
						$opts[] = "AutoStart : true";
					if ($animunit!='')
						$opts[] = "Unit : '".$animunit."'";
//					$opts[] = "zoomlevel : ".$zoom;
					if ($dirtype=='W')
						$opts[] = "travelMode : G_TRAVEL_MODE_WALKING";
					else
						$opts[] = "travelMode : G_TRAVEL_MODE_DRIVING";
					
					if ($avoidhighways=='1')
						$opts[] = "avoidHighways : true";
					else
						$opts[] = "avoidHighways : false";
	
					$code.="\nvar panobj = document.getElementById('svpanorama".$mapnm."');
							svpanorama".$mapnm." = new GStreetviewPanorama(panobj);
							directions".$mapnm." = new GDirections(map".$mapnm.");
							";

					$lang = "";
					foreach ($langanim as $al) {
						$lang.=(($lang=='')?"":",")."'".$al."'";
					}
					
					$code.="\nopts = {".implode(",",$opts)."};
							lang = [".$lang."];
							";
					$code .="\nroute".$mapnm." = new Directionsobj('route".$mapnm."', map".$mapnm.", '".$mapnm."', svpanorama".$mapnm.", svclient".$mapnm.", directions".$mapnm.", centerpoint, opts, lang);";
					
					if (is_array($waypoints)&&count($waypoints)>0) {
						if ($address!="")
							array_unshift($waypoints, $address);
						if ($toaddress!="")
							array_push($waypoints, $toaddress);
						$wpstring="";
						foreach ($waypoints as $wp) {
							if ($wpstring!="")
								$wpstring.= ", ";
							$wpstring .= "'".$wp."'";
						}
						$code.="\ndirections".$mapnm.".loadFromWaypoints([".$wpstring."], {".implode(",",$xmloptions)."});";
					} else
						$code.="\ndirections".$mapnm.".load('from: ".$address." to: ".$toaddress."', {".implode(",",$xmloptions)."});";
				}
				
				if($zoom_wheel=='1')
				{
					$code.="GEvent.addDomListener(tst".$mapnm.", 'DOMMouseScroll', CancelEvent".$mapnm.");
							GEvent.addDomListener(tst".$mapnm.", 'mousewheel', CancelEvent".$mapnm.");
						";
				}

				/* remove copyright, terms and mapdata. Do not use 					
				$code.= "test_div = document.getElementById('googlemap".$mapnm."');";
				$code.= "test_obj = test_div.childNodes[1].style.display='none';";
				$code.= "test_obj = test_div.childNodes[2].style.display='none';";
				*/

				if($client_geo == 1) {
					if ($clientgeotype=="local")
						$code.="	});
							localSearch.execute(address);";
					else
						$code.="		       
									  });";
				}

				// End of script voor showing the map 
				if ($show!=0)
					$code.="\n	}";
			$code.="\n}
			//]]></script>
			";
	
			// Call the Maps through timeout to render in IE also
			// Set an event for watching the changing of the map so it can refresh itself
			$code.= "<script type=\"text/javascript\">//<![CDATA[
					if (GBrowserIsCompatible()) {
                        window.onunload=function(){window.onunload;GUnload()};
						tst".$mapnm.".setAttribute(\"oldValue\",0);
						tst".$mapnm.".setAttribute(\"refreshMap\",0);
						";

			if ($loadmootools=='1') {
			$code.= "if (window.MooTools==null)
						tstint".$mapnm."=setInterval(\"checkMap".$mapnm."()\",".$timeinterval.");
					else
						window.addEvent('domready', function() {
   								tstint".$mapnm."=setInterval('checkMap".$mapnm."()', ".$timeinterval.");
							});
					";
			} else {
				$code.= "tstint".$mapnm."=setInterval(\"checkMap".$mapnm."()\",".$timeinterval.");
						";
			}

			$code.= "}
			//]]></script>
			";
			$endmem = round($this->memory_get_usage()/1024);
			$diffmem = $endmem-$startmem;
			$this->debug_log("Memory Usage End: " . $endmem . " KB (".$diffmem." KB)");
			$code = "\n<!-- Plugin Google Maps version 2.12n by Mike Reumer ".(($this->debug_text!='')?$this->debug_text."\n":"")."-->".$code;
				
			$this->debug_text = '';
			// Depending of show place the code at end of page or on the {mosmap} position		
			if ($show==0) {
				$offset = strpos($row->text, $mosmap);
				$row->text = preg_replace($regex, $lbcode, $row->text, 1);
				// If pagebreak add code before pagebreak
				preg_match($pagebreak, $row->text, $m, PREG_OFFSET_CAPTURE, $offset);
				if (count($m)>0)
					$offsetpagebreak = $m[0][1];
				else
					$offsetpagebreak = 0;
				if ($offsetpagebreak!=0) 
					$row->text = substr($row->text, 0, $offsetpagebreak).$code.substr($row->text, $offsetpagebreak);
				else
					$row->text .= $code;
			} else
				$row->text = preg_replace($regex, $code, $row->text, 1);
			} 
	
		}

		return true;
	}
}
?>