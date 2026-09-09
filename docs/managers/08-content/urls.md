<!--
status: imported
source: https://help.hubzero.org/documentation/240/managers/content/urls
source-id: 3369
imported: 2026-09-09
-->
# URLs

## Understanding URLs for Sections, Categories, and Articles

When SEF URLs are being used, it can become tricky at times to discover which article a URL path leads to. HUBs try to simplify some of this and make the association(s) of the path to sections, categories, and articles a little more obvious.

For the purposes of this explanation, we assume every section, category, and article has an alias. In its simplest form, a URL will map to a path of `http://yourhub.org/{SectionAlias}/{CategoryAlias}/{ArticleAlias}`. So, a URL of `http://yourhub.org/about/this/site` would map to a section with an alias of **about**, a category with an alias of **this**, and an article with an alias of **site**.

The resulting URL will start to collapse, or shorten, when sections, categories, and articles have the same alias. For instance, if you have a section with alias **about**, a category with an alias of **this**, and an article with an alias of **this**– notice it is the same as the category–the resulting URL will be `http://yourhub.org/about/this`.

If all three portions have the same alias, the `http://yourhub.org/about/this/site` shortens further. A section alias of **about**, category alias of **about** and article alias of **about** will simply produce `http://yourhub.org/about`.

| Section | Category | Article | URL |
|---|---|---|---|
| about | this | site | http://yourhub.org/about/this/site |
| about | this | this | http://yourhub.org/about/this |
| this | this | site | http://yourhub.org/this/site |
| about | this | about | http://yourhub.org/about/this/about |
| about | about | about | http://yourhub.org/about |

## URL Redirects

If you find yourself wanting to redirect a certain URL to another page, then *Joomla!* has a solution for you. And fortunately, the solution is as simple as creating a menu.

1. Navigate to **Menus -> Menu Manager** on the Joomla! backend
2. At this point, you can either create a new Menu or use an existing one. If you prefer the former, click **New** in the upper right-hand corner, and give your new Menu a name such as **Redirects**. Alternatively, you could use the **Default** menu. The only thing to remember is that you should use a menu that is not displayed on the site (e.g. the **Main Menu** and **About** menus or displayed on the standard hub)
3. Irrelevant of which option you chose, select the icon in the **Menu Item(s)** row to add a new **Menu Item** to your **Menu** to handle the redirect
4. Next, fill in the necessary fields
   - Add a **Title**
   - The **Alias** will be the address that a user would enter into the address bar of their browser
   - The **Link** will be the page where the actual content exists
     - **Example:** According to the image below, if a user types `http://myhub.org/example`, though that page does not exist, they will get the content found at `http://myhub.org/example/examplearticle1`
5. Select **Save**
