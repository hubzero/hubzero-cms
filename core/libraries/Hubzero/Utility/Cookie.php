<?php

/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Utility;

/**
 * Cookie utility class
 *
 * Set and retrieve cookies in consistent manner
 */
class Cookie
{
    /**
     * Drop a cookie
     *
     * @param  (string) $namespace - make sure the cookie name is unique
     * @param  (time)   $lifetime  - how long the cookie should last
     * @param  (array)  $data      - data to be saved in cookie
     * @return void
     **/
    public static function bake($namespace, $lifetime, $data = array())
    {
        $hash   = \Hubzero\Facades\App::hash(\Hubzero\Facades\App::get('client')->name . ':' . $namespace);

        $key = \Hubzero\Facades\App::hash('');
        $crypt = new \Hubzero\Encryption\Encrypter(
            new \Hubzero\Encryption\Cipher\Simple(),
            new \Hubzero\Encryption\Key('simple', $key, $key)
        );
        $cookie = $crypt->encrypt(serialize($data));

        // Determine whether cookie should be 'secure' or not
        $secure   = false;
        $forceSsl = \Hubzero\Facades\Config::get('force_ssl', false);

        if (\Hubzero\Facades\App::isAdmin() && $forceSsl >= 1) {
            $secure = true;
        } elseif (\Hubzero\Facades\App::isSite() && $forceSsl == 2) {
            $secure = true;
        }

        // Set the actual cookie
        setcookie($hash, $cookie, $lifetime, '/', '', $secure, true);
    }

    /**
     * Retrieve a cookie
     *
     * @param  (string) $namespace - make sure the cookie name is unique
     * @return (object) $cookie data
     **/
    public static function eat($namespace)
    {
        $hash  = \Hubzero\Facades\App::hash(\Hubzero\Facades\App::get('client')->name . ':' . $namespace);

        $key = \Hubzero\Facades\App::hash('');
        $crypt = new \Hubzero\Encryption\Encrypter(
            new \Hubzero\Encryption\Cipher\Simple(),
            new \Hubzero\Encryption\Key('simple', $key, $key)
        );

        if ($str = \Hubzero\Facades\App::get('request')->getString($hash, '', 'cookie')) {
            $sstr   = $crypt->decrypt($str);
            $cookie = @unserialize($sstr);

            return (object)$cookie;
        }

        return false;
    }
}
