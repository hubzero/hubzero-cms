<?php
/**
 * $Id: german.php 41 2009-07-23 20:50:14Z guilleva $
 * $LastChangedDate: 2009-07-23 14:50:14 -0600 (jue, 23 jul 2009) $
 * $LastChangedBy: guilleva $
 * Xmap by Guillermo Vargas
 * A sitemap component for Joomla! CMS (http://www.joomla.org)
 * Author Website: http://joomla.vargas.co.cr
 * Project License: GNU/GPL http://www.gnu.org/copyleft/gpl.html
 * Language file by Daniel Grothe, http://www.ko-ca.com/
*/

defined( '_JEXEC' ) or die( 'Direct Access to this location is not allowed.' );

if( !defined( 'JOOMAP_LANG' )) {
	define('JOOMAP_LANG', 1 );
	// -- General ------------------------------------------------------------------
	define('_XMAP_CFG_COM_TITLE',			'Xmap-Konfiguration');
	define('_XMAP_CFG_OPTIONS',			'Anzeige-Einstellungen');
	define('_XMAP_CFG_CSS_CLASSNAME',		'CSS-Klassenname');
	define('_XMAP_CFG_EXPAND_CATEGORIES',	'Kategorien ausklappen');
	define('_XMAP_CFG_EXPAND_SECTIONS',	'Bereiche ausklappen');
	define('_XMAP_CFG_SHOW_MENU_TITLES',	'Men&uuml;titel anzeigen');
	define('_XMAP_CFG_NUMBER_COLUMNS',	'Spaltenanzahl');
	define('_XMAP_EX_LINK',				'Externe Links markieren');
	define('_XMAP_CFG_CLICK_HERE', 		'Hier Klicken');
	define('_XMAP_CFG_GOOGLE_MAP',		'Google Sitemap');
	define('_XMAP_EXCLUDE_MENU',			'Men&uuml;-IDs ausschließen');
	define('_XMAP_TAB_DISPLAY',			'Anzeige');
	define('_XMAP_TAB_MENUS',				'Men&uuml;s');
	define('_XMAP_CFG_WRITEABLE',			'Beschreibbar');
	define('_XMAP_CFG_UNWRITEABLE',		'Nicht beschreibbar');
	define('_XMAP_MSG_MAKE_UNWRITEABLE',	'Nach dem Speicher auf Nur-Lesen setzen');
	define('_XMAP_MSG_OVERRIDE_WRITE_PROTECTION', 'Schreibschutz beim Speichern &uuml;berschreiben');
	define('_XMAP_GOOGLE_LINK',			'Google-Link');
	define('_XMAP_CFG_INCLUDE_LINK',		'Unsichtbarer Link zum Autoren');

	// -- Tips ---------------------------------------------------------------------
		define('_XMAP_EXCLUDE_MENU_TIP',		'Geben Sie die ID des Men&uuml;s an, das im Inhaltsverzeichnis eingetragen werden soll.<br /><strong>BEMERKUNG</strong><br />Mehrere IDs durch Kommata trennen!');
	// -- Menus --------------------------------------------------------------------
	define('_XMAP_CFG_SET_ORDER',			'Reihen folge der Men&uuml;-Anzeige');
	define('_XMAP_CFG_MENU_SHOW',			'Zeige');
	define('_XMAP_CFG_MENU_REORDER',		'Neu ordnen');
	define('_XMAP_CFG_MENU_ORDER',		'Reihenfolge');
	define('_XMAP_CFG_MENU_NAME',			'Name des Men&uuml;s');
	define('_XMAP_CFG_DISABLE',			'Zum Deaktivieren klicken');
	define('_XMAP_CFG_ENABLE',			'Zum Aktivieren klicken');
	define('_XMAP_SHOW',					'Anzeigen');
	define('_XMAP_NO_SHOW',				'Nicht anzeigen');

	// -- Toolbar ------------------------------------------------------------------
	define('_XMAP_TOOLBAR_SAVE', 			'Speichern');
	define('_XMAP_TOOLBAR_CANCEL', 		'Abbrechen');

	// -- Errors -------------------------------------------------------------------
	define('_XMAP_ERR_NO_LANG',			'Konnte die Sprachdatei [ %s ] nicht finden. Lade die Standardsprach: Englisch<br />');    
	define('_XMAP_ERR_CONF_SAVE',         'FEHELER: Konfiguration konnte nicht gespeichert werden.');
	define('_XMAP_ERR_NO_CREATE',         'FEHELER: Konnte Tabelle mit den Einstellungen nicht speichern.');    
	define('_XMAP_ERR_NO_DEFAULT_SET',    'FEHLER: Konnte Standardeinstellungen nicht einf&uuml;gen.');
	define('_XMAP_ERR_NO_PREV_BU',        'WARNUNG: Konnte vorherige Sicherung nicht l&ouml;schen.');    
	define('_XMAP_ERR_NO_BACKUP',         'FEHLER: Konnte Sicherung nicht erstellen.');    
	define('_XMAP_ERR_NO_DROP_DB',        'FEHLER: Konnte Tabelle mit den Einstellungen nicht l&ouml;schen.');    
	define('_XMAP_ERR_NO_SETTINGS',		'FEHLER: Konnte Einstellungen nicht aus der Datenbank laden: <a href="%s">Tablle f&uuml;r die Einstellungen erstellen</a>');
	

	// -- Config -------------------------------------------------------------------
	define('_XMAP_MSG_SET_RESTORED',      'Einstellungen wurden wieder hergestellt');
	define('_XMAP_MSG_SET_BACKEDUP',      'Einstellungen wurden gespeichert');
	define('_XMAP_MSG_SET_DB_CREATED',    'Tabelle f&uuml;r die Einstellungen wurde erstellt');
	define('_XMAP_MSG_SET_DEF_INSERT',    'Standardeinstellungen wurden eingef&uuml;gt');    
	define('_XMAP_MSG_SET_DB_DROPPED','Die Tabellen f&uuml;r Xmap wurden gespeichert!');
	
	// -- CSS ----------------------------------------------------------------------
	define('_XMAP_CSS',					'Xmap CSS');
	define('_XMAP_CSS_EDIT',				'Template editieren'); // Edit template
	
	// -- Sitemap (Frontend) -------------------------------------------------------
	define('_XMAP_SHOW_AS_EXTERN_ALT',	'Link &ouml;ffnet eine neues Fenster');
	
	// -- Added for Xmap 
	define('_XMAP_CFG_MENU_SHOW_HTML',		'Anzeige auf der Seite');    
	define('_XMAP_CFG_MENU_SHOW_XML',		'Anzeige in XML-Inhaltsverichnis');
	define('_XMAP_CFG_MENU_PRIORITY',		'Priorit&auml;t');
	define('_XMAP_CFG_MENU_CHANGEFREQ',		'H&auml;ufigkeit &auml;ndern');
	define('_XMAP_CFG_CHANGEFREQ_ALWAYS',		'Immer');
	define('_XMAP_CFG_CHANGEFREQ_HOURLY',		'St&uuml;ndlich');
	define('_XMAP_CFG_CHANGEFREQ_DAILY',		'T&auml;glich');
	define('_XMAP_CFG_CHANGEFREQ_WEEKLY',		'W&ouml;chentlich');
	define('_XMAP_CFG_CHANGEFREQ_MONTHLY',		'Monatlich');
	define('_XMAP_CFG_CHANGEFREQ_YEARLY',		'J&auml;hrlich');
	define('_XMAP_CFG_CHANGEFREQ_NEVER',		'Nie');
	define('_XMAP_TIT_SETTINGS_OF',			'Voreinstellungen f&uuml;r %s'); 
	define('_XMAP_TAB_SITEMAPS',			'Inhaltsverzeichnisse');
	define('_XMAP_MSG_NO_SITEMAPS',			'Bisher wurden noch keine Inhaltsverzeichnisse erstellt');
	define('_XMAP_MSG_NO_SITEMAP',			'Dieses Inhaltsverzeichnis ist nicht verf&uuml;gbar');
	define('_XMAP_MSG_LOADING_SETTINGS',		'Lade Voreinstellungen...'); 
	define('_XMAP_MSG_ERROR_LOADING_SITEMAP',		'Fehler. Kann Inhaltsverzeichnis nicht laden');
	define('_XMAP_MSG_ERROR_SAVE_PROPERTY',		'Fehler. Kann die Eigenschaften des Inhaltsverzeichnisses nicht speichrn.');
	define('_XMAP_MSG_ERROR_CLEAN_CACHE',		'Fehler. Kann den Zwischenspeicher des Inhaltsverzeichnisses nicht l&ouml;schen.');
	define('_XMAP_ERROR_DELETE_DEFAULT',		'Standard-Inhaltsverzeichnis kann nicht gel&ouml;scht werden!');
	define('_XMAP_MSG_CACHE_CLEANED',			'Zwischenspeicher gel&ouml;scht!');
	define('_XMAP_CHARSET',				'UTF-8');
	define('_XMAP_SITEMAP_ID',				'ID des Inhaltsverzeichnisses');
	define('_XMAP_ADD_SITEMAP',				'Inhaltsverzeichnis hinzuf&uuml;gen');
	define('_XMAP_NAME_NEW_SITEMAP',			'Neues Inhaltsverzeichnis');
	define('_XMAP_DELETE_SITEMAP',			'L&ouml;schen');
	define('_XMAP_SETTINGS_SITEMAP',			'Voreinstellungen'); 
	define('_XMAP_COPY_SITEMAP',			'Kopieren');
	define('_XMAP_SITEMAP_SET_DEFAULT',			'Als Standdard');
	define('_XMAP_EDIT_MENU',				'Optionen');
	define('_XMAP_DELETE_MENU',				'L&ouml;schen');
	define('_XMAP_CLEAR_CACHE',				'Zwischenspeicher l&ouml;schen');
	define('_XMAP_MOVEUP_MENU',		'Hoch');
	define('_XMAP_MOVEDOWN_MENU',	'Runter');
	define('_XMAP_ADD_MENU',		'Neues Men&uuml;');
	define('_XMAP_COPY_OF',		'Kopie von %s');
	define('_XMAP_INFO_LAST_VISIT',	'Letzter Aufruf');
	define('_XMAP_INFO_COUNT_VIEWS',	'Anzahl der Aufrufe');
	define('_XMAP_INFO_TOTAL_LINKS',	'Anzahl der Links');
	define('_XMAP_CFG_URLS',		'URL des Inhaltsverzeichnisses');
	define('_XMAP_XML_LINK_TIP',	'Link kopieren und an Google und Yahoo senden');
	define('_XMAP_HTML_LINK_TIP',	'Das ist die URL des Inhaltsverzeichnisses. Sie k&ouml;nnen diese benutzen, um Eintr&auml;ge in Men&uuml;s vorzunehmen.');
	define('_XMAP_CFG_XML_MAP',		'XML-Inhaltsverzeichnis');
	define('_XMAP_CFG_HTML_MAP',	'HTML-Inhaltsverzeichnis');
	define('_XMAP_XML_LINK',		'Google-Link');
	//define('_XMAP_CFG_XML_MAP_TIP',	'The XML file generated for the search engines');
	define('_XMAP_CFG_XML_MAP_TIP',	'Die XML-Datei wurde f&uuml;r die Suchmaschine generiert');    
	define('_XMAP_ADD', 'Speichern');
	define('_XMAP_CANCEL', 'Abbrechen');
	define('_XMAP_LOADING', 'Lade...');
	define('_XMAP_CACHE', 'Zwischenspeicher');
	define('_XMAP_USE_CACHE', 'Zwischenspeichern');
	define('_XMAP_CACHE_LIFE_TIME', 'Speicherzeit');
	define('_XMAP_NEVER_VISITED', 'Nie');

	// New on Xmap 1.1 beta 1
	define('_XMAP_PLUGINS','Plugins');	
	define( '_XMAP_INSTALL_3PD_WARN', 'Warnung: Die Installation von Erweiterungen Dritter kann die Sicherheit des Servers beeintr&auml;chtigen.' );
	define('_XMAP_INSTALL_NEW_PLUGIN', 'Neue Plugins installieren');
	define('_XMAP_UNKNOWN_AUTHOR','Unbekannter Autor');
	define('_XMAP_PLUGIN_VERSION','Version %s');
	define('_XMAP_TAB_INSTALL_PLUGIN','Installieren');
	define('_XMAP_TAB_EXTENSIONS','Erweiterungen');
	define('_XMAP_TAB_INSTALLED_EXTENSIONS','Installierte Erweiterungen');
	define('_XMAP_NO_PLUGINS_INSTALLED','Kein benutzerdefiniertes Plugin installiert');
	define('_XMAP_AUTHOR','Author');
	define('_XMAP_CONFIRM_DELETE_SITEMAP','M&ouml;chten Sie diese Sitemap wirklich l&ouml;schen?');
	define('_XMAP_CONFIRM_UNINSTALL_PLUGIN','M&ouml;chten Sie dieses Plugin wirklich deinstallieren?');
	define('_XMAP_UNINSTALL','Deinstallieren');
	define('_XMAP_EXT_PUBLISHED','Ver&ouml;ffentlicht');
	define('_XMAP_EXT_UNPUBLISHED','Unver&ouml;ffentlicht');
	define('_XMAP_PLUGIN_OPTIONS','Optionen');
	define('_XMAP_EXT_INSTALLED_MSG','Die Erweiterung wurde erfolgreich installiert, bitte &uuml;berp&uuml;fen sie deren Einstellungen und ver&ouml;ffentlichen Sie die Erweiterung anschlie?end');
	define('_XMAP_CONTINUE','Fortfahren...');
	define('_XMAP_MSG_EXCLUDE_CSS_SITEMAP','Die CSS Datei nicht f&uuml;r die Sitemap verwenden');
	define('_XMAP_MSG_EXCLUDE_XSL_SITEMAP','Klassische XML Sitemap Anzeige verwenden');

	// New on Xmap 1.1
	define('_XMAP_MSG_SELECT_FOLDER','Bitte w&auml;hlen Sie ein Verzeichnis aus');
	define('_XMAP_UPLOAD_PKG_FILE','Dateien hochladen');
	define('_XMAP_UPLOAD_AND_INSTALL','Dateien hochladen und installieren');
	define('_XMAP_INSTALL_F_DIRECTORY','Aus lokalem Verzeichnis installieren');
	define('_XMAP_INSTALL_DIRECTORY','Installationsverzeichnis');
	define('_XMAP_INSTALL','Installieren');
	define('_XMAP_WRITEABLE','Beschreibbar');
	define('_XMAP_UNWRITEABLE','Schreibgesch&uuml;tzt');

	// New on Xmap 1.2
	define('_XMAP_COMPRESSION','Kompression');
   	define('_XMAP_USE_COMPRESSION','Komprimieren Sie die XML-Sitemap, um Bandbreite zu sparen');
	
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
