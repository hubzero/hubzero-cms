<?php 
/**
 * $Id: italian.php 41 2009-07-23 20:50:14Z guilleva $
 * $LastChangedDate: 2009-07-23 14:50:14 -0600 (jue, 23 jul 2009) $
 * $LastChangedBy: guilleva $
 * Xmap by Guillermo Vargas
 * A sitemap component for Joomla! CMS (http://www.joomla.org)
 * Author Website: http://joomla.vargas.co.cr
 * Project License: GNU/GPL http://www.gnu.org/copyleft/gpl.html
*/

defined( '_JEXEC' ) or die( 'Direct Access to this location is not allowed.' );

if( !defined( 'JOOMAP_LANG' )) {
	define('JOOMAP_LANG', 1 );
	// -- General ------------------------------------------------------------------
	define('_XMAP_CFG_OPTIONS',			'Visualizza Opzionie');
	define('_XMAP_CFG_CSS_CLASSNAME',		'Classname CSS');
	define('_XMAP_CFG_EXPAND_CATEGORIES',	'Espandi Contenuto Categorie');
	define('_XMAP_CFG_EXPAND_SECTIONS',	'Espandi Contenuto Sezioni');
	define('_XMAP_CFG_SHOW_MENU_TITLES',	'Visualizza Titoli Menu');
	define('_XMAP_CFG_NUMBER_COLUMNS',	'Numero di colonne');
	define('_XMAP_EX_LINK',				'Seleziona link esterni');
	define('_XMAP_CFG_CLICK_HERE', 		'Clicca qui');
	define('_XMAP_CFG_GOOGLE_MAP',		'Google Sitemap');
	define('_XMAP_EXCLUDE_MENU',			'Escludi Menu IDs');
	define('_XMAP_TAB_DISPLAY',			'Visualizza');
	define('_XMAP_TAB_MENUS',				'Menu');
	define('_XMAP_CFG_WRITEABLE',			'Scrivibile');
	define('_XMAP_CFG_UNWRITEABLE',		'Non Scrivibile');
	define('_XMAP_MSG_MAKE_UNWRITEABLE',	'Dopo il salvataggio imposta come non scrivibile');
	define('_XMAP_MSG_OVERRIDE_WRITE_PROTECTION', 'Sovrascri protezione scrittura, mentre salvi');
	define('_XMAP_GOOGLE_LINK',			'Googlelink');
	define('_XMAP_CFG_INCLUDE_LINK',		'Includi link autore');

	// -- Tips ---------------------------------------------------------------------
	define('_XMAP_EXCLUDE_MENU_TIP',		'Specifica gli IDs dei menu che non vuoi includere nella sitemap.<br /><strong>NOTA</strong><br />Separa gli IDs con la virgola!');

	// -- Menus --------------------------------------------------------------------
	define('_XMAP_CFG_SET_ORDER',			'Modifica l’ordine del menu');
	define('_XMAP_CFG_MENU_SHOW',			'Visualizza');
	define('_XMAP_CFG_MENU_REORDER',		'Riordina');
	define('_XMAP_CFG_MENU_ORDER',		'Ordina');
	define('_XMAP_CFG_MENU_NAME',			'Nome Menu');
	define('_XMAP_CFG_DISABLE',			'Clicca per disabilitare');
	define('_XMAP_CFG_ENABLE',			'Clicca per abilitare');
	define('_XMAP_SHOW',					'Visualizza');
	define('_XMAP_NO_SHOW',				'Non visualizzare');

	// -- Toolbar ------------------------------------------------------------------
	define('_XMAP_TOOLBAR_SAVE', 			'Salva');
	define('_XMAP_TOOLBAR_CANCEL', 			'Cancella');

	// -- Errors -------------------------------------------------------------------
	define('_XMAP_ERR_NO_LANG',			'Lingua file [ %s ] non trovato, è stata caricata la lingua predefinita: inglese<br />');
	define('_XMAP_ERR_CONF_SAVE',         'ERRORE: Impossibile salvare la configurazione.');
	define('_XMAP_ERR_NO_CREATE',         'ERRORE: Impossibile creare le tabelle di configurazione');
	define('_XMAP_ERR_NO_DEFAULT_SET',    'ERRORE: Impossibile inserire le configurazioni predefinite');
	define('_XMAP_ERR_NO_PREV_BU',        'ATTENZIONE: Impossibile cancellare backup precedenti');
	define('_XMAP_ERR_NO_BACKUP',         'ERRORE: Impossibile creare un backup');
	define('_XMAP_ERR_NO_DROP_DB',        'ERRORE: Impossibile cancellare le tabelle di configurazione');
	define('_XMAP_ERR_NO_SETTINGS',		'ERRORE: Impossibile caricare le impostazioni dal database: <a href="%s">Crea impostazioni tabella</a>');

	// -- Config -------------------------------------------------------------------
	define('_XMAP_MSG_SET_RESTORED',      'Impostazioni ristabilite');
	define('_XMAP_MSG_SET_BACKEDUP',      'Impostazioni salvate');
	define('_XMAP_MSG_SET_DB_CREATED',    'Impostazioni tabella creata');
	define('_XMAP_MSG_SET_DEF_INSERT',    'Impostazioni predefinite inserite');
	define('_XMAP_MSG_SET_DB_DROPPED','Xmap le tabelle sono state salvate!');
	
	// -- CSS ----------------------------------------------------------------------
	define('_XMAP_CSS',					'Xmap CSS');
	define('_XMAP_CSS_EDIT',				'Modifica template'); // Edit template
	
	// -- Sitemap (Frontend) -------------------------------------------------------
	define('_XMAP_SHOW_AS_EXTERN_ALT',	'Apri il link in una nuova finestra');
	
	// -- Added for Xmap 
	define('_XMAP_CFG_MENU_SHOW_HTML',		'Mostra nel sito');
	define('_XMAP_CFG_MENU_SHOW_XML',		'Mostra in formato XML');
	define('_XMAP_CFG_MENU_PRIORITY',		'Priorità');
	define('_XMAP_CFG_MENU_CHANGEFREQ',		'Frequenza aggiornamenti');
	define('_XMAP_CFG_CHANGEFREQ_ALWAYS',		'Sempre');
	define('_XMAP_CFG_CHANGEFREQ_HOURLY',		'Ogni ora');
	define('_XMAP_CFG_CHANGEFREQ_DAILY',		'Quotidianamente');
	define('_XMAP_CFG_CHANGEFREQ_WEEKLY',		'Settimanalmente');
	define('_XMAP_CFG_CHANGEFREQ_MONTHLY',		'Mensilmente');
	define('_XMAP_CFG_CHANGEFREQ_YEARLY',		'Annualmente');
	define('_XMAP_CFG_CHANGEFREQ_NEVER',		'Mai');

	define('_XMAP_TIT_SETTINGS_OF',			'Preferenze per %s');
	define('_XMAP_TAB_SITEMAPS',			'Sitemap');
	define('_XMAP_MSG_NO_SITEMAPS',			'Non sono state ancora create sitemap');
	define('_XMAP_MSG_NO_SITEMAP',			'Questa sitemap non è disponibile');
	define('_XMAP_MSG_LOADING_SETTINGS',		'Caricamento preferenze...');
	define('_XMAP_MSG_ERROR_LOADING_SITEMAP',		'Errore. Impossibile caricare la sitemap');
	define('_XMAP_MSG_ERROR_SAVE_PROPERTY',		'Errore. Impossibile salvare le proprietà della sitemap.');
	define('_XMAP_MSG_ERROR_CLEAN_CACHE',		'Errore. Impossibile pulire la cache della sitemap');
	define('_XMAP_ERROR_DELETE_DEFAULT',		'Impossibile eliminare la sitemap predefinta!');
	define('_XMAP_MSG_CACHE_CLEANED',			'Cache eliminata!');
	define('_XMAP_CHARSET',				'UTF-8');
	define('_XMAP_SITEMAP_ID',				'ID Sitemap');
	define('_XMAP_ADD_SITEMAP',				'Aggiungi Sitemap');
	define('_XMAP_NAME_NEW_SITEMAP',			'Nuova Sitemap');
	define('_XMAP_DELETE_SITEMAP',			'Cancella');
	define('_XMAP_SETTINGS_SITEMAP',			'Preferenze');
	define('_XMAP_COPY_SITEMAP',			'Copia');
	define('_XMAP_SITEMAP_SET_DEFAULT',			'Applica predefinita');
	define('_XMAP_EDIT_MENU',				'Opzioni');
	define('_XMAP_DELETE_MENU',				'Cancella');
	define('_XMAP_CLEAR_CACHE',				'Cancella cache');
	define('_XMAP_MOVEUP_MENU',		'Su');
	define('_XMAP_MOVEDOWN_MENU',	'Giù');
	define('_XMAP_ADD_MENU',		'Aggiungi menu');
	define('_XMAP_COPY_OF',		'Copia di %s');
	define('_XMAP_INFO_LAST_VISIT',	'Ultima visita');
	define('_XMAP_INFO_COUNT_VIEWS',	'Numero di visite');
	define('_XMAP_INFO_TOTAL_LINKS',	'Numero di link');
	define('_XMAP_CFG_URLS',		'URL Sitemap');
	define('_XMAP_XML_LINK_TIP',	'Copia link e segnala a Google e Yahoo');
	define('_XMAP_HTML_LINK_TIP',	'Questo è l’URL della Sitemap. Puoi utilizzarlo per creare delle voci nel tuo menu.');
	define('_XMAP_CFG_XML_MAP',		'XML Sitemap');
	define('_XMAP_CFG_HTML_MAP',	'HTML Sitemap');
	define('_XMAP_XML_LINK',		'Googlelink');
	define('_XMAP_CFG_XML_MAP_TIP',	'Il file XML generato per i motori di ricerca');
	define('_XMAP_ADD', 'Salva');
	define('_XMAP_CANCEL', 'Annula');
	define('_XMAP_LOADING', 'Caricamento...');
	define('_XMAP_CACHE', 'Cache');
	define('_XMAP_USE_CACHE', 'Usa Cache');
	define('_XMAP_CACHE_LIFE_TIME', 'Durata cache');
	define('_XMAP_NEVER_VISITED', 'Mai');
	
	// New on Xmap 1.1 beta 1
	define('_XMAP_PLUGINS','Plugins');	
	define( '_XMAP_INSTALL_3PD_WARN', 'Attenzione: installare estensioni di terze parti possono compromettere la sicurezza del tuo server.' );
	define('_XMAP_INSTALL_NEW_PLUGIN', 'Installa nuovo plugin');
	define('_XMAP_UNKNOWN_AUTHOR','Autore sconosciuto');
	define('_XMAP_PLUGIN_VERSION','Versione %s');
	define('_XMAP_TAB_INSTALL_PLUGIN','Installa');
	define('_XMAP_TAB_EXTENSIONS','Estensioni');
	define('_XMAP_TAB_INSTALLED_EXTENSIONS','Estensioni Installate');
	define('_XMAP_NO_PLUGINS_INSTALLED','Nessun plugin personalizzato installato');
	define('_XMAP_AUTHOR','Autore');
	define('_XMAP_CONFIRM_DELETE_SITEMAP','Sei sicuro di voler eliminare questa sitemap?');
	define('_XMAP_CONFIRM_UNINSTALL_PLUGIN','Sei certo di voler rimuovere questo plugin?');
	define('_XMAP_UNINSTALL','Rimuovi');
	define('_XMAP_EXT_PUBLISHED','Pubblicato');
	define('_XMAP_EXT_UNPUBLISHED','Non pubblicato');
	define('_XMAP_PLUGIN_OPTIONS','Opzioni');
	define('_XMAP_EXT_INSTALLED_MSG','L’estensione è stata installata correttamente. Ora puoi controllarne le opzioni e pubblicarla.');
	define('_XMAP_CONTINUE','Continua');
	define('_XMAP_MSG_EXCLUDE_CSS_SITEMAP','Non includere il CSS all’interno della Sitemap');
	define('_XMAP_MSG_EXCLUDE_XSL_SITEMAP','Usa la visualizzazione XML Sitemap classica');

	// New on Xmap 1.1
	define('_XMAP_MSG_SELECT_FOLDER','Seleziona una cartella');
	define('_XMAP_UPLOAD_PKG_FILE','Carica un pacchetto file');
	define('_XMAP_UPLOAD_AND_INSTALL','Carica file &amp; Installa');
	define('_XMAP_INSTALL_F_DIRECTORY','Installa dalla cartella');
	define('_XMAP_INSTALL_DIRECTORY','Cartella d’installazione');
	define('_XMAP_INSTALL','Installa');
	define('_XMAP_WRITEABLE','Scrivibile');
	define('_XMAP_UNWRITEABLE','Non scrivibile');

	// New on Xmap 1.2
	define('_XMAP_COMPRESSION','Compressione');
	define('_XMAP_USE_COMPRESSION','Comprimi la Sitemap XML per risparmiare banda');

	// New on Xmap 1.2.1
	define('_XMAP_CFG_NEWS_MAP',		'News Sitemap');
	define('_XMAP_NEWS_LINK_TIP',   'URL della Sitemap News.');

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

