<?php

/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Geocode;

use Hubzero\Facades\Component;
use Hubzero\Facades\Event;

/**
 * Helper class for getting geolocation information
 */
class Geocode
{
    /**
     * Get a list of countries and their country code
     *
     * @var  array
     */
    public static $countries = array();

    /**
     * Get a list of countries and their coutnry code
     *
     * @param   string  $continent  Continent to filter by
     * @return  array
     */
    public static function countries($continent = 'all')
    {
        if (isset(self::$countries[$continent])) {
            return self::$countries[$continent];
        }

        $adapter = new \Http\Discovery\Psr18Client();

        $p = array();

        // Get a list of providers
        //
        // Each provider has an associated plugin. If the provider supports
        // the desired data look-up, it (the provider) will be returned by
        // the plugin. Otherwise, the plugin returns nothing.
        if ($providers = Event::trigger('geocode.onGeocodeProvider', array('geocode.countries', $adapter))) {
            foreach ($providers as $provider) {
                if ($provider) {
                    $p[] = $provider;
                }
            }
        }

        if (!count($p)) {
            return self::$countries;
        }

        // Instantiate the Geocoder service and pass it the list of providers
        $geocoder = new \Geocoder\ProviderAggregator();

        $chain = new \Geocoder\Provider\Chain\Chain($p);

        $geocoder->registerProvider($chain);

        // Try to get some data...

        $countries = array();

        if ($data = $geocoder->geocode($continent)) {
            foreach ($data as $item) {
                $country = new \stdClass();
                $country->code      = $item->getCountry()->getCode();
                $country->name      = $item->getCountry()->getName();
                $country->continent = $item->getLocality();

                $countries[] = $country;
            }
        }

        self::$countries[$continent] = $countries;

        return self::$countries[$continent];
    }

    /**
     * Get country based on short code
     *
     * @param   string  $code  Short code (ex: us, de, fr, jp)
     * @return  string
     */
    public static function country($code)
    {
        $adapter = new \Http\Discovery\Psr18Client();

        $p = array();

        // Get a list of providers
        if ($providers = Event::trigger('geocode.onGeocodeProvider', array('geocode.country', $adapter))) {
            foreach ($providers as $provider) {
                if ($provider) {
                    $p[] = $provider;
                }
            }
        }

        if (!count($p)) {
            return '';
        }

        // Instantiate the Geocoder service and pass it the list of providers
        $geocoder = new \Geocoder\ProviderAggregator();

        $chain = new \Geocoder\Provider\Chain\Chain($p);

        $geocoder->registerProvider($chain);

        $country = $code;

        if ($data = $geocoder->geocode($code)) {
            $country = $data->first()->getCountry()->getName();
        }

        return $country;
    }

    /**
     * Get continent based on country name or short code
     *
     * @param   string  $country  Country name or Short code (ex: us, de, fr, jp)
     * @return  string
     */
    public static function continent($country)
    {
        $adapter = new \Http\Discovery\Psr18Client();

        $p = array();

        // Get a list of providers
        if ($providers = Event::trigger('geocode.onGeocodeProvider', array('geocode.continent', $adapter))) {
            foreach ($providers as $provider) {
                if ($provider) {
                    $p[] = $provider;
                }
            }
        }

        if (!count($p)) {
            return '';
        }

        // Instantiate the Geocoder service and pass it the list of providers
        $geocoder = new \Geocoder\ProviderAggregator();

        $chain = new \Geocoder\Provider\Chain\Chain($p);

        $geocoder->registerProvider($chain);

        $continent = null;

        if ($data = $geocoder->geocode($country)) {
            $continent = $data->first()->getLocality();
        }

        return $continent;
    }

    /**
     * Geo-locate an address
     *
     * @param   string  $address
     * @return  array
     */
    public static function locate($address)
    {
        $ip = false;
        if (filter_var($address, FILTER_VALIDATE_IP)) {
            $ip = true;
        }

        $adapter = new \Http\Discovery\Psr18Client();

        $p = array();

        // Get a list of providers
        if ($providers = Event::trigger('geocode.onGeocodeProvider', array('geocode.locate', $adapter, $ip))) {
            foreach ($providers as $provider) {
                if ($provider) {
                    $p[] = $provider;
                }
            }
        }

        if (!count($p)) {
            return '';
        }

        // Instantiate the Geocoder service and pass it the list of providers
        $geocoder = new \Geocoder\ProviderAggregator();

        $chain = new \Geocoder\Provider\Chain\Chain($p);

        $geocoder->registerProvider($chain);

        // Try to get some data...
        $data = $geocoder->geocode($address);

        return $data;
    }

    /**
     * Get the address (reverse locate)
     *
     * @param   array  $coordinates  array(latitude, longitude)
     * @return  array
     */
    public static function address($coordinates)
    {
        $adapter = new \Http\Discovery\Psr18Client();
        $p = array();

        // Get a list of providers
        if ($providers = Event::trigger('geocode.onGeocodeProvider', array('geocode.address', $adapter))) {
            foreach ($providers as $provider) {
                if ($provider) {
                    $p[] = $provider;
                }
            }
        }

        if (!count($p)) {
            return '';
        }

        $latitude =  isset($coordinates['latitude'])  ? $coordinates['latitude']  : $coordinates[0];
        $longitude = isset($coordinates['longitude']) ? $coordinates['longitude'] : $coordinates[1];

        // Instantiate the Geocoder service and pass it the list of providers
        $geocoder = new \Geocoder\ProviderAggregator();

        $chain = new \Geocoder\Provider\Chain\Chain($p);

        $geocoder->registerProvider($chain);

        // Try to get some data...
        $query = \Geocoder\Query\ReverseQuery::fromCoordinates($latitude, $longitude);

        return $geocoder->reverseQuery($query);
    }

    /**
     * Get the geo database
     *
     * @return  mixed  Database object upon success, null if error
     */
    public static function getGeoDBO()
    {
        static $instance;

        if (!is_object($instance)) {
            $geodb_params = Component::params('com_system');

            $options = array();
            $options['driver']   = $geodb_params->get('geodb_driver', 'pdo');
            $options['host']     = $geodb_params->get('geodb_host', 'localhost');
            $options['port']     = $geodb_params->get('geodb_port', '');
            $options['user']     = $geodb_params->get('geodb_user', '');
            $options['password'] = $geodb_params->get('geodb_password', '');
            $options['database'] = $geodb_params->get('geodb_database', '');
            $options['prefix']   = $geodb_params->get('geodb_prefix', '');

            if (empty($options['database']) || empty($options['user']) || empty($options['password'])) {
                return null;
            }

            try {
                $instance = \Hubzero\Database\Driver::getInstance($options);
            } catch (\Exception $e) {
                $instance = false;
            }
        }

        if (!$instance || ($instance instanceof \Exception) || !$instance->getConnection()) {
            return null;
        }

        return $instance;
    }

    /**
     * Get a list of countries and their coutnry code
     *
     * @return  array
     */
    public static function getcountries()
    {
        $countries = array();

        if (!($gdb = self::getGeoDBO())) {
            return $countries;
        }

        $gdb->setQuery("SELECT code, name FROM countries ORDER BY name");
        $results = $gdb->loadObjectList();

        if ($results) {
            foreach ($results as $row) {
                if ($row->code != '-' && $row->name != '-') {
                    array_push($countries, array(
                        'code' => strtolower($row->code),
                        'id'   => $row->code,
                        'name' => $row->name
                    ));
                }
            }
        }

        return $countries;
    }

    /**
     * Get a list of countries by continent
     *
     * @param   string  $continent
     * @return  array
     */
    public static function getCountriesByContinent($continent = '')
    {
        if (!$continent || !($gdb = self::getGeoDBO())) {
            return array();
        }

        $gdb->setQuery("SELECT DISTINCT country FROM country_continent WHERE LOWER(continent) =" . $gdb->quote(strtolower($continent)));
        return $gdb->loadColumn();
    }

    /**
     * Get continent by the country
     *
     * @param   string  $country
     * @return  array
     */
    public static function getContinentByCountry($country = '')
    {
        if (!$country || !($gdb = self::getGeoDBO())) {
            return array();
        }

        $gdb->setQuery("SELECT DISTINCT continent FROM country_continent WHERE LOWER(country) =" . $gdb->quote(strtolower($country)));
        return $gdb->loadColumn();
    }

    /**
     * Get a list of country codes by names
     *
     * @param   array  $names  List of country names
     * @return  array
     */
    public static function getCodesByNames($names = array())
    {
        if (!($gdb = self::getGeoDBO())) {
            return array();
        }

        $names = array_map('strtolower', $names);
        foreach ($names as $k => $name) {
            $names[$k] = $gdb->quote($name);
        }

        $gdb->setQuery("SELECT DISTINCT name, code FROM countries WHERE LOWER(name) IN (" . implode(",", $names) . ")");
        $values = $gdb->loadAssocList('name');
        if (!is_array($values)) {
            $values = array();
        }
        return $values;
    }

    /**
     * Get country based on short code
     *
     * @param   string  $code  Short code (ex: us, de, fr, jp)
     * @return  string
     */
    public static function getCodeByName($name = '')
    {
        $code = '';
        if ($name) {
            if (!($gdb = self::getGeoDBO())) {
                return $code;
            }

            $gdb->setQuery("SELECT code FROM countries WHERE LOWER(name) = " . $gdb->quote(strtolower($name)));
            $code = stripslashes($gdb->loadResult() ?? '');
        }
        return $code;
    }

    /**
     * Get country based on short code
     *
     * @param   string  $code  Short code (ex: us, de, fr, jp)
     * @return  string
     */
    public static function getcountry($code = '')
    {
        $name = '';
        if ($code) {
            if (!($gdb = self::getGeoDBO())) {
                return $name;
            }

            $gdb->setQuery("SELECT name FROM countries WHERE code = " . $gdb->quote($code));
            $name = stripslashes($gdb->loadResult() ?? '');
        }
        return $name;
    }

    /**
     * Lookups already made, by kind, so that callers asking the same thing
     * in one request (both sign-in plugins, the export-control check) cost
     * one query. Static, so in a CLI process they last for the process; geo
     * data changes monthly, and each kind is emptied past 1000 entries so a
     * run over many addresses does not grow without bound.
     *
     * @var  array
     */
    private static $cache = array();

    /**
     * Whether each IPv6 table is known unusable, as found in this request
     *
     * @var  array
     */
    private static $unusable = array();

    /**
     * A cached lookup, made when not cached yet
     *
     * A lookup that could not be made (no geo database, an unusable table)
     * returns null, which is not cached: it is asked again next time.
     *
     * @param   string    $kind
     * @param   string    $key
     * @param   callable  $lookup
     * @return  mixed     null when the lookup could not be made
     */
    private static function cached($kind, $key, $lookup)
    {
        if (isset(self::$cache[$kind]) && array_key_exists($key, self::$cache[$kind])) {
            return self::$cache[$kind][$key];
        }

        $result = $lookup();

        if ($result === null) {
            return null;
        }

        if (isset(self::$cache[$kind]) && count(self::$cache[$kind]) >= 1000) {
            self::$cache[$kind] = array();
        }

        return self::$cache[$kind][$key] = $result;
    }

    /**
     * Whether an IPv6 table is known unusable (missing, or with other
     * columns than expected)
     *
     * Kept in the CMS cache for five minutes, since a static does not
     * outlive a web request; read once per request. Where the cache is
     * off, each request finds out for itself.
     *
     * @param   string     $table
     * @param   bool|null  $set    True to record it unusable
     * @return  bool
     */
    private static function unusable($table, $set = null)
    {
        $key = 'geocode.unusable.' . $table;

        if ($set) {
            self::$unusable[$table] = true;

            try {
                \App::get('cache')->put($key, 1, 5);
            } catch (\Exception $e) {
            }

            return true;
        }

        if (!isset(self::$unusable[$table])) {
            try {
                self::$unusable[$table] = (bool) \App::get('cache')->get($key);
            } catch (\Exception $e) {
                self::$unusable[$table] = false;
            }
        }

        return self::$unusable[$table];
    }

    /**
     * Where an address is looked up in the geo database
     *
     * An IPv4 address, an IPv4-mapped one (::ffff:1.2.3.4) included, is
     * looked up in the IPv4 tables (ipcountry, iplocation), keyed by the
     * address as an integer. An IPv6 address is looked up in the IPv6 ones
     * (ipcountry6, iplocation6), keyed by DECIMAL(39,0), as the ip2location
     * role loads them: its value goes in through CAST, because MySQL
     * compares a quoted number with a DECIMAL as a double, which cannot tell
     * neighbouring IPv6 addresses apart.
     *
     * @param   object      $gdb    Geo database
     * @param   string      $ip     Address
     * @param   string      $table  ipcountry or iplocation
     * @return  array|null  array(table, SQL value), or null when there is no lookup
     */
    protected static function ipLookup($gdb, $ip, $table)
    {
        $ip = \Hubzero\Utility\Ip::normalize($ip);

        if ($ip === '') {
            return null;
        }

        if (strpos($ip, ':') === false) {
            return array($table, $gdb->quote(ip2long($ip)));
        }

        $table .= '6';

        if (self::unusable($table)) {
            return null;
        }

        return array($table, 'CAST(' . $gdb->quote(\Hubzero\Utility\Ip::toDecimal($ip)) . ' AS DECIMAL(39,0))');
    }

    /**
     * The result of a lookup query
     *
     * A query on an IPv6 table that fails is no result, not an error: a geo
     * database may not have those tables, or have them with other columns,
     * and these lookups run at every sign-in. The failure is logged, and
     * the table not asked again for five minutes (see unusable()). The IPv4
     * tables are queried as they always were.
     *
     * @param   object  $gdb     Geo database
     * @param   string  $table
     * @param   string  $sql
     * @param   bool    $failed  Set true when an IPv6 query failed
     * @return  mixed   null when an IPv6 query failed
     */
    private static function lookupResult($gdb, $table, $sql, &$failed = false)
    {
        $failed = false;

        $gdb->setQuery($sql);

        if (substr($table, -1) !== '6') {
            return $gdb->loadResult();
        }

        try {
            return $gdb->loadResult();
        } catch (\Exception $e) {
            $failed = true;

            self::unusable($table, true);

            \Log::warning('Geocode: IPv6 lookups skipped for five minutes, querying ' . $table . ' failed: ' . $e->getMessage());

            return null;
        }
    }

    /**
     * Get the country based on IP address
     *
     * @param   string  $ip  IP address to look up
     * @return  string  Lower-case country code, or '' when not known
     */
    public static function ipcountry($ip = '')
    {
        $ip = \Hubzero\Utility\Ip::normalize($ip);

        if ($ip === '') {
            return '';
        }

        return (string) self::cached('ipcountry', $ip, function () use ($ip) {
            if (!($gdb = self::getGeoDBO()) || !($lookup = self::ipLookup($gdb, $ip, 'ipcountry'))) {
                return null;
            }
            list($table, $n_ip) = $lookup;

            // The range starting closest below the address, if it reaches it
            $sql = "SELECT LOWER(countrySHORT) FROM " . $table
                . " INNER JOIN (SELECT MAX(ipFROM) AS start FROM " . $table
                . " WHERE ipFROM <= " . $n_ip . ") s ON (ipFROM = s.start)"
                . " WHERE ipTO >= " . $n_ip;

            $result = self::lookupResult($gdb, $table, $sql, $failed);

            return $failed ? null : stripslashes((string) $result);
        });
    }

    /**
     * The export-control groups of a country (D1, E1, ...), in one query
     * shared by is_d1nation() and is_e1nation()
     *
     * @param   string  $country  Country code
     * @return  array
     */
    protected static function countryGroups($country)
    {
        return self::cached('countrygroup', strtoupper((string) $country), function () use ($country) {
            if (!($gdb = self::getGeoDBO())) {
                return null;
            }

            // Both cases, whatever the collation, as LOWER() on both sides
            // did; IN keeps the primary key in use, which LOWER() did not
            $gdb->setQuery("SELECT countrygroup FROM countrygroup WHERE countrycode IN ("
                . $gdb->quote(strtoupper($country)) . ", " . $gdb->quote(strtolower($country)) . ")");

            return array_map('strtoupper', (array) $gdb->loadColumn());
        });
    }

    /**
     * Is a country an D1 nation?
     *
     * @param   string   $country  Country to check
     * @return  boolean  True if D1
     */
    public static function is_d1nation($country)
    {
        return $country ? in_array('D1', (array) self::countryGroups($country)) : false;
    }

    /**
     * Is a country an E1 nation?
     *
     * @param   string   $country  Country to check
     * @return  boolean  True if E1
     */
    public static function is_e1nation($country)
    {
        return $country ? in_array('E1', (array) self::countryGroups($country)) : false;
    }

    /**
     * Check if an IP is in a certain location
     *
     * @param   string   $ip        IP address to check
     * @param   string   $location  Location to check in
     * @return  boolean  True if IP is in the location
     */
    public static function is_iplocation($ip, $location)
    {
        $ip = \Hubzero\Utility\Ip::normalize($ip);

        if ($ip === '' || !$location) {
            return false;
        }

        return (bool) self::cached('iplocation', strtolower($location) . ' ' . $ip, function () use ($ip, $location) {
            if (!($gdb = self::getGeoDBO()) || !($lookup = self::ipLookup($gdb, $ip, 'iplocation'))) {
                return null;
            }
            list($table, $n_ip) = $lookup;

            $sql = "SELECT COUNT(*) FROM " . $table . " WHERE ipfrom <= " . $n_ip . " AND ipto >= " . $n_ip . " AND LOWER(location) = " .  $gdb->quote(strtolower($location));

            $result = self::lookupResult($gdb, $table, $sql, $failed);

            return $failed ? null : $result > 0;
        });
    }
}
