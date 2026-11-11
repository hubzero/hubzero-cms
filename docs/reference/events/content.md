<!--
status: generated
source: Event::trigger('content.*') call sites and core/plugins/content/
-->

# Content events

Events in the `content` group. A plugin in `core/plugins/content/` receives an event by defining a public method with the event's name; the arguments are those the call site passes, in order.

## `content.onAfterContentSubmission`

Fired from:

- [`core/components/com_answers/site/controllers/questions.php:620`](../../../core/components/com_answers/site/controllers/questions.php#L620) with `['Question']`
- [`core/components/com_blog/site/controllers/entries.php:158`](../../../core/components/com_blog/site/controllers/entries.php#L158) with `['Blog']`
- [`core/components/com_groups/site/controllers/groups.php:360`](../../../core/components/com_groups/site/controllers/groups.php#L360) with `['Group']`
- [`core/components/com_projects/site/controllers/projects.php:677`](../../../core/components/com_projects/site/controllers/projects.php#L677) with `['Project']`
- [`core/components/com_resources/site/controllers/create.php:1522`](../../../core/components/com_resources/site/controllers/create.php#L1522) with `['Resource']`

No plugin in the source tree listens for this event.

## `content.onContentAfterDelete`

Fired from:

- [`core/components/com_media/admin/controllers/media.php:584`](../../../core/components/com_media/admin/controllers/media.php#L584) with `['com_media.file', &$object_file]`
- [`core/components/com_media/admin/controllers/media.php:613`](../../../core/components/com_media/admin/controllers/media.php#L613) with `['com_media.folder', &$object_file]`
- [`core/components/com_media/admin/controllers/media.php:710`](../../../core/components/com_media/admin/controllers/media.php#L710) with `['com_media.file', &$object_file]`
- [`core/components/com_media/admin/controllers/media.php:739`](../../../core/components/com_media/admin/controllers/media.php#L739) with `['com_media.folder', &$object_file]`

No plugin in the source tree listens for this event.

## `content.onContentAfterDisplay`

Fired from:

- [`core/components/com_content/site/controllers/articles.php:190`](../../../core/components/com_content/site/controllers/articles.php#L190) with `['com_content.article', &$item, &$params, $offset]`
- [`core/components/com_content/site/controllers/articles.php:808`](../../../core/components/com_content/site/controllers/articles.php#L808) with `['com_content.featured', &$item, &$item->params, 0]`
- [`core/components/com_content/site/controllers/articles.php:1272`](../../../core/components/com_content/site/controllers/articles.php#L1272) with `['com_content.category', &$item, &$item->params, 0]`

Listeners:

- `plg_content_opengraph` — [`onContentAfterDisplay($context, &$article, &$params, $page = 0)`](../../../core/plugins/content/opengraph/opengraph.php)

## `content.onContentAfterSave`

Fired from:

- [`core/components/com_media/admin/controllers/media.php:155`](../../../core/components/com_media/admin/controllers/media.php#L155) with `['com_media.folder', &$object_file, true]`
- [`core/components/com_media/admin/controllers/media.php:225`](../../../core/components/com_media/admin/controllers/media.php#L225) with `['com_media.folder', &$object_file, true]`
- [`core/components/com_media/admin/controllers/media.php:343`](../../../core/components/com_media/admin/controllers/media.php#L343) with `['com_media.file', &$object_file, true]`
- [`core/components/com_media/admin/controllers/media.php:493`](../../../core/components/com_media/admin/controllers/media.php#L493) with `['com_media.file', &$object_file, true]`

No plugin in the source tree listens for this event.

## `content.onContentAfterTitle`

Fired from:

- [`core/components/com_content/site/controllers/articles.php:184`](../../../core/components/com_content/site/controllers/articles.php#L184) with `['com_content.article', &$item, &$params, $offset]`
- [`core/components/com_content/site/controllers/articles.php:796`](../../../core/components/com_content/site/controllers/articles.php#L796) with `['com_content.featured', &$item, &$item->params, 0]`
- [`core/components/com_content/site/controllers/articles.php:1260`](../../../core/components/com_content/site/controllers/articles.php#L1260) with `['com_content.category', &$item, &$item->params, 0]`

No plugin in the source tree listens for this event.

## `content.onContentBeforeDelete`

Fired from:

- [`core/components/com_media/admin/controllers/media.php:565`](../../../core/components/com_media/admin/controllers/media.php#L565) with `['com_media.file', &$object_file]`
- [`core/components/com_media/admin/controllers/media.php:594`](../../../core/components/com_media/admin/controllers/media.php#L594) with `['com_media.folder', &$object_file]`
- [`core/components/com_media/admin/controllers/media.php:691`](../../../core/components/com_media/admin/controllers/media.php#L691) with `['com_media.file', &$object_file]`
- [`core/components/com_media/admin/controllers/media.php:720`](../../../core/components/com_media/admin/controllers/media.php#L720) with `['com_media.folder', &$object_file]`

Listeners:

- `plg_content_categories` — [`onContentBeforeDelete($context, $data)`](../../../core/plugins/content/categories/categories.php)

## `content.onContentBeforeDisplay`

Fired from:

- [`core/components/com_content/site/controllers/articles.php:187`](../../../core/components/com_content/site/controllers/articles.php#L187) with `['com_content.article', &$item, &$params, $offset]`
- [`core/components/com_content/site/controllers/articles.php:802`](../../../core/components/com_content/site/controllers/articles.php#L802) with `['com_content.featured', &$item, &$item->params, 0]`
- [`core/components/com_content/site/controllers/articles.php:1266`](../../../core/components/com_content/site/controllers/articles.php#L1266) with `['com_content.category', &$item, &$item->params, 0]`

Listeners:

- `plg_content_pagenavigation` — [`onContentBeforeDisplay($context, &$row, &$params, $page = 0)`](../../../core/plugins/content/pagenavigation/pagenavigation.php)
- `plg_content_vote` — [`onContentBeforeDisplay($context, &$row, &$params, $page = 0)`](../../../core/plugins/content/vote/vote.php)

## `content.onContentBeforeSave`

Fired from:

- [`core/components/com_answers/models/comment.php:81`](../../../core/components/com_answers/models/comment.php#L81) with `[ 'com_answers.comment.content', &$this, $this->isNew(), ]`
- [`core/components/com_answers/models/question.php:522`](../../../core/components/com_answers/models/question.php#L522) with `[ 'com_answers.question.question', &$this, $this->isNew(), ]`
- [`core/components/com_answers/models/response.php:437`](../../../core/components/com_answers/models/response.php#L437) with `[ 'com_answers.response.answer', &$this, $this->isNew(), ]`
- [`core/components/com_blog/models/comment.php:345`](../../../core/components/com_blog/models/comment.php#L345) with `[ 'com_blog.comment.content', &$this, $this->isNew(), ]`
- [`core/components/com_blog/models/entry.php:723`](../../../core/components/com_blog/models/entry.php#L723) with `[ 'com_blog.entry.content', &$this, $this->isNew(), ]`
- [`core/components/com_forum/models/category.php:327`](../../../core/components/com_forum/models/category.php#L327) with `[ 'com_forum.category.description', &$this, $this->isNew(), ]`
- [`core/components/com_forum/models/post.php:648`](../../../core/components/com_forum/models/post.php#L648) with `[ 'com_forum.post.comment', &$this, $this->isNew(), ]`
- [`core/components/com_groups/site/controllers/groups.php:680`](../../../core/components/com_groups/site/controllers/groups.php#L680) with `[ 'com_groups.group.public_desc', &$g_public_desc, ($this->task == 'new'), ]`
- [`core/components/com_kb/models/comment.php:379`](../../../core/components/com_kb/models/comment.php#L379) with `[ 'com_kb.comment.content', &$this, $this->isNew(), ]`
- [`core/components/com_media/admin/controllers/media.php:140`](../../../core/components/com_media/admin/controllers/media.php#L140) with `['com_media.folder', &$object_file, true]`
- [`core/components/com_media/admin/controllers/media.php:207`](../../../core/components/com_media/admin/controllers/media.php#L207) with `['com_media.folder', &$object_file, true]`
- [`core/components/com_media/admin/controllers/media.php:325`](../../../core/components/com_media/admin/controllers/media.php#L325) with `['com_media.file', &$object_file, true]`
- and 4 more call sites

Listeners:

- `plg_content_antispam` — [`onContentBeforeSave($context, $article, $isNew)`](../../../core/plugins/content/antispam/antispam.php)
- `plg_content_externalhref` — [`onContentBeforeSave($context, &$article, $isNew)`](../../../core/plugins/content/externalhref/externalhref.php)
- `plg_content_formathtml` — [`onContentBeforeSave($context, &$article, $isNew)`](../../../core/plugins/content/formathtml/formathtml.php)
- `plg_content_formatwiki` — [`onContentBeforeSave($context, &$article, $isNew)`](../../../core/plugins/content/formatwiki/formatwiki.php)

## `content.onContentPrepare`

Fired from:

- [`core/components/com_content/site/controllers/articles.php:181`](../../../core/components/com_content/site/controllers/articles.php#L181) with `['com_content.article', &$item, &$params, $offset]`
- [`core/components/com_content/site/controllers/articles.php:791`](../../../core/components/com_content/site/controllers/articles.php#L791) with `['com_content.featured', &$item, &$params, 0]`
- [`core/components/com_content/site/controllers/articles.php:1255`](../../../core/components/com_content/site/controllers/articles.php#L1255) with `['com_content.category', &$item, &$item->params, 0]`
- [`core/components/com_courses/site/views/assets/tmpl/text_wiki.php:35`](../../../core/components/com_courses/site/views/assets/tmpl/text_wiki.php#L35) with `[ 'com_courses.asset.content', &$this->model, &$config, ]`
- [`core/components/com_publications/models/publication.php:1390`](../../../core/components/com_publications/models/publication.php#L1390) with `[ 'com_publications.publication.description', &$this, &$config, ]`
- [`core/components/com_publications/models/publication.php:1460`](../../../core/components/com_publications/models/publication.php#L1460) with `[ 'com_publications.publication.release_notes', &$this, &$config, ]`
- [`core/components/com_publications/models/publication.php:1562`](../../../core/components/com_publications/models/publication.php#L1562) with `[ 'com_publications.publication.' . $field, &$this, &$config, ]`
- [`core/components/com_storefront/site/views/overview/tmpl/default.php:39`](../../../core/components/com_storefront/site/views/overview/tmpl/default.php#L39) with `['com_content.article', &$article, []]`
- [`core/libraries/Hubzero/Html/Builder/Content.php:35`](../../../core/libraries/Hubzero/Html/Builder/Content.php#L35) with `[$context, &$article, &$params, 0]`
- [`core/libraries/Hubzero/User/Group.php:1523`](../../../core/libraries/Hubzero/User/Group.php#L1523) with `[ 'com_groups.group.' . $type . '_desc', &$this, &$config, ]`
- [`core/libraries/Hubzero/User/Profile.php:1538`](../../../core/libraries/Hubzero/User/Profile.php#L1538) with `[ 'com_members.profile.bio', &$this, &$config, ]`
- [`core/plugins/members/profile/views/index/tmpl/default.php:276`](../../../core/plugins/members/profile/views/index/tmpl/default.php#L276) with `['com_content.article', &$page, &$params, 0]`

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

- [`core/modules/mod_resourcemenu/mod_resourcemenu.php:39`](../../../core/modules/mod_resourcemenu/mod_resourcemenu.php#L39) with `[ '', $obj, $this->params, ]`

No plugin in the source tree listens for this event.
