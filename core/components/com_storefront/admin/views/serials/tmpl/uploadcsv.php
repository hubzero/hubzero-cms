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
        window.parent.location='index.php?option=<?php echo $this->option; ?>' +
            '&controller=<?php echo $this->controller; ?>' +
            '&sId=<?php echo $this->sId; ?>';
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
                <?php echo Lang::txt('Upload a file with serial numbers') ?>
            </div>
        </fieldset>
    <?php } ?>
    <?php if ($this->getError()) { ?>
        <p class="error"><?php echo $this->getError(); ?></p>
    <?php } else { ?>
        <div class="col span12">
            <div class="current">
        <?php $insertedPlural = $this->inserted == 1 ? '' : 's'; ?>
                <p><?php echo $this->inserted; ?> serial number<?php echo $insertedPlural; ?> inserted.</p>

                <?php if (!empty($this->skipped)) { ?>
                    <?php $skippedCount = count($this->skipped); ?>
                    <?php $skippedPlural = $skippedCount == 1 ? '' : 's'; ?>
                    <p><?php echo $skippedCount; ?> duplicate serial number<?php echo $skippedPlural; ?> skipped.</p>
                <?php } ?>

                <?php if (!empty($this->ignored)) { ?>
                    <?php
                    $ignoredCount = count($this->ignored);
                    $ignoredPlural = $ignoredCount == 1 ? '' : 's';
                    $ignoredVerb = $ignoredCount > 1 ? 'were' : 'was';
                    ?>
                    <p><?php echo $ignoredCount; ?> serial number<?php echo $ignoredPlural; ?>
                        <?php echo $ignoredVerb; ?> ignored:</p>
                    <ul>
                        <?php
                        foreach ($this->ignored as $ignore) {
                            echo '<li>' . $ignore . '</li>';
                        }
                        ?>
                    </ul>
                <?php } ?>
            </div>
        </div>
    <?php } ?>
</form>