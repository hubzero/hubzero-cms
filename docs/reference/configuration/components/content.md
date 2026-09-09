<!--
status: generated
source: core/components/com_content/config/config.xml
-->

# Content (com_content)

Article management component

Parameters from [`core/components/com_content/config/config.xml`](../../../../core/components/com_content/config/config.xml), as shown on the component's **Options** screen in the administrator interface.

## Articles

These settings apply for article layouts unless they are changed for a specific menu item.

| Parameter | Label | Type | Default | Description |
|---|---|---|---|---|
| `article_layout` | Choose a layout | componentlayout | — | Default layout to use for articles |
| `show_title` | Show Title | radio | `1 (Show)` | If set to Show, the article title is shown. Options: `0` Hide, `1` Show. |
| `link_titles` | Linked Titles | radio | `1 (Yes)` | If set to Yes, the article title will be a link to the article. Options: `0` No, `1` Yes. |
| `show_intro` | Show Intro Text | radio | `1 (Show)` | If set to Show, the Intro Text of the article will show when you drill down to the article. If set to Hide, only the part of the article after the &quot;Read More&quot; break will show. Options: `0` Hide, `1` Show. |
| `show_category` | Show Category | radio | `1 (Show)` | If set to Show, the title of the article&rsquo;s category will show. Options: `0` Hide, `1` Show. |
| `link_category` | Link Category | radio | `1 (Yes)` | If set to Yes, and if Show Category is set to 'Show', the Category Title will link to a layout showing articles in that Category. Options: `0` No, `1` Yes. |
| `show_parent_category` | Show Parent | radio | `1 (Show)` | If set to Show, the title of the article&rsquo;s parent category will show. Options: `0` Hide, `1` Show. |
| `link_parent_category` | Link Parent | radio | `1 (Yes)` | If set to Yes, and if Show Parent is set to 'Show', the Parent Category Title will link to a layout showing articles in that Category. Options: `0` No, `1` Yes. |
| `show_author` | Show Author | radio | `1 (Show)` | If set to Show, the Name of the article's Author will be displayed.  This is a global setting but can be changed at the Category, Menu and Article levels. Options: `0` Hide, `1` Show. |
| `link_author` | Link Author | radio | `0 (No)` | If set to Yes, the Name of the article's Author will be linked to its contact page. You must create a contact linked to the author's user record for this to be ineffect.  This is a global setting but can be changed at the Category, Menu and Article levels. Options: `0` No, `1` Yes. |
| `show_create_date` | Show Create Date | radio | `1 (Show)` | If set to Show, the date and time an Article was created will be displayed. This a global setting but can be changed at Menu and Article levels. Options: `0` Hide, `1` Show. |
| `show_modify_date` | Show Modify Date | radio | `1 (Show)` | If set to Show, the date and time an Article was last modified will be displayed. This is a global setting but can be changed at the Category, Menu and Article levels. Options: `0` Hide, `1` Show. |
| `show_publish_date` | Show Publish Date | radio | `1 (Show)` | If set to Show, the date and time an Article was published will be displayed. This is a global setting but can be changed at the Category, Menu and Article levels. Options: `0` Hide, `1` Show. |
| `show_item_navigation` | Show Navigation | radio | `1 (Show)` | If set to Show, shows a navigation link (Next, Previous) between articles. Options: `0` Hide, `1` Show. |
| `show_vote` | Show Voting | radio | `0 (Hide)` | If set to show, a voting system will be enabled for Articles. Options: `0` Hide, `1` Show. |
| `show_readmore` | Show &quot;Read More&quot; | radio | `1 (Show)` | If set to Show, the Read more... Link will show if Main text has been provided for the Article. Options: `0` Hide, `1` Show. |
| `show_readmore_title` | Show Title with Read More | radio | `1 (Show)` | If set to show the Title of the Article will be shown on the Read More button. Options: `0` Hide, `1` Show. |
| `readmore_limit` | Read More Limit | text | `100` | Set a limit of number of characters in Article Title to show in Read More button |
| `show_icons` | Show Icons | radio | `1 (Show)` | Print and email will utilise Icons or Text. Options: `0` Hide, `1` Show. |
| `show_print_icon` | Show Print Icon | radio | `1 (Show)` | Show/Hide the Item Print button. Options: `0` Hide, `1` Show. |
| `show_email_icon` | Show Email Icon | radio | `1 (Show)` | Show/Hide the email icon. This allows you to email an article. Options: `0` Hide, `1` Show. |
| `show_hits` | Show Hits | radio | `1 (Show)` | If set to Show, the number of Hits on a particular Article will be displayed. This is a global setting but can be changed at the Category, Menu and Article levels. Options: `0` Hide, `1` Show. |
| `show_noauth` | Show Unauthorised Links | radio | `0 (No)` | If set to Yes, links to registered content will be shown even if you are not logged-in. You will need to log in to access the full item. Options: `0` No, `1` Yes. |
| `urls_position` | Positioning of the Links | radio | `0 (Above)` | Display the links above or below the content. Options: `0` Above, `1` Below. |

## Editing Layout

These options control the layout of the article editing page.

| Parameter | Label | Type | Default | Description |
|---|---|---|---|---|
| `show_publishing_options` | Show Publishing Options | radio | `1 (Yes)` | Display or hide the publishing options slider in the article edit view. These options allow changes in dates and author identities. Options: `0` No, `1` Yes. |
| `show_article_options` | Show Article Options | radio | `1 (Yes)` | Display or hide article options slider in the backend article edit view. These options allow overriding of the global options. Options: `0` No, `1` Yes. |
| `show_urls_images_frontend` | Frontend Images and Links | radio | `0 (No)` | Display or hide fields to insert standardized images and links when front end editing. Options: `0` No, `1` Yes. |
| `show_urls_images_backend` | Administrator Images and Links | radio | `0 (No)` | Display or hide fields to insert standardized images and links in the administrator. Options: `0` No, `1` Yes. |
| `targeta` | URL A Target Window | list | `Parent` | Target browser window when the menu item is clicked. Options: `0` Open in parent window, `1` Open in new window, `2` Open in popup, `3` Modal. |
| `targetb` | URL B Target Window | list | `Parent` | Target browser window when the menu item is clicked. Options: `0` Open in parent window, `1` Open in new window, `2` Open in popup, `3` Modal. |
| `targetc` | URL C Target Window | list | `Parent` | Target browser window when the menu item is clicked. Options: `0` Open in parent window, `1` Open in new window, `2` Open in popup, `3` Modal. |
| `float_intro` | Intro Image Float | list | — | Controls placement of the image. Options: `right` Right, `left` Left, `none` None. |
| `float_fulltext` | Full Text Image Float | list | — | Controls placement of the image. Options: `right` Right, `left` Left, `none` None. |

## Category

These settings apply for Articles Category Options unless they are changed by the individual category or menu settings.

| Parameter | Label | Type | Default | Description |
|---|---|---|---|---|
| `category_layout` | Choose a layout | componentlayout | — | Default layout to use for articles |
| `show_category_heading_title_text` | Show Subcategories Text | radio | `1 (Show)` | If Show, the &quot;Subcategories&quot; will show as a subheading on the page. The subheading is usually displayed inside the &quot;H3&quot; tag. Options: `0` Hide, `1` Show. |
| `show_category_title` | Category Title | radio | `1 (Show)` | If Show, the Category Title will show as a subheading on the page. The subheading is usually displayed inside the &quot;H2&quot; tag. Options: `0` Hide, `1` Show. |
| `show_description` | Category Description | radio | `1 (Show)` | Show or hide the description of the selected Category. Options: `0` Hide, `1` Show. |
| `show_description_image` | Category Image | radio | `0 (Hide)` | Show or hide the image of the selected Category. Options: `0` Hide, `1` Show. |
| `maxLevel` | Subcategory Levels | list | `-1 (All)` | The number of subcategory levels to display. Options: `0` None, `-1` All, `1`, `2`, `3`, `4`, `5`. |
| `show_empty_categories` | Empty Categories | radio | `0 (Hide)` | If Show, empty categories will display. A category is only empty - if it has no articles or subcategories. Options: `0` Hide, `1` Show. |
| `show_no_articles` | No Articles Message | radio | `1 (Show)` | If Show, the message 'There are no articles in this category' will display when there are no articles in the category or when 'Empty Categories' is set to show. Options: `0` Hide, `1` Show. |
| `show_subcat_desc` | Subcategories Descriptions | radio | `1 (Show)` | Show/Hide the subcategories descriptions. Options: `0` Hide, `1` Show. |
| `show_cat_num_articles` | # Articles in Category | radio | `1 (Show)` | If Show, the number of articles in the category will show. Options: `0` Hide, `1` Show. |

## Categories

These settings apply for Articles Categories Options, unless they are changed by the individual category or menu settings.

| Parameter | Label | Type | Default | Description |
|---|---|---|---|---|
| `show_base_description` | Top Level Category Description | radio | `1 (Show)` | Show description of the top level category or optionally override with the text from the description field found in menu item. If using Root as top level category, the description field has to be filled. Options: `0` Hide, `1` Show. |
| `maxLevelcat` | Subcategory Levels | list | `-1 (All)` | The number of subcategory levels to display. Options: `-1` All, `1`, `2`, `3`, `4`, `5`. |
| `show_empty_categories_cat` | Empty Categories | radio | `0 (Hide)` | If Show, empty categories will display. A category is only empty - if it has no articles or subcategories. Options: `0` Hide, `1` Show. |
| `show_subcat_desc_cat` | Subcategories Descriptions | radio | `1 (Show)` | Show/Hide the subcategories descriptions. Options: `0` Hide, `1` Show. |
| `show_cat_num_articles_cat` | # Articles in Category | radio | `1 (Show)` | If Show, the number of articles in the category will show. Options: `0` Hide, `1` Show. |

## Blog / Featured Layouts

These settings apply for blog or featured layouts unless they are changed for a specific menu item.

| Parameter | Label | Type | Default | Description |
|---|---|---|---|---|
| `num_leading_articles` | # Leading Articles | text | `1` | Number of leading articles to display as full-width at the beginning of the page. |
| `num_intro_articles` | # Intro Articles | text | `4` | Number of articles to show after the leading article. Articles will be shown in columns. |
| `num_columns` | # Columns | text | `2` | The number of columns in which to show Intro Articles. Normally 1, 2, or 3. |
| `num_links` | # Links | text | `4` | Number of articles to display as links, normally below the Intro Articles. |
| `multi_column_order` | Multi Column Order | radio | `0 (Down)` | Order articles down or across columns. Options: `0` Down, `1` Across. |
| `show_subcategory_content` | Include Subcategories | list | `0 (None)` | If None, only articles from this category will show. If a number, all articles from the category and the subcategories up to and including that level will show in the blog. Options: `0` None, `-1` All, `1`, `2`, `3`, `4`, `5`. |

## List Layouts

These settings apply for List Layouts Options unless they are changed for a specific menu item or category.

| Parameter | Label | Type | Default | Description |
|---|---|---|---|---|
| `show_pagination_limit` | Display Select | radio | `1 (Show)` | Whether to show or hide the Display Select dropdown listbox. Options: `0` Hide, `1` Show. |
| `filter_field` | Filter Field | list | `hide (Hide)` | Whether to show a Filter field for the list of articles. Select Hide to hide the filter field, or select which field you wish to filter on. Options: `hide` Hide, `title` Title, `author` Author, `hits` Hits. |
| `show_headings` | Table Headings | radio | `1 (Show)` | Show or Hide the headings in list layouts. Options: `0` Hide, `1` Show. |
| `list_show_date` | Show Date | list | `0 (Hide)` | Whether to show a date column in the list of articles. Select Hide to hide the date, or select which date you wish to show. Options: `0` Hide, `created` Created, `modified` Modified, `published` Published. |
| `date_format` | Date Format | text | — | Optional format string for showing the date. If left blank, it uses DATE_FORMAT_LC1 from your language file (for example, D M Y for day month year or you can use d-m-y for a short version eg. 10-07-10. See http://www.php.net/manual/en/function.date.php). |
| `list_show_hits` | Show Hits in List | radio | `1 (Show)` | Whether to show article hits in the list of articles. Options: `0` Hide, `1` Show. |
| `list_show_author` | Show Author in List | radio | `1 (Show)` | Whether to show article author in the list of articles. Options: `0` Hide, `1` Show. |

## Shared Options

These settings apply for Shared Options in List, Blog and Featured unless they are changed by the menu settings.

| Parameter | Label | Type | Default | Description |
|---|---|---|---|---|
| `orderby_pri` | Category Order | list | `none (No Order)` | The order that categories will show in. Options: `none` No Order, `alpha` Title Alphabetical, `ralpha` Title Reverse Alphabetical, `order` Category Manager Order. |
| `orderby_sec` | Article Order | list | `rdate (Most recent first)` | The order that articles will show in. Options: `rdate` Most recent first, `date` Oldest first, `alpha` Title Alphabetical, `ralpha` Title Reverse Alphabetical, `author` Author Alphabetical, `rauthor` Author Reverse Alphabetical, `hits` Most Hits, `rhits` Least Hits, `order` Ordering. |
| `order_date` | Date for Ordering | list | `published (Published)` | If articles are ordered by date, which date to use. Options: `created` Created, `modified` Modified, `published` Published. |
| `show_pagination` | Pagination | list | `2 (Auto)` | Show or hide Pagination support. Pagination provides page links at the bottom of the page that allow the User to navigate to additional pages. These are needed if the Information will not fit on one page. Options: `0` Hide, `1` Show, `2` Auto. |
| `show_pagination_results` | Pagination Results | radio | `1 (Show)` | Show or hide pagination results information, for example, &quot;Page 1 of 4&quot;. Options: `0` Hide, `1` Show. |

## Integration

These settings determine how the Article Component will integrate with other extensions.

| Parameter | Label | Type | Default | Description |
|---|---|---|---|---|
| `show_feed_link` | Show Feed Link | radio | `1 (Show)` | Show or hide an RSS Feed Link. (A Feed Link will show up as a feed icon in the address bar of most modern browsers). Options: `0` Hide, `1` Show. |
| `feed_summary` | For each feed item show | radio | `0 (Intro Text)` | If set to Intro Text, only the Intro Text of each article will show in the newsfeed. If set to Full Text, the whole article will show in the newsfeed. Options: `0` Intro Text, `1` Full Text. |
| `feed_show_readmore` | Show &quot;Read More&quot; | radio | `0 (Hide)` | Displays a &quot;Read More&quot; link in the newsfeeds if Intro Text is set to Show. Options: `0` Hide, `1` Show. |
