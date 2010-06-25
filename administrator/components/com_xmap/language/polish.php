<?php 
/* @package Xmap
 * @author Guillermo Vargas, http://joomla.vargas.co.cr
 */

defined( '_JEXEC' ) or die( 'Direct Access to this location is not allowed.' );

if( !defined( 'JOOMAP_LANG' )) {
    define('JOOMAP_LANG', 1 );
    // -- General ------------------------------------------------------------------
    define('_XMAP_CFG_COM_TITLE',			'Konfiguracja Xmap');
    define('_XMAP_CFG_OPTIONS',			'Opcje wyswietlania');
    define('_XMAP_CFG_CSS_CLASSNAME',		'Nazwa klasy CSS');
    define('_XMAP_CFG_EXPAND_CATEGORIES',	'Rozwin kategorie zawartosci');
    define('_XMAP_CFG_EXPAND_SECTIONS',	'Rozwin sekcje zawartosci');
    define('_XMAP_CFG_SHOW_MENU_TITLES',	'Pokaz tytuly menu');
    define('_XMAP_CFG_NUMBER_COLUMNS',	'Liczba kolumn');
    define('_XMAP_EX_LINK',				'Zaznacz linki zewnetrzne');
    define('_XMAP_CFG_CLICK_HERE', 		'Kliknij tutaj');
    define('_XMAP_CFG_GOOGLE_MAP',		'Google Sitemap');
    define('_XMAP_EXCLUDE_MENU',			'Wyklucz ID pozycji w menu');
    define('_XMAP_TAB_DISPLAY',			'Wyswietl');
    define('_XMAP_TAB_MENUS',				'Menu');
    define('_XMAP_CFG_WRITEABLE',			'Zapisywalny');
    define('_XMAP_CFG_UNWRITEABLE',		'Niezapisywalny');
    define('_XMAP_MSG_MAKE_UNWRITEABLE',	'Ustaw niezapisywalny po zapisaniu');
    define('_XMAP_MSG_OVERRIDE_WRITE_PROTECTION', 'Pomin ograniczenia zapisu podczas zapisywania');
    define('_XMAP_GOOGLE_LINK',			'Googlelink');
    define('_XMAP_CFG_INCLUDE_LINK',		'Pokaz link do autora');

    // -- Tips ---------------------------------------------------------------------
    define('_XMAP_EXCLUDE_MENU_TIP',		'Wybierz te ID pozycji w menu, które chcesz wykluczyc z mapy strony.<br 
/><strong>UWAGA:</strong><br />Oddzielaj ID przecinkami!');

    // -- Menus --------------------------------------------------------------------
    define('_XMAP_CFG_SET_ORDER',			'Ustaw kolejnosc wyswietlania menu');
    define('_XMAP_CFG_MENU_SHOW',			'Wyswietl');
    define('_XMAP_CFG_MENU_REORDER',		'Ulóz ponownie');
    define('_XMAP_CFG_MENU_ORDER',		'Ulóz');
    define('_XMAP_CFG_MENU_NAME',			'Nazwa Menu');
    define('_XMAP_CFG_DISABLE',			'Kliknij aby wylaczyc');
    define('_XMAP_CFG_ENABLE',			'Kliknij aby wlaczyc');
    define('_XMAP_SHOW',					'Wyswietl');
    define('_XMAP_NO_SHOW',				'Nie wyswietlaj');

    // -- Toolbar ------------------------------------------------------------------
    define('_XMAP_TOOLBAR_SAVE', 			'Zapisz');
    define('_XMAP_TOOLBAR_CANCEL', 		'Zrezygnuj');

    // -- Errors -------------------------------------------------------------------
    define('_XMAP_ERR_NO_LANG',			' Nie znaleziono pliku jezyka [ %s ], zaladowano jezyk domyslny: Polish<br 
/>');
    define('_XMAP_ERR_CONF_SAVE',         'ERROR: Nie udalo sie zapisac konfiguracji.');
    define('_XMAP_ERR_NO_CREATE',         'ERROR: Nie udalo sie utworzyc tabeli ustawien');
    define('_XMAP_ERR_NO_DEFAULT_SET',    'ERROR: Nie udalo sie wpisac Ustawien domyslnych');
    define('_XMAP_ERR_NO_PREV_BU',        'WARNING: Nie udalo sie usunac poprzedniej kopii zapasowej');
    define('_XMAP_ERR_NO_BACKUP',         'ERROR: Nie udalo sie utworzyc kopii zapasowej');
    define('_XMAP_ERR_NO_DROP_DB',        'ERROR: Nie udalo sie usunac yabeli ustawien');
    define('_XMAP_ERR_NO_SETTINGS',		'ERROR: Nie udalo sie zaladowac ustawien z bazy danych: <a href="%s">Utwórz 
tabele ustawien</a>');

    // -- Config -------------------------------------------------------------------
    define('_XMAP_MSG_SET_RESTORED',      'Ustawienia przywrócono');
    define('_XMAP_MSG_SET_BACKEDUP',      'Ustawienia zapisano');
    define('_XMAP_MSG_SET_DB_CREATED',    'Utworzono tabele ustawien');
    define('_XMAP_MSG_SET_DEF_INSERT',    'Wpisano ustawienia domyslne');
    define('_XMAP_MSG_SET_DB_DROPPED','Tabele Xmap zostaly zapisane!');
	
    // -- CSS ----------------------------------------------------------------------
    define('_XMAP_CSS',					'CSS Xmap');
    define('_XMAP_CSS_EDIT',				'Edytuj szablon'); // Edit template
	
    // -- Sitemap (Frontend) -------------------------------------------------------
    define('_XMAP_SHOW_AS_EXTERN_ALT',	'Linki otwieraja nowe okno');
	
    // -- Added for Xmap 
    define('_XMAP_CFG_MENU_SHOW_HTML',		'Pokazane na stronie');
    define('_XMAP_CFG_MENU_SHOW_XML',		'Pokazane w mapie strony XML');
    define('_XMAP_CFG_MENU_PRIORITY',		'Prioritet');
    define('_XMAP_CFG_MENU_CHANGEFREQ',		'Zmien czestotliwosc');
    define('_XMAP_CFG_CHANGEFREQ_ALWAYS',		'Zawsze');
    define('_XMAP_CFG_CHANGEFREQ_HOURLY',		'Co godzine');
    define('_XMAP_CFG_CHANGEFREQ_DAILY',		'Codziennie');
    define('_XMAP_CFG_CHANGEFREQ_WEEKLY',		'Co tydzien');
    define('_XMAP_CFG_CHANGEFREQ_MONTHLY',		'Co miesiac');
    define('_XMAP_CFG_CHANGEFREQ_YEARLY',		'Co rok');
    define('_XMAP_CFG_CHANGEFREQ_NEVER',		'Brak');

    define('_XMAP_TIT_SETTINGS_OF',			'Preferencje dla %s');
    define('_XMAP_TAB_SITEMAPS',			'Mapy strony');
    define('_XMAP_MSG_NO_SITEMAPS',			'Nie ma jeszcze utworzonych map strony');
    define('_XMAP_MSG_NO_SITEMAP',			'Ta mapa strony jest niedostepna');
    define('_XMAP_MSG_LOADING_SETTINGS',		'Ladowanie preferencji...');
    define('_XMAP_MSG_ERROR_LOADING_SITEMAP',		'Blad. Nie mozna zaladowac mapy strony');
    define('_XMAP_MSG_ERROR_SAVE_PROPERTY',		'Blad. Nie mozna zapisac wlasciwosci mapy strony.');
    define('_XMAP_MSG_ERROR_CLEAN_CACHE',		'Blad. Nie mozna wyczyscic pamieci podrecznej mapy strony');
    define('_XMAP_ERROR_DELETE_DEFAULT',		'Nie mozna skasowac domyslnej mapy strony!');
    define('_XMAP_MSG_CACHE_CLEANED',			'Pamiec podreczna zostala wyczyszczona!');
    define('_XMAP_CHARSET',				'ISO-8859-2');
    define('_XMAP_SITEMAP_ID',				'ID mapy strony');
    define('_XMAP_ADD_SITEMAP',				'Dodaj mape strony');
    define('_XMAP_NAME_NEW_SITEMAP',			'Nowa mapa strony');
    define('_XMAP_DELETE_SITEMAP',			'Usun');
    define('_XMAP_SETTINGS_SITEMAP',			'Preferencje');
    define('_XMAP_COPY_SITEMAP',			'Kopiuj');
    define('_XMAP_SITEMAP_SET_DEFAULT',			'Ustawienia domyslne');
    define('_XMAP_EDIT_MENU',				'Opcje');
    define('_XMAP_DELETE_MENU',				'Usun');
    define('_XMAP_CLEAR_CACHE',				'Wyczysc pamiec podreczna');
    define('_XMAP_MOVEUP_MENU',		'Do góry');
    define('_XMAP_MOVEDOWN_MENU',	'Do dolu');
    define('_XMAP_ADD_MENU',		'Dodaj menu');
    define('_XMAP_COPY_OF',		'Kopia %s');
    define('_XMAP_INFO_LAST_VISIT',	'Ostatnie wejscie');
    define('_XMAP_INFO_COUNT_VIEWS',	'Liczba wejsc');
    define('_XMAP_INFO_TOTAL_LINKS',	'Liczba linków');
    define('_XMAP_CFG_URLS',		'URL mapy strony');
    define('_XMAP_XML_LINK_TIP',	'Skopiuj link I wyslij go do Google i Yahoo');
    define('_XMAP_HTML_LINK_TIP',	'To jest URL mapy strony. Mozesz go uzyc aby dodac nowa pozycje w Twoim menu.');
    define('_XMAP_CFG_XML_MAP',		'Format XML');
    define('_XMAP_CFG_HTML_MAP',	'Format HTML');
    define('_XMAP_XML_LINK',		'Googlelink');
    define('_XMAP_CFG_XML_MAP_TIP',	'Plik XML utworzony na potrzeby wyszukiwarek');
    define('_XMAP_ADD', 'Zapisz');
    define('_XMAP_CANCEL', 'Zrezygnuj');
    define('_XMAP_LOADING', 'Laduje...');
    define('_XMAP_CACHE', 'Pamiec podreczna');
    define('_XMAP_USE_CACHE', 'Uzywaj pamieci podrecznej');
    define('_XMAP_CACHE_LIFE_TIME', 'Czas wygasania zawartosci pamieci podrecznej');
    define('_XMAP_NEVER_VISITED', 'Brak');

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
