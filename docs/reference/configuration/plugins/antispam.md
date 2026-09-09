<!--
status: generated
source: core/plugins/antispam/*/*.xml
-->

# Antispam plugins

Parameters of every plugin in the `antispam` group, from each plugin's manifest. Set them under **Extensions > Plugins** in the administrator interface.

## Antispam - Akismet (`plg_antispam_akismet`)

This plugin uses the remote Akismet service to scan posted content and determine is content has a high probability of being spam or not.

### Basic

| Parameter | Label | Type | Default | Description |
|---|---|---|---|---|
| `apiKey` | API Key | text | — | The API Key of the service. |
| `apiPort` | API Port | text | `80` | The API Port to use. |
| `akismetServer` | Akismet Server | text | `rest.akismet.com` | The URL for the Akismet server. |
| `akismetVersion` | Akismet Version | text | `1.1` | The version of Akismet to use. |

## Antispam - Baba Ji Spam Detector (`plg_antispam_babajispam`)

Plugin provides content spam detection of the mass spammer Baba Ji

### Basic

| Parameter | Label | Type | Default | Description |
|---|---|---|---|---|
| `message` | Message | text | `The submitted text was detected as possible spam or containing inappropriate content.` | The message to display when content is detected as possibly being spam. |

## Antispam - Bayesian Filter (`plg_antispam_bayesian`)

This is a Spam Detector that employs a basic bayesian filter.

### Basic

| Parameter | Label | Type | Default | Description |
|---|---|---|---|---|
| `threshold` | Threshold | text | `0.95` | Ratio (In Percentage) of the number of links to the number of words in the string. If the percentage ratio is greater than the specified ratio, it is considered a 'Link Overflow'. |
| `learn` | Train | list | `1 (Yes)` | Train the system from content that is marked as Spam? Enabling this allows the system to more accurately detect spam. Options: `0` No, `1` Yes. |

## Antispam - Black List (`plg_antispam_blacklist`)

This is a Spam Detector that detects black-listed words.

### Basic

| Parameter | Label | Type | Default | Description |
|---|---|---|---|---|
| `badwords` | Bad words | textarea | `viagra, xanax, phentermine, ringtones, tramadol, hydrocodone, levitra, ambien, vicodin, fioricet, diazepam, accarat, casino, fuck, crap, shit, asshole, cunt, fucker, fuckers, motherfucker, fucking, milf, cocksucker, porno, videosex, hentai, kasino, kasinos, poker` | A comma-separated list of spam words |

## Antispam - Link Rife (`plg_antispam_linkrife`)

This is a Spam Detector that detects link frequency in a body of text.

### Basic

| Parameter | Label | Type | Default | Description |
|---|---|---|---|---|
| `linkFrequency` | Link Frequency | text | `10` | Maximum number of links allowed in a text before it is considered spam. |
| `linkRatio` | Link to Text Ratio | text | `40` | Ratio (In Percentage) of the number of links to the number of words in the string. If the percentage ratio is greater than the specified ratio, it is considered a 'Link Overflow' |
| `linkValidation` | Link validation | list | `0 (No)` | Check found links against known blacklists. Options: `0` No, `1` Yes. |

## Antispam - SpamAssassin (`plg_antispam_spamassassin`)

This plugin scans posted content with SpamAssassin to determine is content has a high probability of being spam or not.

### Basic

| Parameter | Label | Type | Default | Description |
|---|---|---|---|---|
| `client` | Client | list | `local (Local)` | The client type. Local will use a local SpamAssassin service. Options: `local` Local, `remote` Remote. |

### Local

| Parameter | Label | Type | Default | Description |
|---|---|---|---|---|
| `hostname` | Hostname | text | `localhost` | Hostname to use. |
| `port` | Port | text | `783` | Port to use. |
| `protocolVersion` | Protocol Version | text | `1.5` | Protocol version to use. |
| `socket` | Socket | text | — | Socket to use. |
| `socketPath` | Socket Path | text | — | Socket path to use. |
| `enableZlib` | Enable Zlib | list | `0 (No)` | Enable Zlib or not. Options: `0` No, `1` Yes. |

### Remote

| Parameter | Label | Type | Default | Description |
|---|---|---|---|---|
| `server` | Server | text | `http://spamcheck.postmarkapp.com/filter` | Remote client server to submit to. |
| `verbose` | Verbose report | list | `0 (No)` | Return a short or long report. Options: `0` No, `1` Yes. |
