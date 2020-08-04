<?php
/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Mail;

use RuntimeException;
use HubmailConfig;

/**
 * Hubzero library class for creating a unique token to
 * include in emails
 */
class Token
{
	/**
	 * Description for 'mailTokenTicket'
	 */
	const EMAIL_TOKEN_TICKET = 1;

	/**
	 * Description for 'mailTokenGroupThread'
	 */
	const EMAIL_TOKEN_GROUP_THREAD = 2;

	/**
	 * Description for '_currentVersion'
	 *
	 * @var  string
	 */
	private $_currentVersion;

	/**
	 * Description for '_iv'
	 *
	 * @var  unknown
	 */
	private $_iv;

	/**
	 * Description for '_key'
	 *
	 * @var  unknown
	 */
	private $_key;

	/**
	 * Description for '_blocksize'
	 *
	 * @var  number
	 */
	private $_blocksize;

	/**
	 * Read encryption configuration from config file
	 *
	 * @return  void
	 */
	public function __construct()
	{
		// HubmailConfig is defined here
		$file = '/etc/hubmail_gw.conf';

		if (!file_exists($file))
		{
			throw new RuntimeException(sprintf('File "%s" does not exist', $file));
		}

		include_once $file;

		if (!class_exists('HubmailConfig'))
		{
			throw new RuntimeException('Class HubmailConfig not loaded');
		}

		$HubmailConfig1 = new HubmailConfig();

		// Get current token version
		$this->_currentVersion = $HubmailConfig1->email_token_current_version;

		if (empty($this->_currentVersion))
		{
			throw new RuntimeException('Class HubmailConfig->email_token_current_version not found in config file');
		}

		// Grab the encryption info for that version
		$prop = 'email_token_encryption_info_v' . $this->_currentVersion;
		$encryption_info = $HubmailConfig1->$prop;

		if (empty($encryption_info))
		{
			throw new RuntimeException('Class HubmailConfig->email_token_encryption_info_vX not found for version: ' . $this->_currentVersion);
		}

		// Encryption info is comma delimited (key, iv) in this configuraiton value
		$keyArray = explode(',', $encryption_info);

		if (count($keyArray) <> 2)
		{
			throw new RuntimeException(__CLASS__ . '::__construct(); config.email_token_encryption_info_v' . $tokenVersion . ' cannot be split');
		}

		$this->_key = $keyArray[0];
		$this->_iv  = $keyArray[1];
		$this->_blocksize = 8; // in bytes
	}

	/**
	 * Build a unique email token
	 *
	 * @param   int     $version
	 * @param   int     $action
	 * @param   int     $userid
	 * @param   int     $id
	 * @return  string  Base 16 string representing token
	 */
	public function buildEmailToken($version, $action, $userid, $id)
	{
		$rv = '';

		$binaryString = pack("NNN", $userid, $id, intval(time()));

		// Hash the unencrypted version hex version of the binary string
		// Include the unencrypted version and action bytes as well
		$hash = sha1(bin2hex(pack("C", $version)) . bin2hex(pack("C", $action)) .  bin2hex($binaryString));

		// We're only using a portion of the hash as a checksum
		$hashsub = substr($hash, 0, 4);

		// Append hash to end of binary string, two hex digits stuffed into a single unsigned byte
		$binaryString .= pack("n", hexdec($hashsub));

		// Add PKCS7 style padding before encryption
		$pad = $this->_blocksize - (strlen($binaryString) % $this->_blocksize);
		$binaryString .= str_repeat(chr($pad), $pad);

		// Do the encryption
		$cipher = mcrypt_module_open(MCRYPT_RIJNDAEL_128, '', 'cbc', '');
		mcrypt_generic_init($cipher, $this->_key, $this->_iv);
		$encrypted = mcrypt_generic($cipher, $binaryString);
		mcrypt_generic_deinit($cipher);

		// Prepend an unencrypted version byte and action byte (in base16)
		$rv = bin2hex(pack("C", $version)) . bin2hex(pack("C", $action)) .  bin2hex($encrypted);

		return $rv;
	}

	/**
	 * Function to decrypt email token
	 * 
	 * @param   string  $t  Email token
	 * @return  array   Email token details
	 */
	public function decryptEmailToken($t)
	{
		// returns 3 element array, depending on the context, userid will be first,
		// followed by another id (groupid, ticketid, etc) and a timestamp indicating
		// the age of the token if you want to consider expiring it after a certain age

		// strip the unencrypted version and action bytes at the beginning of the token
		$rawtoken = substr($t, 4);

		// Convert from hex to bin
		$encrypted = hex2bin($rawtoken);

		// Do the decryption
		$cipher = mcrypt_module_open(MCRYPT_RIJNDAEL_128, '', 'cbc', '');
		mcrypt_generic_init($cipher, $this->_key, $this->_iv);
		$decrypted = mdecrypt_generic($cipher, $encrypted);

		// unpack the original values, no need to strip padding or hash 
		// we'll just unpack what we need
		$arr = unpack("N3", $decrypted);
		return array($arr[1], $arr[2], $arr[3]);
	}
}
