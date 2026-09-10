<!--
status: imported
source: core/components/com_courses/admin/help/en-GB/offering.phtml
imported: 2026-09-09
-->
# Courses: Edit Offering

- [Overview](#overview)
- [Toolbar](#toolbar)
- [Details](#details)
- [Publishing](#publishing)
- [Logo](#logo)
- [Parameters](#parameters)

<a id="overview"></a>

## Overview

The edit/creation form for a course offering.

<a id="toolbar"></a>

## Toolbar

At the top right you will see the toolbar.

The functions are:

- **Save**  
  Save changes and return to the edit form.
- **Save & Close**  
  Save changes and return to the listing of entries.
- **Cancel**  
  Discard any changes made and return to the listing of entries.
- **Help**  
  Opens this help screen.

<a id="details"></a>

## Details

Every entry consists of a **title** and **alias**. Optional fields include **starts**, **ends**, and a logo.

- **Title**  
  The title of the post. A required field.
- **Alias**  
  This is an optional identifier used priamrily for URLs. When an alias isn't provided, one is generated from the provided title. The text is made lowercase, all punctuation is stripped, and spaces are turned into dashes.

<a id="publishing"></a>

## Publishing

The following options control various aspects of the published state (when and if an entry is available to users).

- **State**  
  The published status of the entry. Unpublished and trashed jobs will never be run.
- **Starts**  
  A timestamp (YYYY-MM-DD hh:mm:ss) for when an entry should **start** being available. This allows for an entry to be set up and published in advance of when one wishes the entry to be read. When used with "Ends", this gives the option of creating a window of time one wants the entry to available. For instance, an entry can be scheduled to be only available between March and September.
- **Ends**  
  A timestamp (YYYY-MM-DD hh:mm:ss) for when an entry should **stop** being available. This allows for an entry to stop being available after a given date/time. When used with "Starts", this gives the option of creating a window of time one wants an entry to be available. For instance, an entry can be scheduled to be only available between March and September.

<a id="logo"></a>

## Logo

This area is for attaching a picture or logo for the specific offering, allowing for more specific branding beyond the course's logo.

<a id="parameters"></a>

## Parameters

Here one will find various optional parameters for the offering.
