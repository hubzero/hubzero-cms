<?php
/* @package xmap
 * @author: nartconcept, nartconcept@gmail.com, http://www.modos-groupware.info/
 * @translate by Daneel, www.joomlafrance.org
 */

defined( '_JEXEC' ) or die( 'Direct Access to this location is not allowed.' );

if( !defined( 'JOOMAP_LANG' )) {
    define ('JOOMAP_LANG', 1 );
    // -- General ------------------------------------------------------------------
    define("_XMAP_CFG_COM_TITLE", "Pr&eacute;f&eacute;rences de Xmap");
    define("_XMAP_CFG_OPTIONS", "Pr&eacute;f&eacute;rences");
    define("_XMAP_CFG_TITLE", "Titre");
    define("_XMAP_CFG_CSS_CLASSNAME", "Nom de fichier CSS");
    define("_XMAP_CFG_EXPAND_CATEGORIES","D&eacute;velopper les cat&eacute;gories");
    define("_XMAP_CFG_EXPAND_SECTIONS","D&eacute;velopper les sections");
    define("_XMAP_CFG_SHOW_MENU_TITLES", "Afficher le(s) titres de(s) menu(s)");
    define("_XMAP_CFG_NUMBER_COLUMNS", "Nombre de colonnes");
    define('_XMAP_EX_LINK', 'Marquer les liens externes');
    define('_XMAP_CFG_CLICK_HERE', 'Cliquer ici');
    define('_XMAP_CFG_GOOGLE_MAP',		'Google Sitemap');
    define('_XMAP_EXCLUDE_MENU',			'Exclure lien(s) [ Menu IDs ]');
    define('_XMAP_TAB_DISPLAY',			'Pr&eacute;f&eacute;rences');
    define('_XMAP_TAB_MENUS',				'Menus');
    define('_XMAP_CFG_WRITEABLE',			'Modifiable');
    define('_XMAP_CFG_UNWRITEABLE',		'Non modifiable');
    define('_XMAP_MSG_MAKE_UNWRITEABLE',	'Rendre [ <span style="color: red;">non modifiable</span> ] apr&egrave;s la sauvegarde');
    define('_XMAP_MSG_OVERRIDE_WRITE_PROTECTION', 'Prot&eacute;ger en &eacute;criture en sauvegardant');
    define('_XMAP_GOOGLE_LINK',			'Lien Google');

    // -- Tips ---------------------------------------------------------------------
    define('_XMAP_EXCLUDE_MENU_TIP',		'S&eacute;lectionner le(s) lien(s) [ Menu IDs ] que vous ne souhaitez pas ajouter dans le Plan du site.<br /><strong>NOTE</strong><br />S&eacute;parer les liens [ IDs ] par une virgule!');
    define('_XMAP_CFG_GOOGLE_MAP_TIP',	'Fichier XML g&eacute;n&eacute;r&eacute; pour Google SiteMap');
    define('_XMAP_GOOGLE_LINK_TIP',		'Copier et Soumettre le lien &agrave; Google SiteMap');

    // -- Menus --------------------------------------------------------------------
    define("_XMAP_CFG_SET_ORDER", "Ordre d'affichage de(s) menu(s)");
    define("_XMAP_CFG_MENU_SHOW", "Afficher");
    define("_XMAP_CFG_MENU_REORDER", "R&eacute;organiser");
    define("_XMAP_CFG_MENU_ORDER", "Ordre");
    define("_XMAP_CFG_MENU_NAME", "Nom du menu");
    define("_XMAP_CFG_DISABLE", "Cliquer pour masquer");
    define("_XMAP_CFG_ENABLE", "Cliquer pour afficher");
    define('_XMAP_SHOW','Affich&eacute;');
    define('_XMAP_NO_SHOW','Masqu&eacute;');

    // -- Toolbar ------------------------------------------------------------------
    define("_XMAP_TOOLBAR_SAVE", "Sauver");
    define("_XMAP_TOOLBAR_CANCEL", "Quitter");

    // -- Errors -------------------------------------------------------------------
    define('_XMAP_ERR_NO_LANG','Aucun fichier de la langue [ %s ] n\'a pas &eacute;t&eacute; trouv&eacute;, la langue par d&eacute;faut est: anglais<br />'); // %s = $GLOBALS['mosConfig_lang']
    define('_XMAP_ERR_CONF_SAVE',         '<h2>Echec de sauvegarde de table de pr&eacute;f&eacute;rences.</h2>');
    define('_XMAP_ERR_NO_CREATE',         'ERREUR: Impossible de cr&eacute;er la table de pr&eacute;f&eacute;rences');
    define('_XMAP_ERR_NO_DEFAULT_SET',    'ERREUR: Impossible d\'appliquer la table de pr&eacute;f&eacute;rences par d&eacute;faut');
    define('_XMAP_ERR_NO_PREV_BU',        'ERREUR: Impossible de supprimer la derni&egrave;re sauvegarde');
    define('_XMAP_ERR_NO_BACKUP',         'ERREUR: Impossible de cr&eacute;er une sauvegarde');
    define('_XMAP_ERR_NO_DROP_DB',        'ERREUR: Impossible de supprimer la table de préférences');

    // -- Config -------------------------------------------------------------------
    define('_XMAP_MSG_SET_RESTORED',      'Table de pr&eacute;f&eacute;rences a &eacute;t&eacute; restaur&eacute;e<br />');
    define('_XMAP_MSG_SET_BACKEDUP',      'Les pr&eacute;f&eacute;rences ont &eacute;t&eacute; sauvegard&eacute;e<br />');
    define('_XMAP_MSG_SET_DB_CREATED',    'Table de pr&eacute;f&eacute;rences a &eacute;t&eacute; cr&eacute;&eacute;e<br />');
    define('_XMAP_MSG_SET_DEF_INSERT',    'Table de pr&eacute;f&eacute;rences par d&eacute;faut a &eacute;t&eacute; inser&eacute;e');
    define('_XMAP_MSG_SET_DB_DROPPED',    'Table de pr&eacute;f&eacute;rences a &eacute;t&eacute; supprim&eacute;e');
	
    // -- CSS ----------------------------------------------------------------------
    define('_XMAP_CSS',					'Xmap CSS');
    define('_XMAP_CSS_EDIT',				'Editer le template [ fichier CSS ]'); // Edit template
	
    // -- Sitemap ------------------------------------------------------------------
    define('_XMAP_SHOW_AS_EXTERN_ALT','Ouvrire le lien dans une nouvelle fen&ecirc;tre');
    define('_XMAP_PREVIEW','Aper&ccedil;u');
    define('_XMAP_SITEMAP_NAME','Plan du site');

    // -- Added for Xmap 
    define('_XMAP_CFG_MENU_SHOW_HTML',		'Afficher dans le site');
    define('_XMAP_CFG_MENU_SHOW_XML',		'Afficher dans Sitemap XML');
    define('_XMAP_CFG_MENU_PRIORITY',		'Priorit&eacute;');
    define('_XMAP_CFG_MENU_CHANGEFREQ',		'Changer la fr&eacute;quence');
    define('_XMAP_CFG_CHANGEFREQ_ALWAYS',		'Toujours');
    define('_XMAP_CFG_CHANGEFREQ_HOURLY',		'Heure');
    define('_XMAP_CFG_CHANGEFREQ_DAILY',		'Jour');
    define('_XMAP_CFG_CHANGEFREQ_WEEKLY',		'Semaine');
    define('_XMAP_CFG_CHANGEFREQ_MONTHLY',		'Mois');
    define('_XMAP_CFG_CHANGEFREQ_YEARLY',		'Ann&eacute;e');
    define('_XMAP_CFG_CHANGEFREQ_NEVER',		'Jamais');

    define('_XMAP_TIT_SETTINGS_OF',			'Pr&eacute;ferences de %s');
    define('_XMAP_TAB_SITEMAPS',			'Sitemaps');
    define('_XMAP_MSG_NO_SITEMAPS',			'Il n\'y a aucun plan de site cr&eacute;e');
    define('_XMAP_MSG_NO_SITEMAP',			'Ce plan de site est indisponible');
    define('_XMAP_MSG_LOADING_SETTINGS',		'Chargement pr&eacute;ferences...');
    define('_XMAP_MSG_ERROR_LOADING_SITEMAP',		'Erreur. Ne peut charger le plan de site');
    define('_XMAP_MSG_ERROR_SAVE_PROPERTY',		'Erreur. Ne peut charger les proprietes du plan de site.');
    define('_XMAP_MSG_ERROR_CLEAN_CACHE',		'Erreur. ne peut vider le plan de site en cache');
    define('_XMAP_ERROR_DELETE_DEFAULT',		'Le cache ne peut &ecirc;tre vid&eacute;!');
    define('_XMAP_MSG_CACHE_CLEANED',			'Le cache est vide!');
    define('_XMAP_CHARSET',				'ISO-8859-1');
    define('_XMAP_SITEMAP_ID',				'Plan de site ID');
    define('_XMAP_ADD_SITEMAP',				'Ajouter Plan de site');
    define('_XMAP_NAME_NEW_SITEMAP',			'Nouveau plan de site');
    define('_XMAP_DELETE_SITEMAP',			'Effacer');
    define('_XMAP_SETTINGS_SITEMAP',			'Pr&eacute;ferences');
    define('_XMAP_COPY_SITEMAP',			'Copier');
    define('_XMAP_SITEMAP_SET_DEFAULT',			'D&eacute;finir par D&eacute;faut');
    define('_XMAP_EDIT_MENU',				'Options');
    define('_XMAP_DELETE_MENU',				'Effacer');
    define('_XMAP_CLEAR_CACHE',				'Vider cache');
    define('_XMAP_MOVEUP_MENU',		'Monter');
    define('_XMAP_MOVEDOWN_MENU',	'Descendre');
    define('_XMAP_ADD_MENU',		'Ajoutermenus');
    define('_XMAP_COPY_OF',		'Copie de %s');
    define('_XMAP_INFO_LAST_VISIT',	'Derni&egrave;re visite');
    define('_XMAP_INFO_COUNT_VIEWS',	'Nombre de visites');
    define('_XMAP_INFO_TOTAL_LINKS',	'Nombre de liens');
    define('_XMAP_CFG_URLS',		'URL Sitemap');
    define('_XMAP_XML_LINK_TIP',	'Copier ce lien et envoyer dans les moteurs de recherche Google et Yahoo');
    define('_XMAP_HTML_LINK_TIP',	'Lien URL plan de site. Vous pouvez utiliser pour la liste des titres de vos menus.');
    define('_XMAP_CFG_XML_MAP',		'XML Plan de Site');
    define('_XMAP_CFG_HTML_MAP',	'HTML Plan de Site');
    define('_XMAP_XML_LINK',		'Lien Google');
    define('_XMAP_CFG_XML_MAP_TIP',	'Le fichier XML est g&eacute;n&eacute;r&eacute; pour les moteurs de recherche');
    define('_XMAP_ADD', 'Sauvegarder');
    define('_XMAP_CANCEL', 'Annuler');
    define('_XMAP_LOADING', 'Chargement...');
    define('_XMAP_CACHE', 'Cache');
    define('_XMAP_USE_CACHE', 'Utiliser le Cache');
    define('_XMAP_CACHE_LIFE_TIME', 'Dur&eacute;e du Cache');
    define('_XMAP_NEVER_VISITED', 'Jamais');
    define('_XMAP_CFG_INCLUDE_LINK', 'lien cliquable');
	

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
