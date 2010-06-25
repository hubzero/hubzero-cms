<?php 
/**
 * $Id: arabic.php 41 2009-07-23 20:50:14Z guilleva $
 * $LastChangedDate: 2009-07-23 14:50:14 -0600 (jue, 23 jul 2009) $
 * $LastChangedBy: guilleva $
 * Xmap by Guillermo Vargas
 * A sitemap component for Joomla! CMS (http://www.joomla.org)
 * Author Website: http://joomla.vargas.co.cr
 * Project License: GNU/GPL http://www.gnu.org/copyleft/gpl.html
 * Translated By : Mohammad Alkhuzzi mohd.khuzzi@gmail.com
*/

defined( '_JEXEC' ) or die( 'Direct Access to this location is not allowed.' );

if( !defined( 'JOOMAP_LANG' )) {
	define('JOOMAP_LANG', 1 );
	// -- General ------------------------------------------------------------------
	define('_XMAP_CFG_OPTIONS',			'خيارات العرض');
	define('_XMAP_CFG_CSS_CLASSNAME',		'CSS Classname');
	define('_XMAP_CFG_EXPAND_CATEGORIES',	'توسعة تصنيفات المحتويات');
	define('_XMAP_CFG_EXPAND_SECTIONS',	'توسعة أقسام المحتويات');
	define('_XMAP_CFG_SHOW_MENU_TITLES',	'عرض عناوين القوائم');
	define('_XMAP_CFG_NUMBER_COLUMNS',	'عدد الإعمدة');
	define('_XMAP_EX_LINK',				'توضيح الروابط الخارجية');
	define('_XMAP_CFG_CLICK_HERE', 		'إضغط هنا');
	define('_XMAP_CFG_GOOGLE_MAP',		'خريطة الموقع لقوقل');
	define('_XMAP_EXCLUDE_MENU',			'إستثناء القوائم ذات الأرقام');
	define('_XMAP_TAB_DISPLAY',			'عرض');
	define('_XMAP_TAB_MENUS',				'قوائم');
	define('_XMAP_CFG_WRITEABLE',			'قابل للكتابة');
	define('_XMAP_CFG_UNWRITEABLE',		'غير قابل للكتابة');
	define('_XMAP_MSG_MAKE_UNWRITEABLE',	'تغييره لغير قابل للكتابة بعد الحفظ');
	define('_XMAP_MSG_OVERRIDE_WRITE_PROTECTION', 'تجاوز حماية الكتابة خلال الحفظ');
	define('_XMAP_GOOGLE_LINK',			'رابط قوقل');
	define('_XMAP_CFG_INCLUDE_LINK',		'تضمين روابط للكتابة');

	// -- Tips ---------------------------------------------------------------------
	define('_XMAP_EXCLUDE_MENU_TIP',		'حدد أرقام القوائم التي لا تريد تضمينها في خريطة الموقع.<br /><strong>ملاحظة:</strong><br />فرق بين الأرقام بفاصلة!');

	// -- Menus --------------------------------------------------------------------
	define('_XMAP_CFG_SET_ORDER',			'حدد ترتيب عرض القوائم');
	define('_XMAP_CFG_MENU_SHOW',			'عرض');
	define('_XMAP_CFG_MENU_REORDER',		'إعادة ترتيب');
	define('_XMAP_CFG_MENU_ORDER',		'ترتيب');
	define('_XMAP_CFG_MENU_NAME',			'إسم القائمة');
	define('_XMAP_CFG_DISABLE',			'إضغط للتعطيل');
	define('_XMAP_CFG_ENABLE',			'إضغط للتفعيل');
	define('_XMAP_SHOW',					'عرض');
	define('_XMAP_NO_SHOW',				'عدم عرض');

	// -- Toolbar ------------------------------------------------------------------
	define('_XMAP_TOOLBAR_SAVE', 			'حفظ');
	define('_XMAP_TOOLBAR_CANCEL', 			'إلغاء');

	// -- Errors -------------------------------------------------------------------
	define('_XMAP_ERR_NO_LANG',			'ملف اللغة [ %s ] غير موجود, أعرض ملف اللغة الإفتراضي: الإنجليزي<br />');
	define('_XMAP_ERR_CONF_SAVE',         'خطأ: فشل حفظ الإعدادات.');
	define('_XMAP_ERR_NO_CREATE',         'خطأ: غير قادر على إنشاء جدول الإعدادات.');
	define('_XMAP_ERR_NO_DEFAULT_SET',    'خطأ: غير قادر على إدخال الإعدادات الإفتراضية.');
	define('_XMAP_ERR_NO_PREV_BU',        'تحذير: غير قادر على حذف النسخة الإحتياطية السابقة');
	define('_XMAP_ERR_NO_BACKUP',         'خطأ: غير قادر على إنشاء نسخة إحتياطية.');
	define('_XMAP_ERR_NO_DROP_DB',        'خطأ: غير قادر على حذف جدول الإعدادات');
	define('_XMAP_ERR_NO_SETTINGS',		'خطأ: غير قادر على تحميل الإعدادات من قاعدة البيانات: <a href="%s">إنشاء جدول الإعدادات</a>');

	// -- Config -------------------------------------------------------------------
	define('_XMAP_MSG_SET_RESTORED',      'الإعدادات أسترجعت');
	define('_XMAP_MSG_SET_BACKEDUP',      'الإعدادات حفظت');
	define('_XMAP_MSG_SET_DB_CREATED',    'جدول الإعدادات أنشيء');
	define('_XMAP_MSG_SET_DEF_INSERT',    'الإعدادات الإفتراضية أدخلت');
	define('_XMAP_MSG_SET_DB_DROPPED','جداول Xmap  حفظت!');
	
	// -- CSS ----------------------------------------------------------------------
	define('_XMAP_CSS',					'Xmap CSS');
	define('_XMAP_CSS_EDIT',				'تعديل التملبت'); // Edit template
	
	// -- Sitemap (Frontend) -------------------------------------------------------
	define('_XMAP_SHOW_AS_EXTERN_ALT',	'الرابط يفتح صفحة جديدة');
	
	// -- Added for Xmap 
	define('_XMAP_CFG_MENU_SHOW_HTML',		'عرض في الموقع');
	define('_XMAP_CFG_MENU_SHOW_XML',		'عرض في خريطة ال XML');
	define('_XMAP_CFG_MENU_PRIORITY',		'الأهمية');
	define('_XMAP_CFG_MENU_CHANGEFREQ',		'تغيير التردد');
	define('_XMAP_CFG_CHANGEFREQ_ALWAYS',		'دائماً');
	define('_XMAP_CFG_CHANGEFREQ_HOURLY',		'كل ساعة');
	define('_XMAP_CFG_CHANGEFREQ_DAILY',		'كل يوم');
	define('_XMAP_CFG_CHANGEFREQ_WEEKLY',		'كل أسبوع');
	define('_XMAP_CFG_CHANGEFREQ_MONTHLY',		'كل شهر');
	define('_XMAP_CFG_CHANGEFREQ_YEARLY',		'كل سنة');
	define('_XMAP_CFG_CHANGEFREQ_NEVER',		'أبداً');

	define('_XMAP_TIT_SETTINGS_OF',			'الإعدادات لـ %s');
	define('_XMAP_TAB_SITEMAPS',			'خرائط الموقع');
	define('_XMAP_MSG_NO_SITEMAPS',			'لم يتم إنشاء خريطة الموقع حتى الآن');
	define('_XMAP_MSG_NO_SITEMAP',			'خريطة الموقع هذه غير متوفرة');
	define('_XMAP_MSG_LOADING_SETTINGS',		'تحميل الإعدادات...');
	define('_XMAP_MSG_ERROR_LOADING_SITEMAP',		'خطأ. لا يمك تحميل خريطة الموقع');
	define('_XMAP_MSG_ERROR_SAVE_PROPERTY',		'خطأ. لا يمكن حفظ خصائص خريطة الموقع.');
	define('_XMAP_MSG_ERROR_CLEAN_CACHE',		'خطأ. لا يمكن مسح كاش خريطة الموقع');
	define('_XMAP_ERROR_DELETE_DEFAULT',		'لا يمكن حذف خريطة الموقع الإفتراضية!');
	define('_XMAP_MSG_CACHE_CLEANED',			'تم مسح الكاش!');
	define('_XMAP_CHARSET',				'UTF-8');
	define('_XMAP_SITEMAP_ID',				'رقم خريطة الموقع');
	define('_XMAP_ADD_SITEMAP',				'إضافة خريطة موقع');
	define('_XMAP_NAME_NEW_SITEMAP',			'خريطة موقع جديدة');
	define('_XMAP_DELETE_SITEMAP',			'حذف');
	define('_XMAP_SETTINGS_SITEMAP',			'الإعدادات');
	define('_XMAP_COPY_SITEMAP',			'نسخ');
	define('_XMAP_SITEMAP_SET_DEFAULT',			'تحديد كإفتراضي');
	define('_XMAP_EDIT_MENU',				'خيارات');
	define('_XMAP_DELETE_MENU',				'حذف');
	define('_XMAP_CLEAR_CACHE',				'مسح الكاش');
	define('_XMAP_MOVEUP_MENU',		'أعلى');
	define('_XMAP_MOVEDOWN_MENU',	'أسفل');
	define('_XMAP_ADD_MENU',		'إضافة قوائم');
	define('_XMAP_COPY_OF',		'نسخ من %s');
	define('_XMAP_INFO_LAST_VISIT',	'آخر زيارة');
	define('_XMAP_INFO_COUNT_VIEWS',	'عدد الزيارات');
	define('_XMAP_INFO_TOTAL_LINKS',	'عدد الروابط');
	define('_XMAP_CFG_URLS',		'رابط خريطة الموقع');
	define('_XMAP_XML_LINK_TIP',	'نسخ الرابط و تسليمه إلى قوقل و ياهو');
	define('_XMAP_HTML_LINK_TIP',	'هذا هو رابط خريطة الموقع. بإمكانك إستخدامه لإنشاء عناصر جديدة في القوائم.');
	define('_XMAP_CFG_XML_MAP',		'خريطة موقع XML');
	define('_XMAP_CFG_HTML_MAP',	'خريطة موقع HTML');
	define('_XMAP_XML_LINK',		'رابط قوقل');
	define('_XMAP_CFG_XML_MAP_TIP',	'تم إنشاء ملف XML لمحركات البحث');
	define('_XMAP_ADD', 'حفظ');
	define('_XMAP_CANCEL', 'إلغاء');
	define('_XMAP_LOADING', 'تحميل...');
	define('_XMAP_CACHE', 'كاش');
	define('_XMAP_USE_CACHE', 'إستخدام الكاش');
	define('_XMAP_CACHE_LIFE_TIME', 'عمر الكاش');
	define('_XMAP_NEVER_VISITED', 'أبداً');
	
	// New on Xmap 1.1 beta 1
	define('_XMAP_PLUGINS','إضافات');	
	define( '_XMAP_INSTALL_3PD_WARN', 'تحذير: تركيب إضافات من طرف ثالث ممكن أن يسبب لك مشاكل أمنية.' );
	define('_XMAP_INSTALL_NEW_PLUGIN', 'تركيب إضافة برمجية');
	define('_XMAP_UNKNOWN_AUTHOR','ناشر غير معروف');
	define('_XMAP_PLUGIN_VERSION','النسخة %s');
	define('_XMAP_TAB_INSTALL_PLUGIN','تركيب');
	define('_XMAP_TAB_EXTENSIONS','الإضافات');
	define('_XMAP_TAB_INSTALLED_EXTENSIONS','الإضافة المخزنة');
	define('_XMAP_NO_PLUGINS_INSTALLED','لم يتم تركيب إضافات معدلة');
	define('_XMAP_AUTHOR','الناشر');
	define('_XMAP_CONFIRM_DELETE_SITEMAP','هل أنت متأكد من حذف خريطة الموقع هذه؟');
	define('_XMAP_CONFIRM_UNINSTALL_PLUGIN','هل أنت متأكد من حذف هذه الإضافة البرمجية؟');
	define('_XMAP_UNINSTALL','إلغاء التركيب');
	define('_XMAP_EXT_PUBLISHED','تم النشر');
	define('_XMAP_EXT_UNPUBLISHED','ألغي النشر');
	define('_XMAP_PLUGIN_OPTIONS','خيارات');
	define('_XMAP_EXT_INSTALLED_MSG','تم تركيب الإضافة البرمجية بنجاح, رجاءاً راجع خياراتها ثم قم بنشرها.');
	define('_XMAP_CONTINUE','الإستمرار');
	define('_XMAP_MSG_EXCLUDE_CSS_SITEMAP','لا تشمل ال CSS مع خريطة الموقع');
	define('_XMAP_MSG_EXCLUDE_XSL_SITEMAP','إستخدم عرض ال XML الكلاسيكي لخريطة الموقع');

	// New on Xmap 1.1
	define('_XMAP_MSG_SELECT_FOLDER','فضلاً أختر مجلد');
	define('_XMAP_UPLOAD_PKG_FILE','رفع ملف الباقة');
	define('_XMAP_UPLOAD_AND_INSTALL','رفع الملف &amp; تركيب');
	define('_XMAP_INSTALL_F_DIRECTORY','التركيب من مجلد');
	define('_XMAP_INSTALL_DIRECTORY','مجلد التركيب');
	define('_XMAP_INSTALL','تركيب');
	define('_XMAP_WRITEABLE','قابل للكتابة');
	define('_XMAP_UNWRITEABLE','غير قابل للكتابة');

	// New on Xmap 1.2
	define('_XMAP_COMPRESSION','ضغط');
	define('_XMAP_USE_COMPRESSION','ضغط ملف ال XML لتقليل كمية استهلاك  الباندوث');

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
