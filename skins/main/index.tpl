[SET {fileExists}={{FN_'file_exists','{D_PATH_MEDIA}firstUser.lock'}}]
<div class="centred {fileExists}">
	<div>
		<img src="{THEME}/../core/cardinal.svg">
		<p>Поздравляем с успешной установкой <b>Cardinal Engine</b>!<br>Приятного использования!</p>
		[if {fileExists}==false]
		<a href="{C_default_http_local}{D_ADMINCP_DIRECTORY}/" target="_blank">Войти в админ панель</a>
		<p>Доступы к админ-панели:</p>
		<div><label for="">Логин:</label><input type="text" readonly="readonly" value="{FN_'User::getUserData','admin;username'}" onclick="this.select()"></div>
		<div><label for="">Пароль:</label><input type="text" readonly="readonly" value="{FN_'User::getUserData','admin;light'}" onclick="this.select()"></div>
		<span style="display:none;">{FN_'file_put_contents','{D_PATH_MEDIA}firstUser.lock;null'}</span>
		[/if {fileExists}==false]
	</div>
	<span id="garland" class="garland_4"><span id="nums_1">1</span></span>  
	<span id="muted" class="notLoad"></span>
</div>
<style>
	body {
		max-height: 100%;
		overflow: hidden;
	}
	.centred {
		display: inline-flex;
		align-items: center;
		height: 100vh;
		width: 100%;
		color: #fff;
		text-shadow: 0px 1px 6px #000;
		font-size: 1.1em;
	}
	.centred:before {
		content: '';
		background: url('https://images5.alphacoders.com/700/thumb-1920-700049.jpg');
		position: absolute;
		top: 0em;
		left: 0em;
		z-index: -1;
		width: 100%;
		height: 100%;
		background-size: cover;
		background-position: center center;
		-webkikt-filter: url('#myblurfilter') hue-rotate(40deg); 
		filter: url('#myblurfilter') hue-rotate(40deg);
		opacity: 0.75;
	}
	.centred:after {
		content: '';
		position: absolute;
		top: 0px;
		left: 0px;
		width: 100%;
		height: 100%;
		z-index: -2;
		background: #000
	}
	.centred > div {
		margin: 0px auto;
		font-family: 'Roboto',sans-serif;
		filter: drop-shadow(0px 1px 1px #222);
		font-weight: 500;
		letter-spacing: 0.03em;
		color: #eee;
		margin-top: 4.25em;
		text-align: center;
		line-height: 1.6em
	}
	.centred.false > div {
		margin-top: 17.25em;
	}
	.centred > div > img {
		height: 80px;
		margin: 0px auto 2em;
		display: table
	}
	.centred > div > p {
		margin: 0px 0px 0.75em;
	}
	.centred > div > a {
		border-radius: 0.3em;
		padding: 1em;
		display: inline-block;
		margin: 1em auto;
		color: #000;
		background: #fff;
		text-shadow: none;
		letter-spacing: 0.01em;
		transition: all 300ms ease-in-out;
	}
	.centred > div > a:hover {
		background: #0054b5;
		color: #fff;
	}
	.centred label {
		text-align: right;
		margin-right: 1em;
		min-width: 75px;
	}
	.centred input {
		border: 1px solid #fff;
		border-radius: 0.3em;
		padding: 0.4em;
		color: #fff;
		width: 220px;
	}
	#muted {
		position: fixed;
		bottom: 2em;
		right: 2em;
		background: url('data:image/svg+xml;base64,PD94bWwgdmVyc2lvbj0iMS4wIiBlbmNvZGluZz0idXRmLTgiPz4KPCFET0NUWVBFIHN2ZyBQVUJMSUMgIi0vL1czQy8vRFREIFNWRyAxLjEvL0VOIiAiaHR0cDovL3d3dy53My5vcmcvR3JhcGhpY3MvU1ZHLzEuMS9EVEQvc3ZnMTEuZHRkIj4KPHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHhtbG5zOnhsaW5rPSJodHRwOi8vd3d3LnczLm9yZy8xOTk5L3hsaW5rIiB3aWR0aD0iNTEycHgiIHZlcnNpb249IjEuMSIgaGVpZ2h0PSI1MTJweCIgdmlld0JveD0iMCAwIDY0IDY0IiBlbmFibGUtYmFja2dyb3VuZD0ibmV3IDAgMCA2NCA2NCI+CiAgPGc+CiAgICA8ZyBmaWxsPSIjMUQxRDFCIj4KICAgICAgPHBhdGggZD0ibTguMTQ2LDIyLjc2OGMxLjE1NiwwIDIuMDkxLTAuOTM2IDIuMDkxLTIuMDkxcy0wLjkzNS0yLjA5MS0yLjA5MS0yLjA5MWMtNC40OTEsMC04LjE0NiwzLjE0OS04LjE0Niw3LjAyMXYxNy44MThjMCwzLjg3MiAzLjY1NSw3LjAyMSA4LjE0Niw3LjAyMSAxLjE1NiwwIDIuMDkxLTAuOTM2IDIuMDkxLTIuMDkxcy0wLjkzNS0yLjA5MS0yLjA5MS0yLjA5MWMtMi4xNDcsMC0zLjk2NC0xLjMtMy45NjQtMi44Mzl2LTE3LjgxOGMwLTEuNTM5IDEuODE2LTIuODM5IDMuOTY0LTIuODM5eiIgZmlsbD0iIzAwNkRGMCIvPgogICAgICA8cGF0aCBkPSJtMzQuNDU0LDQuNTMzYy0wLjY2OC0wLjM2Ni0xLjQ4NC0wLjM0LTIuMTMxLDAuMDczbC0yMC4zNjEsMTMuMDI0Yy0wLjYwMSwwLjM4NC0wLjk2MywxLjA0Ny0wLjk2MywxLjc2djI5LjgyNmMwLDAuNzI2IDAuMzc0LDEuMzk5IDAuOTkzLDEuNzc5bDIwLjM2MSwxMi41NTFjMC4zMzcsMC4yMDcgMC43MTcsMC4zMTIgMS4wOTYsMC4zMTIgMC4zNTIsMCAwLjcwMi0wLjA4OCAxLjAyMS0wLjI2NiAwLjY2My0wLjM3IDEuMDctMS4wNjcgMS4wNy0xLjgyNXYtNTUuNDAxYzAtMC43NjMtMC40MTctMS40NjctMS4wODYtMS44MzN6bS0zLjA5Niw1My40ODlsLTE2LjE3OC05Ljk3M3YtMjcuNTE0bDE2LjE3OS0xMC4zNTF2NDcuODM4eiIgZmlsbD0iIzAwNkRGMCIvPgogICAgICA8cGF0aCBkPSJtNTIuMTI5LDE2LjYzMmMtMC40Ni0xLjA1OC0xLjY5MS0xLjUzOC0yLjc0Ny0xLjA4NC0xLjA2LDAuNDU5LTEuNTQ1LDEuNjg5LTEuMDksMi43NDkgMC4wNjMsMC4xNDUgNi4xNzgsMTQuNjk2LTAuMDQ0LDMxLjY1MS0wLjM5NiwxLjA4MyAwLjE1OSwyLjI4NSAxLjI0NCwyLjY4MyAwLjIzOCwwLjA4NyAwLjQ4LDAuMTI4IDAuNzE5LDAuMTI4IDAuODUzLDAgMS42NTItMC41MjUgMS45NjQtMS4zNzIgNi44MTQtMTguNTgyIDAuMjM3LTM0LjEwMy0wLjA0Ni0zNC43NTV6IiBmaWxsPSIjMDA2REYwIi8+CiAgICAgIDxwYXRoIGQ9Im00My4xOTEsMjQuNzc5Yy0xLjExOCwwLjI3OC0xLjgwMSwxLjQxMi0xLjUyMiwyLjUzMiAwLjAxNiwwLjA2MiAxLjUyMiw2LjMxMi0wLjAxNiwxMy41ODMtMC4yNDIsMS4xMyAwLjQ4MSwyLjIzOSAxLjYxMywyLjQ3OSAwLjE0NSwwLjAzIDAuMjksMC4wNDUgMC40MzMsMC4wNDUgMC45NjcsMCAxLjgzNy0wLjY3NCAyLjA0NS0xLjY1OCAxLjc0NS04LjI1NCAwLjA1My0xNS4xNzItMC4wMjEtMTUuNDYyLTAuMjc4LTEuMTItMS40MTItMS43OTUtMi41MzItMS41MTl6IiBmaWxsPSIjMDA2REYwIi8+CiAgICA8L2c+CiAgPC9nPgo8L3N2Zz4K');
		background-position: 50% 50%;
		background-size: contain;
		width: 2em;
		height: 2em;
		filter: drop-shadow(0px 0px 1px #ccc) hue-rotate(30deg);
	}
	#muted.muted {
		background-image: url('data:image/svg+xml;base64,PD94bWwgdmVyc2lvbj0iMS4wIiBlbmNvZGluZz0idXRmLTgiPz4KPCFET0NUWVBFIHN2ZyBQVUJMSUMgIi0vL1czQy8vRFREIFNWRyAxLjEvL0VOIiAiaHR0cDovL3d3dy53My5vcmcvR3JhcGhpY3MvU1ZHLzEuMS9EVEQvc3ZnMTEuZHRkIj4KPHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHhtbG5zOnhsaW5rPSJodHRwOi8vd3d3LnczLm9yZy8xOTk5L3hsaW5rIiB3aWR0aD0iNTEycHgiIHZlcnNpb249IjEuMSIgaGVpZ2h0PSI1MTJweCIgdmlld0JveD0iMCAwIDY0IDY0IiBlbmFibGUtYmFja2dyb3VuZD0ibmV3IDAgMCA2NCA2NCI+CiAgPGc+CiAgICA8ZyBmaWxsPSIjMUQxRDFCIj4KICAgICAgPHBhdGggZD0ibTguMTQ2LDIyLjc2OGMxLjE1NiwwIDIuMDkxLTAuOTM2IDIuMDkxLTIuMDkxcy0wLjkzNS0yLjA5MS0yLjA5MS0yLjA5MWMtNC40OTEsMC04LjE0NiwzLjE0OS04LjE0Niw3LjAyMXYxNy44MThjMCwzLjg3MiAzLjY1NSw3LjAyMSA4LjE0Niw3LjAyMSAxLjE1NiwwIDIuMDkxLTAuOTM2IDIuMDkxLTIuMDkxcy0wLjkzNS0yLjA5MS0yLjA5MS0yLjA5MWMtMi4xNDcsMC0zLjk2NC0xLjMtMy45NjQtMi44Mzl2LTE3LjgxOGMwLTEuNTM5IDEuODE2LTIuODM5IDMuOTY0LTIuODM5eiIgZmlsbD0iIzAwNkRGMCIvPgogICAgICA8cGF0aCBkPSJtMzQuNDU0LDQuNTMzYy0wLjY2OC0wLjM2Ni0xLjQ4NC0wLjM0LTIuMTMxLDAuMDczbC0yMC4zNjEsMTMuMDI0Yy0wLjYwMSwwLjM4NC0wLjk2MywxLjA0Ny0wLjk2MywxLjc2djI5LjgyNmMwLDAuNzI2IDAuMzc0LDEuMzk5IDAuOTkzLDEuNzc5bDIwLjM2MSwxMi41NTFjMC4zMzcsMC4yMDcgMC43MTcsMC4zMTIgMS4wOTYsMC4zMTIgMC4zNTIsMCAwLjcwMi0wLjA4OCAxLjAyMS0wLjI2NiAwLjY2My0wLjM3IDEuMDctMS4wNjcgMS4wNy0xLjgyNXYtNTUuNDAxYzAtMC43NjMtMC40MTctMS40NjctMS4wODYtMS44MzN6bS0zLjA5Niw1My40ODlsLTE2LjE3OC05Ljk3M3YtMjcuNTE0bDE2LjE3OS0xMC4zNTF2NDcuODM4eiIgZmlsbD0iIzAwNkRGMCIvPgogICAgPC9nPgogIDwvZz4KPC9zdmc+Cg==');
	}
	#muted.notLoad {
		opacity: 0.5;
	}
	canvas#snowflakesCanvas {
	    position: fixed;
	    top: 0px;
	    left: 0px;
	}
	#garland{position:fixed;top:0;left:0;background-image:url('{THEME}/img/garland.png');height:36px;width:100%;overflow:hidden;z-index:99}
	#nums_1{display:none;}
	.garland_1{background-position: 0 0}
	.garland_2{background-position: 0 -36px}
	.garland_3{background-position: 0 -72px}
	.garland_4{background-position: 0 -108px}
</style>
<svg xmlns="http://www.w3.org/2000/svg" version="1.1" height="0" style="display:none;">
	<filter id="myblurfilter" width="110%" height="110%">
		<feColorMatrix in="SourceGraphic" type="saturate" values="1.5" />
		<feGaussianBlur stdDeviation="5" result="blur" />
	</filter>
</svg>
<script type="text/javascript">
	function garland() {  
	nums = document.getElementById('nums_1').innerHTML;
	if(nums == 1) {document.getElementById('garland').className='garland_1';document.getElementById('nums_1').innerHTML='2'}
	if(nums == 2) {document.getElementById('garland').className='garland_2';document.getElementById('nums_1').innerHTML='3'}
	if(nums == 3) {document.getElementById('garland').className='garland_3';document.getElementById('nums_1').innerHTML='4'}
	if(nums == 4) {document.getElementById('garland').className='garland_4';document.getElementById('nums_1').innerHTML='1'}
	}
	setInterval(function(){garland()}, 300) 
	context = new AudioContext();
	var t = new Audio();
	t.src = "{THEME}/holidays_-_jingle_bells.mp3";
	t.preload="true";
	var btn = document.querySelector("#muted");
	t.addEventListener('loadeddata', function() {
		t.loop=true;
		t.muted=true;
		t.autoplay=true;
		t.playbackRate=1.5;
		t.play().then(function() {
			btn.className = btn.className.replace("notLoad", "");
			t.muted=false;
		});
	});
	btn.addEventListener("click", function(e) {
		e.preventDefault();
		if(this.className.indexOf("notLoad")>-1) {
			return false;
		}
		t.muted = (!t.muted);
		if(t.muted) {
			this.className += " muted ";
		} else {
			this.className = t.className.replace(" muted ", "");
		}
	});
	t.load();
	var aaSnowConfig = {snowflakes: '200'};
	var idleTimeout = 1000,
		idleNow = false,
		idleTimestamp = null,
		idleTimer = null;
	 
	function setIdleTimeout(ms){
	    idleTimeout = ms;
	    idleTimestamp = new Date().getTime() + ms;
	    if (idleTimer != null) {
		clearTimeout (idleTimer);
	    }
	    idleTimer = setTimeout(makeIdle, ms + 50);
	}
	 
	function makeIdle(){
	    var t = new Date().getTime();
	    if (t < idleTimestamp) {
			idleTimer = setTimeout(makeIdle, idleTimestamp - t + 50);
			return;
	    }
	    // console.log('** IDLE **');
	    idleNow = true;
	    try {
			if (document.onIdle) document.onIdle();
	    } catch (err) {
	    }
	}
	 
	function active(event){
	    var t = new Date().getTime();
	    idleTimestamp = t + idleTimeout;
	    // console.log('not idle.');
	 
	    if (idleNow) {
			setIdleTimeout(idleTimeout);
	    }
		// console.log('** BACK **');
		if ((idleNow) && document.onBack) document.onBack(idleNow);

	    idleNow = false;
	}

	var ready = (function(){

	    var readyList,
	        DOMContentLoaded,
	        class2type = {};
	        class2type["[object Boolean]"] = "boolean";
	        class2type["[object Number]"] = "number";
	        class2type["[object String]"] = "string";
	        class2type["[object Function]"] = "function";
	        class2type["[object Array]"] = "array";
	        class2type["[object Date]"] = "date";
	        class2type["[object RegExp]"] = "regexp";
	        class2type["[object Object]"] = "object";

	    var ReadyObj = {
	        // Is the DOM ready to be used? Set to true once it occurs.
	        isReady: false,
	        // A counter to track how many items to wait for before
	        // the ready event fires. See #6781
	        readyWait: 1,
	        // Hold (or release) the ready event
	        holdReady: function( hold ) {
	            if ( hold ) {
	                ReadyObj.readyWait++;
	            } else {
	                ReadyObj.ready( true );
	            }
	        },
	        // Handle when the DOM is ready
	        ready: function( wait ) {
	            // Either a released hold or an DOMready/load event and not yet ready
	            if ( (wait === true && !--ReadyObj.readyWait) || (wait !== true && !ReadyObj.isReady) ) {
	                // Make sure body exists, at least, in case IE gets a little overzealous (ticket #5443).
	                if ( !document.body ) {
	                    return setTimeout( ReadyObj.ready, 1 );
	                }

	                // Remember that the DOM is ready
	                ReadyObj.isReady = true;
	                // If a normal DOM Ready event fired, decrement, and wait if need be
	                if ( wait !== true && --ReadyObj.readyWait > 0 ) {
	                    return;
	                }
	                // If there are functions bound, to execute
	                readyList.resolveWith( document, [ ReadyObj ] );

	                // Trigger any bound ready events
	                //if ( ReadyObj.fn.trigger ) {
	                //    ReadyObj( document ).trigger( "ready" ).unbind( "ready" );
	                //}
	            }
	        },
	        bindReady: function() {
	            if ( readyList ) {
	                return;
	            }
	            readyList = ReadyObj._Deferred();

	            // Catch cases where $(document).ready() is called after the
	            // browser event has already occurred.
	            if ( document.readyState === "complete" ) {
	                // Handle it asynchronously to allow scripts the opportunity to delay ready
	                return setTimeout( ReadyObj.ready, 1 );
	            }

	            // Mozilla, Opera and webkit nightlies currently support this event
	            if ( document.addEventListener ) {
	                // Use the handy event callback
	                document.addEventListener( "DOMContentLoaded", DOMContentLoaded, false );
	                // A fallback to window.onload, that will always work
	                window.addEventListener( "load", ReadyObj.ready, false );

	            // If IE event model is used
	            } else if ( document.attachEvent ) {
	                // ensure firing before onload,
	                // maybe late but safe also for iframes
	                document.attachEvent( "onreadystatechange", DOMContentLoaded );

	                // A fallback to window.onload, that will always work
	                window.attachEvent( "onload", ReadyObj.ready );

	                // If IE and not a frame
	                // continually check to see if the document is ready
	                var toplevel = false;

	                try {
	                    toplevel = window.frameElement == null;
	                } catch(e) {}

	                if ( document.documentElement.doScroll && toplevel ) {
	                    doScrollCheck();
	                }
	            }
	        },
	        _Deferred: function() {
	            var // callbacks list
	                callbacks = [],
	                // stored [ context , args ]
	                fired,
	                // to avoid firing when already doing so
	                firing,
	                // flag to know if the deferred has been cancelled
	                cancelled,
	                // the deferred itself
	                deferred  = {

	                    // done( f1, f2, ...)
	                    done: function() {
	                        if ( !cancelled ) {
	                            var args = arguments,
	                                i,
	                                length,
	                                elem,
	                                type,
	                                _fired;
	                            if ( fired ) {
	                                _fired = fired;
	                                fired = 0;
	                            }
	                            for ( i = 0, length = args.length; i < length; i++ ) {
	                                elem = args[ i ];
	                                type = ReadyObj.type( elem );
	                                if ( type === "array" ) {
	                                    deferred.done.apply( deferred, elem );
	                                } else if ( type === "function" ) {
	                                    callbacks.push( elem );
	                                }
	                            }
	                            if ( _fired ) {
	                                deferred.resolveWith( _fired[ 0 ], _fired[ 1 ] );
	                            }
	                        }
	                        return this;
	                    },

	                    // resolve with given context and args
	                    resolveWith: function( context, args ) {
	                        if ( !cancelled && !fired && !firing ) {
	                            // make sure args are available (#8421)
	                            args = args || [];
	                            firing = 1;
	                            try {
	                                while( callbacks[ 0 ] ) {
	                                    callbacks.shift().apply( context, args );//shifts a callback, and applies it to document
	                                }
	                            }
	                            finally {
	                                fired = [ context, args ];
	                                firing = 0;
	                            }
	                        }
	                        return this;
	                    },

	                    // resolve with this as context and given arguments
	                    resolve: function() {
	                        deferred.resolveWith( this, arguments );
	                        return this;
	                    },

	                    // Has this deferred been resolved?
	                    isResolved: function() {
	                        return !!( firing || fired );
	                    },

	                    // Cancel
	                    cancel: function() {
	                        cancelled = 1;
	                        callbacks = [];
	                        return this;
	                    }
	                };

	            return deferred;
	        },
	        type: function( obj ) {
	            return obj == null ?
	                String( obj ) :
	                class2type[ Object.prototype.toString.call(obj) ] || "object";
	        }
	    }
	    // The DOM ready check for Internet Explorer
	    function doScrollCheck() {
	        if ( ReadyObj.isReady ) {
	            return;
	        }

	        try {
	            // If IE is used, use the trick by Diego Perini
	            // http://javascript.nwbox.com/IEContentLoaded/
	            document.documentElement.doScroll("left");
	        } catch(e) {
	            setTimeout( doScrollCheck, 1 );
	            return;
	        }

	        // and execute any waiting functions
	        ReadyObj.ready();
	    }
	    // Cleanup functions for the document ready method
	    if ( document.addEventListener ) {
	        DOMContentLoaded = function() {
	            document.removeEventListener( "DOMContentLoaded", DOMContentLoaded, false );
	            ReadyObj.ready();
	        };

	    } else if ( document.attachEvent ) {
	        DOMContentLoaded = function() {
	            // Make sure body exists, at least, in case IE gets a little overzealous (ticket #5443).
	            if ( document.readyState === "complete" ) {
	                document.detachEvent( "onreadystatechange", DOMContentLoaded );
	                ReadyObj.ready();
	            }
	        };
	    }
	    function ready( fn ) {
	        // Attach the listeners
	        ReadyObj.bindReady();

	        var type = ReadyObj.type( fn );

	        // Add the callback
	        readyList.done( fn );//readyList is result of _Deferred()
	    }
	    return ready;
	})();
	var doc = document;
	ready(function () {
		doc.addEventListener("mousemove", active); 
		try {
			doc.addEventListener("mouseenter", active);
		} catch (err) { }
		try {
			doc.addEventListener("scroll", active);
		} catch (err) { }
		try {
			doc.addEventListener("keydown", active);
		} catch (err) { }
		try {
			doc.addEventListener("click", active);
		} catch (err) { }
		try {
			doc.addEventListener("dblclick", active);
		} catch (err) { }
	});

	// Initialization and events code for the app
	(function () {
	    "use strict";

	    // preparing the elements we'll need further
	    var snowflakesCanvas = null;
	    var snowflakesContext = null;
		
	    function resizeCanvasElements() {
			// resize falling snowflakes canvas to fit the screen
	        snowflakesCanvas.width = window.innerWidth;
	        snowflakesCanvas.height = window.innerHeight;
	    }

	    ready(function () {
		    var canv = document.createElement("div");
	        canv.innerHTML = '<canvas id="snowflakesCanvas" />';
	        canv = canv.children[0];
			snowflakesCanvas = canv;
			document.body.appendChild(snowflakesCanvas );
			
			snowflakesCanvas = document.getElementById("snowflakesCanvas");
			snowflakesContext = snowflakesCanvas.getContext("2d");
			
			// initialiaze the Snowflakes
			Snowflakes.generate( aaSnowConfig.snowflakes );
			
			// initialize out animation functions and start animation:
			// falling snowflakes
			Animation.addFrameRenderer(Snowflakes.render, snowflakesContext);
			
			// start the animation
			Animation.start();
			
			if( aaSnowConfig.play_sound == true ){
				// start audio 
				playAudio.init( aaSnowConfig.volume, aaSnowConfig.mp3, aaSnowConfig.ogg );
				playAudio.play();
			}
			
			if( aaSnowConfig.hideUnderContentBlock != "" ){
				var jQSnow = snowflakesCanvas;
				var zInx = jQSnow.style['zIndex'];
				
				// set idle time out
				setIdleTimeout( 1000 );
				
				// go to idle function
				document.onIdle = function() {
					jQSnow.style['display']= "block";
					jQSnow.style['zIndex']= 9999;
				}
				
				// back from idle function
				document.onBack = function(isIdle) {
					if (isIdle) {
						if( aaSnowConfig.hideUnderContentBlock == false ) {
							jQSnow.style['display']= "none";
						}else {
							jQSnow.style['zIndex']=-1;
						}
					};
				}
			}
			
			// properly resize the canvases
			resizeCanvasElements();
	    });

	    window.addEventListener("resize", function () {
	        // properly resize the canvases
	        resizeCanvasElements();
	    });
	})();

	// single animation loop and fps calculation
	Animation = (function () {

	    "use strict";

	    // collection of function to render single frame (snowflakes falling, background gradient animation)
	    var frameRenderersCollection = [];
	    // each animation should be rendered on corresponding context. 
	    // If animation doesn't have context (non-visual parameter change every frame) - it should be last (several framerenderers can be last without contexts)
	    var renderContextesCollection = [];
	    // if animation is running
	    var isRunning = false;
		
		// show debug 
		var debug = false;
		
	    // callback pointer for cancelling
	    var animationCallback;
	    // if browser doesn't support requestAnimationFrame - we use setInterval for 60Hz displays (16.7ms per frame)
	    var minInterval = 16.7;

	    // fps tracking
	    var avgTime = 0;
	    var trackFrames = 60;
	    var frameCounter = 0;

	    // register new renderer and corresponding context
	    function addFrameRenderer(frameRender, renderContext) {
	        if (frameRender && typeof (frameRender) === "function") {
	            frameRenderersCollection.push(frameRender);
	            renderContextesCollection.push(renderContext);
	        }
	    }

	    // detecting requestAnimationFrame feature
	    function getRequestAnimationFrame(code) {
	        if (window.requestAnimationFrame) {
	            return window.requestAnimationFrame(code);
	        } else if (window.msRequestAnimationFrame) {
	            return window.msRequestAnimationFrame(code);
	        } else if (window.webkitRequestAnimationFrame) {
	            return window.webkitRequestAnimationFrame(code);
	        } else if (window.mozRequestAnimationFrame) {
	            return window.mozRequestAnimationFrame(code);
	        } else {
	            return setTimeout(code, minInterval);
	        }
	    }

	    // iterate and render all registered renderers
	    function frameRenderCore() {

	        var startDate = new Date();

	        for (var ii = 0; ii < frameRenderersCollection.length; ii++) {
	            if (frameRenderersCollection[ii]) {
	                frameRenderersCollection[ii](renderContextesCollection[ii]);
	            }
	        }

	        if (isRunning) {
	            animationCallback = getRequestAnimationFrame(frameRenderCore);
	        }

	        var endDate = new Date();
	        var duration = (endDate - startDate);
	        avgTime += duration;

	        // we count fps every trackFrames frame
	        frameCounter++;
	        if (frameCounter >= trackFrames) {
	            avgTime = avgTime / trackFrames;
	            var avgFps = Math.floor(1000 / avgTime);
	            if (avgFps > 60) avgFps = 60;

				if( debug === true ) {
					// update fps information and snowflake count if dynamic
					console.log({
						fps: avgFps,
						snowflakes: (Snowflakes.dynamicSnowflakesCount) ? Snowflakes.count() : ""
					});
				}

	            avgTime = 0;
	            frameCounter = 0;
	        }
	    }

	    // playback control: play, pause, toggle
	    function start() {
	        if (isRunning) return;
	        animationCallback = getRequestAnimationFrame(frameRenderCore);
	        isRunning = true;
	    }

	    function stop() {
	        if (!isRunning) return;
	        clearInterval(animationCallback);
	        isRunning = false;
	    }

	    function toggle() {
	        var playbackControl = (isRunning) ? stop : start;
	        playbackControl();
	    }

	    return {
	        "addFrameRenderer": addFrameRenderer,
	        "start": start,
	        "stop": stop,
	        "toggle": toggle,
	        "getRequestAnimationFrame": getRequestAnimationFrame
	    }

	})();

	Snowflakes = (function () {

	    "use strict";

	    // snowflakes objects collection
	    var snowflakes = [];
	    var snowflakesDefaultCount = 1000;
	    // if true - we'll guess the best number of snowflakes for the system
	    var dynamicSnowflakesCount = false;
	    // we increment snowflakes with this rate
	    var snowflakeCountIncrement = 0.1;
	    // we can remove aggressively (to quicker free system resources), basically we remove at snowflakeCountIncrement*snowflakeRemoveFactor rate
	    var snowflakeRemoveFactor = 2;
	    // snowflakes sprites
	    var snowflakeSpritesLocation = "data:image/png;base64,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";
	    var snowflakeSprites = [];
	    var spritesCount = 5;
	    var spriteWidth = 20;
	    var spriteHeight = 20;

	    // canvas bounds used for snowflake animation
	    var bounds = { width: window.innerWidth, height: window.innerHeight };

	    // particle movement parameters:
	    // we'll advance each particle vertically at least by this amount (think gravity and resistance)
	    var minVerticalVelocity = 1;
	    // we'll advance each particle vertically at most by this amount (think gravity and resistance)
	    var maxVerticalVelocity = 4;
	    // we'll shift each particle horizontally at least by this amound (think wind and resistance)
	    var minHorizontalVelocity = -1;
	    // we'll shift each particle horizontally at least by this amound (think wind and resistance)
	    var maxHorizontalVelocity = 3;
	    // each particle sprite will be scaled down maxScale / this (this < maxScale) at max
	    var minScale = 0.2;
	    // each particle sprite will be scaled down this / minScale (this > minScale) at max
	    var maxScale = 1.25;
	    // each particle also "bobs" on horizontal axis (think volumetric resistance) by this amount at least
	    var minHorizontalDelta = 2;
	    // each particle also "bobs" on horizontal axis (think volumetric resistance) by this amount at most
	    var maxHorizontalDelta = 3;
	    // each particle is at least this opaque
	    var minOpacity = 0.2;
	    // each particle is at least this opaque
	    var maxOpacity = 0.9;
	    // change opacity by at max 1/maxOpacityIncrement
	    var maxOpacityIncrement = 50;

	    // dynamic speed:
	    // do speed correction every speedCorrectionFrames frames
	    var speedCorrectionFrames = 60;
	    var currentSpeedCorrectionFrame = 0;
	    // start without any speed correction
	    var speedFactor = 1;
	    // fall down to this value at most
	    var minSpeedFactor = 0.1;
	    // get fast at this value at most
	    var maxSpeedFactor = 1.5;
	    // don't set value immidietly change gradually by this amount
	    var speedFactorDelta = 0.05;

	    // create number of snowflakes adding if required (or regenerate from scratch)
	    function generate(number, add) {
	        // initialize sprite
	        var image = new Image();
	        image.onload = function () {
	            for (var ii = 0; ii < spritesCount; ii++) {
	                var sprite = document.createElement("canvas");
	                sprite.width = spriteWidth;
	                sprite.height = spriteHeight;
	                var context = sprite.getContext("2d");
	                context.drawImage(
	                // source image
	                    image,
	                // source x
	                    ii * spriteWidth,
	                // source y
	                    0,
	                // source width
	                    spriteWidth,
	                // source height
	                    spriteHeight,
	                // target x
	                    0,
	                //target y
	                    0,
	                // target width
	                    spriteWidth,
	                // target height
	                    spriteHeight);
	                snowflakeSprites.push(sprite);
	            }

	            if (number) {
	                snowflakesDefaultCount = number;
	            }
	            if (!add) {
	                snowflakes = [];
	            }
	            for (var ii = 0; ii < snowflakesDefaultCount; ii++) {
	                snowflakes.push(generateSnowflake());
	            }
	        }
	        image.src = snowflakeSpritesLocation;
	    }

	    function generateSnowflake() {
	        var scale = Math.random() * (maxScale - minScale) + minScale;
	        return {
	            // x position
	            x: Math.random() * bounds.width,
	            // y position
	            y: Math.random() * bounds.height,
	            // vertical velocity
	            vv: Math.random() * (maxVerticalVelocity - minVerticalVelocity) + minVerticalVelocity,
	            // horizontal velocity
	            hv: Math.random() * (maxHorizontalVelocity - minHorizontalVelocity) + minHorizontalVelocity,
	            // scaled sprite width
	            sw: scale * spriteWidth,
	            // scaled sprite width
	            sh: scale * spriteHeight,
	            // maximum horizontal delta
	            mhd: Math.random() * (maxHorizontalDelta - minHorizontalDelta) + minHorizontalDelta,
	            // horizontal delta
	            hd: 0,
	            // horizontal delta increment
	            hdi: Math.random() / (maxHorizontalVelocity * minHorizontalDelta),
	            // opacity
	            o: Math.random() * (maxOpacity - minOpacity) + minOpacity,
	            // opacity increment
	            oi: Math.random() / maxOpacityIncrement,
	            // sprite index
	            si: Math.ceil(Math.random() * (spritesCount - 1)),
	            // not landing flag
	            nl: false
	        }
	    }
		
	    // help snowflakes fall
	    function advanceSnowFlakes() {
	        for (var ii = 0; ii < snowflakes.length; ii++) {
	            var sf = snowflakes[ii];
	            // we obey the gravity, 'cause it's the law
	            sf.y += sf.vv * speedFactor;
	            // while we're obeying the gravity, we do it with style
	            sf.x += (sf.hd + sf.hv) * speedFactor;
	            // advance horizontal axis "bobbing"                
	            sf.hd += sf.hdi;
	            // inverse "bobbing" direction if we get to maximum delta limit
	            if (sf.hd < -sf.mhd || sf.hd > sf.mhd) {
	                sf.hdi = -sf.hdi;
	            };

	            // increment opacity and check opacity value bounds
	            sf.o += sf.oi;
	            if (sf.o > maxOpacity || sf.o < minOpacity) { sf.oi = -sf.oi };
	            if (sf.o > maxOpacity) sf.o = maxOpacity;
	            if (sf.o < minOpacity) sf.o = minOpacity;
	            // return within dimensions bounds if we've crossed them
	            // and don't forget to reset the not-landing (sf.nl) flag
	            var resetNotLanding = false;
	            if (sf.y > bounds.height + spriteHeight / 2) {
	                sf.y = 0
	                resetNotLanding = true;
	            };
	            if (sf.y < 0) {
	                sf.y = bounds.height
	                resetNotLanding = true;
	            };
	            if (sf.x > bounds.width + spriteWidth / 2) {
	                sf.x = 0
	                resetNotLanding = true;
	            };
	            if (sf.x < 0) {
	                sf.x = bounds.width
	                resetNotLanding = true;
	            };
	            if (resetNotLanding) { sf.nl = false; }
	        }
	    }

	    // not using, but it allows to increase/decrease speed based on fps
	    // in essence - visual feedback on fps value
	    function adjustSpeedFactor() {
	        if (++currentSpeedCorrectionFrame === speedCorrectionFrames) {
	            var lastFps = SystemInformation.getLastFps();
	            var targetSpeedFactor = (lastFps * (maxSpeedFactor - minSpeedFactor) / 60) + minSpeedFactor;
	            speedFactor += (targetSpeedFactor < speedFactor) ? -speedFactorDelta : speedFactorDelta;
	            if (speedFactor > maxSpeedFactor) { speedFactor = maxSpeedFactor; }
	            if (speedFactor < minSpeedFactor) { speedFactor = minSpeedFactor; }
	            currentSpeedCorrectionFrame = 0;
	        }
	    }

	    function renderFrame(context) {
	        // fall down one iteration            
	        advanceSnowFlakes();
	        // clear context and save it 
	        context.clearRect(0, 0, context.canvas.width, context.canvas.height);
	        for (var ii = 0; ii < snowflakes.length; ii++) {
	            var sf = snowflakes[ii];
	            context.globalAlpha = sf.o;
	            context.drawImage(
	                // image
	                snowflakeSprites[sf.si],
	                // source x
	                0,
	                // source y
	                0,
	                // source width
	                spriteWidth,
	                // source height
	                spriteHeight,
	                // target x
	                sf.x,
	                // target y
	                sf.y,
	                // target width
	                sf.sw,
	                // target height
	                sf.sh);
	        }
	    }

	    function updateBounds() {
	        bounds.width = window.innerWidth;
	        bounds.height = window.innerHeight;
	    }

	    function count() {
	        return snowflakes.length;
	    }

	    // increase number of falling snowflakes
	    // the default increase is snowflakeCountIncrement
	    function add(number) {
	        if (!number) { number = snowflakes.length * snowflakeCountIncrement; }
	        generate(number, true);
	    }

	    // remove some snowflakes
	    // by default we remove more aggressively to free resources faster
	    function remove(number) {
	        if (!number) { number = snowflakes.length * snowflakeCountIncrement * snowflakeRemoveFactor; }
	        if (snowflakes.length - number > 0) {
	            snowflakes = snowflakes.slice(0, snowflakes.length - number);
	        }
	    }

	    return {
	        "generate": generate,
	        "add": add,
	        "remove": remove,
	        "render": renderFrame,
	        "count": count,
	        "updateBounds": updateBounds,
	        "dynamicSnowflakesCount": dynamicSnowflakesCount
	    }

	})();
</script>