

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


// Handle params replacement for facets
function updateQueryStringParameter(key, value) {

  uri = window.location.href;

  // Recherche du séparateur
  var separator = uri.indexOf('?') !== -1 ? "&" : "?";

  if (uri.endsWith('/workspace')) {
  	var noSeparator = true;
    separator = '&';
  }

  // Ajout du paramètre 'refine' à chaque fois
  var refine_key = 'refine';
  var refine_value = 'yes';
  var re = new RegExp("([?&])" + refine_key + "=.*?(&|$)", "i");
  if (uri.match(re)) {
    uri = uri.replace(re, '$1' + refine_key + "=" + refine_value + '$2');
  }
  else {
  	if (noSeparator)
    	uri = uri + '?' + refine_key + "=" + refine_value;
    else
    	uri = uri + separator + refine_key + "=" + refine_value;
  }

  // Réinitialisation du paramètre "page" à 1
  var re = new RegExp("([?&])page=.*?(&|$)");
  if (uri.match(re)) {
    uri = uri.replace(re, '$1' + "page=1" + '$2');
  }
  else {
    uri = uri + separator + "page=1";
  }
  // Traitement de l'URL

  // Recherche de la clé
  var re = new RegExp("([?&])" + key + "=.*?(&|$)", "i");
  if (uri.match(re)) {
    var pattern = uri.match(re)[0];

    // Suppression du "&" à la fin du motif
    if (pattern.charAt(pattern.length - 1) == '&')
        pattern = pattern.substring(0,pattern.length - 1);
    console.log("Pattern : " + pattern);

    // Suppression du prefixe (par exemple &tags= )
    var pos = pattern.indexOf('=') + 1;
    pattern = pattern.slice(pos);
    console.log("Pattern : " + pattern);

    var delimiter = '%2C';
    var tab = pattern.split(delimiter);
    console.log("Tab : " + tab);
    var rank = tab.indexOf(value);
    if (rank >= 0) {
        tab.splice(rank, 1);
    } else {
        tab.push(value);
    }
    result = tab.join(delimiter);
    console.log("Result : " + result);
    if (result.charAt(0) == delimiter)
        result = result.substr(1);

    return uri.replace(re, '$1' + key + "=" + result + '$2');
      }

  else {
    return uri + separator + key + "=" + value;
  }
}