<?php

// @codeCoverageIgnoreStart
if (!class_exists('JConfig', false)) {
    eval(<<<'JCONFIG'
class JConfig
{
    public $access = '1';
    public $api_server = '1';
    public $application_env = 'production';
    public $captcha = '0';
    public $debug = '0';
    public $debug_lang = '0';
    public $editor = 'ckeditor';
    public $error_reporting = 'relaxed';
    public $feed_email = 'author';
    public $feed_limit = '10';
    public $force_ssl = '1';
    public $gzip = '0';
    public $helpurl = 'English (GB) - HUBzero help';
    public $list_limit = '20';
    public $live_site = '';
    public $log_path = '/var/www/hub/logs';
    public $log_post_data = '0';
    public $offset = 'America/Indiana/Indianapolis';
    public $profile = '0';
    public $robots = '';
    public $secret = '';
    public $sitecode = 'hz';
    public $sitename = 'hubzero.org';
    public $tmp_path = '/var/www/hub/tmp';
    public $xmlrpc_server = '0';
    public $cache_handler = 'file';
    public $cachetime = '120';
    public $caching = '0';
    public $memcache_settings = '';
    public $db = 'hub';
    public $dbcharset = '';
    public $dbcollation = '';
    public $dbprefix = 'jos_';
    public $dbtype = 'pdo';
    public $host = 'localhost';
    public $password = 'drowssap';
    public $user = 'hubadmin';
    public $ftp_enable = '';
    public $ftp_host = '';
    public $ftp_pass = '';
    public $ftp_port = '0';
    public $ftp_root = '';
    public $ftp_user = '';
    public $fromname = 'HUBzero';
    public $mailer = 'mail';
    public $mailfrom = 'support@hubzero.org';
    public $sendmail = '/usr/sbin/sendmail';
    public $smtpauth = '0';
    public $smtphost = 'localhost';
    public $smtppass = '';
    public $smtpport = '25';
    public $smtpsecure = 'none';
    public $smtpuser = '';
    public $MetaAuthor = '0';
    public $MetaDesc = 'a hub for doing things';
    public $MetaKeys = 'hub, hubzero';
    public $MetaRights = '';
    public $MetaTitle = '0';
    public $MetaVersion = '0';
    public $display_offline_message = '1';
    public $offline = '0';
    public $offline_image = '';
    public $offline_message = 'This site is currently offline.';
    public $long = array('period' => '1440', 'limit' => '10000');
    public $short = array('period' => '1', 'limit' => '120');
    public $redis_password = 'warglebargle';
    public $sef = '1';
    public $sef_groups = '0';
    public $sef_rewrite = '1';
    public $sef_suffix = '0';
    public $sitename_pagetitles = '0';
    public $unicodeslugs = '0';
    public $cookie_domain = '';
    public $cookie_path = '';
    public $cookiesubdomains = '0';
    public $lifetime = '120';
    public $session_handler = 'database';
    public $solr_client_id = '12b910947122dfab5238b9e728774486';
    public $solr_client_secret = '6e291d7c6a9c8859104dd04332f5f07cbb30d6c0';
    public $solr_host = 'localhost';
    public $solr_password = 'drowssaprlos';
    public $solr_port = '2093';
    public $solr_username = 'hubzerosolrworker';
}
JCONFIG);
}
// @codeCoverageIgnoreEnd
