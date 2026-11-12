<!--
status: generated
source: Event::trigger('courses.*') call sites and core/plugins/courses/
-->

# Courses events

Events in the `courses` group. A plugin in `core/plugins/courses/` receives an event by defining a public method with the event's name; the arguments are those the call site passes, in order.

## `courses.onAfterDeleteCoupon`

No call site in the CMS fires this event; the listener may answer an event fired by another package or by an older plugin.

Listeners:

- `plg_courses_store` — [`onAfterDeleteCoupon($model)`](../../../core/plugins/courses/store/store.php)

## `courses.onAfterSaveCoupon`

No call site in the CMS fires this event; the listener may answer an event fired by another package or by an older plugin.

Listeners:

- `plg_courses_store` — [`onAfterSaveCoupon($model, $isNew = false)`](../../../core/plugins/courses/store/store.php)

## `courses.onAssetgroupDelete`

No call site in the CMS fires this event; the listener may answer an event fired by another package or by an older plugin.

Listeners:

- `plg_courses_discussions` — [`onAssetgroupDelete($assetgroup)`](../../../core/plugins/courses/discussions/discussions.php)

## `courses.onAssetgroupEdit`

Fired from:

- [`core/components/com_courses/admin/views/assetgroups/tmpl/edit.php:213`](../../../core/components/com_courses/admin/views/assetgroups/tmpl/edit.php#L213)
- [`core/plugins/courses/outline/views/outline/tmpl/editassetgroup.php:47`](../../../core/plugins/courses/outline/views/outline/tmpl/editassetgroup.php#L47)

Listeners:

- `plg_courses_discussions` — [`onAssetgroupEdit()`](../../../core/plugins/courses/discussions/discussions.php)

## `courses.onAssetgroupSave`

No call site in the CMS fires this event; the listener may answer an event fired by another package or by an older plugin.

Listeners:

- `plg_courses_discussions` — [`onAssetgroupSave($assetgroup)`](../../../core/plugins/courses/discussions/discussions.php)

## `courses.onCourse`

Fired from:

- [`core/components/com_courses/site/controllers/offering.php:211`](../../../core/components/com_courses/site/controllers/offering.php#L211) with `[ $this->course, $this->course->offering(), true, ]`
- [`core/components/com_courses/site/controllers/offering.php:230`](../../../core/components/com_courses/site/controllers/offering.php#L230) with `[ $this->course, $this->course->offering(), ]`
- [`core/plugins/courses/guide/views/guide/tmpl/overlay.php:23`](../../../core/plugins/courses/guide/views/guide/tmpl/overlay.php#L23) with `[ $this->course, $this->offering, true, ]`

Listeners:

- `plg_courses_announcements` — [`onCourse($course, $offering, $describe = false)`](../../../core/plugins/courses/announcements/announcements.php)
- `plg_courses_dashboard` — [`onCourse($course, $offering, $describe = false)`](../../../core/plugins/courses/dashboard/dashboard.php)
- `plg_courses_discussions` — [`onCourse($course, $offering, $describe = false)`](../../../core/plugins/courses/discussions/discussions.php)
- `plg_courses_guide` — [`onCourse($course, $offering, $describe = false)`](../../../core/plugins/courses/guide/guide.php)
- `plg_courses_notes` — [`onCourse($course, $offering, $describe = false)`](../../../core/plugins/courses/notes/notes.php)
- `plg_courses_outline` — [`onCourse($course, $offering, $describe = false)`](../../../core/plugins/courses/outline/outline.php)
- `plg_courses_pages` — [`onCourse($course, $offering, $describe = false)`](../../../core/plugins/courses/pages/pages.php)
- `plg_courses_progress` — [`onCourse($course, $offering, $describe = false)`](../../../core/plugins/courses/progress/progress.php)

## `courses.onCourseAfterLecture`

Fired from:

- [`core/plugins/courses/outline/views/outline/tmpl/lecture.php:315`](../../../core/plugins/courses/outline/views/outline/tmpl/lecture.php#L315) with `[ $this->course, $unit, $lecture, ]`

Listeners:

- `plg_courses_discussions` — [`onCourseAfterLecture($course, $unit, $lecture)`](../../../core/plugins/courses/discussions/discussions.php)
- `plg_courses_notes` — [`onCourseAfterLecture($course, $unit, $lecture)`](../../../core/plugins/courses/notes/notes.php)

## `courses.onCourseAfterOutline`

No call site in the CMS fires this event; the listener may answer an event fired by another package or by an older plugin.

Listeners:

- `plg_courses_guide` — [`onCourseAfterOutline($course, $offering)`](../../../core/plugins/courses/guide/guide.php)

## `courses.onCourseAreas`

No call site in the CMS fires this event; the listener may answer an event fired by another package or by an older plugin.

Listeners:

- `plg_courses_discussions` — [`onCourseAreas()`](../../../core/plugins/courses/discussions/discussions.php)

## `courses.onCourseBeforeOutline`

Fired from:

- [`core/plugins/courses/outline/views/outline/tmpl/default.php:108`](../../../core/plugins/courses/outline/views/outline/tmpl/default.php#L108) with `[ $course, $offering, ]`

Listeners:

- `plg_courses_announcements` — [`onCourseBeforeOutline($course, $offering)`](../../../core/plugins/courses/announcements/announcements.php)

## `courses.onCourseDashboard`

Fired from:

- [`core/plugins/courses/dashboard/views/overview/tmpl/default.php:176`](../../../core/plugins/courses/dashboard/views/overview/tmpl/default.php#L176) with `[$this->course, $this->offering]`

Listeners:

- `plg_courses_announcements` — [`onCourseDashboard($course, $offering)`](../../../core/plugins/courses/announcements/announcements.php)
- `plg_courses_discussions` — [`onCourseDashboard($course, $offering)`](../../../core/plugins/courses/discussions/discussions.php)

## `courses.onCourseDelete`

Fired from:

- [`core/components/com_courses/models/orm/course.php:388`](../../../core/components/com_courses/models/orm/course.php#L388) with `[$this]`

Listeners:

- `plg_courses_discussions` — [`onCourseDelete($course)`](../../../core/plugins/courses/discussions/discussions.php)
- `plg_courses_store` — [`onCourseDelete($model)`](../../../core/plugins/courses/store/store.php)

## `courses.onCourseDeleteCount`

Fired from:

- [`core/components/com_courses/site/controllers/course.php:573`](../../../core/components/com_courses/site/controllers/course.php#L573) with `[$this->course]`

No plugin in the source tree listens for this event.

## `courses.onCourseEdit`

Fired from:

- [`core/components/com_courses/admin/views/courses/tmpl/edit.php:249`](../../../core/components/com_courses/admin/views/courses/tmpl/edit.php#L249)

No plugin in the source tree listens for this event.

## `courses.onCourseEnrollLink`

No call site in the CMS fires this event; the listener may answer an event fired by another package or by an older plugin.

Listeners:

- `plg_courses_store` — [`onCourseEnrollLink($course, $offering, $section)`](../../../core/plugins/courses/store/store.php)

## `courses.onCourseEnrolled`

Fired from:

- [`core/components/com_courses/site/controllers/offering.php:378`](../../../core/components/com_courses/site/controllers/offering.php#L378) with `[ $this->course, $offering, $offering->section(), ]`

Listeners:

- `plg_courses_pec` — [`onCourseEnrolled($course, $offering, $section)`](../../../core/plugins/courses/pec/pec.php)

## `courses.onCourseSave`

Fired from:

- [`core/components/com_courses/models/orm/course.php:375`](../../../core/components/com_courses/models/orm/course.php#L375) with `[$this]`

Listeners:

- `plg_courses_store` — [`onCourseSave($model)`](../../../core/plugins/courses/store/store.php)

## `courses.onCourseView`

Fired from:

- [`core/components/com_courses/site/controllers/course.php:150`](../../../core/components/com_courses/site/controllers/course.php#L150) with `[ $this->course, $this->view->active, ]`

Listeners:

- `plg_courses_offerings` — [`onCourseView($course, $active = null)`](../../../core/plugins/courses/offerings/offerings.php)
- `plg_courses_overview` — [`onCourseView($course, $active = null)`](../../../core/plugins/courses/overview/overview.php)
- `plg_courses_reviews` — [`onCourseView($course, $active = null)`](../../../core/plugins/courses/reviews/reviews.php)
- `plg_courses_store` — [`onCourseView($course, $active=null)`](../../../core/plugins/courses/store/store.php)

## `courses.onCourseViewAfter`

Fired from:

- [`core/components/com_courses/site/views/course/tmpl/display.php:778`](../../../core/components/com_courses/site/views/course/tmpl/display.php#L778) with `[$this->course]`

Listeners:

- `plg_courses_related` — [`onCourseViewAfter($course)`](../../../core/plugins/courses/related/related.php)

## `courses.onOfferingDelete`

No call site in the CMS fires this event; the listener may answer an event fired by another package or by an older plugin.

Listeners:

- `plg_courses_store` — [`onOfferingDelete($model)`](../../../core/plugins/courses/store/store.php)

## `courses.onOfferingEdit`

Fired from:

- [`core/components/com_courses/admin/views/offerings/tmpl/edit.php:363`](../../../core/components/com_courses/admin/views/offerings/tmpl/edit.php#L363)

Listeners:

- `plg_courses_pec` — [`onOfferingEdit()`](../../../core/plugins/courses/pec/pec.php)
- `plg_courses_store` — [`onOfferingEdit()`](../../../core/plugins/courses/store/store.php)

## `courses.onOfferingSave`

No call site in the CMS fires this event; the listener may answer an event fired by another package or by an older plugin.

Listeners:

- `plg_courses_store` — [`onOfferingSave($model)`](../../../core/plugins/courses/store/store.php)

## `courses.onSectionDelete`

No call site in the CMS fires this event; the listener may answer an event fired by another package or by an older plugin.

Listeners:

- `plg_courses_pec` — [`onSectionDelete($model)`](../../../core/plugins/courses/pec/pec.php)
- `plg_courses_store` — [`onSectionDelete($model)`](../../../core/plugins/courses/store/store.php)

## `courses.onSectionEdit`

Fired from:

- [`core/components/com_courses/admin/views/sections/tmpl/edit.php:663`](../../../core/components/com_courses/admin/views/sections/tmpl/edit.php#L663)

Listeners:

- `plg_courses_discussions` — [`onSectionEdit()`](../../../core/plugins/courses/discussions/discussions.php)

## `courses.onSectionSave`

No call site in the CMS fires this event; the listener may answer an event fired by another package or by an older plugin.

Listeners:

- `plg_courses_pec` — [`onSectionSave($model, $isNew = false)`](../../../core/plugins/courses/pec/pec.php)
- `plg_courses_store` — [`onSectionSave($model, $isNew = false)`](../../../core/plugins/courses/store/store.php)

## `courses.onUnitDelete`

No call site in the CMS fires this event; the listener may answer an event fired by another package or by an older plugin.

Listeners:

- `plg_courses_discussions` — [`onUnitDelete($unit)`](../../../core/plugins/courses/discussions/discussions.php)

## `courses.onUnitSave`

No call site in the CMS fires this event; the listener may answer an event fired by another package or by an older plugin.

Listeners:

- `plg_courses_discussions` — [`onUnitSave($unit)`](../../../core/plugins/courses/discussions/discussions.php)
