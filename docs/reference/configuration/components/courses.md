<!--
status: generated
source: core/components/com_courses/config/config.xml
-->

# Courses (com_courses)

Parameters from [`core/components/com_courses/config/config.xml`](../../../../core/components/com_courses/config/config.xml), as shown on the component's **Options** screen in the administrator interface.

## Basic

| Parameter | Label | Type | Default | Description |
|---|---|---|---|---|
| `uploadpath` | Upload path | text | `/site/courses` | File path for pictures |
| `tmpl` | Template | text | — | Template to use |
| `default_asset_groups` | Default Asset Groups | text | `Lectures, Homework, Exam` | Comma separated list of default asset groups to create when creating a new unit using the instructor builder. |
| `default_enrollment` | Default Enrollment | list | `0 (Open (anyone can join))` | Specify the enrollment setting for newly created sections. Options: `0` Open (anyone can join), `1` Restricted (coupon code is required), `2` Closed (no new enrollment). |
| `section_grade_policy` | Can section owners edit grading policy? | list | `1 (Yes)` | Specify whether or not section owners are allowed to editing the grading policy for their section. If no, policy inteface only shows up for course instructors. Options: `0` No, `1` Yes. |
| `progress_calculation` | Progress calculation based on: | list | `0` | Specify how progress fill calculation is performed. Options: `all` All published assets, `graded` All published, graded assets, `videos` All published, video assets, `manual` All published, manually selected assets. |
| `tool_path` | Tool parameter path | text | — | Path to shared directory used when crafting tool file passing path |
| `auto_approve` | Auto Approve Courses? | list | `1 (Yes)` | Courses are auto approved at creation. Otherwise Hub administrator must manually approve course. Options: `0` No, `1` Yes. |
| `show_stats` | Show Enrollment Numbers | list | `0 (No)` | Show enrollment numbers and stats for each course?. Options: `0` No, `1` Yes. |

## Badges

| Parameter | Label | Type | Default | Description |
|---|---|---|---|---|
| `passport_consumer_key` | Passport Key | text | — | API Key for Passport |
| `passport_consumer_secret` | Passport Secret | text | — | API Secret for Passport |
| `passport_issuer_id` | Passport Issuer ID | text | — | Passport ID for your issuer |
| `passport_client_id` | Passport Client ID | text | — | Passport ID for your client |
| `passport_username` | Passport Username | text | — | Passport username for hub |
| `passport_password` | Passport password | text | — | Passport password for hub |

## Unity

| Parameter | Label | Type | Default | Description |
|---|---|---|---|---|
| `unity_key` | Unity Key | text | — | Encryption key for unity communication |
