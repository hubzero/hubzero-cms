<!--
status: rewritten
reviewed-against: 2.4-main @ 42a7a5b5c7
reviewed: 2026-09-10
screenshots: none
source: https://help.hubzero.org/documentation/240/users/courses
source-id: 3289
modified: 2014-11-17
imported: 2026-09-09
-->
# Courses

A course is an online class run on the hub. It holds lectures, videos, wiki
pages, homework, quizzes and exams, tracks how far each student has got, and
can hand out a certificate or a badge at the end. Courses are at
`/courses` on the hub.

Hubs use them for the teaching that sits alongside the research: a summer
school that has to run for people in four time zones, the simulation training
a lab makes every new graduate student do, a semester course whose students
need the hub's tools anyway. The point of running it here rather than
emailing PDFs around is that the hub marks the quizzes, keeps the score, and
knows when somebody has finished.

The student half of this page follows one course the whole way through:
*Introduction to Molecular Dynamics*, eight units, one released each week,
with a quiz at the end of most of them and an exam at the end.

> **Note:** A course is not a group. Courses keep their own membership and
> their own roles, so enrolling in a course puts you in no
> [group](11-groups/README.md), and belonging to the group a course is
> attached to does not enrol you.

## How a course is put together

Four levels sit inside each other. You meet all of them in the URL of a
course page.

| Level | What it is |
|---|---|
| **Course** | The catalogue entry: title, short description, tags, a long description, instructors and any extra overview pages |
| **Offering** | One run of the course. A course that changes significantly gets a new offering rather than an edit |
| **Section** | One cohort inside an offering. Sections decide who may enrol, when material becomes available, and whether a certificate is offered |
| **Unit** | A week or module of an offering, holding asset groups — **Lectures**, **Homework**, **Exam** — and the assets in them |

Students enrol in a section. Most hubs run a single default section per
offering, so enrolling looks like enrolling in the course itself.

## The two views of a course

The **course overview** at `/courses/{alias}` is public. It shows the title,
the short description, the long description under an **Overview** tab, the
instructors, how long the course takes and how much effort it needs, an
**Offerings** tab, and any extra pages the instructor has added. A
**Go to Course** button takes you in.

Inside the course, tabs come from the courses plugins the hub has enabled:
**Getting Started**, **Outline**, **Progress**, **Announcements**,
**Discussions**, **Notes**, **Pages**, and, for instructors, **Dashboard**.
The **Outline** tab is where the material is.

## What to read next

- [Student features](#student-features) — enrolling, working through the
  outline, tracking your progress, and claiming a certificate or badge.
- [Course manager features](#course-manager-features) — what an
  instructor can do from the course pages themselves.
- The [Courses chapter](../managers/09-components/10-courses.md) in the
  Hub managers book covers everything an administrator does in the back end,
  including sections, coupon codes, certificates and roles.

## Student features

Finding a course, enrolling in it, working through it, and seeing how far you
have got. If you were sent a link by an instructor, start at
[Enrolling](#enrolling).

### Finding a course

1. Go to `https://yourhub.org/courses`.
2. Search from the box on that page, or select **Browse the catalog** for the
   full list.
3. The catalog sorts by **Title**, **Alias** or **Popularity**, and narrows to
   one tag at a time. Each entry shows the course number, its short
   description and its instructors.
4. Select a course title to open its overview.

### Enrolling

Enrolling is what turns a catalogue entry into your course. Until you do it
the hub is not tracking you: no progress, no grades, no certificate, and no
access to anything but the overview page.

The course overview is public. The long description, the instructors, the
course length and the estimated effort are all readable before you enrol.
Everything under **Go to Course** — the outline, discussions, your progress —
needs enrolment.

To enrol, select **Go to Course** on the course overview. What happens next
depends on how the section is set up:

| Setting | What you see |
|---|---|
| **Open** | You are enrolled and dropped straight into the course. There is no confirmation message; the outline simply appears |
| **Restricted** | A **Redeem Coupon Code** form. Type the code you were given into **Coupon Code** and select **Redeem** |
| **Closed** | *Course enrollment is closed.* with links to the support form and the course catalog |

Our molecular dynamics student was sent a code by the summer school
organisers, so she gets the middle row: one field, one button, and she is in.
A code is tied to a section, so it also decides which cohort she joins.

A course may also be sold through the hub's store. When an offering has a
price, the button reads **Enroll for only $30.00!** rather than
**Enroll for free!**, and it takes you to the cart instead of enrolling you.

Where a course runs more than one offering, or you belong to more than one
section, the button opens a list so you can pick which one to enter.

> **Note:** Enrolment errors are specific, and they tell you which problem
> you have. A coupon code can be reported as invalid, already redeemed, or
> expired — *"ABC123" has already been redeemed.* means somebody used your
> code, not that you typed it wrong. A code issued for a different section
> moves you to that section before redeeming, so a code from the wrong page
> still works.

#### Looking before you enrol

Worth knowing before you commit to eight weeks of somebody's course.

An instructor can turn on a preview, so the outline can be read without
enrolling. There are two kinds: a full preview, and a preview of the first
unit only. While previewing, a banner reads *You're currently viewing this
course in preview mode. Some features may be disabled.*

Quizzes and exams can never be previewed. Opening one without being enrolled
gives *You must be enrolled to utilize this asset.*

### Inside the course

The **Outline** tab is the course. Everything else is support: somewhere to
ask a question, somewhere to keep notes, somewhere to read the syllabus.

Tabs run across the top. Which ones appear depends on the plugins the hub has
enabled:

| Tab | What it holds |
|---|---|
| **Getting Started** | How the course works |
| **Outline** | The units, lectures, files, videos, wiki pages, homework, quizzes and exams. This is where most of the work happens |
| **Progress** | Your own progress and grades |
| **Announcements** | Notices from the instructor |
| **Discussions** | Threads for the course. Depending on the section, you see threads from your own section only or from all of them; sticky threads always show everywhere |
| **Notes** | Your own notes, taken against the material |
| **Pages** | Syllabus, errata, and anything else the instructor has written |

#### What a week looks like

A course is not a folder of files you can work through at your own speed
unless the instructor built it that way. Material becomes available on the
dates set for your section, and an item with a prerequisite stays shut until
you have finished the item it requires.

That produces a rhythm you can plan around, and it looks like this on the
**Outline** tab. Every unit in the course is listed from day one, including
the ones you cannot open yet, so you can see the whole eight weeks in advance
and read the unit titles. A unit that has not opened is marked and carries
its date in place of its contents:

> Content for this unit will be available starting July 14, 2026, 9:00 am
> EDT.

When that moment passes, the unit opens by itself. Nobody has to do anything
and there is no announcement unless the instructor writes one. Our student
opens the course on the Monday, finds Unit 3 has unfolded, works through the
lecture and the reading, and takes the quiz at the end. Then Unit 3 closes
again if the instructor set an end date — *Content for this unit expired on*
and the date — so the work is not there to come back to indefinitely. Within
an open unit, the same applies to individual items: an item is a plain
heading rather than a link when it is not yet available.

Prerequisites work differently. They are not about the calendar but about
you: an item stays shut until you have completed whatever it depends on, and
it says so.

> This unit has prerequisites that have not yet been met. Begin by
> completing: Unit 2

The **Progress** timeline moves along with all of this. *Unit 3 of 8* at the
top of that tab means the third unit is the one currently open to you, not
the one you have reached.

> **Warning:** A section that has not started yet shows the same message as
> one that has finished: *The access time for this section has expired and
> the content is no longer available.* If you enrol before the course begins
> and see that, it usually means you are early rather than late. Check the
> start date on the course overview before contacting support.

### Tracking your progress

The **Progress** tab answers two questions a student actually has: am I
passing, and how much is left. It is worth opening once a week rather than
once at the end, because the score it shows is the same one the certificate
test uses.

Open the **Progress** tab. If you are not enrolled you get
*You must be enrolled to utilize the progress feature.* instead.

**The timeline** runs across the top: a walking figure moves along a bar
divided into one segment per unit, from a start marker to a finish marker. A
segment fills in as you work through that unit's material. The heading above
reads *Course begins* and a date, *Course currently in progress*, or *Course
ended* and a date; the line beneath says which unit you are on — *Unit 3 of
8*.

**Four tiles** sit below it:

| Tile | What it counts |
|---|---|
| **Your current score** | Your grade as a percentage, marked passing or failing |
| **Quizzes taken** | Out of the total number in the course |
| **Homeworks submitted** | Out of the total number in the course |
| **Exams taken** | Out of the total number in the course |

The three counters are the ones to watch, because they are the certificate
test in visible form: an item counts as taken once you have submitted it, or
once an instructor has entered a score for you by hand. *Quizzes taken 5 out
of 8* means three quizzes are outstanding, and three outstanding quizzes mean
no certificate however good the five were.

A **grading policy** link beside the score explains how it is worked out, and
the policy's own description is printed under the tiles. Read it early. It
says what counts and for how much — and, as the [Certificates](#certificates)
section below explains, a category worth nothing in the policy is a category
you do not have to finish. The grading policy is set per section, by the
course's instructors rather than by the hub administrators.

**The breakdown** below lists every unit with its percentage. Open a unit and
you get a table of **Assignment**, **Score** and **Date taken** for each
graded item in it. A score you were expecting to see may read *Not yet open*,
*Not yet available*, *Not taken* or *Withheld* — the last means the item has
closed and the instructor chose not to show results for it, not that you
scored zero.

![The Progress tab](media/student-features-210courses.png)

### Certificates

A certificate is what you have to show for the course afterwards: a PDF
carrying your name, login, email address, the course, the offering, the
section and the date. Not every course has one, and a course that does may
offer it on some sections and not others, so check before you count on it.

Three things all have to be true before you can claim one. The course has a
certificate, your section offers it, and you are eligible: you have taken
every graded exam, quiz and homework that the grading policy gives a weight
to, and your score is a pass. When they are, a panel appears at the top of
your **Progress** tab, headed *Congratulations!*, with a
**Download your certificate!** link. The PDF is generated the first time you
ask for it and downloaded as an attachment.

The eligibility test is worth reading twice, because it is stricter than
"pass the course" and it catches people out. Passing is only the second half
of it. The first half is completeness: for each of the three categories —
exams, quizzes, homework — that the grading policy gives any weight at all,
you must have taken *every* graded item in that category. Not most of them,
and not enough of them to pass. Our student who skipped the week 5 quiz
because she was already on 88% has a passing score and no certificate, and
nothing on the page tells her why until she looks at *Quizzes taken 7 out of
8* on her own **Progress** tab.

The one relief is the other side of the same rule. A category the grading
policy weights at zero is ignored entirely, so on a course whose homework
counts for nothing, unsubmitted homework does not block the certificate. An
instructor entering a score for you by hand also counts as having taken the
item.

> **Note:** Eligibility is checked when your **Progress** tab is drawn, so
> the panel appears the next time you open it after the last score lands —
> not the moment you submit. If you believe you have finished everything and
> no panel appears, reload the tab before asking anyone.

Where the course has a certificate that some section offers, the course
overview's summary table says **Certificate: Available** before you enrol.

### Badges

A badge is the shareable version of the same thing: a credential issued
through the hub's badge provider that lives in a backpack you can point
employers at, rather than a PDF in your downloads folder. A section can offer
a badge, a certificate, both, or neither.

It uses exactly the same eligibility test as the certificate, so everything
in the section above applies unchanged. When you qualify, a panel appears on
your **Progress** tab:

- *Congratulations! You've earned the badge...and you deserve it!* with a
  **Claim your badge!** link — or, where the provider gives no claim URL, a
  note to watch your email.
- After claiming: *Congratulations! You've got the badge!* with
  **View your badges!**
- If you turned it down: *Congratulations! You earned the badge!* with
  **View denied badges**, and you can go back and claim it later.

## Course manager features

What an instructor does from the course pages themselves, without going near
the administrator interface. Everything in this section happens on the site.

Read it if you are the one running the course. Building the material —
writing the outline, dropping in lectures and videos, turning a PDF into a
quiz the hub marks — is all here, and none of it needs an administrator. What
does need one is anything about the shape of the run rather than its content:
the dates, the cohorts, the certificate. The table below is the dividing
line, and it is the first thing to check when a control you are looking for
is not on the screen.

> **Note:** You need a privileged role in the course — Instructor, Manager, or
> whatever extra roles the hub has defined. Those roles are the course's own,
> so belonging to the group a course is tied to does not make you a course
> manager.

### What is on the site and what is not

The front end is where you write the course and build its material. The
back end is where the hub's shape is set: sections, who may enrol, when
material becomes available, certificates, badges, coupon codes and roles.

| Task | Where |
|---|---|
| Course title, short description, tags | Course overview page |
| Long description | **Overview** tab |
| Extra overview pages | Course overview page and the **Pages** tab |
| Instructors and managers | Course overview page |
| Creating an offering | Course overview page and the **Offerings** tab |
| Publishing a draft course | Course overview page |
| Building the outline: units, asset groups, assets | **Outline** tab |
| Quizzes and exams, and their deployment settings | **Outline** tab |
| Prerequisites | **Outline** tab |
| Gradebook and grading policy | **Progress** tab |
| Editing an offering's title, dates or state | Administrator only |
| Sections, enrolment setting, coupon codes | Administrator only |
| Availability dates for units and assets | Administrator only |
| Certificates and badges | Administrator only |
| Course roles | Administrator only |

The administrator side is covered in the
[Courses chapter](../managers/09-components/10-courses.md) of the Hub
managers book.

### Creating a course

If the hub lets you create courses, a **Create Course** button appears at
the top right of `/courses` and `/courses/browse`.

1. Select **Create Course**.
2. Fill in **Alias** — alphanumeric, and it becomes part of the course URL.
   The field checks as you type whether the alias is free.
3. Fill in **Title**.
4. Add a **Short description**. This is the text that appears in the course
   catalog.
5. Add **Categories (tags)**, separated by commas.
6. Decide whether to tick **Allow forks (derivatives) of this course?**. When
   ticked, another hub user may copy your course as a starting point for
   their own, and their copy keeps a link back to yours.
7. Select **Save**.

A new course is created in **Draft** state and you become its manager. Only
course managers can see a draft course, so you can build it before anyone
arrives. When it is ready, select **Publish** in the banner at the top of the
course overview.

> **Note:** The component has an **Auto Approve Courses?** option, but nothing
> in the code reads it. Every course you create is yours to publish.

### Editing the course overview page

Go to `/courses/{alias}`. Where you can change something, a small **Edit**
button appears next to a label saying what it changes.

**Title & Short description** — opens a form with **Title**,
**Short description**, **Categories (tags)** and the **Allow forks**
checkbox. Select **Save**.

**Time & Effort** — the panel on the right. Opens **Course length** and
**Estimated Effort**, both free text (`4 weeks`, `5 - 8 hours per week`).
Whatever you type appears in the summary table beside the enrolment button.

**Long description** — on the **Overview** tab. Opens the rich-text editor on
the course's long description.

**Page contents** — on each extra page tab you have added. Gives you **Edit**
and **Delete** for that page.

**Logo** — drop an image onto the panel on the right, or use the file field
beneath it. It appears beside the course title.

#### Adding an overview page

An overview page is an extra tab on the course overview.

1. Select the **Add page** tab at the end of the tab strip.
2. Fill in **Title**, and an **Alias** if you want to control the URL.
   Leave the alias empty and it is made from the title.
3. Write the page in the editor.
4. Select **Save**.

#### Instructors and managers

The **Manage** button beside **Instructors/Managers** in the right-hand
column opens a pop-up.

1. In **Enter comma-separated usernames or IDs**, type the people to add.
   The field takes several at once.
2. Pick a role from **Select role**. Roles that belong to a single offering
   are grouped under that offering's name.
3. Select **Add**. **Changes saved** appears and the person is listed below.
4. To change somebody's role, pick a different one from the drop-down beside
   their name. It saves as soon as you change it.
5. To remove somebody, tick the box beside their name and select **Remove**.

> **Note:** A course must keep at least one manager. Removing the last one
> fails with an error.

#### Creating an offering

An offering is one run of the course. Create a new offering when the material
changes significantly, rather than editing the old one.

- On a course with no offerings, a panel says *This course needs an
  offering!* with a **Create an offering** button.
- On a course that has one, use the **New** button on the **Offerings** tab.

Either way the form asks for an **Alias** and a **Title**; only the title is
required. Select **Save**.

The **Offerings** tab lists each offering with the number **Enrolled** and its
**Enrollment** status — **Accepting**, **Restricted** or **Closed** — and a
button reading **Access Course** or **Enroll in Course**.

> **Note:** Once an offering exists you cannot edit its title, dates or
> state from the site. The offering controller has `edit`, `save` and
> `delete` tasks, but `edit` has no template and the other two only redirect.
> Use the administrator interface.

#### Copying and forking

**Copy** appears at the top right for a manager of the course, and makes a
duplicate you own. **Fork** appears instead for anyone else when the course
has **Allow forks** turned on, and makes a copy that keeps an attribution
back to the original. Both ask you for a new alias.

### Building the outline

The outline is the course. A unit is a week, an asset group is a kind of
thing inside that week, and an asset is one lecture, file, video or quiz. If
you get the units right the rest is filling them.

1. Select **Go to Course** on the course overview to enter the offering.
2. Open the **Outline** tab and select **Edit outline**.

The builder is a single page that saves as you go. **Done** at the top right
returns to the outline; **Deleted Assets** opens the tray of removed items.

#### Units

Select **Add a new unit** at the bottom of the list. To rename one, select
**edit** beside its title, change **Title:**, and select **Save**.

Each new unit comes with the hub's default asset groups, which an
administrator sets under the component's **Default Asset Groups** option —
normally **Lectures**, **Homework** and **Exam**.

#### Asset groups

Select **edit** on an asset group's title to open its form:

| Field | Notes |
|---|---|
| **Title:** | What the group is called |
| **Published:** | **Yes** or **No** |
| **Short Description:** | One or two sentences |

Inside each group, **Add a new lecture** (or homework, or exam — the label is
the group's title in the singular) creates a place to put material.

#### Adding material

Each place you have added carries a drop zone reading *Drag files here to
upload*, and beneath it a row of buttons:

| Button | What it does |
|---|---|
| **Attach a link** | Paste one or more URLs into the box and select **Add** |
| **Embed a Kaltura or YouTube Video** | Paste the video's embed code |
| **Include a wiki page** | Write a wiki page in place, with references |
| **Include a tool** | Attach one of your hub tools. Only appears when the hub has a tool directory configured |
| **Browse for files** | Pick files from your computer |

All of them take several items at once. Once a file lands, the hub works out
what kind of asset it is from the extension. Where more than one kind is
possible you are asked **What do you want to do with these files?** and given
the choice — a PDF, for example, can become **Post notes or slides (i.e. a
downloadable file)** or **Create a quiz/exam**.

Each asset in the list carries icons for **preview**, **edit** and **delete**,
and a checkbox reading **Mark as reviewed and publish?**. Tick it and the
label changes to **Published**. Selecting the asset's title lets you rename it
in place.

#### The Edit Asset dialog

The **edit** icon opens a dialog with:

| Field | Notes |
|---|---|
| **Title:** | |
| **URL:** | Hidden for quizzes and exams |
| **Type:** | Video, File, Form, Text, URL |
| **Subtype:** | Video, Embedded, File, Exam, Quiz, Homework, Note, Wiki, Link, Tool |
| **Attach to:** | Move the asset to a different asset group |
| **Create a gradebook entry for this item?** | Makes it gradable |
| **Include this item in the progress calculation?** | Counts it towards the student's progress bar |
| **Launch a tool with this file?** | Only when the hub has tools and the asset is a file or a tool URL |
| **Prerequisites:** | See below |

Select **Submit** to save, **Cancel** to close.

### Quizzes and exams

This is the part that pays for running the course on the hub rather than by
email: you draw boxes over a PDF of your existing exam paper, say which
answer is right, and the hub marks every submission and puts the score in the
gradebook. Note the limit before you plan around it.

Only a PDF can become a quiz or exam the hub marks for itself. Any asset can
be given a gradebook column with **Create a gradebook entry for this item?**
in the **Edit Asset** dialog, but you enter those scores by hand from the
**Progress** tab.

1. Drop the PDF onto an asset group.
2. Answer **What do you want to do with these files?** with
   **Create a quiz/exam**.
3. Fill in **Title:**.
4. For each question, drag a box around the question and all of its answers.
   A pale yellow marker appears around it.
5. Click beside each possible answer to drop a radio button there.
6. Select the radio button next to the correct answer.
7. Repeat for every question, then select **Save and Close**.

#### Deployment settings

Tick the publish checkbox on the finished quiz and the deployment form opens.

**Times**

- **Time limit:** in minutes. A timed form shows the student a landing page
  first, so the countdown does not start before they are ready.
- **Attempts:** how many tries are allowed. The default is 1.

**Results**

Two sets of radio buttons, one for *While the form is open, show users:* and
one for *After the form is closed, show users:*. Both offer:

- **only confirmation that their submission was accepted**
- **their score**
- **a complete comparison of their answers to the correct answers**

Select **Create deployment**.

> **Note:** There are no dates on this form. When a quiz or exam becomes
> available is set per section, on the **Dates/Times** tab of the section in
> the administrator interface.

You can reopen the deployment later with the *edit deployment* icon beside the
asset, and the question layout with the *edit layout* icon.

### Prerequisites

A prerequisite must be completed before the item that requires it can be
opened. Units require units; assets require assets.

**On a unit:** in the outline builder, select **edit** beside the unit title.
Under **Prerequisites:**, pick a unit from the *add prerequisite…* drop-down.

**On an asset:** open the asset's **Edit Asset** dialog and use the
**Prerequisites:** section the same way.

Either way the prerequisite is saved as soon as you pick it, and appears in a
list above the drop-down with an **x** to take it off again. You can set more
than one. Only published assets are offered as asset prerequisites.

### Restoring deleted material

Deleting an asset in the builder moves it to a tray rather than destroying it.

1. In the outline builder, select **Deleted Assets** at the top.
2. Find the item and select **Restore**.

The button is hidden when the tray is empty.

### The gradebook

The **Progress** tab shows students their own progress. For an instructor it
shows the whole class, with three views selected from the buttons at the top
left: *progress view*, *gradebook view* and *reports view*. Alongside them:

| Button | What it does |
|---|---|
| *edit grade policy* | Set how the course grade is worked out. Hidden when the hub has turned off **Can section owners edit grading policy?** and you are not a course manager |
| *add a new entry* | Add a gradebook column by hand. Course managers only |
| *export to csv* | Download the gradebook |
| *refresh gradebook view* | Reload the grid |
| *download selected detailed results* | Download the individual responses to a form |

A **Search students** box and a pager sit below.

### Putting an image in a page

The **Pages** tab inside a course offering — not the overview pages on the
course — has a file uploader beside the editor, so an image can be uploaded
and then referenced from the page body.

1. Open the **Pages** tab and select **Add page**, or the **Edit** icon on an
   existing page.
2. Drop an image onto the **Click or drop file** area on the right.
3. Copy the URL that appears under the uploaded file.
4. Put the cursor where the image should go, then use your editor's image
   button and paste the URL into its URL field. In CKEditor that is the
   **Image** button and the **Image Info** tab; other editors differ, because
   the hub chooses which editor to load.
5. Select **Save**.

The page form also has **Page appears for section:**, which restricts the page
to one section of the offering, or **- All sections -**.
