<!--
status: rewritten
reviewed-against: 2.4-main @ f22290e4e4
reviewed: 2026-09-09
screenshots: ok
source: https://help.hubzero.org/documentation/240/managers/components/courses
source-id: 3377
modified: 2016-07-12
imported: 2026-09-09
-->
# Courses

Courses are the hub's teaching component. A course holds a syllabus and a set
of overview pages; an offering is one run of that course; a section is one
cohort within an offering; and a section's content is a tree of units, asset
groups and assets. Students enrol in a section.

Go to **Components → Courses**. The submenu holds **Courses**, **Pages**,
**Students**, **Roles**, and a **Plugins** link that jumps to the courses
plugin group under **Extensions → Plugins**. Everything else — offerings,
sections, units, asset groups, coupon codes, certificates — is reached by
drilling down from the course list.

Every screen needs `core.manage` on `com_courses`.

> **Note:** The gradebook is not in the administrator interface. Grading,
> progress reports and the instructor's gradebook live on the course pages on
> the site, in the `progress` courses plugin.

## The hierarchy

| Level | Reached from | Holds |
|---|---|---|
| Course | **Components → Courses** | Title, description, managers, a logo, overview pages, one certificate |
| Offering | the **Offerings** count on a course row | A run of the course: start and end dates, units, its own pages |
| Section | the **Sections** count on an offering row | A cohort: enrolment setting, dates, students, coupon codes |
| Unit | the **Units** count on an offering row | A week or module |
| Asset group | the **Asset groups** count on a unit row | Lectures, Homework, Exam — whatever the **Default Asset Groups** option names |
| Asset | inside a unit or asset group's edit form | One lecture, file, video, quiz or tool |

Counts in the list are links. A count of zero shows a **[ + ]** or **Add**
link instead, which goes straight to the new-item form at that level.

## The course list

Columns: **ID**, **Title**, **Alias**, **State**, **Cert.**, **Managers**,
**Offerings**, and **Pages**. Filters above the table are a **Search** box and
a **State** drop-down (*All States*, Unpublished, Published, Draft, Trashed).

The toolbar carries **Duplicate**, **New**, **Edit**, **Delete**, and — for
`core.admin` — **Options**.

The **State** cell is a button: clicking a published course unpublishes it,
clicking anything else publishes it. The **Cert.** cell goes to the
certificate screen for that course, and says **Certificate set** or **No
certificate set**.

![The Cert. column showing a course with no certificate](../media/courses-210course.png)

## Creating a course

1. Press **New**.
2. Fill in the **Details** fieldset.
3. Set **State** under **Publishing**.
4. Press **Save**. Managers and the logo cannot be added until the course
   exists — the form says so.
5. Add managers in the **Managers** panel.
6. Drop an image into the **Logo** panel.
7. Press **Save & Close**.

### Details

| Field | Notes |
|---|---|
| **Group** | An existing hub group to tie the course to, or **None** |
| **Alias** | Alphanumeric. Generated from the title when left empty. Becomes part of the URL |
| **Title** | Required |
| **Blurb** | A sentence or two for the course catalogue |
| **Course length** | Free text, e.g. `4 weeks` |
| **Estimated Effort** | Free text, e.g. `5 - 8 hours per week` |
| **Description** | The long description |
| **Tags** | A comma-separated list of keywords |

### Publishing

**State** offers **Unpublished**, **Published**, **Draft**, and **Trashed**.

- **Published** — anyone can find the course.
- **Draft** — only course managers can see it, so a course can be built
  before students arrive.
- **Unpublished** — off the site.
- **Trashed** — off the site and out of the default list view.

> **Note:** **Trashed** is not a permanent delete. Deleting is the **Delete**
> button on the course list.

The right-hand column also shows read-only ID, created date and creator, and
a **Parameters** fieldset for each courses plugin that offers course-level
settings.

### Managers

The **Managers** panel is an embedded list of the course's managers. Add a
person by name, and set their role. Roles come from the **Roles** screen.

## Course overview pages

A page is a tab on the course or offering overview. Reach them from the
**Pages** count on a course row, from the same count on an offering row, or
from **Components → Courses → Pages**.

The list shows **ID**, **Title**, **State** and **Ordering**.

A page's edit form has:

| Field | Notes |
|---|---|
| **Title** | Required |
| **Alias** | The URL segment. Generated from the title when empty |
| **Content** | The page body, in the rich-text editor |
| **Active** | **Yes** puts the tab on the overview; **No** hides it |

The read-only panel says the page's **Type** — **Course page**, **Offering
page**, or **User Guide** — depending on whether it is attached to a course,
an offering, or neither.

> **Note:** Files can only be attached after the page has been saved once.

## Offerings

The **Offerings** count on a course row opens the offering list: **Ordering**,
**ID**, **Title**, **Starts**, **Ends**, **State**, **Sections**,
**Enrollment**, **Units**, and **Pages**. The toolbar carries **Duplicate**,
**New**, **Edit** and **Delete**.

An offering's edit form has:

- **Details** — **Title** (required) and **Alias**.
- **Publishing** — **State** (Unpublished, Published, Trashed), **Starts**
  and **Ends**.
- **Logo** — an image for the offering.
- **Parameters** — **Progress Calculation**, plus a fieldset for each courses
  plugin that offers offering-level settings.

**Progress Calculation** decides which assets count towards a student's
progress bar:

| Option | Meaning |
|---|---|
| **Inherit from courses defaults** | Use the component's **Progress calculation based on:** option |
| **All published assets** | Every published asset |
| **All published, graded assets** | Only graded assets |
| **All published, video assets** | Only videos |
| **All published, manually selected assets** | Only assets marked by the instructor |

## Sections

The **Sections** count on an offering row opens the section list: **ID**,
**Title**, **Alias**, **Default**, **State**, **Starts**, **Ends**,
**Enrolled**, and **Codes**. **Enrolled** links to the students for that
section; **Codes** links to its coupon codes.

A section's edit form has four tabs.

### Details

| Field | Notes |
|---|---|
| **Offering** | Which offering the section belongs to |
| **Alias** | Alphanumeric. Generated from the title when empty |
| **Title** | Required |
| **Default section** | **Yes** or **No**. The default section supplies the dates every other section inherits |
| **Enrollment** | See below |
| **State** | Unpublished, Draft, Published, Trashed |

**Enrollment** has three settings:

- **Open (anyone can join)** — any hub user can enrol.
- **Restricted (coupon code is required)** — only someone with a code from
  the section's **Codes** screen can enrol.
- **Closed (no new enrollment)** — nobody new can enrol.

The default for a new section comes from the component's **Default
Enrollment** option.

**Publishing** on the same tab carries **Publish start**, **Section starts**,
**Section finishes** and **Publish down**.

**Parameters**, also on this tab, carries:

- **Progress Calculation** — the same list as the offering, except the first
  option reads **Inherit from offering settings**.
- **Preview mode** — **No preview offered**, **Yes, offer a full preview**,
  or **Yes, offer a preview of the first unit**. This is how you let people
  look at a course before enrolling.
- A fieldset for each courses plugin with section-level settings. The
  **Discussions** plugin puts **Show threads from** here — **All sections** or
  **This section only**. Sticky threads show across all sections whatever this
  says.

### Managers

The section's own instructors and assistants, added the same way as course
managers.

### Dates/Times

A tree of every asset in the section with a **From** and a **To** date. Dates
are inherited from the default section for the offering, and a value set at
one level is inherited by everything below it. Set a date here to change when
a lecture, homework or exam becomes available in this section only.

### Recognition/Rewards

- **Offer a certificate for this seciton?** — **Yes** or **No**. This only
  does anything when the course has a certificate.
- A **Badge** fieldset with the badge's enabled flag, image, provider and
  criteria.

## Students

The **Enrollment** count on an offering row, the **Enrolled** count on a
section row, and **Components → Courses → Students** all reach the same
screen. Columns: **ID**, **Name**, **Email**, **Course : Offering**,
**Section**, **Cert.**, and **Enrolled**. Filters are a **Search** box, an
**Offering** drop-down, and a **Section** drop-down.

**New** opens a form asking for a **User**, an **Offering**, a **Section**,
and an **Enrolled** date. **Delete** unenrols the selected students.

> **Note:** The controller has a `csv` task that exports the current list as
> `registrations.csv`, but nothing links to it. Reach it by hand at
> `index.php?option=com_courses&controller=students&task=csv`.

## Coupon codes

The **Codes** cell on a section row opens the coupon codes for that section:
**ID**, **Code**, **Created**, **Expires**, **Redeemed**, and **Redeemed
by**.

The toolbar carries **Generate**, **Export codes to a CSV file.**, **New**,
**Edit** and **Delete**. **Generate** opens a pop-up asking for a **Number of
codes** (5 by default) and an expiry date, then creates that many codes at
once.

Codes only matter for a section whose **Enrollment** is **Restricted**.

## Units, asset groups and assets

The **Units** count on an offering row opens the unit list: **ID**,
**Title**, **Alias**, **State**, **Ordering**, **Asset groups**, and
**Assets**. The toolbar carries **Duplicate**, **New**, **Edit** and
**Delete**.

The **Asset groups** count opens that unit's asset groups: **ID**, **Title**,
**Alias**, **State**, **Ordering**, and **Assets**. Its toolbar adds
**Duplicate Assets to Existing Groups**, which copies a group's assets into
other groups.

Assets themselves are edited inside the unit's or asset group's edit form,
in an embedded **Assets** panel. There you can **Attach asset** — pick an
existing one — or **Create asset**, which opens the asset editor in an
overlay. The panel lists each asset's **ID**, **Title**, **Type**, **State**
and **Ordering**.

To delete content:

1. Go to **Components → Courses**, and click the course's **Offerings**
   count.
2. Click the offering's **Units** count.
3. To remove a whole asset group, click the unit's **Asset groups** count,
   tick the group, and press **Delete**. Confirm *Are you sure you want to
   remove these items?*
4. To remove one asset, open the unit or asset group for editing and delete
   the asset from the **Assets** panel.

Deleting an asset group deletes the assets in it.

## Certificates

A course has at most one certificate: a PDF with placeholders the hub fills
in when a student claims it.

To create one:

1. On the course list, click the **No certificate set** icon in the course's
   **Cert.** column.
2. The upload form appears. Choose a PDF and press **Upload**.
3. The certificate opens in a placement canvas showing the PDF. Drag the
   placeholder buttons onto it and resize them: **Login**, **Name**,
   **Date**, **Email**, **Course**, **Offering**, **Section**. **Clear All**
   removes them all.
4. **Preview** in the toolbar renders the certificate as a student would see
   it.
5. Press **Save & Close**.

To remove one, open the certificate the same way — the **Cert.** column will
read **Certificate set** — and press **Delete** in the toolbar.

A section only offers the certificate when its **Recognition/Rewards** tab
says **Yes, offer a certificate.**

## Roles

**Components → Courses → Roles** lists the roles a manager can hold:
**ID**, **Alias**, **Title**, **Offering**, and **Total** — how many people
hold the role. A role belongs to one offering, or to **None** for a role
available everywhere.

A role's edit form asks for an **Offering**, a **Title** and an **Alias**.

## Options

![The Options button in the toolbar](../media/courses-210options.png)

Press **Options** in the toolbar of the course list. `core.admin` is
required. The full list is in the
[generated parameter reference](../../reference/configuration/components/courses.md).
The ones worth knowing:

| Option | Default | Effect |
|---|---|---|
| **Upload path** | `/site/courses` | Where course pictures and files are stored |
| **Default Asset Groups** | `Lectures, Homework, Exam` | The groups created with a new unit |
| **Default Enrollment** | Open (anyone can join) | The enrolment setting a new section starts with |
| **Can section owners edit grading policy?** | Yes | When No, only course instructors see the grading policy interface |
| **Progress calculation based on:** | — | The hub-wide default that offerings inherit |
| **Auto Approve Courses?** | Yes | When No, an administrator must approve each new course |
| **Show Enrollment Numbers** | No | Show enrolment numbers and stats on each course |

To show enrolment numbers on the course pages, set **Show Enrollment
Numbers** to **Yes** on the **Basic** tab and save.

> **Note:** The old version of this page put this setting on a **Defaults**
> tab. There is no such tab. The Options pop-up has **Basic**, then two
> fieldsets — the Passport badge keys and the Unity key — whose tab titles
> render as raw language keys because `config.xml` gives them no label.

## Presentations

The old version of this page ended with a section on HUB Presenter, the
slide-and-video presentation format. That belongs to
[Resources](17-resources.md), not to Courses: a presentation is a resource of
an online-presentation type, whose files are assembled on the hub's file
system and described by a `presentation.json` manifest. Nothing about it is
configured in the Courses component.
