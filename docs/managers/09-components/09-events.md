<!--
status: rewritten
reviewed-against: 2.4-main @ 6efbbe32ed
reviewed: 2026-09-09
source: https://help.hubzero.org/documentation/240/managers/components/events
-->
# Events

The Events component is the hub calendar. It holds events with a start
and end time, a category, an optional set of extra pages, and optional
registration that collects respondent details. Events appear on the site
at `/events`. This chapter covers the administrator's side; the
[Hub users](../../users/events.md) book covers browsing, submitting, and
registering.

Open it in the administrator interface under **Components > Events**.
Three sub-menu links sit at the top left: **Events**, the list below;
**Categories**, which opens the shared Categories component filtered to
Events; and **Configuration**, the component's own settings screen.

> **Note:** Group calendars are a separate feature. They live in the
> `core/plugins/groups/calendar` plugin and are edited inside a group,
> not here. Group events do appear in the Events Manager list (marked
> with their group name), and opening one on the site redirects to the
> group's calendar.

## Events

The **Events Manager** screen lists every event. Above the list, filter
by a search term (matched against titles), a category, or a group, then
press **Go**. The columns are:

| Column | Shows |
|---|---|
| ID | The record's numeric identifier. |
| Title | The event title, linking to its edit form. An event another administrator has open shows the title as plain text with a "Checked out" tooltip and no checkbox. |
| Category | The category the event belongs to. |
| State | **Published**, **Unpublished**, **Pending** (published, but the start date has not arrived) or **Expired** (published, but the end date has passed). Click it to toggle between published and unpublished. |
| Timesheet | The From and To dates, or **Always** / **Never** when a date is missing. |
| Access | **Public** for hub events, or a link to the owning group for group events. |
| Pages | An "*n* Page(s)" link to the event's pages. |

The columns do not sort; use the filters and the pager at the bottom.

The toolbar offers:

- **Options** — the component-wide configuration form built from
  `config.xml`; see [Options](#options) below.
- **Add Page** — with one event checked, opens a new page form for it.
- **View Respondents** — with one event checked, opens its registration
  list.
- **Publish** and **Unpublish** — change the state of the checked events.
- **New** — create an event.
- **Edit** — open the checked event. Clicking a title does the same.
- **Delete** — remove the checked events. This is permanent, and it also
  deletes their pages, their respondents, and their tags.
- **Help** — the built-in help screen.

## Creating or editing an event

The edit screen is titled **Event: New** or **Event: Edit**, with
**Save**, **Cancel** and **Help** in the toolbar. There is no Save &
Close; **Save** returns to the list.

**Event**

| Field | Notes |
|---|---|
| Title | Required. Up to 250 characters. |
| Category | Required. Only categories that belong to Events are listed. At least one must exist, or the form refuses to open. |
| Activity | The event description, in the editor. Stored as the event's body text. |
| Where | The location. Up to 120 characters. Email addresses and URLs in it are turned into links. |
| Contact | Up to 120 characters. An address or a `mailto:` value becomes an obfuscated mail link. This text is also shown above the registration form as the "For Information Contact" block. |
| Website | Up to 240 characters. `http://` is prepended if you leave the scheme off. |
| *Custom fields* | Any custom fields defined on the Configuration screen appear here as text boxes or checkboxes, marked required where configured. |
| Tags | Comma-separated. Tags connect the event to searches and to other tagged content. |

**Publishing**

| Field | Notes |
|---|---|
| Start date | Date and time the event begins. |
| End date | Date and time it ends. |
| Time Zone | The event's own zone, chosen from the full IANA list labelled with its current UTC offset. Times are stored in UTC and always displayed in this zone on the site. |

**Recurrence** appears only for group events, and takes an RRULE string.

**Registration**

| Field | Notes |
|---|---|
| Register by | The date and time registration closes. Leave it empty and the event has no registration at all: no Register tab appears on the site. |
| Email | The address registration confirmations are copied to. |
| Restricted | A password. Set it and visitors must enter it before the registration form is shown — an invite-only event. |

The right-hand column shows the event's state, and when and by whom it
was created and last modified. Below that, **Registration Fields**
toggles each optional part of the registration form for this event
(affiliation, title, address, phone, fax, email, website, position,
degree, gender, race, arrival, departure, disability, dietary needs,
dinner, abstract with its instruction text, and comments).

> **Note:** The state is not editable on this form. A new event is
> published as soon as you save it; to change the state later, use
> **Publish** / **Unpublish** in the list. The **Email** registration
> field is always shown regardless of the toggle — saving forces it back
> on.

## Pages

An event can carry extra pages — an agenda, directions, a speaker list —
which appear as tabs beside **Overview** on the event's page on the site.
Reach them with **Add Page**, or by clicking the "*n* Page(s)" link in
the Events list.

The list shows the ID, the title with its alias in parentheses, and
reorder arrows. The toolbar has **New**, **Edit** and **Delete**. The
page form asks for a **Title** (required), an **Alias** (the URL segment;
alphanumeric, no spaces) and **Page text** (required, in the editor), and
shows the ordering and creation details beside it.

## Respondents

**View Respondents** opens the registrations for the checked event. The
list shows each respondent's **Name** (linking to the full submission),
**Email**, **Registered** date, **Special needs** (dietary text and a
note when disability contact was requested) and **Comment**. Search by
name with the box above the list.

The toolbar has **Download CSV**, which returns `eventrsvp.csv` with the
columns Name, Email, Telephone, Affiliation, Arrival, Departure,
Disability, Dietary, Dinner and Registered; **Delete**, which permanently
removes the checked registrations; and **Cancel**.

Clicking a name shows every field the respondent submitted, including
affiliation, position, location, phone and fax, website, racial
background, gender, arrival and departure, disability and dietary needs,
dinner attendance, abstract and comments. Only fields with a value are
listed.

> **Note:** The **Name** and **Special needs** column headings are
> rendered as sort links, but the respondents table has no such columns
> and sorting on them fails. Sort on **Email** or **Registered** instead.

## Configuration

The **Configuration** sub-menu link opens the component's own settings,
stored separately from the Options form:

| Setting | Effect |
|---|---|
| Admin mail | No effect; the value is stored but never read. |
| First day | Sunday first or Monday first. Sets the day the week view and the mini calendar start on. |
| View mail ? | No effect. |
| View "By" ? | Yes adds a **Submitted by** row to the event page on the site. |
| View "Hits" ? | No effect. |
| Date Format ? | French-English, US or Deutsch. Controls how dates are assembled in the calendar headings. |
| Use 12hr time Format ? | Yes turns the time boxes on the event forms into 12-hour entry with AM/PM radio buttons. |
| Start Page ? | Which view `/events` opens on. Day, Week, Month and Year all work; there is no Categories view in the component, so do not choose it. |
| No. of Events to List per page… | No effect. |

Below that, **Custom fields** defines up to ten (or more, once ten are
filled) extra fields for the event form. Each row takes a **Field**
label, a **Type** of Text or Checkbox, **Required**, and **Show in
listing** — which puts the field's value on the event's row in the
calendar listing instead of a description excerpt. Custom field values
are stored inside the event's description text.

## Options

The **Options** button on the Events list opens a second, older settings
form built from `config.xml`. Every parameter on it is listed in the
[configuration reference](../../reference/configuration/components/events.md).

> **Note:** Nothing in the component reads those parameters — the
> component reads the **Configuration** screen described above instead.
> Use **Configuration** for anything you want to take effect; the
> Options form is only useful for its **Permissions** tab.

The **Permissions** tab controls who may configure the component
(`core.admin`), access the administrator screens (`core.manage`), and
create, delete, edit, change the state of, and edit their own events.
The same actions can be set on categories, and delete, edit and edit
state can be set on individual events. On the site, an event's owner may
edit or delete it when granted **Edit Own**, and an event submitted by
someone without **Edit State** is saved unpublished and waits for an
administrator to publish it.

## API

The component exposes two read-only endpoints: a list of active events
and a single event by ID. Both are documented in the
[API reference](../../reference/api/events.md).
