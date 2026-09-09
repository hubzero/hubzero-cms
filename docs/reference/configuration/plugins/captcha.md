<!--
status: generated
source: core/plugins/captcha/*/*.xml
-->

# Captcha plugins

Parameters of every plugin in the `captcha` group, from each plugin's manifest. Set them under **Extensions > Plugins** in the administrator interface.

## Image CAPTCHA (`plg_captcha_image`)

Generates an image based CAPTCHA

### Basic

| Parameter | Label | Type | Default | Description |
|---|---|---|---|---|
| `bgColor` | Background Color | text | `#ffffff` | Enter color (6 letter hex value) |
| `textColor` | Text Color | text | `#2c8007` | Enter color (6 letter hex value) |
| `imageFunction` | Select Image Function | list | `Adv (Distorted letters)` | Select wether you want to show distorted letters or plane letters. Options: `Plain` Plain letters, `Adv` Distorted letters. |

## Captcha - Math (`plg_captcha_math`)

Generates a math based CAPTCHA

### Basic

| Parameter | Label | Type | Default | Description |
|---|---|---|---|---|
| `lower` | Random integer Min value | text | `0` | Minimum integer value to generate a random number from |
| `upper` | Random integer Max value | text | `10` | Maximum integer value to generate a random number from |

## Captcha - ReCaptcha (`plg_captcha_recaptcha`)

This CAPTCHA plugin uses the reCAPTCHA service to prevent spammers while it helps to digitize books, newspapers and old radio shows. To get a public and private key for your domain, go to http://www.google.com/recaptcha. To use this for new account registration, go to Options in the User Manager and select Captcha - reCaptcha as the Captcha.

### Basic

| Parameter | Label | Type | Default | Description |
|---|---|---|---|---|
| `public` | Public Key | text | — | ReCaptcha Public Key. Visit www.google.com/recaptcha |
| `private` | Private Key | text | — | ReCaptcha Private Key Visit www.google.com/recaptcha |
| `theme` | Theme | list | `light (Light)` | The color theme of the CAPTCHA widget. Options: `light` Light, `dark` Dark. |
| `type` | Type | list | `image (Image)` | The type of CAPTCHA to serve. Options: `image` Image, `audio` Audio. |
| `language` | Language Code | text | `en` | Language code used when rendering reCAPTCHA. See https://developers.google.com/recaptcha/docs/language. |
