<?php
/* @package Xmap
 * @author Bogdan Ionut Georgescu
 * @email georgescu_bogdan@hotmail.com
*/
defined( '_JEXEC' ) or die( 'Direct Access to this location is not allowed.' ); 

if( !defined( 'JOOMAP_LANG' )) {
    define ('JOOMAP_LANG', 1 );
    // -- General ------------------------------------------------------------------
    define("_XMAP_CFG_COM_TITLE", "Configurare Xmap");
    define("_XMAP_CFG_OPTIONS", "Vizualizare optiuni");
    define("_XMAP_CFG_TITLE", "Titlu");
    define("_XMAP_CFG_CSS_CLASSNAME", "Nume clasa CSS");
    define("_XMAP_CFG_EXPAND_CATEGORIES","Expandeaza categoriile");
    define("_XMAP_CFG_EXPAND_SECTIONS","Expandeaza sectiunile");
    define('_XMAP_CFG_SHOW_MENU_TITLES',	'Arata titlurile meniurilor');
    define('_XMAP_CFG_NUMBER_COLUMNS',	'Numarul de coloane');
    define('_XMAP_EX_LINK',				'Marcheaza link-urile externe');
    define('_XMAP_CFG_CLICK_HERE', 		'Click aici');
    define('_XMAP_CFG_GOOGLE_MAP',		'Google Sitemap');
    define('_XMAP_EXCLUDE_MENU',			'Exclude ID-urile');
    define('_XMAP_TAB_DISPLAY',			'Afiseaza');
    define('_XMAP_TAB_MENUS',				'Meniuri');
    define('_XMAP_CFG_WRITEABLE',			'Neprotejat la scriere');
    define('_XMAP_CFG_UNWRITEABLE',		'Protejat la scriere');
    define('_XMAP_MSG_MAKE_UNWRITEABLE',	'Protejeaza la scriere dupa salvare');
    define('_XMAP_MSG_OVERRIDE_WRITE_PROTECTION', 'Suprascrie protectia la scriere dupa salvare');
    define('_XMAP_GOOGLE_LINK',			'link Google');
    define('_XMAP_CFG_INCLUDE_LINK',		'Arata link catre autor');

    // -- Tips ---------------------------------------------------------------------
    define('_XMAP_EXCLUDE_MENU_TIP',		'Specifica ID-urile meniurilor ce nu doresti sa fie incluse.<br /><strong>NOTA</strong><br />Separa ID-urile cu virgula!');

    // -- Menus --------------------------------------------------------------------
    define('_XMAP_CFG_SET_ORDER',			'Arata ordinea de afisare a meniurilor');
    define('_XMAP_CFG_MENU_SHOW',			'Arata');
    define('_XMAP_CFG_MENU_REORDER',		'Reordoneaza');
    define('_XMAP_CFG_MENU_ORDER',		'Ordoneaza');
    define('_XMAP_CFG_MENU_NAME',			'Nume meniu');
    define('_XMAP_CFG_DISABLE',			'Dezactivare');
    define('_XMAP_CFG_ENABLE',			'Activare');
    define('_XMAP_SHOW',					'Arata');
    define('_XMAP_NO_SHOW',				'Ascunde');

    // -- Toolbar ------------------------------------------------------------------
    define('_XMAP_TOOLBAR_SAVE', 			'Salveaza');
    define('_XMAP_TOOLBAR_CANCEL', 		'Anuleaza');

    // -- EROAREs -------------------------------------------------------------------
    define('_XMAP_ERR_NO_LANG',			'Limbajul [ %s ] nu a fost gasit, a fost incarcat limbajul: english<br />');
    define('_XMAP_ERR_CONF_SAVE',         'EROARE: Configurarea nu a putut fi salvata');
    define('_XMAP_ERR_NO_CREATE',         'EROARE: Tabela de setari nu a putut fi creata');
    define('_XMAP_ERR_NO_DEFAULT_SET',    'EROARE: Nu au putut fi inserate setarile implicite');
    define('_XMAP_ERR_NO_PREV_BU',        'ATENTIE: Nu s-a putut renunta la arhivarea anterioara');
    define('_XMAP_ERR_NO_BACKUP',         'EROARE: Nu am putut crea arhivarea');
    define('_XMAP_ERR_NO_DROP_DB',        'EROARE: Nu s-a putut renunta la tabela de setari');
    define('_XMAP_ERR_NO_SETTINGS',		'EROARE: Nu au putut fi incarcate tabela cu setari: <a href="%s">Creeaza tabela</a>');

    // -- Config -------------------------------------------------------------------
    define('_XMAP_MSG_SET_RESTORED',      'Setari au fost restaurate');
    define('_XMAP_MSG_SET_BACKEDUP',      'Setari au fost salvate');
    define('_XMAP_MSG_SET_DB_CREATED',    'Tabela cu setari a fost creata');
    define('_XMAP_MSG_SET_DEF_INSERT',    'Setarile implicite au fost inserate');
    define('_XMAP_MSG_SET_DB_DROPPED','Tabelele Xmap au fost salvate!');
	
    // -- CSS ----------------------------------------------------------------------
    define('_XMAP_CSS',					'Xmap CSS');
    define('_XMAP_CSS_EDIT',				'Editeaza template'); // Edit template
	
    // -- Sitemap (Frontend) -------------------------------------------------------
    define('_XMAP_SHOW_AS_EXTERN_ALT',	'Link opens new window');
	
    // -- Added for Xmap 
    define('_XMAP_CFG_MENU_SHOW_HTML',		'Afisat pe site');
    define('_XMAP_CFG_MENU_SHOW_XML',		'Afiseaza in harta Xmap');
    define('_XMAP_CFG_MENU_PRIORITY',		'Prioritate');
    define('_XMAP_CFG_MENU_CHANGEFREQ',		'Modifica frecventa');
    define('_XMAP_CFG_CHANGEFREQ_ALWAYS',		'Always'); // Not to be translated
    define('_XMAP_CFG_CHANGEFREQ_HOURLY',		'Hourly'); // Not to be translated
    define('_XMAP_CFG_CHANGEFREQ_DAILY',		'Daily'); // Not to be translated
    define('_XMAP_CFG_CHANGEFREQ_WEEKLY',		'Weekly'); // Not to be translated
    define('_XMAP_CFG_CHANGEFREQ_MONTHLY',		'Monthly'); // Not to be translated
    define('_XMAP_CFG_CHANGEFREQ_YEARLY',		'Yearly'); // Not to be translated
    define('_XMAP_CFG_CHANGEFREQ_NEVER',		'Never'); // Not to be translated

    define('_XMAP_TIT_SETTINGS_OF',			'Preferinte pentru %s');
    define('_XMAP_TAB_SITEMAPS',			'Harti');
    define('_XMAP_MSG_NO_SITEMAPS',			'Nu a fost creata o harta a site-ului, inca');
    define('_XMAP_MSG_NO_SITEMAP',			'Aceasta harta nu este disponibila');
    define('_XMAP_MSG_LOADING_SETTINGS',		'Se incarca preferintele...');
    define('_XMAP_MSG_ERROR_LOADING_SITEMAP',		'EROARE. Harta nu a putut fi incarcata');
    define('_XMAP_MSG_ERROR_SAVE_PROPERTY',		'EROARE. Proprietatile hartii nu au putut fi salvate');
    define('_XMAP_MSG_ERROR_CLEAN_CACHE',		'EROARE. Cache-ul hartii nu a putut fi golit');
    define('_XMAP_ERROR_DELETE_DEFAULT',		'Harta implicita nu poate fi stearsa!');
    define('_XMAP_MSG_CACHE_CLEANED',			'Cache-ul a fost golit!');
    define('_XMAP_CHARSET',				'ISO-8859-1');
    define('_XMAP_SITEMAP_ID',				'ID-ul hartii');
    define('_XMAP_ADD_SITEMAP',				'Adauga harta');
    define('_XMAP_NAME_NEW_SITEMAP',			'Harta noua');
    define('_XMAP_DELETE_SITEMAP',			'Sterge');
    define('_XMAP_SETTINGS_SITEMAP',			'Preferinte');
    define('_XMAP_COPY_SITEMAP',			'Copiaza');
    define('_XMAP_SITEMAP_SET_DEFAULT',			'Seteaza implicit');
    define('_XMAP_EDIT_MENU',				'Optiuni');
    define('_XMAP_DELETE_MENU',				'Sterge');
    define('_XMAP_CLEAR_CACHE',				'Goleste cache');
    define('_XMAP_MOVEUP_MENU',		'Sus');
    define('_XMAP_MOVEDOWN_MENU',	'Jos');
    define('_XMAP_ADD_MENU',		'Adauga meniuri');
    define('_XMAP_COPY_OF',		'Copie a %s');
    define('_XMAP_INFO_LAST_VISIT',	'Ultima vizita');
    define('_XMAP_INFO_COUNT_VIEWS',	'Numarul de vizite');
    define('_XMAP_INFO_TOTAL_LINKS',	'Numarul de link-uri');
    define('_XMAP_CFG_URLS',		'URL-ul hartii');
    define('_XMAP_XML_LINK_TIP',	'Copiaza link-ul si trimite-l la Google si Yahoo');
    define('_XMAP_HTML_LINK_TIP',	'Acesta este URL-ul hartii. Il poti folosi pentru a crea entitati in meniul tau.');
    define('_XMAP_CFG_XML_MAP',		'Harta XML');
    define('_XMAP_CFG_HTML_MAP',	'Harta HTML');
    define('_XMAP_XML_LINK',		'link Google');
    define('_XMAP_CFG_XML_MAP_TIP',	'Fisierul XML generat pentru motoarele de cautare');
    define('_XMAP_ADD', 'Salveaza');
    define('_XMAP_CANCEL', 'Anuleaza');
    define('_XMAP_LOADING', 'Se incarca...');
    define('_XMAP_CACHE', 'Cache');
    define('_XMAP_USE_CACHE', 'Foloseste Cache');
    define('_XMAP_CACHE_LIFE_TIME', 'Durata de valabilitate a Cache-ului');
    define('_XMAP_NEVER_VISITED', 'Niciodata');


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
