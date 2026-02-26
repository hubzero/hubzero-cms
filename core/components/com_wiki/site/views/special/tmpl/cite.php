<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

use Hubzero\Facades\Config;
use Hubzero\Facades\Date;
use Hubzero\Facades\Request;
use Hubzero\Facades\Route;

// No direct access.
defined('_HZEXEC_') or die();

$page = $this->book->pages()
    ->whereEquals('pagename', Request::getString('page', ''))
    ->whereEquals('path', Request::getString('scope', ''))
    ->row();

if ($v = Request::getInt('version', 0)) {
    $revision = $page->versions()
        ->whereEquals('id', $v)
        ->row();
} else {
    $revision = $page->version();
}

$yFormat = 'Y';
$apaFormat = 'Y, M d';
$apaFormatRetrieved = 'H:i, M d, Y';
$mlaFormat = 'd M. Y';
$mlaFormatRetrieved = 'd M. Y';
$mhraFormat = 'd M Y H:i';
$mhraFormatRetrieved = 'd M Y';
$cbeFormat = 'Y M d';
$bluebookFormat = 'M. d, Y';
$amaFormat = 'M d, Y, H:i';
$amaFormatRetrieved = 'M d, Y';

$now = Date::toSql();

$permalink = rtrim(Request::base(), '/') . '/'
    . ltrim(Route::url($page->link() . '&version=' . $revision->get('version')), '/');
?>
<div class="admon-note">
<p>
    <strong>IMPORTANT NOTE</strong>: Most educators and professionals do not consider it appropriate to use
    tertiary sources such as encyclopedias as a sole source for any information—citing an encyclopedia as an
    important reference in footnotes or bibliographies may result in censure or a failing grade. Wiki articles
    should be used for background information, as a reference for correct terminology and search terms, and as
    a starting point for further research.
</p>
<p>
    As with any community-built reference, there is a possibility for error in content&ndash;please check
    your facts against multiple sources and read our disclaimers for more information.
</p>
</div>
<div class="wiki-box highlight-box">
    <h3>Bibliographic details for "<?php echo $this->escape(stripslashes($page->get('title', '') ?? '')); ?>"</h3>
    <ul>
        <li>
            Page name: <?php echo $this->escape(stripslashes($page->get('pagename', '') ?? '')); ?>
        </li>
        <li>
            Author: <?php echo $this->escape(Config::get('sitename')); ?> contributors
        </li>
        <li>
            Publisher: <i><?php echo $this->escape(Config::get('sitename')); ?></i>
        </li>
        <li>
            Date of last revision: <?php echo $this->escape(stripslashes($revision->get('created', '') ?? '')); ?>
        </li>
        <li>
            Date retrieved: <?php echo $now; ?>
        </li>
        <li>
            Permanent link: <a href="<?php echo $permalink; ?>"><?php echo $permalink; ?></a>
        </li>
        <li>
            Primary contributors: <a href="<?php echo Route::url($page->link('history')); ?>">Revision history</a>
        </li>
        <li>
            Page version ID: <?php echo $this->escape($revision->get('id')); ?>
        </li>
    </ul>
    <p>
        Please remember to check your manual of style, standards guide or instructor's guidelines for the
        exact syntax to suit your needs.
    </p>
</div>

<?php
$citeTitle = $this->escape(stripslashes($page->get('title', '') ?? ''));
$citeSite = $this->escape(Config::get('sitename'));
$citeRevCreated = $revision->get('created');
$citeApaDate = Date::of($citeRevCreated)->format($apaFormat);
$citeMhraDate = Date::of($citeRevCreated)->format($mhraFormat);
$citeAmaDate = Date::of($citeRevCreated)->format($amaFormat);
$citeBluebookDate = Date::of($citeRevCreated)->format($bluebookFormat);
$citeApaRetr = Date::of($now)->format($apaFormatRetrieved);
$citeMlaRetr = Date::of($now)->format($mlaFormatRetrieved);
$citeMhraRetr = Date::of($now)->format($mhraFormatRetrieved);
$citeCbeDate = Date::of($now)->format($cbeFormat);
$citeAmaRetr = Date::of($now)->format($amaFormatRetrieved);
?>
<div class="wiki-box">
    <h3>Citation styles for "<?php echo $citeTitle; ?>"</h3>

    <h4>APA style</h4>
    <p>
        <?php echo $citeTitle; ?>. (<?php echo $citeApaDate; ?>).
        In <i><?php echo $citeSite; ?></i>.
        Retrieved <?php echo $citeApaRetr; ?>,
        from <a href="<?php echo $permalink; ?>"><?php echo $permalink; ?></a>
    </p>

    <h4>MLA style</h4>
    <p>
        <?php echo $citeSite; ?> contributors.
        "<?php echo $citeTitle; ?>."
        <i><?php echo $citeSite; ?></i>.
        <?php echo $citeSite; ?>, <?php echo $citeApaDate; ?>. Web. <?php echo $citeMlaRetr; ?>
    </p>

    <h4>MHRA style</h4>
    <p>
        <?php echo $citeSite; ?> contributors,
        '<?php echo $citeTitle; ?>,'
        <i><?php echo $citeSite; ?></i>, <?php echo $citeMhraDate; ?>,
        &lt;<a href="<?php echo $permalink; ?>"><?php echo $permalink; ?></a>&gt;
        [accessed <?php echo $citeMhraRetr; ?>]
    </p>

    <h4>Chicago style</h4>
    <p>
        <?php echo $citeSite; ?> contributors,
        "<?php echo $citeTitle; ?>,"
        <i><?php echo $citeSite; ?></i>,
        <a href="<?php echo $permalink; ?>"><?php echo $permalink; ?></a>
        (accessed <?php echo $citeMhraRetr; ?>).
    </p>

    <h4>CBE/CSE style</h4>
    <p>
        <?php echo $citeSite; ?> contributors.
        <?php echo $citeTitle; ?> [Internet].
        <?php echo $citeSite; ?>; <?php echo $citeBluebookDate; ?>
        [cited <?php echo $citeCbeDate; ?>].
        Available from: <a href="<?php echo $permalink; ?>"><?php echo $permalink; ?></a>.
    </p>

    <h4>Bluebook style</h4>
    <p>
        <?php echo $citeTitle; ?>,
        <a href="<?php echo $permalink; ?>"><?php echo $permalink; ?></a>
        (last visited <?php echo $citeBluebookDate; ?>).
    </p>

    <h4>Bluebook: Harvard JOLT style</h4>
    <p>
        <?php echo $citeSite; ?>,
        <i><?php echo $citeTitle; ?></i>,
        <a href="<?php echo $permalink; ?>"><?php echo $permalink; ?></a>
        (optional description here) (as of <?php echo $citeBluebookDate; ?>).
    </p>

    <h4>AMA style</h4>
    <p>
        <?php echo $citeSite; ?> contributors.
        <?php echo $citeTitle; ?>.
        <?php echo $citeSite; ?>. <?php echo $citeAmaDate; ?>.
        Available at <a href="<?php echo $permalink; ?>"><?php echo $permalink; ?></a>.
        Accessed <?php echo $citeAmaRetr; ?>.
    </p>

    <h4>BibTeX entry</h4>
<pre>
@misc{ wiki:xxx,
    author = "<?php echo $citeSite; ?>",
    title = "<?php echo $citeTitle; ?> --- <?php echo $citeSite; ?>",
    year = "<?php echo Date::of($revision->get('created'))->format($yFormat); ?>",
    url = "<?php echo $permalink; ?>",
    note = "[Online; accessed 1-October-2012]"
}
</pre>