<?php
/**
 * @package Xmap
 * @author Guillermo Vargas, http://joomla.vargas.co.cr/
 * @Translated by Čolović Vladan, http://www.cvladan.com
*/

defined( '_JEXEC' ) or die( 'Pristup ovoj lokaciji nije dozvoljen' );

if( !defined( 'JOOMAP_LANG' ))
{
	define('JOOMAP_LANG', 1 );
	// -- General ------------------------------------------------------------------
	define('_XMAP_CFG_COM_TITLE',				'Xmap podešavanja');
	define('_XMAP_CFG_OPTIONS',					'Opcije prikaza');
	define('_XMAP_CFG_CSS_CLASSNAME',			'CSS ime class-e');
	define('_XMAP_CFG_EXPAND_CATEGORIES',		'Prikaži kategorije');
	define('_XMAP_CFG_EXPAND_SECTIONS',			'Prikaži sekcije');
	define('_XMAP_CFG_SHOW_MENU_TITLES',		'Prikaži imena menija');
	define('_XMAP_CFG_NUMBER_COLUMNS',			'Broj kolona');
	define('_XMAP_EX_LINK',						'Obeleži spoljne veze van domena');
	define('_XMAP_CFG_CLICK_HERE',				'Klikni ovdje');
	define('_XMAP_CFG_GOOGLE_MAP',				'Google mapa');
	define('_XMAP_EXCLUDE_MENU',				'Izuzmi ID-je menija');
	define('_XMAP_TAB_DISPLAY',					'Prikaži');
	define('_XMAP_TAB_MENUS',					'Meniji');
	define('_XMAP_CFG_WRITEABLE',				'Dozvoljen upis');
	define('_XMAP_CFG_UNWRITEABLE',				'Nije dozvoljeno upisivanje');
	define('_XMAP_MSG_MAKE_UNWRITEABLE',			'Onemogući upis nakon snimanja');
	define('_XMAP_MSG_OVERRIDE_WRITE_PROTECTION',	'Zaobiđi zaštitu od snimanja');
	define('_XMAP_GOOGLE_LINK',						'Google link');
	define('_XMAP_CFG_INCLUDE_LINK',				'Prikaži sakriveni link ka autoru');

	// -- Tips ---------------------------------------------------------------------
	define('_XMAP_EXCLUDE_MENU_TIP',		'Navedi ID stavki menija koji ne treba da se nalaze u mapi.<br /><strong>NAPOMENA:</strong> ID brojeve razdvoj zarezima!');

	// -- Menus --------------------------------------------------------------------
	define('_XMAP_CFG_SET_ORDER',		'Postavi redosled prikazivanja menija');
	define('_XMAP_CFG_MENU_SHOW',		'Prikaži');
	define('_XMAP_CFG_MENU_REORDER',	'Novi redosled');
	define('_XMAP_CFG_MENU_ORDER',		'Redosled');
	define('_XMAP_CFG_MENU_NAME',		'Ime menija');
	define('_XMAP_CFG_DISABLE',			'Isključi');
	define('_XMAP_CFG_ENABLE',			'Uključi');
	define('_XMAP_SHOW',				'Prikaži');
	define('_XMAP_NO_SHOW',				'Sakrij');

	// -- Toolbar ------------------------------------------------------------------
	define('_XMAP_TOOLBAR_SAVE',		'Sačuvaj');
	define('_XMAP_TOOLBAR_CANCEL',		'Otkaži');

	// -- Errors -------------------------------------------------------------------
	define('_XMAP_ERR_NO_LANG',			'Jezička datoteka [ %s ] nije pronađena, pa se koristi podrazumevani jezik: english<br />');
	define('_XMAP_ERR_CONF_SAVE',		'GREŠKA: Snimanje konfiguracije nije uspelo');
	define('_XMAP_ERR_NO_CREATE',		'GREŠKA: Nemoguće je napraviti tabelu sa podešavanjima');
	define('_XMAP_ERR_NO_DEFAULT_SET',	'GREŠKA: Nemoguće je ubaciti podrazumevane postavke');
	define('_XMAP_ERR_NO_PREV_BU',		'UPOZORENJE: Nije moguće ukloniti prethodnu sigurnosnu kopiju');
	define('_XMAP_ERR_NO_BACKUP',		'GREŠKA: Nije moguće napraviti sigurnosnu kopiju');
	define('_XMAP_ERR_NO_DROP_DB',		'GREŠKA: Nije moguće ukloniti tabelu sa podešavanjima');
	define('_XMAP_ERR_NO_SETTINGS',		'GREŠKA: Nije moguće učitati podešavanja iz baze podataka: <a href="%s">Napravi novu tabelu podešavanja</a>');

	// -- Config -------------------------------------------------------------------
	define('_XMAP_MSG_SET_RESTORED',	'Podešavanja su povraćena u početno stanje');
	define('_XMAP_MSG_SET_BACKEDUP',	'Podešavanja snimljena');
	define('_XMAP_MSG_SET_DB_CREATED',	'Tabela podešavanja je kreirana');
	define('_XMAP_MSG_SET_DEF_INSERT',	'Ubačena su podrazumevana podešavanja');
	define('_XMAP_MSG_SET_DB_DROPPED',	'Tabela sa podešavanjima je obrisana!');

	// -- CSS ----------------------------------------------------------------------
	define('_XMAP_CSS',					'Xmap CSS');
	define('_XMAP_CSS_EDIT',			'Izmeni template'); // Edit template

	// -- Sitemap (Frontend) -------------------------------------------------------
	define('_XMAP_SHOW_AS_EXTERN_ALT',		'Otvaraj linkove u novom prozoru');

	// -- Added for Xmap
	define('_XMAP_CFG_MENU_SHOW_HTML',		'Prikaži u HTML strani');
	define('_XMAP_CFG_MENU_SHOW_XML',		'Prikaži u XML sitemapu');
	define('_XMAP_CFG_MENU_PRIORITY',		'Prioritet');
	define('_XMAP_CFG_MENU_CHANGEFREQ',		'Promeni učestalost');
	define('_XMAP_CFG_CHANGEFREQ_ALWAYS',	'Uvek');
	define('_XMAP_CFG_CHANGEFREQ_HOURLY',	'Svakoga časa');
	define('_XMAP_CFG_CHANGEFREQ_DAILY',	'Dnevno');
	define('_XMAP_CFG_CHANGEFREQ_WEEKLY',	'Sedmično');
	define('_XMAP_CFG_CHANGEFREQ_MONTHLY',	'Mesečno');
	define('_XMAP_CFG_CHANGEFREQ_YEARLY',	'Godišnje');
	define('_XMAP_CFG_CHANGEFREQ_NEVER',	'Nikad');

	define('_XMAP_TIT_SETTINGS_OF',				'Podešavanja za %s');
	define('_XMAP_TAB_SITEMAPS',				'Sitemap-e');
	define('_XMAP_MSG_NO_SITEMAPS',				'Nema kreiranih mapa');
	define('_XMAP_MSG_NO_SITEMAP',				'Mapa je nedostupna');
	define('_XMAP_MSG_LOADING_SETTINGS',		'Učitavanje podešavanja...');
	define('_XMAP_MSG_ERROR_LOADING_SITEMAP',	'Greška: Nije moguće učitati mapu');
	define('_XMAP_MSG_ERROR_SAVE_PROPERTY',		'Greška: Nije moguće snimiti mapu');
	define('_XMAP_MSG_ERROR_CLEAN_CACHE',		'Greška: Nije moguće obrisati cache');
	define('_XMAP_ERROR_DELETE_DEFAULT',		'Greška: Nije moguće izbrisati podrazumevanu mapu!');
	define('_XMAP_MSG_CACHE_CLEANED',			'Cache je obrisan');
	define('_XMAP_CHARSET',						'utf-8');
	define('_XMAP_SITEMAP_ID',					'ID oznaka mape');
	define('_XMAP_ADD_SITEMAP',					'Dodaj mapu');
	define('_XMAP_NAME_NEW_SITEMAP',			'Nova mapa');
	define('_XMAP_DELETE_SITEMAP',				'Obriši');
	define('_XMAP_SETTINGS_SITEMAP',			'Podešavanja');
	define('_XMAP_COPY_SITEMAP',				'Kopiraj');
	define('_XMAP_SITEMAP_SET_DEFAULT',			'Podrazumevana');
	define('_XMAP_EDIT_MENU',					'Izmeni');
	define('_XMAP_DELETE_MENU',					'Obriši');
	define('_XMAP_CLEAR_CACHE',					'Očisti cache');
	define('_XMAP_MOVEUP_MENU',					'Gore');
	define('_XMAP_MOVEDOWN_MENU',	   			'Dole');
	define('_XMAP_ADD_MENU',			   		'Dodaj menije');
	define('_XMAP_COPY_OF',						'Kopija od %s');
	define('_XMAP_INFO_LAST_VISIT',	   			'Poslednja poseta');
	define('_XMAP_INFO_COUNT_VIEWS',	 		'Broj poseta');
	define('_XMAP_INFO_TOTAL_LINKS',	  		'Broj linkova');
	define('_XMAP_CFG_URLS',			   		'URL-ovi mape');
	define('_XMAP_XML_LINK_TIP',				'Kopiraj link i postavi ga na Google i Yahoo');
	define('_XMAP_HTML_LINK_TIP',				'Ovo je URL mape, možete ga koristiti prilikom kreiranja stavki u menijima');
	define('_XMAP_CFG_XML_MAP',					'XML mapa sajta');
	define('_XMAP_CFG_HTML_MAP',				'HTML mapa sajta');
	define('_XMAP_XML_LINK',					'Googlelink');
	define('_XMAP_CFG_XML_MAP_TIP',				'XML datoteka je generisana za pretraživače');
	define('_XMAP_ADD',							'Snimi');
	define('_XMAP_CANCEL',						'Odustani');
	define('_XMAP_LOADING',						'Učitavam...');
	define('_XMAP_CACHE',						'Cache');
	define('_XMAP_USE_CACHE',					'Koristi cache');
	define('_XMAP_CACHE_LIFE_TIME',				'Cache trajanje');
	define('_XMAP_NEVER_VISITED',				'Nikad');


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
