<?php

// phpcs:disable PSR1.Files.SideEffects

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

$numaff = 0;
$numnon = 0;

// Did we get any results back?
if ($this->citations) {
    // Set some vars
    $affiliated    = '';
    $nonaffiliated = '';

    $formatter = new \Components\Citations\Helpers\Format();
    $formatter->setTemplate($this->format);

    // Loop through the citations and build the HTML
    foreach ($this->citations as $cite) {
        $showLinks = ($cite->title && $cite->author && $cite->publisher) ? true : false;
        $formatted = $cite->formatted();

        if ($cite->doi && $cite->url) {
            $formatted = str_replace(
                'doi:' . $cite->doi,
                '<a href="' . $cite->url . '" rel="external">' . 'doi:' . $cite->doi . '</a>',
                $formatted
            );
        }

        $item  = "\t" . '<li>' . "\n";
        $item .= $formatted;
        $item .= "\t\t" . '<p class="details">' . "\n";
        if ($showLinks) {
            $item .= "\t\t\t" . '<a href="' . Route::url('index.php?option=com_citations&task=download&id=' . $cite->id
            . '&citationFormat=bibtex&no_html=1') . '" title="' . Lang::txt('PLG_PUBLICATION_CITATIONS_DOWNLOAD_BIBTEX')
            . '">BibTex</a> <span>|</span> ' . "\n";
            $item .= "\t\t\t" . '<a href="' . Route::url('index.php?option=com_citations&task=download&id=' . $cite->id
            . '&citationFormat=endnote&no_html=1') . '" title="' .
            Lang::txt('PLG_PUBLICATION_CITATIONS_DOWNLOAD_ENDNOTE') . '">EndNote</a>' . "\n";
        }
        if ($cite->eprint) {
            if ($cite->eprint) {
                $item .= "\t\t\t"
                    . ' <span>|</span> <a href="'
                    . stripslashes($cite->eprint)
                    . '">'
                    . Lang::txt('PLG_PUBLICATION_CITATIONS_ELECTRONIC_PAPER')
                    . '</a>'
                    . "\n";
            }
        }
        $item .= "\t\t" . '</p>' . "\n";
        $item .= "\t" . '</li>' . "\n";

        // Decide which group to add it to
        if ($cite->affiliated) {
            $affiliated .= $item;
            $numaff++;
        } else {
            $nonaffiliated .= $item;
            $numnon++;
        }
    }
}
?>
<h3 id="citations">
    <?php echo Lang::txt('PLG_PUBLICATION_CITATIONS'); ?>
    <?php
    $citBase = 'index.php?option=' . $this->option
        . '&id=' . $this->publication->id
        . '&active=citations&v=' . $this->publication->version_number;
    $nonaffUrl = Route::url($citBase . '#nonaffiliated');
    $affUrl = Route::url($citBase . '#affiliated');
    $nonaffLabel = Lang::txt('PLG_PUBLICATION_CITATIONS_NONAFF');
    $affLabel = Lang::txt('PLG_PUBLICATION_CITATIONS_AFF');
    ?>
    <span>
        <a href="<?php echo $nonaffUrl; ?>"><?php
            echo $nonaffLabel;
        ?> (<?php echo $numnon; ?>)</a> |
        <a href="<?php echo $affUrl; ?>"><?php
            echo $affLabel;
        ?> (<?php echo $numaff; ?>)</a>
    </span>
</h3>
<?php if ($this->citations) { ?>
    <?php if ($nonaffiliated) { ?>
        <h4 id="nonaffiliated"><?php echo Lang::txt('PLG_PUBLICATION_CITATIONS_NOT_AFFILIATED'); ?></h4>
        <ul class="citations results">
            <?php echo $nonaffiliated; ?>
        </ul>
    <?php } ?>
    <?php if ($affiliated) { ?>
        <h4 id="affiliated"><?php echo Lang::txt('PLG_PUBLICATION_CITATIONS_AFFILIATED'); ?></h4>
        <ul class="citations results">
            <?php echo $affiliated; ?>
        </ul>
    <?php } ?>
<?php } else { ?>
    <p><?php echo Lang::txt('PLG_PUBLICATION_CITATIONS_NO_CITATIONS_FOUND'); ?></p>
<?php }
