<?php
/**
 * @package Xmap
 * @author: Guillermo Vargas
*/

defined( '_JEXEC' ) or die( 'Direct Access to this location is not allowed.' ); 


if( !defined( 'JOOMAP_LANG' )) {
    define('JOOMAP_LANG', 1 );
    // -- General ------------------------------------------------------------------
    define('_XMAP_CFG_COM_TITLE',			'Xmap 设置');
    define('_XMAP_CFG_OPTIONS',			'显示选项');
    define('_XMAP_CFG_CSS_CLASSNAME',		'CSS Classname');
    define('_XMAP_CFG_EXPAND_CATEGORIES',	'展开内容分类');
    define('_XMAP_CFG_EXPAND_SECTIONS',	'展开内容单元');
    define('_XMAP_CFG_SHOW_MENU_TITLES',	'显示菜单标题');
    define('_XMAP_CFG_NUMBER_COLUMNS',	'栏数');
    define('_XMAP_EX_LINK',				'外部链接标记');
    define('_XMAP_CFG_CLICK_HERE', 		'点击这里');
    define('_XMAP_CFG_GOOGLE_MAP',		'Google Sitemap');
    define('_XMAP_EXCLUDE_MENU',			'排除菜单 IDs');
    define('_XMAP_TAB_DISPLAY',			'显示');
    define('_XMAP_TAB_MENUS',				'菜单');
    define('_XMAP_CFG_WRITEABLE',			'可写');
    define('_XMAP_CFG_UNWRITEABLE',		'不可写');
    define('_XMAP_MSG_MAKE_UNWRITEABLE',	'保存后更改为不可写');
    define('_XMAP_MSG_OVERRIDE_WRITE_PROTECTION', '保存时不考虑写保护');
    define('_XMAP_GOOGLE_LINK',			'Googlelink');
    define('_XMAP_CFG_INCLUDE_LINK',		'链接到作者为不可见');

    // -- Tips ---------------------------------------------------------------------
    define('_XMAP_EXCLUDE_MENU_TIP',		'选择你不想包含在网站地图中的菜单 IDs.<br /><strong>注</strong><br />每个 IDs 用逗号隔开!');

    // -- Menus --------------------------------------------------------------------
    define('_XMAP_CFG_SET_ORDER',			'设置菜单显示顺序');
    define('_XMAP_CFG_MENU_SHOW',			'显示');
    define('_XMAP_CFG_MENU_REORDER',		'重排序');
    define('_XMAP_CFG_MENU_ORDER',		'排序');
    define('_XMAP_CFG_MENU_NAME',			'菜单名');
    define('_XMAP_CFG_DISABLE',			'点击禁用');
    define('_XMAP_CFG_ENABLE',			'点击启用');
    define('_XMAP_SHOW',					'显示');
    define('_XMAP_NO_SHOW',				'不显示');

    // -- Toolbar ------------------------------------------------------------------
    define('_XMAP_TOOLBAR_SAVE', 			'保存');
    define('_XMAP_TOOLBAR_CANCEL', 		'取消');

    // -- Errors -------------------------------------------------------------------
    define('_XMAP_ERR_NO_LANG',			'Language file [ %s ] not found, loaded default language: english<br />');
    define('_XMAP_ERR_CONF_SAVE',         'ERROR: Failed to save the configuration.');
    define('_XMAP_ERR_NO_CREATE',         'ERROR: Not able to create Settings table');
    define('_XMAP_ERR_NO_DEFAULT_SET',    'ERROR: Not able to insert default Settings');
    define('_XMAP_ERR_NO_PREV_BU',        'WARNING: Not able to drop previous backup');
    define('_XMAP_ERR_NO_BACKUP',         'ERROR: Not able to create backup');
    define('_XMAP_ERR_NO_DROP_DB',        'ERROR: Not able to drop Settings table');
    define('_XMAP_ERR_NO_SETTINGS',		'ERROR: Unable to load Settings from Database: <a href="%s">Create Settings table</a>');

    // -- Config -------------------------------------------------------------------
    define('_XMAP_MSG_SET_RESTORED',      '设置恢复');
    define('_XMAP_MSG_SET_BACKEDUP',      '设置已保存');
    define('_XMAP_MSG_SET_DB_CREATED',    '设置表已经创建');
    define('_XMAP_MSG_SET_DEF_INSERT',    '默认设置已插入');
    define('_XMAP_MSG_SET_DB_DROPPED','Xmap 表已保存!');
	
    // -- CSS ----------------------------------------------------------------------
    define('_XMAP_CSS',					'Xmap CSS');
    define('_XMAP_CSS_EDIT',				'编辑模板'); // Edit template
	
    // -- Sitemap (Frontend) -------------------------------------------------------
    define('_XMAP_SHOW_AS_EXTERN_ALT',	'链接在新窗口打开');
	
    // -- Added for Xmap 
    define('_XMAP_CFG_MENU_SHOW_HTML',		'在站内显示');
    define('_XMAP_CFG_MENU_SHOW_XML',		'在 XML Sitemap 显示');
    define('_XMAP_CFG_MENU_PRIORITY',		'优先');
    define('_XMAP_CFG_MENU_CHANGEFREQ',		'改变频率');
    define('_XMAP_CFG_CHANGEFREQ_ALWAYS',		'总是');
    define('_XMAP_CFG_CHANGEFREQ_HOURLY',		'小时');
    define('_XMAP_CFG_CHANGEFREQ_DAILY',		'天');
    define('_XMAP_CFG_CHANGEFREQ_WEEKLY',		'星期');
    define('_XMAP_CFG_CHANGEFREQ_MONTHLY',		'月');
    define('_XMAP_CFG_CHANGEFREQ_YEARLY',		'年');
    define('_XMAP_CFG_CHANGEFREQ_NEVER',		'从不');

    define('_XMAP_TIT_SETTINGS_OF',			'%s 个性化选项');
    define('_XMAP_TAB_SITEMAPS',			'网站地图');
    define('_XMAP_MSG_NO_SITEMAPS',			'还没有创建网站地图');
    define('_XMAP_MSG_NO_SITEMAP',			'此 sitemap 不可用');
    define('_XMAP_MSG_LOADING_SETTINGS',		'载入个性化选项...');
    define('_XMAP_MSG_ERROR_LOADING_SITEMAP',		'错误. 不能载入网站地图');
    define('_XMAP_MSG_ERROR_SAVE_PROPERTY',		'错误. 不能保存网站地图属性.');
    define('_XMAP_MSG_ERROR_CLEAN_CACHE',		'错误. 不能清除网站地图缓存');
    define('_XMAP_ERROR_DELETE_DEFAULT',		'不能删除默认网站地图!');
    define('_XMAP_MSG_CACHE_CLEANED',			'缓存已经清除!');
    define('_XMAP_CHARSET',				'utf-8');
    define('_XMAP_SITEMAP_ID',				'网站地图 ID');
    define('_XMAP_ADD_SITEMAP',				'添加网站地图');
    define('_XMAP_NAME_NEW_SITEMAP',			'新建网站地图');
    define('_XMAP_DELETE_SITEMAP',			'删除');
    define('_XMAP_SETTINGS_SITEMAP',			'个性化选项');
    define('_XMAP_COPY_SITEMAP',			'复制');
    define('_XMAP_SITEMAP_SET_DEFAULT',			'设为默认');
    define('_XMAP_EDIT_MENU',				'选项');
    define('_XMAP_DELETE_MENU',				'删除');
    define('_XMAP_CLEAR_CACHE',				'清除缓存');
    define('_XMAP_MOVEUP_MENU',		'上移');
    define('_XMAP_MOVEDOWN_MENU',	'下移');
    define('_XMAP_ADD_MENU',		'添加菜单');
    define('_XMAP_COPY_OF',		'复制 %s');
    define('_XMAP_INFO_LAST_VISIT',	'最后访问');
    define('_XMAP_INFO_COUNT_VIEWS',	'访问数');
    define('_XMAP_INFO_TOTAL_LINKS',	'链接数');
    define('_XMAP_CFG_URLS',		'网站地图 URL');
    define('_XMAP_XML_LINK_TIP',	'复制链接并提交到 Google 和 Yahoo');
    define('_XMAP_HTML_LINK_TIP',	'这是网站地图的 URL. 你可以用来创建一个菜单项目.');
    define('_XMAP_CFG_XML_MAP',		'XML 网站地图');
    define('_XMAP_CFG_HTML_MAP',	'HTML 网站地图');
    define('_XMAP_XML_LINK',		'Googlelink');
    define('_XMAP_CFG_XML_MAP_TIP',	'为搜索引擎生成 XML 文件');
    define('_XMAP_ADD', '保存');
    define('_XMAP_CANCEL', '取消');
    define('_XMAP_LOADING', '载入...');
    define('_XMAP_CACHE', '缓存');
    define('_XMAP_USE_CACHE', '使用缓存');
    define('_XMAP_CACHE_LIFE_TIME', '缓存有效期');
    define('_XMAP_NEVER_VISITED', '从不');


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
