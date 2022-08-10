"use strict";
(self["webpackChunk"] = self["webpackChunk"] || []).push([["resources_assets_js_components_passport_Clients_vue"],{

/***/ "./node_modules/babel-loader/lib/index.js??clonedRuleSet-5[0].rules[0].use[0]!./node_modules/vue-loader/lib/index.js??vue-loader-options!./resources/assets/js/components/passport/Clients.vue?vue&type=script&lang=js&":
/*!******************************************************************************************************************************************************************************************************************************!*\
  !*** ./node_modules/babel-loader/lib/index.js??clonedRuleSet-5[0].rules[0].use[0]!./node_modules/vue-loader/lib/index.js??vue-loader-options!./resources/assets/js/components/passport/Clients.vue?vue&type=script&lang=js& ***!
  \******************************************************************************************************************************************************************************************************************************/
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
      clients: [],
      createForm: {
        errors: [],
        name: '',
        redirect: ''
      },
      editForm: {
        errors: [],
        name: '',
        redirect: ''
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
      this.getClients();
      $('#modal-create-client').on('shown.bs.modal', function () {
        $('#create-client-name').focus();
      });
      $('#modal-edit-client').on('shown.bs.modal', function () {
        $('#edit-client-name').focus();
      });
    },

    /**
     * Get all of the OAuth clients for the user.
     */
    getClients: function getClients() {
      var _this = this;

      axios.get('/oauth/clients').then(function (response) {
        _this.clients = response.data;
      });
    },

    /**
     * Show the form for creating new clients.
     */
    showCreateClientForm: function showCreateClientForm() {
      $('#modal-create-client').modal('show');
    },

    /**
     * Create a new OAuth client for the user.
     */
    store: function store() {
      this.persistClient('post', '/oauth/clients', this.createForm, '#modal-create-client');
    },

    /**
     * Edit the given client.
     */
    edit: function edit(client) {
      this.editForm.id = client.id;
      this.editForm.name = client.name;
      this.editForm.redirect = client.redirect;
      $('#modal-edit-client').modal('show');
    },

    /**
     * Update the client being edited.
     */
    update: function update() {
      this.persistClient('put', '/oauth/clients/' + this.editForm.id, this.editForm, '#modal-edit-client');
    },

    /**
     * Persist the client to storage using the given form.
     */
    persistClient: function persistClient(method, uri, form, modal) {
      var _this2 = this;

      form.errors = [];
      axios[method](uri, form).then(function (response) {
        _this2.getClients();

        form.name = '';
        form.redirect = '';
        form.errors = [];
        $(modal).modal('hide');
      })["catch"](function (error) {
        if (_typeof(error.response.data) === 'object') {
          form.errors = _.flatten(_.toArray(error.response.data.errors));
        } else {
          form.errors = ['Something went wrong. Please try again.'];
        }
      });
    },

    /**
     * Destroy the given client.
     */
    destroy: function destroy(client) {
      var _this3 = this;

      axios["delete"]('/oauth/clients/' + client.id).then(function (response) {
        _this3.getClients();
      });
    }
  }
});

/***/ }),

/***/ "./resources/assets/js/components/passport/Clients.vue":
/*!*************************************************************!*\
  !*** ./resources/assets/js/components/passport/Clients.vue ***!
  \*************************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": () => (__WEBPACK_DEFAULT_EXPORT__)
/* harmony export */ });
/* harmony import */ var _Clients_vue_vue_type_template_id_5d1d7d82___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./Clients.vue?vue&type=template&id=5d1d7d82& */ "./resources/assets/js/components/passport/Clients.vue?vue&type=template&id=5d1d7d82&");
/* harmony import */ var _Clients_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./Clients.vue?vue&type=script&lang=js& */ "./resources/assets/js/components/passport/Clients.vue?vue&type=script&lang=js&");
/* harmony import */ var _node_modules_vue_loader_lib_runtime_componentNormalizer_js__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! !../../../../../node_modules/vue-loader/lib/runtime/componentNormalizer.js */ "./node_modules/vue-loader/lib/runtime/componentNormalizer.js");





/* normalize component */
;
var component = (0,_node_modules_vue_loader_lib_runtime_componentNormalizer_js__WEBPACK_IMPORTED_MODULE_2__["default"])(
  _Clients_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_1__["default"],
  _Clients_vue_vue_type_template_id_5d1d7d82___WEBPACK_IMPORTED_MODULE_0__.render,
  _Clients_vue_vue_type_template_id_5d1d7d82___WEBPACK_IMPORTED_MODULE_0__.staticRenderFns,
  false,
  null,
  null,
  null
  
)

/* hot reload */
if (false) { var api; }
component.options.__file = "resources/assets/js/components/passport/Clients.vue"
/* harmony default export */ const __WEBPACK_DEFAULT_EXPORT__ = (component.exports);

/***/ }),

/***/ "./resources/assets/js/components/passport/Clients.vue?vue&type=script&lang=js&":
/*!**************************************************************************************!*\
  !*** ./resources/assets/js/components/passport/Clients.vue?vue&type=script&lang=js& ***!
  \**************************************************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": () => (__WEBPACK_DEFAULT_EXPORT__)
/* harmony export */ });
/* harmony import */ var _node_modules_babel_loader_lib_index_js_clonedRuleSet_5_0_rules_0_use_0_node_modules_vue_loader_lib_index_js_vue_loader_options_Clients_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../../../node_modules/babel-loader/lib/index.js??clonedRuleSet-5[0].rules[0].use[0]!../../../../../node_modules/vue-loader/lib/index.js??vue-loader-options!./Clients.vue?vue&type=script&lang=js& */ "./node_modules/babel-loader/lib/index.js??clonedRuleSet-5[0].rules[0].use[0]!./node_modules/vue-loader/lib/index.js??vue-loader-options!./resources/assets/js/components/passport/Clients.vue?vue&type=script&lang=js&");
 /* harmony default export */ const __WEBPACK_DEFAULT_EXPORT__ = (_node_modules_babel_loader_lib_index_js_clonedRuleSet_5_0_rules_0_use_0_node_modules_vue_loader_lib_index_js_vue_loader_options_Clients_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_0__["default"]); 

/***/ }),

/***/ "./resources/assets/js/components/passport/Clients.vue?vue&type=template&id=5d1d7d82&":
/*!********************************************************************************************!*\
  !*** ./resources/assets/js/components/passport/Clients.vue?vue&type=template&id=5d1d7d82& ***!
  \********************************************************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "render": () => (/* reexport safe */ _node_modules_vue_loader_lib_loaders_templateLoader_js_vue_loader_options_node_modules_vue_loader_lib_index_js_vue_loader_options_Clients_vue_vue_type_template_id_5d1d7d82___WEBPACK_IMPORTED_MODULE_0__.render),
/* harmony export */   "staticRenderFns": () => (/* reexport safe */ _node_modules_vue_loader_lib_loaders_templateLoader_js_vue_loader_options_node_modules_vue_loader_lib_index_js_vue_loader_options_Clients_vue_vue_type_template_id_5d1d7d82___WEBPACK_IMPORTED_MODULE_0__.staticRenderFns)
/* harmony export */ });
/* harmony import */ var _node_modules_vue_loader_lib_loaders_templateLoader_js_vue_loader_options_node_modules_vue_loader_lib_index_js_vue_loader_options_Clients_vue_vue_type_template_id_5d1d7d82___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../../../node_modules/vue-loader/lib/loaders/templateLoader.js??vue-loader-options!../../../../../node_modules/vue-loader/lib/index.js??vue-loader-options!./Clients.vue?vue&type=template&id=5d1d7d82& */ "./node_modules/vue-loader/lib/loaders/templateLoader.js??vue-loader-options!./node_modules/vue-loader/lib/index.js??vue-loader-options!./resources/assets/js/components/passport/Clients.vue?vue&type=template&id=5d1d7d82&");


/***/ }),

/***/ "./node_modules/vue-loader/lib/loaders/templateLoader.js??vue-loader-options!./node_modules/vue-loader/lib/index.js??vue-loader-options!./resources/assets/js/components/passport/Clients.vue?vue&type=template&id=5d1d7d82&":
/*!***********************************************************************************************************************************************************************************************************************************!*\
  !*** ./node_modules/vue-loader/lib/loaders/templateLoader.js??vue-loader-options!./node_modules/vue-loader/lib/index.js??vue-loader-options!./resources/assets/js/components/passport/Clients.vue?vue&type=template&id=5d1d7d82& ***!
  \***********************************************************************************************************************************************************************************************************************************/
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
            [_vm._v("OAuth Clients")]
          ),
          _vm._v(" "),
          _c(
            "a",
            {
              staticClass: "button--indigo",
              attrs: { tabindex: "-1" },
              on: { click: _vm.showCreateClientForm },
            },
            [
              _c("img", {
                staticClass: "mr-2 inline",
                attrs: { src: "/images/icon-plus.svg", alt: "Plus icon" },
              }),
              _vm._v(" Create new client\n        "),
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
          _vm.clients.length === 0
            ? _c("p", { staticClass: "mb-0 p-8" }, [
                _vm._v(
                  "\n            You have not created any OAuth clients.\n        "
                ),
              ])
            : _vm._e(),
          _vm._v(" "),
          _vm.clients.length > 0
            ? _c("table", { staticClass: "flex flex-col w-full" }, [
                _vm._m(0),
                _vm._v(" "),
                _c(
                  "tbody",
                  { staticClass: "flex flex-col p-6 sm:p-8 sm:pb-4" },
                  _vm._l(_vm.clients, function (client) {
                    return _c(
                      "tr",
                      { staticClass: "flex justify-between mb-4" },
                      [
                        _c(
                          "td",
                          { staticClass: "w-1/5 align-middle hidden md:block" },
                          [
                            _vm._v(
                              "\n                        " +
                                _vm._s(client.id) +
                                "\n                    "
                            ),
                          ]
                        ),
                        _vm._v(" "),
                        _c(
                          "td",
                          { staticClass: "w-1/5 align-middle text-left" },
                          [
                            _vm._v(
                              "\n                        " +
                                _vm._s(client.name) +
                                "\n                    "
                            ),
                          ]
                        ),
                        _vm._v(" "),
                        _c("td", { staticClass: "w-1/2 align-middle" }, [
                          _c("code", [_vm._v(_vm._s(client.secret))]),
                        ]),
                        _vm._v(" "),
                        _c(
                          "td",
                          {
                            staticClass:
                              "flex flex-col w-1/4 align-middle justify-around text-center text-xs sm:text-sm sm:flex-row sm:justify-end md:w-1/5 ",
                          },
                          [
                            _c(
                              "a",
                              {
                                staticClass:
                                  "cursor-pointer border inline-block mb-2 p-2 hover:bg-gray-100 sm:mr-2 hover:border-gray-600",
                                attrs: { tabindex: "-1" },
                                on: {
                                  click: function ($event) {
                                    return _vm.edit(client)
                                  },
                                },
                              },
                              [
                                _vm._v(
                                  "\n                            Edit\n                        "
                                ),
                              ]
                            ),
                            _vm._v(" "),
                            _c(
                              "a",
                              {
                                staticClass:
                                  "cursor-pointer border inline-block mb-2 p-2 text-red hover:bg-gray-100 hover:border-gray-600",
                                on: {
                                  click: function ($event) {
                                    return _vm.destroy(client)
                                  },
                                },
                              },
                              [
                                _vm._v(
                                  "\n                            Delete\n                        "
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
          attrs: { id: "modal-create-client", tabindex: "-1", role: "dialog" },
        },
        [
          _c("div", { staticClass: "modal-dialog" }, [
            _c("div", { staticClass: "modal-content" }, [
              _vm._m(1),
              _vm._v(" "),
              _c("div", { staticClass: "modal-body" }, [
                _vm.createForm.errors.length > 0
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
                          _vm._l(_vm.createForm.errors, function (error) {
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
                _c("form", { attrs: { role: "form" } }, [
                  _c("div", { staticClass: "mb-4 flex flex-wrap" }, [
                    _c(
                      "label",
                      {
                        staticClass:
                          "md:w-1/4 pr-4 pl-4 pt-2 pb-2 mb-0 leading-normal",
                      },
                      [_vm._v("Name")]
                    ),
                    _vm._v(" "),
                    _c("div", { staticClass: "md:w-3/4 pr-4 pl-4" }, [
                      _c("input", {
                        directives: [
                          {
                            name: "model",
                            rawName: "v-model",
                            value: _vm.createForm.name,
                            expression: "createForm.name",
                          },
                        ],
                        staticClass:
                          "block appearance-none w-full py-1 px-2 mb-1 text-base leading-normal bg-white text-gray-600 border border-grey rounded",
                        attrs: { id: "create-client-name", type: "text" },
                        domProps: { value: _vm.createForm.name },
                        on: {
                          keyup: function ($event) {
                            if (
                              !$event.type.indexOf("key") &&
                              _vm._k(
                                $event.keyCode,
                                "enter",
                                13,
                                $event.key,
                                "Enter"
                              )
                            ) {
                              return null
                            }
                            return _vm.store.apply(null, arguments)
                          },
                          input: function ($event) {
                            if ($event.target.composing) {
                              return
                            }
                            _vm.$set(
                              _vm.createForm,
                              "name",
                              $event.target.value
                            )
                          },
                        },
                      }),
                      _vm._v(" "),
                      _c("span", { staticClass: "block mt-1 text-grey" }, [
                        _vm._v(
                          "\n                                    Something your users will recognize and trust.\n                                "
                        ),
                      ]),
                    ]),
                  ]),
                  _vm._v(" "),
                  _c("div", { staticClass: "mb-4 flex flex-wrap" }, [
                    _c(
                      "label",
                      {
                        staticClass:
                          "md:w-1/4 pr-4 pl-4 pt-2 pb-2 mb-0 leading-normal",
                      },
                      [_vm._v("Redirect URL")]
                    ),
                    _vm._v(" "),
                    _c("div", { staticClass: "md:w-3/4 pr-4 pl-4" }, [
                      _c("input", {
                        directives: [
                          {
                            name: "model",
                            rawName: "v-model",
                            value: _vm.createForm.redirect,
                            expression: "createForm.redirect",
                          },
                        ],
                        staticClass:
                          "block appearance-none w-full py-1 px-2 mb-1 text-base leading-normal bg-white text-gray-600 border border-grey rounded",
                        attrs: { type: "text", name: "redirect" },
                        domProps: { value: _vm.createForm.redirect },
                        on: {
                          keyup: function ($event) {
                            if (
                              !$event.type.indexOf("key") &&
                              _vm._k(
                                $event.keyCode,
                                "enter",
                                13,
                                $event.key,
                                "Enter"
                              )
                            ) {
                              return null
                            }
                            return _vm.store.apply(null, arguments)
                          },
                          input: function ($event) {
                            if ($event.target.composing) {
                              return
                            }
                            _vm.$set(
                              _vm.createForm,
                              "redirect",
                              $event.target.value
                            )
                          },
                        },
                      }),
                      _vm._v(" "),
                      _c("span", { staticClass: "block mt-1 text-grey" }, [
                        _vm._v(
                          "\n                                    Your application's authorization callback URL.\n                                "
                        ),
                      ]),
                    ]),
                  ]),
                ]),
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
          staticClass: "modal show",
          attrs: { id: "modal-edit-client", tabindex: "-1", role: "dialog" },
        },
        [
          _c("div", { staticClass: "modal-dialog" }, [
            _c("div", { staticClass: "modal-content" }, [
              _vm._m(3),
              _vm._v(" "),
              _c("div", { staticClass: "modal-body" }, [
                _vm.editForm.errors.length > 0
                  ? _c(
                      "div",
                      {
                        staticClass:
                          "relative px-3 py-3 mb-4 border rounded text-red-900 border-red-700 bg-red-300",
                      },
                      [
                        _vm._m(4),
                        _vm._v(" "),
                        _c("br"),
                        _vm._v(" "),
                        _c(
                          "ul",
                          _vm._l(_vm.editForm.errors, function (error) {
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
                _c("form", { attrs: { role: "form" } }, [
                  _c("div", { staticClass: "mb-4 flex flex-wrap" }, [
                    _c(
                      "label",
                      {
                        staticClass:
                          "md:w-1/4 pr-4 pl-4 pt-2 pb-2 mb-0 leading-normal",
                      },
                      [_vm._v("Name")]
                    ),
                    _vm._v(" "),
                    _c("div", { staticClass: "md:w-3/4 pr-4 pl-4" }, [
                      _c("input", {
                        directives: [
                          {
                            name: "model",
                            rawName: "v-model",
                            value: _vm.editForm.name,
                            expression: "editForm.name",
                          },
                        ],
                        staticClass:
                          "block appearance-none w-full py-1 px-2 mb-1 text-base leading-normal bg-white text-gray-600 border border-grey rounded",
                        attrs: { id: "edit-client-name", type: "text" },
                        domProps: { value: _vm.editForm.name },
                        on: {
                          keyup: function ($event) {
                            if (
                              !$event.type.indexOf("key") &&
                              _vm._k(
                                $event.keyCode,
                                "enter",
                                13,
                                $event.key,
                                "Enter"
                              )
                            ) {
                              return null
                            }
                            return _vm.update.apply(null, arguments)
                          },
                          input: function ($event) {
                            if ($event.target.composing) {
                              return
                            }
                            _vm.$set(_vm.editForm, "name", $event.target.value)
                          },
                        },
                      }),
                      _vm._v(" "),
                      _c("span", { staticClass: "block mt-1 text-grey" }, [
                        _vm._v(
                          "\n                                    Something your users will recognize and trust.\n                                "
                        ),
                      ]),
                    ]),
                  ]),
                  _vm._v(" "),
                  _c("div", { staticClass: "mb-4 flex flex-wrap" }, [
                    _c(
                      "label",
                      {
                        staticClass:
                          "md:w-1/4 pr-4 pl-4 pt-2 pb-2 mb-0 leading-normal",
                      },
                      [_vm._v("Redirect URL")]
                    ),
                    _vm._v(" "),
                    _c("div", { staticClass: "md:w-3/4 pr-4 pl-4" }, [
                      _c("input", {
                        directives: [
                          {
                            name: "model",
                            rawName: "v-model",
                            value: _vm.editForm.redirect,
                            expression: "editForm.redirect",
                          },
                        ],
                        staticClass:
                          "block appearance-none w-full py-1 px-2 mb-1 text-base leading-normal bg-white text-gray-600 border border-grey rounded",
                        attrs: { type: "text", name: "redirect" },
                        domProps: { value: _vm.editForm.redirect },
                        on: {
                          keyup: function ($event) {
                            if (
                              !$event.type.indexOf("key") &&
                              _vm._k(
                                $event.keyCode,
                                "enter",
                                13,
                                $event.key,
                                "Enter"
                              )
                            ) {
                              return null
                            }
                            return _vm.update.apply(null, arguments)
                          },
                          input: function ($event) {
                            if ($event.target.composing) {
                              return
                            }
                            _vm.$set(
                              _vm.editForm,
                              "redirect",
                              $event.target.value
                            )
                          },
                        },
                      }),
                      _vm._v(" "),
                      _c("span", { staticClass: "block mt-1 text-grey" }, [
                        _vm._v(
                          "\n                                    Your application's authorization callback URL.\n                                "
                        ),
                      ]),
                    ]),
                  ]),
                ]),
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
                    on: { click: _vm.update },
                  },
                  [
                    _vm._v(
                      "\n                        Save Changes\n                    "
                    ),
                  ]
                ),
              ]),
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
    return _c(
      "thead",
      { staticClass: "border-b border-gray-300 p-6 sm:p-8 pb-4" },
      [
        _c("tr", { staticClass: "flex pb-2 justify-between text-gray-800" }, [
          _c(
            "th",
            { staticClass: "text-left w-1/5 font-semibold hidden md:block" },
            [_vm._v("Client ID")]
          ),
          _vm._v(" "),
          _c("th", { staticClass: "text-left font-semibold w-1/5" }, [
            _vm._v("Name"),
          ]),
          _vm._v(" "),
          _c("th", { staticClass: "text-left font-semibold w-1/2" }, [
            _vm._v("Secret"),
          ]),
          _vm._v(" "),
          _c("th", { staticClass: "text-left font-semibold w-1/5" }),
        ]),
      ]
    )
  },
  function () {
    var _vm = this
    var _h = _vm.$createElement
    var _c = _vm._self._c || _h
    return _c("div", { staticClass: "modal-header" }, [
      _c("h4", { staticClass: "modal-title" }, [
        _vm._v("\n                        Create Client\n                    "),
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
        _vm._v("\n                        Edit Client\n                    "),
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