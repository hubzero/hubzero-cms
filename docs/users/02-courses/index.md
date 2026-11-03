<!--
status: rewritten
reviewed-against: 2.4-main @ 42a7a5b5c7
reviewed: 2026-09-10
screenshots: ok
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
> [group](../11-groups/index.md), and belonging to the group a course is
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

- [Student features](01-students.md) — enrolling, working through the
  outline, tracking your progress, and claiming a certificate or badge.
- [Course manager features](02-course-managers.md) — what an
  instructor can do from the course pages themselves.
- The [Courses chapter](../../managers/09-components/10-courses.md) in the
  Hub managers book covers everything an administrator does in the back end,
  including sections, coupon codes, certificates and roles.
