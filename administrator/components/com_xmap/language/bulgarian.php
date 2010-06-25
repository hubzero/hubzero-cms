<?php 
/* @package xmap
 * @author Guillermo Vargas
 * @email guille@vargas.co.cr
 * @translator webg, http://www.seo.webg.org/
 */

defined( '_JEXEC' ) or die( 'Direct Access to this location is not allowed.' ); 

if( !defined( 'JOOMAP_LANG' )) {
    define('JOOMAP_LANG', 1 );
    // -- General ------------------------------------------------------------------
    define('_XMAP_CFG_COM_TITLE',			'Xmap Конфигуриране');
    define('_XMAP_CFG_OPTIONS',			'Външен вид');
    define('_XMAP_CFG_CSS_CLASSNAME',		'Име на CSS-клас');
    define('_XMAP_CFG_EXPAND_CATEGORIES',	'Разшири категориите на съдържанието');
    define('_XMAP_CFG_EXPAND_SECTIONS',	'Разшири секциите на съдържанието');
    define('_XMAP_CFG_SHOW_MENU_TITLES',	'Покажи заглавията в менюто');
    define('_XMAP_CFG_NUMBER_COLUMNS',	'Брой колони');
    define('_XMAP_EX_LINK',				'Маркирай вътрешните връзки');
    define('_XMAP_CFG_CLICK_HERE', 		'Кликни тук');
    define('_XMAP_CFG_GOOGLE_MAP',		'Google карта на сайта');
    define('_XMAP_EXCLUDE_MENU',			'Не включвай ID на менюто');
    define('_XMAP_TAB_DISPLAY',			'Покажи');
    define('_XMAP_TAB_MENUS',				'Менюта');
    define('_XMAP_CFG_WRITEABLE',			'Запис разрешен');
    define('_XMAP_CFG_UNWRITEABLE',		'Запис забранен');
    define('_XMAP_MSG_MAKE_UNWRITEABLE',	'След запис маркирай като [ <span style="color: red;">заключен</span> ]');
    define('_XMAP_MSG_OVERRIDE_WRITE_PROTECTION', 'При запис пренебрегва забраната за записване');
    define('_XMAP_GOOGLE_LINK',			'Google-връзка');
    define('_XMAP_CFG_INCLUDE_LINK',		'Невидим линк към автора');

    // -- Tips ---------------------------------------------------------------------
    define('_XMAP_EXCLUDE_MENU_TIP',		'Посочете ID-то на менютата, които не искате да бъдат включени в картата.<br /><strong>Забележка</strong><br />Разделете ID-тата със запетая!');

    // -- Menus --------------------------------------------------------------------
    define('_XMAP_CFG_SET_ORDER',			'Настройка за реда на показване на менютата');
    define('_XMAP_CFG_MENU_SHOW',			'Покажи');
    define('_XMAP_CFG_MENU_REORDER',		'Пренареждане');
    define('_XMAP_CFG_MENU_ORDER',		'Подредба');
    define('_XMAP_CFG_MENU_NAME',			'Име на менюто');
    define('_XMAP_CFG_DISABLE',			'Кликни за да го изключиш');
    define('_XMAP_CFG_ENABLE',			'Кликни за да го включиш');
    define('_XMAP_SHOW',					'Покажи');
    define('_XMAP_NO_SHOW',				'Не показвай');

    // -- Toolbar ------------------------------------------------------------------
    define('_XMAP_TOOLBAR_SAVE', 			'Запази');
    define('_XMAP_TOOLBAR_CANCEL', 		'Отказ');

    // -- Errors -------------------------------------------------------------------
    define('_XMAP_ERR_NO_LANG',			'Езиковия файл [ %s ] не е открит, включен е езика по подразбиране: english<br />');
    define('_XMAP_ERR_CONF_SAVE',         '<h2>ГРЕШКА: Failed to save the configuration.</h2>');
    define('_XMAP_ERR_NO_CREATE',         'ГРЕШКА: Неможе да създаде таблицата с настройки');
    define('_XMAP_ERR_NO_DEFAULT_SET',    'ГРЕШКА: Неможе да включи настройките по подразбиране');
    define('_XMAP_ERR_NO_PREV_BU',        'ВНИМАНИЕ: Неможе да възстанови предишен бекъп');
    define('_XMAP_ERR_NO_BACKUP',         'ГРЕШКА: Неможе да създаде бекъп');
    define('_XMAP_ERR_NO_DROP_DB',        'ГРЕШКА: Неможе да изтрие таблицата с настройки');
    define('_XMAP_ERR_NO_SETTINGS',		'ГРЕШКА: Неможе да включи настройките от БД: <a href="%s">Създаване на таблица с настройки</a>');

    // -- Config -------------------------------------------------------------------
    define('_XMAP_MSG_SET_RESTORED',      'Настройките са възстановени');
    define('_XMAP_MSG_SET_BACKEDUP',      'Настройките са запазени');
    define('_XMAP_MSG_SET_DB_CREATED',    'Таблицата с настройки е създадена');
    define('_XMAP_MSG_SET_DEF_INSERT',    'Включени са настройките по подразбиране');
    define('_XMAP_MSG_SET_DB_DROPPED','Xmap таблиците са запазени!');
	
    // -- CSS ----------------------------------------------------------------------
    define('_XMAP_CSS',					'Xmap CSS');
    define('_XMAP_CSS_EDIT',				'Редакция на шаблона'); // Edit template
	
    // -- Sitemap (Frontend) -------------------------------------------------------
    define('_XMAP_SHOW_AS_EXTERN_ALT',	'Отваря се връзката в нов прозорец');
	
    // -- Added for Xmap 
    define('_XMAP_CFG_MENU_SHOW_HTML',		'Покажи в HTML формат');
    define('_XMAP_CFG_MENU_SHOW_XML',		'Покажи в XML формат');
    define('_XMAP_CFG_MENU_PRIORITY',		'Приоритет');
    define('_XMAP_CFG_MENU_CHANGEFREQ',		'Промени честотата');
    define('_XMAP_CFG_CHANGEFREQ_ALWAYS',		'Винаги');
    define('_XMAP_CFG_CHANGEFREQ_HOURLY',		'Почасово');
    define('_XMAP_CFG_CHANGEFREQ_DAILY',		'Ежедневно');
    define('_XMAP_CFG_CHANGEFREQ_WEEKLY',		'Седмично');
    define('_XMAP_CFG_CHANGEFREQ_MONTHLY',		'Месечно');
    define('_XMAP_CFG_CHANGEFREQ_YEARLY',		'Годишно');
    define('_XMAP_CFG_CHANGEFREQ_NEVER',		'Никога');

    define('_XMAP_TIT_SETTINGS_OF',			'Настройки за %s');
    define('_XMAP_TAB_SITEMAPS',			'Карти');
    define('_XMAP_MSG_NO_SITEMAPS',			'Все още нямате създадени карти');
    define('_XMAP_MSG_NO_SITEMAP',			'Тази карта не съществува');
    define('_XMAP_MSG_LOADING_SETTINGS',		'Зареждане на настройките...');
    define('_XMAP_MSG_ERROR_LOADING_SITEMAP',		'Грешка. Неможе да зареди картата');
    define('_XMAP_MSG_ERROR_SAVE_PROPERTY',		'Грешка. Неможе да запази приоритета на картата.');
    define('_XMAP_MSG_ERROR_CLEAN_CACHE',		'Грешка. Неможе да изчисти кеша на картата');
    define('_XMAP_ERROR_DELETE_DEFAULT',		'Неможе да изтрие подразбиращата се карта!');
    define('_XMAP_MSG_CACHE_CLEANED',			'Кешът е изчистен!');
    define('_XMAP_CHARSET',				'utf-8');
    define('_XMAP_SITEMAP_ID',				'ID на Картата');
    define('_XMAP_ADD_SITEMAP',				'Добави Карта');
    define('_XMAP_NAME_NEW_SITEMAP',			'Нова Карта');
    define('_XMAP_DELETE_SITEMAP',			'Изтрий');
    define('_XMAP_SETTINGS_SITEMAP',			'Настройки');
    define('_XMAP_COPY_SITEMAP',			'Копирай');
    define('_XMAP_SITEMAP_SET_DEFAULT',			'Добави по подразбиране');
    define('_XMAP_EDIT_MENU',				'Опции');
    define('_XMAP_DELETE_MENU',				'Изтрий');
    define('_XMAP_CLEAR_CACHE',				'Изчисти кеша');
    define('_XMAP_MOVEUP_MENU',		'Нагоре');
    define('_XMAP_MOVEDOWN_MENU',	'Надолу');
    define('_XMAP_ADD_MENU',		'Добави менюта');
    define('_XMAP_COPY_OF',		'Копиране на %s');
    define('_XMAP_INFO_LAST_VISIT',	'Последно посещение');
    define('_XMAP_INFO_COUNT_VIEWS',	'Брой посещения');
    define('_XMAP_INFO_TOTAL_LINKS',	'Брой линкове');
    define('_XMAP_CFG_URLS',		'URL на Картата');
    define('_XMAP_XML_LINK_TIP',	'Копирай линка и го добави в Google и Yahoo');
    define('_XMAP_HTML_LINK_TIP',	'Това е URL-то на картата. Може да го използвате за добавяне на артикули към менюто.');
    define('_XMAP_CFG_XML_MAP',		'XML Карта');
    define('_XMAP_CFG_HTML_MAP',	'HTML Карта');
    define('_XMAP_XML_LINK',		'Googlelink');
    define('_XMAP_CFG_XML_MAP_TIP',	'The XML файлът генериран за търсачките');
    define('_XMAP_ADD', 'Запази');
    define('_XMAP_CANCEL', 'Отказ');
    define('_XMAP_LOADING', 'Зареждане...');
    define('_XMAP_CACHE', 'Кеш');
    define('_XMAP_USE_CACHE', 'Използване на кеша');
    define('_XMAP_CACHE_LIFE_TIME', 'Живот на кеша');
    define('_XMAP_NEVER_VISITED', 'Никога');

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
