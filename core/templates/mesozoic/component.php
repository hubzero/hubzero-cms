<?php

use Hubzero\Facades\Html;
use Hubzero\Facades\Request;

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

// A page shown in a frame as a preview of itself (the contribution review step,
// a tool's preview) should look like the page it previews, and those styles are
// in main.css; component.css alone left its tabs and tags as bare lists. It goes
// last so the page's own rules win, among them a heading's line height.
$preview = (Request::getCmd('mode') == 'preview');
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
        <?php if ($preview) : ?>
            <link rel="stylesheet" type="text/css" media="screen"
                  href="<?php echo $this->asset('less/main.css'); ?>" />
        <?php endif; ?>

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
