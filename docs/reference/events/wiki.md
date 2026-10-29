<!--
status: generated
source: Event::trigger('wiki.*') call sites and core/plugins/wiki/
-->

# Wiki events

Events in the `wiki` group. A plugin in `core/plugins/wiki/` receives an event by defining a public method with the event's name; the arguments are those the call site passes, in order.

## `wiki.onAfterDisplayContent`

Fired from:

- [`core/components/com_wiki/site/controllers/pages.php:265`](../../../core/components/com_wiki/site/controllers/pages.php#L265) with `[&$this->page, &$revision, $this->config]`

No plugin in the source tree listens for this event.

## `wiki.onAfterDisplayTitle`

Fired from:

- [`core/components/com_wiki/site/controllers/pages.php:259`](../../../core/components/com_wiki/site/controllers/pages.php#L259) with `[$this->page, &$revision, $this->config]`

No plugin in the source tree listens for this event.

## `wiki.onBeforeDisplayContent`

Fired from:

- [`core/components/com_wiki/site/controllers/pages.php:262`](../../../core/components/com_wiki/site/controllers/pages.php#L262) with `[&$this->page, &$revision, $this->config]`

No plugin in the source tree listens for this event.

## `wiki.onDisplayEditor`

No call site in the CMS fires this event; the listener may answer an event fired by another package or by an older plugin.

Listeners:

- `plg_wiki_editortoolbar` — [`onDisplayEditor($name, $id, $content, $cls='wiki-toolbar-content', $col=10, $row=35)`](../../../core/plugins/wiki/editortoolbar/editortoolbar.php)
- `plg_wiki_editorwykiwyg` — [`onDisplayEditor($name, $id, $content, $cls='wiki-toolbar-content', $col=10, $row=35)`](../../../core/plugins/wiki/editorwykiwyg/editorwykiwyg.php)

## `wiki.onGetWikiParser`

No call site in the CMS fires this event; the listener may answer an event fired by another package or by an older plugin.

Listeners:

- `plg_wiki_parserdefault` — [`onGetWikiParser($config, $getnew=false)`](../../../core/plugins/wiki/parserdefault/parserdefault.php)
- `plg_wiki_parsermarkdown` — [`onGetWikiParser($config, $getnew=false)`](../../../core/plugins/wiki/parsermarkdown/parsermarkdown.php)

## `wiki.onInitEditor`

No call site in the CMS fires this event; the listener may answer an event fired by another package or by an older plugin.

Listeners:

- `plg_wiki_editortoolbar` — [`onInitEditor()`](../../../core/plugins/wiki/editortoolbar/editortoolbar.php)
- `plg_wiki_editorwykiwyg` — [`onInitEditor()`](../../../core/plugins/wiki/editorwykiwyg/editorwykiwyg.php)

## `wiki.onWikiAfterBeforeComment`

Fired from:

- [`core/components/com_wiki/admin/controllers/comments.php:255`](../../../core/components/com_wiki/admin/controllers/comments.php#L255) with `[&$row, $isNew]`

No plugin in the source tree listens for this event.

## `wiki.onWikiAfterDelete`

Fired from:

- [`core/components/com_wiki/admin/controllers/pages.php:365`](../../../core/components/com_wiki/admin/controllers/pages.php#L365) with `[$id]`

No plugin in the source tree listens for this event.

## `wiki.onWikiAfterDeleteComment`

Fired from:

- [`core/components/com_wiki/admin/controllers/comments.php:323`](../../../core/components/com_wiki/admin/controllers/comments.php#L323) with `[$id]`

No plugin in the source tree listens for this event.

## `wiki.onWikiAfterDeleteVersion`

Fired from:

- [`core/components/com_wiki/admin/controllers/versions.php:354`](../../../core/components/com_wiki/admin/controllers/versions.php#L354) with `[$id]`

No plugin in the source tree listens for this event.

## `wiki.onWikiAfterSave`

Fired from:

- [`core/components/com_wiki/admin/controllers/pages.php:276`](../../../core/components/com_wiki/admin/controllers/pages.php#L276) with `[&$page, $isNew]`
- [`core/components/com_wiki/site/controllers/history.php:485`](../../../core/components/com_wiki/site/controllers/history.php#L485) with `[&$this->page, false]`
- [`core/components/com_wiki/site/controllers/pages.php:832`](../../../core/components/com_wiki/site/controllers/pages.php#L832) with `[&$this->page, $isNew]`

No plugin in the source tree listens for this event.

## `wiki.onWikiAfterSaveComment`

Fired from:

- [`core/components/com_wiki/admin/controllers/comments.php:271`](../../../core/components/com_wiki/admin/controllers/comments.php#L271) with `[&$row, $isNew]`

No plugin in the source tree listens for this event.

## `wiki.onWikiAfterSaveVersion`

Fired from:

- [`core/components/com_wiki/admin/controllers/versions.php:246`](../../../core/components/com_wiki/admin/controllers/versions.php#L246) with `[&$version, $isNew]`

No plugin in the source tree listens for this event.

## `wiki.onWikiBeforeSave`

Fired from:

- [`core/components/com_wiki/admin/controllers/pages.php:252`](../../../core/components/com_wiki/admin/controllers/pages.php#L252) with `[&$page, $isNew]`
- [`core/components/com_wiki/site/controllers/pages.php:708`](../../../core/components/com_wiki/site/controllers/pages.php#L708) with `[&$this->page, $isNew]`

No plugin in the source tree listens for this event.

## `wiki.onWikiBeforeSaveVersion`

Fired from:

- [`core/components/com_wiki/admin/controllers/versions.php:211`](../../../core/components/com_wiki/admin/controllers/versions.php#L211) with `[&$version, $isNew]`

No plugin in the source tree listens for this event.

## `wiki.onWikiParseText`

Fired from:

- [`core/plugins/content/formatwiki/formatwiki.php:150`](../../../core/plugins/content/formatwiki/formatwiki.php#L150) with `[$content, $params, $params['fullparse'], true]`

Listeners:

- `plg_wiki_parserdefault` — [`onWikiParseText($text, $config, $fullparse=true, $getnew=false)`](../../../core/plugins/wiki/parserdefault/parserdefault.php)
- `plg_wiki_parsermarkdown` — [`onWikiParseText($text, $config, $fullparse=true, $getnew=false)`](../../../core/plugins/wiki/parsermarkdown/parsermarkdown.php)
