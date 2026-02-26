<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

use Hubzero\Facades\Lang;
use Hubzero\Facades\Route;
use Hubzero\Facades\User;

// No direct access.
defined('_HZEXEC_') or die();

if (!$this->sub) {
    $this->css();
}
$this->js();

$templates = $this->book->templates()
    ->whereEquals('state', \Components\Wiki\Models\Page::STATE_PUBLISHED)
    ->rows();

$url = Route::url($this->page->link('new'));
if (User::isGuest()) {
    $return = base64_encode(Route::url($this->page->link('new'), false, true));
    $url = Route::url('index.php?option=com_users&view=login&return=' . $return, false);
}
?>

<header id="<?php echo ($this->sub) ? 'sub-content-header' : 'content-header'; ?>">
    <h2><?php echo $this->escape($this->page->title); ?></h2>
</header><!-- /#content-header -->

<?php if (!$this->sub) { ?>
<section class="main section">
    <div class="aside">
        <?php
        $this->view('wikimenu')
            ->set('option', $this->option)
            ->set('controller', $this->controller)
            ->set('page', $this->page)
            ->set('task', $this->task)
            ->set('sub', $this->sub)
            ->display();
        ?>
    </div>
    <div class="subject">
<?php } ?>

        <?php
        $this->view('submenu', 'pages')
            //->setBasePath($this->base_path)
            ->set('option', $this->option)
            ->set('controller', $this->controller)
            ->set('page', $this->page)
            ->set('task', $this->task)
            ->set('sub', $this->sub)
            ->display();
        ?>

<?php if ($this->sub) { ?>
<section class="main section">
    <div class="section-inner">
<?php } ?>

        <p class="warning">
            <?php echo Lang::txt('COM_WIKI_WARNING_PAGE_DOES_NOT_EXIST_CREATE_IT', $url); ?>
        </p>
        <?php if ($templates->count()) { ?>
            <p>
                <?php echo Lang::txt('COM_WIKI_CHOOSE_TEMPLATE'); ?>
            </p>
            <ul>
                <?php foreach ($templates as $template) {
                    $tplate = stripslashes($template->get('pagename'));
                    $tplUrl = Route::url($this->page->link('new') . '&tplate=' . $tplate);
                    if (User::isGuest()) {
                        $tplReturn = base64_encode(
                            Route::url($this->page->link('new') . '&tplate=' . $tplate, false, true)
                        );
                        $tplUrl = Route::url(
                            'index.php?option=com_users&view=login&return=' . $tplReturn,
                            false
                        );
                    }
                    ?><li>
                        <a href="<?php echo $tplUrl; ?>">
                            <?php echo $this->escape(stripslashes($template->title)); ?>
                        </a>
                    </li>
                <?php } ?>
            </ul>
        <?php } ?>

    </div>
</section><!-- / .main section -->
