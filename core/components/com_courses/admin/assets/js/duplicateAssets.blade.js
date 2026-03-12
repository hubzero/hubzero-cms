/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */

document.addEventListener('DOMContentLoaded', function() {
	var coursesSelectTarget = document.querySelector('#coursesSelect');
	var offeringsSelectTarget = document.querySelector('#offeringsSelect');
	var unitsSelectTarget = document.querySelector('#unitsSelect');
	var assetGroupsSelectTarget = document.querySelector('#assetGroupsSelect');

	getList("/api/courses/assetgroup/getAllCourses").then(function(res) {
		var courses = res.courses;
		for (var i = 0; i < courses.length; i++) {
			var opt = document.createElement('option');
			opt.value = courses[i]["id"];
			opt.innerHTML = courses[i]["title"];
			coursesSelectTarget.appendChild(opt);
		}
	});

	coursesSelectTarget.addEventListener('change', function(event) {
		offeringsSelectTarget.innerHTML = '<option selected disabled>Select a Course Offering</option>';
		unitsSelectTarget.innerHTML = '<option selected disabled>Select a Course Unit</option>';
		assetGroupsSelectTarget.innerHTML = '<option selected disabled>Select a Asset Group</option>';

		var selectedCourseId = event.target.value;
		getList("/api/courses/assetgroup/getAllCourseOfferings?courseId=" + selectedCourseId).then(function(res) {
			var courseOfferings = res.course_offerings;
			for (var i = 0; i < courseOfferings.length; i++) {
				var opt = document.createElement('option');
				opt.value = courseOfferings[i]["id"];
				opt.innerHTML = courseOfferings[i]["title"];
				offeringsSelectTarget.appendChild(opt);
			}
		});
	});

	offeringsSelectTarget.addEventListener('change', function(event) {
		unitsSelectTarget.innerHTML = '<option selected disabled>Select a Course Unit</option>';
		assetGroupsSelectTarget.innerHTML = '<option selected disabled>Select a Asset Group</option>';

		var selectedOfferingId = event.target.value;
		getList("/api/courses/assetgroup/getAllCourseUnits?offeringId=" + selectedOfferingId).then(function(res) {
			var courseUnits = res.course_units;
			for (var i = 0; i < courseUnits.length; i++) {
				var opt = document.createElement('option');
				opt.value = courseUnits[i]["id"];
				opt.innerHTML = courseUnits[i]["title"];
				unitsSelectTarget.appendChild(opt);
			}
		});
	});

	unitsSelectTarget.addEventListener('change', function(event) {
		assetGroupsSelectTarget.innerHTML = '<option selected disabled>Select a Asset Group</option>';

		var selectedUnitId = event.target.value;
		getList("/api/courses/assetgroup/getAllAssetGroups?unitId=" + selectedUnitId).then(function(res) {
			var assetGroupsMap = res.asset_groups;
			var keys = Object.keys(assetGroupsMap);

			for (var i = 0; i < keys.length; i++) {
				var key = keys[i];
				var value = assetGroupsMap[key];
				var opt = document.createElement('option');
				opt.value = key;
				opt.innerHTML = value['title'];
				assetGroupsSelectTarget.appendChild(opt);
			}
		});
	});
});

function getList(url) {
	return fetch(url)
		.then(function(response) {
			if (!response.ok) {
				window.confirm("Server Error with API");
				console.error("Error Code: " + response.status + " / Error Message: " + response.statusText);
			}
			return response.json();
		})
		.catch(function(error) {
			if (error instanceof SyntaxError) {
				console.error('There was a SyntaxError', error);
			} else {
				console.error('There was an error', error);
			}
		});
}
