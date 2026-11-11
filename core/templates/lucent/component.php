<?php

use Hubzero\Facades\Html;

defined('_HZEXEC_') or die();

/**
 * What this template is given.
 *
 * @var \Hubzero\Document\Type\Html $this
 */

$browser = new \Hubzero\Browser\Detector();
$cls = [
    $this->direction,
    $browser->name(),
    $browser->name() . $browser->major(),
];

Html::behavior('framework', true);
?>
<!DOCTYPE html>
<html dir="<?php echo $this->direction; ?>"
      lang="<?php echo $this->language; ?>"
      class="<?php echo implode(' ', $cls); ?>">
    <head>
        <meta name="viewport"
              content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no" />
        <link rel="stylesheet" type="text/css" media="all"
              href="<?php echo $this->asset('css/component.css'); ?>" />

        <jdoc:include type="head" />

        <!--[if lt IE 9]>
            <script type="text/javascript"
                    src="<?php echo $this->asset('js/html5.js'); ?>"></script>
        <![endif]-->
    </head>
    <body id="component-body">
        <jdoc:include type="message" />
        <jdoc:include type="component" />
    </body>
</html>
