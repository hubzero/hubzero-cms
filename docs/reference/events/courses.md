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

- `plg_courses_store` — [`onAfterSaveCoupon($model, $isNew=false)`](../../../core/plugins/courses/store/store.php)

## `courses.onAssetgroupDelete`

No call site in the CMS fires this event; the listener may answer an event fired by another package or by an older plugin.

Listeners:

- `plg_courses_discussions` — [`onAssetgroupDelete($assetgroup)`](../../../core/plugins/courses/discussions/discussions.php)

## `courses.onAssetgroupEdit`

No call site in the CMS fires this event; the listener may answer an event fired by another package or by an older plugin.

Listeners:

- `plg_courses_discussions` — [`onAssetgroupEdit()`](../../../core/plugins/courses/discussions/discussions.php)

## `courses.onAssetgroupSave`

No call site in the CMS fires this event; the listener may answer an event fired by another package or by an older plugin.

Listeners:

- `plg_courses_discussions` — [`onAssetgroupSave($assetgroup)`](../../../core/plugins/courses/discussions/discussions.php)

## `courses.onCourse`

Fired from:

- [`core/components/com_courses/site/controllers/offering.php:201`](../../../core/components/com_courses/site/controllers/offering.php#L201) with `[ $this->course, $this->course->offering(), true ]`
- [`core/components/com_courses/site/controllers/offering.php:217`](../../../core/components/com_courses/site/controllers/offering.php#L217) with `[ $this->course, $this->course->offering() ]`
- [`core/plugins/courses/guide/views/guide/tmpl/overlay.php:10`](../../../core/plugins/courses/guide/views/guide/tmpl/overlay.php#L10) with `[ $this->course, $this->offering, true ]`

Listeners:

- `plg_courses_announcements` — [`onCourse($course, $offering, $describe=false)`](../../../core/plugins/courses/announcements/announcements.php)
- `plg_courses_dashboard` — [`onCourse($course, $offering, $describe=false)`](../../../core/plugins/courses/dashboard/dashboard.php)
- `plg_courses_discussions` — [`onCourse($course, $offering, $describe=false)`](../../../core/plugins/courses/discussions/discussions.php)
- `plg_courses_guide` — [`onCourse($course, $offering, $describe=false)`](../../../core/plugins/courses/guide/guide.php)
- `plg_courses_notes` — [`onCourse($course, $offering, $describe=false)`](../../../core/plugins/courses/notes/notes.php)
- `plg_courses_outline` — [`onCourse($course, $offering, $describe=false)`](../../../core/plugins/courses/outline/outline.php)
- `plg_courses_pages` — [`onCourse($course, $offering, $describe = false)`](../../../core/plugins/courses/pages/pages.php)
- `plg_courses_progress` — [`onCourse($course, $offering, $describe=false)`](../../../core/plugins/courses/progress/progress.php)

## `courses.onCourseAfterLecture`

Fired from:

- [`core/plugins/courses/outline/views/outline/tmpl/lecture.php:284`](../../../core/plugins/courses/outline/views/outline/tmpl/lecture.php#L284) with `[ $this->course, $unit, $lecture ]`

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

- [`core/plugins/courses/outline/views/outline/tmpl/default.php:82`](../../../core/plugins/courses/outline/views/outline/tmpl/default.php#L82) with `[ $course, $offering ]`

Listeners:

- `plg_courses_announcements` — [`onCourseBeforeOutline($course, $offering)`](../../../core/plugins/courses/announcements/announcements.php)

## `courses.onCourseDashboard`

Fired from:

- [`core/plugins/courses/dashboard/views/overview/tmpl/default.php:133`](../../../core/plugins/courses/dashboard/views/overview/tmpl/default.php#L133) with `[$this->course, $this->offering]`

Listeners:

- `plg_courses_announcements` — [`onCourseDashboard($course, $offering)`](../../../core/plugins/courses/announcements/announcements.php)
- `plg_courses_discussions` — [`onCourseDashboard($course, $offering)`](../../../core/plugins/courses/discussions/discussions.php)

## `courses.onCourseDelete`

Fired from:

- [`core/components/com_courses/models/orm/course.php:407`](../../../core/components/com_courses/models/orm/course.php#L407) with `[$this]`

Listeners:

- `plg_courses_discussions` — [`onCourseDelete($course)`](../../../core/plugins/courses/discussions/discussions.php)
- `plg_courses_store` — [`onCourseDelete($model)`](../../../core/plugins/courses/store/store.php)

## `courses.onCourseDeleteCount`

Fired from:

- [`core/components/com_courses/site/controllers/course.php:589`](../../../core/components/com_courses/site/controllers/course.php#L589) with `[$course]`

No plugin in the source tree listens for this event.

## `courses.onCourseEnrollLink`

No call site in the CMS fires this event; the listener may answer an event fired by another package or by an older plugin.

Listeners:

- `plg_courses_store` — [`onCourseEnrollLink($course, $offering, $section)`](../../../core/plugins/courses/store/store.php)

## `courses.onCourseEnrolled`

Fired from:

- [`core/components/com_courses/site/controllers/offering.php:364`](../../../core/components/com_courses/site/controllers/offering.php#L364) with `[ $this->course, $offering, $offering->section() ]`

Listeners:

- `plg_courses_pec` — [`onCourseEnrolled($course, $offering, $section)`](../../../core/plugins/courses/pec/pec.php)

## `courses.onCourseSave`

Fired from:

- [`core/components/com_courses/models/orm/course.php:394`](../../../core/components/com_courses/models/orm/course.php#L394) with `[$this]`

Listeners:

- `plg_courses_store` — [`onCourseSave($model)`](../../../core/plugins/courses/store/store.php)

## `courses.onCourseView`

Fired from:

- [`core/components/com_courses/site/controllers/course.php:146`](../../../core/components/com_courses/site/controllers/course.php#L146) with `[ $this->course, $this->view->active ]`

Listeners:

- `plg_courses_offerings` — [`onCourseView($course, $active=null)`](../../../core/plugins/courses/offerings/offerings.php)
- `plg_courses_overview` — [`onCourseView($course, $active=null)`](../../../core/plugins/courses/overview/overview.php)
- `plg_courses_reviews` — [`onCourseView($course, $active=null)`](../../../core/plugins/courses/reviews/reviews.php)
- `plg_courses_store` — [`onCourseView($course, $active=null)`](../../../core/plugins/courses/store/store.php)

## `courses.onCourseViewAfter`

Fired from:

- [`core/components/com_courses/site/views/course/tmpl/display.php:721`](../../../core/components/com_courses/site/views/course/tmpl/display.php#L721) with `[$this->course]`

Listeners:

- `plg_courses_related` — [`onCourseViewAfter($course)`](../../../core/plugins/courses/related/related.php)

## `courses.onOfferingDelete`

No call site in the CMS fires this event; the listener may answer an event fired by another package or by an older plugin.

Listeners:

- `plg_courses_store` — [`onOfferingDelete($model)`](../../../core/plugins/courses/store/store.php)

## `courses.onOfferingEdit`

No call site in the CMS fires this event; the listener may answer an event fired by another package or by an older plugin.

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

No call site in the CMS fires this event; the listener may answer an event fired by another package or by an older plugin.

Listeners:

- `plg_courses_discussions` — [`onSectionEdit()`](../../../core/plugins/courses/discussions/discussions.php)

## `courses.onSectionSave`

No call site in the CMS fires this event; the listener may answer an event fired by another package or by an older plugin.

Listeners:

- `plg_courses_pec` — [`onSectionSave($model, $isNew=false)`](../../../core/plugins/courses/pec/pec.php)
- `plg_courses_store` — [`onSectionSave($model, $isNew=false)`](../../../core/plugins/courses/store/store.php)

## `courses.onUnitDelete`

No call site in the CMS fires this event; the listener may answer an event fired by another package or by an older plugin.

Listeners:

- `plg_courses_discussions` — [`onUnitDelete($unit)`](../../../core/plugins/courses/discussions/discussions.php)

## `courses.onUnitSave`

No call site in the CMS fires this event; the listener may answer an event fired by another package or by an older plugin.

Listeners:

- `plg_courses_discussions` — [`onUnitSave($unit)`](../../../core/plugins/courses/discussions/discussions.php)
