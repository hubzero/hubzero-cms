<!--
status: generated
source: Event::trigger('geocode.*') call sites and core/plugins/geocode/
-->

# Geocode events

Events in the `geocode` group. A plugin in `core/plugins/geocode/` receives an event by defining a public method with the event's name; the arguments are those the call site passes, in order.

## `geocode.onGeocodeProvider`

Fired from:

- [`core/libraries/Hubzero/Geocode/Geocode.php:44`](../../../core/libraries/Hubzero/Geocode/Geocode.php#L44) with `array('geocode.countries', $adapter))) { foreach ($providers as $provider) { if ($provider) { $p[] = $provider; } } } if (!count($p)) { r…`
- [`core/libraries/Hubzero/Geocode/Geocode.php:102`](../../../core/libraries/Hubzero/Geocode/Geocode.php#L102) with `array('geocode.country', $adapter))) { foreach ($providers as $provider) { if ($provider) { $p[] = $provider; } } } if (!count($p)) { ret…`
- [`core/libraries/Hubzero/Geocode/Geocode.php:148`](../../../core/libraries/Hubzero/Geocode/Geocode.php#L148) with `array('geocode.continent', $adapter))) { foreach ($providers as $provider) { if ($provider) { $p[] = $provider; } } } if (!count($p)) { r…`
- [`core/libraries/Hubzero/Geocode/Geocode.php:200`](../../../core/libraries/Hubzero/Geocode/Geocode.php#L200) with `array('geocode.locate', $adapter, $ip))) { foreach ($providers as $provider) { if ($provider) { $p[] = $provider; } } } if (!count($p)) {…`
- [`core/libraries/Hubzero/Geocode/Geocode.php:241`](../../../core/libraries/Hubzero/Geocode/Geocode.php#L241) with `array('geocode.address', $adapter))) { foreach ($providers as $provider) { if ($provider) { $p[] = $provider; } } } if (!count($p)) { ret…`

Listeners:

- `plg_geocode_arcgisonline` — [`onGeocodeProvider($context, $adapter, $ip=false)`](../../../core/plugins/geocode/arcgisonline/arcgisonline.php)
- `plg_geocode_bingmaps` — [`onGeocodeProvider($context, $adapter, $ip=false)`](../../../core/plugins/geocode/bingmaps/bingmaps.php)
- `plg_geocode_geonames` — [`onGeocodeProvider($context, $adapter, $ip=false)`](../../../core/plugins/geocode/geonames/geonames.php)
- `plg_geocode_geoplugin` — [`onGeocodeProvider($context, $adapter, $ip=false)`](../../../core/plugins/geocode/geoplugin/geoplugin.php)
- `plg_geocode_googlemaps` — [`onGeocodeProvider($context, $adapter, $ip=false)`](../../../core/plugins/geocode/googlemaps/googlemaps.php)
- `plg_geocode_hostip` — [`onGeocodeProvider($context, $adapter, $ip=false)`](../../../core/plugins/geocode/hostip/hostip.php)
- `plg_geocode_ipinfodb` — [`onGeocodeProvider($context, $adapter, $ip=false)`](../../../core/plugins/geocode/ipinfodb/ipinfodb.php)
- `plg_geocode_ipstack` — [`onGeocodeProvider($context, $adapter, $ip=false)`](../../../core/plugins/geocode/ipstack/ipstack.php)
- `plg_geocode_local` — [`onGeocodeProvider($context, $adapter, $ip=false)`](../../../core/plugins/geocode/local/local.php)
- `plg_geocode_mapquest` — [`onGeocodeProvider($context, $adapter, $ip=false)`](../../../core/plugins/geocode/mapquest/mapquest.php)
- `plg_geocode_maxmind` — [`onGeocodeProvider($context, $adapter, $ip=false)`](../../../core/plugins/geocode/maxmind/maxmind.php)
- `plg_geocode_maxmindbinary` — [`onGeocodeProvider($context, $adapter, $ip=false)`](../../../core/plugins/geocode/maxmindbinary/maxmindbinary.php)
- `plg_geocode_nominatim` — [`onGeocodeProvider($context, $adapter, $ip=false)`](../../../core/plugins/geocode/nominatim/nominatim.php)
- `plg_geocode_tomtom` — [`onGeocodeProvider($context, $adapter, $ip=false)`](../../../core/plugins/geocode/tomtom/tomtom.php)
- `plg_geocode_yandex` — [`onGeocodeProvider($context, $adapter, $ip=false)`](../../../core/plugins/geocode/yandex/yandex.php)
