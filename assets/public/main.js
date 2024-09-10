/**
 * This file contains the scripts for cardview frontend.
 *
 * @author Suraj Singh
 *
 * @package bit_background_process
 */

//import './../scss/common.scss';

(function ($) {
	/**
	 * Represents a Cardview object.
	 * This class represents a Cardview and provides methods and properties to interact with it.
	 *
	 * @class
	 */
	class Cardview {
		/**
		 * Create a new Cardview.
		 *
		 * @constructor
		 * @param {Object} options - The options for the Cardview.
		 * @param {string} options.cardviewId - The ID of the Cardview.
		 */
		constructor(options) {
			this.options = options;
		}

		/**
		 * This function registers the events.
		 */
		init() {

		}
	}

	$(document).ready(function () {
		if (cardviewBuilder && cardviewBuilder.options) {
			cardviewBuilder.options.forEach(option => {
				new Cardview(option).init();
			});
		}
	});
}(jQuery));
