<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Migrations;

use Hubzero\Content\Migration\Base;

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
        $schema = $this->db->schema();

        if ($schema->tableExists('#__extensions')) {
            $components = [];
            foreach (self::$components as $c) {
                $components[] = $c;
            }
            $this->db
                ->getQuery()
                ->update('#__extensions')
                ->set(['protected' => 1])
                ->where('type', '=', 'component')
                ->whereIn('element', $components)
                ->execute();

            $modules = [];
            foreach (self::$modules as $c) {
                $modules[] = $c;
            }
            $this->db
                ->getQuery()
                ->update('#__extensions')
                ->set(['protected' => 1])
                ->where('type', '=', 'module')
                ->whereIn('element', $modules)
                ->execute();

            $templates = [];
            foreach (self::$templates as $c) {
                $templates[] = $c;
            }
            $this->db
                ->getQuery()
                ->update('#__extensions')
                ->set(['protected' => 1])
                ->where('type', '=', 'template')
                ->whereIn('element', $templates)
                ->execute();

            foreach (self::$plugins as $folder => $plugs) {
                $contents = [];
                foreach ($plugs as $c) {
                    $contents[] = $c;
                }
                $this->db
                    ->getQuery()
                    ->update('#__extensions')
                    ->set(['protected' => 1])
                    ->where('type', '=', 'plugin')
                    ->where('folder', '=', $folder)
                    ->whereIn('element', $contents)
                    ->execute();
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

    protected static $components = [
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
        'com_wishlist',
    ];

    protected static $modules = [
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
        'mod_youtube',
    ];

    protected static $templates = [
        'baselayer',
        'hubbasic2012',
        'hubbasic2013',
        'kameleon',
        'system',
        'welcome',
    ];

    protected static $plugins = [
        'antispam' => [
            'akismet',
            'babajispam',
            'bayesian',
            'blacklist',
            'linkrife',
            'mollom',
            'spamassassin',
        ],
        'authentication' => [
            'certificate',
            'facebook',
            'google',
            'hubzero',
            'linkedin',
            'pucas',
            'shibboleth',
            'twitter',
        ],
        'authfactors' => [
            'authy',
            'certificate',
        ],
        'captcha' => [
            'recaptcha',
        ],
        'citation' => [
            'bibtex',
            'default',
            'endnote',
        ],
        'content' => [
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
            'xhubtags',
        ],
        'courses' => [
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
            'store',
        ],
        'cron' => [
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
            'users',
        ],
        'editors' => [
            'ckeditor',
            'codemirror',
            'none',
            'tinymce',
            'wikitoolbar',
            'wikiwyg',
        ],
        'editors-xtd' => [
            'article',
            'image',
            'pagebreak',
            'readmore',
        ],
        'extension' => [
            'joomla',
        ],
        'geocode' => [
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
            'yandex',
        ],
        'groups' => [
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
            'wishlist',
        ],
        'hubzero' => [
            'autocompleter',
            'comments',
            'imagecaptcha',
            'mathcaptcha',
            'recaptcha',
            'systemplate',
            'systickets',
            'sysusers',
        ],
        'mail' => [
            'mandrill',
        ],
        'members' => [
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
            'wiki',
        ],
        'oaipmh' => [
            'publications',
            'resources',
        ],
        'projects' => [
            'blog',
            'databases',
            'example',
            'files',
            'forms',
            'links',
            'notes',
            'publications',
            'team',
            'todo',
        ],
        'publications' => [
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
            'wishlist',
        ],
        'quickicon' => [
            'extensionupdate',
            'joomlaupdate',
        ],
        'resources' => [
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
            'wishlist',
        ],
        'search' => [
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
            'wishlists',
        ],
        'support' => [
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
            'wishlist',
        ],
        'system' => [
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
            'xfeed',
        ],
        'tags' => [
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
            'wiki',
        ],
        'time' => [
            'csv',
            'summary',
        ],
        'tools' => [
            'java',
            'novnc',
        ],
        'usage' => [
            'domainclass',
            'domains',
            'maps',
            'overview',
            'partners',
            'region',
            'tools',
        ],
        'user' => [
            'constantcontact',
            'geo',
            'joomla',
            'ldap',
            'middleware',
            'profile',
            'xusers',
        ],
        'whatsnew' => [
            'blogs',
            'content',
            'events',
            'kb',
            'publications',
            'resources',
            'wiki',
        ],
        'wiki' => [
            'editortoolbar',
            'editorwykiwyg',
            'parserdefault',
        ],
        'xmessage' => [
            'email',
            'handler',
            'im',
            'internal',
            'rss',
            'smstxt',
        ],
    ];
}
