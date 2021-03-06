(function() {
    var a;
    a = function() {
        var a, b, c;
        return b = [],
        a = function(b, d) {
            var e;
            return b ? "function" === a.toType(b) ? a(document).ready(b) : (e = a.getDOMObject(b, d), c(e, b)) : c()
        },
        c = function(a, d) {
            return a = a || b,
            a.__proto__ = c.prototype,
            a.selector = d || "",
            a
        },
        a.extend = function(a) {
            return Array.prototype.slice.call(arguments, 1).forEach(function(b) {
                var c, d;
                d = [];
                for (c in b) d.push(a[c] = b[c]);
                return d
            }),
            a
        },
        c.prototype = a.fn = {},
        a
    } (),
    window.Quo = a,
    "$$" in window || (window.$$ = a)
}).call(this),
function() { !
    function(a) {
        var b, c, d, e, f, g, h, i, j, k, l;
        return b = {
            TYPE: "GET",
            MIME: "json"
        },
        d = {
            script: "text/javascript, application/javascript",
            json: "application/json",
            xml: "application/xml, text/xml",
            html: "text/html",
            text: "text/plain"
        },
        c = 0,
        a.ajaxSettings = {
            type: b.TYPE,
            async: !0,
            success: {},
            error: {},
            context: null,
            dataType: b.MIME,
            headers: {},
            xhr: function() {
                return new window.XMLHttpRequest
            },
            crossDomain: !1,
            timeout: 0
        },
        a.ajax = function(c) {
            var d, h, k, m;
            if (k = a.mix(a.ajaxSettings, c), k.type === b.TYPE ? k.url += a.serializeParameters(k.data, "?") : k.data = a.serializeParameters(k.data), e(k.url)) return a.jsonp(k);
            m = k.xhr(),
            m.onreadystatechange = function() {
                return 4 === m.readyState ? (clearTimeout(d), j(m, k)) : void 0
            },
            m.open(k.type, k.url, k.async),
            i(m, k),
            k.timeout > 0 && (d = setTimeout(function() {
                return l(m, k)
            },
            k.timeout));
            try {
                m.send(k.data)
            } catch(n) {
                h = n,
                m = h,
                g("Resource not found", m, k)
            }
            return k.async ? m: f(m, k)
        },
        a.jsonp = function(b) {
            var d, e, f, g;
            return b.async ? (e = "jsonp" + ++c, f = document.createElement("script"), g = {
                abort: function() {
                    return a(f).remove(),
                    e in window ? window[e] = {}: void 0
                }
            },
            d = void 0, window[e] = function(c) {
                return clearTimeout(d),
                a(f).remove(),
                delete window[e],
                k(c, g, b)
            },
           f.src = b.url.replace(RegExp("=\\?"), "=" + e), a("head").append(f), b.timeout > 0 && (d = setTimeout(function() {
                return l(g, b)
            },
            b.timeout)), g) : console.error("QuoJS.ajax: Unable to make jsonp synchronous call.")
        },
        a.get = function(b, c, d, e) {
            return a.ajax({
                url: b,
                data: c,
                success: d,
                dataType: e
            })
        },
        a.post = function(a, b, c, d) {
            return h("POST", a, b, c, d)
        },
        a.put = function(a, b, c, d) {
            return h("PUT", a, b, c, d)
        },
        a["delete"] = function(a, b, c, d) {
            return h("DELETE", a, b, c, d)
        },
        a.json = function(c, d, e) {
            return a.ajax({
                url: c,
                data: d,
                success: e,
                dataType: b.MIME
            })
        },
        a.serializeParameters = function(a, b) {
            var c, d;
            null == b && (b = ""),
            d = b;
            for (c in a) a.hasOwnProperty(c) && (d !== b && (d += "&"), d += "" + encodeURIComponent(c) + "=" + encodeURIComponent(a[c]));
            return d === b ? "": d
        },
        j = function(a, b) {
            a.status >= 200 && a.status < 300 || 0 === a.status ? b.async && k(f(a, b), a, b) : g("QuoJS.ajax: Unsuccesful request", a, b)
        },
        k = function(a, b, c) {
            c.success.call(c.context, a, b)
        },
        g = function(a, b, c) {
            c.error.call(c.context, a, b, c)
        },
        i = function(a, b) {
            var c;
            b.contentType && (b.headers["Content-Type"] = b.contentType),
            b.dataType && (b.headers.Accept = d[b.dataType]);
            for (c in b.headers) a.setRequestHeader(c, b.headers[c])
        },
        l = function(a, b) {
            a.onreadystatechange = {},
            a.abort(),
            g("QuoJS.ajax: Timeout exceeded", a, b)
        },
        h = function(b, c, d, e, f) {
            return a.ajax({
                type: b,
                url: c,
                data: d,
                success: e,
                dataType: f,
                contentType: "application/x-www-form-urlencoded"
            })
        },
        f = function(a, c) {
            var d, e;
            if (e = a.responseText) if (c.dataType === b.MIME) try {
                e = JSON.parse(e)
            } catch(f) {
                d = f,
                e = d,
                g("QuoJS.ajax: Parse Error", a, c)
            } else "xml" === c.dataType && (e = a.responseXML);
            return e
        },
        e = function(a) {
            return RegExp("=\\?").test(a)
        }
    } (Quo)
}.call(this),
function() { !
    function(a) {
        var b, c, d, e, f, g, h, i;
        return b = [],
        e = Object.prototype,
        d = /^\s*<(\w+|!)[^>]*>/,
        f = document.createElement("table"),
        g = document.createElement("tr"),
        c = {
            tr: document.createElement("tbody"),
            tbody: f,
            thead: f,
            tfoot: f,
            td: g,
            th: g,
            "*": document.createElement("div")
        },
        a.toType = function(a) {
            return e.toString.call(a).match(/\s([a-z|A-Z]+)/)[1].toLowerCase()
        },
        a.isOwnProperty = function(a, b) {
            return e.hasOwnProperty.call(a, b)
        },
        a.getDOMObject = function(b, c) {
            var e, f, g;
            return e = null,
            f = [1, 9, 11],
            g = a.toType(b),
            "array" === g ? e = h(b) : "string" === g && d.test(b) ? (e = a.fragment(b.trim(), RegExp.$1), b = null) : "string" === g ? (e = a.query(document, b), c && (e = 1 === e.length ? a.query(e[0], c) : a.map(function() {
                return a.query(e, c)
            }))) : (f.indexOf(b.nodeType) >= 0 || b === window) && (e = [b], b = null),
            e
        },
        a.map = function(b, c) {
            var d, e, f, g;
            if (g = [], d = void 0, e = void 0, "array" === a.toType(b)) for (d = 0; d < b.length;) f = c(b[d], d),
            null != f && g.push(f),
            d++;
            else for (e in b) f = c(b[e], e),
            null != f && g.push(f);
            return i(g)
        },
        a.each = function(b, c) {
            var d, e;
            if (d = void 0, e = void 0, "array" === a.toType(b)) for (d = 0; d < b.length;) {
                if (c.call(b[d], d, b[d]) === !1) return b;
                d++
            } else for (e in b) if (c.call(b[e], e, b[e]) === !1) return b;
            return b
        },
        a.mix = function() {
            var b, c, d, e, f;
            for (d = {},
            b = 0, e = arguments.length; e > b;) {
                c = arguments[b];
                for (f in c) a.isOwnProperty(c, f) && void 0 !== c[f] && (d[f] = c[f]);
                b++
            }
            return d
        },
        a.fragment = function(b, d) {
            var e;
            return null == d && (d = "*"),
            d in c || (d = "*"),
            e = c[d],
            e.innerHTML = "" + b,
            a.each(Array.prototype.slice.call(e.childNodes),
            function() {
                return e.removeChild(this)
            })
        },
        a.fn.map = function(b) {
            return a.map(this,
            function(a, c) {
                return b.call(a, c, a)
            })
        },
        a.fn.instance = function(a) {
            return this.map(function() {
                return this[a]
            })
        },
        a.fn.filter = function(b) {
            return a([].filter.call(this,
            function(c) {
                return c.parentNode && a.query(c.parentNode, b).indexOf(c) >= 0
            }))
        },
        a.fn.forEach = b.forEach,
        a.fn.indexOf = b.indexOf,
        h = function(a) {
            return a.filter(function(a) {
                return void 0 !== a && null !== a
            })
        },
        i = function(a) {
            return a.length > 0 ? [].concat.apply([], a) : a
        }
    } (Quo)
}.call(this),
function() { !
    function(a) {
        return a.fn.attr = function(b, c) {
            return 0 === this.length && null,
            "string" === a.toType(b) && void 0 === c ? this[0].getAttribute(b) : this.each(function() {
                return this.setAttribute(b, c)
            })
        },
        a.fn.removeAttr = function(a) {
            return this.each(function() {
                return this.removeAttribute(a)
            })
        },
        a.fn.data = function(a, b) {
            return this.attr("data-" + a, b)
        },
        a.fn.removeData = function(a) {
            return this.removeAttr("data-" + a)
        },
        a.fn.val = function(b) {
            return "string" === a.toType(b) ? this.each(function() {
                return this.value = b
            }) : this.length > 0 ? this[0].value: null
        },
        a.fn.show = function() {
            return this.style("display", "block")
        },
        a.fn.hide = function() {
            return this.style("display", "none")
        },
        a.fn.height = function() {
            var a;
            return a = this.offset(),
            a.height
        },
        a.fn.width = function() {
            var a;
            return a = this.offset(),
            a.width
        },
        a.fn.offset = function() {
            var a;
            return a = this[0].getBoundingClientRect(),
            {
                left: a.left + window.pageXOffset,
                top: a.top + window.pageYOffset,
                width: a.width,
                height: a.height
            }
        },
        a.fn.remove = function() {
            return this.each(function() {
                return null != this.parentNode ? this.parentNode.removeChild(this) : void 0
            })
        }
    } (Quo)
}.call(this),
function() { !
    function(a) {
        var b, c, d, e, f, g, h;
        return d = null,
        b = /WebKit\/([\d.]+)/,
        c = {
            Android: /(Android)\s+([\d.]+)/,
            ipad: /(iPad).*OS\s([\d_]+)/,
            iphone: /(iPhone\sOS)\s([\d_]+)/,
            Blackberry: /(BlackBerry|BB10|Playbook).*Version\/([\d.]+)/,
            FirefoxOS: /(Mozilla).*Mobile[^\/]*\/([\d\.]*)/,
            webOS: /(webOS|hpwOS)[\s\/]([\d.]+)/
        },
        a.isMobile = function() {
            return d = d || f(),
            d.isMobile && "firefoxOS" !== d.os.name
        },
        a.environment = function() {
            return d = d || f()
        },
        a.isOnline = function() {
            return navigator.onLine
        },
        f = function() {
            var a, b;
            return b = navigator.userAgent,
            a = {},
            a.browser = e(b),
            a.os = g(b),
            a.isMobile = !!a.os,
            a.screen = h(),
            a
        },
        e = function(a) {
            var c;
            return c = a.match(b),
            c ? c[0] : a
        },
        g = function(a) {
            var b, d, e;
            b = null;
            for (d in c) if (e = a.match(c[d])) {
                b = {
                    name: "iphone" === d || "ipad" === d ? "ios": d,
                    version: e[2].replace("_", ".")
                };
				
                break
            }
            return b
        },
        h = function() {
            return {
                width: window.innerWidth,
                height: window.innerHeight
            }
        }
    } (Quo)
}.call(this),
function() { !
    function(a) {
        var b, c, d, e, f, g, h, i, j, k, l, m;
        return b = 1,
        e = {},
        d = {
            preventDefault: "isDefaultPrevented",
            stopImmediatePropagation: "isImmediatePropagationStopped",
            stopPropagation: "isPropagationStopped"
        },
        c = {
            touchstart: "mousedown",
            touchmove: "mousemove",
            touchend: "mouseup",
            touch: "click",
            doubletap: "dblclick",
            orientationchange: "resize"
        },
        f = /complete|loaded|interactive/,
        a.fn.on = function(b, c, d) {
            return "undefined" === c || "function" === a.toType(c) ? this.bind(b, c) : this.delegate(c, b, d)
        },
        a.fn.off = function(b, c, d) {
            return "undefined" === c || "function" === a.toType(c) ? this.unbind(b, c) : this.undelegate(c, b, d)
        },
        a.fn.ready = function(b) {
            return f.test(document.readyState) ? b(a) : a.fn.addEvent(document, "DOMContentLoaded",
            function() {
                return b(a)
            })
        },
        a.Event = function(a, b) {
            var c, d;
            if (c = document.createEvent("Events"), c.initEvent(a, !0, !0, null, null, null, null, null, null, null, null, null, null, null, null), b) for (d in b) c[d] = b[d];
            return c
        },
        a.fn.bind = function(a, b) {
            return this.each(function() {
                l(this, a, b)
            })
        },
        a.fn.unbind = function(a, b) {
            return this.each(function() {
                m(this, a, b)
            })
        },
        a.fn.delegate = function(b, c, d) {
            return this.each(function(e, f) {
                l(f, c, d, b,
                function(c) {
                    return function(d) {
                        var e, h;
                        return h = a(d.target).closest(b, f).get(0),
                        h ? (e = a.extend(g(d), {
                            currentTarget: h,
                            liveFired: f
                        }), c.apply(h, [e].concat([].slice.call(arguments, 1)))) : void 0
                    }
                })
            })
        },
        a.fn.undelegate = function(a, b, c) {
            return this.each(function() {
                m(this, b, c, a)
            })
        },
        a.fn.trigger = function(b, c, d) {
            return "string" === a.toType(b) && (b = a.Event(b, c)),
            null != d && (b.originalEvent = d),
            this.each(function() {
                this.dispatchEvent(b)
            })
        },
        a.fn.addEvent = function(a, b, c) {
            return a.addEventListener ? a.addEventListener(b, c, !1) : a.attachEvent ? a.attachEvent("on" + b, c) : a["on" + b] = c
        },
        a.fn.removeEvent = function(a, b, c) {
            return a.removeEventListener ? a.removeEventListener(b, c, !1) : a.detachEvent ? a.detachEvent("on" + b, c) : a["on" + b] = null
        },
        l = function(b, c, d, f, g) {
            var j, l, m, n;
            return c = i(c),
            m = k(b),
            l = e[m] || (e[m] = []),
            j = g && g(d, c),
            n = {
                event: c,
                callback: d,
                selector: f,
                proxy: h(j, d, b),
                delegate: j,
                index: l.length
            },
            l.push(n),
            a.fn.addEvent(b, n.event, n.proxy)
        },
        m = function(b, c, d, f) {
            var g;
            return c = i(c),
            g = k(b),
            j(g, c, d, f).forEach(function(c) {
                return delete e[g][c.index],
                a.fn.removeEvent(b, c.event, c.proxy)
            })
        },
        k = function(a) {
            return a._id || (a._id = b++)
        },
        i = function(b) {
            var d;
            return d = a.isMobile() ? b: c[b],
            d || b
        },
        h = function(a, b, c) {
            var d;
            return b = a || b,
            d = function(a) {
                var d;
				
				
				var ua = navigator.userAgent,
				isIOS = ua.match(/iPhone|iPad|iPod/i) ? true : false,
				isIpad = ua.match(/iPad/i) ? true : false,
				isAndroid = ua.match(/Android/i) ? true : false;
				 
				 if(isIOS) {//IOS
				 	if(isIpad){
						return d = b.apply(c, [a].concat(a.data)),
						d === !1 && a.preventDefault(),
						d
					}else{
						return d = b.apply(c, [a].concat(a.data)),
						d === !1,
						d
					}
					
				 } else if(isAndroid) {//android
					return d = b.apply(c, [a].concat(a.data)),
					d === !1 && a.preventDefault(),
					d
				 } else {//other
					return d = b.apply(c, [a].concat(a.data)),
					d === !1 && a.preventDefault(),
					d
				 }

				
                
            }
        },
        j = function(a, b, c, d) {
            return (e[a] || []).filter(function(a) {
                return ! (!a || b && a.event !== b || c && a.callback !== c || d && a.selector !== d)
            })
        },
        g = function(b) {
            var c;
            return c = a.extend({
                originalEvent: b
            },
            b),
            a.each(d,
            function(a, d) {
                return c[a] = function() {
                    return this[d] = function() {
                        return ! 0
                    },
                    b[a].apply(b, arguments)
                },
                c[d] = function() {
                    return ! 1
                }
            }),
            c
        }
    } (Quo)
}.call(this),
function() { !
    function($$) {
        var CURRENT_TOUCH, EVENT, FIRST_TOUCH, GESTURE, GESTURES, HOLD_DELAY, TAPS, TOUCH_TIMEOUT, _angle, _capturePinch, _captureRotation, _cleanGesture, _distance, _fingersPosition, _getTouches, _hold, _isSwipe, _listenTouches, _onTouchEnd, _onTouchMove, _onTouchStart, _parentIfText, _swipeDirection, _trigger;
        return TAPS = null,
        EVENT = void 0,
        GESTURE = {},
        FIRST_TOUCH = [],
        CURRENT_TOUCH = [],
        TOUCH_TIMEOUT = void 0,
        HOLD_DELAY = 650,
        GESTURES = ["touch", "tap", "singleTap", "doubleTap", "hold", "swipe", "swiping", "swipeLeft", "swipeRight", "swipeUp", "swipeDown", "rotate", "rotating", "rotateLeft", "rotateRight", "pinch", "pinching", "pinchIn", "pinchOut", "drag", "dragLeft", "dragRight", "dragUp", "dragDown"],
        GESTURES.forEach(function(a) {
            return $$.fn[a] = function(b) {
                var c;
                return c = "touch" === a ? "touchend": a,
                $$(document.body).delegate(this.selector, c, b)
            },
            this
        }),
        $$(document).ready(function() {
            return _listenTouches()
        }),
        _listenTouches = function() {
            var a;
            return a = $$(document.body),
            a.bind("touchstart", _onTouchStart),
            a.bind("touchmove", _onTouchMove),
            a.bind("touchend", _onTouchEnd),
            a.bind("touchcancel", _cleanGesture)
        },
        _onTouchStart = function(a) {
            var b, c, d, e;
            return EVENT = a,
            d = Date.now(),
            b = d - (GESTURE.last || d),
            TOUCH_TIMEOUT && clearTimeout(TOUCH_TIMEOUT),
            e = _getTouches(a),
            c = e.length,
            FIRST_TOUCH = _fingersPosition(e, c),
            GESTURE.el = $$(_parentIfText(e[0].target)),
            GESTURE.fingers = c,
            GESTURE.last = d,
            GESTURE.taps || (GESTURE.taps = 0),
            GESTURE.taps++,
            1 === c ? (c >= 1 && (GESTURE.gap = b > 0 && 250 >= b), setTimeout(_hold, HOLD_DELAY)) : 2 === c ? (GESTURE.initial_angle = parseInt(_angle(FIRST_TOUCH), 10), GESTURE.initial_distance = parseInt(_distance(FIRST_TOUCH), 10), GESTURE.angle_difference = 0, GESTURE.distance_difference = 0) : void 0
        },
        _onTouchMove = function(a) {
            var b, c, d;
            return EVENT = a,
            GESTURE.el && (d = _getTouches(a), b = d.length, b === GESTURE.fingers ? (CURRENT_TOUCH = _fingersPosition(d, b), c = _isSwipe(a), c && (GESTURE.prevSwipe = !0), (c || GESTURE.prevSwipe === !0) && _trigger("swiping"), 2 === b && (_captureRotation(), _capturePinch(), a.preventDefault())) : _cleanGesture()),
            !0
        },
        _isSwipe = function() {
            var a, b, c;
            return a = !1,
            CURRENT_TOUCH[0] && (b = Math.abs(FIRST_TOUCH[0].x - CURRENT_TOUCH[0].x) > 30, c = Math.abs(FIRST_TOUCH[0].y - CURRENT_TOUCH[0].y) > 30, a = GESTURE.el && (b || c)),
            a
        },
        _onTouchEnd = function(a) {
            var b, c, d, e, f;
            return EVENT = a,
            _trigger("touch"),
            1 === GESTURE.fingers ? 2 === GESTURE.taps && GESTURE.gap ? (_trigger("doubleTap"), _cleanGesture()) : _isSwipe() || GESTURE.prevSwipe ? (_trigger("swipe"), f = _swipeDirection(FIRST_TOUCH[0].x, CURRENT_TOUCH[0].x, FIRST_TOUCH[0].y, CURRENT_TOUCH[0].y), _trigger("swipe" + f), _cleanGesture()) : (_trigger("tap"), 1 === GESTURE.taps && (TOUCH_TIMEOUT = setTimeout(function() {
                return _trigger("singleTap"),
                _cleanGesture()
            },
            100))) : (b = !1, 0 !== GESTURE.angle_difference && (_trigger("rotate", {
                angle: GESTURE.angle_difference
            }), e = GESTURE.angle_difference > 0 ? "rotateRight": "rotateLeft", _trigger(e, {
                angle: GESTURE.angle_difference
            }), b = !0), 0 !== GESTURE.distance_difference && (_trigger("pinch", {
                angle: GESTURE.distance_difference
            }), d = GESTURE.distance_difference > 0 ? "pinchOut": "pinchIn", _trigger(d, {
                distance: GESTURE.distance_difference
            }), b = !0), !b && CURRENT_TOUCH[0] && (Math.abs(FIRST_TOUCH[0].x - CURRENT_TOUCH[0].x) > 10 || Math.abs(FIRST_TOUCH[0].y - CURRENT_TOUCH[0].y) > 10) && (_trigger("drag"), c = _swipeDirection(FIRST_TOUCH[0].x, CURRENT_TOUCH[0].x, FIRST_TOUCH[0].y, CURRENT_TOUCH[0].y), _trigger("drag" + c)), _cleanGesture()),
            EVENT = void 0
        },
        _fingersPosition = function(a, b) {
            var c, d;
            for (d = [], c = 0, a = a[0].targetTouches ? a[0].targetTouches: a; b > c;) d.push({
                x: a[c].pageX,
                y: a[c].pageY
            }),
            c++;
            return d
        },
        _captureRotation = function() {
            var angle, diff, i, symbol;
            if (angle = parseInt(_angle(CURRENT_TOUCH), 10), diff = parseInt(GESTURE.initial_angle - angle, 10), Math.abs(diff) > 20 || 0 !== GESTURE.angle_difference) {
                for (i = 0, symbol = GESTURE.angle_difference < 0 ? "-": "+"; Math.abs(diff - GESTURE.angle_difference) > 90 && i++<10;) eval("diff " + symbol + "= 180;");
                return GESTURE.angle_difference = parseInt(diff, 10),
                _trigger("rotating", {
                    angle: GESTURE.angle_difference
                })
            }
        },
        _capturePinch = function() {
            var a, b;
            return b = parseInt(_distance(CURRENT_TOUCH), 10),
            a = GESTURE.initial_distance - b,
            Math.abs(a) > 10 ? (GESTURE.distance_difference = a, _trigger("pinching", {
                distance: a
            })) : void 0
        },
        _trigger = function(a, b) {
            return GESTURE.el ? (b = b || {},
            CURRENT_TOUCH[0] && (b.iniTouch = GESTURE.fingers > 1 ? FIRST_TOUCH: FIRST_TOUCH[0], b.currentTouch = GESTURE.fingers > 1 ? CURRENT_TOUCH: CURRENT_TOUCH[0]), GESTURE.el.trigger(a, b, EVENT)) : void 0
        },
        _cleanGesture = function() {
            return FIRST_TOUCH = [],
            CURRENT_TOUCH = [],
            GESTURE = {},
            clearTimeout(TOUCH_TIMEOUT)
        },
        _angle = function(a) {
            var b, c, d;
            return b = a[0],
            c = a[1],
            d = Math.atan( - 1 * (c.y - b.y) / (c.x - b.x)) * (180 / Math.PI),
            0 > d ? d + 180 : d
        },
        _distance = function(a) {
            var b, c;
            return b = a[0],
            c = a[1],
            -1 * Math.sqrt((c.x - b.x) * (c.x - b.x) + (c.y - b.y) * (c.y - b.y))
        },
        _getTouches = function(a) {
            return $$.isMobile() ? a.touches: [a]
        },
        _parentIfText = function(a) {
            return "tagName" in a ? a: a.parentNode
        },
        _swipeDirection = function(a, b, c, d) {
            var e, f;
            return e = Math.abs(a - b),
            f = Math.abs(c - d),
            e >= f ? a - b > 0 ? "Left": "Right": c - d > 0 ? "Up": "Down"
        },
        _hold = function() {
            return GESTURE.last && Date.now() - GESTURE.last >= HOLD_DELAY ? (_trigger("hold"), GESTURE.taps = 0) : void 0
        }
    } (Quo)
}.call(this),
function() { !
    function(a) {
        return a.fn.text = function(b) {
            return b || "number" === a.toType(b) ? this.each(function() {
                return this.textContent = b
            }) : this[0].textContent
        },
        a.fn.html = function(b) {
            var c;
            return c = a.toType(b),
            b || "number" === c || "string" === c ? this.each(function() {
                var a, d, e, f;
                if ("string" === c || "number" === c) return this.innerHTML = b;
                if (this.innerHTML = null, "array" === c) {
                    for (f = [], d = 0, e = b.length; e > d; d++) a = b[d],
                    f.push(this.appendChild(a));
                    return f
                }
                return this.appendChild(b)
            }) : this[0].innerHTML
        },
        a.fn.append = function(b) {
            var c;
            return c = a.toType(b),
            this.each(function() {
                var a = this;
                return "string" === c ? this.insertAdjacentHTML("beforeend", b) : "array" === c ? b.each(function(b, c) {
                    return a.appendChild(c)
                }) : this.appendChild(b)
            })
        },
        a.fn.prepend = function(b) {
            var c;
            return c = a.toType(b),
            this.each(function() {
                var a = this;
                return "string" === c ? this.insertAdjacentHTML("afterbegin", b) : "array" === c ? b.each(function(b, c) {
                    return a.insertBefore(c, a.firstChild)
                }) : this.insertBefore(b, this.firstChild)
            })
        },
        a.fn.replaceWith = function(b) {
            var c;
            return c = a.toType(b),
            this.each(function() {
                var a = this;
                return this.parentNode ? "string" === c ? this.insertAdjacentHTML("beforeBegin", b) : "array" === c ? b.each(function(b, c) {
                    return a.parentNode.insertBefore(c, a)
                }) : this.parentNode.insertBefore(b, this) : void 0
            }),
            this.remove()
        },
        a.fn.empty = function() {
            return this.each(function() {
                return this.innerHTML = null
            })
        }
    } (Quo)
}.call(this),
function() { !
    function(a) {
        var b, c, d, e, f, g;
        return d = "parentNode",
        b = /^\.([\w-]+)$/,
        c = /^#[\w\d-]+$/,
        e = /^[\w-]+$/,
        a.query = function(a, d) {
            var f;
            return d = d.trim(),
            b.test(d) ? f = a.getElementsByClassName(d.replace(".", "")) : e.test(d) ? f = a.getElementsByTagName(d) : c.test(d) && a === document ? (f = a.getElementById(d.replace("#", "")), f || (f = [])) : f = a.querySelectorAll(d),
            f.nodeType ? [f] : Array.prototype.slice.call(f)
        },
        a.fn.find = function(b) {
            var c;
            return c = 1 === this.length ? Quo.query(this[0], b) : this.map(function() {
                return Quo.query(this, b)
            }),
            a(c)
        },
        a.fn.parent = function(a) {
            var b;
            return b = a ? g(this) : this.instance(d),
            f(b, a)
        },
        a.fn.siblings = function(a) {
            var b;
            return b = this.map(function(a, b) {
                return Array.prototype.slice.call(b.parentNode.children).filter(function(a) {
                    return a !== b
                })
            }),
            f(b, a)
        },
        a.fn.children = function(a) {
            var b;
            return b = this.map(function() {
                return Array.prototype.slice.call(this.children)
            }),
            f(b, a)
        },
        a.fn.get = function(a) {
            return void 0 === a ? this: this[a]
        },
        a.fn.first = function() {
            return a(this[0])
        },
        a.fn.last = function() {
            return a(this[this.length - 1])
        },
        a.fn.closest = function(b, c) {
            var d, e;
            for (e = this[0], d = a(b), d.length || (e = null); e && d.indexOf(e) < 0;) e = e !== c && e !== document && e.parentNode;
            return a(e)
        },
        a.fn.each = function(a) {
            return this.forEach(function(b, c) {
                return a.call(b, c, b)
            }),
            this
        },
        g = function(b) {
            var c;
            for (c = []; b.length > 0;) b = a.map(b,
            function(a) {
                return (a = a.parentNode) && a !== document && c.indexOf(a) < 0 ? (c.push(a), a) : void 0
            });
            return c
        },
        f = function(b, c) {
            return void 0 === c ? a(b) : a(b).filter(c)
        }
    } (Quo)
}.call(this),
function() { !
    function(a) {
        var b, c, d;
        return b = ["-webkit-", "-moz-", "-ms-", "-o-", ""],
        a.fn.addClass = function(a) {
            return this.each(function() {
                return d(a, this.className) ? void 0 : (this.className += " " + a, this.className = this.className.trim())
            })
        },
        a.fn.removeClass = function(a) {
            return this.each(function() {
                return a ? d(a, this.className) ? this.className = this.className.replace(a, " ").replace(/\s+/g, " ").trim() : void 0 : this.className = ""
            })
        },
        a.fn.toggleClass = function(a) {
            return this.each(function() {
                return d(a, this.className) ? this.className = this.className.replace(a, " ") : (this.className += " " + a, this.className = this.className.trim())
            })
        },
        a.fn.hasClass = function(a) {
            return d(a, this[0].className)
        },
        a.fn.style = function(a, b) {
            return b ? this.each(function() {
                return this.style[a] = b
            }) : this[0].style[a] || c(this[0], a)
        },
        a.fn.css = function(a, b) {
            return this.style(a, b)
        },
        a.fn.vendor = function(a, c) {
            var d, e, f, g;
            for (g = [], e = 0, f = b.length; f > e; e++) d = b[e],
            g.push(this.style("" + d + a, c));
            return g
        },
        d = function(a, b) {
            var c;
            return c = b.split(/\s+/g),
            c.indexOf(a) >= 0
        },
        c = function(a, b) {
            return document.defaultView.getComputedStyle(a, "")[b]
        }
    } (Quo)
}.call(this),
!
function() {
    function a() {
        this._events = {},
        this._conf && b.call(this, this._conf)
    }
    function b(a) {
        a && (this._conf = a, a.delimiter && (this.delimiter = a.delimiter), a.maxListeners && (this._events.maxListeners = a.maxListeners), a.wildcard && (this.wildcard = a.wildcard), a.newListener && (this.newListener = a.newListener), this.wildcard && (this.listenerTree = {}))
    }
    function c(a) {
        this._events = {},
        this.newListener = !1,
        b.call(this, a)
    }
    function d(a, b, c, e) {
        if (!c) return [];
        var f, g, h, i, j, k, l, m = [],
        n = b.length,
        o = b[e],
        p = b[e + 1];
        if (e === n && c._listeners) {
            if ("function" == typeof c._listeners) return a && a.push(c._listeners),
            [c];
            for (f = 0, g = c._listeners.length; g > f; f++) a && a.push(c._listeners[f]);
            return [c]
        }
        if ("*" === o || "**" === o || c[o]) {
            if ("*" === o) {
                for (h in c)"_listeners" !== h && c.hasOwnProperty(h) && (m = m.concat(d(a, b, c[h], e + 1)));
                return m
            }
            if ("**" === o) {
                l = e + 1 === n || e + 2 === n && "*" === p,
                l && c._listeners && (m = m.concat(d(a, b, c, n)));
                for (h in c)"_listeners" !== h && c.hasOwnProperty(h) && ("*" === h || "**" === h ? (c[h]._listeners && !l && (m = m.concat(d(a, b, c[h], n))), m = m.concat(d(a, b, c[h], e))) : m = m.concat(h === p ? d(a, b, c[h], e + 2) : d(a, b, c[h], e)));
                return m
            }
            m = m.concat(d(a, b, c[o], e + 1))
        }
        if (i = c["*"], i && d(a, b, i, e + 1), j = c["**"], j) if (n > e) {
            j._listeners && d(a, b, j, n);
            for (h in j)"_listeners" !== h && j.hasOwnProperty(h) && (h === p ? d(a, b, j[h], e + 2) : h === o ? d(a, b, j[h], e + 1) : (k = {},
            k[h] = j[h], d(a, b, {
                "**": k
            },
            e + 1)))
        } else j._listeners ? d(a, b, j, n) : j["*"] && j["*"]._listeners && d(a, b, j["*"], n);
        return m
    }
    function e(a, b) {
        a = "string" == typeof a ? a.split(this.delimiter) : a.slice();
        for (var c = 0,
        d = a.length; d > c + 1; c++) if ("**" === a[c] && "**" === a[c + 1]) return;
        for (var e = this.listenerTree,
        h = a.shift(); h;) {
            if (e[h] || (e[h] = {}), e = e[h], 0 === a.length) {
                if (e._listeners) {
                    if ("function" == typeof e._listeners) e._listeners = [e._listeners, b];
                    else if (f(e._listeners) && (e._listeners.push(b), !e._listeners.warned)) {
                        var i = g;
                        "undefined" != typeof this._events.maxListeners && (i = this._events.maxListeners),
                        i > 0 && e._listeners.length > i && (e._listeners.warned = !0, console.error("(node) warning: possible EventEmitter memory leak detected. %d listeners added. Use emitter.setMaxListeners() to increase limit.", e._listeners.length), console.trace())
                    }
                } else e._listeners = b;
                return ! 0
            }
            h = a.shift()
        }
        return ! 0
    }
    var f = Array.isArray ? Array.isArray: function(a) {
        return "[object Array]" === Object.prototype.toString.call(a)
    },
    g = 10;
    c.prototype.delimiter = ".",
    c.prototype.setMaxListeners = function(b) {
        this._events || a.call(this),
        this._events.maxListeners = b,
        this._conf || (this._conf = {}),
        this._conf.maxListeners = b
    },
    c.prototype.event = "",
    c.prototype.once = function(a, b) {
        return this.many(a, 1, b),
        this
    },
    c.prototype.many = function(a, b, c) {
        function d() {
            0 === --b && e.off(a, d),
            c.apply(this, arguments)
        }
        var e = this;
        if ("function" != typeof c) throw new Error("many only accepts instances of Function");
        return d._origin = c,
        this.on(a, d),
        e
    },
    c.prototype.emit = function() {
        this._events || a.call(this);
        var b = arguments[0];
        if ("newListener" === b && !this.newListener && !this._events.newListener) return ! 1;
        if (this._all) {
            for (var c = arguments.length,
            e = new Array(c - 1), f = 1; c > f; f++) e[f - 1] = arguments[f];
            for (f = 0, c = this._all.length; c > f; f++) this.event = b,
            this._all[f].apply(this, e)
        }
        if (! ("error" !== b || this._all || this._events.error || this.wildcard && this.listenerTree.error)) throw arguments[1] instanceof Error ? arguments[1] : new Error("Uncaught, unspecified 'error' event.");
        var g;
        if (this.wildcard) {
            g = [];
            var h = "string" == typeof b ? b.split(this.delimiter) : b.slice();
            d.call(this, g, h, this.listenerTree, 0)
        } else g = this._events[b];
        if ("function" == typeof g) {
            if (this.event = b, 1 === arguments.length) g.call(this);
            else if (arguments.length > 1) switch (arguments.length) {
            case 2:
                g.call(this, arguments[1]);
                break;
            case 3:
                g.call(this, arguments[1], arguments[2]);
                break;
            default:
                for (var c = arguments.length,
                e = new Array(c - 1), f = 1; c > f; f++) e[f - 1] = arguments[f];
                g.apply(this, e)
            }
            return ! 0
        }
        if (g) {
            for (var c = arguments.length,
            e = new Array(c - 1), f = 1; c > f; f++) e[f - 1] = arguments[f];
            for (var i = g.slice(), f = 0, c = i.length; c > f; f++) this.event = b,
            i[f].apply(this, e);
            return i.length > 0 || !!this._all
        }
        return !! this._all
    },
    c.prototype.on = function(b, c) {
        if ("function" == typeof b) return this.onAny(b),
        this;
        if ("function" != typeof c) throw new Error("on only accepts instances of Function");
        if (this._events || a.call(this), this.emit("newListener", b, c), this.wildcard) return e.call(this, b, c),
        this;
        if (this._events[b]) {
            if ("function" == typeof this._events[b]) this._events[b] = [this._events[b], c];
            else if (f(this._events[b]) && (this._events[b].push(c), !this._events[b].warned)) {
                var d = g;
                "undefined" != typeof this._events.maxListeners && (d = this._events.maxListeners),
                d > 0 && this._events[b].length > d && (this._events[b].warned = !0, console.error("(node) warning: possible EventEmitter memory leak detected. %d listeners added. Use emitter.setMaxListeners() to increase limit.", this._events[b].length), console.trace())
            }
        } else this._events[b] = c;
        return this
    },
    c.prototype.onAny = function(a) {
        if ("function" != typeof a) throw new Error("onAny only accepts instances of Function");
        return this._all || (this._all = []),
        this._all.push(a),
        this
    },
    c.prototype.addListener = c.prototype.on,
    c.prototype.off = function(a, b) {
        if ("function" != typeof b) throw new Error("removeListener only takes instances of Function");
        var c, e = [];
        if (this.wildcard) {
            var g = "string" == typeof a ? a.split(this.delimiter) : a.slice();
            e = d.call(this, null, g, this.listenerTree, 0)
        } else {
            if (!this._events[a]) return this;
            c = this._events[a],
            e.push({
                _listeners: c
            })
        }
        for (var h = 0; h < e.length; h++) {
            var i = e[h];
            if (c = i._listeners, f(c)) {
                for (var j = -1,
                k = 0,
                l = c.length; l > k; k++) if (c[k] === b || c[k].listener && c[k].listener === b || c[k]._origin && c[k]._origin === b) {
                    j = k;
                    break
                }
                if (0 > j) continue;
                return this.wildcard ? i._listeners.splice(j, 1) : this._events[a].splice(j, 1),
                0 === c.length && (this.wildcard ? delete i._listeners: delete this._events[a]),
                this
            } (c === b || c.listener && c.listener === b || c._origin && c._origin === b) && (this.wildcard ? delete i._listeners: delete this._events[a])
        }
        return this
    },
    c.prototype.offAny = function(a) {
        var b, c = 0,
        d = 0;
        if (a && this._all && this._all.length > 0) {
            for (b = this._all, c = 0, d = b.length; d > c; c++) if (a === b[c]) return b.splice(c, 1),
            this
        } else this._all = [];
        return this
    },
    c.prototype.removeListener = c.prototype.off,
    c.prototype.removeAllListeners = function(b) {
        if (0 === arguments.length) return ! this._events || a.call(this),
        this;
        if (this.wildcard) for (var c = "string" == typeof b ? b.split(this.delimiter) : b.slice(), e = d.call(this, null, c, this.listenerTree, 0), f = 0; f < e.length; f++) {
            var g = e[f];
            g._listeners = null
        } else {
            if (!this._events[b]) return this;
            this._events[b] = null
        }
        return this
    },
    c.prototype.listeners = function(b) {
        if (this.wildcard) {
            var c = [],
            e = "string" == typeof b ? b.split(this.delimiter) : b.slice();
            return d.call(this, c, e, this.listenerTree, 0),
            c
        }
        return this._events || a.call(this),
        this._events[b] || (this._events[b] = []),
        f(this._events[b]) || (this._events[b] = [this._events[b]]),
        this._events[b]
    },
    c.prototype.listenersAny = function() {
        return this._all ? this._all: []
    },
    "function" == typeof define && define.amd ? define(function() {
        return c
    }) : "object" == typeof exports ? exports.EventEmitter2 = c: window.EventEmitter2 = c
} (),
!
function() {
    function a(a) {
        return a.replace(t, "").replace(u, ",").replace(v, "").replace(w, "").replace(x, "").split(y)
    }
    function b(a) {
        return "'" + a.replace(/('|\\)/g, "\\$1").replace(/\r/g, "\\r").replace(/\n/g, "\\n") + "'"
    }
    function c(c, d) {
        function e(a) {
            return m += a.split(/\n/).length - 1,
            k && (a = a.replace(/\s+/g, " ").replace(/<!--[\w\W]*?-->/g, "")),
            a && (a = s[1] + b(a) + s[2] + "\n"),
            a
        }
        function f(b) {
            var c = m;
            if (j ? b = j(b, d) : g && (b = b.replace(/\n/g,
            function() {
                return m++,
                "$line=" + m + ";"
            })), 0 === b.indexOf("=")) {
                var e = l && !/^=[=#]/.test(b);
                if (b = b.replace(/^=[=#]?|[\s;]*$/g, ""), e) {
                    var f = b.replace(/\s*\([^\)]+\)/, "");
                    n[f] || /^(include|print)$/.test(f) || (b = "$escape(" + b + ")")
                } else b = "$string(" + b + ")";
                b = s[1] + b + s[2]
            }
            return g && (b = "$line=" + c + ";" + b),
            r(a(b),
            function(a) {
                if (a && !p[a]) {
                    var b;
                    b = "print" === a ? u: "include" === a ? v: n[a] ? "$utils." + a: o[a] ? "$helpers." + a: "$data." + a,
                    w += a + "=" + b + ",",
                    p[a] = !0
                }
            }),
            b + "\n"
        }
        var g = d.debug,
        h = d.openTag,
        i = d.closeTag,
        j = d.parser,
        k = d.compress,
        l = d.escape,
        m = 1,
        p = {
            $data: 1,
            $filename: 1,
            $utils: 1,
            $helpers: 1,
            $out: 1,
            $line: 1
        },
        q = "".trim,
        s = q ? ["$out='';", "$out+=", ";", "$out"] : ["$out=[];", "$out.push(", ");", "$out.join('')"],
        t = q ? "$out+=text;return $out;": "$out.push(text);",
        u = "function(){var text=''.concat.apply('',arguments);" + t + "}",
        v = "function(filename,data){data=data||$data;var text=$utils.$include(filename,data,$filename);" + t + "}",
        w = "'use strict';var $utils=this,$helpers=$utils.$helpers," + (g ? "$line=0,": ""),
        x = s[0],
        y = "return new String(" + s[3] + ");";
        r(c.split(h),
        function(a) {
            a = a.split(i);
            var b = a[0],
            c = a[1];
            1 === a.length ? x += e(b) : (x += f(b), c && (x += e(c)))
        });
        var z = w + x + y;
        g && (z = "try{" + z + "}catch(e){throw {filename:$filename,name:'Render Error',message:e.message,line:$line,source:" + b(c) + ".split(/\\n/)[$line-1].replace(/^\\s+/,'')};}");
        try {
            var A = new Function("$data", "$filename", z);
            return A.prototype = n,
            A
        } catch(B) {
            throw B.temp = "function anonymous($data,$filename) {" + z + "}",
            B
        }
    }
    var d = function(a, b) {
        return "string" == typeof b ? q(b, {
            filename: a
        }) : g(a, b)
    };
    d.version = "3.0.0",
    d.config = function(a, b) {
        e[a] = b
    };
    var e = d.defaults = {
        openTag: "<%",
        closeTag: "%>",
        escape: !0,
        cache: !0,
        compress: !1,
        parser: null
    },
    f = d.cache = {};
    d.render = function(a, b) {
        return q(a, b)
    };
    var g = d.renderFile = function(a, b) {
        var c = d.get(a) || p({
            filename: a,
            name: "Render Error",
            message: "Template not found"
        });
        return b ? c(b) : c
    };
    d.get = function(a) {
        var b;
        if (f[a]) b = f[a];
        else if ("object" == typeof document) {
            var c = document.getElementById(a);
            if (c) {
                var d = (c.value || c.innerHTML).replace(/^\s*|\s*$/g, "");
                b = q(d, {
                    filename: a
                })
            }
        }
        return b
    };
    var h = function(a, b) {
        return "string" != typeof a && (b = typeof a, "number" === b ? a += "": a = "function" === b ? h(a.call(a)) : ""),
        a
    },
    i = {
        "<": "&#60;",
        ">": "&#62;",
        '"': "&#34;",
        "'": "&#39;",
        "&": "&#38;"
    },
    j = function(a) {
        return i[a]
    },
    k = function(a) {
        return h(a).replace(/&(?![\w#]+;)|[<>"']/g, j)
    },
    l = Array.isArray ||
    function(a) {
        return "[object Array]" === {}.toString.call(a)
    },
    m = function(a, b) {
        var c, d;
        if (l(a)) for (c = 0, d = a.length; d > c; c++) b.call(a, a[c], c, a);
        else for (c in a) b.call(a, a[c], c)
    },
    n = d.utils = {
        $helpers: {},
        $include: g,
        $string: h,
        $escape: k,
        $each: m
    };
    d.helper = function(a, b) {
        o[a] = b
    };
    var o = d.helpers = n.$helpers;
    d.onerror = function(a) {
        var b = "Template Error\n\n";
        for (var c in a) b += "<" + c + ">\n" + a[c] + "\n\n";
        "object" == typeof console && console.error(b)
    };
    var p = function(a) {
        return d.onerror(a),
        function() {
            return "{Template Error}"
        }
    },
    q = d.compile = function(a, b) {
        function d(c) {
            try {
                return new i(c, h) + ""
            } catch(d) {
                return b.debug ? p(d)() : (b.debug = !0, q(a, b)(c))
            }
        }
        b = b || {};
        for (var g in e) void 0 === b[g] && (b[g] = e[g]);
        var h = b.filename;
        try {
            var i = c(a, b)
        } catch(j) {
            return j.filename = h || "anonymous",
            j.name = "Syntax Error",
            p(j)
        }
        return d.prototype = i.prototype,
        d.toString = function() {
            return i.toString()
        },
        h && b.cache && (f[h] = d),
        d
    },
    r = n.$each,
    s = "break,case,catch,continue,debugger,default,delete,do,else,false,finally,for,function,if,in,instanceof,new,null,return,switch,this,throw,true,try,typeof,var,void,while,with,abstract,boolean,byte,char,class,const,double,enum,export,extends,final,float,goto,implements,import,int,interface,long,native,package,private,protected,public,short,static,super,synchronized,throws,transient,volatile,arguments,let,yield,undefined",
    t = /\/\*[\w\W]*?\*\/|\/\/[^\n]*\n|\/\/[^\n]*$|"(?:[^"\\]|\\[\w\W])*"|'(?:[^'\\]|\\[\w\W])*'|\s*\.\s*[$\w\.]+/g,
    u = /[^\w$]+/g,
    v = new RegExp(["\\b" + s.replace(/,/g, "\\b|\\b") + "\\b"].join("|"), "g"),
    w = /^\d[^,]*|,\d[^,]*/g,
    x = /^,+|,+$/g,
    y = /^$|,+/;
    e.openTag = "{{",
    e.closeTag = "}}";
    var z = function(a, b) {
        var c = b.split(":"),
        d = c.shift(),
        e = c.join(":") || "";
        return e && (e = ", " + e),
        "$helpers." + d + "(" + a + e + ")"
    };
    e.parser = function(a) {
        a = a.replace(/^\s/, "");
        var b = a.split(" "),
        c = b.shift(),
        e = b.join(" ");
        switch (c) {
        case "if":
            a = "if(" + e + "){";
            break;
        case "else":
            b = "if" === b.shift() ? " if(" + b.join(" ") + ")": "",
            a = "}else" + b + "{";
            break;
        case "/if":
            a = "}";
            break;
        case "each":
            var f = b[0] || "$data",
            g = b[1] || "as",
            h = b[2] || "$value",
            i = b[3] || "$index",
            j = h + "," + i;
            "as" !== g && (f = "[]"),
            a = "$each(" + f + ",function(" + j + "){";
            break;
        case "/each":
            a = "});";
            break;
        case "echo":
            a = "print(" + e + ");";
            break;
        case "print":
        case "include":
            a = c + "(" + b.join(",") + ");";
            break;
        default:
            if (/^\s*\|\s*[\w\$]/.test(e)) {
                var k = !0;
                0 === a.indexOf("#") && (a = a.substr(1), k = !1);
                for (var l = 0,
                m = a.split("|"), n = m.length, o = m[l++]; n > l; l++) o = z(o, m[l]);
                a = (k ? "=": "=#") + o
            } else a = d.helpers[c] ? "=#" + c + "(" + b.join(",") + ");": "=" + a
        }
        return a
    },
    "function" == typeof define ? define(function() {
        return d
    }) : "undefined" != typeof exports ? module.exports = d: this.template = d
} (),
!
function a(b, c, d) {
    function e(g, h) {
        if (!c[g]) {
            if (!b[g]) {
                var i = "function" == typeof require && require;
                if (!h && i) return i(g, !0);
                if (f) return f(g, !0);
                var j = new Error("Cannot find module '" + g + "'");
                throw j.code = "MODULE_NOT_FOUND",
                j
            }
            var k = c[g] = {
                exports: {}
            };
            b[g][0].call(k.exports,
            function(a) {
                var c = b[g][1][a];
                return e(c ? c: a)
            },
            k, k.exports, a, b, c, d)
        }
        return c[g].exports
    }
    for (var f = "function" == typeof require && require,
    g = 0; g < d.length; g++) e(d[g]);
    return e
} ({
    1 : [function(a, b, c) { !
        function() {
            function a() {
                this._events = {},
                this._conf && b.call(this, this._conf)
            }
            function b(a) {
                a && (this._conf = a, a.delimiter && (this.delimiter = a.delimiter), a.maxListeners && (this._events.maxListeners = a.maxListeners), a.wildcard && (this.wildcard = a.wildcard), a.newListener && (this.newListener = a.newListener), this.wildcard && (this.listenerTree = {}))
            }
            function d(a) {
                this._events = {},
                this.newListener = !1,
                b.call(this, a)
            }
            function e(a, b, c, d) {
                if (!c) return [];
                var f, g, h, i, j, k, l, m = [],
                n = b.length,
                o = b[d],
                p = b[d + 1];
                if (d === n && c._listeners) {
                    if ("function" == typeof c._listeners) return a && a.push(c._listeners),
                    [c];
                    for (f = 0, g = c._listeners.length; g > f; f++) a && a.push(c._listeners[f]);
                    return [c]
                }
                if ("*" === o || "**" === o || c[o]) {
                    if ("*" === o) {
                        for (h in c)"_listeners" !== h && c.hasOwnProperty(h) && (m = m.concat(e(a, b, c[h], d + 1)));
                        return m
                    }
                    if ("**" === o) {
                        l = d + 1 === n || d + 2 === n && "*" === p,
                        l && c._listeners && (m = m.concat(e(a, b, c, n)));
                        for (h in c)"_listeners" !== h && c.hasOwnProperty(h) && ("*" === h || "**" === h ? (c[h]._listeners && !l && (m = m.concat(e(a, b, c[h], n))), m = m.concat(e(a, b, c[h], d))) : m = m.concat(h === p ? e(a, b, c[h], d + 2) : e(a, b, c[h], d)));
                        return m
                    }
                    m = m.concat(e(a, b, c[o], d + 1))
                }
                if (i = c["*"], i && e(a, b, i, d + 1), j = c["**"]) if (n > d) {
                    j._listeners && e(a, b, j, n);
                    for (h in j)"_listeners" !== h && j.hasOwnProperty(h) && (h === p ? e(a, b, j[h], d + 2) : h === o ? e(a, b, j[h], d + 1) : (k = {},
                    k[h] = j[h], e(a, b, {
                        "**": k
                    },
                    d + 1)))
                } else j._listeners ? e(a, b, j, n) : j["*"] && j["*"]._listeners && e(a, b, j["*"], n);
                return m
            }
            function f(a, b) {
                a = "string" == typeof a ? a.split(this.delimiter) : a.slice();
                for (var c = 0,
                d = a.length; d > c + 1; c++) if ("**" === a[c] && "**" === a[c + 1]) return;
                for (var e = this.listenerTree,
                f = a.shift(); f;) {
                    if (e[f] || (e[f] = {}), e = e[f], 0 === a.length) {
                        if (e._listeners) {
                            if ("function" == typeof e._listeners) e._listeners = [e._listeners, b];
                            else if (g(e._listeners) && (e._listeners.push(b), !e._listeners.warned)) {
                                var i = h;
                                "undefined" != typeof this._events.maxListeners && (i = this._events.maxListeners),
                                i > 0 && e._listeners.length > i && (e._listeners.warned = !0, console.error("(node) warning: possible EventEmitter memory leak detected. %d listeners added. Use emitter.setMaxListeners() to increase limit.", e._listeners.length), console.trace())
                            }
                        } else e._listeners = b;
                        return ! 0
                    }
                    f = a.shift()
                }
                return ! 0
            }
            var g = Array.isArray ? Array.isArray: function(a) {
                return "[object Array]" === Object.prototype.toString.call(a)
            },
            h = 10;
            d.prototype.delimiter = ".",
            d.prototype.setMaxListeners = function(b) {
                this._events || a.call(this),
                this._events.maxListeners = b,
                this._conf || (this._conf = {}),
                this._conf.maxListeners = b
            },
            d.prototype.event = "",
            d.prototype.once = function(a, b) {
                return this.many(a, 1, b),
                this
            },
            d.prototype.many = function(a, b, c) {
                function d() {
                    0 === --b && e.off(a, d),
                    c.apply(this, arguments)
                }
                var e = this;
                if ("function" != typeof c) throw new Error("many only accepts instances of Function");
                return d._origin = c,
                this.on(a, d),
                e
            },
            d.prototype.emit = function() {
                this._events || a.call(this);
                var b = arguments[0];
                if ("newListener" === b && !this.newListener && !this._events.newListener) return ! 1;
                if (this._all) {
                    for (var c = arguments.length,
                    d = new Array(c - 1), f = 1; c > f; f++) d[f - 1] = arguments[f];
                    for (f = 0, c = this._all.length; c > f; f++) this.event = b,
                    this._all[f].apply(this, d)
                }
                if ("error" === b && !(this._all || this._events.error || this.wildcard && this.listenerTree.error)) throw arguments[1] instanceof Error ? arguments[1] : new Error("Uncaught, unspecified 'error' event.");
                var g;
                if (this.wildcard) {
                    g = [];
                    var h = "string" == typeof b ? b.split(this.delimiter) : b.slice();
                    e.call(this, g, h, this.listenerTree, 0)
                } else g = this._events[b];
                if ("function" == typeof g) {
                    if (this.event = b, 1 === arguments.length) g.call(this);
                    else if (arguments.length > 1) switch (arguments.length) {
                    case 2:
                        g.call(this, arguments[1]);
                        break;
                    case 3:
                        g.call(this, arguments[1], arguments[2]);
                        break;
                    default:
                        for (var c = arguments.length,
                        d = new Array(c - 1), f = 1; c > f; f++) d[f - 1] = arguments[f];
                        g.apply(this, d)
                    }
                    return ! 0
                }
                if (g) {
                    for (var c = arguments.length,
                    d = new Array(c - 1), f = 1; c > f; f++) d[f - 1] = arguments[f];
                    for (var i = g.slice(), f = 0, c = i.length; c > f; f++) this.event = b,
                    i[f].apply(this, d);
                    return i.length > 0 || !!this._all
                }
                return !! this._all
            },
            d.prototype.on = function(b, c) {
                if ("function" == typeof b) return this.onAny(b),
                this;
                if ("function" != typeof c) throw new Error("on only accepts instances of Function");
                if (this._events || a.call(this), this.emit("newListener", b, c), this.wildcard) return f.call(this, b, c),
                this;
                if (this._events[b]) {
                    if ("function" == typeof this._events[b]) this._events[b] = [this._events[b], c];
                    else if (g(this._events[b]) && (this._events[b].push(c), !this._events[b].warned)) {
                        var d = h;
                        "undefined" != typeof this._events.maxListeners && (d = this._events.maxListeners),
                        d > 0 && this._events[b].length > d && (this._events[b].warned = !0, console.error("(node) warning: possible EventEmitter memory leak detected. %d listeners added. Use emitter.setMaxListeners() to increase limit.", this._events[b].length), console.trace())
                    }
                } else this._events[b] = c;
                return this
            },
            d.prototype.onAny = function(a) {
                if ("function" != typeof a) throw new Error("onAny only accepts instances of Function");
                return this._all || (this._all = []),
                this._all.push(a),
                this
            },
            d.prototype.addListener = d.prototype.on,
            d.prototype.off = function(a, b) {
                if ("function" != typeof b) throw new Error("removeListener only takes instances of Function");
                var c, d = [];
                if (this.wildcard) {
                    var f = "string" == typeof a ? a.split(this.delimiter) : a.slice();
                    d = e.call(this, null, f, this.listenerTree, 0)
                } else {
                    if (!this._events[a]) return this;
                    c = this._events[a],
                    d.push({
                        _listeners: c
                    })
                }
                for (var h = 0; h < d.length; h++) {
                    var i = d[h];
                    if (c = i._listeners, g(c)) {
                        for (var j = -1,
                        k = 0,
                        l = c.length; l > k; k++) if (c[k] === b || c[k].listener && c[k].listener === b || c[k]._origin && c[k]._origin === b) {
                            j = k;
                            break
                        }
                        if (0 > j) continue;
                        return this.wildcard ? i._listeners.splice(j, 1) : this._events[a].splice(j, 1),
                        0 === c.length && (this.wildcard ? delete i._listeners: delete this._events[a]),
                        this
                    } (c === b || c.listener && c.listener === b || c._origin && c._origin === b) && (this.wildcard ? delete i._listeners: delete this._events[a])
                }
                return this
            },
            d.prototype.offAny = function(a) {
                var b, c = 0,
                d = 0;
                if (a && this._all && this._all.length > 0) {
                    for (b = this._all, c = 0, d = b.length; d > c; c++) if (a === b[c]) return b.splice(c, 1),
                    this
                } else this._all = [];
                return this
            },
            d.prototype.removeListener = d.prototype.off,
            d.prototype.removeAllListeners = function(b) {
                if (0 === arguments.length) return ! this._events || a.call(this),
                this;
                if (this.wildcard) for (var c = "string" == typeof b ? b.split(this.delimiter) : b.slice(), d = e.call(this, null, c, this.listenerTree, 0), f = 0; f < d.length; f++) {
                    var g = d[f];
                    g._listeners = null
                } else {
                    if (!this._events[b]) return this;
                    this._events[b] = null
                }
                return this
            },
            d.prototype.listeners = function(b) {
                if (this.wildcard) {
                    var c = [],
                    d = "string" == typeof b ? b.split(this.delimiter) : b.slice();
                    return e.call(this, c, d, this.listenerTree, 0),
                    c
                }
                return this._events || a.call(this),
                this._events[b] || (this._events[b] = []),
                g(this._events[b]) || (this._events[b] = [this._events[b]]),
                this._events[b]
            },
            d.prototype.listenersAny = function() {
                return this._all ? this._all: []
            },
            "function" == typeof define && define.amd ? define(function() {
                return d
            }) : "object" == typeof c ? c.EventEmitter2 = d: window.EventEmitter2 = d
        } ()
    },
    {}],
    2 : [function(a, b) {
        var c, d;
        c = [],
        d = function() {
            var a, b;
            return b = navigator.userAgent.toLowerCase(),
            (a = b.match(/rv:([\d.]+)\) like gecko/)) && (c = ["ie", a[1]]),
            (a = b.match(/msie ([\d.]+)/)) && (c = ["ie", a[1]]),
            (a = b.match(/chrome\/([\d.]+)/)) && (c = ["chrome", a[1]]),
            (a = b.match(/firefox\/([\d.]+)/)) && (c = ["firefox", a[1]]),
            (a = b.match(/opera.([\d.]+)/)) && (c = ["opera", a[1]]),
            (a = b.match(/version\/([\d.]+).*safari/)) && (c = ["safari", a[1]]),
            c
        },
        b.exports = {
            version: d
        }
    },
    {}],
    3 : [function(a, b) {
        var c, d, e, f, g, h, i, j, k, l, m, n, o, p, q, r;
        d = a("./browser.coffee"),
        h = function(a, b, d) {
            return d = d || "ease",
            c(a),
            a.style.webkitBackfaceisibility = "hidden",
            a.style.webkitPerspective = "1000",
            a.style.webkitTransition = "all " + b + "s " + d
        },
        c = function(a) {
            var b, c, d, e, f;
            b = $("div.simulator div.pages section.page.active")[0],
            e = "undefined" != typeof b && $(b).length >= 1 ? -n(b) : 0,
            /translateZ\(.+?\)/.test(a.style.webkitTransform) || (d = "translateX(" + e + "px) translateY(0) translateZ(0) rotateX(0deg) rotateY(0deg) rotateZ(0deg) scaleX(1) scaleY(1) scaleZ(1) skewX(0deg) skewY(0deg)", a.style.left = 0, a.style.top = 0, a.style.transform = d, a.style.webkitTransform = d);
            try {
                if (!/z-index/.test(a.style.zIndex)) return c = $(".simulator section.active").find(".component").size(),
                a.style.zIndex = 200 + c
            } catch(g) {
                f = g
            }
        },
        g = function(a) {
            return a.style.webkitTransition = "",
            a.style.transition = ""
        },
        f = function(a, b) {
            var c, d, f;
            b.transformOrigin ? a.style.webkitTransformOrigin = b.transformOrigin: (a.style.webkitTransformOrigin = "", a.style.transformOrigin = ""),
            d = [];
            for (c in b) f = b[c],
            "x" === c || "y" === c || "z" === c || "rotationX" === c || "rotationY" === c || "rotationZ" === c || "skewY" === c || "skewX" === c || "scaleX" === c || "scaleY" === c || "scaleZ" === c ? d.push(r(a, c, f)) : (c = e(c), d.push(c in a.style ? a.style[c] = f: void 0));
            return d
        },
        e = function(a) {
            return a.replace(/-+(.)?/g,
            function(a, b) {
                return b ? b.toUpperCase() : ""
            })
        },
        p = function(a, b) {
            var c, d, e, f, g;
            return d = document.getElementsByTagName("head")[0],
            c = new RegExp("@keyframes " + a),
            d.getElementsByTagName("style")[0] ? (f = d.getElementsByTagName("style")[0], g = f.innerHTML, c.test(g) ? void 0 : (e = "@keyframes " + a + b + "@-moz-keyframes " + a + b + "@-o-keyframes " + a + b + "@-webkit-keyframes " + a + b, f.styleSheet ? f.styleSheet.cssText = e: f.appendChild(document.createTextNode(e)))) : (f = document.createElement("style"), f.type = "text/css", e = "@keyframes " + a + b + "@-moz-keyframes " + a + b + "@-o-keyframes " + a + b + "@-webkit-keyframes " + a + b, f.styleSheet ? f.styleSheet.cssText = e: f.appendChild(document.createTextNode(e)), d.appendChild(f))
        },
        q = function(a) {
            return a.style["-webkit-animation"] ? a.style["-webkit-animation"] = null: void 0
        },
        r = function(a, b, c) {
            var e, f;
            return f = "ie" === d.version()[0] || "firefox" === d.version()[0] ? a.style.transform: f = a.style.webkitTransform,
            "x" === b ? (e = /translateX\(.+?\)/, e.test(f) ? (a.style.transform = f.replace(e, "translateX(" + c + "px)"), a.style.webkitTransform = f.replace(e, "translateX(" + c + "px)")) : (a.style.transform += " translateX(" + c + "px)", a.style.webkitTransform += " translateX(" + c + "px)"), void(a.style.left && (a.style.left = "0px"))) : "y" === b ? (e = /translateY\(.+?\)/, e.test(f) ? (a.style.transform = f.replace(e, "translateY(" + c + "px)"), a.style.webkitTransform = f.replace(e, "translateY(" + c + "px)")) : (a.style.transform += " translateY(" + c + "px)", a.style.webkitTransform += " translateY(" + c + "px)"), void(a.style.top && (a.style.top = "0px"))) : "z" === b ? (e = /translateZ\(.+?\)/, void(e.test(f) ? (a.style.transform = f.replace(e, "translateZ(" + c + "px)"), a.style.webkitTransform = f.replace(e, "translateZ(" + c + "px)")) : (a.style.transform += " translateZ(" + c + "px)", a.style.webkitTransform += " translateZ(" + c + "px)"))) : "rotationX" === b ? (e = /rotateX\(.+?\)/, void(e.test(f) ? (a.style.transform = f.replace(e, "rotateX(" + c + "deg)"), a.style.webkitTransform = f.replace(e, "rotateX(" + c + "deg)")) : (a.style.transform += " rotateX(" + c + "deg)", a.style.webkitTransform += " rotateX(" + c + "deg)"))) : "rotationY" === b ? (e = /rotateY\(.+?\)/, void(e.test(f) ? (a.style.transform = f.replace(e, "rotateY(" + c + "deg)"), a.style.webkitTransform = f.replace(e, "rotateY(" + c + "deg)")) : (a.style.transform += " rotateY(" + c + "deg)", a.style.webkitTransform += " rotateY(" + c + "deg)"))) : "rotationZ" === b ? (e = /rotateZ\(.+?\)/, void(e.test(f) ? (a.style.transform = f.replace(e, "rotateZ(" + c + "deg)"), a.style.webkitTransform = f.replace(e, "rotateZ(" + c + "deg)")) : (a.style.transform += " rotateZ(" + c + "deg)", a.style.webkitTransform += " rotateZ(" + c + "deg)"))) : "scaleX" === b ? (e = /scaleX\(.+?\)/, void(e.test(f) ? (a.style.transform = f.replace(e, "scaleX(" + c + ")"), a.style.webkitTransform = f.replace(e, "scaleX(" + c + ")")) : (a.style.transform += " scaleX(" + c + ")", a.style.webkitTransform += " scaleX(" + c + ")"))) : "scaleY" === b ? (e = /scaleY\(.+?\)/, void(e.test(f) ? (a.style.transform = f.replace(e, "scaleY(" + c + ")"), a.style.webkitTransform = f.replace(e, "scaleY(" + c + ")")) : (a.style.transform += " scaleY(" + c + ")", a.style.webkitTransform += " scaleY(" + c + ")"))) : "scaleZ" === b ? (e = /scaleZ\(.+?\)/, void(e.test(f) ? (a.style.transform = f.replace(e, "scaleZ(" + c + ")"), a.style.webkitTransform = f.replace(e, "scaleZ(" + c + ")")) : (a.style.transform += " scaleZ(" + c + ")", a.style.webkitTransform += " scaleZ(" + c + ")"))) : "skewX" === b ? (e = /skewX\(.+?\)/, void(e.test(f) ? (a.style.transform = f.replace(e, "skewX(" + c + "deg)"), a.style.webkitTransform = f.replace(e, "skewX(" + c + "deg)")) : (a.style.transform += " skewX(" + c + "deg)", a.style.webkitTransform += " skewX(" + c + "deg)"))) : void("skewY" === b && (e = /skewY\(.+?\)/, e.test(f) ? (a.style.transform = f.replace(e, "skewY(" + c + "deg)"), a.style.webkitTransform = f.replace(e, "skewY(" + c + "deg)")) : (a.style.transform += " skewY(" + c + "deg)", a.style.webkitTransform += " skewY(" + c + "deg)")))
        },
        l = function(a) {
            var b, c;
            return c = "ie" === d.version()[0] || "firefox" === d.version()[0] ? a.style.transform: c = a.style.webkitTransform,
            b = parseFloat(c.match(/scaleX\(.*?\)/)[0].replace(/(scaleX\()|\)/, ""))
        },
        m = function(a) {
            var b, c;
            return c = "ie" === d.version()[0] || "firefox" === d.version()[0] ? a.style.transform: c = a.style.webkitTransform,
            b = parseFloat(c.match(/scaleY\(.*?\)/)[0].replace(/(scaleY\()|\)/, ""))
        },
        i = function(a) {
            var b, c, e;
            e = "ie" === d.version()[0] || "firefox" === d.version()[0] ? a.style.transform: e = a.style.webkitTransform;
            try {
                return c = parseFloat(e.match(/rotateX\(.*?\)/)[0].replace(/(rotateX\()|\)/, ""))
            } catch(f) {
                return b = f,
                0
            }
        },
        j = function(a) {
            var b, c, e;
            e = "ie" === d.version()[0] || "firefox" === d.version()[0] ? a.style.transform: e = a.style.webkitTransform;
            try {
                return c = parseFloat(e.match(/rotateY\(.*?\)/)[0].replace(/(rotateY\()|\)/, ""))
            } catch(f) {
                return b = f,
                0
            }
        },
        k = function(a) {
            var b, c, e;
            e = "ie" === d.version()[0] || "firefox" === d.version()[0] ? a.style.transform: e = a.style.webkitTransform;

            try {
                return c = parseFloat(e.match(/rotateZ\(.*?\)/)[0].replace(/(rotateZ\()|\)/, ""))
            } catch(f) {
                return b = f,
                0
            }
        },
        o = function(a) {
            var b, c, e;
            return c = "ie" === d.version()[0] || "firefox" === d.version()[0] ? a.style.transform: c = a.style.webkitTransform,
            e = 0,
            b = c.match(/translateY\(.*?\)/),
            null !== b && (e = parseFloat(b[0].replace(/(translateY\()|\)/, ""))),
            0 !== e ? e: e = a.style.top ? parseFloat(a.style.top.replace("px", "")) : 0
        },
        n = function(a) {
            var b, c, e;
            return c = "ie" === d.version()[0] || "firefox" === d.version()[0] ? a.style.transform: c = a.style.webkitTransform,
            e = 0,
            b = c.match(/translateX\(.*?\)/),
            null !== b && (e = parseFloat(b[0].replace(/(translateX\()|\)/, ""))),
            0 !== e ? e: e = a.style.left ? parseFloat(a.style.left.replace("px", "")) : 0
        },
        b.exports = {
            enableAnimation: h,
            disableAnimation: g,
            addDefaultTransform: c,
            css: f,
            getScaleX: l,
            getScaleY: m,
            getRotateX: i,
            getRotateY: j,
            getRotateZ: k,
            getY: o,
            getX: n,
            regKeyFrames: p,
            rmKeyFrames: q
        }
    },
    {
        "./browser.coffee": 2
    }],
    4 : [function(a) {
        var b, c;
        b = a("eventemitter2").EventEmitter2,
        c = new b,
        c.setMaxListeners(16),
        window.RP.Bus = c
    },
    {
        eventemitter2: 1
    }],
    5 : [function(a) {
        var b, c;
        window.RP = {},
        window.$ = $$,
        a("./../event-bus.coffee"),
        c = a("./../slides/slide.coffee"),
        window.RP.Slide = c,
        b = a("./../css.coffee"),
        window.RP.Css = b
    },
    {
        "./../css.coffee": 3,
        "./../event-bus.coffee": 4,
        "./../slides/slide.coffee": 7
    }],
    6 : [function(a, b) {
        var c, d, e, f, g, h, i, j, k, l, m, n, o, p;
        h = document.documentElement.clientWidth,
        e = document.documentElement.clientHeight,
        k = new EventEmitter2,
        j = null,
        m = null,
        n = null,
        d = 24,
        c = 16,
        g = !1,
        f = !1,
        o = void 0,
        p = !1,
        $(window).on("touchstart",
        function(a) {
            return $(a.target).hasClass("swipe-component") ? void 0 : (o = a, -1 !== navigator.userAgent.indexOf("iPhone") ? !1 : !0)
        }),
        $(window).on("touchmove",
        function(a) {
            var b, q, r, s;
            return ! $(a.target).hasClass("swipe-component") && o && (p || (p = !0, g = !1, f = !1, n = +new Date, m = j = l(a)), null !== m) ? (j = l(a), j.x < 0 || j.x > h || j.y < 0 || j.y > e ? i(a) : (k.emit("touchmove", j), r = j.x - m.x, s = j.y - m.y, b = Math.abs(r), q = Math.abs(s), q > d && !g ? (f = !0, 0 > s ? k.emit("Swiping Up", q) : k.emit("Swiping Down", q)) : b > c && !f && (g = !0, 0 > r ? k.emit("Swiping Left", b) : k.emit("Swiping Right", b)), !1)) : void 0
        }),
        $(window).on("touchend",
        function(a) {
            return o && p ? (o = void 0, p = !1, i(a)) : void 0
        }),
        i = function(a) {
            var b, e, h, i, l, o, p, q;
            return $(a.target).hasClass("swipe-component") || null === m ? void 0 : (h = +new Date, k.emit("touchend", j), i = h - n, l = j.x - m.x, o = j.y - m.y, b = Math.abs(l), e = Math.abs(o), p = b / i, q = e / i, e > d && !g ? (f = !0, 0 > o ? k.emit("Swipe Up", e, q) : k.emit("Swipe Down", e, q)) : b > c && !f && (g = !0, 0 > l ? k.emit("Swipe Left", b, p) : k.emit("Swipe Right", b, p)), m = null)
        },
        l = function(a) {
            var b, c;
            return b = a.clientX || a.touches[0].clientX,
            c = a.clientY || a.touches[0].clientY,
            {
                x: b,
                y: c
            }
        },
        b.exports = k
    },
    {}],
    7 : [function(a, b) {
        var c, d, e, f, g, h, i, j = function(a, b) {
            function c() {
                this.constructor = a
            }
            for (var d in b) k.call(b, d) && (a[d] = b[d]);
            return c.prototype = b.prototype,
            a.prototype = new c,
            a.__super__ = b.prototype,
            a
        },
        k = {}.hasOwnProperty;
        f = document.documentElement.clientWidth,
        d = document.documentElement.clientHeight,
        c = 320,
        g = a("./../css.coffee"),
        h = a("./gesture-event.coffee"),
        i = null,
        void 0 !== window.top.RP && (i = window.top.RP.Bus),
        e = function(a) {
            function b(a) {
                this.selectionSize = a,
                this.$elementPages = $("div.pages"),
                this.$elementProgress = $('<ul id="slide-progress"></ul>'),
                this.$elementSections = $("section.page"),
                this.$elementPages.css("width", f + "px"),
                $("div.wrapper").append(this.$elementProgress),
                this.GAP = 32,
                this.MAX_GAP = .1 * d,
                this.currentIndex = 0,
                this.able = !1,
                this.duration = 1,
                this.isAnimating = !1,
                this.swipePermission = !0,
                this.currentSectionLeft = 0
            }
            return j(b, a),
            b.prototype.init = function() {
                var a, b, c, d;
                for (c = "", a = b = 0, d = this.selectionSize; d >= 0 ? d > b: b > d; a = d >= 0 ? ++b: --b) c += "<li id='progress-" + a + "'></li>";
                return this.$elementProgress.append(c),
                this._setPagesPosByIndex(0),
                this._activeProgressByIndex(this.currentIndex),
                this._initEvents(),
                this._changeSwipePermission()
            },
            b.prototype.initPage = function(a) {
                var b;
                return b = parseInt(a.style.width.replace("px", "")) / 320,
                $(a).css("width", f * b + "px"),
                $(a).css("height", d + "px"),
                a.style.webkitTransform = "translateX(0px) translateY(0px) translateZ(0px)"
            },
            b.prototype.enable = function() {
                return this.able = !0
            },
            b.prototype.disable = function() {
                return this.able = !1
            },
            b.prototype.setDuration = function(a) {
                return this.duration = a || this.duration
            },
            b.prototype._setPagesVerticalPos = function(a, b) {
                var c;
                return "string" == typeof b && a.y && (c = "+" === b[0] ? 1 : -1, b = a.y + c * parseInt(b.slice(2))),
                a[0].style.webkitTransform = "translateX(0px) translateY(" + b + "px) translateZ(0px)",
                a.y = b
            },
            b.prototype._setSectionHorizontalPos = function(a, b) {
                var c;
                return "string" == typeof b && a.x && (c = "+" === b[0] ? 1 : -1, b = a.x + c * parseInt(b.slice(2))),
                a[0].style.webkitTransform = "translateX(" + b + "px) translateY(0px) translateZ(0px)",
                a.x = b
            },
            b.prototype._changeSwipePermission = function() {
                return this.swipePermission = !0
            },
            b.prototype._reachFirstSection = function() {
                return 0 === this.currentIndex
            },
            b.prototype._reachLastSection = function() {
                return this.currentIndex === this.$elementSections.length - 1
            },
            b.prototype._notOverMaxGapLimitDist = function(a) {
                return a > this.MAX_GAP ? this.MAX_GAP: a
            },
            b.prototype._renderProgress = function(a, b) {
                return a > this.GAP ? this._activeProgressByIndex(b) : this.currentActiveProgressIndex !== this.currentIndex ? this._activeProgressByIndex(this.currentIndex) : void 0
            },
            b.prototype._initSectionLeft = function() {
                var a;
                return a = $(this.$elementSections[this.currentIndex]),
                this.currentSectionLeft = g.getX(a[0]) ? g.getX(a[0]) : 0
            },
            b.prototype._swipeUp = function(a, b) {
                var c, e;
                return this.able && !this.isAnimating ? (e = !1, !this._reachLastSection() && (a > this.GAP || b > 1) && (this.previousIndex = this.currentIndex, this.currentIndex++, e = !0, c = b > 2 ? .15 : (1 - a / d) * this.duration, this._enableAnimation(c), setTimeout(function(a) {
                    return function() {
                        return a._disableAnimation(),
                        e ? a._triggerActive() : void 0
                    }
                } (this), 1.1 * c * 1e3)), this._setPagesPosByIndex(this.currentIndex)) : void 0
            },
            b.prototype._swipeDown = function(a, b) {
                var c, e;
                return this.able && !this.isAnimating ? (e = !1, 0 !== this.currentIndex && (a > this.GAP || b > 1) && (this.previousIndex = this.currentIndex, this.currentIndex--, e = !0, c = b > 2 ? .15 : (1 - a / d) * this.duration, this._enableAnimation(c), setTimeout(function(a) {
                    return function() {
                        return a._disableAnimation(),
                        e ? a._triggerActive() : void 0
                    }
                } (this), 1.1 * c * 1e3)), this._setPagesPosByIndex(this.currentIndex)) : void 0
            },
            b.prototype._initEvents = function() {
                return $(this.$elementSections[this.currentIndex]).swipeLeft(function(a) {
                    return function() {
                        return a._initSectionLeft()
                    }
                } (this)),
                $(this.$elementSections[this.currentIndex]).swipeRight(function(a) {
                    return function() {
                        return a._initSectionLeft()
                    }
                } (this)),
                h.on("Swiping Up",
                function(a) {
                    return function(b) {
                        return a.able && !a.isAnimating ? (a._reachLastSection() ? b = a._notOverMaxGapLimitDist(b) : a._renderProgress(b, a.currentIndex + 1), a.swipePermission ? a._setPagesVerticalPos(a.$elementPages, -a.currentIndex * d - b) : void 0) : void 0
                    }
                } (this)),
                h.on("Swiping Down",
                function(a) {
                    return function(b) {
                        return a.able && !a.isAnimating ? (a._reachFirstSection() ? b = a._notOverMaxGapLimitDist(b) : a._renderProgress(b, a.currentIndex - 1), a.swipePermission ? a._setPagesVerticalPos(a.$elementPages, -a.currentIndex * d + b) : void 0) : void 0
                    }
                } (this)),
                h.on("Swiping Left",
                function(a) {
                    return function(b) {
                        var c;
                        return ! a.able || a.isAnimating || (c = $(a.$elementSections[a.currentIndex]), c.width() <= f || a.currentSectionLeft <= f - c.width() || a.currentSectionLeft - b <= f - c.width()) ? void 0 : a._setSectionHorizontalPos(c, a.currentSectionLeft - b)
                    }
                } (this)),
                h.on("Swiping Right",
                function(a) {
                    return function(b) {
                        var c;
                        return ! a.able || a.isAnimating || (c = $(a.$elementSections[a.currentIndex]), c.width() <= f || a.currentSectionLeft >= 0 || a.currentSectionLeft + b > 0) ? void 0 : a._setSectionHorizontalPos(c, a.currentSectionLeft + b)
                    }
                } (this)),
                h.on("Swipe Left",
                function(a) {
                    return function(b, c) {
                        var d, e, g, h;
                        return a.able && !a.isAnimating ? (h = !1, d = $(a.$elementSections[a.currentIndex]), g = c > 2 ? .1 : (1 - b / d.width()) * a.duration / 2, a._enableHorizontalAnimation(g), setTimeout(function() {
                            return a._disableHorizontalAnimation()
                        },
                        1.1 * g * 1e3), e = Math.ceil(a.currentSectionLeft / f - 1) * f, e <= f - d.width() && (e = f - d.width()), a._setSectionHorizontalPos(d, e), a.currentSectionLeft = e) : void 0
                    }
                } (this)),
                h.on("Swipe Right",
                function(a) {
                    return function(b, c) {
                        var d, e, g, h;
                        return a.able && !a.isAnimating ? (h = !1, d = $(a.$elementSections[a.currentIndex]), g = c > 2 ? .1 : (1 - b / d.width()) * a.duration / 2, a._enableHorizontalAnimation(g), setTimeout(function() {
                            return a._disableHorizontalAnimation()
                        },
                        1.1 * g * 1e3), e = Math.floor(a.currentSectionLeft / f + 1) * f, e >= 0 && (e = 0), a._setSectionHorizontalPos(d, e), a.currentSectionLeft = e) : void 0
                    }
                } (this)),
                h.on("Swipe Up",
                function(a) {
                    return function(b, c) {
                        return a._swipeUp(b, c)
                    }
                } (this)),
                h.on("Swipe Down",
                function(a) {
                    return function(b, c) {
                        return a._swipeDown(b, c)
                    }
                } (this)),
                null !== i ? (i.on("Btn Swipe Up",
                function(a) {
                    return function(b, c) {
                        return null == b && (b = 120),
                        null == c && (c = .8),
                        a._swipeUp(b, c)
                    }
                } (this)), i.on("Btn Swipe Down",
                function(a) {
                    return function(b, c) {
                        return null == b && (b = 120),
                        null == c && (c = .8),
                        a._swipeDown(b, c)
                    }
                } (this))) : void 0
            },
            b.prototype._triggerActive = function() {
                return this._activeProgressByIndex(this.currentIndex),
                "number" == typeof this.previousIndex && this.emit("deactive", this.$elementSections[this.previousIndex]),
                "number" == typeof this.currentIndex ? this.emit("active", this.$elementSections[this.currentIndex]) : void 0
            },
            b.prototype._setPagesPosByIndex = function(a) {
                return this._setPagesVerticalPos(this.$elementPages, -a * d)
            },
            b.prototype._activeProgressByIndex = function(a) {
                return this.currentActiveProgressIndex = a,
                $("#slide-progress li.active").removeClass("active"),
                $("#progress-" + a).addClass("active")
            },
            b.prototype._getHeight = function() {
                return d
            },
            b.prototype._getWidth = function() {
                return f
            },
            b.prototype._enableHorizontalAnimation = function(a) {
                return this.isAnimating = !0,
                this.$elementSections[this.currentIndex].style.webkitTransition = "all " + a + "s ease-out",
                this.$elementSections[this.currentIndex].style.Transition = "all " + a + "s ease-out"
            },
            b.prototype._disableHorizontalAnimation = function() {
                return this.isAnimating = !1,
                this.$elementSections[this.currentIndex].style.webkitTransition = "",
                this.$elementSections[this.currentIndex].style.Transition = ""
            },
            b.prototype._enableAnimation = function() {},
            b.prototype._disableAnimation = function() {},
            b
        } (EventEmitter2),
        b.exports = e
    },
    {
        "./../css.coffee": 3,
        "./gesture-event.coffee": 6
    }]
},
{},
[5]),
function b(a, c, d) {
    function e(g, h) {
        if (!c[g]) {
            if (!a[g]) {
                var i = "function" == typeof require && require;
                if (!h && i) return i(g, !0);
                if (f) return f(g, !0);
                throw new Error("Cannot find module '" + g + "'")
            }
            var j = c[g] = {
                exports: {}
            };
            a[g][0].call(j.exports,
            function(b) {
                var c = a[g][1][b];
                return e(c ? c: b)
            },
            j, j.exports, b, a, c, d)
        }
        return c[g].exports
    }
    for (var f = "function" == typeof require && require,
    g = 0; g < d.length; g++) e(d[g]);
    return e
} ({
    1 : [function() {
        var a, b, c, d, e, f, g, h, i, j, k;
        c = window.DOWNLOADURL + window.APPID + ".mp3?" + (new Date).getTime(),
        g = !1,
        b = function(a) {
            var b, c, d, e, f, g, h, i, j;
            return i = !0,
            d = !0,
            f = !1,
            b = null,
            c = null,
            e = null,
            g = null,
            h = null,
            j = null,
            $("#car_audio").length > 0 && (a || "open" === $("#car_audio").attr("val")) ? ($("#car_audio").get(0).play(), $("#car_audio").attr("val", "close"), $(".music_open").css("display", "block"), $(".music_close").css("display", "none")) : "close" === $("#car_audio").attr("val") ? ($("#car_audio").get(0).pause(), $("#car_audio").attr("val", "open"), $(".music_open").css("display", "none"), $(".music_close").css("display", "block")) : ($("#car_audio").attr("val", "open"), $(".music_open").css("display", "none"), $(".music_close").css("display", "block"))
        },
        d = function() {
            return g ? void 0 : (b(!0), g = !0)
        },
        j = function(a) {
            var b, d;
            return null != a.musicName && "" !== a.musicName && (c = window.DOWNLOADURL + a.musicName),
            b = document.getElementById("car_audio"),
            d = !1,
            $("#car_audio").on("canplay",
            function() {
                return d === !0 ? ($("#car_audio").attr("val", "open"), d = !0) : void 0
            }),
            $("#car_audio").attr("src", c)
        },
        k = function() {
            return setTimeout(function() {
                var a;
                return a = document.createElement("script"),
                a.defer = !0,
                a.async = !0,
                a.src = $("#viewLogUrl").val(),
                document.body.appendChild(a)
            },
            0)
        },
        a = function(a) {
            /*return $.ajax({
                type: "GET",
                url: "",
                data: {
                    musicId: a
                },
                dataType: "json",
                success: function(a) {
                    return "OK" !== a.result ? k() : ($(".music").css("display", "block"), j(a), k())
                }
            })*/
        },
        e = function() {
            return $(document.body).on("swipe", d),
            $(document.body).on("tap", d),
            $(document).on("tap", ".open_img",
            function() {
                return b()
            }),
            $(document).on("tap", ".close_img",
            function() {
                return b()
            })
        },
        f = function() {
            return a(window.APPID, c),
            e()
        },
        i = !1,
        h = function() {
            var a, b, c, d, e, g;
            if (!i) {
                if (i = !0, document.getElementById("loading-container")) {
                    for (a = .275 * window.innerWidth / 150, $("div#circle")[0].style.webkitTransform = "scaleX(" + a + ") scaleY(" + a + ")", $("div#circle").css("top", .367 * window.innerHeight - 75 * (1 - a) + "px"), $("div#circle").css("left", .3625 * window.innerWidth - 75 * (1 - a) + "px"), $("div#circle").removeClass("invisible"), $("#circle-filled-right").addClass("percent50"), c = 500, setTimeout(function() {
                        return $("#circle-filled-right").removeClass("percent50"),
                        $("#circle-filled-right").addClass("finish-percent50"),
                        $("#circle-unfilled-left").addClass("invisible"),
                        $("#circle-filled-left").removeClass("invisible"),
                        $("#circle-filled-left").addClass("percent80")
                    },
                    c / 2), setTimeout(function() {
                        return $("#circle-filled-left").addClass("percent100")
                    },
                    4 * c / 5), d = function(a, b) {
                        return setTimeout(function() {
                            return 10 > a && (a = "0" + a),
                            99 === a ? ($("#circle div.text.percent").addClass("hide"), $("#circle div.text.unit").addClass("hide"), $("#circle div.img").removeClass("hide")) : $("#circle div.text.percent").text(a)
                        },
                        a * b / 100)
                    },
                    g = [], b = e = 0; 100 > e; b = ++e) d(b, c),
                    g.push(function(a, b) {
                        return setTimeout(function() {
                            return $("#loading-container").css("opacity", 1 - (a + 1) / 100),
                            99 === a ? ($("#loading-container").addClass("hide"), startPage(), f()) : void 0
                        },
                        10 * a + b)
                    } (b, c));
                    return g
                }
                return f()
            }
        },
        window.onload = h,
        setTimeout(function() {
            return h()
        },
        3e3)
    },
    {}]
},
{},
[1]),
function c(a, b, d) {
    function e(g, h) {
        if (!b[g]) {
            if (!a[g]) {
                var i = "function" == typeof require && require;
                if (!h && i) return i(g, !0);
                if (f) return f(g, !0);
                throw new Error("Cannot find module '" + g + "'")
            }
            var j = b[g] = {
                exports: {}
            };
            a[g][0].call(j.exports,
            function(b) {
                var c = a[g][1][b];
                return e(c ? c: b)
            },
            j, j.exports, c, a, b, d)
        }
        return b[g].exports
    }
    for (var f = "function" == typeof require && require,
    g = 0; g < d.length; g++) e(d[g]);
    return e
} ({
    1 : [function(a, b) {
        var c, d, e, f;
        f = window.RP.Css,
        e = {},
        c = 0,
        d = {},
        e.getAnimationName = function(a, b) {
            var e, f;
            return e = "translateX" + a + "-translateY" + b,
            f = d[e],
            f || (f = d[e] = "bounce" + c++),
            f
        },
        e.start = function(a) {
            var b, c, d, e, g;
            return f.disableAnimation(a[0]),
            f.css(a[0], JSON.parse(a.attr("data-style-cache"))),
            c = null != (e = styler.x) ? e: f.getX(a[0]),
            d = null != (g = styler.y) ? g: f.getY(a[0]),
            b = this.getAnimationName(c, d),
            f.css(a[0], {
                "-webkit-animation": b + " 2s  backwards"
            }),
            f.regKeyFrames(b, "{0% { -webkit-transform: translateX(" + (5 + c) + "px) translateY(" + (100 + d) + "px); transform: translateX(" + (5 + c) + "px) translateY(" + (100 + d) + "px);} 5%{ -webkit-transform: translateX(" + (10 + c) + "px) translateY(" + (150 + d) + "px);  transform: translateX(" + (10 + c) + "px) translateY(" + (150 + d) + "px); } 15%{ -webkit-transform: scale(0.8) translateX(" + (15 + c) + "px) translateY(" + (200 + d) + "px); transform: scale(0.8) translateX(" + (15 + c) + "px) translateY(" + (200 + d) + "px);} 30%{ -webkit-transform: scale(1) translateX(" + (20 + c) + "px) translateY(" + ( - 50 + d) + "px); transform: scale(1) translateX(" + (18 + c) + "px) translateY(" + ( - 50 + d) + "px);} 40%{ -webkit-transform: scale(1) translateX(" + (20 + c) + "px) translateY(" + ( - 100 + d) + "px); transform: scale(1) translateX(" + (20 + c) + "px) translateY(" + ( - 100 + d) + "px);} 50%{ -webkit-transform: translateX(" + (25 + c) + "px) translateY(" + ( - 150 + d) + "px); transform: translateX(" + (25 + c) + "px) translateY(" + ( - 150 + d) + "px);} 60%{ -webkit-transform: ttranslateX(" + (30 + c) + "px) translateY(" + ( - 100 + d) + "px); transform: translateX(" + (30 + c) + "px) translateY(" + ( - 100 + d) + "px);} 70%{ -webkit-transform: translateX(" + (35 + c) + "px) translateY(" + ( - 50 + d) + "px); transform: translateX(" + (35 + c) + "px) translateY(" + ( - 50 + d) + "px);} 80%{ -webkit-transform: scale(0.9) translateX(" + (40 + c) + "px) translateY(" + (0 + d) + "px); transform: scale(0.9) translateX(" + (40 + c) + "px) translateY(" + (0 + d) + "px);} 90%{ -webkit-transform: scale(1) translateX(" + (45 + c) + "px) translateY(" + ( - 50 + d) + "px); transform: scale(1) translateX(" + (45 + c) + "px) translateY(" + ( - 50 + d) + "px);} 100% { -webkit-transform: translateX(" + (50 + c) + "px) translateY(" + (0 + d) + "px); transform: translateX(" + (50 + c) + "px) translateY(" + (0 + d) + "px); } }")
        },
        e.ready = function(a) {
            var b, c;
            return c = a.attr("data-style-cache"),
            c ? (b = JSON.parse(c), {
                y: b.y - 200,
                x: b.x - 50
            }) : (b = {
                opacity: a.css("opacity"),
                zIndex: a.css("z-index"),
                x: f.getX(a[0]),
                y: f.getY(a[0])
            },
            a.attr("data-style-cache", JSON.stringify(b))),
            f.rmKeyFrames(a[0])
        },
        b.exports = e
    },
    {}],
    2 : [function(a, b) {
        var c, d, e, f;
        f = window.RP.Css,
        e = {},
        c = 0,
        d = {},
        e.getAnimationName = function(a, b, e, f) {
            var g, h;
            return null == a && (a = 1),
            null == b && (b = 1),
            g = "scaleX-" + a + "-scaleY" + b + "-translateX" + e + "-translateY" + f,
            h = d[g],
            h || (h = d[g] = "bounceIn" + c++),
            h
        },
        e.start = function(a, b) {
            var c, d, e, g, h, i, j, k, l, m, n, o, p, q, r;
            return null == b && (b = 1),
            f.disableAnimation(a[0]),
            k = JSON.parse(a.attr("data-style-cache")),
            f.css(a[0], k),
            i = null != (o = k.scaleX) ? o: 1,
            j = null != (p = k.scaleY) ? p: 1,
            d = "scaleX(" + .3 * i + ") scaleY(" + .3 * j + ")",
            g = "scaleX(" + 1.05 * i + ") scaleY(" + 1.05 * j + ")",
            h = "scaleX(" + .9 * i + ") scaleY(" + .9 * j + ")",
            e = "scaleX(" + 1 * i + ") scaleY(" + 1 * j + ")",
            m = null != (q = k.x) ? q: f.getX(a[0]),
            n = null != (r = k.y) ? r: f.getY(a[0]),
            l = "translateX(" + m + "px) translateY(" + n + "px)",
            c = this.getAnimationName(i, j, m, n),
            f.css(a[0], {
                "-webkit-animation": c + " " + b + "s backwards",
                animation: c + " " + b + "s backwards"
            }),
            f.regKeyFrames(c, "{ 0%{opacity:0; -webkit-transform: " + l + " " + d + "; transform: " + l + " " + d + ";} 50%{opacity:1;transform: " + l + " " + g + "; -webkit-transform: " + l + " " + g + ";} 70%{transform: " + l + " " + h + "; -webkit-transform: " + l + " " + h + ";} 100%{transform: " + l + " " + e + "; -webkit-transform: " + l + " " + e + ";} }")
        },
        e.ready = function(a) {
            var b, c;
            return c = a.attr("data-style-cache"),
            c ? b = JSON.parse(c) : (b = {
                opacity: a.css("opacity"),
                zIndex: a.css("z-index"),
                scaleX: f.getScaleX(a[0]),
                scaleY: f.getScaleY(a[0]),
                y: f.getY(a[0]),
                x: f.getX(a[0])
            },
            a.attr("data-style-cache", JSON.stringify(b))),
            f.css(a[0], {
                opacity: 0
            }),
            f.rmKeyFrames(a[0])
        },
        b.exports = e
    },
    {}],
    3 : [function(a, b) {
        var c, d, e, f;
        e = window.RP.Css,
        c = function() {
            function a(a) {
                this.dx = a.x,
                this.dy = a.y,
                this.dz = a.z,
                this.tx = 0,
                this.ty = 0,
                this.tz = 0,
                this.z = a.x,
                this.x = a.y,
                this.y = a.z,
                this.radius = a.radius,
                this.r = a.r,
                this.g = a.g,
                this.b = a.b,
                this.a = a.a,
                this.canvas = a.canvas,
                this.context = a.context,
                this.focallength = a.focallength
            }
            return a.prototype.paint = function() {
                var a, b, c, d, e, f;
                return a = this.canvas,
                c = this.context,
                e = this.focallength,
                d = a.width,
                b = a.height,
                c.save(),
                c.beginPath(),
                f = e / (e + this.z),
                c.arc(d / 2 + (this.x - d / 2) * f, b / 2 + (this.y - b / 2) * f, this.radius * f, 0, 2 * Math.PI),
                c.fillStyle = "rgba(" + this.r + "," + this.g + "," + this.b + "," + this.a + ")",
                c.fill(),
                c.restore()
            },
            a
        } (),
        d = function() {
            function a(a) {
                this.source = a.source,
                this.delay = a.delay,
                this.duration = a.duration,
                this.focallength = a.focallength || 250,
                this.pause = !1,
                this.lastTime = null,
                this.derection = !0,
                this.init()
            }
            return a.prototype.init = function() {
                var a, b, c;
                return b = $(this.source),
                a = b.parent(),
                c = document.createElement("canvas"),
                $(c).attr("style", this.getPositionStyle()),
                c.width = $(this.source).width(),
                c.height = $(this.source).height(),
                this.canvas = c,
                this.context = c.getContext("2d"),
                a.append(c),
                $(this.canvas).addClass("lizihua-canvas"),
                $(this.source).addClass("lizihua-source"),
                this.drawSource()
            },
            a.prototype.drawSource = function() {
                var a, b, c, d, e, f, g, h;
                return b = this.canvas,
                c = this.context,
                a = $(this.source),
                c.save(),
                a.hasClass("image") && (d = this, h = a.attr("src"), h.indexOf("http://") > -1 && (g = h.substring(7), e = g.indexOf("/"), f = "http://" + g.substring(0, e + 1), this.source.crossOrigin = f), this.source.onload = function(a) {
					//console.log(e);
					//console.log(a.target);
                    return c.drawImage(a.target, 0, 0, b.width, b.height),
                    d.getImageData()
                },
                a.attr("src", h + "?_=" + (new Date).getTime())),
                c.restore()
            },
            a.prototype.getFontStyle = function() {
                var a, b, c, d, e, f;
                return a = $(this.source),
                d = a.css("font-family"),
                e = a.css("font-size"),
                f = a.css("font-weight"),
                b = a.css("text-align"),
                c = a.css("color"),
                {
                    color: c,
                    align: b,
                    font: e + " " + d + " " + f
                }
            },
            a.prototype.getPositionStyle = function() {
                var a;
                return a = $(this.source),
                a.attr("style")
            },
            a.prototype.forEach = function(a, b) {
                var c, d, e, f, g;
                for (g = [], c = e = 0, f = a.length; f > e; c = ++e) d = a[c],
                g.push(b.call(d, d));
                return g
            },
            a.prototype.Dot = c,
            a.prototype.newDot = function(a) {
                return a.canvas = this.canvas,
                a.context = this.context,
                a.focallength = this.focallength,
                new this.Dot(a)
            },
            a.prototype.getImageData = function() {
                var a, b, c, d, e, f, g, h, i, j, k, l, m, n, o, p, q;
                for (c = this.canvas, d = this.context, j = d.getImageData(0, 0, c.width, c.height), d.clearRect(0, 0, c.width, c.height), g = [], e = j.data, l = n = 0, p = j.width; p >= n; l = n += 6) for (m = o = 0, q = j.height; q >= o; m = o += 6) i = 4 * (m * j.width + l),
                k = e[i],
                h = e[i + 1],
                b = e[i + 2],
                a = e[i + 3],
                a > .2 && (f = this.newDot({
                    x: l - 1,
                    y: m - 1,
                    z: 0,
                    r: k,
                    g: h,
                    b: b,
                    a: a,
                    radius: 1
                }), g.push(f));
                return this.dots = g
            },
            a.prototype.initAnimate = function() {
                var a, b, c, d;
                return a = this.canvas,
                b = this.context,
                c = this.focallength,
                this.forEach(this.dots,
                function() {
                    return this.x = Math.random() * a.width,
                    this.y = Math.random() * a.height,
                    this.z = Math.random() * c * 2 - c,
                    this.tx = Math.random() * a.width,
                    this.ty = Math.random() * a.height,
                    this.tz = Math.random() * c * 2 - c,
                    this.paint()
                }),
                this.pause = !1,
                this.lastTime = void 0,
                this.derection = !0,
                d = this.source,
                $(a).css("display", "block"),
                $(d).css("display", "block"),
                this.animate()
            },
            a.prototype.animate = function() {
                var a, b, c, d, e;
                if (d = this, a = this.canvas, b = this.context, c = this.dots, e = +new Date, b.clearRect(0, 0, a.width, a.height), this.forEach(c,
                function() {
                    var a;
                    return a = this,
                    d.derection ? Math.abs(a.dx - a.x) < .1 && Math.abs(a.dy - a.y) < .1 && Math.abs(a.dz - a.z) < .1 ? (a.x = a.dx, a.y = a.dy, a.z = a.dz, d.pause = !0) : (a.x = a.x + .1 * (a.dx - a.x), a.y = a.y + .1 * (a.dy - a.y), a.z = a.z + .1 * (a.dz - a.z), d.lastTime = +new Date) : Math.abs(a.tx - a.x) < .1 && Math.abs(a.ty - a.y) < .1 && Math.abs(a.tz - a.z) < .1 ? (a.x = a.tx, a.y = a.ty, a.z = a.tz, d.pause = !0) : (a.x = a.x + .1 * (a.tx - a.x), a.y = a.y + .1 * (a.ty - a.y), a.z = a.z + .1 * (a.tz - a.z), d.pause = !1),
                    a.paint()
                }), !this.pause) {
                    if (window.requestAnimationFrame) return requestAnimationFrame(function() {
                        return d.animate()
                    });
                    if (window.webkitRequestAnimationFrame) return webkitRequestAnimationFrame(function() {
                        return d.animate()
                    });
                    if (window.msRequestAnimationFrame) return msRequestAnimationFrame(function() {
                        return d.animate()
                    });
                    if (window.mozRequestAnimationFrame) return d.animate()
                }
            },
            a
        } (),
        f = {},
        f.start = function(a) {
            var b;
            return $("div.simulator").length <= 0 && (b = a[0], b && b.dotEffect) ? b.dotEffect.initAnimate() : void 0
        },
        f.ready = function(a) {
            var b;
            return $("div.simulator").length <= 0 && (b = a[0], b && !a.hasClass("lizihua-source") && (b.dotEffect = new d({
                source: b
            })), b && b.dotEffect) ? ($(b.dotEffect.canvas).css("display", "none"), $(b.dotEffect.source).css("display", "none")) : void 0
        },
        b.exports = f
    },
    {}],
    4 : [function(a) {
        var b;
        b = {},
        b.show = a("./show.coffee"),
        b.fade = a("./fade.coffee"),
        b.fadeFromTop = a("./fadeFromTop.coffee"),
        b.fadeFromRight = a("./fadeFromRight.coffee"),
        b.fadeFromBottom = a("./fadeFromBottom.coffee"),
        b.fadeFromLeft = a("./fadeFromLeft.coffee"),
        b.fromtop = a("./fromtop.coffee"),
        b.fromright = a("./fromright.coffee"),
        b.frombottom = a("./frombottom.coffee"),
        b.fromleft = a("./fromleft.coffee"),
        b.rainy = a("./rainy.coffee"),
        b.rotation = a("./rotation.coffee"),
        b.spread = a("./spread.coffee"),
        b.flashing = a("./flashing.coffee"),
        b.bounceIn = a("./bounceIn.coffee"),
        b.erase = a("./erase.coffee"),
        b.bounce = a("./bounce.coffee"),
        b.jitter = a("./jitter.coffee"),
        b.light = a("./light.coffee"),
        b.rotation2d = a("./rotation2d.coffee"),
        b.small2big = a("./small2big.coffee"),
        b.dotEffect = a("./dot.coffee"),
        b.fireWorksEffect = a("./fireworks.coffee"),
        window.RP.effects = b
    },
    {
        "./bounce.coffee": 1,
        "./bounceIn.coffee": 2,
        "./dot.coffee": 3,
        "./erase.coffee": 5,
        "./fade.coffee": 6,
        "./fadeFromBottom.coffee": 7,
        "./fadeFromLeft.coffee": 8,
        "./fadeFromRight.coffee": 9,
        "./fadeFromTop.coffee": 10,
        "./fireworks.coffee": 11,
        "./flashing.coffee": 12,
        "./frombottom.coffee": 13,
        "./fromleft.coffee": 14,
        "./fromright.coffee": 15,
        "./fromtop.coffee": 16,
        "./jitter.coffee": 17,
        "./light.coffee": 18,
        "./rainy.coffee": 19,
        "./rotation.coffee": 21,
        "./rotation2d.coffee": 22,
        "./show.coffee": 23,
        "./small2big.coffee": 24,
        "./spread.coffee": 25
    }],
    5 : [function(a, b) {
        var c, d, e, f;
        c = a("./stackblur.js"),
        d = window.RP.Css,
        e = {},
        f = function(a, b, c) {
            var e, f, g, h, i, j, k, l, m, n, o, p;
            return o = 0,
            p = 0,
            k = 0,
            i = d.getScaleX(a[0]),
            j = d.getScaleY(a[0]),
            f = d.getX(a[0]),
            g = d.getY(a[0]),
            h = parseInt(a.css("width").replace("px", "")),
            e = parseInt(a.css("height").replace("px", "")),
            n = function() {
                return function(a) {
                    clearTimeout(k),
                    a.preventDefault(),
                    a.stopPropagation();
                    try {
                        return o = (a.targetTouches[0].pageX - f - (1 - i) * h / 2) / i,
                        p = (a.targetTouches[0].pageY - g - (1 - j) * e / 2) / j,
                        c.lineCap = "round",
                        c.lineJoin = "round",
                        c.lineWidth = 50,
                        c.globalCompositeOperation = "destination-out",
                        b.on("touchmove", m)
                    } catch(d) {
                        a = d
                    }
                }
            } (this),
            m = function() {
                return function(a) {
                    var b, d;
                    clearTimeout(k),
                    a.preventDefault(),
                    a.stopPropagation();
                    try {
                        return b = (a.targetTouches[0].pageX - f - (1 - i) * h / 2) / i,
                        d = (a.targetTouches[0].pageY - g - (1 - j) * e / 2) / j,
                        c.save(),
                        c.moveTo(o, p),
                        c.lineTo(b, d),
                        c.stroke(),
                        c.restore(),
                        o = b,
                        p = d
                    } catch(l) {
                        a = l
                    }
                }
            } (this),
            l = function() {
                return function(a) {
                    var d;
                    return b.off("touchmove", m),
                    a.preventDefault(),
                    a.stopPropagation(),
                    d = function() {
                        var a, d, e, f, g, h, i, j, k, m, o;
                        for (a = b[0], f = c.getImageData(0, 0, a.width, a.height), g = 30, d = 0, h = j = 0, m = f.width; g > 0 ? m >= j: j >= m; h = j += g) for (i = k = 0, o = f.height; g > 0 ? o >= k: k >= o; i = k += g) e = 4 * (i * f.width + h),
                        f.data[e + 3] > 0 && d++;
                        return d / (f.width * f.height / (g * g)) < .7 ? (b.addClass("hide"), b.off("touchstart", n), b.off("touchend", l)) : void 0
                    },
                    k = setTimeout(d, 100)
                }
            } (this),
            b.on("touchstart", n),
            b.on("touchend", l)
        },
        e.start = function() {},
        e.ready = function(a) {
            var b, d, e, g, h, i, j;
            return $("div.simulator").length <= 0 && (j = a.attr("src"), j && a.hasClass("image")) ? (a[0].$canvas && (a[0].$canvas.remove(), a[0].$canvas = void 0), b = $("<canvas></canvas>"), a.parent().append(b), a[0].$canvas = b, d = b[0].getContext("2d"), g = new Image, -1 !== j.indexOf("http://") && (i = j.substring(7), e = i.indexOf("/"), h = "http://" + i.substring(0, e + 1), g.crossOrigin = h), g.src = j + "?_=" + (new Date).getTime(), g.onload = function() {
                return function() {
                    var e, f;
                    return f = a.css("border-width"),
                    e = a.css("border-radius"),
                    b.attr("width", a.css("width")),
                    b.attr("height", a.css("height")),
                    b.css("position", "absolute"),
                    b.css("-webkit-transform", a.css("-webkit-transform")),
                    b.css("top", a.css("top")),
                    b.css("left", a.css("left")),
                    b.css("z-index", parseInt(a.css("z-index")) + 1),
                    b.css("border-width", f),
                    b.css("border-radius", e),
                    b.css("border-style", "solid"),
                    b.css("border-color", "transparent"),
                    d.globalCompositeOperation = "source-over",
                    d.drawImage(g, 0, 0, b[0].width, b[0].height),
                    d.globalCompositeOperation = "destination-out",
                    c.stackBlurCanvasRGBA(b[0], 0, 0, b[0].width, b[0].height, 25)
                }
            } (this), f(a, b, d)) : void 0
        },
        b.exports = e
    },
    {
        "./stackblur.js": 26
    }],
    6 : [function(a, b) {
        var c, d;
        c = window.RP.Css,
        d = {},
        d.start = function(a, b) {
            return c.enableAnimation(a[0], 2, "ease-out"),
            c.css(a[0], JSON.parse(a.attr("data-style-cache"))),
            setTimeout(function() {
                return c.disableAnimation(a[0])
            },
            1e3 * b)
        },
        d.ready = function(a) {
            var b, d;
            return d = a.attr("data-style-cache"),
            d ? b = JSON.parse(d) : (b = {
                opacity: a.css("opacity"),
                zIndex: a.css("z-index"),
                y: c.getY(a[0]),
                x: c.getX(a[0])
            },
            a.attr("data-style-cache", JSON.stringify(b))),
            c.css(a[0], {
                opacity: 0,
                x: b.x,
                y: b.y
            })
        },
        b.exports = d
    },
    {}],
    7 : [function(a, b) {
        var c, d;
        c = window.RP.Css,
        d = {},
        d.start = function(a) {
            return c.enableAnimation(a[0], .8, "ease"),
            c.css(a[0], JSON.parse(a.attr("data-style-cache"))),
            setTimeout(function() {
                return c.disableAnimation(a[0])
            },
            800)
        },
        d.ready = function(a) {
            var b, d;
            return d = a.attr("data-style-cache"),
            d ? b = JSON.parse(d) : (b = {
                opacity: a.css("opacity"),
                zIndex: a.css("z-index"),
                y: c.getY(a[0]),
                x: c.getX(a[0])
            },
            a.attr("data-style-cache", JSON.stringify(b))),
            c.css(a[0], {
                opacity: 0,
                y: b.y + 75
            })
        },
        b.exports = d
    },
    {}],
    8 : [function(a, b) {
        var c, d;
        c = window.RP.Css,
        d = {},
        d.start = function(a, b) {
            return null == b && (b = 1),
            c.enableAnimation(a[0], .8, "ease"),
            c.css(a[0], JSON.parse(a.attr("data-style-cache"))),
            setTimeout(function() {
                return c.disableAnimation(a[0])
            },
            800)
        },
        d.ready = function(a) {
            var b, d;
            return d = a.attr("data-style-cache"),
            d ? b = JSON.parse(d) : (b = {
                opacity: a.css("opacity"),
                zIndex: a.css("z-index"),
                y: c.getY(a[0]),
                x: c.getX(a[0])
            },
            a.attr("data-style-cache", JSON.stringify(b))),
            c.css(a[0], {
                opacity: 0,
                x: b.x - 75
            })
        },
        b.exports = d
    },
    {}],
    9 : [function(a, b) {
        var c, d;
        c = window.RP.Css,
        d = {},
        d.start = function(a) {
            return c.enableAnimation(a[0], .8, "ease"),
            c.css(a[0], JSON.parse(a.attr("data-style-cache"))),
            setTimeout(function() {
                return c.disableAnimation(a[0])
            },
            800)
        },
        d.ready = function(a) {
            var b, d;
            return d = a.attr("data-style-cache"),
            d ? b = JSON.parse(d) : (b = {
                opacity: a.css("opacity"),
                zIndex: a.css("z-index"),
                y: c.getY(a[0]),
                x: c.getX(a[0])
            },
            a.attr("data-style-cache", JSON.stringify(b))),
            c.css(a[0], {
                opacity: 0,
                x: b.x + 75
            })
        },
        b.exports = d
    },
    {}],
    10 : [function(a, b) {
        var c, d;
        c = window.RP.Css,
        d = {},
        d.start = function(a) {
            return c.enableAnimation(a[0], .8, "ease"),
            c.css(a[0], JSON.parse(a.attr("data-style-cache"))),
            setTimeout(function() {
                return c.disableAnimation(a[0])
            },
            800)
        },
        d.ready = function(a) {
            var b, d;
            return d = a.attr("data-style-cache"),
            d ? b = JSON.parse(d) : (b = {
                opacity: a.css("opacity"),
                zIndex: a.css("z-index"),
                y: c.getY(a[0]),
                x: c.getX(a[0])
            },
            a.attr("data-style-cache", JSON.stringify(b))),
            c.css(a[0], {
                opacity: 0,
                y: b.y - 75
            })
        },
        b.exports = d
    },
    {}],
    11 : [function(a, b) {
        var c, d, e, f, g, h, i, j;
        g = window.RP.Css,
        i = function() {
            return window.requestAnimFrame || window.webkitRequestAnimFrame || window.mozRequestAnimationFrame ||
            function(a) {
                return window.setTimeout(a, 1e3 / 60)
            }
        },
        window.requestAnimFrame = i(),
        j = function(a, b) {
            return Math.random() * (b - a) + a
        },
        f = function(a, b, c, d) {
            var e, f;
            return e = a - c,
            f = b - d,
            Math.sqrt(Math.pow(e, 2) + Math.pow(f, 2))
        },
        e = function() {
            function a(a) {
                var b;
                for (this.Fireworks = a.Fireworks, this.x = a.x, this.y = a.y, this.coordinates = [], this.coordinateCount = null != (b = a.coordinateCount) ? b: 5; this.coordinateCount--;) this.coordinates.push([this.x, this.y]);
                this.angle = j(0, 2 * Math.PI),
                this.speed = j(1, 10),
                this.friction = .95,
                this.gravity = 1,
                this.hue = j(a.hue - 20, a.hue + 20),
                this.brightness = j(50, 80),
                this.alpha = 1,
                this.decay = j(.015, .03)
            }
            return a.prototype.update = function(a) {
                var b;
                return this.coordinates.pop(),
                this.coordinates.unshift([this.x, this.y]),
                this.speed *= this.friction,
                this.x += Math.cos(this.angle) * this.speed,
                this.y += Math.sin(this.angle) * this.speed + this.gravity,
                this.alpha -= this.decay,
                this.alpha <= this.decay ? (b = this.Fireworks.particles, b.splice(a, 1)) : void 0
            },
            a.prototype.draw = function() {
                var a, b;
                return b = this.Fireworks.ctx,
                b.beginPath(),
                a = this.coordinates[this.coordinates.length - 1],
                b.moveTo(a[0], a[1]),
                b.lineTo(this.x, this.y),
                b.strokeStyle = "hsla(" + this.hue + ",100%," + this.brightness + "%," + this.alpha + ")",
                b.stroke()
            },
            a
        } (),
        c = function() {
            function a(a) {
                var b, c, d, e, g, h;
                for (b = a.sx, c = a.sy, d = a.tx, e = a.ty, this.Fireworks = a.Fireworks, this.x = b, this.y = c, this.sx = b, this.sy = c, this.tx = d, this.ty = e, this.distanceToTarget = f(b, c, d, e), this.distanceTraveled = 0, this.coordinates = [], this.coordinateCount = 3; this.coordinateCount--;) this.coordinates.push([this.x, this.y]);
                this.angle = Math.atan2(e - c, d - b),
                this.speed = null != (g = a.speed) ? g: 2,
                this.acceleration = null != (h = a.acceleration) ? h: 1.05,
                this.brightness = j(50, 70),
                this.targetRadius = 1
            }
            return a.prototype.update = function(a) {
                var b, c, d;
                return this.coordinates.pop(),
                this.coordinates.unshift([this.x, this.y]),
                this.targetRadius < 8 ? this.targetRadius += .3 : this.targetRadius = 1,
                this.speed *= this.acceleration,
                c = Math.cos(this.angle) * this.speed,
                d = Math.sin(this.angle) * this.speed,
                this.distanceTraveled = f(this.sx, this.sy, this.x + c, this.y + d),
                this.distanceTraveled >= this.distanceToTarget ? (b = this.Fireworks.fireworks, this.Fireworks.createParticles({
                    x: this.tx,
                    y: this.ty
                }), b.splice(a, 1)) : (this.x += c, this.y += d)
            },
            a.prototype.draw = function() {
                var a, b, c;
                return b = this.Fireworks.ctx,
                c = this.Fireworks.hue,
                b.beginPath(),
                a = this.coordinates[this.coordinates.length - 1],
                b.moveTo(a[0], a[1]),
                b.lineTo(this.x, this.y),
                b.strokeStyle = "hsl(" + c + ", 100%," + this.brightness + "%)",
                b.stroke(),
                b.beginPath(),
                b.arc(this.tx, this.ty, this.targetRadius, 0, 2 * Math.PI),
                b.stroke()
            },
            a
        } (),
        d = function() {
            function a(a) {
                var b, c, d, e, f;
                this.source = a.source,
                this.fireworks = [],
                this.particles = [],
                this.maxWorkSize = null != (b = a.maxWorkSize) ? b: 8,
                this.hue = null != (c = a.hue) ? c: 120,
                this.limiterTotal = null != (d = a.limiterTotal) ? d: 5,
                this.limiterTick = 0,
                this.maxTimerTotal = null != (e = a.maxTimerTotal) ? e: 40,
                this.minTimerTotal = null != (f = a.minTimerTotal) ? f: 20,
                this.initTimerTotal(),
                this.mousedown = !1,
                this.mx = void 0,
                this.my = void 0,
                this.playing = !1,
                this.initCtx(),
                this.initCtxEvent()
            }
            return a.prototype.initCtx = function() {
                var a, b, c;
                return b = $(this.source),
                a = b.parent(),
                c = document.createElement("canvas"),
                $(c).attr("style", this.getPositionStyle()),
                c.width = $(this.source).width(),
                c.height = $(this.source).height(),
                this.cw = c.width,
                this.ch = c.height,
                this.canvas = c,
                this.ctx = c.getContext("2d"),
                a.append(c)
            },
            a.prototype.initCtxEvent = function() {
                var a, b;
                return b = this,
                a = $(this.canvas),
                a.on("touchmove",
                function(a) {
                    var c;
                    return c = b.getEventPosition(a),
                    b.mx = c.x,
                    b.my = c.y
                }),
                a.on("touchstart",
                function(a) {
                    var c;
                    return b.mousedown = !0,
                    c = b.getEventPosition(a),
                    b.mx = c.x,
                    b.my = c.y
                }),
                a.on("touchend",
                function() {
                    return b.mousedown = !1
                })
            },
            a.prototype.getEventPosition = function(a) {
                var b, c, d, e, f, g, h;
                return d = {
                    x: 0,
                    y: 0
                },
                "touchstart" === a.type || "touchmove" === a.type || "touchend" === a.type || "touchcancel" === a.type ? (c = null != (g = a.originalEvent) ? g: a, f = null != (h = c.touches) ? h: c.changedTouches, f && f[0] && (e = f[0], d.x = e.pageX, d.y = e.pageY)) : ("mousedown" === a.type || "mouseup" === a.type || "mousemove" === a.type || "mouseover" === a.type || "mouseout" === a.type || "mouseenter" === a.type || "mouseleave" === a.type) && (d.x = a.pageX, d.y = a.pageY),
                b = $(this.canvas).offset(),
                d.x -= b.left,
                d.y -= b.top,
                d
            },
            a.prototype.initTimerTotal = function() {
                return this.timerTotal = this.maxTimerTotal,
                this.timerTick = 0
            },
            a.prototype.getPositionStyle = function() {
                var a;
                return a = $(this.source),
                "position:absolute;" + a.attr("style")
            },
            a.prototype.createParticles = function(a) {
                var b, c, d;
                for (c = 30, a.Fireworks = this, a.hue = this.hue, d = []; c--;) b = new e(a),
                d.push(this.particles.push(b));
                return d
            },
            a.prototype.createFireWork = function(a) {
                var b;
                return this.fireworks.length < this.maxWorkSize ? (a.Fireworks = this, b = new c(a), this.fireworks.push(b)) : void 0
            },
            a.prototype.play = function() {
                var a, b, c, d, e, f, g, h;
                if (d = this, this.playing === !0) {
                    for (requestAnimFrame(function() {
                        return d.play()
                    }), b = this.ctx, c = this.cw, a = this.ch, e = this.fireworks, g = this.particles, this.hue += .5, b.globalCompositeOperation = "destination-out", b.fillStyle = "rgba(0, 0, 0, 0.5)", b.fillRect(0, 0, c, a), b.globalCompositeOperation = "lighter", f = e.length; f--;) e[f].draw(),
                    e[f].update(f);
                    for (f = g.length; f--;) g[f].draw(),
                    g[f].update(f);
                    return this.timerTick >= this.timerTotal ? this.mousedown !== !0 && (h = j(0, c), this.createFireWork({
                        sx: h,
                        sy: a,
                        tx: h,
                        ty: j(0, a / 2)
                    }), this.initTimerTotal()) : this.timerTick++,
                    this.limiterTick >= this.limiterTotal ? this.mousedown === !0 ? (this.createFireWork({
                        sx: this.mx,
                        sy: a,
                        tx: this.mx,
                        ty: this.my
                    }), this.limiterTick = 0) : void 0 : this.limiterTick++
                }
            },
            a.prototype.start = function() {
                return this.playing !== !0 ? ($(this.canvas).css("display", "block"), this.playing = !0, this.play()) : void 0
            },
            a.prototype.stop = function() {
                return this.playing === !0 ? ($(this.canvas).css("display", "none"), this.playing = !1) : void 0
            },
            a
        } (),
        h = {},
        h.start = function(a) {
            var b;
            return $("div.simulator").length <= 0 && (b = a[0], b && !b.fireWorksEffect && (b.fireWorksEffect = new d({
                source: b
            })), b && b.fireWorksEffect) ? b.fireWorksEffect.start() : void 0
        },
        h.ready = function(a) {
            var b;
            return $("div.simulator").length <= 0 && (b = a[0], b && b.fireWorksEffect) ? b.fireWorksEffect.stop() : void 0
        },
        b.exports = h
    },
    {}],
    12 : [function(a, b) {
        var c, d;
        c = window.RP.Css,
        d = {},
        d.start = function(a, b) {
            return c.enableAnimation(a[0], b),
            c.css(a[0], {
                "-webkit-animation": "flashing " + b + "s"
            }),
            c.regKeyFrames("flashing", "{ 0% {background: red;opacity:1} 50% {background:yellow;opacity:1} 100% {background:blue;opacity:1} }"),
            setTimeout(function() {
                return c.disableAnimation(a[0])
            },
            1e3 * b)
        },
        d.ready = function(a) {
            var b, d;
            return d = a.attr("data-style-cache"),
            d ? b = JSON.parse(d) : (b = {
                opacity: a.css("opacity"),
                zIndex: a.css("z-index")
            },
            a.attr("data-style-cache", JSON.stringify(b))),
            c.rmKeyFrames(a[0])
        },
        b.exports = d
    },
    {}],
    13 : [function(a, b) {
        var c, d;
        c = window.RP.Css,
        d = {},
        d.start = function(a, b) {
            return c.enableAnimation(a[0], b, "ease"),
            c.css(a[0], JSON.parse(a.attr("data-style-cache"))),
            setTimeout(function() {
                return c.disableAnimation(a[0])
            },
            1e3 * b)
        },
        d.ready = function(a) {
            var b, d;
            return d = a.attr("data-style-cache"),
            d ? b = JSON.parse(d) : (b = {
                opacity: a.css("opacity"),
                zIndex: a.css("z-index"),
                y: c.getY(a[0]),
                x: c.getX(a[0])
            },
            a.attr("data-style-cache", JSON.stringify(b))),
            c.css(a[0], {
                opacity: 0,
                y: b.y + 200
            })
        },
        b.exports = d
    },
    {}],
    14 : [function(a, b) {
        var c, d;
        c = window.RP.Css,
        d = {},
        d.start = function(a, b) {
            return c.enableAnimation(a[0], b, "ease", "backwards"),
            c.css(a[0], JSON.parse(a.attr("data-style-cache"))),
            setTimeout(function() {
                return c.disableAnimation(a[0])
            },
            1e3 * b)
        },
        d.ready = function(a) {
            var b, d;
            return d = a.attr("data-style-cache"),
            d ? b = JSON.parse(d) : (b = {
                opacity: a.css("opacity"),
                zIndex: a.css("z-index"),
                x: c.getX(a[0]),
                y: c.getY(a[0])
            },
            a.attr("data-style-cache", JSON.stringify(b))),
            c.css(a[0], {
                opacity: 0,
                x: b.x - 200
            })
        },
        b.exports = d
    },
    {}],
    15 : [function(a, b) {
        var c, d;
        c = window.RP.Css,
        d = {},
        d.start = function(a, b) {
            return c.enableAnimation(a[0], b, "ease"),
            c.css(a[0], JSON.parse(a.attr("data-style-cache"))),
            setTimeout(function() {
                return c.disableAnimation(a[0])
            },
            1e3 * b)
        },
        d.ready = function(a) {
            var b, d;
            return d = a.attr("data-style-cache"),
            d ? b = JSON.parse(d) : (b = {
                opacity: a.css("opacity"),
                zIndex: a.css("z-index"),
                x: c.getX(a[0]),
                y: c.getY(a[0])
            },
            a.attr("data-style-cache", JSON.stringify(b))),
            c.css(a[0], {
                opacity: 0,
                x: b.x + 200
            })
        },
        b.exports = d
    },
    {}],
    16 : [function(a, b) {
        var c, d;
        c = window.RP.Css,
        d = {},
        d.start = function(a, b) {
            return c.enableAnimation(a[0], b, "ease"),
            c.css(a[0], JSON.parse(a.attr("data-style-cache"))),
            setTimeout(function() {
                return c.disableAnimation(a[0])
            },
            1e3 * b)
        },
        d.ready = function(a) {
            var b, d;
            return d = a.attr("data-style-cache"),
            d ? b = JSON.parse(d) : (b = {
                opacity: a.css("opacity"),
                zIndex: a.css("z-index"),
                y: c.getY(a[0]),
                x: c.getX(a[0])
            },
            a.attr("data-style-cache", JSON.stringify(b))),
            c.css(a[0], {
                opacity: 0,
                y: b.y - 200
            })
        },
        b.exports = d
    },
    {}],
    17 : [function(a, b) {
        var c, d, e, f;
        e = window.RP.Css,
        f = {},
        c = 0,
        d = {},
        f.getAnimationName = function(a, b, e, f) {
            var g, h;
            return null == a && (a = 1),
            null == b && (b = 1),
            g = "scaleX-" + a + "-scaleY" + b + "-translateX" + e + "-translateY" + f,
            h = d[g],
            h || (h = d[g] = "jitter" + c++),
            h
        },
        f.start = function(a, b) {
            var c, d, f, g, h, i, j, k, l, m, n;
            return e.disableAnimation(a[0]),
            h = JSON.parse(a.attr("data-style-cache")),
            e.css(a[0], h),
            f = null != (k = h.scaleX) ? k: 1,
            g = null != (l = h.scaleY) ? l: 1,
            i = null != (m = h.x) ? m: e.getX(a[0]),
            j = null != (n = h.y) ? n: e.getY(a[0]),
            c = this.getAnimationName(f, g, i, j),
            d = "scaleX(" + f + ") scaleY(" + g + ")",
            e.css(a[0], {
                "-webkit-animation": c + " " + b + "s backwards ",
                animation: c + " " + b + "s backwards "
            }),
            e.regKeyFrames(c, "{ 0% { -webkit-transform: translate(" + (0 + i) + "px, " + (0 + j) + "px) rotate(0deg) " + d + "; transform: translate(" + (0 + i) + "px, " + (0 + j) + "px) rotate(0deg) " + d + ";} 2% { -webkit-transform: translate(" + ( - 1 + i) + "px, " + (3 + j) + "px) rotate(-1.5deg) " + d + "; transform: translate(" + ( - 1 + i) + "px, " + (3 + j) + "px) rotate(-1.5deg) " + d + "; } 4% { -webkit-transform: translate(" + ( - 4 + i) + "px, " + (5 + j) + "px) rotate(-1.5deg) " + d + "; transform: translate(" + ( - 4 + i) + "px, " + (5 + j) + "px) rotate(-1.5deg) " + d + "; } 6% { -webkit-transform: translate(" + ( - 1 + i) + "px, " + (6 + j) + "px) rotate(-0.5deg) " + d + "; transform: translate(" + ( - 1 + i) + "px, " + (6 + j) + "px) rotate(-0.5deg) " + d + "; } 8% { -webkit-transform: translate(" + (5 + i) + "px, " + ( - 4 + j) + "px) rotate(-3.5deg) " + d + "; transform: translate(" + (5 + i) + "px, " + ( - 4 + j) + "px) rotate(-3.5deg) " + d + "; } 10% { -webkit-transform: translate(" + ( - 7 + i) + "px, " + ( - 3 + j) + "px) rotate(-3.5deg) " + d + "; transform: translate(" + ( - 7 + i) + "px, " + ( - 3 + j) + "px) rotate(-3.5deg) " + d + "; } 12% { -webkit-transform: translate(" + ( - 1 + i) + "px, " + (8 + j) + "px) rotate(2.5deg) " + d + "; transform: translate(" + ( - 1 + i) + "px, " + (8 + j) + "px) rotate(2.5deg) " + d + "; } 14% { -webkit-transform: translate(" + (3 + i) + "px, " + ( - 5 + j) + "px) rotate(-1.5deg) " + d + "; transform: translate(" + (3 + i) + "px, " + ( - 5 + j) + "px) rotate(-1.5deg) " + d + "; } 16% { -webkit-transform: translate(" + (1 + i) + "px, " + (0 + j) + "px) rotate(2.5deg) " + d + "; transform: translate(" + (1 + i) + "px, " + (0 + j) + "px) rotate(2.5deg) " + d + "; } 18% { -webkit-transform: translate(" + ( - 6 + i) + "px, " + ( - 10 + j) + "px) rotate(-0.5deg) " + d + "; transform: translate(" + ( - 6 + i) + "px, " + ( - 10 + j) + "px) rotate(-0.5deg) " + d + "; } 20% { -webkit-transform: translate(" + (3 + i) + "px, " + ( - 2 + j) + "px) rotate(1.5deg) " + d + "; transform: translate(" + (3 + i) + "px, " + ( - 2 + j) + "px) rotate(1.5deg) " + d + "; } 22% { -webkit-transform: translate(" + (0 + i) + "px, " + (0 + j) + "px) rotate(-2.5deg) " + d + "; transform: translate(" + (0 + i) + "px, " + (0 + j) + "px) rotate(-2.5deg) " + d + "; } 24% { -webkit-transform: translate(" + ( - 5 + i) + "px, " + ( - 4 + j) + "px) rotate(1.5deg) " + d + "; transform: translate(" + ( - 5 + i) + "px, " + ( - 4 + j) + "px) rotate(1.5deg) " + d + "; } 26% { -webkit-transform: translate(" + ( - 1 + i) + "px, " + (3 + j) + "px) rotate(-3.5deg) " + d + "; transform: translate(" + ( - 1 + i) + "px, " + (3 + j) + "px) rotate(-3.5deg) " + d + "; } 28% { -webkit-transform: translate(" + (1 + i) + "px, " + (1 + j) + "px) rotate(-3.5deg) " + d + "; transform: translate(" + (1 + i) + "px, " + (1 + j) + "px) rotate(-3.5deg) " + d + "; } 30% { -webkit-transform: translate(" + ( - 4 + i) + "px, " + (8 + j) + "px) rotate(1.5deg) " + d + "; transform: translate(" + ( - 4 + i) + "px, " + (8 + j) + "px) rotate(1.5deg) " + d + "; } 32% { -webkit-transform: translate(" + ( - 9 + i) + "px, " + (7 + j) + "px) rotate(-3.5deg) " + d + "; transform: translate(" + ( - 9 + i) + "px, " + (7 + j) + "px) rotate(-3.5deg) " + d + "; } 34% { -webkit-transform: translate(" + (4 + i) + "px, " + ( - 9 + j) + "px) rotate(-2.5deg) " + d + "; transform: translate(" + (4 + i) + "px, " + ( - 9 + j) + "px) rotate(-2.5deg) " + d + "; } 36% { -webkit-transform: translate(" + (1 + i) + "px, " + ( - 6 + j) + "px) rotate(-2.5deg) " + d + "; transform: translate(" + (1 + i) + "px, " + ( - 6 + j) + "px) rotate(-2.5deg) " + d + "; } 38% { -webkit-transform: translate(" + ( - 4 + i) + "px, " + (0 + j) + "px) rotate(-2.5deg) " + d + "; transform: translate(" + ( - 4 + i) + "px, " + (0 + j) + "px) rotate(-2.5deg) " + d + "; } 40% { -webkit-transform: translate(" + (3 + i) + "px, " + ( - 7 + j) + "px) rotate(0.5deg) " + d + "; transform: translate(" + (3 + i) + "px, " + ( - 7 + j) + "px) rotate(0.5deg) " + d + "; } 42% { -webkit-transform: translate(" + (4 + i) + "px, " + (4 + j) + "px) rotate(-0.5deg) " + d + "; transform: translate(" + (4 + i) + "px, " + (4 + j) + "px) rotate(-0.5deg) " + d + "; } 44% { -webkit-transform: translate(" + (8 + i) + "px, " + ( - 4 + j) + "px) rotate(-2.5deg) " + d + "; transform: translate(" + (8 + i) + "px, " + ( - 4 + j) + "px) rotate(-2.5deg) " + d + "; } 46% { -webkit-transform: translate(" + (9 + i) + "px, " + (9 + j) + "px) rotate(-3.5deg) " + d + "; transform: translate(" + (9 + i) + "px, " + (9 + j) + "px) rotate(-3.5deg) " + d + "; } 48% { -webkit-transform: translate(" + (6 + i) + "px, " + ( - 8 + j) + "px) rotate(-0.5deg) " + d + "; transform: translate(" + (6 + i) + "px, " + ( - 8 + j) + "px) rotate(-0.5deg) " + d + "; } 50% { -webkit-transform: translate(" + ( - 1 + i) + "px, " + (4 + j) + "px) rotate(-3.5deg) " + d + "; transform: translate(" + ( - 1 + i) + "px, " + (4 + j) + "px) rotate(-3.5deg) " + d + "; } 52% { -webkit-transform: translate(" + (4 + i) + "px, " + (6 + j) + "px) rotate(-1.5deg) " + d + "; transform: translate(" + (4 + i) + "px, " + (6 + j) + "px) rotate(-1.5deg) " + d + "; } 54% { -webkit-transform: translate(" + (9 + i) + "px, " + ( - 3 + j) + "px) rotate(2.5deg) " + d + "; transform: translate(" + (9 + i) + "px, " + ( - 3 + j) + "px) rotate(2.5deg) " + d + "; } 56% { -webkit-transform: translate(" + (8 + i) + "px, " + ( - 2 + j) + "px) rotate(-3.5deg) " + d + "; transform: translate(" + (8 + i) + "px, " + ( - 2 + j) + "px) rotate(-3.5deg) " + d + "; } 58% { -webkit-transform: translate(" + ( - 2 + i) + "px, " + ( - 9 + j) + "px) rotate(-0.5deg) " + d + "; transform: translate(" + ( - 2 + i) + "px, " + ( - 9 + j) + "px) rotate(-0.5deg) " + d + "; } 60% { -webkit-transform: translate(" + ( - 1 + i) + "px, " + ( - 5 + j) + "px) rotate(2.5deg) " + d + "; transform: translate(" + ( - 1 + i) + "px, " + ( - 5 + j) + "px) rotate(2.5deg) " + d + "; } 62% { -webkit-transform: translate(" + ( - 8 + i) + "px, " + (3 + j) + "px) rotate(2.5deg) " + d + "; transform: translate(" + ( - 8 + i) + "px, " + (3 + j) + "px) rotate(2.5deg) " + d + "; } 64% { -webkit-transform: translate(" + (6 + i) + "px, " + ( - 2 + j) + "px) rotate(-3.5deg) " + d + "; transform: translate(" + (6 + i) + "px, " + ( - 2 + j) + "px) rotate(-3.5deg) " + d + "; } 66% { -webkit-transform: translate(" + ( - 5 + i) + "px, " + (9 + j) + "px) rotate(-1.5deg) " + d + "; transform: translate(" + ( - 5 + i) + "px, " + (9 + j) + "px) rotate(-1.5deg) " + d + "; } 68% { -webkit-transform: translate(" + (3 + i) + "px, " + (1 + j) + "px) rotate(-0.5deg) " + d + "; transform: translate(" + (3 + i) + "px, " + (1 + j) + "px) rotate(-0.5deg) " + d + "; } 70% { -webkit-transform: translate(" + (6 + i) + "px, " + (4 + j) + "px) rotate(-1.5deg) " + d + "; transform: translate(" + (6 + i) + "px, " + (4 + j) + "px) rotate(-1.5deg) " + d + "; } 72% { -webkit-transform: translate(" + ( - 6 + i) + "px, " + ( - 5 + j) + "px) rotate(1.5deg) " + d + "; transform: translate(" + ( - 6 + i) + "px, " + ( - 5 + j) + "px) rotate(1.5deg) " + d + "; } 74% { -webkit-transform: translate(" + ( - 8 + i) + "px, " + (0 + j) + "px) rotate(-0.5deg) " + d + "; transform: translate(" + ( - 8 + i) + "px, " + (0 + j) + "px) rotate(-0.5deg) " + d + "; } 76% { -webkit-transform: translate(" + ( - 5 + i) + "px, " + ( - 8 + j) + "px) rotate(1.5deg) " + d + "; transform: translate(" + ( - 5 + i) + "px, " + ( - 8 + j) + "px) rotate(1.5deg) " + d + "; } 78% { -webkit-transform: translate(" + (5 + i) + "px, " + ( - 3 + j) + "px) rotate(-1.5deg) " + d + "; transform: translate(" + (5 + i) + "px, " + ( - 3 + j) + "px) rotate(-1.5deg) " + d + "; } 80% { -webkit-transform: translate(" + ( - 6 + i) + "px, " + ( - 3 + j) + "px) rotate(-1.5deg) " + d + "; transform: translate(" + ( - 6 + i) + "px, " + ( - 3 + j) + "px) rotate(-1.5deg) " + d + "; } 82% { -webkit-transform: translate(" + (7 + i) + "px, " + (8 + j) + "px) rotate(-1.5deg) " + d + "; transform: translate(" + (7 + i) + "px, " + (8 + j) + "px) rotate(-1.5deg) " + d + "; } 84% { -webkit-transform: translate(" + ( - 6 + i) + "px, " + (9 + j) + "px) rotate(0.5deg) " + d + "; transform: translate(" + ( - 6 + i) + "px, " + (9 + j) + "px) rotate(0.5deg) " + d + "; } 86% { -webkit-transform: translate(" + (1 + i) + "px, " + (8 + j) + "px) rotate(-3.5deg) " + d + "; transform: translate(" + (1 + i) + "px, " + (8 + j) + "px) rotate(-3.5deg) " + d + "; } 88% { -webkit-transform: translate(" + ( - 9 + i) + "px, " + ( - 2 + j) + "px) rotate(1.5deg) " + d + "; transform: translate(" + ( - 9 + i) + "px, " + ( - 2 + j) + "px) rotate(1.5deg) " + d + "; } 90% { -webkit-transform: translate(" + (4 + i) + "px, " + ( - 6 + j) + "px) rotate(-1.5deg) " + d + "; transform: translate(" + (4 + i) + "px, " + ( - 6 + j) + "px) rotate(-1.5deg) " + d + "; } 92% { -webkit-transform: translate(" + (0 + i) + "px, " + ( - 1 + j) + "px) rotate(0.5deg) " + d + "; transform: translate(" + (0 + i) + "px, " + ( - 1 + j) + "px) rotate(0.5deg) " + d + "; } 94% { -webkit-transform: translate(" + (2 + i) + "px, " + ( - 9 + j) + "px) rotate(2.5deg) " + d + "; transform: translate(" + (2 + i) + "px, " + ( - 9 + j) + "px) rotate(2.5deg) " + d + "; } 96% { -webkit-transform: translate(" + ( - 9 + i) + "px, " + (1 + j) + "px) rotate(-2.5deg) " + d + "; transform: translate(" + ( - 9 + i) + "px, " + (1 + j) + "px) rotate(-2.5deg) " + d + "; } 98% { -webkit-transform: translate(" + ( - 9 + i) + "px, " + ( - 5 + j) + "px) rotate(-3.5deg) " + d + "; transform: translate(" + ( - 9 + i) + "px, " + ( - 5 + j) + "px) rotate(-3.5deg) " + d + ";} }")
        },
        f.ready = function(a) {
            var b, c;
            return c = a.attr("data-style-cache"),
            c ? b = JSON.parse(c) : (b = {
                opacity: a.css("opacity"),
                zIndex: a.css("z-index"),
                scaleX: e.getScaleX(a[0]),
                scaleY: e.getScaleY(a[0]),
                y: e.getY(a[0]),
                x: e.getX(a[0])
            },
            a.attr("data-style-cache", JSON.stringify(b))),
            e.css(a[0], {
                opacity: 0
            }),
            e.rmKeyFrames(a[0])
        },
        b.exports = f
    },
    {}],
    18 : [function(a, b) {
        var c, d;
        c = window.RP.Css,
        d = {},
        d.start = function(a, b) {
            var d;
            return c.disableAnimation(a[0]),
            d = JSON.parse(a.attr("data-style-cache")),
            c.css(a[0], d),
            c.css(a[0], {
                "-webkit-animation": "light " + b + "s ease-in-out infinite both"
            }),
            c.regKeyFrames("light", "{ 0% {opacity: 0;} 50% {opacity: 1;} 100%{opacity: 0;} }")
        },
        d.ready = function(a) {
            var b, d;
            return d = a.attr("data-style-cache"),
            d ? b = JSON.parse(d) : (b = {
                zIndex: a.css("z-index"),
                y: c.getY(a[0]),
                x: c.getX(a[0])
            },
            a.attr("data-style-cache", JSON.stringify(b))),
            c.css(a[0], {
                opacity: 0
            }),
            c.rmKeyFrames(a[0])
        },
        b.exports = d
    },
    {}],
    19 : [function(a, b) {
        var c, d, e, f;
        c = a("./rainyday.js"),
        d = window.RP.Css,
        e = {},
        f = function(a) {
            var b, e, f, g;
            return b = a.parent(),
            f = a.css("border-width"),
            e = a.css("border-radius"),
            g = new c({
                top: a[0].style.top,
                left: a[0].style.left,
                webkitTransform: a[0].style.webkitTransform,
                zIndex: a[0].style.zIndex,
                image: a[0],
                parentElement: b[0]
            }),
            g.gravity = g.GRAVITY_NON_LINEAR,
            g.trail = g.TRAIL_DROPS,
            d.css(g.canvas, {
                opacity: 0,
                "border-width": f,
                "border-radius": e,
                "border-style": "solid",
                "border-color": "transparent"
            }),
            setTimeout(function() {
                return d.enableAnimation(g.canvas, 1.5, "linear"),
                d.css(g.canvas, {
                    opacity: 1
                })
            },
            0),
            g.rain([[0, 2, 100]]),
            g.rain([[0, 2, .5], [1, 5, 1]], 60)
        },
        e.start = function(a) {
            var b, c, e, g;
            return $("div.simulator").length <= 0 && (b = {},
            b.bg = a.attr("src"), b.bg && a.hasClass("image") && ( - 1 !== b.bg.indexOf("http://") && (g = b.bg.substring(7), c = g.indexOf("/"), e = "http://" + g.substring(0, c + 1), a[0].crossOrigin = e), a[0].onload = function() {
                return f(a),
                a[0].style.zIndex = parseInt(a[0].style.zIndex) - 200
            },
            a.attr("src", b.bg + "?_=" + (new Date).getTime()))),
            d.css(a[0], JSON.parse(a.attr("data-style-cache")))
        },
        e.ready = function(a) {
            var b, c;
            return c = a.attr("data-style-cache"),
            c ? b = JSON.parse(c) : (b = {
                opacity: a.css("opacity"),
                zIndex: a.css("z-index"),
                y: d.getY(a[0]),
                x: d.getX(a[0])
            },
            a.attr("data-style-cache", JSON.stringify(b)))
        },
        b.exports = e
    },
    {
        "./rainyday.js": 20
    }],
    20 : [function(a, b) {
        function c(a, b) {
            if (this === window) return new c(a, b);
            this.img = a.image;
            var d = {
                opacity: 1,
                blur: 10,
                crop: [0, 0, this.img.naturalWidth, this.img.naturalHeight],
                enableSizeChange: !0,
                parentElement: document.getElementsByTagName("body")[0],
                fps: 30,
                fillStyle: "#8ED6FF",
                enableCollisions: !0,
                gravityThreshold: 3,
                gravityAngle: Math.PI / 2,
                gravityAngleVariance: 0,
                reflectionScaledownFactor: 5,
                reflectionDropMappingWidth: 200,
                reflectionDropMappingHeight: 200,
                width: this.img.clientWidth,
                height: this.img.clientHeight,
                position: "absolute",
                top: 0,
                left: 0
            };
            for (var e in d)"undefined" == typeof a[e] && (a[e] = d[e]);
            this.options = a,
            this.drops = [],
            this.canvas = b || this.prepareCanvas(),
            this.prepareBackground(),
            this.prepareGlass(),
            this.reflection = this.REFLECTION_MINIATURE,
            this.trail = this.TRAIL_DROPS,
            this.gravity = this.GRAVITY_NON_LINEAR,
            this.collision = this.COLLISION_SIMPLE,
            this.setRequestAnimFrame()
        }
        function d(a, b, c, d, e) {
            this.x = Math.floor(b),
            this.y = Math.floor(c),
            this.r = Math.random() * e + d,
            this.rainyday = a,
            this.context = a.context,
            this.reflection = a.reflected
        }
        function e() {
            this.r = 0,
            this.g = 0,
            this.b = 0,
            this.next = null
        }
        function f(a, b, c) {
            this.resolution = c,
            this.xc = a,
            this.yc = b,
            this.matrix = new Array(a);
            for (var d = 0; a + 5 >= d; d++) {
                this.matrix[d] = new Array(b);
                for (var e = 0; b + 5 >= e; ++e) this.matrix[d][e] = new g(null)
            }
        }
        function g(a) {
            this.drop = a,
            this.next = null
        }
        c.prototype.prepareCanvas = function() {
            var a = document.createElement("canvas");
            return a.style.position = this.options.position,
            a.style.top = this.options.top,
            a.style.left = this.options.left,
            a.style.zIndex = this.options.zIndex,
            a.style.webkitTransform = this.options.webkitTransform,
            a.width = this.options.width,
            a.height = this.options.height,
            this.options.parentElement.appendChild(a),
            this.options.enableSizeChange && this.setResizeHandler(),
            a
        },
        c.prototype.setResizeHandler = function() {
            null !== window.onresize ? window.setInterval(this.checkSize.bind(this), 100) : (window.onresize = this.checkSize.bind(this), window.onorientationchange = this.checkSize.bind(this))
        },
        c.prototype.checkSize = function() {
            var a = this.img.clientWidth,
            b = this.img.clientHeight,
            c = this.img.offsetLeft,
            d = this.img.offsetTop,
            e = this.canvas.width,
            f = this.canvas.height,
            g = this.canvas.offsetLeft,
            h = this.canvas.offsetTop; (e !== a || f !== b) && (this.canvas.width = a, this.canvas.height = b, this.prepareBackground(), this.glass.width = this.canvas.width, this.glass.height = this.canvas.height, this.prepareReflections()),
            (g !== c || h !== d) && (this.canvas.offsetLeft = c, this.canvas.offsetTop = d)
        },
        c.prototype.animateDrops = function() {
            this.addDropCallback && this.addDropCallback();
            for (var a = this.drops.slice(), b = [], c = 0; c < a.length; ++c) a[c].animate() && b.push(a[c]);
            this.drops = b,
            window.requestAnimFrame(this.animateDrops.bind(this))
        },
        c.prototype.setRequestAnimFrame = function() {
            var a = this.options.fps;
            window.requestAnimFrame = function() {
                return window.requestAnimationFrame || window.webkitRequestAnimationFrame || window.mozRequestAnimationFrame ||
                function(b) {
                    window.setTimeout(b, 1e3 / a)
                }
            } ()
        },
        c.prototype.prepareReflections = function() {
            this.reflected = document.createElement("canvas"),
            this.reflected.width = this.canvas.width / this.options.reflectionScaledownFactor,
            this.reflected.height = this.canvas.height / this.options.reflectionScaledownFactor;
            var a = this.reflected.getContext("2d");
            a.drawImage(this.img, this.options.crop[0], this.options.crop[1], this.options.crop[2], this.options.crop[3], 0, 0, this.reflected.width, this.reflected.height)
        },
        c.prototype.prepareGlass = function() {
            this.glass = document.createElement("canvas"),
            this.glass.width = this.canvas.width,
            this.glass.height = this.canvas.height,
            this.context = this.glass.getContext("2d")
        },
        c.prototype.rain = function(a, b) {
            if (this.reflection !== this.REFLECTION_NONE && this.prepareReflections(), this.animateDrops(), this.presets = a, this.PRIVATE_GRAVITY_FORCE_FACTOR_Y = .001 * this.options.fps / 25, this.PRIVATE_GRAVITY_FORCE_FACTOR_X = .001 * (Math.PI / 2 - this.options.gravityAngle) * this.options.fps / 50, this.options.enableCollisions) {
                for (var c = 0,
                e = 0; e < a.length; e++) a[e][0] + a[e][1] > c && (c = Math.floor(a[e][0] + a[e][1]));
                if (c > 0) {
                    var g = Math.ceil(this.canvas.width / c),
                    h = Math.ceil(this.canvas.height / c);
                    this.matrix = new f(g, h, c)
                } else this.options.enableCollisions = !1
            }
            for (var e = 0; e < a.length; e++) a[e][3] || (a[e][3] = -1);
            var i = 0;
            this.addDropCallback = function() {
                var c = (new Date).getTime();
                if (! (b > c - i)) {
                    i = c;
                    var e = this.canvas.getContext("2d");
                    e.clearRect(0, 0, this.canvas.width, this.canvas.height),
                    e.drawImage(this.background, 0, 0, this.canvas.width, this.canvas.height);
                    for (var f, g = 0; g < a.length; g++) if (a[g][2] > 1 || -1 === a[g][3]) {
                        if (0 !== a[g][3]) {
                            a[g][3]--;
                            for (var h = 0; h < a[g][2]; ++h) this.putDrop(new d(this, Math.random() * this.canvas.width, Math.random() * this.canvas.height, a[g][0], a[g][1]))
                        }
                    } else if (Math.random() < a[g][2]) {
                        f = a[g];
                        break
                    }
                    f && this.putDrop(new d(this, Math.random() * this.canvas.width, Math.random() * this.canvas.height, f[0], f[1])),
                    e.save(),
                    e.globalAlpha = this.options.opacity,
                    e.drawImage(this.glass, 0, 0, this.canvas.width, this.canvas.height),
                    e.restore()
                }
            }.bind(this)
        },
        c.prototype.putDrop = function(a) {
            a.draw(),
            this.gravity && a.r > this.options.gravityThreshold && (this.options.enableCollisions && this.matrix.update(a), this.drops.push(a))
        },
        c.prototype.clearDrop = function(a, b) {
            var c = a.clear(b);
            if (c) {
                var d = this.drops.indexOf(a);
                d >= 0 && this.drops.splice(d, 1)
            }
            return c
        },
        d.prototype.draw = function() {
            this.context.save(),
            this.context.beginPath();
            var a = this.r;
            if (this.r = .95 * this.r, this.r < 3) this.context.arc(this.x, this.y, this.r, 0, 2 * Math.PI, !0),
            this.context.closePath();
            else if (this.colliding || this.yspeed > 2) {
                if (this.colliding) {
                    var b = this.colliding;
                    this.r = 1.001 * (this.r > b.r ? this.r: b.r),
                    this.x += b.x - this.x,
                    this.colliding = null
                }
                var c = 1 + .1 * this.yspeed;
                this.context.moveTo(this.x - this.r / c, this.y),
                this.context.bezierCurveTo(this.x - this.r, this.y - 2 * this.r, this.x + this.r, this.y - 2 * this.r, this.x + this.r / c, this.y),
                this.context.bezierCurveTo(this.x + this.r, this.y + c * this.r, this.x - this.r, this.y + c * this.r, this.x - this.r / c, this.y)
            } else this.context.arc(this.x, this.y, .9 * this.r, 0, 2 * Math.PI, !0),
            this.context.closePath();
            this.context.clip(),
            this.r = a,
            this.rainyday.reflection && this.rainyday.reflection(this),
            this.context.restore()
        },
        d.prototype.clear = function(a) {
            return this.context.clearRect(this.x - this.r - 1, this.y - this.r - 2, 2 * this.r + 2, 2 * this.r + 2),
            a ? (this.terminate = !0, !0) : this.y - this.r > this.rainyday.canvas.height || this.x - this.r > this.rainyday.canvas.width || this.x + this.r < 0 ? !0 : !1
        },
        d.prototype.animate = function() {
            if (this.terminate) return ! 1;
            var a = this.rainyday.gravity(this);
            if (!a && this.rainyday.trail && this.rainyday.trail(this), this.rainyday.options.enableCollisions) {
                var b = this.rainyday.matrix.update(this, a);
                b && this.rainyday.collision(this, b)
            }
            return ! a || this.terminate
        },
        c.prototype.TRAIL_NONE = function() {},
        c.prototype.TRAIL_DROPS = function(a) { (!a.trailY || a.y - a.trailY >= 100 * Math.random() * a.r) && (a.trailY = a.y, this.putDrop(new d(this, a.x + (2 * Math.random() - 1) * Math.random(), a.y - a.r - 5, Math.ceil(a.r / 5), 0)))
        },
        c.prototype.TRAIL_SMUDGE = function(a) {
            var b = a.y - a.r - 3,
            c = a.x - a.r / 2 + 2 * Math.random();
            0 > b || 0 > c || this.context.drawImage(this.clearbackground, c, b, a.r, 2, c, b, a.r, 2)
        },
        c.prototype.GRAVITY_NONE = function() {
            return ! 0
        },
        c.prototype.GRAVITY_LINEAR = function(a) {
            return this.clearDrop(a) ? !0 : (a.yspeed ? (a.yspeed += this.PRIVATE_GRAVITY_FORCE_FACTOR_Y * Math.floor(a.r), a.xspeed += this.PRIVATE_GRAVITY_FORCE_FACTOR_X * Math.floor(a.r)) : (a.yspeed = this.PRIVATE_GRAVITY_FORCE_FACTOR_Y, a.xspeed = this.PRIVATE_GRAVITY_FORCE_FACTOR_X), a.y += a.yspeed, a.draw(), !1)
        },
        c.prototype.GRAVITY_NON_LINEAR = function(a) {
            return this.clearDrop(a) ? !0 : (a.collided ? (a.collided = !1, a.seed = Math.floor(a.r * Math.random() * this.options.fps), a.skipping = !1, a.slowing = !1) : (!a.seed || a.seed < 0) && (a.seed = Math.floor(a.r * Math.random() * this.options.fps), a.skipping = a.skipping === !1 ? !0 : !1, a.slowing = !0), a.seed--, a.yspeed ? a.slowing ? (a.yspeed /= 1.1, a.xspeed /= 1.1, a.yspeed < this.PRIVATE_GRAVITY_FORCE_FACTOR_Y && (a.slowing = !1)) : a.skipping ? (a.yspeed = this.PRIVATE_GRAVITY_FORCE_FACTOR_Y, a.xspeed = this.PRIVATE_GRAVITY_FORCE_FACTOR_X) : (a.yspeed += 1 * this.PRIVATE_GRAVITY_FORCE_FACTOR_Y * Math.floor(a.r), a.xspeed += 1 * this.PRIVATE_GRAVITY_FORCE_FACTOR_X * Math.floor(a.r)) : (a.yspeed = this.PRIVATE_GRAVITY_FORCE_FACTOR_Y, a.xspeed = this.PRIVATE_GRAVITY_FORCE_FACTOR_X), 0 !== this.options.gravityAngleVariance && (a.xspeed += (2 * Math.random() - 1) * a.yspeed * this.options.gravityAngleVariance), a.y += a.yspeed, a.x += a.xspeed, a.draw(), !1)
        },
        c.prototype.positiveMin = function(a, b) {
            var c = 0;
            return c = b > a ? 0 >= a ? b: a: 0 >= b ? a: b,
            0 >= c ? 1 : c
        },
        c.prototype.REFLECTION_NONE = function() {
            this.context.fillStyle = this.options.fillStyle,
            this.context.fill()
        },
        c.prototype.REFLECTION_MINIATURE = function(a) {
            var b = Math.max((a.x - this.options.reflectionDropMappingWidth) / this.options.reflectionScaledownFactor, 0),
            c = Math.max((a.y - this.options.reflectionDropMappingHeight) / this.options.reflectionScaledownFactor, 0),
            d = this.positiveMin(2 * this.options.reflectionDropMappingWidth / this.options.reflectionScaledownFactor, this.reflected.width - b),
            e = this.positiveMin(2 * this.options.reflectionDropMappingHeight / this.options.reflectionScaledownFactor, this.reflected.height - c),
            f = Math.max(a.x - 1.1 * a.r, 0),
            g = Math.max(a.y - 1.1 * a.r, 0);
            this.context.drawImage(this.reflected, b, c, d, e, f, g, 2 * a.r, 2 * a.r)
        },
        c.prototype.COLLISION_SIMPLE = function(a, b) {
            for (var c, d = b; null != d;) {
                var e = d.drop;
                if (Math.sqrt(Math.pow(a.x - e.x, 2) + Math.pow(a.y - e.y, 2)) < a.r + e.r) {
                    c = e;
                    break
                }
                d = d.next
            }
            if (c) {
                var f, g;
                a.y > c.y ? (f = a, g = c) : (f = c, g = a),
                this.clearDrop(g),
                this.clearDrop(f, !0),
                this.matrix.remove(f),
                g.draw(),
                g.colliding = f,
                g.collided = !0
            }
        },
        c.prototype.prepareBackground = function() {
            this.background = document.createElement("canvas"),
            this.background.width = this.canvas.width,
            this.background.height = this.canvas.height,
            this.clearbackground = document.createElement("canvas"),
            this.clearbackground.width = this.canvas.width,
            this.clearbackground.height = this.canvas.height;
            var a = this.background.getContext("2d");
            a.clearRect(0, 0, this.canvas.width, this.canvas.height),
            a.drawImage(this.img, this.options.crop[0], this.options.crop[1], this.options.crop[2], this.options.crop[3], 0, 0, this.canvas.width, this.canvas.height),
            a = this.clearbackground.getContext("2d"),
            a.clearRect(0, 0, this.canvas.width, this.canvas.height),
            a.drawImage(this.img, this.options.crop[0], this.options.crop[1], this.options.crop[2], this.options.crop[3], 0, 0, this.canvas.width, this.canvas.height),
            !isNaN(this.options.blur) && this.options.blur >= 1 && this.stackBlurCanvasRGB(this.canvas.width, this.canvas.height, this.options.blur)
        },
        c.prototype.stackBlurCanvasRGB = function(a, b, c) {
            var d = [[0, 9], [1, 11], [2, 12], [3, 13], [5, 14], [7, 15], [11, 16], [15, 17], [22, 18], [31, 19], [45, 20], [63, 21], [90, 22], [127, 23], [181, 24]],
            f = [512, 512, 456, 512, 328, 456, 335, 512, 405, 328, 271, 456, 388, 335, 292, 512, 454, 405, 364, 328, 298, 271, 496, 456, 420, 388, 360, 335, 312, 292, 273, 512, 482, 454, 428, 405, 383, 364, 345, 328, 312, 298, 284, 271, 259, 496, 475, 456, 437, 420, 404, 388, 374, 360, 347, 335, 323, 312, 302, 292, 282, 273, 265, 512, 497, 482, 468, 454, 441, 428, 417, 405, 394, 383, 373, 364, 354, 345, 337, 328, 320, 312, 305, 298, 291, 284, 278, 271, 265, 259, 507, 496, 485, 475, 465, 456, 446, 437, 428, 420, 412, 404, 396, 388, 381, 374, 367, 360, 354, 347, 341, 335, 329, 323, 318, 312, 307, 302, 297, 292, 287, 282, 278, 273, 269, 265, 261, 512, 505, 497, 489, 482, 475, 468, 461, 454, 447, 441, 435, 428, 422, 417, 411, 405, 399, 394, 389, 383, 378, 373, 368, 364, 359, 354, 350, 345, 341, 337, 332, 328, 324, 320, 316, 312, 309, 305, 301, 298, 294, 291, 287, 284, 281, 278, 274, 271, 268, 265, 262, 259, 257, 507, 501, 496, 491, 485, 480, 475, 470, 465, 460, 456, 451, 446, 442, 437, 433, 428, 424, 420, 416, 412, 408, 404, 400, 396, 392, 388, 385, 381, 377, 374, 370, 367, 363, 360, 357, 354, 350, 347, 344, 341, 338, 335, 332, 329, 326, 323, 320, 318, 315, 312, 310, 307, 304, 302, 299, 297, 294, 292, 289, 287, 285, 282, 280, 278, 275, 273, 271, 269, 267, 265, 263, 261, 259];
            c |= 0;
            var g, h, i, j, k, l, m, n, o, p, q, r, s, t, u, v, w, x, y, z, A = this.background.getContext("2d"),
            B = A.getImageData(0, 0, a, b),
            C = B.data,
            D = c + 1,
            E = D * (D + 1) / 2,
            F = new e,
            G = new e,
            H = F;
            for (i = 1; 2 * c + 1 > i; i++) H = H.next = new e,
            i === D && (G = H);
            H.next = F;
            var I = null,
            J = null;
            m = l = 0;
            for (var K, L = f[c], M = 0; M < d.length; ++M) if (c <= d[M][0]) {
                K = d[M - 1][1];
                break
            }
            for (h = 0; b > h; h++) {
                for (t = u = v = n = o = p = 0, q = D * (w = C[l]), r = D * (x = C[l + 1]), s = D * (y = C[l + 2]), n += E * w, o += E * x, p += E * y, H = F, i = 0; D > i; i++) H.r = w,
                H.g = x,
                H.b = y,
                H = H.next;
                for (i = 1; D > i; i++) j = l + ((i > a - 1 ? a - 1 : i) << 2),
                n += (H.r = w = C[j]) * (z = D - i),
                o += (H.g = x = C[j + 1]) * z,
                p += (H.b = y = C[j + 2]) * z,
                t += w,
                u += x,
                v += y,
                H = H.next;
                for (I = F, J = G, g = 0; a > g; g++) C[l] = n * L >> K,
                C[l + 1] = o * L >> K,
                C[l + 2] = p * L >> K,
                n -= q,
                o -= r,
                p -= s,
                q -= I.r,
                r -= I.g,
                s -= I.b,
                j = m + ((j = g + c + 1) < a - 1 ? j: a - 1) << 2,
                t += I.r = C[j],
                u += I.g = C[j + 1],
                v += I.b = C[j + 2],
                n += t,
                o += u,
                p += v,
                I = I.next,
                q += w = J.r,
                r += x = J.g,
                s += y = J.b,
                t -= w,
                u -= x,
                v -= y,
                J = J.next,
                l += 4;
                m += a
            }
            for (g = 0; a > g; g++) {
                for (u = v = t = o = p = n = 0, l = g << 2, q = D * (w = C[l]), r = D * (x = C[l + 1]), s = D * (y = C[l + 2]), n += E * w, o += E * x, p += E * y, H = F, i = 0; D > i; i++) H.r = w,
                H.g = x,
                H.b = y,
                H = H.next;
                for (k = a, i = 1; D > i; i++) l = k + g << 2,
                n += (H.r = w = C[l]) * (z = D - i),
                o += (H.g = x = C[l + 1]) * z,
                p += (H.b = y = C[l + 2]) * z,
                t += w,
                u += x,
                v += y,
                H = H.next,
                b - 1 > i && (k += a);
                for (l = g, I = F, J = G, h = 0; b > h; h++) j = l << 2,
                C[j] = n * L >> K,
                C[j + 1] = o * L >> K,
                C[j + 2] = p * L >> K,
                n -= q,
                o -= r,
                p -= s,
                q -= I.r,
                r -= I.g,
                s -= I.b,
                j = g + ((j = h + D) < b - 1 ? j: b - 1) * a << 2,
                n += t += I.r = C[j],
                o += u += I.g = C[j + 1],
                p += v += I.b = C[j + 2],
                I = I.next,
                q += w = J.r,
                r += x = J.g,
                s += y = J.b,
                t -= w,
                u -= x,
                v -= y,
                J = J.next,
                l += a
            }
            A.putImageData(B, 0, 0)
        },
        f.prototype.update = function(a, b) {
            if (a.gid) {
                if (!this.matrix[a.gmx] || !this.matrix[a.gmx][a.gmy]) return null;
                if (this.matrix[a.gmx][a.gmy].remove(a), b) return null;
                if (a.gmx = Math.floor(a.x / this.resolution), a.gmy = Math.floor(a.y / this.resolution), !this.matrix[a.gmx] || !this.matrix[a.gmx][a.gmy]) return null;
                this.matrix[a.gmx][a.gmy].add(a);
                var c = this.collisions(a);
                if (c && null != c.next) return c.next
            } else {
                if (a.gid = Math.random().toString(36).substr(2, 9), a.gmx = Math.floor(a.x / this.resolution), a.gmy = Math.floor(a.y / this.resolution), !this.matrix[a.gmx] || !this.matrix[a.gmx][a.gmy]) return null;
                this.matrix[a.gmx][a.gmy].add(a)
            }
            return null
        },
        f.prototype.collisions = function(a) {
            var b = new g(null),
            c = b;
            return b = this.addAll(b, a.gmx - 1, a.gmy + 1),
            b = this.addAll(b, a.gmx, a.gmy + 1),
            b = this.addAll(b, a.gmx + 1, a.gmy + 1),
            c
        },
        f.prototype.addAll = function(a, b, c) {
            if (b > 0 && c > 0 && b < this.xc && c < this.yc) for (var d = this.matrix[b][c]; null != d.next;) d = d.next,
            a.next = new g(d.drop),
            a = a.next;
            return a
        },
        f.prototype.remove = function(a) {
            this.matrix[a.gmx][a.gmy].remove(a)
        },
        g.prototype.add = function(a) {
            for (var b = this; null != b.next;) b = b.next;
            b.next = new g(a)
        },
        g.prototype.remove = function(a) {
            for (var b = this,
            c = null; null != b.next;) c = b,
            b = b.next,
            b.drop.gid === a.gid && (c.next = b.next)
        },
        b && b.exports && (b.exports = c)
    },
    {}],
    21 : [function(a, b) {
        var c, d;
        c = window.RP.Css,
        d = {},
        d.start = function(a, b) {
            return c.enableAnimation(a[0], b),
            c.css(a[0], JSON.parse(a.attr("data-style-cache"))),
            setTimeout(function() {
                return c.disableAnimation(a[0])
            },
            1e3 * b)
        },
        d.ready = function(a) {
            var b, d;
            return d = a.attr("data-style-cache"),
            d ? b = JSON.parse(d) : (b = {
                rotationY: c.getRotateY(a[0]),
                zIndex: a.css("z-index"),
                y: c.getY(a[0]),
                x: c.getX(a[0])
            },
            a.attr("data-style-cache", JSON.stringify(b))),
            c.css(a[0], {
                rotationY: b.rotationY + 180
            })
        },
        b.exports = d
    },
    {}],
    22 : [function(a, b) {
        var c, d, e, f;
        e = window.RP.Css,
        f = {},
        c = 0,
        d = {},
        f.getAnimationName = function(a, b, e, f) {
            var g, h;
            return null == a && (a = 1),
            null == b && (b = 1),
            g = "scaleX-" + a + "-scaleY" + b + "-translateX" + e + "-translateY" + f,
            h = d[g],
            h || (h = d[g] = "rotation2d" + c++),
            h
        },
        f.start = function(a, b) {
            var c, d, f, g, h, i, j, k, l, m, n, o, p;
            return null == b && (b = 10),
            e.disableAnimation(a[0]),
            i = JSON.parse(a.attr("data-style-cache")),
            g = null != (m = e.getScaleX(a[0])) ? m: 1,
            h = null != (n = e.getScaleY(a[0])) ? n: 1,
            d = Math.min(g, h),
            f = "scaleX(" + d + ") scaleY(" + d + ")",
            k = null != (o = i.x) ? o: e.getX(a[0]),
            l = null != (p = i.y) ? p: e.getY(a[0]),
            c = this.getAnimationName(g, h, k, l),
            j = "translate(" + k + "px, " + l + "px)",
            e.css(a[0], i),
            e.css(a[0], {
                "-webkit-transform-origin": "50% 50%",
                "-webkit-animation": c + " " + b + "s linear infinite"
            }),
            e.regKeyFrames(c, "{ 0% {-webkit-transform:" + j + " rotate(0deg) " + f + ";} 100% {-webkit-transform:" + j + " rotate(360deg) " + f + ";} }")
        },
        f.ready = function(a) {
            var b, c, d, f, g;
            return d = a.attr("data-style-cache"),
            d ? c = JSON.parse(d) : (c = {
                opacity: a.css("opacity"),
                zIndex: a.css("z-index"),
                y: e.getY(a[0]),
                x: e.getX(a[0])
            },
            a.attr("data-style-cache", JSON.stringify(c))),
            f = e.getScaleX(a[0]),
            g = e.getScaleY(a[0]),
            b = Math.min(f, g),
            e.css(a[0], {
                scaleX: b,
                scaleY: b
            }),
            e.rmKeyFrames(a[0])
        },
        b.exports = f
    },
    {}],
    23 : [function(a, b) {
        var c, d;
        c = window.RP.Css,
        d = {},
        d.start = function(a, b) {
            return c.enableAnimation(a[0], b, "ease-in"),
            c.css(a[0], JSON.parse(a.attr("data-style-cache"))),
            setTimeout(function() {
                return c.disableAnimation(a[0])
            },
            1e3 * b)
        },
        d.ready = function(a) {
            var b, d;
            return d = a.attr("data-style-cache"),
            d ? b = JSON.parse(d) : (b = {
                opacity: a.css("opacity"),
                zIndex: a.css("z-index"),
                scaleX: c.getScaleX(a[0]),
                scaleY: c.getScaleY(a[0]),
                y: c.getY(a[0]),
                x: c.getX(a[0])
            },
            a.attr("data-style-cache", JSON.stringify(b))),
            c.css(a[0], {
                opacity: 0,
                scaleX: 2 * b.scaleX,
                scaleY: 2 * b.scaleY
            })
        },
        b.exports = d
    },
    {}],
    24 : [function(a, b) {
        var c, d;
        c = window.RP.Css,
        d = {},
        d.start = function(a, b) {
            return c.enableAnimation(a[0], b, "ease-in"),
            c.css(a[0], JSON.parse(a.attr("data-style-cache"))),
            setTimeout(function() {
                return c.disableAnimation(a[0])
            },
            1e3 * b)
        },
        d.ready = function(a) {
            var b, d;
            return d = a.attr("data-style-cache"),
            d ? b = JSON.parse(d) : (b = {
                opacity: a.css("opacity"),
                zIndex: a.css("z-index"),
                scaleX: c.getScaleX(a[0]),
                scaleY: c.getScaleY(a[0]),
                y: c.getY(a[0]),
                x: c.getX(a[0])
            },
            a.attr("data-style-cache", JSON.stringify(b))),
            c.css(a[0], {
                opacity: 0,
                scaleX: b.scaleX / 2,
                scaleY: b.scaleY / 2
            })
        },
        b.exports = d
    },
    {}],
    25 : [function(a, b) {
        var c, d, e;
        c = window.RP.Css,
        d = {},
        e = function(a, b) {
            var c, d, e, f, g, h, i, j, k;
            for (this.$masks = function() {
                var a, c;
                for (c = [], f = a = 1; 4 >= a; f = ++a) c.push(b.find(".mask-" + f));
                return c
            } (), this.$buttons = function() {
                var a, d;
                for (d = [], c = a = 1; 3 >= a; c = ++a) d.push(b.find(".mask-btn-" + c));
                return d
            } (), this.count = 0, i = function(a) {
                var c, d, e, f, g;
                for (e = b.width(), c = g = 0; 11 >= g; c = ++g) d = 60 * c,
                f = c * e,
                setTimeout(function() {
                    return function(a, c) {
                        return function() {
                            return b.css("-webkit-mask-position", "-" + c + "px 0")
                        }
                    }
                } (this)(d, f), d);
                return this.$buttons[a] && this.$buttons[a].hide(),
                this.count++,
                3 === this.count ? setTimeout(function() {
                    return function() {
                        return i(3)
                    }
                } (this), 1e3) : void 0
            },
            j = this.$buttons, k = [], e = g = 0, h = j.length; h > g; e = ++g) d = j[e],
            k.push(d.on("tap",
            function() {
                return function(a) {
                    return function() {
                        return i(a)
                    }
                }
            } (this)(e)));
            return k
        },
        d.start = function(a) {
            var b;
            return $("div.simulator").length <= 0 ? (b = $("<div class='bg'> <div class='mask-btns'> <div class='mask-btn mask-btn-1'></div> <div class='mask-btn mask-btn-2'></div> <div class='mask-btn mask-btn-3'></div> </div> </div>"), a.parent().append(b), b.css("width", a.css("width")), b.css("height", a.css("height")), b.css("position", "absolute"), b.css("-webkit-transform", a.css("-webkit-transform")), b.css("top", a.css("top")), b.css("left", a.css("left")), b.css("z-index", parseInt(a.css("z-index")) + 1), b.css("backgroundImage", 'url("http://wx.drjou.cc/wap/event/2015/05/sjz/images/rabbit-bg.jpg")'), b.css("backgroundSize", "100% 100%"), b.css("backgroundSize", "100% 100%"), b.css("webkitMaskPosition", "640px 640px"), b.css("webkitMaskRepeat", "no-repeat"), b.css("webkitMaskSize", "1200% 100%"), b.css("webkitMaskImage", 'url("http://wx.drjou.cc/wap/event/2015/05/sjz/images/rabbit-Touch4.png")'), b.find("div.mask").css("position", "absolute"), b.find("div.mask").css("top", "0"), b.find("div.mask").css("left", "0"), b.find("div.mask").css("width", "100%"), b.find("div.mask").css("height", "100%"), b.find("div.mask-btns").css("width", "100%"), b.find("div.mask-btns").css("height", "100%"), b.find("div.mask-btn").css("position", "absolute"), b.find("div.mask-btn").css("width", "100px"), b.find("div.mask-btn").css("height", "100px"), b.find("div.mask-btn").css("background", 'url("http://wx.drjou.cc/wap/event/2015/05/sjz/images/rabbit-mask_btn_touch.png")  no-repeat center'), b.find("div.mask-btn-1").css("left", "7%"), b.find("div.mask-btn-1").css("top", "40%"), b.find("div.mask-btn-2").css("left", "68%"), b.find("div.mask-btn-2").css("top", "37%"), b.find("div.mask-btn-3").css("left", "31%"), b.find("div.mask-btn-3").css("top", "63%"), a.remove(), e(a, b)) : void 0
        },
        d.ready = function() {},
        b.exports = d
    },
    {}],
    26 : [function(a, b) {
        function c() {
            this.r = 0,
            this.g = 0,
            this.b = 0,
            this.a = 0,
            this.next = null
        }
        StackBlur = {},
        StackBlur.mul_table = [512, 512, 456, 512, 328, 456, 335, 512, 405, 328, 271, 456, 388, 335, 292, 512, 454, 405, 364, 328, 298, 271, 496, 456, 420, 388, 360, 335, 312, 292, 273, 512, 482, 454, 428, 405, 383, 364, 345, 328, 312, 298, 284, 271, 259, 496, 475, 456, 437, 420, 404, 388, 374, 360, 347, 335, 323, 312, 302, 292, 282, 273, 265, 512, 497, 482, 468, 454, 441, 428, 417, 405, 394, 383, 373, 364, 354, 345, 337, 328, 320, 312, 305, 298, 291, 284, 278, 271, 265, 259, 507, 496, 485, 475, 465, 456, 446, 437, 428, 420, 412, 404, 396, 388, 381, 374, 367, 360, 354, 347, 341, 335, 329, 323, 318, 312, 307, 302, 297, 292, 287, 282, 278, 273, 269, 265, 261, 512, 505, 497, 489, 482, 475, 468, 461, 454, 447, 441, 435, 428, 422, 417, 411, 405, 399, 394, 389, 383, 378, 373, 368, 364, 359, 354, 350, 345, 341, 337, 332, 328, 324, 320, 316, 312, 309, 305, 301, 298, 294, 291, 287, 284, 281, 278, 274, 271, 268, 265, 262, 259, 257, 507, 501, 496, 491, 485, 480, 475, 470, 465, 460, 456, 451, 446, 442, 437, 433, 428, 424, 420, 416, 412, 408, 404, 400, 396, 392, 388, 385, 381, 377, 374, 370, 367, 363, 360, 357, 354, 350, 347, 344, 341, 338, 335, 332, 329, 326, 323, 320, 318, 315, 312, 310, 307, 304, 302, 299, 297, 294, 292, 289, 287, 285, 282, 280, 278, 275, 273, 271, 269, 267, 265, 263, 261, 259],
        StackBlur.shg_table = [9, 11, 12, 13, 13, 14, 14, 15, 15, 15, 15, 16, 16, 16, 16, 17, 17, 17, 17, 17, 17, 17, 18, 18, 18, 18, 18, 18, 18, 18, 18, 19, 19, 19, 19, 19, 19, 19, 19, 19, 19, 19, 19, 19, 19, 20, 20, 20, 20, 20, 20, 20, 20, 20, 20, 20, 20, 20, 20, 20, 20, 20, 20, 21, 21, 21, 21, 21, 21, 21, 21, 21, 21, 21, 21, 21, 21, 21, 21, 21, 21, 21, 21, 21, 21, 21, 21, 21, 21, 21, 22, 22, 22, 22, 22, 22, 22, 22, 22, 22, 22, 22, 22, 22, 22, 22, 22, 22, 22, 22, 22, 22, 22, 22, 22, 22, 22, 22, 22, 22, 22, 22, 22, 22, 22, 22, 22, 23, 23, 23, 23, 23, 23, 23, 23, 23, 23, 23, 23, 23, 23, 23, 23, 23, 23, 23, 23, 23, 23, 23, 23, 23, 23, 23, 23, 23, 23, 23, 23, 23, 23, 23, 23, 23, 23, 23, 23, 23, 23, 23, 23, 23, 23, 23, 23, 23, 23, 23, 23, 23, 23, 24, 24, 24, 24, 24, 24, 24, 24, 24, 24, 24, 24, 24, 24, 24, 24, 24, 24, 24, 24, 24, 24, 24, 24, 24, 24, 24, 24, 24, 24, 24, 24, 24, 24, 24, 24, 24, 24, 24, 24, 24, 24, 24, 24, 24, 24, 24, 24, 24, 24, 24, 24, 24, 24, 24, 24, 24, 24, 24, 24, 24, 24, 24, 24, 24, 24, 24, 24, 24, 24, 24, 24, 24, 24],
        StackBlur.stackBlurCanvasRGBA = function(a, b, d, e, f, g) {
            if (! (isNaN(g) || 1 > g)) {
                g |= 0;
                var h, i = a,
                j = i.getContext("2d");
                try {
                    try {
                        h = j.getImageData(b, d, e, f)
                    } catch(k) {
                        h = j.getImageData(b, d, e, f)
                    }
                } catch(k) {
                    throw alert("Cannot access image"),
                    new Error("unable to access image data: " + k)
                }
                var l, m, n, o, p, q, r, s, t, u, v, w, x, y, z, A, B, C, D, E, F, G, H, I, J = h.data,
                K = g + g + 1,
                L = e - 1,
                M = f - 1,
                N = g + 1,
                O = N * (N + 1) / 2,
                P = new c,
                Q = P;
                for (n = 1; K > n; n++) if (Q = Q.next = new c, n == N) var R = Q;
                Q.next = P;
                var S = null,
                T = null;
                r = q = 0;
                var U = StackBlur.mul_table[g],
                V = StackBlur.shg_table[g];
                for (m = 0; f > m; m++) {
                    for (A = B = C = D = s = t = u = v = 0, w = N * (E = J[q]), x = N * (F = J[q + 1]), y = N * (G = J[q + 2]), z = N * (H = J[q + 3]), s += O * E, t += O * F, u += O * G, v += O * H, Q = P, n = 0; N > n; n++) Q.r = E,
                    Q.g = F,
                    Q.b = G,
                    Q.a = H,
                    Q = Q.next;
                    for (n = 1; N > n; n++) o = q + ((n > L ? L: n) << 2),
                    s += (Q.r = E = J[o]) * (I = N - n),
                    t += (Q.g = F = J[o + 1]) * I,
                    u += (Q.b = G = J[o + 2]) * I,
                    v += (Q.a = H = J[o + 3]) * I,
                    A += E,
                    B += F,
                    C += G,
                    D += H,
                    Q = Q.next;
                    for (S = P, T = R, l = 0; e > l; l++) J[q + 3] = H = v * U >> V,
                    0 != H ? (H = 255 / H, J[q] = (s * U >> V) * H, J[q + 1] = (t * U >> V) * H, J[q + 2] = (u * U >> V) * H) : J[q] = J[q + 1] = J[q + 2] = 0,
                    s -= w,
                    t -= x,
                    u -= y,
                    v -= z,
                    w -= S.r,
                    x -= S.g,
                    y -= S.b,
                    z -= S.a,
                    o = r + ((o = l + g + 1) < L ? o: L) << 2,
                    A += S.r = J[o],
                    B += S.g = J[o + 1],
                    C += S.b = J[o + 2],
                    D += S.a = J[o + 3],
                    s += A,
                    t += B,
                    u += C,
                    v += D,
                    S = S.next,
                    w += E = T.r,
                    x += F = T.g,
                    y += G = T.b,
                    z += H = T.a,
                    A -= E,
                    B -= F,
                    C -= G,
                    D -= H,
                    T = T.next,
                    q += 4;
                    r += e
                }
                for (l = 0; e > l; l++) {
                    for (B = C = D = A = t = u = v = s = 0, q = l << 2, w = N * (E = J[q]), x = N * (F = J[q + 1]), y = N * (G = J[q + 2]), z = N * (H = J[q + 3]), s += O * E, t += O * F, u += O * G, v += O * H, Q = P, n = 0; N > n; n++) Q.r = E,
                    Q.g = F,
                    Q.b = G,
                    Q.a = H,
                    Q = Q.next;
                    for (p = e, n = 1; g >= n; n++) q = p + l << 2,
                    s += (Q.r = E = J[q]) * (I = N - n),
                    t += (Q.g = F = J[q + 1]) * I,
                    u += (Q.b = G = J[q + 2]) * I,
                    v += (Q.a = H = J[q + 3]) * I,
                    A += E,
                    B += F,
                    C += G,
                    D += H,
                    Q = Q.next,
                    M > n && (p += e);
                    for (q = l, S = P, T = R, m = 0; f > m; m++) o = q << 2,
                    J[o + 3] = H = v * U >> V,
                    H > 0 ? (H = 255 / H, J[o] = (s * U >> V) * H, J[o + 1] = (t * U >> V) * H, J[o + 2] = (u * U >> V) * H) : J[o] = J[o + 1] = J[o + 2] = 0,
                    s -= w,
                    t -= x,
                    u -= y,
                    v -= z,
                    w -= S.r,
                    x -= S.g,
                    y -= S.b,
                    z -= S.a,
                    o = l + ((o = m + N) < M ? o: M) * e << 2,
                    s += A += S.r = J[o],
                    t += B += S.g = J[o + 1],
                    u += C += S.b = J[o + 2],
                    v += D += S.a = J[o + 3],
                    S = S.next,
                    w += E = T.r,
                    x += F = T.g,
                    y += G = T.b,
                    z += H = T.a,
                    A -= E,
                    B -= F,
                    C -= G,
                    D -= H,
                    T = T.next,
                    q += e
                }
                j.putImageData(h, b, d)
            }
        },
        b.exports = StackBlur
    },
    {}]
},
{},
[4]),
function d(a, b, c) {
    function e(g, h) {
        if (!b[g]) {
            if (!a[g]) {
                var i = "function" == typeof require && require;
                if (!h && i) return i(g, !0);
                if (f) return f(g, !0);
                throw new Error("Cannot find module '" + g + "'")
            }
            var j = b[g] = {
                exports: {}
            };
            a[g][0].call(j.exports,
            function(b) {
                var c = a[g][1][b];
                return e(c ? c: b)
            },
            j, j.exports, d, a, b, c)
        }
        return b[g].exports
    }
    for (var f = "function" == typeof require && require,
    g = 0; g < c.length; g++) e(c[g]);
    return e
} ({
    1 : [function(a, b) {
        var c, d, e = {}.hasOwnProperty,
        f = function(a, b) {
            function c() {
                this.constructor = a
            }
            for (var d in b) e.call(b, d) && (a[d] = b[d]);
            return c.prototype = b.prototype,
            a.prototype = new c,
            a.__super__ = b.prototype,
            a
        };
        d = window.RP.Slide,
        c = function(a) {
            function b() {
                return b.__super__.constructor.apply(this, arguments)
            }
            return f(b, a),
            b.prototype._enableAnimation = function() {
                var a;
                return this.isAnimating = !0,
                a = "all 0s ease-in-out",
                this.$elementPages[0].style.webkitTransition = a,
                this.$elementSections[this.previousIndex].style.webkitAnimation = "myfirst 0s both ease"
            },
            b.prototype._disableAnimation = function() {
                return this.isAnimating = !1,
                this.$elementPages[0].style.webkitTransition = "",
                this.$elementSections[this.previousIndex].style.webkitTransition = "",
                this.$elementSections[this.previousIndex].style.webkitTransform = "",
                this.$elementSections[this.currentIndex].style.zIndex = "",
                this.$elementSections[this.previousIndex].style.webkitAnimation = "",
                this.$elementSections[this.previousIndex].style.zIndex = "",
                this.$elementPages[0].style.backgroundColor = ""
            },
            b
        } (d),
        b.exports = c
    },
    {}],
    2 : [function(a, b) {
        var c, d, e, f = {}.hasOwnProperty,
        g = function(a, b) {
            function c() {
                this.constructor = a
            }
            for (var d in b) f.call(b, d) && (a[d] = b[d]);
            return c.prototype = b.prototype,
            a.prototype = new c,
            a.__super__ = b.prototype,
            a
        };
        e = window.RP.Slide,
        d = window.RP.Css,
        c = function(a) {
            function b() {
                return b.__super__.constructor.apply(this, arguments)
            }
            return g(b, a),
            b.prototype._changeSwipePermission = function() {
                return this.swipePermission = !0
            },
            b.prototype._enableAnimation = function() {
                return this.isAnimating = !0,
                d.regKeyFrames("slides-cover-scaleFromBottom", "{ to {  -webkit-transform: translateY(100%) scale(.5); transform: translateY(100%) scale(.5); } }"),
                d.regKeyFrames("slides-cover-scaleFromTop", "{ to { -webkit-transform: translateY(-100%) scale(.5); transform: translateY(-100%) scale(.5); } }"),
                this.$elementPages[0].style.webkitTransition = "-webkit-transform .4s ease-in-out",
                this.$elementPages[0].style.Transition = "transform .4s ease-in-out",
                this.$elementSections[this.previousIndex].style.webkitAnimation = this.previousIndex < this.currentIndex ? "slides-cover-scaleFromBottom .8s ease both": "slides-cover-scaleFromTop .8s ease both",
                this.$elementSections[this.previousIndex].style.zIndex = "0",
                this.$elementSections[this.currentIndex].style.zIndex = "1"
            },
            b.prototype._disableAnimation = function() {
                return this.isAnimating = !1,
                this.$elementPages[0].style.webkitTransition = "",
                this.$elementPages[0].style.Transition = "",
                this.$elementSections[this.previousIndex].style.webkitAnimation = "",
                this.$elementSections[this.previousIndex].style.Animation = "",
                this.$elementSections[this.previousIndex].style.zIndex = "",
                this.$elementSections[this.currentIndex].style.zIndex = ""
            },
            b
        } (e),
        b.exports = c
    },
    {}],
    3 : [function(a, b) {
        var c, d, e, f = {}.hasOwnProperty,
        g = function(a, b) {
            function c() {
                this.constructor = a
            }
            for (var d in b) f.call(b, d) && (a[d] = b[d]);
            return c.prototype = b.prototype,
            a.prototype = new c,
            a.__super__ = b.prototype,
            a
        };
        e = window.RP.Slide,
        c = window.RP.Css,
        d = function(a) {
            function b() {
                return b.__super__.constructor.apply(this, arguments)
            }
            return g(b, a),
            b.prototype._changeSwipePermission = function() {
                return this.swipePermission = !1
            },
            b.prototype._enableAnimation = function() {
                return this.isAnimating = !0,
                c.regKeyFrames("rotateCubeTopOut", "{0% { } 50% { -webkit-animation-timing-function: ease-out; animation-timing-function:ease-out; -webkit-transform: translateY(-50%) translateZ(-200px) rotateX(45deg); transform: translateY(-50%) translateZ(-200px) rotateX(45deg); } 100% { opacity: .3; -webkit-transform: translateY(-77%) translateZ(-286px) rotateX(58deg); transform:  translateY(-77%) translateZ(-286px) rotateX(58deg); }}"),
                c.regKeyFrames("rotateCubeTopIn", "{0% { opacity: .3; -webkit-transform: translateY(-30%) rotateX(-90deg); transform: translateY(-30%) rotateX(-90deg); } 50% { -webkit-animation-timing-function: ease-out; animation-timing-function: ease-out; -webkit-transform: translateY(-83%) translateZ(-230px) rotateX(-45deg); transform: translateY(-83%) translateZ(-230px) rotateX(-45deg); } 100% { -webkit-transform: translateY(-100%); transform: translateY(-100%);}}"),
                c.regKeyFrames("rotateCubeBottomOut", "{0% { } 50% { -webkit-animation-timing-function: ease-out; animation-timing-function: ease-out; -webkit-transform: translateY(50%) translateZ(-200px) rotateX(-45deg); transform: translateY(50%) translateZ(-200px) rotateX(-45deg); } 100% { opacity: .3; -webkit-transform: translateY(23%) translateZ(-286px) rotateX(-122deg); transform: translateY(23%) translateZ(-286px) rotateX(-122deg);}}"),
                c.regKeyFrames("rotateCubeBottomIn", "{0% { opacity: .3; } 50% { -webkit-animation-timing-function: ease-out; animation-timing-function: ease-out; -webkit-transform: translateY(83%) translateZ(-230px) rotateX(45deg); transform: translateY(80%) translateZ(-200px) rotateX(45deg); } 100% { -webkit-transform: translateY(100%); transform: translateY(100%); }}"),
                this.$elementPages[0].style.webkitTransition = "-webkit-transform .6s linear 10s",
                this.previousIndex < this.currentIndex ? (this.$elementSections[this.previousIndex].style.webkitAnimation = "rotateCubeTopOut .6s both linear", this.$elementSections[this.currentIndex].style.webkitAnimation = "rotateCubeTopIn .6s both linear") : (this.$elementSections[this.previousIndex].style.webkitAnimation = "rotateCubeBottomOut .6s both linear", this.$elementSections[this.currentIndex].style.webkitAnimation = "rotateCubeBottomIn .6s both linear"),
                this.$elementSections[this.previousIndex].style.zIndex = "0",
                this.$elementSections[this.currentIndex].style.zIndex = "1"
            },
            b.prototype._disableAnimation = function() {
                return this.isAnimating = !1,
                this.$elementPages[0].style.webkitTransition = "",
                this.$elementSections[this.previousIndex].style.webkitAnimation = "",
                this.$elementSections[this.currentIndex].style.webkitAnimation = "",
                this.$elementSections[this.previousIndex].style.zIndex = "",
                this.$elementSections[this.currentIndex].style.zIndex = ""
            },
            b
        } (e),
        b.exports = d
    },
    {}],
    4 : [function(a, b) {
        var c, d, e = {}.hasOwnProperty,
        f = function(a, b) {
            function c() {
                this.constructor = a
            }
            for (var d in b) e.call(b, d) && (a[d] = b[d]);
            return c.prototype = b.prototype,
            a.prototype = new c,
            a.__super__ = b.prototype,
            a
        };
        d = window.RP.Slide,
        c = function(a) {
            function b() {
                return b.__super__.constructor.apply(this, arguments)
            }
            return f(b, a),
            b.prototype._changeSwipePermission = function() {
                return this.swipePermission = !0
            },
            b.prototype._enableAnimation = function() {
                return this.isAnimating = !0,
                this.$elementPages[0].style.webkitTransition = "all .4s ease-out",
                this.$elementPages[0].style.Transition = "all .4s ease-out"
            },
            b.prototype._disableAnimation = function() {
                return this.isAnimating = !1,
                this.$elementPages[0].style.webkitTransition = "",
                this.$elementPages[0].style.Transition = ""
            },
            b
        } (d),
        b.exports = c
    },
    {}],
    5 : [function(a, b) {
        var c, d, e, f = {}.hasOwnProperty,
        g = function(a, b) {
            function c() {
                this.constructor = a
            }
            for (var d in b) f.call(b, d) && (a[d] = b[d]);
            return c.prototype = b.prototype,
            a.prototype = new c,
            a.__super__ = b.prototype,
            a
        };
        e = window.RP.Slide,
        c = window.RP.Css,
        d = function(a) {
            function b() {
                return b.__super__.constructor.apply(this, arguments)
            }
            return g(b, a),
            b.prototype._changeSwipePermission = function() {
                return this.swipePermission = !0
            },
            b.prototype._enableAnimation = function() {
                return this.isAnimating = !0,
                c.regKeyFrames("slides-fade-moveToTopFade", "{ to { opacity: 0.3; -webkit-transform: translateY(100%); transform: translateY(100%); } }"),
                c.regKeyFrames("slides-fade-moveToBottomFade", "{ to { opacity: 0.3; -webkit-transform: translateY(-100%); transform: translateY(-100%); } }"),
                this.$elementPages[0].style.webkitTransition = "-webkit-transform .4s ease-in-out",
                this.$elementPages[0].style.Transition = "transform .4s ease-in-out",
                this.$elementSections[this.previousIndex].style.webkitAnimation = this.previousIndex < this.currentIndex ? "slides-fade-moveToTopFade .8s ease both": "slides-fade-moveToBottomFade .8s ease both",
                this.$elementSections[this.previousIndex].style.Animation = this.previousIndex < this.currentIndex ? "slides-fade-moveToTopFade .8s ease both": "slides-fade-moveToBottomFade .8s ease both",
                this.$elementSections[this.previousIndex].style.zIndex = "0",
                this.$elementSections[this.currentIndex].style.zIndex = "1"
            },
            b.prototype._disableAnimation = function() {
                return this.isAnimating = !1,
                this.$elementPages[0].style.webkitTransition = "",
                this.$elementSections[this.previousIndex].style.webkitAnimation = "",
                this.$elementPages[0].style.Transition = "",
                this.$elementSections[this.previousIndex].style.Animation = "",
                this.$elementSections[this.previousIndex].style.zIndex = "",
                this.$elementSections[this.currentIndex].style.zIndex = ""
            },
            b
        } (e),
        b.exports = d
    },
    {}],
    6 : [function(a, b) {
        var c, d, e, f = {}.hasOwnProperty,
        g = function(a, b) {
            function c() {
                this.constructor = a
            }
            for (var d in b) f.call(b, d) && (a[d] = b[d]);
            return c.prototype = b.prototype,
            a.prototype = new c,
            a.__super__ = b.prototype,
            a
        };
        e = window.RP.Slide,
        c = window.RP.Css,
        d = function(a) {
            function b() {
                return b.__super__.constructor.apply(this, arguments)
            }
            return g(b, a),
            b.prototype._changeSwipePermission = function() {
                return this.swipePermission = !1
            },
            b.prototype._enableAnimation = function() {
                var a;
                return c.regKeyFrames("fallFromTop", "{ 0% {} 20% { -webkit-transform: rotateZ(0deg) ; transform: rotateZ(10deg) ; -webkit-animation-timing-function: ease-out; animation-timing-function: ease-out; } 40% { -webkit-transform: rotateZ(10deg); transform: rotateZ(17deg); } 60% { -webkit-transform: rotateZ(17deg); transform: rotateZ(16deg); } 80% { -webkit-transform: rotateZ(16deg); transform: rotateZ(16deg); } 100% { -webkit-transform:  rotateZ(17deg) translateY(200%); transform:  rotateZ(17deg) translateY(200%); }}"),
                c.regKeyFrames("fallFromBottom", "{ 0% { -webkit-transform: rotateZ(0deg)  translateY(-100%); transform: rotateZ(0deg)  translateY(-100%); } 20% { -webkit-transform: rotateZ(10deg)  translateY(-100%); transform: rotateZ(10deg)  translateY(-100%); -webkit-animation-timing-function: ease-out; animation-timing-function: ease-out; } 40% { -webkit-transform: rotateZ(17deg) translateY(-100%); transform: rotateZ(17deg) translateY(-100%); } 60% { -webkit-transform: rotateZ(16deg) translateY(-100%); transform: rotateZ(16deg) translateY(-100%); } 100% { -webkit-transform:  rotateZ(17deg) translateY(0); transform:  rotateZ(17deg) translateY(0); }}"),
                this.isAnimating = !0,
                a = "all .1s ease-in-out",
                this.$elementPages[0].style.webkitTransition = a,
                this.$elementSections[this.previousIndex].style.zIndex = "1",
                this.$elementSections[this.currentIndex].style.zIndex = "0",
                this.previousIndex < this.currentIndex ? (this.$elementSections[this.previousIndex].style.webkitTransform = "translateY(100%)", this.$elementSections[this.previousIndex].style.webkitAnimation = "fallFromTop 1s both ease-in") : this.$elementSections[this.previousIndex].style.webkitAnimation = "fallFromBottom .8s both ease-in"
            },
            b.prototype._disableAnimation = function() {
                return this.isAnimating = !1,
                this.$elementPages[0].style.webkitTransition = "",
                this.$elementSections[this.previousIndex].style.webkitTransition = "",
                this.$elementSections[this.previousIndex].style.webkitTransform = "",
                this.$elementSections[this.currentIndex].style.zIndex = "",
                this.$elementSections[this.previousIndex].style.webkitAnimation = "",
                this.$elementSections[this.previousIndex].style.zIndex = "",
                this.$elementPages[0].style.backgroundColor = ""
            },
            b
        } (e),
        b.exports = d
    },
    {}],
    7 : [function(a, b) {
        var c, d, e = {}.hasOwnProperty,
        f = function(a, b) {
            function c() {
                this.constructor = a
            }
            for (var d in b) e.call(b, d) && (a[d] = b[d]);
            return c.prototype = b.prototype,
            a.prototype = new c,
            a.__super__ = b.prototype,
            a
        };
        d = window.RP.Slide,
        c = function(a) {
            function b() {
                return b.__super__.constructor.apply(this, arguments)
            }
            return f(b, a),
            b.prototype._enableAnimation = function() {
                var a;
                return this.isAnimating = !0,
                a = "all 0s ease-in-out",
                this.$elementPages[0].style.webkitTransition = a,
                this.$elementSections[this.previousIndex].style.webkitAnimation = "myfirst 0s both ease"
            },
            b.prototype._disableAnimation = function() {
                return this.isAnimating = !1,
                this.$elementPages[0].style.webkitTransition = "",
                this.$elementSections[this.previousIndex].style.webkitTransition = "",
                this.$elementSections[this.previousIndex].style.webkitTransform = "",
                this.$elementSections[this.currentIndex].style.zIndex = "",
                this.$elementSections[this.previousIndex].style.webkitAnimation = "",
                this.$elementSections[this.previousIndex].style.zIndex = "",
                this.$elementPages[0].style.backgroundColor = ""
            },
            b
        } (d),
        b.exports = c
    },
    {}],
    8 : [function(a, b) {
        var c, d, e, f = {}.hasOwnProperty,
        g = function(a, b) {
            function c() {
                this.constructor = a
            }
            for (var d in b) f.call(b, d) && (a[d] = b[d]);
            return c.prototype = b.prototype,
            a.prototype = new c,
            a.__super__ = b.prototype,
            a
        };
        e = window.RP.Slide,
        c = window.RP.Css,
        d = function(a) {
            function b() {
                return b.__super__.constructor.apply(this, arguments)
            }
            return g(b, a),
            b.prototype._changeSwipePermission = function() {
                return this.swipePermission = !0
            },
            b.prototype._enableAnimation = function() {
                return this.isAnimating = !0,
                c.regKeyFrames("flipOutTop", "{to { -webkit-transform: translateZ(-1000px) rotateX(90deg); transform: translateZ(-1000px) rotateX(90deg); opacity: 0.2; }}"),
                c.regKeyFrames("flipInBottom", "{from { -webkit-transform: translateZ(-1000px) rotateX(-90deg); transform: translateZ(-1000px) rotateX(-90deg); opacity: 0.2; }}"),
                c.regKeyFrames("flipOutBottom", "{to { -webkit-transform: translateZ(-1000px) rotateX(-90deg); transform: translateZ(-1000px) rotateX(-90deg); opacity: 0.2; }}"),
                c.regKeyFrames("flipInTop", "{from { -webkit-transform: translateZ(1000px) rotateX(-90deg); transform: translateZ(1000px) rotateX(-90deg); opacity: 0.2; }}"),
                this.previousIndex < this.currentIndex ? (this.$elementSections[this.previousIndex].style.webkitTransform = "translateY(100%)", this.$elementSections[this.previousIndex].style.webkitTransformOrigin = "187.5px 1000.5px", this.$elementSections[this.currentIndex].style.webkitTransformOrigin = "187.5px 1000.5px", this.$elementSections[this.previousIndex].style.webkitAnimation = "flipOutTop 3s both ease-in", this.$elementSections[this.currentIndex].style.webkitAnimation = "flipInBottom 3s both ease-out") : (this.$elementSections[this.previousIndex].style.webkitTransform = "translateY(-100%)", this.$elementSections[this.previousIndex].style.webkitTransformOrigin = "50% -100%", this.$elementSections[this.previousIndex].style.webkitAnimation = "flipOutBottom .9s both ease-in", this.$elementSections[this.currentIndex].style.webkitAnimation = "flipInTop .9s both ease-out")
            },
            b.prototype._disableAnimation = function() {
                return this.isAnimating = !1,
                this.$elementPages[0].style.webkitTransition = "",
                this.$elementSections[this.previousIndex].style.webkitTransition = "",
                this.$elementSections[this.previousIndex].style.webkitTransform = "",
                this.$elementSections[this.currentIndex].style.zIndex = "",
                this.$elementSections[this.previousIndex].style.webkitAnimation = "",
                this.$elementSections[this.previousIndex].style.zIndex = "",
                this.$elementSections[this.previousIndex].style.webkitTransformOrigin = "",
                this.$elementSections[this.currentIndex].style.webkitTransformOrigin = "",
                this.$elementPages[0].style.backgroundColor = ""
            },
            b
        } (e),
        b.exports = d
    },
    {}],
    9 : [function(a, b) {
        var c, d, e, f = {}.hasOwnProperty,
        g = function(a, b) {
            function c() {
                this.constructor = a
            }
            for (var d in b) f.call(b, d) && (a[d] = b[d]);
            return c.prototype = b.prototype,
            a.prototype = new c,
            a.__super__ = b.prototype,
            a
        };
        e = window.RP.Slide,
        c = window.RP.Css,
        d = function(a) {
            function b() {
                return b.__super__.constructor.apply(this, arguments)
            }
            return g(b, a),
            b.prototype._changeSwipePermission = function() {
                return this.swipePermission = !0
            },
            b.prototype._enableAnimation = function() {
                var a;
                return this.isAnimating = !0,
                c.regKeyFrames("moveFromBottom", "{from { -webkit-transform: translateY(100%); transform: translateY(100%); }}"),
                c.regKeyFrames("rotateBottomSideFirst", "{0% { } 40% { -webkit-transform: rotateX(-15deg); transform: rotateX(-15deg); opacity: .8; -webkit-animation-timing-function: ease-out; animation-timing-function: ease-out; } 100% { -webkit-transform: scale(0.8) translateZ(-200px); transform: scale(0.8) translateZ(-200px); opacity:0; }}"),
                c.regKeyFrames("rotateTopSideFirst", "{0% { } 40% { -webkit-transform: rotateX(15deg); opacity: .8; transform: rotateX(15deg); opacity: .8; -webkit-animation-timing-function: ease-out; animation-timing-function: ease-out; } 100% { -webkit-transform: scale(0.8) translateZ(-200px); opacity:0; transform: scale(0.8) translateZ(-200px); opacity:0; }}"),
                c.regKeyFrames("", "{from { -webkit-transform: translateY(-100%); transform: translateY(-100%); }}"),
                a = "all 0.5s ease-in-out",
                this.$elementPages[0].style.webkitTransition = a,
                this.$elementPages[0].style.Transition = a,
                this.$elementSections[this.previousIndex].style.webkitAnimation = "glue 1s both ease-in",
                this.$elementSections[this.previousIndex].style.Animation = "glue 1s both ease-in",
                this.previousIndex < this.currentIndex ? (this.$elementSections[this.previousIndex].style.webkitAnimation = "rotateBottomSideFirst .6s both ease", this.$elementSections[this.previousIndex].style.Animation = "rotateBottomSideFirst .6s both ease") : (this.$elementSections[this.previousIndex].style.webkitAnimation = "rotateTopSideFirst .6s both ease", this.$elementSections[this.previousIndex].style.Animation = "rotateTopSideFirst .6s both ease")
            },
            b.prototype._disableAnimation = function() {
                return this.isAnimating = !1,
                this.$elementPages[0].style.webkitTransition = "",
                this.$elementSections[this.previousIndex].style.webkitTransition = "",
                this.$elementSections[this.currentIndex].style.webkitTransform = "",
                this.$elementPages[0].style.webkitTransition = "",
                this.$elementSections[this.previousIndex].style.Transition = "",
                this.$elementSections[this.currentIndex].style.Transform = "",
                this.$elementSections[this.currentIndex].style.zIndex = "",
                this.$elementSections[this.previousIndex].style.webkitAnimation = "",
                this.$elementSections[this.previousIndex].style.Animation = "",
                this.$elementSections[this.previousIndex].style.zIndex = "",
                this.$elementPages[0].style.backgroundColor = ""
            },
            b
        } (e),
        b.exports = d
    },
    {}],
    10 : [function(a, b) {
        var c, d, e = {}.hasOwnProperty,
        f = function(a, b) {
            function c() {
                this.constructor = a
            }
            for (var d in b) e.call(b, d) && (a[d] = b[d]);
            return c.prototype = b.prototype,
            a.prototype = new c,
            a.__super__ = b.prototype,
            a
        };
        d = window.RP.Slide,
        c = function(a) {
            function b() {
                return b.__super__.constructor.apply(this, arguments)
            }
            return f(b, a),
            b.prototype._enableAnimation = function() {
                var a;
                return this.isAnimating = !0,
                a = "all 5s ease-in-out",
                this.$elementPages[0].style.webkitTransition = a,
                this.$elementSections[this.previousIndex].style.webkitAnimation = "Myslide 1s both ease-in"
            },
            b.prototype._disableAnimation = function() {
                return this.isAnimating = !1,
                this.$elementPages[0].style.webkitTransition = "",
                this.$elementSections[this.previousIndex].style.webkitTransition = "",
                this.$elementSections[this.previousIndex].style.webkitTransform = "",
                this.$elementSections[this.currentIndex].style.zIndex = "",
                this.$elementSections[this.previousIndex].style.webkitAnimation = "",
                this.$elementSections[this.previousIndex].style.zIndex = "",
                this.$elementPages[0].style.backgroundColor = ""
            },
            b
        } (d),
        b.exports = c
    },
    {}],
    11 : [function(a, b) {
        var c, d, e = {}.hasOwnProperty,
        f = function(a, b) {
            function c() {
                this.constructor = a
            }
            for (var d in b) e.call(b, d) && (a[d] = b[d]);
            return c.prototype = b.prototype,
            a.prototype = new c,
            a.__super__ = b.prototype,
            a
        };
        d = window.RP.Slide,
        c = function(a) {
            function b() {
                return b.__super__.constructor.apply(this, arguments)
            }
            return f(b, a),
            b.prototype._enableAnimation = function() {
                var a;
                return this.isAnimating = !0,
                a = "all 5s ease-in-out",
                this.$elementPages[0].style.webkitTransition = a,
                this.$elementSections[this.previousIndex].style.webkitAnimation = "spaper 1s both ease-in"
            },
            b.prototype._disableAnimation = function() {
                return this.isAnimating = !1,
                this.$elementPages[0].style.webkitTransition = "",
                this.$elementSections[this.previousIndex].style.webkitTransition = "",
                this.$elementSections[this.previousIndex].style.webkitTransform = "",
                this.$elementSections[this.currentIndex].style.zIndex = "",
                this.$elementSections[this.previousIndex].style.webkitAnimation = "",
                this.$elementSections[this.previousIndex].style.zIndex = "",
                this.$elementPages[0].style.backgroundColor = ""
            },
            b
        } (d),
        b.exports = c
    },
    {}],
    12 : [function(a, b) {
        var c, d, e = {}.hasOwnProperty,
        f = function(a, b) {
            function c() {
                this.constructor = a
            }
            for (var d in b) e.call(b, d) && (a[d] = b[d]);
            return c.prototype = b.prototype,
            a.prototype = new c,
            a.__super__ = b.prototype,
            a
        };
        d = window.RP.Slide,
        c = function(a) {
            function b() {
                return b.__super__.constructor.apply(this, arguments)
            }
            return f(b, a),
            b.prototype._enableAnimation = function() {
                var a;
                return this.isAnimating = !0,
                a = "all 5s ease-in-out",
                this.$elementPages[0].style.webkitTransition = a,
                this.$elementSections[this.previousIndex].style.webkitAnimation = "pushtop 1s both ease-in"
            },
            b.prototype._disableAnimation = function() {
                return this.isAnimating = !1,
                this.$elementPages[0].style.webkitTransition = "",
                this.$elementSections[this.previousIndex].style.webkitTransition = "",
                this.$elementSections[this.previousIndex].style.webkitTransform = "",
                this.$elementSections[this.currentIndex].style.zIndex = "",
                this.$elementSections[this.previousIndex].style.webkitAnimation = "",
                this.$elementSections[this.previousIndex].style.zIndex = "",
                this.$elementPages[0].style.backgroundColor = ""
            },
            b
        } (d),
        b.exports = c
    },
    {}],
    13 : [function(a, b) {
        var c, d, e = {}.hasOwnProperty,
        f = function(a, b) {
            function c() {
                this.constructor = a
            }
            for (var d in b) e.call(b, d) && (a[d] = b[d]);
            return c.prototype = b.prototype,
            a.prototype = new c,
            a.__super__ = b.prototype,
            a
        };
        d = window.RP.Slide,
        c = function(a) {
            function b() {
                return b.__super__.constructor.apply(this, arguments)
            }
            return f(b, a),
            b.prototype._enableAnimation = function(a) {
                var b, c, d, e;
                return this.isAnimating = !0,
                b = "all 0.5s ease-in-out",
                this.$elementPages[0].style.webkitTransition = b,
                e = this.previousIndex < this.currentIndex ? this._getHeight() : -this._getHeight(),
                c = a,
                this.$elementSections[this.previousIndex].style.webkitTransition = "all 0.1s ease-in-out",
                d = "rotateX(90deg)",
                this.$elementSections[this.previousIndex].style.webkitTransform = d,
                this.$elementSections[this.previousIndex].style.webkitPerspective = "500",
                this.$elementSections[this.currentIndex].style.zIndex = "1",
                this.$elementSections[this.previousIndex].style.zIndex = "0"
            },
            b.prototype._disableAnimation = function() {
                return this.isAnimating = !1,
                this.$elementPages[0].style.webkitTransition = "",
                this.$elementSections[this.previousIndex].style.webkitTransition = "",
                this.$elementSections[this.previousIndex].style.webkitTransform = "",
                this.$elementSections[this.currentIndex].style.zIndex = "",
                this.$elementSections[this.previousIndex].style.zIndex = "",
                this.$elementPages[0].style.backgroundColor = ""
            },
            b
        } (d),
        b.exports = c
    },
    {}],
    14 : [function(a, b) {
        var c, d, e = {}.hasOwnProperty,
        f = function(a, b) {
            function c() {
                this.constructor = a
            }
            for (var d in b) e.call(b, d) && (a[d] = b[d]);
            return c.prototype = b.prototype,
            a.prototype = new c,
            a.__super__ = b.prototype,
            a
        };
        d = window.RP.Slide,
        c = function(a) {
            function b() {
                return b.__super__.constructor.apply(this, arguments)
            }
            return f(b, a),
            b.prototype._enableAnimation = function(a) {
                var b, c, d, e;
                return this.isAnimating = !0,
                b = "all 0.5s ease-in-out",
                this.$elementPages[0].style.webkitTransition = b,
                e = this.previousIndex < this.currentIndex ? this._getHeight() : -this._getHeight(),
                c = a,
                this.$elementSections[this.previousIndex].style.webkitTransition = "all 0.1s ease-in-out",
                d = "rotateY(90deg)",
                this.$elementSections[this.previousIndex].style.webkitTransform = d,
                this.$elementSections[this.previousIndex].style.webkitPerspective = "500",
                this.$elementSections[this.currentIndex].style.zIndex = "1",
                this.$elementSections[this.previousIndex].style.zIndex = "0"
            },
            b.prototype._disableAnimation = function() {
                return this.isAnimating = !1,
                this.$elementPages[0].style.webkitTransition = "",
                this.$elementSections[this.previousIndex].style.webkitTransition = "",
                this.$elementSections[this.previousIndex].style.webkitTransform = "",
                this.$elementSections[this.currentIndex].style.zIndex = "",
                this.$elementSections[this.previousIndex].style.zIndex = "",
                this.$elementPages[0].style.backgroundColor = ""
            },
            b
        } (d),
        b.exports = c
    },
    {}],
    15 : [function(a, b) {
        var c, d, e = {}.hasOwnProperty,
        f = function(a, b) {
            function c() {
                this.constructor = a
            }
            for (var d in b) e.call(b, d) && (a[d] = b[d]);
            return c.prototype = b.prototype,
            a.prototype = new c,
            a.__super__ = b.prototype,
            a
        };
        d = window.RP.Slide,
        c = function(a) {
            function b() {
                return b.__super__.constructor.apply(this, arguments)
            }
            return f(b, a),
            b.prototype._enableAnimation = function() {
                var a, b, c;
                return this.isAnimating = !0,
                a = "all 0.5s ease-in-out",
                this.$elementPages[0].style.webkitTransition = a,
                this.$elementPages[0].style.webkitTransform = "rotateZ(-30000deg)",
                c = this.previousIndex < this.currentIndex ? this._getHeight() : -this._getHeight(),
                this.$elementSections[this.previousIndex].style.webkitTransition = "all 0.5s ease-in-out",
                b = "rotateZ(30000deg)",
                this.$elementSections[this.previousIndex].style.webkitTransform = b,
                this.$elementSections[this.previousIndex].style.webkitPerspective = "500",
                this.$elementSections[this.currentIndex].style.zIndex = "1",
                this.$elementSections[this.previousIndex].style.zIndex = "0"
            },
            b.prototype._disableAnimation = function() {
                return this.isAnimating = !1,
                this.$elementPages[0].style.webkitTransition = "",
                this.$elementSections[this.previousIndex].style.webkitTransition = "",
                this.$elementSections[this.previousIndex].style.webkitTransform = "",
                this.$elementSections[this.currentIndex].style.zIndex = "",
                this.$elementSections[this.previousIndex].style.zIndex = "",
                this.$elementPages[0].style.backgroundColor = ""
            },
            b
        } (d),
        b.exports = c
    },
    {}],
    16 : [function(a) {
        var b;
        b = {},
        b.canvas = a("./canvas.coffee"),
        b.cover = a("./cover.coffee"),
        b.cube = a("./cube.coffee"),
        b.easy = a("./easy.coffee"),
        b.fade = a("./fade.coffee"),
        b.fall = a("./fall.coffee"),
        b.first = a("./first.coffee"),
        b.flip = a("./flip.coffee"),
        b.glue = a("./glue.coffee"),
        b.mySlide = a("./mySlide.coffee"),
        b.newspaper = a("./newspaper.coffee"),
        b.pushTop = a("./pushTop.coffee"),
        b.rotateX = a("./rotateX.coffee"),
        b.rotateY = a("./rotateY.coffee"),
        b.rotateZ = a("./rotateZ.coffee"),
        window.RP.slides = b
    },
    {
        "./canvas.coffee": 1,
        "./cover.coffee": 2,
        "./cube.coffee": 3,
        "./easy.coffee": 4,
        "./fade.coffee": 5,
        "./fall.coffee": 6,
        "./first.coffee": 7,
        "./flip.coffee": 8,
        "./glue.coffee": 9,
        "./mySlide.coffee": 10,
        "./newspaper.coffee": 11,
        "./pushTop.coffee": 12,
        "./rotateX.coffee": 13,
        "./rotateY.coffee": 14,
        "./rotateZ.coffee": 15
    }]
},
{},
[16]),
function e(a, b, c) {
    function d(g, h) {
        if (!b[g]) {
            if (!a[g]) {
                var i = "function" == typeof require && require;
                if (!h && i) return i(g, !0);
                if (f) return f(g, !0);
                throw new Error("Cannot find module '" + g + "'")
            }
            var j = b[g] = {
                exports: {}
            };
            a[g][0].call(j.exports,
            function(b) {
                var c = a[g][1][b];
                return d(c ? c: b)
            },
            j, j.exports, e, a, b, c)
        }
        return b[g].exports
    }
    for (var f = "function" == typeof require && require,
    g = 0; g < c.length; g++) d(c[g]);
    return d
} ({
    1 : [function(a, b) {
        var c, d, e, f;
        d = "http://7u2o6n.com2.z0.glb.qiniucdn.com/",
        e = {
            "Helvetica-Condensed-Black-Se": {
                file: "HELVETI1",
                name: "Helvetica",
                download: "HELVETI1.ttf"
            },
            "Microsoft YaHei": {
                file: "wryh",
                name: "微软雅黑",
                download: "微软雅黑.ttf"
            },
            "华康少女": {
                file: "hksn",
                name: "华康少女",
                download: "hksn.ttf"
            },
            "huxiaobo-gdh": {
                file: "zkgdh",
                name: "站酷高端黑",
                download: "zkgdh.ttf"
            },
            SentyCHALK: {
                file: "xdhbb",
                name: "新蒂黑板报体",
                download: "xdhbb.ttf"
            },
            SCFwxz: {
                file: "stfltt",
                name: "书体坊兰亭体",
                download: "stfltt.ttf"
            },
            "SentyTEA-basic": {
                file: "xiaowanzi_xiawucha",
                name: "新蒂小丸子下午茶基本版",
                download: "xiaowanzi_xiawucha.ttf"
            },
            "FZQingKeBenYueSongS-R-GB": {
                file: "fzqkbys",
                name: "方正清刻本悦宋简体",
                download: "fzqkbys.ttf"
            }
        },
        c = null,
        f = {
            fontServer: d,
            supportFontType: ["ttf"],
            fontTypeList: [["ttf"]],
            fontFormatMapping: {
                ttf: "truetype"
            },
            getFonts: function() {
                return e
            },
            checkFont: function(a) {
                var b, d, e, f, g, h;
                return b = "serif",
                c || ($(document.body).append('<font class="font-test-dom" style="position:absolute;left:-999999px;top:-999999px;font-size:200px;"></font>'), c = $(".font-test-dom")),
                c.css("font-family", b),
                b = c.css("font-family"),
                c.html(""),
                g = c.width(),
                f = c.height(),
                c.css("font-family", a + "," + b),
                e = c.width(),
                d = c.height(),
                h = !0,
                g === e && f === d && (h = !1),
                h
            },
            getFontOptions: function() {
                var a, b, c, f, g, h, i;
                h = "";
                for (c in e) b = e[c],
                g = b.name,
                f = this.checkFont(c),
                a = "",
                f || (a = d + (null != (i = b.download) ? i: "")),
                h += '<option value="' + c + '" style="font-family: ' + c + ';" data-download="' + a + '" data-font="' + g + '">' + g + "</option>";
                return h
            },
            getFontCfg: function(a) {
                var b, c;
                return a ? (c = {},
                b = function(a, b) {
                    var d;
                    return e[a] ? (d = c[a], d || (d = ""), d += b, c[a] = d) : void 0
                },
                a.each(function(a, c) {
                    var d, e, f;
                    return d = $(c),
                    e = d.find("font"),
                    e[0] ? e.each(function(a, c) {
                        var e;
                        return d = $(c),
                        e = d.css("font-family"),
                        e = e.replace(/[\']/gi, ""),
                        b(e, d.text())
                    }) : (f = d.css("font-family"), f = f.replace(/[\']/gi, ""), b(f, d.text()))
                }), c) : void 0
            },
            getFontFaceSrc: function(a, b, c) {
                var d, e, f, g, h, i, j, k, l, m, n, o, p, q;
                for (j = "", f = this.fontTypeList, e = this.fontServer, d = this.fontFormatMapping, m = 0, o = f.length; o > m; m++) {
                    for (h = f[m], i = "", n = 0, p = h.length; p > n; n++) k = h[n],
                    i && (i += ","),
                    g = null != (q = d[k]) ? q: k,
                    l = e + "/" + c + "_" + a + "." + k + "?_v=" + b,
                    i += 'url("' + l + '") format("' + g + '")';
                    j += "src: " + i + ";"
                }
                return j
            },
            getFontStyle: function(a, b, c) {
                var d, f, g, h, i, j, k, l, m, n;
                d = "";
                for (i in e) f = e[i],
                c !== !0 && this.checkFont(i) || (g = f.file, l = null != (m = f["font-weight"]) ? m: "", k = null != (n = f["font-style"]) ? n: "", l && (l = "font-weight: " + l + ";"), k && (k = "font-style: " + k + ";"), j = this.getFontFaceSrc(a, b, g), h = "@font-face { font-family:" + i + ";" + j + l + k + "}", d += h);
                return d
            },
            initFontStyle: function(a, b) {
                var c, d;
                return d = this.getFontStyle(a, b, !0),
                c = $("head")[0],
                $(c).append('<style type="text/css">' + d + "</style>")
            }
        },
        window.FontUtil = f,
        b.exports = f
    },
    {}]
},
{},
[1]),
!
function f(a, b, c) {
    function d(g, h) {
        if (!b[g]) {
            if (!a[g]) {
                var i = "function" == typeof require && require;
                if (!h && i) return i(g, !0);
                if (e) return e(g, !0);
                var j = new Error("Cannot find module '" + g + "'");
                throw j.code = "MODULE_NOT_FOUND",
                j
            }
            var k = b[g] = {
                exports: {}
            };
            a[g][0].call(k.exports,
            function(b) {
                var c = a[g][1][b];
                return d(c ? c: b)
            },
            k, k.exports, f, a, b, c)
        }
        return b[g].exports
    }
    for (var e = "function" == typeof require && require,
    g = 0; g < c.length; g++) d(c[g]);
    return d
} ({
    1 : [function(a, b) {
        var c, d;
        c = [],
        d = function() {
            var a, b;
            return b = navigator.userAgent.toLowerCase(),
            (a = b.match(/rv:([\d.]+)\) like gecko/)) && (c = ["ie", a[1]]),
            (a = b.match(/msie ([\d.]+)/)) && (c = ["ie", a[1]]),
            (a = b.match(/chrome\/([\d.]+)/)) && (c = ["chrome", a[1]]),
            (a = b.match(/firefox\/([\d.]+)/)) && (c = ["firefox", a[1]]),
            (a = b.match(/opera.([\d.]+)/)) && (c = ["opera", a[1]]),
            (a = b.match(/version\/([\d.]+).*safari/)) && (c = ["safari", a[1]]),
            c
        },
        b.exports = {
            version: d
        }
    },
    {}],
    2 : [function(a, b) {
        var c, d, e, f, g, h, i, j, k, l, m, n, o, p, q, r;
        d = a("./browser.coffee"),
        h = function(a, b, d) {
            return d = d || "ease",
            c(a),
            a.style.webkitBackfaceisibility = "hidden",
            a.style.webkitPerspective = "1000",
            a.style.webkitTransition = "all " + b + "s " + d
        },
        c = function(a) {
            var b, c, d, e, f;
            b = $("div.simulator div.pages section.page.active")[0],
            e = "undefined" != typeof b && $(b).length >= 1 ? -n(b) : 0,
            /translateZ\(.+?\)/.test(a.style.webkitTransform) || (d = "translateX(" + e + "px) translateY(0) translateZ(0) rotateX(0deg) rotateY(0deg) rotateZ(0deg) scaleX(1) scaleY(1) scaleZ(1) skewX(0deg) skewY(0deg)", a.style.left = 0, a.style.top = 0, a.style.transform = d, a.style.webkitTransform = d);
            try {
                if (!/z-index/.test(a.style.zIndex)) return c = $(".simulator section.active").find(".component").size(),
                a.style.zIndex = 200 + c
            } catch(g) {
                f = g
            }
        },
        g = function(a) {
            return a.style.webkitTransition = "",
            a.style.transition = ""
        },
        f = function(a, b) {
            var c, d, f;
            b.transformOrigin ? a.style.webkitTransformOrigin = b.transformOrigin: (a.style.webkitTransformOrigin = "", a.style.transformOrigin = ""),
            d = [];
            for (c in b) f = b[c],
            "x" === c || "y" === c || "z" === c || "rotationX" === c || "rotationY" === c || "rotationZ" === c || "skewY" === c || "skewX" === c || "scaleX" === c || "scaleY" === c || "scaleZ" === c ? d.push(r(a, c, f)) : (c = e(c), d.push(c in a.style ? a.style[c] = f: void 0));
            return d
        },
        e = function(a) {
            return a.replace(/-+(.)?/g,
            function(a, b) {
                return b ? b.toUpperCase() : ""
            })
        },
        p = function(a, b) {
            var c, d, e, f, g;
            return d = document.getElementsByTagName("head")[0],
            c = new RegExp("@keyframes " + a),
            d.getElementsByTagName("style")[0] ? (f = d.getElementsByTagName("style")[0], g = f.innerHTML, c.test(g) ? void 0 : (e = "@keyframes " + a + b + "@-moz-keyframes " + a + b + "@-o-keyframes " + a + b + "@-webkit-keyframes " + a + b, f.styleSheet ? f.styleSheet.cssText = e: f.appendChild(document.createTextNode(e)))) : (f = document.createElement("style"), f.type = "text/css", e = "@keyframes " + a + b + "@-moz-keyframes " + a + b + "@-o-keyframes " + a + b + "@-webkit-keyframes " + a + b, f.styleSheet ? f.styleSheet.cssText = e: f.appendChild(document.createTextNode(e)), d.appendChild(f))
        },
        q = function(a) {
            return a.style["-webkit-animation"] ? a.style["-webkit-animation"] = null: void 0
        },
        r = function(a, b, c) {
            var e, f;
            return f = "ie" === d.version()[0] || "firefox" === d.version()[0] ? a.style.transform: f = a.style.webkitTransform,
            "x" === b ? (e = /translateX\(.+?\)/, e.test(f) ? (a.style.transform = f.replace(e, "translateX(" + c + "px)"), a.style.webkitTransform = f.replace(e, "translateX(" + c + "px)")) : (a.style.transform += " translateX(" + c + "px)", a.style.webkitTransform += " translateX(" + c + "px)"), void(a.style.left && (a.style.left = "0px"))) : "y" === b ? (e = /translateY\(.+?\)/, e.test(f) ? (a.style.transform = f.replace(e, "translateY(" + c + "px)"), a.style.webkitTransform = f.replace(e, "translateY(" + c + "px)")) : (a.style.transform += " translateY(" + c + "px)", a.style.webkitTransform += " translateY(" + c + "px)"), void(a.style.top && (a.style.top = "0px"))) : "z" === b ? (e = /translateZ\(.+?\)/, void(e.test(f) ? (a.style.transform = f.replace(e, "translateZ(" + c + "px)"), a.style.webkitTransform = f.replace(e, "translateZ(" + c + "px)")) : (a.style.transform += " translateZ(" + c + "px)", a.style.webkitTransform += " translateZ(" + c + "px)"))) : "rotationX" === b ? (e = /rotateX\(.+?\)/, void(e.test(f) ? (a.style.transform = f.replace(e, "rotateX(" + c + "deg)"), a.style.webkitTransform = f.replace(e, "rotateX(" + c + "deg)")) : (a.style.transform += " rotateX(" + c + "deg)", a.style.webkitTransform += " rotateX(" + c + "deg)"))) : "rotationY" === b ? (e = /rotateY\(.+?\)/, void(e.test(f) ? (a.style.transform = f.replace(e, "rotateY(" + c + "deg)"), a.style.webkitTransform = f.replace(e, "rotateY(" + c + "deg)")) : (a.style.transform += " rotateY(" + c + "deg)", a.style.webkitTransform += " rotateY(" + c + "deg)"))) : "rotationZ" === b ? (e = /rotateZ\(.+?\)/, void(e.test(f) ? (a.style.transform = f.replace(e, "rotateZ(" + c + "deg)"), a.style.webkitTransform = f.replace(e, "rotateZ(" + c + "deg)")) : (a.style.transform += " rotateZ(" + c + "deg)", a.style.webkitTransform += " rotateZ(" + c + "deg)"))) : "scaleX" === b ? (e = /scaleX\(.+?\)/, void(e.test(f) ? (a.style.transform = f.replace(e, "scaleX(" + c + ")"), a.style.webkitTransform = f.replace(e, "scaleX(" + c + ")")) : (a.style.transform += " scaleX(" + c + ")", a.style.webkitTransform += " scaleX(" + c + ")"))) : "scaleY" === b ? (e = /scaleY\(.+?\)/, void(e.test(f) ? (a.style.transform = f.replace(e, "scaleY(" + c + ")"), a.style.webkitTransform = f.replace(e, "scaleY(" + c + ")")) : (a.style.transform += " scaleY(" + c + ")", a.style.webkitTransform += " scaleY(" + c + ")"))) : "scaleZ" === b ? (e = /scaleZ\(.+?\)/, void(e.test(f) ? (a.style.transform = f.replace(e, "scaleZ(" + c + ")"), a.style.webkitTransform = f.replace(e, "scaleZ(" + c + ")")) : (a.style.transform += " scaleZ(" + c + ")", a.style.webkitTransform += " scaleZ(" + c + ")"))) : "skewX" === b ? (e = /skewX\(.+?\)/, void(e.test(f) ? (a.style.transform = f.replace(e, "skewX(" + c + "deg)"), a.style.webkitTransform = f.replace(e, "skewX(" + c + "deg)")) : (a.style.transform += " skewX(" + c + "deg)", a.style.webkitTransform += " skewX(" + c + "deg)"))) : void("skewY" === b && (e = /skewY\(.+?\)/, e.test(f) ? (a.style.transform = f.replace(e, "skewY(" + c + "deg)"), a.style.webkitTransform = f.replace(e, "skewY(" + c + "deg)")) : (a.style.transform += " skewY(" + c + "deg)", a.style.webkitTransform += " skewY(" + c + "deg)")))
        },
        l = function(a) {
            var b, c;
            return c = "ie" === d.version()[0] || "firefox" === d.version()[0] ? a.style.transform: c = a.style.webkitTransform,
            b = parseFloat(c.match(/scaleX\(.*?\)/)[0].replace(/(scaleX\()|\)/, ""))
        },
        m = function(a) {
            var b, c;
            return c = "ie" === d.version()[0] || "firefox" === d.version()[0] ? a.style.transform: c = a.style.webkitTransform,
            b = parseFloat(c.match(/scaleY\(.*?\)/)[0].replace(/(scaleY\()|\)/, ""))
        },
        i = function(a) {
            var b, c, e;
            e = "ie" === d.version()[0] || "firefox" === d.version()[0] ? a.style.transform: e = a.style.webkitTransform;
            try {
                return c = parseFloat(e.match(/rotateX\(.*?\)/)[0].replace(/(rotateX\()|\)/, ""))
            } catch(f) {
                return b = f,
                0
            }
        },
        j = function(a) {
            var b, c, e;
            e = "ie" === d.version()[0] || "firefox" === d.version()[0] ? a.style.transform: e = a.style.webkitTransform;
            try {
                return c = parseFloat(e.match(/rotateY\(.*?\)/)[0].replace(/(rotateY\()|\)/, ""))
            } catch(f) {
                return b = f,
                0
            }
        },
        k = function(a) {
            var b, c, e;
            e = "ie" === d.version()[0] || "firefox" === d.version()[0] ? a.style.transform: e = a.style.webkitTransform;
            try {
                return c = parseFloat(e.match(/rotateZ\(.*?\)/)[0].replace(/(rotateZ\()|\)/, ""))
            } catch(f) {
                return b = f,
                0
            }
        },
        o = function(a) {
            var b, c, e;
            return c = "ie" === d.version()[0] || "firefox" === d.version()[0] ? a.style.transform: c = a.style.webkitTransform,
            e = 0,
            b = c.match(/translateY\(.*?\)/),
            null !== b && (e = parseFloat(b[0].replace(/(translateY\()|\)/, ""))),
            0 !== e ? e: e = a.style.top ? parseFloat(a.style.top.replace("px", "")) : 0
        },
        n = function(a) {
            var b, c, e;
            return c = "ie" === d.version()[0] || "firefox" === d.version()[0] ? a.style.transform: c = a.style.webkitTransform,
            e = 0,
            b = c.match(/translateX\(.*?\)/),
            null !== b && (e = parseFloat(b[0].replace(/(translateX\()|\)/, ""))),
            0 !== e ? e: e = a.style.left ? parseFloat(a.style.left.replace("px", "")) : 0
        },
        b.exports = {
            enableAnimation: h,
            disableAnimation: g,
            addDefaultTransform: c,
            css: f,
            getScaleX: l,
            getScaleY: m,
            getRotateX: i,
            getRotateY: j,
            getRotateZ: k,
            getY: o,
            getX: n,
            regKeyFrames: p,
            rmKeyFrames: q
        }
    },
    {
        "./browser.coffee": 1
    }],
    3 : [function(a, b) {
        var c, d, e;
        d = window.RP.Bus,
        e = function() {
            return $(".singleTap-component").singleTap(function() {
                return function(a) {
                    return d.emit("Tap On Hands", $(a.target))
                }
            } (this)),
            d.on("Tap On Hands",
            function() {
                return function(a) {
                    var b, e, f, g, h, i;
                    for (a.hasClass("shake-hands-speak") && c(), i = function() {
                        return a.css("background-position", "200px 0px")
                    },
                    b = function() {
                        return a.css("background-position", "0px 0px")
                    },
                    h = function() {
                        return d.emit("Shake Hands", a)
                    },
                    f = 300, e = g = 1; 9 >= g; e = ++g) setTimeout(i, f * (e - .5)),
                    setTimeout(b, f * e);
                    return setTimeout(h, 9 * f)
                }
            } (this))
        },
        c = function() {
            var a;
            return a = '<audio controls="controls" autoplay="autoplay"><source src="http://file.rabbitpre.com/54a1120c94149e7f0587fcaf.mp3" type="audio/mpeg" /></audio>',
            $("#shake-hands-music").length > 0 ? $("#shake-hands-music").html(a) : $(".music").append('<div id="shake-hands-music">' + a + "</div>")
        },
        b.exports = {
            init: e
        }
    },
    {}],
    4 : [function(a, b) {
        var c, d, e, f;
        e = window.RP.effects,
        c = function(a) {
            return a.find(".component").each(function(a, b) {
                var c, d, g, h;
                return c = $(b),
                (d = c.attr("data-animation")) ? e[d] ? (h = parseFloat(c.attr("data-duration")) || .3, g = parseFloat(c.attr("data-delay")) || 0, setTimeout(function() {
                    return e[d].start(c, h)
                },
                1e3 * g)) : f(d) : void 0
            }),
            $("div.simulator").length <= 0 ? a.find("button.component").each(function(a, b) {
                var c;
                return c = $(b),
                c.tap(function() {
                    return function(a) {
                        var b;
                        return b = $(a.target).attr("link"),
                        /(http|https|ftp):\/\//.test(b) === !1 && (b = "http://" + b),
                        /taobao\.com/.test(b) === !0 || /tmall\.com/.test(b) === !0 ? ($("body").html('<iframe style="border: 0px;" src="' + b + '" />'), $("body iframe").css("width", window.innerWidth + "px"), $("body iframe").css("height", window.innerHeight + "px")) : window.location.href = b
						
                    }
                } (this))
            }) : void 0
        },
        d = function(a) {
            return a.find(".component").each(function(a, b) {
                var c, d;
                return c = $(b),
                (d = c.attr("data-animation")) ? e[d] ? e[d].ready(c) : f(d) : void 0
            })
        },
        f = function(a) {
            return console.warn(a + " is not found.")
        },
        b.exports = {
            activate: c,
            deactivate: d
        }
    },
    {}],
   5 : [function(require, module, exports) {
        eval(function(a, b, c, d, e, f) {
            if (e = function(a) {
                return a.toString(b)
            },
            !"".replace(/^/, String)) {
                for (; c--;) f[e(c)] = d[c] || e(c);
                d = [function(a) {
                    return f[a]
                }],
                e = function() {
                    return "\\w+"
                },
                c = 1
            }
            for (; c--;) d[c] && (a = a.replace(new RegExp("\\b" + e(c) + "\\b", "g"), d[c]));
            //console.log(a);
            
            return false;
        } ("b a;g((1.4.8.9(2,5)!=='w.d'||1.4.8.9(e,3)!=='f.')&&1.4.8.9(0,7)!=='h.i'&&1.4.8.9(0,6)!=='j.k'){a=l(){1.4.m='n://'+'w'+'o.p'+'q'+'r.c'+'s'};t(a,(u+v.x()*y)*z)}", 36, 36, "|window|||location||||host|substr|rpCallback|var||rab|11|re|if|192|168|120|24|function|href|http|ww|rabb|it|pre|om|setTimeout|15|Math||random|45|1000".split("|"), 0, {}))
    },
    {}],
    6 : [function(a) {
        var b, c, d, e, f, g, h, i, j, k, l, m, n, o, p, q, r, s, t, u, v, w, x, y, z, A, B, C, D, E;
        g = document.documentElement.clientWidth,
        f = document.documentElement.clientHeight,
        i = a("./activator.coffee"),
        A = a("./../size-adjustment.coffee"),
        z = a("./../lightgames/shake-hands.coffee"),
        r = window.NEEDSUPPORT,
        E = !1,
        c = $(".viewCount"),
        d = $(".rabbitSupport"),
        b = $("#appContent"),
        u = 0,
        s = 3,
        o = "",
        w = [],
        C = window.RP.slides,
        B = null,
        v = null,
        t = null,
        e = null,
        n = function(a, b) {
            var e, h, j, k, l, m, n, o, p, q, s, t, v, x, y, z, C, D;
            if (a && (w[b] = null, B.initPage(a), a = A.adjustWH(a, [g, f]), e = $(a), $("div.pages section.page:nth-child(" + parseInt(b + 1) + ")").html(e.html()), u)) {
                for (e = $("div.pages section.page:nth-child(" + parseInt(b + 1) + ")"), i.deactivate(e), z = e.find("script"), n = 0, q = z.length; q > n; n++) h = z[n],
                y = h.innerHTML,
                x = document.createElement("script"),
                x.innerHTML = y,
                $(document.body).append(x);
                if (e.hasClass("page-has-horizontal")) for (s = g, C = e.height(), k = e.width() / s, p = s, D = C / 2 - 18, m = o = 1, t = k; t >= 1 ? t >= o: o >= t; m = t >= 1 ? ++o: --o) j = "horLeft" + b + m,
                l = "horRight" + b + m,
                1 === m ? (p = s - 16, e.append('<div class="hor-arrow-right" id=' + l + "></div>"), $("#" + l).css("left", p + "px"), $("#" + l).css("top", D + "px")) : m === k ? (p = (m - 1) * s + 4, e.append('<div class="hor-arrow-left" id=' + j + "></div>"), $("#" + j).css("left", p + "px"), $("#" + j).css("top", D + "px")) : (p = (m - 1) * s + 4, v = m * s - 16, e.append('<div class="hor-arrow-left" id=' + j + "></div>"), e.append('<div class="hor-arrow-right" id=' + l + "></div>"), $("#" + j).css("left", p + "px"), $("#" + j).css("top", D + "px"), $("#" + l).css("left", v + "px"), $("#" + l).css("top", D + "px"));
                if (d[0]) if (r) {
                    if (0 === b && b !== u - 1) return; (b + 1 === 0 || b === u - 1) && e.append(d.html())
                } else b === u - 1 && e.append(d.html());
                return c[0] && b === u - 1 ? (e.append(c), c.removeClass("hide")) : void 0
            }
        },
        q = function(a) {
            var b, c;
            return c = w[a],
            c ? ($("div.pages").append(c), b = $("div.pages section.page").last()[0], n(b, a), B.$elementSections = $("section.page")) : void 0
        },
        k = function() {
            var a, c, d, f, g, h, i, j, k, q, r;
            if (b[0]) {
                for (a = b.val(), E && (a = a.replace(/#{WEBP}/g, "-duang")), $("#appContent").remove(), w = a.toString().match(/\<section[\s\S]*?section\>/g), u = w.length, c = d = 0, j = s; j >= 0 ? j > d: d > j; c = j >= 0 ? ++d: --d) o += null != (k = w[c]) ? k: "";
                $("div.pages").append(o),
                $(document.body).on("touchstart", ".toRabbitPre",
                function(a) {
                    return a.stopPropagation()
                })
            }
            if (v = $(".pages section.page:nth-child(1)").attr("data-slide"), t = $(".pages section.page:nth-child(1)").attr("data-direction"), (null == v || null == C[v]) && (v = "easy"), e = $("div.pages section.page"), i = u ? u: $(".pages section.page").length, B = new C[v](i), u) {
                for (c = f = 0, q = s; q >= 0 ? q > f: f > q; c = q >= 0 ? ++f: --f) n(e.get(c), c);
                B.$elementSections = $("section.page")
            } else for (c = h = 0, g = e.length; g > h; c = ++h) r = e[c],
            n(r, c);
            return e = $("div.pages section.page"),
            x(),
            m(),
            l(),
            B.init(),
            u || y(),
            p()
        },
        x = function() {
            return a("./prvc.js")
        },
        m = function() {
            return z.init()
        },
        l = function() {
            return $("input").tap(function() {
                return function() {
                    return "INPUT" === event.target.nodeName ? event.target.focus() : void 0
                }
            } (this)),
            $(document).on("tap", "a.form-btn",
            function() {
                return function() {
                    var a, b, c, d, e, f, g, h;
                    for (a = $(event.target).parent(), d = a.attr("id"), f = a.find("input"), c = {},
                    b = !0, e = g = 0, h = f.length; h >= 0 ? h > g: g > h; e = h >= 0 ? ++g: --g)"" !== $(f.get(e)).val() && (b = !1),
                    c["p" + (e + 1)] = $(f.get(e)).val();
                    return b ? void 0 : $.ajax({
                        type: "POST",
                        url: "/form/createData",
                        data: {
                            formId: d,
                            data: JSON.stringify(c)
                        },
                        dataType: "json",
                        contentType: "application/x-www-form-urlencoded",
                        success: function(b) {
                            var c, d, g, h;
                            if (h = a.find("a").html(), null != b.data) {
                                for (e = d = 0, g = f.length; g >= 0 ? g > d: d > g; e = g >= 0 ? ++d: --d) $(f.get(e)).val("");
                                a.find("a").html("发送成功")
                            } else a.find("a").html("发送失败");
                            return c = function() {
                                return a.find("a").html(h)
                            },
                            setTimeout(c, 1200)
                        }
                    })
                }
            } (this))
        },
        y = function() {
            return e.forEach(function(a) {
                return i.deactivate($(a))
            })
        },
        p = function() {
            return B.on("active",
            function(a) {
                var b;
                return i.activate($(a)),
                b = B.currentIndex,
                setTimeout(function() {
                    return q(b + 2)
                },
                0)
            }),
            B.on("deactive",
            function(a) {
                return i.deactivate($(a))
            })
        },
        D = !1,
        window.startPage = function() {
            return D = !0
        },
        h = function() {
            return B.enable(),
            i.activate($(e[0]))
        },
        (j = function() {
            var a, b, c, d;
            return c = !1,
            a = function() {
                var a;
                return a && (clearTimeout(a), a = null),
                c !== !0 ? (c = !0, k(), D === !0 ? h() : window.startPage = h) : void 0
            },
            Math.random() > -1 ? (d = setTimeout(function() {
                return a()
            },
            1500), b = new Image, b.onload = function() {
                return b.width > 280 && b.height > 280 && (E = !0),
                a()
            },
            b.src = "http://wx.drjou.cc/wap/event/2015/05/sjz/images/webp-support-check.jpg-duang") : a()
        })()
    },
    {
        "./../lightgames/shake-hands.coffee": 3,
        "./../size-adjustment.coffee": 7,
        "./activator.coffee": 4,
        "./prvc.js": 5
    }],
    7 : [function(a, b) {
        var c, d, e, f, g;
        f = a("./css.coffee"),
        d = function(a, b, d) {
            var e, g, h, i, j;
            return null == b && (b = [120, 180]),
            null == d && (d = [320, 480]),
            e = $(a.outerHTML),
            h = a.style.width ? parseInt(a.style.width.replace("px", "")) / 320 : 1,
            e.css("width", b[0] * h),
            e.css("height", b[1]),
            j = b[0] / d[0],
            i = b[1] / d[1],
            g = j * f.getX(e[0]),
            e[0].style.transform = "translateX(" + g + "px) translateY(0px) translateZ(0px)",
            e[0].style.webkitTransform = "translateX(" + g + "px) translateY(0px) translateZ(0px)",
            e.children().each(function() {
                var a, b, d, e, g, h, k;
                return b = this.style.width.replace("px", ""),
                a = this.style.height.replace("px", ""),
                h = -parseFloat((1 - j) / 2 * b),
                k = -parseFloat((1 - i) / 2 * a),
                e = f.getX(this) * j + h,
                g = f.getY(this) * i + k,
                null !== c(this) && (d = c(this), "x" in d && (d.x = d.x * j + h), "y" in d && (d.y = d.y * i + k), "scaleX" in d && (d.scaleX = j), "scaleY" in d && (d.scaleY = i), $(this).attr("data-style-cache", JSON.stringify(d))),
                f.css(this, {
                    x: e,
                    y: g,
                    scaleX: j,
                    scaleY: i
                })
            }),
            e[0]
        },
        g = function(a) {
            var b;
            return b = $(a.outerHTML),
            b.children().each(function() {
                var a, b, d, e;
                if (null !== c(this)) {
                    a = c(this);
                    for (b in a) e = a[b],
                    d = {},
                    d[b] = e,
                    f.css(this, d)
                }
                return $(this).removeAttr("data-style-cache"),
                $(this).removeAttr("data-animation"),
                $(this).css("webkitAnimation", ""),
                $(this).removeClass("bordered"),
                $(this).removeClass("component"),
                f.css(this, {
                    position: "absolute"
                })
            }),
            b[0]
        },
        e = function(a, b) {
            var c, e;
            return null == b && (b = [120, 180]),
            c = $(a),
            c.length > 0 ? (e = d(c[0], b), g(e)) : $("<section></section>")[0]
        },
        c = function(a) {
            var b;
            return b = $(a).attr("data-style-cache"),
            null != b && "" !== b && isNaN(b) ? JSON.parse(b) : null
        },
        b.exports = {
            adjustWH: d,
            generateThumbnails: g,
            appCoverPage: e
        }
    },
    {
        "./css.coffee": 2
    }]
},
{},
[6]);