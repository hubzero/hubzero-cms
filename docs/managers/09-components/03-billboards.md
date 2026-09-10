<!--
status: rewritten
reviewed-against: 2.4-main @ f22290e4e4
reviewed: 2026-09-09
screenshots: stale
source: https://help.hubzero.org/documentation/240/managers/components/billboards
source-id: 3372
modified: 2016-07-12
imported: 2026-09-09
-->
# Billboards

Billboards are the rotating slides a hub shows on its front page. Each slide
carries a background image, a heading, a block of text, and an optional
"learn more" link. Slides belong to a collection, and the `mod_billboards`
module displays one collection as a carousel. Go to **Components →
Billboards**.

The component has two screens, reached from the submenu:

- **Billboards** — the slides.
- **Collections** — the named groups a module points at.

## Collections

A collection is a name and nothing else. It exists so that a hub can run more
than one carousel — a front-page set and a landing-page set, say — and point
each module instance at a different one.

Press **New** on the **Collections** screen, fill in **Collection Name**, and
**Save & Close**. The list shows each collection's ID and name, and the
toolbar carries **New**, **Edit**, and **Delete**.

Every billboard needs a collection. If you save a billboard without picking
one, the component creates a collection called *Default Collection* and puts
the slide in it.

> **Note:** Deleting a collection does not delete the billboards in it. They
> stay in the database, out of every carousel, until you edit each one and
> give it a collection that exists.

## Creating a billboard

1. Go to **Components → Billboards** and press **New**.
2. Fill in the fields below.
3. Press **Save & Close**.

The edit form has three fieldsets.

### Content

| Field | Notes |
|---|---|
| **Name** | Required. What the slide is called in the list |
| **Collection** | The collection this slide belongs to |
| **Ordering** | Position within the collection. On a new billboard this reads *New Items Last*; it becomes a drop-down once the slide is saved |
| **Header** | The heading drawn over the image |
| **Background image** | A file picker. See below |
| **Text** | The body text, edited in the rich-text editor |

### Learn More Link

| Field | Notes |
|---|---|
| **Learn more text** | The link's label. Leave empty for no link |
| **Learn more target** | Where the link goes |
| **Learn more class** | An extra CSS class for the link |
| **Learn more location** | **Top left**, **Top right**, **Bottom left**, **Bottom right**, or **Relative** |

### Styling

| Field | Notes |
|---|---|
| **Alias** | The slide's HTML `id`. The module writes the slide's CSS against it, so give each billboard a distinct alias |
| **Text padding** | A CSS `padding` value applied to the slide's text |
| **CSS** | Free-form CSS added to the page for this slide |

## Adding a background image

The **Background image** field is an ordinary file input on the edit form.
There is no media browser and no separate upload step.

1. Open the billboard you want to edit.
2. Press the button beside **Background image** and choose a file from your
   computer.
3. Press **Save** or **Save & Close**.

The file is scanned for viruses and moved into the directory named by the
component's **Image Location** option, which defaults to
`/site/media/images/billboards/`. See the
[generated parameter list](../../reference/configuration/components/billboards.md).
Uploading a new image deletes the one it replaces.

Once a billboard has an image, the edit form shows it under **Current Image**,
scaled to 500 pixels wide.

> **Note:** The screenshots on this page show an older interface in which the
> image was chosen through a **Media Manager** pop-up with **Browse** and
> **Start Upload** buttons, and the filename was then pasted into a text
> field. That flow no longer exists. Ignore the pop-up in the second and third
> images.

![The Components menu with Billboards selected](../media/billboards-billboards.png)

![The old Media Manager pop-up, no longer used](../media/billboards-billboards2.png)

![The old background image field, no longer used](../media/billboards-billboards3.png)

## The billboard list

The list shows each slide's ID, name, collection, ordering, and published
state. Click a name to edit it. Click the icon in the **Published** column to
toggle the slide on or off. Reorder slides by typing new numbers in the
**Ordering** column and pressing the column's sort arrows.

The toolbar carries **Publish**, **Unpublish**, **New**, **Edit**,
**Delete**, and — for users with `core.admin` — **Options**.

Only published billboards appear in a carousel.

## Displaying a carousel

Slides are not shown by the component. They are shown by the
`mod_billboards` module, which you publish to a template position from
**Extensions → Modules**. Its options are:

| Option | Default | Notes |
|---|---|---|
| **Billboard Collection** | collection `1` | Which collection to display |
| **Slide Transition Style** | Scroll Horizontal | Scroll Horizontal, Scroll Vertical, Fade, Shuffle, Zoom, or Turn left |
| **Random** | No | Show the slides in random order |
| **Slide Time** | 5 | Seconds each slide stays on screen |
| **Transition Speed** | 1 | Seconds a transition takes |
| **Display Pager** | Yes | Show the dots that let a visitor jump between slides |

Several instances of the module can run at once, each on its own collection
and its own timing.
