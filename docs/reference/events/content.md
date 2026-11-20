<!--
status: generated
source: Event::trigger('content.*') call sites and core/plugins/content/
-->

# Content events

Events in the `content` group. A plugin in `core/plugins/content/` receives an event by defining a public method with the event's name; the arguments are those the call site passes, in order.

## `content.onAfterContentSubmission`

Fired from:

- [`core/components/com_answers/site/controllers/questions.php:618`](../../../core/components/com_answers/site/controllers/questions.php#L618) with `['Question']`
- [`core/components/com_blog/site/controllers/entries.php:160`](../../../core/components/com_blog/site/controllers/entries.php#L160) with `['Blog']`
- [`core/components/com_groups/site/controllers/groups.php:373`](../../../core/components/com_groups/site/controllers/groups.php#L373) with `['Group']`
- [`core/components/com_projects/site/controllers/projects.php:667`](../../../core/components/com_projects/site/controllers/projects.php#L667) with `['Project']`
- [`core/components/com_resources/site/controllers/create.php:1544`](../../../core/components/com_resources/site/controllers/create.php#L1544) with `['Resource']`

No plugin in the source tree listens for this event.

## `content.onContentAfterDelete`

Fired from:

- [`core/components/com_media/admin/controllers/media.php:586`](../../../core/components/com_media/admin/controllers/media.php#L586) with `['com_media.file', &$object_file]`
- [`core/components/com_media/admin/controllers/media.php:615`](../../../core/components/com_media/admin/controllers/media.php#L615) with `['com_media.folder', &$object_file]`
- [`core/components/com_media/admin/controllers/media.php:714`](../../../core/components/com_media/admin/controllers/media.php#L714) with `['com_media.file', &$object_file]`
- [`core/components/com_media/admin/controllers/media.php:743`](../../../core/components/com_media/admin/controllers/media.php#L743) with `['com_media.folder', &$object_file]`

No plugin in the source tree listens for this event.

## `content.onContentAfterDisplay`

Fired from:

- [`core/components/com_content/site/controllers/articles.php:185`](../../../core/components/com_content/site/controllers/articles.php#L185) with `['com_content.article', &$item, &$params, $offset]`
- [`core/components/com_content/site/controllers/articles.php:807`](../../../core/components/com_content/site/controllers/articles.php#L807) with `['com_content.featured', &$item, &$item->params, 0]`
- [`core/components/com_content/site/controllers/articles.php:1262`](../../../core/components/com_content/site/controllers/articles.php#L1262) with `['com_content.category', &$item, &$item->params, 0]`

Listeners:

- `plg_content_opengraph` — [`onContentAfterDisplay($context, &$article, &$params, $page = 0)`](../../../core/plugins/content/opengraph/opengraph.php)

## `content.onContentAfterSave`

Fired from:

- [`core/components/com_media/admin/controllers/media.php:155`](../../../core/components/com_media/admin/controllers/media.php#L155) with `['com_media.folder', &$object_file, true]`
- [`core/components/com_media/admin/controllers/media.php:227`](../../../core/components/com_media/admin/controllers/media.php#L227) with `['com_media.folder', &$object_file, true]`
- [`core/components/com_media/admin/controllers/media.php:347`](../../../core/components/com_media/admin/controllers/media.php#L347) with `['com_media.file', &$object_file, true]`
- [`core/components/com_media/admin/controllers/media.php:493`](../../../core/components/com_media/admin/controllers/media.php#L493) with `['com_media.file', &$object_file, true]`

No plugin in the source tree listens for this event.

## `content.onContentAfterTitle`

Fired from:

- [`core/components/com_content/site/controllers/articles.php:179`](../../../core/components/com_content/site/controllers/articles.php#L179) with `['com_content.article', &$item, &$params, $offset]`
- [`core/components/com_content/site/controllers/articles.php:795`](../../../core/components/com_content/site/controllers/articles.php#L795) with `['com_content.featured', &$item, &$item->params, 0]`
- [`core/components/com_content/site/controllers/articles.php:1250`](../../../core/components/com_content/site/controllers/articles.php#L1250) with `['com_content.category', &$item, &$item->params, 0]`

No plugin in the source tree listens for this event.

## `content.onContentBeforeDelete`

Fired from:

- [`core/components/com_media/admin/controllers/media.php:567`](../../../core/components/com_media/admin/controllers/media.php#L567) with `['com_media.file', &$object_file]`
- [`core/components/com_media/admin/controllers/media.php:596`](../../../core/components/com_media/admin/controllers/media.php#L596) with `['com_media.folder', &$object_file]`
- [`core/components/com_media/admin/controllers/media.php:695`](../../../core/components/com_media/admin/controllers/media.php#L695) with `['com_media.file', &$object_file]`
- [`core/components/com_media/admin/controllers/media.php:724`](../../../core/components/com_media/admin/controllers/media.php#L724) with `['com_media.folder', &$object_file]`

Listeners:

- `plg_content_categories` — [`onContentBeforeDelete($context, $data)`](../../../core/plugins/content/categories/categories.php)

## `content.onContentBeforeDisplay`

Fired from:

- [`core/components/com_content/site/controllers/articles.php:182`](../../../core/components/com_content/site/controllers/articles.php#L182) with `['com_content.article', &$item, &$params, $offset]`
- [`core/components/com_content/site/controllers/articles.php:801`](../../../core/components/com_content/site/controllers/articles.php#L801) with `['com_content.featured', &$item, &$item->params, 0]`
- [`core/components/com_content/site/controllers/articles.php:1256`](../../../core/components/com_content/site/controllers/articles.php#L1256) with `['com_content.category', &$item, &$item->params, 0]`

Listeners:

- `plg_content_pagenavigation` — [`onContentBeforeDisplay($context, &$row, &$params, $page = 0)`](../../../core/plugins/content/pagenavigation/pagenavigation.php)
- `plg_content_vote` — [`onContentBeforeDisplay($context, &$row, &$params, $page = 0)`](../../../core/plugins/content/vote/vote.php)

## `content.onContentBeforeSave`

Fired from:

- [`core/components/com_answers/models/comment.php:81`](../../../core/components/com_answers/models/comment.php#L81) with `[ 'com_answers.comment.content', &$this, $this->isNew(), ]`
- [`core/components/com_answers/models/question.php:534`](../../../core/components/com_answers/models/question.php#L534) with `[ 'com_answers.question.question', &$this, $this->isNew(), ]`
- [`core/components/com_answers/models/response.php:439`](../../../core/components/com_answers/models/response.php#L439) with `[ 'com_answers.response.answer', &$this, $this->isNew(), ]`
- [`core/components/com_blog/models/comment.php:348`](../../../core/components/com_blog/models/comment.php#L348) with `[ 'com_blog.comment.content', &$this, $this->isNew(), ]`
- [`core/components/com_blog/models/entry.php:720`](../../../core/components/com_blog/models/entry.php#L720) with `[ 'com_blog.entry.content', &$this, $this->isNew(), ]`
- [`core/components/com_forum/models/category.php:329`](../../../core/components/com_forum/models/category.php#L329) with `[ 'com_forum.category.description', &$this, $this->isNew(), ]`
- [`core/components/com_forum/models/post.php:656`](../../../core/components/com_forum/models/post.php#L656) with `[ 'com_forum.post.comment', &$this, $this->isNew(), ]`
- [`core/components/com_groups/site/controllers/groups.php:707`](../../../core/components/com_groups/site/controllers/groups.php#L707) with `[ 'com_groups.group.public_desc', &$g_public_desc, ($this->task == 'new'), ]`
- [`core/components/com_kb/models/comment.php:381`](../../../core/components/com_kb/models/comment.php#L381) with `[ 'com_kb.comment.content', &$this, $this->isNew(), ]`
- [`core/components/com_media/admin/controllers/media.php:140`](../../../core/components/com_media/admin/controllers/media.php#L140) with `['com_media.folder', &$object_file, true]`
- [`core/components/com_media/admin/controllers/media.php:209`](../../../core/components/com_media/admin/controllers/media.php#L209) with `['com_media.folder', &$object_file, true]`
- [`core/components/com_media/admin/controllers/media.php:329`](../../../core/components/com_media/admin/controllers/media.php#L329) with `['com_media.file', &$object_file, true]`
- and 4 more call sites

Listeners:

- `plg_content_antispam` — [`onContentBeforeSave($context, $article, $isNew)`](../../../core/plugins/content/antispam/antispam.php)
- `plg_content_externalhref` — [`onContentBeforeSave($context, &$article, $isNew)`](../../../core/plugins/content/externalhref/externalhref.php)
- `plg_content_formathtml` — [`onContentBeforeSave($context, &$article, $isNew)`](../../../core/plugins/content/formathtml/formathtml.php)
- `plg_content_formatwiki` — [`onContentBeforeSave($context, &$article, $isNew)`](../../../core/plugins/content/formatwiki/formatwiki.php)

## `content.onContentPrepare`

Fired from:

- [`core/components/com_content/site/controllers/articles.php:176`](../../../core/components/com_content/site/controllers/articles.php#L176) with `['com_content.article', &$item, &$params, $offset]`
- [`core/components/com_content/site/controllers/articles.php:790`](../../../core/components/com_content/site/controllers/articles.php#L790) with `['com_content.featured', &$item, &$params, 0]`
- [`core/components/com_content/site/controllers/articles.php:1245`](../../../core/components/com_content/site/controllers/articles.php#L1245) with `['com_content.category', &$item, &$item->params, 0]`
- [`core/components/com_courses/site/views/assets/tmpl/text_wiki.php:35`](../../../core/components/com_courses/site/views/assets/tmpl/text_wiki.php#L35) with `[ 'com_courses.asset.content', &$this->model, &$config, ]`
- [`core/components/com_publications/models/publication.php:1405`](../../../core/components/com_publications/models/publication.php#L1405) with `[ 'com_publications.publication.description', &$this, &$config, ]`
- [`core/components/com_publications/models/publication.php:1475`](../../../core/components/com_publications/models/publication.php#L1475) with `[ 'com_publications.publication.release_notes', &$this, &$config, ]`
- [`core/components/com_publications/models/publication.php:1576`](../../../core/components/com_publications/models/publication.php#L1576) with `[ 'com_publications.publication.' . $field, &$this, &$config, ]`
- [`core/components/com_storefront/site/views/overview/tmpl/default.php:39`](../../../core/components/com_storefront/site/views/overview/tmpl/default.php#L39) with `['com_content.article', &$article, []]`
- [`core/libraries/Hubzero/Html/Builder/Content.php:35`](../../../core/libraries/Hubzero/Html/Builder/Content.php#L35) with `[$context, &$article, &$params, 0]`
- [`core/libraries/Hubzero/User/Group.php:1491`](../../../core/libraries/Hubzero/User/Group.php#L1491) with `[ 'com_groups.group.' . $type . '_desc', &$this, &$config, ]`
- [`core/libraries/Hubzero/User/Profile.php:1512`](../../../core/libraries/Hubzero/User/Profile.php#L1512) with `[ 'com_members.profile.bio', &$this, &$config, ]`
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

- [`core/modules/mod_resourcemenu/mod_resourcemenu.php:48`](../../../core/modules/mod_resourcemenu/mod_resourcemenu.php#L48) with `[ '', $obj, $this->params, ]`

No plugin in the source tree listens for this event.
