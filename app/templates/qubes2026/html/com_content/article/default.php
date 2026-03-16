<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// no direct access
defined('_HZEXEC_') or die();

Html::addIncludePath(PATH_COMPONENT . '/helpers');

// Create shortcuts to some parameters.
$params  = $this->item->params;
$images  = json_decode($this->item->images);
$urls    = json_decode($this->item->urls);
$canEdit = $this->item->params->get('access-edit');

?>

<?php if ($this->params->get('show_page_heading')): ?>
	<h1>
		<?php echo $this->escape($this->params->get('page_heading')); ?>
	</h1>
<?php endif; ?>

<?php
if (!empty($this->item->pagination) && $this->item->pagination && !$this->item->paginationposition && $this->item->paginationrelative)
{
	echo $this->item->pagination;
}
?>

<?php
$menu   = App::get('menu');
$active = $menu->getActive();
$isFrontPage = false;
if (trim(strtok($_SERVER['REQUEST_URI'], '?'), '/') === '') {
    $isFrontPage = true;
}
if ($active && $active->alias === 'home') {
    $isFrontPage = true;
}
?>

<div class="contentpane item-page<?php echo $this->pageclass_sfx; ?>">
<?php if ($isFrontPage) : ?>
    <!-- custom homepage layout -->
    <?php Document::addStylesheet('/app/templates/qubes2026/css/pages/homepage2026.css'); ?>

    <?php
    // --- Homepage dynamic data ---
    $hp_db = App::get('db');

    // Featured resources (top 3 by ranking)
    $hp_db->setQuery(
        "SELECT id, title, alias, introtext, path
         FROM jos_resources
         WHERE published = 1 AND standalone = 1
         ORDER BY ranking DESC, created DESC
         LIMIT 3"
    );
    $hp_resources = $hp_db->loadAssocList() ?: [];

    // Featured communities/groups (top 6)
    $hp_db->setQuery(
        "SELECT cn, description, type, logo
         FROM jos_xgroups
         WHERE published = 1
         ORDER BY created DESC
         LIMIT 6"
    );
    $hp_groups = $hp_db->loadAssocList() ?: [];

    // Published tools
    $hp_db->setQuery(
        "SELECT tv.toolname, tv.title
         FROM jos_tool_version tv
         INNER JOIN jos_tool t ON t.toolname = tv.toolname
         WHERE tv.state = 1 AND t.published = 1
         GROUP BY tv.toolname
         LIMIT 12"
    );
    $hp_tools = $hp_db->loadAssocList() ?: [];

    // Group type badge labels
    $group_type_labels = [0 => 'GROUP', 1 => 'PARTNER', 2 => 'WORKSHOP', 3 => 'CLASSROOM'];

    // Partner organizations (type=1)
    $hp_db->setQuery(
        "SELECT cn, description, logo
         FROM jos_xgroups
         WHERE published = 1 AND type = 1
         ORDER BY cn ASC
         LIMIT 12"
    );
    $hp_partners = $hp_db->loadAssocList() ?: [];

    // Fallback: hardcoded partners from wireframe if DB has none
    if (empty($hp_partners)) {
        $hp_partners = [
            ['cn' => 'biosigmaa',  'label' => 'BioSIGMAA',                   'logo' => null, 'url' => 'https://www.sigmaa.maa.org/bio/'],
            ['cn' => 'nabt',       'label' => 'NABT',                         'logo' => null, 'url' => 'https://www.nabt.org/'],
            ['cn' => 'coursesource','label' => 'CourseSource',                'logo' => null, 'url' => 'https://qubeshub.org/community/groups/coursesource'],
            ['cn' => 'esa',        'label' => 'Ecological Society of America','logo' => null, 'url' => 'https://www.esa.org/'],
            ['cn' => 'abcc',       'label' => 'ABCC',                         'logo' => null, 'url' => 'https://qubeshub.org/community/groups/abcc'],
            ['cn' => 'niblse',     'label' => 'NIBLSE',                       'logo' => null, 'url' => 'https://niblse.org/'],
            ['cn' => 'nimbios',    'label' => 'NIMBioS',                      'logo' => null, 'url' => 'https://www.nimbios.org/'],
            ['cn' => 'neon',       'label' => 'NEON',                         'logo' => null, 'url' => 'https://www.neonscience.org/'],
            ['cn' => 'bsa',        'label' => 'Botanical Society of America', 'logo' => null, 'url' => 'https://botany.org/'],
        ];
    }
    ?>

    <div class="hp-container">
      <div class="top-banner">
        BioQUEST hosts QUBES for more info click <a href="#">here</a>
      </div>

      <section class="three-up" aria-label="Primary navigation">
        <div class="panel">
          <h2>Resources</h2>
          <p>
            Individual faculty and partner projects share teaching resources for others to
            adopt in their own courses. QUBES supports Open Education Practices, making
            materials free to use and modify with attribution.
          </p>
          <div class="btn-stack">
            <a href="/publications/browse" class="btn">Browse Resources</a>
            <a href="/publications/submitresource" class="btn">Submit a Resource</a>
            <a href="/publications/oer" class="btn">About OER</a>
            <a href="/publications/software" class="btn">Software</a>
          </div>
          <div class="footer">
            For more information, click below.<br />
            <span class="chev">⌄</span>
          </div>
        </div>

        <div class="panel">
          <h2>Community</h2>
          <p>
            QUBES provides an open, inclusive virtual space for sharing classroom resources
            and discussing how to adapt materials to local contexts.
          </p>
          <div class="btn-stack">
            <a href="/community/groups" class="btn">Browse Groups</a>
            <button class="btn">Browse Events</button>
            <a href="/community/partners" class="btn">Browse Partners</a>
            <a href="/community/fmns" class="btn">Browse FMN</a>
            <a href="/news" class="btn">News &amp; Activities</a>
            <a href="/news/newsletter/spotlight" class="btn">Community Spotlight</a>
          </div>
          <div class="footer">
            For more information, click below.<br />
            <span class="chev">⌄</span>
          </div>
        </div>

        <div class="panel">
          <h2>Services</h2>
          <p>
            We have knowledge articles, office hours and contact information in case you
            have questions or inquiries regarding the site.
          </p>
          <div class="btn-stack">
            <a href="/support/ticket/new" class="btn">Support</a>
            <button class="btn">Office Hours</button>
            <a href="/about/#contact" class="btn">Contact Information</a>
            <a href="https://qubeshub.org/support/" class="btn" target="_blank" rel="noopener">Knowledge Base Articles</a>
          </div>
          <div class="footer">
            For more information, click below.<br />
            <span class="chev">⌄</span>
          </div>
        </div>
      </section>

      <section class="ideas">
        <h3>Not sure where to start? Here are some ideas!</h3>
        <div class="ideas-grid">
          <div class="idea">
            <h4>Find a Teaching Module</h4>
            <p>
              Dive right into teaching materials created by real educators and
              researchers. Browse modules on everything from data analysis to ecology.
            </p>
          </div>
          <div class="idea">
            <h4>Find a Group</h4>
            <p>
              Are you searching for an academically oriented community to join?
              Consider looking through our extensive catalogue of groups.
            </p>
          </div>
          <div class="idea">
            <h4>Sign Up For Our Newsletter</h4>
            <p>
              Subscribe to our monthly newsletter to receive curated resources,
              upcoming events, and spotlight features right in your inbox.
              <a href="#">Access it here</a>.
            </p>
          </div>
          <div class="idea">
            <h4>Curate Your Own Resources</h4>
            <p>
              Build your personal library of materials to use, reuse, and share.
              Bookmark resources, track projects, and organize content.
            </p>
          </div>
        </div>
      </section>

      <section class="featured">
        <h3>Featured Content</h3>
        <div class="featured-grid">
          <article class="feature">
            <div class="thumb" aria-hidden="true"></div>
            <div class="label">Resource X</div>
          </article>
          <article class="feature">
            <div class="thumb" aria-hidden="true"></div>
            <div class="label">Group Y</div>
          </article>
          <article class="feature">
            <div class="thumb" aria-hidden="true"></div>
            <div class="label">Event Z</div>
          </article>
        </div>
      </section>

      <!-- Resources section -->
      <section class="hp-section">
        <div class="resources-header">
          <div>
            <h2>Resources</h2>
            <h3>Created by our members and partners</h3>
            <p>
              Individual faculty and partnering projects contribute teaching resources for others to adopt,
              adapt, and implement in their own courses. QUBES supports Open Education Practices that make
              it free to use, modify, and repost materials with attribution, increasing the utility and
              value of the original materials.
            </p>
          </div>
          <a href="/resources" class="btn-primary">Browse</a>
        </div>
        <div class="resources-grid">
          <?php if (!empty($hp_resources)) : ?>
            <?php foreach ($hp_resources as $res) :
              $thumb = !empty($res['path'])
                ? '/app/site/media/resources/' . $res['id'] . '/' . $res['path']
                : null;
            ?>
            <a href="/resources/<?php echo (int)$res['id']; ?>" class="resource-card">
              <div class="resource-thumb">
                <?php if ($thumb) : ?>
                  <img src="<?php echo htmlspecialchars($thumb); ?>" alt="<?php echo htmlspecialchars($res['title']); ?>">
                <?php endif; ?>
                <span class="resource-badge">RESOURCE</span>
              </div>
              <div class="resource-label"><?php echo htmlspecialchars($res['title']); ?></div>
            </a>
            <?php endforeach; ?>
          <?php else : ?>
            <div class="resource-card"><div class="resource-thumb"><span class="resource-badge">RESOURCE</span></div></div>
            <div class="resource-card"><div class="resource-thumb"><span class="resource-badge">RESOURCE</span></div></div>
            <div class="resource-card"><div class="resource-thumb"><span class="resource-badge">RESOURCE</span></div></div>
          <?php endif; ?>
        </div>
      </section>

      <!-- Software showcase -->
      <section class="hp-section">
        <div class="showcase reverse">
          <div class="showcase-text">
            <h2>Software</h2>
            <h3>For students and teachers</h3>
            <p>
              Students can run free modeling and statistical software through their browser, eliminating
              the need to purchase or install software locally. Instructors can customize codebases and
              datasets to fit their courses, minimizing logistical barriers between students and course concepts.
            </p>
            <a href="/resources/browse?type=tools" class="btn-primary">Browse</a>
          </div>
          <div>
            <?php
            // Static known tools from wireframe; supplement with DB tools
            $static_tools = [
              ['toolname' => 'rstudio',      'title' => 'R-Studio IDE'],
              ['toolname' => 'jupyter',      'title' => 'Jupyter Notebooks'],
              ['toolname' => 'jupyterlab',   'title' => 'Jupyter Lab'],
              ['toolname' => 'netlogo',      'title' => 'NetLogo'],
              ['toolname' => 'copasi',       'title' => 'Copasi'],
              ['toolname' => 'gdoctave',     'title' => 'GDOctave'],
              ['toolname' => 'imagej',       'title' => 'ImageJ'],
              ['toolname' => 'mesquite',     'title' => 'Mesquite'],
              ['toolname' => 'pplane',       'title' => 'PPLANE'],
              ['toolname' => 'xppaut',       'title' => 'XPPAUT'],
              ['toolname' => 'avida-ed',     'title' => 'Avida-ED'],
            ];
            // Merge DB tools, avoiding duplicates
            $shown = array_column($static_tools, 'toolname');
            foreach ($hp_tools as $t) {
              if (!in_array($t['toolname'], $shown)) {
                $static_tools[] = $t;
                $shown[] = $t['toolname'];
              }
            }
            ?>
            <div class="software-grid">
              <?php foreach ($static_tools as $tool) :
                $tool_logo = '/app/site/media/resources/' . $tool['toolname'] . '/logo.png';
              ?>
              <a href="/resources/<?php echo htmlspecialchars($tool['toolname']); ?>" class="tool-card">
                <div class="tool-thumb">
                  <img src="<?php echo $tool_logo; ?>"
                       alt="<?php echo htmlspecialchars($tool['title']); ?>"
                       onerror="this.style.display='none'">
                </div>
                <?php echo htmlspecialchars($tool['title']); ?>
              </a>
              <?php endforeach; ?>
            </div>
          </div>
        </div>
      </section>

      <!-- Network + Featured Communities -->
      <section class="hp-section">
        <div class="network-layout">
          <!-- Partner logos -->
          <div class="partner-logos">
            <?php foreach ($hp_partners as $partner) :
              $pLogo = !empty($partner['logo'])
                ? '/app/site/media/groups/' . $partner['cn'] . '/' . $partner['logo']
                : null;
              $pUrl  = isset($partner['url']) ? $partner['url'] : '/community/groups/' . $partner['cn'];
              $pLabel = isset($partner['label']) ? $partner['label'] : $partner['cn'];
            ?>
            <a href="<?php echo htmlspecialchars($pUrl); ?>" class="partner-logo" target="_blank" rel="noopener">
              <?php if ($pLogo) : ?>
                <img src="<?php echo htmlspecialchars($pLogo); ?>"
                     alt="<?php echo htmlspecialchars($pLabel); ?>"
                     onerror="this.style.display='none';this.nextElementSibling.style.display='block'">
                <span style="display:none"><?php echo htmlspecialchars($pLabel); ?></span>
              <?php else : ?>
                <span><?php echo htmlspecialchars($pLabel); ?></span>
              <?php endif; ?>
            </a>
            <?php endforeach; ?>
          </div>
          <!-- Network text -->
          <div class="network-text">
            <h2>Our Network...</h2>
            <p>
              We believe in the power of collective impact. We create opportunities for professional societies,
              curriculum projects, and STEM Ed researchers to join with and support educators.
            </p>
            <a href="/community/groups" class="btn-primary">Explore</a>
          </div>
        </div>

        <?php if (!empty($hp_groups)) : ?>
        <h3 style="font-size:18px;font-weight:700;margin:0 0 12px;">Featured Communities</h3>
        <div class="communities-grid">
          <?php foreach ($hp_groups as $group) :
            $logo = !empty($group['logo'])
              ? '/app/site/media/groups/' . $group['cn'] . '/' . $group['logo']
              : null;
            $badge = $group_type_labels[$group['type']] ?? 'GROUP';
          ?>
          <a href="/community/groups/<?php echo htmlspecialchars($group['cn']); ?>" class="community-card">
            <div class="community-thumb">
              <?php if ($logo) : ?>
                <img src="<?php echo htmlspecialchars($logo); ?>" alt="<?php echo htmlspecialchars($group['cn']); ?>">
              <?php endif; ?>
              <span class="community-badge"><?php echo $badge; ?></span>
            </div>
            <div class="community-label">
              <?php echo htmlspecialchars(Hubzero\Utility\Str::truncate(strip_tags($group['description']), 80)); ?>
            </div>
          </a>
          <?php endforeach; ?>
        </div>
        <?php endif; ?>
      </section>

      <!-- Community text + links -->
      <section class="hp-section">
        <div class="community-section">
          <div class="community-desc">
            <h2 style="font-size:32px;margin:0 0 16px;font-style:italic;">...and Community</h2>
            <p>
              We recognize that moving quantitative biology education forward requires a community that
              works together to achieve its goals. QUBES supports community members by providing an open
              and inclusive virtual space for sharing quantitative classroom activities and resources,
              discussing teaching and the adaptation of educational materials to specific institutional
              contexts, and working together to develop new ideas and insights. We also connect educators
              with opportunities through our broader network in STEM education, broadening the impact of
              quantitative biology education curriculum and research projects, conferences, and other
              initiatives.
            </p>
          </div>
          <div class="community-links">
            <ul>
              <li><a href="/community/groups?type=fmn">Faculty Mentoring Networks</a> <span class="arrow">&#8592;</span></li>
              <li><a href="/community/groups?type=workshop">Workshops and Meetings</a> <span class="arrow">&#8592;</span></li>
              <li><a href="/community/groups?type=classroom">Classrooms</a> <span class="arrow">&#8592;</span></li>
              <li><a href="/community/groups?type=publishing">Collaborative Publishing</a> <span class="arrow">&#8592;</span></li>
              <li><a href="/community/groups?type=project">Project Websites</a> <span class="arrow">&#8592;</span></li>
              <li><a href="/community/groups?type=private">Private Working Groups</a> <span class="arrow">&#8592;</span></li>
            </ul>
            <div class="newsletter-row">
              <a href="/community/groups" class="btn-discover">Discover</a>
              <input type="email" placeholder="email address" aria-label="Subscribe to newsletter">
              <button class="btn-green">Sign Up</button>
            </div>
          </div>
        </div>
      </section>

      <!-- Need Assistance -->
      <section class="hp-section assistance">
        <h2>Need Assistance?</h2>
        <div class="assistance-grid">
          <div class="assist-col">
            <h3>Knowledge Base Articles</h3>
            <p>
              We have an extensive catalogue of knowledge base articles to peruse. These include topics
              such as creating a group or purchasing a resource. These articles can be accessed below.
            </p>
            <a href="/support/kb" class="btn-primary">Knowledge Base Articles</a>
          </div>
          <div class="assist-col">
            <h3>Office Hours</h3>
            <p>
              We hold Office Hours on <em>[Insert times/weekdays Here]</em>. Feel free to attend if
              you would like to solve any questions or inquiries. Use the zoom link below to join.
            </p>
            <a href="#" class="btn-primary">Zoom Room</a>
          </div>
          <div class="assist-col">
            <h3>Help Desk</h3>
            <p>
              If you are unable to meet with us directly, you can also submit a help desk ticket with
              your question or issue, and Probuild will write back to you as soon as possible!
            </p>
            <a href="/support" class="btn-primary">Help Desk</a>
          </div>
        </div>
      </section>

    </div><!-- /.hp-container -->
    <?php return; ?>
<?php endif; ?>

	<?php if ($params->get('show_title')): ?>
		<div class="content-header">
			<h2>
				<?php if ($params->get('link_titles') && !empty($this->item->readmore_link)): ?>
					<a href="<?php echo $this->item->readmore_link; ?>">
						<?php echo $this->escape($this->item->title); ?>
					</a>
				<?php else : ?>
					<?php echo $this->escape($this->item->title); ?>
				<?php endif; ?>
			</h2>
		</div><!-- / .content-header -->
	<?php endif; ?>

	<?php if ($canEdit || $params->get('show_print_icon') || $params->get('show_email_icon')): ?>
		<ul class="actions">
			<?php if (!$this->print): ?>
				<?php if ($params->get('show_print_icon')): ?>
					<li class="print-icon">
						<?php echo Html::icon('print_popup', $this->item, $params); ?>
					</li>
				<?php endif; ?>
				<?php if ($params->get('show_email_icon')): ?>
					<li class="email-icon">
						<?php echo Html::icon('email', $this->item, $params); ?>
					</li>
				<?php endif; ?>
				<?php /*if ($canEdit): ?>
					<li class="edit-icon">
						<?php echo Html::icon('edit', $this->item, $params); ?>
					</li>
				<?php endif;*/ ?>
			<?php else : ?>
				<li>
					<?php echo Html::icon('print_screen', $this->item, $params); ?>
				</li>
			<?php endif; ?>
		</ul>
	<?php endif; ?>

	<?php if (!$params->get('show_intro')) :
		echo $this->item->event->afterDisplayTitle;
	endif; ?>

	<?php echo $this->item->event->beforeDisplayContent; ?>

	<div class="contentpaneopen">
		<?php $useDefList = (($params->get('show_author')) or ($params->get('show_category')) or ($params->get('show_parent_category'))
		or ($params->get('show_create_date')) or ($params->get('show_modify_date')) or ($params->get('show_publish_date'))
		or ($params->get('show_hits'))); ?>

		<?php if ($useDefList) : ?>
			<dl class="article-info">
				<dt class="article-info-term"><?php  echo Lang::txt('COM_CONTENT_ARTICLE_INFO'); ?></dt>
		<?php endif; ?>
			<?php if ($params->get('show_parent_category') && $this->item->parent_slug != '1:root'): ?>
				<dd class="parent-category-name">
					<?php
					$title = $this->escape($this->item->parent_title);
					$url = '<a href="' . Route::url(Components\Content\Site\Helpers\Route::getCategoryRoute($this->item->parent_slug)) . '">' . $title . '</a>';
					?>
					<?php if ($params->get('link_parent_category') && $this->item->parent_slug) : ?>
						<?php echo Lang::txt('COM_CONTENT_PARENT', $url); ?>
					<?php else : ?>
						<?php echo Lang::txt('COM_CONTENT_PARENT', $title); ?>
					<?php endif; ?>
				</dd>
			<?php endif; ?>
			<?php if ($params->get('show_category')) : ?>
				<dd class="category-name">
					<?php
					$title = $this->escape($this->item->category_title);
					$url = '<a href="' . Route::url(Components\Content\Site\Helpers\Route::getCategoryRoute($this->item->catslug)) . '">' . $title . '</a>';
					?>
					<?php if ($params->get('link_category') && $this->item->catslug) : ?>
						<?php echo Lang::txt('COM_CONTENT_CATEGORY', $url); ?>
					<?php else : ?>
						<?php echo Lang::txt('COM_CONTENT_CATEGORY', $title); ?>
					<?php endif; ?>
				</dd>
			<?php endif; ?>
			<?php if ($params->get('show_create_date')) : ?>
				<dd class="create">
					<?php echo Lang::txt('COM_CONTENT_CREATED_DATE_ON', Date::of($this->item->created)->toLocal(Lang::txt('DATE_FORMAT_LC2'))); ?>
				</dd>
			<?php endif; ?>
			<?php if ($params->get('show_modify_date')) : ?>
				<dd class="modified">
					<?php echo Lang::txt('COM_CONTENT_LAST_UPDATED', Date::of($this->item->modified)->toLocal(Lang::txt('DATE_FORMAT_LC2'))); ?>
				</dd>
			<?php endif; ?>
			<?php if ($params->get('show_publish_date')) : ?>
				<dd class="published">
					<?php echo Lang::txt('COM_CONTENT_PUBLISHED_DATE_ON', Date::of($this->item->publish_up)->toLocal(Lang::txt('DATE_FORMAT_LC2'))); ?>
				</dd>
			<?php endif; ?>
			<?php if ($params->get('show_author') && !empty($this->item->author )) : ?>
				<dd class="createdby">
					<?php $author = $this->item->created_by_alias ? $this->item->created_by_alias : $this->item->author; ?>
					<?php if (!empty($this->item->contactid) && $params->get('link_author') == true): ?>
						<?php
						$needle = 'index.php?option=com_contact&view=contact&id=' . $this->item->contactid;
						$menu = App::get('menu');
						$item = $menu->getItems('link', $needle, true);
						$cntlink = !empty($item) ? $needle . '&Itemid=' . $item->id : $needle;

						echo Lang::txt('COM_CONTENT_WRITTEN_BY', '<a href="' . Route::url($cntlink) . '">' . $author . '</a>');
						?>
					<?php else: ?>
						<?php echo Lang::txt('COM_CONTENT_WRITTEN_BY', $author); ?>
					<?php endif; ?>
				</dd>
			<?php endif; ?>
			<?php if ($params->get('show_hits')): ?>
				<dd class="hits">
					<?php echo Lang::txt('COM_CONTENT_ARTICLE_HITS', $this->item->hits); ?>
				</dd>
			<?php endif; ?>
		<?php if ($useDefList) : ?>
			</dl>
		<?php endif; ?>

		<?php if (isset ($this->item->toc)): ?>
			<?php echo $this->item->toc; ?>
		<?php endif; ?>

		<?php if (isset($urls) && ((!empty($urls->urls_position) && ($urls->urls_position == '0'))
				|| ($params->get('urls_position') == '0' && empty($urls->urls_position)))
				|| (empty($urls->urls_position) && (!$params->get('urls_position')))): ?>
			<?php echo $this->loadTemplate('links'); ?>
		<?php endif; ?>

		<?php if ($params->get('access-view')):?>
			<?php if (isset($images->image_fulltext) && !empty($images->image_fulltext)): ?>
				<?php $imgfloat = (empty($images->float_fulltext)) ? $params->get('float_fulltext') : $images->float_fulltext; ?>
				<div class="img-fulltext-<?php echo htmlspecialchars($imgfloat); ?>">
					<img
						<?php if ($images->image_fulltext_caption):
							echo 'class="caption" title="' . htmlspecialchars($images->image_fulltext_caption) . '"';
						endif; ?>
						src="<?php echo htmlspecialchars($images->image_fulltext); ?>" alt="<?php echo htmlspecialchars($images->image_fulltext_alt); ?>"/>
				</div>
			<?php endif; ?>

			<?php
			if (!empty($this->item->pagination) && $this->item->pagination && !$this->item->paginationposition && !$this->item->paginationrelative):
				echo $this->item->pagination;
			endif;
			?>

			<?php echo $this->item->text; ?>

			<?php
			if (!empty($this->item->pagination) && $this->item->pagination && $this->item->paginationposition && !$this->item->paginationrelative):
				echo $this->item->pagination;
			endif;
			?>

			<?php
			if (isset($urls) && ((!empty($urls->urls_position) && ($urls->urls_position=='1')) || ($params->get('urls_position') == '1'))):
				echo $this->loadTemplate('links');
			endif;
			?>
			<?php //optional teaser intro text for guests ?>
		<?php elseif ($params->get('show_noauth') == true && User::isGuest()): ?>
			<?php echo $this->item->introtext; ?>
			<?php //Optional link to let them register to see the whole article. ?>
			<?php if ($params->get('show_readmore') && $this->item->fulltext != null):
				$link1 = Route::url('index.php?option=com_login');
				$link = new Hubzero\Utility\Uri($link1);?>
				<p class="readmore">
					<a href="<?php echo $link; ?>">
					<?php $attribs = json_decode($this->item->attribs); ?>
					<?php
					if ($attribs->alternative_readmore == null) :
						echo Lang::txt('COM_CONTENT_REGISTER_TO_READ_MORE');
					elseif ($readmore = $this->item->alternative_readmore):
						echo $readmore;
						if ($params->get('show_readmore_title', 0) != 0):
							echo Hubzero\Utility\Str::truncate($this->item->title, $params->get('readmore_limit'));
						endif;
					elseif ($params->get('show_readmore_title', 0) == 0):
						echo Lang::txt('COM_CONTENT_READ_MORE_TITLE');
					else :
						echo Lang::txt('COM_CONTENT_READ_MORE');
						echo Hubzero\Utility\Str::truncate($this->item->title, $params->get('readmore_limit'));
					endif; ?></a>
				</p>
			<?php endif; ?>
		<?php endif; ?>
		<?php
		if (!empty($this->item->pagination) && $this->item->pagination && $this->item->paginationposition && $this->item->paginationrelative):
			echo $this->item->pagination;
		endif;
		?>
	</div>
	<?php echo $this->item->event->afterDisplayContent; ?>
</div>
