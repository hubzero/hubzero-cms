<!--
status: generated
source: Event::trigger('antispam.*') call sites and core/plugins/antispam/
-->

# Antispam events

Events in the `antispam` group. A plugin in `core/plugins/antispam/` receives an event by defining a public method with the event's name; the arguments are those the call site passes, in order.

## `antispam.onAntispamDetector`

Fired from:

- [`core/components/com_support/admin/controllers/abusereports.php:390`](../../../core/components/com_support/admin/controllers/abusereports.php#L390)
- [`core/plugins/content/antispam/antispam.php:85`](../../../core/plugins/content/antispam/antispam.php#L85)

Listeners:

- `plg_antispam_akismet` — [`onAntispamDetector()`](../../../core/plugins/antispam/akismet/akismet.php)
- `plg_antispam_babajispam` — [`onAntispamDetector()`](../../../core/plugins/antispam/babajispam/babajispam.php)
- `plg_antispam_bayesian` — [`onAntispamDetector()`](../../../core/plugins/antispam/bayesian/bayesian.php)
- `plg_antispam_blacklist` — [`onAntispamDetector()`](../../../core/plugins/antispam/blacklist/blacklist.php)
- `plg_antispam_linkrife` — [`onAntispamDetector()`](../../../core/plugins/antispam/linkrife/linkrife.php)
- `plg_antispam_spamassassin` — [`onAntispamDetector()`](../../../core/plugins/antispam/spamassassin/spamassassin.php)

## `antispam.onAntispamTrain`

Fired from:

- [`core/components/com_support/admin/controllers/abusereports.php:281`](../../../core/components/com_support/admin/controllers/abusereports.php#L281) with `[ $reported->text, $isSpam, ]`
- [`core/plugins/content/antispam/antispam.php:117`](../../../core/plugins/content/antispam/antispam.php#L117) with `[ $content, true, ]`
- [`core/plugins/content/antispam/antispam.php:141`](../../../core/plugins/content/antispam/antispam.php#L141) with `[ $content, false, ]`

Listeners:

- `plg_antispam_akismet` — [`onAntispamTrain($content, $isSpam)`](../../../core/plugins/antispam/akismet/akismet.php)
- `plg_antispam_bayesian` — [`onAntispamTrain($content, $isSpam)`](../../../core/plugins/antispam/bayesian/bayesian.php)
- `plg_antispam_spamassassin` — [`onAntispamTrain($content, $isSpam)`](../../../core/plugins/antispam/spamassassin/spamassassin.php)
