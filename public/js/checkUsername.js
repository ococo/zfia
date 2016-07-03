(function() {

    var handleFailure = function(o) {
        if(o.responseText !== undefined) { 
            alert("Checking Username Failed");
        }
    }

    var handleSuccess = function(o) {
        if(o.responseText !== undefined) {
            var json = eval("(" + o.responseText + ")") ;
            var scr = document.getElementById('username'); 
            var ulId = 'usernameError';

            if(!document.getElementById(ulId) && json.username) {
                var errorMessage = json.username.notUnique;
                var ul = document.createElement('ul'); 
                ul.className = 'errors'; 
                ul.id = ulId; 
                var newcontent = document.createElement('li'); 
                ul.appendChild(newcontent); 
                newcontent.appendChild(document.createTextNode(errorMessage)); 
                scr.parentNode.appendChild(ul, scr); 
            } else if(document.getElementById(ulId) && !json.username) {
                var ul = document.getElementById(ulId);
                scr.parentNode.removeChild(ul, scr); 
            }
        }
    }

    var callback = {
        failure:handleFailure,
        success:handleSuccess,
    };

        
    //A function that pops up a "Hello World" alert:
    var checkUsername = function(e) {
        var username = document.getElementById('username').value;
        var sUrl = "/register/";
        var postData = "username=" + username;
        var request = YAHOO.util.Connect.asyncRequest('POST', sUrl, callback, postData);
    }

    //subscribe the helloWorld function as an event
    //handler for the click event on the container
    //div:
    YAHOO.util.Event.addListener("username", "blur", checkUsername);

})();
