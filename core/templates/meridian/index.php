<?php

use Hubzero\Facades\App;
use Hubzero\Facades\Component;
use Hubzero\Facades\Html;
use Hubzero\Facades\Lang;
use Hubzero\Facades\Request;
use Hubzero\Facades\Route;
use Hubzero\Facades\User;

// No Direct Access stuff, whatever, just keep it there
defined('_HZEXEC_') or die();

// What the hub has chosen for this style in the admin. Both off by default:
// a help pane needs a module in the position before it shows anything, and a
// register link needs registration to be open.
$tplSettingDisplayHelp = (bool) $this->params->get('helpPane', 0);
$tplSettingRegisterLink = (bool) $this->params->get('registerLink', 0);

// The two knobs worth having
//
// A hub that keeps this template will still want it to be its own, and colour
// is the thing everybody changes first. The stylesheet reaches for every
// accent-bearing value through a custom property with the teal as its
// fallback, so setting a handful of properties here repoints the whole page
// without a rebuild - and leaving them unset leaves the stylesheet alone.
//
// Only the one colour is asked for. Palette works out the shades and the ink
// that goes over them, and works them out so they cannot fall below 4.5:1 -
// see palette.php for why that is the template's job rather than the admin's.
require_once __DIR__ . '/palette.php';

$tplAccent = new \Templates\Meridian\Palette($this->params->get('accentColor', ''));
$tplStyle  = $tplAccent->properties();

$tplRadius = $this->params->get('cornerStyle', '');

if (in_array($tplRadius, array('0', '8', '18'), true)) {
    $tplStyle['--hub-radius'] = $tplRadius . 'px';
}

// Built here rather than echoed a line at a time in the head, so that what
// reaches the page is one string whose every part came from either Palette,
// which only ever returns sprintf('#%02X%02X%02X'), or the list above.
$tplStyleRule = '';

foreach ($tplStyle as $tplProperty => $tplValue) {
    $tplStyleRule .= $tplProperty . ':' . $tplValue . ';';
}

if ($tplStyleRule) {
    $tplStyleRule = ':root{' . $tplStyleRule . '}';
}

$menu = App::get('menu');
Html::behavior('framework', true);
Html::behavior('modal');

// Add js
$jsBase = $this->baseurl . '/templates/' . $this->template;
$this->addScript($jsBase . '/js/core.js?v=' . filemtime(__DIR__ . '/js/core.js'));
$this->addScript($jsBase . '/js/hub.js?v=' . filemtime(__DIR__ . '/js/hub.js'));

$active = '';
if ($menu->getActive()) {
    $active = ' active-' . $menu->getActive()->alias;
}

// Body class setting
$bodyClass = 'page-' . Request::getCmd('option', '') . ' ' . Request::getCmd('option', '') . $active;

// Figure out if this page is a home page
$isFrontPage = ($menu->getActive() == $menu->getDefault());

// A hub can build a front page out of module positions rather than leaving it
// to the menu item's component. It has one when it has put something in any of
// the positions home.php lays out.
$hasHomePage = $isFrontPage && $this->countModules('home-1 or home-2 or home-3 or home-4');

// Current page (used in the login link)
$url = Request::getString('REQUEST_URI', '', 'server');

if ($isFrontPage) {
    $bodyClass = 'page-home';

    // Add homepage-specific stuff
    // $this->addScript(
    //     $jsBase . '/js/pages/home.js?v=' . filemtime(__DIR__ . '/js/pages/home.js')
    // );
}

$browser = new \Hubzero\Browser\Detector();
$cls = array(
    $this->direction,
    $browser->name(),
    $browser->name() . $browser->major()
);

$this->setTitle(Config::get('sitename') . ' - ' . $this->getTitle());
?>
<!DOCTYPE html>
<html dir="<?php echo $this->direction; ?>"
      lang="<?php echo $this->language; ?>"
      class="<?php echo implode(' ', $cls); ?>">
<head>
    <?php if ($this->countModules('html-head')) : ?>
        <jdoc:include type="modules" name="html-head" />
    <?php endif; ?>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <link rel="stylesheet" type="text/css" media="screen"
          href="<?php echo $this->baseurl . '/templates/' . $this->template; ?>/less/main.css" />
    <?php if ($tplStyleRule) : ?>
    <style><?php echo $tplStyleRule; ?></style>
    <?php endif; ?>

    <jdoc:include type="head" />
</head>

<body class="<?php echo $bodyClass; ?>">
<a href="#maincontent" class="vh">Skip to main content</a>

<section class="hub-top">
    <jdoc:include type="modules" name="notices" />

    <?php
    if ($tplSettingDisplayHelp) {
        ?>
        <jdoc:include type="modules" name="helppane"/>
        <?php
    }
    ?>

    <?php if ($this->getBuffer('message')) : ?>
        <jdoc:include type="message" />
    <?php endif; ?>
</section>


<div class="wrap">
    <?php // On the front page the header and the hero share one ground ?>
    <?php if ($isFrontPage && $this->countModules('hero')) : ?>
    <div class="home-head">
    <?php endif; ?>

    <div class="page-head">
        <div class="inner">
            <header>
                <div class="inner">
                    <div class="logo">
                        <a href="<?php echo Request::root(); ?>"
                           title="<?php echo Config::get('sitename'); ?>">
                            <?php echo Config::get('sitename'); ?>
                        </a>
                    </div>

                    <nav class="nav">
                        <?php // main navigation bar ?>
                        <nav role="navigation" aria-label="Primary navigation"
                             title="Primary navigation" class="main-nav">
                            <jdoc:include type="modules" name="user3" />
                        </nav>

                        <nav class="subnav">
                            <ul>
                                <?php
                                // Search icon SVG path data
                                $searchSvg = 'M241.877 230.131l-72.929-72.929c13.973-16.67 '
                                    . '22.465-38.004 22.465-61.061 0-52.204-43.503-95.707-95.707'
                                    . '-95.707S0 43.938 0 96.142s43.503 95.707 95.707 95.707c'
                                    . '22.621 0 43.573-8.179 60.104-21.682l72.145 72.145c1.74 '
                                    . '1.74 3.48 1.74 6.96 1.74s5.22 0 6.96-1.74c3.482-3.48 '
                                    . '3.482-8.7.001-12.181zm-146.17-55.683c-43.503 0-78.305'
                                    . '-34.802-78.305-78.305s34.802-78.305 78.305-78.305 '
                                    . '78.305 34.802 78.305 78.305-34.802 78.305-78.305 78.305z';
                                ?>
                                <li>
                                    <a class="search-trigger" role="button"
                                       href="/search" title="Open site search">
                                        <svg xmlns="http://www.w3.org/2000/svg"
                                             viewBox="0 0 244.487 244.487">
                                            <path d="<?php echo $searchSvg; ?>"/>
                                        </svg>
                                        <span class="vh">Search</span>
                                    </a>
                                </li>
                                <?php if (!User::isGuest()) { ?>
                                    <?php
                                    $userId = User::get('id');
                                    $profileUrl = Route::url(
                                        'index.php?option=com_members&id=' . $userId
                                    );
                                    $dashboardUrl = Route::url(
                                        'index.php?option=com_members&id=' . $userId
                                        . '&active=dashboard'
                                    );
                                    $profileEditUrl = Route::url(
                                        'index.php?option=com_members&id=' . $userId
                                        . '&active=profile'
                                    );
                                    $logoutUrl = Route::url(
                                        'index.php?option=com_users&view=logout'
                                    );
                                    $userName = stripslashes(User::get('name'));
                                    $userLogin = stripslashes(User::get('username'));
                                    ?>
                                    <li>
                                        <div class="menu-button-links user-account">
                                            <button type="button" class="" id="menubutton"
                                                    aria-haspopup="true" aria-controls="menu2">
                                                <span class="user-image">
                                                    <img src="<?php echo User::picture(); ?>"
                                                         alt="<?php echo User::get('name'); ?>" />
                                                </span>
                                            </button>

                                            <ul id="menu2" role="menu"
                                                aria-labelledby="menubutton"
                                                class="account-details">
                                                <li role="none">
                                                    <a role="menuitem" class="user-name cf"
                                                       href="<?php echo $profileUrl; ?>">
                                                        <?php
                                                        echo $userName . ' (' . $userLogin . ')';
                                                        ?>
                                                        <br>
                                                        <span><?php
                                                            echo User::get('email');
                                                        ?></span>
                                                    </a>
                                                </li>
                                                <li role="none" class="sub" id="account-dashboard">
                                                    <a role="menuitem"
                                                       href="<?php echo $dashboardUrl; ?>">
                                                        <span><?php
                                                            echo Lang::txt('TPL_ACCOUNT_DASHBOARD');
                                                        ?></span>
                                                    </a>
                                                </li>
                                                <li role="none" class="sub" id="account-profile">
                                                    <a role="menuitem"
                                                       href="<?php echo $profileEditUrl; ?>">
                                                        <span><?php
                                                            echo Lang::txt('TPL_ACCOUNT_PROFILE');
                                                        ?></span>
                                                    </a>
                                                </li>
                                                <li role="none" class="sub" id="account-logout">
                                                    <a role="menuitem"
                                                       href="<?php echo $logoutUrl; ?>">
                                                        <span><?php
                                                            echo Lang::txt('TPL_LOGOUT');
                                                        ?></span>
                                                    </a>
                                                </li>
                                            </ul>
                                        </div>
                                    </li>
                                <?php } else { ?>
                                    <li>
                                        <?php
                                        $loginUrl = Route::url(
                                            'index.php?option=com_users&view=login'
                                        );
                                        ?>
                                        <a href="<?php echo $loginUrl; ?>"
                                           title="<?php echo Lang::txt('TPL_LOGIN'); ?>"
                                           class="user-account-link loggedout">
                                            <?php echo Lang::txt('TPL_LOGIN'); ?>
                                        </a>
                                    </li>
                                    <?php
                                    $allowReg = Component::params('com_members')
                                        ->get('allowUserRegistration');
                                    ?>
                                    <?php if ($tplSettingRegisterLink && $allowReg) : ?>
                                        <li>
                                            <a href="<?php
                                                echo Route::url('index.php?option=com_register');
                                            ?>"
                                               title="<?php echo Lang::txt('TPL_SIGNUP'); ?>"
                                               class="user-account-link loggedout">
                                                <?php echo Lang::txt('TPL_SIGNUP'); ?>
                                            </a>
                                        </li>
                                    <?php endif; ?>
                                <?php } ?>
                                <?php if ($this->countModules('helppane') && $tplSettingDisplayHelp) : ?>
                                    <?php
                                    // set module REPORTPROBLEMS parameter to work with .helpme
                                    $supportUrl = Route::url('index.php?option=com_support');
                                    ?>
                                    <li class="subnav-helpme helpme">
                                        <a class="nav-btn"
                                           href="<?php echo $supportUrl; ?>"
                                           title="<?php echo Lang::txt('Help'); ?>">
                                            <span><?php echo Lang::txt('Help'); ?></span>
                                        </a>
                                    </li>
                                <?php endif; ?>
                            </ul>
                        </nav>
                    </nav>

                    <nav class="mobile-menu" role="navigation"
                         aria-label="Mobile navigation" title="Mobile navigation">
                        <button><span>Menu</span></button>
                    </nav>
                </div>
            </header>
        </div>
    </div>

    <?php if ($isFrontPage && $this->countModules('hero')) : ?>
        <div class="hero" role="region" aria-label="<?php echo Lang::txt('TPL_HERO'); ?>">
            <div class="inner">
                <jdoc:include type="modules" name="hero" />
            </div>
        </div>
    </div><!-- / .home-head -->
    <?php endif; ?>

    <?php if ($this->countModules('banner')) : ?>
        <div class="page-banner">
            <jdoc:include type="modules" name="banner" />
        </div>
    <?php endif; ?>

    <?php if (!$isFrontPage && $this->countModules('breadcrumbs')) : ?>
        <div class="page-trail">
            <div class="inner">
                <jdoc:include type="modules" name="breadcrumbs" />
            </div>
        </div>
    <?php endif; ?>

    <?php if ($this->countModules('welcome or introblock')) : ?>
        <div class="page-intro">
            <div class="inner">
                <jdoc:include type="modules" name="welcome" />
                <jdoc:include type="modules" name="introblock" />
            </div>
        </div>
    <?php endif; ?>

    <main id="maincontent" class="page">
        <div class="inner<?php if ($this->countModules('left or right')) {
            echo ' withmenu';
                         } ?>">
            <?php if ($this->countModules('left or right')) : ?>
            <section class="main section">
                <div class="section-inner<?php if ($this->countModules('left or right')) {
                    echo ' hz-layout-with-aside';
                                         } ?>">
            <?php endif; ?>

                    <?php if ($this->countModules('left')) : ?>
                        <aside class="aside">
                            <jdoc:include type="modules" name="left" />
                        </aside>
                    <?php endif; ?>

                    <?php if ($this->countModules('left or right')) : ?>
                    <div class="subject">
                    <?php endif; ?>

                        <!-- start component output -->
                        <?php if ($hasHomePage) : ?>
                            <?php require __DIR__ . '/home.php'; ?>
                        <?php else : ?>
                            <jdoc:include type="component" />
                        <?php endif; ?>
                        <!-- end component output -->

                        <?php if ($this->countModules('left or right')) : ?>
                    </div><!-- / .subject -->
                        <?php endif; ?>

                    <?php if ($this->countModules('right')) : ?>
                        <aside class="aside">
                            <jdoc:include type="modules" name="right" />
                        </aside>
                    <?php endif; ?>

                    <?php if ($this->countModules('left or right')) : ?>
                </div>
            </section><!-- / .main section -->
                    <?php endif; ?>
        </div><!-- / .inner -->
    </main>
</div>

<?php if ($this->countModules('footer')) : ?>
    <footer class="page-footer">
        <div class="inner">
            <jdoc:include type="modules" name="footer" />
        </div>
    </footer>
<?php endif; ?>

<section class="dialog-backdrop">
    <div id="big-search" class="template-panel" aria-label="search popup"
         role="dialog" aria-modal="true" aria-labelledby="search-label">
        <div class="inner">
            <h2 class="vh" id="search-label">Search</h2>
            <jdoc:include type="modules" name="search" />
        </div>
        <button class="close">
            <span><?php echo Lang::txt('TPL_SEARCH_CLOSE'); ?></span>
        </button>
    </div>
</section>

<jdoc:include type="modules" name="endpage" />
</body>

</html>
