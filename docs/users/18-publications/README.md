<!--
status: rewritten
reviewed-against: 2.4-main @ d48e29db14
reviewed: 2026-09-09
source: https://help.hubzero.org/documentation/240/users/publications
-->
# Publications

A publication is a citable release of your work — a dataset, a paper, a set
of images, a piece of software. You assemble one from files you have already
put in a project, a curator reviews it, and once it is approved the hub gives
it a permanent page and a DOI so other people can cite it. Published work
lives at `/publications` on the hub.

## Finding a publication

The Publications home page lists **Recent Publications** and **Popular
Publications** side by side, with a **Browse Publications** button at the top
right. Each entry shows a thumbnail, the title, the publication date, the
category, and the contributors.

**Browse Publications** is where you search. Type a word or phrase into
**Enter keyword or phrase** and press **Search**. Narrow the results with the
**Category** list — pick one and press **Search** again — or click a tag in
the **Popular Tags** list beside the results. Tags stack: each one you add
appears above the results with an `x` that removes it. Sort with **Title**,
**Published**, and, where the hub shows rankings, **Ranking**.

Every publication has a permanent address you can share:
`https://<your hub>/publications/<id>`. Adding a version number reaches one
particular release, as in `/publications/42/3`.

## Reading a publication

The top of a publication page carries the title, the authors, a short summary
and the category it is listed in. To the right sits the download or launch
button and, under it, the version and license lines. The version line reads
something like *Version 1.2 - published on 12 Mar 2026* and, where the work
has a DOI, shows `doi:` followed by the identifier and a **cite this** link.

The **About** tab below holds the full description, the image gallery, the
list of what is in the publication, any extra metadata the hub collects, the
tags, and the release **Notes** for that version. Other tabs appear depending
on what the hub has enabled for that category:

| Tab | What it holds |
|---|---|
| Versions | Every release of this publication, with its DOI, so you can move between them. |
| Supporting Docs | Files that come with the work but are not the main content. |
| Citations | Works this publication references, and works that reference it. |
| Questions | Questions and answers about the publication. |
| Reviews | Star ratings and written reviews from members. |
| Usage | Download and view figures. |
| Forks | Copies other members have made of this work. |
| Wishlist | Requests for changes or additions. |

> **Note:** **Reviews** and **Questions** only appear on the most recent
> public release. Open an older version and those tabs are gone.

**Watch publication**, in the sidebar, tells the hub to email you when a new
version of that publication is released; the same button then reads **Stop
watching publication**.

## Downloading

The button at the top right of the page downloads the content. What it says
depends on what is attached: **Download** for a single file, **Download
Bundle** with the total size for several, **View publication** for a link,
**Go to data** for a database. Where the files have been packaged, **Show
bundle contents** lists what is inside, with the size and an MD5 checksum you
can use to verify what you got.

Very large bundles are built in the background. The button then reads
**Download Bundle (preparing…)** and starts the download by itself once the
package is ready. If the dataset is too big to bundle at all, the button says
**Download Bundle (unavailable)** and you download the files one at a time
from the publication page instead.

Some publications are restricted. If the content needs an account you are
told *To access the content, you need to be logged in to the site*; if it is
limited to particular groups, *Publication content is restricted to
logged-in members of limited groups*. A retired publication says *We're
sorry! The content of this version is unpublished and cannot be viewed*, and
following the DOI of a withdrawn dataset lands you on a **Tombstone Dataset**
page that says why it was retracted.

## Citing

Under **Cite this work** on the About tab the hub prints the citation in the
form it wants you to use, with the DOI as a link. Two export links follow:
**BibTex** and **EndNote**, each of which downloads a file you can import
into a reference manager.

## Publishing your own work

You need an account, and the hub must have publishing switched on. Most work
starts in a project: go to your project, open **Publications** in its menu,
and select **Start a new publication**. Working inside a project means your
team-mates appear in the author picker, your files are already there, and
related publications share a project page.

If you only want to release a few files and do not need a project, go to
`/publications`, press **Start publishing »**, and use the buttons under
*Want to publish file(s) quickly without starting a project? There is a way.*
The hub creates a hidden project for you behind the scenes. Some hubs turn
this shortcut off, in which case only the project route is offered.

Either way you first answer **What are you going to publish?** and pick a
type — **File(s)**, **Databases**, **Series** and so on. The type decides
which panels you are asked to fill in.

### Working through the panels

A bar across the top of the draft shows every panel, and its colour tells you
whether that panel is finished. Press **Save & Continue** to move on, or
click any panel in the bar to jump to it. What you get depends on the type,
but a file publication usually asks for:

1. **Content** — the files to publish. **Add a file** opens a selector over
   your project's files; tick what you want and press **save selection**.
   Files you have not uploaded yet can be uploaded from the same selector.
   Each item in the list has a **Remove** control that takes it back out of
   the publication. Types that accept several kinds of content give primary
   files, supporting documents and gallery images their own **Add a file**
   buttons.
2. **Description** — the title, a short abstract, and the full description.
3. **Authors** — **Select author(s)** lists your project team; **Add an
   author** looks up any hub member, or takes a name, organization and email
   for someone without an account. Drag the names to change their order.
4. **Tags** — words and phrases that describe the work, plus the category it
   belongs in.
5. **License** — **Choose License** picks one, and where the license demands
   it you tick a box agreeing to its terms. Some licenses let you edit the
   text.
6. **Citations** — related works. Paste a DOI and the hub fetches the
   citation for you; otherwise enter it by hand.
7. **Notes** — what changed in this version.

### Review and submit

The last panel is **Review**. It tells you whether the draft is complete —
*Your draft is complete and ready for submission* — or what is still missing,
and it collects the last few decisions:

- **Preview publication page**, which opens the page as it will look.
- **Publishing settings**: a **Publication date**. Leave it blank to publish
  as soon as it is approved, or set a future date to embargo the work.
- **Point of Contact** — one or more corresponding authors, which trusted
  repositories require.
- **Publish or Post?**, where the type allows a choice. **Publish draft**
  issues a DOI and freezes most of the record. **Post draft** issues no DOI,
  keeps the publication editable, and leaves the page out of search, reachable
  only by its direct link.
- **Comments** for the curator, and the **Agreements** box: *I and all
  publication authors have read and agree to* the hub's **Terms of Deposit**.

Press **Submit draft**. The publication goes to *pending approval*, curators
are notified, and you get an email when they respond. If they ask for
changes, see [Curation](curation.md).

Once a version is published you cannot change most of it — that is what the
DOI guarantees. To correct or extend the work, start a new version from the
**Versions** panel; it keeps the same publication page and adds a new release
with its own DOI.

## Forking

Where the hub allows it and the license permits derivatives, the **Forks**
tab offers **Fork Publication**. Forking copies the files, authors, tags and
citations into a draft of your own — either in a project you already have or
in a new one — so you can build on the work and publish the result. The fork
records where it came from, and the original's page lists it. The **Diff**
button on the Forks tab compares two versions side by side, section by
section.
