<template>
    <div class="flex flex-col my-8 rounded-lg text-gray-600">
        <div class="flex flex-col items-center justify-between mb-6 sm:flex-row">
            <h3 class="text-gray-800 text-xl font-semibold mb-4 sm:mb-0">Personal Access Tokens</h3>

            <a class="button--indigo" tabindex="-1" @click="showCreateTokenForm">
                <img src="/images/icon-plus.svg" alt="Plus icon" class="mr-2 inline"> Create new token
            </a>
        </div>

        <div class="bg-white flex flex-col min-w-0 rounded break-words shadow">
            <!-- No Tokens Notice -->
            <p class="p-8" v-if="tokens.length === 0">
                You have not created any personal access tokens.
            </p>

            <!-- Personal Access Tokens -->
            <table class="flex flex-col w-full" v-if="tokens.length > 0">
                <thead class="border-b border-gray-300 p-8 pb-4">
                    <tr class="flex pb-2 justify-between text-gray-800">
                        <th class="w-2/3 text-left text-semibold text-gray-800">Name</th>
                        <th></th>
                    </tr>
                </thead>

                <tbody class="flex flex-col p-8 pb-4">
                    <tr v-for="token in tokens" class="flex justify-between mb-8">
                        <!-- Client Name -->
                        <td class="align-middle w-2/3">
                            {{ token.name }}
                        </td>

                        <!-- Delete Button -->
                        <td class="w-1/3 align-middle text-right">
                            <a class="cursor-pointer border p-2 text-red text-xs sm:text-sm hover:bg-gray-100 hover:border-gray-600" @click="revoke(token)">
                                Delete
                            </a>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

    <!-- Create Token Modal -->
    <div class="modal hide" id="modal-create-token" tabindex="-1" role="dialog">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">
                        Create Token
                    </h4>

                    <button type="button" class="absolute top-0 right-0 pr-2 pt-1" data-dismiss="modal" aria-hidden="true">&times;</button>
                </div>

                <div class="modal-body">
                    <!-- Form Errors -->
                    <div class="relative px-3 py-3 mb-4 border rounded text-red-900 border-red-700 bg-red-300" v-if="form.errors.length > 0">
                        <p class="mb-0"><strong>Whoops!</strong> Something went wrong!</p>
                        <br>
                        <ul>
                            <li v-for="error in form.errors">
                                {{ error }}
                            </li>
                        </ul>
                    </div>

                    <!-- Create Token Form -->
                    <form role="form" @submit.prevent="store">
                        <!-- Name -->
                        <div class="mb-4 flex flex-wrap">
                            <label class="md:w-1/3 pr-4 pl-4 pt-2 pb-2 mb-0 leading-normal">Name</label>

                            <div class="md:w-1/2 pr-4 pl-4">
                                <input id="create-token-name" type="text" class="block appearance-none w-full py-1 px-2 mb-1 text-base leading-normal bg-white text-gray-600 border border-grey rounded" name="name" v-model="form.name">
                            </div>
                        </div>

                        <!-- Scopes -->
                        <div class="mb-4 flex flex-wrap" v-if="scopes.length > 0">
                            <label class="md:w-1/3 pr-4 pl-4 pt-2 pb-2 mb-0 leading-normal">Scopes</label>

                            <div class="md:w-1/2 pr-4 pl-4">
                                <div v-for="scope in scopes">
                                    <div class="checkbox">
                                        <label>
                                            <input type="checkbox"
                                                @click="toggleScope(scope.id)"
                                                :checked="scopeIsAssigned(scope.id)">

                                                {{ scope.id }}
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>

                <!-- Modal Actions -->
                <div class="modal-footer">
                    <button type="button" class="inline-block align-middle text-center select-none border font-normal whitespace-no-wrap py-2 px-4 rounded text-base leading-normal no-underline text-gray-100 bg-gray-500 hover:bg-gray-400" data-dismiss="modal">Close</button>

                    <button type="button" class="inline-block align-middle text-center select-none border font-normal whitespace-no-wrap py-2 px-4 rounded text-base leading-normal no-underline text-blue-100 bg-blue-500 hover:bg-blue-400" @click="store">
                        Create
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Access Token Modal -->
    <div class="modal hide" id="modal-access-token" tabindex="-1" role="dialog">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">
                        Personal Access Token
                    </h4>

                    <button type="button" class="absolute top-0 right-0 pr-2 pt-1" data-dismiss="modal" aria-hidden="true">&times;</button>
                </div>

                <div class="modal-body">
                    <p>
                        Here is your new personal access token. This is the only time it will be shown so don't lose it!
                        You may now use this token to make API requests.
                    </p>

                    <textarea class="block appearance-none w-full py-1 px-2 mb-1 text-base leading-normal bg-white text-gray-600 border border-grey rounded" rows="10">{{ accessToken }}</textarea>
                </div>

                <!-- Modal Actions -->
                <div class="modal-footer">
                    <button type="button" class="inline-block align-middle text-center select-none border font-normal whitespace-no-wrap py-2 px-4 rounded text-base leading-normal no-underline text-gray-100 bg-gray-500 hover:bg-gray-400" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
</div>
</template>

<script>
    export default {
        /*
         * The component's data.
         */
        data() {
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
        ready() {
            this.prepareComponent();
        },

        /**
         * Prepare the component (Vue 2.x).
         */
        mounted() {
            this.prepareComponent();
        },

        methods: {
            /**
             * Prepare the component.
             */
            prepareComponent() {
                this.getTokens();
                this.getScopes();

                $('#modal-create-token').on('shown.bs.modal', () => {
                    $('#create-token-name').focus();
                });
            },

            /**
             * Get all of the personal access tokens for the user.
             */
            getTokens() {
                axios.get('/oauth/personal-access-tokens')
                        .then(response => {
                            this.tokens = response.data;
                        });
            },

            /**
             * Get all of the available scopes.
             */
            getScopes() {
                axios.get('/oauth/scopes')
                        .then(response => {
                            this.scopes = response.data;
                        });
            },

            /**
             * Show the form for creating new tokens.
             */
            showCreateTokenForm() {
                $('#modal-create-token').modal('show');
            },

            /**
             * Create a new personal access token.
             */
            store() {
                this.accessToken = null;

                this.form.errors = [];

                axios.post('/oauth/personal-access-tokens', this.form)
                        .then(response => {
                            this.form.name = '';
                            this.form.scopes = [];
                            this.form.errors = [];

                            this.tokens.push(response.data.token);

                            this.showAccessToken(response.data.accessToken);
                        })
                        .catch(error => {
                            if (typeof error.response.data === 'object') {
                                this.form.errors = _.flatten(_.toArray(error.response.data.errors));
                            } else {
                                this.form.errors = ['Something went wrong. Please try again.'];
                            }
                        });
            },

            /**
             * Toggle the given scope in the list of assigned scopes.
             */
            toggleScope(scope) {
                if (this.scopeIsAssigned(scope)) {
                    this.form.scopes = _.reject(this.form.scopes, s => s == scope);
                } else {
                    this.form.scopes.push(scope);
                }
            },

            /**
             * Determine if the given scope has been assigned to the token.
             */
            scopeIsAssigned(scope) {
                return _.indexOf(this.form.scopes, scope) >= 0;
            },

            /**
             * Show the given access token to the user.
             */
            showAccessToken(accessToken) {
                $('#modal-create-token').modal('hide');

                this.accessToken = accessToken;

                $('#modal-access-token').modal('show');
            },

            /**
             * Revoke the given token.
             */
            revoke(token) {
                axios.delete('/oauth/personal-access-tokens/' + token.id)
                        .then(response => {
                            this.getTokens();
                        });
            }
        }
    }
</script>
