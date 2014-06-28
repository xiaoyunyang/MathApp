(function () {

var FBCFOX = {};

// mock objects to start
var FoxPlayer = (function ($) {
    var self;
    self = {
        is_ios: (/\b(iPad|iPhone|iPod)\b/.test(navigator.userAgent)
                || (location.hash + location.search).indexOf('isIOS') !== -1),
        is_android: /\bAndroid\b/.test(navigator.userAgent),

        isAuthRequired: function (video) {
            var _video = video || self.getVideo();
            if( !_video ){
                throw new Error( 'No video is loaded so cannot check if isAuthRequired.' );
            }
            return !!_video.is_locked
                ? _video.is_locked
                : (!!_video.authEndDate && (new Date().valueOf() < parseInt(_video.authEndDate, 10)));
        },

        load: function () {

        },

        setVideo: function (video) {
            window.player.video = video;
        },

        getVideo: function () {
            if (typeof window.player.video === 'undefined') {
                FoxPlayer.setVideo(window.module_data.video_page_player.current_video);
            }
            return window.player.video;
        },

        setToken: function () {

        },

        setTrackingInfo: function () {

        },

        togglePause: function () {

        }
    };
    return self;
}(jQuery));

var FoxId = {
    omniture: {
        notify: function () {
            
        },
        
        getMemberValues: function () {

        }
    }
};

/*!	SWFObject v2.2 <http://code.google.com/p/swfobject/> 
	is released under the MIT License <http://www.opensource.org/licenses/mit-license.php> 
*/

var swfobject = function() {
	
	var UNDEF = "undefined",
		OBJECT = "object",
		SHOCKWAVE_FLASH = "Shockwave Flash",
		SHOCKWAVE_FLASH_AX = "ShockwaveFlash.ShockwaveFlash",
		FLASH_MIME_TYPE = "application/x-shockwave-flash",
		EXPRESS_INSTALL_ID = "SWFObjectExprInst",
		ON_READY_STATE_CHANGE = "onreadystatechange",
		
		win = window,
		doc = document,
		nav = navigator,
		
		plugin = false,
		domLoadFnArr = [main],
		regObjArr = [],
		objIdArr = [],
		listenersArr = [],
		storedAltContent,
		storedAltContentId,
		storedCallbackFn,
		storedCallbackObj,
		isDomLoaded = false,
		isExpressInstallActive = false,
		dynamicStylesheet,
		dynamicStylesheetMedia,
		autoHideShow = true,
	
	/* Centralized function for browser feature detection
		- User agent string detection is only used when no good alternative is possible
		- Is executed directly for optimal performance
	*/	
	ua = function() {
		var w3cdom = typeof doc.getElementById != UNDEF && typeof doc.getElementsByTagName != UNDEF && typeof doc.createElement != UNDEF,
			u = nav.userAgent.toLowerCase(),
			p = nav.platform.toLowerCase(),
			windows = p ? /win/.test(p) : /win/.test(u),
			mac = p ? /mac/.test(p) : /mac/.test(u),
			webkit = /webkit/.test(u) ? parseFloat(u.replace(/^.*webkit\/(\d+(\.\d+)?).*$/, "$1")) : false, // returns either the webkit version or false if not webkit
			ie = !+"\v1", // feature detection based on Andrea Giammarchi's solution: http://webreflection.blogspot.com/2009/01/32-bytes-to-know-if-your-browser-is-ie.html
			playerVersion = [0,0,0],
			d = null;
		if (typeof nav.plugins != UNDEF && typeof nav.plugins[SHOCKWAVE_FLASH] == OBJECT) {
			d = nav.plugins[SHOCKWAVE_FLASH].description;
			if (d && !(typeof nav.mimeTypes != UNDEF && nav.mimeTypes[FLASH_MIME_TYPE] && !nav.mimeTypes[FLASH_MIME_TYPE].enabledPlugin)) { // navigator.mimeTypes["application/x-shockwave-flash"].enabledPlugin indicates whether plug-ins are enabled or disabled in Safari 3+
				plugin = true;
				ie = false; // cascaded feature detection for Internet Explorer
				d = d.replace(/^.*\s+(\S+\s+\S+$)/, "$1");
				playerVersion[0] = parseInt(d.replace(/^(.*)\..*$/, "$1"), 10);
				playerVersion[1] = parseInt(d.replace(/^.*\.(.*)\s.*$/, "$1"), 10);
				playerVersion[2] = /[a-zA-Z]/.test(d) ? parseInt(d.replace(/^.*[a-zA-Z]+(.*)$/, "$1"), 10) : 0;
			}
		}
		else if (typeof win.ActiveXObject != UNDEF) {
			try {
				var a = new ActiveXObject(SHOCKWAVE_FLASH_AX);
				if (a) { // a will return null when ActiveX is disabled
					d = a.GetVariable("$version");
					if (d) {
						ie = true; // cascaded feature detection for Internet Explorer
						d = d.split(" ")[1].split(",");
						playerVersion = [parseInt(d[0], 10), parseInt(d[1], 10), parseInt(d[2], 10)];
					}
				}
			}
			catch(e) {}
		}
		return { w3:w3cdom, pv:playerVersion, wk:webkit, ie:ie, win:windows, mac:mac };
	}(),
	
	/* Cross-browser onDomLoad
		- Will fire an event as soon as the DOM of a web page is loaded
		- Internet Explorer workaround based on Diego Perini's solution: http://javascript.nwbox.com/IEContentLoaded/
		- Regular onload serves as fallback
	*/ 
	onDomLoad = function() {
		if (!ua.w3) { return; }
		if ((typeof doc.readyState != UNDEF && doc.readyState == "complete") || (typeof doc.readyState == UNDEF && (doc.getElementsByTagName("body")[0] || doc.body))) { // function is fired after onload, e.g. when script is inserted dynamically 
			callDomLoadFunctions();
		}
		if (!isDomLoaded) {
			if (typeof doc.addEventListener != UNDEF) {
				doc.addEventListener("DOMContentLoaded", callDomLoadFunctions, false);
			}		
			if (ua.ie && ua.win) {
				doc.attachEvent(ON_READY_STATE_CHANGE, function() {
					if (doc.readyState == "complete") {
						doc.detachEvent(ON_READY_STATE_CHANGE, arguments.callee);
						callDomLoadFunctions();
					}
				});
				if (win == top) { // if not inside an iframe
					(function(){
						if (isDomLoaded) { return; }
						try {
							doc.documentElement.doScroll("left");
						}
						catch(e) {
							setTimeout(arguments.callee, 0);
							return;
						}
						callDomLoadFunctions();
					})();
				}
			}
			if (ua.wk) {
				(function(){
					if (isDomLoaded) { return; }
					if (!/loaded|complete/.test(doc.readyState)) {
						setTimeout(arguments.callee, 0);
						return;
					}
					callDomLoadFunctions();
				})();
			}
			addLoadEvent(callDomLoadFunctions);
		}
	}();
	
	function callDomLoadFunctions() {
		if (isDomLoaded) { return; }
		try { // test if we can really add/remove elements to/from the DOM; we don't want to fire it too early
			var t = doc.getElementsByTagName("body")[0].appendChild(createElement("span"));
			t.parentNode.removeChild(t);
		}
		catch (e) { return; }
		isDomLoaded = true;
		var dl = domLoadFnArr.length;
		for (var i = 0; i < dl; i++) {
			domLoadFnArr[i]();
		}
	}
	
	function addDomLoadEvent(fn) {
		if (isDomLoaded) {
			fn();
		}
		else { 
			domLoadFnArr[domLoadFnArr.length] = fn; // Array.push() is only available in IE5.5+
		}
	}
	
	/* Cross-browser onload
		- Based on James Edwards' solution: http://brothercake.com/site/resources/scripts/onload/
		- Will fire an event as soon as a web page including all of its assets are loaded 
	 */
	function addLoadEvent(fn) {
		if (typeof win.addEventListener != UNDEF) {
			win.addEventListener("load", fn, false);
		}
		else if (typeof doc.addEventListener != UNDEF) {
			doc.addEventListener("load", fn, false);
		}
		else if (typeof win.attachEvent != UNDEF) {
			addListener(win, "onload", fn);
		}
		else if (typeof win.onload == "function") {
			var fnOld = win.onload;
			win.onload = function() {
				fnOld();
				fn();
			};
		}
		else {
			win.onload = fn;
		}
	}
	
	/* Main function
		- Will preferably execute onDomLoad, otherwise onload (as a fallback)
	*/
	function main() { 
		if (plugin) {
			testPlayerVersion();
		}
		else {
			matchVersions();
		}
	}
	
	/* Detect the Flash Player version for non-Internet Explorer browsers
		- Detecting the plug-in version via the object element is more precise than using the plugins collection item's description:
		  a. Both release and build numbers can be detected
		  b. Avoid wrong descriptions by corrupt installers provided by Adobe
		  c. Avoid wrong descriptions by multiple Flash Player entries in the plugin Array, caused by incorrect browser imports
		- Disadvantage of this method is that it depends on the availability of the DOM, while the plugins collection is immediately available
	*/
	function testPlayerVersion() {
		var b = doc.getElementsByTagName("body")[0];
		var o = createElement(OBJECT);
		o.setAttribute("type", FLASH_MIME_TYPE);
		var t = b.appendChild(o);
		if (t) {
			var counter = 0;
			(function(){
				if (typeof t.GetVariable != UNDEF) {
					var d = t.GetVariable("$version");
					if (d) {
						d = d.split(" ")[1].split(",");
						ua.pv = [parseInt(d[0], 10), parseInt(d[1], 10), parseInt(d[2], 10)];
					}
				}
				else if (counter < 10) {
					counter++;
					setTimeout(arguments.callee, 10);
					return;
				}
				b.removeChild(o);
				t = null;
				matchVersions();
			})();
		}
		else {
			matchVersions();
		}
	}
	
	/* Perform Flash Player and SWF version matching; static publishing only
	*/
	function matchVersions() {
		var rl = regObjArr.length;
		if (rl > 0) {
			for (var i = 0; i < rl; i++) { // for each registered object element
				var id = regObjArr[i].id;
				var cb = regObjArr[i].callbackFn;
				var cbObj = {success:false, id:id};
				if (ua.pv[0] > 0) {
					var obj = getElementById(id);
					if (obj) {
						if (hasPlayerVersion(regObjArr[i].swfVersion) && !(ua.wk && ua.wk < 312)) { // Flash Player version >= published SWF version: Houston, we have a match!
							setVisibility(id, true);
							if (cb) {
								cbObj.success = true;
								cbObj.ref = getObjectById(id);
								cb(cbObj);
							}
						}
						else if (regObjArr[i].expressInstall && canExpressInstall()) { // show the Adobe Express Install dialog if set by the web page author and if supported
							var att = {};
							att.data = regObjArr[i].expressInstall;
							att.width = obj.getAttribute("width") || "0";
							att.height = obj.getAttribute("height") || "0";
							if (obj.getAttribute("class")) { att.styleclass = obj.getAttribute("class"); }
							if (obj.getAttribute("align")) { att.align = obj.getAttribute("align"); }
							// parse HTML object param element's name-value pairs
							var par = {};
							var p = obj.getElementsByTagName("param");
							var pl = p.length;
							for (var j = 0; j < pl; j++) {
								if (p[j].getAttribute("name").toLowerCase() != "movie") {
									par[p[j].getAttribute("name")] = p[j].getAttribute("value");
								}
							}
							showExpressInstall(att, par, id, cb);
						}
						else { // Flash Player and SWF version mismatch or an older Webkit engine that ignores the HTML object element's nested param elements: display alternative content instead of SWF
							displayAltContent(obj);
							if (cb) { cb(cbObj); }
						}
					}
				}
				else {	// if no Flash Player is installed or the fp version cannot be detected we let the HTML object element do its job (either show a SWF or alternative content)
					setVisibility(id, true);
					if (cb) {
						var o = getObjectById(id); // test whether there is an HTML object element or not
						if (o && typeof o.SetVariable != UNDEF) { 
							cbObj.success = true;
							cbObj.ref = o;
						}
						cb(cbObj);
					}
				}
			}
		}
	}
	
	function getObjectById(objectIdStr) {
		var r = null;
		var o = getElementById(objectIdStr);
		if (o && o.nodeName == "OBJECT") {
			if (typeof o.SetVariable != UNDEF) {
				r = o;
			}
			else {
				var n = o.getElementsByTagName(OBJECT)[0];
				if (n) {
					r = n;
				}
			}
		}
		return r;
	}
	
	/* Requirements for Adobe Express Install
		- only one instance can be active at a time
		- fp 6.0.65 or higher
		- Win/Mac OS only
		- no Webkit engines older than version 312
	*/
	function canExpressInstall() {
		return !isExpressInstallActive && hasPlayerVersion("6.0.65") && (ua.win || ua.mac) && !(ua.wk && ua.wk < 312);
	}
	
	/* Show the Adobe Express Install dialog
		- Reference: http://www.adobe.com/cfusion/knowledgebase/index.cfm?id=6a253b75
	*/
	function showExpressInstall(att, par, replaceElemIdStr, callbackFn) {
		isExpressInstallActive = true;
		storedCallbackFn = callbackFn || null;
		storedCallbackObj = {success:false, id:replaceElemIdStr};
		var obj = getElementById(replaceElemIdStr);
		if (obj) {
			if (obj.nodeName == "OBJECT") { // static publishing
				storedAltContent = abstractAltContent(obj);
				storedAltContentId = null;
			}
			else { // dynamic publishing
				storedAltContent = obj;
				storedAltContentId = replaceElemIdStr;
			}
			att.id = EXPRESS_INSTALL_ID;
			if (typeof att.width == UNDEF || (!/%$/.test(att.width) && parseInt(att.width, 10) < 310)) { att.width = "310"; }
			if (typeof att.height == UNDEF || (!/%$/.test(att.height) && parseInt(att.height, 10) < 137)) { att.height = "137"; }
			doc.title = doc.title.slice(0, 47) + " - Flash Player Installation";
			var pt = ua.ie && ua.win ? "ActiveX" : "PlugIn",
				fv = "MMredirectURL=" + win.location.toString().replace(/&/g,"%26") + "&MMplayerType=" + pt + "&MMdoctitle=" + doc.title;
			if (typeof par.flashvars != UNDEF) {
				par.flashvars += "&" + fv;
			}
			else {
				par.flashvars = fv;
			}
			// IE only: when a SWF is loading (AND: not available in cache) wait for the readyState of the object element to become 4 before removing it,
			// because you cannot properly cancel a loading SWF file without breaking browser load references, also obj.onreadystatechange doesn't work
			if (ua.ie && ua.win && obj.readyState != 4) {
				var newObj = createElement("div");
				replaceElemIdStr += "SWFObjectNew";
				newObj.setAttribute("id", replaceElemIdStr);
				obj.parentNode.insertBefore(newObj, obj); // insert placeholder div that will be replaced by the object element that loads expressinstall.swf
				obj.style.display = "none";
				(function(){
					if (obj.readyState == 4) {
						obj.parentNode.removeChild(obj);
					}
					else {
						setTimeout(arguments.callee, 10);
					}
				})();
			}
			createSWF(att, par, replaceElemIdStr);
		}
	}
	
	/* Functions to abstract and display alternative content
	*/
	function displayAltContent(obj) {
		if (ua.ie && ua.win && obj.readyState != 4) {
			// IE only: when a SWF is loading (AND: not available in cache) wait for the readyState of the object element to become 4 before removing it,
			// because you cannot properly cancel a loading SWF file without breaking browser load references, also obj.onreadystatechange doesn't work
			var el = createElement("div");
			obj.parentNode.insertBefore(el, obj); // insert placeholder div that will be replaced by the alternative content
			el.parentNode.replaceChild(abstractAltContent(obj), el);
			obj.style.display = "none";
			(function(){
				if (obj.readyState == 4) {
					obj.parentNode.removeChild(obj);
				}
				else {
					setTimeout(arguments.callee, 10);
				}
			})();
		}
		else {
			obj.parentNode.replaceChild(abstractAltContent(obj), obj);
		}
	} 

	function abstractAltContent(obj) {
		var ac = createElement("div");
		if (ua.win && ua.ie) {
			ac.innerHTML = obj.innerHTML;
		}
		else {
			var nestedObj = obj.getElementsByTagName(OBJECT)[0];
			if (nestedObj) {
				var c = nestedObj.childNodes;
				if (c) {
					var cl = c.length;
					for (var i = 0; i < cl; i++) {
						if (!(c[i].nodeType == 1 && c[i].nodeName == "PARAM") && !(c[i].nodeType == 8)) {
							ac.appendChild(c[i].cloneNode(true));
						}
					}
				}
			}
		}
		return ac;
	}
	
	/* Cross-browser dynamic SWF creation
	*/
	function createSWF(attObj, parObj, id) {
		var r, el = getElementById(id);
		if (ua.wk && ua.wk < 312) { return r; }
		if (el) {
			if (typeof attObj.id == UNDEF) { // if no 'id' is defined for the object element, it will inherit the 'id' from the alternative content
				attObj.id = id;
			}
			if (ua.ie && ua.win) { // Internet Explorer + the HTML object element + W3C DOM methods do not combine: fall back to outerHTML
				var att = "";
				for (var i in attObj) {
					if (attObj[i] != Object.prototype[i]) { // filter out prototype additions from other potential libraries
						if (i.toLowerCase() == "data") {
							parObj.movie = attObj[i];
						}
						else if (i.toLowerCase() == "styleclass") { // 'class' is an ECMA4 reserved keyword
							att += ' class="' + attObj[i] + '"';
						}
						else if (i.toLowerCase() != "classid") {
							att += ' ' + i + '="' + attObj[i] + '"';
						}
					}
				}
				var par = "";
				for (var j in parObj) {
					if (parObj[j] != Object.prototype[j]) { // filter out prototype additions from other potential libraries
						par += '<param name="' + j + '" value="' + parObj[j] + '" />';
					}
				}
				el.outerHTML = '<object classid="clsid:D27CDB6E-AE6D-11cf-96B8-444553540000"' + att + '>' + par + '</object>';
				objIdArr[objIdArr.length] = attObj.id; // stored to fix object 'leaks' on unload (dynamic publishing only)
				r = getElementById(attObj.id);	
			}
			else { // well-behaving browsers
				var o = createElement(OBJECT);
				o.setAttribute("type", FLASH_MIME_TYPE);
				for (var m in attObj) {
					if (attObj[m] != Object.prototype[m]) { // filter out prototype additions from other potential libraries
						if (m.toLowerCase() == "styleclass") { // 'class' is an ECMA4 reserved keyword
							o.setAttribute("class", attObj[m]);
						}
						else if (m.toLowerCase() != "classid") { // filter out IE specific attribute
							o.setAttribute(m, attObj[m]);
						}
					}
				}
				for (var n in parObj) {
					if (parObj[n] != Object.prototype[n] && n.toLowerCase() != "movie") { // filter out prototype additions from other potential libraries and IE specific param element
						createObjParam(o, n, parObj[n]);
					}
				}
				el.parentNode.replaceChild(o, el);
				r = o;
			}
		}
		return r;
	}
	
	function createObjParam(el, pName, pValue) {
		var p = createElement("param");
		p.setAttribute("name", pName);	
		p.setAttribute("value", pValue);
		el.appendChild(p);
	}
	
	/* Cross-browser SWF removal
		- Especially needed to safely and completely remove a SWF in Internet Explorer
	*/
	function removeSWF(id) {
		var obj = getElementById(id);
		if (obj && obj.nodeName == "OBJECT") {
			if (ua.ie && ua.win) {
				obj.style.display = "none";
				(function(){
					if (obj.readyState == 4) {
						removeObjectInIE(id);
					}
					else {
						setTimeout(arguments.callee, 10);
					}
				})();
			}
			else {
				obj.parentNode.removeChild(obj);
			}
		}
	}
	
	function removeObjectInIE(id) {
		var obj = getElementById(id);
		if (obj) {
			for (var i in obj) {
				if (typeof obj[i] == "function") {
					obj[i] = null;
				}
			}
			obj.parentNode.removeChild(obj);
		}
	}
	
	/* Functions to optimize JavaScript compression
	*/
	function getElementById(id) {
		var el = null;
		try {
			el = doc.getElementById(id);
		}
		catch (e) {}
		return el;
	}
	
	function createElement(el) {
		return doc.createElement(el);
	}
	
	/* Updated attachEvent function for Internet Explorer
		- Stores attachEvent information in an Array, so on unload the detachEvent functions can be called to avoid memory leaks
	*/	
	function addListener(target, eventType, fn) {
		target.attachEvent(eventType, fn);
		listenersArr[listenersArr.length] = [target, eventType, fn];
	}
	
	/* Flash Player and SWF content version matching
	*/
	function hasPlayerVersion(rv) {
		var pv = ua.pv, v = rv.split(".");
		v[0] = parseInt(v[0], 10);
		v[1] = parseInt(v[1], 10) || 0; // supports short notation, e.g. "9" instead of "9.0.0"
		v[2] = parseInt(v[2], 10) || 0;
		return (pv[0] > v[0] || (pv[0] == v[0] && pv[1] > v[1]) || (pv[0] == v[0] && pv[1] == v[1] && pv[2] >= v[2])) ? true : false;
	}
	
	/* Cross-browser dynamic CSS creation
		- Based on Bobby van der Sluis' solution: http://www.bobbyvandersluis.com/articles/dynamicCSS.php
	*/	
	function createCSS(sel, decl, media, newStyle) {
		if (ua.ie && ua.mac) { return; }
		var h = doc.getElementsByTagName("head")[0];
		if (!h) { return; } // to also support badly authored HTML pages that lack a head element
		var m = (media && typeof media == "string") ? media : "screen";
		if (newStyle) {
			dynamicStylesheet = null;
			dynamicStylesheetMedia = null;
		}
		if (!dynamicStylesheet || dynamicStylesheetMedia != m) { 
			// create dynamic stylesheet + get a global reference to it
			var s = createElement("style");
			s.setAttribute("type", "text/css");
			s.setAttribute("media", m);
			dynamicStylesheet = h.appendChild(s);
			if (ua.ie && ua.win && typeof doc.styleSheets != UNDEF && doc.styleSheets.length > 0) {
				dynamicStylesheet = doc.styleSheets[doc.styleSheets.length - 1];
			}
			dynamicStylesheetMedia = m;
		}
		// add style rule
		if (ua.ie && ua.win) {
			if (dynamicStylesheet && typeof dynamicStylesheet.addRule == OBJECT) {
				dynamicStylesheet.addRule(sel, decl);
			}
		}
		else {
			if (dynamicStylesheet && typeof doc.createTextNode != UNDEF) {
				dynamicStylesheet.appendChild(doc.createTextNode(sel + " {" + decl + "}"));
			}
		}
	}
	
	function setVisibility(id, isVisible) {
		if (!autoHideShow) { return; }
		var v = isVisible ? "visible" : "hidden";
		if (isDomLoaded && getElementById(id)) {
			getElementById(id).style.visibility = v;
		}
		else {
			createCSS("#" + id, "visibility:" + v);
		}
	}

	/* Filter to avoid XSS attacks
	*/
	function urlEncodeIfNecessary(s) {
		var regex = /[\\\"<>\.;]/;
		var hasBadChars = regex.exec(s) != null;
		return hasBadChars && typeof encodeURIComponent != UNDEF ? encodeURIComponent(s) : s;
	}
	
	/* Release memory to avoid memory leaks caused by closures, fix hanging audio/video threads and force open sockets/NetConnections to disconnect (Internet Explorer only)
	*/
	var cleanup = function() {
		if (ua.ie && ua.win) {
			window.attachEvent("onunload", function() {
				// remove listeners to avoid memory leaks
				var ll = listenersArr.length;
				for (var i = 0; i < ll; i++) {
					listenersArr[i][0].detachEvent(listenersArr[i][1], listenersArr[i][2]);
				}
				// cleanup dynamically embedded objects to fix audio/video threads and force open sockets and NetConnections to disconnect
				var il = objIdArr.length;
				for (var j = 0; j < il; j++) {
					removeSWF(objIdArr[j]);
				}
				// cleanup library's main closures to avoid memory leaks
				for (var k in ua) {
					ua[k] = null;
				}
				ua = null;
				for (var l in swfobject) {
					swfobject[l] = null;
				}
				swfobject = null;
			});
		}
	}();
	
	return {
		/* Public API
			- Reference: http://code.google.com/p/swfobject/wiki/documentation
		*/ 
		registerObject: function(objectIdStr, swfVersionStr, xiSwfUrlStr, callbackFn) {
			if (ua.w3 && objectIdStr && swfVersionStr) {
				var regObj = {};
				regObj.id = objectIdStr;
				regObj.swfVersion = swfVersionStr;
				regObj.expressInstall = xiSwfUrlStr;
				regObj.callbackFn = callbackFn;
				regObjArr[regObjArr.length] = regObj;
				setVisibility(objectIdStr, false);
			}
			else if (callbackFn) {
				callbackFn({success:false, id:objectIdStr});
			}
		},
		
		getObjectById: function(objectIdStr) {
			if (ua.w3) {
				return getObjectById(objectIdStr);
			}
		},
		
		embedSWF: function(swfUrlStr, replaceElemIdStr, widthStr, heightStr, swfVersionStr, xiSwfUrlStr, flashvarsObj, parObj, attObj, callbackFn) {
			var callbackObj = {success:false, id:replaceElemIdStr};
			if (ua.w3 && !(ua.wk && ua.wk < 312) && swfUrlStr && replaceElemIdStr && widthStr && heightStr && swfVersionStr) {
				setVisibility(replaceElemIdStr, false);
				addDomLoadEvent(function() {
					widthStr += ""; // auto-convert to string
					heightStr += "";
					var att = {};
					if (attObj && typeof attObj === OBJECT) {
						for (var i in attObj) { // copy object to avoid the use of references, because web authors often reuse attObj for multiple SWFs
							att[i] = attObj[i];
						}
					}
					att.data = swfUrlStr;
					att.width = widthStr;
					att.height = heightStr;
					var par = {}; 
					if (parObj && typeof parObj === OBJECT) {
						for (var j in parObj) { // copy object to avoid the use of references, because web authors often reuse parObj for multiple SWFs
							par[j] = parObj[j];
						}
					}
					if (flashvarsObj && typeof flashvarsObj === OBJECT) {
						for (var k in flashvarsObj) { // copy object to avoid the use of references, because web authors often reuse flashvarsObj for multiple SWFs
							if (typeof par.flashvars != UNDEF) {
								par.flashvars += "&" + k + "=" + flashvarsObj[k];
							}
							else {
								par.flashvars = k + "=" + flashvarsObj[k];
							}
						}
					}
					if (hasPlayerVersion(swfVersionStr)) { // create SWF
						var obj = createSWF(att, par, replaceElemIdStr);
						if (att.id == replaceElemIdStr) {
							setVisibility(replaceElemIdStr, true);
						}
						callbackObj.success = true;
						callbackObj.ref = obj;
					}
					else if (xiSwfUrlStr && canExpressInstall()) { // show Adobe Express Install
						att.data = xiSwfUrlStr;
						showExpressInstall(att, par, replaceElemIdStr, callbackFn);
						return;
					}
					else { // show alternative content
						setVisibility(replaceElemIdStr, true);
					}
					if (callbackFn) { callbackFn(callbackObj); }
				});
			}
			else if (callbackFn) { callbackFn(callbackObj);	}
		},
		
		switchOffAutoHideShow: function() {
			autoHideShow = false;
		},
		
		ua: ua,
		
		getFlashPlayerVersion: function() {
			return { major:ua.pv[0], minor:ua.pv[1], release:ua.pv[2] };
		},
		
		hasFlashPlayerVersion: hasPlayerVersion,
		
		createSWF: function(attObj, parObj, replaceElemIdStr) {
			if (ua.w3) {
				return createSWF(attObj, parObj, replaceElemIdStr);
			}
			else {
				return undefined;
			}
		},
		
		showExpressInstall: function(att, par, replaceElemIdStr, callbackFn) {
			if (ua.w3 && canExpressInstall()) {
				showExpressInstall(att, par, replaceElemIdStr, callbackFn);
			}
		},
		
		removeSWF: function(objElemIdStr) {
			if (ua.w3) {
				removeSWF(objElemIdStr);
			}
		},
		
		createCSS: function(selStr, declStr, mediaStr, newStyleBoolean) {
			if (ua.w3) {
				createCSS(selStr, declStr, mediaStr, newStyleBoolean);
			}
		},
		
		addDomLoadEvent: addDomLoadEvent,
		
		addLoadEvent: addLoadEvent,
		
		getQueryParamValue: function(param) {
			var q = doc.location.search || doc.location.hash;
			if (q) {
				if (/\?/.test(q)) { q = q.split("?")[1]; } // strip question mark
				if (param == null) {
					return urlEncodeIfNecessary(q);
				}
				var pairs = q.split("&");
				for (var i = 0; i < pairs.length; i++) {
					if (pairs[i].substring(0, pairs[i].indexOf("=")) == param) {
						return urlEncodeIfNecessary(pairs[i].substring((pairs[i].indexOf("=") + 1)));
					}
				}
			}
			return "";
		},
		
		// For internal usage only
		expressInstallCallback: function() {
			if (isExpressInstallActive) {
				var obj = getElementById(EXPRESS_INSTALL_ID);
				if (obj && storedAltContent) {
					obj.parentNode.replaceChild(storedAltContent, obj);
					if (storedAltContentId) {
						setVisibility(storedAltContentId, true);
						if (ua.ie && ua.win) { storedAltContent.style.display = "block"; }
					}
					if (storedCallbackFn) { storedCallbackFn(storedCallbackObj); }
				}
				isExpressInstallActive = false;
			} 
		}
	};
}();
/**
 * Copyright (c) Copyright (c) 2007, Carl S. Yestrau All rights reserved.
 * Code licensed under the BSD License: http://www.featureblend.com/license.txt
 * Version: 1.0.4
 */
var FlashDetect = new function(){
    var self = this;
    self.installed = false;
    self.raw = "";
    self.major = -1;
    self.minor = -1;
    self.revision = -1;
    self.revisionStr = "";
    var activeXDetectRules = [
        {
            "name":"ShockwaveFlash.ShockwaveFlash.7",
            "version":function(obj){
                return getActiveXVersion(obj);
            }
        },
        {
            "name":"ShockwaveFlash.ShockwaveFlash.6",
            "version":function(obj){
                var version = "6,0,21";
                try{
                    obj.AllowScriptAccess = "always";
                    version = getActiveXVersion(obj);
                }catch(err){}
                return version;
            }
        },
        {
            "name":"ShockwaveFlash.ShockwaveFlash",
            "version":function(obj){
                return getActiveXVersion(obj);
            }
        }
    ];
    /**
     * Extract the ActiveX version of the plugin.
     * 
     * @param {Object} The flash ActiveX object.
     * @type String
     */
    var getActiveXVersion = function(activeXObj){
        var version = -1;
        try{
            version = activeXObj.GetVariable("$version");
        }catch(err){}
        return version;
    };
    /**
     * Try and retrieve an ActiveX object having a specified name.
     * 
     * @param {String} name The ActiveX object name lookup.
     * @return One of ActiveX object or a simple object having an attribute of activeXError with a value of true.
     * @type Object
     */
    var getActiveXObject = function(name){
        var obj = -1;
        try{
            obj = new ActiveXObject(name);
        }catch(err){
            obj = {activeXError:true};
        }
        return obj;
    };
    /**
     * Parse an ActiveX $version string into an object.
     * 
     * @param {String} str The ActiveX Object GetVariable($version) return value. 
     * @return An object having raw, major, minor, revision and revisionStr attributes.
     * @type Object
     */
    var parseActiveXVersion = function(str){
        var versionArray = str.split(",");//replace with regex
        return {
            "raw":str,
            "major":parseInt(versionArray[0].split(" ")[1], 10),
            "minor":parseInt(versionArray[1], 10),
            "revision":parseInt(versionArray[2], 10),
            "revisionStr":versionArray[2]
        };
    };
    /**
     * Parse a standard enabledPlugin.description into an object.
     * 
     * @param {String} str The enabledPlugin.description value.
     * @return An object having raw, major, minor, revision and revisionStr attributes.
     * @type Object
     */
    var parseStandardVersion = function(str){
        var descParts = str.split(/ +/);
        var majorMinor = descParts[2].split(/\./);
        var revisionStr = descParts[3];
        return {
            "raw":str,
            "major":parseInt(majorMinor[0], 10),
            "minor":parseInt(majorMinor[1], 10), 
            "revisionStr":revisionStr,
            "revision":parseRevisionStrToInt(revisionStr)
        };
    };
    /**
     * Parse the plugin revision string into an integer.
     * 
     * @param {String} The revision in string format.
     * @type Number
     */
    var parseRevisionStrToInt = function(str){
        return parseInt(str.replace(/[a-zA-Z]/g, ""), 10) || self.revision;
    };
    /**
     * Is the major version greater than or equal to a specified version.
     * 
     * @param {Number} version The minimum required major version.
     * @type Boolean
     */
    self.majorAtLeast = function(version){
        return self.major >= version;
    };
    /**
     * Is the minor version greater than or equal to a specified version.
     * 
     * @param {Number} version The minimum required minor version.
     * @type Boolean
     */
    self.minorAtLeast = function(version){
        return self.minor >= version;
    };
    /**
     * Is the revision version greater than or equal to a specified version.
     * 
     * @param {Number} version The minimum required revision version.
     * @type Boolean
     */
    self.revisionAtLeast = function(version){
        return self.revision >= version;
    };
    /**
     * Is the version greater than or equal to a specified major, minor and revision.
     * 
     * @param {Number} major The minimum required major version.
     * @param {Number} (Optional) minor The minimum required minor version.
     * @param {Number} (Optional) revision The minimum required revision version.
     * @type Boolean
     */
    self.versionAtLeast = function(major){
        var properties = [self.major, self.minor, self.revision];
        var len = Math.min(properties.length, arguments.length);
        for(i=0; i<len; i++){
            if(properties[i]>=arguments[i]){
                if(i+1<len && properties[i]==arguments[i]){
                    continue;
                }else{
                    return true;
                }
            }else{
                return false;
            }
        }
    };
    /**
     * Constructor, sets raw, major, minor, revisionStr, revision and installed public properties.
     */
    self.FlashDetect = function(){
        if(navigator.plugins && navigator.plugins.length>0){
            var type = 'application/x-shockwave-flash';
            var mimeTypes = navigator.mimeTypes;
            if(mimeTypes && mimeTypes[type] && mimeTypes[type].enabledPlugin && mimeTypes[type].enabledPlugin.description){
                var version = mimeTypes[type].enabledPlugin.description;
                var versionObj = parseStandardVersion(version);
                self.raw = versionObj.raw;
                self.major = versionObj.major;
                self.minor = versionObj.minor; 
                self.revisionStr = versionObj.revisionStr;
                self.revision = versionObj.revision;
                self.installed = true;
            }
        }else if(navigator.appVersion.indexOf("Mac")==-1 && window.execScript){
            var version = -1;
            for(var i=0; i<activeXDetectRules.length && version==-1; i++){
                var obj = getActiveXObject(activeXDetectRules[i].name);
                if(!obj.activeXError){
                    self.installed = true;
                    version = activeXDetectRules[i].version(obj);
                    if(version!=-1){
                        var versionObj = parseActiveXVersion(version);
                        self.raw = versionObj.raw;
                        self.major = versionObj.major;
                        self.minor = versionObj.minor; 
                        self.revision = versionObj.revision;
                        self.revisionStr = versionObj.revisionStr;
                    }
                }
            }
        }
    }();
};
FlashDetect.JS_RELEASE = "1.0.4";/*global jQuery, AdobePass, VideoAuth, FoxEnv, State, Mustache */

(function(){
var $ = jQuery;

var AuthModal;
var Steps;
var Step;
var Notifications;

AuthModal = {
    template_url: '/_app/components/auth-2.0/template.html',
    error_screen_url: '/_app/components/auth-2.0/screens/error_screen.html',

    is_open: new State(false),
    is_complete: new State(false),
    isOpen: function(){
        return this.is_open.get();
    },

    holder: null,
    screen_loader: new State(),
    title: new State(),
    onBack: null,

    setTitle: function (title) {
        this.title.set(title);
    },

    _attachColorboxEvents: function(){
        var self = this;

        // Re-emit the cbox events in the context of the AuthModal
        var relay_event = function(e){
            return $(self).trigger(e);
        };
        var event_types = ['cbox_open', 'cbox_load', 'cbox_complete', 'cbox_cleanup', 'cbox_closed'];
        $.each(event_types, function(i, event_type){
            $(document).one(event_type, function(e){ // 'one' for cleanup, and since each only fires once anyway
                $(self).trigger(e);
            });
        });
    },

    /**
     * Open the AuthModal with the relevant settings, and populate the lightbox with the auth template
     */
    open: function() {
        var self = this;
        if (self.isOpen()) {
            return;
        }
        self.is_open.set(true);

        function onrendered() {
            self.resize();
        }

        var previous_step_id = null;
        function onchange_step(step) {
            self.Steps.render();
            if (previous_step_id) {
                self.holder.removeClass(previous_step_id);
            }
            if (step) {
                self.screen_loader.set(step.screen_loader.get());
                self.holder.addClass(step.id);
                previous_step_id = step.id;
                self.setTitle(step.title);
            }
            else {
                // Note: We are not setting self.setTitle(null) because we assume the error handler is setting it
                previous_step_id = null;
            }
        }
        function onchange_notification(notification) {
            self.Notifications.render();
        }
        function onchange_title(title) {
            self.holder.find('.title:first').text(title || '');
        }
        function onchange_screen_loader(screen_loader) {
            self.renderScreen(screen_loader);
        }

        function setup_colorbox(data) {
            self._attachColorboxEvents();
            var step_screen_loader_watchers = []; // for cleanup

            self.holder = $(data);
            $.colorbox({
                html: self.holder,
                scrolling: false,
                opacity: 0.75,
                onOpen: function(){
                    $('#colorbox').addClass('auth-modal');

                    self.Steps.container = self.holder.find('.steps');
                    $(self.Steps).on('rendered', onrendered);
                    self.Steps.current.watch(onchange_step);

                    self.Notifications.container = self.holder.find('.notifications-container');
                    $(self.Notifications).on('rendered', onrendered);
                    self.Notifications.current.watch(onchange_notification);

                    self.title.watch(onchange_title);
                    self.screen_loader.watch(onchange_screen_loader);

                    self.holder.find('.btn-back').click(function () {
                        if (self.onBack) {
                            self.onBack();
                        }
                    });

                    // Start watching for changes to the each step's screens
                    $.each(self.Steps.items, function(i, step){
                        var step_screen_loader_watcher = function(screen_loader){
                            if(self.Steps.isCurrent(step)){
                                self.screen_loader.set(screen_loader);
                            }
                        };
                        step.screen_loader.watch(step_screen_loader_watcher);

                        // Bookkeeping for cleanup
                        step_screen_loader_watchers.push({
                            "screen_loader": step.screen_loader,
                            "watcher": step_screen_loader_watcher
                        });
                    });
                },
                onComplete: function(){
                    self.resize();
                    self.is_complete.set(true);
                    // @todo There is a chance that a Step is added in the course of the lifetime of the AuthModal; this is not accounted for
                },
                onCleanup: function(){
                    self.is_complete.set(false);
                    self.title.unwatch(onchange_title);
                    self.screen_loader.unwatch(onchange_screen_loader);

                    // Stop watching for changes to the step screens
                    $.each(step_screen_loader_watchers, function(i, item){
                        item.screen_loader.unwatch(item.watcher);
                    });

                    $(self.Steps).off('rendered', onrendered);
                    self.Steps.current.unwatch(onchange_step);

                    $(self.Notifications).off('rendered', onrendered);
                    self.Notifications.current.unwatch(onchange_notification);
                },
                onClosed: function(){
                    $('#colorbox').removeClass('auth-modal');
                    self.holder = null;
                    self.is_open.set(false);
                    location.reload();
                }
            });
        }

        $.ajax({
            url: self.template_url, // @todo We might want to inline this
            type: 'GET',
            cache: (typeof FoxEnv === 'undefined' || FoxEnv.getCurrent() === 'production'),
            dataType: 'html',
            success: setup_colorbox,
            error: function(xhr, textStatus, err){
                self.is_open.set(false);
                throw (err || new Error(textStatus));
            }
        });
    },

    /**
     * Generate the 'So Awkward' screen loader
     */
    createErrorScreenLoader: function (error) {
        var self = this;

        return function (callback) {
            $.ajax({
                url: self.error_screen_url, // @todo We might want to inline this
                type: 'GET',
                cache: (typeof FoxEnv === 'undefined' || FoxEnv.getCurrent() === 'production'),
                dataType: 'html',
                success: function (tpl) {
                    var elm = $(Mustache.render(tpl, error));
                    elm.find('.utl-closebutton').click(function(e){
                        e.preventDefault();
                        jQuery.colorbox.close();
                    });
                    elm.find('.utl-tryagainbutton').click(function(e){
                        e.preventDefault();
                        location.reload();
                    });
                    callback(null, {
                        title: 'Error',
                        content: elm
                    });
                },
                error: function(xhr, textStatus, err){
                    callback(err || new Error(textStatus));
                }
            });
        };
    },

    /**
     * Helper function for what to do when a capture callback throws an error
     */
    populateError: function (error) {
        this.Notifications.remove();
        this.Steps.clear();
        this.screen_loader.set(this.createErrorScreenLoader(error));
        console.error(error); // Don't `throw error` here because lightbox can't open afterward
    },

    /**
     * Render the currently selected screen
     * @param {Function|null} screen_loader If null, then the screen is emptied
     */
    renderScreen: function(screen_loader){
        var self = this;
        var screens_holder = self.holder.find('.screens');

        if (!screen_loader) {
            screens_holder.empty();
            return;
        }

        if (typeof screen_loader !== 'function') {
            throw new TypeError('The screen_loader must be a function.');
        }
        screen_loader(function(err, props){
            try {
                if (err) {
                    throw err;
                }

                var title;
                var content;
                var back_button_handler;

                // Blessed way of passing data back from callback where props is {title:X, info/error:Y, content:$(Z)}
                if ($.isPlainObject(props)) {
                    if (props.title) {
                        title = props.title;
                    }
                    if (!props.content) {
                        throw new Error("Failure to pass content back from screen_loader.");
                    }
                    content = $(props.content);

                    // @todo It would be nice if we could conflate these into a single property
                    if (props.info) {
                        self.Notifications.info(props.info);
                    }
                    else if (props.error) {
                        self.Notifications.error(props.error);
                    }
                    if (props.onBack) {
                        back_button_handler = props.onBack;
                    }
                }
                // Deprecated way of passing content element directly without object wrapper
                else {
                    // @todo Also reset the notification?
                    content = $(props);
                }

                self.setTitle(title || (self.Steps.current.get() || {}).title);
                if (!content.is('iframe') && $.contains(screens_holder[0], content[0])) {
                    throw new Error("AuthModal.renderScreen(): attempted to set overwrite a screen with itself which would destroy jQuery event handlers.");
                }
                if (back_button_handler) {
                    self.onBack = back_button_handler;
                    self.holder.find('.btn-back:first').show();
                }
                else {
                    self.onBack = null;
                    self.holder.find('.btn-back:first').hide();
                }

                screens_holder.empty().append(content);
                setTimeout(function () {
                    self.resize();
                }, 250);
                $(self).trigger('screen_rendered', [screen_loader]);
            }
            catch(e){
                self.populateError(e);
            }
        });
    },

    /**
     * Alias for remove()
     */
    close: function(){
        this.remove();
    },

    /**
     * Remove the colorbox if it is for the auth modal
     */
    remove: function(){
        if (this.isOpen()) {
            this.is_open.set(false);
            this.is_complete.set(false);
            $.colorbox.close();
        }
    },

    /**
     * Resize the authmodal according the dimensions of the contents
     */
    resize: function(){
        if (!this.is_complete.get()) {
            return;
        }

        var dimensions = {
            innerHeight: this.holder.outerHeight(),
            innerWidth: this.holder.outerWidth()
        };

        try {
            $.colorbox.resize(dimensions);
        }
        catch(e){
            console.warn(e);
        }
    }

};


Step = AuthModal.Step = function(args){
    $.extend(this, args);
    if (!(this.screen_loader instanceof State)) {
        this.screen_loader = new State(this.screen_loader);
    }
};


/**
 * Management of the steps that appears above the screens
 */
Steps = AuthModal.Steps = {
    tpl:  '<ul>'
        + '{{#steps}}'
        + '<li class="{{step_number}} {{status}} {{id}}">{{description}}</li>'
        + '{{/steps}}'
        +'</ul>',
    min_visible: 2,

    is_visible: new State(true),
    current: new State(),
    container: null,
    getContainer: function(){
        return this.container;
    },

    items: [],

    /**
     * Clear out the step list and re-add each of the steps to it, setting the
     * current step to the active state with each other getting inactive.
     * Automatically shows the steps if min_visible
     * @param {Function} complete_callback when the function finishes rendering
     */
    render: function(complete_callback){
        var self = this;
        var container = this.getContainer();
        if (!container){
            return;
        }

        var trigger_rendered = function () {
            if (typeof complete_callback !== 'undefined') {
                complete_callback.apply(this, arguments);
            }
            $(self).trigger('rendered');
        };

        // So that slideDown will initially work when container is not yet visible
        if (container.is(':empty')) {
            container.hide();
        }

        var steps_vars = $.map(self.items, function(step, i){
            return $.extend({}, step, {
                step_number: 'step-' + (i+1),
                status: self.isCurrent(step) ? 'active' : 'inactive'
            });
        });

        var steps_elm = Mustache.render(self.tpl, {steps: steps_vars});
        container.empty().append(steps_elm);

        var has_enough_to_show = (self.items.length >= self.min_visible);
        var is_visible = (self.is_visible.get() && has_enough_to_show);

        // We behave differently if parent is hidden since slideUp does nothing
        // if the element is not visible already.
        var is_parent_hidden = container.parent().is(':hidden');
        if (is_parent_hidden) {
            container.toggle(is_visible);
            trigger_rendered.call(container.get(0));
        }
        else {
            if (is_visible) {
                container.slideDown(trigger_rendered);
            }
            else {
                container.slideUp(trigger_rendered);
            }
        }
    },

    /**
     * @param {Function} complete_callback
     */
    hide: function (complete_callback) {
        this.toggle(false, complete_callback);
    },

    /**
     * @param {Function} complete_callback
     */
    show: function (complete_callback) {
        var has_enough_to_show = (this.items.length >= this.min_visible);
        this.toggle(has_enough_to_show, complete_callback);
    },

    /**
     * @param {Boolean} is_visible
     * @param {Function} complete_callback
     */
    toggle: function (is_visible, complete_callback) {
        var self = this;
        var container = this.getContainer();
        if (typeof is_visible === 'undefined') {
            throw Error("Expected is_visible to be provided as first arg as bool");
        }
        this.is_visible.set(!!is_visible);
        this.render(complete_callback);
    },

    /**
     * @param {String|Step|null} step id or object or null to clear current
     */
    go: function(step){
        if (step === null) {
            this.current.set(null);
            return;
        }
        var index = this.indexOf(step);
        if(index === -1){
            throw new Error('Step does not exist');
        }
        var old_step = this.getCurrent();
        var new_step = this.items[index];
        this.current.set(new_step);
    },

    /**
     * Advance the current step to the next, or to the first if we haven't gone anywhere yet
     */
    next: function(){
        var old_step = this.getCurrent();
        var new_step;
        var i;
        if(old_step){
            i = this.indexOf(old_step);
            i += 1;
            new_step = this.items[i];
        }
        else {
            new_step = this.items[0];
        }
        if(typeof new_step === 'undefined'){
            new_step = null;
        }
        this.current.set(new_step);
    },

    /**
     * Advance the step if we're at {step_id}
     * @param {String} step_id
     */
    nextIf: function(step_id){
        if(this.isCurrent(step_id)){
            this.next();
            return true;
        }
        return false;
    },

    /**
     *
     */
    hasNext: function(){
        return this.indexOf(this.getCurrent().id) < this.items.length-1;
    },

    getCurrent: function(){
        return this.current.get();
    },

    /**
     * @param {String|Step}
     * @returns {Boolean}
     */
    isCurrent: function(step){
        if( this.getCurrent() === null){
            return false;
        }
        if( typeof step === 'object' ){
            step = step.id;
        }
        return this.current.get().id === step;
    },

    /**
     * Alias for isCurrent
     */
    is: function(step){
        return this.isCurrent(step);
    },

    // @todo add checks for isBefore, isAfter?

    /**
     * Get the index of the provided named step
     * @param {String|Step} step
     * @returns {Number} Index of the step, else -1 if not exists
     */
    indexOf: function(step){
        var step_index;
        var step_id = (typeof step === 'string' ? step : step.id);
        $.each(this.items, function(i, step){
            if(step.id === step_id){
                step_index = i;
                return false;
            }
            else {
                return true;
            }
        });
        if(typeof step_index === 'undefined'){
            step_index = -1;
        }
        return step_index;
    },

    /**
     * @returns {Boolean}
     */
    has: function(step_id){
        return this.indexOf(step_id) !== -1;
    },

    /**
     * Remove a step from the items collection. Does nothing if the step does not exist.
     * @param {String} step_id
     * @returns {Object|null} The step object if it exists
     */
    remove: function(step_id){
        var index = this.indexOf(step_id);
        var removed = null;
        if(index !== -1){
            removed = this.splice(index, 1);
            if(this.current.get() === removed){
                this.current.set(null);
            }
        }
        this.render();
        return removed;
    },

    /**
     * Remove all steps
     */
    clear: function () {
        this.items.length = 0;
        this.current.set(null);
    }
};


/**
 * API for notifications
 */
Notifications = AuthModal.Notifications = {
    // @todo Allow template to be defined in the lightbox, perhaps in a <script type="text/template" class="notification">
    tpl:  '<div class="notifications message {{type}}">'
        + '<div class="notification-wrapper">'
        + '<div class="close"><a href="#close" class="close">Close</a></div>'
        + '<p class="notification">{{{notification}}}</p>'
        + '</div>'
        + '</div>',

    is_visible: new State(true),
    current: new State(),
    container: null,
    getContainer: function(){
        return this.container;
    },
    close_btn_selector: 'a.close',

    /**
     * Shortcut for show with type
     */
    info: function(msg){
        this.current.set({
            notification: msg,
            type: 'info'
        });
        this.show();
    },

    /**
     * Shortcut for show with type
     */
    error: function(msg){
        this.current.set({
            notification: msg,
            type: 'error'
        });
        this.show();
    },

    /**
     * Show a message with a specific type and resize the lightbox
     * @param {jQueryElement} Where the notification should be rendered
     * @param {Function} complete_callback When the element finishes rendering
     */
    render: function(complete_callback){
        var self = this;

        var trigger_rendered = function () {
            if (typeof complete_callback !== 'undefined') {
                complete_callback.apply(this, arguments);
            }
            $(self).trigger('rendered');
        };

        // If no container has been supplied, then we do nothing
        var container = this.getContainer();
        if (!container){
            return;
        }

        var is_visible = self.is_visible.get();

        // Populate the container with the notification
        if(!self.current.get()){
            is_visible = false;

            var inner_trigger_rendered = trigger_rendered;
            trigger_rendered = function () {
                container.empty();
                return inner_trigger_rendered.apply(this, arguments);
            };
        }
        else {
            if (container.is(':empty')) {
                container.hide();
            }

            var elm = $(Mustache.render(self.tpl, self.current.get()));
            elm.addClass(self.current.get().type);
            elm.find(self.close_btn_selector).click(function(e){
                self.hide();
                e.preventDefault();
            });
            container.empty().append(elm);
        }

        // We behave differently if parent is hidden since slideUp does nothing
        // if the element is not visible already.
        var is_parent_hidden = container.parent().is(':hidden');
        if (is_parent_hidden) {
            container.toggle(is_visible);
            trigger_rendered.call(container.get(0));
        }
        else {
            if (is_visible) {
                container.slideDown(trigger_rendered);
            }
            else {
                container.slideUp(trigger_rendered);
            }
        }
    },

    /**
     * Slide up the message and resize the lightbox
     * @param {Function} complete_callback
     */
    remove: function(complete_callback){
        this.current.set(null);
    },

    /**
     * @param {Function} complete_callback
     */
    hide: function (complete_callback) {
        this.toggle(false, complete_callback);
    },

    /**
     * @param {Function} complete_callback
     */
    show: function (complete_callback) {
        this.toggle(true, complete_callback);
    },

    /**
     * @param {Boolean} is_visible
     * @param {Function} complete_callback
     */
    toggle: function (is_visible, complete_callback) {
        if (typeof is_visible === 'undefined') {
            throw Error("Expected is_visible to be provided as first arg as bool");
        }
        this.is_visible.set(!!is_visible);
        this.render(complete_callback);
    }
};


// Exports
this.AuthModal = AuthModal;
}());
/**
 * Infinite Carousel
 *
 * @copyright	Fantasy Interactive
 * @author		Karl Stanton - Fantasy Interactive
 * @version		0.1 - Original prototype
 * @version		0.2 - Added class documentation
 * @version		0.3 - Added code documentation
 * @version		0.4 - Added check if number of items warrants pagination
 * @version		0.5	- Added page numbers
 * @version		0.6 - Added direction change for auto rotate after clicking next / back
 * @version		0.7 - Added rotation time
 *
 * The infinite carousels purpose is to allow the user to scroll through the
 * carousel items infinitely, no matter how many items exist in the item list.
 *
 * The carousel will not scroll if there are less items than
 *
 * oOptions.iPerPage + 1
 *
 * For example:
 *
 * If we are showing 5 items per 'page', there must be a minimum of 6 items for
 * the carousel to activate.
 *
 * Setup HTML:
 *
 * 	<div id="fullEpisodesSlider">
 * 		<ul>
 * 			<li> Item </li>
 * 			<li> Item </li>
 * 			<li> Item </li>
 *		</ul>
 * 	</div>
 *
 * Setup CSS:
 *
 * #fullEpisodesSlider {
 * 		overflow: hidden;
 * 		height: 360px;
 * 		width: 940px;
 * }
 *
 * #fullEpisodesSlider ul {
 * 		clear: both;
 * 		width: 10000px;
 * 		position: relative;
 * }
 *
 * #fullEpisodesSlider li {
 * 		width: 188px;
 * 		float: left;
 * 		position: relative;
 * }
 *
 *
 * Invoke by:
 *
 * var oClipsList = $('ul', '#fullEpisodesSlider').infiniteCarousel({
 * 		iPerPage: 5,
 * 		oNavigationNext: $('a.btnFullEpisodesNext', '#fullEpisodes'),
 * 		oNavigationPrevious: $('a.btnFullEpisodesPrevious', '#fullEpisodes'),
 *      iRotationTime: 1000
 * });
 *
 */
(function ($) {

	/**
	 * Infinite Carousel
	 * @constructor
	 * @param {Object} oCustomOptions
	 */
	$.fn.infiniteCarousel = function (oCustomOptions) {

		// Return if this element is not found
		if (!this[0]) {
			return;
		};

		var oDefaults = {
			iPerPage: 5,
			oNavigationNext: '',
			oNavigationPrevious: '',
			iAutoRotate: 0,
			iRotateDir: 1,
			bShowPageNumbers: false,
			iRotationTime: 500
		};

		var oOptions	= $.extend(oDefaults, oCustomOptions || {});

		// Global setInterval property
		var oRotateInterval;

		// Apply to each instance of the selected element(s)
		return this.each(function () {

			/**
			 * @globals
			 */
			var oContainer		= $(this),
				oItems			= oContainer.children(),
				iItemWidth		= oContainer.children(':first').outerWidth(),
				bAnimating		= false,
				iNumItems,
				iAnimationLength,
				oPageNumbers,

				// Buttons
				btnPrevious		= oOptions.oNavigationPrevious,
				btnNext			= oOptions.oNavigationNext,


			// The total number of items in the carousel
			iNumItems			= oItems.length;
			
			// Prepare the container
			oContainer.css({
				'position': 'relative',
				'left': 0
			});

			// If the number of items is less than or equal too the amount
			// required to view per page, then hide the pagination buttons
			// and exit the plugin
			if (iNumItems <= oOptions.iPerPage) {
				
				oOptions.oNavigationPrevious.hide();
				oOptions.oNavigationNext.hide();
				return;
				
			}
			// Kick off
			bindEvents();

			// If we are showing page numbers
			if (oOptions.bShowPageNumbers) {
				showPageNumbers();
			}

			setupAutoRotate();


			/**
			 * Binds events to the previous and next buttons, as well as
			 * stopping the carousel auto-rotate on hover
			 *
			 * @author Karl Stanton
			 */
			function bindEvents () {

				// Previous button
				btnPrevious.show().click(function (oEvent) {

					if (!bAnimating) {
						onPreviousNextClick(-1);
					}
					return false;

				}).hover(function () {
					clearInterval(oRotateInterval);
				}, function () {
					setupAutoRotate();
				});

				// Next button
				btnNext.show().click(function (oEvent) {

					if (!bAnimating) {
						onPreviousNextClick(1);
					}
					return false;

				}).hover(function () {
					clearInterval(oRotateInterval);
				}, function () {
					setupAutoRotate();
				});

				// Stop rotating on hover
				oContainer.hover(function () {
					clearInterval(oRotateInterval);
				}, function () {
					setupAutoRotate();
				});

			}

			/**
			 * Builds the page numbers elements out to the page. They will be placed
			 * between the "Previous" and the "Next" buttons.
			 * 
			 * @author Karl Stanton
			 */
			function showPageNumbers () {
			
				var sCurrent;
			
				// Begin the html build
				oPageNumbers = '<div class="pageNumbers">';
				
				// For each page, put in a page number
				for (var i = 0; i < iNumItems; i++) {
					
					if (i === 0) {
						sCurrent = ' class="current"';
					}
					else {
						sCurrent = '';
					}
					
					oPageNumbers += '<a href="#"' + sCurrent + '><span>' + (i + 1) + '</span></a>';
					
				}
				// Close the HTML
				oPageNumbers += '</div>';
				
				// Turn into jQuery object
				oPageNumbers = $(oPageNumbers);
				
				// Append after the Previous button (which will sit between
				// the previous button and the next button)
				oOptions.oNavigationPrevious.after(oPageNumbers);
				
				// Bind events to these anchors
				$('a', oPageNumbers).click(function () {
					
					var iSelfIndex		= $(this).index(),
						iCurrentIndex	= $('a.current', oPageNumbers).index(),
						iNewIndex		= iSelfIndex - iCurrentIndex;
					
					onPreviousNextClick(iNewIndex);
					
					return false;
					
				});
				
			}
			
			/**
			 * Selects the correct page number after a direction has been requested.
			 *
			 * @author Karl Stanton
			 * @param {Number} iDirection
			 */
			function moveSelectedPageNumber (iDirection) {
				
				// Get the currently selected element
				var oCurrent		= $('a.current', oPageNumbers),
					iCurrentIndex	= oCurrent.index();
				
				// If we're at the end, send it back to 0
				if (iCurrentIndex === (iNumItems - 1) && iDirection > 0) {
					iCurrentIndex = 0;
				}
				// If we're at the start, set to iNumItems - 1 (0 based)
				else if (iCurrentIndex === 0 && iDirection < 0) {
					iCurrentIndex = iNumItems - 1;
				}
				// Or just move it in the direction requested
				else {
					iCurrentIndex += iDirection;
				}
				
				oCurrent.removeClass('current');
				
				$('a:eq(' + (iCurrentIndex) + ')', oPageNumbers).addClass('current');
				
			}

			/**
			 * When the user clicks next or previous, depending on the iDirection
			 * the appropriate carousel logic will be applied. Please read the
			 * documentation above the iDirection condition for further detail.
			 *
			 * @author Karl Stanton
			 * @param {Number} iDirection
			 */
			function onPreviousNextClick (iDirection) {

				var iNewLeft,
					iOldLeft,
					iNumDistance;

				// Get the width and height set
				resetItemsAndWidth();

				// Update the Auto Rotate Direction
				oOptions.iRotateDir = iDirection;

				// We are animating, so prevent any further triggers
				bAnimating = true;
				
				// If the iDirection value is greater than 1, then we are jumping through
				// pages. We must then adjust our slicing parameters
				if (iDirection > 1 || iDirection < -1) {
					iNumDistance = iDirection;
				}
				else if (iDirection === 1) {
					iNumDistance = oOptions.iPerPage;
				}
				else if (iDirection === -1) {
					iNumDistance = -oOptions.iPerPage;
				}

				// Update the animation length variable
				iAnimationLength = iItemWidth * iNumDistance;
					
					
				// For when the user clicks Previous
				if (iDirection < 0) {
					iNewLeft = prepareCarouselPrevious(iNumDistance);
				}
				// For when the user clicks Next
				else {
					iNewLeft = prepareCarouselNext(iNumDistance);
				}
				
				// Set the page number
				if (oOptions.bShowPageNumbers) {
					moveSelectedPageNumber(iDirection);
				}
				
				// Animate the carousel
				oContainer.animate({
					left:	iNewLeft
				}, oOptions.iRotationTime, function () {

					// Depending on the direction, we must remove the old items from the DOM

					// Previous
					if (iDirection < 0) {

						// Remove anything that's greater than the original number
						// of items (-1 to get to 0 base indexing of elements)
						oItems.filter(':gt(' + (iNumItems - 1) + ')').remove();

					}
					// Next
					else {

						// Remove anything less than oOptions.iPerPage and adjust
						// the left position to return to 0
						oItems.filter(':lt(' + (iNumDistance) + ')').remove();
						oContainer.css('left', 0);

					}

					// Reset
					resetItemsAndWidth();

					// We have finished the animation process. So drop the flag
					bAnimating = false;

				});

			}

			/**
			 * Reset the oItems variable with the updated amount of children
			 * elements
			 *
			 * Adjust the width of the carousel
			 */
			function resetItemsAndWidth () {

				oItems = oContainer.children();
				
				// Reset the width and animation length if the width was never found at build
				if (iItemWidth === 0) {
					
					iItemWidth			= oContainer.children(':first').outerWidth();
					
					iAnimationLength	= iItemWidth * oOptions.iPerPage;
					
				}

				oContainer.width(oItems.length * iItemWidth);

			}

			/**
			 * Prepares the carousel for moving to the RIGHT
			 *
			 * Since the carousel is animating RIGHT, we need to make some
			 * adjustments to the carousel before we animate.
			 *
			 * We have to copy the last (iNumDistance) items and place
			 * them at the beginning of the carousel, then adjust the left
			 * position of the carousel to compensate for the new items.
			 *
			 * The user won't see the update happen, however the carousel
			 * will now have a cloned version of itself on the left hand side
			 * ready for animation.
			 *
			 * 1) Set our left variables based on the above activity
			 * 2) Clone the elements
			 * 3) Adjust the width and left position of the carousel
			 * 
			 * @author Karl Stanton
			 * @param {Number} iNumDistance
			 * @return {Number} iNewLeft
			 */
			function prepareCarouselPrevious (iNumDistance) {

				var iOldLeft,
					iNewLeft;
				// Set the current position
				iCurrentPosition = oContainer.position().left;

				// The old left position is the current position minus the
				// animation length
				iOldLeft		= iAnimationLength;

				// The new left position will be the current position
				iNewLeft		= 0;

				// Clone the elements
				oItems.filter(':first').before(oItems.slice(iNumDistance).clone(true));

				resetItemsAndWidth();

				oContainer.css('left', iOldLeft);

				return iNewLeft;
				
			}

			/**
			 * Prepares the carousel for moving to the LEFT
			 *
			 * Since the carousel is moving to the left, we have to copy
			 * the first (iNumDistance) items and place them after
			 * the last item.
			 *
			 * Since the left position will remain the same, all that's
			 * left to do is adjust the width of the carousel to compensate
			 * for the newly added items.
			 * 
			 * @author Karl Stanton
			 * @param {Number} iNumDistance
			 * @return {Number} iNewLeft
			 */
			function prepareCarouselNext (iNumDistance) {
				// The new left position is the current position minus
				// the length of animation length. So we just pass back
				// the animation length
				var iNewLeft	= -(iAnimationLength);

				// Copy the first oOptions.iPerPage items and append them to
				// the end of the carousel
				oItems.filter(':last').after(oItems.slice(0, iNumDistance).clone(true));
				resetItemsAndWidth();
				return iNewLeft;

			}

			/**
			 * Sets up the auto rotate timer
			 *
			 * @author Karl Stanton
			 */
			function setupAutoRotate () {

				// Auto Rotate?
				if (oOptions.iAutoRotate > 0) {
					oRotateInterval = setInterval(function(){
						onPreviousNextClick(oOptions.iRotateDir)
					}, oOptions.iAutoRotate);

				} else {
					oRotateInterval = setInterval(function () {}, oOptions.iAutoRotate);

				}

			}

		});

		return this;

	};

})(jQuery);





/**
 * Tooltip Plugin for Fox.com Prototype
 * 
 * @copyright Fantasy Interactive
 * @author Brian Fegan
 * @author Karl Stanton
 * @version 0.1 
 * @version 0.2 - Set tool tip into body instead of next to launching element
 * @version 0.3 - Refactored codebase
 * @version 0.4 - Documentation
 * @version 0.5 - Left and Right Boundary detection
 * @version 0.6 - New options hideDelay and hideOnScroll; custom events for showing and hiding the tool tip
 * 
 * @param {Object} oCustomOptions (See oDefaults)
 *  
 */
(function ($) {
	
	$.fn.toolTip = function (oCustomOptions) {
	
		// If this element is not found, return
		if (this.length == 0) {
			return this;
		};

		// Size of the border to show (small, large)	
		var oDefaults = { 
			width: 305, 				// width of the entire tooltip, including shadows
			orientation: 'horizontal',
			selector: null,
			displayDelay: 0,
			
			id:null,
			hideOnScroll: false,
			hideDelay: 10000,
			
			// needed for horizontal
			arrowAdjust: 29, 			// width or height of arrow for bottom placement math
			leftRightShadowPadding: 8,	// total width of left + right shadows of both sides of content box
			topShadowHeight: 9,			// height of top shadows in left/right floats. subtract to get repeating shadow
			bottomShadowHeight: 8,		// height of bottom shadows in left/right floats. subtract to get repeating shadow 
			toolTipLinkLeftPadding: 20	// padding of "i" icon to find true center of element
		};
		
		var oOptions = $.extend(oDefaults, oCustomOptions || {});
		
		var oToolTip,
			oToolTipPos = {},
			iSelfHeight,
			oSelf,
			bBelowTheFold,
			bAboveTheFold;
			iShadowWidth = oOptions.width,
			iDocumentTopThreshold = 10;
		
		// Loop through all passed in elements
		this.each(function () {
		
			oSelf = $(this);
			
			oToolTip = $(oOptions.selector);
			oToolTip.data('tooltip-id', oOptions.id);
		
			// If there is no tooltop, or the height of the selected element is less than the scroll top
			if (oToolTip.length <= 0 || (oSelf.offset().top - $(document).scrollTop()) < iDocumentTopThreshold) {
				return;
			}
			
			
			oToolTip.bind('mouseover mouseenter', function (event) {
				event.stopPropagation();
			});
			
			if (oOptions.orientation == 'horizontal') {
				oToolTip.addClass('toolTipBtmArrow');
			} else {
				//vertical orientation
				oToolTip.addClass('toolTipLeftArrow');
			}
			
			// get html of initial SEO markup
			// append proper left/right shadow marku
			// nest SEO markup in content box
			wrapDefaultMarkup();
			
			// set proper tooltip widths for outer box and content box
			// accounting for padding, borders, and shadows
			setToolTipWidth();
			
			// retrieve height
			// set height of vertical repeating shadows
			appendShadowsAndArrrow();

			// vars used for each tooltip, independant of
			// each separate mouseenter/mouseleave event
			iSelfHeight		= oSelf.outerHeight();
			
			oSelf.mouseenter(function () {				
				clearTimeout(oToolTip.timeout);
			});

			oSelf.mouseleave(function () {
			
				oToolTip.timeout = setTimeout(function () {
					hideToolTip();
				}, oOptions.hideDelay);
				
			});

			$(window).resize(function () {
				hideToolTip();
			});
			
			if(oOptions.hideOnScroll){
				$(window).scroll(function(){
					hideToolTip();
				});
			}
			
			showToolTip();
		
		});
		
		/**
		 * Shows the tooltip. 
		 * 
		 * 1) Get the new tooltip position
		 * 2) Set the CSS
		 * 3) After a time out, set the tooltip to visible
		 * 4) Animate the tooltip in
		 * 
		 * @author Karl Stanton
		 * 
		 */
		function showToolTip () {
			
			if(oToolTip.timeout) {
				clearTimeout(oToolTip.timeout);
			}

			oToolTipPos = getToolTipPos();

			// Set the properties
			oToolTip
				.hide() //ensure hidden before coming into view
				.css({
					'top': oToolTipPos.top,
					'left': oToolTipPos.left,
					'visibility': 'hidden'
				});
			
			// Set a delay before showing the tooltip
			setTimeout(function () {
				oToolTip.css('visibility', 'visible').show();
				oToolTip.trigger('tooltip:show', oOptions);
	
				// Animate the tooltip
				oToolTip.animate({
					top: oToolTipPos.top - 5
				}, 150,
					// After animation, bind event listeners to tooltip itself
					function () {
						oToolTip.trigger('tooltip:shown', oOptions);
						oToolTip.mouseenter(function () {
							
							clearTimeout(oToolTip.timeout);
							
						}).mouseleave(function () {
	
							oToolTip.timeout = setTimeout(function () {
								hideToolTip();
							}, oOptions.hideDelay);
							
						});
						
					}
				);
				
			}, oOptions.displayDelay);			
			
		}
		
		/**
		 * Hides the tooltip by removing it from the DOM
		 * 
		 * @author Karl Stanton
		 */
		function hideToolTip () {
			oToolTip.trigger('tooltip:hide', oOptions);
			oToolTip.fadeOut(100, function () {
				oToolTip.remove();
				oToolTip.trigger('tooltip:hidden', oOptions);
			});
			
		}
		
		/**
		 * Get html of initial SEO markup, set width of outer box,append proper left/right shadow markup
		 * 
		 * @author Brian Fegan
		 */
		function wrapDefaultMarkup () {
			
			var strToolTipHtml = $(oToolTip).html(),
				strToolTipWrapLeft,
				strToolTipWrapRight;
				
			
			if (oOptions.orientation == 'horizontal') {
				strToolTipWrapLeft = '<div class="left"><div class="top"></div><div class="repeat"></div><div class="bottom"></div></div><div class="content">';
			} else {
				//vertical
				strToolTipWrapLeft = '<div class="left"><div class="top"></div><div class="repeat above"></div><div class="holder"></div><div class="repeat below"></div><div class="bottom"></div></div><div class="content">';
			}
			strToolTipWrapRight = '</div><div class="right"><div class="top"></div><div class="repeat"></div><div class="bottom"></div></div>';
			
			$(oToolTip).html(strToolTipWrapLeft + strToolTipHtml + strToolTipWrapRight);
			
		}
		
		/**
		 * Sets the width of the tooltip
		 * @author Brian Fegan
		 */
		function setToolTipWidth () {
			
			var oContentBox		= $('.content', oToolTip);
			var iContentPadding = parseInt($(oContentBox).css('padding-left'), 10) * 2; 
			var iContentBorders = parseInt($(oContentBox).css('border-left-width'), 10) * 2;
			
			if (isNaN(iContentPadding)){
				iContentPadding = 0;
			}
			if (isNaN(iContentBorders)){
				iContentBorders = 0;
			}
			
			$(oContentBox).width(oOptions.width - iContentPadding - iContentBorders);
			$(oToolTip).width(oOptions.width + oOptions.leftRightShadowPadding);
			
		}
		
		/**
		 * Resets the position of the vertical arrow
		 * 
		 * @author Brian Fegan
		 * @param {Object} startTop
		 */
		function resetVerticalArrrow (startTop) {
		
			var iContentHeight	= $('.content', oToolTip).outerHeight();
			var iArrowTop		= iContentHeight - oOptions.arrowAdjust - (iContentHeight / 2);
			var iShadowTop,
				iShadowBottom;
			
			if (bBelowTheFold || bAboveTheFold) {
				iArrowTop		= oSelf.offset().top + (oSelf.height() / 2) - startTop;
			}
			
			// Helps keep the arrow in line
			if (iArrowTop < oOptions.arrowAdjust) {
				iArrowTop = oOptions.arrowAdjust;
			}
			
			iShadowTop 		= iArrowTop - oOptions.topShadowHeight;
			iShadowBottom	= iContentHeight - iShadowTop - oOptions.arrowAdjust - oOptions.topShadowHeight;
			if (iShadowBottom < 0){
				iShadowBottom = 0;
			}			
			
			$('.arrow', oToolTip).css('top', iArrowTop + 'px');
			
			$('.left .repeat.above', oToolTip).height(iShadowTop);
			$('.left .repeat.below', oToolTip).height(iShadowBottom);
		
		}
		
		/**
		 * Appends the shadows and the arrow to the tooltip
		 * 
		 * @author Brian Fegan
		 * 
		 */
		function appendShadowsAndArrrow () {
			
			//get proper content width/height
			var oContentBox		= $('.content', oToolTip);
			var iContentHeight	= $(oContentBox).outerHeight();
			var iContentWidth	= $(oContentBox).outerWidth();
			
			//append arrow and bottom shadows
			$(oToolTip).append('<div class="arrow"></div>');
			$(oToolTip).append('<div class="shadow right"></div><div class="shadow left"></div>');
			
			$('.shadow.left', oToolTip).css({
				'top':  iContentHeight + 'px'
			});
			$('.shadow.right', oToolTip).css({
				'top':  iContentHeight + 'px'
			});
			
			// Horizontal Orientation
			if (oOptions.orientation == 'horizontal') {
				
				// Set height of both repeating vertical shadows
				$('.repeat', oToolTip).height(iContentHeight - oOptions.bottomShadowHeight);
				
				// Place arrow
				$('.arrow', oToolTip).css('left', Math.ceil((iShadowWidth / 2) + (oOptions.leftRightShadowPadding / 2)) + 'px');

			// Vertical orientation				
			} else {
				
				// Set height of right shadow, leave space for vertical arrow on left
				$('.holder', oToolTip).height(oOptions.arrowAdjust);
				$('.right .repeat', oToolTip).height(iContentHeight - oOptions.topShadowHeight);
				
				// Get bottom shadow width, with no accounting for arrow
				$('.shadow.left', oToolTip).width(Math.ceil(iContentWidth / 2));
				$('.shadow.right', oToolTip).width(Math.floor(iContentWidth / 2));
				
			}
			
		}
		
		/**
		 * Calculates the tool tip document placement
		 * 
		 * @author Brian Fegan
		 * @return {Object} oToolTipPos
		 */
		function getToolTipPos () {
			
			var iToolTipHeight  	= oToolTip.height(),
				oSelfPos 			= oSelf.offset(),
				iSelfWidth 			= oSelf.width(),
				iSelfTopMargin  	= parseInt(oSelf.css('margin-top'), 0);
				
			if (isNaN(iSelfTopMargin)) {
				iSelfTopMargin = 0;
			}
				
			// Horizontal orientation
			if (oOptions.orientation == 'horizontal') {
				
				oToolTipPos.top		= oSelfPos.top + iSelfTopMargin - iToolTipHeight - 5;

				// See if the top bounds are within the visible document boundaries		
				oToolTipPos.top		= getStartTopHorizontal();

				oToolTipPos.left	= Math.ceil(((iSelfWidth / 2) + oOptions.toolTipLinkLeftPadding + oSelfPos.left) - (oOptions.width / 2));
				
				oToolTipPos.left	= getStartLeftHorizontal();
				
			// Vertical orientation 
			} else {
				
				// TODO - Make sense of this equation!
				// +5 = animated -5 pixel offset on tooltip show
				oToolTipPos.top		= oSelfPos.top - Math.ceil((iToolTipHeight - iSelfHeight) / 2) + oOptions.bottomShadowHeight + 5;
				
				// See if the top bounds are within the visible document boundaries				
				oToolTipPos.top		= getStartTopVertical();

				oToolTipPos.left	= oSelfPos.left + iSelfWidth - Math.ceil(iSelfWidth * 0.1);
				
				oToolTipPos.left	= getStartLeftVertical();
				
				
			}
			
			return oToolTipPos;
			
		}
		
		/**
		 * Determines if the popup will be pushed over to the left of the browser
		 * 
		 * @author Karl Stanton
		 */
		function getStartLeftHorizontal () {
			
			var iWindowWidth	= $(window).width(),
				oSelfPos 		= oSelf.offset(),
				iEndLeft		= oToolTipPos.left + oToolTip.outerWidth(),
				iStartLeft		= oToolTipPos.left,
				iMiddlePoint,
				iShadowLeft;
			
			// Tool tip will be out of bounds -> move it to be flush right with the window
			if (iEndLeft > iWindowWidth) {
			
				iStartLeft = iWindowWidth - oToolTip.outerWidth();
				
				// Preset the left position so we have use of it later
				oToolTip.css('left', iStartLeft);
				
				// Find the middle point of the element
				iMiddlePoint = oSelfPos.left + (oSelf.width() / 2);
				
				// Set the arrow position to be the middle of the element we are hovering over
				iArrowLeft = iMiddlePoint - oToolTip.offset().left;
				
				// If it's too far to the right, then set it to the absolute right position of the tooltip
				if (iArrowLeft > oToolTip.outerWidth()) {
					iArrowLeft = oToolTip.outerWidth() - oOptions.arrowAdjust - oOptions.leftRightShadowPadding;
				}

				// Get bottom shadow width, with no accounting for arrow
				iShadowLeft = iArrowLeft - 4;
				
				if (bBelowTheFold) {
					iShadowLeft += oOptions.arrowAdjust;
				}
				
				// Set the shadow widths
				$('.shadow.left', oToolTip).width(Math.ceil(iShadowLeft));
				$('.shadow.right', oToolTip).width(Math.floor(oToolTip.outerWidth() - (iArrowLeft + oOptions.arrowAdjust) - 4));
				
				
				// Set the width
				$('.arrow', oToolTip).css({
					'left': iArrowLeft
				});
			
			}
			
			return iStartLeft;
			
		}
		
		/**
		 * 
		 * @author Karl Stanton
		 */
		function getStartLeftVertical () {
			
			var iWindowWidth	= $(window).width(),
				oSelfPos 		= oSelf.offset(),
				iEndLeft		= oToolTipPos.left + oToolTip.outerWidth(),
				iStartLeft		= oToolTipPos.left;
			
			// Store a reference of the HTML for each side
			var leftShadow		= $('div.left', oToolTip).html();
			var rightShadow		= $('div.right', oToolTip).html();
			
			// Tool tip will be out of bounds -> flip it to the right
			if (iEndLeft > iWindowWidth) {
				
				// Flip to right
				iStartLeft = oSelfPos.left - oToolTip.outerWidth();
				
				// Update the shadows
				$('div.left:eq(0)', oToolTip).html(rightShadow);
				
				oToolTip.removeClass('toolTipLeftArrow').addClass('toolTipRightArrow');
				
			}
			
			return iStartLeft;
			
		}
		
		/**
		 * Gets the top position of the tooltip if it's in the horizontal orientation
		 * 
		 * @author Karl Stanton
		 * @return {Integer} iStartTop
		 */
		function getStartTopHorizontal () {
			
			var iScrollTop = $(document).scrollTop();
			var iStartTop	= oToolTipPos.top;
			
			// Show the horizontal tool tip below oSelf
			if (iStartTop < iScrollTop) {
				
				oToolTip.removeClass('toolTipBtmArrow');
				oToolTip.addClass('toolTipTopArrow');
				
				iStartTop = oSelf.offset().top + iSelfHeight + oOptions.arrowAdjust;
				
				bBelowTheFold	= true;
			
			// Show the horizontal tool tip above oSelf
			} else {
				
				oToolTip.removeClass('toolTipTopArrow');
				oToolTip.addClass('toolTipBtmArrow');
				
				iShadowWidth = iShadowWidth - oOptions.arrowAdjust;
								
				iStartTop = iStartTop - 15;
				
				$('.arrow', oToolTip).css('left', Math.ceil((iShadowWidth / 2) + (oOptions.leftRightShadowPadding / 2)) + 'px');
				
				bAboveTheFold = true;
				
			}
			
			
			// Reset bottom shadows dependant of where the arrow is
			$('.shadow.left', oToolTip).width(Math.ceil(iShadowWidth / 2));
			$('.shadow.right', oToolTip).width(Math.floor(iShadowWidth / 2));
			
			return iStartTop;
			
		}		
		
		/**
		 * Gets the top position of the tooltip if it's in the vertical orientation
		 * 
		 * @author Karl Stanton
		 * @return {Integer} iStartTop
		 */
		function getStartTopVertical () {
				
			var iScrollTop		= $(document).scrollTop();
			var iWindowHeight	= $(window).height();
			var iStartTop		= oToolTipPos.top;
			var iBottomTooltip	= iStartTop + getToolTipHeight();
			var iBottomWindow	= iScrollTop + iWindowHeight;
			
			// Move the tip above the fold
			if (iBottomTooltip > iBottomWindow) {
				
				iStartTop		= iBottomWindow - getToolTipHeight();
				
				bBelowTheFold	= true;
				
				resetVerticalArrrow(iStartTop);
				
				return iStartTop;
				
			}
			
			// Will the tool tip load above the top of the browser?
			if (iStartTop < iScrollTop) {
				
				iStartTop = iScrollTop + iDocumentTopThreshold;
				
				bAboveTheFold	= true;

				// Set the position of the arrow for the vertical tool tip
				resetVerticalArrrow(iStartTop);
				
				return iStartTop;
				
			}
			
			// Set the position of the arrow for the vertical tool tip
			resetVerticalArrrow(iStartTop);
			
			return iStartTop;
			
		}
		
		/**
		 * Returns the height of the tooltip including top-margin
		 * 
		 * @author Karl Stanton
		 * @return {Integer} iHeight
		 */
		function getToolTipHeight () {

			var iHeight		= oToolTip.outerHeight();
			var iMarginTop	= parseInt(oSelf.css('margin-top'), 10);
			
			// If the result isn't a number, then force it to be 0
			if (isNaN(iMarginTop)) {
				iMarginTop = 0;
			}
			
			iHeight			= iHeight + iMarginTop;
			
			return iHeight;

		}
		
		// Make this plugin chainable by returning the object passed in
		return this;
		
	}
	
})(jQuery);
/**
 * ToolTip Creator for FOX
 *
 * @author Weston Ruter (X-Team)
 *
 * Uses refactored from:
 * @author Karl Stanton
 * 
 */

(function($){

if (typeof window.FBCFOX === 'undefined') {
    window.FBCFOX = {};
}

window.FBCFOX.ToolTip = {
	
	_visibleToolTipsById: {},
	_count: 0,
	_templates: {
		defaultToolTip:'<div class="toolTip"></div>'
	},
	
	show: function(options){
		options = jQuery.extend({
			// Options not used by jQuery.fi.toolTip (and thus then deleted)
			context: null,
			data: null,
			src: null,
			selector: null, // if null, then creates div.toolTip
			
			// Options passed through to jQuery.fi.toolTip
			id: null,
			width: 320,
			orientation: 'vertical',
			hideOnScroll: false,
			displayDelay: 500, //if Ajax, factor in include load time
			hideDelay: 250
		}, options || {});
		
		// Abort if it's currently being displayed
		if(options.id && this._visibleToolTipsById[options.id]){
			return;
		}
		
		var $context = $(options.context);
		if(options.selector){
			options.selector = $(options.selector);
		}
		
		if(!options.selector){
			$('div.toolTip').remove(); //There can only be one! {hah!}
			options.selector = $(this._templates.defaultToolTip);
			$('body').append(options.selector);
		}
		
		var displayTooltip = function(){
			// Options that shouldn't be passed along to jQuery.fn.toolTip
			delete options.context;
			delete options.data;
			delete options.src;
			delete options.element;
			//delete options.id;
			
			$context.toolTip(options);
		}
		
		++this._count;
		
		// Load data provided
		if(options.data){
			options.selector.empty().append(options.data);
			displayTooltip();
		}
		// Load data via a URL
		else if(options.src){
			var count = this._count;
			var loadStartTime = new Date().valueOf();
			var request = $.ajax({
				url: options.src,
				type: 'get',
				//timeout: options.displayDelay,
				success: function(data, textStatus, xhr){
					// Abort if no data (aborted) or if another tooltip was opened while this one is loading
					if(count != window.FBCFOX.ToolTip._count){
						return;
					}
					
					// Abort if no data or if the XHR was aborted
					if(!data || xhr.readyState != 4){
						return;
					}
					
					// Factor in the time it takes to load the data in the displayDelay
					options.displayDelay -= (new Date().valueOf() - loadStartTime);
					if(options.displayDelay < 0){
						options.displayDelay = 0;
					}
					options.selector.empty().append(data);
					
					displayTooltip();
				}
			});
			
			$context.mouseleave(function(){
				request.abort();
			});
		}
		
	}
};


/**
 * Keep track of the tooltips that are currently displayed
 */
$(document).bind('tooltip:hide', function(event, options){
	if(options.id){
		delete window.FBCFOX.ToolTip._visibleToolTipsById[options.id];
	}
});

$(document).bind('tooltip:show', function(event, options){
	if(options.id){
		window.FBCFOX.ToolTip._visibleToolTipsById[options.id] = true;
	}
});

})(jQuery);/*!
 * mustache.js - Logic-less {{mustache}} templates with JavaScript
 * http://github.com/janl/mustache.js
 */
var Mustache = (typeof module !== "undefined" && module.exports) || {};

(function (exports) {

  exports.name = "mustache.js";
  exports.version = "0.5.0-dev";
  exports.tags = ["{{", "}}"];
  exports.parse = parse;
  exports.compile = compile;
  exports.render = render;
  exports.clearCache = clearCache;

  // This is here for backwards compatibility with 0.4.x.
  exports.to_html = function (template, view, partials, send) {
    var result = render(template, view, partials);

    if (typeof send === "function") {
      send(result);
    } else {
      return result;
    }
  };

  var _toString = Object.prototype.toString;
  var _isArray = Array.isArray;
  var _forEach = Array.prototype.forEach;
  var _trim = String.prototype.trim;

  var isArray;
  if (_isArray) {
    isArray = _isArray;
  } else {
    isArray = function (obj) {
      return _toString.call(obj) === "[object Array]";
    };
  }

  var forEach;
  if (_forEach) {
    forEach = function (obj, callback, scope) {
      return _forEach.call(obj, callback, scope);
    };
  } else {
    forEach = function (obj, callback, scope) {
      for (var i = 0, len = obj.length; i < len; ++i) {
        callback.call(scope, obj[i], i, obj);
      }
    };
  }

  var spaceRe = /^\s*$/;

  function isWhitespace(string) {
    return spaceRe.test(string);
  }

  var trim;
  if (_trim) {
    trim = function (string) {
      return string == null ? "" : _trim.call(string);
    };
  } else {
    var trimLeft, trimRight;

    if (isWhitespace("\xA0")) {
      trimLeft = /^\s+/;
      trimRight = /\s+$/;
    } else {
      // IE doesn't match non-breaking spaces with \s, thanks jQuery.
      trimLeft = /^[\s\xA0]+/;
      trimRight = /[\s\xA0]+$/;
    }

    trim = function (string) {
      return string == null ? "" :
        String(string).replace(trimLeft, "").replace(trimRight, "");
    };
  }

  var escapeMap = {
    "&": "&amp;",
    "<": "&lt;",
    ">": "&gt;",
    '"': '&quot;',
    "'": '&#39;'
  };

  function escapeHTML(string) {
    return String(string).replace(/&(?!\w+;)|[<>"']/g, function (s) {
      return escapeMap[s] || s;
    });
  }

  /**
   * Adds the `template`, `line`, and `file` properties to the given error
   * object and alters the message to provide more useful debugging information.
   */
  function debug(e, template, line, file) {
    file = file || "<template>";

    var lines = template.split("\n"),
        start = Math.max(line - 3, 0),
        end = Math.min(lines.length, line + 3),
        context = lines.slice(start, end);

    var c;
    for (var i = 0, len = context.length; i < len; ++i) {
      c = i + start + 1;
      context[i] = (c === line ? " >> " : "    ") + context[i];
    }

    e.template = template;
    e.line = line;
    e.file = file;
    e.message = [file + ":" + line, context.join("\n"), "", e.message].join("\n");

    return e;
  }

  /**
   * Looks up the value of the given `name` in the given context `stack`.
   */
  function lookup(name, stack, defaultValue) {
    if (name === ".") {
      return stack[stack.length - 1];
    }

    var names = name.split(".");
    var lastIndex = names.length - 1;
    var target = names[lastIndex];

    var value, context, i = stack.length, j, localStack;
    while (i) {
      localStack = stack.slice(0);
      context = stack[--i];

      j = 0;
      while (j < lastIndex) {
        context = context[names[j++]];

        if (context == null) {
          break;
        }

        localStack.push(context);
      }

      if (context && target in context) {
        value = context[target];
        break;
      }
    }

    // If the value is a function, call it in the current context.
    if (typeof value === "function") {
      value = value.call(localStack[localStack.length - 1]);
    }

    if (value == null)  {
      return defaultValue;
    }

    return value;
  }

  function renderSection(name, stack, callback, inverted) {
    var buffer = "";
    var value =  lookup(name, stack);

    if (inverted) {
      // From the spec: inverted sections may render text once based on the
      // inverse value of the key. That is, they will be rendered if the key
      // doesn't exist, is false, or is an empty list.
      if (value == null || value === false || (isArray(value) && value.length === 0)) {
        buffer += callback();
      }
    } else if (isArray(value)) {
      forEach(value, function (value) {
        stack.push(value);
        buffer += callback();
        stack.pop();
      });
    } else if (typeof value === "object") {
      stack.push(value);
      buffer += callback();
      stack.pop();
    } else if (typeof value === "function") {
      var scope = stack[stack.length - 1];
      var scopedRender = function (template) {
        return render(template, scope);
      };
      buffer += value.call(scope, callback(), scopedRender) || "";
    } else if (value) {
      buffer += callback();
    }

    return buffer;
  }

  /**
   * Parses the given `template` and returns the source of a function that,
   * with the proper arguments, will render the template. Recognized options
   * include the following:
   *
   *   - file     The name of the file the template comes from (displayed in
   *              error messages)
   *   - tags     An array of open and close tags the `template` uses. Defaults
   *              to the value of Mustache.tags
   *   - debug    Set `true` to log the body of the generated function to the
   *              console
   *   - space    Set `true` to preserve whitespace from lines that otherwise
   *              contain only a {{tag}}. Defaults to `false`
   */
  function parse(template, options) {
    options = options || {};

    var tags = options.tags || exports.tags,
        openTag = tags[0],
        closeTag = tags[tags.length - 1];

    var code = [
      'var buffer = "";', // output buffer
      "\nvar line = 1;", // keep track of source line number
      "\ntry {",
      '\nbuffer += "'
    ];

    var spaces = [],      // indices of whitespace in code on the current line
        hasTag = false,   // is there a {{tag}} on the current line?
        nonSpace = false; // is there a non-space char on the current line?

    // Strips all space characters from the code array for the current line
    // if there was a {{tag}} on it and otherwise only spaces.
    var stripSpace = function () {
      if (hasTag && !nonSpace && !options.space) {
        while (spaces.length) {
          code.splice(spaces.pop(), 1);
        }
      } else {
        spaces = [];
      }

      hasTag = false;
      nonSpace = false;
    };

    var sectionStack = [], updateLine, nextOpenTag, nextCloseTag;

    var setTags = function (source) {
      tags = trim(source).split(/\s+/);
      nextOpenTag = tags[0];
      nextCloseTag = tags[tags.length - 1];
    };

    var includePartial = function (source) {
      code.push(
        '";',
        updateLine,
        '\nvar partial = partials["' + trim(source) + '"];',
        '\nif (partial) {',
        '\n  buffer += render(partial,stack[stack.length - 1],partials);',
        '\n}',
        '\nbuffer += "'
      );
    };

    var openSection = function (source, inverted) {
      var name = trim(source);

      if (name === "") {
        throw debug(new Error("Section name may not be empty"), template, line, options.file);
      }

      sectionStack.push({name: name, inverted: inverted});

      code.push(
        '";',
        updateLine,
        '\nvar name = "' + name + '";',
        '\nvar callback = (function () {',
        '\n  return function () {',
        '\n    var buffer = "";',
        '\nbuffer += "'
      );
    };

    var openInvertedSection = function (source) {
      openSection(source, true);
    };

    var closeSection = function (source) {
      var name = trim(source);
      var openName = sectionStack.length != 0 && sectionStack[sectionStack.length - 1].name;

      if (!openName || name != openName) {
        throw debug(new Error('Section named "' + name + '" was never opened'), template, line, options.file);
      }

      var section = sectionStack.pop();

      code.push(
        '";',
        '\n    return buffer;',
        '\n  };',
        '\n})();'
      );

      if (section.inverted) {
        code.push("\nbuffer += renderSection(name,stack,callback,true);");
      } else {
        code.push("\nbuffer += renderSection(name,stack,callback);");
      }

      code.push('\nbuffer += "');
    };

    var sendPlain = function (source) {
      code.push(
        '";',
        updateLine,
        '\nbuffer += lookup("' + trim(source) + '",stack,"");',
        '\nbuffer += "'
      );
    };

    var sendEscaped = function (source) {
      code.push(
        '";',
        updateLine,
        '\nbuffer += escapeHTML(lookup("' + trim(source) + '",stack,""));',
        '\nbuffer += "'
      );
    };

    var line = 1, c, callback;
    for (var i = 0, len = template.length; i < len; ++i) {
      if (template.slice(i, i + openTag.length) === openTag) {
        i += openTag.length;
        c = template.substr(i, 1);
        updateLine = '\nline = ' + line + ';';
        nextOpenTag = openTag;
        nextCloseTag = closeTag;
        hasTag = true;

        switch (c) {
        case "!": // comment
          i++;
          callback = null;
          break;
        case "=": // change open/close tags, e.g. {{=<% %>=}}
          i++;
          closeTag = "=" + closeTag;
          callback = setTags;
          break;
        case ">": // include partial
          i++;
          callback = includePartial;
          break;
        case "#": // start section
          i++;
          callback = openSection;
          break;
        case "^": // start inverted section
          i++;
          callback = openInvertedSection;
          break;
        case "/": // end section
          i++;
          callback = closeSection;
          break;
        case "{": // plain variable
          closeTag = "}" + closeTag;
          // fall through
        case "&": // plain variable
          i++;
          nonSpace = true;
          callback = sendPlain;
          break;
        default: // escaped variable
          nonSpace = true;
          callback = sendEscaped;
        }

        var end = template.indexOf(closeTag, i);

        if (end === -1) {
          throw debug(new Error('Tag "' + openTag + '" was not closed properly'), template, line, options.file);
        }

        var source = template.substring(i, end);

        if (callback) {
          callback(source);
        }

        // Maintain line count for \n in source.
        var n = 0;
        while (~(n = source.indexOf("\n", n))) {
          line++;
          n++;
        }

        i = end + closeTag.length - 1;
        openTag = nextOpenTag;
        closeTag = nextCloseTag;
      } else {
        c = template.substr(i, 1);

        switch (c) {
        case '"':
        case "\\":
          nonSpace = true;
          code.push("\\" + c);
          break;
        case "\r":
          // Ignore carriage returns.
          break;
        case "\n":
          spaces.push(code.length);
          code.push("\\n");
          stripSpace(); // Check for whitespace on the current line.
          line++;
          break;
        default:
          if (isWhitespace(c)) {
            spaces.push(code.length);
          } else {
            nonSpace = true;
          }

          code.push(c);
        }
      }
    }

    if (sectionStack.length != 0) {
      throw debug(new Error('Section "' + sectionStack[sectionStack.length - 1].name + '" was not closed properly'), template, line, options.file);
    }

    // Clean up any whitespace from a closing {{tag}} that was at the end
    // of the template without a trailing \n.
    stripSpace();

    code.push(
      '";',
      "\nreturn buffer;",
      "\n} catch (e) { throw {error: e, line: line}; }"
    );

    // Ignore `buffer += "";` statements.
    var body = code.join("").replace(/buffer \+= "";\n/g, "");

    if (options.debug) {
      if (typeof console != "undefined" && console.log) {
        console.log(body);
      } else if (typeof print === "function") {
        print(body);
      }
    }

    return body;
  }

  /**
   * Used by `compile` to generate a reusable function for the given `template`.
   */
  function _compile(template, options) {
    var args = "view,partials,stack,lookup,escapeHTML,renderSection,render";
    var body = parse(template, options);
    var fn = new Function(args, body);

    // This anonymous function wraps the generated function so we can do
    // argument coercion, setup some variables, and handle any errors
    // encountered while executing it.
    return function (view, partials) {
      partials = partials || {};

      var stack = [view]; // context stack

      try {
        return fn(view, partials, stack, lookup, escapeHTML, renderSection, render);
      } catch (e) {
        throw debug(e.error, template, e.line, options.file);
      }
    };
  }

  // Cache of pre-compiled templates.
  var _cache = {};

  /**
   * Clear the cache of compiled templates.
   */
  function clearCache() {
    _cache = {};
  }

  /**
   * Compiles the given `template` into a reusable function using the given
   * `options`. In addition to the options accepted by Mustache.parse,
   * recognized options include the following:
   *
   *   - cache    Set `false` to bypass any pre-compiled version of the given
   *              template. Otherwise, a given `template` string will be cached
   *              the first time it is parsed
   */
  function compile(template, options) {
    options = options || {};

    // Use a pre-compiled version from the cache if we have one.
    if (options.cache !== false) {
      if (!_cache[template]) {
        _cache[template] = _compile(template, options);
      }

      return _cache[template];
    }

    return _compile(template, options);
  }

  /**
   * High-level function that renders the given `template` using the given
   * `view` and `partials`. If you need to use any of the template options (see
   * `compile` above), you must compile in a separate step, and then call that
   * compiled function.
   */
  function render(template, view, partials) {
    return compile(template)(view, partials);
  }

})(Mustache);/*global jQuery: true, FoxEnv: true */

var ConfirmPopup = (function ($) {
    var env = (typeof FoxEnv !== 'undefined')
        ? FoxEnv.getCurrent()
        : 'production';

    return {
        config: {
            tpl_url: '/_app/components/confirm_popup-1.0/confirm_popup.html'
        },

        create: function (content, callback) {
            var self = this;
            var bindClick = function (event) {
                event.preventDefault();

                var button = $(this);
                var name = button.attr('rel');
                var result = (name === 'ok');

                $.colorbox.close();
                callback(result);
            };

            $.colorbox({
                href : this.config.tpl_url,
                scrolling : false,
                opacity : 0.85,
                onComplete : function() {
                    var $el = $('#colorbox .modal-box');
                    $el.find('a.btn').on('click',bindClick);
                    $el.find('.question').prepend(content);
                    self.resize($el);
                },
                onCleanup : function() {
                    $('#colorbox .modal-box').find('a.btn').off('click',bindClick);
                }
            });
        },

        resize : function($el) {
            var dimensions = {
                innerHeight: $el.outerHeight(),
                innerWidth: $el.outerWidth()
            };

            try {
                $.colorbox.resize(dimensions);
            }
            catch(e){
                console.warn(e);
            }
        }
    };
}(jQuery));
(function(context){
    var names = ["log", "debug", "info", "warn", "error", "assert", "dir",
    "dirxml", "group", "groupEnd", "time", "timeEnd", "count", "trace",
    "profile", "profileEnd"];

    if (typeof context.console === 'undefined'){
        context.console = {};
    }

    var i;
    var noop = function(){};
    for(i = 0; i < names.length; i += 1){
        if (typeof context.console[names[i]] === 'undefined') {
            context.console[names[i]] = noop;
        }
    }
}(window));
/*global jQuery */
/**
 * @author Weston Ruter <weston@x-team.com>
 */

function RequestBatch(callbacks) {
    this.callbacks = callbacks || [];
}
RequestBatch.prototype.add = function (callback) {
    this.callbacks.push(callback);
};
RequestBatch.prototype.process = function (complete_callback) {
    var batch = this;
    var remaining_count = this.callbacks.length;
    var failures = [];
    var successes = [];
    jQuery.each(this.callbacks, function (i, callback) {
        callback.call(batch, function(err, data) {
            remaining_count -= 1;
            if (err) {
                failures.push(err);
            }
            else {
                successes.push(data);
            }
            if (remaining_count === 0) {
                complete_callback(failures, successes);
            }
        });
    });
};
/*
    http://www.JSON.org/json2.js
    2011-10-19

    Public Domain.

    NO WARRANTY EXPRESSED OR IMPLIED. USE AT YOUR OWN RISK.

    See http://www.JSON.org/js.html


    This code should be minified before deployment.
    See http://javascript.crockford.com/jsmin.html

    USE YOUR OWN COPY. IT IS EXTREMELY UNWISE TO LOAD CODE FROM SERVERS YOU DO
    NOT CONTROL.


    This file creates a global JSON object containing two methods: stringify
    and parse.

        JSON.stringify(value, replacer, space)
            value       any JavaScript value, usually an object or array.

            replacer    an optional parameter that determines how object
                        values are stringified for objects. It can be a
                        function or an array of strings.

            space       an optional parameter that specifies the indentation
                        of nested structures. If it is omitted, the text will
                        be packed without extra whitespace. If it is a number,
                        it will specify the number of spaces to indent at each
                        level. If it is a string (such as '\t' or '&nbsp;'),
                        it contains the characters used to indent at each level.

            This method produces a JSON text from a JavaScript value.

            When an object value is found, if the object contains a toJSON
            method, its toJSON method will be called and the result will be
            stringified. A toJSON method does not serialize: it returns the
            value represented by the name/value pair that should be serialized,
            or undefined if nothing should be serialized. The toJSON method
            will be passed the key associated with the value, and this will be
            bound to the value

            For example, this would serialize Dates as ISO strings.

                Date.prototype.toJSON = function (key) {
                    function f(n) {
                        // Format integers to have at least two digits.
                        return n < 10 ? '0' + n : n;
                    }

                    return this.getUTCFullYear()   + '-' +
                         f(this.getUTCMonth() + 1) + '-' +
                         f(this.getUTCDate())      + 'T' +
                         f(this.getUTCHours())     + ':' +
                         f(this.getUTCMinutes())   + ':' +
                         f(this.getUTCSeconds())   + 'Z';
                };

            You can provide an optional replacer method. It will be passed the
            key and value of each member, with this bound to the containing
            object. The value that is returned from your method will be
            serialized. If your method returns undefined, then the member will
            be excluded from the serialization.

            If the replacer parameter is an array of strings, then it will be
            used to select the members to be serialized. It filters the results
            such that only members with keys listed in the replacer array are
            stringified.

            Values that do not have JSON representations, such as undefined or
            functions, will not be serialized. Such values in objects will be
            dropped; in arrays they will be replaced with null. You can use
            a replacer function to replace those with JSON values.
            JSON.stringify(undefined) returns undefined.

            The optional space parameter produces a stringification of the
            value that is filled with line breaks and indentation to make it
            easier to read.

            If the space parameter is a non-empty string, then that string will
            be used for indentation. If the space parameter is a number, then
            the indentation will be that many spaces.

            Example:

            text = JSON.stringify(['e', {pluribus: 'unum'}]);
            // text is '["e",{"pluribus":"unum"}]'


            text = JSON.stringify(['e', {pluribus: 'unum'}], null, '\t');
            // text is '[\n\t"e",\n\t{\n\t\t"pluribus": "unum"\n\t}\n]'

            text = JSON.stringify([new Date()], function (key, value) {
                return this[key] instanceof Date ?
                    'Date(' + this[key] + ')' : value;
            });
            // text is '["Date(---current time---)"]'


        JSON.parse(text, reviver)
            This method parses a JSON text to produce an object or array.
            It can throw a SyntaxError exception.

            The optional reviver parameter is a function that can filter and
            transform the results. It receives each of the keys and values,
            and its return value is used instead of the original value.
            If it returns what it received, then the structure is not modified.
            If it returns undefined then the member is deleted.

            Example:

            // Parse the text. Values that look like ISO date strings will
            // be converted to Date objects.

            myData = JSON.parse(text, function (key, value) {
                var a;
                if (typeof value === 'string') {
                    a =
/^(\d{4})-(\d{2})-(\d{2})T(\d{2}):(\d{2}):(\d{2}(?:\.\d*)?)Z$/.exec(value);
                    if (a) {
                        return new Date(Date.UTC(+a[1], +a[2] - 1, +a[3], +a[4],
                            +a[5], +a[6]));
                    }
                }
                return value;
            });

            myData = JSON.parse('["Date(09/09/2001)"]', function (key, value) {
                var d;
                if (typeof value === 'string' &&
                        value.slice(0, 5) === 'Date(' &&
                        value.slice(-1) === ')') {
                    d = new Date(value.slice(5, -1));
                    if (d) {
                        return d;
                    }
                }
                return value;
            });


    This is a reference implementation. You are free to copy, modify, or
    redistribute.
*/

/*jslint evil: true, regexp: true */

/*members "", "\b", "\t", "\n", "\f", "\r", "\"", JSON, "\\", apply,
    call, charCodeAt, getUTCDate, getUTCFullYear, getUTCHours,
    getUTCMinutes, getUTCMonth, getUTCSeconds, hasOwnProperty, join,
    lastIndex, length, parse, prototype, push, replace, slice, stringify,
    test, toJSON, toString, valueOf
*/


// Create a JSON object only if one does not already exist. We create the
// methods in a closure to avoid creating global variables.

var JSON;
if (!JSON) {
    JSON = {};
}

(function () {
    'use strict';

    function f(n) {
        // Format integers to have at least two digits.
        return n < 10 ? '0' + n : n;
    }

    if (typeof Date.prototype.toJSON !== 'function') {

        Date.prototype.toJSON = function (key) {

            return isFinite(this.valueOf())
                ? this.getUTCFullYear()     + '-' +
                    f(this.getUTCMonth() + 1) + '-' +
                    f(this.getUTCDate())      + 'T' +
                    f(this.getUTCHours())     + ':' +
                    f(this.getUTCMinutes())   + ':' +
                    f(this.getUTCSeconds())   + 'Z'
                : null;
        };

        String.prototype.toJSON      =
            Number.prototype.toJSON  =
            Boolean.prototype.toJSON = function (key) {
                return this.valueOf();
            };
    }

    var cx = /[\u0000\u00ad\u0600-\u0604\u070f\u17b4\u17b5\u200c-\u200f\u2028-\u202f\u2060-\u206f\ufeff\ufff0-\uffff]/g,
        escapable = /[\\\"\x00-\x1f\x7f-\x9f\u00ad\u0600-\u0604\u070f\u17b4\u17b5\u200c-\u200f\u2028-\u202f\u2060-\u206f\ufeff\ufff0-\uffff]/g,
        gap,
        indent,
        meta = {    // table of character substitutions
            '\b': '\\b',
            '\t': '\\t',
            '\n': '\\n',
            '\f': '\\f',
            '\r': '\\r',
            '"' : '\\"',
            '\\': '\\\\'
        },
        rep;


    function quote(string) {

// If the string contains no control characters, no quote characters, and no
// backslash characters, then we can safely slap some quotes around it.
// Otherwise we must also replace the offending characters with safe escape
// sequences.

        escapable.lastIndex = 0;
        return escapable.test(string) ? '"' + string.replace(escapable, function (a) {
            var c = meta[a];
            return typeof c === 'string'
                ? c
                : '\\u' + ('0000' + a.charCodeAt(0).toString(16)).slice(-4);
        }) + '"' : '"' + string + '"';
    }


    function str(key, holder) {

// Produce a string from holder[key].

        var i,          // The loop counter.
            k,          // The member key.
            v,          // The member value.
            length,
            mind = gap,
            partial,
            value = holder[key];

// If the value has a toJSON method, call it to obtain a replacement value.

        if (value && typeof value === 'object' &&
                typeof value.toJSON === 'function') {
            value = value.toJSON(key);
        }

// If we were called with a replacer function, then call the replacer to
// obtain a replacement value.

        if (typeof rep === 'function') {
            value = rep.call(holder, key, value);
        }

// What happens next depends on the value's type.

        switch (typeof value) {
        case 'string':
            return quote(value);

        case 'number':

// JSON numbers must be finite. Encode non-finite numbers as null.

            return isFinite(value) ? String(value) : 'null';

        case 'boolean':
        case 'null':

// If the value is a boolean or null, convert it to a string. Note:
// typeof null does not produce 'null'. The case is included here in
// the remote chance that this gets fixed someday.

            return String(value);

// If the type is 'object', we might be dealing with an object or an array or
// null.

        case 'object':

// Due to a specification blunder in ECMAScript, typeof null is 'object',
// so watch out for that case.

            if (!value) {
                return 'null';
            }

// Make an array to hold the partial results of stringifying this object value.

            gap += indent;
            partial = [];

// Is the value an array?

            if (Object.prototype.toString.apply(value) === '[object Array]') {

// The value is an array. Stringify every element. Use null as a placeholder
// for non-JSON values.

                length = value.length;
                for (i = 0; i < length; i += 1) {
                    partial[i] = str(i, value) || 'null';
                }

// Join all of the elements together, separated with commas, and wrap them in
// brackets.

                v = partial.length === 0
                    ? '[]'
                    : gap
                    ? '[\n' + gap + partial.join(',\n' + gap) + '\n' + mind + ']'
                    : '[' + partial.join(',') + ']';
                gap = mind;
                return v;
            }

// If the replacer is an array, use it to select the members to be stringified.

            if (rep && typeof rep === 'object') {
                length = rep.length;
                for (i = 0; i < length; i += 1) {
                    if (typeof rep[i] === 'string') {
                        k = rep[i];
                        v = str(k, value);
                        if (v) {
                            partial.push(quote(k) + (gap ? ': ' : ':') + v);
                        }
                    }
                }
            } else {

// Otherwise, iterate through all of the keys in the object.

                for (k in value) {
                    if (Object.prototype.hasOwnProperty.call(value, k)) {
                        v = str(k, value);
                        if (v) {
                            partial.push(quote(k) + (gap ? ': ' : ':') + v);
                        }
                    }
                }
            }

// Join all of the member texts together, separated with commas,
// and wrap them in braces.

            v = partial.length === 0
                ? '{}'
                : gap
                ? '{\n' + gap + partial.join(',\n' + gap) + '\n' + mind + '}'
                : '{' + partial.join(',') + '}';
            gap = mind;
            return v;
        }
    }

// If the JSON object does not yet have a stringify method, give it one.

    if (typeof JSON.stringify !== 'function') {
        JSON.stringify = function (value, replacer, space) {

// The stringify method takes a value and an optional replacer, and an optional
// space parameter, and returns a JSON text. The replacer can be a function
// that can replace values, or an array of strings that will select the keys.
// A default replacer method can be provided. Use of the space parameter can
// produce text that is more easily readable.

            var i;
            gap = '';
            indent = '';

// If the space parameter is a number, make an indent string containing that
// many spaces.

            if (typeof space === 'number') {
                for (i = 0; i < space; i += 1) {
                    indent += ' ';
                }

// If the space parameter is a string, it will be used as the indent string.

            } else if (typeof space === 'string') {
                indent = space;
            }

// If there is a replacer, it must be a function or an array.
// Otherwise, throw an error.

            rep = replacer;
            if (replacer && typeof replacer !== 'function' &&
                    (typeof replacer !== 'object' ||
                    typeof replacer.length !== 'number')) {
                throw new Error('JSON.stringify');
            }

// Make a fake root object containing our value under the key of ''.
// Return the result of stringifying the value.

            return str('', {'': value});
        };
    }


// If the JSON object does not yet have a parse method, give it one.

    if (typeof JSON.parse !== 'function') {
        JSON.parse = function (text, reviver) {

// The parse method takes a text and an optional reviver function, and returns
// a JavaScript value if the text is a valid JSON text.

            var j;

            function walk(holder, key) {

// The walk method is used to recursively walk the resulting structure so
// that modifications can be made.

                var k, v, value = holder[key];
                if (value && typeof value === 'object') {
                    for (k in value) {
                        if (Object.prototype.hasOwnProperty.call(value, k)) {
                            v = walk(value, k);
                            if (v !== undefined) {
                                value[k] = v;
                            } else {
                                delete value[k];
                            }
                        }
                    }
                }
                return reviver.call(holder, key, value);
            }


// Parsing happens in four stages. In the first stage, we replace certain
// Unicode characters with escape sequences. JavaScript handles many characters
// incorrectly, either silently deleting them, or treating them as line endings.

            text = String(text);
            cx.lastIndex = 0;
            if (cx.test(text)) {
                text = text.replace(cx, function (a) {
                    return '\\u' +
                        ('0000' + a.charCodeAt(0).toString(16)).slice(-4);
                });
            }

// In the second stage, we run the text against regular expressions that look
// for non-JSON patterns. We are especially concerned with '()' and 'new'
// because they can cause invocation, and '=' because it can cause mutation.
// But just to be safe, we want to reject all unexpected forms.

// We split the second stage into 4 regexp operations in order to work around
// crippling inefficiencies in IE's and Safari's regexp engines. First we
// replace the JSON backslash pairs with '@' (a non-JSON character). Second, we
// replace all simple value tokens with ']' characters. Third, we delete all
// open brackets that follow a colon or comma or that begin the text. Finally,
// we look to see that the remaining characters are only whitespace or ']' or
// ',' or ':' or '{' or '}'. If that is so, then the text is safe for eval.

            if (/^[\],:{}\s]*$/
                    .test(text.replace(/\\(?:["\\\/bfnrt]|u[0-9a-fA-F]{4})/g, '@')
                        .replace(/"[^"\\\n\r]*"|true|false|null|-?\d+(?:\.\d*)?(?:[eE][+\-]?\d+)?/g, ']')
                        .replace(/(?:^|:|,)(?:\s*\[)+/g, ''))) {

// In the third stage we use the eval function to compile the text into a
// JavaScript structure. The '{' operator is subject to a syntactic ambiguity
// in JavaScript: it can begin a block or an object literal. We wrap the text
// in parens to eliminate the ambiguity.

                j = eval('(' + text + ')');

// In the optional fourth stage, we recursively walk the new structure, passing
// each name/value pair to a reviver function for possible transformation.

                return typeof reviver === 'function'
                    ? walk({'': j}, '')
                    : j;
            }

// If the text is not JSON parseable, then a SyntaxError is thrown.

            throw new SyntaxError('JSON.parse');
        };
    }
}());
/*global jQuery */

/**
 * @author Weston Ruter <weston@x-team.com>
 */

var JSONPRequest = (function(){
    var $ = jQuery;
    var request_count = 0;

    var jreq = function (args) {
        $.extend(this,
            {
                timeout: 10*1000,
                params: {},
                callback_param: 'jsonp',
                endpoint: null
            },
            args || {}
        );

        if (!this.endpoint) {
            throw new Error('You must supply an endpoint');
        }
        if (this.endpoint.indexOf('?') !== -1) {
            throw new Error('Do not pass query params in the endpoint URL.');
        }
    };
    jreq.manifest = {};
    jreq.prototype.cleanup = function () {
        var script = document.getElementById(this._callback_id);
        if (script) {
            script.parentNode.removeChild(script);
        }
        delete jreq.manifest[this._callback_id];
    };
    jreq.prototype.send = function (callback) {
        var request = this;
        request_count += 1;
        request._callback_id = 'jsonprequest_callback_' + String(request_count);

        // Construct the JSON-P request
        var script;
        script = document.createElement('script');
        var params = $.extend({}, request.params);
        params[request.callback_param] = 'JSONPRequest.manifest.' + request._callback_id;
        script.src = request.endpoint + '?' + $.param(params);
        script.id = request._callback_id;
        script.type = 'text/javascript';
        script.async = request;

        // Abort the JSONP request once we've timed out
        request.has_timed_out = false;
        var timeout_id = setTimeout(
            function () {
                request.has_timed_out = true;
                var err = new Error('JSONP request timed out for ' + script.src);
                request.cleanup();
                callback(err);
            },
            this.timeout
        );

        jreq.manifest[this._callback_id] = function (response) {
            try {
                clearTimeout(timeout_id);
                request.cleanup();
                callback(null, response);
            }
            catch (err) {
                callback(err);
            }
        };

        // Initiate the request
        document.body.appendChild(script);
    };

    return jreq;
}());
var FoxEnv = {
    _envs: [
        'dev',
        'staging',
        'production'
    ],
    getCurrent: function(){
        var matches = (location.search + location.hash).toLowerCase().match(new RegExp('\\benv=(' + this._envs.join('|') + ')\\b'));
        if(matches){
            return matches[1];
        }
        else if(/local|foxqa|foxdev/.test(location.host)){
            return 'dev';
        }
        else if(/foxstg|authstg|auth.staging/.test(location.host)){
            return 'staging';
        }
        else {
            return 'production';
        }
    }
};
/*global AdobePass, ConfirmPopup, FoxEnv, FoxPlayer, State, Mustache, jQuery */

var VideoAuth = (function ($) {
    // ----
    // states

    var screen_id = new State();
    var is_signed_in = new State(false);

    var self;
    self = {
        version: 2.1,
        screen_id: screen_id,
        is_signed_in: is_signed_in,
        screens: {},

        setup: function () {
            var self = this;

            // make sure we only call this once
            this.setup = function () {};

            this._setupAdobePass();
            this._setupOmniture();
            this._setupStates();

            if (typeof FoxPlayer === 'undefined') {
                throw new Error('Expected FoxPlayer to be loaded.');
            }

            // add a class for the screen-set so we can have custom css
            var screen_set_id = this.getScreenSetId();
            $('body').addClass('auth_screen_set-' + (screen_set_id || 'default'));

            this._renderLoadingScreen();

            VideoAuth.Modal.is_open.watch(function (is_open) {
                if (typeof FoxPlayer !== 'undefined' && is_open) {
                    FoxPlayer.togglePause(is_open);
                }
                if ($.cookie('hasJustShownMvpdLogin') == 1 && !is_open) {
                    $.cookie('hasJustShownMvpdLogin',0);
                    location.reload();
                }
            });

        },

        _setupAdobePass: function () {
            var self = this;

            $(AdobePass).on('accessEnablerEmbedError', function () {
                VideoAuth.error = null;
                VideoAuth.showScreen('oops','Error');
            });

            $(AdobePass).on('adobePassError', function (e, error) {
                var mvpd_id = VideoAuth.mvpds.getSelected();
                VideoAuth.omniture.notify('auth-error',{
                    prop72: mvpd_id,
                    eVar72: mvpd_id,
                    prop73: error.message,
                    eVar73: error.message
                });
                VideoAuth.showScreen('auth-error','Authentication Error');
            });

            $(AdobePass).on('tempPassExpired', function (event) {
                if (FoxPlayer.isAuthRequired()) {
                    VideoAuth.omniture.notifyOnce('self-pass-expired',{
                        prop72: VideoAuth.mvpds.tempPass,
                        eVar72: VideoAuth.mvpds.tempPass
                    });
                    VideoAuth.showScreen('self-pass-expired','Authentication Error');
                    $.cookie("tempPassUsed", 1);
                }
            });

            AdobePass.init('fbc-fox');
        },

        _setupOmniture: function () {
            screen_id.when('select-provider', function () {
                VideoAuth.omniture.notifyOnce('select-provider');
            });

            screen_id.when('please-sign-in-again', function () {
                VideoAuth.omniture.notifyOnce('please-sign-in-again');
            });

            screen_id.when('self-pass', function () {
                VideoAuth.omniture.notifyOnce('self-pass',VideoAuth.mvpds.tempPass);
            });

            if (typeof FoxEnv !== 'undefined' && FoxEnv.getCurrent() === 'dev') {
                VideoAuth.omniture.startLogging();
            }
        },

        _setupDomEvents: function (elm) {
            $(elm).on('click', "a[href*='#state:']", function (e) {
                e.preventDefault();
                var prefix = '#state:';
                var name = this.hash.substr(prefix.length);
                VideoAuth.showScreen(name, $(this).text());
            });
        },

        _setupStates: function () {
            is_signed_in.watch(function (value) {
                // AdobePass still authed but cookies have been cleared or don't match, update from AdobePass
                var apselected = AdobePass.getSelectedMvpd();
                if (value === true && (VideoAuth.mvpds.getSelected() == null || VideoAuth.mvpds.getSelected() !== apselected)) {
                    VideoAuth.mvpds.rememberSelection(apselected);
                }
                $('body').toggleClass('unlock', value); // toggles the ui lock icons
            });
        },

        _renderLoadingScreen: function() {
            // to be considered 'signed in', we need AdobePass authentication
            if ($('#player .loading').length == 0) {
                var message = VideoAuth._getLoadingMessage();
                var message_html = message ? '<span>' + message + '</span>' : '';
                $('#player').append($('<div class="loading">' + message_html + '</div>'));
            }
        },

        // ----

        confirmLogout: function (callback) {
            self._loadTemplateById('confirm_logout', function (err, tpl) {
                var mvpd_id = VideoAuth.mvpds.getSelected();
                if (!mvpd_id) {
                    throw new Error('Expected VideoAuth.mvpds.getSelected() to return an mvpd.');
                }
                VideoAuth.mvpds.load(mvpd_id, function (err, info) {
                    var tpl_vars = {
                        mvpd: info
                    };

                    VideoAuth.omniture.notify('confirm-logout',VideoAuth.mvpds.getSelected());
                    ConfirmPopup.create(Mustache.render(tpl, tpl_vars), callback);
                });
            });
        },

        startAuthFlow: function (video) {
            // display the video player overlay
            self.loadScreen('video-auth-overlay', function (err, elm) {
                if (err) {
                    throw err;
                }

                if (!!video) {
                    elm.find('.modalEpiTitle').text(video.name);
                    elm.find('.modalAired').text(video.airdate);
                }

                VideoAuth.omniture.notifyOnce('video-auth-overlay');
                if ($('.video-auth.signin-overlay').length > 0) {
                    $('.video-auth.signin-overlay').replaceWith(elm);
                }
                else {
                    $('#player').append(elm);
                }
                $('#player .loading').hide();
                $('.video-auth.signin-overlay').show();
            });
        },

        logout: function () {
            $.cookie('fox_omniture',JSON.stringify({
                    prop72: '',
                    eVar72: ''
            }), { path: '/', domain: '.fox.com'});
            AdobePass.logout();
        },

        // ----
        // screens

        /**
         * @wiki VideoAuthABTest:How is the screen set ID chosen?
         */
        getScreenSetId: function () {
            // querystring override
            if (location.href.indexOf('auth-ab=') > 0) {
                return (location.href.indexOf('auth-ab=a') > 0) ? 'a' : 'b';
            }

            // look for a global flag
            var flag = (window.AB && window.AB.video_auth_screen_set);
            if (flag) {
                return (flag === 'a') ? 'a' : 'b';
            }

            return null;
        },

  	    _getLoadingMessage: function () {
	        var id = self.getScreenSetId();
	        switch (id) {
	            case 'a':
                    return 'Just a minute, we\'re working on it&hellip;';
                break;

                case 'b':
                    return 'This will just take a minute&hellip;';
                break;

        	    default:
                    return 'Just a minute, we\'re working on it&hellip;';
		        break;
    	    }
	    },

        _loadTemplates: function (callback) {
            var data = {};
            var screen_set_id = self.getScreenSetId();
            if (screen_set_id) {
            data.set = screen_set_id;
            }

            $.ajax({
            url: '/_ajax/components/video_auth_templates.php',
            data: data,
            dataType: 'json',
            success: function (res) {
                if (!res) {
                return callback(new Error('invalid auth templates'));
                }

                callback(null, res);
            },
            error: function () {
                callback(new Error('failed loading templates'));
            }
            });
        },

        _loadTemplateById: function (id, callback) {
            self._loadTemplates(function (err, templates) {
                if (err) { return callback(err); }

                var tpl = templates[id];
                if (!tpl) { return callback(new Error('invalid auth template: ' + id)); }
                callback(null, tpl);
            });
        },

        showScreen: function (name,title) {
            this.screen_id.set(name);
            VideoAuth.Modal.switchScreen(name,title);
        },

        loadScreen: function (id, callback) {
            self._loadTemplateById(id, function (err, tpl) {
                if (err) { return callback(err); }

                try {
                    if (VideoAuth.screens[id] && VideoAuth.screens[id].load) {
                        VideoAuth.screens[id].load(tpl, function(err,elm) {
                            self._setupDomEvents(elm);
                            callback(err,elm);
                        });
                    }
                    else {
                        var elm = $(tpl);
                        self._setupDomEvents(elm);
                        callback(null,elm);
                    }
                }
                catch(err){
                    callback(err);
                }
            });
        }
    };

    return self;
}(jQuery));

VideoAuth.Modal = (function ($) {
    var self;
    self = {
        is_open: new State(false),
        is_complete: new State(false),
        isOpen: function(){
            return this.is_open.get();
        },

        holder: null,

        title: new State('Sign In'),
        content: new State(),

        setTitle: function (title) {
            this.title.set(title);
        },

        setContent: function(id) {
            var self = this;
            VideoAuth.loadScreen(id,function(err, elm) {
                if (err) {
                    VideoAuth.error = 'Video authentication is not available at this time. Please try again later.';
                    VideoAuth.showScreen('oops','Error');
                }
                else {
                    self.content.set(elm);
                }
            });
        },

        /**
         * Open the modal with the relevant settings, and populate the lightbox
         */
        open: function(id,title) {
            var self = this;
            if (self.isOpen()) {
                return;
            }
            self.is_open.set(true);

            function onchange_title(title) {
                self.holder.find('h1.title:first').text(title || '');
                self.resize();
            }

            function onchange_content(content) {
                self.holder.find('div.content:first').empty().append(content);
                self.resize();
            }

            VideoAuth._loadTemplateById('template', function (err, tpl) {
                if (err) { return console.error(err); }

                if (!self.holder) {
                    self.holder = $(tpl);
                }


                self.setTitle(title);
                self.setContent(id);

                jQuery.colorbox({
                    html: self.holder,
                    scrolling: false,
                    opacity: 0.75,
                    onOpen: function(){
                        self.title.watch(onchange_title);
                        self.content.watch(onchange_content);
                    },
                    onComplete: function(){
                        setTimeout(function() {
                            self.resize();
                        },0);
                        self.is_complete.set(true);
                    },
                    onCleanup: function(){
                        self.is_complete.set(false);
                        self.title.unwatch(onchange_title);
                        self.content.unwatch(onchange_content);
                    },
                    onClosed: function(){
                        self.content.set(null);
                        self.is_open.set(false);
                        if (VideoAuth.screen_id.get() === 'self-pass') {
                            VideoAuth.mvpds.rememberSelection(null);
                        }
                    }
                });
            });
        },

        switchScreen: function(id,title){
            if (!this.isOpen()) {
                this.open(id,title);
            }
            else {
                if (!!this.holder) {
                    this.holder.find('.header > div button').remove();
                }
                this.setTitle(title);
                this.setContent(id);
            }
        },

        /**
         * Alias for remove()
         */
        close: function(){
            this.remove();
        },

        /**
         * Remove the colorbox if it is for the auth modal
         */
        remove: function(){
            if (this.isOpen()) {
                this.is_open.set(false);
                this.is_complete.set(false);
                jQuery.colorbox.close();
            }
        },

        /**
         * Resize the modal according the dimensions of the contents
         */
        resize: function(){
            if (!this.is_complete.get()) {
                return;
            }

            var dimensions = {
                innerHeight: this.holder.outerHeight(),
                innerWidth: this.holder.outerWidth()
            };

            try {
                jQuery.colorbox.resize(dimensions);
            }
            catch(e){
                console.warn(e);
            }
        }
    };

    return self;

}(jQuery));

/**
 * This is a hack to handle actions within iframe content we don't have control over.
 * Which will still work regardless of version of auth component being used. (None of the
 * shows use auth-1.0 anymore).
 */
window.AUTH = (function ($) {
    var self;
    self = {
        checkProviderResponse: function (hash) {
            if (hash.match(/action=/)) {
                var prefix = 'action=';
                var name = hash.substr(prefix.length);
                if (name === 'cancel') {
                    VideoAuth.Modal.close();
                }
                VideoAuth.showScreen(name, 'Sign In');
            }
        },

        playedObject: null,
        updatePage: function () {
            console.warn('AUTH.updatePage() no longer does anything, as instead the page listens for when to update via the FoxPlayer "loadVideo" event.');
        },
        play: function () {
            FoxPlayer.load(this.playedObject);
        }
    };

    return self;
}(jQuery));

jQuery(function () {

    var s_sess = jQuery.cookie('s_sess');
    var authToken = jQuery.cookie('authToken');
    var wasAuthenticated = jQuery.cookie('VideoAuthWasAuthenticated');

    VideoAuth.setup();

    /**
     * tealium fires a page view event which needs to send mvpd information if
     * the guest is logged in to their mvpd.  onAdobePassAuthCheck is an event
     * tealium checks for if the page contains VideoAuth so that there is time
     * for AdobePass.isAuthenticated to return and update session info.
     */
    AdobePass.isAuthenticated(function (is_auth) {
        var fox_omniture = {};
        var params = [];

        VideoAuth.is_signed_in.set(is_auth);

        // Setup params for omniture and tealium calls

        if (is_auth && VideoAuth.screen_id.get() !== 'auth-error') {
            var mvpd_id = VideoAuth.mvpds.getSelected();

            // event35 - login
            // event95 - page view

            // If not previously authenticated, send the login event to tealium
            // We do it this way because on AdobePass login, the page reloads
            // before we can fire the event
            if (wasAuthenticated != null && wasAuthenticated == 0) {
                jQuery.cookie('VideoAuthWasAuthenticated', 1, { path: '/', domain: '.fox.com'});
                jQuery.cookie('AuthedWithMvpd',1, { path: '/', domain: '.fox.com'});
                // set the cookie for telenium to send the mvpd data and member info
                fox_omniture = jQuery.extend({
                    prop72: mvpd_id,
                    eVar72: mvpd_id
                },FoxId.omniture.getMemberValues());
                var events = 'event35,event95';
                params = [is_auth,events,{
                    prop17: "mvpd authentication:mvpd successfully authenticated",
                    eVar17: "mvpd authentication"
                }];
            } else {
                // set the cookie for tealium to send the mvpd data
                fox_omniture = {
                    prop72: mvpd_id,
                    eVar72: mvpd_id
                };
                var events = (s_sess == null && authToken != null) ? 'event95' : null;
                params = [is_auth,events];
            }
        } else {
            jQuery.cookie('VideoAuthWasAuthenticated', 0, { path: '/', domain: '.fox.com'});
            // empty the cookie for the tealium mvpd data
            fox_omniture = {
                    prop72: '',
                    eVar72: ''
            };
            var events = (s_sess == null && authToken != null) ? 'event95' : null;
            params = [is_auth,events];
        }

        // Update the fox_omniture cookie
        // Fire onAdobePassAuthCheck event
        jQuery.cookie('fox_omniture', JSON.stringify(fox_omniture), { path: '/', domain: '.fox.com'});
        jQuery(document).ready(function() { jQuery('body').trigger('onAdobePassAuthCheck',params); });
    });
});
/*global FoxEnv, jQuery, swfobject, State, VideoAuth */

var AdobePass = {
    requestor: null,
    providerDialogURL: null,
    _is_authenticated: null,
    error: new State(),
    previousSelectedProvider: null,
    tempPassSelected: false,

    /**
     *
     */
    init: function (requestor) {
        this.ensureNoFragmentInLocation();
        this.requestor = requestor;
        this.addEventListeners();
        this.AccessEnabler.load();
    },

    /**
     * In at least the Dish adapter for AdobePass, the iframe on authentication success
     * calls this function:
     * $ function reloadintop() { try{if (self.parent.frames.length != 0)self.parent.location="http://www.fox.com/example/full-episodes/demo/#test";}catch (Exception) {} }
     * If the current page's location contains a fragment as shown in the
     * example above, this function then results in a no-op as only the fragment
     * in the location is changed since the paths are the same. Therefore, we
     * must ensure that no fragment is in the page when we initialize it.
     * We could poll to ensure no fragments are added via setInterval.
     * AdobePass should look into a better way to do cross-domain frame communication.
     */
    ensureNoFragmentInLocation: function () {
        var fragmentPos = location.href.indexOf('#');
        if (fragmentPos !== -1) {
            location.href = location.href.substr(0, fragmentPos);
        }
    },

    /**
     * Tell if we're authenticated or not
     * @param {function} callback
     */
    isAuthenticated: function (callback) {
        var is_authenticated = this._is_authenticated;
        var $ = jQuery;

        // don't know yet - run the callback when we find out
        if (is_authenticated === null) {
            $(this).one('authenticationStatus', function (event, is_authenticated) {
                callback(is_authenticated);
            });
        }
        // send back the answer
        else {
            setTimeout(function () {
                callback(is_authenticated);
            });
        }
    },


    logout: function () {
        try {
            this.AccessEnabler.getInstance().logout();
            VideoAuth.omniture.notify('logout',VideoAuth.mvpds.getSelected());
        }
        catch (e) {}
    },

    getSelectedMvpd: function () {
        try {
            return this.AccessEnabler.getInstance().getSelectedProvider().MVPD;
        }
        catch(e){
            return null;
        }
    },

    /**
     * Tell if the user has previously selected an mvpd
     * @return {boolean}
     */
    hasPreviouslySelectedMvpd: function () {
        return !!this.getSelectedMvpd();
    },

    selectTempPass: function() {
        this.tempPassSelected = true;

        this.AccessEnabler.getInstance().setSelectedProvider(null);
        this.AccessEnabler.getInstance().getAuthentication();
    },

    clearTempPass: function() {
        this.tempPassSelected = false;
        this.previousSelectedProvider = null;
        this.AccessEnabler.getInstance().setSelectedProvider(null);
    },

    /**
     * addEventListeners
     *
     * Default callbacks for events.
     *
     * Supported events:
     * - accessEnablerLoaded
     * - authenticationStatus : Fired by isAuthenticated
     */
    addEventListeners: function () {
        var $ = jQuery;
        var that = this;

        $(this).on('accessEnablerLoaded', function (event, accessEnabler) {
            accessEnabler.setProviderDialogURL(that.providerDialogURL || 'none');
            accessEnabler.setRequestor(that.requestor);
            that.previousSelectedProvider = accessEnabler.getSelectedProvider();
            accessEnabler.checkAuthentication();
        });
        $(this).on('authenticationStatus', function (event, is_authenticated, code) {
            if (is_authenticated) {
                this.checkAuthorization();
            }
            else {
                if (that.tempPassSelected) {
                    if ($.cookie('tempPassUsed') == 1) {
                        that.tempPassSelected = false;
                        VideoAuth.mvpds.rememberSelection(null);
                        that.AccessEnabler.getInstance().setSelectedProvider(null);
                        that.triggerEvent('tempPassExpired');
                    }
                    else {
                        // Authenticate with temp pass
                        that.AccessEnabler.getInstance().setSelectedProvider(window.VideoAuth.mvpds.tempPass);
                        that.AccessEnabler.getInstance().getSelectedProvider();
                    }
                }
                else if (that.previousSelectedProvider && that.previousSelectedProvider.MVPD === window.VideoAuth.mvpds.tempPass) {
                    that.previousSelectedProvider = null;
                    VideoAuth.mvpds.rememberSelection(null);
                    that.AccessEnabler.getInstance().setSelectedProvider(null);
                    that.triggerEvent('tempPassExpired');
                }
            }
        });
    },

    /**
     * checkAuthorization
     *
     * Checks if the authenticated user is authorized for the specified content.
     * On success, _setToken will be invoked.
     * On failure, _tokenRequestFailed will be invoked.
     *
     * Dependencies:
     * - window.player must have the video object set which contains the video data returned by the content provider.
     *
     * @throws {Error} If MVPD or window.player object is missing.
     */
    checkAuthorization: function() {
        var $ = jQuery;
        var that = this;

        if (!!window.debug) {
            console.log('checkAuthorization()', arguments);
        }

        var checkAuthzForVideo = function (mvpd, video) {
            // MVPDs with support for parental control require an MRSS fragment
            if (mvpd.parentalAuthz) {
                var video_id = !!video.referenceId ? video.referenceId : video.id;
                var url = 'http://feed.theplatform.com/f/fox.com/mvpdauthzfox?byGuid='+video_id;
                var mrssCallback = function(xmlstr) {
                    var rss = $($.parseXML(xmlstr)).find('rss')[0];
                    var str = (new XMLSerializer()).serializeToString(rss);
                    that.AccessEnabler.checkAuthorization(str);
                };

                // IE support, use Microsoft XDR
                if (typeof window.XDomainRequest !== 'undefined') {
                    var xdr = new XDomainRequest();
                    xdr.open("get", url);
                    xdr.onload = function() {
                        if (typeof window.XMLSerializer !== 'undefined') {
                            mrssCallback(xdr.responseText);
                        }
                        else {
                            var str = xdr.responseText.replace(/<\?xml.*?\?>/,'');
                            that.AccessEnabler.checkAuthorization(str);
                        }
                    };
                    // These need to be defined and the send wrapped in a setTimeout,
                    // otherwise it may not work consistently.
                    xdr.onprogress = function(){ };
                    xdr.ontimeout = function(){ };
                    xdr.onerror = function () { };
                    setTimeout(function(){
                        xdr.send();
                    }, 0);
                } else {
                    $.ajax({
                        url : url,
                        dataType : 'text',
                        async : false,
                        success: function(data) {
                            mrssCallback(data);
                        }
                    });
                }
            }
            // Non-parental auth controlled MVPDs only checkAuthz for locked episodes
            else if (video.is_locked || (!!video.authEndDate && (new Date().valueOf() < parseInt(video.authEndDate, 10)))) {
                that.AccessEnabler.checkAuthorization(that.requestor);
            }
            else {
                that.triggerEvent('startPlayback', []);
            }
        };

        VideoAuth.mvpds.load(VideoAuth.mvpds.getSelected(), function (error, mvpd) {
            if (typeof mvpd === 'undefined') { throw Error('AdobePass: MVPD is missing'); }
            if (typeof window.player === 'undefined') { throw Error('AdobePass: Player is missing'); }

            // poll until video is ready
            var interval = setInterval(function () {
                if (typeof window.player.video === 'undefined') { return; }

                clearInterval(interval);
                checkAuthzForVideo(mvpd, window.player.video);
            }, 10);
        });        
    },

    /**
     * Called by window.setAuthenticationStatus()
     */
    _setAuthenticationStatus: function () {
        var args = Array.prototype.slice.call(arguments);
        args[0] = !!args[0]; // convert 1/0 to true/false

        // TODO, REMOVE FOR MERGE
        var getParameterByName = function(name) {
            name = name.replace(/[\[]/, "\\\[").replace(/[\]]/, "\\\]");
            var regex = new RegExp("[\\?&]" + name + "=([^&#]*)"),
                results = regex.exec(location.search);
            return results == null ? "" : decodeURIComponent(results[1].replace(/\+/g, " "));
        };

        if (getParameterByName('fake_expired')) {
            args[0] = false;
        }
        // END

        this._is_authenticated = args[0];
        this.triggerEvent('authenticationStatus', args);
    },

    /**
     * Called by window.tokenRequestFailed()
     */
    _tokenRequestFailed: function (resource_id, reason, provider_message) {
        console.error('tokenRequestFailed(%s, %s, %s)', resource_id, reason, provider_message);
        var error = new Error(reason);
        error.reason = reason;
        // Needed to handle ClearLeap which returns XML
        try {
            var xml = $.parseXML(provider_message);
            error.htmlProviderMessage = null;
        }
        catch(err) {
            error.htmlProviderMessage = provider_message;
        }
        this.error.set(error);
        this.triggerEvent('adobePassError', error);
    },

    /**
     * Called by window.setToken()
     */
    _setToken: function (token,expiry) {
        expiry = 1*expiry;
        var d = new Date(parseInt(expiry));
        document.cookie = [
            'authToken=', encodeURIComponent(token),
            '; expires=', d.toGMTString(),
            '; path=/',
            '; domain=.fox.com'
        ].join('');
        this.triggerEvent('setToken', [token]);
    },

    /**
     * Called by window.createIFrame
     */
    _createIFrame: function () {
        this.triggerEvent('createIFrame', arguments);
    },

    /**
     * Trigger an event along with a 'before-' event immediately before which can
     * prevent it from firing.
     * @todo This before/main/after construct should be separated out into a separate library if we want to keep it.
     */
    triggerEvent: function (type, params) {
        var mainEvent = new jQuery.Event(type);
        var beforeEvent = new jQuery.Event('before-' + type);
        var afterEvent = new jQuery.Event('after-' + type);

        // @todo uh, are we introducing circular references that will thwart GC?
        beforeEvent.refEvent = mainEvent;
        afterEvent.refEvent = mainEvent;
        mainEvent.beforeEvent = beforeEvent;
        mainEvent.afterEvent = afterEvent;

        var events = [beforeEvent, mainEvent, afterEvent];
        while( events.length ){
            var event = events.shift();
            jQuery(this).trigger(event, params);
            if(event.isImmediatePropagationStopped() || event.isDefaultPrevented()){
                return false;
            }
        }
        return true;
    }

};


/**
 *
 */
AdobePass.AccessEnabler = {
    flashPlayerVersion: '11.0', // Note: FoxPlayer.hasValidFlashVersion() relies on this being here

    stagingSrc: 'http://entitlement.auth-staging.adobe.com/entitlement/AccessEnabler.swf',
    productionSrc: 'http://entitlement.auth.adobe.com/entitlement/AccessEnabler.swf',
    placeholderElementId: 'accessEnablerPlaceholder',
    flashvars: {},
    id: 'accessEnabler',
    width: 1,
    height: 1,
    params: {
        menu: "false",
        quality: "high",
        AllowScriptAccess: "always",
        swliveconnect: "true",
        wmode: "transparent",
        bgcolor: 'black'
    },

    checkAuthorization: function (requestor) {
        this.getInstance().checkAuthorization(requestor);
    },

    /**
     * @throws {Error} If the instance hasn't been created yet. Wait for accessEnablerEmbedSuccess event.
     * @returns {DOMElement} The embedded SWF
     */
    getInstance: function () {
        var id = this.id;
        var instance = (!!document[id])
            ? document[id]
            : window[id];
        if(!instance){
            throw Error("Unable to locate AccessEnabler; wait until it has loaded or handle error.");
        }
        return instance;
    },

    /**
     *
     */
    load: function () {
        var self = this;
        var $ = jQuery;

        // Add the swf container element to the page
        // Get the AccessEnabler SWF source
        var apenv = $.cookie('AdobePassEnvironment');
        var src = ( typeof window.FoxEnv !== 'undefined' && (window.FoxEnv.getCurrent() === 'local' || window.FoxEnv.getCurrent() === 'development' || window.FoxEnv.getCurrent() === 'staging') )
            ? ((apenv != null && apenv == 'production') ? this.productionSrc : this.stagingSrc)
            : this.productionSrc;

        // Append a placeholder element that the SWF will be added to
        var placeholder = $('<span>');
        placeholder.attr('id', this.placeholderElementId);
        $('body').append(placeholder);

        // Embed the SWF
        var attributes = {
            id: this.id,
            name: this.id
        };
        swfobject.embedSWF(
            src,
            this.placeholderElementId,
            this.width,
            this.height,
            this.flashPlayerVersion,
            'expressInstall.swf',
            this.flashvars,
            this.params,
            attributes,
            function (e) {
                if(e.success){
                    AdobePass.triggerEvent('accessEnablerEmbedSuccess', [e]);
                }
                else {
                    var error = new Error('AccessEnabler failed to embed');
                    error.flashPlayerVersion = self.flashPlayerVersion;
                    AdobePass.error.set(error);
                    AdobePass.triggerEvent('accessEnablerEmbedError', [e]);
                }
            }
        );

        /**
         * AdobePass environment toggle
         * Only on dev and staging. Allows the user to set the AdobePass
         * environment with a cookie toggled by a button at the top right of
         * the screen where VideoAuth is loaded.
         **/
        if (typeof FoxEnv !== 'undefined' && FoxEnv.getCurrent() !== 'production' ) {
            var toggle = $('<button id="adobepass-toggle"></button>');
            var opp;
            if (apenv != null && apenv == 'production') {
                opp = 'stage';
                toggle.text('AdobePass: '+apenv);
                toggle.addClass('prod');
            }
            else {
                opp = 'production';
                toggle.text('AdobePass: stage');
            }
            toggle.on('click',function() {
                $.cookie('AdobePassEnvironment',opp,{ path: '/', domain: '.fox.com', expires: 365 });
                location.reload();
            });
            $('body').append(toggle);
        }
    },

    /**
     * Called by window.swfLoaded()
     */
    _onload: function () {
        AdobePass.triggerEvent('accessEnablerLoaded', [this.getInstance()]);
    }
};


/******************************************************************************
 * Global callbacks invoked by the AccessEnabler SWF; trigger events to redirect
 *****************************************************************************/

/**
 * Called by AccessEnabler SWF ExternalInterface once loaded
 */
window.swfLoaded = function swfLoaded() {
    if (!!window.debug) {
        console.log("swfLoaded()", arguments);
    }
    setTimeout(function () {
        AdobePass.AccessEnabler._onload();
    });
};

/**
 * Called by AccessEnabler SWF ExternalInterface upon AccessEnabler.setRequestor
 */
window.setAuthenticationStatus = function setAuthenticationStatus() {
    if (!!window.debug) {
        console.log("setAuthenticationStatus()", arguments);
    }
    var args = arguments;
    setTimeout(function () {
        AdobePass._setAuthenticationStatus.apply(AdobePass, args);
    });
};

/**
 * Called by adobepass when there's something wrong with the token
 */
window.tokenRequestFailed = function tokenRequestFailed() {
    if (!!window.debug) {
        console.log("tokenRequestFailed()", arguments);
    }
    var args = arguments;
    setTimeout(function () {
        AdobePass._tokenRequestFailed.apply(AdobePass, args);
    });
};

/**
 * Called by adobepass when we receive an auth token
 */
window.setToken = function setToken(requestor, token) {
    if (!!window.debug) {
        console.log("setToken()", arguments);
    }
    setTimeout(function () {
        AdobePass._token = token;
        AdobePass.AccessEnabler.getInstance().getMetadata('TTL_AUTHN');
    });
};

/**
 * Called by adobepass as a callback from getMetadata()
 */
window.setMetadataStatus = function setMetadataStatus(key,args,value) {
    if (!!window.debug) {
        console.log("setMetadataStatus()", arguments);
    }
    setTimeout(function () {
        if (key === 'TTL_AUTHN') {
            AdobePass._setToken(AdobePass._token,value);
        }
    });
};

/**
 * Called by adobepass after we do AE.setSelectedProvider()
 */
window.createIFrame = function createIFrame() {
    if (!!window.debug) {
        console.log("createIFrame()", arguments);
    }
    var args = arguments;
    setTimeout(function () {
        AdobePass._createIFrame.apply(AdobePass, args);
    });
};

window.displayProviderDialog = function displayProviderDialog() {
    if (!!window.debug) {
        console.log("displayProviderDialog()", arguments);
    }
    for(var i in arguments) {
        console.log(arguments[i]);
    }
};
/*global AdobePass, FoxEnv, jQuery, VideoAuth, State, FoxId */

/**
 * Handle MVPD interactions
 */
VideoAuth.mvpds = (function (){
    var $ = jQuery;
    var self;

    self = {
        // ----
        // user mvpd

        selected: new State(),
        previouslySelected: null,
        tempPass: 'TempPass_Fox1',
        manifest: null,

        /**
         * Boot called immediately below before returning self
         * Saved MVPD precedence is: Cookie < Janrain < AdobePass
         */
        _init: function () {
            var self = this;
            var cookie = self._getCookie();
            if(cookie){
                self.selected.set(cookie);
            }
        },

        getSlate: function() {
            if (typeof this.getSelected() !== 'undefined') {
                var branded_slate = this.manifest[this.getSelected()].branded_slate;
                return (branded_slate !== '') ? branded_slate : null;
            }

            return null;
        },

        /**
         * getSelected
         *
         * Return the currently selected MVPD.
         * This content is derived from the last_mvpd cookie, and falls back to a call to AdobePass.
         *
         * @returns {string} The selected MVPD
         */
        getSelected: function () {
            // state (initial state is AdobePass selected MVPD or the last_mvpd cookie)
            if (typeof this.selected.get() !== 'undefined') {
                return this.selected.get();
            }

            return AdobePass.getSelectedMvpd();
        },

        /**
         * Remember the MVPD
         */
        rememberSelection: function (mvpd) {
            this.selected.set(mvpd);
            this._setCookie(mvpd);
        },

        _getCookie: function () {
            var matches = document.cookie.match(/last_mvpd=([^;]+)/);
            if (matches) {
                return decodeURIComponent(matches[1]);
            }
            else {
                return null;
            }
        },

        _setCookie: function (mvpd) {
            var in_10_years = new Date();
            in_10_years.setFullYear(in_10_years.getFullYear() + 10);

            document.cookie = [
                'last_mvpd=', encodeURIComponent(mvpd),
                '; path=/; expires=', in_10_years.toGMTString(),
                '; domain=.fox.com'
            ].join('');
        },

        // ----
        // loading

        _data_path: '/_ugc/json/providers/mvpd_providers.json',

        /**
         * Load all mvpds
         * @param {string} id
         * @param {function} callback
         */
        loadAll: function (callback) {
            var self = this;
            $.ajax({
                type: 'GET',
                cache: (typeof FoxEnv === 'undefined' || FoxEnv.getCurrent() === 'production'),
                url: this._data_path,
                dataType: 'json',
                success: function (list) {

                    // Make the mvpds object a lookup for each mvpd by ID
                    self.manifest = [];
                    $.each(list['providers'], function(i, mvpd){
                        self.manifest.push(mvpd);
                        self.manifest[mvpd.id] = mvpd;
                    });

                    callback(null, self.manifest);
                },
                error: function (xhr, textStatus, error) {
                    if(error){
                        callback(error);
                    }
                    else {
                        callback(new Error('failed loading mvpds: ' + textStatus));
                    }
                }
            });
        },

        /**
         * Load a single mvpd
         * @param {string} id
         * @param {function} callback
         */
        load: function (id, callback) {
            if (id === this.tempPass) {
                callback(null, {
                    id: id,
                    name: 'Temporary Pass'
                });
                return;
            }
            this.loadAll(function (err, mvpds) {
                if (err) {
                    callback(err);
                    return;
                }

                if(typeof mvpds[id] !== 'undefined'){
                    callback(null, mvpds[id]);
                }
                else {
                    callback(new Error('invalid mvpd: ' + id));
                }
            });
        }
    };

    self._init();
    return self;
}());
/*global jQuery, s_analytics, FoxId, VideoAuth */

VideoAuth.omniture = (function ($) {
    var original_page_name = window.s_analytics.pageName;
    // In some cases, this isn't set at the time this code is hit
    if (!original_page_name) {
        $(document).ready(function() {
            original_page_name = window.s_analytics.pageName;
        });
    }

    return {
        _hasNotified: {},
        _siteName: 'fox',
        _handlers: {
            // user views an entitled content warning
            'video-auth-overlay': function (site, info) {
                return {
                    pageName: site + ":mvpd:login:sign in screen",

                    prop17: "mvpd authentication:sign in screen",
                    eVar17: "mvpd authentication",
                    events: "event34"
                };
            },

            // user views the 'select a provider' screen
            'select-provider': function (site, info) {
                return {
                    pageName: site + ":mvpd:login:choose provider",

                    prop17: "mvpd authentication:choose provider",
                    eVar17: "mvpd authentication"
                };
            },

            'self-pass': function (site, info) {
                return {
                    pageName: site+ ":mvpd:login:choose sign-in option",

                    prop17: "mvpd authentication:choose sign-in option",
                    eVar17: "mvpd authentication",
                    prop72: info,
                    eVar72: info
                };
            },

            // user views the 'please sign in again' screen
            'please-sign-in-again': function (site, info) {
                return {
                    pageName: site + ":mvpd:login:choose provider",

                    prop17: "mvpd authentication:choose provider",
                    eVar17: "mvpd authentication"
                };
            },

            // user views the mvpd sign in screen
            'sign-in': function (site, info) {
                return {
                    pageName: site + ":mvpd:login:enter login criteria",

                    prop17: "mvpd authentication:enter login criteria",
                    eVar17: "mvpd authentication",
                    prop72: info,
                    eVar72: info
                };
            },

            // user has submitted the the mvpd sign in form
            'sign-in-submit': function (site, info) {
                return {
                    pageName: site + ":mvpd:login:user criteria submitted",

                    prop17: "mvpd authentication:user criteria submitted",
                    eVar17: "mvpd authentication",
                    prop72: info,
                    eVar72: info
                };
            },

            // user has logged in with an mvpd
            'login': function (site, info) {
                return $.extend({
                    pageName: original_page_name,

                    prop17: "mvpd authentication:mvpd successfully authenticated",
                    eVar17: "mvpd authentication",
                    events: "event35,event95"
                },info);
            },

            // user is logging out of an mvpd
            'confirm-logout' : function (site, info) {
                return {
                    pageName: site + ":mvpd:confirm sign out",

                    prop72: info,
                    eVar72: info
                };
            },

            // user has logged out of an mvpd
            'logout' : function (site, info) {
                return {
                    pageName: site + ":mvpd:mvpd logout",

                    prop72: info,
                    eVar72: info,
                    events: "event18"
                };
            },

            // user received authz error
            'auth-error' : function(site, info) {
                return $.extend({
                    pageName: original_page_name,

                    prop17: "mvpd authentication:mvpd error thrown",
                    eVar17: "mvpd authentication",
                    events: "event92"
                },info);
            },

            'self-pass-expired' : function(site, info) {
                return $.extend({
                    pageName: original_page_name,

                    prop17: "mvpd authentication:self pass error thrown",
                    eVar17: "mvpd authentication"
                },info);
            },

            'token-exists': function(site, info) {
                return $.extend({
                    pageName: original_page_name
                },info);
            }
        },

        reset: function () {
            this._hasNotified = {};
        },

        notifyOnce: function (type, info) {
            if (this._hasNotified[type]) {
                return;
            }
            this.notify(type, info);
        },

        notify: function (type, info) {
            // make sure we only send notification once
            this._hasNotified[type] = true;

            var notify_poll_interval = 500; //ms
            var notify_decay_rate = 2;
            var self = this;

            /**
             * Since the s_analytics.t function may not be defined yet, keep
             * trying until it is.
             */
            function notifyWhenReady(){
                if (typeof (s_analytics || {}).t === 'undefined') {
                    setTimeout( notifyWhenReady, notify_poll_interval );
                    notify_poll_interval *= notify_decay_rate;
                    return;
                }

                self._send(self.createValues(type, info));
            }
            notifyWhenReady();
        },

        createValues: function (type, info) {
            var handlers = this._handlers;
            if (!handlers[type]) {
                throw new Error('No omniture handler for type `' + type + '`');
            }

            return handlers[type](this._siteName, info);
        },
        
        /**
         * Send a tracking signal
         * @param {object} values
         */
        _send: function (values) {
            this._log(values);
            
            var a = s_analytics;
            $.each(values, function (key, value) {
                a[key] = value;
            });
            a.t();

            // reset values
            $.each(values, function (key, value) {
                if (key == 'pageName') {
                    a['pageName'] = original_page_name;
                }
                else if (key == 'events') {
                    a['events'] = 'event2';
                }
                else {
                    a[key] = '';
                }
            });
        },

        // ----
        // logging
        
        _log: function () {},
        startLogging: function () {
            this._log = function () {
                //console.log.apply(console, ['VideoAuth.omniture', arguments]);
            };
        },
        stopLogging: function () {
            this._log = function () {};
        }
    };
}(jQuery));
/*global VideoAuth, jQuery, AdobePass */

VideoAuth.screens['oops'] = (function ($) {
    return {
        load: function (tpl, callback) {
            $('#cboxClose, #cboxOverlay').unbind().on('click', function() {
                VideoAuth.error = null;
                AdobePass.logout();
            });

            var error = AdobePass.error.get();
            var auth_error = VideoAuth.error;
            var tpl_vars = {
                error: (error && error.flashPlayerVersion) || auth_error != null ? true : false,
                flashPlayerVersion: (error && error.flashPlayerVersion) ? error.flashPlayerVersion : null,
                message: auth_error ? auth_error : null
            };
            var elm = $(Mustache.render(tpl, tpl_vars));
            callback(null, elm);
        }
    };
}(jQuery));
/*global VideoAuth, jQuery, Mustache, AdobePass, AuthModal */

VideoAuth.screens['auth-error'] = (function ($) {
    return {
        load: function (tpl, callback) {

            var mvpd_id = VideoAuth.mvpds.getSelected();
            VideoAuth.mvpds.load(mvpd_id, function (err, mvpd) {
                var error = AdobePass.error.get();
                var tpl_vars = {
                    mvpd: mvpd,
                    is_unauthorized: (error && error.reason === 'User not Authorized Error') ? true : false,
                    // Prioritize the provider specific message over the generic one.
                    error_message_html: !!error
                        ? (!!error.htmlProviderMessage
                            ? error.htmlProviderMessage
                            : error.message)
                        : null
                };

                var elm = $(Mustache.render(tpl, tpl_vars));

                elm.find('.provider').on('click',function (e) {
                    e.preventDefault();
                    return false;
                });

                elm.find('a[href^="#select-provider-upon-adobepass-logout"]').on('click', function (e) {
                    e.preventDefault();

                    // Set cookies to remember what to do once AdobePass.logout() reloads the page
                    // See the auth-*/triggers.js and video_auth-*/triggers.js
                    var videoAuthCookie = {
                        autoOpen : true,
                        screen : 'select-provider'
                    };

                    jQuery.cookie('videoAuth',JSON.stringify(videoAuthCookie));

                    AdobePass.logout();
                });
                
                // dirty way to get the cbox close and overlay to do the same as above

                $('#cboxClose, #cboxOverlay').unbind().on('click', function() {
                    // Authorization failures should not log the user out.
                    if(!error.message.match(/User not Authorized/)) {
                        AdobePass.logout();    
                    } else {
                        $('#player .loading').hide();
                        $.colorbox.close();
                    }
                });

                callback(null, elm);
            });
        }
    };
}(jQuery));
/*global VideoAuth, jQuery */

VideoAuth.screens['dont-see-provider'] = (function ($) {
    return {
        load: function (tpl, callback) {
            var elm = $($.trim(tpl));
            callback(null, elm);
        }
    };
}(jQuery));
/**
 * global VideoAuth, jQuery 
 * 
 * This sceen is used by watchnewepisodes page
 */

VideoAuth.screens['dont-see-provider2'] = (function ($) {
    return {
        load: function (tpl, callback) {
            var elm = $(tpl);
            callback(null, elm);
        }
    };
}(jQuery));
/*global VideoAuth, jQuery */

VideoAuth.screens['no-tv-provider'] = (function ($) {
    return {
        load: function (tpl, callback) {
            var total = !!window.module_data && !!window.module_data.video_auth_rules
                ? window.module_data.video_auth_rules.days_to_unlock
                : 8;
            var tpl_vars = {
                days_to_unlock : total
            };
            var elm = $(Mustache.render(tpl, tpl_vars));

            // TODO: refactor globals
            var stub = window.showfolder;
            var show_full_name = window.showFullName;

            // Place clip data
            // @todo This can be rendered using the Mustache template
            $.getJSON("/_data/" + stub + "/videos?type=clips&page_size=1", function(data){
                var item = data.results[0];
                elm.find(".suggestion-clips:first div div img").attr("src", item.videoStillURL);
                elm.find(".suggestion-clips:first div h3").text(item.name);
                elm.find(".suggestion-clips:first .season-num").text(item.season);
                elm.find(".suggestion-clips:first .clip-length").text(item.length);
                elm.find(".suggestion-clips:first .airdate").text(item.airdate);
                elm.find(".suggestion-clips:first .link").attr('href', "/" + item.series + "/videos/");
                elm.find(".suggestion-clips").attr("rel", "/watch/" + item.id);
            });

            // @todo This can be rendered using the Mustache template
            $.getJSON("/_data/" + stub + "/videos?type=episodes&is_locked=no&page_size=1", function(data){
                var item = data.results[0];

                if(item.series){
                    elm.find(".featured-show").text(show_full_name);
                    elm.find(".featured-clip>a.link").attr("href", "/" + item.series + "/full-episodes/");
                }
                else{
                    elm.find(".featured-clip a").remove();
                    if(elm.find(".suggestion-clips:not(:first)").length > 0){
                        elm.find(".suggestion-clips:not(:first)").remove();
                        elm.find(".suggestion-clips:first").css({"margin": "15px auto", "float": "none"});
                    }
                }
            });

            elm.find(".suggestion-clips").on('click', function(e){
                var el = $(this);
                if (el.attr('rel') && el.attr('rel').length > 0) {
                    window.location = el.attr('rel');
                }
            });

            VideoAuth.Modal.title.set("Don't Have A Provider?");
            callback(null, elm);
        }
    };
}(jQuery));
/*global VideoAuth, jQuery, Mustache, FoxId, State, RequestBatch */

VideoAuth.screens['please-sign-in-again'] = (function ($) {
    return {
        load: function (tpl, callback) {
            var id = VideoAuth.mvpds.getSelected();
            if (!id) {
                callback(new Error("Expected that an MVPD was already selected."));
                return;
            }

            var request_batch = new RequestBatch();
            var tpl_vars = {};
            var mvpd;

            function showPicker () {
                // Clear the selection so the user can choose 
                // a different one.
                VideoAuth.mvpds.rememberSelection('');
                
                // change to the mvpd selection screen
                VideoAuth.showScreen('select-provider','Sign In');
            }

            request_batch.add(function (request_callback) {
                VideoAuth.mvpds.load(id, function (err, info) {
                    if (err) {
                        // invalid mvpd: 
                        if (err.message && err.message.indexOf('invalid mvpd') === 0) {
                            showPicker();
                            return;
                        }

                        // unknown error
                        callback(err);
                    }
                    else {
                        mvpd = info;
                        $.extend(tpl_vars, info);
                        request_callback();
                    }
                });
            });

            // Render the screen once all of the information is loaded
            request_batch.process(function (failures, successes) {
                if (failures.length !== 0) {
                    throw new Error('Did not expect there to be failures.');
                }

                var elm = $(Mustache.render(tpl, tpl_vars));
                elm.append(OpinionLab.createInlineButton('authentication','Support'));
                //elm.find('img.logo').attr('src',tpl_vars['logos']['general']);

                // remove tooltip from beta providers
                elm.find('img.logo[src*="beta"]').parent().removeAttr('title');

                var handleSignInClick = function(event) {
                    event.preventDefault();
                    VideoAuth.mvpds.rememberSelection(mvpd.id);
                    VideoAuth.showScreen('sign-in','Sign In');
                };

                elm.find('.provider').click(handleSignInClick);
                elm.find('button[name=sign-in]').click(handleSignInClick);
                elm.find('a[href$="#state:select-provider"]').click(function (event) {
                    event.preventDefault();
                    showPicker();
                });

                callback(null, elm);
            });
        }
    };
}(jQuery));
/*global VideoAuth, jQuery */

VideoAuth.screens['learn-more'] = (function ($) {
    return {
        load: function (tpl, callback) {
            window.location = '/watchnewepisodes';
        }
    };
}(jQuery));
/*global VideoAuth, jQuery, Mustache, FBCFOX, State, FoxId */

VideoAuth.screens['select-provider'] = (function ($) {

    return {
        load: function (tpl, callback) {

            VideoAuth.mvpds.loadAll(function (err, mvpds) {
                if (err) {
                    callback(err);
                    return;
                }

                var i;

                // Sort online from not online and order by name before display
                var online = [];
                var other = [];
                var demo;
                for (i = 0; i<mvpds.length;i++) {
                    if (mvpds[i]['id'] === 'zzElasticSSOIframe') {
                        demo = mvpds[i];
                        continue;
                    }

                    if ($.inArray('fox_www',mvpds[i]['availability']) !== -1) {
                        online.push(mvpds[i]);
                    }
                    else {
                        other.push(mvpds[i]);
                    }
                }

                var sortByName = function(a,b) {
                    var aTitle = a.title.toLowerCase();
                    var bTitle = b.title.toLowerCase();
                    return ((aTitle < bTitle) ? -1 : ((aTitle > bTitle) ? 1 : 0));
                };

                online.sort(sortByName);
                other.sort(sortByName);

                mvpds = $.merge(online,other);
                if (!!demo) {
                    mvpds.push(demo);
                }
                var mvpds_by_two = [];
                for (i = 0; i < mvpds.length; i += 2) {
                    mvpds_by_two.push([
                        mvpds[i],
                        mvpds[i+1]
                    ]);
                }
                var tpl_vars = {
                    mvpds: mvpds,
                    mvpds_by_two: mvpds_by_two
                };

                var elm = $(Mustache.render(tpl, tpl_vars));
                elm.append(OpinionLab.createInlineButton('authentication','Support'));

                // remove tooltip from beta providers
                elm.find('li.provider img[src*="beta"]').parent().data('tooltip','');

                // create infinite carousel of providers
                elm.find('#mvpdSelector .items').infiniteCarousel({
                    iPerPage: 3,
                    oNavigationNext: elm.find('#mvpdSelector a.btnNext'),
                    oNavigationPrevious: elm.find('#mvpdSelector a.btnPrevious'),
                    iRotationTime: 500,
                    bShowPageNumbers: false
                });

                // bind click events for selecting an mvpd
                elm.find('.provider').click(function (event) {
                    event.preventDefault();
                    var id = $(this).data('mvpd');
                    VideoAuth.mvpds.rememberSelection(id);

                    if ($.cookie('AuthedWithMvpd') == 1 || $.cookie('tempPassUsed') == 1) {
                        VideoAuth.showScreen('sign-in','Sign In');
                    }
                    else {
                        VideoAuth.showScreen('self-pass','Sign In');
                    }

                    $('div.toolTip').remove(); // @todo There is probably a better selector or method for this, like FBCFOX.ToolTip.hide()
                });

                // tooltip
                elm.find('.provider').on('mouseenter', function(e) {
                    if($(this).data('tooltip')){
                        window.FBCFOX.ToolTip.show({
                            context: this,
                            data: $(this).data('tooltip'),
                            width: 275,
                            displayDelay: 0
                        });
                    }
                });

                callback(null, elm);
            });
        }
    };
}(jQuery));
/*global AdobePass, jQuery, Mustache, VideoAuth */

VideoAuth.screens['sign-in'] = (function ($) {
    return {
        load: function (tpl, callback) {
            var mvpd_id = VideoAuth.mvpds.getSelected();
            if (!mvpd_id) {
                callback(new Error('Expected an MVPD to be selected.'));
                return;
            }

            VideoAuth.mvpds.load(mvpd_id, function (err, mvpd) {
                if (err) {
                    callback(err);
                    return;
                }

                var resize_video_modal = function () {
                    if (typeof VideoAuth !== 'undefined' && typeof VideoAuth.Modal !== 'undefined') {
                        VideoAuth.Modal.resize();
                    }
                };

                var elm = $(Mustache.render(tpl, mvpd));

                $(AdobePass).one('createIFrame', function(e, width, height){
                    elm.find('iframe').css({
                        width: width,
                        height: height
                    });
                    setTimeout(resize_video_modal);
                });

                elm.addClass('loading');
                elm.find('iframe').one('load', function () {
                    // fade out & remove the loading popup
                    setTimeout(function () {
                        elm.find('.information-loader').animate({ opacity: 0 }, {
                            complete: function () {
                                elm.removeClass('loading');
                            }
                        });
                    }, 1000);

                    var swf = AdobePass.AccessEnabler.getInstance();
                    var provider = AdobePass.AccessEnabler.getInstance().getSelectedProvider();
                    if (provider.MVPD === VideoAuth.mvpds.tempPass) {
                        AdobePass.clearTempPass();
                    }
                    swf.getAuthentication();
                    swf.setSelectedProvider(mvpd.id);

                    // if the lightbox is closed at any time after this
                    // point, we need to refresh the page
                    // (otherwise AdobePass complains about multiple requests)
                    // so we let the page invoke location.reload() when the
                    // lightbox is closed once the iframe is loaded.
                    VideoAuth.omniture.notify('sign-in', VideoAuth.mvpds.getSelected());
                    $(AdobePass).trigger('mvpdIframeLoaded');
                });


                // Here we set a cookie to keep track of the fact that we have
                // shown the user the MVPD login screen. If upon the next page
                // load we get an adobePassError event, then we will open the
                // modal to the mvpd step and set the screen_id to auth-error
                jQuery.cookie('hasJustShownMvpdLogin',1);
                callback(null, elm);
            });
        }
    };
}(jQuery));


jQuery(function ($) {
    if (jQuery.cookie('hasJustShownMvpdLogin') == 1) {
        jQuery.cookie('hasJustShownMvpdLogin',0);
    }

    if (jQuery.cookie('hasJustShownMvpdLogin') == 1) {
        $(AdobePass).on('adobePassError', function (e, error){
            VideoAuth.is_signed_in.when(true, function () {
                VideoAuth.showScreen('auth-error','Authentication Error');
            });
        });
    }
});
/*global AdobePass, jQuery, VideoAuth, FoxPlayer, Mustache */

VideoAuth.screens['video-auth-overlay'] = (function ($) {
    return {
        load: function (tpl, callback) {
            var $ = jQuery;
            var video = FoxPlayer.getVideo();
            var video_airdate_parts = video.airdate.split('-');
            var airdate_year = parseInt(video_airdate_parts[0], 10);
            var airdate_month = parseInt(video_airdate_parts[1], 10);
            var airdate_day = parseInt(video_airdate_parts[2], 10);
            var airdate = new Date(0);
            airdate.setUTCFullYear(airdate_year);
            airdate.setUTCMonth(airdate_month - 1);
            airdate.setUTCDate(airdate_day);
            var locked_duration;
            // Pre-calculated
            if (!!video.days_to_unlock) {
                locked_duration = video.days_to_unlock;
            }
            // Manually calculate
            else {
                var auth_end_date = new Date(parseInt(video.authEndDate, 10));
                var locked_duration_ms = auth_end_date.valueOf() - airdate.valueOf();
                locked_duration = Math.max(1, Math.round(locked_duration_ms / 24 / 60 / 60 / 1000));
            }

            var tpl_vars = {
                episode_title: video.name,
                episode_date: video.airdate,
		locked_duration_num: locked_duration,
                locked_duration: String(locked_duration) + ' ' + (locked_duration === 1 ? 'day' : 'days')
            };

            var elm = $(Mustache.render(tpl, tpl_vars));

            elm.find('.sign-in').click(function(e){
                e.preventDefault();

                var selected = VideoAuth.mvpds.getSelected();
                if (selected && selected !== VideoAuth.mvpds.tempPass) {
                    if ($.cookie('AuthedWithMvpd') == 1 || $.cookie('tempPassUsed') == 1) {
                        VideoAuth.showScreen('please-sign-in-again','Sign In');
                    }
                    else {
                        VideoAuth.showScreen('self-pass','Sign In');
                    }
                }
                else {
                    VideoAuth.showScreen('select-provider','Sign In');
                }
            });

            if (callback && typeof callback === 'function') {
                callback(null, elm);
            }
        }
    };
}(jQuery));
/*global VideoAuth, jQuery, Mustache, FoxId, State, RequestBatch */

VideoAuth.screens['self-pass'] = (function ($) {
    return {
        load: function (tpl, callback) {
            var id = VideoAuth.mvpds.getSelected();
            if (!id) {
                callback(new Error("Expected that an MVPD was already selected."));
                return;
            }

            var request_batch = new RequestBatch();
            var tpl_vars = {};
            var mvpd;

            function showPicker () {
                // Clear the selection so the user can choose
                // a different one.
                VideoAuth.mvpds.rememberSelection('');

                // change to the mvpd selection screen
                VideoAuth.showScreen('select-provider','Sign In');
            }

            request_batch.add(function (request_callback) {
                VideoAuth.mvpds.load(id, function (err, info) {
                    if (err) {
                        // invalid mvpd: 
                        if (err.message && err.message.indexOf('invalid mvpd') === 0) {
                            showPicker();
                            return;
                        }

                        // unknown error
                        callback(err);
                    }
                    else {
                        mvpd = info;
                        $.extend(tpl_vars, info);
                        request_callback();
                    }
                });
            });

            // Render the screen once all of the information is loaded
            request_batch.process(function (failures, successes) {
                if (failures.length !== 0) {
                    throw new Error('Did not expect there to be failures.');
                }

                var elm = $(Mustache.render(tpl, tpl_vars));

                // remove tooltip from beta providers
                elm.find('img.logo[src*="beta"]').parent().removeAttr('title');

                var handleSignInClick = function(event) {
                    event.preventDefault();
                    VideoAuth.mvpds.rememberSelection(mvpd.id);
                    VideoAuth.showScreen('sign-in','Sign In');
                };

                var handleSelfPassClick = function(event) {
                    event.preventDefault();
                    VideoAuth.mvpds.rememberSelection(VideoAuth.mvpds.tempPass);
                    window.AdobePass.selectTempPass();
                };

                var handleBackClick = function(event) {
                    event.preventDefault();
                    VideoAuth.mvpds.rememberSelection(null);
                    VideoAuth.showScreen('select-provider','Sign In');
                };

                elm.find('.provider').click(handleSignInClick);
                elm.find('button[name=sign-in]').click(handleSignInClick);
                elm.find('button[name=self-pass]').click(handleSelfPassClick);
                var button = $('<button class="back" data-screen="select-provider"></button>');
                window.VideoAuth.Modal.holder.find('.header > div').prepend(button);
                button.on('click',handleBackClick);

                elm.find('a.tooltip').on('mouseenter', function(e) {
                    if($(this).data('tooltip')){
                        window.FBCFOX.ToolTip.show({
                            context: this,
                            data: $(this).data('tooltip'),
                            width: 275,
                            displayDelay: 0,
                            orientation: 'horizontal'
                        });
                    }
                });

                callback(null, elm);
            });
        }
    };
}(jQuery));
/*global VideoAuth, jQuery */

VideoAuth.screens['self-pass-expired'] = (function ($) {
    return {
        load: function (tpl, callback) {
            var elm = $($.trim(tpl));
            callback(null, elm);
        }
    };
}(jQuery));/*global jQuery, AuthModal, FoxId, VideoAuth */
/**
 * Querystring triggers
 */
jQuery(function () {
    function parseQuerystring (input) {
        var args = {};
        var parts = input.split('&');
        var keyval;
        var i = parts.length;
        while (i > 0) {
            i -= 1;
            keyval = parts[i].split('=', 2);
            args[keyval[0]] = (keyval.length > 1) ? keyval[1] : true;

            // Prevent any possible XSS holes
            if (typeof args[keyval[0]] === 'string') {
                args[keyval[0]] = args[keyval[0]].replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/"/g, '&quot;');
            }
        }
        return args;
    }

    // ----

    var $ = jQuery;
    var args = parseQuerystring(location.search.substr(1));

    if ($.cookie('videoAuth')) {
        try {
            var videoAuthCookie = JSON.parse($.cookie('videoAuth'));
            $.cookie('videoAuth',null);
            args.mvpd_autoOpen = videoAuthCookie.autoOpen ? true : false;
            args.mvpd_screen = videoAuthCookie.screen ? videoAuthCookie.screen : null;
        }
        catch(err){
        }
    }

    if (args.mvpd_autoOpen) {
        VideoAuth.Modal.is_open.when(true, function () {
            VideoAuth.is_signed_in.when(false, function () {
                if (args.mvpd_screen) {
                    VideoAuth.showScreen(args.mvpd_screen);
                }
                else if (VideoAuth.mvpds.getSelected()) {
                    VideoAuth.showScreen('please-sign-in-again','Sign In');
                }
                else {
                    VideoAuth.showScreen('select-provider','Sign In');
                }
            });
        });
    }

});
/*global Auth, CAPTURE, VideoAuth, jQuery, FoxId, AdobePass */

var AuthMessageBox = (function ($) {
    var self;
    self = {
        _base_elm: null,
        _mvpd: null,
        default_unauthenticated_state_message: null,
        default_provider_title: null,

        _getMessage: function (type) {
            switch (type) {
                case 'a':
                    return 'To watch your favorite FOX shows the next day after they air, just pop in your TV Provider\'s Username and Password.';
                break;

                case 'b':
                    return 'To watch your favorite shows the next day after they air, just enter your TV Provider Username and Password.';
                break;

                default:
                    return 'To watch your favorite FOX shows the next day after they air, just pop in your TV Provider\'s Username and Password.';
                break;
	        }
        },

        setup: function (elm) {
            var screen_set_id = VideoAuth.getScreenSetId();
	        var custom_message = self._getMessage(screen_set_id);

	        // add a class for the screen-set so we can have custom css
	        elm.addClass('screen_set-' + (screen_set_id || 'default'));
	    
            self._base_elm = elm;
            self.default_provider_title = elm.find('.mvpd_title').text();

            if (custom_message) {
                self.default_unauthenticated_state_message = custom_message;
            }

            self.update('unauthenticated');

            VideoAuth.is_signed_in.watch(function (is_signed_in) {
                self._base_elm.toggleClass('signed-in', is_signed_in);
                $('#showVideoPlayerComponent').toggleClass('signed-in', is_signed_in);
                self.update(is_signed_in ? 'authenticated' : 'unauthenticated');
            });

            self._base_elm.on('click', "a[href='#login'], a[href='#activate']", function(event) {
                event.preventDefault();
                if (VideoAuth.mvpds.getSelected()) {
                    if ($.cookie('AuthedWithMvpd') == 1 || $.cookie('tempPassUsed') == 1) {
                        VideoAuth.showScreen('please-sign-in-again','Sign In');
                    }
                    else {
                        VideoAuth.showScreen('self-pass','Sign In');
                    }
                }
                else {
                    VideoAuth.showScreen('select-provider','Sign In');
                }
            });

            self._base_elm.on('click', "a[href='#logout']", function(event){
                event.preventDefault();

                // TempPass can sign in with a different provider
                if (AdobePass.getSelectedMvpd() == VideoAuth.mvpds.tempPass) {
                    VideoAuth.showScreen('select-provider','Sign In');
                }
                else {
                    VideoAuth.confirmLogout(function (result) {
                        if (result) {
                            AdobePass.logout();
                        }
                    });
                }
            });

            VideoAuth.mvpds.selected.watch(function(mvpd){
                self._base_elm.toggleClass('has-mvpd', !!mvpd);
            });
        },

        update: function (state) {
            var refresh_ui = function (mvpd) {
                self._base_elm.toggleClass('has-mvpd', (!!mvpd && state === 'authenticated'));
                if (mvpd && state == 'authenticated') {
                    var msg = '';
                    // Regular MVPDs
                    if(!!mvpd.state_messages) {
                        msg = '';
                        self.setMvpdLogo(mvpd.logos.mvpdLogo, (mvpd.urls && mvpd.urls.home) ? mvpd.urls.home : null);
                    }
                    // TempPass
                    else {
                        $('#mvpdLogoVideoPlayer').hide();
                        self._base_elm.find("a.logout").text('Sign in now');
                        self._base_elm.find("a#lrnMoreBtn").text('Learn more');
                        self._base_elm.find("#mvpdLogo").hide();
                        self._base_elm.find("#authModule").css('cssText','width: auto !important');
                        msg = "You currently have Temporary Access to locked full episodes. To continue watching your favorite FOX shows the next day after they air, just pop in your TV Provider's Username and Password.";
                    }
                    self._base_elm.find(".state-message").html(msg);
                    self._base_elm.find(".mvpd_title").text(mvpd.title);
                }
                else {
                    // assert state === unauthenticated
                    self._base_elm.find(".mvpd_title").text(self.default_provider_title);
                    self._base_elm.find(".state-message").text(self.default_unauthenticated_state_message);
                }
            };

            var id = VideoAuth.mvpds.getSelected();
            if (!id) {
                setTimeout(function () {
                    refresh_ui(null);
                });
            }
            else {
                VideoAuth.mvpds.load(id, function (error, mvpd) {
                    refresh_ui(mvpd);
                });
            }
        },

        setMvpdLogo: function (src, url) {
            var elm = self._base_elm.find('#mvpdLogo');
            var image = new Image();
            image.src = src;

            if (url) {
                var a = $('<a></a>');
                a
                    .attr('target','_blank')
                    .attr('href',url)
                    .css('display','inline-block')
                    .append(image);
                elm.html(a);
            }
            else {
                elm.html(image);
            }

            var video_player_logo = $('#mvpdLogoVideoPlayer');
            if (video_player_logo.length > 0) {
                video_player_logo.html(elm.html());
            }
        },

        isLockedEpisode: function () {
            return $('#fullEpisodesList .episode.active .padlock').length > 0;
        },

        isFirstEpisode: function () {
            return $('#fullEpisodesList .episode:eq(0)').hasClass('active');
        },

        getFirstEpisodeHref: function () {
            return $('#fullEpisodesList .episode a').attr('href');
        }
    };
    return self;
}(jQuery));

// ----
// boot

jQuery(function () {
    AuthMessageBox.setup(jQuery('#authModuleContainer'));
    if (typeof auth_test_mvpd_logo != 'undefined' && typeof VideoAuth != 'undefined') {
        VideoAuth.is_signed_in.watch(function (is_signed_in) {
            VideoAuth.mvpds.selected.set(auth_test_mvpd_logo);
            VideoAuth.is_signed_in.set(true);
        });
    }
});
/*global jQuery*/

var OpinionLab = (function($){
    var base_url = '/_app/components/opinion_lab-1.0';

    var self;
    self = {
        createInlineButton: function (type,label) {
            if (!label) {
                label = type.charAt(0).toUpperCase() + type.slice(1);
            }
            return $('<div class="ol_inline"><a class="ol_'+type+'" id="oo_feedback_btn" href="javascript:void(0);" onClick="oo_'+type+'.show()">'+label+'</a></div>');
        },
        createSidebarButton: function (show, mbox) {
            return $('<div class="ol_button"><div class="mboxDefault"><a href="javascript:void(0);" onClick="oo_videoExtras.show()">'+show+' Feedback</a></div><script type="text/javascript">mboxCreate("'+mbox+'");</script></div>');
        }
    };

    return self;
}(jQuery));
/*0.199 s*/
// ----
// exports

window.FoxEnv = window.FoxEnv || {};
window.FoxEnv.getCurrent = function() {
    return "production";
}

window.VideoAuth = VideoAuth;
window.AdobePass = AdobePass;
window.FoxPlayer = FoxPlayer;

}());

