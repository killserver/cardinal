let cardinalEngine = {
	getElem: function(elem) {
		return this.getQuery(elem);
	},
	generateUID: function() {
		var firstPart = (Math.random() * 46656) | 0;
		var secondPart = (Math.random() * 46656) | 0;
		firstPart = ("000" + firstPart.toString(36)).slice(-3);
		secondPart = ("000" + secondPart.toString(36)).slice(-3);
		return firstPart + secondPart;
	},
	getQuery: function(elemz, get) {
		if(typeof(get)!="undefined") {
			var gets = document.querySelector(elemz).querySelectorAll(get);
			if(gets.length>1) {
				gets.forEach(function(i, d) {
					Object.keys(cardinalEngineElement).map(function(objectKey, index) {
						i.prototype[objectKey] = cardinalEngineElement[objectKey];
					});
				});
			}
			return gets.length>1 ? gets : gets[0];
		} else {
			var gets = document.querySelectorAll(elemz);
			if(gets.length>1) {
				gets.forEach(function(i, d) {
					Object.keys(cardinalEngineElement).map(function(objectKey, index) {
						i.prototype[objectKey] = cardinalEngineElement[objectKey];
					});
				});
			}
			return gets.length>1 ? gets : gets[0];
		}
	},
	execReady: function() {
		Object.keys(cardinalEngine.funcList).map(function(objectKey, index) {
			cardinalEngine.funcList[objectKey]();
		});
	},
	completedReady: function() {
		cardinalEngine.execReady();
		document.removeEventListener("DOMContentLoaded", cardinalEngine.completedReady);
		window.removeEventListener("load", cardinalEngine.completedReady);
	},
	readyLoad: function() {
		if(document.readyState === "complete" || (document.readyState !== "loading" && !document.documentElement.doScroll)) {
			window.setTimeout(cardinalEngine.completedReady);
		} else {
			document.addEventListener("DOMContentLoaded", cardinalEngine.completedReady);
			window.addEventListener("load", cardinalEngine.completedReady);
		}
	},
	funcList: {},
	addLoad: function(name, func) {
		if(typeof(func)!="undefined") {
			cardinalEngine.funcList[name] = func;
		} else {
			cardinalEngine.funcList[cardinalEngine.generateUID()] = name;
		}
	},
	removeLoad: function(nameZ) {
		if(typeof(nameZ)=="function") {
			Object.keys(cardinalEngine.funcList).map(function(objectKey, index) {
				if(cardinalEngine.funcList[objectKey]==nameZ) {
					delete cardinalEngine.funcList[objectKey];
				}
			});
		} else {
			Object.keys(cardinalEngine.funcList).map(function(objectKey, index) {
				if(objectKey==nameZ) {
					delete cardinalEngine.funcList[objectKey];
				}
			});
		}
	},
	HasClass: function(elem, c) {
		return ('classList' in document.documentElement ? elem.classList.contains(c) : new RegExp("(^|\\s+)" + c + "(\\s+|$)").test(elem.className));
	},
	AddClass: function(elem, c) {
		('classList' in document.documentElement ? elem.classList.add(c) : (!cardinalEngine.HasClass(elem, c) ? elem.className = elem.className + ' ' + c : ""));
	},
	RemoveClass: function(elem, c) {
		('classList' in document.documentElement ? elem.classList.remove(c) : elem.className = elem.className.replace(new RegExp("(^|\\s+)" + c + "(\\s+|$)"), ' '));
	},
	ToggleClass: function(elem, c) {
		var fn = cardinalEngine.HasClass(elem, c) ? cardinalEngine.RemoveClass : cardinalEngine.AddClass;
		fn(elem, c);
	},
	getParents: function(el, parentSelector) {
		if(parentSelector === undefined) {
			parentSelector = document;
		}
		var parents = [];
		var p = el.parentNode;
		while(p !== parentSelector) {
			var o = p;
			parents.push(o);
			p = o.parentNode;
		}
		parents.push(parentSelector);
		return parents;
	},
	executeForElements: function() {
		Object.keys(cardinalEngineElement).map(function(objectKey, index) {
			Element.prototype[objectKey] = cardinalEngineElement[objectKey];
		});
	},
	AddEvent: function(elem, type, handler) {
		if(elem.addEventListener) {
			elem.addEventListener(type, handler, false);
		} else if(elem.attachEvent) {
			elem.attachEvent("on"+type, handler);
		}
	},
	RemoveEvent: function(elem, type, handler) {
		if(elem.removeEventListener) {
			elem.removeEventListener(type, handler, false);
		} else if(elem.detachEvent) {
			elem.detachEvent("on"+type, handler);
		}
	},
	ClearEvent: function(elem, type) {
		var clone = elem.cloneNode();
		while(elem.firstChild) {
		  clone.appendChild(elem.lastChild);
		}
		elem.parentNode.replaceChild(clone, elem);
	},
	ElementExist: function(elem) {
		return (typeof(document.querySelector)!="undefined" && document.querySelector(elem).length!=0 ? true : false);
	},
	createRequest: function() {
		try {
			return new ActiveXObject("Msxml2.XMLHTTP");
		} catch(e) {
			try {
				return new ActiveXObject("Microsoft.XMLHTTP");
			} catch(ee) {}
		}
		if(typeof XMLHttpRequest!='undefined') {
			return new XMLHttpRequest();
		}
	},
	loadJs: function(list) {
		if(typeof(list)=="object" && typeof(list.join)=="undefined") {
			Object.keys(list).map(function(objectKey, index) {
				loadedJs(list[index]);
			});
		} else if(typeof(list)=="object") {
			list.forEach(function(i) {
				loadedJs(i);
			});
		} else if(typeof(list)=="string") {
			loadedJs(list);
		}
	},
	loadRemoveJs: function(list) {
		if(typeof(list)=="object" && typeof(list.join)=="undefined") {
			Object.keys(list).map(function(objectKey, index) {
				get(list[index], loadedJs);
			});
		} else if(typeof(list)=="object") {
			list.forEach(function(i) {
				get(i, loadedJs);
			});
		} else if(typeof(list)=="string") {
			get(list, loadedJs);
		}
	},
	loadedJs: function(resp, stat, header, url) {
		try {
			eval(resp);
		} catch(ex) {
			console.error(ex.name + " in "+url);
		}
	},
	get: function(url, post, cb) {
		if(typeof(cb)=="undefined") {
			cb = post;
			post = "";
		}
		var postOnSite = "";
		if(typeof(post)=="object" && typeof(post.join)=="undefined") {
			var postObject = [];
			Object.keys(post).map(function(objectKey, index) {
				postObject.push(objectKey+"="+post[objectKey]);
			});
			postOnSite = postObject.join("&");
		} else if(typeof(post)=="object") {
			postOnSite = post.join("&");
		}
		var xmlhttp = cardinalEngine.createRequest();
		xmlhttp.open("GET", url+'?'+encodeURIComponent(postOnSite)+(postOnSite.length==0 ? "" : "&")+'r='+Math.random());
		xmlhttp.onreadystatechange = function() {
			if(xmlhttp.readyState == 4) {
				cb((typeof(xmlhttp.responseHTML)!="undefined" ? xmlhttp.responseHTML : xmlhttp.responseText), xmlhttp.status, xmlhttp.getAllResponseHeaders(), url+'?'+encodeURIComponent(postOnSite), null);
			}
		}
		xmlhttp.send(null);
	},
	post: function(url, post, cb) {
		if(typeof(cb)=="undefined") {
			cb = post;
			post = "";
		}
		var postOnSite = "";
		if(typeof(post)=="object" && typeof(post.join)=="undefined") {
			var postObject = [];
			Object.keys(post).map(function(objectKey, index) {
				postObject.push(objectKey+"="+post[objectKey]);
			});
			postOnSite = postObject.join("&");
		} else if(typeof(post)=="object") {
			postOnSite = post.join("&");
		}
		var xmlhttp = cardinalEngine.createRequest();
		xmlhttp.open("POST", url+'?r='+Math.random());
		xmlhttp.onreadystatechange = function() {
			if(xmlhttp.readyState == 4) {
				cb((typeof(xmlhttp.responseHTML)!="undefined" ? xmlhttp.responseHTML : xmlhttp.responseText), xmlhttp.status, xmlhttp.getAllResponseHeaders(), url, postOnSite);
			}
		}
		xmlhttp.send(postOnSite);
	},
};
let cardinalEngineElement = {
	HasClass: function(c) {
		if(typeof(this)=="object") {
			this.forEach(function(i, d) {
				i.HasClass(c);
			});
		} else {
			return ('classList' in document.documentElement ? this.classList.contains(c) : new RegExp("(^|\\s+)" + c + "(\\s+|$)").test(this.className));
		}
	},
	AddClass: function(c) {
		if(typeof(this)=="object") {
			this.forEach(function(i, d) {
				i.AddClass(c);
			});
		} else {
			('classList' in document.documentElement ? this.classList.add(c) : (!cardinalEngine.HasClass(this, c) ? this.className = this.className + ' ' + c : ""));
		}
	},
	RemoveClass: function(c) {
		if(typeof(this)=="object") {
			this.forEach(function(i, d) {
				i.RemoveClass(c);
			});
		} else {
			('classList' in document.documentElement ? this.classList.remove(c) : this.className = this.className.replace(new RegExp("(^|\\s+)" + c + "(\\s+|$)"), ' '));
		}
	},
	ToggleClass: function(c) {
		if(typeof(this)=="object") {
			this.forEach(function(i, d) {
				i.ToggleClass(c);
			});
		} else {
			var fn = cardinalEngine.HasClass(this, c) ? cardinalEngine.RemoveClass : cardinalEngine.AddClass;
			fn(this, c);
		}
	},
	AddEvent: function(type, handler) {
		if(typeof(this)=="object") {
			this.forEach(function(i, d) {
				i.AddEvent(type, handler);
			});
		} else {
			if(this.addEventListener) {
				this.addEventListener(type, handler, false);
			} else if(this.attachEvent) {
				this.attachEvent("on"+type, handler);
			}
		}
	},
	RemoveEvent: function(type, handler) {
		if(this.removeEventListener) {
			this.removeEventListener(type, handler, false);
		} else if(this.detachEvent) {
			this.detachEvent("on"+type, handler);
		}
	},
	ClearEvent: function(type) {
		cardinalEngine.ClearEvent(this, type);
	},
	parent: function() {
		return cardinalEngine.getParents(this)[0];
	},
	parents: function() {
		return cardinalEngine.getParents(this);
	},
	children: function(elem) {
		if(typeof(elem)!="undefined") {
			return cardinalEngine.getQuery(elem.children, elem);
		} else {
			return this.children;
		}
	},
	show: function() {
		if(typeof(this)=="object") {
			this.forEach(function(i, d) {
				i.show();
			});
		} else {
			this.style.display = "";
		}
	},
	hide: function() {
		if(typeof(this)=="object") {
			this.forEach(function(i, d) {
				i.hide();
			});
		} else {
			this.style.display = "none";
		}
	},
	after: function(insert) {
		this.insertAdjacentHTML('afterend', insert);
	},
	append: function(insert) {
		this.appendChild(insert);
	},
	before: function(insert) {
		this.insertAdjacentHTML('beforebegin', insert);
	},
	Clone: function() {
		this.cloneNode(true);
	},
	Find: function(elem) {
		return this.querySelectorAll(elem);
	},
	Attr: function(attr, set) {
		if(typeof(set)!="undefined") {
			return this.setAttribute(attr, set);
		} else {
			return this.getAttribute(attr);
		}
	},
	css: function(attr, set) {
		if(typeof(set)!="undefined") {
			return this.style[attr] = set;
		} else {
			return this.style[attr];
		}
	},
	text: function() {
		return this.textContent;
	},
	is: function(check) {
		if(typeof(this)=="object") {
			this.forEach(function(i, d) {
				i.is(check);
			});
		} else {
			var matches = function(el, selector) {
				return (el.matches || el.matchesSelector || el.msMatchesSelector || el.mozMatchesSelector || el.webkitMatchesSelector || el.oMatchesSelector).call(el, selector);
			};
			return matches(this, check);
		}
	},
	next: function() {
		if(typeof(this)=="object") {
			this.forEach(function(i, d) {
				i.next();
			});
		} else {
			return this.nextElementSibling;
		}
	},
	offset: function() {
		if(typeof(this)=="object") {
			this.forEach(function(i, d) {
				i.offset();
			});
		} else {
			var rect = el.getBoundingClientRect();
			return { top: rect.top + document.body.scrollTop, left: rect.left + document.body.scrollLeft };
		}
	},
	position: function() {
		if(typeof(this)=="object") {
			this.forEach(function(i, d) {
				i.position();
			});
		} else {
			return { left: this.offsetLeft, top: this.offsetTop };
		}
	},
	prepend: function(el) {
		if(typeof(this)=="object") {
			this.forEach(function(i, d) {
				i.prepend(el);
			});
		} else {
			this.insertBefore(el, this.firstChild);
		}
	},
	remove: function() {
		if(typeof(this)=="object") {
			this.forEach(function(i, d) {
				i.remove();
			});
		} else {
			this.parentNode.removeChild(this);
		}
	},
	html: function(set) {
		if(typeof(set)!="undefined") {
			return this.innerHTML = set;
		} else {
			return this.innerHTML;
		}
	},
	each: function(fn) {
		return this.forEach(fn);
	},
};
cardinalEngineElement['animate'] = function(name, props) {
	for(var i in props) {
		if(props.hasOwnProperty(i)) {
			this.style.setProperty('-webkit-animation-'+i, props[i], "");
			this.style.setProperty('-moz-animation-'+i, props[i], "");
			this.style.setProperty('-ms-animation-'+i, props[i], "");
			this.style.setProperty('-o-animation-'+i, props[i], "");
			this.style.setProperty('animation-'+i, props[i], "");
		}
	}
};
cardinalEngineElement['fadeIn'] = function() {
	var el = this;
	el.style.opacity = 0;
	var last = +(new Date());
	var tick = function() {
		el.style.opacity += (+(new Date() - last) / 400);
		last = +new Date();
		if(+el.style.opacity < 1) {
			(window.requestAnimationFrame && requestAnimationFrame(tick)) || setTimeout(tick, 16);
		}
	};
	tick();
};
cardinalEngineElement['fadeOut'] = function() {
	var el = this;
	el.style.opacity = 1;
	var last = +(new Date());
	var tick = function() {
		el.style.opacity -= (+(new Date() - last) / 400);
		last = +new Date();
		if(+el.style.opacity > 0) {
			(window.requestAnimationFrame && requestAnimationFrame(tick)) || setTimeout(tick, 16);
		}
	};
	tick();
};
if(typeof(removeLoader)=="undefined") {
	var doc = document.createElement("div");
	doc.setAttribute("class", "spinner");
	var fixed = document.createElement("div");
	fixed.setAttribute("class", "loader");
	fixed.appendChild(doc);
	var bodyDone = setInterval(function() {
		if(document.body!=null) {
			document.body.appendChild(fixed);
			clearInterval(bodyDone);
		}
	})
}
function cardinal(elem) {
	return cardinalEngine.getElem(elem);
}
cardinalEngine.addLoad(function() {
	cardinalEngine.AddEvent(window, 'scroll', function(e) {
		var distanceY = window.pageYOffset || document.documentElement.scrollTop, header = ('querySelector' in document.documentElement ? document.querySelector(".header.isFixed") : document.getElementsByClassName("header isFixed")[0]);
		var shrinkOn = header.offsetTop;
		if(distanceY > shrinkOn) {
			cardinalEngine.AddClass(header, "headFix");
		} else {
			if(cardinalEngine.HasClass(header, "headFix")) {
				cardinalEngine.RemoveClass(header, "headFix");
			}
		}
	});
	var foced = ('querySelector' in document.documentElement ? document.querySelector(".header.isFixed") : document.getElementsByClassName("header isFixed")[0]);
	if(foced!=null) {
		cardinalEngine.AddEvent(foced, 'focus', function(e) {
			cardinalEngine.AddClass(this, "focused");
		});
		cardinalEngine.AddEvent(foced, 'blur', function(e) {
			if(cardinalEngine.HasClass(this, "focused")) {
				cardinalEngine.RemoveClass(this, "focused");
			}
		});
	}
	if(typeof(removeLoader)=="undefined") {
		cardinal("div.loader").fadeOut();
	}
	var animation = false, animationstring = 'animation', keyframeprefix = '', domPrefixes = 'Webkit Moz O ms Khtml'.split(' '), pfx  = '', elm = document.createElement('div');
	if(elm.style.animationName!==undefined) {
		animation = true;
	}
	if(animation===false) {
		for(var i=0;i<domPrefixes.length;i++) {
			if(elm.style[domPrefixes[i]+'AnimationName']!==undefined) {
				pfx = domPrefixes[i];
				animationstring = pfx+'Animation';
				keyframeprefix = '-'+pfx.toLowerCase()+'-';
				animation = true;
				break;
			}
		}
	}
	if(animation) {
		cardinalEngine.AddClass(document.body, "supportAnimation");
		cardinalEngine.AddClass(document.body, "support"+keyframeprefix+"Animation");
	}
	if(!!document.createElement('canvas').getContext) {
		cardinalEngine.AddClass(document.body, "supportCanvas");
		if(!!document.createElement('canvas').getContext('2d').fillText == 'function') {
			cardinalEngine.AddClass(document.body, "supportCanvasFillText");
		}
		var canvas = document.createElement('canvas');
		var gl = canvas.getContext("webgl") || canvas.getContext("experimental-webgl");
		if(gl && gl instanceof WebGLRenderingContext) {
			cardinalEngine.AddClass(document.body, "supportCanvasFillText");
		}
	}
	if(!!document.createElement('video').canPlayType) {
		cardinalEngine.AddClass(document.body, "supportVideo");
		if(!!document.createElement("video").canPlayType('video/mp4; codecs="avc1.42E01E, mp4a.40.2"')!="") {
			cardinalEngine.AddClass(document.body, "supportVideoMP4");
		}
		if(!!document.createElement("video").canPlayType('video/ogg; codecs="theora, vorbis"')!="") {
			cardinalEngine.AddClass(document.body, "supportVideoOGG");
		}
		if(!!document.createElement("video").canPlayType('video/webm; codecs="vp8, vorbis"')!="") {
			cardinalEngine.AddClass(document.body, "supportVideoWEBM");
		}
	}
	if('localStorage' in window && window['localStorage'] !== null) {
		cardinalEngine.AddClass(document.body, "supportLocalStorage");
	}
	if(window.File && window.FileReader && window.FileList && window.Blob) {
		cardinalEngine.AddClass(document.body, "supportFile");
	}
	if(!!window.Worker) {
		cardinalEngine.AddClass(document.body, "supportWorker");
	}
	if(!!document.fonts) {
		cardinalEngine.AddClass(document.body, "supportFontsLoader");
	}
	if(!!window.applicationCache) {
		cardinalEngine.AddClass(document.body, "supportOffline");
	}
	if(!!navigator.geolocation) {
		cardinalEngine.AddClass(document.body, "supportGeoLocation");
	}
	var i = document.createElement("input");
	i.setAttribute("type", "search");
	if(i.type !== "text") {
		cardinalEngine.AddClass(document.body, "supportInputSearch");
	}
	var i = document.createElement("input");
	i.setAttribute("type", "number");
	if(i.type !== "text") {
		cardinalEngine.AddClass(document.body, "supportInputNumber");
	}
	var i = document.createElement("input");
	i.setAttribute("type", "range");
	if(i.type !== "text") {
		cardinalEngine.AddClass(document.body, "supportInputRange");
	}
	var i = document.createElement("input");
	i.setAttribute("type", "tel");
	if(i.type !== "text") {
		cardinalEngine.AddClass(document.body, "supportInputTel");
	}
	var i = document.createElement("input");
	i.setAttribute("type", "url");
	if(i.type !== "text") {
		cardinalEngine.AddClass(document.body, "supportInputUrl");
	}
	var i = document.createElement("input");
	i.setAttribute("type", "email");
	if(i.type !== "text") {
		cardinalEngine.AddClass(document.body, "supportInputEmail");
	}
	var i = document.createElement("input");
	i.setAttribute("type", "date");
	if(i.type !== "text") {
		cardinalEngine.AddClass(document.body, "supportInputDate");
	}
	var i = document.createElement("input");
	i.setAttribute("type", "month");
	if(i.type !== "text") {
		cardinalEngine.AddClass(document.body, "supportInputMonth");
	}
	var i = document.createElement("input");
	i.setAttribute("type", "week");
	if(i.type !== "text") {
		cardinalEngine.AddClass(document.body, "supportInputWeek");
	}
	var i = document.createElement("input");
	i.setAttribute("type", "time");
	if(i.type !== "text") {
		cardinalEngine.AddClass(document.body, "supportInputTime");
	}
	var i = document.createElement("input");
	i.setAttribute("type", "datetime");
	if(i.type !== "text") {
		cardinalEngine.AddClass(document.body, "supportInputDatetime");
	}
	var i = document.createElement("input");
	i.setAttribute("type", "datetime-local");
	if(i.type !== "text") {
		cardinalEngine.AddClass(document.body, "supportInputDatetimeLocal");
	}
	var i = document.createElement("input");
	if('placeholder' in i) {
		cardinalEngine.AddClass(document.body, "supportInputPlaceholder");
	}
	var i = document.createElement("input");
	if('autofocus' in i) {
		cardinalEngine.AddClass(document.body, "supportInputAutofocus");
	}
	if(!!document.getItems) {
		cardinalEngine.AddClass(document.body, "supportMicrodata");
	}
	if(!!(window.history && history.pushState)) {
		cardinalEngine.AddClass(document.body, "supportHistory");
	}
});
cardinalEngine.executeForElements();
cardinalEngine.readyLoad();