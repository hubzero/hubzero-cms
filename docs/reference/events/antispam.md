<!--
status: generated
source: Event::trigger('antispam.*') call sites and core/plugins/antispam/
-->

# Antispam events

Events in the `antispam` group. A plugin in `core/plugins/antispam/` receives an event by defining a public method with the event's name; the arguments are those the call site passes, in order.

## `antispam.onAntispamDetector`

No call site in the CMS fires this event; the listener may answer an event fired by another package or by an older plugin.

Listeners:

- `plg_antispam_akismet` — [`onAntispamDetector()`](../../../core/plugins/antispam/akismet/akismet.php)
- `plg_antispam_babajispam` — [`onAntispamDetector()`](../../../core/plugins/antispam/babajispam/babajispam.php)
- `plg_antispam_bayesian` — [`onAntispamDetector()`](../../../core/plugins/antispam/bayesian/bayesian.php)
- `plg_antispam_blacklist` — [`onAntispamDetector()`](../../../core/plugins/antispam/blacklist/blacklist.php)
- `plg_antispam_linkrife` — [`onAntispamDetector()`](../../../core/plugins/antispam/linkrife/linkrife.php)
- `plg_antispam_spamassassin` — [`onAntispamDetector()`](../../../core/plugins/antispam/spamassassin/spamassassin.php)

## `antispam.onAntispamTrain`

Fired from:

- [`core/components/com_support/admin/controllers/abusereports.php:293`](../../../core/components/com_support/admin/controllers/abusereports.php#L293) with `[ $reported->text, $isSpam ]`
- [`core/plugins/content/antispam/antispam.php:124`](../../../core/plugins/content/antispam/antispam.php#L124) with `[ $content, true ]`
- [`core/plugins/content/antispam/antispam.php:151`](../../../core/plugins/content/antispam/antispam.php#L151) with `[ $content, false ]`

Listeners:

- `plg_antispam_akismet` — [`onAntispamTrain($content, $isSpam)`](../../../core/plugins/antispam/akismet/akismet.php)
- `plg_antispam_bayesian` — [`onAntispamTrain($content, $isSpam)`](../../../core/plugins/antispam/bayesian/bayesian.php)
- `plg_antispam_spamassassin` — [`onAntispamTrain($content, $isSpam)`](../../../core/plugins/antispam/spamassassin/spamassassin.php)
