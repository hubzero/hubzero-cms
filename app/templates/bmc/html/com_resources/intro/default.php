<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// no direct access
defined('_HZEXEC_') or die();

$this->css('introduction.css', 'system')
     ->css('resourceportal.css')
     ->js();

Document::addScript('/app/templates' . DS . App::get('template')->template . DS . 'js' . DS . 'masonry.pkgd.min.js');
Document::addScript('/app/templates' . DS . App::get('template')->template . DS . 'js' . DS . 'fit.js');
// \Hubzero\Module\Helper::displayModules($position);

?>


<section class="feature">
  <?php
 \Hubzero\Module\Helper::displayModules('resourcesFeature');
   ?>
</section>

<div class="search-wrapper">
  <h4>Browse our many open education resources created by the QUBES community</h4>
  <form action="<?php echo Route::url('index.php?option=com_search'); ?>" method="get" class="search">
    <fieldset>
      <p>
        <!-- <label for="rsearch"><?php echo Lang::txt('Keyword or phrase:'); ?></label> -->
        <input type="text" name="terms" id="rsearch" value="" />
        <input type="hidden" name="domain" value="resources" />
        <input type="submit" value="<?php echo Lang::txt('Search'); ?>" />
      </p>
    </fieldset>
  </form>
</div>

<section class="discover">
  <h2>Discover our Resources</h2>
  <div class="resources">
    <a href="#one" class="link-anchor">
      <h4>FMN & Partner Products</h4>
      <p>Discover resources created by our Faculty Mentoring Networks (FMNs) and partners - all based upon the foundation of Open Education Practices</p>
    </a>
  </div>
  <div class="resources">
    <a href="#two" class="link-anchor">
      <h4>Collections</h4>
      <p>Browse community driven collections of resources or create your own collection around your topic of interest</p>
    </a>
  </div>
  <div class="resources">
    <a href="#three" class="link-anchor">
      <h4>Software</h4>
      <p>Create your own customized activities and datasets which students can run using free software without having the need to purchase and install locally</p>
    </a>
  </div>
</section>

<section id="one">
  <article class="resource-oer">
    <div class="oer">
      <h2>Open Education Resources</h2>
      <p>Open Education Practices (OEP) are the basis of the QUBES community.  OEP communities are built around a shared interest and desire to continuously improve and share resources and tools.  In open source software communities, users are also developers.  In a similar vein, QUBES users are invited to share their adaptations to existing educational resources through our publishing process.</p>
    </div>
  </article>
  <article class="resource-fmn">
    <div class="module-fmn">
      <?php
     \Hubzero\Module\Helper::displayModules('resourcesFmn');
       ?>
    </div>
    <div class="fmn">
      <h2>Faculty Mentoring Network Products</h2>
      <p>Participants in QUBES Faculty Mentoring Networks have developed new education materials and adapted existing classroom modules. Our FMN participants have shared these modifications along with “how to” teaching notes for you to adopt and adapt in your own courses. </p>
      <!-- <h6 class="link"><a href="#">browse</a></h6> -->
    </div>
  </article>
  <article class="resource-partner">
    <div class="partner">
      <h2>Partner Products</h2>
      <p>QUBES partners bring a range of resources to the community including teaching modules, powerpoints on tricky topics, and datasets. Resources from QUBES partners are available on their websites for you to adopt and adapt in your classroom. </p>
      <!-- <h6 class="link"><a href="#">browse</a></h6> -->
    </div>
    <div class="module-partner">
      <?php
     \Hubzero\Module\Helper::displayModules('resourcesPartner');
       ?>
    </div>
    <div class="browse">
      <h6 class="link"><a href="/publications/browse">browse our oer products</a></h6>
      <h6 class="link"><a href="/publications/submit">submit a resource</a></h6>
    </div>
  </article>
</section>

<section id="two">
  <div class="big">
    <img src="/app/site/media/images/collections.jpg" alt="Collections">
  </div>
  <div class="collections">
    <h2>Collections</h2>
    <p>Collections are both a community driven set of resources and a tool used by our groups and members to share resources found not only on QUBES, but across other sites as well.</p>
    <h6 class="link"><a href="/resources/collections">browse</a></h6>
  </div>
</section>

<section id="three">
  <div class="software">
    <h2>Software</h2>
    <p>QUBES hosts various software that can be run in the browser without having to worry about any installation on your local machine. Faculty can now have students run free modeling and statistical software using customized activities and datasets made by you.</p>
    <h6 class="link"><a href="/resources/software">run it</a></h6>
  </div>
  <div class="software-logos">
    <div class="logo-wrap">
      <a href="/tools/rstudio">
        <img src="/app/site/media/images/tools/RStudio-Logo-All-White.png" />
        <h6>R-Studio IDE for R</h6>
      </a>
    </div>
    <div class="logo-wrap">
      <a href="/tools/netlogo">
        <img src="/app/site/media/images/tools/netlogo-logo.png" />
        <h6>NetLogo</h6>
      </a>
    </div>
    <div class="logo-wrap">
      <a href="/tools/copasi">
        <img src="/app/site/media/images/tools/copasi-logo.png" />
        <h6>Copasi</h6>
      </a>
    </div>
    <div class="logo-wrap">
      <a href="/tools/qtoctave">
        <img src="/app/site/media/images/tools/qtoctave-logo.png" />
        <h6>QtOctave</h6>
      </a>
    </div>
    <div class="logo-wrap">
      <a href="/tools/imagej">
        <img src="/app/site/media/images/tools/imagej-logo.png" />
        <h6>ImageJ</h6>
      </a>
    </div>
    <div class="logo-wrap">
      <a href="/tools/mesquite">
        <img src="/app/site/media/images/tools/mesquite-logo.png" />
        <h6>Mesquite</h6>
      </a>
    </div>
    <div class="logo-wrap">
      <a href="/tools/pplane">
        <img src="/app/site/media/images/tools/pplane-logo.png" />
        <h6>PPLANE</h6>
      </a>
    </div>
    <div class="logo-wrap">
      <a href="/tools/xppaut">
        <img src="/app/site/media/images/tools/xppaut-logo.gif" />
        <h6>XPPAUT</h6>
      </a>
    </div>
    <div class="logo-wrap">
      <a href="/resources/1015">
        <img src="/app/site/media/images/tools/Avida-ED-logo.png" />
        <h6>Avida-ED</h6>
      </a>
    </div>
  </div>
</section>
