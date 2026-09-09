<!--
status: imported
source: https://help.hubzero.org/documentation/240/webdevs/api
source-id: 3532
modified: 2015-10-01
imported: 2026-09-09
-->
# API

## Internal Requests via JavaScript

Internal requests to the API can easily be handled through the XMLHttpRequest feature of JavaScript. Many JavaScript libraries abstract away the details of this feature, and jQuery is no different.

In a typical API call, one would be expected to provide authentication in the form of a user access token. This token is often retrieved by asked the user to authenticate to the API server. But, because the client and the server in this scenario are the same, and the user is already logged in, it seems silly to ask them to authenticate again. Even still, we need a token to make an authenticated call to the API. To do this, we must then request a token based on the clients credentials in the form of an HTTP cookie, much the same way standard sessions are managed with a browser.

To make this even simpler, we've added some snippets to the HUBzero libraries to help you out. To setup the API request in your jQuery code, simply include the `initApi` call, as shown:

```javascript
jQuery(document).ready(function($) {
    Hubzero.initApi(function() {
        // your code here
    });
});
```

All this does is it makes a request to the API to get a token based on your current session cookie. It then sets that token as a header that will be used by jQuery in all future `$.ajax` requests.

Lastly, you'll need to make sure the Hubzero JavaScript object is available. To do so, just make sure to include the Html environment call somewhere in your PHP to include our core JS library.

```php
Html::behavior('core');
```

For additional details see "https://hubname.org/developer/api/docs" on your respective hub.
