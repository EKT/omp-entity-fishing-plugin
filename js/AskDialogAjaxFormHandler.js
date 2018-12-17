/**
 * @file plugins/generic/entityFishing/js/AskDialogAjaxFormHandler.js
 *
 * Copyright (c) 2018 Hirmeos EU Project (http://hirmeos.eu/)
 * Copyright (c) 2018 National Documentation Center (http://www.ekt.gr/)
 * Copyright (c) 2018 Kostas Stamatis
 *
 * This is a deliverable of the HIRMEOS project. The project has received funding from the European Union’s
 * Horizon 2020 research and innovation programme under grant agreement No 731102.
 *
 * Distributed under the GNU GPLv3. For full terms see: https://www.gnu.org/licenses/gpl-3.0.en.html
 *
 * @class AjaxFormHandler
 * @ingroup js_controllers_form
 *
 * @brief Form handler that submits the form to the server via AJAX and
 *  either replaces the form if it is re-rendered by the server or
 *  triggers the "formSubmitted" event after the server confirmed
 *  form submission.
 */
(function($) {


	/**
	 * @constructor
	 *
	 * @extends $.pkp.controllers.form.FormHandler
	 *
	 * @param {jQueryObject} $form the wrapped HTML form element.
	 * @param {Object} options options to configure the AJAX form handler.
	 */
	$.pkp.controllers.form.AskDialogAjaxFormHandler = function($form, options) {
		this.parent($form, options);
	};
	$.pkp.classes.Helper.inherits(
			$.pkp.controllers.form.AskDialogAjaxFormHandler,
			$.pkp.controllers.form.AjaxFormHandler);


	/**
	 * Overridden default from FormHandler -- disable form controls
	 * on AJAX forms by default.
	 * @protected
	 * @type {boolean}
	 */
	$.pkp.controllers.form.AskDialogAjaxFormHandler.prototype.
			disableControlsOnSubmit = true;



	/**
	 * Internal callback called after form validation to handle the
	 * response to a form submission.
	 *
	 * You can override this handler if you want to do custom handling
	 * of a form response.
	 *
	 * @param {HTMLElement} formElement The wrapped HTML form.
	 * @param {Object} jsonData The data returned from the server.
	 * @return {boolean} The response status.
	 */
	$.pkp.controllers.form.AskDialogAjaxFormHandler.prototype.handleResponse =
			function(formElement, jsonData) {
				location.reload();
				return /** @type {boolean} */ (
					this.parent('handleResponse', formElement, jsonData));
	};


/** @param {jQuery} $ jQuery closure. */
}(jQuery));
