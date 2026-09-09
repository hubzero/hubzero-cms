<!--
status: generated
source: Event::trigger('content.*') call sites and core/plugins/content/
-->

# Content events

Events in the `content` group. A plugin in `core/plugins/content/` receives an event by defining a public method with the event's name; the arguments are those the call site passes, in order.

## `content.onAfterContentSubmission`

Fired from:

- [`core/components/com_answers/site/controllers/questions.php:544`](../../../core/components/com_answers/site/controllers/questions.php#L544) with `['Question']`
- [`core/components/com_blog/site/controllers/entries.php:169`](../../../core/components/com_blog/site/controllers/entries.php#L169) with `['Blog']`
- [`core/components/com_groups/site/controllers/groups.php:379`](../../../core/components/com_groups/site/controllers/groups.php#L379) with `['Group']`
- [`core/components/com_projects/site/controllers/projects.php:724`](../../../core/components/com_projects/site/controllers/projects.php#L724) with `['Project']`
- [`core/components/com_resources/site/controllers/create.php:1503`](../../../core/components/com_resources/site/controllers/create.php#L1503) with `['Resource']`

No plugin in the source tree listens for this event.

## `content.onContentAfterDelete`

Fired from:

- [`core/components/com_media/admin/controllers/media.php:581`](../../../core/components/com_media/admin/controllers/media.php#L581) with `['com_media.file', &$object_file]`
- [`core/components/com_media/admin/controllers/media.php:601`](../../../core/components/com_media/admin/controllers/media.php#L601) with `['com_media.folder', &$object_file]`
- [`core/components/com_media/admin/controllers/media.php:684`](../../../core/components/com_media/admin/controllers/media.php#L684) with `['com_media.file', &$object_file]`
- [`core/components/com_media/admin/controllers/media.php:704`](../../../core/components/com_media/admin/controllers/media.php#L704) with `['com_media.folder', &$object_file]`

No plugin in the source tree listens for this event.

## `content.onContentAfterDisplay`

Fired from:

- [`core/components/com_content/site/controllers/articles.php:212`](../../../core/components/com_content/site/controllers/articles.php#L212) with `['com_content.article', &$item, &$params, $offset]`
- [`core/components/com_content/site/controllers/articles.php:847`](../../../core/components/com_content/site/controllers/articles.php#L847) with `['com_content.featured', &$item, &$item->params, 0]`
- [`core/components/com_content/site/controllers/articles.php:1314`](../../../core/components/com_content/site/controllers/articles.php#L1314) with `['com_content.category', &$item, &$item->params, 0]`

Listeners:

- `plg_content_opengraph` — [`onContentAfterDisplay($context, &$article, &$params, $page=0)`](../../../core/plugins/content/opengraph/opengraph.php)

## `content.onContentAfterSave`

Fired from:

- [`core/components/com_media/admin/controllers/media.php:154`](../../../core/components/com_media/admin/controllers/media.php#L154) with `['com_media.folder', &$object_file, true]`
- [`core/components/com_media/admin/controllers/media.php:225`](../../../core/components/com_media/admin/controllers/media.php#L225) with `['com_media.folder', &$object_file, true]`
- [`core/components/com_media/admin/controllers/media.php:351`](../../../core/components/com_media/admin/controllers/media.php#L351) with `['com_media.file', &$object_file, true]`
- [`core/components/com_media/admin/controllers/media.php:502`](../../../core/components/com_media/admin/controllers/media.php#L502) with `['com_media.file', &$object_file, true]`

No plugin in the source tree listens for this event.

## `content.onContentAfterTitle`

Fired from:

- [`core/components/com_content/site/controllers/articles.php:206`](../../../core/components/com_content/site/controllers/articles.php#L206) with `['com_content.article', &$item, &$params, $offset]`
- [`core/components/com_content/site/controllers/articles.php:841`](../../../core/components/com_content/site/controllers/articles.php#L841) with `['com_content.featured', &$item, &$item->params, 0]`
- [`core/components/com_content/site/controllers/articles.php:1308`](../../../core/components/com_content/site/controllers/articles.php#L1308) with `['com_content.category', &$item, &$item->params, 0]`

No plugin in the source tree listens for this event.

## `content.onContentBeforeDelete`

Fired from:

- [`core/components/com_media/admin/controllers/media.php:570`](../../../core/components/com_media/admin/controllers/media.php#L570) with `['com_media.file', &$object_file]`
- [`core/components/com_media/admin/controllers/media.php:590`](../../../core/components/com_media/admin/controllers/media.php#L590) with `['com_media.folder', &$object_file]`
- [`core/components/com_media/admin/controllers/media.php:673`](../../../core/components/com_media/admin/controllers/media.php#L673) with `['com_media.file', &$object_file]`
- [`core/components/com_media/admin/controllers/media.php:693`](../../../core/components/com_media/admin/controllers/media.php#L693) with `['com_media.folder', &$object_file]`

Listeners:

- `plg_content_categories` — [`onContentBeforeDelete($context, $data)`](../../../core/plugins/content/categories/categories.php)

## `content.onContentBeforeDisplay`

Fired from:

- [`core/components/com_content/site/controllers/articles.php:209`](../../../core/components/com_content/site/controllers/articles.php#L209) with `['com_content.article', &$item, &$params, $offset]`
- [`core/components/com_content/site/controllers/articles.php:844`](../../../core/components/com_content/site/controllers/articles.php#L844) with `['com_content.featured', &$item, &$item->params, 0]`
- [`core/components/com_content/site/controllers/articles.php:1311`](../../../core/components/com_content/site/controllers/articles.php#L1311) with `['com_content.category', &$item, &$item->params, 0]`

Listeners:

- `plg_content_pagenavigation` — [`onContentBeforeDisplay($context, &$row, &$params, $page=0)`](../../../core/plugins/content/pagenavigation/pagenavigation.php)
- `plg_content_vote` — [`onContentBeforeDisplay($context, &$row, &$params, $page=0)`](../../../core/plugins/content/vote/vote.php)

## `content.onContentBeforeSave`

Fired from:

- [`core/components/com_answers/models/comment.php:79`](../../../core/components/com_answers/models/comment.php#L79) with `[ 'com_answers.comment.content', &$this, $this->isNew() ]`
- [`core/components/com_answers/models/question.php:546`](../../../core/components/com_answers/models/question.php#L546) with `[ 'com_answers.question.question', &$this, $this->isNew() ]`
- [`core/components/com_answers/models/response.php:466`](../../../core/components/com_answers/models/response.php#L466) with `[ 'com_answers.response.answer', &$this, $this->isNew() ]`
- [`core/components/com_blog/models/comment.php:355`](../../../core/components/com_blog/models/comment.php#L355) with `[ 'com_blog.comment.content', &$this, $this->isNew() ]`
- [`core/components/com_blog/models/entry.php:743`](../../../core/components/com_blog/models/entry.php#L743) with `[ 'com_blog.entry.content', &$this, $this->isNew() ]`
- [`core/components/com_forum/models/category.php:336`](../../../core/components/com_forum/models/category.php#L336) with `[ 'com_forum.category.description', &$this, $this->isNew() ]`
- [`core/components/com_forum/models/post.php:678`](../../../core/components/com_forum/models/post.php#L678) with `[ 'com_forum.post.comment', &$this, $this->isNew() ]`
- [`core/components/com_groups/site/controllers/groups.php:683`](../../../core/components/com_groups/site/controllers/groups.php#L683) with `[ 'com_groups.group.public_desc', &$g_public_desc, ($this->_task == 'new') ]`
- [`core/components/com_kb/models/comment.php:403`](../../../core/components/com_kb/models/comment.php#L403) with `[ 'com_kb.comment.content', &$this, $this->isNew() ]`
- [`core/components/com_media/admin/controllers/media.php:140`](../../../core/components/com_media/admin/controllers/media.php#L140) with `['com_media.folder', &$object_file, true]`
- [`core/components/com_media/admin/controllers/media.php:208`](../../../core/components/com_media/admin/controllers/media.php#L208) with `['com_media.folder', &$object_file, true]`
- [`core/components/com_media/admin/controllers/media.php:334`](../../../core/components/com_media/admin/controllers/media.php#L334) with `['com_media.file', &$object_file, true]`
- and 4 more call sites

Listeners:

- `plg_content_antispam` — [`onContentBeforeSave($context, $article, $isNew)`](../../../core/plugins/content/antispam/antispam.php)
- `plg_content_externalhref` — [`onContentBeforeSave($context, &$article, $isNew)`](../../../core/plugins/content/externalhref/externalhref.php)
- `plg_content_formathtml` — [`onContentBeforeSave($context, &$article, $isNew)`](../../../core/plugins/content/formathtml/formathtml.php)
- `plg_content_formatwiki` — [`onContentBeforeSave($context, &$article, $isNew)`](../../../core/plugins/content/formatwiki/formatwiki.php)

## `content.onContentPrepare`

Fired from:

- [`core/components/com_content/site/controllers/articles.php:203`](../../../core/components/com_content/site/controllers/articles.php#L203) with `['com_content.article', &$item, &$params, $offset]`
- [`core/components/com_content/site/controllers/articles.php:836`](../../../core/components/com_content/site/controllers/articles.php#L836) with `['com_content.featured', &$item, &$params, 0]`
- [`core/components/com_content/site/controllers/articles.php:1303`](../../../core/components/com_content/site/controllers/articles.php#L1303) with `['com_content.category', &$item, &$item->params, 0]`
- [`core/components/com_courses/site/views/assets/tmpl/text_wiki.php:24`](../../../core/components/com_courses/site/views/assets/tmpl/text_wiki.php#L24) with `[ 'com_courses.asset.content', &$this->model, &$config ]`
- [`core/components/com_publications/models/publication.php:1468`](../../../core/components/com_publications/models/publication.php#L1468) with `[ 'com_publications.publication.description', &$this, &$config ]`
- [`core/components/com_publications/models/publication.php:1542`](../../../core/components/com_publications/models/publication.php#L1542) with `[ 'com_publications.publication.release_notes', &$this, &$config ]`
- [`core/components/com_publications/models/publication.php:1652`](../../../core/components/com_publications/models/publication.php#L1652) with `[ 'com_publications.publication.' . $field, &$this, &$config ]`
- [`core/components/com_storefront/site/views/overview/tmpl/default.php:32`](../../../core/components/com_storefront/site/views/overview/tmpl/default.php#L32) with `['com_content.article', &$article, array()]`
- [`core/libraries/Hubzero/Html/Builder/Content.php:35`](../../../core/libraries/Hubzero/Html/Builder/Content.php#L35) with `[$context, &$article, &$params, 0]`
- [`core/libraries/Hubzero/User/Group.php:1552`](../../../core/libraries/Hubzero/User/Group.php#L1552) with `[ 'com_groups.group.' . $type . '_desc', &$this, &$config ]`
- [`core/libraries/Hubzero/User/Profile.php:1557`](../../../core/libraries/Hubzero/User/Profile.php#L1557) with `[ 'com_members.profile.bio', &$this, &$config ]`
- [`core/plugins/members/profile/views/index/tmpl/default.php:256`](../../../core/plugins/members/profile/views/index/tmpl/default.php#L256) with `['com_content.article', &$page, &$params, 0]`

Listeners:

- `plg_content_emailcloak` — [`onContentPrepare($context, &$row, &$params, $page = 0)`](../../../core/plugins/content/emailcloak/emailcloak.php)
- `plg_content_externalhref` — [`onContentPrepare($context, &$article, &$params, $page = 0)`](../../../core/plugins/content/externalhref/externalhref.php)
- `plg_content_formathtml` — [`onContentPrepare($context, &$article, &$params, $page = 0)`](../../../core/plugins/content/formathtml/formathtml.php)
- `plg_content_formatwiki` — [`onContentPrepare($context, &$article, &$params, $page = 0)`](../../../core/plugins/content/formatwiki/formatwiki.php)
- `plg_content_geshi` — [`onContentPrepare($context, &$article, &$params, $page = 0)`](../../../core/plugins/content/geshi/geshi.php)
- `plg_content_loadmodule` — [`onContentPrepare($context, &$article, &$params, $page = 0)`](../../../core/plugins/content/loadmodule/loadmodule.php)
- `plg_content_pagebreak` — [`onContentPrepare($context, &$row, &$params, $page = 0)`](../../../core/plugins/content/pagebreak/pagebreak.php)
- `plg_content_xhubtags` — [`onContentPrepare($context, &$article, &$params, $page = 0)`](../../../core/plugins/content/xhubtags/xhubtags.php)

## `content.onPrepareContent`

Fired from:

- [`core/modules/mod_resourcemenu/helper.php:34`](../../../core/modules/mod_resourcemenu/helper.php#L34) with `[ '', $obj, $this->params ]`

No plugin in the source tree listens for this event.
