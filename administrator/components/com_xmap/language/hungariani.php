<?php
/* @package Xmap
 * @author Guillermo Vargas, http://joomla.vargas.co.cr/
 * @translator Jozsef Tamas Herczeg, http://www.joomlandia.eu/
*/

defined( '_JEXEC' ) or die( 'A közvetlen hozzáférés ehhez a helyhez nem engedélyezett.' );

if( !defined( 'JOOMAP_LANG' )) {
    define ('JOOMAP_LANG', 1 );
    // -- General ------------------------------------------------------------------
    define('_XMAP_CFG_COM_TITLE', 'Xmap beállításai');
    define('_XMAP_CFG_OPTIONS', 'Megjelenítés beállításai');
    define('_XMAP_CFG_CSS_CLASSNAME', 'CSS osztálynév');
    define('_XMAP_CFG_EXPAND_CATEGORIES','A tartalomkategóriák kibontása');
    define('_XMAP_CFG_EXPAND_SECTIONS','A tartalomszekciók kibontása');
    define('_XMAP_CFG_SHOW_MENU_TITLES', 'A menüpontok megjelenítése');
    define('_XMAP_CFG_NUMBER_COLUMNS', 'Az oszlopok száma');
    define('_XMAP_EX_LINK', 'A külsõ hivatkozások megjelölése');
    define('_XMAP_CFG_CLICK_HERE', 'Kattints ide');
    define('_XMAP_CFG_GOOGLE_MAP',		'Google Sitemap');
    define('_XMAP_EXCLUDE_MENU',			'Kizárandó menüazonosítók');
    define('_XMAP_TAB_DISPLAY',			'Megjelenítés');
    define('_XMAP_TAB_MENUS',				'Menük');
    define('_XMAP_CFG_WRITEABLE',			'Írható');
    define('_XMAP_CFG_UNWRITEABLE',		'Írásvédett');
    define('_XMAP_MSG_MAKE_UNWRITEABLE',	'Írásvédetté tétel mentés után');
    define('_XMAP_MSG_OVERRIDE_WRITE_PROTECTION', 'Az írásvédettség hatálytalanítása mentéskor');
    define('_XMAP_GOOGLE_LINK',			'Google hivatkozás');
    define('_XMAP_CFG_INCLUDE_LINK',		'A szerzõre mutató láthatatlan hivatkozás');

    // -- Tips ---------------------------------------------------------------------
    define('_XMAP_EXCLUDE_MENU_TIP',		'Add meg a helytérképbõl kihagyandó menüazonosítókat.<br /><strong>MEGJEGYZÉS</strong><br />Válaszd el vesszõvel az azoosítókat!');

    // -- Menus --------------------------------------------------------------------
    define('_XMAP_CFG_SET_ORDER', 'Állítsd be a menük megjelenítésének sorrendjét');
    define('_XMAP_CFG_MENU_SHOW', 'Látszik');
    define('_XMAP_CFG_MENU_REORDER', 'Átrendezés');
    define('_XMAP_CFG_MENU_ORDER', 'Sorrend');
    define('_XMAP_CFG_MENU_NAME', 'Menünév');
    define('_XMAP_CFG_DISABLE', 'Kattints rá a letiltáshoz');
    define('_XMAP_CFG_ENABLE', 'Kattints rá az engedélyezéshez');
    define('_XMAP_SHOW','Látszik');
    define('_XMAP_NO_SHOW','Nem látszik');

    // -- Toolbar ------------------------------------------------------------------
    define('_XMAP_TOOLBAR_SAVE', 'Mentés');
    define('_XMAP_TOOLBAR_CANCEL', 'Mégse');

    // -- Errors -------------------------------------------------------------------
    define('_XMAP_ERR_NO_LANG','[ %s ] nyelvi fájl nem található, betöltésre került az alapértelmezett nyelv: angol<br />'); // %s = $GLOBALS['mosConfig_lang']
    define('_XMAP_ERR_CONF_SAVE',         'HIBA: A beállítások mentése nem sikerült.');
    define('_XMAP_ERR_NO_CREATE',         'HIBA: Nem hozható létre a Settings tábla');
    define('_XMAP_ERR_NO_DEFAULT_SET',    'HIBA: Nem szúrhatók be az alapértelmezett beállítások');
    define('_XMAP_ERR_NO_PREV_BU',        'FIGYELEM! Nem dobható el az elõzõ biztonsági mentés');
    define('_XMAP_ERR_NO_BACKUP',         'HIBA: Nem hozható létre a biztonsági mentés');
    define('_XMAP_ERR_NO_DROP_DB',        'HIBA: Nem dobható el a Settings tábla');
    define('_XMAP_ERR_NO_SETTINGS',		'HIBA: Nem tölthetõk be az adatbázisból a beállítások: <a href="%s">A Settings tábla létrehozása</a>');

    // -- Config -------------------------------------------------------------------
    define('_XMAP_MSG_SET_RESTORED',      'A beállítások visszaállítása kész');
    define('_XMAP_MSG_SET_BACKEDUP',      'A beállítások mentése kész');
    define('_XMAP_MSG_SET_DB_CREATED',    'A Settings tábla létrehozása kész');
    define('_XMAP_MSG_SET_DEF_INSERT',    'Az alapértelmezett beállítások beszúrása kész');
    define('_XMAP_MSG_SET_DB_DROPPED',    'A Settings tábla eldobása megtörtént');
	
    // -- CSS ----------------------------------------------------------------------
    define('_XMAP_CSS',					'Xmap CSS');
    define('_XMAP_CSS_EDIT',				'Sablon szerkesztése'); // Edit template
	
    // -- Sitemap (Frontend) -------------------------------------------------------
    define('_XMAP_SHOW_AS_EXTERN_ALT','Új ablakban nyílik meg a hivatkozás');
	
    // -- Added for Xmap 
    define('_XMAP_CFG_MENU_SHOW_HTML',		'Látható a webhelyen');
    define('_XMAP_CFG_MENU_SHOW_XML',		'Látható az XML oldaltérképben');
    define('_XMAP_CFG_MENU_PRIORITY',		'Prioritás');
    define('_XMAP_CFG_MENU_CHANGEFREQ',		'Gyakoriság módosítása');
    define('_XMAP_CFG_CHANGEFREQ_ALWAYS',		'Mindig');
    define('_XMAP_CFG_CHANGEFREQ_HOURLY',		'Óránként');
    define('_XMAP_CFG_CHANGEFREQ_DAILY',		'Naponta');
    define('_XMAP_CFG_CHANGEFREQ_WEEKLY',		'Hetente');
    define('_XMAP_CFG_CHANGEFREQ_MONTHLY',		'Havonta');
    define('_XMAP_CFG_CHANGEFREQ_YEARLY',		'Évente');
    define('_XMAP_CFG_CHANGEFREQ_NEVER',		'Soha');

    define('_XMAP_TIT_SETTINGS_OF',			'%s beállításai');
    define('_XMAP_TAB_SITEMAPS',			'Oldaltérképek');
    define('_XMAP_MSG_NO_SITEMAPS',			'Még nem történt meg az oldaltérkép létrehozása');
    define('_XMAP_MSG_NO_SITEMAP',			'Ez az oldaltérkép nem elérhetõ');
    define('_XMAP_MSG_LOADING_SETTINGS',		'Beállítások betöltése...');
    define('_XMAP_MSG_ERROR_LOADING_SITEMAP',		'Hiba. Nem tölthetõ be az oldaltérkép');
    define('_XMAP_MSG_ERROR_SAVE_PROPERTY',		'Hiba. Nem menthetõ az oldaltérkép tulajdonsága.');
    define('_XMAP_MSG_ERROR_CLEAN_CACHE',		'Hiba. Nem törölhetõ az oldaltérkép gyorsítótára');
    define('_XMAP_ERROR_DELETE_DEFAULT',		'Az alapértelmezett oldaltérkép nem törölhetõ!');
    define('_XMAP_MSG_CACHE_CLEANED',			'A gyorsítótár törlése kész!');
    define('_XMAP_CHARSET',				'ISO-8859-2');
    define('_XMAP_SITEMAP_ID',				'Az oldaltérkép azonosítója');
    define('_XMAP_ADD_SITEMAP',				'Oldaltérkép hozzáadása');
    define('_XMAP_NAME_NEW_SITEMAP',			'Új oldaltérkép');
    define('_XMAP_DELETE_SITEMAP',			'Törlés');
    define('_XMAP_SETTINGS_SITEMAP',			'Beállítások');
    define('_XMAP_COPY_SITEMAP',			'Másolás');
    define('_XMAP_SITEMAP_SET_DEFAULT',			'Alapértelmezésként');
    define('_XMAP_EDIT_MENU',				'Beállítások');
    define('_XMAP_DELETE_MENU',				'Törlés');
    define('_XMAP_CLEAR_CACHE',				'Gyorsítótár törlése');
    define('_XMAP_MOVEUP_MENU',		'Fel');
    define('_XMAP_MOVEDOWN_MENU',	'Le');
    define('_XMAP_ADD_MENU',		'Menük hozzáadása');
    define('_XMAP_COPY_OF',		'%s másolata');
    define('_XMAP_INFO_LAST_VISIT',	'Utolsó látogatás');
    define('_XMAP_INFO_COUNT_VIEWS',	'Látogatások száma');
    define('_XMAP_INFO_TOTAL_LINKS',	'Hivatkozások száma');
    define('_XMAP_CFG_URLS',		'Az oldaltérkép URL-je');
    define('_XMAP_XML_LINK_TIP',	'A hivatkozás másolása és beküldése a Google-nek és a Yahoonak');
    define('_XMAP_HTML_LINK_TIP',	'Ez az oldaltérkép URL-je. Menüpontok létrehozásához is felhasználhatod.');
    define('_XMAP_CFG_XML_MAP',		'XML oldaltérkép');
    define('_XMAP_CFG_HTML_MAP',	'HTML oldaltérkép');
    define('_XMAP_XML_LINK',		'Google hivatkozás');
    define('_XMAP_CFG_XML_MAP_TIP',	'A keresõmotorok számára generált XML fájl');
    define('_XMAP_ADD', 'Mentés');
    define('_XMAP_CANCEL', 'Mégse');
    define('_XMAP_LOADING', 'Betöltés...');
    define('_XMAP_CACHE', 'Gyorsítótár');
    define('_XMAP_USE_CACHE', 'A gyorsítótár használata');
    define('_XMAP_CACHE_LIFE_TIME', 'A gyorsítótár élettartama');
    define('_XMAP_NEVER_VISITED', 'Soha');

	// New on Xmap 1.1
	define('_XMAP_PLUGINS','Plugins');	
	define( '_XMAP_INSTALL_3PD_WARN', 'Warning: Installing 3rd party extensions may compromise your server\'s security.' );
	define('_XMAP_INSTALL_NEW_PLUGIN', 'Install new Plugins');
	define('_XMAP_UNKNOWN_AUTHOR','Unknown author');
	define('_XMAP_PLUGIN_VERSION','Version %s');
	define('_XMAP_TAB_INSTALL_PLUGIN','Install');
	define('_XMAP_TAB_EXTENSIONS','Extensions');
	define('_XMAP_TAB_INSTALLED_EXTENSIONS','Installed Extensions');
	define('_XMAP_NO_PLUGINS_INSTALLED','No custom plugins installed');
	define('_XMAP_AUTHOR','Author');
	define('_XMAP_CONFIRM_DELETE_SITEMAP','Are you sure you want to delete this sitemap?');
	define('_XMAP_CONFIRM_UNINSTALL_PLUGIN','Are you sure you want to uninstall this plugin?');
	define('_XMAP_UNINSTALL','Uninstall');
	define('_XMAP_EXT_PUBLISHED','Published');
	define('_XMAP_EXT_UNPUBLISHED','Unpublished');
	define('_XMAP_PLUGIN_OPTIONS','Options');
	define('_XMAP_EXT_INSTALLED_MSG','The extension was installed successfully, please review their options and then publish the extension.');
	define('_XMAP_CONTINUE','Continue');
	define('_XMAP_MSG_EXCLUDE_CSS_SITEMAP','Do not include the CSS within the Sitemap');
	define('_XMAP_MSG_EXCLUDE_XSL_SITEMAP','Use classic XML Sitemap display');

	// New on Xmap 1.1
	define('_XMAP_MSG_SELECT_FOLDER','Please select a directory');
	define('_XMAP_UPLOAD_PKG_FILE','Upload Package File');
	define('_XMAP_UPLOAD_AND_INSTALL','Upload File &amp; Install');
	define('_XMAP_INSTALL_F_DIRECTORY','Install from directory');
	define('_XMAP_INSTALL_DIRECTORY','Install directory');
	define('_XMAP_INSTALL','Install');
	define('_XMAP_WRITEABLE','Writeable');
	define('_XMAP_UNWRITEABLE','Unwriteable');

	// New on Xmap 1.2
	define('_XMAP_COMPRESSION','Compression');
	define('_XMAP_USE_COMPRESSION','Compress the XML sitemap to save bandwidth');

        // New on Xmap 1.2.1
        define('_XMAP_CFG_NEWS_MAP',            'News Sitemap');
        define('_XMAP_NEWS_LINK_TIP',   'This is the news sitemap\'s URL.');

        // New on Xmap 1.2.2
       define('_XMAP_CFG_MENU_MODULE',            'Module');
       define('_XMAP_CFG_MENU_MODULE_TIP',            'Specify the module you use to show this menu in your site (Default: mod_mainmenu).');

        // New on Xmap 1.2.3
    define('_XMAP_TEXT',            'Link Text');
        define('_XMAP_TITLE',            'Link Title');
        define('_XMAP_LINK',            'Link URL');
        define('_XMAP_CSS_STYLE',            'CSS style');
        define('_XMAP_CSS_CLASS',            'CSS class');
        define('_XMAP_INVALID_SITEMAP',            'Invalid Sitemap');
        define('_XMAP_OK', 'Ok');
}
