<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright © 2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Massmail\Helpers;

/**
 * Signs and verifies per-recipient unsubscribe links. The token is a keyed
 * HMAC of "mailingId:email" so a link can only unsubscribe the address it was
 * issued for, and no state needs to be stored to validate it.
 */
class Token
{
    /**
     * Site secret used as the HMAC key.
     *
     * @return  string
     */
    protected static function secret()
    {
        // The site secret is always configured; the static salt keeps the key
        // from ever degrading to a guessable value in a misconfigured install.
        return (string) \Config::get('secret') . '::com_massmail::unsubscribe';
    }

    /**
     * Compute the token for a mailing/email pair.
     *
     * @param   integer  $mailingId
     * @param   string   $email
     * @return  string
     */
    public static function make($mailingId, $email)
    {
        return hash_hmac('sha256', (int) $mailingId . ':' . strtolower(trim($email)), self::secret());
    }

    /**
     * Constant-time verify.
     *
     * @param   integer  $mailingId
     * @param   string   $email
     * @param   string   $token
     * @return  boolean
     */
    public static function verify($mailingId, $email, $token)
    {
        return hash_equals(self::make($mailingId, $email), (string) $token);
    }

    /**
     * Build the absolute unsubscribe URL for a recipient.
     *
     * @param   integer  $mailingId
     * @param   string   $email
     * @return  string
     */
    public static function unsubscribeUrl($mailingId, $email)
    {
        $query = http_build_query(array(
            'option' => 'com_massmail',
            'task'   => 'unsubscribe',
            'e'      => $email,
            'm'      => (int) $mailingId,
            't'      => self::make($mailingId, $email)
        ));

        return self::siteBase() . '/index.php?' . $query;
    }

    /**
     * Absolute site base URL (scheme + host, no trailing slash).
     *
     * Delivery usually runs under cron with no request to derive a host from,
     * so the link has to come from configuration: the component's site_url
     * option, then live_site. Request::root() answers "http://:/" under CLI,
     * so it is only used when it names a real host.
     *
     * @return  string
     */
    public static function siteBase()
    {
        $base = rtrim((string) \Component::params('com_massmail')->get('site_url', ''), '/');

        if ($base === '') {
            $base = rtrim((string) \Config::get('live_site'), '/');
        }

        if ($base === '') {
            $root = rtrim((string) \Request::root(), '/');
            $base = parse_url($root, PHP_URL_HOST) ? $root : '';
        }

        return $base;
    }
}
