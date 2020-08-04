<?php
/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Browser;

/**
 * Browser class, provides capability information about the current web client.
 *
 * Browser identification is performed by examining the HTTP_USER_AGENT
 * environment variable provided by the web server.
 *
 * This class has many influences from the lib/Browser.php code in version 3 of
 * Horde by Chuck Hagenbuch and Jon Parise.
 */
class Detector
{
	/**
	 * @var  integer  Major version number
	 */
	protected $majorVersion = 0;

	/**
	 * @var  integer  Minor version number
	 */
	protected $minorVersion = 0;

	/**
	 * @var  string  Browser name.
	 */
	protected $browser = '';

	/**
	 * @var  string  Full user agent string.
	 */
	protected $agent = '';

	/**
	 * @var  string  Lower-case user agent string
	 */
	protected $lowerAgent = '';

	/**
	 * @var  string  HTTP_ACCEPT string.
	 */
	protected $accept = '';

	/**
	 * @var  array  Parsed HTTP_ACCEPT string
	 */
	protected $acceptParsed = array();

	/**
	 * @var  string  Platform the browser is running on
	 */
	protected $platform = '';

	/**
	 * @var  string  Platform version the browser is running on
	 */
	protected $platformVersion = '';

	/**
	 * @var  string  Device the browser is running on
	 */
	protected $device = '';

	/**
	 * @var  array  Known robots.
	 */
	protected $robots = array(
		'Googlebot\/',
		'Googlebot-Mobile',
		'Googlebot-Image',
		'Googlebot-News',
		'Googlebot-Video',
		'AdsBot-Google([^-]|$)',
		'AdsBot-Google-Mobile',
		'Feedfetcher-Google',
		'Mediapartners \(Googlebot\)',
		'APIs-Google',
		'bingbot',
		'Slurp',
		'[wW]get',
		'curl',
		'LinkedInBot',
		'Python-urllib',
		'python-requests',
		'libwww',
		'httpunit',
		'nutch',
		'Go-http-client',
		'phpcrawl',
		'msnbot',
		'jyxobot',
		'FAST-WebCrawler',
		'FAST Enterprise Crawler',
		'BIGLOTRON',
		'convera',
		'seekbot',
		'Gigablast',
		'exabot',
		'GingerCrawler',
		'webmon ',
		'grub.org',
		'UsineNouvelleCrawler',
		'antibot',
		'netresearchserver',
		'speedy',
		'fluffy',
		'bibnum.bnf',
		'findlink',
		'msrbot',
		'panscient',
		'yacybot',
		'AISearchBot',
		'ips-agent',
		'tagoobot',
		'MJ12bot',
		'woriobot',
		'yanga',
		'buzzbot',
		'mlbot',
		'YandexBot',
		'yandex.com\/bots',
		'purebot',
		'Linguee Bot',
		'CyberPatrol',
		'voilabot',
		'Baiduspider',
		'citeseerxbot',
		'spbot',
		'twengabot',
		'postrank',
		'scribdbot',
		'page2rss',
		'sitebot',
		'linkdex',
		'Adidxbot',
		'blekkobot',
		'ezooms',
		'dotbot',
		'Mail.RU_Bot',
		'discobot',
		'heritrix',
		'findthatfile',
		'europarchive.org',
		'NerdByNature.Bot',
		'sistrix crawler',
		'Ahrefs(Bot|SiteAudit)',
		'fuelbot',
		'CrunchBot',
		'centurybot9',
		'IndeedBot',
		'mappydata',
		'woobot',
		'ZoominfoBot',
		'PrivacyAwareBot',
		'Multiviewbot',
		'SWIMGBot',
		'Grobbot',
		'eright',
		'Apercite',
		'semanticbot',
		'Aboundex',
		'domaincrawler',
		'wbsearchbot',
		'summify',
		'CCBot',
		'edisterbot',
		'seznambot',
		'ec2linkfinder',
		'gslfbot',
		'aiHitBot',
		'intelium_bot',
		'facebookexternalhit',
		'Yeti',
		'RetrevoPageAnalyzer',
		'lb-spider',
		'Sogou',
		'lssbot',
		'careerbot',
		'wotbox',
		'wocbot',
		'ichiro',
		'DuckDuckBot',
		'lssrocketcrawler',
		'drupact',
		'webcompanycrawler',
		'acoonbot',
		'openindexspider',
		'gnam gnam spider',
		'web-archive-net.com.bot',
		'backlinkcrawler',
		'coccoc',
		'integromedb',
		'content crawler spider',
		'toplistbot',
		'it2media-domain-crawler',
		'ip-web-crawler.com',
		'siteexplorer.info',
		'elisabot',
		'proximic',
		'changedetection',
		'arabot',
		'WeSEE:Search',
		'niki-bot',
		'CrystalSemanticsBot',
		'rogerbot',
		'360Spider',
		'psbot',
		'InterfaxScanBot',
		'CC Metadata Scaper',
		'g00g1e.net',
		'GrapeshotCrawler',
		'urlappendbot',
		'brainobot',
		'fr-crawler',
		'binlar',
		'SimpleCrawler',
		'Twitterbot',
		'cXensebot',
		'smtbot',
		'bnf.fr_bot',
		'A6-Indexer',
		'ADmantX',
		'Facebot',
		'OrangeBot\/',
		'memorybot',
		'AdvBot',
		'MegaIndex',
		'SemanticScholarBot',
		'ltx71',
		'nerdybot',
		'xovibot',
		'BUbiNG',
		'Qwantify',
		'archive.org_bot',
		'Applebot',
		'TweetmemeBot',
		'crawler4j',
		'findxbot',
		'S[eE][mM]rushBot',
		'yoozBot',
		'lipperhey',
		'Y!J',
		'Domain Re-Animator Bot',
		'AddThis',
		'Screaming Frog SEO Spider',
		'MetaURI',
		'Scrapy',
		'Livelap[bB]ot',
		'OpenHoseBot',
		'CapsuleChecker',
		'collection@infegy.com',
		'IstellaBot',
		'DeuSu\/',
		'betaBot',
		'Cliqzbot\/',
		'MojeekBot\/',
		'netEstate NE Crawler',
		'SafeSearch microdata crawler',
		'Gluten Free Crawler\/',
		'Sonic',
		'Sysomos',
		'Trove',
		'deadlinkchecker',
		'Slack-ImgProxy',
		'Embedly',
		'RankActiveLinkBot',
		'iskanie',
		'SafeDNSBot',
		'SkypeUriPreview',
		'Veoozbot',
		'Slackbot',
		'redditbot',
		'datagnionbot',
		'Google-Adwords-Instant',
		'adbeat_bot',
		'WhatsApp',
		'contxbot',
		'pinterest',
		'electricmonk',
		'GarlikCrawler',
		'BingPreview\/',
		'vebidoobot',
		'FemtosearchBot',
		'Yahoo Link Preview',
		'MetaJobBot',
		'DomainStatsBot',
		'mindUpBot',
		'Daum\/',
		'Jugendschutzprogramm-Crawler',
		'Xenu Link Sleuth',
		'Pcore-HTTP',
		'moatbot',
		'KosmioBot',
		'pingdom',
		'PhantomJS',
		'Gowikibot',
		'PiplBot',
		'Discordbot',
		'TelegramBot',
		'Jetslide',
		'newsharecounts',
		'James BOT',
		'Barkrowler',
		'TinEye',
		'SocialRankIOBot',
		'trendictionbot',
		'Ocarinabot',
		'epicbot',
		'Primalbot',
		'DuckDuckGo-Favicons-Bot',
		'GnowitNewsbot',
		'Leikibot',
		'LinkArchiver',
		'YaK\/',
		'PaperLiBot',
		'Digg Deeper',
		'dcrawl',
		'Snacktory',
		'AndersPinkBot',
		'Fyrebot',
		'EveryoneSocialBot',
		'Mediatoolkitbot',
		'Luminator-robots',
		'ExtLinksBot',
		'SurveyBot',
		'NING\/',
		'okhttp',
		'Nuzzel',
		'omgili',
		'PocketParser',
		'YisouSpider',
		'um-LN',
		'ToutiaoSpider',
		'MuckRack',
		'Jamie\'s Spider',
		'AHC\/',
		'NetcraftSurveyAgent',
		'Laserlikebot',
		'Apache-HttpClient',
		'AppEngine-Google',
		'Jetty',
		'Upflow',
		'Thinklab',
		'Traackr.com',
		'Twurly',
		'Mastodon',
		'http_get',
		'DnyzBot',
		'botify',
		'007ac9 Crawler',
		'BehloolBot',
		'BrandVerity',
		'check_http',
		'BDCbot',
		'ZumBot',
		'EZID',
		'ICC-Crawler',
		'ArchiveBot',
		'^LCC ',
		'filterdb.iss.net\/crawler',
		'BLP_bbot',
		'BomboraBot',
		'Buck\/',
		'Companybook-Crawler',
		'Genieo',
		'magpie-crawler',
		'MeltwaterNews',
		'Moreover',
		'newspaper\/',
		'ScoutJet',
		'(^| )sentry\/',
		'StorygizeBot',
		'UptimeRobot',
		'OutclicksBot',
		'seoscanners',
		'Hatena',
		'Google Web Preview',
		'MauiBot',
		'AlphaBot',
		'SBL-BOT',
		'IAS crawler',
		'adscanner',
		'Netvibes',
		'acapbot',
		'Baidu-YunGuanCe',
		'bitlybot',
		'blogmuraBot',
		'Bot.AraTurka.com',
		'bot-pge.chlooe.com',
		'BoxcarBot',
		'BTWebClient',
		'ContextAd Bot',
		'Digincore bot',
		'Disqus',
		'Feedly',
		'Fetch\/',
		'Fever',
		'Flamingo_SearchEngine',
		'FlipboardProxy',
		'g2reader-bot',
		'imrbot',
		'K7MLWCBot',
		'Kemvibot',
		'Landau-Media-Spider',
		'linkapediabot',
		'vkShare',
		'Siteimprove.com',
		'BLEXBot\/',
		'DareBoost',
		'ZuperlistBot\/',
		'Miniflux\/',
		'Feedspotbot\/',
		'Diffbot\/',
		'SEOkicks',
		'tracemyfile',
		'Nimbostratus-Bot',
		'zgrab',
		'PR-CY.RU',
		'AdsTxtCrawler',
		'Datafeedwatch',
		'Zabbix',
		'TangibleeBot',
		'google-xrawler',
		'axios',
		'Amazon CloudFront',
		'Pulsepoint',
		'alexa',
		'Arachnoidea',
		'ArchitextSpider',
		'Ask Jeeves',
		'B-l-i-t-z-Bot',
		'BecomeBot',
		'cfetch',
		'ConveraCrawler',
		'ExtractorPro',
		'FDSE robot',
		'fido',
		'geckobot',
		'Gigabot',
		'Girafabot',
		'grub-client',
		'Gulliver',
		'HTTrack',
		'ia_archiver',
		'InfoSeek',
		'kinjabot',
		'KIT-Fireball',
		'larbin',
		'LEIA',
		'lmspider',
		'Lycos_Spider',
		'Mediapartners-Google',
		'MuscatFerret',
		'NaverBot',
		'OmniExplorer_Bot',
		'polybot',
		'Pompos',
		'Scooter',
		'Teoma',
		'TheSuBot',
		'TurnitinBot',
		'Ultraseek',
		'ViolaBot',
		'webbandit',
		'www\.almaden\.ibm\.com\/cs\/crawler',
		'ZyBorg'
	);

	/**
	 * Is this a mobile browser?
	 *
	 * @var  boolean
	 */
	protected $mobile = false;

	/**
	 * List of viewable image MIME subtypes.
	 * This list of viewable images works for IE and Netscape/Mozilla.
	 *
	 * @var  array
	 */
	protected $images = array(
		'jpeg',
		'gif',
		'png',
		'pjpeg',
		'x-png',
		'bmp'
	);

	/**
	 * List of browser regex
	 *
	 * @var  array
	 */
	protected $regexes = array(
		array(
			'regex'    => '|Opera[/ ]([0-9.]+)|',
			'version'  => '|Version[/ ]([0-9.]+)|',
			'name'     => 'Opera',
			'platform' => '',
			'mobile'   => false,
			'engine'   => ''
		),
		array(
			'regex'    => '|OPR[/ ]([0-9.]+)|',
			'name'     => 'Opera',
			'platform' => '',
			'mobile'   => false,
			'engine'   => ''
		),
		array(
			'regex'    => '|OPiOS[/ ]([0-9.]+)|',
			'name'     => 'Opera Mini',
			'platform' => 'iOS',
			'mobile'   => true,
			'engine'   => ''
		),
		array(
			'regex'    => '|Edge[/ ]([0-9.]+)|',
			'name'     => 'Edge',
			'platform' => 'Windows',
			'mobile'   => false,
			'engine'   => 'Edge'
		),
		array(
			'regex'    => '|Vivaldi[/ ]([0-9.]+)|',
			'name'     => 'Vivaldi',
			'platform' => 'Windows',
			'mobile'   => false,
			'engine'   => 'Blink'
		),
		array(
			'regex'    => '|YaBrowser[/ ]([0-9.]+)|',
			'name'     => 'Yandex',
			'platform' => 'Mac OS',
			'mobile'   => false,
			'engine'   => ''
		),
		array(
			'regex'    => '|Yowser[/ ]([0-9.]+)|',
			'name'     => 'Yandex',
			'platform' => 'Mac OS',
			'mobile'   => false,
			'engine'   => ''
		),
		array(
			'regex'    => '|Chrome[/ ]([0-9.]+)|',
			'name'     => 'Chrome',
			'platform' => '',
			'mobile'   => false,
			'engine'   => ''
		),
		array(
			'regex'    => '|CrMo[/ ]([0-9.]+)|',
			'name'     => 'Chrome',
			'platform' => '',
			'mobile'   => false,
			'engine'   => 'WebKit'
		),
		array(
			'regex'    => '|CriOS[/ ]([0-9.]+)|',
			'name'     => 'Chrome',
			'platform' => '',
			'mobile'   => true,
			'engine'   => 'WebKit'
		),
		array(
			'regex'    => '|MSIE ([0-9.]+)|',
			'name'     => 'MSIE',
			'platform' => 'Windows',
			'mobile'   => false,
			'engine'   => ''
		),
		array(
			'regex'    => '|Internet Explorer/([0-9.]+)|',
			'name'     => 'MSIE',
			'platform' => 'Windows',
			'mobile'   => false,
			'engine'   => ''
		),
		array(
			'regex'    => '|elaine/|',
			'name'     => 'Palm',
			'platform' => '',
			'mobile'   => true,
			'engine'   => ''
		),
		array(
			'regex'    => '|palmsource|',
			'name'     => 'Palm',
			'platform' => '',
			'mobile'   => true,
			'engine'   => ''
		),
		array(
			'regex'    => '|digital paths|',
			'name'     => 'Palm',
			'platform' => '',
			'mobile'   => true,
			'engine'   => ''
		),
		array(
			'regex'    => '|amaya/([0-9.]+)|',
			'name'     => 'Amaya',
			'platform' => '',
			'mobile'   => false,
			'engine'   => ''
		),
		array(
			'regex'    => '|ANTFresco/([0-9]+)|',
			'name'     => 'Fresco',
			'platform' => '',
			'mobile'   => false,
			'engine'   => ''
		),
		array(
			'regex'    => '|avantgo|',
			'name'     => 'Avantgo',
			'platform' => '',
			'mobile'   => false,
			'engine'   => ''
		),
		array(
			'regex'    => '|Android|',
			'name'     => 'Android',
			'platform' => 'Android',
			'mobile'   => true,
			'engine'   => 'WebKit'
		),
		array(
			'regex'    => '|[Kk]onqueror/([0-9]+)|',
			'name'     => 'Konqueror',
			'platform' => '',
			'mobile'   => false,
			'engine'   => ''
		),
		array(
			'regex'    => '|Safari/([0-9]+)\.?([0-9]+)?|',
			'version'  => '|Version[/ ]([0-9.]+)|',
			'name'     => 'Safari',
			'platform' => '',
			'mobile'   => false,
			'engine'   => 'WebKit'
		),
		array(
			'regex'    => '|Iceweasel/([0-9.]+)|',
			'name'     => 'Iceweasel',
			'platform' => 'Linux',
			'mobile'   => false,
			'engine'   => ''
		),
		array(
			'regex'    => '|Firefox/([0-9.]+)|',
			'name'     => 'Firefox',
			'platform' => '',
			'mobile'   => false,
			'engine'   => ''
		),
		array(
			'regex'    => '|Mozilla/([0-9.]+)|',
			'name'     => 'Mozilla',
			'platform' => '',
			'mobile'   => false,
			'engine'   => ''
		),
		array(
			'regex'    => '|Lynx/([0-9]+)|',
			'name'     => 'Lynx',
			'platform' => '',
			'mobile'   => false,
			'engine'   => ''
		),
		array(
			'regex'    => '|Links \(([0-9]+)|',
			'name'     => 'Links',
			'platform' => '',
			'mobile'   => false,
			'engine'   => ''
		),
		array(
			'regex'    => '|HotJava/([0-9]+)|',
			'name'     => 'HotJava',
			'platform' => '',
			'mobile'   => false,
			'engine'   => ''
		),
		array(
			'regex'    => '|UP[\/\.B\.L]|',
			'name'     => 'Up',
			'platform' => '',
			'mobile'   => false,
			'engine'   => ''
		),
		array(
			'regex'    => '|Xiino/|',
			'name'     => 'Xiino',
			'platform' => '',
			'mobile'   => true,
			'engine'   => ''
		),
		array(
			'regex'    => '|Nokia|',
			'name'     => 'Nokia',
			'platform' => '',
			'mobile'   => true,
			'engine'   => ''
		),
		array(
			'regex'    => '|Ericsson|',
			'name'     => 'Ericsson',
			'platform' => '',
			'mobile'   => true,
			'engine'   => ''
		),
		array(
			'regex'    => '/BlackBerry|PlayBook|BB10/',
			'name'     => 'BlackBerry',
			'platform' => '',
			'mobile'   => true,
			'engine'   => ''
		),
		array(
			'regex'    => '|MOT-|',
			'name'     => 'Motorola',
			'platform' => '',
			'mobile'   => true,
			'engine'   => ''
		),
		array(
			'regex'    => '/docomo|portalmmm/',
			'name'     => 'imode',
			'platform' => '',
			'mobile'   => false,
			'engine'   => ''
		)
	);

	/**
	 * Browser instances container.
	 *
	 * @var  array
	 */
	protected static $instances = array();

	/**
	 * Create a browser instance (constructor).
	 *
	 * @param   string  $userAgent  The browser string to parse.
	 * @param   string  $accept     The HTTP_ACCEPT settings to use.
	 * @return  void
	 */
	public function __construct($userAgent = null, $accept = null)
	{
		$this->match($userAgent, $accept);
	}

	/**
	 * Returns the global Browser object, only creating it
	 * if it doesn't already exist.
	 *
	 * @param   string  $userAgent  The browser string to parse.
	 * @param   string  $accept     The HTTP_ACCEPT settings to use.
	 * @return  object  The Browser object.
	 */
	public static function getInstance($userAgent = null, $accept = null)
	{
		$signature = serialize(array($userAgent, $accept));

		if (empty(self::$instances[$signature]))
		{
			self::$instances[$signature] = new self($userAgent, $accept);
		}

		return self::$instances[$signature];
	}

	/**
	 * Parses the user agent string and inititializes the object with
	 * all the known features and quirks for the given browser.
	 *
	 * @param   string  $userAgent  The browser string to parse.
	 * @param   string  $accept     The HTTP_ACCEPT settings to use.
	 * @return  void
	 */
	public function match($userAgent = null, $accept = null)
	{
		// Set our agent string.
		if (is_null($userAgent))
		{
			if (isset($_SERVER['HTTP_USER_AGENT']))
			{
				$this->agent = trim($_SERVER['HTTP_USER_AGENT']);
			}
		}
		else
		{
			$this->agent = $userAgent;
		}

		$this->lowerAgent = strtolower($this->agent);

		// Set our accept string.
		if (is_null($accept))
		{
			if (isset($_SERVER['HTTP_ACCEPT']))
			{
				$this->accept = strtolower(trim($_SERVER['HTTP_ACCEPT']));
			}
		}
		else
		{
			$this->accept = strtolower($accept);
		}

		if (!empty($this->agent))
		{
			$this->_setPlatform();

			foreach ($this->regexes as $regex)
			{
				if (preg_match($regex['regex'], $this->agent, $version))
				{
					$this->browser  = strtolower($regex['name']);
					$this->platform = ($regex['platform'] ? $regex['platform'] : $this->platform);
					if (!$this->mobile)
					{
						$this->mobile   = $regex['mobile'];
					}

					if (isset($regex['version']))
					{
						if (preg_match($regex['version'], $this->agent, $ver))
						{
							$version = $ver;
						}
					}

					if (!empty($version) && isset($version[1]))
					{
						$bits = explode('.', $version[1]);

						$this->majorVersion = $bits[0];
						$this->minorVersion = (isset($bits[1]) ? $bits[1] : 0);
					}

					break;
				}
			}
		}
	}

	/**
	 * Match the platform of the browser.
	 *
	 * This is a pretty simplistic implementation, but it's intended
	 * to let us tell what line breaks to send, so it's good enough
	 * for its purpose.
	 *
	 * @return  void
	 */
	protected function _setPlatform()
	{
		$this->device = 'computer';

		// Determine platform
		//
		// packs the os array
		// use this order since some navigator user agents will put 'macintosh' in the navigator user agent string
		// which would make the nt test register true
		$a_mobile = array(
			'ios', 'android', 'blackberry os', 'symbian os', 'web os' //, 'windows'
		);

		$a_mac = array(
			'mac68k', 'macppc'
		); // this is not used currently
		// same logic, check in order to catch the os's in order, last is always default item
		$a_unix = array(
			'unixware', 'solaris', 'sunos', 'sun4', 'sun5', 'suni86', 'sun',
			'freebsd', 'openbsd', 'bsd' , 'irix5', 'irix6', 'irix', 'hpux9',
			'hpux10', 'hpux11', 'hpux', 'hp-ux', 'aix1', 'aix2', 'aix3', 'aix4',
			'aix5', 'aix', 'sco', 'unixware', 'mpras', 'reliant', 'dec', 'sinix',
			'unix'
		);
		// only sometimes will you get a linux distro to id itself...
		$a_linux = array(
			'kanotix', 'ubuntu', 'mepis', 'debian', 'suse', 'redhat', 'slackware',
			'mandrake', 'gentoo', 'linux'
		);
		// note, order of os very important in os array, you will get failed ids if changed
		$a_os = array(
			'beos', 'os2', 'amiga', 'webtv', 'android', 'iphone', 'ipad', 'mac', 'nt', 'win',
			$a_unix,
			$a_linux
		);

		//os tester
		for ($i = 0; $i < count($a_os); $i++)
		{
			//unpacks os array, assigns to variable
			$s_os = $a_os[$i];

			//assign os to global os variable, os flag true on success
			//!stristr($browser_string, "linux") corrects a linux detection bug
			if (stristr($this->lowerAgent, 'android'))
			{
				$this->platform = 'Android';
			}
			else if (!is_array($s_os) && stristr($this->lowerAgent, $s_os) && !stristr($this->lowerAgent, 'linux'))
			{
				$this->platform = $s_os;

				switch ($this->platform)
				{
					case 'ipad':
					case 'iphone':
						$this->platform = 'iOS';
					break;

					case 'win':
						$this->platform = 'Windows';
						if (stristr($this->lowerAgent, '95'))
						{
							$this->platformVersion = '95';
						}
						elseif ((stristr($this->lowerAgent, '9x 4.9')) || (stristr($this->lowerAgent, 'me')))
						{
							$this->platformVersion = 'me';
						}
						elseif (stristr($this->lowerAgent, '98'))
						{
							$this->platformVersion = '98';
						}
						elseif (stristr($this->lowerAgent, '2000')) // windows 2000, for opera ID
						{
							$this->platformVersion = 5.0;
							//$this->platform .= ' NT';
						}
						elseif (stristr($this->lowerAgent, 'xp')) // windows 2000, for opera ID
						{
							$this->platformVersion = 5.1;
							//$this->platform .= ' NT';
						}
						elseif (stristr($this->lowerAgent, '2003')) // windows server 2003, for opera ID
						{
							$this->platformVersion = 5.2;
							//$this->platform .= ' NT';
						}
						elseif (stristr($this->lowerAgent, 'ce')) // windows CE
						{
							$this->platformVersion = 'ce';
						}
					break;

					case 'nt':
						$this->platform = 'Windows';
						if (stristr($this->lowerAgent, 'nt 5.2')) // windows server 2003
						{
							$this->platformVersion = 5.2;
						}
						elseif (stristr($this->lowerAgent, 'nt 5.1') || stristr($this->lowerAgent, 'xp')) // windows xp
						{
							//$this->platformVersion = 5.1;
							$this->platformVersion = 'XP';
							$this->platform = 'Windows';
						}
						elseif (stristr($this->lowerAgent, 'nt 5') || stristr($this->lowerAgent, '2000')) // windows 2000
						{
							//$this->platformVersion = 5.0;
							$this->platformVersion = '2000';
							$this->platform = 'Windows';
						}
						elseif (stristr($this->lowerAgent, 'nt 4')) // nt 4
						{
							$this->platformVersion = 4;
						}
						elseif (stristr($this->lowerAgent, 'nt 3')) // nt 4
						{
							$this->platformVersion = 3;
						} else {
							$this->platformVersion = '';
						}
					break;

					case 'mac':
						$this->platform = 'Mac OS';
						if (stristr($this->lowerAgent, 'os x'))
						{
							$this->platformVersion = 10;
						}
						// this is a crude test for os x, since safari, camino, ie 5.2, & moz >= rv 1.3
						// are only made for os x
						/*elseif (($browser == 'safari') || ($browser == 'camino') || ($browser == 'shiira') ||
							(($browser == 'mozilla') && ($browser_ver >= 1.3)) ||
							(($browser == 'msie') && ($browser_ver >= 5.2)))
						{
							$this->platformVersion = 10;
						}*/
					break;

					default:
					break;
				}
				break;
			}
			// check that it's an array, check it's the second to last item
			// in the main os array, the unix one that is
			elseif (is_array($s_os) && ($i == (count($a_os) - 2)))
			{
				for ($j = 0; $j < count($s_os); $j++)
				{
					if (stristr($this->lowerAgent, $s_os[$j]))
					{
						$this->platform = 'Unix'; // if the os is in the unix array, it's unix, obviously...
						$this->platformVersion = ($s_os[$j] != 'unix') ? $s_os[$j] : ''; // assign sub unix version from the unix array
						break;
					}
				}
			}
			// check that it's an array, check it's the last item
			// in the main os array, the linux one that is
			elseif (is_array($s_os) && ($i == (count($a_os) - 1)))
			{
				for ($j = 0; $j < count($s_os); $j++)
				{
					if (stristr($this->lowerAgent, $s_os[$j]))
					{
						$this->platform = 'Linux';
						// assign linux distro from the linux array, there's a default
						//search for 'lin', if it's that, set version to ''
						$this->platformVersion = ($s_os[$j] != 'linux') ? $s_os[$j] : '';
						break;
					}
				}
			}
		}

		// if we're on iOS
		if (in_array(strtolower($this->platform), $a_mobile))
		{
			$this->mobile = true;
			$this->device = 'phone';

			if (preg_match('/iphone/i', strtolower($this->lowerAgent)))
			{
				$this->device = 'iPhone';
			}
			if (preg_match('/ipad/i', strtolower($this->lowerAgent)))
			{
				$this->device = 'iPad';
			}
		}

		if (strtolower($this->platform) == 'ios')
		{
			if (preg_match('/OS (\d\w\d)/i', $this->lowerAgent, $matches))
			{
				if (isset($matches[1]))
				{
					$v = explode('_', $matches[1]);
					$this->platformVersion = $v[0] . '.' . $v[1];
				}
			}
		}

		return $this;
	}

	/**
	 * Return the currently matched device.
	 *
	 * @return  string  The user's device.
	 */
	public function device()
	{
		return $this->device;
	}

	/**
	 * Return the currently matched platform.
	 *
	 * @return  string  The user's platform.
	 */
	public function platform()
	{
		return $this->platform;
	}

	/**
	 * Return the currently matched platform.
	 *
	 * @return  string  The user's platform.
	 */
	public function platformVersion()
	{
		return $this->platformVersion;
	}

	/**
	 * Retrieve the current browser.
	 *
	 * @return  string  The current browser.
	 */
	public function name()
	{
		return $this->browser;
	}

	/**
	 * Retrieve the current browser's major version.
	 *
	 * @return  integer  The current browser's major version
	 */
	public function major()
	{
		return $this->majorVersion;
	}

	/**
	 * Retrieve the current browser's minor version.
	 *
	 * @return  integer  The current browser's minor version.
	 */
	public function minor()
	{
		return $this->minorVersion;
	}

	/**
	 * Retrieve the current browser's version.
	 *
	 * @return  string  The current browser's version.
	 */
	public function version($for='')
	{
		switch (strtolower($for))
		{
			case 'major':
				return $this->major();
			break;

			case 'minor':
				return $this->minor();
			break;

			case 'platform':
				return $this->platformVersion();
			break;

			default:
				return $this->majorVersion . '.' . $this->minorVersion;
			break;
		}
	}

	/**
	 * Return the full browser agent string.
	 *
	 * @return  string  The browser agent string
	 */
	public function agent()
	{
		return $this->agent;
	}

	/**
	 * Determine if the given browser is the same as the current.
	 *
	 * @param   string  $browser  The browser to check.
	 * @return  boolean  Is the given browser the same as the current?
	 */
	public function isBrowser($browser)
	{
		$browser = strtolower($browser);
		return ($this->browser === $browser);
	}

	/**
	 * Determines if the browser is a robot or not.
	 *
	 * @return  boolean  True if browser is a known robot.
	 */
	public function isRobot()
	{
		foreach ($this->robots as $robot)
		{
			//if (strpos($this->agent, $robot) !== false)
			if (preg_match('/' . $robot . '/', $this->agent))
			{
				return true;
			}
		}

		return false;
	}

	/**
	 * Determines if the browser is mobile version or not.
	 *
	 * @return boolean  True if browser is a known mobile version.
	 */
	public function isMobile()
	{
		return $this->mobile;
	}
}
