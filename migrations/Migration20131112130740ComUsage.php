<?php

use Hubzero\Content\Migration\Base;

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

/**
 * Migration script for usage setup
 **/
class Migration20131112130740ComUsage extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		// Get stats DB object
		$config     = \JComponentHelper::getParams('com_usage');
		$siteConfig = \JFactory::getConfig();

		$options['driver']   = $config->get('statsDBDriver');
		$options['host']     = $config->get('statsDBHost');
		$options['user']     = $config->get('statsDBUsername');
		$options['password'] = $config->get('statsDBPassword');
		$options['database'] = $config->get('statsDBDatabase');

		if (empty($options['driver']))
		{
			$options['driver']   = $siteConfig->get('dbtype');
		}
		if (empty($options['host']))
		{
			$options['host']     = $siteConfig->get('host');
		}
		if (empty($options['user']))
		{
			$options['user']     = $siteConfig->get('user');
		}
		if (empty($options['password']))
		{
			$options['password'] = $siteConfig->get('password');
		}
		if (empty($options['database']))
		{
			$options['database'] = $siteConfig->get('db') . '_metrics';
		}

		$originalDriver    = $options['driver'];
		$options['driver'] = 'pdo';

		try
		{
			$statsDb = \JDatabase::getInstance($options);
		}
		catch (Exception $e)
		{
			// Fail silently
			return true;
		}

		$options['driver'] = $originalDriver;

		if ($this->db->tableExists('#__extensions'))
		{
			$query = 'SELECT `params` FROM `#__extensions` WHERE element = "com_usage"';
			$this->db->setQuery($query);
			$result = $this->db->loadResult();

			$params = (array) json_decode($result);
		}
		else
		{
			$query = 'SELECT `params` FROM `#__plugins` WHERE element = "com_usage"';
			$this->db->setQuery($query);
			$result = $this->db->loadResult();

			$params = array();

			if (!empty($result))
			{
				$ar = explode("\n", $result);

				foreach ($ar as $a)
				{
					$a = trim($a);
					if (empty($a))
					{
						continue;
					}

					$ar2 = explode("=", $a, 2);
					$params[$ar2[0]] = (isset($ar2[1])) ? $ar2[1] : '';
				}
			}
		}

		$params['statsDBDriver']   = $options['driver'];
		$params['statsDBHost']     = $options['host'];
		$params['statsDBUsername'] = $options['user'];
		$params['statsDBPassword'] = $options['password'];
		$params['statsDBDatabase'] = $options['database'];

		if ($this->db->tableExists('#__extensions'))
		{
			$params = json_encode($params);

			$query = 'UPDATE `#__extensions` SET params = '.$this->db->quote($params).' WHERE element = "com_usage"';
			$this->db->setQuery($query);
			$this->db->query();
		}
		else
		{
			$p = '';
			foreach ($params as $k => $v)
			{
				$p .= "{$k}={$v}\n";
			}

			$params = $p;

			$query = 'UPDATE `#__plugins` SET params = '.$this->db->quote($params).' WHERE element = "com_usage"';
			$this->db->setQuery($query);
			$this->db->query();
		}

		// Set up return if needed
		$return = new \stdClass();
		$return->error = new \stdClass();
		$return->error->type = 'warning';
		$return->error->message = 'Failed to create stats table. Try running again with elevated privileges';

		if (!$statsDb->tableExists('countries'))
		{
			try
			{
				$query = "CREATE TABLE IF NOT EXISTS `countries` (
							`code` varchar(4) NOT NULL DEFAULT '',
							`name` varchar(128) NOT NULL DEFAULT '',
							PRIMARY KEY  (`code`)
							) ENGINE=MyISAM DEFAULT CHARSET=utf8;";
				$statsDb->setQuery($query);
				$statsDb->query();

				$query = "INSERT INTO `countries` VALUES ('CN','CHINA'),('AU','AUSTRALIA'),('JP','JAPAN'),('TH','THAILAND'),('IN','INDIA'),('MY','MALAYSIA'),('KR','KOREA, REPUBLIC OF'),('HK','HONG KONG'),('TW','TAIWAN'),('PH','PHILIPPINES'),('VN','VIET NAM'),('FR','FRANCE'),('UK','UNITED KINGDOM'),('DE','GERMANY'),('SE','SWEDEN'),('IT','ITALY'),('ES','SPAIN'),('AT','AUSTRIA'),('NL','NETHERLANDS'),('AE','UNITED ARAB EMIRATES'),('IL','ISRAEL'),('UA','UKRAINE'),('CZ','CZECH REPUBLIC'),('RU','RUSSIAN FEDERATION'),('KZ','KAZAKHSTAN'),('PT','PORTUGAL'),('GR','GREECE'),('SA','SAUDI ARABIA'),('DK','DENMARK'),('IR','IRAN, ISLAMIC REPUBLIC OF'),('NO','NORWAY'),('US','UNITED STATES'),('CA','CANADA'),('MX','MEXICO'),('BM','BERMUDA'),('VI','VIRGIN ISLANDS, U.S.'),('PR','PUERTO RICO'),('NZ','NEW ZEALAND'),('SG','SINGAPORE'),('ID','INDONESIA'),('NP','NEPAL'),('PG','PAPUA NEW GUINEA'),('PK','PAKISTAN'),('CH','SWITZERLAND'),('IE','IRELAND'),('BS','BAHAMAS'),('VC','SAINT VINCENT AND THE GRENADINES'),('AR','ARGENTINA'),('UY','URUGUAY'),('DM','DOMINICA'),('BD','BANGLADESH'),('TK','TOKELAU'),('KH','CAMBODIA'),('MO','MACAO'),('MV','MALDIVES'),('AF','AFGHANISTAN'),('NC','NEW CALEDONIA'),('FJ','FIJI'),('MN','MONGOLIA'),('WF','WALLIS AND FUTUNA'),('PL','POLAND'),('RO','ROMANIA'),('TR','TURKEY'),('SK','SLOVAKIA'),('MK','MACEDONIA, THE FORMER YUGOSLAV REPUBLIC OF'),('FI','FINLAND'),('AM','ARMENIA'),('SI','SLOVENIA'),('SY','SYRIAN ARAB REPUBLIC'),('LI','LIECHTENSTEIN'),('QA','QATAR'),('BE','BELGIUM'),('NG','NIGERIA'),('BG','BULGARIA'),('IS','ICELAND'),('AL','ALBANIA'),('CY','CYPRUS'),('LU','LUXEMBOURG'),('HU','HUNGARY'),('EE','ESTONIA'),('BY','BELARUS'),('LV','LATVIA'),('IQ','IRAQ'),('KG','KYRGYZSTAN'),('MD','MOLDOVA, REPUBLIC OF'),('YE','YEMEN'),('LT','LITHUANIA'),('HR','CROATIA'),('BA','BOSNIA AND HERZEGOVINA'),('UZ','UZBEKISTAN'),('GE','GEORGIA'),('AZ','AZERBAIJAN'),('JE','JERSEY'),('SM','SAN MARINO'),('BR','BRAZIL'),('SJ','SVALBARD AND JAN MAYEN'),('ZA','SOUTH AFRICA'),('VE','VENEZUELA, BOLIVARIAN REPUBLIC OF'),('CO','COLOMBIA'),('EG','EGYPT'),('CL','CHILE'),('DZ','ALGERIA'),('PE','PERU'),('KW','KUWAIT'),('MA','MOROCCO'),('AO','ANGOLA'),('LY','LIBYAN ARAB JAMAHIRIYA'),('SD','SUDAN'),('EC','ECUADOR'),('OM','OMAN'),('DO','DOMINICAN REPUBLIC'),('LK','SRI LANKA'),('TN','TUNISIA'),('GT','GUATEMALA'),('LB','LEBANON'),('RS','SERBIA'),('MM','MYANMAR'),('CR','COSTA RICA'),('KE','KENYA'),('ET','ETHIOPIA'),('PA','PANAMA'),('JO','JORDAN'),('TZ','TANZANIA, UNITED REPUBLIC OF'),('CI','COTE DIVOIRE'),('CM','CAMEROON'),('SV','EL SALVADOR'),('BH','BAHRAIN'),('TT','TRINIDAD AND TOBAGO'),('BO','BOLIVIA, PLURINATIONAL STATE OF'),('GH','GHANA'),('PY','PARAGUAY'),('UG','UGANDA'),('ZM','ZAMBIA'),('HN','HONDURAS'),('GQ','EQUATORIAL GUINEA'),('JM','JAMAICA'),('AX','ALAND ISLANDS'),('AD','ANDORRA'),('FO','FAROE ISLANDS'),('GI','GIBRALTAR'),('GL','GREENLAND'),('GG','GUERNSEY'),('VA','HOLY SEE (VATICAN CITY STATE)'),('IM','ISLE OF MAN'),('MT','MALTA'),('MC','MONACO'),('ME','MONTENEGRO'),('PS','PALESTINIAN TERRITORY, OCCUPIED'),('TJ','TAJIKISTAN'),('TM','TURKMENISTAN'),('CD','CONGO, THE DEMOCRATIC REPUBLIC OF THE'),('AN','NETHERLANDS ANTILLES'),('BZ','BELIZE'),('SN','SENEGAL'),('MG','MADAGASCAR'),('NA','NAMIBIA'),('MW','MALAWI'),('GA','GABON'),('ML','MALI'),('BJ','BENIN'),('TD','CHAD'),('BW','BOTSWANA'),('CV','CAPE VERDE'),('RW','RWANDA'),('CG','CONGO'),('MZ','MOZAMBIQUE'),('GM','GAMBIA'),('LS','LESOTHO'),('MU','MAURITIUS'),('ZW','ZIMBABWE'),('BF','BURKINA FASO'),('SL','SIERRA LEONE'),('SO','SOMALIA'),('NE','NIGER'),('CF','CENTRAL AFRICAN REPUBLIC'),('SZ','SWAZILAND'),('TG','TOGO'),('BI','BURUNDI'),('SC','SEYCHELLES'),('GN','GUINEA'),('GW','GUINEA-BISSAU'),('LR','LIBERIA'),('MR','MAURITANIA'),('DJ','DJIBOUTI'),('RE','REUNION'),('NI','NICARAGUA'),('CU','CUBA'),('KY','CAYMAN ISLANDS'),('VG','VIRGIN ISLANDS, BRITISH'),('MH','MARSHALL ISLANDS'),('AQ','ANTARCTICA'),('BB','BARBADOS'),('AW','ARUBA'),('AI','ANGUILLA'),('KN','SAINT KITTS AND NEVIS'),('GD','GRENADA'),('LC','SAINT LUCIA'),('MS','MONTSERRAT'),('TC','TURKS AND CAICOS ISLANDS'),('AG','ANTIGUA AND BARBUDA'),('TV','TUVALU'),('PF','FRENCH POLYNESIA'),('SB','SOLOMON ISLANDS'),('VU','VANUATU'),('ER','ERITREA'),('HT','HAITI'),('SH','SAINT HELENA, ASCENSION AND TRISTAN DA CUNHA'),('FM','MICRONESIA, FEDERATED STATES OF'),('EH','WESTERN SAHARA'),('CX','CHRISTMAS ISLAND'),('LA','LAO PEOPLES DEMOCRATIC REPUBLIC'),('IO','BRITISH INDIAN OCEAN TERRITORY'),('GU','GUAM'),('WS','SAMOA'),('SR','SURINAME'),('CK','COOK ISLANDS'),('KI','KIRIBATI'),('NU','NIUE'),('TO','TONGA'),('TF','FRENCH SOUTHERN TERRITORIES'),('MQ','MARTINIQUE'),('YT','MAYOTTE'),('NF','NORFOLK ISLAND'),('AS','AMERICAN SAMOA'),('BN','BRUNEI DARUSSALAM'),('BT','BHUTAN'),('BV','BOUVET ISLAND'),('CC','COCOS (KEELING) ISLANDS'),('FK','FALKLAND ISLANDS (MALVINAS)'),('GF','FRENCH GUIANA'),('GP','GUADELOUPE'),('GS','SOUTH GEORGIA AND THE SOUTH SANDWICH ISLANDS'),('GY','GUYANA'),('HM','HEARD ISLAND AND MCDONALD ISLANDS'),('MP','NORTHERN MARIANA ISLANDS'),('PW','PALAU'),('UM','UNITED STATES MINOR OUTLYING ISLANDS'),('KP','KOREA, DEMOCRATIC PEOPLES REPUBLIC OF'),('NR','NAURU'),('PM','SAINT PIERRE AND MIQUELON'),('MF','SAINT MARTIN'),('KM','COMOROS'),('TL','TIMOR-LESTE'),('ST','SAO TOME AND PRINCIPE');";
				$statsDb->setQuery($query);
				$statsDb->query();
			}
			catch (Exception $e)
			{
				// Internally catch errors and only return a warning.
				return $return;
			}
		}

		if (!$statsDb->tableExists('sessionlog_metrics'))
		{
			try
			{
				$query = "CREATE TABLE IF NOT EXISTS `sessionlog_metrics` (
							`id` bigint(20) unsigned NOT NULL auto_increment,
							`sessnum` bigint(20) unsigned NOT NULL,
							`user` varchar(150) NOT NULL DEFAULT '',
							`ip` varchar(15) NOT NULL DEFAULT '',
							`start` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
							`appname` varchar(150) NOT NULL DEFAULT '',
							`host` tinytext,
							`domain` tinytext,
							`orgtype` tinytext,
							`countryresident` char(2) DEFAULT NULL,
							`countrycitizen` char(2) DEFAULT NULL,
							`ipcountry` char(2) DEFAULT NULL,
							PRIMARY KEY (`sessnum`),
							UNIQUE KEY (`id`),
							KEY `user` (`user`),
							KEY `start` (`start`),
							KEY `appname` (`appname`),
							KEY `countryresident` (`countryresident`),
							KEY `countrycitizen` (`countrycitizen`),
							KEY `orgtype` (`orgtype`(255))
							) ENGINE=MyISAM DEFAULT CHARSET=utf8;";
				$statsDb->setQuery($query);
				$statsDb->query();
			}
			catch (Exception $e)
			{
				// Internally catch errors and only return a warning.
				return $return;
			}
		}

		if (!$statsDb->tableExists('#__xprofiles_metrics'))
		{
			try
			{
				$query = "CREATE TABLE IF NOT EXISTS `#__xprofiles_metrics` (
							`uidNumber` int(11) NOT NULL,
							`name` varchar(255) NOT NULL DEFAULT '',
							`username` varchar(150) NOT NULL DEFAULT '',
							`email` varchar(100) NOT NULL DEFAULT '',
							`registerDate` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
							`gidNumber` varchar(11) NOT NULL DEFAULT '',
							`homeDirectory` varchar(255) NOT NULL DEFAULT '',
							`loginShell` varchar(255) NOT NULL DEFAULT '',
							`ftpShell` varchar(255) NOT NULL DEFAULT '',
							`userPassword` varchar(255) NOT NULL DEFAULT '',
							`gid` varchar(255) NOT NULL DEFAULT '',
							`orgtype` varchar(255) NOT NULL DEFAULT '',
							`organization` varchar(255) NOT NULL DEFAULT '',
							`countryresident` char(2) NOT NULL DEFAULT '',
							`countryorigin` char(2) NOT NULL DEFAULT '',
							`gender` varchar(255) NOT NULL DEFAULT '',
							`url` varchar(255) NOT NULL DEFAULT '',
							`reason` text NOT NULL,
							`mailPreferenceOption` int(11) NOT NULL DEFAULT '0',
							`usageAgreement` int(11) NOT NULL DEFAULT '0',
							`jobsAllowed` int(11) NOT NULL DEFAULT '0',
							`modifiedDate` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
							`emailConfirmed` int(11) NOT NULL DEFAULT '0',
							`regIP` varchar(255) NOT NULL DEFAULT '',
							`regHost` varchar(255) NOT NULL DEFAULT '',
							`nativeTribe` varchar(255) NOT NULL DEFAULT '',
							`phone` varchar(255) NOT NULL DEFAULT '',
							`proxyPassword` varchar(255) NOT NULL DEFAULT '',
							`proxyUidNumber` varchar(255) NOT NULL DEFAULT '',
							`givenName` varchar(255) NOT NULL DEFAULT '',
							`middleName` varchar(255) NOT NULL DEFAULT '',
							`surname` varchar(255) NOT NULL DEFAULT '',
							`picture` varchar(255) NOT NULL DEFAULT '',
							`vip` int(11) NOT NULL DEFAULT '0',
							`public` tinyint(2) NOT NULL DEFAULT '0',
							`params` text NOT NULL,
							`note` text NOT NULL,
							`shadowExpire` int(11) DEFAULT NULL,
							PRIMARY KEY  (`uidNumber`),
							KEY `username` (`username`),
							KEY `orgtype` (`orgtype`),
							KEY `countryresident` (`countryresident`),
							KEY `countryorigin` (`countryorigin`),
							KEY `registerDate` (`registerDate`)
							) ENGINE=MyISAM DEFAULT CHARSET=utf8;";
				$statsDb->setQuery($query);
				$statsDb->query();
			}
			catch (Exception $e)
			{
				// Internally catch errors and only return a warning.
				return $return;
			}
		}

		if (!$statsDb->tableExists('summary_andmore_vals'))
		{
			try
			{
				$query = "CREATE TABLE IF NOT EXISTS `summary_andmore_vals` (
							`rowid` tinyint(4) NOT NULL DEFAULT '0',
							`colid` tinyint(4) NOT NULL DEFAULT '0',
							`datetime` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
							`period` tinyint(4) NOT NULL DEFAULT '1',
							`value` bigint(20) DEFAULT '0',
							`valfmt` tinyint(4) NOT NULL DEFAULT '0'
							) ENGINE=MyISAM DEFAULT CHARSET=utf8;";
				$statsDb->setQuery($query);
				$statsDb->query();
			}
			catch (Exception $e)
			{
				// Internally catch errors and only return a warning.
				return $return;
			}
		}

		if (!$statsDb->tableExists('summary_misc_vals'))
		{
			try
			{
				$query = "CREATE TABLE IF NOT EXISTS `summary_misc_vals` (
							`rowid` tinyint(4) NOT NULL DEFAULT '0',
							`colid` tinyint(4) NOT NULL DEFAULT '0',
							`datetime` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
							`period` tinyint(4) NOT NULL DEFAULT '1',
							`value` varchar(200) DEFAULT '',
							`valfmt` tinyint(4) NOT NULL DEFAULT '0'
							) ENGINE=MyISAM DEFAULT CHARSET=utf8;";
				$statsDb->setQuery($query);
				$statsDb->query();
			}
			catch (Exception $e)
			{
				// Internally catch errors and only return a warning.
				return $return;
			}
		}

		if (!$statsDb->tableExists('summary_simusage_vals'))
		{
			try
			{
				$query = "CREATE TABLE IF NOT EXISTS `summary_simusage_vals` (
							`rowid` tinyint(4) NOT NULL DEFAULT '0',
							`colid` tinyint(4) NOT NULL DEFAULT '0',
							`datetime` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
							`period` tinyint(4) NOT NULL DEFAULT '1',
							`value` bigint(20) DEFAULT '0',
							`valfmt` tinyint(4) NOT NULL DEFAULT '0'
							) ENGINE=MyISAM DEFAULT CHARSET=utf8;";
				$statsDb->setQuery($query);
				$statsDb->query();
			}
			catch (Exception $e)
			{
				// Internally catch errors and only return a warning.
				return $return;
			}
		}

		if (!$statsDb->tableExists('toolstart'))
		{
			try
			{
				$query = "CREATE TABLE IF NOT EXISTS `toolstart` (
							`id` bigint(20) NOT NULL auto_increment,
							`sessionid` bigint(20) DEFAULT NULL,
							`datetime` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
							`orgtype` tinytext,
							`countryresident` char(2) DEFAULT NULL,
							`countrycitizen` char(2) DEFAULT NULL,
							`success` tinyint(4) NOT NULL DEFAULT '0',
							`ipcountry` char(2) DEFAULT NULL,
							`ip` varchar(15) NOT NULL DEFAULT '',
							`host` tinytext,
							`user` varchar(150) DEFAULT NULL,
							`tool` tinytext NOT NULL,
							`pid` int(11) DEFAULT NULL,
							`domain` tinytext,
							`filesystem` tinytext,
							`execunit` tinytext,
							`walltime` float unsigned DEFAULT '0',
							`cputime` float unsigned DEFAULT '0',
							`error` tinytext,
							PRIMARY KEY  (`id`),
							KEY `datetime` (`datetime`),
							KEY `success` (`success`),
							KEY `sessionid` (`sessionid`),
							KEY `ipcountry` (`ipcountry`),
							KEY `countrycitizen` (`countrycitizen`),
							KEY `countryresident` (`countryresident`),
							KEY `orgtype` (`orgtype`(255)),
							KEY `ip` (`ip`),
							KEY `user` (`user`)
							) ENGINE=MyISAM DEFAULT CHARSET=utf8;";
				$statsDb->setQuery($query);
				$statsDb->query();
			}
			catch (Exception $e)
			{
				// Internally catch errors and only return a warning.
				return $return;
			}
		}

		if (!$statsDb->tableExists('userlogin'))
		{
			try
			{
				$query = "CREATE TABLE IF NOT EXISTS `userlogin` (
							`id` bigint(20) NOT NULL auto_increment,
							`datetime` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
							`user` varchar(255) NOT NULL DEFAULT '-',
							`uidNumber` bigint(20) DEFAULT '0',
							`ip` varchar(15) NOT NULL DEFAULT '',
							`action` varchar(40) NOT NULL DEFAULT '',
							PRIMARY KEY  (`id`),
							UNIQUE KEY `userlogin` (`datetime`,`user`,`uidNumber`,`ip`,`action`)
							) ENGINE=MyISAM DEFAULT CHARSET=utf8;";
				$statsDb->setQuery($query);
				$statsDb->query();
			}
			catch (Exception $e)
			{
				// Internally catch errors and only return a warning.
				return $return;
			}
		}

		if (!$statsDb->tableExists('web'))
		{
			try
			{
				$query = "CREATE TABLE IF NOT EXISTS `web` (
							`id` bigint(20) NOT NULL auto_increment,
							`elementid` bigint(20) DEFAULT NULL,
							`sessionid` bigint(20) DEFAULT NULL,
							`datetime` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
							`ipcountry` char(2) DEFAULT NULL,
							`content` tinytext NOT NULL,
							`referrer` tinytext,
							`useragent` tinytext,
							`ip` varchar(15) NOT NULL DEFAULT '',
							`host` tinytext,
							`domain` tinytext,
							`uidNumber` int(11) DEFAULT NULL,
							`apache_pid` varchar(120) NOT NULL DEFAULT '',
							`joomla_sessionid` varchar(120) NOT NULL DEFAULT '',
							`site_cookie` varchar(120) NOT NULL DEFAULT '',
							`auth_type` varchar(120) NOT NULL DEFAULT '',
							`component_name` varchar(120) NOT NULL DEFAULT '',
							`view_name` varchar(120) NOT NULL DEFAULT '',
							`task_name` varchar(120) NOT NULL DEFAULT '',
							`action_name` varchar(120) NOT NULL DEFAULT '',
							`item_name` varchar(120) NOT NULL DEFAULT '',
							`dnload` tinyint(4) DEFAULT NULL,
							PRIMARY KEY  (`id`),
							KEY `datetime` (`datetime`),
							KEY `sessionid` (`sessionid`),
							KEY `elementid` (`elementid`),
							KEY `ipcountry` (`ipcountry`),
							KEY `ip` (`ip`),
							KEY `content` (`content`(255))
							) ENGINE=MyISAM DEFAULT CHARSET=utf8;";
				$statsDb->setQuery($query);
				$statsDb->query();
			}
			catch (Exception $e)
			{
				// Internally catch errors and only return a warning.
				return $return;
			}
		}

		if (!$statsDb->tableExists('webhits'))
		{
			try
			{
				$query = "CREATE TABLE IF NOT EXISTS `webhits` (
							`datetime` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
							`hits` bigint(20) NOT NULL DEFAULT '0'
							) ENGINE=MyISAM DEFAULT CHARSET=utf8;";
				$statsDb->setQuery($query);
				$statsDb->query();
			}
			catch (Exception $e)
			{
				// Internally catch errors and only return a warning.
				return $return;
			}
		}

		if (!$statsDb->tableExists('websessions'))
		{
			try
			{
				$query = "CREATE TABLE IF NOT EXISTS `websessions` (
							`id` bigint(20) NOT NULL DEFAULT '0',
							`datetime` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
							`ipcountry` char(2) NOT NULL DEFAULT '',
							`ip` varchar(15) NOT NULL DEFAULT '',
							`host` tinytext,
							`domain` tinytext,
							`duration` bigint(20) NOT NULL DEFAULT '0',
							`jobs` tinyint(4) NOT NULL DEFAULT '0',
							`webevents` bigint(20) NOT NULL DEFAULT '0',
							PRIMARY KEY  (`id`),
							KEY `datetime` (`datetime`),
							KEY `ipcountry` (`ipcountry`),
							KEY `ip` (`ip`)
							) ENGINE=MyISAM DEFAULT CHARSET=utf8;";
				$statsDb->setQuery($query);
				$statsDb->query();
			}
			catch (Exception $e)
			{
				// Internally catch errors and only return a warning.
				return $return;
			}
		}

		if (!$statsDb->tableExists('domainclass'))
		{
			try
			{
				$query = "CREATE TABLE IF NOT EXISTS `domainclass` (
							`domain` varchar(64) NOT NULL DEFAULT '',
							`class` tinyint(4) NOT NULL DEFAULT '0',
							`country` varchar(4) NOT NULL DEFAULT '',
							`state` varchar(4) NOT NULL DEFAULT '',
							`name` tinytext NOT NULL,
							PRIMARY KEY  (`domain`),
							KEY `class` (`class`),
							KEY `domain` (`domain`,`class`)
							) ENGINE=MyISAM DEFAULT CHARSET=utf8;";
				$statsDb->setQuery($query);
				$statsDb->query();

				$query = "INSERT IGNORE INTO `domainclass` VALUES ('cnea.gov.ar',3,'ar','','Comision Nacional de la Energia Atomica'),('surfer.at',4,'at','','Telekabel Wien GmbH, Vienna'),('teleweb.at',4,'at','','Telekabel Wien GmbH, Wien'),('univie.ac.at',1,'at','','Universitaet Wien Zentraler Informatikdienst'),('tuwien.ac.at',1,'at','','Vienna University of Technology'),('ecu.edu.au',1,'au','','Edith Cowan University, Perth'),('tu-graz.ac.at',1,'au','','Graz University of Technology, Graz'),('mq.edu.au',1,'au','','Macquarie University, Sydney'),('optusnet.com.au',4,'au','','Optus Administration Pty Ltd'),('ozemail.com.au',4,'au','','OzEmail Pty Ltd'),('rmit.edu.au',1,'au','','RMIT University, Melbourne'),('tpgi.com.au',4,'au','','TPG Internet'),('telstra.com.au',4,'au','','Telecom Australia (Telstra)'),('newcastle.edu.au',1,'au','','University of Newcastle'),('usyd.edu.au',1,'au','','University of Sydney'),('azerin.com',0,'az','','AZERIN AZERBAYCAN-TURKIYE BILGI MUESSESESI, Baku'),('imec.be',0,'be','','BELNET, Brussels'),('skynet.be',4,'be','','Belgacom sa/nv, Brussels'),('coditel.net',0,'be','','CODITEL, Brussels'),('globaltt.com',4,'be','','Global Telephone & Telecommunication S. A.'),('kuleuven.ac.be',1,'be','','Katholieke Universiteit Leuven'),('tele2.be',4,'be','','Tele2 Belgium S.A.'),('ucl.ac.be',1,'be','','Universite Catholique de Louvain'),('brasiltelecom.net.br',4,'br','','Brasil Telecom S. A.'),('virtua.com.br',4,'br','','NET Servios de Communicao S.A.'),('puc-rio.br',1,'br','','PONTIFICIA UNIVERSIDADE CATOLICA DO RIO DE JANEIRO'),('ajato.com.br',0,'br','','Rede Ajato Ltda'),('telesp.net.br',4,'br','','TELECOMUNICACOES DE SAO PAULO S.A.'),('tdatabrasil.net.br',4,'br','','TELEFONICA EMPRESAS S/A, Sao Paulo'),('veloxzone.com.br',4,'br','','Telemar Norte Leste S.A., Rio de Janeiro'),('unicamp.br',1,'br','','UNIVERSIDADE ESTADUAL DE CAMPINAS'),('ufc.br',1,'br','','Universidade Federal do Ceara'),('tche.br',1,'br','','Universidade Federal do Rio Grande do Sul'),('usp.br',1,'br','','University de Sao Paulo'),('shawcable.net',4,'ca','ab','Shaw Cablesystems G.P.'),('telus.net',4,'ca','ab','Telus Communications Inc.'),('ualberta.ca',1,'ca','ab','University of Alberta'),('ubc.ca',1,'ca','bc','University of British Columbia'),('dal.ca',1,'ca','ns','Dalhousie University, Halifax'),('carleton.ca',1,'ca','on','Carleton University, Ottawa'),('iasl.com',4,'ca','on','Chunghwa Telecom Co., Ltd.'),('iplink.net',4,'ca','on','Interlink'),('mcmaster.ca',1,'ca','on','McMaster University, Hamilton'),('nrc.ca',3,'ca','on','National Research Council Canada, Ottawa'),('rogers.com',4,'ca','on','Rogers Communications Inc.'),('utoronto.ca',1,'ca','on','University of Toronto'),('uwindsor.ca',1,'ca','on','University of Windsor'),('sympatico.ca',4,'ca','qc','Bell Canada'),('concordia.ca',1,'ca','qc','Concordia University, Montreal'),('polymtl.ca',1,'ca','qc','Ecole Polytechnique de Montreal'),('ericsson.ca',2,'ca','qc','Ericsson Canada Inc.'),('mcgill.ca',1,'ca','qc','McGill University, Montreal'),('ulaval.ca',1,'ca','qc','Universite Laval, Quebec'),('usherb.ca',1,'ca','qc','Universite de Sherbrooke'),('uqtr.ca',1,'ca','qc','University du Quebec la Trois-Rivieres'),('videotron.ca',4,'ca','qc','Videotron Ltd., Montreal'),('sasknet.sk.ca',4,'ca','sk','SaskTel corp., Regina'),('hispeed.ch',4,'ch','','Cablecom GmbH'),('csfb.com',2,'ch','','Credit Suisse Group'),('ethz.ch',1,'ch','','Eidgenossische Technische Hochschule Zurich'),('psi.ch',1,'ch','','Paul Scherrer Institute'),('epfl.ch',2,'ch','','Swiss Federal Institute of Technology'),('unige.ch',1,'ch','','University de Geneve'),('unibas.ch',1,'ch','','University of Basel'),('bta.net.cn',4,'cn','','Bajing Telecom'),('tijmu.edu.cn',1,'cn','','Tianjin Medical University'),('tsinghua.edu.cn',1,'cn','','Tsinghua University'),('ysu.edu.cn',1,'cn','','Yanshan University'),('jiitindia.org',0,'cn','','no organization name'),('sh.cn',4,'cn','','unknwon china ISP'),('uniweb.net.co',4,'co','','UNIWEB'),('unal.edu.co',1,'co','','Universidad Nacional de Colombia, Bogata'),('uniandes.edu.co',1,'co','','Universidad de los Andes, Bogata'),('ucy.ac.cy',1,'cy','','University of Cyprus'),('fh-giessen.de',1,'de','','FH Giessen-Friedberg University of Applied Sciences'),('kfa-juelich.de',2,'de','','Forschungszentrum Julich in der Helmholtz'),('fhg.de',2,'de','','Fraunhofer-Gesellschaft'),('gatel.net',4,'de','','Global Access Telecommunications, Inc.'),('hu-berlin.de',1,'de','','Humboldt-Universitat zu Berlin'),('teleport-iabg.de',4,'de','','IABG-Infocom-Teleport'),('lrz-muenchen.de',2,'de','','Leibniz-Rechenzentrum Munchen'),('arcor-ip.net',0,'de','','Mannesmann Arcor AG & Co, Eschborn'),('mpg.de',1,'de','','Max Plank Institute'),('mpi-stuttgart.mpg.de',1,'de','','Max Plank Institute Stuttgart'),('mpis.mpg.de',1,'de','','Max Plank Institute Stuttgart'),('mpipks-dresden.mpg.de',1,'de','','Max Plank Institute fur Physik Komplexer Systeme'),('aktivanet.de',4,'de','','Preiswerter und Leistungsfaehig'),('rwth-aachen.de',1,'de','','RWTH-Aachen University'),('sbs.de',2,'de','','Siemens'),('t-dialin.net',4,'de','','T-Online International AG'),('tu-bs.de',1,'de','','Technische Universitat Braunschweig'),('tu-chemnitz.de',1,'de','','Technische Universitat Chemnitz'),('tu-darmstadt.de',1,'de','','Technische Universitat Darmstadt'),('tu-ilmenau.de',1,'de','','Technische Universitat Ilmenau'),('tu-muenchen.de',1,'de','','Technische Universitat Muenchen'),('tiscali.de',4,'de','','Tiscali SpA'),('uni-duisburg.de',1,'de','','Universitat Duisburg-Essen'),('uni-essen.de',1,'de','','Universitat Duisburg-Essen'),('unibw-hamburg.de',1,'de','','Universitat Duisburg-Essen'),('uni-erlangen.de',1,'de','','Universitat Erlangen-Nur'),('uni-hamburg.de',1,'de','','Universitat Hamburg'),('uni-hannover.de',1,'de','','Universitat Hannover-Startseite'),('uni-jena.de',1,'de','','Universitat Jena'),('uni-konstanz.de',1,'de','','Universitat Konstanz'),('uni-leipzig.de',1,'de','','Universitat Leipzig'),('uni-paderborn.de',1,'de','','Universitat Paderborn'),('uni-wuerzburg.de',1,'de','','Universitat Wurzburg'),('mcbone.net',4,'de','','freenet Cityline GmbH'),('t-ipconnect.de',4,'de','','t-ipconnect.de'),('dtu.dk',1,'dk','','Danmarks Tekniske Universitet, Kongens Lnngby'),('tele.dk',4,'dk','','Tele Danmark'),('starman.ee',4,'ee','','Starman Kaabeltelevisiooni AS'),('ttu.ee',1,'ee','','Tallinn University of Technology'),('link.net',4,'eg','','Link Egypt'),('tedata.net',0,'eg','','TE Data'),('auna.net',0,'es','','Auna Telecomunicaciones, S.A., Barcelona'),('csic.es',2,'es','','Consejo Superior de Investigaciones Cientificas, Madrid'),('intervip.com.br',0,'es','','INTERVIP INFORMATICA LTDA'),('jazztel.es',4,'es','','Jazztel, Madrid'),('rima-tde.net',4,'es','','TELEFONICA, S.A., Madrid'),('tecnun.es',1,'es','','Technologico de la Universidad de Navarra'),('upm.es',1,'es','','Universidad Politecnica de Madrid'),('usal.es',1,'es','','Universidad Politecnica de Salamanca'),('urv.es',1,'es','','Universidad Politecnica de Tarragona'),('upv.es',1,'es','','Universidad Politecnica de Valencia'),('ugr.es',1,'es','','Universidad de Granada'),('uab.es',1,'es','','Universitat Autonoma de Barcelona '),('uam.es',1,'es','','Universitat Autonoma de Madrid '),('jyu.fi',1,'fi','','University of Jyvaskylan'),('suomi.net',4,'fi','','oulu telephone company'),('noos.net',0,'fr','','Auxipar, Paris'),('ceram.fr',1,'fr','','CERAM Sophia Anitpolis European School of Business'),('univ-lyon1.fr',1,'fr','','Centre Informatique Scientifique et Medical de l\'Universite Claude Bernard Lyon 1'),('cea.fr',3,'fr','','Commissariat a L\'Energie Atomique'),('ec-lyon.fr',1,'fr','','Ecole Centrale de Lyon'),('enserg.fr',2,'fr','','Ecole Nationale Superieure d\'Electricite et de Radioelectricite de Grenoble'),('ensta.fr',2,'fr','','Ecole Nationale Superieure des Techniques Avancees'),('exabot.com',5,'fr','','Exalead S.A., Paris'),('dir.com',0,'fr','','FERMIC SA'),('insa-lyon.fr',1,'fr','','Institut National des Sciences Appliquees de Lyon'),('insa-toulouse.fr',1,'fr','','Institut National des Sciences Appliquuees de Toulouse'),('inist.fr',3,'fr','','Institut de l\'Information Scientifique et Technique'),('kaptech.net',0,'fr','','KAPTECH'),('laas.fr',2,'fr','','Laboratoire d\'Automatique et d\'Analyse des Systemes du CNRS'),('noos.fr',4,'fr','','NOOS (Lyonnaise Communications), Paris'),('nerim.net',4,'fr','','Nerim Networks, Paris'),('proxad.net',4,'fr','','ONLINE, Paris'),('univ-mrs.fr',1,'fr','','Reseau Informatique'),('st.com',2,'fr','','STMicroelectronics'),('club-internet.fr',4,'fr','','T-Online France, Paris'),('tele2.fr',4,'fr','','Tele2 France S.A.'),('u-psud.fr',1,'fr','','Universite Paris Sud, Orsay'),('ups-tlse.fr',1,'fr','','Universite Paul Sabatier de Toulous'),('univ-nantes.fr',1,'fr','','Universite de Nantes, Nantes'),('uvsq.fr',1,'fr','','Universite de Versailles-Saint-Quentin-en-Yvelines'),('wanadoo.fr',4,'fr','','Wanadoo France'),('baesystems.com',2,'gb','','BAE Systems plc'),('btopenworld.com',4,'gb','','British Telecommunications plc'),('btcentralplus.com',4,'gb','','British Telecommunications plc, London'),('brunel.ac.uk',1,'gb','','Brunel University'),('clara.net',0,'gb','','ClaraNET Ltd.'),('dl.ac.uk',1,'gb','','Daresbury Laboratory'),('dmu.ac.uk',1,'gb','','De Montfort University, Leicester'),('ed.ac.uk',1,'gb','','Edinburgh University'),('lancs.ac.uk',1,'gb','','Lancaster University'),('leeds.ac.uk',1,'gb','','Leeds University'),('npl.co.uk',0,'gb','','NPL Management Ltd'),('oracle.co.uk',2,'gb','','Oracle Ltd'),('plus.com',0,'gb','','PlusNet Technologies Ltd'),('qinetiq.com',0,'gb','','QinetiQ'),('qmul.ac.uk',1,'gb','','Queen Mary and Westfield College, University of London'),('qub.ac.uk',1,'gb','','Queens University Belfast'),('rhul.ac.uk',1,'gb','','Royal Holloway- University of London'),('soton.ac.uk',1,'gb','','Southampton University'),('surrey.ac.uk',1,'gb','','Surrey University'),('blueyonder.co.uk',4,'gb','','Telewest Communications Networks Limited'),('ucl.ac.uk',1,'gb','','University College London'),('cam.ac.uk',1,'gb','','University of Cambridge'),('gla.ac.uk',1,'gb','','University of Glasgow'),('luton.ac.uk',1,'gb','','University of Luton'),('rdg.ac.uk',1,'gb','','University of Reading'),('swan.ac.uk',1,'gb','','University of Wales Swansea'),('zen.co.uk',4,'gb','','Zen Internet Ltd'),('gtu.edu.ge',1,'ge','','Georgian Technical University'),('demokritos.gr',2,'gr','',' National Centre of Scientific Research \"DEMOKRITOS\"'),('uoa.gr',1,'gr','','National & Kapodistrian University of Athens'),('otenet.gr',4,'gr','','OTEnet'),('tellas.gr',4,'gr','','Tellas'),('cuhk.edu.hk',1,'hk','','Chinese University of Hong Kong Shatin'),('cityu.edu.hk',1,'hk','','City University of Hong Kong'),('polyu.edu.hk',1,'hk','','Hong Kong Polytechnic University'),('ust.hk',1,'hk','','Hong Kong University of Science and Technology'),('netvigator.com',0,'hk','','PCCW-HKT Datacom Services Limited'),('hku.hk',1,'hk','','University of Hong Kong'),('ifs.hr',1,'hr','','Institute of Physics, Zagreb'),('ui.edu',1,'id','','University of Indonesia, Jawa Borat'),('biu.ac.il',1,'il','','Bar-Ilan University, Ramat-Gan'),('barak.net.il',4,'il','','Barak I.T.C., Rosh Ha\'ayin'),('bezeqint.net',0,'il','','Bezeq International'),('knet.co.il',0,'il','','Mishkei Hatakam, Tel-Aviv'),('netvision.net.il',4,'il','','NetVision LTD., Haifa'),('tau.ac.il',1,'il','','Tel Aviv University, Tel Aviv'),('weizmann.ac.il',1,'il','','Weizmann Institute of Science, Rehovot'),('amity.edu',1,'in','','Amity Business School, Noida'),('bol.net.in',4,'in','','Bharat online, New Delhi'),('touchtelindia.net',4,'in','','Bharti Tele- Ventures limited, New Delhi'),('eth.net',4,'in','','Dishnet DSL Ltd., Chennai'),('ernet.in',4,'in','','ERNET'),('in2cable.com',0,'in','','In2cable (India) Ltd.'),('iitb.ac.in',1,'in','','Indian Institute of Technology Bombay'),('iitm.ac.in',1,'in','','Indian Institute of Technology Madras'),('da-iict.org',2,'in','','Institute Of Information and Communication Technology'),('jncasr.ac.in',1,'in','','Jawaharial Nehru Centre for Advanced Scientific Research'),('jnu.ac.in',1,'in','','Jawaharial Nehru University'),('tunmail.com',4,'in','','Metro Cable Internet Services, New Delhi'),('bose.res.in',2,'in','','S.N. Bose National Centre for Basic Sciences, Kolkata'),('sify.net',0,'in','','Satyam Infoway Limited, Chennai'),('vsnl.net.in',4,'in','','TATA indicom'),('jdvu.ac.in',1,'in','','jdvu.ac.in'),('kashanu.ac.ir',1,'ir','','Kashan University'),('sinet.ir',0,'ir','','Soroush Interactive Network'),('cnr.it',2,'it','','Consiglio Nazionale delle Ricerche'),('enea.it',2,'it','','ENEA Italian National Agency for New Technologies, Energy and the Environment'),('fastres.net',0,'it','','FASTWEB S.P.A., Milano'),('trieste.it',0,'it','','Geographical Domain Trieste'),('infn.it',3,'it','','Istituto Nazionale di Fisica Nucleare, Bologna'),('libero.it',1,'it','','Italia Online S.p.A.'),('garr.it',1,'it','','Italian Academic and Research Network'),('eutelia.it',0,'it','','Plugit Spa, Arezzo'),('polimi.it',1,'it','','Politecnico di Milano'),('sns.it',0,'it','','Scuola Normale Superiore di Pisa'),('interbusiness.it',4,'it','','Telecom Italia S.p.A., Roma'),('tiscali.it',4,'it','','Tiscali SpA'),('unibo.it',1,'it','','Universita\' degli Studi di Bologna'),('unile.it',1,'it','','Universita\' degli Studi di Lecce'),('unimib.it',1,'it','','Universita\' degli Studi di Milano - Bicocca'),('unimo.it',1,'it','','Universita\' degli Studi di Modena'),('unipr.it',1,'it','','Universita\' degli Studi di Parma'),('unipg.it',1,'it','','Universita\' degli Studi di Perugia'),('unipi.it',1,'it','','Universita\' degli Studi di Pisa'),('uniroma1.it',1,'it','','Universita\' degli Studi di Roma La Sapienza'),('uniroma2.it',1,'it','','Universita\' degli Studi di Roma Tor Vergata'),('unict.it',1,'it','','Universita\' di Catania'),('univ-lille1.fr',1,'it','','Universite des Sciences et Technologies de Lille'),('accsnet.ne.jp',4,'jp','','ACCS NET'),('asahi-net.or.jp',4,'jp','','ASAHI NET'),('ayu.ne.jp',4,'jp','','AYUNET'),('cybernet.co.jp',0,'jp','','CYBERNET SYSTEMS CO., LTD.'),('dion.ne.jp',4,'jp','','DION'),('yournet.ne.jp',4,'jp','','FreeBit.Net Service'),('fujitsu.co.jp',2,'jp','','Fujitsu Limited'),('goo.ne.jp',4,'jp','','GOO'),('hi-ho.ne.jp',4,'jp','','Hi-HO Internet Service'),('hokudai.ac.jp',1,'jp','','Hokkaido University'),('infoweb.ne.jp',4,'jp','','InfoWeb'),('jst.go.jp',3,'jp','','Japan Science and Technology Agency'),('kcn.ne.jp',4,'jp','','KCN-Net Service'),('kobe-u.ac.jp',1,'jp','','Kobe University'),('kyoto-u.ac.jp',1,'jp','','Kyoto University'),('mei.co.jp',2,'jp','','Matsushita Electric Industrial Co., Ltd.'),('mesh.ad.jp',2,'jp','','NEC Corporation'),('nec.co.jp',2,'jp','','NEC Corporation'),('nims.go.jp',3,'jp','','National Institute for Materials Science'),('ocn.ne.jp',4,'jp','','Open Computer Network'),('odn.ne.jp',4,'jp','','Open Data Network'),('osaka-u.ac.jp',1,'jp','','Osaka University'),('plala.or.jp',0,'jp','','PLALA'),('renesas.com',2,'jp','','Renesas Technology Corp., Tokyo'),('sec.or.jp',2,'jp','','Sapporo Electronics and Industries Cultivation'),('sel.co.jp',2,'jp','','Semiconductor Energy Laboratory Co., Ltd.'),('sharp.co.jp',2,'jp','','Sharp Corporation'),('shu.ac.uk',1,'jp','','Sheffield Hallam University'),('so-net.ne.jp',4,'jp','','So-net Service'),('bbtec.net',0,'jp','','SoftbankBB Corp., Tokyo'),('sony.co.jp',2,'jp','','Sony Corporation'),('t-com.ne.jp',4,'jp','','T-COM ADSL Service'),('tohoku.ac.jp',1,'jp','','Tohoku University'),('titech.ac.jp',1,'jp','','Tokyo Institute of Technology'),('pu-toyama.ac.jp',1,'jp','','Toyama Prefectural University'),('u-tokyo.ac.jp',1,'jp','','University of Tokyo'),('tsukuba.ac.jp',1,'jp','','University of Tsukuba'),('eonet.ne.jp',4,'jp','','eonet'),('ewha.ac.kr',1,'kr','','EWHA University'),('gist.ac.kr',1,'kr','','Gwangju Institute of Scinece and Technology'),('hananet.net',4,'kr','','Hanaro Telecom, Inc'),('inha.ac.kr',1,'kr','','INHA University, Nam-gu Incheon'),('icu.ac.kr',1,'kr','','Information and Communications University, Daejeon'),('korea.ac.kr',1,'kr','','Korea University Anam-dong'),('kaist.ac.kr',1,'kr','','Korean Advanced Institute of Science and Technology'),('kjist.ac.kr',1,'kr','','Kwangju Institute of Science & Technology'),('kyungwon.ac.kr',1,'kr','','Kyungwon University'),('postech.ac.kr',1,'kr','','Pohang Kyungbuk Korea System Management Team'),('samsung.co.kr',2,'kr','','Samsung Networks Inc, Seoul'),('snu.ac.kr',1,'kr','','Seoul National University, Seoul'),('skku.ac.kr',1,'kr','','SungKyunKwan University, Seoul'),('mii.lt',1,'lt','','Institute of Mathematics and Informatics, Vilnius'),('rst.lt',2,'lt','','Rytu Skirstomieji Tinklai'),('vtex.lt',0,'lt','','VTeX Typesetting Services'),('restena.lu',0,'lu','','Fondation RESTENA'),('aui.ma',1,'ma','','Al Akhawayn University in Ifrane'),('unet.com.mk',4,'mk','','Unet Ineternet, Skopje'),('dial.net.mx',4,'mx','','Avantel'),('ipicyt.edu.mx',1,'mx','','ipicyt, San Luis Potosi'),('issuecrawler.net',0,'nl','','Govcom.org Foundation'),('planet.nl',4,'nl','','Planet Media Group N.V.'),('quicknet.nl',4,'nl','','QuickNet B. V., Alkmaar'),('rug.nl',1,'nl','','Rijksuniversiteit Groningen'),('tudelft.nl',1,'nl','','Technische Universiteit Delft, Delft'),('tue.nl',1,'nl','','Technische Universiteit Eindhoven'),('chello.nl',4,'nl','','UPC Broadband N.V'),('utwente.nl',1,'nl','','Universiteit Twente'),('uu.nl',1,'nl','','Utrecht University'),('sulphurcanyon.com',4,'nm','','Sulphur Canyon Internet Services'),('chipcon.no',2,'no','','Chipcon AS'),('fastsearch.net',5,'no','','Fast Search and Transfer ASA'),('picsearch.com',5,'no','','Hammarby Fabriksv'),('nextgentel.com',0,'no','','NextGenTel AS'),('unik.no',1,'no','','UNIVERSITETSSTUDIENE P305 KJELLER'),('uib.no',1,'no','','Universitetet i Bergen'),('ntc.net.np',4,'np','','Napal Telecom Co. Ltd. '),('clarkson.edu',1,'ny','','Clarkson University'),('xtra.co.nz',4,'nz','','Telecom IP Limited'),('paradise.net.nz',4,'nz','','TelstraClear Limited'),('cwru.edu',1,'us','oh','Case Western Reserve University'),('telmex.com.pe',4,'pe','','Telmex'),('pieas.edu.pk',1,'pk','','Pakistan Institute of engineering & Applied Sciences'),('agh.edu.pl',1,'pl','','Akademia Gorniczo-Hutnicza, Krakow'),('aster.pl',4,'pl','','Aster City Cable SP. Z O.O., Warszawa'),('e-wro.net.pl',0,'pl','','DCG DOMINAS CONSULTING GROUP SP. Z O.O., Wroclaw'),('lublin.pl',0,'pl','','LUBMAN UMCS SP. Z O.O.'),('pp.com.pl',0,'pl','','PETER PAN KOMPUTERY'),('tpnet.pl',0,'pl','','TP S.A., Warszawa'),('kv.net.pl',0,'pl','','TYTUS.NET, Krakow'),('poznan.pl',1,'pl','','UNIWERSYTET IM.ADAMA MICKIEWICZA, Poznan'),('uj.edu.pl',1,'pl','','UNIWERSYTET JAGIELLONSKI, Krakow'),('wroc.pl',0,'pl','','WROCLAWSKIE CENTRUM SIECIOWO-SUPERKOMPUTEROWE PWR., Wroclav'),('fuw.edu.pl',1,'pl','','WYDZIAL FIZYKI UNIWERSYTETU WARSZAWSKIEGO, Warszawa'),('coqui.net',4,'pr','','Puerto Rico Telephone Company'),('uprr.pr',1,'pr','','University of Puerto Rico, Rio Piedras'),('edil.pub.ro',2,'ro','','EDIL Microelectronics R&D Centre, Bucharest'),('metav.ro',0,'ro','','Metav S.A., Bucharest'),('ras.ru',1,'ru','','Center for Science Telecommunications and Information technologies of RAS'),('imvs.ru',2,'ru','','Institute of Microprocessor Computer Systems State Company'),('mrsu.ru',1,'ru','','Mordovian State University N.P.Ogareva'),('miet.ru',1,'ru','','Moscow Institute of Electronic Technology'),('sci-nnov.ru',0,'ru','','Sandy Info'),('nsc.ru',1,'ru','','Siberian Branch of Russian Academy of Science'),('rssi.ru',1,'ru','','Space Research Institute of Russian Academy of Sciences'),('icomtex.ru',0,'ru','','icomtex.ru'),('isu.net.sa',4,'sa','','Internet Services Unit'),('bredbandsbolaget.se',4,'se','','Bredbandsbolaget'),('chalmers.se',1,'se','','Chalmers Tekneska Hogskola, Goteborg'),('liu.se',1,'se','','Linkopings Universitet'),('lu.se',1,'se','','Lunds university'),('mh.se',1,'se','','Mid Sweden University'),('kth.se',1,'se','','Royal Institute of Technology, Stockholm'),('siceit.se',4,'se','','SizeIT Drift AB'),('songnetworks.se',4,'se','','TDC Song'),('telia.com',0,'se','','TeliaSonera AB'),('skanova.com',0,'se','','TeliaSonera AB, Stockholm'),('chello.se',4,'se','','UPC Broadband N.V'),('uu.se',1,'se','','Uppsala universitet'),('ntu.edu.sg',1,'sg','','Namyang Technological University'),('nus.edu.sg',1,'sg','','National University of Singapore'),('pacific.net.sg',4,'sg','','Pacific Internet'),('singnet.com.sg',4,'sg','','SingTel'),('mystarhub.com.sg',4,'sg','','Starhub'),('maxonline.com.sg',4,'sg','','maxonline.com.sg'),('siol.net',0,'si','','SiOL d.o.o'),('telecom.sk',4,'sk','','Slovak Telecom a.s.'),('uniba.sk',1,'sk','','Univerzita Komenskeho v Bratislave'),('asianet.co.th',4,'th','','Asia Infornet co.,ltd'),('chula.ac.th',1,'th','','Chulalongkorn University, Bangkok'),('mahidol.ac.th',1,'th','','Mahidol University, Bangkok'),('psu.ac.th',1,'th','','Prince of Songkla University, Songkhla'),('bilkent.edu.tr',1,'tr','','Bilkent University, Ankara'),('doruk.net.tr',4,'tr','','Doruknet'),('metu.edu.tr',1,'tr','','Middle East Technical University, Ankara'),('atlas.net.tr',4,'tr','','Telekom Atlas Online'),('ttnet.net.tr',4,'tr','','Turk Telekom A.S.'),('ebix.net.tw',4,'tw','','Asia Pacific Online Service Inc'),('hinet.net',4,'tw','','Chunghwa Telecom Co., Ltd.'),('seed.net.tw',0,'tw','','Digital United Inc.'),('fju.edu.tw',1,'tw','','Fu Jen Catholic University, Taipei'),('itri.org.tw',2,'tw','','Industrial Technology Research Institute'),('mtksg.com',0,'tw','','Mediatek Singapore Pte Ltd'),('hcrc.edu.tw',1,'tw','','Ministry of Education Computer Center'),('ncnu.edu.tw',1,'tw','','National Chi Nan University'),('nctu.edu.tw',1,'tw','','National Chiao Tung University'),('nsc.gov.tw',3,'tw','','National Science Council'),('ntu.edu.tw',1,'tw','','National Taiwan University, Taipei'),('ntcu.net',1,'tw','','Northern Taiwan Community University'),('nthu.edu.tw',1,'tw','','Northern Taiwan Community University'),('roam.com.tw',0,'tw','','ROAM Multimedia Co. Ltd.'),('roam.net.tw',0,'tw','','ROAM Multimedia Co. Ltd.'),('stic.gov.tw',3,'tw','','Science & Technology Policy Research and Information Center'),('so-net.net.tw',4,'tw','','Sony Network Taiwan Limited.'),('tfn.net.tw',4,'tw','','Taiwan Fixed Network Co.,Ltd., Taipei'),('tsmc.com.tw',2,'tw','','Taiwan Semiconductor Manufacturing Company, Ltd.'),('umc.com',2,'tw','','United Microelectronics Corp.'),('bestnet.ua',0,'ua','','BestNet, Kharkov'),('kharkov.ua',0,'ua','','Geographical domain for Kharkov'),('kiev.ua',0,'ua','','Geographical domain for Kiev'),('gluk.org',0,'ua','','GlasNet-Ukraine, Ltd.'),('ntl.com',4,'uk','','NTL Internet Ltd'),('ntli.net',0,'uk','','NTL Internet Ltd'),('lanl.gov',3,'us','','Los Alamos National Labs'),('nasa.gov',3,'us','','NASA'),('nist.gov',3,'us','','NIST'),('nih.gov',3,'us','','National Institute of Health'),('nsf.gov',3,'us','','National Science Foundation'),('af.mil',3,'us','','US Airforce'),('army.mil',3,'us','','US Army'),('usda.gov',3,'us','','US Department of Agriculture'),('navy.mil',3,'us','','US Navy'),('nipr.mil',3,'us','','Unknown Military'),('uaf.edu',1,'us','ak','University of Alaska Fairbanks'),('cfdrc.com',2,'us','al','CFD Research Corporation'),('usouthal.edu',1,'us','al','University of South Alabama'),('alltel.net',4,'us','ar','ALLTEL Communications'),('asu.edu',1,'us','az','Arizona State University'),('jetstreamwireless.com',4,'us','az','JetStream Wireless'),('arizona.edu',1,'us','az','University of Arizona'),('attens.net',5,'us','ca','AT&T Enhanced Network Services'),('accelrys.com',2,'us','ca','Accelrys'),('atgi.net',0,'us','ca','Advanced Telcom Group'),('agilent.com',2,'us','ca','Agilent Technologies'),('alexa.com',0,'us','ca','Alexa Internet'),('amat.com',2,'us','ca','Applied Materials'),('ask.com',5,'us','ca','Ask Jeeves, Inc.'),('teoma.com',5,'us','ca','Ask Jeeves, Inc.'),('calpoly.edu',1,'us','ca','Cal Poly State University'),('caltech.edu',1,'us','ca','California Institute of Technology'),('csun.edu',1,'us','ca','California State University, Northridge'),('covad.net',4,'us','ca','Covad Communications Company'),('googlebot.com',5,'us','ca','Google Inc.'),('intel.com',2,'us','ca','Intel Corporation'),('jeteye.com',5,'us','ca','JetEye Technologies Inc.'),('looksmart.com',5,'us','ca','Looksmart, LTD'),('mediaserve.net',0,'us','ca','MediaServe LLC'),('o1.com',4,'us','ca','Option One Communications'),('av.com',0,'us','ca','Overture Services Inc.'),('overture.com',5,'us','ca','Overture Services Inc.'),('pe.net',0,'us','ca','Press-Enterprise Co.'),('saic.com',2,'us','ca','SAIC'),('silvaco.com',2,'us','ca','SILVACO Data Systems'),('sjsu.edu',1,'us','ca','San Jose State University'),('scu.edu',1,'us','ca','Santa Clara University'),('seagate.com',2,'us','ca','Seagate Technology, LLC.'),('sensitron.net',0,'us','ca','Sensitron'),('spectrolab.com',2,'us','ca','Spectrolab'),('stanford.edu',1,'us','ca','Stanford University'),('sun.com',2,'us','ca','Sun Microsystems, Inc.'),('synopsys.com',2,'us','ca','Synopsys, Inc.'),('telepacific.net',4,'us','ca','TelePacific Cummunications'),('isi.edu',1,'us','ca','USC/Information Sciences Institute'),('berkeley.edu',1,'us','ca','University of California at Berkeley'),('ucdavis.edu',1,'us','ca','University of California at Davis'),('uci.edu',1,'us','ca','University of California, Irvine'),('ucla.edu',1,'us','ca','University of California, Los Angles'),('ucr.edu',1,'us','ca','University of California, Riverside'),('ucsd.edu',1,'us','ca','University of California, San Diego'),('ucsb.edu',1,'us','ca','University of California, Santa Barbara'),('wj.com',2,'us','ca','Watkins-Johnson Company'),('algx.net',0,'us','ca','XO Communications'),('xo.net',4,'us','ca','XO Communications, Inc.'),('xilinx.com',2,'us','ca','Xilinx, Inc.'),('inktomi.com',5,'us','ca','Yahoo! Inc.'),('inktomisearch.com',5,'us','ca','Yahoo! Inc.'),('yahoo.com',5,'us','ca','Yahoo! Inc.'),('turnitin.com',5,'us','ca','iParadigms Inc.'),('lcinet.net',0,'us','ca','linkLINE Communications, Inc.'),('colostate.edu',1,'us','co','Colorado State University'),('level3.net',4,'us','co','Level 3 Communications, Inc'),('qwest.net',4,'us','co','Qwest Communications International Inc.'),('twtelecom.net',4,'us','co','Time Warner Telecom'),('colorado.edu',1,'us','co','University of Colorado'),('virtela.com',4,'us','co','Virtela Communications, Inc.'),('patmedia.net',4,'us','ct','Patriot Media & Communications, LLC'),('uconn.edu',1,'us','ct','University of Connecticut'),('howard.edu',1,'us','dc','Howard University'),('udel.edu',1,'us','de','University of Delaware'),('fau.edu',1,'us','fl','Florida Atlantic University'),('fsu.edu',1,'us','fl','Florida State University'),('ucf.edu',1,'us','fl','University of Central Florida'),('ufl.edu',1,'us','fl','University of Florida'),('usf.edu',1,'us','fl','University of South Florida'),('bellsouth.net',4,'us','ga','Bellsouth Internet Services'),('cdc.gov',3,'us','ga','Centers for Disease Control and Prevention'),('cau.edu',1,'us','ga','Clark Atlanta University'),('coastalnow.net',4,'us','ga','Coastal Communications'),('cox.net',4,'us','ga','Cox Communications'),('earthlink.net',4,'us','ga','Earthlink, Inc.'),('lightspeed.net',4,'us','ga','Earthlink, Inc.'),('mindspring.com',4,'us','ga','Earthlink, Inc.'),('gatech.edu',1,'us','ga','Georgia Institute of Technology'),('knology.net',0,'us','ga','KNOLOGY Holdings, Inc.'),('lordaecksargent.com',0,'us','ga','Lord, Aeck & Sargent'),('noment.net',0,'us','ga','Noment Networks'),('ameslab.gov',3,'us','ia','DOE Ames Laboratory (Iowa State)'),('iastate.edu',1,'us','ia','Iowa State University'),('micron.com',2,'us','id','Micron Technology'),('tcd.ie',1,'us','ie','University of Dublin Trinity College'),('prserv.net',4,'us','il','AT&T Global Network Services'),('bradley.edu',1,'us','il','Bradley University'),('dmisinetworks.com',0,'us','il','Fusion Broadband'),('dmisinetworks.net',0,'us','il','Fusion Broadband'),('iit.edu',1,'us','il','Illinois Institute of Technology'),('flexabit.net',0,'us','il','McLeodUSA'),('motorola.com',2,'us','il','Motorola Inc.'),('sc03.org',4,'us','il','National Center for Supercomputing Applications'),('niu.edu',1,'us','il','Northern Illinois University'),('northwestern.edu',1,'us','il','Northwestern University'),('procommcable.com',2,'us','il','Procomm Solutions'),('scnet.net',0,'us','il','Server Central Network'),('siu.edu',1,'us','il','Southern Illinois University'),('popsite.net',0,'us','il','StarNet, Inc.'),('travelclick.net',0,'us','il','TravelCLICK Inc.'),('uchicago.edu',1,'us','il','University of Chicago'),('uic.edu',1,'us','il','University of Illinois at Chicago'),('uiuc.edu',1,'us','il','University of Illinois at Urbana Champaign'),('bsu.edu',1,'us','in','Ball State University'),('bloomingdaletel.com',0,'us','in','Bloomingdale Telephone'),('ffni.com',4,'us','in','Fairnet, LLC.'),('in-motion.net',0,'us','in','In-Motion Inc.'),('indiana.edu',1,'us','in','Indiana University'),('iupui.edu',1,'us','in','Indiana University-Purdue University at Indianapolis'),('ssi-pci.net',0,'us','in','NameMyDots.Com, Inc.'),('purdue.edu',1,'us','in','Purdue University'),('riverwalk-apts.com',4,'us','in','River Walk Apartments'),('nd.edu',1,'us','in','University of Notre Dame'),('wintek.com',4,'us','in','Wintek Corporation'),('ksu.edu',1,'us','ks','Kansas State University'),('dialsprint.net',4,'us','ks','Sprint Communications Company L.P.'),('sprintbbd.net',4,'us','ks','Sprint Communications Company L.P.'),('spcsdns.net',4,'us','ks','Sprint PCS'),('louisville.edu',1,'us','ky','University of Louisville'),('lsu.edu',1,'us','la','Louisiana State University'),('louisiana.edu',1,'us','la','University of Louisiana at Lafayette'),('bu.edu',1,'us','ma','Boston University'),('conversent.net',4,'us','ma','Conversent Communications'),('harvard.edu',1,'us','ma','Harvard University'),('hhpublications.com',6,'us','ma','Horizon House Publications'),('mitre.org',2,'us','ma','MITRE Corporation'),('mit.edu',1,'us','ma','Massachusetts Institute of Technology'),('neu.edu',1,'us','ma','Northeastern University'),('raytheon.com',2,'us','ma','Raytheon Company'),('townisp.com',0,'us','ma','SHREWSBURY ELECTRIC & COMMUNITY CABLE'),('umass.edu',1,'us','ma','University of Massachusetts'),('wpi.edu',1,'us','ma','Worcester Polytechnic Institute'),('w3.org',2,'us','ma','World Wide Web Consortium'),('chesapeake.edu',1,'us','md','Chesapeake College'),('dmv.com',4,'us','md','DelMarVa OnLine'),('morgan.edu',1,'us','md','Morgan State University'),('tsi-telsys.com',0,'us','md','TSI Telsys, Inc.'),('umd.edu',1,'us','md','University of Maryland'),('delphi.com',2,'us','mi','Delphi Automotive Systems'),('dow.com',2,'us','mi','Dow Chemical Company'),('gvsu.edu',1,'us','mi','Grand Valley State University'),('msu.edu',1,'us','mi','Michigan State University'),('mtu.edu',1,'us','mi','Michigan Technological University'),('qltd.com',2,'us','mi','QLTD'),('ussignalcom.net',4,'us','mi','RVP Development'),('umich.edu',1,'us','mi','University of Michigan'),('mmm.com',2,'us','mn','3M Company'),('umn.edu',1,'us','mn','University of Minnesota'),('charter.com',4,'us','mo','Charter Communications'),('charterpipeline.net',4,'us','mo','Charter Communications'),('charter-stl.com',4,'us','mo','Charter Communications, St Louis'),('umr.edu',1,'us','mo','University of Missouri-Rolla'),('jsums.edu',1,'us','ms','Jackson State University'),('msstate.edu',1,'us','ms','Mississippi State University'),('montana.edu',1,'us','mt','Montana State University'),('duke.edu',1,'us','nc','Duke University'),('goodrich.com',2,'us','nc','Goodrich Corporation'),('ncsu.edu',1,'us','nc','North Carolina State University'),('uslec.net',4,'us','nc','USLEC Corp.'),('uncc.edu',1,'us','nc','University of North Carolina at Charlotte'),('alhyde.com',2,'us','nj','A.L.Hyde Company'),('att.net',4,'us','nj','AT&T Corp.'),('adp.com',0,'us','nj','Automatic Data Processing, Inc.'),('ge.com',2,'us','nj','General Electric Company'),('njit.edu',1,'us','nj','New Jersey Institute of Technology'),('pppl.gov',3,'us','nj','Princeton Plasma Physics Laboratory'),('princeton.edu',1,'us','nj','Princeton University'),('rcn.com',0,'us','nj','RCN'),('smartcity.com',0,'us','nj','Smart City Solutions'),('gigablast.com',5,'us','nm','Matt Wells'),('nmsu.edu',1,'us','nm','New Mexico State University'),('unlv.edu',1,'us','nv','University of Nevada at Las Vegas'),('attbi.com',4,'us','ny','AT&T Corp. (Comcast)'),('bnl.gov',3,'us','ny','Brookhaven National Laboratory'),('optonline.net',0,'us','ny','CSC Holdings, Inc'),('choiceone.net',4,'us','ny','Choice One OnLine, Inc.'),('cloud9.net',4,'us','ny','Cloud 9 Internet'),('columbia.edu',1,'us','ny','Columbia University'),('cornell.edu',1,'us','ny','Cornell University'),('corning.com',2,'us','ny','Corning Incorporated'),('deshaw.com',2,'us','ny','D. E. Shaw & Co., L. P.'),('kodak.com',2,'us','ny','Eastman Kodak'),('edelman.com',0,'us','ny','Edelman Public Relations'),('ibm.com',2,'us','ny','IBM Corporation'),('insightbb.com',4,'us','ny','Insight Communications'),('mchsi.com',4,'us','ny','Mediacom Communications Corporation'),('midtel.net',4,'us','ny','Middleburg Telephone Co.'),('rpi.edu',1,'us','ny','Rensselaer Polytechnic Institute'),('rit.edu',1,'us','ny','Rochester Institute of Technology'),('russreyn.com',0,'us','ny','Russell Reynolds Associates Inc'),('sunyit.edu',1,'us','ny','SUNY Institute of Technology at Utica/Rome'),('binghamton.edu',1,'us','ny','SUNY at Binghamton'),('sunysb.edu',1,'us','ny','SUNY at Stony Brook'),('buffalo.edu',1,'us','ny','State University of New York at Buffalo'),('stonybrook.edu',1,'us','ny','State University of New York/Stony Brook'),('syr.edu',1,'us','ny','Syracuse University'),('xerox.com',2,'us','ny','Xerox Corporation'),('fuse.net',4,'us','oh','Cincinnati Bell Telephone'),('convergys.com',2,'us','oh','Convergys Inc'),('muohio.edu',1,'us','oh','Miami University'),('ohio-state.edu',1,'us','oh','Ohio State University'),('ohiou.edu',1,'us','oh','Ohio University'),('sierralobo.com',0,'us','oh','Sierra Lobo, Inc.'),('uc.edu',1,'us','oh','University of Cincinnati'),('utoledo.edu',1,'us','oh','University of Toledo'),('okstate.edu',1,'us','ok','Oklahoma State University'),('ou.edu',1,'us','ok','University of Oklahoma'),('mentorg.com',2,'us','or','Mentor Graphics Corporation'),('ogi.edu',1,'us','or','OGI School of Science and Engineering'),('pdx.edu',1,'us','or','Portland State University'),('adelphia.net',4,'us','pa','Adelphia Communications Corp.'),('agere.com',2,'us','pa','Agere Systems, Inc.'),('zoominternet.net',4,'us','pa','Armstrong Cable Services'),('cmu.edu',1,'us','pa','Carnegie-Mellon University'),('comcast.net',4,'us','pa','Comcast Corporation'),('lutron.com',0,'us','pa','Lutron Electronics Co., Inc.'),('psu.edu',1,'us','pa','Pennsylvania State University'),('temple.edu',1,'us','pa','Temple University'),('upenn.edu',1,'us','pa','University of Pennsylvania'),('pitt.edu',1,'us','pa','University of Pittsburgh'),('hks.com',0,'us','ri','ABAQUS, Inc'),('brown.edu',1,'us','ri','Brown University'),('lodgenet.net',0,'us','sd','LodgeNet Entertainment Company'),('cti-pet.com',2,'us','tn','CTI Molecular Imaging, Inc.'),('utk.edu',1,'us','tn','University of Tennessee'),('vanderbilt.edu',1,'us','tn','Vanderbilt University'),('cox-internet.com',4,'us','tx','Cox Communications'),('ev1servers.net',5,'us','tx','Ev1Servers.net'),('freescale.com',2,'us','tx','Freescale Semiconductor, Inc.'),('rice.edu',1,'us','tx','Rice University'),('ameritech.net',4,'us','tx','SBC Internet Services, Inc.'),('pacbell.net',4,'us','tx','SBC Internet Services, Inc.'),('snet.net',4,'us','tx','SBC Internet Services, Inc.'),('swbell.net',4,'us','tx','SBC Internet Services, Inc.'),('swri.edu',1,'us','tx','Southwest Research Institute, San Antonio'),('tamu.edu',1,'us','tx','Texas A&M University'),('tamuk.edu',1,'us','tx','Texas A&M University - Kingsville'),('ti.com',2,'us','tx','Texas Instruments, Inc.'),('ttu.edu',1,'us','tx','Texas Tech University'),('theplanet.com',4,'us','tx','The Planet Internet Services, Inc.'),('uh.edu',1,'us','tx','University of Houston'),('uta.edu',1,'us','tx','University of Texas at Arlington'),('utexas.edu',1,'us','tx','University of Texas at Austin'),('utep.edu',1,'us','tx','University of Texas at El Paso'),('utsa.edu',1,'us','tx','University of Texas at San Antonio'),('verizon.net',0,'us','tx','Verison'),('dsl-verizon.net',4,'us','tx','Verizon'),('vzavenue.net',4,'us','tx','Verizon'),('usu.edu',1,'us','ut','Utah State University'),('stsn.com',4,'us','ut','iBAHN (formerly STSN)'),('aol.com',4,'us','va','America Online, Inc.'),('bnsi.net',4,'us','va','Broadband Network Services Inc.'),('cryptic.net',0,'us','va','Cryptic Net Communications'),('gmai.com',2,'us','va','GMA Industries, Inc.'),('jrmtech.com',2,'us','va','Joseph Russ Moulton, jr.'),('marketscore.com',0,'us','va','Marketscore Inc'),('ntc-com.net',0,'us','va','NTC, LLC'),('newskies.net',4,'us','va','New Skies Networks, Inc.'),('rr.com',4,'us','va','Road Runner Hold Co, LLC'),('src.org',2,'us','va','Semiconductor Research Corporation'),('sysplan.com',0,'us','va','System Planning Corporation'),('uu.net',4,'us','va','UUNET Technologies, Inc.'),('virginia.edu',1,'us','va','University of Virginia'),('gte.net',0,'us','va','Verizon'),('cray.com',2,'us','wa','Cray Inc.'),('ncplus.net',0,'us','wa','Komko, Inc'),('search.msn.com',5,'us','wa','Microsoft'),('msn.com',4,'us','wa','Microsoft Corporation'),('transedge.com',0,'us','wa','New Edge Networks.'),('nctv.com',4,'us','wa','Northland Cable Television'),('pnl.gov',3,'us','wa','Pacific Northwest National Laboratory'),('tmodns.net',4,'us','wa','T-Mobile USA'),('washington.edu',1,'us','wa','University of Washington'),('wsu.edu',1,'us','wa','Washington State University'),('wisc.edu',1,'us','wi','University of Wisconsin'),('cantv.net',0,'ve','','CANTV Servicios, Caracas'),('genesisbci.net',0,'ve','','Genesis Telecom C.A.'),('bg.ac.yu',1,'yu','','Univerzitet u Beogradu'),('saix.net',0,'za','','Telkom SA Ltd'),('sfu.ca',1,'ca','bc','Simon Fraser University'),('uwaterloo.ca',1,'ca','on','University of Waterloo'),('auburn.edu',1,'us','al','Auburn University'),('uark.edu',1,'us','ar','University of Arkansas, Fayetteville'),('csupomona.edu',1,'us','ca','California State Polytechnic University, Pomona'),('glendale.cc.ca.us',1,'us','ca','Glendale Community College'),('untd.com',1,'us','ca','United Online, Inc.'),('ucsc.edu',1,'us','ca','University of California, Santa Cruz'),('du.edu',1,'us','co','University of Denver'),('yale.edu',1,'us','ct','Yale University'),('fiu.edu',1,'us','fl','Florida International University'),('uiowa.edu',1,'us','ia','University of Iowa'),('sxu.edu',1,'us','il','Saint Xavier University'),('uno.edu',1,'us','la','University of New Orleans'),('xula.edu',1,'us','la','Xavier University of Louisiana'),('bc.edu',1,'us','ma','Boston College'),('kopin.com',1,'us','ma','Kopin Corporation'),('jhu.edu',1,'us','md','Johns Hopkins University'),('umbc.edu',1,'us','md','University of Maryland Baltimore County'),('wayne.edu',1,'us','mi','Wayne State University'),('mnscu.edu',1,'us','mn','Minnesota State Colleges and Universities'),('wfu.edu',1,'us','nc','Wake Forest University'),('unh.edu',1,'us','nh','University of New Hampshire'),('rowan.edu',1,'us','nj','Rowan College of New Jersey'),('unm.edu',1,'us','nm','University of New Mexico'),('nyit.edu',1,'us','ny','New York Institute of Technology'),('nyu.edu',1,'us','ny','New York University'),('albany.edu',1,'us','ny','State University of New York, Albany'),('usma.army.mil',1,'us','ny','US Military Academy at West Point'),('lehigh.edu',1,'us','pa','Lehigh University'),('sdsmt.edu',1,'us','sd','South Dakota School of Mines & Technology'),('cbu.edu',1,'us','tn','Christian Brothers University'),('accd.edu',1,'us','tx','Alamo Community College District'),('smu.edu',1,'us','tx','Southern Methodist University'),('utah.edu',1,'us','ut','University of Utah'),('odu.edu',1,'us','va','Old Dominion University'),('vcu.edu',1,'us','va','Virginia Commonwealth University'),('cvtc.edu',1,'us','wi','Chippewa Valley Technical College'),('celestica.com',2,'ca','on','Celestica, Inc'),('sogetel.net',2,'ca','qb','Sogetel inc.'),('cypress.com',2,'us','ca','Cypress Semiconductor'),('gat.com',2,'us','ca','General Atomics'),('hp.com',2,'us','ca','Hewlett-Packard Company'),('northropgrumman.com',2,'us','ca','Northrop Grumman Corporation'),('qualcomm.com',2,'us','ca','Qualcomm Inc.'),('quantum.com',2,'us','ca','Quantum Corporation'),('rdl.com',2,'us','ca','Research & Development Laboratories'),('itnes.com',2,'us','co','ITN Energy Systems'),('lilly.com',2,'us','in','Eli Lilly and Company'),('guidant.com',2,'us','in','Guidant Corporation'),('htch.com',2,'us','mn','Hutchinson Technology Inc.'),('rea-alp.com',2,'us','mn','Runestone Electric Association'),('honeywell.com',2,'us','nj','Honeywell International Inc'),('fluentenergy.com',2,'us','ny','Fluent Energy'),('spectral-sys.com',2,'us','oh','Spectral Systems Inc'),('cmicro.com',2,'us','or','Cascade Microtech Inc.'),('mxim.com',2,'us','or','Maxim Integrated Products'),('nanomat.com',2,'us','pa','Nanomat, Inc.'),('amd.com',2,'us','tx','Advanced Micro Devices, Inc.'),('natinst.com',2,'us','tx','National Instruments Corporation'),('sematech.org',2,'us','tx','Sematech'),('fedsources.com',2,'us','va','Federal Sources Inc.'),('scitor.com',2,'us','va','Scitor Corporation'),('cit.org',2,'us','va','Virginia\'s Center for Innovation'),('boeing.com',2,'us','wa','Boeing Company'),('lbl.gov',3,'us','ca','Lawernce Berkeley National Laboratory'),('fda.gov',3,'us','dc','Food and Drug Administration'),('anl-external.org',3,'us','il','Argonne National Lab'),('fnal.gov',3,'us','il','Fermi National Laboratory'),('state.nj.us',3,'us','nj','NJ Office of Information Technology'),('sandia.gov',3,'us','nm','Sandia Natational Laboratory'),('wpafb.af.mil',3,'us','oh','Wright Patterson AFB'),('iisc.ernet.in',1,'in','','Indian Institute of Science'),('nanohub.org',1,'xx','xx','xx'),('zntu.edu.ua',1,'xx','xx','xx'),('zambonet.edu.ph',1,'xx','xx','xx'),('yzu.edu.cn',1,'xx','xx','xx'),('yuntech.edu.tw',1,'xx','xx','xx'),('yu.edu',1,'xx','xx','xx'),('ysu.edu',1,'xx','xx','xx'),('ym.edu.tw',1,'xx','xx','xx'),('ycrc.edu.tw',1,'xx','xx','xx'),('ycp.edu',1,'xx','xx','xx'),('xmu.edu.cn',1,'xx','xx','xx'),('xjtu.edu.cn',1,'xx','xx','xx'),('wwu.edu',1,'xx','xx','xx'),('wwc.edu',1,'xx','xx','xx'),('wvu.edu',1,'xx','xx','xx'),('wustl.edu',1,'xx','xx','xx'),('wse.edu.pl',1,'xx','xx','xx'),('wright.edu',1,'xx','xx','xx'),('worcester.edu',1,'xx','xx','xx'),('wofford.edu',1,'xx','xx','xx'),('wnmu.edu',1,'xx','xx','xx'),('wnec.edu',1,'xx','xx','xx'),('wmich.edu',1,'xx','xx','xx'),('wm.edu',1,'xx','xx','xx'),('wlc.edu',1,'xx','xx','xx'),('wku.edu',1,'xx','xx','xx'),('wiu.edu',1,'xx','xx','xx'),('wittenberg.edu',1,'xx','xx','xx'),('witc.edu',1,'xx','xx','xx'),('winona.edu',1,'xx','xx','xx'),('wilmcoll.edu',1,'xx','xx','xx'),('williams.edu',1,'xx','xx','xx'),('wilkes.edu',1,'xx','xx','xx'),('wichita.edu',1,'xx','xx','xx'),('whu.edu.cn',1,'xx','xx','xx'),('whitworth.edu',1,'xx','xx','xx'),('whitman.edu',1,'xx','xx','xx'),('wheaton.edu',1,'xx','xx','xx'),('westga.edu',1,'xx','xx','xx'),('wesleyan.edu',1,'xx','xx','xx'),('wednet.edu',1,'xx','xx','xx'),('weber.edu',1,'xx','xx','xx'),('wcu.edu',1,'xx','xx','xx'),('wat.edu.pl',1,'xx','xx','xx'),('washjeff.edu',1,'xx','xx','xx'),('washburn.edu',1,'xx','xx','xx'),('wartburgseminary.edu',1,'xx','xx','xx'),('warren-wilson.edu',1,'xx','xx','xx'),('walsh.edu',1,'xx','xx','xx'),('waldenu.edu',1,'xx','xx','xx'),('vwc.edu',1,'xx','xx','xx'),('vu.edu.au',1,'xx','xx','xx'),('vt.edu',1,'xx','xx','xx'),('vsu.edu',1,'xx','xx','xx'),('vnu.edu.tw',1,'xx','xx','xx'),('vims.edu',1,'xx','xx','xx'),('villanova.edu',1,'xx','xx','xx'),('viit.edu',1,'xx','xx','xx'),('vic.edu.au',1,'xx','xx','xx'),('vernoncollege.edu',1,'xx','xx','xx'),('vccs.edu',1,'xx','xx','xx'),('vc.edu',1,'xx','xx','xx'),('vassar.edu',1,'xx','xx','xx'),('valpo.edu',1,'xx','xx','xx'),('valdosta.edu',1,'xx','xx','xx'),('uwyo.edu',1,'xx','xx','xx'),('uwstout.edu',1,'xx','xx','xx'),('uwsp.edu',1,'xx','xx','xx'),('uws.edu.au',1,'xx','xx','xx'),('uwp.edu',1,'xx','xx','xx'),('uwm.edu.pl',1,'xx','xx','xx'),('uwm.edu',1,'xx','xx','xx'),('uwf.edu',1,'xx','xx','xx'),('uwec.edu',1,'xx','xx','xx'),('uwb.edu.pl',1,'xx','xx','xx'),('uwa.edu.au',1,'xx','xx','xx'),('uw.edu.pl',1,'xx','xx','xx'),('uvsc.edu',1,'xx','xx','xx'),('uvm.edu',1,'xx','xx','xx'),('uvi.edu',1,'xx','xx','xx'),('uu.edu',1,'xx','xx','xx'),('utulsa.edu',1,'xx','xx','xx'),('uts.edu.au',1,'xx','xx','xx'),('utn.edu.ec',1,'xx','xx','xx'),('utmem.edu',1,'xx','xx','xx'),('utmb.edu',1,'xx','xx','xx'),('utm.edu',1,'xx','xx','xx'),('uthscsa.edu',1,'xx','xx','xx'),('utdallas.edu',1,'xx','xx','xx'),('utc.edu',1,'xx','xx','xx'),('utas.edu.au',1,'xx','xx','xx'),('ust.edu.ph',1,'xx','xx','xx'),('usra.edu',1,'xx','xx','xx'),('usna.edu',1,'xx','xx','xx'),('usmd.edu',1,'xx','xx','xx'),('usg.edu',1,'xx','xx','xx'),('usfca.edu',1,'xx','xx','xx'),('usd.edu',1,'xx','xx','xx'),('usc.edu.ph',1,'xx','xx','xx'),('usc.edu',1,'xx','xx','xx'),('us.edu.pl',1,'xx','xx','xx'),('uri.edu',1,'xx','xx','xx'),('uq.edu.au',1,'xx','xx','xx'),('ups.edu',1,'xx','xx','xx'),('uprm.edu',1,'xx','xx','xx'),('uprh.edu',1,'xx','xx','xx'),('upr.edu',1,'xx','xx','xx'),('upmc.edu',1,'xx','xx','xx'),('uplb.edu.ph',1,'xx','xx','xx'),('upf.edu',1,'xx','xx','xx'),('upd.edu.ph',1,'xx','xx','xx'),('upc.edu',1,'xx','xx','xx'),('upb.edu.co',1,'xx','xx','xx'),('up.edu.ph',1,'xx','xx','xx'),('up.edu',1,'xx','xx','xx'),('uow.edu.au',1,'xx','xx','xx'),('uoregon.edu',1,'xx','xx','xx'),('uophx.edu',1,'xx','xx','xx'),('uop.edu',1,'xx','xx','xx'),('uofs.edu',1,'xx','xx','xx'),('uoa.edu.er',1,'xx','xx','xx'),('unt.edu',1,'xx','xx','xx'),('unsw.edu.au',1,'xx','xx','xx'),('uns.edu.ar',1,'xx','xx','xx'),('unrc.edu.ar',1,'xx','xx','xx'),('unr.edu.ar',1,'xx','xx','xx'),('unr.edu',1,'xx','xx','xx'),('unomaha.edu',1,'xx','xx','xx'),('unne.edu.ar',1,'xx','xx','xx'),('unmsm.edu.pe',1,'xx','xx','xx'),('unmc.edu',1,'xx','xx','xx'),('unlp.edu.ar',1,'xx','xx','xx'),('unl.edu',1,'xx','xx','xx'),('unk.edu',1,'xx','xx','xx'),('universia.edu.ve',1,'xx','xx','xx'),('univdhaka.edu',1,'xx','xx','xx'),('univalle.edu.co',1,'xx','xx','xx'),('unisa.edu.au',1,'xx','xx','xx'),('union.edu',1,'xx','xx','xx'),('uninorte.edu.co',1,'xx','xx','xx'),('unimelb.edu.au',1,'xx','xx','xx'),('unilibrecali.edu.co',1,'xx','xx','xx'),('uni.edu.pe',1,'xx','xx','xx'),('uni.edu',1,'xx','xx','xx'),('unf.edu',1,'xx','xx','xx'),('uner.edu.ar',1,'xx','xx','xx'),('une.edu.au',1,'xx','xx','xx'),('une.edu',1,'xx','xx','xx'),('uncw.edu',1,'xx','xx','xx'),('unco.edu',1,'xx','xx','xx'),('uncg.edu',1,'xx','xx','xx'),('unca.edu.ar',1,'xx','xx','xx'),('unca.edu',1,'xx','xx','xx'),('unc.edu.ar',1,'xx','xx','xx'),('unc.edu',1,'xx','xx','xx'),('unan.edu.ni',1,'xx','xx','xx'),('unam.edu.ar',1,'xx','xx','xx'),('unad.edu.co',1,'xx','xx','xx'),('una.edu',1,'xx','xx','xx'),('umw.edu',1,'xx','xx','xx'),('umuc.edu',1,'xx','xx','xx'),('umt.edu',1,'xx','xx','xx'),('umsmed.edu',1,'xx','xx','xx'),('umsl.edu',1,'xx','xx','xx'),('umontana.edu',1,'xx','xx','xx'),('uml.edu',1,'xx','xx','xx'),('umkc.edu',1,'xx','xx','xx'),('umh.edu',1,'xx','xx','xx'),('umdnj.edu',1,'xx','xx','xx'),('umassmed.edu',1,'xx','xx','xx'),('umassd.edu',1,'xx','xx','xx'),('umaryland.edu',1,'xx','xx','xx'),('umaine.edu',1,'xx','xx','xx'),('um.edu.my',1,'xx','xx','xx'),('uludag.edu.tr',1,'xx','xx','xx'),('ulm.edu',1,'xx','xx','xx'),('uky.edu',1,'xx','xx','xx'),('uitm.edu.my',1,'xx','xx','xx'),('uis.edu',1,'xx','xx','xx'),('uidaho.edu',1,'xx','xx','xx'),('uhv.edu',1,'xx','xx','xx'),('uga.edu',1,'xx','xx','xx'),('ufsj.edu.br',1,'xx','xx','xx'),('ufcg.edu.br',1,'xx','xx','xx'),('ufam.edu.br',1,'xx','xx','xx'),('udea.edu.co',1,'xx','xx','xx'),('udayton.edu',1,'xx','xx','xx'),('udallas.edu',1,'xx','xx','xx'),('uctm.edu',1,'xx','xx','xx'),('ucsf.edu',1,'xx','xx','xx'),('ucsc-extension.edu',1,'xx','xx','xx'),('ucop.edu',1,'xx','xx','xx'),('ucok.edu',1,'xx','xx','xx'),('ucmerced.edu',1,'xx','xx','xx'),('uclv.edu.cu',1,'xx','xx','xx'),('uchsc.edu',1,'xx','xx','xx'),('uchc.edu',1,'xx','xx','xx'),('uceou.edu',1,'xx','xx','xx'),('uccs.edu',1,'xx','xx','xx'),('ucatolica.edu.co',1,'xx','xx','xx'),('ucar.edu',1,'xx','xx','xx'),('ubaguio.edu',1,'xx','xx','xx'),('uan.edu.co',1,'xx','xx','xx'),('uams.edu',1,'xx','xx','xx'),('ualr.edu',1,'xx','xx','xx'),('uakron.edu',1,'xx','xx','xx'),('uah.edu',1,'xx','xx','xx'),('uab.edu',1,'xx','xx','xx'),('ua.edu',1,'xx','xx','xx'),('tyrc.edu.tw',1,'xx','xx','xx'),('txstate.edu',1,'xx','xx','xx'),('tvi.edu',1,'xx','xx','xx'),('tuskegee.edu',1,'xx','xx','xx'),('tulane.edu',1,'xx','xx','xx'),('tufts.edu',1,'xx','xx','xx'),('ttu.edu.tw',1,'xx','xx','xx'),('tsu.edu',1,'xx','xx','xx'),('truman.edu',1,'xx','xx','xx'),('trnty.edu',1,'xx','xx','xx'),('triton.edu',1,'xx','xx','xx'),('trinity.edu',1,'xx','xx','xx'),('trincoll.edu',1,'xx','xx','xx'),('tridenttech.edu',1,'xx','xx','xx'),('tpu.edu.ru',1,'xx','xx','xx'),('tpc.edu.tw',1,'xx','xx','xx'),('tp2rc.edu.tw',1,'xx','xx','xx'),('tp1rc.edu.tw',1,'xx','xx','xx'),('tp.edu.tw',1,'xx','xx','xx'),('toronto.edu',1,'xx','xx','xx'),('tntech.edu',1,'xx','xx','xx'),('tnrc.edu.tw',1,'xx','xx','xx'),('tmcc.edu',1,'xx','xx','xx'),('tmc.edu',1,'xx','xx','xx'),('tm.edu.ro',1,'xx','xx','xx'),('tlu.edu',1,'xx','xx','xx'),('tln.edu.ee',1,'xx','xx','xx'),('tku.edu.tw',1,'xx','xx','xx'),('tju.edu.cn',1,'xx','xx','xx'),('tju.edu',1,'xx','xx','xx'),('thu.edu.tw',1,'xx','xx','xx'),('tenet.edu',1,'xx','xx','xx'),('tcu.edu.tw',1,'xx','xx','xx'),('tcu.edu',1,'xx','xx','xx'),('tcnj.edu',1,'xx','xx','xx'),('tc.edu.tw',1,'xx','xx','xx'),('tayloru.edu',1,'xx','xx','xx'),('tarleton.edu',1,'xx','xx','xx'),('tamucc.edu',1,'xx','xx','xx'),('tamu-commerce.edu',1,'xx','xx','xx'),('tamiu.edu',1,'xx','xx','xx'),('sysu.edu.cn',1,'xx','xx','xx'),('swosu.edu',1,'xx','xx','xx'),('swmed.edu',1,'xx','xx','xx'),('swinburne.edu.my',1,'xx','xx','xx'),('swin.edu.au',1,'xx','xx','xx'),('swau.edu',1,'xx','xx','xx'),('swarthmore.edu',1,'xx','xx','xx'),('svsu.edu',1,'xx','xx','xx'),('svcc.edu',1,'xx','xx','xx'),('sunyrockland.edu',1,'xx','xx','xx'),('sulross.edu',1,'xx','xx','xx'),('sullivan.edu',1,'xx','xx','xx'),('suffolk.edu',1,'xx','xx','xx'),('subr.edu',1,'xx','xx','xx'),('stuy.edu',1,'xx','xx','xx'),('stut.edu.tw',1,'xx','xx','xx'),('stsci.edu',1,'xx','xx','xx'),('stolaf.edu',1,'xx','xx','xx'),('stmarytx.edu',1,'xx','xx','xx'),('stlcop.edu',1,'xx','xx','xx'),('stikom.edu',1,'xx','xx','xx'),('stgregorys.edu',1,'xx','xx','xx'),('stfrancis.edu',1,'xx','xx','xx'),('stevens.edu',1,'xx','xx','xx'),('stevens-tech.edu',1,'xx','xx','xx'),('stetson.edu',1,'xx','xx','xx'),('stedwards.edu',1,'xx','xx','xx'),('stcloudstate.edu',1,'xx','xx','xx'),('stchas.edu',1,'xx','xx','xx'),('sru.edu',1,'xx','xx','xx'),('squ.edu.om',1,'xx','xx','xx'),('spu.edu',1,'xx','xx','xx'),('spelman.edu',1,'xx','xx','xx'),('spcollege.edu',1,'xx','xx','xx'),('sp.edu.sg',1,'xx','xx','xx'),('sou.edu',1,'xx','xx','xx'),('sonoma.edu',1,'xx','xx','xx'),('solano.edu',1,'xx','xx','xx'),('smu.edu.ph',1,'xx','xx','xx'),('smsu.edu',1,'xx','xx','xx'),('smcm.edu',1,'xx','xx','xx'),('slu.edu',1,'xx','xx','xx'),('skku.edu',1,'xx','xx','xx'),('sju.edu.tw',1,'xx','xx','xx'),('sju.edu',1,'xx','xx','xx'),('sjtu.edu.cn',1,'xx','xx','xx'),('sjsmit.edu.tw',1,'xx','xx','xx'),('sivitanidios.edu.gr',1,'xx','xx','xx'),('siue.edu',1,'xx','xx','xx'),('sinte.edu',1,'xx','xx','xx'),('sinica.edu.tw',1,'xx','xx','xx'),('sinclair.edu',1,'xx','xx','xx'),('simmons.edu',1,'xx','xx','xx'),('si.edu',1,'xx','xx','xx'),('shu.edu',1,'xx','xx','xx'),('shsu.edu',1,'xx','xx','xx'),('ship.edu',1,'xx','xx','xx'),('shepherd.edu',1,'xx','xx','xx'),('shawu.edu',1,'xx','xx','xx'),('sfusd.edu',1,'xx','xx','xx'),('sfsu.edu',1,'xx','xx','xx'),('semo.edu',1,'xx','xx','xx'),('selu.edu',1,'xx','xx','xx'),('selcuk.edu.tr',1,'xx','xx','xx'),('seattleu.edu',1,'xx','xx','xx'),('sdu.edu.tr',1,'xx','xx','xx'),('sdsu.edu',1,'xx','xx','xx'),('sdstate.edu',1,'xx','xx','xx'),('sdsc.edu',1,'xx','xx','xx'),('scripps.edu',1,'xx','xx','xx'),('scranton.edu',1,'xx','xx','xx'),('sckans.edu',1,'xx','xx','xx'),('sccnc.edu',1,'xx','xx','xx'),('sc.edu',1,'xx','xx','xx'),('santarosa.edu',1,'xx','xx','xx'),('santafe.edu',1,'xx','xx','xx'),('sandburg.edu',1,'xx','xx','xx'),('samford.edu',1,'xx','xx','xx'),('salve.edu',1,'xx','xx','xx'),('sals.edu',1,'xx','xx','xx'),('salk.edu',1,'xx','xx','xx'),('saintmarys.edu',1,'xx','xx','xx'),('sabanciuniv.edu',1,'xx','xx','xx'),('sa.edu.au',1,'xx','xx','xx'),('rutgers.edu',1,'xx','xx','xx'),('rush.edu',1,'xx','xx','xx'),('rtc.edu',1,'xx','xx','xx'),('rpslmc.edu',1,'xx','xx','xx'),('rp.edu.sg',1,'xx','xx','xx'),('rose.edu',1,'xx','xx','xx'),('rose-hulman.edu',1,'xx','xx','xx'),('rosalindfranklin.edu',1,'xx','xx','xx'),('roosevelt.edu',1,'xx','xx','xx'),('rockhurst.edu',1,'xx','xx','xx'),('rockefeller.edu',1,'xx','xx','xx'),('rochester.edu',1,'xx','xx','xx'),('roch.edu',1,'xx','xx','xx'),('roanoke.edu',1,'xx','xx','xx'),('rider.edu',1,'xx','xx','xx'),('richmond.edu',1,'xx','xx','xx'),('rhodesstate.edu',1,'xx','xx','xx'),('regis.edu',1,'xx','xx','xx'),('reed.edu',1,'xx','xx','xx'),('ranken.edu',1,'xx','xx','xx'),('radford.edu',1,'xx','xx','xx'),('qut.edu.au',1,'xx','xx','xx'),('quinnipiac.edu',1,'xx','xx','xx'),('qld.edu.au',1,'xx','xx','xx'),('qc.edu',1,'xx','xx','xx'),('pwsz-kalisz.edu.pl',1,'xx','xx','xx'),('pw.edu.pl',1,'xx','xx','xx'),('pvam.edu',1,'xx','xx','xx'),('pupr.edu',1,'xx','xx','xx'),('puc.edu',1,'xx','xx','xx'),('pu.edu.tw',1,'xx','xx','xx'),('ptsem.edu',1,'xx','xx','xx'),('ptr.edu.ie',1,'xx','xx','xx'),('ptloma.edu',1,'xx','xx','xx'),('psc.edu',1,'xx','xx','xx'),('proxy.edu.tw',1,'xx','xx','xx'),('pratt.edu',1,'xx','xx','xx'),('potsdam.edu',1,'xx','xx','xx'),('pomona.edu',1,'xx','xx','xx'),('poly.edu',1,'xx','xx','xx'),('pnc.edu',1,'xx','xx','xx'),('plu.edu',1,'xx','xx','xx'),('planet.edu',1,'xx','xx','xx'),('pku.edu.cn',1,'xx','xx','xx'),('pk.edu.pl',1,'xx','xx','xx'),('pittstate.edu',1,'xx','xx','xx'),('pinecrest.edu',1,'xx','xx','xx'),('pie.edu.pl',1,'xx','xx','xx'),('phoenix.edu',1,'xx','xx','xx'),('pepperdine.edu',1,'xx','xx','xx'),('peachnet.edu',1,'xx','xx','xx'),('pccu.edu.tw',1,'xx','xx','xx'),('parkland.edu',1,'xx','xx','xx'),('pap.edu.pl',1,'xx','xx','xx'),('panam.edu',1,'xx','xx','xx'),('palomar.edu',1,'xx','xx','xx'),('pacificu.edu',1,'xx','xx','xx'),('owens.edu',1,'xx','xx','xx'),('ouhk.edu.hk',1,'xx','xx','xx'),('oswego.edu',1,'xx','xx','xx'),('osumc.edu',1,'xx','xx','xx'),('osu-okmulgee.edu',1,'xx','xx','xx'),('osc.edu',1,'xx','xx','xx'),('oru.edu',1,'xx','xx','xx'),('orst.edu',1,'xx','xx','xx'),('oregonstate.edu',1,'xx','xx','xx'),('onu.edu',1,'xx','xx','xx'),('oneonta.edu',1,'xx','xx','xx'),('omu.edu.tr',1,'xx','xx','xx'),('olin.edu',1,'xx','xx','xx'),('olemiss.edu',1,'xx','xx','xx'),('ohlone.edu',1,'xx','xx','xx'),('oc.edu',1,'xx','xx','xx'),('oberlin.edu',1,'xx','xx','xx'),('oakton.edu',1,'xx','xx','xx'),('oakland.edu',1,'xx','xx','xx'),('nyp.edu.sg',1,'xx','xx','xx'),('nwmissouri.edu',1,'xx','xx','xx'),('nuu.edu.tw',1,'xx','xx','xx'),('nung.edu.ua',1,'xx','xx','xx'),('nuk.edu.tw',1,'xx','xx','xx'),('nu.edu',1,'xx','xx','xx'),('ntut.edu.tw',1,'xx','xx','xx'),('ntust.edu.tw',1,'xx','xx','xx'),('nttu.edu.tw',1,'xx','xx','xx'),('nttc.edu',1,'xx','xx','xx'),('ntpu.edu.tw',1,'xx','xx','xx'),('ntou.edu.tw',1,'xx','xx','xx'),('ntnu.edu.tw',1,'xx','xx','xx'),('ntit.edu.tw',1,'xx','xx','xx'),('ntcpe.edu.tw',1,'xx','xx','xx'),('ntc.edu.ph',1,'xx','xx','xx'),('ntc.edu',1,'xx','xx','xx'),('nsysu.edu.tw',1,'xx','xx','xx'),('nsu.edu',1,'xx','xx','xx'),('nrao.edu',1,'xx','xx','xx'),('npust.edu.tw',1,'xx','xx','xx'),('nps.edu',1,'xx','xx','xx'),('nova.edu',1,'xx','xx','xx'),('norwich.edu',1,'xx','xx','xx'),('nodak.edu',1,'xx','xx','xx'),('noao.edu',1,'xx','xx','xx'),('nnu.edu',1,'xx','xx','xx'),('nmu.edu',1,'xx','xx','xx'),('nmt.edu',1,'xx','xx','xx'),('nku.edu',1,'xx','xx','xx'),('njnu.edu.cn',1,'xx','xx','xx'),('niu.edu.tw',1,'xx','xx','xx'),('nitt.edu',1,'xx','xx','xx'),('niit.edu.pk',1,'xx','xx','xx'),('nicholls.edu',1,'xx','xx','xx'),('nhust.edu.tw',1,'xx','xx','xx'),('nhu.edu.tw',1,'xx','xx','xx'),('ngcsu.edu',1,'xx','xx','xx'),('nfu.edu.tw',1,'xx','xx','xx'),('newhaven.edu',1,'xx','xx','xx'),('newenglandconservatory.edu',1,'xx','xx','xx'),('nevada.edu',1,'xx','xx','xx'),('neu.edu.tr',1,'xx','xx','xx'),('neiu.edu',1,'xx','xx','xx'),('neduet.edu.pk',1,'xx','xx','xx'),('ndmctsgh.edu.tw',1,'xx','xx','xx'),('ndhu.edu.tw',1,'xx','xx','xx'),('ncyu.edu.tw',1,'xx','xx','xx'),('ncue.edu.tw',1,'xx','xx','xx'),('ncu.edu.tw',1,'xx','xx','xx'),('ncku.edu.tw',1,'xx','xx','xx'),('ncit.edu.tw',1,'xx','xx','xx'),('nchu.edu.tw',1,'xx','xx','xx'),('nccu.edu.tw',1,'xx','xx','xx'),('ncat.edu',1,'xx','xx','xx'),('naz.edu',1,'xx','xx','xx'),('nau.edu',1,'xx','xx','xx'),('mwsu.edu',1,'xx','xx','xx'),('mwsc.edu',1,'xx','xx','xx'),('must.edu.tw',1,'xx','xx','xx'),('muskingum.edu',1,'xx','xx','xx'),('musc.edu',1,'xx','xx','xx'),('murdoch.edu.au',1,'xx','xx','xx'),('mum.edu',1,'xx','xx','xx'),('muhlenberg.edu',1,'xx','xx','xx'),('muctr.edu.ru',1,'xx','xx','xx'),('mu.edu.tr',1,'xx','xx','xx'),('mu.edu',1,'xx','xx','xx'),('mtholyoke.edu',1,'xx','xx','xx'),('msuiit.edu.ph',1,'xx','xx','xx'),('mstc.edu',1,'xx','xx','xx'),('mssm.edu',1,'xx','xx','xx'),('msoe.edu',1,'xx','xx','xx'),('msm.edu',1,'xx','xx','xx'),('mscd.edu',1,'xx','xx','xx'),('morehouse.edu',1,'xx','xx','xx'),('moody.edu',1,'xx','xx','xx'),('montcalm.edu',1,'xx','xx','xx'),('monroecc.edu',1,'xx','xx','xx'),('monroe.edu',1,'xx','xx','xx'),('monmouth.edu',1,'xx','xx','xx'),('monm.edu',1,'xx','xx','xx'),('monash.edu.my',1,'xx','xx','xx'),('monash.edu.au',1,'xx','xx','xx'),('moet.edu.vn',1,'xx','xx','xx'),('moe.edu.sg',1,'xx','xx','xx'),('mnsu.edu',1,'xx','xx','xx'),('mmu.edu.my',1,'xx','xx','xx'),('mmc.edu',1,'xx','xx','xx'),('mit.edu.au',1,'xx','xx','xx'),('missouriwestern.edu',1,'xx','xx','xx'),('missouristate.edu',1,'xx','xx','xx'),('missouri.edu',1,'xx','xx','xx'),('misericordia.edu',1,'xx','xx','xx'),('minnesota.edu',1,'xx','xx','xx'),('mines.edu',1,'xx','xx','xx'),('mimuw.edu.pl',1,'xx','xx','xx'),('milligan.edu',1,'xx','xx','xx'),('millersville.edu',1,'xx','xx','xx'),('miem.edu.ru',1,'xx','xx','xx'),('middlebury.edu',1,'xx','xx','xx'),('michelangelo.edu.br',1,'xx','xx','xx'),('miami.edu',1,'xx','xx','xx'),('mfldclin.edu',1,'xx','xx','xx'),('messiah.edu',1,'xx','xx','xx'),('mesastate.edu',1,'xx','xx','xx'),('merrimack.edu',1,'xx','xx','xx'),('mercynet.edu',1,'xx','xx','xx'),('mercyhurst.edu',1,'xx','xx','xx'),('menominee.edu',1,'xx','xx','xx'),('memphis.edu',1,'xx','xx','xx'),('meduohio.edu',1,'xx','xx','xx'),('mcw.edu',1,'xx','xx','xx'),('mcneese.edu',1,'xx','xx','xx'),('mcg.edu',1,'xx','xx','xx'),('mccd.edu',1,'xx','xx','xx'),('mc3.edu',1,'xx','xx','xx'),('mbl.edu',1,'xx','xx','xx'),('mayo.edu',1,'xx','xx','xx'),('matcmadison.edu',1,'xx','xx','xx'),('marshall.edu',1,'xx','xx','xx'),('marmara.edu.tr',1,'xx','xx','xx'),('marlboro.edu',1,'xx','xx','xx'),('maricopa.edu',1,'xx','xx','xx'),('mans.edu.eg',1,'xx','xx','xx'),('manhattan.edu',1,'xx','xx','xx'),('manchester.edu',1,'xx','xx','xx'),('maine.edu',1,'xx','xx','xx'),('macalester.edu',1,'xx','xx','xx'),('lyon.edu',1,'xx','xx','xx'),('lynchburg.edu',1,'xx','xx','xx'),('luzerne.edu',1,'xx','xx','xx'),('luther.edu',1,'xx','xx','xx'),('ludwig.edu.au',1,'xx','xx','xx'),('luc.edu',1,'xx','xx','xx'),('ltu.edu',1,'xx','xx','xx'),('loyno.edu',1,'xx','xx','xx'),('losrios.edu',1,'xx','xx','xx'),('llumc.edu',1,'xx','xx','xx'),('ljcrf.edu',1,'xx','xx','xx'),('linfield.edu',1,'xx','xx','xx'),('liberty.edu',1,'xx','xx','xx'),('lhup.edu',1,'xx','xx','xx'),('lfc.edu',1,'xx','xx','xx'),('letu.edu',1,'xx','xx','xx'),('lesley.edu',1,'xx','xx','xx'),('lemoyne.edu',1,'xx','xx','xx'),('lclark.edu',1,'xx','xx','xx'),('lccc.edu',1,'xx','xx','xx'),('lcc.edu',1,'xx','xx','xx'),('latrobe.edu.au',1,'xx','xx','xx'),('latech.edu',1,'xx','xx','xx'),('lamar.edu',1,'xx','xx','xx'),('lacoe.edu',1,'xx','xx','xx'),('kyu.edu.tw',1,'xx','xx','xx'),('kutkm.edu.my',1,'xx','xx','xx'),('kumc.edu',1,'xx','xx','xx'),('kuittho.edu.my',1,'xx','xx','xx'),('kuas.edu.tw',1,'xx','xx','xx'),('ku.edu.tr',1,'xx','xx','xx'),('ku.edu',1,'xx','xx','xx'),('ktu.edu.tr',1,'xx','xx','xx'),('ksu.edu.tw',1,'xx','xx','xx'),('ksu.edu.tr',1,'xx','xx','xx'),('kstu.edu.ua',1,'xx','xx','xx'),('ks.edu.tw',1,'xx','xx','xx'),('kpprc.edu.tw',1,'xx','xx','xx'),('kou.edu.tr',1,'xx','xx','xx'),('knox.edu',1,'xx','xx','xx'),('kiet.edu',1,'xx','xx','xx'),('kh.edu.tw',1,'xx','xx','xx'),('kgi.edu',1,'xx','xx','xx'),('kettering.edu',1,'xx','xx','xx'),('kentlaw.edu',1,'xx','xx','xx'),('kent.edu',1,'xx','xx','xx'),('kennesaw.edu',1,'xx','xx','xx'),('keene.edu',1,'xx','xx','xx'),('kctcs.edu',1,'xx','xx','xx'),('karunya.edu',1,'xx','xx','xx'),('kacst.edu.sa',1,'xx','xx','xx'),('jsu.edu',1,'xx','xx','xx'),('jmu.edu',1,'xx','xx','xx'),('jlu.edu.cn',1,'xx','xx','xx'),('jhuapl.edu',1,'xx','xx','xx'),('jhsph.edu',1,'xx','xx','xx'),('jhmi.edu',1,'xx','xx','xx'),('jcu.edu.au',1,'xx','xx','xx'),('iyte.edu.tr',1,'xx','xx','xx'),('iwu.edu',1,'xx','xx','xx'),('iwcc.edu',1,'xx','xx','xx'),('ivytech.edu',1,'xx','xx','xx'),('iusb.edu',1,'xx','xx','xx'),('iup.edu',1,'xx','xx','xx'),('iun.edu',1,'xx','xx','xx'),('iu.edu',1,'xx','xx','xx'),('itu.edu.tr',1,'xx','xx','xx'),('itmorelia.edu.mx',1,'xx','xx','xx'),('isunet.edu',1,'xx','xx','xx'),('isu.edu',1,'xx','xx','xx'),('istanbul.edu.tr',1,'xx','xx','xx'),('ist.edu.gr',1,'xx','xx','xx'),('isra.edu.jo',1,'xx','xx','xx'),('ismm.edu.cu',1,'xx','xx','xx'),('island.edu.hk',1,'xx','xx','xx'),('is.edu.ro',1,'xx','xx','xx'),('ircc.edu',1,'xx','xx','xx'),('ips.edu.ar',1,'xx','xx','xx'),('ipfw.edu',1,'xx','xx','xx'),('iona.edu',1,'xx','xx','xx'),('intimal.edu.my',1,'xx','xx','xx'),('internet2.edu',1,'xx','xx','xx'),('inonu.edu.tr',1,'xx','xx','xx'),('indstate.edu',1,'xx','xx','xx'),('indianhills.edu',1,'xx','xx','xx'),('imsa.edu',1,'xx','xx','xx'),('imr.edu',1,'xx','xx','xx'),('ilstu.edu',1,'xx','xx','xx'),('ilot.edu.pl',1,'xx','xx','xx'),('ilc.edu.tw',1,'xx','xx','xx'),('iiu.edu.my',1,'xx','xx','xx'),('igf.edu.pl',1,'xx','xx','xx'),('ifpan.edu.pl',1,'xx','xx','xx'),('ifj.edu.pl',1,'xx','xx','xx'),('iest.edu.mx',1,'xx','xx','xx'),('icm.edu.pl',1,'xx','xx','xx'),('ichf.edu.pl',1,'xx','xx','xx'),('ibun.edu.tr',1,'xx','xx','xx'),('ibu.edu.tr',1,'xx','xx','xx'),('ibngr.edu.pl',1,'xx','xx','xx'),('hvcc.edu',1,'xx','xx','xx'),('hut.edu.vn',1,'xx','xx','xx'),('humtec.edu.pe',1,'xx','xx','xx'),('hsu.edu',1,'xx','xx','xx'),('hsph.edu.vn',1,'xx','xx','xx'),('hsc.edu',1,'xx','xx','xx'),('howardcc.edu',1,'xx','xx','xx'),('hope.edu',1,'xx','xx','xx'),('hmc.edu',1,'xx','xx','xx'),('hkbu.edu.hk',1,'xx','xx','xx'),('hho.edu.tr',1,'xx','xx','xx'),('hfh.edu',1,'xx','xx','xx'),('herzing.edu',1,'xx','xx','xx'),('hcmuttt.edu.vn',1,'xx','xx','xx'),('hcmuns.edu.vn',1,'xx','xx','xx'),('hccs.edu',1,'xx','xx','xx'),('hccfl.edu',1,'xx','xx','xx'),('hbs.edu',1,'xx','xx','xx'),('hawaii.edu',1,'xx','xx','xx'),('haverford.edu',1,'xx','xx','xx'),('hartford.edu',1,'xx','xx','xx'),('hamilton.edu',1,'xx','xx','xx'),('hacettepe.edu.tr',1,'xx','xx','xx'),('gyte.edu.tr',1,'xx','xx','xx'),('gwu.edu',1,'xx','xx','xx'),('gu.edu.au',1,'xx','xx','xx'),('gsu.edu',1,'xx','xx','xx'),('grinnell.edu',1,'xx','xx','xx'),('griffith.edu.au',1,'xx','xx','xx'),('grcc.edu',1,'xx','xx','xx'),('grayson.edu',1,'xx','xx','xx'),('govst.edu',1,'xx','xx','xx'),('gonzaga.edu',1,'xx','xx','xx'),('gmu.edu',1,'xx','xx','xx'),('giki.edu.pk',1,'xx','xx','xx'),('gettysburg.edu',1,'xx','xx','xx'),('georgiasouthern.edu',1,'xx','xx','xx'),('georgetown.edu',1,'xx','xx','xx'),('geneseo.edu',1,'xx','xx','xx'),('geisinger.edu',1,'xx','xx','xx'),('gcsu.edu',1,'xx','xx','xx'),('gcc.edu',1,'xx','xx','xx'),('gazi.edu.tr',1,'xx','xx','xx'),('gatewaycc.edu',1,'xx','xx','xx'),('gasou.edu',1,'xx','xx','xx'),('gantep.edu.tr',1,'xx','xx','xx'),('galileo.edu',1,'xx','xx','xx'),('gac.edu',1,'xx','xx','xx'),('furman.edu',1,'xx','xx','xx'),('fullerton.edu',1,'xx','xx','xx'),('fscwv.edu',1,'xx','xx','xx'),('francis.edu',1,'xx','xx','xx'),('fq.edu.uy',1,'xx','xx','xx'),('fmarion.edu',1,'xx','xx','xx'),('flinders.edu.au',1,'xx','xx','xx'),('flcc.edu',1,'xx','xx','xx'),('fit.edu',1,'xx','xx','xx'),('fisk.edu',1,'xx','xx','xx'),('fisica.edu.uy',1,'xx','xx','xx'),('fing.edu.uy',1,'xx','xx','xx'),('fi.edu',1,'xx','xx','xx'),('fhda.edu',1,'xx','xx','xx'),('feu-eastasia.edu.ph',1,'xx','xx','xx'),('fdltcc.edu',1,'xx','xx','xx'),('fcu.edu.tw',1,'xx','xx','xx'),('fatih.edu.tr',1,'xx','xx','xx'),('fandm.edu',1,'xx','xx','xx'),('famu.edu',1,'xx','xx','xx'),('fairfield.edu',1,'xx','xx','xx'),('exploratorium.edu',1,'xx','xx','xx'),('exeter.edu',1,'xx','xx','xx'),('ewu.edu',1,'xx','xx','xx'),('evergreen.edu',1,'xx','xx','xx'),('evansville.edu',1,'xx','xx','xx'),('etsu.edu',1,'xx','xx','xx'),('esu.edu',1,'xx','xx','xx'),('espol.edu.ec',1,'xx','xx','xx'),('esb3-tomazfigueiredo.edu.pt',1,'xx','xx','xx'),('esb3-idhenrique.edu.pt',1,'xx','xx','xx'),('erciyes.edu.tr',1,'xx','xx','xx'),('erau.edu',1,'xx','xx','xx'),('eq.edu.au',1,'xx','xx','xx'),('epcc.edu',1,'xx','xx','xx'),('eou.edu',1,'xx','xx','xx'),('enmu.edu',1,'xx','xx','xx'),('emporia.edu',1,'xx','xx','xx'),('emory.edu',1,'xx','xx','xx'),('emich.edu',1,'xx','xx','xx'),('emerson.edu',1,'xx','xx','xx'),('elon.edu',1,'xx','xx','xx'),('elgin.edu',1,'xx','xx','xx'),('eku.edu',1,'xx','xx','xx'),('eiu.edu',1,'xx','xx','xx'),('eitw.edu.au',1,'xx','xx','xx'),('einstein.edu',1,'xx','xx','xx'),('ege.edu.tr',1,'xx','xx','xx'),('eduhq.edu.sc',1,'xx','xx','xx'),('educause.edu',1,'xx','xx','xx'),('edinboro.edu',1,'xx','xx','xx'),('edgewood.edu',1,'xx','xx','xx'),('ecu.edu',1,'xx','xx','xx'),('ecpi.edu',1,'xx','xx','xx'),('ecc.edu',1,'xx','xx','xx'),('eb23-vjuromenha.edu.pt',1,'xx','xx','xx'),('eb23-qtmarrocos.edu.pt',1,'xx','xx','xx'),('earlham.edu',1,'xx','xx','xx'),('eafit.edu.co',1,'xx','xx','xx'),('dyu.edu.tw',1,'xx','xx','xx'),('duq.edu',1,'xx','xx','xx'),('dtcc.edu',1,'xx','xx','xx'),('dsu.edu',1,'xx','xx','xx'),('dstc.edu.au',1,'xx','xx','xx'),('dri.edu',1,'xx','xx','xx'),('drexelmed.edu',1,'xx','xx','xx'),('drexel.edu',1,'xx','xx','xx'),('dordt.edu',1,'xx','xx','xx'),('dogus.edu.tr',1,'xx','xx','xx'),('dodea.edu',1,'xx','xx','xx'),('dicle.edu.tr',1,'xx','xx','xx'),('dhsphn.edu.vn',1,'xx','xx','xx'),('devry.edu',1,'xx','xx','xx'),('deu.edu.tr',1,'xx','xx','xx'),('depaul.edu',1,'xx','xx','xx'),('denison.edu',1,'xx','xx','xx'),('deakin.edu.au',1,'xx','xx','xx'),('dcs.edu',1,'xx','xx','xx'),('dcccd.edu',1,'xx','xx','xx'),('davidson.edu',1,'xx','xx','xx'),('davenport.edu',1,'xx','xx','xx'),('dartmouth.edu',1,'xx','xx','xx'),('cy.edu.tw',1,'xx','xx','xx'),('cwu.edu',1,'xx','xx','xx'),('curtin.edu.au',1,'xx','xx','xx'),('cuny.edu',1,'xx','xx','xx'),('cune.edu',1,'xx','xx','xx'),('cujae.edu.cu',1,'xx','xx','xx'),('cudenver.edu',1,'xx','xx','xx'),('cuc.edu',1,'xx','xx','xx'),('cua.edu',1,'xx','xx','xx'),('cu-portland.edu',1,'xx','xx','xx'),('ctu.edu',1,'xx','xx','xx'),('csusb.edu',1,'xx','xx','xx'),('csus.edu',1,'xx','xx','xx'),('csuohio.edu',1,'xx','xx','xx'),('csumb.edu',1,'xx','xx','xx'),('csulb.edu',1,'xx','xx','xx'),('csufresno.edu',1,'xx','xx','xx'),('csueastbay.edu',1,'xx','xx','xx'),('csudh.edu',1,'xx','xx','xx'),('csuchico.edu',1,'xx','xx','xx'),('csu.edu.tw',1,'xx','xx','xx'),('csu.edu.au',1,'xx','xx','xx'),('csu.edu',1,'xx','xx','xx'),('cshl.edu',1,'xx','xx','xx'),('cscc.edu',1,'xx','xx','xx'),('criba.edu.ar',1,'xx','xx','xx'),('creighton.edu',1,'xx','xx','xx'),('cqu.edu.au',1,'xx','xx','xx'),('covenant.edu',1,'xx','xx','xx'),('cooper.edu',1,'xx','xx','xx'),('conncoll.edu',1,'xx','xx','xx'),('colum.edu',1,'xx','xx','xx'),('colstate.edu',1,'xx','xx','xx'),('coloradotech.edu',1,'xx','xx','xx'),('coloradomtn.edu',1,'xx','xx','xx'),('coloradocollege.edu',1,'xx','xx','xx'),('collinscollege.edu',1,'xx','xx','xx'),('colgate.edu',1,'xx','xx','xx'),('colegiatura.edu.co',1,'xx','xx','xx'),('colby-sawyer.edu',1,'xx','xx','xx'),('cofc.edu',1,'xx','xx','xx'),('cod.edu',1,'xx','xx','xx'),('cobacalc.edu',1,'xx','xx','xx'),('coastalbend.edu',1,'xx','xx','xx'),('cnm.edu',1,'xx','xx','xx'),('cna-qatar.edu.qa',1,'xx','xx','xx'),('cmu.edu.tw',1,'xx','xx','xx'),('cmsu.edu',1,'xx','xx','xx'),('cmich.edu',1,'xx','xx','xx'),('cma.edu.tw',1,'xx','xx','xx'),('clu.edu',1,'xx','xx','xx'),('clemson.edu',1,'xx','xx','xx'),('clarku.edu',1,'xx','xx','xx'),('clark.edu',1,'xx','xx','xx'),('clarion.edu',1,'xx','xx','xx'),('claremont.edu',1,'xx','xx','xx'),('ckit.edu.tw',1,'xx','xx','xx'),('cju.edu.tw',1,'xx','xx','xx'),('cjcu.edu.tw',1,'xx','xx','xx'),('ciw.edu',1,'xx','xx','xx'),('cityue.edu.hk',1,'xx','xx','xx'),('cin.edu.uy',1,'xx','xx','xx'),('cier.edu.tw',1,'xx','xx','xx'),('cic.edu.tw',1,'xx','xx','xx'),('chw.edu',1,'xx','xx','xx'),('chu.edu.tw',1,'xx','xx','xx'),('christcollege.edu',1,'xx','xx','xx'),('chop.edu',1,'xx','xx','xx'),('chnu.edu.ua',1,'xx','xx','xx'),('chemeketa.edu',1,'xx','xx','xx'),('champlain.edu',1,'xx','xx','xx'),('cgu.edu.tw',1,'xx','xx','xx'),('cgu.edu',1,'xx','xx','xx'),('cfs.edu.hk',1,'xx','xx','xx'),('cfcc.edu',1,'xx','xx','xx'),('cdrewu.edu',1,'xx','xx','xx'),('ccut.edu.tw',1,'xx','xx','xx'),('ccu.edu.tw',1,'xx','xx','xx'),('ccit.edu.tw',1,'xx','xx','xx'),('ccc.edu',1,'xx','xx','xx'),('carthage.edu',1,'xx','xx','xx'),('carleton.edu',1,'xx','xx','xx'),('canyons.edu',1,'xx','xx','xx'),('cankaya.edu.tr',1,'xx','xx','xx'),('canberra.edu.au',1,'xx','xx','xx'),('camk.edu.pl',1,'xx','xx','xx'),('calvinchr.edu',1,'xx','xx','xx'),('calvin.edu',1,'xx','xx','xx'),('calstate.edu',1,'xx','xx','xx'),('callutheran.edu',1,'xx','xx','xx'),('calarts.edu',1,'xx','xx','xx'),('cabrillo.edu',1,'xx','xx','xx'),('byuh.edu',1,'xx','xx','xx'),('byu.edu',1,'xx','xx','xx'),('bvb.edu',1,'xx','xx','xx'),('butler.edu',1,'xx','xx','xx'),('bucknell.edu',1,'xx','xx','xx'),('bu.edu.ro',1,'xx','xx','xx'),('brynmawr.edu',1,'xx','xx','xx'),('bryantstratton.edu',1,'xx','xx','xx'),('broward.edu',1,'xx','xx','xx'),('brooklaw.edu',1,'xx','xx','xx'),('brockport.edu',1,'xx','xx','xx'),('bridgew.edu',1,'xx','xx','xx'),('brandeis.edu',1,'xx','xx','xx'),('boun.edu.tr',1,'xx','xx','xx'),('boisestate.edu',1,'xx','xx','xx'),('bluffton.edu',1,'xx','xx','xx'),('bju.edu',1,'xx','xx','xx'),('biola.edu',1,'xx','xx','xx'),('bia.edu',1,'xx','xx','xx'),('bhc.edu',1,'xx','xx','xx'),('bh.edu.ro',1,'xx','xx','xx'),('bgsu.edu',1,'xx','xx','xx'),('berry.edu',1,'xx','xx','xx'),('berklee.edu',1,'xx','xx','xx'),('bennington.edu',1,'xx','xx','xx'),('bellevue.edu',1,'xx','xx','xx'),('bcm.edu',1,'xx','xx','xx'),('bchs.edu',1,'xx','xx','xx'),('bcc.edu',1,'xx','xx','xx'),('baylor.edu',1,'xx','xx','xx'),('bates.edu',1,'xx','xx','xx'),('baskent.edu.tr',1,'xx','xx','xx'),('bahcesehir.edu.tr',1,'xx','xx','xx'),('austincollege.edu',1,'xx','xx','xx'),('austincc.edu',1,'xx','xx','xx'),('augustana.edu',1,'xx','xx','xx'),('augie.edu',1,'xx','xx','xx'),('aucegypt.edu',1,'xx','xx','xx'),('aub.edu.lb',1,'xx','xx','xx'),('au.edu',1,'xx','xx','xx'),('atu.edu',1,'xx','xx','xx'),('athens.edu',1,'xx','xx','xx'),('asbury.edu',1,'xx','xx','xx'),('armstrong.edu',1,'xx','xx','xx'),('aquinas.edu',1,'xx','xx','xx'),('apu.edu',1,'xx','xx','xx'),('aps.edu',1,'xx','xx','xx'),('appstate.edu',1,'xx','xx','xx'),('apollogrp.edu',1,'xx','xx','xx'),('anu.edu.au',1,'xx','xx','xx'),('annauniv.edu',1,'xx','xx','xx'),('ankara.edu.tr',1,'xx','xx','xx'),('angelo.edu',1,'xx','xx','xx'),('anadolu.edu.tr',1,'xx','xx','xx'),('amwaw.edu.pl',1,'xx','xx','xx'),('amu.edu.pl',1,'xx','xx','xx'),('ammanu.edu.jo',1,'xx','xx','xx'),('ammanacademy.edu.jo',1,'xx','xx','xx'),('amherst.edu',1,'xx','xx','xx'),('american.edu',1,'xx','xx','xx'),('amc.edu',1,'xx','xx','xx'),('alverno.edu',1,'xx','xx','xx'),('altamahatech.edu',1,'xx','xx','xx'),('allegheny.edu',1,'xx','xx','xx'),('alfredstate.edu',1,'xx','xx','xx'),('alaska.edu',1,'xx','xx','xx'),('aku.edu.tr',1,'xx','xx','xx'),('akdeniz.edu.tr',1,'xx','xx','xx'),('afit.edu',1,'xx','xx','xx'),('adnu.edu.ph',1,'xx','xx','xx'),('admu.edu.ph',1,'xx','xx','xx'),('adfa.edu.au',1,'xx','xx','xx'),('adelphi.edu',1,'xx','xx','xx'),('adelaide.edu.au',1,'xx','xx','xx'),('ab.edu.pl',1,'xx','xx','xx'),('aamu.edu',1,'xx','xx','xx'),('a-star.edu.sg',1,'xx','xx','xx'),('3sheep.edu',1,'xx','xx','xx'),('yorkcounty.gov',3,'xx','xx','xx'),('york.gov.uk',3,'xx','xx','xx'),('ymp.gov',3,'xx','xx','xx'),('wa.gov.au',3,'xx','xx','xx'),('wa.gov',3,'xx','xx','xx'),('vssc.gov.in',3,'xx','xx','xx'),('vpn.gov.ie',3,'xx','xx','xx'),('virginia.gov',3,'xx','xx','xx'),('vic.gov.au',3,'xx','xx','xx'),('va.gov',3,'xx','xx','xx'),('uspto.gov',3,'xx','xx','xx'),('usps.gov',3,'xx','xx','xx'),('usgs.gov',3,'xx','xx','xx'),('usdoj.gov',3,'xx','xx','xx'),('uscourts.gov',3,'xx','xx','xx'),('usbr.gov',3,'xx','xx','xx'),('umirm.gov.pl',3,'xx','xx','xx'),('ucia.gov',3,'xx','xx','xx'),('tva.gov',3,'xx','xx','xx'),('tubitak.gov.tr',3,'xx','xx','xx'),('treas.gov',3,'xx','xx','xx'),('to.gov.br',3,'xx','xx','xx'),('tas.gov.au',3,'xx','xx','xx'),('syzefxis.gov.gr',3,'xx','xx','xx'),('state.gov',3,'xx','xx','xx'),('srs.gov',3,'xx','xx','xx'),('sr.gov.yu',3,'xx','xx','xx'),('sfwmd.gov',3,'xx','xx','xx'),('sanantonio.gov',3,'xx','xx','xx'),('rzgw.gov.pl',3,'xx','xx','xx'),('railnet.gov.in',3,'xx','xx','xx'),('qld.gov.au',3,'xx','xx','xx'),('pidc.gov.tw',3,'xx','xx','xx'),('peacecorps.gov',3,'xx','xx','xx'),('pbh.gov.br',3,'xx','xx','xx'),('osti.gov',3,'xx','xx','xx'),('osis.gov',3,'xx','xx','xx'),('ornl.gov',3,'xx','xx','xx'),('orau.gov',3,'xx','xx','xx'),('nyc.gov',3,'xx','xx','xx'),('nsw.gov.au',3,'xx','xx','xx'),('nrel.gov',3,'xx','xx','xx'),('nrc.gov',3,'xx','xx','xx'),('norfolk.gov',3,'xx','xx','xx'),('noaa.gov',3,'xx','xx','xx'),('nitesl.gov.lk',3,'xx','xx','xx'),('nik.gov.pl',3,'xx','xx','xx'),('nersc.gov',3,'xx','xx','xx'),('nencki.gov.pl',3,'xx','xx','xx'),('neda.gov.ph',3,'xx','xx','xx'),('ncifcrf.gov',3,'xx','xx','xx'),('nasb.gov.by',3,'xx','xx','xx'),('nas.gov.ua',3,'xx','xx','xx'),('mte.gov.br',3,'xx','xx','xx'),('mpf.gov.br',3,'xx','xx','xx'),('mod.gov.il',3,'xx','xx','xx'),('mizoram.gov.in',3,'xx','xx','xx'),('mg.gov.br',3,'xx','xx','xx'),('maricopa.gov',3,'xx','xx','xx'),('mam.gov.tr',3,'xx','xx','xx'),('lnk.gov.cl',3,'xx','xx','xx'),('llnl.gov',3,'xx','xx','xx'),('la.gov',3,'xx','xx','xx'),('kgm.gov.tr',3,'xx','xx','xx'),('kapl.gov',3,'xx','xx','xx'),('judicial.gov.tw',3,'xx','xx','xx'),('johor.gov.my',3,'xx','xx','xx'),('jccbi.gov',3,'xx','xx','xx'),('irs.gov',3,'xx','xx','xx'),('ippt.gov.pl',3,'xx','xx','xx'),('inti.gov.ar',3,'xx','xx','xx'),('inmetro.gov.br',3,'xx','xx','xx'),('iner.gov.tw',3,'xx','xx','xx'),('inel.gov',3,'xx','xx','xx'),('il.gov',3,'xx','xx','xx'),('iimcb.gov.pl',3,'xx','xx','xx'),('ihs.gov',3,'xx','xx','xx'),('iem.gov.lv',3,'xx','xx','xx'),('idsc.gov.eg',3,'xx','xx','xx'),('ida.gov.sg',3,'xx','xx','xx'),('house.gov',3,'xx','xx','xx'),('hmgcc.gov.uk',3,'xx','xx','xx'),('hants.gov.uk',3,'xx','xx','xx'),('hanford.gov',3,'xx','xx','xx'),('gwynedd.gov.uk',3,'xx','xx','xx'),('gsi.gov.uk',3,'xx','xx','xx'),('gov66.gov.cz',3,'xx','xx','xx'),('gov12.gov.cz',3,'xx','xx','xx'),('gba.gov.ar',3,'xx','xx','xx'),('gao.gov',3,'xx','xx','xx'),('ga.gov',3,'xx','xx','xx'),('frb.gov',3,'xx','xx','xx'),('finance.gov.mk',3,'xx','xx','xx'),('fdic.gov',3,'xx','xx','xx'),('fcc.gov',3,'xx','xx','xx'),('fairfaxcounty.gov',3,'xx','xx','xx'),('faa.gov',3,'xx','xx','xx'),('epa.gov',3,'xx','xx','xx'),('eop.gov',3,'xx','xx','xx'),('environment-agency.gov.uk',3,'xx','xx','xx'),('eln.gov.br',3,'xx','xx','xx'),('eeoc.gov',3,'xx','xx','xx'),('ed.gov',3,'xx','xx','xx'),('dstl.gov.uk',3,'xx','xx','xx'),('dst.gov.za',3,'xx','xx','xx'),('dpf.gov.br',3,'xx','xx','xx'),('dot.gov',3,'xx','xx','xx'),('doechicago.gov',3,'xx','xx','xx'),('doeal.gov',3,'xx','xx','xx'),('doe.gov',3,'xx','xx','xx'),('doc.gov',3,'xx','xx','xx'),('dhs.gov',3,'xx','xx','xx'),('dfat.gov.au',3,'xx','xx','xx'),('dera.gov.uk',3,'xx','xx','xx'),('defence.gov.au',3,'xx','xx','xx'),('dcita.gov.au',3,'xx','xx','xx'),('dc.gov',3,'xx','xx','xx'),('csl.gov.uk',3,'xx','xx','xx'),('cise-nsf.gov',3,'xx','xx','xx'),('cia.gov',3,'xx','xx','xx'),('cga.gov.tw',3,'xx','xx','xx'),('ceride.gov.ar',3,'xx','xx','xx'),('census.gov',3,'xx','xx','xx'),('cenpra.gov.br',3,'xx','xx','xx'),('cat.gov.in',3,'xx','xx','xx'),('capes.gov.br',3,'xx','xx','xx'),('cabq.gov',3,'xx','xx','xx'),('ca.gov',3,'xx','xx','xx'),('bsmi.gov.tw',3,'xx','xx','xx'),('brighton-hove.gov.uk',3,'xx','xx','xx'),('bom.gov.au',3,'xx','xx','xx'),('blm.gov',3,'xx','xx','xx'),('bettis.gov',3,'xx','xx','xx'),('bernco.gov',3,'xx','xx','xx'),('bdep.gov.br',3,'xx','xx','xx'),('bart.gov',3,'xx','xx','xx'),('bank.gov.ua',3,'xx','xx','xx'),('bacninh.gov.vn',3,'xx','xx','xx'),('ansto.gov.au',3,'xx','xx','xx'),('anl.gov',3,'xx','xx','xx'),('alabama.gov',3,'xx','xx','xx'),('ac.gov.br',3,'xx','xx','xx'),('aao.gov.au',3,'xx','xx','xx'),('sbcglobal.net',4,'yy','yy','yy'),('bell.ca',4,'yy','yy','yy'),('telecomitalia.it',4,'yy','yy','yy'),('airtelbroadband.in',4,'yy','yy','yy'),('india.net',4,'yy','yy','yy'),('vutbr.cz',1,'cz','yy','BRNO University of Technology'),('sc06.org',1,'','yy','SC06'),('live.com',5,'yy','yy','yy'),('phx.gbl',5,'yy','yy','yy'),('bloglines.com',5,'yy','yy','yy'),('become.com',5,'yy','yy','yy'),('quest.net',2,'yy','yy','yy'),('brain.grub.org',5,'xx','xx','xx'),('cosmixcorp.com',5,'xx','xx','xx'),('crawl8-public.alexa.com',5,'xx','xx','xx'),('crawler918.com',5,'xx','xx','xx'),('girafa.com',5,'xx','xx','xx'),('hanta.yahoo.com',5,'xx','xx','xx'),('idle.eidetica.com',5,'xx','xx','xx'),('live-servers.net',5,'xx','xx','xx'),('looksmart.net',5,'xx','xx','xx'),('markwatch.com',5,'xx','xx','xx'),('metacarta.com',5,'xx','xx','xx'),('morgue1.corp.yahoo.com',5,'xx','xx','xx'),('msnbot.msn.com',5,'xx','xx','xx'),('panchma.tivra.com',5,'xx','xx','xx'),('tpiol.com',5,'xx','xx','xx'),('tpiol.tpiol.com',5,'xx','xx','xx'),('tracerlock.com',5,'xx','xx','xx'),('webclipping.com',5,'xx','xx','xx'),('websmostlinked.com',5,'xx','xx','xx'),('websquash.com',5,'xx','xx','xx'),('whizbang.com',5,'xx','xx','xx'),('xs4.kso.co.uk',5,'xx','xx','xx'),('zeus.nj.nec.com',5,'xx','xx','xx'),('archive.org',5,'xx','xx','xx'),('authoritativeweb.com',5,'xx','xx','xx'),('crawl.yahoo.net',5,'xx','xx','xx'),('entireweb.com',5,'xx','xx','xx'),('internetserviceteam.com',5,'xx','xx','xx'),('paginasamarillas.es',5,'xx','xx','xx'),('sac.overture.com',5,'xx','xx','xx'),('san2.attens.net',5,'xx','xx','xx'),('worio.com',5,'xx','xx','xx');";
				$statsDb->setQuery($query);
				$statsDb->query();
			}
			catch (Exception $e)
			{
				// Internally catch errors and only return a warning.
				return $return;
			}
		}

		if (!$statsDb->tableExists('domainclasses'))
		{
			try
			{
				$query = "CREATE TABLE IF NOT EXISTS `domainclasses` (
							`class` tinyint(4) NOT NULL DEFAULT '0',
							`name` varchar(64) NOT NULL DEFAULT '',
							PRIMARY KEY  (`class`),
							UNIQUE KEY (`class`,`name`)
							) ENGINE=MyISAM DEFAULT CHARSET=utf8;";
				$statsDb->setQuery($query);
				$statsDb->query();

				$query = "INSERT IGNORE INTO `domainclasses` VALUES (0,'Unknown'),(1,'Educational Institution'),(2,'Industrial/Corporate'),(3,'Governmental'),(4,'Internet Service Provider'),(5,'Search Engine'),(6,'Press/Media/Publication');";
				$statsDb->setQuery($query);
				$statsDb->query();
			}
			catch (Exception $e)
			{
				// Internally catch errors and only return a warning.
				return $return;
			}
		}

		if (!$statsDb->tableExists('summary_user'))
		{
			try
			{
				$query = "CREATE TABLE IF NOT EXISTS `summary_user` (
							`id` tinyint(4) NOT NULL DEFAULT '0',
							`label` varchar(255) NOT NULL DEFAULT '',
							`plot` int(1) DEFAULT '0',
							UNIQUE KEY (`label`)
							) ENGINE=MyISAM DEFAULT CHARSET=utf8;";
				$statsDb->setQuery($query);
				$statsDb->query();

				$query = "INSERT IGNORE INTO `summary_user` VALUES (1,'Total Users {1}',1),(6,' - Registered Users {2}',1),(7,' - Unregistered Interactive Users {3}',1),(8,' - Unregistered Download Users {4}',1),(3,' - Interactive Users {6}',1),(2,' - Simulation Users {5}',1),(4,' - Download Users {7}',1);";
				$statsDb->setQuery($query);
				$statsDb->query();
			}
			catch (Exception $e)
			{
				// Internally catch errors and only return a warning.
				return $return;
			}
		}

		if (!$statsDb->tableExists('summary_user_vals'))
		{
			try
			{
				$query = "CREATE TABLE IF NOT EXISTS `summary_user_vals` (
							`rowid` tinyint(4) NOT NULL DEFAULT '0',
							`colid` tinyint(4) NOT NULL DEFAULT '0',
							`datetime` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
							`period` tinyint(4) NOT NULL DEFAULT '1',
							`value` bigint(20) DEFAULT '0',
							`valfmt` tinyint(4) NOT NULL DEFAULT '0'
							) ENGINE=MyISAM DEFAULT CHARSET=utf8;";
				$statsDb->setQuery($query);
				$statsDb->query();
			}
			catch (Exception $e)
			{
				// Internally catch errors and only return a warning.
				return $return;
			}
		}

		if (!$statsDb->tableExists('summary_andmore'))
		{
			try
			{
				$query = "CREATE TABLE IF NOT EXISTS `summary_andmore` (
							`id` tinyint(4) NOT NULL DEFAULT '0',
							`label` varchar(255) NOT NULL DEFAULT '',
							`plot` int(1) DEFAULT '0',
							UNIQUE KEY (`label`)
							) ENGINE=MyISAM DEFAULT CHARSET=utf8;";
				$statsDb->setQuery($query);
				$statsDb->query();

				$query = "INSERT IGNORE INTO `summary_andmore` VALUES (1,'Nano 101',0),(2,'Nano 501',0),(3,'Research Seminars',0),(4,'Courses',0),(5,'Series',0),(6,'Workshops',0),(7,'Teaching Materials',0),(8,'Other non-interactive Resources',0),(9,'Breeze Presentations',0),(10,'PDF Files',0),(11,'Podcasts',0),(12,'Other Documents',0);";
				$statsDb->setQuery($query);
				$statsDb->query();
			}
			catch (Exception $e)
			{
				// Internally catch errors and only return a warning.
				return $return;
			}
		}

		if (!$statsDb->tableExists('summary_misc'))
		{
			try
			{
				$query = "CREATE TABLE IF NOT EXISTS `summary_misc` (
							`id` tinyint(4) NOT NULL DEFAULT '0',
							`label` varchar(255) NOT NULL DEFAULT '',
							`plot` int(1) DEFAULT '0',
							UNIQUE KEY (`label`)
							) ENGINE=MyISAM DEFAULT CHARSET=utf8;";
				$statsDb->setQuery($query);
				$statsDb->query();

				$query = "INSERT IGNORE INTO `summary_misc` VALUES (1,'Domains Served',0),(2,'Cummulative Interactive User Sessions {10}',0),(3,'Cummulative Session Time',0),(4,'Visitors {11}',0),(5,'Visits {12}',0),(6,'New Accounts',0),(7,'Max User Logins',0),(8,'Web Server Hits',1);";
				$statsDb->setQuery($query);
				$statsDb->query();
			}
			catch (Exception $e)
			{
				// Internally catch errors and only return a warning.
				return $return;
			}
		}

		if (!$statsDb->tableExists('summary_simusage'))
		{
			try
			{
				$query = "CREATE TABLE IF NOT EXISTS `summary_simusage` (
							`id` tinyint(4) NOT NULL DEFAULT '0',
							`label` varchar(255) NOT NULL DEFAULT '',
							`plot` int(1) DEFAULT '0',
							UNIQUE KEY (`label`)
							) ENGINE=MyISAM DEFAULT CHARSET=utf8;";
				$statsDb->setQuery($query);
				$statsDb->query();

				$query = "INSERT IGNORE INTO `summary_simusage` VALUES (1,'Simulation Users {5}',1),(2,'Simulation Runs',1),(3,'Total CPU Time',0),(4,'Total Wall Time',0),(5,'Total Interaction Time',0),(6,'Users with > 10 mins of CPU Time',0),(7,'Avg. Number of Simulation Runs/User',0),(8,'Avg. Time between First and Last Simulation',0),(9,'Repeat Users with > 10 Simulation Jobs',0),(10,'Repeat Users with > 3 Months {9}',0);";
				$statsDb->setQuery($query);
				$statsDb->query();
			}
			catch (Exception $e)
			{
				// Internally catch errors and only return a warning.
				return $return;
			}
		}

		if (!$statsDb->tableExists('continents'))
		{
			try
			{
				$query = "CREATE TABLE IF NOT EXISTS `continents` (
							`continentSHORT` char(2) NOT NULL DEFAULT '',
							`continentLONG` varchar(45) NOT NULL DEFAULT '',
							UNIQUE KEY (`continentSHORT`,`continentLONG`)
							) ENGINE=MyISAM DEFAULT CHARSET=utf8;";
				$statsDb->setQuery($query);
				$statsDb->query();

				$query = "INSERT IGNORE INTO `continents` VALUES ('AF','Africa'),('AN','Antartica'),('AS','Asia'),('EU','Europe'),('NA','North America'),('OC','Oceania'),('SA','South America');";
				$statsDb->setQuery($query);
				$statsDb->query();
			}
			catch (Exception $e)
			{
				// Internally catch errors and only return a warning.
				return $return;
			}
		}

		if (!$statsDb->tableExists('country_continent'))
		{
			try
			{
				$query = "CREATE TABLE IF NOT EXISTS `country_continent` (
							`country` char(2) NOT NULL DEFAULT '',
							`continent` char(2) NOT NULL DEFAULT '',
							PRIMARY KEY  (`country`,`continent`)
							) ENGINE=MyISAM DEFAULT CHARSET=utf8;";
				$statsDb->setQuery($query);
				$statsDb->query();

				$query = "INSERT INTO `country_continent` VALUES ('AD','EU'),('AE','AS'),('AF','AS'),('AG','NA'),('AI','NA'),('AL','EU'),('AM','EU'),('AN','NA'),('AO','AF'),('AQ','AN'),('AR','SA'),('AS','OC'),('AT','EU'),('AU','OC'),('AW','NA'),('AX','EU'),('AZ','EU'),('BA','EU'),('BB','NA'),('BD','AS'),('BE','EU'),('BF','AF'),('BG','EU'),('BH','AS'),('BI','AF'),('BJ','AF'),('BM','NA'),('BN','AS'),('BO','SA'),('BR','SA'),('BS','NA'),('BT','AS'),('BV','AN'),('BW','AF'),('BY','EU'),('BZ','NA'),('CA','NA'),('CC','AS'),('CD','AF'),('CF','AF'),('CG','AF'),('CH','EU'),('CI','AF'),('CK','OC'),('CL','SA'),('CM','AF'),('CN','AS'),('CO','SA'),('CR','NA'),('CU','NA'),('CV','AF'),('CX','AS'),('CY','EU'),('CZ','EU'),('DE','EU'),('DJ','AF'),('DK','EU'),('DM','NA'),('DO','NA'),('DZ','AF'),('EC','SA'),('EE','EU'),('EG','AF'),('EH','AF'),('ER','AF'),('ES','EU'),('ET','AF'),('FI','EU'),('FJ','OC'),('FK','SA'),('FM','OC'),('FO','EU'),('FR','EU'),('GA','AF'),('GD','NA'),('GE','EU'),('GF','SA'),('GG','EU'),('GH','AF'),('GI','EU'),('GL','NA'),('GM','AF'),('GN','AF'),('GP','NA'),('GQ','AF'),('GR','EU'),('GS','EU'),('GT','NA'),('GU','OC'),('GW','AF'),('GY','SA'),('HK','AS'),('HM','OC'),('HN','NA'),('HR','EU'),('HT','NA'),('HU','EU'),('ID','AS'),('IE','EU'),('IL','AS'),('IM','EU'),('IN','AS'),('IO','AS'),('IQ','AS'),('IR','AS'),('IS','EU'),('IT','EU'),('JE','EU'),('JM','NA'),('JO','AS'),('JP','AS'),('KE','AF'),('KG','AS'),('KH','AS'),('KI','OC'),('KM','AF'),('KN','NA'),('KP','AS'),('KR','AS'),('KW','AS'),('KY','NA'),('KZ','AS'),('LA','AS'),('LB','AS'),('LC','NA'),('LI','EU'),('LK','AS'),('LR','AF'),('LS','AF'),('LT','EU'),('LU','EU'),('LV','EU'),('LY','AF'),('MA','AF'),('MC','EU'),('MD','EU'),('ME','EU'),('MF','NA'),('MG','AF'),('MH','OC'),('MK','EU'),('ML','AF'),('MM','AS'),('MN','AS'),('MO','AS'),('MP','OC'),('MQ','NA'),('MR','AF'),('MS','NA'),('MT','EU'),('MU','AF'),('MV','AS'),('MW','AF'),('MX','NA'),('MY','AS'),('MZ','AF'),('NA','AF'),('NC','OC'),('NE','AF'),('NF','OC'),('NG','AF'),('NI','NA'),('NL','EU'),('NO','EU'),('NP','AS'),('NR','OC'),('NU','OC'),('NZ','OC'),('OM','AS'),('PA','NA'),('PE','SA'),('PF','OC'),('PG','OC'),('PH','AS'),('PK','AS'),('PL','EU'),('PM','NA'),('PN','OC'),('PR','NA'),('PS','AS'),('PT','EU'),('PW','OC'),('PY','SA'),('QA','AS'),('RE','AF'),('RO','EU'),('RS','EU'),('RU','EU'),('RW','AF'),('SA','AS'),('SB','OC'),('SC','AF'),('SD','AF'),('SE','EU'),('SG','AS'),('SH','AF'),('SI','EU'),('SJ','EU'),('SK','EU'),('SL','AF'),('SM','EU'),('SN','AF'),('SO','AF'),('SR','SA'),('ST','AF'),('SV','NA'),('SY','EU'),('SZ','AF'),('TC','NA'),('TD','AF'),('TF','AN'),('TG','AF'),('TH','AS'),('TJ','AS'),('TK','OC'),('TL','AS'),('TM','AS'),('TN','AF'),('TO','OC'),('TR','EU'),('TT','NA'),('TV','OC'),('TW','AS'),('TZ','AF'),('UA','EU'),('UG','AF'),('UK','EU'),('UM','NA'),('US','NA'),('UY','SA'),('UZ','AS'),('VA','EU'),('VC','NA'),('VE','SA'),('VG','EU'),('VI','NA'),('VN','AS'),('VU','OC'),('WF','OC'),('WS','OC'),('YE','AS'),('YT','AF'),('ZA','AF'),('ZM','AF'),('ZW','AF');";
				$statsDb->setQuery($query);
				$statsDb->query();
			}
			catch (Exception $e)
			{
				// Internally catch errors and only return a warning.
				return $return;
			}
		}

		if (!$statsDb->tableExists('exclude_list'))
		{
			try
			{
				$query = "CREATE TABLE IF NOT EXISTS `exclude_list` (
							`filter` varchar(65) NOT NULL,
							`type` varchar(65) NOT NULL DEFAULT 'domain',
							`notes` varchar(120),
							UNIQUE KEY filter_type (`filter`,`type`)
							) ENGINE=MyISAM DEFAULT CHARSET=utf8;";
				$statsDb->setQuery($query);
				$statsDb->query();

				$query = "INSERT IGNORE INTO `exclude_list` VALUES ('googlebot.com','domain','bot'),('crawl.yahoo.net','domain','bot'),('search.msn.com','domain','bot'),('inktomisearch.com','domain','bot'),('msnbot.msn.com','domain','bot'),('%googlebot.com','host','bot'),('%crawl.yahoo.net','host','bot'),('%search.msn.com','host','bot'),('%inktomisearch.com','host','bot'),('%msnbot.msn.com','host','bot'),('rtmp1.hubzero.org','host','bot'),('spider%.yandex.ru','host','bot'),('crawl%.cuill.com','host','bot'),('crawler%.ask.com','host','bot'),('crawl%.dotnetdotcom.org','host','bot'),('%robot.spinn3r.com','host','bot'),('livebot-%.search.live.com','host','bot'),('arch%.miss.archive.org','host','bot'),('crawl%.exabot.com','host','bot'),('crawler.bloglines.com','host','bot'),('msnbot-%.msn.com','host','bot'),('crawler%.fastsearch.net','host','bot'),('%.robot.spinn3r.com','host','bot'),('spider%.picsearch.com','host','bot'),('spider%.logika.net','host','bot'),('spider%.mail.ru','host','bot'),('spider%.proxy.aol.com','host','bot'),('spider.chem.uw.edu.pl','host','bot'),('crawler4.irl.cs.tamu.edu','host','bot'),('%.crawl.baidu.com','host','bot'),('crawl%.searchme.com','host','bot'),('speedyspider.entireweb.com','host','bot'),('robot.acoon.de','host','bot'),('crawler%.gingersoftware.com','host','bot'),('crawler%.aurarianetworks.com','host','bot'),('crawler.flatlandindustries.com','host','bot'),('robot%.feeds.yandex.net','host','bot'),('ng20.exabot.com','host','bot'),('crawler.kalooga.com','host','bot'),('%crawler%.cs.tamu.edu','host','bot'),('turbospider.yandex.ru','host','bot'),('crawlers.looksmart.com','host','bot'),('red-gw2.exabot.com','host','bot'),('robot%.rambler.ru','host','bot'),('%crawler%.ig.ntnu.no','host','bot'),('crawl%.us.archive.org','host','bot'),('%dnaspider%.mia.lycos.com','host','bot'),('%crawler%.x-echo.com','host','bot'),('robot.szukacz.pl','host','bot'),('spider.interseek.com','host','bot'),('spider%.szukaj.onet.pl','host','bot'),('%spider.entireweb.com','host','bot'),('137.187.22.','ip','NIH Crawler'),('128.231.86.','ip','NIH Crawler'),('128.231.88.','ip','NIH Crawler'),('128.46.16.17','ip','rtmp1.hubzero.org'),('128.210.7.18','ip','Purdue Google Appliance'),('128.46.16.59','ip','Pascal Meunier\'s security crawler'),('zeus.nj.nec.com','domain','bot'),('yandex.ru','domain','bot'),('yandex.net','domain','bot'),('yahoo.net','domain','bot'),('yahoo.com','domain','bot'),('xs4.kso.co.uk','domain','bot'),('worio.com','domain','bot'),('whizbang.com','domain','bot'),('websquash.com','domain','bot'),('websmostlinked.com','domain','bot'),('webclipping.com','domain','bot'),('webbot.org','domain','bot'),('turnitin.com','domain','bot'),('tracerlock.com','domain','bot'),('tpiol.tpiol.com','domain','bot'),('tpiol.com','domain','bot'),('teoma.com','domain','bot'),('searchme.com','domain','bot'),('san2.attens.net','domain','bot'),('sac.overture.com','domain','bot'),('robotiker.es','domain','bot'),('rfcrawler.com','domain','bot'),('rcac.purdue.edu','domain','bot'),('punch.purdue.edu','domain','bot'),('picsearch.com','domain','bot'),('phx.gbl','domain','bot'),('panchma.tivra.com','domain','bot'),('paginasamarillas.es','domain','bot'),('overture.com','domain','bot'),('morgue1.corp.yahoo.com','domain','bot'),('metacarta.com','domain','bot'),('markwatch.com','domain','bot'),('looksmart.net','domain','bot'),('looksmart.com','domain','bot'),('live.com','domain','bot'),('live-servers.net','domain','bot'),('linuxhardcore.com','domain','bot'),('jeteye.com','domain','bot'),('internetserviceteam.com','domain','bot'),('inktomi.com','domain','bot'),('idle.eidetica.com','domain','bot'),('hanta.yahoo.com','domain','bot'),('girafa.com','domain','bot'),('gigablast.com','domain','bot'),('fastsearch.net','domain','bot'),('exabot.com','domain','bot'),('ev1servers.net','domain','bot'),('entireweb.com','domain','bot'),('cuill.com','domain','bot'),('crawler918.com','domain','bot'),('crawl8-public.alexa.com','domain','bot'),('cosmixcorp.com','domain','bot'),('brain.grub.org','domain','bot'),('bloglines.com','domain','bot'),('betaspider.com','domain','bot'),('become.com','domain','bot'),('authoritativeweb.com','domain','bot'),('attens.net','domain','bot'),('ask.com','domain','bot'),('archive.org','domain','bot'),('67.108.223.130.ptr.us.xo.net','domain','bot'),('67.106.152.131.ptr.us.xo.net','domain','bot'),('66.237.109.194.ptr.us.xo.net','domain','bot'),('task=diskusage','url','Middleware Disk Quota Checker'),('gsa-purdue-crawler','useragent','Purdue GSA Crawler');";
				$statsDb->setQuery($query);
				$statsDb->query();
			}
			catch (Exception $e)
			{
				// Internally catch errors and only return a warning.
				return $return;
			}
		}

		if (!$statsDb->tableExists('bot_useragents'))
		{
			try
			{
				$query = "CREATE TABLE IF NOT EXISTS `bot_useragents` (
							`useragent` tinytext NOT NULL,
							PRIMARY KEY  (`useragent`(255))
							) ENGINE=MyISAM DEFAULT CHARSET=utf8;";
				$statsDb->setQuery($query);
				$statsDb->query();
			}
			catch (Exception $e)
			{
				// Internally catch errors and only return a warning.
				return $return;
			}
		}
	}
}