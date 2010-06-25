<?php
/**
 * @package xmap
 * @author: Guillermo Vargas, http://joomla.vargas.co.cr
 * translated by: Andr?s Victoria Ortega
*/


defined( '_JEXEC' ) or die( 'O acesso direto a esta p?gina n?o foi autorizado' );

if( !defined( 'JOOMAP_LANG' )) {
    define ('JOOMAP_LANG', 1 );
    // -- General ------------------------------------------------------------------
    define("_XMAP_CFG_COM_TITLE", "Configura??o de Xmap");
    define("_XMAP_CFG_OPTIONS", "Op??es de configura??o");
    define("_XMAP_CFG_TITLE", "T?tulo");
    define("_XMAP_CFG_CSS_CLASSNAME", "Nome da clsase CSS");
    define("_XMAP_CFG_EXPAND_CATEGORIES","Expandir o conte?do das categorias");
    define("_XMAP_CFG_EXPAND_SECTIONS","Expandir o conte?do das se??es");
    define("_XMAP_CFG_SHOW_MENU_TITLES", "Mostrar os t?tulos dos menus");
    define("_XMAP_CFG_NUMBER_COLUMNS", "N?mero de colunas");
    define('_XMAP_EX_LINK', 'Marcar links externos');
    define('_XMAP_CFG_CLICK_HERE', 'Clique aqui');
    define('_XMAP_EXCLUDE_MENU',			'Excluir IDs do menu');
    define('_XMAP_TAB_DISPLAY',			'Mostrar');
    define('_XMAP_TAB_MENUS',				'Menus');
    define('_XMAP_CFG_WRITEABLE',			'N?o protegido contra escrita');
    define('_XMAP_CFG_UNWRITEABLE',		'Protegido contra escrita');
    define('_XMAP_MSG_MAKE_UNWRITEABLE',	'Ap?s salvar, marcar como [ <span style="color: red;">protegido contra escrita</span> ]');
    define('_XMAP_MSG_OVERRIDE_WRITE_PROTECTION', 'Anular a prote??o contra escrita ao salvar');
    define('_XMAP_CFG_INCLUDE_LINK',		'Links invis?veis ao autor');

    // -- Tips ---------------------------------------------------------------------
    define('_XMAP_EXCLUDE_MENU_TIP',		'Especificar os IDs do menu que n?o deseja incluir no mapa do site.<br /><strong>NOTA</strong><br />Separe os IDs por v?rgulas!');

    // -- Menus --------------------------------------------------------------------
    define("_XMAP_CFG_SET_ORDER", "Selecionar a ordem na qual ser?o mostrados os menus");
    define("_XMAP_CFG_MENU_SHOW", "Mostrar");
    define("_XMAP_CFG_MENU_REORDER", "Reordenar");
    define("_XMAP_CFG_MENU_ORDER", "Ordenar");
    define("_XMAP_CFG_MENU_NAME", "Nome do Menu");
    define("_XMAP_CFG_DISABLE", "Clique para desativar");
    define("_XMAP_CFG_ENABLE", "Clique para ativar");
    define('_XMAP_SHOW','Mostrar');
    define('_XMAP_NO_SHOW','N?o mostrar');

    // -- Toolbar ------------------------------------------------------------------
    define("_XMAP_TOOLBAR_SAVE", "Salvar");
    define("_XMAP_TOOLBAR_CANCEL", "Cancelar");

    // -- Errors -------------------------------------------------------------------
    define('_XMAP_ERR_NO_LANG','L?ngua [ %s ] n?o encontrada, usando a l?ngua default: ingl?s<br />'); // %s = $GLOBALS['mosConfig_lang']
    define('_XMAP_ERR_CONF_SAVE',         '<h2>Erro ao salvar a configura??o.</h2>');
    define('_XMAP_ERR_NO_CREATE',         'ERRO: N?o foi poss?vel criar a tabela de op??es');
    define('_XMAP_ERR_NO_DEFAULT_SET',    'ERRO: N?o foi poss?vel inserir as op??es default');
    define('_XMAP_ERR_NO_PREV_BU',        'ATEN??O: N?o foi poss?vel apagar a c?pia de seguran?a anterior');
    define('_XMAP_ERR_NO_BACKUP',         'ERRO: N?o foi poss?vel criar a c?pia de seguran?a');
    define('_XMAP_ERR_NO_DROP_DB',        'ERRO: N?o foi poss?vel apagar a tabela de op??es');

    // -- Config -------------------------------------------------------------------
    define('_XMAP_MSG_SET_RESTORED',      'Op??es restauradas<br />');
    define('_XMAP_MSG_SET_BACKEDUP',      'Op??es salvas<br />');
    define('_XMAP_MSG_SET_DB_CREATED',    'Tabela de op??es criada<br />');
    define('_XMAP_MSG_SET_DEF_INSERT',    'Op??es default inseridas');
    define('_XMAP_MSG_SET_DB_DROPPED',    'Tabela de op??es salva!');
	
    // -- CSS ----------------------------------------------------------------------
    define('_XMAP_CSS',					'Xmap CSS');
    define('_XMAP_CSS_EDIT',				'Editar modelo'); // Edit template
	
    // -- Sitemap ------------------------------------------------------------------
    define('_XMAP_SHOW_AS_EXTERN_ALT','O link se abre em uma nova janela');
    define('_XMAP_PREVIEW','Previsualiza??o');
    define('_XMAP_SITEMAP_NAME','Mapa do site');

    // -- Added for Xmap 
    define('_XMAP_CFG_MENU_SHOW_HTML',			'Mostrar o site');
    define('_XMAP_CFG_MENU_SHOW_XML',		'Mostrar no Sitemap XML');
    define('_XMAP_CFG_MENU_PRIORITY',		'Prioridade');
    define('_XMAP_CFG_MENU_CHANGEFREQ',		'Freq??ncia de Atualiza??o');
    define('_XMAP_CFG_CHANGEFREQ_ALWAYS',		'Sempre');
    define('_XMAP_CFG_CHANGEFREQ_HOURLY',		'Hor?ria');
    define('_XMAP_CFG_CHANGEFREQ_DAILY',		'Di?ria');
    define('_XMAP_CFG_CHANGEFREQ_WEEKLY',		'Semanal');
    define('_XMAP_CFG_CHANGEFREQ_MONTHLY',		'Mensal');
    define('_XMAP_CFG_CHANGEFREQ_YEARLY',		'Anual');
    define('_XMAP_CFG_CHANGEFREQ_NEVER',		'Nunca');

    define('_XMAP_TIT_SETTINGS_OF',				'Prefer?ncias para %s');
    define('_XMAP_TAB_SITEMAPS',			'Sitemaps');
    define('_XMAP_MSG_NO_SITEMAPS',			'Nenhum Sitemap criado ainda');
    define('_XMAP_MSG_NO_SITEMAP',			'Este mapa n?o se encontra dispon?vel');
    define('_XMAP_MSG_LOADING_SETTINGS',		'Carregando as prefer?ncias...');
    define('_XMAP_MSG_ERROR_LOADING_SITEMAP',		'ERRO: N?o foi poss?vel carregar o mapa indicado');
    define('_XMAP_MSG_ERROR_SAVE_PROPERTY',		'ERRO: N?o foi poss?vel salvar a propiedade para o sitemap');
    define('_XMAP_MSG_ERROR_CLEAN_CACHE',		'ERRO: N?o foi poss?vel limpar o cache do sitemap');
    define('_XMAP_MSG_CACHE_CLEANED',			'O cache foi limpo com sucesso!');
    define('_XMAP_CHARSET',				'ISO-8859-1');
    define('_XMAP_SITEMAP_ID',                          'ID do Sitemap');
    define('_XMAP_ADD_SITEMAP',				'Adicionar Sitemap');
    define('_XMAP_NAME_NEW_SITEMAP',			'Novo Sitemap');
    define('_XMAP_DELETE_SITEMAP',			'Apagar');
    define('_XMAP_SETTINGS_SITEMAP',			'Prefer?ncias');
    define('_XMAP_COPY_SITEMAP',			'Duplicar');
    define('_XMAP_SITEMAP_SET_DEFAULT',			'Marcar como Default');
    define('_XMAP_EDIT_MENU',				'Op??es');
    define('_XMAP_DELETE_MENU',				'Sair');
    define('_XMAP_CLEAR_CACHE',				'Limpar Cache');
    define('_XMAP_MOVEUP_MENU',				'Para cima');
    define('_XMAP_MOVEDOWN_MENU',			'Para baixo');
    define('_XMAP_ADD_MENU',				'Adicionar Menus');
    define('_XMAP_COPY_OF',				'C?pia de %s');
    define('_XMAP_INFO_LAST_VISIT',				'?ltima Visita');
    define('_XMAP_INFO_COUNT_VIEWS',				'N?mero de Visitas');
    define('_XMAP_INFO_TOTAL_LINKS',				'Quantidade de links');
    define('_XMAP_CFG_URLS',            'URLs do Sitemap');
    define('_XMAP_XML_LINK_TIP',		'Copie o link e o envie a buscadores como Google e Yahoo');
    define('_XMAP_CFG_XML_MAP',		'XML Sitemap');

    define('_XMAP_CFG_HTML_MAP',	'HTML Sitemap');
    define('_XMAP_XML_LINK',			'Googlelink');
    define('_XMAP_CFG_XML_MAP_TIP',	'Arquivo XML gerado para o mapa do site.');
    define('_XMAP_HTML_LINK_TIP',       'Esta ? a URL do Sitemap. Use-a para criar itens em seus menus.'); 
    define('_XMAP_ADD', 'Salvar');
    define('_XMAP_CANCEL', 'Cancelar');
    define('_XMAP_LOADING', 'Carregando...');
    define('_XMAP_CACHE', 'Cache');
    define('_XMAP_USE_CACHE', 'Utilizar Cache');
    define('_XMAP_CACHE_LIFE_TIME', 'Tempo do Cache');
    define('_XMAP_NEVER_VISITED', 'Nunca');
    define('_XMAP_MSG_SET_DB_DROPPED','As tabelas do Xmap foram salvas!');


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
