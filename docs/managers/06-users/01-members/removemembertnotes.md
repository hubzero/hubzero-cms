<!--
status: rewritten
reviewed-against: 2.4-main @ 123ea53b14
reviewed: 2026-09-09
screenshots: none
source: https://help.hubzero.org/documentation/240/managers/users/members/removemembertnotes
source-id: 3356
modified: 2021-03-04
source-state: unpublished
summary: Where member deletion is now documented.
-->
# Members removal tech notes

This page used to hold pasted excerpts of the code that runs when a member is
deleted. That material now lives in the Members manager chapter, checked
against the current source and with the plugin cascade written out:

- [What deleting a member removes](README.md#what-deleting-a-member-removes)

Two neighbouring sections cover the operations people usually mean when they
ask about removing a member:

- [Blocking an account](README.md#blocking-an-account) — take an account out
  of service without losing anything.
- [De-identifying members](README.md#de-identifying-members) — strip personally
  identifiable information but keep the account row.
