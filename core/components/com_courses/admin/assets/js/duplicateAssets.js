console.log("duplicate assets");

window.addEventListener('DOMContentLoaded', (domEvent) => {
    const coursesSelectTarget = document.querySelector('#coursesSelect')
    const offeringsSelectTarget = document.querySelector('#offeringsSelect')
    const unitsSelectTarget = document.querySelector('#unitsSelect')
    const assetGroupsSelectTarget = document.querySelector('#assetGroupsSelect')

    getList("/api/courses/assetgroup/getAllCourses").then((res) => {
        const courses = res.courses;
        for (x of courses) {
            var opt = document.createElement('option');
            opt.value = x["id"];
            opt.innerHTML = x["title"];
            coursesSelectTarget.appendChild(opt);
        }
    })
    
    coursesSelectTarget.onchange = (event) => {
        offeringsSelectTarget.innerHTML = '<option selected disabled>Select a Course Offering</option>';
        unitsSelectTarget.innerHTML = '<option selected disabled>Select a Course Unit</option>';
        assetGroupsSelectTarget.innerHTML = '<option selected disabled>Select a Asset Group</option>';

        const selectedCourseId = event.target.value;
        getList(`/api/courses/assetgroup/getAllCourseOfferings?courseId=${selectedCourseId}`).then((res) => {
            const courseOfferings = res.course_offerings;
            for (x of courseOfferings) {
                var opt = document.createElement('option');
                opt.value = x["id"];
                opt.innerHTML = x["title"];
                offeringsSelectTarget.appendChild(opt);
            }
        })
    }

    offeringsSelectTarget.onchange = (event) => {
        unitsSelectTarget.innerHTML = '<option selected disabled>Select a Course Unit</option>';
        assetGroupsSelectTarget.innerHTML = '<option selected disabled>Select a Asset Group</option>';

        const selectedOfferingId = event.target.value;
        getList(`/api/courses/assetgroup/getAllCourseUnits?offeringId=${selectedOfferingId}`).then((res) => {
            const courseUnits = res.course_units;
            for (x of courseUnits) {
                var opt = document.createElement('option');
                opt.value = x["id"];
                opt.innerHTML = x["title"];
                unitsSelectTarget.appendChild(opt);
            }
        })
    }

    unitsSelectTarget.onchange = (event) => {
        assetGroupsSelectTarget.innerHTML = '<option selected disabled>Select a Asset Group</option>';

        const selectedUnitId = event.target.value;
        getList(`/api/courses/assetgroup/getAllAssetGroups?unitId=${selectedUnitId}`).then((res) => {
            const assetGroupsMap = res.asset_groups;

            for (const [key, value] of Object.entries(assetGroupsMap)) {
                // console.log(`Key: ${key}, Value: ${value['alias']}`);

                var opt = document.createElement('option');
                opt.value = key;
                opt.innerHTML = value['title'];
                assetGroupsSelectTarget.appendChild(opt);    
            }
        })
    }
});

const getList = async (url) => {
    try {
        let response = await fetch(url);

        if (!response.ok) {
            window.confirm("Server Error with API");
            console.error(`Error Code: ${response.status} / Error Message: ${response.statusText}`);
        }

        return response.json();
    } catch (error) {
        if (error instanceof SyntaxError) {
            console.error('There was a SyntaxError', error);
        } else {
            console.error('There was an error', error);
        }
    }
};