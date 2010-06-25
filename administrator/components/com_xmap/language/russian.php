<?php 
/* @package Xmap
 * @author Guillermo Vargas, http://joomla.vargas.co.cr/
 * @translator Oleg Eneev, Oleg.Eneev@gmail.com
 * @translator Michael Grigorev, sleuthhound@gmail.com
*/

defined( '_JEXEC' ) or die( 'Direct Access to this location is not allowed.' );

if( !defined( 'JOOMAP_LANG' )) {
	define('JOOMAP_LANG', 1 );

	// -- General ------------------------------------------------------------------
	define('_XMAP_CFG_OPTIONS',			'Настройки отображения');
	define('_XMAP_CFG_CSS_CLASSNAME',		'Имя класса CSS');
	define('_XMAP_CFG_EXPAND_CATEGORIES',	'Раскрывать категории');
	define('_XMAP_CFG_EXPAND_SECTIONS',	'Раскрывать разделы');
	define('_XMAP_CFG_SHOW_MENU_TITLES',	'Показывать заголовки меню');
	define('_XMAP_CFG_NUMBER_COLUMNS',	'Количество колонок');
	define('_XMAP_EX_LINK',				'Пометить внешние ссылки');
	define('_XMAP_CFG_CLICK_HERE', 		'Нажмите здесь');
	define('_XMAP_CFG_GOOGLE_MAP',		'Карта сайта Google');
	define('_XMAP_EXCLUDE_MENU',			'Исключить пункты меню (по ID)');
	define('_XMAP_TAB_DISPLAY',			'Показать');
	define('_XMAP_TAB_MENUS',				'Меню');
	define('_XMAP_CFG_WRITEABLE',			'Доступен на запись');
	define('_XMAP_CFG_UNWRITEABLE',		'Недоступен на запись');
	define('_XMAP_MSG_MAKE_UNWRITEABLE',	'Сделать недоступным для записи после сохранения');
	define('_XMAP_MSG_OVERRIDE_WRITE_PROTECTION', 'Преодолеть запрет на запись при сохранении');
	define('_XMAP_GOOGLE_LINK',			'Ссылка для Google');
	define('_XMAP_CFG_INCLUDE_LINK',		'Показать ссылку на сайт разработчика');

	// -- Tips ---------------------------------------------------------------------
	define('_XMAP_EXCLUDE_MENU_TIP',		'Укажите идентификаторы (ID) меню, которые вы хотели бы исключить из карты сайта.<br /><strong>Примечание:</strong><br />Разделяйте идентификаторы (ID) запятыми!');

	// -- Menus --------------------------------------------------------------------
	define('_XMAP_CFG_SET_ORDER',			'Установить порядок отображения меню');
	define('_XMAP_CFG_MENU_SHOW',			'Показать');
	define('_XMAP_CFG_MENU_REORDER',		'Упорядочить');
	define('_XMAP_CFG_MENU_ORDER',		'Порядок');
	define('_XMAP_CFG_MENU_NAME',			'Имя меню');
	define('_XMAP_CFG_DISABLE',			'Нажмите, чтобы отключить');
	define('_XMAP_CFG_ENABLE',			'Нажмите, чтобы включить');
	define('_XMAP_SHOW',					'Показать');
	define('_XMAP_NO_SHOW',				'Не показывать');

	// -- Toolbar ------------------------------------------------------------------
	define('_XMAP_TOOLBAR_SAVE', 			'Сохранить');
	define('_XMAP_TOOLBAR_CANCEL', 			'Отменить');

	// -- Errors -------------------------------------------------------------------
	define('_XMAP_ERR_NO_LANG',			'[ %s ] языковой файл не найден, по умолчанию загружен английский<br />');
	define('_XMAP_ERR_CONF_SAVE',         'ERROR: Невозможно сохранить настройки.');
	define('_XMAP_ERR_NO_CREATE',         'ERROR: Невозможно создать таблицу настроек');
	define('_XMAP_ERR_NO_DEFAULT_SET',    'ERROR: Невозможно вставить (в таблицу БД) настройки по умолчанию');
	define('_XMAP_ERR_NO_PREV_BU',        'WARNING: Невозможно очистить предыдущую резервную копию');
	define('_XMAP_ERR_NO_BACKUP',         'ERROR: Невозможно создать резервную копию');
	define('_XMAP_ERR_NO_DROP_DB',        'ERROR: Невозможно удалить таблицу настроек');
	define('_XMAP_ERR_NO_SETTINGS',		'ERROR: Невозможно загрузить настройки из БД: <a href="%s">Создать таблицу настроек</a>');

	// -- Config -------------------------------------------------------------------
	define('_XMAP_MSG_SET_RESTORED',      'Настройки восстановлены');
	define('_XMAP_MSG_SET_BACKEDUP',      'Настройки сохранены');
	define('_XMAP_MSG_SET_DB_CREATED',    'Создана таблица настроек');
	define('_XMAP_MSG_SET_DEF_INSERT',    'Установлены настройки по умолчанию');
	define('_XMAP_MSG_SET_DB_DROPPED','Таблицы карты сайта сохранены!');
	
	// -- CSS ----------------------------------------------------------------------
	define('_XMAP_CSS',					'Стили CSS карты сайта');
	define('_XMAP_CSS_EDIT',				'Редактировать шаблон'); // Edit template
	
	// -- Sitemap (Frontend) -------------------------------------------------------
	define('_XMAP_SHOW_AS_EXTERN_ALT',	'Открывать ссылки в новом окне');
	
	// -- Added for Xmap 
	define('_XMAP_CFG_MENU_SHOW_HTML',		'Показаны на сайте');
	define('_XMAP_CFG_MENU_SHOW_XML',		'Показывать в карте сайта на XML');
	define('_XMAP_CFG_MENU_PRIORITY',		'Приоритет');
	define('_XMAP_CFG_MENU_CHANGEFREQ',		'Изменить частоту');
	define('_XMAP_CFG_CHANGEFREQ_ALWAYS',		'Всегда');
	define('_XMAP_CFG_CHANGEFREQ_HOURLY',		'Каждый час');
	define('_XMAP_CFG_CHANGEFREQ_DAILY',		'Ежедневно');
	define('_XMAP_CFG_CHANGEFREQ_WEEKLY',		'Каждую неделю');
	define('_XMAP_CFG_CHANGEFREQ_MONTHLY',		'Каждый месяц');
	define('_XMAP_CFG_CHANGEFREQ_YEARLY',		'Каждый год');
	define('_XMAP_CFG_CHANGEFREQ_NEVER',		'Никогда');

	define('_XMAP_TIT_SETTINGS_OF',			'Установки для %s');
	define('_XMAP_TAB_SITEMAPS',			'Карты сайта');
	define('_XMAP_MSG_NO_SITEMAPS',			'Карта сайта еще не создана');
	define('_XMAP_MSG_NO_SITEMAP',			'Эта карта сайта недоступна');
	define('_XMAP_MSG_LOADING_SETTINGS',		'Загрузка установок...');
	define('_XMAP_MSG_ERROR_LOADING_SITEMAP',		'Ошибка. Невозможно загрузить карту сайта');
	define('_XMAP_MSG_ERROR_SAVE_PROPERTY',		'Ошибка. Невозможно сохранить свойство карты сатйа.');
	define('_XMAP_MSG_ERROR_CLEAN_CACHE',		'Ошибка. Невозможно сбросить кэш карты сайта');
	define('_XMAP_ERROR_DELETE_DEFAULT',		'Невозможно удалить карту сайта по умолчанию!');
	define('_XMAP_MSG_CACHE_CLEANED',			'Кэш сброшен!');
    define('_XMAP_CHARSET', 'UTF-8');
	define('_XMAP_SITEMAP_ID',				'Идентияикатор (ID) карты сайта');
	define('_XMAP_ADD_SITEMAP',				'Добавить карту сайта');
	define('_XMAP_NAME_NEW_SITEMAP',			'Новая карта сайта');
	define('_XMAP_DELETE_SITEMAP',			'Удалить');
	define('_XMAP_SETTINGS_SITEMAP',			'Установки');
	define('_XMAP_COPY_SITEMAP',			'Копировать');
	define('_XMAP_SITEMAP_SET_DEFAULT',			'Установить значение по умолчанию');
	define('_XMAP_EDIT_MENU',				'Изменить');
	define('_XMAP_DELETE_MENU',				'Удалить');
	define('_XMAP_CLEAR_CACHE',				'Сбросить кэш');
	define('_XMAP_MOVEUP_MENU',		'Вверх');
	define('_XMAP_MOVEDOWN_MENU',	'Вниз');
	define('_XMAP_ADD_MENU',		'Добавить меню');
	define('_XMAP_COPY_OF',		'Копия %s');
	define('_XMAP_INFO_LAST_VISIT',	'Последнее посещение');
	define('_XMAP_INFO_COUNT_VIEWS',	'Количество посещений');
	define('_XMAP_INFO_TOTAL_LINKS',	'Количество ссылок');
	define('_XMAP_CFG_URLS',		'Ссылка (URL) на карту сайта');
	define('_XMAP_XML_LINK_TIP',	'Скопируйте эту ссылку и сообщите Google и Yahoo');
	define('_XMAP_HTML_LINK_TIP',	'Это ссылка на карту сайта. Вы можете использовать ее для создания пунктов меню.');
	define('_XMAP_CFG_XML_MAP',		'Карта сайта на XML ');
	define('_XMAP_CFG_HTML_MAP',	'Катра сайта в HTML');
	define('_XMAP_XML_LINK',		'Ссылка для Google');
	define('_XMAP_CFG_XML_MAP_TIP',	'XML-файл для поисковых машин создан');
	define('_XMAP_ADD', 'Сохранить');
	define('_XMAP_CANCEL', 'Отменить');
	define('_XMAP_LOADING', 'Загрузка...');
	define('_XMAP_CACHE', 'Кэширование');
	define('_XMAP_USE_CACHE', 'Использовать кэширование');
	define('_XMAP_CACHE_LIFE_TIME', 'Время жизни кэша');
	define('_XMAP_NEVER_VISITED', 'Никогда');
	
	// New on Xmap 1.1 beta 1
	define('_XMAP_PLUGINS','Расширения (Plugins)');	
	define( '_XMAP_INSTALL_3PD_WARN', 'Внимание: Установка сторонних расширений может нарушить безопасность вашего сервера.' );
	define('_XMAP_INSTALL_NEW_PLUGIN', 'Установить новые расширения');
	define('_XMAP_UNKNOWN_AUTHOR','Неизвестный автор');
	define('_XMAP_PLUGIN_VERSION','Версия %s');
	define('_XMAP_TAB_INSTALL_PLUGIN','Установка');
	define('_XMAP_TAB_EXTENSIONS','Расширения (Extensions)');
	define('_XMAP_TAB_INSTALLED_EXTENSIONS','Установленные расширения (Extensions)');
	define('_XMAP_NO_PLUGINS_INSTALLED','Дополнительные расширения (plugins) не установлены');
	define('_XMAP_AUTHOR','Автор');
	define('_XMAP_CONFIRM_DELETE_SITEMAP','Вы действительно хотите удалить эту карту сайта?');
	define('_XMAP_CONFIRM_UNINSTALL_PLUGIN','Вы действительно хотите удалить это расширение (plugin)?');
	define('_XMAP_UNINSTALL','Удалить');
	define('_XMAP_EXT_PUBLISHED','Опубликовано');
	define('_XMAP_EXT_UNPUBLISHED','Скрыто');
	define('_XMAP_PLUGIN_OPTIONS','Настройки');
	define('_XMAP_EXT_INSTALLED_MSG','Расширение (extension) успешно установлено, проверьте его настройки, затем опубликуйте это расширение.');
	define('_XMAP_CONTINUE','Продолжить');
	define('_XMAP_MSG_EXCLUDE_CSS_SITEMAP','Не подключать CSS в карте сайта');
	define('_XMAP_MSG_EXCLUDE_XSL_SITEMAP','Использовать классическое отображение карты сайта на XML');

	// New on Xmap 1.1
	define('_XMAP_MSG_SELECT_FOLDER','Выберите каталог');
	define('_XMAP_UPLOAD_PKG_FILE','Загрузить файл пакета');
	define('_XMAP_UPLOAD_AND_INSTALL','Загрузить файл &amp; Установить');
	define('_XMAP_INSTALL_F_DIRECTORY','Установить из каталога');
	define('_XMAP_INSTALL_DIRECTORY','Каталог установкиы');
	define('_XMAP_INSTALL','Установить');
	define('_XMAP_WRITEABLE','Доступен на запись');
	define('_XMAP_UNWRITEABLE','Недоступен на запись');

	// New on Xmap 1.2
	define('_XMAP_COMPRESSION','Сжатие');
	define('_XMAP_USE_COMPRESSION','Сжать XML карту сайта, для увеличения пропускной способности.');

	// New on Xmap 1.2.1
	define('_XMAP_CFG_NEWS_MAP','Новости Карты сайта');
	define('_XMAP_NEWS_LINK_TIP','Это новости Карты сайта.');

	// New on Xmap 1.2.2
	define('_XMAP_CFG_MENU_MODULE','Модуль');
	define('_XMAP_CFG_MENU_MODULE_TIP','Укажите модуль который Вы используете, чтобы показать меню на своем сайте (По умолчанию: mod_mainmenu).');

	// New on Xmap 1.2.3
	define('_XMAP_TEXT','Текст ссылки');
	define('_XMAP_TITLE','Заголовок ссылки');
	define('_XMAP_LINK','URL ссылки');
	define('_XMAP_CSS_STYLE','CSS стиль');
	define('_XMAP_CSS_CLASS','CSS класс');
	define('_XMAP_INVALID_SITEMAP','Неверная Карта сайта');
	define('_XMAP_OK', 'ОК');

}
