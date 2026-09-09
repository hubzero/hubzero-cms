<!--
status: generated
source: core/components/com_saml/config/config.xml
-->

# Saml (com_saml)

SAML IdP

Parameters from [`core/components/com_saml/config/config.xml`](../../../../core/components/com_saml/config/config.xml), as shown on the component's **Options** screen in the administrator interface.

## Idp

| Parameter | Label | Type | Default | Description |
|---|---|---|---|---|
| `IdP_enable` | Enable IdP | radio | `1 (Yes)` | Master switch for all /saml/idp/* endpoints. Options: `0` No, `1` Yes. |
| `IdP_entityID` | IdP Entity ID | text | — | The SAML entity ID this hub asserts as. Leave empty to use the hub root URL. |
| `enableIdPMetadataEndpoint` | Enable metadata endpoint | radio | `1 (Yes)` | Serve the IdP metadata document at /saml/idp/metadata. Options: `0` No, `1` Yes. |
| `IdPCertificateFile` | Signing certificate file | text | `/etc/saml/cert/saml.crt` | Absolute path to the IdP X.509 signing certificate (PEM). |
| `IdPKeyFile` | Private key file | text | `/etc/saml/cert/saml.pem` | Absolute path to the IdP private key (PEM). |
| `IdPKeyPassphrase` | Private key passphrase | password | — | Passphrase for the private key, if it has one. |

## Protocol

| Parameter | Label | Type | Default | Description |
|---|---|---|---|---|
| `assertion_lifetime` | Assertion lifetime (seconds) | text | `180` | How long issued assertions stay valid (NotOnOrAfter). Default 180. |
| `clock_skew` | Clock skew (seconds) | text | `60` | Tolerance for clock differences with SPs (NotBefore). Default 60. |
| `signature_algorithm` | Signature algorithm | list | `rsa-sha256 (RSA-SHA256)` | Signature and digest algorithm for issued messages. SHA-256 or better only. Options: `rsa-sha256` RSA-SHA256, `rsa-sha384` RSA-SHA384, `rsa-sha512` RSA-SHA512. |
