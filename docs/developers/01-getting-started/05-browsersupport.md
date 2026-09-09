<!--
status: imported
source: https://help.hubzero.org/documentation/240/webdevs/index/browsersupport
source-id: 3427
modified: 2012-04-09
imported: 2026-09-09
source-state: unpublished
-->
# Browser Support

## Overview

The Browser Test Baseline provides a baseline set of browsers that should be tested. It is designed to maximize coverage with limited testing resources by testing the smallest possible subset of browser combinations and leveraging implicit coverage from shared core browser engines. At the very least, all listed browsers should be tested in one operating system, in order to provide "baseline" coverage. Testing on multiple operating systems should be accommodated after all browsers have been verified with baseline coverage and should start with features that have known platform-specific issues. The test platforms should be chosen based on usage statistics and market trends.

The Browser Test Baseline defines the current set of browers that should receive a verified, usable experience. However, trying to deliver the same "A-grade" experience across all tested browsers is neither cost-effective nor common. We support a tiered approach to user experience design, development, and testing, and encourage each project to define their own tiers that serve their users and their testing resources best.

## Browser Test Baseline

<table>
<tbody>
<tr>
<th scope="row">Internet Explorer</th>
<td>7.0</td>
<td>8.0</td>
<td>9.0</td>
</tr>
<tr>
<th scope="row">Firefox</th>
<td>3.†</td>
<td>4.†</td>
<td>5.†</td>
</tr>
<tr>
<th scope="row">Chrome †</th>
<td colspan="3">Latest stable</td>
</tr>
<tr>
<th scope="row">Safari</th>
<td>5.†</td>
<td>iOS 3.†</td>
<td>iOS 4.†</td>
</tr>
<tr>
<th scope="row">Webkit</th>
<td colspan="3">Android 2.†</td>
</tr>
<tr>
<th scope="row">Opera</th>
<td colspan="3">10.†</td>
</tr>
</tbody>
</table>

*Notes:*

- The dagger symbol (as in "Firefox 4.†") indicates that the most-current non-beta version at that branch level receives support.
- No guidance is given on iOS or Android OS device usage. The recommendation is that you choose the devices that are most representative of your user base for each OS.

The Browser Test Baseline provides a baseline set of browsers that should be tested. It is designed to maximize coverage with limited testing resources by testing the smallest possible subset of browser combinations and leveraging implicit coverage from shared core browser engines. At the very least, all listed browsers should be tested in one operating system, in order to provide "baseline" coverage. Testing on multiple operating systems should be accommodated after all browsers have been verified with baseline coverage and should start with features that have known platform-specific issues. The test platforms should be chosen based on usage statistics and market trends.

The Browser Test Baseline defines the current set of browers that should receive a verified, usable experience. However, trying to deliver the same "A-grade" experience across all tested browsers is neither cost-effective nor common. We support a tiered approach to user experience design, development, and testing, and encourage each project to define their own tiers that serve their users and their testing resources best.

## Graded Browser Support: What and Why

In the first 10 years of professional web development, back in the early '90s, browser support was binary: Do you — or don't you — support a given browser? When the answer was "No", user access to the site was often actively prevented. In the years following IE5's release in 1998, professional web designers and developers have become accustomed to asking at the outset of any new undertaking, "Do I have to support Netscape 4.x browsers for this project?"

By contrast, in modern web development we must support *all* browsers. Choosing to exclude a segment of users is inappropriate, and, with a "GradedBrowser Support" strategy, unnecessary.

Graded Browser Support offers two fundamental ideas:

- A broader and more reasonable definition of "support."
- The notion of "grades" of support.

### What Does "Support" Mean?

Support does not mean that everybody gets the same thing. Expecting two users using different browser software to have an identical experience fails to embrace or acknowledge the heterogeneous essence of the Web. In fact, requiring the same experience for all users creates an artificial barrier to participation. Availability and accessibility of content should be our key priority.

Consider television. At the core: TV distributes information. A hand-cranked emergency radio is capable of receiving television audio transmissions. It would be counter-productive to prevent access to this content, even though it's a fringe experience.

Some viewers still have black-and-white televisions. Broadcasting only in black-and-white — the "lowest common denominator" approach — ensures a shared experience but benefits no one. Excluding the black-and-white television owners — the "you must be this tall to ride" approach — provides no benefit either.

An appropriate support strategy allows every user to consume as much visual and interactive richness as their environment can support. This approach—commonly referred to as *progressive enhancement* — builds a rich experience on top of an accessible core, without compromising that core.

### Progressive Enhancement vs. Graceful Degradation

The concepts of *graceful degradation* and *progressive enhancement* are often applied to describe browser support strategies. Indeed, they are closely related approaches to the engineering of "fault tolerance".

These two concepts influence decision-making about browser support. Because they reflect different priorities, they frame the support discussion differently. Graceful degradation prioritizes *presentation*, and permits less widely-used browsers to receive less (and give less to the user). Progressive enhancement puts *content* at the center, and allows most browsers to receive more (and show more to the user). While close in meaning, progressive enhancement is a healthier and more forward-looking approach. Progressive enhancement is a core concept of Graded Browser Support.

## Grades of Support

### What are Grades of Support?

While an inclusive definition of browser support is necessary, the support continuum does present design, development, and testing challenges. If anything goes, how do I know when the experience is broken? To address this question and return a sense of order to the system, we define *grades* of support. There are three grades: A-grade, C-grade, and X-grade support.

Before examining each grade, here are some characteristics useful for defining levels of support.

Identified vs. Unknown There are over 10,000 browser brands, versions, and configurations and that number is growing. It is possible to group known browsers together.

Capable vs. Incapable No two browsers have an identical implementation. However, it is possible to group browsers according to their support for most web standards.

Modern vs. Antiquated As newer browser versions are released, the relevancy of earlier versions decreases.

Common vs. Rare There are thousands of browsers in use, but only a few dozen are widely used.

### <a id="the-three-grades"></a>Three Grades of Support

- **<a id="c-grade"></a>C-grade**  
  C-grade is the base level of support, providing core content and functionality. It is sometimes called core support. Delivered via nothing more than semantic HTML, the content and experience is highly accessible, unenhanced by decoration or advanced functionality, and forward and backward compatible. Layers of style and behavior are omitted.
  
  C-grade browsers should be identified on a blacklist.
  
  **Summary:** C-grade browsers are identified, incapable, antiquated and rare. QA tests a sampling of C-grade browsers, and bugs are addressed with high priority.
- **<a id="a-grade"></a>A-grade**  
  A-grade support is the highest support level. By taking full advantage of the powerful capabilities of modern web standards, the A-grade experience provides advanced functionality and visual fidelity.
  
  A-grade browsers should be identified on a whitelist. Approximately 96% of our audience enjoys an A-grade experience.
  
  **Summary:** A-grade browsers are identified, capable, modern and common. QA tests all A-grade browsers, and bugs are addressed with high priority.
- **<a id="x-grade"></a>X-grade**  
  X-grade provides support for unknown, fringe or rare browsers as well as browsers on which development has ceased. Browsers receiving X-grade support are assumed to be capable. (If a browser is shown to be incapable — if it chokes on modern methodologies and its user would be better served without decoration or functionality — then it should considered a C-grade browser.)
  
  X-grade browsers are all browsers not designated as any other grade.
  
  **Summary:** X-grade browsers are assumed to be capable and modern. QA does not test, and bugs are not opened against X-grade browsers.

#### <a id="a-grade-vs-x-grade"></a>The Relationship Between A-grade and X-grade Support

A bit more on the relationship between A-grade and X-grade browsers: One unexpected instance of X-grade is a newly released version of an A-grade browser. Since thorough QA testing is an A-grade requirement, a brand-new (and therefore untested) browser does not qualify as an A-grade browser. This example highlights a strength of the Graded Browser Support approach. The only practical difference between A-grade and X-grade browsers is that QA actively tests against A-grade browsers.

Unlike the C-grade, which receives only HTML, the X-grade receives everything that A-grade does. Though a brand-new browser might be characterized initially as a X-grade browser, we give its users every chance to have the same experience as A-grade browsers.

### <a id="beyond-three-grades"></a>Beyond the Three Grades

In recent years, we have seen a proliferation in tiers of support above and beyond the three grades identified above, where certain subsets of features are implemented only on certain subsets of browsers. Defining and implementing tiers of user experience should be done by each individual project. Overall, we promote the simplest Progressive Enhancement approach possible and discourage projects from creating new tiers without accounting for the additional costs in development, testing, and maintenance resources.

### <a id="testing"></a>Quality Assurance (QA) Testing

Grading the browser ecosystem enables meaningful, targeted, and cost-effective QA testing. As noted, representative C-grade testing and systematic A-grade testing ensures a usable and verified experience for the vast majority of our audience. A-grade testing must be thorough and complete, while C-grade testing can be accomplished with one or two representative browsers (e.g., Netscape 4.x and [Lynx](http://lynx.browser.org/)), or by using a modern browser with CSS and JavaScript disabled.

It's worth reiterating that testing resources do *not* examine X-grade browsers.

Representative testing of the core experience is critical. If you choose to adopt a Graded Browser Support approach for your own projects, be sure your site's core content and functionality are accessible without images, CSS, and JS. Ensure that the keyboard is adequate for task completion and that when your site is accessed by a C-grade browser all advanced functionality prompts are hidden.

## Conclusion

Graded Browser Support provides an inclusive definition of support and a framework for taming the ever-expanding world of browsers and frontend technologies.
