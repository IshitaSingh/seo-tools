

	/**
	 * Initialize namespace
	 */
	var PHPRumDebug = {};

	/**
	* Function : print_r()
	* Arguments: The data - array,hash(associative array),object
	*	The level - OPTIONAL
	* Returns  : The textual representation of the array.
	* This function was inspired by the print_r function of PHP.
	* This will accept some data as the argument and return a
	* text that will be a more readable version of the
	* array/hash/object that is given.
	*/
	function print_r(arr,level) {
		var dumped_text = "";
		if(!level) level = 0;

		//The padding given at the beginning of the line.
		var level_padding = "";
		for(var j=0;j<level+1;j++) level_padding += "	";
		
		if(typeof(arr) == 'object') { //Array/Hashes/Objects 
			for(var item in arr) {
				var value = arr[item];
				
				if(typeof(value) == 'object') { //If it is an array,
					dumped_text += level_padding + "'" + item + "' ...\n";
					dumped_text += print_r(value,level+1);
				} else {
					dumped_text += level_padding + "'" + item + "' => \"" + value + "\"\n";
				}
			}
		} else { //Stings/Chars/Numbers etc.
			dumped_text = "===>"+arr+"<===("+typeof(arr)+")";
		}
		return dumped_text;
	}

	/**
	* Function : dmp()
	*/
	function dmp(arr,exit) {
		if(!exit) exit = 0;
		alert( print_r( arr ));
	}

	/**
	 * setCookie
	 */
	PHPRumDebug.setCookie = function(c_name,value,exdays,path) {
		var exdate=new Date();
		exdate.setDate(exdate.getDate() + exdays);
		var c_value=escape(value) + ((exdays==null) ? "" : "; expires="+exdate.toUTCString()) + "; path="+path;
		document.cookie=c_name + "=" + c_value;
	}

	/**
	 * getCookie
	 */
	PHPRumDebug.getCookie = function(c_name) {
		var i,x,y,ARRcookies=document.cookie.split(";");
		for (i=0;i<ARRcookies.length;i++) {
			x=ARRcookies[i].substr(0,ARRcookies[i].indexOf("="));
			y=ARRcookies[i].substr(ARRcookies[i].indexOf("=")+1);
			x=x.replace(/^\s+|\s+$/g,"");
			if (x==c_name) {
				return unescape(y);
			}
		}
		return false;
	}

	/**
	 * debugCheck
	 */
	PHPRumDebug.debugCheck = function() {
		var value = PHPRumDebug.getCookie("Rum.debug")
		if(value=="opened") {
			PHPRumDebug.debugOpen();
		}
		else if(value=="closed") {
			PHPRumDebug.debugClose();
		}
		else {
			PHPRumDebug.debugClose();
		}
	}

	/**
	 * debugOpen
	 */
	PHPRumDebug.debugOpen = function() {
		if(document.getElementById('debug_panel').style.display=='block')
		{
			document.getElementById('debug_panel').style.display='none';
			PHPRumDebug.setCookie("Rum.debug", "closed", 30, "/");
			document.getElementById('debug_toolbar').firstChild.removeChild(document.getElementById('debug_toolbar').firstChild.firstChild);
			document.getElementById('debug_toolbar').firstChild.insertBefore(document.createElement('span'), document.getElementById('debug_toolbar').firstChild.firstChild);
			document.getElementById('debug_toolbar').firstChild.firstChild.appendChild(document.createTextNode('Open debug panel'));
		}
		else
		{
			document.getElementById('debug_panel').style.display='block';
			PHPRumDebug.setCookie("Rum.debug", "opened", 30, "/");
			document.getElementById('debug_toolbar').firstChild.removeChild(document.getElementById('debug_toolbar').firstChild.firstChild);
			document.getElementById('debug_toolbar').firstChild.insertBefore(document.createElement('span'), document.getElementById('debug_toolbar').firstChild.firstChild);
			document.getElementById('debug_toolbar').firstChild.firstChild.appendChild(document.createTextNode('Close debug panel'));
		}
	}

	/**
	 * debugClose
	 */
	PHPRumDebug.debugClose = function() {
		document.getElementById('debug_panel').style.display='none';
		PHPRumDebug.setCookie("Rum.debug", "closed", 30, "/");
	}

	/**
	 * launch in iframe
	 */
	PHPRumDebug.launchFrame = function (src) {
		var input = document.createElement("input");
		input.setAttribute("type", "button");
		input.setAttribute("value", "close");
		input.setAttribute("onclick", "PHPRumDebug.closeFrame();");

		var iframe = document.createElement("iframe");
		iframe.setAttribute("src", src);

		document.getElementById('debug_toolbar').appendChild(iframe);
		document.getElementById('debug_toolbar').appendChild(input);
	}

	/**
	 * launch in iframe
	 */
	PHPRumDebug.closeFrame = function () {
		var iframeTags = document.getElementById('debug_toolbar').getElementsByTagName( 'iframe' );
		var len = iframeTags.length;

		for( var i = 0; i < len; i++ ) {
			document.getElementById('debug_toolbar').removeChild(iframeTags[i]);
		}

		var inputTags = document.getElementById('debug_toolbar').getElementsByTagName( 'input' );
		var len = inputTags.length;

		for( var i = 0; i < len; i++ ) {
			document.getElementById('debug_toolbar').removeChild(inputTags[i]);
		}
	}

