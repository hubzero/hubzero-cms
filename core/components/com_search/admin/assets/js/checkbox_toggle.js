
var HUB = HUB || {}

HUB.adminList = HUB.adminList || {}

const adminList = HUB.adminList

adminList.CLASS = 'adminlist'
adminList.MASTER_CHECKBOX_CLASS = 'checkbox-toggle'
adminList.RECORD_CHECKBOX_CLASS = 'checkbox-record'

adminList.init = () => {
		adminList._domElement = $(`table.${adminList.CLASS}`)
		adminList._masterCheckbox = $(`input[class="${adminList.MASTER_CHECKBOX_CLASS}"]`)
		adminList._recordCheckboxes = $(`input[class="${adminList.RECORD_CHECKBOX_CLASS}"]`)
}

adminList.registerMasterCheckboxListener = () => {
	adminList._masterCheckbox.click(adminList.masterCheckboxHandler)
}

adminList.masterCheckboxHandler = () => {
	const $masterCheckbox = adminList._masterCheckbox
	const masterIsChecked = $masterCheckbox.prop('checked')

	adminList._recordCheckboxes.prop('checked', masterIsChecked)
	adminList.setChecked(masterIsChecked, $masterCheckbox)
}

adminList.registerRecordCheckboxListener = () => {
	adminList._recordCheckboxes.click(adminList.recordCheckboxHandler)
}

adminList.recordCheckboxHandler = (e) => {
	const $checkbox = $(e.target)
	const checked = $checkbox.prop('checked')

	adminList.setChecked(checked, $checkbox)
}

adminList.setChecked = (checked, $checkbox) => {
		isChecked(checked, $checkbox)
}

$(document).ready(() => {
	adminList.init()
	adminList.registerMasterCheckboxListener()
	adminList.registerRecordCheckboxListener()
})
