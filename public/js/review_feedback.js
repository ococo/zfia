// ReviewFeedback class used as callback to
// YUI connection manager
function ReviewFeedback(response, reviewId, baseUrl) {
	this.id = reviewId;
	this.baseUrl = baseUrl;
	
	// turn on spinner and empty the informationmessage
	this.startSpinner();
	this.message("","");

	// ensure that we don't have to encode parameters
	var reviewId = parseInt(reviewId);
	var response = parseInt(response);

    var sUrl = baseUrl + "/review/feedback/format/json/id/"
        + reviewId + "/helpful/" + response;

	// perform the request.
    YAHOO.util.Connect.initHeader('X_REQUESTED_WITH', 'XMLHttpRequest');
    YAHOO.util.Connect.asyncRequest('GET', sUrl, this);

} 

ReviewFeedback.prototype.success = function(o) {
    if(o.responseText !== undefined){
		var json = eval("(" + o.responseText + ")") ;
		if(json.result && json.id == this.id) {		
			// update the information text to include the new counts
            document.getElementById('counts-'+json.id).innerHTML 
                = json.helpful_yes + ' of ' + json.helpful_total;
			
            // say thank you and stop the spinner
            this.message("success", 'Thank you for your feedback.');
            this.stopSpinner();
			
            // remove yes/no buttons as they aren't needed after feedback
            document.getElementById('yesno-'+json.id).innerHTML = "";			
        } else {
            this.failure(o);
        }
    }
}

ReviewFeedback.prototype.failure = function(o) {
	// inform the user of failure and stop the spinner
	var text = "Sorry, our feedback system hasn't worked. Please try later.";
	this.message("failed", text);
	this.stopSpinner();
}

ReviewFeedback.prototype.startSpinner = function() {
    var spinner = document.getElementById('spinner-'+this.id);
    var url = this.baseUrl+'/img/spinner.gif';
    spinner.innerHTML = '<img src="'+ url +' " border="0" />';
}

ReviewFeedback.prototype.stopSpinner = function() {
	document.getElementById('spinner-'+this.id).innerHTML = ""; 
}

ReviewFeedback.prototype.message = function(class, text) { 
	document.getElementById('message-'+this.id).className = class; 
	document.getElementById('message-'+this.id).innerHTML = text; 
}
