<!--
status: generated
source: core/components/com_developer/api/controllers/
-->

# Developer API

Endpoints under `/api/developer`, from the `com_developer` API controllers. Authenticate with an OAuth bearer token or a session cookie; see the developers book for the API basics.

| Method | Endpoint | Purpose |
|---|---|---|
| `POST` | [`/developer/oauth/token`](#post-developer-oauth-token) | Handle a request for an OAuth2.0 Access Token and send the response to the client |

## POST /developer/oauth/token

Handle a request for an OAuth2.0 Access Token and send the response to the client

API version 1.0, task `token` in [`oauthv1_0.php`](../../../core/components/com_developer/api/controllers/oauthv1_0.php#L19).
