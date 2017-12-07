

// Display error on modal windows
function displayErrorsOnModals(form, response) {

	removeErrorsOnModals();
	if (response)
    	form.find('input[type=submit]').before('<div class="ajax-errors">'+response.error+'</div>');
  	else
    	form.find('input[type=submit]').before('<div class="ajax-errors">Ajax error</div>');
};


// Remove errors on modal windows
function removeErrorsOnModals() {

	jQuery('label.error').remove();
    jQuery('div.ajax-errors').remove();
};

