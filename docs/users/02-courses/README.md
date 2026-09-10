<!--
status: rewritten
reviewed-against: 2.4-main @ 1924c22171
reviewed: 2026-09-09
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

## Finding a course

`/courses` has a search box and a **Browse the catalog** button. The catalog
at `/courses/browse` lists published courses with their instructors, filters
by tag, and sorts by **Title**, **Alias** or **Popularity**.

## What to read next

- [Student features](student_features.md) — enrolling, working through the
  outline, tracking your progress, and claiming a certificate or badge.
- [Course manager features](course_manager_features.md) — what an
  instructor can do from the course pages themselves.
- The [Courses chapter](../../managers/09-components/10-courses.md) in the
  Hub managers book covers everything an administrator does in the back end,
  including sections, coupon codes, certificates and roles.
