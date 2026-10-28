<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

$tmpl = Request::getCmd('tmpl', '');

$text = 'Upload a CSV file';

if ($tmpl != 'component') {
    Toolbar::title(Lang::txt('COM_STOREFRONT') . ': ' . $text, 'storefront');
}

Html::behavior('framework');
?>

<script type="text/javascript">
    function closeAndRefresh(pressbutton)
    {
<?php
$redirectUrl = Route::url(
    "index.php?option=" . $this->option
    . "&controller=" . $this->controller
    . "&id=" . $this->sId
);
?>
        window.parent.location='<?php echo $redirectUrl; ?>';
    }

    jQuery(document).ready(function($){
        $(window).on('keypress', function(){
            if (window.event.keyCode == 13) {
                submitbutton('uploadcsv');
            }
        })
    });
</script>

<?php $formAction = Route::url('index.php?option=' . $this->option); ?>
<form action="<?php echo $formAction; ?>" method="post" name="adminForm" id="component-form">
    <?php if ($tmpl == 'component') { ?>
        <fieldset>
            <div class="configuration" >
                <div class="fltrt configuration-options">
                    <button type="button" onclick="closeAndRefresh();"><?php echo Lang::txt('Close');?></button>
                </div>
                <?php echo Lang::txt('Upload a file with users') ?>
            </div>
        </fieldset>
    <?php } ?>
    <?php if ($this->getError()) { ?>
        <p class="error"><?php echo $this->getError(); ?></p>
    <?php } else { ?>
        <div class="col span12">
            <div class="current">
                <p><?php echo $this->inserted; ?> user<?php echo $this->inserted == 1 ? '' : 's'; ?> inserted.</p>

                <?php
                if (!empty($this->skipped)) {
                    ?>
                    <?php $skippedCount = count($this->skipped); ?>
                <p><?php echo $skippedCount; ?> duplicate user<?php echo $skippedCount == 1 ? '' : 's'; ?> skipped.</p>
                    <?php
                }
                ?>

                <?php
                if (!empty($this->ignored)) {
                    ?>
                    <?php
                    $ignoredCount = count($this->ignored);
                    $ignoredPlural = $ignoredCount == 1 ? '' : 's';
                    $ignoredVerb = $ignoredCount > 1 ? 'were' : 'was';
                    ?>
                    <p><?php echo $ignoredCount; ?> user<?php echo $ignoredPlural; ?>
                        could not be found and <?php echo $ignoredVerb; ?> ignored:<br>

                    <?php
                    $i = 0;
                    foreach ($this->ignored as $ignore) {
                        if ($i) {
                            echo ', ';
                        }
                        echo $ignore;
                        $i = 1;
                    }
                    ?>

                    </p>
                    <?php
                }
                ?>
            </div>
        </div>
    <?php } ?>
</form>