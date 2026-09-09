<!--
status: generated
source: Event::trigger('blog.*') call sites and core/plugins/blog/
-->

# Blog events

Events in the `blog` group. A plugin in `core/plugins/blog/` receives an event by defining a public method with the event's name; the arguments are those the call site passes, in order.

## `blog.onBlogView`

Fired from:

- [`core/components/com_blog/site/controllers/entries.php:172`](../../../core/components/com_blog/site/controllers/entries.php#L172) with `[$row]`
- [`core/plugins/groups/blog/blog.php:618`](../../../core/plugins/groups/blog/blog.php#L618) with `[$row]`
- [`core/plugins/members/blog/blog.php:470`](../../../core/plugins/members/blog/blog.php#L470) with `[$row]`

Listeners:

- `plg_blog_opengraph` — [`onBlogView($model)`](../../../core/plugins/blog/opengraph/opengraph.php)
- `plg_blog_twitter` — [`onBlogView($model)`](../../../core/plugins/blog/twitter/twitter.php)
