# QUBES super group templates
QUBESHub supergroup template used by all supergroups. Multiple branches will be created for different templates.

 * **master** branch:  Default HubZero template with modified header, banner and footer (green color scheme)
   * *Banner image* (1425px x 150px): Place a `banner.jpg` or `banner.png` file in the uploads directory.
   * Banner and title is clickable and will take you to the Overview page.
   * *Favicon image*: Place a `favicon.ico` file in the uploads directory to change the favicon icon in the browser tab (default is the QUBES logo). To generate a favicon from an image/logo:  [http://www.favicomatic.com/](http://www.favicomatic.com/).
   * All pages automatically display under the Overview tab in the menu.
   
 * **menu** branch:  All features of the **master** branch, with the addition of:
   * Changed the Overview tab to Main.
   * Added a down caret icon in the Main tab if there are additional pages in the menu.
   * Only pages with the page category **menu** will be displayed under the Main tab.

* **sidebar** branch:  All features of the **menu** branch, with the addition of:
  * All community areas (e.g. forum, members, announcements, etc.) are in a floating left sidebar.
  * All pages that were previously under the Main tab are now top level in the menu and displayed in the menu bar.

# For Developers

## Adding Features

If you are adding a global feature, please work in the **master** branch and use `git cherry-pick` to merge that feature into the other branches.  For example, if the feature has already been pushed to the **master** branch, do the following:

```bash
git checkout menu
git cherry-pick <commit hash>
git push

git checkout sidebar
git cherry-pick <commit hash>
git push
```

## Submodule in qubeshub/hubzero-cms

This repo is a [submodule](https://git-scm.com/book/en/v2/Git-Tools-Submodules) within the [qubeshub/hubzero-cms](https://github.com/qubeshub/hubzero-cms) repo.  Additional instructions for working with submodules will be put here in the near future.
