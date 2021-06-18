# Flysystem Adapter for Github

[![Latest Version](https://img.shields.io/github/release/potherca/flysystem-github.svg?style=flat-square)](https://github.com/potherca/flysystem-github/releases)
[![Software License](https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square)](LICENSE.md)
[![Build Status](https://img.shields.io/travis/potherca/flysystem-github.svg?style=flat-square)](https://travis-ci.org/potherca/flysystem-github)
[![Coverage Status](https://coveralls.io/repos/potherca/flysystem-github/badge.svg)](https://coveralls.io/github/potherca/flysystem-github)
[![Quality Score](https://img.shields.io/scrutinizer/g/potherca/flysystem-github.svg?style=flat-square)](https://scrutinizer-ci.com/g/potherca/flysystem-github)
[![Total Downloads](https://img.shields.io/packagist/dt/potherca/flysystem-github.svg?style=flat-square)](https://packagist.org/packages/potherca/flysystem-github)

## Install

Via Composer

``` bash
$ composer require potherca/flysystem-github
```

## Usage

The Github adapter can be used *without* credentials to do read-only actions on
public repositories. To avoid reaching the Github API limit, to save changes, or 
to read from private repositories, credentials are required.

Caching can be utilized to save traffic or to postpone reaching the Github API 
limit.

### Basic Usage

```php
use Github\Client;
use League\Flysystem\Filesystem;
use Potherca\Flysystem\Github\Api;
use Potherca\Flysystem\Github\GithubAdapter;
use Potherca\Flysystem\Github\Settings;

$project = 'thephpleague/flysystem';

$settings = new Settings($project);

$api = new Api(new Client(), $settings);
$adapter = new GithubAdapter($api);
$filesystem = new Filesystem($adapter);
```

### Authentication

```php
use Github\Client;
use League\Flysystem\Filesystem;
use Potherca\Flysystem\Github\Api;
use Potherca\Flysystem\Github\GithubAdapter;
use Potherca\Flysystem\Github\Settings;

$project = 'thephpleague/flysystem';
$credentials = [Settings::AUTHENTICATE_USING_TOKEN, '83347e315b8bb4790a48ed6953a5ad9e825b4e10'];
// or $authentications = [Settings::AUTHENTICATE_USING_PASSWORD, $username, $password];
    
$settings = new Settings($project, $credentials);

$api = new Api(new Client(), $settings);
$adapter = new GithubAdapter($api);
$filesystem = new Filesystem($adapter);
```

### Cache Usage

```php
use Github\Client;
use Github\HttpClient\CachedHttpClient as CachedClient;
use Github\HttpClient\Cache\FilesystemCache as Cache;
use League\Flysystem\Filesystem;
use Potherca\Flysystem\Github\Api;
use Potherca\Flysystem\Github\GithubAdapter;
use Potherca\Flysystem\Github\Settings;

$project = 'thephpleague/flysystem';

$settings = new Settings($project);

$cache = new Cache('/tmp/github-api-cache')
$cacheClient = new CachedClient();
$cacheClient->setCache($cache);

$api = new Api($cacheClient, $settings);
$adapter = new GithubAdapter($api);
$filesystem = new Filesystem($adapter);

```

## Testing

``` bash
$ composer test
```

## Security

If you discover any security related issues, please email potherca@gmail.com instead of using the issue tracker.

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

## Change Log

Please see [CHANGELOG](CHANGELOG.md) for details.

## Credits

- [Potherca](https://github.com/potherca)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
