<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2026 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Saml\Helpers;

/**
 * Parser for SP EntityDescriptor metadata (admin import)
 *
 * Turns a pasted or fetched SAML metadata document into the field
 * values of a ServiceProvider row.
 */
class Metadata
{
    /**
     * SAML metadata XML namespace
     */
    const NS_MD = 'urn:oasis:names:tc:SAML:2.0:metadata';

    /**
     * XML digital signature namespace
     */
    const NS_DS = 'http://www.w3.org/2000/09/xmldsig#';

    /**
     * SAML binding URNs (kept local so the helper needs no LightSAML classes)
     */
    const BINDING_HTTP_POST     = 'urn:oasis:names:tc:SAML:2.0:bindings:HTTP-POST';
    const BINDING_HTTP_REDIRECT = 'urn:oasis:names:tc:SAML:2.0:bindings:HTTP-Redirect';

    /**
     * Largest metadata document we will read
     */
    const MAX_BYTES = 2097152;

    /**
     * NameID formats we can issue, mapped to the value source that produces
     * them. Formats absent here (transient, kerberos, X.509 subject names…)
     * are ignored on import and left for an admin to decide.
     *
     * @var  array
     */
    protected static $nameIdSources = [
        'urn:oasis:names:tc:SAML:1.1:nameid-format:emailAddress' => 'email',
        'urn:oasis:names:tc:SAML:2.0:nameid-format:persistent'   => 'id',
        'urn:oasis:names:tc:SAML:1.1:nameid-format:unspecified'  => 'username'
    ];

    /**
     * Fetch a metadata document over http(s)
     *
     * @param   string  $url
     * @return  string|false
     */
    public static function fetch($url)
    {
        // https only: the document carries the ACS URL and signing
        // certificate we go on to trust, and nothing verifies a signature
        // over it, so the transport is what protects it
        if (!preg_match('/^https:\/\//i', $url)) {
            return false;
        }

        // A host with only public addresses, IPv4 and IPv6, fetched from those
        // addresses, so it cannot reach the hub's own network; see
        // \Hubzero\Utility\PublicUrl
        $target = \Hubzero\Utility\PublicUrl::check($url, ['https']);

        if (!$target) {
            return false;
        }

        $ch = curl_init();

        \Hubzero\Utility\PublicUrl::apply($ch, $target);

        // Redirects are not followed: each hop would need its own address
        // check, and this endpoint must not become a way to probe the
        // hub's internal network.
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, false);
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
        curl_setopt($ch, CURLOPT_PROTOCOLS, CURLPROTO_HTTPS);

        // Enforced while reading, not from the advertised Content-Length: a
        // chunked response would otherwise buffer without limit
        $body = '';
        $limit = self::MAX_BYTES;

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, false);
        curl_setopt($ch, CURLOPT_WRITEFUNCTION, function ($ch, $chunk) use (&$body, $limit) {
            $body .= $chunk;

            // Returning short of the chunk length aborts the transfer
            return strlen($body) > $limit ? 0 : strlen($chunk);
        });

        $ok   = curl_exec($ch);
        $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $from = \Hubzero\Utility\PublicUrl::fetchedFromChecked($ch, $target);

        curl_close($ch);

        if (!$ok || !$from || $code >= 300 || $body === '' || strlen($body) > self::MAX_BYTES) {
            return false;
        }

        return $body;
    }

    /**
     * Parse an SP EntityDescriptor into ServiceProvider field values
     *
     * @param   string  $xml
     * @return  array|false  array with entity_id, acs_url, slo_url,
     *                       signing_cert, want_requests_signed — or false
     */
    public static function parse($xml)
    {
        $dom = new \DOMDocument();

        // LIBXML_NONET blocks external fetches (XXE) while parsing
        if (!@$dom->loadXML($xml, LIBXML_NONET | LIBXML_NOERROR | LIBXML_NOWARNING)) {
            return false;
        }

        $xpath = new \DOMXPath($dom);
        $xpath->registerNamespace('md', self::NS_MD);
        $xpath->registerNamespace('ds', self::NS_DS);

        $entity = $xpath->query('//md:EntityDescriptor')->item(0);

        if (!$entity || !$entity->getAttribute('entityID')) {
            return false;
        }

        $descriptor = $xpath->query('.//md:SPSSODescriptor', $entity)->item(0);

        if (!$descriptor) {
            return false;
        }

        // Only keys actually found are returned, so importing over an existing
        // registration never blanks a value the descriptor is simply silent
        // about (an SP may publish SSO-only metadata and still have a
        // registered SLO endpoint or certificate)
        $fields = [
            'entity_id' => $entity->getAttribute('entityID')
        ];

        $acsUrl = '';

        // AuthnRequestsSigned is only reported when the descriptor states it.
        // Leaving the key out when it is absent means re-importing metadata
        // cannot silently turn off signature verification for an SP that
        // already requires it; new SPs fall back to the column default (on).
        if ($descriptor->hasAttribute('AuthnRequestsSigned')) {
            $signed = strtolower($descriptor->getAttribute('AuthnRequestsSigned'));

            $fields['want_requests_signed'] = in_array($signed, ['true', '1']) ? 1 : 0;
        }

        // Same rule for WantAssertionsSigned, which decides whether we add an
        // assertion-level signature on top of the response signature
        if ($descriptor->hasAttribute('WantAssertionsSigned')) {
            $wants = strtolower($descriptor->getAttribute('WantAssertionsSigned'));

            $fields['sign_assertion'] = in_array($wants, ['true', '1']) ? 1 : 0;
        }

        // NameID format: take the first one we can actually issue. An SP that
        // publishes only emailAddress will reject (or mis-key) a username, so
        // the matching source is selected too rather than left at the default.
        foreach ($xpath->query('.//md:NameIDFormat', $descriptor) as $format) {
            $value = trim($format->nodeValue);

            if (!isset(self::$nameIdSources[$value])) {
                continue;
            }

            $fields['nameid_format'] = $value;
            $fields['nameid_source'] = self::$nameIdSources[$value];
            break;
        }

        // ACS: only the HTTP-POST binding, which is the one we deliver over.
        // An SP publishing no POST endpoint cannot be served, so the import
        // fails rather than registering an address we would never reach.
        $best = null;
        foreach ($xpath->query('.//md:AssertionConsumerService', $descriptor) as $acs) {
            if ($acs->getAttribute('Binding') != self::BINDING_HTTP_POST) {
                continue;
            }

            $rank = [
                strtolower($acs->getAttribute('isDefault')) == 'true' ? 0 : 1,
                (int) $acs->getAttribute('index')
            ];

            if ($best === null || $rank < $best) {
                $best = $rank;
                $acsUrl = $acs->getAttribute('Location');
            }
        }

        if (!$acsUrl) {
            return false;
        }

        $fields['acs_url'] = $acsUrl;

        // SLO: only the HTTP-Redirect binding, which is the one we respond
        // over. Storing a SOAP or POST endpoint here would look configured
        // while every logout response silently went nowhere.
        foreach ($xpath->query('.//md:SingleLogoutService', $descriptor) as $slo) {
            if ($slo->getAttribute('Binding') == self::BINDING_HTTP_REDIRECT) {
                $fields['slo_url'] = $slo->getAttribute('Location');
                break;
            }
        }

        // Signing certificate: use="signing" wins, use-less descriptors apply to both
        foreach ($xpath->query('.//md:KeyDescriptor', $descriptor) as $key) {
            $use = $key->getAttribute('use');

            if ($use && $use != 'signing') {
                continue;
            }

            $cert = $xpath->query('.//ds:X509Certificate', $key)->item(0);

            if ($cert) {
                $fields['signing_cert'] = preg_replace('/\s+/', '', $cert->nodeValue);

                if ($use == 'signing') {
                    break;
                }
            }
        }

        return $fields;
    }
}
