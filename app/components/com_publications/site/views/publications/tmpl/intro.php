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
      <li><a class="browse-link active" href="#">Browse Resources</a> <span class="nav-descriptor">Browse Resources</span></li>
      <li><a class="oer-link" href="#">Why OER?</a> <span class="nav-descriptor">Learn the benefits of using and sharing OER</span></li>
      <li><a class="submit-link" href="#">Submit a Resource</a> <span class="nav-descriptor">Submit a Resource</span></li>
    </ul>
  </nav>
  
  <section class="live-update">
    <div aria-live="polite" class="live-update-wrapper">
      <div class="browse-resources-wrapper">
        <div class="page-filter-wrapper"><button class="mobile-filter btn icon-filter">Filter</button>
          
          <header class="page-filter">
            <div class="container data-entry">
              <fieldset class="entry-search"><legend>Search</legend> <label for="entry-search-field">Enter keyword or phrase</label> <input id="entry-search-field" name="search" placeholder="Enter keyword or phrase" type="text" value="" /> <input name="sortby" type="hidden" value="date" /> <input name="tag" type="hidden" value="" /></fieldset>
            </div>
            
            <div class="filter-wrapper">
              <fieldset><legend aria-expanded="true"> </legend>
                
                <h6>Resource Type<object class="filter-icon" data="core/assets/icons/chevron-up.svg" type="image/svg+xml">Collapse <!-- fallback image in CSS --></object></h6>
                
                <div class="filter-panel">
                  <div class="filter-item"><input id="r_type_1" type="checkbox" /> <label for="r_type_1">Teaching Materials</label></div>
                  
                  <div class="filter-item"><input id="r_type_2" type="checkbox" /> <label for="r_type_2">Reference Materials</label></div>
                  
                  <div class="filter-item"><input id="r_type_3" type="checkbox" /> <label for="r_type_3">Software</label></div>
                </div>
              </fieldset>
              
              <fieldset><legend aria-expanded="true"> </legend>
                
                <h6>Audience Level<object class="filter-icon" data="core/assets/icons/chevron-up.svg" type="image/svg+xml">Collapse <!-- fallback image in CSS --></object></h6>
                
                <div class="filter-panel">
                  <div class="filter-item"><input id="a_type_1" type="checkbox" /> <label for="a_type_1">High School</label></div>
                  
                  <div class="filter-item"><input id="a_type_2" type="checkbox" /> <label for="a_type_2">Undergraduate</label></div>
                  
                  <div class="filter-item"><input id="a_type_3" type="checkbox" /> <label for="a_type_3">Graduate</label></div>
                  
                  <div class="filter-item"><input id="a_type_4" type="checkbox" /> <label for="a_type_4">Faculty</label></div>
                </div>
              </fieldset>
              
              <fieldset><legend aria-expanded="true"> </legend>
                
                <h6>Instructional Setting<object class="filter-icon" data="core/assets/icons/chevron-up.svg" type="image/svg+xml">Collapse <!-- fallback image in CSS --></object></h6>
                
                <div class="filter-panel">
                  <div class="filter-item"><input id="s_type_1" type="checkbox" /> <label for="s_type_1">Lecture</label></div>
                  
                  <div class="filter-item"><input id="s_type_2" type="checkbox" /> <label for="s_type_2">Lab</label></div>
                  
                  <div class="filter-item"><input id="s_type_3" type="checkbox" /> <label for="s_type_3">Homework</label></div>
                </div>
              </fieldset>
              
              <fieldset><legend aria-expanded="true"> </legend>
                
                <h6>Activity Length<object class="filter-icon" data="core/assets/icons/chevron-up.svg" type="image/svg+xml">Collapse <!-- fallback image in CSS --></object></h6>
                
                <div class="filter-panel">
                  <div class="filter-item"><input id="l_type_1" type="checkbox" /> <label for="l_type_1">Less than 1 hour</label></div>
                  
                  <div class="filter-item"><input id="l_type_2" type="checkbox" /> <label for="l_type_2">1 hour</label></div>
                  
                  <div class="filter-item"><input id="l_type_3" type="checkbox" /> <label for="l_type_3">More than 1 hour</label></div>
                  
                  <div class="filter-item"><input id="l_type_4" type="checkbox" /> <label for="l_type_4">Extended Project</label></div>
                </div>
              </fieldset>
            </div>
            <input name="submit" type="submit" value="Apply" /> <input name="reset" type="reset" value="Reset" />
          </header>
        </div> <!-- .page-filter-wrapper -->
        
        <div aria-live="polite" class="filter-results">
          <div class="featured-header">
            <div class="featured-resource">
              <h3 class="header">Resource of the Week</h3>
              
              <p class="sub-header">Check out more resources that have been <a href="#">featured on ROW</a>!</p>
              <?php echo \Hubzero\Module\Helper::displayModules('rowRecent'); ?>
            </div>
            
            <div class="featured-software">
              <h3 class="header">Featured Software</h3>
              
              <p class="sub-header">Browse more <a href="#">software based resources</a></p>
              <?php echo \Hubzero\Module\Helper::displayModules('rowRecent'); ?>
            </div>
            
            <div class="featured-reference">
              <h3 class="header">Latest Reference Material</h3>
              
              <p class="sub-header">See more <a href="#">reference materials</a> from QUBES and our partners</p>
              <?php echo \Hubzero\Module\Helper::displayModules('rowRecent'); ?></div>
            </div>
            
            <div class="recent-resources">
              <h3>Recent</h3>
              <div class="module-feature"> <!-- Needed to add this for some reason -->
                <?php echo \Hubzero\Module\Helper::displayModules('resourcesFeature'); ?>
              </div>
            </div>
          </div>
        </div> <!-- .browser-resources-wrapper -->
        
        <!-- Why OER -->        
        <div class="oer-wrapper">
          <div class="philosophy-wrapper">
            <h3>Our OER philosophy</h3>
            
            <p>Open Educational Resources (OER) are any type of educational material that are freely available for teachers and students to use, adapt, share, and reuse (<a href="https://pitt.libguides.com/openeducation/intro" target="_blank">Collister, 2019</a>). The QUBES project supports the use of open licensing to make learning resources widely accessible and to provide opportunities for faculty to get professional credit for their teaching scholarship. The use of OER can support Open Educational Practices (<a href="https://library.educause.edu/resources/2018/7/7-things-you-should-know-about-open-education-practices" target="_blank">OEP</a>) more broadly which promote the use of innovative learning environments and pedagogical strategies. There is emerging evidence that the use of open educational resources can have a positive impact on student academic success and creates more inclusive educational settings (<a href="https://rdcu.be/b0fGz" target="_blank">Fischer, et al., 2015</a>).</p>
            
            <p>QUBES emphasizes the central role of participation in a scholarly community as a mechanism to advance the development and use of OER. We believe that professional collaborations built around a shared interest and desire to improve and share resources benefits learners in diverse educational contexts. Parallel to the situation in open source software communities, where the users are also developers, QUBES users are invited to share their adaptations to existing educational resources through our publishing platform.</p>
          </div>
          
          <div class="oer-on-qubes">
            <h3>OER on QUBES</h3>
            <img src="https://loremflickr.com/300/150/kitty" />
            <ul>
              <li>With 1000 user submitted educational resources, QUBES shares diverse materials that can be adapted to fit your classroom.</li>
              <li>New resources are added regularly and cover many topics in biology, mathematics, and data science.</li>
              <li>We also host <a href="https://qubeshub.org/qubesresources/software">open source software tools</a>.</li>
              <li><a href="https://qubeshub.org/community">Join a community</a> around the development and use of OER</li>
            </ul>
          </div>
          
          <div class="share-your-oer">
            <h3>Benefits of sharing on QUBES</h3>
            
            <p>QUBES believes in providing quality resources to improve quantitative biology education with a focus on equity and inclusion in the classroom. We believe in harnessing the power of community to not only create and incubate ideas, but improve or provide feedback which in turn, improves resources for all educators.</p>
            
            <h5>Get credit for your teaching scholarship</h5>
            
            <div class="contribute-descriptor"><img src="https://loremflickr.com/100/100/kitty" />
              <div>
                <p>Participating in OER is valuable work that you should receive credit for! QUBES resources provide multiple ways to document your contributions and track your impact:</p>
                
                <ul>
                  <li><strong>Digital Object Identifier (DOI)</strong>: The QUBES Resource System automatically assigns a DOI to all submitted resources. This DOI provides a stable address that can be used to find your resources and track how it is being used.</li>
                  <li><strong>Usage Metrics</strong>: Information about the impact of your resources are automatically tracked through the number of views, downloads, and adaptations over time.</li>
                  <li><strong>Visibility and Engagement</strong>: QUBES resources are indexed by a variety of OER search engines and our platform makes it easy for interested users to comment on or adapt your materials.</li>
                </ul>
              </div>
            </div>
            
            <h5>Share, Improve, Broaden Impact of Visibility and Engagement</h5>
            
            <div class="contribute-descriptor"><img src="https://loremflickr.com/100/100/kitty" />
              <p><a href="https://qubeshub.org/community/partners">Many educational projects and professional societies</a> collaborate with QUBES to further broaden the impacts of their work. Our <a href="https://qubeshub.org/community/fmns">Faculty Mentoring Networks</a> (FMNs) provide partners with an opportunity to share and improve their materials with faculty who implement these resources in their classrooms - providing valuable real-time feedback. When you share on QUBES, you are also searchable in the <a href="https://mason.deepwebaccess.com/mason__MasonLibrariesOpenEducationResources_5f4/desktop/en/search.html">Mason Open Metafinder</a> database.</p>
            </div>
          </div>
        </div> <!-- .oer-wrapper -->
        
        <!-- Submit a Resource -->
        <div class="submit-resource-wrap">
          <aside>
            <div class="software-instructions-wrapper">
              <div class="arrow-right">&nbsp;</div>
              
              <div class="software-instructions">
                <h4>Got a Shiny App?</h4>
                
                <p>Please read these <a href="#">special instructions</a>. For other types of software, <a href="#">contact us</a> for help.</p>
              </div>
            </div>
            
            <div class="kb-links">
              <h5>Related Articles</h5>
              
              <ul>
                <li>[Article] <a href="#">How to submit a resource</a></li>
                <li>[Article] Author&#39;s notes</li>
              </ul>
            </div>
          </aside>
          
          <div class="submit-a-resource-wrap">
            <h3>Quick and Easy. Get started today!</h3>
            <a class="btn submit-resource-btn" href="#">Submit a Resource</a></div>
            
            <div class="submit-partner-resource-wrap">
              <h5>Submit a Partner Resource</h5>
              
              <p>Possibly a quick description of what partner resources are or...</p>
              
              <p>Possibly a mini-advertisement of offering up specialized curation/publication services that can link to services?</p>
              
              <div class="resource-type-wrap">
                <div class="resource-type"><img alt="Partner logo" class="partner-resource-logo" src="" /></div>
                
                <div class="resource-type"><img alt="Partner logo" class="partner-resource-logo" src="" /></div>
              </div>
            </div>
          </div> <!-- .submit-resource-wrap -->
        </div> <!-- .live-update-wrapper -->
      </section>
