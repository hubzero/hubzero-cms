<!--
status: generated
source: Event::trigger('content.*') call sites and core/plugins/content/
-->

# Content events

Events in the `content` group. A plugin in `core/plugins/content/` receives an event by defining a public method with the event's name; the arguments are those the call site passes, in order.

## `content.onAfterContentSubmission`

Fired from:

- [`core/components/com_answers/site/controllers/questions.php:638`](../../../core/components/com_answers/site/controllers/questions.php#L638) with `['Question']`
- [`core/components/com_blog/site/controllers/entries.php:169`](../../../core/components/com_blog/site/controllers/entries.php#L169) with `['Blog']`
- [`core/components/com_groups/site/controllers/groups.php:379`](../../../core/components/com_groups/site/controllers/groups.php#L379) with `['Group']`
- [`core/components/com_projects/site/controllers/projects.php:725`](../../../core/components/com_projects/site/controllers/projects.php#L725) with `['Project']`
- [`core/components/com_resources/site/controllers/create.php:1604`](../../../core/components/com_resources/site/controllers/create.php#L1604) with `['Resource']`

No plugin in the source tree listens for this event.

## `content.onContentAfterDelete`

Fired from:

- [`core/components/com_media/admin/controllers/media.php:589`](../../../core/components/com_media/admin/controllers/media.php#L589) with `['com_media.file', &$object_file]`
- [`core/components/com_media/admin/controllers/media.php:609`](../../../core/components/com_media/admin/controllers/media.php#L609) with `['com_media.folder', &$object_file]`
- [`core/components/com_media/admin/controllers/media.php:692`](../../../core/components/com_media/admin/controllers/media.php#L692) with `['com_media.file', &$object_file]`
- [`core/components/com_media/admin/controllers/media.php:712`](../../../core/components/com_media/admin/controllers/media.php#L712) with `['com_media.folder', &$object_file]`

No plugin in the source tree listens for this event.

## `content.onContentAfterDisplay`

Fired from:

- [`core/components/com_content/site/controllers/articles.php:212`](../../../core/components/com_content/site/controllers/articles.php#L212) with `['com_content.article', &$item, &$params, $offset]`
- [`core/components/com_content/site/controllers/articles.php:854`](../../../core/components/com_content/site/controllers/articles.php#L854) with `['com_content.featured', &$item, &$item->params, 0]`
- [`core/components/com_content/site/controllers/articles.php:1321`](../../../core/components/com_content/site/controllers/articles.php#L1321) with `['com_content.category', &$item, &$item->params, 0]`

Listeners:

- `plg_content_opengraph` — [`onContentAfterDisplay($context, &$article, &$params, $page=0)`](../../../core/plugins/content/opengraph/opengraph.php)

## `content.onContentAfterSave`

Fired from:

- [`core/components/com_media/admin/controllers/media.php:154`](../../../core/components/com_media/admin/controllers/media.php#L154) with `['com_media.folder', &$object_file, true]`
- [`core/components/com_media/admin/controllers/media.php:225`](../../../core/components/com_media/admin/controllers/media.php#L225) with `['com_media.folder', &$object_file, true]`
- [`core/components/com_media/admin/controllers/media.php:351`](../../../core/components/com_media/admin/controllers/media.php#L351) with `['com_media.file', &$object_file, true]`
- [`core/components/com_media/admin/controllers/media.php:510`](../../../core/components/com_media/admin/controllers/media.php#L510) with `['com_media.file', &$object_file, true]`

No plugin in the source tree listens for this event.

## `content.onContentAfterTitle`

Fired from:

- [`core/components/com_content/site/controllers/articles.php:206`](../../../core/components/com_content/site/controllers/articles.php#L206) with `['com_content.article', &$item, &$params, $offset]`
- [`core/components/com_content/site/controllers/articles.php:848`](../../../core/components/com_content/site/controllers/articles.php#L848) with `['com_content.featured', &$item, &$item->params, 0]`
- [`core/components/com_content/site/controllers/articles.php:1315`](../../../core/components/com_content/site/controllers/articles.php#L1315) with `['com_content.category', &$item, &$item->params, 0]`

No plugin in the source tree listens for this event.

## `content.onContentBeforeDelete`

Fired from:

- [`core/components/com_media/admin/controllers/media.php:578`](../../../core/components/com_media/admin/controllers/media.php#L578) with `['com_media.file', &$object_file]`
- [`core/components/com_media/admin/controllers/media.php:598`](../../../core/components/com_media/admin/controllers/media.php#L598) with `['com_media.folder', &$object_file]`
- [`core/components/com_media/admin/controllers/media.php:681`](../../../core/components/com_media/admin/controllers/media.php#L681) with `['com_media.file', &$object_file]`
- [`core/components/com_media/admin/controllers/media.php:701`](../../../core/components/com_media/admin/controllers/media.php#L701) with `['com_media.folder', &$object_file]`

Listeners:

- `plg_content_categories` — [`onContentBeforeDelete($context, $data)`](../../../core/plugins/content/categories/categories.php)

## `content.onContentBeforeDisplay`

Fired from:

- [`core/components/com_content/site/controllers/articles.php:209`](../../../core/components/com_content/site/controllers/articles.php#L209) with `['com_content.article', &$item, &$params, $offset]`
- [`core/components/com_content/site/controllers/articles.php:851`](../../../core/components/com_content/site/controllers/articles.php#L851) with `['com_content.featured', &$item, &$item->params, 0]`
- [`core/components/com_content/site/controllers/articles.php:1318`](../../../core/components/com_content/site/controllers/articles.php#L1318) with `['com_content.category', &$item, &$item->params, 0]`

Listeners:

- `plg_content_pagenavigation` — [`onContentBeforeDisplay($context, &$row, &$params, $page=0)`](../../../core/plugins/content/pagenavigation/pagenavigation.php)
- `plg_content_vote` — [`onContentBeforeDisplay($context, &$row, &$params, $page=0)`](../../../core/plugins/content/vote/vote.php)

## `content.onContentBeforeSave`

Fired from:

- [`core/components/com_answers/models/comment.php:79`](../../../core/components/com_answers/models/comment.php#L79) with `[ 'com_answers.comment.content', &$this, $this->isNew() ]`
- [`core/components/com_answers/models/question.php:546`](../../../core/components/com_answers/models/question.php#L546) with `[ 'com_answers.question.question', &$this, $this->isNew() ]`
- [`core/components/com_answers/models/response.php:466`](../../../core/components/com_answers/models/response.php#L466) with `[ 'com_answers.response.answer', &$this, $this->isNew() ]`
- [`core/components/com_blog/models/comment.php:355`](../../../core/components/com_blog/models/comment.php#L355) with `[ 'com_blog.comment.content', &$this, $this->isNew() ]`
- [`core/components/com_blog/models/entry.php:744`](../../../core/components/com_blog/models/entry.php#L744) with `[ 'com_blog.entry.content', &$this, $this->isNew() ]`
- [`core/components/com_forum/models/category.php:336`](../../../core/components/com_forum/models/category.php#L336) with `[ 'com_forum.category.description', &$this, $this->isNew() ]`
- [`core/components/com_forum/models/post.php:679`](../../../core/components/com_forum/models/post.php#L679) with `[ 'com_forum.post.comment', &$this, $this->isNew() ]`
- [`core/components/com_groups/site/controllers/groups.php:692`](../../../core/components/com_groups/site/controllers/groups.php#L692) with `[ 'com_groups.group.public_desc', &$g_public_desc, ($this->_task == 'new') ]`
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
- [`core/components/com_content/site/controllers/articles.php:843`](../../../core/components/com_content/site/controllers/articles.php#L843) with `['com_content.featured', &$item, &$params, 0]`
- [`core/components/com_content/site/controllers/articles.php:1310`](../../../core/components/com_content/site/controllers/articles.php#L1310) with `['com_content.category', &$item, &$item->params, 0]`
- [`core/components/com_courses/site/views/assets/tmpl/text_wiki.php:24`](../../../core/components/com_courses/site/views/assets/tmpl/text_wiki.php#L24) with `[ 'com_courses.asset.content', &$this->model, &$config ]`
- [`core/components/com_publications/models/publication.php:1468`](../../../core/components/com_publications/models/publication.php#L1468) with `[ 'com_publications.publication.description', &$this, &$config ]`
- [`core/components/com_publications/models/publication.php:1542`](../../../core/components/com_publications/models/publication.php#L1542) with `[ 'com_publications.publication.release_notes', &$this, &$config ]`
- [`core/components/com_publications/models/publication.php:1652`](../../../core/components/com_publications/models/publication.php#L1652) with `[ 'com_publications.publication.' . $field, &$this, &$config ]`
- [`core/components/com_storefront/site/views/overview/tmpl/default.php:32`](../../../core/components/com_storefront/site/views/overview/tmpl/default.php#L32) with `['com_content.article', &$article, array()]`
- [`core/libraries/Hubzero/Html/Builder/Content.php:35`](../../../core/libraries/Hubzero/Html/Builder/Content.php#L35) with `[$context, &$article, &$params, 0]`
- [`core/libraries/Hubzero/User/Group.php:1569`](../../../core/libraries/Hubzero/User/Group.php#L1569) with `[ 'com_groups.group.' . $type . '_desc', &$this, &$config ]`
- [`core/libraries/Hubzero/User/Profile.php:1562`](../../../core/libraries/Hubzero/User/Profile.php#L1562) with `[ 'com_members.profile.bio', &$this, &$config ]`
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
