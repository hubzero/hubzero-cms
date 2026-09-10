<!--
status: rewritten
reviewed-against: 2.4-main @ 91d03d0a23
reviewed: 2026-09-10
screenshots: none
source: https://help.hubzero.org/documentation/240/webdevs/supergroups/custom_macros
source-id: 3522
modified: 2014-09-10
-->
# Custom macros

A macro is the `[[Name(arguments)]]` markup a page author writes to call PHP.
A super group can add macros of its own and replace any of the ones the hub
ships, by putting classes in the group's `macros/` directory.

Reach for one when the group's own editors need something computed *inside*
ordinary page text, and need it more than once. The Coastal Resilience
Center's macro is `[[GaugeStatus(pier-7)]]`: it reads the latest reading for a
named gauge and prints it. A communications officer with no PHP writes that
line into any page, in the middle of a paragraph, and it stays right.

The alternatives are worth knowing before you write one:

| The group needs | Use |
|---|---|
| A snippet inside prose the editors maintain | a macro |
| A block in a template position | a group module |
| A whole page in code | a [PHP page](04-php_pages.md) |
| Its own URLs and records | a [component](07-components.md) |

```
app/site/groups/<gidNumber>/macros/
```

The directory is created with the rest of the group's skeleton, and is filled
from the server or through the group's
[repository](../14-supergroups-gitlab.md) — the group file browser
reaches only `uploads`.

## Where group macros apply

The group's `macros/` directory is added to the macro search path in two
places, both in com_groups: when a group page version is parsed
([`models/page/version.php`](../../../core/components/com_groups/models/page/version.php))
and when a group module is parsed
([`models/module.php`](../../../core/components/com_groups/models/module.php)).
Both pass the directory as `alt_macro_path` to the
[`content/formathtml`](../../../core/plugins/content/formathtml) plugin, which
parses it before its own `macros/` directory.

So group macros work in group pages and group modules, and only for that one
group. They do not apply to the group's wiki: the wiki runs through
[`wiki/parserdefault`](../../../core/plugins/wiki/parserdefault), which has a
fixed macro path and no group hook. A macro that works on a group page and
vanishes on the group's wiki page is not broken; it is out of scope.

## Writing one

The file name is the macro name, lowercased, plus `.php`. The class is the
macro name in the `Plugins\Content\Formathtml\Macros` namespace, extending
`Plugins\Content\Formathtml\Macro`.

```php
<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2026 Purdue University. All rights reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Plugins\Content\Formathtml\Macros;

use Plugins\Content\Formathtml\Macro;

/**
 * Macro to greet the reader
 */
class HelloWorld extends Macro
{
	/**
	 * Description of the macro, shown in the macro list
	 *
	 * @return  string
	 */
	public function description()
	{
		$txt = array();
		$txt['wiki'] = 'Outputs "Hello world"';
		$txt['html'] = '<p>Outputs "Hello world"</p>';
		return $txt['html'];
	}

	/**
	 * Generate macro output
	 *
	 * @return  string
	 */
	public function render()
	{
		return 'Hello World, args = ' . $this->args;
	}
}
```

That is [`macros/helloworld.php`](../../../core/plugins/content/formathtml/macros/helloworld.php)
from core, which is the shortest working example in the tree. Copy it as a
starting point: change the file name, the class name and `render()`, and you
have the centre's `macros/gaugestatus.php`.

The header above is the one this repository uses. The CMS is MIT licensed, and
that is what the sample says. Older documentation showed the same header
declaring LGPLv3; that is out of date. A group's own code carries whatever
notice the group's owner chooses.

### The rules

- **`render()` is required.** It returns the markup that replaces the macro
  call. Return a string; returning nothing removes the macro from the page.
- **`description()` is optional** and is used only where the hub lists
  available macros.
- **`$this->args`** is the raw text between the parentheses. The base class
  gives `getArguments()`, which splits it on commas and trims, and
  `getArgument($index, $default)`.
- The parser also sets `$this->option`, `$this->scope`, `$this->pagename`,
  `$this->domain`, `$this->pageid` and `$this->filepath` before calling
  `render()`. In a group page `pagename` and `domain` are the group's alias
  and `filepath` is the group's `uploads` directory.
- **`public $allowPartial = true`** lets the macro run during a partial parse
  as well. The base class defaults it to `false`, and without it a partial
  parse renders *Macro "Name" not allowed.* instead of your output.
- A macro name is matched case-insensitively and lowercased before the file is
  looked up, so `[[GaugeStatus()]]`, `[[gaugestatus()]]` and
  `[[GAUGESTATUS()]]` all load `gaugestatus.php`.

> **Warning:** A macro's output is inserted into the page after purification,
> so nothing strips it. Escape anything that came from the database or from
> `$this->args` before you print it. `[[GaugeStatus(<script>…)]]` is a stored
> cross-site scripting hole if `render()` echoes its argument raw.

> **Note:** If the exact file name is not found, the parser pluralises the
> last segment and tries again — `[[Resource()]]` finds `resources.php`. That
> is a compatibility shim for one renamed core macro, and it means a group
> macro named in the singular can be shadowed by a core macro named in the
> plural. Name yours so the two never meet.

### Macros in a subdirectory

A dot in the macro name is a directory separator. `[[Group.Members()]]` loads
`group/members.php` and expects
`Plugins\Content\Formathtml\Macros\Group\Members`. Recreate the same
directory structure under the group's `macros/` folder.

## Overriding a macro the hub ships

1. Copy the original out of
   [`core/plugins/content/formathtml/macros/`](../../../core/plugins/content/formathtml/macros),
   keeping its path and file name, into the group's `macros/` directory.
2. Change what you need in `render()`.

Keep the class name and the namespace exactly as they were. The parser
searches the group directory first and stops at the first file it finds, so
the group's copy is the one that is included; because both files declare the
same class, changing the name would break every other reference to it.

> **Warning:** The override is per group, but the parser caches the macro
> *instance* in a static property keyed by macro name, for the whole request.
> The first group content parsed on a request fixes which version of the class
> is loaded and which object is reused. On a normal page that is the group
> being viewed, but if you render two groups' content in one request — a
> digest, a search result page, a module that pulls another group's text —
> expect the first one to win.

## When a macro does not appear

A macro whose file cannot be found renders as nothing at all — no error, no
placeholder, the markup simply vanishes. So does one whose class name does not
match what the parser expects, because the file is included and then
`class_exists()` fails. If a call disappears from the page, check in this
order:

1. the file name against the macro name, lowercased, with `.php`;
2. the namespace line, `Plugins\Content\Formathtml\Macros`;
3. the class name, and its capitalisation;
4. that you are looking at a group page or module, not the group's wiki.
