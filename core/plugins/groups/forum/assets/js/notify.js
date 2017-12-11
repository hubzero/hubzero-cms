
class Notify {

	static error(message) {
		this._notification(message, 'error')
	}

	static success(message) {
		this._notification(message, 'success')
	}

	static warn(message) {
		this._notification(message, 'warn')
	}

	static _notification(message, type) {
		const color = this._getNotificationColor(type)
		const notificationModal = new NotificationModal({	text: message	})
		notificationModal.addCss({ 'background-color': color })
		const $modalElement = notificationModal.render()

		this.displayNotification($modalElement)
	}

	static _getNotificationColor(type) {
		const typeColorMappings = {
			success: '#A3CA60',
			warn: '#FDD023',
			error: '#CC0000'
		}
		const typeColor = typeColorMappings[type]

		return typeColor
	}

	static displayNotification($notification) {
		$('#content').append($notification)
		setTimeout(() => {
			$notification.hide()
		}, 5000)
	}

}

class NotificationModal {

	constructor({ css, text }) {
		this.css = css || {
			'border-radius': '3px',
			color: '#FFFFFF',
			'font-size': '1.25em',
			'font-weight': '500',
			margin: '0 -15em 0 0',
			'min-height': '5em',
			'min-width': '20em',
			'max-width': '40em',
			position: 'fixed',
			right: '50%',
			'text-align': 'center',
			top: '115px',
			width: '30em',
			'z-index': '50000'
		}
		this.html = text
	}

	render() {
		const $element = $('<div>')

		this._applyStyles($element)
		this._addHtml($element)
		this._addCloseButton($element)
		this._addEventHandlers($element)

		return $element
	}

	_addCloseButton($element) {
		const $closeButton = $('<div>')

		$closeButton.html('×')
		$closeButton.attr('id', 'close-notify')
		$closeButton.css({
			cursor: 'pointer',
			'font-size': '1.25em',
			'font-weight': 'bold',
			margin: '0 0 .2em 0',
			padding: '.25em .5em 0 0',
			'text-align': 'right'
		})
		this.closeButton = $closeButton

		$element.prepend($closeButton)
	}

	_applyStyles($element) {
		for (const key of Object.keys(this.css)) {
			$element.css(key, this.css[key])
		}
	}

	_addHtml($element) {
		$element.html(this.html)
	}

	_addEventHandlers($element) {
		const $closeButton = this.closeButton

		const hideNotification = (e) => {
			if ($(e.target).attr('id') == $closeButton.attr('id')) {
				$element.hide()
			}
		}

		$element.click(hideNotification)
	}

	addCss(newCss) {
		const combinedCss = {
			...this.css,
			...newCss
		}

		this.css = combinedCss
	}

}
