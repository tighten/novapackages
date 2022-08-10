require('./bootstrap');

import Vue from 'vue';

window.hljs = require('highlight.js');

hljs.highlightAll();

if (!! document.getElementById('vue-app')) {
    Vue.directive('click-outside', {
        bind: function(el, binding, vNode) {
            // Provided expression must evaluate to a function.
            if (typeof binding.value !== 'function') {
                const compName = vNode.context.name;
                let warn = `[Vue-click-outside:] provided expression '${
                    binding.expression
                }' is not a function, but has to be`;
                if (compName) {
                    warn += `Found in component '${compName}'`;
                }

                console.warn(warn);
            }
            // Define Handler and cache it on the element
            const bubble = binding.modifiers.bubble;
            const handler = e => {
                if (bubble || (!el.contains(e.target) && el !== e.target)) {
                    binding.value(e);
                }
            };
            el.__vueClickOutside__ = handler;

            // add Event Listeners
            document.addEventListener('click', handler);
        },

        unbind: function(el, binding) {
            // Remove Event Listeners
            document.removeEventListener('click', el.__vueClickOutside__);
            el.__vueClickOutside__ = null;
        }
    });

    Vue.component('admin-dropdown', () => import('./components/AdminDropDown.vue'));
    Vue.component('collaborator-select', () => import('./components/CollaboratorSelect.vue'));
    Vue.component('package-card', () => import('./components/PackageCard.vue'));
    Vue.component('package-detail', () => import('./components/PackageDetail.vue'));
    Vue.component('package-detail-frame', () => import('./components/PackageDetailFrame.vue'));
    Vue.component('package-review-create', () => import('./components/PackageReviewCreate.vue'));
    Vue.component('package-screenshot', () => import('./components/PackageScreenshot.vue'));
    Vue.component('package-screenshots', () => import('./components/PackageScreenshots.vue'));
    Vue.component('package-screenshots-dropzone', () => import('./components/PackageScreenshotsDropzone.vue'));
    Vue.component('package-screenshots-list', () => import('./components/PackageScreenshotsList.vue'));
    Vue.component('rating-count-bar', () => import('./components/RatingCountBar.vue'));
    Vue.component('tag-select', () => import('./components/TagSelect.vue'));
    Vue.component('title-icon', () => import('./components/TitleIcon.vue'));

    Vue.component('passport-clients', () => import('./components/passport/Clients.vue'));
    Vue.component('passport-authorized-clients', () => import('./components/passport/AuthorizedClients.vue'));
    Vue.component('passport-personal-access-tokens', () => import('./components/passport/PersonalAccessTokens.vue'));

    Vue.mixin({
        methods: {
            route: route,
        },
        computed: {
            novapackages: function() {
                return window.novapackages;
            },
        }
    });

    new Vue({
        el: '#vue-app',
        delimiters: ['${', '}'],
    });
}
