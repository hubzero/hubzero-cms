# orcid-php
PHP Library for ORCID

[![Build Status](https://travis-ci.org/hubzero/orcid-php.svg?branch=master)](https://travis-ci.org/hubzero/orcid-php)
[![Coverage Status](https://coveralls.io/repos/hubzero/orcid-php/badge.svg?branch=master&service=github)](https://coveralls.io/github/hubzero/orcid-php?branch=master)
[![StyleCI](https://styleci.io/repos/38449188/shield)](https://styleci.io/repos/38449188)

This library was started to support the ORCID OAuth2 authentication workflow. It also supports basic profile access, but is a work in progress. More features are to come as needed by the developer or requested/contributed by other interested parties.

## Usage

### OAuth2

#### 3-Legged Oauth Authorization

To go through the 3-legged oauth process, you must start by redirecting the user to the ORCID authorization page.

```php
// Set up the config for the ORCID API instance
$oauth = new Oauth;
$oauth->setClientId($clientId)
      ->setScope('/authenticate')
      ->setState($state)
      ->showLogin()
      ->setRedirectUri($redirectUri);

// Create and follow the authorization URL
header("Location: " . $oauth->getAuthorizationUrl());
```

Most of the options described in the ORCID documentation (http://members.orcid.org/api/customize-oauth-login-screen) concerning customizing the user authorization experience are encapsulated in the OAuth class.

Once the user authorizes your app, they will be redirected back to your redirect URI. From there, you can exchange the authorization code for an access token.

```php
if (!isset($_GET['code']))
{
	// User didn't authorize our app
	throw new Exception('Authorization failed');
}

$oauth = new Oauth;
$oauth->setClientId($clientId)
      ->setClientSecret($clientSecret)
      ->setRedirectUri($redirectUri);

// Authenticate the user
$oauth->authenticate($_GET['code']);

// Check for successful authentication
if ($oauth->isAuthenticated())
{
	$orcid = new Profile($oauth);

	// Get ORCID iD
	$id = $orcid->id();
}
```

This example uses the ORCID public API. A members API is also available, but the OAuth process is essentially the same.

#### Client Credential Authorization

To be implemented...

### Profile

As alluded to in the samples above, once successfully authenticated via OAuth, you can make subsequent requests to the other public/member APIs. For example:

```php
$orcid = new Profile($oauth);

// Get ORCID profile details
$id    = $orcid->id();
$email = $orcid->email();
$name  = $orcid->fullName();
```

The profile class currently only supports a limited number of helper methods for directly accessing elements from the profile data. This will be expanded upon as needed. The raw JSON data from the profile output is available by calling the raw() method.

Note that some fields (like email) may return null if the user has not made that field available.
