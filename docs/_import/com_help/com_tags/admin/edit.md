<!--
status: imported
source: core/components/com_tags/admin/help/en-GB/edit.phtml
imported: 2026-09-09
-->
# Tags Manager: Tags: Adding/Editing

- [Overview](#overview)
- [Toolbar](#toolbar)
- [Details](#details)
- [Substitutions](#substitutions)

<a id="overview"></a>

## Overview

<a id="toolbar"></a>

## Toolbar

At the top right you will see the toolbar. The functions are:

- **Save**  
  Save changes and return to the edit form.
- **Save & Close**  
  Save changes and return to the listing of entries.
- **Cancel**  
  Discard any changes made and return to the listing of entries.
- **Help**  
  Opens this help screen.

<a id="details"></a>

You will be presented with a form to enter your new tag. Only the field labeled “Tag” is required.

- **Admin**  
  This is a flag that designates if a tag is an administrative tag. Admin tags are only seen by administrators and are typically used for tagging content with information that may not be relevant or desired to be known by front-end users.
- **Tag**  
  The tag you wish to create.
- **Alias**  
  An alternate version of the tag such as a pluralized spelling of the tag or common abbreviation. For example, the tag “New York” may have an alias of “ny”.
- **Description**  
  A description or definition of the tag.
- **Substitute for**  
  A comma-separated list of tags you wish the current tag being created/edited to be substituted for.

<a id="substitutions"></a>

## Substitutions

Substitutions are similar to aliases in that they allow some basic mapping of one or more tags to another tag. The difference lies in that an alias is still searchable; Substitutions are not.

## How it works

As an example, imagine you are creating a tag called "water". For substitutions, you put in "H2O, aqua, wet stuff that falls from the sky". Then a user enters "aqua" as a tag on their contribution. First, the system will try to find the tag "aqua". If it doesn't exist, it will then check if it is to be substituted for a different tag. In this case, "aqua" is to be substituted with "water". So, the resulting contribution will be tagged with "water" and this is what all users will see.

## Adding a substitution for an existing tag

In the example above, the tag "aqua" doesn't exist before the tag "water" is created. What happens if "aqua" *did* already exist? At the time of creation for the "water" tag—when you hit "save"—the system moved any associations for existing tags that matched the substitutions for "water". That is, if there were three items tagged with "aqua", they would now be tagged with "water" and the "aqua" tag would be deleted. This is functionally the same as merging.

> **Note:** This operation can not be easily undone.
