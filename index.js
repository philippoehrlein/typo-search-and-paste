import { Fragment as e, createBlock as t, createCommentVNode as n, createElementBlock as r, createElementVNode as i, createVNode as a, nextTick as o, normalizeClass as s, onMounted as c, onUnmounted as l, openBlock as u, ref as d, renderList as f, resolveComponent as p, toDisplayString as m, unref as h, watch as g, withCtx as _, withKeys as v } from "vue";
//#region node_modules/kirbyuse/dist/composables-Y8gb-rex.mjs
function y() {
	return window.panel;
}
//#endregion
//#region src/components/TSPButton.vue
var b = {
	__name: "TSPButton",
	setup(e) {
		let t = y();
		function n() {
			window.panel.dialog.open({ component: "k-typo-search-and-paste-dialog" });
		}
		return (e, i) => {
			let o = p("k-button");
			return u(), r("div", null, [a(o, {
				dropdown: !1,
				title: h(t).t("philippoehrlein.typo-search-and-paste.buttonTitle"),
				variant: "filled",
				size: "sm",
				icon: "typo-search-and-paste",
				onClick: n
			}, null, 8, ["title"])]);
		};
	}
}, x = (e, t) => {
	let n = e.__vccOpts || e;
	for (let [e, r] of t) n[e] = r;
	return n;
}, S = {
	key: 0,
	class: "tsp-results-container"
}, C = {
	key: 0,
	class: "tsp-results"
}, w = { class: "tsp-results__result-value" }, T = { class: "tsp-results__result-name" }, E = {
	key: 1,
	class: "tsp-results__no-results"
}, D = /* @__PURE__ */ x({
	__name: "TSPResults",
	props: {
		results: {
			type: Array,
			required: !0
		},
		queryLength: {
			type: Number,
			required: !0
		}
	},
	emits: ["close", "focusinput"],
	setup(a, { expose: c, emit: l }) {
		let b = a, x = l, D = y(), O = d(-1), k = d([]), A = (e, t) => {
			e && (k.value[t] = e);
		};
		g(() => b.results, () => {
			O.value = -1;
		});
		let j = (e) => {
			o(() => {
				k.value[e] && k.value[e].$el?.focus();
			});
		}, M = (e) => {
			let { key: t } = e;
			t === "ArrowDown" ? (e.preventDefault(), O.value < b.results.length - 1 && (O.value++, j(O.value))) : t === "ArrowUp" ? (e.preventDefault(), O.value > 0 ? (O.value--, j(O.value)) : x("focusinput")) : t === "Escape" && (e.preventDefault(), x("close"));
		};
		c({
			focusFirst: () => {
				b.results.length > 0 && (O.value = 0, j(0));
			},
			focusLast: () => {
				b.results.length > 0 && (O.value = b.results.length - 1, j(b.results.length - 1));
			}
		});
		function N(e) {
			navigator.clipboard.writeText(e), x("close"), D.notification.info({
				message: D.t("philippoehrlein.typo-search-and-paste.copiedMessage", { character: e }),
				icon: void 0
			});
		}
		return (o, c) => {
			let l = p("k-button");
			return a.queryLength > 2 ? (u(), r("div", S, [a.results.length > 0 ? (u(), r("div", C, [(u(!0), r(e, null, f(a.results, (e, n) => (u(), t(l, {
				key: e.value,
				ref_for: !0,
				ref: (e) => A(e, n),
				class: s(["tsp-results__result", { "tsp-results__result--active": O.value === n }]),
				tabindex: "0",
				role: "menuitem",
				title: e.name,
				onClick: (t) => N(e.value),
				onKeydown: v(M, ["native"])
			}, {
				default: _(() => [i("span", w, m(e.value), 1), i("span", T, m(e.name), 1)]),
				_: 2
			}, 1032, [
				"class",
				"title",
				"onClick"
			]))), 128))])) : (u(), r("div", E, [i("p", null, m(h(D).t("philippoehrlein.typo-search-and-paste.noResults")), 1)]))])) : n("v-if", !0);
		};
	}
}, [["__scopeId", "data-v-0ed5b5df"]]), O = { class: "tsp-search" }, k = {
	__name: "TSPSearch",
	emits: [
		"result",
		"length",
		"close",
		"focusresults"
	],
	setup(e, { expose: t, emit: n }) {
		let i = n, o = y(), s = d(""), f = d([]), m = d(null), g = null, _ = async (e) => {
			if (e.length < 3) {
				f.value = [], i("length", 0), i("result", f.value);
				return;
			}
			let t = e.trim().replace(/\s+/g, " AND "), n = encodeURIComponent(t), r = await window.panel.api.get(`tsp-search/${n}`);
			try {
				f.value = r.results, i("result", f.value), i("length", e.length);
			} catch (e) {
				throw console.error(e), e;
			}
		}, v = (e) => {
			if (s.value = e, e.length < 3) {
				f.value = [], i("length", e.length), i("result", f.value);
				return;
			}
			g && clearTimeout(g), g = setTimeout(() => {
				_(e);
			}, 300);
		}, b = (e) => {
			let { key: t } = e;
			t === "ArrowDown" && f.value.length > 0 ? (e.preventDefault(), i("focusresults")) : t === "Escape" && (e.preventDefault(), i("close"));
		};
		return c(() => {
			m.value && m.value.$el && m.value.$el.addEventListener("keydown", b);
		}), l(() => {
			m.value && m.value.$el && m.value.$el.removeEventListener("keydown", b);
		}), t({ focus: () => {
			m.value && m.value.focus();
		} }), (e, t) => {
			let n = p("k-search-input"), i = p("k-button");
			return u(), r("div", O, [a(n, {
				ref_key: "searchInput",
				ref: m,
				placeholder: h(o).t("philippoehrlein.typo-search-and-paste.searchPlaceholder", "Search for special characters"),
				value: s.value,
				autofocus: "",
				onInput: v
			}, null, 8, ["placeholder", "value"]), a(i, {
				icon: "cancel",
				title: e.$t("close"),
				class: "k-search-bar-close",
				onClick: t[0] ||= (t) => e.$emit("close")
			}, null, 8, ["title"])]);
		};
	}
}, A = {
	id: "typo-search-and-paste-dialog-title",
	class: "sr-only"
};
//#endregion
//#region src/index.js
window.panel.plugin("philippoehrlein/typo-search-paste", {
	icons: { "typo-search-and-paste": "<path d=\"M11.9424 2.74805C13.8302 2.74809 15.4545 3.06805 16.8145 3.70801C18.1743 4.34801 19.2221 5.30057 19.958 6.56445C20.6778 7.81231 21.038 9.37994 21.0381 11.2676C21.0381 13.1395 20.6779 14.7077 19.958 15.9717C19.2221 17.2356 18.1743 18.1881 16.8145 18.8281C15.4545 19.4681 13.8302 19.788 11.9424 19.7881C11.7184 19.7881 11.4935 19.7797 11.2695 19.7637C11.0298 19.7477 10.798 19.7318 10.5742 19.7158L7.50195 22.6201H4.50098L7.85156 19.4443L8.10156 19.2119L8.09863 19.2109L9.77539 17.6221C10.4438 17.769 11.1662 17.8438 11.9424 17.8438C12.9501 17.8437 13.8619 17.7243 14.6777 17.4844C15.4937 17.2284 16.2065 16.844 16.8145 16.332C17.4063 15.8201 17.8617 15.1716 18.1816 14.3877C18.5016 13.5878 18.6621 12.6361 18.6621 11.5322V11.0039C18.6621 9.89994 18.5016 8.95586 18.1816 8.17188C17.8617 7.37212 17.4062 6.71602 16.8145 6.2041C16.2065 5.6921 15.4937 5.31617 14.6777 5.07617C13.8619 4.82024 12.9502 4.69242 11.9424 4.69238C10.9505 4.69238 10.0464 4.82025 9.23047 5.07617C8.39847 5.31617 7.68575 5.6921 7.09375 6.2041C6.50185 6.71608 6.04555 7.37196 5.72559 8.17188C5.38961 8.95585 5.22169 9.89997 5.22168 11.0039V11.5322C5.2217 12.6359 5.38972 13.5878 5.72559 14.3877C6.04556 15.1716 6.50181 15.82 7.09375 16.332C7.2996 16.5101 7.521 16.6713 7.75586 16.8184L6.19531 18.3115C5.41782 17.8061 4.76499 17.1712 4.23828 16.4043C3.32628 15.0763 2.87012 13.3636 2.87012 11.2676C2.87017 9.38002 3.23788 7.81228 3.97363 6.56445C4.6936 5.30052 5.73385 4.34801 7.09375 3.70801C8.43775 3.06801 10.0544 2.74805 11.9424 2.74805Z\"/>" },
	components: { "k-typo-search-and-paste-dialog": {
		__name: "TSPPanel",
		emits: ["close"],
		setup(e, { emit: n }) {
			let r = n, o = y(), s = d([]), c = d(0), l = d(null), f = d(null), g = (e) => {
				s.value = e;
			}, v = (e) => {
				c.value = e;
			}, b = () => {
				r("cancel");
			}, x = () => {
				f.value && f.value.focusFirst();
			}, S = () => {
				l.value && l.value.focus();
			};
			return (e, n) => {
				let d = p("k-dialog");
				return u(), t(d, {
					"cancel-button": !1,
					"submit-button": !1,
					visible: !0,
					size: "medium",
					class: "k-typo-search-and-paste-dialog",
					role: "dialog",
					"aria-labelledby": "typo-search-and-paste-dialog-title",
					onCancel: n[0] ||= (e) => r("cancel")
				}, {
					default: _(() => [
						i("h2", A, m(h(o).t("philippoehrlein.typo-search-and-paste.buttonTitle", "Search Special Characters")), 1),
						a(k, {
							ref_key: "searchComponent",
							ref: l,
							onResult: g,
							onLength: v,
							onClose: b,
							onFocusresults: x
						}, null, 512),
						a(D, {
							ref_key: "resultsComponent",
							ref: f,
							results: s.value,
							"query-length": c.value,
							onClose: b,
							onFocusinput: S
						}, null, 8, ["results", "query-length"])
					]),
					_: 1
				});
			};
		}
	} },
	viewButtons: { "typo-search-and-paste": b }
});
//#endregion
