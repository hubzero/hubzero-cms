<!--
status: generated
source: core/plugins/content/*/*.xml
-->

# Content plugins

Parameters of every plugin in the `content` group, from each plugin's manifest. Set them under **Extensions > Plugins** in the administrator interface.

## Content - Antispam (`plg_content_antispam`)

Runs content through a spam checker that a number of detectors can be registered with.

### Basic

| Parameter | Label | Type | Default | Description |
|---|---|---|---|---|
| `message` | Message | text | `The submitted text was detected as possible spam or containing inappropriate content.` | The message to display when content is detected as possibly being spam. |
| `learn_spam` | Learn Spam | list | `1 (Yes)` | Allow the plugin to learn from content that is taken down as spam. Options: `0` No, `1` Yes. |
| `learn_ham` | Learn Ham | list | `1 (Yes)` | Allow the plugin to learn from content that is valid (i.e., mistakenly flagged as spam). Options: `0` No, `1` Yes. |
| `log_spam` | Log spam | list | `0 (No)` | Log items flagged as spam. Options: `0` No, `1` Yes. |

### Whitelist

| Parameter | Label | Type | Default | Description |
|---|---|---|---|---|
| `wl_groups` | Access Groups | list | — | Access group to whitelist. Options: `nobody` Disabled/Off, `admin` Administrators. |
| `wl_usernames` | Usernames | textarea | — | Comma-separated list of usernames to whitelist |

## Content - Categories (`plg_content_categories`)

Does category processing for core extensions.

### Basic

| Parameter | Label | Type | Default | Description |
|---|---|---|---|---|
| `check_categories` | Check category deletion | radio | `1 (Yes)` | Check that categories are fully empty before they are deleted. Options: `0` No, `1` Yes. |

## Content - Email Cloaking (`plg_content_emailcloak`)

Cloaks all emails in content from spambots using JavaScript

### Basic

| Parameter | Label | Type | Default | Description |
|---|---|---|---|---|
| `mode` | Mode | list | `1 (As linkable mailto address)` | Select how emails will be displayed. Options: `0` Non-linkable Text, `1` As linkable mailto address. |

## Content - External links (`plg_content_externalhref`)

Apply specific actions or `rel` attributes to external links in content.

### Basic

| Parameter | Label | Type | Default | Description |
|---|---|---|---|---|
| `mode` | Change external links | list | `0 (Set 'No Follow', if no `rel` attribute is specified)` | Follow mode for external links. Options: `0` Set 'No Follow', if no `rel` attribute is specified, `1` Force 'No Follow', `2` Strip 'No Follow', `3` Do not change. |
| `target` | Change link target | list | `0 (Open in new window (_blank) if no `target` attribute specified)` | Add or change the link's `target` attribute. Options: `0` Open in new window (_blank) if no `target` attribute specified, `1` Open in new window (_blank), `2` Open in parent window / frame (_parent), `3` Do not change. |
| `classes` | Ignore with CSS Classes | textarea | — | Links with one or mroe of the specified classes will be ignored. Comma separated values. |

## Content - HTML Format Handler (`plg_content_formathtml`)

Content handler for HTML content. Allows various macro parsing and optional filtering of incoming content.

### Basic

| Parameter | Label | Type | Default | Description |
|---|---|---|---|---|
| `applyFormat` | Apply format marker | list | `0 (No)` | Apply the format marker to the content. Options: `0` No, `1` Yes. |
| `convertFormat` | Convert to HTML | list | `1 (Yes)` | Save rendered HTML back to the database? This will mean the content is no longer wiki markup. Options: `0` No, `1` Yes. |
| `sanitizeBefore` | Sanitize HTML | list | `1 (Yes)` | Run content through an HTML sanitizer before saving?. Options: `0` No, `1` Yes. |
| `unlink` | Unlink links | list | `0 (No)` | Turn HTML links into plain text. Options: `0` No, `1` Yes. |

### Advanced

| Parameter | Label | Type | Default | Description |
|---|---|---|---|---|
| `macropath` | Alt. Macro Path | text | — | Alternate path to laod macros from. |

## Content - Wiki Format Handler (`plg_content_formatwiki`)

Content handler for Wiki formatted content. Converts wiki syntax to HTML on display.

### Basic

| Parameter | Label | Type | Default | Description |
|---|---|---|---|---|
| `applyFormat` | Apply format marker | list | `0 (No)` | Apply the format marker to the content. Options: `0` No, `1` Yes. |
| `convertFormat` | Convert to HTML | list | `1 (Yes)` | Save rendered HTML back to the database? This will mean the content is no longer wiki markup. Options: `0` No, `1` Yes. |

## Content - Code Highlighter (GeSHi) (`plg_content_geshi`)

Displays formatted code in Articles based on the GeSHi highlighting engine

This plugin has no parameters.

## Content - Load Modules (`plg_content_loadmodule`)

Within content loads Module positions, Syntax: {loadposition user1} or Modules by name, Syntax: {loadmodule mod_login}. Optionally can specify module style and for loadmodule a specific module title.

### Basic

| Parameter | Label | Type | Default | Description |
|---|---|---|---|---|
| `style` | Style | list | `table (Wrapped by table (column))` | Code that will wrap Modules. Options: `table` Wrapped by table (column), `horz` Wrapped by table (horizontal), `xhtml` Wrapped by Divs, `rounded` Wrapped by Multiple Divs, `none` No wrapping (raw output). |

## Content - Open Graph (`plg_content_opengraph`)

Adding Open Graph meta information to the site. The Open Graph protocol enables any web page to become a rich object in a social graph.http://ogp.me

### Article Options

| Parameter | Label | Type | Default | Description |
|---|---|---|---|---|
| `title` | Title | text | — | If empty, article title will be set. It is recommended to leave this parameter empty. |
| `type` | Type | list | `article` | Set type. Options: `activity`, `sport`, `bar`, `company`, `cafe`, `hotel`, `restaurant`, `cause`, `sports_league`, `sports_team`, `band`, `government`, `non_profit`, `school`, `university`, `actor`, `athlete`, `author`, `director`, `musician`, `politician`, `public_figure`, `city`, `country`, `landmark`, `state_province`, `album`, `book`, `drink`, `food`, `game`, `product`, `song`, `movie`, `tv_show`, `blog`, `website`, `article`. |
| `image` | Image | media | — | Set image. If image will be not set here, plugin will try to find the image in article content. If article does not contain any image, plugin will try to search /images/phocaopengraph/ folder for image which has the same name as article ID has (e.g. article ID=1 ==> 1.jpg). See documentation to understand this behaviour. |
| `url` | Url | text | — | If empty, article url will be set. It is recommended to leave this parameter empty |
| `site_name` | Site Name | text | — | Set site name - human-readable name of your site. If empty, site name from global configuration will be set. It is recommended to leave this parameter empty. |
| `description` | Site Description | textarea | — | Set site description. If empty, site description from article options will be set, if the description of article will be empty, global configuration will be set. Site Meta Description parameter will be used. |

### Category (Blog) Options

| Parameter | Label | Type | Default | Description |
|---|---|---|---|---|
| `titlec` | Title | text | — | If empty, article title will be set. It is recommended to leave this parameter empty. |
| `typec` | Type | list | `article` | Set type. Options: `activity`, `sport`, `bar`, `company`, `cafe`, `hotel`, `restaurant`, `cause`, `sports_league`, `sports_team`, `band`, `government`, `non_profit`, `school`, `university`, `actor`, `athlete`, `author`, `director`, `musician`, `politician`, `public_figure`, `city`, `country`, `landmark`, `state_province`, `album`, `book`, `drink`, `food`, `game`, `product`, `song`, `movie`, `tv_show`, `blog`, `website`, `article`. |
| `imagec` | Image | media | — | Set image. If image will be not set here, plugin will try to find the image in article content. If article does not contain any image, plugin will try to search /images/phocaopengraph/ folder for image which has the same name as article ID has (e.g. article ID=1 ==> 1.jpg). See documentation to understand this behaviour. |
| `urlc` | Url | text | — | If empty, article url will be set. It is recommended to leave this parameter empty |
| `site_namec` | Site Name | text | — | Set site name - human-readable name of your site. If empty, site name from global configuration will be set. It is recommended to leave this parameter empty. |
| `descriptionc` | Site Description | textarea | — | Set site description. If empty, site description from article options will be set, if the description of article will be empty, global configuration will be set. Site Meta Description parameter will be used. |
| `displayc` | Display (Category) | list | `1 (Yes)` | Run the plugin in Category View. Options: `1` Yes, `0` No. |

### Common Options

| Parameter | Label | Type | Default | Description |
|---|---|---|---|---|
| `app_id` | Application ID | text | — | Set Facebook Application ID |
| `other` | Other Properties | textarea | — | Set other properties. Separate each property value with semicolon (;). E.g. og:audio:title=Some Audio;og:audio:artist=SomeArtist |

## Content - Pagebreak (`plg_content_pagebreak`)

Allow the creation of a paginated article with optional table of contents.Insert page breaks through the use of the page break button normally found beneath the text panel in an Article. The location of the page break in an article will be displayed in the editor as a simple horizontal line.The text displayed will depend on the options chosen and may be either the title, alternate text (if provided) or page numbers.   The HTML usage is: &lt;hr class="system-pagebreak" /&gt;&lt;hr class="system-pagebreak" title="The page title" /&gt; or &lt;hr class="system-pagebreak" alt="The first page" /&gt; or &lt;hr class="system-pagebreak" title="The page title" alt="The first page" /&gt; or &lt;hr class="system-pagebreak" alt="The first page" title="The page title" /&gt;

### Basic

| Parameter | Label | Type | Default | Description |
|---|---|---|---|---|
| `title` | Show Site Title | radio | `1 (Show)` | Title and heading attributes from Plug-in added to Site Title tag. Options: `0` Hide, `1` Show. |
| `article_index` | Article Index Heading | radio | `1 (Show)` | Show/Hide Article Index Heading. The Heading displays on top of the Table of Content. Options: `0` Hide, `1` Show. |
| `article_index_text` | Custom Article Index Heading | text | — | Enter a custom text for the Article Index Heading. If empty, standard will be used. |
| `multipage_toc` | Table of Contents | radio | `1 (Show)` | Display a table of contents on multipage Articles. Options: `0` Hide, `1` Show. |
| `showall` | Show All | radio | `1 (Show)` | Displays the full article. Options: `0` Hide, `1` Show. |
| `style` | Presentation Style | list | `pages (Pages)` | Chose whether to layout the article with separate pages, tabs or sliders. Options: `pages` Pages, `sliders` Sliders, `tabs` Tabs. |

## Content - Page Navigation (`plg_content_pagenavigation`)

Enables you to add the Next &amp; Previous functionality to an Article.

### Basic

| Parameter | Label | Type | Default | Description |
|---|---|---|---|---|
| `position` | Position | radio | `1 (Below)` | The position of the Page Navigation function on the viewed page in relation to the text. Options: `1` Below, `0` Above. |
| `relative` | Relative to | radio | `1 (Full article)` | Assigns the relative location for the Position parameter. Text will place it directly above or below the article content. Full article will place it above or below the full display including title and readmore. Options: `1` Full article, `0` Text. |

## Content - Vote (`plg_content_vote`)

Add the Voting functionality to Articles

This plugin has no parameters.

## Content - Xhubtags (`plg_content_xhubtags`)

Process XHUB tags for including stylehseets, Javascript files, and modules in an article.Modules can be loaded by specifying a module position:{xhub:module position=&quot;featured&quot;}CSS files may be loaded into the document. Paths are relative to the template's css sub-directory unless prefixed with a forward slash, in which case they will be absolute to the root directory:{xhub:include type=&quot;stylesheet&quot; filename=&quot;path/to/file.css&quot;}Javascript files may be loaded into the document. Paths are relative to the template's js sub-directory unless prefixed with a forward slash, in which case they will be absolute to the root directory:{xhub:include type=&quot;script&quot; filename=&quot;path/to/file.js&quot;}

### Basic

| Parameter | Label | Type | Default | Description |
|---|---|---|---|---|
| `style` | Default Module Style | text | `xhtml` | The default module style to display modules with, when a style isn't specified. |
