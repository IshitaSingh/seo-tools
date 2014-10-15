

	/**
	 * Initialize namespace
	 */
	var Rum = new function() {

		/**
		 * Specifies the asyncronous request parameter
		 */
		var asyncParam = '';

		/**
		 * Specifies the validation timeout
		 */
		var validationTimeout = 10;

		/**
		 * Specifies whether a asyncronous validation attempt is ready
		 */
		var validationReady = true;

		/**
		 * Specifies the default ajax start handler
		 * @param params parameters
		 */
		this.defaultFlashHandler = function(message, type){alert(message);};

		/**
		 * Specifies the default ajax start handler
		 * @param params parameters
		 */
		this.defaultAjaxStartHandler = function(params){};

		/**
		 * Specifies the default ajax completion handler
		 * @param params parameters
		 */
		this.defaultAjaxCompletionHandler = function(params){};

		/**
		 * Specifies the default ajax timeout handler
		 */
		this.defaultTimeoutHandler = function(){alert("The Web server request has timed out!");};

		/**
		 * Specifies the default ajax error handler
		 * @param status error status code
		 */
		this.defaultErrorHandler = function(status){alert("The Web server encountered an unexpected condition that prevented it from fulfilling the request!");};

		/**
		 * Specifies the default timeout
		 */
		this.defaultTimeout = 30000;


		/**
		 * Function to get a XMLDom object
		 * @param param param
		 * @param timeout timeout
		 */
		this.init = function(param, timeout) {
			asyncParam = param;
			validationTimeout = timeout;
		};

		/**
		 * Function to get a XMLDom object
		 * @param id element id
		 */
		this.id = function(id) {
			return document.getElementById(id);
		};


		/**
		 * Function to flash a new message for n milliseconds
		 * @param message message
		 * @param type message type
		 * @param delay delay in seconds
		 */
		this.flash = function(message, type) {
			this.defaultFlashHandler(message, type);
		};


		/**
		 * this.to forward
		 * @param url url
		 */
		this.forward = function(url) {
			location.href=url;
		};


		/**
		 * this.to send a xmlhttp request.
		 * @param element html element
		 */
		this.getParams = function( element ) {
			var params = '';
			var inputs = element.getElementsByTagName('input');
			var selects = element.getElementsByTagName('select');
			var textareas = element.getElementsByTagName('textarea');
			for (x=0;x<inputs.length;x++) {
				if(inputs[x].getAttribute('type')!=='button' && inputs[x].getAttribute('type')!=='submit' && inputs[x].getAttribute('type')!=='image') {
					if(inputs[x].getAttribute('type')==='checkbox') {
						if(inputs[x].checked) {
							if(params) params = params + '&';
							params = params + inputs[x].getAttribute('name') + '=' + inputs[x].value;
						}
					}
					else {
						if(params) params = params + '&';
						params = params + inputs[x].getAttribute('name') + '=' + inputs[x].value;
					}
				}
			}
			for (x=0;x<selects.length;x++) {
				if(params) params = params + '&';
				params = params + selects[x].getAttribute('name') + '=' + selects[x].value;
			}
			for (x=0;x<textareas.length;x++) {
				if(params) params = params + '&';
				params = params + textareas[x].getAttribute('name') + '=' + textareas[x].value;
			}
			return params;
		};


		/**
		 * this.to send a xmlhttp request.
		 * @param url url
		 * @param params parameters
		 * @param method method
		 */
		this.sendSync = function( url, params, method ) {

			if (!method){
				method = 'GET';
			}
			if (!params){
				params = '';
			}

			if (method.toUpperCase() === 'GET' && params){
				if( url.indexOf( '?' ) > -1 ) {
					url = url + '&' + params;
				}
				else {
					url = url + '?' + params;
				}
				params = '';

				location.href = url;
			}
			else
			{
				params = params.split('&');
				var temp=document.createElement("form");
				temp.action=url;
				temp.method="POST";
				temp.style.display="none";
				for(var x = 0; x < params.length; x++)
				{
					param = params[x].split('=');
					var input=document.createElement("input");
					input.setAttribute('name', param[0]);
					input.setAttribute('value', param[1]);
					temp.appendChild(input);
				}

				document.body.appendChild(temp);
				temp.submit();
			}
		};


		/**
		 * this.to submit html forms
		 * @param formElement form element
		 */
		this.submit = function(formElement, startHandler, completionHandler) {

			var eventArgs = {};
			if(!startHandler) startHandler = this.defaultAjaxStartHandler;
			if(!completionHandler) completionHandler = this.defaultAjaxCompletionHandler;

			createFrame(formElement, completionHandler, eventArgs);
			startHandler(eventArgs);
			return true;
		};


		/**
		 * this.to send a xmlhttp request.
		 * @param url url
		 * @param params parameters
		 * @param method method
		 * @param callback callback handler
		 * @param timeout timeout
		 */
		this.sendAsync = function(url, params, method, callback, timeout) {

			var timeoutHandler;
			if(!timeout) timeout = this.defaultTimeout;
			if(!timeoutHandler) timeoutHandler = this.defaultTimeoutHandler;

			var http_request = this.createXMLHttpRequest();
			sendHTTPRequest(http_request, url, params, method, callback, timeout, timeoutHandler);
		};


		/**
		 * this.to send a xmlhttp request.
		 * @param url url
		 * @param params parameters
		 * @param method method
		 * @param startHandler start handler
		 * @param completionHandler completion handler
		 * @param timeout timeout
		 */
		this.evalAsync = function(url, params, method, startHandler, completionHandler, timeout) {

			var eventArgs={};
			var errorHandler;
			var timeoutHandler;
			if(!startHandler) startHandler = this.defaultAjaxStartHandler;
			if(!completionHandler) completionHandler = this.defaultAjaxCompletionHandler;
			if(!timeout) timeout = this.defaultTimeout;
			if(!errorHandler) errorHandler = this.defaultErrorHandler;
			if(!timeoutHandler) timeoutHandler = this.defaultTimeoutHandler;

			params.split('&').forEach(function(e) {
				a=e.split('='); eventArgs[a[0]] = a[1];
			});

			var http_request = this.createXMLHttpRequest();
			var callback = function() {evalHttpResponse(http_request, completionHandler, errorHandler, eventArgs);};
			sendHTTPRequest(http_request, url, params, method, callback, timeout, timeoutHandler);
			startHandler(eventArgs);
		};


		/**
		 * this.to reset validation timer
		 * @param formElement form element
		 * @param iframeID iframe id
		 */
		this.documentLoaded = function(formElement, iframeID) {

			//changed frameElement to allow IE10 to work was var frameElement = document.getElementById(iframeID);
			var frameElement = (!document.getElementById(iframeID))?"":document.getElementById(iframeID);
			var documentElement = null;

			if (frameElement.contentDocument) {
				documentElement = frameElement.contentDocument;
			} else if (frameElement.contentWindow) {
				documentElement = frameElement.contentWindow.document;
			} else {
				return;
				//removed below to make this work in IE10
				//documentElement = window.frames[iframeID].document;
			}

			if (documentElement.location.href === "about:blank") {
				return;
			}
			//if (typeof(frameElement.completeCallback) == 'this.function =') {
				frameElement.completeCallback(formElement, documentElement.body.textContent);
			//}
		};


		/**
		 * Funciton to assert a Validation Message
		 * @param id element id
		 * @param msg message
		 * /
		this.assert = function(id, msg) {
			if(this.id(id)) {
				if(this.id(id).className.indexOf(" invalid") === -1) {
					this.id(id).className = this.id(id).className + " invalid";
				}
				setText(this.id(id+"__err"), msg);
			}
			this.reset();
		};
		*/


		/**
		 * Funciton to clear Validation Message
		 * @param id element id
		 * /
		this.clear = function( id ) {
			if(this.id(id)) {
				if(this.id(id+"__err")) {
					this.id(id+"__err").style.display = "none";
				}
				this.id(id).className = this.id(id).className.replace(" invalid", "");
			}
			this.reset();
		};
		*/


		/**
		 * this.to reset validation timer
		 */
		this.reset  = function() {
			validationReady = false;
			window.setTimeout('setValidationReady()', validationTimeout);
		};


		/**
		 * this.to specify whether an asyncronous Validation attempt is ready
		 * @param id element id
		 */
		this.isReady = function( id ) {
			if(hasText(this.id(id))) {
				return validationReady;
			}
			return false;
		};


		/**
		 * Function to get a xmlhttp object.
		 * @ignore
		 */
		this.createXMLHttpRequest = function() {
			var http_request;
			if (window.XMLHttpRequest) { // Mozilla, Safari,...
				http_request = new XMLHttpRequest();

				if (http_request.overrideMimeType) {
					// set type accordingly to anticipated content type
					// http_request.overrideMimeType('text/xml');
					http_request.overrideMimeType('text/html');
				}
			} else if (window.ActiveXObject) { // IE
				try {
					http_request = new ActiveXObject("Msxml2.XMLHTTP");
				} catch (e) {
					try {
						http_request = new ActiveXObject("Microsoft.XMLHTTP");
					} catch (e) {}
				}
			}

			if (!http_request) {
				throw "Cannot create XMLHTTP instance";
				return false;
			}

			return http_request;
		};


		/**
		 * Function to convert multi select items to string
		 * @param element html element
		 */
		this.convertValuesFromListBox = function(element) {
			var converted_string = "";
			for (var i = 0; i < element.options.length; i++) {
				if(element.options[i].selected){
					if(converted_string.length>0) {
						converted_string += "&";
					}
					converted_string += element.getAttribute("name") + "[]=" + element.options[i].value;
				}
			}
			return converted_string;
		};


		/**
		 * this.to set the Validation Ready flag
		 */
		setValidationReady = function() {
			validationReady = true;
		};


		/**
		 * send a xmlhttp request.
		 * @param http_request http request object
		 * @param url url
		 * @param params parameters
		 * @param method method
		 * @param callback callback handler
		 * @param timeout timeout
		 * @param timeoutHandler timeout handler
		 */
		sendHTTPRequest = function(http_request, url, params, method, callback, timeout, timeoutHandler) {

			if (!method){
				method = 'GET';
			}

			if(params) {
				params += '&'+asyncParam+'=1';
			}
			else {
				params = '?'+asyncParam+'=1';
			}

			if (method.toUpperCase() === 'GET' && params){
				if( url.indexOf( '?' ) > -1 ) {
					url = url + '&' + params;
				}
				else {
					url = url + '?' + params;
				}
				params = '';
			}

			if (callback !== null){
				http_request.onreadystatechange = callback;
			}
			http_request.open(method, url, true);
			http_request.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
			//http_request.setRequestHeader("Content-length", params.length);
			//http_request.setRequestHeader("Connection", "close");
			if(timeout) {
				http_request.timeout = timeout;
				http_request.ontimeout = timeoutHandler;
			}
			http_request.send( params );
		};


		/**
		 * parse HTTP response
		 * @param http_request http request object
		 * @param completionHandler completion handler
		 * @param errorHandler event handler
		 * @param eventArgs event args
		 */
		evalHttpResponse = function( http_request, completionHandler, errorHandler, eventArgs  ) {
			eval(getHttpResponse(http_request, completionHandler, errorHandler, eventArgs ));
		};


		/**
		 * this.to receive HTTP response
		 * @param http_request http request object
		 * @param completionHandler completion handler
		 * @param errorHandler event handler
		 * @param eventArgs event args
		 */
		getHttpResponse = function( http_request, completionHandler, errorHandler, eventArgs ) {
			// if xmlhttp shows "loaded"
			if (http_request) {
				// if xmlhttp shows "loaded"
				if (http_request.readyState===4) {
					// if status "OK"
					if (http_request.status===200) {
						// get response
						response = http_request.responseText;

						completionHandler(eventArgs);
						return response;
					}
					else {
						errorHandler(http_request.status);
						console.log("Error Status Code:" + http_request.status);
					}
				}
			}
		};


		/**
		 * this.to set the validation ready flag
		 * @param formElement form element
		 * @param response response
		 */
		evalFormResponse = function(formElement, response) {
			// Kludge: this is kludge, no way to set custom completion handler for this event!
			var eventArgs = {};
			var completionHandler = Rum.defaultAjaxCompletionHandler;

			eval(response);
			completionHandler(eventArgs);
			formElement.removeChild(Rum.id(formElement.getAttribute('id')+'__async'));
			formElement.setAttribute('target', '');
		};


		/**
		 * this.to create frame element
		 * @param formElement form element
		 * @param callback callback
		 */
		createFrame = function(formElement, completionHandler, eventArgs) {

			var callback = this.evalFormResponse;
			var frameName = 'f' + Math.floor(Math.random() * 99999);
			var divElement = document.createElement('DIV');
			var iFrameElement = document.getElementById(formElement.getAttribute('id') + '__async_postback');

			if(iFrameElement) {
				iFrameElement.parentNode.removeChild(iFrameElement);
			}

			divElement.id = formElement.getAttribute('id') + '__async_postback';
			divElement.innerHTML = '<iframe style="display:none" src="about:blank" id="'+frameName+'" name="'+frameName+'" onload="Rum.documentLoaded(Rum.id(\''+formElement.getAttribute('id')+'\'), \''+frameName+'\'); return true;"></iframe>';

			document.body.appendChild(divElement);

			var frameElement = document.getElementById(frameName);
			//if (callback && typeof(callback) == 'this.function =') {
				frameElement.completeCallback = callback;
			//}

			var input = document.createElement("input");
			input.setAttribute("type", "hidden");
			input.setAttribute("name", asyncParam);
			input.setAttribute("value", "1");
			input.setAttribute("id", formElement.getAttribute('id') + "__async");
			formElement.appendChild(input);

			formElement.setAttribute('target', frameName);
		};


		/**
		 * this.to set text of an element
		 * @param element html element
		 * @param text text
		 * @param status status
		 */
		setText = function( element, text, status ) {

			if ( element ) {
				if ( element.hasChildNodes() ) {
					while ( element.childNodes.length >= 1 ) {
						element.removeChild( element.firstChild );
					}
				}
				var span = document.createElement('span');

				if(text.length>0) {
					span.appendChild(document.createTextNode(text));
					element.style.display = 'block';
				}
				else {
					span.appendChild(document.createTextNode(''));
					element.style.display = 'none';
				}

				element.appendChild(span);
			}
		};


		/**
		 * this.to return if element contains text
		 * @param element html element
		 */
		hasText = function( element ) {
			if ( element ) {
				if ( element.hasChildNodes() ) {
					if ( element.childNodes.length >= 1 ) {
						if(element.childNodes[0].textContent.length>0) {
							return true;
						}
					}
				}
			}
			return null;
		};


		/**
		 * add listener to element
		 * @param element html element
		 * @param eventName event name
		 * @param handler event hander
		 */
		addListener = function(element, eventName, handler) {
			if (element.addEventListener) {
				element.addEventListener(eventName, handler, false);
			}
			else if (element.attachEvent) {
				element.attachEvent('on' + eventName, handler);
			}
			else {
				element['on' + eventName] = handler;
			}
		};


		/**
		 * set opacity of element
		 * @param element html element
		 * @param level level
		 */
		setOpacity = function(element, level) {
			if(level===0) {
				element.parentNode.removeChild(element);
			}
			else {
				element.style.opacity = level;
				element.style.MozOpacity = level;
				element.style.KhtmlOpacity = level;
				element.style.filter = "alpha(opacity=" + (level * 100) + ");";
			}
		};


		/**
		 * fadeout timer handler
		 * @param element html element
		 * @param level level
		 */
		createTimeoutHandler = function( element, level ) {
			return function() { setOpacity( element, level ); };
		};


		/**
		 * fadeout element for n milliseconds
		 * @param element html element
		 * @param duration duration in ms
		 */
		fadeOut = function(element, duration) {
			var steps = 20;
			if(!duration) duration = 1000; // duration of fadeout
			for (var i = 1; i <= steps; i++) {
				setTimeout( createTimeoutHandler( element, 1-i/steps ), (i/steps) * duration);
			}
		};

		// GridView methods

		/**
		 * gridViewToggleDisplay
		 *
		 * toggle display attribute of table nodes
		 *
		 * @param	controlId		name of control
		 * @return	TRUE if successfull
		 */
		this.gridViewSelectAll = function( controlId )
		{
			var table = document.getElementById( controlId );
			var selectAll = document.getElementById( controlId + "__selectall" );
			var checkBoxes = table.getElementsByTagName( 'input' );

			for( var i = 0; i < checkBoxes.length; i++ )
			{
				if( checkBoxes[i].className === controlId + '__checkbox' )
				{
					checkBoxes[i].checked = selectAll.checked;
				}
			}
		};


		/**
		 * gridViewUnSelectAll
		 *
		 * toggle display attribute of table nodes
		 *
		 * @param	controlId		name of control
		 * @return	TRUE if successfull
		 */
		this.gridViewUnSelectAll = function( controlId ) {
			var trTags = document.getElementById( controlId ).getElementsByTagName( 'tr' );

			for( var i = 0; i < trTags.length; i++ ) {
				if( trTags[i].className === 'selected row' ) {
					trTags[i].className = 'row';
				}
				if( trTags[i].className === 'selected row_alt' ) {
					trTags[i].className = 'row_alt';
				}
			}
		};
	};