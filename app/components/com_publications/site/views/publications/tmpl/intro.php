<?php
/**
* @package    hubzero-cms
* @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
* @license    http://opensource.org/licenses/MIT MIT
*/
// no direct access
defined('_HZEXEC_') or die();

$this->css()
->css('intro')
->js()
->js('intro');
?>

<div class="page-header">
  <div class="heading">
    <h1>Open. Adaptable. Free.</h1>

    <h2>Resources created by our members and partners.</h2>
  </div>

  <div class="intro">
    <p>QUBES hosts hundreds of <strong>teaching materials</strong>, <strong>reference materials</strong>, and <strong>cloud-based software</strong> free to use and adapts using open Creative Commons licenses. <a class="modal-link" href="#modal"><span class="info-icon"> <svg viewbox="0 0 64 48" xmlns="http://www.w3.org/2000/svg"> <path d="M9.9,33.3c1.3,2.9,3,5.4,5.2,7.6c2.2,2.2,4.7,3.9,7.6,5.2c2.9,1.3,6,1.9,9.3,1.9c3.3,0,6.4-0.6,9.3-1.9
      c2.9-1.3,5.4-3,7.6-5.2c2.2-2.2,3.9-4.7,5.2-7.6c1.3-2.9,1.9-6,1.9-9.3c0-3.3-0.6-6.4-1.9-9.3c-1.3-2.9-3-5.4-5.2-7.6
      c-2.2-2.2-4.7-3.9-7.6-5.2C38.4,0.6,35.3,0,32,0c-3.3,0-6.4,0.6-9.3,1.9c-2.9,1.3-5.4,3-7.6,5.2c-2.2,2.2-3.9,4.7-5.2,7.6
      C8.6,17.6,8,20.7,8,24C8,27.3,8.6,30.4,9.9,33.3z M25.2,17.1c0-0.6,0.3-0.9,0.9-0.9h9.1c0.2,0,0.4,0.1,0.6,0.3
      c0.2,0.2,0.3,0.4,0.3,0.6v17.3h2.3c0.2,0,0.4,0.1,0.6,0.3c0.2,0.2,0.3,0.4,0.3,0.6v4.2c0,0.2-0.1,0.4-0.3,0.6
      c-0.2,0.2-0.4,0.3-0.6,0.3H26.3c-0.2,0-0.4-0.1-0.6-0.3c-0.2-0.2-0.3-0.4-0.3-0.6v-4.2c0-0.2,0.1-0.4,0.3-0.6
      c0.2-0.2,0.4-0.3,0.6-0.3h2.4V22.1h-2.6c-0.6,0-0.9-0.3-0.9-0.9V17.1z M28.6,7.6c0-0.3,0.1-0.5,0.3-0.6c0.2-0.2,0.4-0.3,0.7-0.3h5.6
      c0.2,0,0.4,0.1,0.6,0.3c0.2,0.2,0.3,0.4,0.3,0.6v4.9c0,0.3-0.1,0.5-0.3,0.7c-0.2,0.2-0.4,0.3-0.6,0.3h-5.6c-0.3,0-0.5-0.1-0.7-0.3
      c-0.2-0.2-0.3-0.4-0.3-0.7V7.6z"></path> </svg> </span></a></p>
    </div>

    <div id="modal">
      <p><strong>Teaching Materials</strong>: Resources in this category can range from K-12 to higher education, lectures to lab. Many of these resources have been tested in <a href="/community/fmns">Faculty Mentoring Networks</a> or by our network of partners.</p>

      <p><strong>Reference Materials</strong>: Reference Materials can include presentations, workshop related materials, and other resources which may be use to faculty.</p>

      <p><strong>Cloud-based Software</strong>: Resources such as activities or datasets utilizing free modeling and statistical software which run in the browser. Additionally, students can run these cloud-based software, eliminating the need to purchase or install software locally.</p>
    </div>
  </div> <!-- .page-header -->

  <nav class="nav-page">
    <ul>
      <li><a class="nav-page-link browse-link active" data-target="#live-update-wrapper" href="<?php echo Route::url('index.php?option=' . $this->option . '&task=browse'); ?>">Browse Resources</a> <span class="nav-descriptor">Browse Resources</span></li>
      <li><a class="nav-page-link oer-link" href="<?php echo Route::url('index.php?option=' . $this->option . '&task=oer'); ?>">Why OER?</a> <span class="nav-descriptor">Learn the benefits of using and sharing OER</span></li>
      <li><a class="nav-page-link submit-link" data-target="#live-update-wrapper" href="<?php echo Route::url('index.php?option=' . $this->option . '&task=submit'); ?>">Submit a Resource</a> <span class="nav-descriptor">Submit a Resource</span></li>
    </ul>
  </nav>

  <section class="live-update">
    <div aria-live="polite" id="live-update-wrapper">

    </div> <!-- .live-update-wrapper -->
  </section>
