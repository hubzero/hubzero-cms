<?php
$this->css();
$this->js();
$this->js("https://cdnjs.cloudflare.com/ajax/libs/ScrollMagic/2.0.5/plugins/animation.gsap.min.js");

Document::setTitle(Lang::txt('COM_FMNS'));

Pathway::append(
	Lang::txt('COM_FMNS'),
	'index.php?option=' . $this->option . '&controller=' . $this->controller
);
?>

<main class="wrapper">

  <section class="header">

    <div class="bcg-fmn"></div>
    <h2>Faculty Mentoring Networks</h2>
    <h3>What is an FMN?</h3>
    <p>Imagine meeting biweekly over a semester with a small group of educators around a common interest - exploring new ideas or classroom activities, sharing what has worked and what hasn’t, and gaining some credit for your teaching scholarship. That is the Faculty Mentoring Network model. </p>
    <p>Faculty Mentoring Networks (FMNs) are designed to fit into the busy schedules of college faculty, and provide support and guidance &quot;just in time &quot; during the implementation of course changes.  By capitalizing on the experience of a mentor and peers, FMNs provide a bridge between pedagogical theory and actionable classroom practice.
		<h4>Subscribe to our newsletter to stay informed about all FMNs happening on QUBES.</h4>
		<?php \Hubzero\Module\Helper::displayModules('news'); ?>
</p>

  </section>

  <section class="anchor-links">

    <a href="#new">
      <div class="anchor-wrap">
        <h4>Current and Upcoming</h4>
        <hr>
        <p>Check out current FMNs that are happening right now or see what is comming up and apply!</p>
      </div>
    </a>

    <a href="#benefits">
      <div class="anchor-wrap">
        <h4>Benefits of Participating</h4>
        <hr>
        <p>Learn how participating in an FMN can help you professionally.</p>
      </div>
    </a>

    <a href="#products">
      <div class="anchor-wrap">
        <h4>Featured Resources</h4>
        <hr>
        <p>See featured resources which are produced from FMNs.</p>
      </div>
    </a>

    <a href="#bridge">
      <div class="anchor-wrap">
        <h4>Beyond the FMN</h4>
        <hr>
        <p>Past participants who want to continue spreading their knowledge and experiences to other faculty can through our Mentor or Ambassador programs.</p>
      </div>
    </a>

    <a href="#projects">
      <div class="anchor-wrap">
        <h4>How FMNs Can Help Your Project</h4>
        <hr>
        <p>Learn how running your own FMN can further your project goals.</p>
      </div>
    </a>

  </section>

  <section class="new">

    <h2 id="new">Current and Upcoming</h2>
		<hr>
    <div class="new-wrap">
			<?php \Hubzero\Module\Helper::displayModules('fmnUpcoming'); ?>
    </div>
    <h4><a href="/fmns/browse">View all past FMNs</a></h4>

  </section>

  <section class="benefits">

    <h2 id="benefits">What might I actually <span class="emphasis">do</span> in a Faculty Mentoring Network?</h2>
    <div class="benefits-wrapper">
      <div class="map-wrapper">
				<picture>
					<source type="image/webp" srcset="/app/components/com_fmns/site/assets/img/art-board-network.webp">
          <source type="image/jp2" srcset="/app/components/com_fmns/site/assets/img/art-board-network.jp2">
          <source type="image/vnd.ms-photo" srcset="/app/components/com_fmns/site/assets/img/art-board-network.jxr">
          <img src="/app/components/com_fmns/site/assets/img/art-board-network.jpg" alt="image of networking">
				</picture>
      </div>
      <ul>
        <li>Discover new activities, lab modules, or educational resources</li>
        <li>Get expert advice from mentors with experience in pedagogy, content and quantitative skills</li>
        <li>Exchange &quot;teaching stories&quot; with faculty peers across the country as they implement new approaches in their classrooms</li>
        <li>Develop more comfort teaching challenging quantitative topics to your students</li>
        <li>Engage with participating professional societies, gaining recognition for your scholarship of teaching</li>
        <li>Work with colleagues to adapt or produce new open educational resources, with a citable Digital Object Identifier (DOI) and web metrics that track use</li>
        <li>Find support to travel to a professional conference to share the products of your FMN work</li>
        <li>Earn a scholarly title upon completion of the network</li>
        <li>Opportunity to share your final product as a QUBES Educational Resource with a citable Digital Object Identifier (DOI)</li>
      </ul>
    </div>
  </section>

  <section class="products">

    <h2>Featured Resources</h2>
    <div class="product-wrap">
      <?php \Hubzero\Module\Helper::displayModules('resourcesFmn'); ?>
    </div>

    <h3 id="products">A Solid Commitment to OER</h3>
    <div class="oer-wrap">
      <div class="oer">
        <p>Faculty mentoring networks are built around scholarly teaching practices, including Open Education Resources (OER). OER fosters scholarly teaching through access to infrastructure and community where faculty can find, customize, and share high quality teaching resources and strategies.</p>
        <p>For example, many FMNs lead to teaching materials that provide several options for how to approach a single lesson. This multiplies the potential value of the original educational resource – faculty may more easily find a version they can implement given their unique classroom situation.</p>
      </div>
			<picture>
				<source type="image/webp" srcset="/app/components/com_fmns/site/assets/img/oer-lifecycle.webp">
				<source type="image/jp2" srcset="/app/components/com_fmns/site/assets/img/oer-lifecycle.jp2">
				<img src="/app/components/com_fmns/site/assets/img/oer-lifecycle.png" alt="Graphic of OER Lifecycle">
			</picture>
    </div>

  </section>

  <section class="bridge">

    <h2 id="bridge">Become a Mentor or Ambassador</h2>
    <div class="bridge-wrapper">
      <div class="bridge-wrap">
        <h4>Mentor Program</h4>
        <p class="bridge-text">Do you have a quantitative biology education project or classroom materials that other faculty would find valuable? Are you interested in working with faculty from around the country to discover, adopt, adapt, and implement your project or classroom materials?</p>
        <span class="helpme"><a class="link" href="/support/">Contact Us</a></span>
        <!-- <div class="participant-wrap">
          <div class="participant">
            <img src="http://placekitten.com/200/200" alt="Mentor Image">
            <p>Name <br>
            <span class="institution">Institution</span></p>
          </div>
          <div class="participant">
            <img src="http://placekitten.com/200/200" alt="Mentor Image">
            <p>Name <br>
            <span class="institution">Institution</span></p>
          </div>
          <div class="participant">
            <img src="http://placekitten.com/200/200" alt="Mentor Image">
            <p>Name <br>
            <span class="institution">Institution</span></p>
          </div>
        </div> -->
      </div>

      <div class="bridge-wrap">
        <h4>QUBES Ambassador</h4>
        <p class="ambassador-text">Are you interested in sharing what you learned and developed in your QUBES FMN with a wider audience? Apply to be a QUBES Ambassador!</p>
        <a href="/fmns/ambassadors" class="link">Learn More</a>
        <!-- <div class="participant-wrap">
          <div class="participant">
            <img src="http://placekitten.com/200/200" alt="Mentor Image">
            <p>Name <br>
            <span class="institution">Institution</span></p>
          </div>
          <div class="participant">
            <img src="http://placekitten.com/200/200" alt="Mentor Image">
            <p>Name <br>
            <span class="institution">Institution</span></p>
          </div>
          <div class="participant">
            <img src="http://placekitten.com/200/200" alt="Mentor Image">
            <p>Name <br>
            <span class="institution">Institution</span></p>
          </div>
        </div> -->
      </div>
    </div>

    <div class="quote-wrapper">
      <div class="quote-wrap">
        <blockquote>
          <p>The InTeGrate teaching materials were excellent and participating in a QUBES faculty mentoring network was an absolutely invaluable experience for a first year faculty mentor. The support provided by the FMN ranged from how to think about using modular exercises in class to motivating students as well as general earth sciences teaching help. My students benefited greatly from the teaching materials available through the joint QUBES/InTeGrate faculty mentoring network. Being part of a small focused group with a diversity of career stages and teaching perspectives was an excellent opportunity. I highly recommend participating in a QUBES FMN to first year or early career faculty members. </p>
          <p class="testimonial-author">James Burton Deemy, Ph. D. <br>
          <span class="institution">College of Coastal Georgia</span></p>
        </blockquote>
      </div>

      <div class="quote-wrap">
        <blockquote>
          <p>Joining the Data Discovery Faculty Mentoring Network (FMN) has provided me with the foundation for educational support as well as an abundance of resources for improving the quality of my teaching methods in the classroom.  As a participant in Spring 2018, I implemented Integrate modules into the classroom and was able to immediately share my experiences with other faculty across the nation with ample feedback to help improve my techniques.  The suggestions I was given throughout the virtual conferences were immediately implemented into my courses and I am now relaying the same advice to the entire biology discipline at El Paso Community College.  The benefits I've gained from FMN will benefit my students throughout my career and I encourage any educator to take advantage of an outstanding opportunity such as the Faculty Mentoring Network.</p>
          <p class="testimonial-author">Miguel Vasquez, Ph.D. <br>
          <span class="institution">El Paso Community College</span></p>
        </blockquote>
      </div>
    </div>

  </section>

  <section class="projects">

    <h2 id="projects">Faculty Mentoring Networks can broaden the impacts of your project</h2>
    <p>Do you have an existing curriculum project or materials that you would like to refine, evaluate,  and/or disseminate?  Faculty Mentoring Networks provide an efficient and effective mechanism to:</p>

    <div class="projects-wrapper">
      <div class="projects-wrap">
        <img src="/app/components/com_fmns/site/assets/img/iconmonstr-user-30-240.png" alt="Icon">
        <h5>Train faculty to use your classroom materials or pedagogical strategies</h5>
      </div>
      <div class="projects-wrap">
        <img src="/app/components/com_fmns/site/assets/img/iconmonstr-school-7-240.png" alt="Icon">
        <h5>Implement your project ideas and materials in diverse classroom environments</h5>
      </div>
      <div class="projects-wrap">
        <img src="/app/components/com_fmns/site/assets/img/iconmonstr-school-15-240.png" alt="Icon">
        <h5>Gather formative or summative assessments of faculty teaching experiences, student perceptions, and.or learning gains, to be used in publications and grant proposals</h5>
      </div>
      <div class="projects-wrap">
        <img src="/app/components/com_fmns/site/assets/img/iconmonstr-idea-13-240.png" alt="Icon">
        <h5>Engage with a community of faculty that can adapt and share your teaching materials (e.g., adapt a lesson for a different class size or duration), broadening the ultimate classroom adoption</h5>
      </div>
    </div>

  </section>

</main>
