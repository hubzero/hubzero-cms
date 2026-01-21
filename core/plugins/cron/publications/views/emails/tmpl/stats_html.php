<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

$dateFormat = 'M d, Y';

$baseManage = 'publications/submit';
$baseView = 'publications';

$base = trim(preg_replace('/\/administrator/', '', Request::base()), '/');

$mconfig = Component::params('com_members');
$pPath   = trim($mconfig->get('webpath'), DS);
$profileThumb = null;

// CSS
$backgroundColor = '#FFFFFF';
$introTextColor  = '#efd09c';
$headerBgColor   = '#000000';
$headerTextColor = '#CCCCCC';
$footerBgColor   = '#f8f8f8';
$borderColor     = '#cbb185';
$textColor       = '#616161';
$linkColor       = '#33a9cf';
$titleLinkColor  = '#333';
$footerTextColor = '#999999';
$boxBgColor      = '#f6eddd';

$append = '?from=' . $this->user->get('email');
$lastMonth = date('M Y', strtotime("-1 month"));

$profileLink = $this->user->link();
$profileThumb = '';
$thumbpath = substr(PATH_APP, strlen(PATH_ROOT))
    . '/site/members/'
    . Hubzero\Utility\Str::pad($this->user->get('id'), 5)
    . '/thumb.png';
if (file_exists(PATH_ROOT . $thumbpath)) {
    // picture() will return a /file/hash URL that is tied to session
    // so it doesn't really work in this case
    $profileThumb = rtrim(Request::root(), '/') . $thumbpath; //$this->user->picture();
}

// More publications?
$more = count($this->pubstats) - $this->limit;

?>
<!-- Start Header -->
<table class="tbl-header" width="100%" cellpadding="0" cellspacing="0" border="0">
    <tbody>
        <tr>
            <td width="10%" align="left" valign="bottom" nowrap="nowrap" class="sitename">
                <?php echo Config::get('sitename'); ?>
            </td>
            <td width="80%" align="left" valign="bottom" class="tagline mobilehide">
                <span class="home">
                    <a href="<?php echo Request::base(); ?>"><?php echo Request::base(); ?></a>
                </span>
                <br />
                <span class="description"><?php echo Config::get('MetaDesc'); ?></span>
            </td>
            <td width="10%" align="right" valign="bottom" nowrap="nowrap" class="component">
                Publications
            </td>
        </tr>
    </tbody>
</table>
<!-- End Header -->

<!-- Start Spacer -->
<table class="tbl-spacer" width="100%" cellpadding="0" cellspacing="0" border="0">
    <tbody>
        <tr>
            <td height="30"></td>
        </tr>
    </tbody>
</table>
<!-- End Spacer -->

<!-- ====== Header Table ====== -->
<?php
$headerStyle = 'background-color: ' . $headerBgColor . ';'
    . ' color: ' . $headerTextColor . ';'
    . ' border-right: 1px solid ' . $borderColor . ';'
    . ' border-left: 1px solid ' . $borderColor . ';'
    . ' border-top: 1px solid ' . $borderColor . ';';
?>
<table
    width="670"
    cellpadding="0"
    cellspacing="0"
    border="0"
    style="<?php echo $headerStyle; ?>"
    bgcolor="<?php echo $headerBgColor; ?>"
>
    <tbody>
        <tr>
            <td width="20" height="30"></td>
        <?php if ($this->image) { ?>
            <td width="55">
                <a href="<?php echo $base . $append; ?>">
                    <img width="55" border="0" src="<?php echo $this->image; ?>" alt="" />
                </a>
            </td>
            <td width="10"></td>
            <td width="425">
        <?php } else { ?>
            <td width="480">
        <?php } ?>
<?php
$userName = $this->user->get('name');
$introStyle = 'color: ' . $introTextColor
    . '; margin: 10px 0 3px 0; font-weight: bold;';
$siteName = Config::get('sitename');
$currentMonth = date('M Y');
?>
                <p style="<?php echo $introStyle; ?>">
                    <strong>Dear <?php echo $userName; ?>,</strong>
                </p>
                <p style="margin: 0; font-size: 12px;">
                    Here is a monthly usage report for your
                    published datasets
                    in <?php echo $siteName; ?>:
                    <?php echo $currentMonth; ?>
                </p>
            </td>
            <td width="40">
                <?php if ($profileThumb) { ?>
                    <a href="<?php echo $profileLink . '/profile' . $append; ?>">
                        <img width="30" border="0" src="<?php echo $profileThumb; ?>" alt="" />
                    </a>
                <?php } ?>
            </td>
            <td width="15"></td>
        </tr>
    </tbody>
</table>

<!-- Start Spacer -->
<table class="tbl-spacer" width="100%" cellpadding="0" cellspacing="0" border="0">
    <tbody>
        <tr>
            <td height="30"></td>
        </tr>
    </tbody>
</table>
<!-- End Spacer -->

<!-- ====== Start Content Table ====== -->
<?php
$contentTableStyle = 'background-color: ' . $backgroundColor . ';'
    . ' border-right: 1px solid ' . $borderColor . ';'
    . ' border-left: 1px solid ' . $borderColor . ';';
?>
<?php if ($more > 1) { ?>
    <table
        width="670"
        cellpadding="0"
        cellspacing="0"
        border="0"
        style="<?php echo $contentTableStyle; ?>"
        bgcolor="<?php echo $backgroundColor; ?>"
    >
        <tbody>
            <tr>
                <td width="25"></td>
                <td width="645">
                    <p>Latest usage statistics on your
                        <?php echo $this->limit; ?> top
                        publications:</p>
                </td>
            </tr>
        </tbody>
    </table>
<?php } ?>

<table
    width="670"
    cellpadding="0"
    cellspacing="0"
    border="0"
    style="<?php echo $contentTableStyle; ?>"
    bgcolor="<?php echo $backgroundColor; ?>"
>
    <tbody>
        <?php
        $i = 0;
        foreach ($this->pubstats as $stat) {
            $i++;

            if ($i > $this->limit) {
                break;
            }

            $sefManage = $baseManage . '/' . $stat->publication_id;
            $sefView   = $baseView . '/' . $stat->publication_id;

            $thumb = $base
                . '/'
                . $baseView
                . '/'
                . $stat->publication_id
                . '/'
                . $stat->publication_version_id
                . '/Image:thumb';
            $link  = $base . '/' . trim($sefView, '/') . $append;
            $manageLink  = $base . '/' . trim($sefManage, '/') . $append;

            $titleStyle = 'color: ' . $titleLinkColor
                . '; text-decoration: none;';
            $titlePStyle = 'color: ' . $titleLinkColor
                . '; font-weight:bold;';
            $spacerStyle = 'color: ' . $backgroundColor
                . ' !important;background-color: '
                . $backgroundColor . ';';
            $spacerDiv = '<div style="height: 10px !important;'
                . ' visibility: hidden;">&nbsp;</div>';
            $actionStyle = 'color: #777; font-style: italic;'
                . 'text-align: right;';
            $actionLinkStyle = 'color: ' . $linkColor . ';';
            $spacerDivStyle = 'height: 25px !important;'
                . ' visibility: hidden;'
                . ' color: ' . $backgroundColor;
            ?>
            <tr>
                <td width="25"></td>
                <td width="75">
                    <a href="<?php echo $link; ?>">
                        <img
                            width="55"
                            src="<?php echo $thumb; ?>"
                            style="width:55px;"
                            alt=""
                        >
                    </a>
                </td>
                <td width="545">
                    <p style="<?php echo $titlePStyle; ?>">
                        <a
                            href="<?php echo $link; ?>"
                            style="<?php echo $titleStyle; ?>"
                        ><?php echo $stat->title; ?></a>
                    </p>

                    <table cellpadding="0" cellspacing="0" border="0" style="font-size: 12px; padding: 0; margin: 0;">
                        <tbody>
                            <tr style="padding: 0; margin: 0;">
                                <td width="265">Page views last month:</td>
                                <td width="50"
                                    style="color: <?php echo $titleLinkColor; ?>; font-weight:bold;"
                                    ><?php echo $stat->monthly_views; ?></td>
                                <td width="30"></td>
                                <td width="200"></td>
                            </tr>
                            <tr>
                                <td
                                    height="10"
                                    style="<?php echo $spacerStyle; ?>"
                                ><?php echo $spacerDiv; ?></td>
                            </tr>
                            <tr style="padding: 0; margin: 0;">
                                <td width="265">Downloads last month:</td>
                                <td width="50"
                                    style="color: <?php echo $titleLinkColor; ?>; font-weight:bold;"
                                    ><?php echo $stat->monthly_primary; ?></td>
                                <td width="30"></td>
                                <td width="200" style="padding: 0; margin: 0;">
                                </td>
                            </tr>
                            <tr>
                                <td
                                    height="10"
                                    style="<?php echo $spacerStyle; ?>"
                                ><?php echo $spacerDiv; ?></td>
                                <td></td>
                                <td></td>
                                <td></td>
                            </tr>
                            <tr style="padding: 0; margin: 0;">
                                <td width="265">Total downloads to date:</td>
                                <td width="50"
                                    style="color: <?php echo $titleLinkColor; ?>; font-weight:bold;"
                                    ><?php echo $stat->total_primary; ?></td>
                                <td width="30"></td>
                                <td
                                    width="200"
                                    style="<?php echo $actionStyle; ?>"
                                >
                                    <a
                                        href="<?php echo $link; ?>"
                                        style="<?php echo $actionLinkStyle; ?>"
                                    >View publication</a>
                                    |
                                    <a
                                        href="<?php echo $manageLink; ?>"
                                        style="<?php echo $actionLinkStyle; ?>"
                                    >Manage</a>
                                </td>
                            </tr>
                        </tbody>
                    </table>

                </td>
                <td width="25"></td>
            </tr>
            <tr>
                <td width="25" height="25">
                    <div style="<?php echo $spacerDivStyle; ?>">
                        &nbsp;
                    </div>
                </td>
                <td width="75"></td>
                <td width="545"></td>
                <td width="25"></td>
            </tr>
            <?php
        }
        ?>
    </tbody>
</table>
<!-- ====== End Content Table ====== -->

<!-- Start Spacer -->
<table class="tbl-spacer" width="100%" cellpadding="0" cellspacing="0" border="0">
    <tbody>
        <tr>
            <td height="30"></td>
        </tr>
    </tbody>
</table>
<!-- End Spacer -->

<!-- ====== All datasets table ====== -->
<table
    width="670"
    cellpadding="0"
    cellspacing="0"
    border="0"
    style="<?php echo $contentTableStyle; ?>"
    bgcolor="<?php echo $backgroundColor; ?>"
>
    <tbody>
        <tr>
<?php
$allDatasetsStyle = 'background-color: ' . $backgroundColor
    . '; text-align: center; font-size: 12px;';
$totalDownloads = $this->totals->all_total_primary;
$impactLink = $profileLink . '/impact' . $append;
$impactLinkStyle = 'font-weight: bold; color: ' . $linkColor;
?>
            <td style="<?php echo $allDatasetsStyle; ?>">
                <p style="font-size: 12px; margin: 0">
                    All of your published datasets have been
                    downloaded a total of
                    <span style="font-weight: bold;">
                        <?php echo $totalDownloads; ?>
                    </span>
                    times to date.
                    <a
                        href="<?php echo $impactLink; ?>"
                        style="<?php echo $impactLinkStyle; ?>"
                    >View all usage</a>
                <p>
            </td>
        </tr>
    </tbody>
</table>
<!-- ====== End All datasets table ====== -->

<!-- Start Spacer -->
<table class="tbl-spacer" width="100%" cellpadding="0" cellspacing="0" border="0">
    <tbody>
        <tr>
            <td height="30"></td>
        </tr>
    </tbody>
</table>
<!-- End Spacer -->

<!-- ====== Summary table ====== -->
<table
    width="670"
    cellpadding="0"
    cellspacing="0"
    border="0"
    style="<?php echo $contentTableStyle; ?>"
    bgcolor="<?php echo $backgroundColor; ?>"
>
    <tbody>
        <tr>
            <td width="25"></td>
            <td width="620">
<?php
$boxStyle = 'font-size: 12px;'
    . ' line-height: 24px;'
    . ' color: #666666;'
    . ' font-family: \'Helvetica Neue\','
    . ' Arial, Helvetica, Geneva, sans-serif;'
    . ' background-color: ' . $boxBgColor . ';'
    . ' padding: 10px;'
    . ' border-radius:6px 6px 6px 6px;'
    . ' -moz-border-radius: 6px 6px 6px 6px;'
    . ' -webkit-border-radius:6px 6px 6px 6px;'
    . ' -webkit-font-smoothing: antialiased;'
    . ' text-align: center;';

$siteNameForSummary = Config::get('sitename');

$impactEnabled = Plugin::isEnabled('members', 'impact');
$ctaHref = $impactEnabled
    ? $profileLink . '/impact' . $append
    : $base . '/publications/submit' . $append;

$ctaStyle = 'color: #ffffff;'
    . ' background-color: #000000;'
    . ' padding: 5px 10px;'
    . ' border-radius:6px 6px 6px 6px;'
    . ' -moz-border-radius: 6px 6px 6px 6px;'
    . ' text-decoration: none;';
?>
                <div style="<?php echo $boxStyle; ?>">
                    <p style="margin: 0;">
                        Publishing your data
                        on <?php echo $siteNameForSummary; ?>
                        increases access to and impact of
                        your research!
                    </p>
                    <div style="">
                        <a
                            href="<?php echo $ctaHref; ?>"
                            style="<?php echo $ctaStyle; ?>"
                        >View all publications and
                            publish more data</a>
                    </div>
                </div>
            </td>
            <td width="25"></td>
        </tr>
    </tbody>
</table>

<!-- Start Spacer -->
<table class="tbl-spacer" width="100%" cellpadding="0" cellspacing="0" border="0">
    <tbody>
        <tr>
            <td height="30"></td>
        </tr>
    </tbody>
</table>
<!-- End Spacer -->

<!-- ====== Footer Table ====== -->
<?php
$footerStyle = '-webkit-font-smoothing: antialiased;'
    . ' background-color: ' . $footerBgColor . ';'
    . ' color: ' . $borderColor . ';'
    . ' border-right: 1px solid ' . $borderColor . ';'
    . ' border-left: 1px solid ' . $borderColor . ';'
    . ' border-bottom: 1px solid ' . $borderColor . ';';
$profileUrl = $profileLink . '/profile' . $append;
$footerPStyle = 'text-align: right;'
    . ' font-size: 12px;'
    . ' color: ' . $footerTextColor . ';'
    . ' margin: 15px 0;';
$footerLinkStyle = 'color: ' . $linkColor . ';';
?>
<table
    width="670"
    cellpadding="0"
    cellspacing="0"
    border="0"
    style="<?php echo $footerStyle; ?>"
    bgcolor="<?php echo $footerBgColor; ?>"
>
    <tbody>
        <tr>
            <td width="25"></td>
            <td width="620">
                <p style="<?php echo $footerPStyle; ?>">
                    To unsubscribe, adjust
                    "Receive monthly usage reports
                    and other news" setting on your
                    profile at
                    <a
                        href="<?php echo $profileUrl; ?>"
                        style="<?php echo $footerLinkStyle; ?>"
                    ><?php echo $base; ?></a>
                </p>
            </td>
            <td width="25"></td>
        </tr>
    </tbody>
</table>

<!-- Start Spacer -->
<table class="tbl-spacer" width="100%" cellpadding="0" cellspacing="0" border="0">
    <tbody>
        <tr>
            <td height="30"></td>
        </tr>
    </tbody>
</table>
<!-- End Spacer -->
