<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2015 HUBzero Foundation, LLC.
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 *
 * HUBzero is a registered trademark of Purdue University.
 *
 * @package   hubzero-cms
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

defined('_HZEXEC_') or die();

$this->css('introduction.css', 'system')
     ->css('resourceportal.css')
     ->js();

Document::addScript('/app/templates' . DS . App::get('template')->template . DS . 'js' . DS . 'masonry.pkgd.min.js');
Document::addScript('/app/templates' . DS . App::get('template')->template . DS . 'js' . DS . 'fit.js');

?>


<section class="feature">
  <div class="featured-product"></div>
  <div class="featured-product"></div>
  <div class="featured-product"></div>
  <div class="featured-product"></div>
  <div class="featured-product"></div>
  <div class="featured-product"></div>
</section>

<div class="search-wrapper">
  <h4>Create, download, modify, or browse our free open education resources!</h4>
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
      <h4>OER</h4>
      <p>Maecenas sed diam eget risus varius blandit sit amet non magna. Sed posuere consectetur est at lobortis. Cras mattis consectetur purus sit amet fermentum. Morbi leo risus, porta ac consectetur ac, vestibulum at eros. Donec sed odio dui.</p>
    </a>
  </div>
  <div class="resources">
    <a href="#two" class="link-anchor">
      <h4>Collections</h4>
      <p>Maecenas sed diam eget risus varius blandit sit amet non magna. Sed posuere consectetur est at lobortis. Cras mattis consectetur purus sit amet fermentum. Morbi leo risus, porta ac consectetur ac, vestibulum at eros. Donec sed odio dui.</p>
    </a>
  </div>
  <div class="resources">
    <a href="#three" class="link-anchor">
      <h4>Software</h4>
      <p>Maecenas sed diam eget risus varius blandit sit amet non magna. Sed posuere consectetur est at lobortis. Cras mattis consectetur purus sit amet fermentum. Morbi leo risus, porta ac consectetur ac, vestibulum at eros. Donec sed odio dui.</p>
    </a>
  </div>
</section>

<section id="one">
  <article class="resource-oer">
    <div class="oer">
      <h2>Open Education Resources</h2>
      <p>Cras mattis consectetur purus sit amet fermentum. Duis mollis, est non commodo luctus, nisi erat porttitor ligula, eget lacinia odio sem nec elit. Donec ullamcorper nulla non metus auctor fringilla. Praesent commodo cursus magna, vel scelerisque nisl consectetur et.</p>
    </div>
  </article>
  <article class="resource-fmn">
    <div class="featured-container">
      <div class="featured-product"></div>
      <div class="featured-product"></div>
      <div class="featured-product"></div>
    </div>
    <div class="fmn">
      <h2>Faculty Mentoring Network Products</h2>
      <p>Cras justo odio, dapibus ac facilisis in, egestas eget quam. Duis mollis, est non commodo luctus, nisi erat porttitor ligula, eget lacinia odio sem nec elit. Cras justo odio, dapibus ac facilisis in, egestas eget quam. Donec ullamcorper nulla non metus auctor fringilla.</p>
      <h6 class="link"><a href="#">browse</a></h6>
    </div>
  </article>
  <article class="resource-partner">
    <div class="partner">
      <h2>Partner Products</h2>
      <p>Cras justo odio, dapibus ac facilisis in, egestas eget quam. Duis mollis, est non commodo luctus, nisi erat porttitor ligula, eget lacinia odio sem nec elit. Cras justo odio, dapibus ac facilisis in, egestas eget quam. Donec ullamcorper nulla non metus auctor fringilla.</p>
      <h6 class="link"><a href="#">browse</a></h6>
    </div>
    <div class="featured-container">
      <div class="featured-product"></div>
      <div class="featured-product"></div>
      <div class="featured-product"></div>
    </div>
  </article>
</section>

<section id="two">
  <div class="big"></div>
  <div class="collections">
    <h2>Collections</h2>
    <p>Donec id elit non mi porta gravida at eget metus. Fusce dapibus, tellus ac cursus commodo, tortor mauris condimentum nibh, ut fermentum massa justo sit amet risus. Donec ullamcorper nulla non metus auctor fringilla. Cum sociis natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus. Vestibulum id ligula porta felis euismod semper.</p>
    <h6 class="link"><a href="#">browse</a></h6>
  </div>
</section>

<section id="three">
  <div class="software">
    <h2>Software</h2>
    <p>Donec id elit non mi porta gravida at eget metus. Fusce dapibus, tellus ac cursus commodo, tortor mauris condimentum nibh, ut fermentum massa justo sit amet risus. Donec ullamcorper nulla non metus auctor fringilla. Cum sociis natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus. Vestibulum id ligula porta felis euismod semper.</p>
    <h6 class="link"><a href="#">run it</a></h6>
  </div>
  <div class="software-logos">
    <h6>R-Studio IDE for R</h6>
    <h6>NetLogo</h6>
    <h6>Copasi</h6>
    <h6>QtOctave</h6>
    <h6>ImageJ</h6>
    <h6>Mesquite</h6>
    <h6>PPLANE</h6>
    <h6>XPPAUT</h6>
    <h6>Avida-ED</h6>
  </div>
</section>
