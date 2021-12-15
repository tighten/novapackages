"use strict";
(self["webpackChunk"] = self["webpackChunk"] || []).push([["resources_assets_js_components_passport_PersonalAccessTokens_vue"],{

/***/ "./node_modules/babel-loader/lib/index.js??clonedRuleSet-5[0].rules[0].use[0]!./node_modules/vue-loader/lib/index.js??vue-loader-options!./resources/assets/js/components/passport/PersonalAccessTokens.vue?vue&type=script&lang=js&":
/*!*******************************************************************************************************************************************************************************************************************************************!*\
  !*** ./node_modules/babel-loader/lib/index.js??clonedRuleSet-5[0].rules[0].use[0]!./node_modules/vue-loader/lib/index.js??vue-loader-options!./resources/assets/js/components/passport/PersonalAccessTokens.vue?vue&type=script&lang=js& ***!
  \*******************************************************************************************************************************************************************************************************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": () => (__WEBPACK_DEFAULT_EXPORT__)
/* harmony export */ });
function _typeof(obj) { "@babel/helpers - typeof"; return _typeof = "function" == typeof Symbol && "symbol" == typeof Symbol.iterator ? function (obj) { return typeof obj; } : function (obj) { return obj && "function" == typeof Symbol && obj.constructor === Symbol && obj !== Symbol.prototype ? "symbol" : typeof obj; }, _typeof(obj); }

//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
/* harmony default export */ const __WEBPACK_DEFAULT_EXPORT__ = ({
  /*
   * The component's data.
   */
  data: function data() {
    return {
      accessToken: null,
      tokens: [],
      scopes: [],
      form: {
        name: '',
        scopes: [],
        errors: []
      }
    };
  },

  /**
   * Prepare the component (Vue 1.x).
   */
  ready: function ready() {
    this.prepareComponent();
  },

  /**
   * Prepare the component (Vue 2.x).
   */
  mounted: function mounted() {
    this.prepareComponent();
  },
  methods: {
    /**
     * Prepare the component.
     */
    prepareComponent: function prepareComponent() {
      this.getTokens();
      this.getScopes();
      $('#modal-create-token').on('shown.bs.modal', function () {
        $('#create-token-name').focus();
      });
    },

    /**
     * Get all of the personal access tokens for the user.
     */
    getTokens: function getTokens() {
      var _this = this;

      axios.get('/oauth/personal-access-tokens').then(function (response) {
        _this.tokens = response.data;
      });
    },

    /**
     * Get all of the available scopes.
     */
    getScopes: function getScopes() {
      var _this2 = this;

      axios.get('/oauth/scopes').then(function (response) {
        _this2.scopes = response.data;
      });
    },

    /**
     * Show the form for creating new tokens.
     */
    showCreateTokenForm: function showCreateTokenForm() {
      $('#modal-create-token').modal('show');
    },

    /**
     * Create a new personal access token.
     */
    store: function store() {
      var _this3 = this;

      this.accessToken = null;
      this.form.errors = [];
      axios.post('/oauth/personal-access-tokens', this.form).then(function (response) {
        _this3.form.name = '';
        _this3.form.scopes = [];
        _this3.form.errors = [];

        _this3.tokens.push(response.data.token);

        _this3.showAccessToken(response.data.accessToken);
      })["catch"](function (error) {
        if (_typeof(error.response.data) === 'object') {
          _this3.form.errors = _.flatten(_.toArray(error.response.data.errors));
        } else {
          _this3.form.errors = ['Something went wrong. Please try again.'];
        }
      });
    },

    /**
     * Toggle the given scope in the list of assigned scopes.
     */
    toggleScope: function toggleScope(scope) {
      if (this.scopeIsAssigned(scope)) {
        this.form.scopes = _.reject(this.form.scopes, function (s) {
          return s == scope;
        });
      } else {
        this.form.scopes.push(scope);
      }
    },

    /**
     * Determine if the given scope has been assigned to the token.
     */
    scopeIsAssigned: function scopeIsAssigned(scope) {
      return _.indexOf(this.form.scopes, scope) >= 0;
    },

    /**
     * Show the given access token to the user.
     */
    showAccessToken: function showAccessToken(accessToken) {
      $('#modal-create-token').modal('hide');
      this.accessToken = accessToken;
      $('#modal-access-token').modal('show');
    },

    /**
     * Revoke the given token.
     */
    revoke: function revoke(token) {
      var _this4 = this;

      axios["delete"]('/oauth/personal-access-tokens/' + token.id).then(function (response) {
        _this4.getTokens();
      });
    }
  }
});

/***/ }),

/***/ "./resources/assets/js/components/passport/PersonalAccessTokens.vue":
/*!**************************************************************************!*\
  !*** ./resources/assets/js/components/passport/PersonalAccessTokens.vue ***!
  \**************************************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": () => (__WEBPACK_DEFAULT_EXPORT__)
/* harmony export */ });
/* harmony import */ var _PersonalAccessTokens_vue_vue_type_template_id_89c53f18___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./PersonalAccessTokens.vue?vue&type=template&id=89c53f18& */ "./resources/assets/js/components/passport/PersonalAccessTokens.vue?vue&type=template&id=89c53f18&");
/* harmony import */ var _PersonalAccessTokens_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./PersonalAccessTokens.vue?vue&type=script&lang=js& */ "./resources/assets/js/components/passport/PersonalAccessTokens.vue?vue&type=script&lang=js&");
/* harmony import */ var _node_modules_vue_loader_lib_runtime_componentNormalizer_js__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! !../../../../../node_modules/vue-loader/lib/runtime/componentNormalizer.js */ "./node_modules/vue-loader/lib/runtime/componentNormalizer.js");





/* normalize component */
;
var component = (0,_node_modules_vue_loader_lib_runtime_componentNormalizer_js__WEBPACK_IMPORTED_MODULE_2__["default"])(
  _PersonalAccessTokens_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_1__["default"],
  _PersonalAccessTokens_vue_vue_type_template_id_89c53f18___WEBPACK_IMPORTED_MODULE_0__.render,
  _PersonalAccessTokens_vue_vue_type_template_id_89c53f18___WEBPACK_IMPORTED_MODULE_0__.staticRenderFns,
  false,
  null,
  null,
  null
  
)

/* hot reload */
if (false) { var api; }
component.options.__file = "resources/assets/js/components/passport/PersonalAccessTokens.vue"
/* harmony default export */ const __WEBPACK_DEFAULT_EXPORT__ = (component.exports);

/***/ }),

/***/ "./resources/assets/js/components/passport/PersonalAccessTokens.vue?vue&type=script&lang=js&":
/*!***************************************************************************************************!*\
  !*** ./resources/assets/js/components/passport/PersonalAccessTokens.vue?vue&type=script&lang=js& ***!
  \***************************************************************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": () => (__WEBPACK_DEFAULT_EXPORT__)
/* harmony export */ });
/* harmony import */ var _node_modules_babel_loader_lib_index_js_clonedRuleSet_5_0_rules_0_use_0_node_modules_vue_loader_lib_index_js_vue_loader_options_PersonalAccessTokens_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../../../node_modules/babel-loader/lib/index.js??clonedRuleSet-5[0].rules[0].use[0]!../../../../../node_modules/vue-loader/lib/index.js??vue-loader-options!./PersonalAccessTokens.vue?vue&type=script&lang=js& */ "./node_modules/babel-loader/lib/index.js??clonedRuleSet-5[0].rules[0].use[0]!./node_modules/vue-loader/lib/index.js??vue-loader-options!./resources/assets/js/components/passport/PersonalAccessTokens.vue?vue&type=script&lang=js&");
 /* harmony default export */ const __WEBPACK_DEFAULT_EXPORT__ = (_node_modules_babel_loader_lib_index_js_clonedRuleSet_5_0_rules_0_use_0_node_modules_vue_loader_lib_index_js_vue_loader_options_PersonalAccessTokens_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_0__["default"]); 

/***/ }),

/***/ "./resources/assets/js/components/passport/PersonalAccessTokens.vue?vue&type=template&id=89c53f18&":
/*!*********************************************************************************************************!*\
  !*** ./resources/assets/js/components/passport/PersonalAccessTokens.vue?vue&type=template&id=89c53f18& ***!
  \*********************************************************************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "render": () => (/* reexport safe */ _node_modules_vue_loader_lib_loaders_templateLoader_js_vue_loader_options_node_modules_vue_loader_lib_index_js_vue_loader_options_PersonalAccessTokens_vue_vue_type_template_id_89c53f18___WEBPACK_IMPORTED_MODULE_0__.render),
/* harmony export */   "staticRenderFns": () => (/* reexport safe */ _node_modules_vue_loader_lib_loaders_templateLoader_js_vue_loader_options_node_modules_vue_loader_lib_index_js_vue_loader_options_PersonalAccessTokens_vue_vue_type_template_id_89c53f18___WEBPACK_IMPORTED_MODULE_0__.staticRenderFns)
/* harmony export */ });
/* harmony import */ var _node_modules_vue_loader_lib_loaders_templateLoader_js_vue_loader_options_node_modules_vue_loader_lib_index_js_vue_loader_options_PersonalAccessTokens_vue_vue_type_template_id_89c53f18___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../../../node_modules/vue-loader/lib/loaders/templateLoader.js??vue-loader-options!../../../../../node_modules/vue-loader/lib/index.js??vue-loader-options!./PersonalAccessTokens.vue?vue&type=template&id=89c53f18& */ "./node_modules/vue-loader/lib/loaders/templateLoader.js??vue-loader-options!./node_modules/vue-loader/lib/index.js??vue-loader-options!./resources/assets/js/components/passport/PersonalAccessTokens.vue?vue&type=template&id=89c53f18&");


/***/ }),

/***/ "./node_modules/vue-loader/lib/loaders/templateLoader.js??vue-loader-options!./node_modules/vue-loader/lib/index.js??vue-loader-options!./resources/assets/js/components/passport/PersonalAccessTokens.vue?vue&type=template&id=89c53f18&":
/*!************************************************************************************************************************************************************************************************************************************************!*\
  !*** ./node_modules/vue-loader/lib/loaders/templateLoader.js??vue-loader-options!./node_modules/vue-loader/lib/index.js??vue-loader-options!./resources/assets/js/components/passport/PersonalAccessTokens.vue?vue&type=template&id=89c53f18& ***!
  \************************************************************************************************************************************************************************************************************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "render": () => (/* binding */ render),
/* harmony export */   "staticRenderFns": () => (/* binding */ staticRenderFns)
/* harmony export */ });
var render = function () {
  var _vm = this
  var _h = _vm.$createElement
  var _c = _vm._self._c || _h
  return _c(
    "div",
    { staticClass: "flex flex-col my-8 rounded-lg text-gray-600" },
    [
      _c(
        "div",
        {
          staticClass:
            "flex flex-col items-center justify-between mb-6 sm:flex-row",
        },
        [
          _c(
            "h3",
            { staticClass: "text-gray-800 text-xl font-semibold mb-4 sm:mb-0" },
            [_vm._v("Personal Access Tokens")]
          ),
          _vm._v(" "),
          _c(
            "a",
            {
              staticClass: "button--indigo",
              attrs: { tabindex: "-1" },
              on: { click: _vm.showCreateTokenForm },
            },
            [
              _c("img", {
                staticClass: "mr-2 inline",
                attrs: { src: "/images/icon-plus.svg", alt: "Plus icon" },
              }),
              _vm._v(" Create new token\n            "),
            ]
          ),
        ]
      ),
      _vm._v(" "),
      _c(
        "div",
        {
          staticClass:
            "bg-white flex flex-col min-w-0 rounded break-words shadow",
        },
        [
          _vm.tokens.length === 0
            ? _c("p", { staticClass: "p-8" }, [
                _vm._v(
                  "\n                You have not created any personal access tokens.\n            "
                ),
              ])
            : _vm._e(),
          _vm._v(" "),
          _vm.tokens.length > 0
            ? _c("table", { staticClass: "flex flex-col w-full" }, [
                _vm._m(0),
                _vm._v(" "),
                _c(
                  "tbody",
                  { staticClass: "flex flex-col p-8 pb-4" },
                  _vm._l(_vm.tokens, function (token) {
                    return _c(
                      "tr",
                      { staticClass: "flex justify-between mb-8" },
                      [
                        _c("td", { staticClass: "align-middle w-2/3" }, [
                          _vm._v(
                            "\n                            " +
                              _vm._s(token.name) +
                              "\n                        "
                          ),
                        ]),
                        _vm._v(" "),
                        _c(
                          "td",
                          { staticClass: "w-1/3 align-middle text-right" },
                          [
                            _c(
                              "a",
                              {
                                staticClass:
                                  "cursor-pointer border p-2 text-red text-xs sm:text-sm hover:bg-gray-100 hover:border-gray-600",
                                on: {
                                  click: function ($event) {
                                    return _vm.revoke(token)
                                  },
                                },
                              },
                              [
                                _vm._v(
                                  "\n                                Delete\n                            "
                                ),
                              ]
                            ),
                          ]
                        ),
                      ]
                    )
                  }),
                  0
                ),
              ])
            : _vm._e(),
        ]
      ),
      _vm._v(" "),
      _c(
        "div",
        {
          staticClass: "modal hide",
          attrs: { id: "modal-create-token", tabindex: "-1", role: "dialog" },
        },
        [
          _c("div", { staticClass: "modal-dialog" }, [
            _c("div", { staticClass: "modal-content" }, [
              _vm._m(1),
              _vm._v(" "),
              _c("div", { staticClass: "modal-body" }, [
                _vm.form.errors.length > 0
                  ? _c(
                      "div",
                      {
                        staticClass:
                          "relative px-3 py-3 mb-4 border rounded text-red-900 border-red-700 bg-red-300",
                      },
                      [
                        _vm._m(2),
                        _vm._v(" "),
                        _c("br"),
                        _vm._v(" "),
                        _c(
                          "ul",
                          _vm._l(_vm.form.errors, function (error) {
                            return _c("li", [
                              _vm._v(
                                "\n                                " +
                                  _vm._s(error) +
                                  "\n                            "
                              ),
                            ])
                          }),
                          0
                        ),
                      ]
                    )
                  : _vm._e(),
                _vm._v(" "),
                _c(
                  "form",
                  {
                    attrs: { role: "form" },
                    on: {
                      submit: function ($event) {
                        $event.preventDefault()
                        return _vm.store.apply(null, arguments)
                      },
                    },
                  },
                  [
                    _c("div", { staticClass: "mb-4 flex flex-wrap" }, [
                      _c(
                        "label",
                        {
                          staticClass:
                            "md:w-1/3 pr-4 pl-4 pt-2 pb-2 mb-0 leading-normal",
                        },
                        [_vm._v("Name")]
                      ),
                      _vm._v(" "),
                      _c("div", { staticClass: "md:w-1/2 pr-4 pl-4" }, [
                        _c("input", {
                          directives: [
                            {
                              name: "model",
                              rawName: "v-model",
                              value: _vm.form.name,
                              expression: "form.name",
                            },
                          ],
                          staticClass:
                            "block appearance-none w-full py-1 px-2 mb-1 text-base leading-normal bg-white text-gray-600 border border-grey rounded",
                          attrs: {
                            id: "create-token-name",
                            type: "text",
                            name: "name",
                          },
                          domProps: { value: _vm.form.name },
                          on: {
                            input: function ($event) {
                              if ($event.target.composing) {
                                return
                              }
                              _vm.$set(_vm.form, "name", $event.target.value)
                            },
                          },
                        }),
                      ]),
                    ]),
                    _vm._v(" "),
                    _vm.scopes.length > 0
                      ? _c("div", { staticClass: "mb-4 flex flex-wrap" }, [
                          _c(
                            "label",
                            {
                              staticClass:
                                "md:w-1/3 pr-4 pl-4 pt-2 pb-2 mb-0 leading-normal",
                            },
                            [_vm._v("Scopes")]
                          ),
                          _vm._v(" "),
                          _c(
                            "div",
                            { staticClass: "md:w-1/2 pr-4 pl-4" },
                            _vm._l(_vm.scopes, function (scope) {
                              return _c("div", [
                                _c("div", { staticClass: "checkbox" }, [
                                  _c("label", [
                                    _c("input", {
                                      attrs: { type: "checkbox" },
                                      domProps: {
                                        checked: _vm.scopeIsAssigned(scope.id),
                                      },
                                      on: {
                                        click: function ($event) {
                                          return _vm.toggleScope(scope.id)
                                        },
                                      },
                                    }),
                                    _vm._v(
                                      "\n\n                                                " +
                                        _vm._s(scope.id) +
                                        "\n                                        "
                                    ),
                                  ]),
                                ]),
                              ])
                            }),
                            0
                          ),
                        ])
                      : _vm._e(),
                  ]
                ),
              ]),
              _vm._v(" "),
              _c("div", { staticClass: "modal-footer" }, [
                _c(
                  "button",
                  {
                    staticClass:
                      "inline-block align-middle text-center select-none border font-normal whitespace-no-wrap py-2 px-4 rounded text-base leading-normal no-underline text-gray-100 bg-gray-500 hover:bg-gray-400",
                    attrs: { type: "button", "data-dismiss": "modal" },
                  },
                  [_vm._v("Close")]
                ),
                _vm._v(" "),
                _c(
                  "button",
                  {
                    staticClass:
                      "inline-block align-middle text-center select-none border font-normal whitespace-no-wrap py-2 px-4 rounded text-base leading-normal no-underline text-blue-100 bg-blue-500 hover:bg-blue-400",
                    attrs: { type: "button" },
                    on: { click: _vm.store },
                  },
                  [
                    _vm._v(
                      "\n                        Create\n                    "
                    ),
                  ]
                ),
              ]),
            ]),
          ]),
        ]
      ),
      _vm._v(" "),
      _c(
        "div",
        {
          staticClass: "modal hide",
          attrs: { id: "modal-access-token", tabindex: "-1", role: "dialog" },
        },
        [
          _c("div", { staticClass: "modal-dialog" }, [
            _c("div", { staticClass: "modal-content" }, [
              _vm._m(3),
              _vm._v(" "),
              _c("div", { staticClass: "modal-body" }, [
                _c("p", [
                  _vm._v(
                    "\n                        Here is your new personal access token. This is the only time it will be shown so don't lose it!\n                        You may now use this token to make API requests.\n                    "
                  ),
                ]),
                _vm._v(" "),
                _c(
                  "textarea",
                  {
                    staticClass:
                      "block appearance-none w-full py-1 px-2 mb-1 text-base leading-normal bg-white text-gray-600 border border-grey rounded",
                    attrs: { rows: "10" },
                  },
                  [_vm._v(_vm._s(_vm.accessToken))]
                ),
              ]),
              _vm._v(" "),
              _vm._m(4),
            ]),
          ]),
        ]
      ),
    ]
  )
}
var staticRenderFns = [
  function () {
    var _vm = this
    var _h = _vm.$createElement
    var _c = _vm._self._c || _h
    return _c("thead", { staticClass: "border-b border-gray-300 p-8 pb-4" }, [
      _c("tr", { staticClass: "flex pb-2 justify-between text-gray-800" }, [
        _c(
          "th",
          { staticClass: "w-2/3 text-left text-semibold text-gray-800" },
          [_vm._v("Name")]
        ),
        _vm._v(" "),
        _c("th"),
      ]),
    ])
  },
  function () {
    var _vm = this
    var _h = _vm.$createElement
    var _c = _vm._self._c || _h
    return _c("div", { staticClass: "modal-header" }, [
      _c("h4", { staticClass: "modal-title" }, [
        _vm._v("\n                        Create Token\n                    "),
      ]),
      _vm._v(" "),
      _c(
        "button",
        {
          staticClass: "absolute top-0 right-0 pr-2 pt-1",
          attrs: {
            type: "button",
            "data-dismiss": "modal",
            "aria-hidden": "true",
          },
        },
        [_vm._v("×")]
      ),
    ])
  },
  function () {
    var _vm = this
    var _h = _vm.$createElement
    var _c = _vm._self._c || _h
    return _c("p", { staticClass: "mb-0" }, [
      _c("strong", [_vm._v("Whoops!")]),
      _vm._v(" Something went wrong!"),
    ])
  },
  function () {
    var _vm = this
    var _h = _vm.$createElement
    var _c = _vm._self._c || _h
    return _c("div", { staticClass: "modal-header" }, [
      _c("h4", { staticClass: "modal-title" }, [
        _vm._v(
          "\n                        Personal Access Token\n                    "
        ),
      ]),
      _vm._v(" "),
      _c(
        "button",
        {
          staticClass: "absolute top-0 right-0 pr-2 pt-1",
          attrs: {
            type: "button",
            "data-dismiss": "modal",
            "aria-hidden": "true",
          },
        },
        [_vm._v("×")]
      ),
    ])
  },
  function () {
    var _vm = this
    var _h = _vm.$createElement
    var _c = _vm._self._c || _h
    return _c("div", { staticClass: "modal-footer" }, [
      _c(
        "button",
        {
          staticClass:
            "inline-block align-middle text-center select-none border font-normal whitespace-no-wrap py-2 px-4 rounded text-base leading-normal no-underline text-gray-100 bg-gray-500 hover:bg-gray-400",
          attrs: { type: "button", "data-dismiss": "modal" },
        },
        [_vm._v("Close")]
      ),
    ])
  },
]
render._withStripped = true



/***/ }),

/***/ "./node_modules/vue-loader/lib/runtime/componentNormalizer.js":
/*!********************************************************************!*\
  !*** ./node_modules/vue-loader/lib/runtime/componentNormalizer.js ***!
  \********************************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": () => (/* binding */ normalizeComponent)
/* harmony export */ });
/* globals __VUE_SSR_CONTEXT__ */

// IMPORTANT: Do NOT use ES2015 features in this file (except for modules).
// This module is a runtime utility for cleaner component module output and will
// be included in the final webpack user bundle.

function normalizeComponent (
  scriptExports,
  render,
  staticRenderFns,
  functionalTemplate,
  injectStyles,
  scopeId,
  moduleIdentifier, /* server only */
  shadowMode /* vue-cli only */
) {
  // Vue.extend constructor export interop
  var options = typeof scriptExports === 'function'
    ? scriptExports.options
    : scriptExports

  // render functions
  if (render) {
    options.render = render
    options.staticRenderFns = staticRenderFns
    options._compiled = true
  }

  // functional template
  if (functionalTemplate) {
    options.functional = true
  }

  // scopedId
  if (scopeId) {
    options._scopeId = 'data-v-' + scopeId
  }

  var hook
  if (moduleIdentifier) { // server build
    hook = function (context) {
      // 2.3 injection
      context =
        context || // cached call
        (this.$vnode && this.$vnode.ssrContext) || // stateful
        (this.parent && this.parent.$vnode && this.parent.$vnode.ssrContext) // functional
      // 2.2 with runInNewContext: true
      if (!context && typeof __VUE_SSR_CONTEXT__ !== 'undefined') {
        context = __VUE_SSR_CONTEXT__
      }
      // inject component styles
      if (injectStyles) {
        injectStyles.call(this, context)
      }
      // register component module identifier for async chunk inferrence
      if (context && context._registeredComponents) {
        context._registeredComponents.add(moduleIdentifier)
      }
    }
    // used by ssr in case component is cached and beforeCreate
    // never gets called
    options._ssrRegister = hook
  } else if (injectStyles) {
    hook = shadowMode
      ? function () {
        injectStyles.call(
          this,
          (options.functional ? this.parent : this).$root.$options.shadowRoot
        )
      }
      : injectStyles
  }

  if (hook) {
    if (options.functional) {
      // for template-only hot-reload because in that case the render fn doesn't
      // go through the normalizer
      options._injectStyles = hook
      // register for functional component in vue file
      var originalRender = options.render
      options.render = function renderWithStyleInjection (h, context) {
        hook.call(context)
        return originalRender(h, context)
      }
    } else {
      // inject component registration as beforeCreate hook
      var existing = options.beforeCreate
      options.beforeCreate = existing
        ? [].concat(existing, hook)
        : [hook]
    }
  }

  return {
    exports: scriptExports,
    options: options
  }
}


/***/ })

}]);