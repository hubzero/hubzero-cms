<!--
status: imported
source: core/components/com_cron/admin/help/en-GB/jobs.phtml
imported: 2026-09-09
-->
# Cron Manager: Jobs

- [Overview](#overview)
- [Column Headers](#column-headers)
- [Toolbar](#toolbar)
- [Sub-menu Links](#sub-menu)
- [List Filters](#list-filters)

<a id="overview"></a>

## Overview

Here you will a list of schedule cron jobs.

<a id="column-headers"></a>

## Column Headers

Click on the column heading to sort the list by that column's value. The list will be sorted in order by that column and a sort icon will show next to the column name. Click a second time to reverse the sort to high-to-low. The sort icon will change to high-to-low.

- **Checkbox**  
  Check this box to select one or more items. To select all items, check the box in the column heading. After one or more boxes are checked, click a toolbar button to take an action on the selected item or items. Many toolbar actions, such as Publish and Unpublish, can work with multiple items. Others, such as Edit, only work on one item at a time. If multiple items are checked and you press Edit, the first item will be opened for editing.
- **ID**  
  This is a unique identification number for this item assigned automatically by the system. It is used to identify the item internally and cannot be changed. When creating a new item, this field is 0 until the entry is saved, at which point a new ID is assigned to it.
- **Title**  
  The name of the item. For a Menu Item, the Title will display in the Menu. For an Article or Category, the Title may optionally be displayed on the web page. This entry is required. You can open the item for editing by clicking on the Title.
- **State**  
  (Published/Unpublished/Trashed) The published status of the item.
- **Starts (Date)**  
  The date this cron job will begin to run.
- **Ends (Date)**  
  The date this cron job will stop running.
- **Active**  
  This indicates if the cron job is **currently** running. Some jobs may take longer than others and this ensures that a new process cannot be started until the previous one finishes.
- **Last Run (Date)**  
  The date this cron jobwas last run. The timestamp is automatically set by the system.
- **Next Run (Date)**  
  The date the next time this job will be run. This is calculated from the recurrnce settings on the job.

<a id="toolbar"></a>

## Toolbar

At the top right you will see the toolbar.

The functions are:

- **Options**  
  Opens the Options window where settings such as default parameters or permissions can be edited.
- **Run**  
  Force a job to run, disregarding "next run" and recurrence settings.
- **Publish**  
  Makes the selected job available to be run.
- **Unpublish**  
  Makes the selected job unavailable to be run.
- **New**  
  Opens the editing screen to create a new job.
- **Delete**  
  Permanently remove the selected jobs from the system.
- **Help**  
  Opens this help screen.

<a id="sub-menu"></a>

## Sub-menu Links

At the top left, above the filters, you will see sub-menu options. The links are:

- **Jobs**  
  Show the list of specified cron jobs.
- **Plugins**  
  Show the list of installed plguins in the "cron" plugin group (folder).

<a id="list-filters"></a>

## List Filters

## Page Controls

When the number of items is more than one page, you will see a page control bar as shown below.

- **Display #**  
  Select the number of items to show on one page.
- **Start**  
  Click to go to the first page.
- **Prev**  
  Click to go to the previous page.
- **Page numbers**  
  Click to go to the desired page.
- **Next**  
  Click to go to the next page.
- **End**  
  Click to go to the last page.
