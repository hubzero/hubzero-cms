$(document).ready(() => {
	Hubzero.initApi(() => {
		const $emailSettingsForm = $('#email-settings')
		const $preexistingSubscriptionIdsElement = $('input[id=preexisting-subscriptions]')

		$emailSettingsForm.submit((e) => {
			e.preventDefault()

			const subscriptionsDelta = _determineSubscriptionDelta()
			const changeMade = _subscriptionsWereChanged(subscriptionsDelta)

			if (changeMade) {
				_updateUserSubscriptions(subscriptionsDelta)
			}
		})

		const _determineSubscriptionDelta = () => {
			const preexistingSubscriptionIds = _getPreexistingSubscriptionIds()
			const updatedSubscriptions = _getUpdatedSubscriptions()
			const delta = {}

			delta.delete = preexistingSubscriptionIds.filter((categoryId) => {
				return !updatedSubscriptions.includes(categoryId)
			})
			delta.create = updatedSubscriptions.filter((categoryId) => {
				return !preexistingSubscriptionIds.includes(categoryId)
			})

			return delta
		}

		const _getPreexistingSubscriptionIds = () => {
			const inputValue = $preexistingSubscriptionIdsElement.val()
			const preexistingSubscriptionsIds = (inputValue === "") ? [] : inputValue.split(',')

			return preexistingSubscriptionsIds
		}

		const  _getUpdatedSubscriptions = () => {
			const formData = $emailSettingsForm.serializeArray()
			const updatedSubscriptions = formData.filter((input) => {
				return input.value === 'on'
			})
			const updatedSubscriptionsIds = updatedSubscriptions.map((input) => {
				return input.name
			})

			return updatedSubscriptionsIds
		}

		const _subscriptionsWereChanged = (subscriptionsDelta) => {
			return subscriptionsDelta.delete.length > 0 || subscriptionsDelta.create.length > 0
		}

		const _updateUserSubscriptions = (subscriptionsDelta) => {
			const userId = $emailSettingsForm.find('input[id=user-id]').val()
			const deleteResponse = _deleteSubscriptions(subscriptionsDelta.delete, userId)
			const createResponse = _createSubscriptions(subscriptionsDelta.create, userId)

			Promise.all([deleteResponse, createResponse])
				.then((responses) => {
					const enrichedResponses = _getEnrichedResponses({
						delete: responses[0], create: responses[1]
					})
					let [deleteResponse, createResponse] = enrichedResponses

					_notifyUser(deleteResponse, createResponse)
					_updatePreexistingIdsElement(deleteResponse, createResponse)
				})
		}

		const _deleteSubscriptions = (subscriptionsToDelete, userId) => {
			if (subscriptionsToDelete.length > 0) {
				const promise = $.ajax({
					url: '/api/v2.0/forum/userscategories/destroy',
					method: 'DELETE',
					data: {
						userId,
						categoriesIds: subscriptionsToDelete
					}
				})

				return promise
			}
		}

		const _createSubscriptions = (subscriptionsToCreate, userId) => {
			if (subscriptionsToCreate.length > 0) {
				const promise = $.ajax({
					url: '/api/v2.0/forum/userscategories/create',
					method: 'POST',
					data: {
						userId,
						categoriesIds: subscriptionsToCreate
					}
				})

				return promise
			}
		}

		const _getEnrichedResponses = (responses) => {
			const enrichedResponses = []

			Object.keys(responses).forEach((action) =>	{
				const response = responses[action]
				const enrichedResponse = _enrichResponse(response, action)

				enrichedResponses.push(enrichedResponse)
			})

			return enrichedResponses
		}

		const _enrichResponse = (response, action) => {
			let enrichedResponse

			if (response) {
				enrichedResponse = JSON.parse(response)
				enrichedResponse.action = action
			} else {
				enrichedResponse = _getNullResponse(action)
			}

			return enrichedResponse
		}

		const _getNullResponse = (action) => {
			return {
				status: 'success',
				records: [],
				null: true
			}
		}

		const _updatePreexistingIdsElement = (deleteResponse, createResponse) => {
			const updatedSubscriptionIds =_getUpdatedSubscriptionIds(deleteResponse, createResponse)

			updatedSubscriptionIds.sort()
			$preexistingSubscriptionIdsElement.val(updatedSubscriptionIds.join(','))
		}

		const _getUpdatedSubscriptionIds = (deleteResponse, createResponse) => {
			const preexistingSubscriptionIds = _getPreexistingSubscriptionIds()

			if (_requestSucceeded(deleteResponse)) {
				_removeIds(preexistingSubscriptionIds, deleteResponse.records)
			}

			if (_requestSucceeded(createResponse)) {
				_addIds(preexistingSubscriptionIds, createResponse.records)
			}

			return preexistingSubscriptionIds
		}

		const _requestSucceeded = (response) => {
			return !response.null && response.status === 'success'
		}

		const _removeIds = (preexistingSubscriptionIds, records) => {
			records.forEach((record) => {
				let index = preexistingSubscriptionIds.indexOf(record.category_id)

				preexistingSubscriptionIds.splice(index, 1)
			})
		}

		const _addIds = (preexistingSubscriptionIds, records) => {
			records.forEach((record) => {
				preexistingSubscriptionIds.push(record.category_id)
			})
		}

		const _notifyUser = (deleteResponse, createResponse) => {
			const notificationMessage = _generateNotificationMessage(deleteResponse, createResponse)
			const notificationType = _getNotificationType(deleteResponse, createResponse)

			Notify[notificationType](notificationMessage)
		}

		const _generateNotificationMessage = (deleteResponse, createResponse) => {
			let notifications = []

			if (!deleteResponse.null) {
				notifications.push(_generateDeleteNotification(deleteResponse))
			}
			if (!createResponse.null) {
				notifications.push(_generateCreateNotification(createResponse))
			}

			return notifications.join("<br>")
		}

		const _generateDeleteNotification = (deleteResponse) => {
			let notificationMessage

			if (_requestFailed(deleteResponse)) {
				notificationMessage =	_generateDeleteErrorNotification(deleteResponse.errors)
			} else {
				notificationMessage = 'The specified subscriptions were deleted.'
			}

			return notificationMessage
		}

		const _generateDeleteErrorNotification = (errors) => {
			const baseErrorMessage = 'There were errors when attempting to delete the specified subscriptions'
			const notificationMessage =	_generateErrorNotification(baseErrorMessage, errors)

			return notificationMessage
		}

		const _generateCreateNotification = (createResponse) => {
			let notificationMessage

			if (_requestFailed(createResponse)) {
				notificationMessage =	_generateCreateErrorNotification(createResponse.errors)
			} else {
				notificationMessage = 'Subscriptions created.'
			}

			return notificationMessage
		}

		const _generateCreateErrorNotification = (errors) => {
			const baseErrorMessage = 'There were errors when attempting to create the specified subscriptions'
			const notificationMessage = _generateErrorNotification(baseErrorMessage, errors)

			return notificationMessage
		}

		const _generateErrorNotification = (baseMessage, errors) => {
			let errorNotification
			const joinedErrors = errors.join(', ')

			if (joinedErrors != '') {
				errorNotification = `${baseMessage}: ${joinedErrors}`
			}

			return errorNotification
		}

		const _getNotificationType = (deleteResponse, createResponse) => {
			let notificationType
			const deleteFailed =	_requestFailed(deleteResponse)
			const createFailed =	_requestFailed(createResponse)

			if ((deleteResponse.null || deleteFailed ) && (createResponse.null || createFailed)) {
				notificationType = 'error'
			} else if (deleteFailed || createFailed) {
				notificationType = 'warn'
			}  else {
				notificationType = 'success'
			}

			return notificationType
		}

		const _requestFailed = (response) => {
			return !response.null && response.status === 'error'
		}

	})
})

