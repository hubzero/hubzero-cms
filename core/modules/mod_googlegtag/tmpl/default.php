<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2022 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// no direct access
defined('_HZEXEC_') or die();

/**
 * What this template is given.
 *
 * @var \Hubzero\Module\Module $this
 */
?>
<!-- Global site tag (gtag.js) - Google Analytics -->
<?php $trackingID = $this->params->get("trackingID"); ?>
<script async
    src="https://www.googletagmanager.com/gtag/js?id=<?php echo $trackingID; ?>"
></script>
<script>
    window.dataLayer = window.dataLayer || [];
    function gtag(){dataLayer.push(arguments);}
    gtag('js', new Date());

    gtag('config', '<?php echo $this->params->get("trackingID"); ?>');
</script>
