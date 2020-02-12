<template>
    <div class="flex flex-col my-8 rounded-lg text-gray-600">
        <div class="flex flex-col items-center justify-between mb-6 sm:flex-row">
            <h3 class="text-gray-800 text-xl font-semibold mb-4 sm:mb-0">OAuth Clients</h3>

            <a class="button--indigo" tabindex="-1" @click="showCreateClientForm">
                <img src="/images/icon-plus.svg" alt="Plus icon" class="mr-2 inline"> Create new client
            </a>
        </div>

        <div class="bg-white flex flex-col min-w-0 rounded break-words shadow">
            <!-- Current Clients -->
            <p class="mb-0 p-8" v-if="clients.length === 0">
                You have not created any OAuth clients.
            </p>

            <table class="flex flex-col w-full" v-if="clients.length > 0">
                <thead class="border-b border-gray-300 p-6 sm:p-8 pb-4">
                    <tr class="flex pb-2 justify-between text-gray-800">
                        <th class="text-left w-1/5 font-semibold hidden md:block">Client ID</th>
                        <th class="text-left font-semibold w-1/5">Name</th>
                        <th class="text-left font-semibold w-1/2">Secret</th>
                        <th class="text-left font-semibold w-1/5"></th>
                    </tr>
                </thead>

                <tbody class="flex flex-col p-6 sm:p-8 sm:pb-4">
                    <tr v-for="client in clients" class="flex justify-between mb-4">
                        <!-- ID -->
                        <td class="w-1/5 align-middle hidden md:block">
                            {{ client.id }}
                        </td>

                        <!-- Name -->
                        <td class="w-1/5 align-middle text-left">
                            {{ client.name }}
                        </td>

                        <!-- Secret -->
                        <td class="w-1/2 align-middle">
                            <code>{{ client.secret }}</code>
                        </td>

                        <!-- Edit/Delete Button -->
                        <td class="flex flex-col w-1/4 align-middle justify-around text-center text-xs sm:text-sm sm:flex-row sm:justify-end md:w-1/5 ">
                            <a class="cursor-pointer border inline-block mb-2 p-2 hover:bg-gray-100 sm:mr-2 hover:border-gray-600" tabindex="-1" @click="edit(client)">
                                Edit
                            </a>

                            <a class="cursor-pointer border inline-block mb-2 p-2 text-red hover:bg-gray-100 hover:border-gray-600" @click="destroy(client)">
                                Delete
                            </a>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Create Client Modal -->
        <div class="modal hide" id="modal-create-client" tabindex="-1" role="dialog">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title">
                            Create Client
                        </h4>

                        <button type="button" class="absolute top-0 right-0 pr-2 pt-1" data-dismiss="modal" aria-hidden="true">&times;</button>
                    </div>

                    <div class="modal-body">
                        <!-- Form Errors -->
                        <div class="relative px-3 py-3 mb-4 border rounded text-red-900 border-red-700 bg-red-300" v-if="createForm.errors.length > 0">
                            <p class="mb-0"><strong>Whoops!</strong> Something went wrong!</p>
                            <br>
                            <ul>
                                <li v-for="error in createForm.errors">
                                    {{ error }}
                                </li>
                            </ul>
                        </div>

                        <!-- Create Client Form -->
                        <form role="form">
                            <!-- Name -->
                            <div class="mb-4 flex flex-wrap">
                                <label class="md:w-1/4 pr-4 pl-4 pt-2 pb-2 mb-0 leading-normal">Name</label>

                                <div class="md:w-3/4 pr-4 pl-4">
                                    <input id="create-client-name" type="text" class="block appearance-none w-full py-1 px-2 mb-1 text-base leading-normal bg-white text-gray-600 border border-grey rounded"
                                                                @keyup.enter="store" v-model="createForm.name">

                                    <span class="block mt-1 text-grey">
                                        Something your users will recognize and trust.
                                    </span>
                                </div>
                            </div>

                            <!-- Redirect URL -->
                            <div class="mb-4 flex flex-wrap">
                                <label class="md:w-1/4 pr-4 pl-4 pt-2 pb-2 mb-0 leading-normal">Redirect URL</label>

                                <div class="md:w-3/4 pr-4 pl-4">
                                    <input type="text" class="block appearance-none w-full py-1 px-2 mb-1 text-base leading-normal bg-white text-gray-600 border border-grey rounded" name="redirect"
                                                    @keyup.enter="store" v-model="createForm.redirect">

                                    <span class="block mt-1 text-grey">
                                        Your application's authorization callback URL.
                                    </span>
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

        <!-- Edit Client Modal -->
        <div class="modal show" id="modal-edit-client" tabindex="-1" role="dialog">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title">
                            Edit Client
                        </h4>

                        <button type="button" class="absolute top-0 right-0 pr-2 pt-1" data-dismiss="modal" aria-hidden="true">&times;</button>
                    </div>

                    <div class="modal-body">
                        <!-- Form Errors -->
                        <div class="relative px-3 py-3 mb-4 border rounded text-red-900 border-red-700 bg-red-300" v-if="editForm.errors.length > 0">
                            <p class="mb-0"><strong>Whoops!</strong> Something went wrong!</p>
                            <br>
                            <ul>
                                <li v-for="error in editForm.errors">
                                    {{ error }}
                                </li>
                            </ul>
                        </div>

                        <!-- Edit Client Form -->
                        <form role="form">
                            <!-- Name -->
                            <div class="mb-4 flex flex-wrap">
                                <label class="md:w-1/4 pr-4 pl-4 pt-2 pb-2 mb-0 leading-normal">Name</label>

                                <div class="md:w-3/4 pr-4 pl-4">
                                    <input id="edit-client-name" type="text" class="block appearance-none w-full py-1 px-2 mb-1 text-base leading-normal bg-white text-gray-600 border border-grey rounded"
                                                                @keyup.enter="update" v-model="editForm.name">

                                    <span class="block mt-1 text-grey">
                                        Something your users will recognize and trust.
                                    </span>
                                </div>
                            </div>

                            <!-- Redirect URL -->
                            <div class="mb-4 flex flex-wrap">
                                <label class="md:w-1/4 pr-4 pl-4 pt-2 pb-2 mb-0 leading-normal">Redirect URL</label>

                                <div class="md:w-3/4 pr-4 pl-4">
                                    <input type="text" class="block appearance-none w-full py-1 px-2 mb-1 text-base leading-normal bg-white text-gray-600 border border-grey rounded" name="redirect"
                                                    @keyup.enter="update" v-model="editForm.redirect">

                                    <span class="block mt-1 text-grey">
                                        Your application's authorization callback URL.
                                    </span>
                                </div>
                            </div>
                        </form>
                    </div>

                    <!-- Modal Actions -->
                    <div class="modal-footer">
                        <button type="button" class="inline-block align-middle text-center select-none border font-normal whitespace-no-wrap py-2 px-4 rounded text-base leading-normal no-underline text-gray-100 bg-gray-500 hover:bg-gray-400" data-dismiss="modal">Close</button>

                        <button type="button" class="inline-block align-middle text-center select-none border font-normal whitespace-no-wrap py-2 px-4 rounded text-base leading-normal no-underline text-blue-100 bg-blue-500 hover:bg-blue-400" @click="update">
                            Save Changes
                        </button>
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
                this.getClients();

                $('#modal-create-client').on('shown.bs.modal', () => {
                    $('#create-client-name').focus();
                });

                $('#modal-edit-client').on('shown.bs.modal', () => {
                    $('#edit-client-name').focus();
                });
            },

            /**
             * Get all of the OAuth clients for the user.
             */
            getClients() {
                axios.get('/oauth/clients')
                        .then(response => {
                            this.clients = response.data;
                        });
            },

            /**
             * Show the form for creating new clients.
             */
            showCreateClientForm() {
                $('#modal-create-client').modal('show');
            },

            /**
             * Create a new OAuth client for the user.
             */
            store() {
                this.persistClient(
                    'post', '/oauth/clients',
                    this.createForm, '#modal-create-client'
                );
            },

            /**
             * Edit the given client.
             */
            edit(client) {
                this.editForm.id = client.id;
                this.editForm.name = client.name;
                this.editForm.redirect = client.redirect;

                $('#modal-edit-client').modal('show');
            },

            /**
             * Update the client being edited.
             */
            update() {
                this.persistClient(
                    'put', '/oauth/clients/' + this.editForm.id,
                    this.editForm, '#modal-edit-client'
                );
            },

            /**
             * Persist the client to storage using the given form.
             */
            persistClient(method, uri, form, modal) {
                form.errors = [];

                axios[method](uri, form)
                    .then(response => {
                        this.getClients();

                        form.name = '';
                        form.redirect = '';
                        form.errors = [];

                        $(modal).modal('hide');
                    })
                    .catch(error => {
                        if (typeof error.response.data === 'object') {
                            form.errors = _.flatten(_.toArray(error.response.data.errors));
                        } else {
                            form.errors = ['Something went wrong. Please try again.'];
                        }
                    });
            },

            /**
             * Destroy the given client.
             */
            destroy(client) {
                axios.delete('/oauth/clients/' + client.id)
                        .then(response => {
                            this.getClients();
                        });
            }
        }
    }
</script>
