!(function (t) {
    "object" == typeof exports && "object" == typeof module ? t(require("../../lib/codemirror")) : "function" == typeof define && define.amd ? define(["../../lib/codemirror"], t) : t(CodeMirror);
})(function (T) {
    "use strict";
    var F = "CodeMirror-hint-active";
    function n(t, e) {
        var i;
        (this.cm = t),
            (this.options = e),
            (this.widget = null),
            (this.debounce = 0),
            (this.tick = 0),
            (this.startPos = this.cm.getCursor("start")),
            (this.startLen = this.cm.getLine(this.startPos.line).length - this.cm.getSelection().length),
            this.options.updateOnCursorActivity &&
                t.on(
                    "cursorActivity",
                    ((i = this).activityFunc = function () {
                        i.cursorActivity();
                    })
                );
    }
    (T.showHint = function (t, e, i) {
        if (!e) return t.showHint(i);
        i && i.async && (e.async = !0);
        var n = { hint: e };
        if (i) for (var o in i) n[o] = i[o];
        return t.showHint(n);
    }),
        T.defineExtension("showHint", function (t) {
            t = (function (t, e, i) {
                var n = t.options.hintOptions,
                    o = {};
                for (s in c) o[s] = c[s];
                if (n) for (var s in n) void 0 !== n[s] && (o[s] = n[s]);
                if (i) for (var s in i) void 0 !== i[s] && (o[s] = i[s]);
                o.hint.resolve && (o.hint = o.hint.resolve(t, e));
                return o;
            })(this, this.getCursor("start"), t);
            var e = this.listSelections();
            if (!(1 < e.length)) {
                if (this.somethingSelected()) {
                    if (!t.hint.supportsSelection) return;
                    for (var i = 0; i < e.length; i++) if (e[i].head.line != e[i].anchor.line) return;
                }
                this.state.completionActive && this.state.completionActive.close();
                t = this.state.completionActive = new n(this, t);
                t.options.hint && (T.signal(this, "startCompletion", this), t.update(!0));
            }
        }),
        T.defineExtension("closeHint", function () {
            this.state.completionActive && this.state.completionActive.close();
        });
    var o =
            window.requestAnimationFrame ||
            function (t) {
                return setTimeout(t, 1e3 / 60);
            },
        s = window.cancelAnimationFrame || clearTimeout;
    function M(t) {
        return "string" == typeof t ? t : t.text;
    }
    function O(t, e) {
        for (; e && e != t; ) {
            if ("LI" === e.nodeName.toUpperCase() && e.parentNode == t) return e;
            e = e.parentNode;
        }
    }
    function i(o, t) {
        (this.id = "cm-complete-" + Math.floor(Math.random(1e6))), (this.completion = o), (this.data = t), (this.picked = !1);
        var i = this,
            s = o.cm,
            c = s.getInputField().ownerDocument,
            r = c.defaultView || c.parentWindow,
            l = (this.hints = c.createElement("ul"));
        l.setAttribute("role", "listbox"), l.setAttribute("aria-expanded", "true"), (l.id = this.id);
        var e = o.cm.options.theme;
        (l.className = "CodeMirror-hints " + e), (this.selectedHint = t.selectedHint || 0);
        for (var n = t.list, h = 0; h < n.length; ++h) {
            var a = l.appendChild(c.createElement("li")),
                u = n[h],
                d = "CodeMirror-hint" + (h != this.selectedHint ? "" : " " + F);
            null != u.className && (d = u.className + " " + d),
                (a.className = d),
                h == this.selectedHint && a.setAttribute("aria-selected", "true"),
                (a.id = this.id + "-" + h),
                a.setAttribute("role", "option"),
                u.render ? u.render(a, t, u) : a.appendChild(c.createTextNode(u.displayText || M(u))),
                (a.hintId = h);
        }
        var f = o.options.container || c.body,
            p = s.cursorCoords(o.options.alignWithWord ? t.from : null),
            m = p.left,
            g = p.bottom,
            v = !0,
            y = 0,
            b = 0;
        f !== c.body &&
            ((w = (C = -1 !== ["absolute", "relative", "fixed"].indexOf(r.getComputedStyle(f).position) ? f : f.offsetParent).getBoundingClientRect()),
            (H = c.body.getBoundingClientRect()),
            (y = w.left - H.left - C.scrollLeft),
            (b = w.top - H.top - C.scrollTop)),
            (l.style.left = m - y + "px"),
            (l.style.top = g - b + "px");
        var e = r.innerWidth || Math.max(c.body.offsetWidth, c.documentElement.offsetWidth),
            w = r.innerHeight || Math.max(c.body.offsetHeight, c.documentElement.offsetHeight);
        f.appendChild(l), s.getInputField().setAttribute("aria-autocomplete", "list"), s.getInputField().setAttribute("aria-owns", this.id), s.getInputField().setAttribute("aria-activedescendant", this.id + "-" + this.selectedHint);
        var A,
            H = o.options.moveOnOverlap ? l.getBoundingClientRect() : new DOMRect(),
            C = !!o.options.paddingForScrollbar && l.scrollHeight > l.clientHeight + 1;
        setTimeout(function () {
            A = s.getScrollInfo();
        }),
            0 < H.bottom - w &&
                ((f = H.bottom - H.top),
                0 < p.top - (p.bottom - H.top) - f
                    ? ((l.style.top = (g = p.top - f - b) + "px"), (v = !1))
                    : w < f &&
                      ((l.style.height = w - 5 + "px"),
                      (l.style.top = (g = p.bottom - H.top - b) + "px"),
                      (x = s.getCursor()),
                      t.from.ch != x.ch && ((p = s.cursorCoords(x)), (l.style.left = (m = p.left - y) + "px"), (H = l.getBoundingClientRect()))));
        var k,
            x = H.right - e;
        if ((C && (x += s.display.nativeBarWidth), 0 < x && (H.right - H.left > e && ((l.style.width = e - 5 + "px"), (x -= H.right - H.left - e)), (l.style.left = (m = p.left - x - y) + "px")), C))
            for (var S = l.firstChild; S; S = S.nextSibling) S.style.paddingRight = s.display.nativeBarWidth + "px";
        s.addKeyMap(
            (this.keyMap = (function (t, n) {
                var o = {
                    Up: function () {
                        n.moveFocus(-1);
                    },
                    Down: function () {
                        n.moveFocus(1);
                    },
                    PageUp: function () {
                        n.moveFocus(1 - n.menuSize(), !0);
                    },
                    PageDown: function () {
                        n.moveFocus(n.menuSize() - 1, !0);
                    },
                    Home: function () {
                        n.setFocus(0);
                    },
                    End: function () {
                        n.setFocus(n.length - 1);
                    },
                    Enter: n.pick,
                    Tab: n.pick,
                    Esc: n.close,
                };
                /Mac/.test(navigator.platform) &&
                    ((o["Ctrl-P"] = function () {
                        n.moveFocus(-1);
                    }),
                    (o["Ctrl-N"] = function () {
                        n.moveFocus(1);
                    }));
                var e = t.options.customKeys,
                    s = e ? {} : o;
                function i(t, e) {
                    var i =
                        "string" != typeof e
                            ? function (t) {
                                  return e(t, n);
                              }
                            : o.hasOwnProperty(e)
                            ? o[e]
                            : e;
                    s[t] = i;
                }
                if (e) for (var c in e) e.hasOwnProperty(c) && i(c, e[c]);
                var r = t.options.extraKeys;
                if (r) for (var c in r) r.hasOwnProperty(c) && i(c, r[c]);
                return s;
            })(o, {
                moveFocus: function (t, e) {
                    i.changeActive(i.selectedHint + t, e);
                },
                setFocus: function (t) {
                    i.changeActive(t);
                },
                menuSize: function () {
                    return i.screenAmount();
                },
                length: n.length,
                close: function () {
                    o.close();
                },
                pick: function () {
                    i.pick();
                },
                data: t,
            }))
        ),
            o.options.closeOnUnfocus &&
                (s.on(
                    "blur",
                    (this.onBlur = function () {
                        k = setTimeout(function () {
                            o.close();
                        }, 100);
                    })
                ),
                s.on(
                    "focus",
                    (this.onFocus = function () {
                        clearTimeout(k);
                    })
                )),
            s.on(
                "scroll",
                (this.onScroll = function () {
                    var t = s.getScrollInfo(),
                        e = s.getWrapperElement().getBoundingClientRect();
                    A = A || s.getScrollInfo();
                    var i = g + A.top - t.top,
                        n = i - (r.pageYOffset || (c.documentElement || c.body).scrollTop);
                    if ((v || (n += l.offsetHeight), n <= e.top || n >= e.bottom)) return o.close();
                    (l.style.top = i + "px"), (l.style.left = m + A.left - t.left + "px");
                })
            ),
            T.on(l, "dblclick", function (t) {
                t = O(l, t.target || t.srcElement);
                t && null != t.hintId && (i.changeActive(t.hintId), i.pick());
            }),
            T.on(l, "click", function (t) {
                t = O(l, t.target || t.srcElement);
                t && null != t.hintId && (i.changeActive(t.hintId), o.options.completeOnSingleClick && i.pick());
            }),
            T.on(l, "mousedown", function () {
                setTimeout(function () {
                    s.focus();
                }, 20);
            });
        C = this.getSelectedHintRange();
        return (0 === C.from && 0 === C.to) || this.scrollToActive(), T.signal(t, "select", n[this.selectedHint], l.childNodes[this.selectedHint]), !0;
    }
    function r(t, e, i, n) {
        t.async ? t(e, n, i) : (i = t(e, i)) && i.then ? i.then(n) : n(i);
    }
    (n.prototype = {
        close: function () {
            this.active() &&
                ((this.cm.state.completionActive = null),
                (this.tick = null),
                this.options.updateOnCursorActivity && this.cm.off("cursorActivity", this.activityFunc),
                this.widget && this.data && T.signal(this.data, "close"),
                this.widget && this.widget.close(),
                T.signal(this.cm, "endCompletion", this.cm));
        },
        active: function () {
            return this.cm.state.completionActive == this;
        },
        pick: function (t, e) {
            var i = t.list[e],
                n = this;
            this.cm.operation(function () {
                i.hint ? i.hint(n.cm, t, i) : n.cm.replaceRange(M(i), i.from || t.from, i.to || t.to, "complete"), T.signal(t, "pick", i), n.cm.scrollIntoView();
            }),
                this.options.closeOnPick && this.close();
        },
        cursorActivity: function () {
            this.debounce && (s(this.debounce), (this.debounce = 0));
            var t = this.startPos;
            this.data && (t = this.data.from);
            var e,
                i = this.cm.getCursor(),
                n = this.cm.getLine(i.line);
            i.line != this.startPos.line || n.length - i.ch != this.startLen - this.startPos.ch || i.ch < t.ch || this.cm.somethingSelected() || !i.ch || this.options.closeCharacters.test(n.charAt(i.ch - 1))
                ? this.close()
                : (((e = this).debounce = o(function () {
                      e.update();
                  })),
                  this.widget && this.widget.disable());
        },
        update: function (e) {
            var i, n;
            null != this.tick &&
                ((n = ++(i = this).tick),
                r(this.options.hint, this.cm, this.options, function (t) {
                    i.tick == n && i.finishUpdate(t, e);
                }));
        },
        finishUpdate: function (t, e) {
            this.data && T.signal(this.data, "update");
            e = (this.widget && this.widget.picked) || (e && this.options.completeSingle);
            this.widget && this.widget.close(), (this.data = t) && t.list.length && (e && 1 == t.list.length ? this.pick(t, 0) : ((this.widget = new i(this, t)), T.signal(t, "shown")));
        },
    }),
        (i.prototype = {
            close: function () {
                var t;
                this.completion.widget == this &&
                    ((this.completion.widget = null),
                    this.hints.parentNode && this.hints.parentNode.removeChild(this.hints),
                    this.completion.cm.removeKeyMap(this.keyMap),
                    (t = this.completion.cm.getInputField()).removeAttribute("aria-activedescendant"),
                    t.removeAttribute("aria-owns"),
                    (t = this.completion.cm),
                    this.completion.options.closeOnUnfocus && (t.off("blur", this.onBlur), t.off("focus", this.onFocus)),
                    t.off("scroll", this.onScroll));
            },
            disable: function () {
                this.completion.cm.removeKeyMap(this.keyMap);
                var t = this;
                (this.keyMap = {
                    Enter: function () {
                        t.picked = !0;
                    },
                }),
                    this.completion.cm.addKeyMap(this.keyMap);
            },
            pick: function () {
                this.completion.pick(this.data, this.selectedHint);
            },
            changeActive: function (t, e) {
                t >= this.data.list.length ? (t = e ? this.data.list.length - 1 : 0) : t < 0 && (t = e ? 0 : this.data.list.length - 1),
                    this.selectedHint != t &&
                        ((e = this.hints.childNodes[this.selectedHint]) && ((e.className = e.className.replace(" " + F, "")), e.removeAttribute("aria-selected")),
                        ((e = this.hints.childNodes[(this.selectedHint = t)]).className += " " + F),
                        e.setAttribute("aria-selected", "true"),
                        this.completion.cm.getInputField().setAttribute("aria-activedescendant", e.id),
                        this.scrollToActive(),
                        T.signal(this.data, "select", this.data.list[this.selectedHint], e));
            },
            scrollToActive: function () {
                var t = this.getSelectedHintRange(),
                    e = this.hints.childNodes[t.from],
                    i = this.hints.childNodes[t.to],
                    t = this.hints.firstChild;
                e.offsetTop < this.hints.scrollTop
                    ? (this.hints.scrollTop = e.offsetTop - t.offsetTop)
                    : i.offsetTop + i.offsetHeight > this.hints.scrollTop + this.hints.clientHeight && (this.hints.scrollTop = i.offsetTop + i.offsetHeight - this.hints.clientHeight + t.offsetTop);
            },
            screenAmount: function () {
                return Math.floor(this.hints.clientHeight / this.hints.firstChild.offsetHeight) || 1;
            },
            getSelectedHintRange: function () {
                var t = this.completion.options.scrollMargin || 0;
                return { from: Math.max(0, this.selectedHint - t), to: Math.min(this.data.list.length - 1, this.selectedHint + t) };
            },
        }),
        T.registerHelper("hint", "auto", {
            resolve: function (t, e) {
                var i,
                    c = t.getHelpers(e, "hint");
                if (c.length) {
                    e = function (t, n, o) {
                        var s = (function (t, e) {
                            if (!t.somethingSelected()) return e;
                            for (var i = [], n = 0; n < e.length; n++) e[n].supportsSelection && i.push(e[n]);
                            return i;
                        })(t, c);
                        !(function e(i) {
                            if (i == s.length) return n(null);
                            r(s[i], t, o, function (t) {
                                t && 0 < t.list.length ? n(t) : e(i + 1);
                            });
                        })(0);
                    };
                    return (e.async = !0), (e.supportsSelection = !0), e;
                }
                return (i = t.getHelper(t.getCursor(), "hintWords"))
                    ? function (t) {
                          return T.hint.fromList(t, { words: i });
                      }
                    : T.hint.anyword
                    ? function (t, e) {
                          return T.hint.anyword(t, e);
                      }
                    : function () {};
            },
        }),
        T.registerHelper("hint", "fromList", function (t, e) {
            var i,
                n = t.getCursor(),
                o = t.getTokenAt(n),
                s = T.Pos(n.line, o.start),
                t = n;
            o.start < n.ch && /\w/.test(o.string.charAt(n.ch - o.start - 1)) ? (i = o.string.substr(0, n.ch - o.start)) : ((i = ""), (s = n));
            for (var c = [], r = 0; r < e.words.length; r++) {
                var l = e.words[r];
                l.slice(0, i.length) == i && c.push(l);
            }
            if (c.length) return { list: c, from: s, to: t };
        }),
        (T.commands.autocomplete = T.showHint);
    var c = {
        hint: T.hint.auto,
        completeSingle: !0,
        alignWithWord: !0,
        closeCharacters: /[\s()\[\]{};:>,]/,
        closeOnPick: !0,
        closeOnUnfocus: !0,
        updateOnCursorActivity: !0,
        completeOnSingleClick: !0,
        container: null,
        customKeys: null,
        extraKeys: null,
        paddingForScrollbar: !0,
        moveOnOverlap: !0,
    };
    T.defineOption("hintOptions", null);
});
