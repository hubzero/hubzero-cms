<?php

defined('_HZEXEC_') or die();

$browser = new \Hubzero\Browser\Detector();
$cls = array(
    $this->direction,
    $browser->name(),
    $browser->name() . $browser->major()
);

Html::behavior('framework', true);
?>
<!DOCTYPE html>
<html dir="<?php echo $this->direction; ?>"
      lang="<?php echo $this->language; ?>"
      class="<?php echo implode(' ', $cls); ?>">
    <head>
        <meta name="viewport"
              content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no" />
        <?php
        $cssPath = $this->baseurl . '/templates/' . $this->template . '/css/component.css';
        $cssVersion = filemtime(__DIR__ . '/css/component.css');
        ?>
        <link rel="stylesheet" type="text/css" media="all"
              href="<?php echo $cssPath; ?>?v=<?php echo $cssVersion; ?>" />

        <jdoc:include type="head" />

        <!--[if lt IE 9]>
            <script type="text/javascript"
                    src="<?php echo $this->baseurl . '/templates/' . $this->template; ?>/js/html5.js"></script>
        <![endif]-->
    </head>
    <body id="component-body">
        <jdoc:include type="message" />
        <jdoc:include type="component" />
    </body>
</html>
