<?php
/**
 * @package     hubzero-cms
 * @author      David Benham <dbenham@purdue.edu>
 * @copyright   Copyright 2005-2011 Purdue University. All rights reserved.
 * @license     http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 *
 * Copyright 2005-2011 Purdue University. All rights reserved.
 *
 * This file is part of: The HUBzero(R) Platform for Scientific Collaboration
 *
 * The HUBzero(R) Platform for Scientific Collaboration (HUBzero) is free
 * software: you can redistribute it and/or modify it under the terms of
 * the GNU Lesser General Public License as published by the Free Software
 * Foundation, either version 3 of the License, or (at your option) any
 * later version.
 *
 * HUBzero is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * HUBzero is a registered trademark of Purdue University.
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );

class Hubzero_Email_Token  
{
    const emailTokenTicket = 1;
    const emailTokenGroupThread = 2;

    private $_currentVersion;
    private $_iv;
    private $_key;
    private $_blocksize;

    /**
     * Read encryption configuration from config file
     */
    public function __construct() 
	{
		$config = JFactory::getConfig();
		if(empty($config))
			throw new Exception("Class Hubzero_Email_Token: failed JFactory::getConfig() call");

		//**** Get current token version
		$this->_currentVersion = $config->getValue('config.email_token_current_version', '');

		if(empty($this->_currentVersion))
			throw new Exception("Class Hubzero_Email_Token: config.email_token_current_version not found in config file");

		//**** Grab the encryption info for that version
		$encryption_info = $config->getValue('config.email_token_encryption_info_v' . $this->_currentVersion, '');

		if(empty($encryption_info))
			throw new Exception("Class Hubzero_Email_Token: config.email_token_encryption_info_vX not found for version: " . $tokenVersion);

		//**** Encryption info is comma delimited (key, iv) in this configuraiton value
		$keyArray = explode(",", $encryption_info);

		if(count($keyArray) <> 2)
			throw new Exception("Class Hubzero_Email_Token: config.email_token_encryption_info_v" . $tokenVersion . " cannot be split" );

		$this->_key = $keyArray[0];
		$this->_iv = $keyArray[1];
		$this->_blocksize = 8; // in bytes

	}

    /**
     *
     * @param int $version
     * @param int $action
     * @param int $userid
     * @param int $id
     * @return string - base 16 string representing token
     */
    public function buildEmailToken($version, $action, $userid, $id)
	{
        $rv = '';

		$binaryString = pack("NNN", $userid, $id, intval(time()));

		//**** Add PKCS7 style padding before encryption
		$pad = $this->_blocksize - (strlen($binaryString) % $this->_blocksize);

        $binaryString .= str_repeat(chr($pad), $pad);

		//**** Do the encryption
		$cipher = mcrypt_module_open(MCRYPT_RIJNDAEL_128,'','cbc','');
		mcrypt_generic_init($cipher, $this->_key, $this->_iv);
		$encrypted = mcrypt_generic($cipher, $binaryString);
		mcrypt_generic_deinit($cipher);

		//**** Prepend an unencrypted version byte and action byte (in base16) 
		$rv = bin2hex(pack("C", $version)) . bin2hex(pack("C", $action)) .  bin2hex($encrypted);

		// Put delimiters on here, so nobody else needs to worry
		return "@hts@" . $rv . "@hte@";
 	}

}

