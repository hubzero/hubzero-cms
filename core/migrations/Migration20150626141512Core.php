<?php

use Hubzero\Content\Migration\Base;

// No direct access
defined('_HZEXEC_') or die();

/**
 * Migration script for marking HUBzero extensions as protected
 **/
class Migration20150626141512Core extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		if ($this->db->tableExists('#__extensions'))
		{
			$components = array();
			foreach (self::$components as $c)
			{
				$components[] = $this->db->quote($c);
			}
			$query = "UPDATE `#__extensions` SET `protected`=1 WHERE `type`='component' AND `element` IN (" . implode(',', $components) . ")";
			$this->db->setQuery($query);
			$this->db->query();

			$modules = array();
			foreach (self::$modules as $c)
			{
				$modules[] = $this->db->quote($c);
			}
			$query = "UPDATE `#__extensions` SET `protected`=1 WHERE `type`='module' AND `element` IN (" . implode(',', $modules) . ")";
			$this->db->setQuery($query);
			$this->db->query();

			$templates = array();
			foreach (self::$templates as $c)
			{
				$templates[] = $this->db->quote($c);
			}
			$query = "UPDATE `#__extensions` SET `protected`=1 WHERE `type`='template' AND `element` IN (" . implode(',', $templates) . ")";
			$this->db->setQuery($query);
			$this->db->query();

			$templates = array();
			foreach (self::$plugins as $folder => $plugs)
			{
				$contents = array();
				foreach ($plugs as $c)
				{
					$contents[] = $this->db->quote($c);
				}
				$query = "UPDATE `#__extensions` SET `protected`=1 WHERE `type`='plugin' AND `folder`=" . $this->db->quote($folder) . " AND `element` IN (" . implode(',', $contents) . ")";
				$this->db->setQuery($query);
				$this->db->query();
			}
		}
	}

	/**
	 * Down
	 **/
	public function down()
	{
		// Nothing really to do here
	}

	protected static $components = array(
		'com_admin',
		'com_answers',
		'com_billboards',
		'com_blog',
		'com_cache',
		'com_cart',
		'com_categories',
		'com_checkin',
		'com_citations',
		'com_collections',
		'com_config',
		'com_content',
		'com_courses',
		'com_cpanel',
		'com_cron',
		'com_dataviewer',
		'com_developer',
		'com_events',
		'com_feedaggregator',
		'com_feedback',
		'com_forum',
		'com_geosearch',  // ?
		'com_groups',
		'com_help',
		'com_hubgraph',
		'com_installer',
		'com_jobs',
		'com_kb',
		'com_languages',
		'com_login',
		'com_mailto',
		'com_media',
		'com_members',
		'com_menus',
		'com_messages',
		'com_modules',
		'com_news',
		'com_newsfeeds',
		'com_newsletter',
		'com_oaipmh',
		'com_oauth',
		'com_plugins',
		'com_poll',
		'com_projects',
		'com_publications',
		'com_redirect',
		'com_resources',
		'com_search',
		'com_services',
		'com_store',
		'com_storefront',
		'com_support',
		'com_system',
		'com_tags',
		'com_templates',
		'com_time',
		'com_tools',
		'com_update',
		'com_usage',
		'com_users',
		'com_whatsnew',
		'com_wiki',
		'com_wishlist'
	);

	protected static $modules = array(
		'mod_adminlogin',
		'mod_adminmenu',
		'mod_announcements',
		'mod_answers',
		'mod_application_env',
		'mod_articles_archive',
		'mod_articles_categories',
		'mod_articles_category',
		'mod_articles_latest',
		'mod_articles_news',
		'mod_articles_popular',
		'mod_billboards',
		'mod_breadcrumbs',
		'mod_clippy',
		'mod_collect',
		'mod_courses',
		'mod_custom',
		'mod_events_cal',
		'mod_events_latest',
		'mod_featuredblog',
		'mod_featuredmember',
		'mod_featuredquestion',
		'mod_featuredresource',
		'mod_feed',
		'mod_feed_youtube',
		'mod_findresources',
		'mod_footer',
		'mod_googleanalytics',
		'mod_grouppages',
		'mod_groups',
		'mod_hubzilla',
		'mod_incremental_registration',
		'mod_languages',
		'mod_latest',
		'mod_latestblog',
		'mod_latestdiscussions',
		'mod_latestgroups',
		'mod_latestusage',
		'mod_login',
		'mod_logjserrors',
		'mod_members',
		'mod_menu',
		'mod_multilangstatus',
		'mod_mycontributions',
		'mod_mycourses',
		'mod_mycuration',
		'mod_mygroups',
		'mod_mymessages',
		'mod_mypoints',
		'mod_myprojects',
		'mod_myquestions',
		'mod_myresources',
		'mod_mysessions',
		'mod_mysubmissions',
		'mod_mytickets',
		'mod_mytodos',
		'mod_mytools',
		'mod_mywishes',
		'mod_newsletter',
		'mod_notices',
		'mod_poll',
		'mod_polltitle',
		'mod_popular',
		'mod_popularfaq',
		'mod_popularquestions',
		'mod_quickicon',
		'mod_quicktips',
		'mod_quotes',
		'mod_randomquote',
		'mod_random_image',
		'mod_rapid_contact',
		'mod_recentquestions',
		'mod_related_items',
		'mod_reportproblems',
		'mod_resourcemenu',
		'mod_resources',
		'mod_search',
		'mod_slideshow',
		'mod_sliding_panes',
		'mod_spotlight',
		'mod_stats',
		'mod_submenu',
		'mod_supportactivity',
		'mod_supporttickets',
		'mod_syndicate',
		'mod_title',
		'mod_toolbar',
		'mod_tools',
		'mod_toptags',
		'mod_twitterfeed',
		'mod_users',
		'mod_users_latest',
		'mod_version',
		'mod_whatsnew',
		'mod_whosonline',
		'mod_wishlist',
		'mod_wishvoters',
		'mod_wrapper',
		'mod_youtube'
	);

	protected static $templates = array(
		'baselayer',
		'hubbasic2012',
		'hubbasic2013',
		'kameleon',
		'system',
		'welcome'
	);

	protected static $plugins = array(
		'antispam' => array(
			'akismet',
			'babajispam',
			'bayesian',
			'blacklist',
			'linkrife',
			'mollom',
			'spamassassin'
		),
		'authentication' => array(
			'certificate',
			'facebook',
			'google',
			'hubzero',
			'linkedin',
			'pucas',
			'shibboleth',
			'twitter'
		),
		'authfactors' => array(
			'authy',
			'certificate'
		),
		'captcha' => array(
			'recaptcha'
		),
		'citation' => array(
			'bibtex',
			'default',
			'endnote'
		),
		'content' => array(
			'antispam',
			'emailcloak',
			'formathtml',
			'formatwiki',
			'geshi',
			'joomla',
			'loadmodule',
			'pagebreak',
			'pagenavigation',
			'vote',
			'xhubtags'
		),
		'courses' => array(
			'announcements',
			'dashboard',
			'discussions',
			'guide',
			'memberoptions',
			'notes',
			'offerings',
			'outline',
			'overview',
			'pages',
			'pec',
			'progress',
			'related',
			'reviews',
			'store'
		),
		'cron' => array(
			'cache',
			'courses',
			'forum',
			'groups',
			'members',
			'newsletter',
			'projects',
			'publications',
			'register',
			'resources',
			'support',
			'users'
		),
		'editors' => array(
			'ckeditor',
			'codemirror',
			'none',
			'tinymce',
			'wikitoolbar',
			'wikiwyg'
		),
		'editors-xtd' => array(
			'article',
			'image',
			'pagebreak',
			'readmore'
		),
		'extension' => array(
			'joomla'
		),
		'geocode' => array(
			'arcgisonline',
			'baidu',
			'bingmaps',
			'cloudmade',
			'datasciencetoolkit',
			'freegeoip',
			'geocoderca',
			'geocoderus',
			'geoip',
			'geoips',
			'geonames',
			'geoplugin',
			'googlemaps',
			'googlemapsbusiness',
			'hostip',
			'ignopenls',
			'ipgeobase',
			'ipinfodb',
			'local',
			'mapquest',
			'maxmind',
			'maxmindbinary',
			'nominatim',
			'oiorest',
			'openstreetmap',
			'tomtom',
			'yandex'
		),
		'groups' => array(
			'announcements',
			'blog',
			'calendar',
			'citations',
			'collections',
			'courses',
			'forum',
			'memberoptions',
			'members',
			'messages',
			'projects',
			'resources',
			'usage',
			'wiki',
			'wishlist'
		),
		'hubzero' => array(
			'autocompleter',
			'comments',
			'imagecaptcha',
			'mathcaptcha',
			'recaptcha',
			'systemplate',
			'systickets',
			'sysusers'
		),
		'mail' => array(
			'mandrill'
		),
		'members' => array(
			'account',
			'blog',
			'citations',
			'collections',
			'contributions',
			'courses',
			'dashboard',
			'groups',
			'impact',
			'messages',
			'points',
			'profile',
			'projects',
			'resources',
			'resume',
			'usage',
			'wiki'
		),
		'oaipmh' => array(
			'publications',
			'resources'
		),
		'projects' => array(
			'blog',
			'databases',
			'example',
			'files',
			'forms',
			'links',
			'notes',
			'publications',
			'team',
			'todo'
		),
		'publications' => array(
			'citations',
			'groups',
			'questions',
			'recommendations',
			'related',
			'reviews',
			'share',
			'supportingdocs',
			'usage',
			'versions',
			'watch',
			'wishlist'
		),
		'quickicon' => array(
			'extensionupdate',
			'joomlaupdate'
		),
		'resources' => array(
			'about',
			'citations',
			'classrooms',
			'findthistext',
			'groups',
			'questions',
			'recommendations',
			'related',
			'reviews',
			'share',
			'sponsors',
			'supportingdocs',
			'usage',
			'versions',
			'wishlist'
		),
		'search' => array(
			'blogs',
			'citations',
			'collections',
			'content',
			'courses',
			'events',
			'forum',
			'groups',
			'kb',
			'members',
			'projects',
			'publications',
			'questions',
			'resources',
			'sitemap',
			'sortcourses',
			'sortevents',
			'suffixes',
			'weightcontributor',
			'weighttitle',
			'weighttools',
			'wiki',
			'wishlists'
		),
		'support' => array(
			'answers',
			'blog',
			'captcha',
			'comments',
			'forum',
			'kb',
			'publications',
			'resources',
			'time',
			'transfer',
			'wiki',
			'wishlist'
		),
		'system' => array(
			'authfactors',
			'cache',
			'certificate',
			'debug',
			'disablecache',
			'highlight',
			'hubzero',
			'jquery',
			'languagecode',
			'languagefilter',
			'log',
			'logout',
			'p3p',
			'redirect',
			'remember',
			'sef',
			'spamjail',
			'supergroup',
			'userconsent',
			'xfeed'
		),
		'tags' => array(
			'answers',
			'blogs',
			'citations',
			'collections',
			'events',
			'forum',
			'groups',
			'kb',
			'members',
			'publications',
			'resources',
			'support',
			'wiki'
		),
		'time' => array(
			'csv',
			'summary'
		),
		'tools' => array(
			'java',
			'novnc'
		),
		'usage' => array(
			'domainclass',
			'domains',
			'maps',
			'overview',
			'partners',
			'region',
			'tools'
		),
		'user' => array(
			'constantcontact',
			'geo',
			'joomla',
			'ldap',
			'middleware',
			'profile',
			'xusers'
		),
		'whatsnew' => array(
			'blogs',
			'content',
			'events',
			'kb',
			'publications',
			'resources',
			'wiki'
		),
		'wiki' => array(
			'editortoolbar',
			'editorwykiwyg',
			'parserdefault'
		),
		'xmessage' => array(
			'email',
			'handler',
			'im',
			'internal',
			'rss',
			'smstxt'
		)
	);
}
