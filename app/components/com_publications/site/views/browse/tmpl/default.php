<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

$no_html = Request::getInt('no_html');

// No direct access
defined('_HZEXEC_') or die();

$database = App::get('db');

$this->css()
     ->css('intro')
     ->js();
?>

<?php if (!$no_html) { ?>

  <?php include_once Component::path('com_publications') . DS . 'site' . DS . 'views' . DS . 'publications' . DS . 'tmpl' . DS . 'intro.php';  ?>

  <section class="live-update">
    <div aria-live="polite" id="live-update-wrapper">
<?php } ?>

      <form action="<?php echo Route::url('index.php?option=' . $this->option . '&task=browse'); ?>" id="resourcesform" method="get" data-target="#results-container">
        <div class="container browse-resources-wrapper">
          <div class="page-filter-wrapper">
            <header class="page-filter">
              <div class="search-input-wrapper">
                <fieldset class="entry-search">
                  <legend><?php echo Lang::txt('Search'); ?></legend>
                  <label for="entry-search-field"><?php echo Lang::txt('Enter keyword or phrase'); ?></label>
                  <input type="text" name="search" id="entry-search-field" value="<?php echo $this->escape($this->filters['search']); ?>" placeholder="<?php echo Lang::txt('Enter keyword or phrase'); ?>" />
                </fieldset>
                <input class="entry-search-submit" type="submit" value="<?php echo Lang::txt('Search'); ?>" />
              </div>
            </header>
          </div>

          <div class="container" id="results-container" aria-live="polite">

            <?php
            if ($this->results && $this->results->count() > 0)
            {
              // Display List of items
              $this->view('_list')
                 ->set('results', $this->results)
                 ->set('filters', $this->filters)
                 ->set('config', $this->config)
                 ->display();

              $this->pageNav->setAdditionalUrlParam('tag', $this->filters['tag']);
              $this->pageNav->setAdditionalUrlParam('category', $this->filters['category']);
              $this->pageNav->setAdditionalUrlParam('sortby', $this->filters['sortby']);

              echo $this->pageNav->render();

              echo '<div class="clear"></div>';
            } else { ?>
              <p class="warning"><?php echo Lang::txt('COM_PUBLICATIONS_NO_RESULTS'); ?></p>
            <?php } ?>

            <div class="clearfix"></div>
          </div><!-- / .container -->
        </div>
      </form>

    <?php if (!$no_html) { ?>
        </div> <!-- .live-update-wrapper -->
      </section>
    <?php } ?>
