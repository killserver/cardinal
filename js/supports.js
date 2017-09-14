if(typeof cardinalEngine == "undefined") {
	var cardinalEngine = {
		getElem: function(elem) {
			return this.getQuery(elem);
		},
		merge: function(target, source) {
			if(typeof target !== 'object') {
				target = {};
			}
			for(var property in source) {
				if(source.hasOwnProperty(property)) {
					var sourceProperty = source[property];
					if(typeof sourceProperty === 'object') {
						target[property] = util.merge(target[property], sourceProperty);
						continue;
					}
					target[property] = sourceProperty;
				}
			}
			for(var a=2,l=arguments.length;a<l;a++) {
				merge(target, arguments[a]);
			}
			return target;
		},
		isNumeric: function(n) {
			return !isNaN(parseFloat(n)) && isFinite(n);
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
		ScrollDirection: function(func) {
			var oldScroll = 0;
			var newScroll = 0;
			cardinalEngine.AddEvent(window, "scroll", function(tt) {
				newScroll = cardinalEngine.scrollTop(window);
				if(newScroll>oldScroll) {
					func('down');
				} else {
					func('up');
				}
				oldScroll = newScroll
			});
		},
		scrollTop: function(elem) {
			var win = cardinalEngine.getWindow(elem);
			return win ? win.pageYOffset : elem.scrollTop;
		},
		scrollLeft: function(elem) {
			var win = cardinalEngine.getWindow(elem);
			return win ? win.pageXOffset : elem.scrollLeft;
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
					cardinalEngine.get(list[index], cardinalEngine.loadedJs);
				});
			} else if(typeof(list)=="object") {
				list.forEach(function(i) {
					cardinalEngine.get(i, cardinalEngine.loadedJs);
				});
			} else if(typeof(list)=="string") {
				cardinalEngine.get(list, cardinalEngine.loadedJs);
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
			if("withCredentials" in xmlhttp) {
				xmlhttp.withCredentials = true;
			}
			xmlhttp.open("GET", url+encodeURIComponent(postOnSite)+(postOnSite.length==0 ? "?" : "&")+'r='+Math.random());
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
			if("withCredentials" in xmlhttp) {
				xmlhttp.withCredentials = true;
			}
			xmlhttp.open("POST", url+(postOnSite.length==0 ? "?" : "&")+'r='+Math.random());
			xmlhttp.onreadystatechange = function() {
				if(xmlhttp.readyState == 4) {
					cb((typeof(xmlhttp.responseHTML)!="undefined" ? xmlhttp.responseHTML : xmlhttp.responseText), xmlhttp.status, xmlhttp.getAllResponseHeaders(), url, postOnSite);
				}
			}
			xmlhttp.send(postOnSite);
		},
		isWindow: function(obj) {
			var toString = Object.prototype.toString.call(obj);
			return toString == '[object global]' || toString == '[object Window]' || toString == '[object DOMWindow]';
		}
	};
}
if(typeof cardinalEngineElement == "undefined") {
	var cardinalEngineElement = {
		HasClass: function(c) {
			if(typeof(this)=="object") {
				arr = [];
				for(var i in this) {
					if(!cardinalEngine.isNumeric(i)) {
						continue;
					}
					arr[i] = ('classList' in document.documentElement ? this[i].classList.contains(c) : new RegExp("(^|\\s+)" + c + "(\\s+|$)").test(this[i].className));
				}
				return arr;
			} else {
				return ('classList' in document.documentElement ? this.classList.contains(c) : new RegExp("(^|\\s+)" + c + "(\\s+|$)").test(this.className));
			}
		},
		AddClass: function(c) {
			if(typeof(this)=="object") {
				for(var i in this) {
					if(!cardinalEngine.isNumeric(i)) {
						continue;
					}
					('classList' in document.documentElement ? this[i].classList.add(c) : (!cardinalEngine.HasClass(this[i], c) ? this[i].className = this[i].className + ' ' + c : ""));
				}
			} else {
				('classList' in document.documentElement ? this.classList.add(c) : (!cardinalEngine.HasClass(this, c) ? this.className = this.className + ' ' + c : ""));
			}
		},
		RemoveClass: function(c) {
			if(typeof(this)=="object") {
				for(var i in this) {
					if(!cardinalEngine.isNumeric(i)) {
						continue;
					}
					('classList' in document.documentElement ? this[i].classList.remove(c) : this[i].className = this[i].className.replace(new RegExp("(^|\\s+)" + c + "(\\s+|$)"), ' '));
				}
			} else {
				('classList' in document.documentElement ? this.classList.remove(c) : this.className = this.className.replace(new RegExp("(^|\\s+)" + c + "(\\s+|$)"), ' '));
			}
		},
		ToggleClass: function(c) {
			if(typeof(this)=="object") {
				for(var i in this) {
					if(!cardinalEngine.isNumeric(i)) {
						continue;
					}
					var fn = cardinalEngine.HasClass(this[i], c) ? cardinalEngine.RemoveClass : cardinalEngine.AddClass;
					fn(this[i], c);
				}
			} else {
				var fn = cardinalEngine.HasClass(this, c) ? cardinalEngine.RemoveClass : cardinalEngine.AddClass;
				fn(this, c);
			}
		},
		AddEvent: function(type, handler) {
			if(typeof(this)=="object") {
				for(var i in this) {
					if(!cardinalEngine.isNumeric(i)) {
						continue;
					}
					if(this[i].addEventListener) {
						this[i].addEventListener(type, handler, false);
					} else if(this[i].attachEvent) {
						this[i].attachEvent("on"+type, handler);
					}
				}
			} else {
				if(this.addEventListener) {
					this.addEventListener(type, handler, false);
				} else if(this.attachEvent) {
					this.attachEvent("on"+type, handler);
				}
			}
		},
		RemoveEvent: function(type, handler) {
			if(typeof(this)=="object") {
				for(var i in this) {
					if(!cardinalEngine.isNumeric(i)) {
						continue;
					}
					if(this[i].removeEventListener) {
						this[i].removeEventListener(type, handler, false);
					} else if(this[i].detachEvent) {
						this[i].detachEvent("on"+type, handler);
					}
				}
			} else {
				if(this.removeEventListener) {
					this.removeEventListener(type, handler, false);
				} else if(this.detachEvent) {
					this.detachEvent("on"+type, handler);
				}
			}
		},
		ClearEvent: function(type) {
			if(typeof(this)=="object") {
				for(var i in this) {
					if(!cardinalEngine.isNumeric(i)) {
						continue;
					}
					cardinalEngine.ClearEvent(this[i], type);
				}
			} else {
				cardinalEngine.ClearEvent(this, type);
			}
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
				for(var i in this) {
					if(!cardinalEngine.isNumeric(i)) {
						continue;
					}
					this[i].style.display = "";
				}
			} else {
				this.style.display = "";
			}
		},
		hide: function() {
			if(typeof(this)=="object") {
				for(var i in this) {
					if(!cardinalEngine.isNumeric(i)) {
						continue;
					}
					this[i].style.display = "none";
				}
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
				if(typeof(this)=="object") {
					arr = [];
					for(var i in this) {
						if(!cardinalEngine.isNumeric(i)) {
							continue;
						}
						arr[i] = this[i].setAttribute(attr, set);
					}
					return arr;
				} else {
					return this.setAttribute(attr, set);
				}
			} else {
				if(typeof(this)=="object") {
					arr = [];
					for(var i in this) {
						if(!cardinalEngine.isNumeric(i)) {
							continue;
						}
						arr[i] = this[i].getAttribute(attr);
					}
					return arr;
				} else {
					return this.getAttribute(attr);
				}
			}
		},
		css: function(attr, set) {
			if(typeof(set)!="undefined") {
				if(typeof(this)=="object") {
					arr = [];
					for(var i in this) {
						if(!cardinalEngine.isNumeric(i)) {
							continue;
						}
						arr[i] = (this[i].style[attr] = set);
					}
					return arr;
				} else {
					return this.style[attr] = set;
				}
			} else {
				if(typeof(this)=="object") {
					arr = [];
					for(var i in this) {
						if(!cardinalEngine.isNumeric(i)) {
							continue;
						}
						arr[i] = this[i].style[attr];
					}
					return arr;
				} else {
					return this.style[attr];
				}
			}
		},
		text: function() {
			return this.textContent;
		},
		is: function(check) {
			if(typeof(this)=="object") {
				arr = [];
				for(var i in this) {
					if(!cardinalEngine.isNumeric(i)) {
						continue;
					}
					var matches = function(el, selector) {
						return (el.matches || el.matchesSelector || el.msMatchesSelector || el.mozMatchesSelector || el.webkitMatchesSelector || el.oMatchesSelector).call(el, selector);
					};
					arr[i] = matches(this[i], check);
				}
				return arr;
			} else {
				var matches = function(el, selector) {
					return (el.matches || el.matchesSelector || el.msMatchesSelector || el.mozMatchesSelector || el.webkitMatchesSelector || el.oMatchesSelector).call(el, selector);
				};
				return matches(this, check);
			}
		},
		next: function() {
			if(typeof(this)=="object") {
				arr = [];
				for(var i in this) {
					if(!cardinalEngine.isNumeric(i)) {
						continue;
					}
					arr[i] = this[i].nextElementSibling;
				}
				return arr;
			} else {
				return this.nextElementSibling;
			}
		},
		offset: function() {
			if(typeof(this)=="object") {
				arr = [];
				for(var i in this) {
					if(!cardinalEngine.isNumeric(i)) {
						continue;
					}
					var rect = el.getBoundingClientRect();
					arr[i] = { top: rect.top + document.body.scrollTop, left: rect.left + document.body.scrollLeft };
				}
				return arr;
			} else {
				var rect = el.getBoundingClientRect();
				return { top: rect.top + document.body.scrollTop, left: rect.left + document.body.scrollLeft };
			}
		},
		position: function() {
			if(typeof(this)=="object") {
				arr = [];
				for(var i in this) {
					if(!cardinalEngine.isNumeric(i)) {
						continue;
					}
					arr[i] = { left: this[i].offsetLeft, top: this[i].offsetTop };
				}
				return arr;
			} else {
				return { left: this.offsetLeft, top: this.offsetTop };
			}
		},
		prepend: function(el) {
			if(typeof(this)=="object") {
				for(var i in this) {
					if(!cardinalEngine.isNumeric(i)) {
						continue;
					}
					this[i].insertBefore(el, this[i].firstChild);
				}
			} else {
				this.insertBefore(el, this.firstChild);
			}
		},
		remove: function() {
			if(typeof(this)=="object") {
				for(var i in this) {
					if(!cardinalEngine.isNumeric(i)) {
						continue;
					}
					this[i].parentNode.removeChild(this[i]);
				}
			} else {
				this.parentNode.removeChild(this);
			}
		},
		html: function(set) {
			if(typeof(set)!="undefined") {
				if(typeof(this)=="object") {
					arr = [];
					for(var i in this) {
						if(!cardinalEngine.isNumeric(i)) {
							continue;
						}
						arr[i] = (this[i].innerHTML = set);
					}
					return arr;
				} else {
					return this.innerHTML = set;
				}
			} else {
				if(typeof(this)=="object") {
					arr = [];
					for(var i in this) {
						if(!cardinalEngine.isNumeric(i)) {
							continue;
						}
						arr[i] = this[i].innerHTML;
					}
					return arr;
				} else {
					return this.innerHTML;
				}
			}
		},
		each: function(fn) {
			return this.forEach(fn);
		},
		innerHeight: function() {
			var isWin = cardinalEngine.isWindow(this);
			return (isWin ? this.innerHeight : this.clientHeight);
		},
		innerWidth: function() {
			var isWin = cardinalEngine.isWindow(this);
			return (isWin ? this.innerWidth : this.clientWidth);
		},
		outerHeight: function(includeMargin) {
			var height = this.innerHeight();
			var computedStyle;
			if(includeMargin && !cardinalEngine.isWindow(this)) {
				computedStyle = window.getComputedStyle(this);
				height += parseInt(computedStyle.marginTop, 10);
				height += parseInt(computedStyle.marginBottom, 10);
			}
			return height;
		},
		outerWidth: function(includeMargin) {
			var width = this.innerWidth()
			var computedStyle;
			if (includeMargin && !isWindow(this)) {
				computedStyle = window.getComputedStyle(this);
				width += parseInt(computedStyle.marginLeft, 10);
				width += parseInt(computedStyle.marginRight, 10);
			}
			return width;
		},
		scrollTop: function() {
			var win = cardinalEngine.getWindow(this);
			return win ? win.pageYOffset : this.scrollTop;
		},
		scrollLeft: function() {
			var win = cardinalEngine.getWindow(this);
			return win ? win.pageXOffset : this.scrollLeft;
		},
		bind: function(event, handler) {
			var eventParts = event.split('.');
			var eventType = eventParts[0];
			var namespace = eventParts[1] || '__default';
			var nsHandlers = this.handlers[namespace] = this.handlers[namespace] || {};
			var nsTypeList = nsHandlers[eventType] = nsHandlers[eventType] || [];
			nsTypeList.push(handler);
			this.addEventListener(eventType, handler);
		},
		unbind: function(event, handler) {
			function removeListeners(element, listeners, handler) {
				for(var i=0,end=listeners.length-1;i<end;i++) {
					var listener = listeners[i];
					if(!handler || handler === listener) {
						element.removeEventListener(listener);
					}
				}
			}
			var eventParts = event.split('.');
			var eventType = eventParts[0];
			var namespace = eventParts[1];
			var element = this;
			if (namespace && this.handlers[namespace] && eventType) {
				removeListeners(element, this.handlers[namespace][eventType], handler);
				this.handlers[namespace][eventType] = [];
			} else if(eventType) {
				for(var ns in this.handlers) {
					removeListeners(element, this.handlers[ns][eventType] || [], handler);
					this.handlers[ns][eventType] = [];
				}
			} else if(namespace && this.handlers[namespace]) {
				for(var type in this.handlers[namespace]) {
					removeListeners(element, this.handlers[namespace][type], handler);
				}
				this.handlers[namespace] = {};
			}
		}
	};
}
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
	if(typeof(this)=="object") {
		var lastId = [];
		for(var i in this) {
			if(!cardinalEngine.isNumeric(i)) {
				continue;
			}
			var el = this[i];
			if(el.style==undefined) {
				return;
			}
			setTimeout(function(el) {
				el.style.opacity = 0;
				console.log(el);
				var last = +(new Date());
				var tick = function() {
					el.style.opacity += (+(new Date() - last) / 400);
					last = +new Date();
					if(+el.style.opacity < 1) {
						lastId[i] = (window.requestAnimationFrame && requestAnimationFrame(tick)) || setTimeout(tick, 16);
					} else {
						(window.cancelAnimationFrame && cancelAnimationFrame(lastId[i])) || clearTimeout(lastId[i]);
					}
				};
				tick();
			}, 10+i, el, i);
		}
	} else {
		var el = this;
		if(el.style==undefined) {
			return;
		}
		el.style.opacity = 0;
		var last = +(new Date());
		var lastId;
		var tick = function() {
			el.style.opacity += (+(new Date() - last) / 400);
			last = +new Date();
			if(+el.style.opacity < 1) {
				lastId = (window.requestAnimationFrame && requestAnimationFrame(tick)) || setTimeout(tick, 16);
			} else {
				(window.cancelAnimationFrame && cancelAnimationFrame(lastId)) || clearTimeout(lastId);
			}
		};
		tick();
	}
};
cardinalEngineElement['fadeOut'] = function() {
	if(typeof(this)=="object") {
		var lastId = [];
		for(var i in this) {
			if(!cardinalEngine.isNumeric(i)) {
				continue;
			}
			var el = this[i];
			if(el.style==undefined) {
				return;
			}
			setTimeout(function(el) {
				el.style.opacity = 1;
				var last = +(new Date());
				var tick = function() {
					el.style.opacity -= (+(new Date() - last) / 400);
					last = +new Date();
					if(+el.style.opacity > 0) {
						lastId[i] = (window.requestAnimationFrame && requestAnimationFrame(tick)) || setTimeout(tick, 16);
					} else {
						(window.cancelAnimationFrame && cancelAnimationFrame(lastId[i])) || clearTimeout(lastId[i]);
					}
				};
				tick();
			}, 10+i, el, i);
		}
	} else {
		var el = this;
		if(el.style==undefined) {
			return;
		}
		el.style.opacity = 1;
		var last = +(new Date());
		var lastId;
		var tick = function() {
			el.style.opacity -= (+(new Date() - last) / 400);
			last = +new Date();
			if(+el.style.opacity > 0) {
				lastId = (window.requestAnimationFrame && requestAnimationFrame(tick)) || setTimeout(tick, 16);
			} else {
				(window.cancelAnimationFrame && cancelAnimationFrame(lastId)) || clearTimeout(lastId);
			}
		};
		tick();
	}
};
cardinalEngineElement['animate'] = function(draw, duration) {
	var start = performance.now();
	var th = this;
	requestAnimationFrame(function animate(time) {
		var timePassed = time - start;
		if(timePassed > duration) timePassed = duration;
		draw(timePassed, th);
		if(timePassed < duration) {
			requestAnimationFrame(animate);
		}
	});
};
cardinalEngineElement['scrollCheck'] = function(sc, st) {
	var cacheElemth;
	cardinalEngine.AddEvent(window, "scroll", function() {
		clearTimeout(cacheElemth);
		sc();
		cacheElemth = setTimeout(function() {
			if(st!=undefined) {
				st();
			}
		}, 250);
	});
}
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
cardinalEngine.addLoad(function() {
	cardinalEngine.AddEvent(window, 'scroll', function(e) {
		var distanceY = window.pageYOffset || document.documentElement.scrollTop, header = ('querySelector' in document.documentElement ? document.querySelector(".header.isFixed") : document.getElementsByClassName("header isFixed")[0]);
		if(header==undefined) {
			return;
		}
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
	if(/constructor/i.test(window.HTMLElement)) {
		cardinalEngine.AddClass(document.body, "isSafari");
	}
	if(Object.prototype.toString.call(window.operamini) === '[object OperaMini]') {
		cardinalEngine.AddClass(document.body, "isOperaMini");
	}
	if(!!window.opera || /opera|opr/i.test(navigator.userAgent)) {
		cardinalEngine.AddClass(document.body, "isOpera");
	}
	if(document.all && !window.atob) {
		cardinalEngine.AddClass(document.body, "isIEL9");
	}
	if(window.navigator.msPointerEnabled) {
		cardinalEngine.AddClass(document.body, "isIEG10");
	}
	if('MozAppearance' in document.documentElement.style) {
		cardinalEngine.AddClass(document.body, "isFirefox");
	}
	if(!!window.chrome && !!window.chrome.webstore) {
		cardinalEngine.AddClass(document.body, "isChrome");
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
/*var cardinal = (function() {
    var cardinal = function(elems) {
		return new cardinal(elems);
	},
	cardinal = function(elems) {
		this.collection = elems[1] ? Array.prototype.slice.call(elems) : [elems];
		return this;
	};
	cardinal.fn = cardinal.prototype = cardinalEngineElement;
	return cardinal;
})();*/
var cardinalReadySelector = function(selector, context) {
	var array = [];
	if (typeof selector === 'string' ) {
		if (/^(?:\s*(<[\w\W]+>)[^>]*|#([\w-]+))$/.exec(selector)) {
			var DOM = new DOMParser().parseFromString(selector, 'text/html');
			var DOMList = DOM.body.childNodes;
			if(!context || {}.toString.call(context)!=='[object Object]') {
				context = null;
			}
			for(var i=0;i<DOMList.length;i++) {
				if(context) {
					for(var attr in context) {
						DOMList[i].setAttribute(attr, context + '');
					};
				};
				array[array.length] = DOMList[i];
			}
			return array;
		} else {
			var DOMList = {}.toString.call(context) === '[object HTMLElement]' ? context.querySelectorAll(selector) : document.querySelectorAll(selector);
			for(var i=0;i<DOMList.length;i++) {
				array[array.length] = DOMList[i];
			}
			return array;
		};
	} else if({}.toString.call(selector) === '[object Array]') {
		for (var i=0;i<selector.length;i++) {
			array[array.length] = selector[i];
		}
		return array;
	} else if({}.toString.call(selector) === '[object Object]' || {}.toString.call(selector) === '[object HTMLElement]') {
		array[0] = selector;
		return array;
	} else if({}.toString.call(selector) === '[object HTMLCollection]' || {}.toString.call(selector) === '[object NodeList]') {
		for(var i=0;i<selector.length;i++) {
			array[array.length] = selector[i];
		}
		return array;
	} else {
		return array;
	}
}
var cardinal = function(selector, context) {
    var array = new cardinalReadySelector(selector, context);
    var object = {
        __proto__: cardinal.prototype
    }
    for(var i=0;i<array.length;i++) {
        object[i] = array[i];
    }
    object.length = array.length;
    return object;
};
Object.keys(cardinalEngineElement).forEach(function(elem) {
	Object.defineProperty(Element.prototype, elem, cardinalEngineElement[elem]);
	cardinal.prototype[elem] = cardinalEngineElement[elem];
});
cardinalEngine.readyLoad();