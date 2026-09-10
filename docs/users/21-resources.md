<!--
status: rewritten
reviewed-against: 2.4-main @ d48e29db14
reviewed: 2026-09-09
source: https://help.hubzero.org/documentation/240/users/resources
-->
# Resources

Think of the hub as a library and resources as the items on its shelves:
publications, datasets, presentations, videos, teaching materials, and
simulation tools. Anyone with an account can contribute one. They live at
`/resources` on the hub.

## Finding a resource

The resources home page explains what resources are and who may submit
them, and gives you three ways in: a **Find a resource** search box, a
**Browse the list of available resources** link, and a list of
**Categories** — the resource types this hub offers, each with a short
description. **Need Help?** opens the built-in help.

**Browse** at `/resources/browse` lists everything you are allowed to see.
Search by keyword or phrase, sort by **Title**, **Published**, or
**Ranking**, and narrow the list with the type menu, which starts on **All
Types**. The **Popular Tags** cloud beside the list filters by tag; click
more tags to narrow further, and the `x` on an applied tag removes it.

> **Note:** You can filter on at most five tags at once. Extra tags are
> ignored and listed in a warning above the results.

Clicking a category on the home page opens a tag browser for that type,
where you pick a tag and then a resource from the column beside it. Hub
managers can turn the tag browser off, in which case the category link goes
to the ordinary browse list.

Every resource has a permanent address, `https://<your hub>/resources/<id>`,
and often a friendlier one built from its alias.

## Reading a resource page

A resource opens on its **About** tab. That shows the abstract, any
screenshots, an **At a glance** table of details the contributor filled in,
the **Contributor(s)** list, **Tags**, and a **Cite this work** block with
the citation to use in a publication — with EndNote and BibTeX downloads,
and a DOI where the hub assigns one. A prominent button at the top launches
the tool, opens the presentation, or downloads the main file.

More tabs run across the top. Which ones appear depends on the resource
type and on what the hub has enabled:

| Tab | What it holds |
|---|---|
| About | The description and details, as above. |
| Supporting Docs | Extra files and documents attached to the resource. |
| Reviews | Star ratings and reviews from other members. |
| Questions | Questions and answers about the resource. |
| Wishlist | Feature requests and wishes for it. |
| Citations | Published work that cites this resource. |
| Usage | Usage statistics and a map of where it is used. Tools only. |
| Versions | The released versions of a tool. Tools only. |
| Find this Text | Links that help you locate a copy elsewhere. |
| Setup/Instructions | How to launch a Windows tool. |

A sidebar beside the tabs may show the group that owns the resource, a
**See also** list of related pages, the resource's sponsor, and a **Watch
resource** button.

Some resources are restricted. A **Registered** one asks you to log in
first; a **Protected** one shows its abstract to everyone but limits the
files to members of the owning group; a **Private** one is only visible to
that group. If you hit one you may not read, the page tells you which group
to join.

> **Note:** Video and audio resources play in the page where the hub
> supports it, and are usually downloadable as well: right-click the
> resource link and choose your browser's save-link command. MP4 is the
> recommended format.

## Rating and reviewing

Open the **Reviews** tab and select **Write a review**. Pick **Your
Rating** from one to five stars, add **Comments** and any **Tags** you
think fit, and press **Submit**. Check the anonymous box if you would
rather your name did not appear. You must be logged in, and you cannot
review a resource you are a contributor on. Come back and use **Edit your
review** to change it. Other members can reply to a review, vote on whether
it was helpful, and use **Report abuse** on anything offensive.

## Watching a resource

**Watch resource** in the sidebar subscribes you to changes; the hub then
notifies you when the resource is updated. **Stop watching resource** ends
it. You have to be logged in for the button to appear.

## Contributing a resource

Go to `/resources/new`, or use **Submit a resource** on the resources
pages, and select **Get Started**. You must be logged in. The same page
lists your submissions still **In Progress**, so you can pick up where you
left off; each step saves as you leave it.

First choose the type of resource you are contributing. Only types the hub
has marked contributable appear.

> **Note:** Choosing **Tools** does not open this wizard. Simulation tools
> are registered through the tool pipeline instead; see
> [Tools](22-tools.md).

Then work through five steps.

**Compose.** Give the resource a **Title** and an **Abstract/Description**;
both are required. **Manage files** below the abstract uploads images to
use *inside* the abstract — not the resource's own files. It lists each
uploaded file's address as plain text; copy that and paste it into the
editor's image dialog where you want the picture. The **Details** section
holds whatever extra fields your resource type defines, such as Credits,
Sponsored by, or References.

**Attach.** These are the files a visitor actually downloads or views. Use
the **Click or drop file** box to upload one, or the link adder beside it to
point at a URL. Click a name to rename it, then press **save**, or
**cancel** to leave it alone. The **Order** arrows move an entry up or down,
the trash icon removes it without asking, and the **Access** cell toggles
between **Public** (anyone may view or download it) and **Registered**
(logged-in members only). If the type is a collection, such as a series,
this step adds existing resources as members instead of uploading files.

> **Note:** The instructions above the list say to double-click a name and
> press Tab, Enter, or Escape. The editor actually opens on a single click
> and is saved with its own **save** button.

**Authors.** Optionally hand the resource to one of your groups under
**Group ownership**, and set the **Access level** non-members get: Public,
Registered, Protected (abstract visible, files for group members only), or
Private (group members only). Protected and Private require a group. Then
list the contributors — enter names or logins separated by commas and press
**Add**. Someone without a hub account can still be listed by name; they
just will not link to a profile. Set each person's **Organization** and
**Role** and press **Save**; use the up and down arrows to change the order
they are credited in, and the delete icon to take someone off.

> **Note:** Changes to a contributor's organization or role are only kept
> if you press **Save**; moving to the next step is not enough. On a new hub
> the **Role** menu offers only *Author* until a manager defines more roles.

Being listed as a contributor is what grants editing rights. If you take
yourself off the list, you keep them only if you also created the
submission.

**Tags.** Type keywords into **Assigned Tags**, choosing from the
auto-complete list where you can so your resource sits with related work.
The hub may also suggest tags, and may ask you to pick a focus area.

**Review.** Read the authorization statement and tick the box to agree —
the submission is rejected without it. Choose a **License** if the hub
offers them; its text appears below the menu. Check the preview of the
page, then press **Submit Contribution**.

A submission must have at least one contributor, and where the hub requires
it, at least one attachment.

## After you submit

Most hubs review submissions. Yours goes to **pending** and the hub's
managers are emailed; when they approve it, it goes live in the resources
listing and near the top of What's New, and you are notified if the hub has
that turned on. Some hubs approve everything automatically, or approve
particular contributors automatically, in which case the resource is
published straight away.

The **In Progress** table on `/resources/new` tracks all of this. A draft
shows **Review & Submit ›**, and its title, attachment, author, and tag
counts link straight to the matching step. A pending submission shows
**‹ Retract**, which puts it back to draft so you can keep working. Once a
resource has passed review, opening it again takes you to the **Review**
step, where you can change the license and press **Save**; the other steps
stay editable from the step bar, and each **Next** saves your changes.

The **Delete** icon at the end of a row discards the contribution — its
description, linked files, and tags — after you tick **Confirm discard**.
This cannot be undone.

> **Note:** You cannot delete a resource that has already been published —
> others may be citing it. It is marked as deleted and disappears from the
> site. To have one retired or removed entirely, ask the hub's support team;
> see [Support](12-support.md).

## Where to go next

Tags connect your resource to everything else on the hub — see
[Tags](27-tags.md). To gather resources into a personal reading list, see
[Collections](01-collections.md). To ask about a resource or request a
feature, see [Questions and answers](19-questions.md) and
[Wishlist](29-wishlist.md).
