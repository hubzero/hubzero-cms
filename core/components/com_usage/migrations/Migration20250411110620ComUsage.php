<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

use Hubzero\Content\Migration\Base;

// No direct access
defined('_HZEXEC_') or die();

/**
 * Migration script for altering exclude_list metrics table
 **/
class Migration20250411110620ComUsage extends Base
{

	/**
	 * Up
	 **/
	public function up()
	{
		// Connection to metrics database uses helper class:
		if (!file_exists(dirname(__DIR__) . '/helpers/helper.php'))
		{
			$this->log('Unable to locate usage helper class.', 'error');
			return;
		}

		include_once dirname(__DIR__) . '/helpers/helper.php';

		$statsDb = \Components\Usage\Helpers\Helper::getUDBO();

		if (!$statsDb)
		{
			$this->log('Unable to establish connection for usage database.', 'error');
			return;
		}

		// No variable support in SQL string when using metrics db via helper class?
		if ($statsDb->tableExists('exclude_list'))
		{
			try
			{
				$run_ddl = "TRUNCATE TABLE exclude_list";
				$statsDb->setQuery($run_ddl);
				$statsDb->query();
				$this->log("Truncated table `exclude_list`");

				$run_ddl = "ALTER TABLE exclude_list ADD `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY FIRST";
				$statsDb->setQuery($run_ddl);
				$statsDb->query();
				$this->log("Altered table, added id column");

				$run_ddl = "ALTER TABLE exclude_list ADD UNIQUE INDEX (filter, type)";
				$statsDb->setQuery($run_ddl);
				$statsDb->query();
				$this->log("Altered table, added index");

				$run_ddl = "ALTER TABLE exclude_list ADD `date_added` TIMESTAMP ON UPDATE CURRENT_TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP";
				$statsDb->setQuery($run_ddl);
				$statsDb->query();
				$this->log("Altered table, added date col");

				$query = "INSERT INTO `exclude_list` (`filter`, `type`, `notes`) VALUES ('googlebot.com','domain','bot'), 
				('crawl.yahoo.net','domain','bot'), ('search.msn.com','domain','bot'), ('msnbot.msn.com','domain','bot'), 
				('crawl%googlebot.com','host','bot'), ('%crawl.yahoo.net','host','bot'), ('%search.msn.com','host','bot'), 
				('%msnbot.msn.com','host','bot'), ('yahoo.com','domain','bot'), ('turnitin.com','domain','bot'), 
				('task=diskusage','url','Middleware Disk Quota Checker'), ('gsa-purdue-crawler','useragent','Purdue GSA Crawler'), 
				('47.76.209.138','ip','Alibaba bot'), ('47.76.99.127','ip','Alibaba bot'), ('crawl.%.web.naver.com','host','naver crawler'), 
				('%crawl.bytedance.com','host','bytedance crawler'), ('bytedance.com','domain','bot'), ('%dataproviderbot.com','host','crawler'), 
				('semrush.com','domain','bot'), ('sogou.com','domain','bot'), ('petalsearch.com','domain','bot'), ('yandex.com','domain','bot'), 
				('baidu.com','domain','bot'), ('naver.com','domain','bot'), ('seznam.cz','domain','bot'), ('internet-measurement.com','domain','bot'), 
				('monsido.com','domain','bot'), ('babbar.eu','domain','bot'), ('ahrefsbot','useragent','bot'), ('applebot','useragent','bot'), 
				('archive','useragent','bot'), ('baiduspider','useragent','bot'), ('barkrowler','useragent','bot'), ('bingbot','useragent','bot'), 
				('bytespider','useragent','bot'), ('claudebot','useragent','bot'), ('crawl','useragent','bot'), ('dataforseo','useragent','bot'), 
				('facebookexternalhit','useragent','bot'), ('facebot','useragent','bot'), ('feedfetcher','useragent','bot'), 
				('findlinks','useragent','bot'), ('gatus','useragent','security scanner'), ('googlebot','useragent','bot'), 
				('googleother','useragent','bot'), ('google-cloudvertexbot','useragent','bot'), ('google-extended','useragent','bot'), 
				('apis-google','useragent','bot'), ('adsense-google','useragent','bot'), ('adsbot-google','useragent','bot'), 
				('mediapartners-google','useragent','bot'), ('googleproducer','useragent','bot'), ('google-read-aloud','useragent','bot'), 
				('googleimageproxy','useragent','bot'), ('gptbot','useragent','bot'), ('gsa-crawler','useragent','bot'), ('fbsv.com','domain','bot'), 
				('harvest','useragent','bot'), ('internet-census','useragent','bot'), ('internet-measurement','useragent','bot'), 
				('mj12bot','useragent','bot'), ('monsidobot','useragent','bot'), ('msnbot','useragent','bot'), ('naver.me','useragent','bot'), 
				('nutch','useragent','bot'), ('ows.eu/owler','useragent','bot'), ('paqlebot','useragent','bot'), ('petalbot','useragent','bot'), 
				('prtg network monitor','useragent','security monitor'), ('robot','useragent','bot'), ('crawler','useragent','bot'), 
				('search','useragent','bot'), ('searchbot','useragent','bot'), ('semanticscholar','useragent','bot'), ('semrushbot','useragent','bot'), 
				('serpstatbot','useragent','bot'), ('seznam','useragent','bot'), ('slurp','useragent','bot'), ('sogou','useragent','bot'), 
				('spider','useragent','bot'), ('turnitin','useragent','bot'), ('twitterbot','useragent','bot'), ('yacybot','useragent','bot'), 
				('yandexbot','useragent','bot'), ('yeti','useragent','bot'), ('yisouspider','useragent','bot'), ('ccbot','useragent','bot'), 
				('dotbot','useragent','bot'), ('facebookplatform','useragent','bot'), ('oai-searchbot','useragent','bot'), 
				('rawvoice','useragent','bot'), ('%.web.naver.com','host','bot'), ('%.applebot.apple.com','host','bot'), 
				('crawl%.dataproviderbot.com','host','bot'), ('%.fetch.tunnel.googlezip.net','host','bot'), ('googlezip.net','domain','bot'), 
				('rate-limited-proxy-%.google.com','host','bot'), ('%.gae.googleusercontent.com','host','bot'), 
				('google-proxy-%.google.com','host','bot'), ('crawl-%mojeek.com','host','bot'), ('mojeek.com','domain','bot'), 
				('qwantbot-%qwant.com','host','bot'), ('qwantbot','useragent','bot'), ('qwant.com','domain','bot'), 
				('vm%.kaj.pouta.csc.fi','host','bot'), ('csc.fi','domain','bot'), ('security.criminalip.com','host','security scanner'), 
				('criminalip.com','domain','security scanner'), ('bot%coccoc.com','host','bot'), ('coccoc.com','domain','bot'), 
				('websetup.net','domain','bot'), ('%websetup.net','host','bot'), ('cdmhub.org','domain','Hubzero'), 
				('web01.int.cdmhub.org','host','Hubzero'), ('132.249.202.211','ip','Hubzero cdmhub'), ('132.249.202.195','ip','Hubzero ghub/vhub'), 
				('web01.int.vhub.org','host','Hubzero'), ('vhub.org','domain','Hubzero'), ('132.249.202.77','ip','Hubzero nanohub'), 
				('web01.int.nanohub.org ','host','Hubzero'), ('nanohub.org','domain','Hubzero'), ('132.249.203.35','ip','Hubzero mygeohub'), 
				('web01.int.mygeohub.org','host','hubzero'), ('mygeohub.org','domain','hubzero'), 
				('web01.int.communityhub.hubzero.org','host','hubzero'), ('132.249.202.227','ip','hubzero'), 
				('wup.sdsc.edu','host','sdsc monitoring'), ('sdsc.edu','domain','sdsc'), ('132.249.203.3','ip','hubzero qubes'), 
				('web01.int.qubeshub.org','host','hubzero'), ('qubeshub.org','domain','hubzero'), ('132.249.202.243','ip','hubzero pharmahub'), 
				('web01.int.pharmahub.org','host','hubzero'), ('pharmahub.org','domain','hubzero'),('web01.int.purr.hubzero.org','host','hubzero'),
				('132.249.202.163','ip','hubzero purr'),('132.249.202.51','ip','hubzero stemedhub'),('web01.int.stemedhub.org','host','hubzero'),
				('stemedhub.org','domain','hubzero'),('132.249.202.147','ip','hubzero help'),('web01.int.help.hubzero.org','host','hubzero'),
				('hubzero.org','domain','hubzero'),('132.249.203.115','ip','hubzero geodynamics'),('web01.int.geodynamics.org','host','hubzero'),
				('geodynamics.org','domain','hubzero'),('132.249.69.168','ip','SDSC PRTG Network Monitor'),
				('132.249.119.214','ip','SDSC Nessus security scanner'),('/cron/tick','useragent','Hub cron tick'),('52.17.9.21','ip','Detectify scanner, Ireland'),
				('52.17.98.131','ip','Detectify scanner, Ireland'),('107.20.158.220','ip','Detectify scanner, VA USA'),('3.234.180.95','ip','Detectify scanner, VA USA'),
				('34.234.177.119','ip','Detectify scanner, VA USA'),('3.23.244.253','ip','gatus.aws.hubzero.org'),('ahrefs.net','domain','bot'),
				('ahrefs.com','domain','bot'),('140.228.23.','ip','websetup.net'),('fbsv.net','domain','facebook indexers'),
				('fwdproxy%fbsv.net','host','facebook indexers'),('96.62.105.','ip','cdmhub unknown bot'),('91.124.117.','ip','cdmhub unknown bot'),
				('45.93.184.','ip','cdmhub unknown bot'),('45.89.148.','ip','cdmhub unknown bot'),('202.155.137.','ip','cdmhub unknown bot'),
				('146.103.10','ip','cdmhub unknown bot'),('102.129.130.','ip','cdmhub unknown bot'),('protopage','useragent','bot'),
				('twittr.com','domain','bot'),('example.com','domain','bot'),('94.102.55.18','ip','NL bot associated with example.com'),
				('f3netze.de', 'domain', 'bot'),('118.193.38.134','ip','bot'),('scrapy', 'useragent', 'bot'),('internet-census.org','domain','bot'),
				('%internet-census.org','host','bot'),('trafilatura','useragent','bot'),('censys-scanner.com','domain','security bot'),
				('scanner%censys-scanner.com','host','security bot'),('hosted-by-vdsina.ru','domain','bot'),('skypeuripreview','useragent','bot'),
				('panscient.com','useragent','bot'),('censysinspect','useragent','security bot'),('go-http-client','useragent','bot/scraper'),
				('python-requests','useragent','bot/scraper'),('cms-checker','useragent','bot'),('125.122.36.248','ip','attempt login 12k times'),
				('52.54.191.160','ip','tries to access /cron/tick'),('%.rcac.purdue.edu','host','Purdue')";
				$statsDb->setQuery($query);
				$statsDb->query();
				$this->log("Inserted records");
			}
			catch (\Exception $e)
			{
				$this->log($e->getMessage(), 'error');
			}
		}
	}

	/**
	 * Down
	 **/
	public function down()
	{
		// DB modifications don't typically provide symmetric down() functions
	}
}
