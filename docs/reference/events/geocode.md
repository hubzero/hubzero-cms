<!--
status: generated
source: Event::trigger('geocode.*') call sites and core/plugins/geocode/
-->

# Geocode events

Events in the `geocode` group. A plugin in `core/plugins/geocode/` receives an event by defining a public method with the event's name; the arguments are those the call site passes, in order.

## `geocode.onGeocodeProvider`

Fired from:

- [`core/libraries/Hubzero/Geocode/Geocode.php:49`](../../../core/libraries/Hubzero/Geocode/Geocode.php#L49) with `['geocode.countries', $adapter])) { foreach ($providers as $provider) { if ($provider) { $p[] = $provider; } } } if (!count($p)) { return…`
- [`core/libraries/Hubzero/Geocode/Geocode.php:101`](../../../core/libraries/Hubzero/Geocode/Geocode.php#L101) with `['geocode.country', $adapter])) { foreach ($providers as $provider) { if ($provider) { $p[] = $provider; } } } if (!count($p)) { return '…`
- [`core/libraries/Hubzero/Geocode/Geocode.php:142`](../../../core/libraries/Hubzero/Geocode/Geocode.php#L142) with `['geocode.continent', $adapter])) { foreach ($providers as $provider) { if ($provider) { $p[] = $provider; } } } if (!count($p)) { return…`
- [`core/libraries/Hubzero/Geocode/Geocode.php:188`](../../../core/libraries/Hubzero/Geocode/Geocode.php#L188) with `['geocode.locate', $adapter, $ip])) { foreach ($providers as $provider) { if ($provider) { $p[] = $provider; } } } if (!count($p)) { retu…`
- [`core/libraries/Hubzero/Geocode/Geocode.php:225`](../../../core/libraries/Hubzero/Geocode/Geocode.php#L225) with `['geocode.address', $adapter])) { foreach ($providers as $provider) { if ($provider) { $p[] = $provider; } } } if (!count($p)) { return '…`

Listeners:

- `plg_geocode_arcgisonline` — [`onGeocodeProvider($context, $adapter, $ip = false)`](../../../core/plugins/geocode/arcgisonline/arcgisonline.php)
- `plg_geocode_bingmaps` — [`onGeocodeProvider($context, $adapter, $ip = false)`](../../../core/plugins/geocode/bingmaps/bingmaps.php)
- `plg_geocode_geonames` — [`onGeocodeProvider($context, $adapter, $ip = false)`](../../../core/plugins/geocode/geonames/geonames.php)
- `plg_geocode_geoplugin` — [`onGeocodeProvider($context, $adapter, $ip = false)`](../../../core/plugins/geocode/geoplugin/geoplugin.php)
- `plg_geocode_googlemaps` — [`onGeocodeProvider($context, $adapter, $ip = false)`](../../../core/plugins/geocode/googlemaps/googlemaps.php)
- `plg_geocode_hostip` — [`onGeocodeProvider($context, $adapter, $ip = false)`](../../../core/plugins/geocode/hostip/hostip.php)
- `plg_geocode_ipinfodb` — [`onGeocodeProvider($context, $adapter, $ip = false)`](../../../core/plugins/geocode/ipinfodb/ipinfodb.php)
- `plg_geocode_ipstack` — [`onGeocodeProvider($context, $adapter, $ip = false)`](../../../core/plugins/geocode/ipstack/ipstack.php)
- `plg_geocode_local` — [`onGeocodeProvider($context, $adapter, $ip = false)`](../../../core/plugins/geocode/local/local.php)
- `plg_geocode_mapquest` — [`onGeocodeProvider($context, $adapter, $ip = false)`](../../../core/plugins/geocode/mapquest/mapquest.php)
- `plg_geocode_maxmind` — [`onGeocodeProvider($context, $adapter, $ip = false)`](../../../core/plugins/geocode/maxmind/maxmind.php)
- `plg_geocode_nominatim` — [`onGeocodeProvider($context, $adapter, $ip = false)`](../../../core/plugins/geocode/nominatim/nominatim.php)
- `plg_geocode_tomtom` — [`onGeocodeProvider($context, $adapter, $ip = false)`](../../../core/plugins/geocode/tomtom/tomtom.php)
- `plg_geocode_yandex` — [`onGeocodeProvider($context, $adapter, $ip = false)`](../../../core/plugins/geocode/yandex/yandex.php)
