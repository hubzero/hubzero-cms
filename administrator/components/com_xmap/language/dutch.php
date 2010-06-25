<?php
/* @package Xmap
 * @author Guillermo Vargas, http://joomla.vargas.co.cr/
 * @email guille@vargas.co.cr
 * @translator Arjan Menger, http://joomla.taalbestand.nl/
 * @email arjan@taalbestand.nl
 */

defined( '_JEXEC' ) or die( 'Direct Access to this location is not allowed.' ); 

if( !defined( 'JOOMAP_LANG' )) {
    define('JOOMAP_LANG', 1 );

    // -- Really Important --------
    define('_XMAP_CHARSET','ISO-8859-1');

    // -- General ------------------------------------------------------------------
    define('_XMAP_CFG_COM_TITLE',			'Xmap Instellingen');
    define('_XMAP_CFG_OPTIONS',			'Weergave-opties');
    define('_XMAP_CFG_CSS_CLASSNAME',		'CSS Classnaam');
    define('_XMAP_CFG_EXPAND_CATEGORIES',	'Content categorieën uitvouwen');
    define('_XMAP_CFG_EXPAND_SECTIONS',	'Content sections uitvouwen');
    define('_XMAP_CFG_SHOW_MENU_TITLES',	'Menutitels tonen');
    define('_XMAP_CFG_NUMBER_COLUMNS',	'Aantal kolommen');
    define('_XMAP_EX_LINK',				'Externe links markeren');
    define('_XMAP_CFG_CLICK_HERE', 		'Klik hier');
    define('_XMAP_CFG_GOOGLE_MAP',		'Google Sitemap');
    define('_XMAP_EXCLUDE_MENU',			'Menu ID\'s uitsluiten');
    define('_XMAP_TAB_DISPLAY',			'Weergave');
    define('_XMAP_TAB_MENUS',				'Menu\'s');
    define('_XMAP_CFG_WRITEABLE',			'Schrijfbaar');
    define('_XMAP_CFG_UNWRITEABLE',		'Onschrijfbaar');
    define('_XMAP_MSG_MAKE_UNWRITEABLE',	'Onschrijfbaar maken na opslaan');
    define('_XMAP_MSG_OVERRIDE_WRITE_PROTECTION', 'Schrijfbescherming opheffen tijdens opslaan');
    define('_XMAP_GOOGLE_LINK',			'Google link');
    define('_XMAP_CFG_INCLUDE_LINK',		'Link naar auteur (Powered by ..)');

    // -- Tips ---------------------------------------------------------------------
    define('_XMAP_EXCLUDE_MENU_TIP',		'Specificeer menu ID\'s die je niet in de sitemap wilt invoegen.<br /><strong>NOTITIE</strong><br />Scheid ID\'s met een komma!');

    // -- Menus --------------------------------------------------------------------
    define('_XMAP_CFG_SET_ORDER',			'Set Menu Display Order');
    define('_XMAP_CFG_MENU_SHOW',			'Tonen');
    define('_XMAP_CFG_MENU_REORDER',		'Herordenen');
    define('_XMAP_CFG_MENU_ORDER',		'Ordenen');
    define('_XMAP_CFG_MENU_NAME',			'Menu naam');
    define('_XMAP_CFG_DISABLE',			'Klik om uit te schakelen');
    define('_XMAP_CFG_ENABLE',			'Klik om in te schakelen');
    define('_XMAP_SHOW',					'Tonen');
    define('_XMAP_NO_SHOW',				'Niet tonen');

    // -- Toolbar ------------------------------------------------------------------
    define('_XMAP_TOOLBAR_SAVE', 			'Opslaan');
    define('_XMAP_TOOLBAR_CANCEL', 		'Annuleren');

    // -- Errors -------------------------------------------------------------------
    define('_XMAP_ERR_NO_LANG',			'Taalbestand [ %s ] niet gevonden, de standaard taal wordt geladen: english<br />');
    define('_XMAP_ERR_CONF_SAVE',         'FOUT: De instellingen konden niet worden opgeslagen.');
    define('_XMAP_ERR_NO_CREATE',         'FOUT: De instellingen tabel kon niet worden aangemaakt');
    define('_XMAP_ERR_NO_DEFAULT_SET',    'FOUT: De standaard instellingen konden niet ingevoegd worden');
    define('_XMAP_ERR_NO_PREV_BU',        'WAARSCHUWING: De vorige backup kon niet worden verwijderd');
    define('_XMAP_ERR_NO_BACKUP',         'FOUT: Backup kon niet worden gemaakt');
    define('_XMAP_ERR_NO_DROP_DB',        'FOUT: De instellingen tabel kon niet worden verwijderd');
    define('_XMAP_ERR_NO_SETTINGS',		'FOUT: De instellingen konden niet vanuit de database worden geladen: <a href="%s">Instellingen tabel aanmaken</a>');

    // -- Config -------------------------------------------------------------------
    define('_XMAP_MSG_SET_RESTORED',      'De instellingen zijn hersteld');
    define('_XMAP_MSG_SET_BACKEDUP',      'De instellingen zijn opgeslagen');
    define('_XMAP_MSG_SET_DB_CREATED',    'De instellingen tabel is aangemaakt');
    define('_XMAP_MSG_SET_DEF_INSERT',    'De standaard instellingen zijn ingevoegd');
    define('_XMAP_MSG_SET_DB_DROPPED','De tabellen van Xmap zijn opgeslagen!');
	
    // -- CSS ----------------------------------------------------------------------
    define('_XMAP_CSS',					'Xmap CSS');
    define('_XMAP_CSS_EDIT',				'Template bewerken'); // Edit template
	
    // -- Sitemap (Frontend) -------------------------------------------------------
    define('_XMAP_SHOW_AS_EXTERN_ALT',	'Link opent in een nieuw venster');
	
    // -- Added for Xmap 
    define('_XMAP_CFG_MENU_SHOW_HTML',		'Wordt in de website getoond');
    define('_XMAP_CFG_MENU_SHOW_XML',		'In XML sitemap tonen');
    define('_XMAP_CFG_MENU_PRIORITY',		'Prioriteit');
    define('_XMAP_CFG_MENU_CHANGEFREQ',		'Frequentie aanpassen');
    define('_XMAP_CFG_CHANGEFREQ_ALWAYS',		'Altijd');
    define('_XMAP_CFG_CHANGEFREQ_HOURLY',		'Uurlijks');
    define('_XMAP_CFG_CHANGEFREQ_DAILY',		'Dagelijks');
    define('_XMAP_CFG_CHANGEFREQ_WEEKLY',		'Wekelijks');
    define('_XMAP_CFG_CHANGEFREQ_MONTHLY',		'Maandelijks');
    define('_XMAP_CFG_CHANGEFREQ_YEARLY',		'Jaarlijks');
    define('_XMAP_CFG_CHANGEFREQ_NEVER',		'Nooit');

    define('_XMAP_TIT_SETTINGS_OF',			'Voorkeuren voor %s');
    define('_XMAP_TAB_SITEMAPS',			'Sitemaps');
    define('_XMAP_MSG_NO_SITEMAPS',			'Er zijn nog geen sitemaps aangemaakt');
    define('_XMAP_MSG_NO_SITEMAP',			'Deze sitemap is niet beschikbaar');
    define('_XMAP_MSG_LOADING_SETTINGS',		'De voorkeuren worden geladen...');
    define('_XMAP_MSG_ERROR_LOADING_SITEMAP',		'Fout. De sitemap kan niet worden geladen');
    define('_XMAP_MSG_ERROR_SAVE_PROPERTY',		'Fout. De eigenschap van de sitemap kan niet worden opgeslagen.');
    define('_XMAP_MSG_ERROR_CLEAN_CACHE',		'Fout. De buffer van de sitemap kan niet worden geleegd');
    define('_XMAP_ERROR_DELETE_DEFAULT',		'De standaard sitemap kan niet worden verwijderd!');
    define('_XMAP_MSG_CACHE_CLEANED',			'De buffer is geleegd!');
    define('_XMAP_SITEMAP_ID',				'Sitemap\'s ID');
    define('_XMAP_ADD_SITEMAP',				'Sitemap toevoegen');
    define('_XMAP_NAME_NEW_SITEMAP',			'Nieuwe sitemap');
    define('_XMAP_DELETE_SITEMAP',			'Verwijderen');
    define('_XMAP_SETTINGS_SITEMAP',			'Voorkeuren');
    define('_XMAP_COPY_SITEMAP',			'Kopiëren');
    define('_XMAP_SITEMAP_SET_DEFAULT',			'Als standaard instellen');
    define('_XMAP_EDIT_MENU',				'Opties');
    define('_XMAP_DELETE_MENU',				'Verwijderen');
    define('_XMAP_CLEAR_CACHE',				'Buffer legen');
    define('_XMAP_MOVEUP_MENU',		'Omhoog');
    define('_XMAP_MOVEDOWN_MENU',	'Omlaag');
    define('_XMAP_ADD_MENU',		'Menu\'s toevoegen');
    define('_XMAP_COPY_OF',		'Kopie van %s');
    define('_XMAP_INFO_LAST_VISIT',	'Laatste bezoek');
    define('_XMAP_INFO_COUNT_VIEWS',	'Aantal bezoeken');
    define('_XMAP_INFO_TOTAL_LINKS',	'Aantal links');
    define('_XMAP_CFG_URLS',		'Sitemap\'s adres');
    define('_XMAP_XML_LINK_TIP',	'Link kopiëren en versturen naar Google en Yahoo');
    define('_XMAP_HTML_LINK_TIP',	'Dit is het adres van de sitemap. Je kan het gebruiken om items in menu\'s aan te maken.');
    define('_XMAP_CFG_XML_MAP',		'XML sitemap');
    define('_XMAP_CFG_HTML_MAP',	'HTML sitemap');
    define('_XMAP_XML_LINK',		'Google link');
    define('_XMAP_CFG_XML_MAP_TIP',	'Het gegenereerde XML-bestand voor zoekmachines');
    define('_XMAP_ADD', 'Opslaan');
    define('_XMAP_CANCEL', 'Annuleren');
    define('_XMAP_LOADING', 'Laden...');
    define('_XMAP_CACHE', 'Buffer');
    define('_XMAP_USE_CACHE', 'Buffer gebruiken');
    define('_XMAP_CACHE_LIFE_TIME', 'Tijdsduur van buffer');
    define('_XMAP_NEVER_VISITED', 'Nooit');


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
