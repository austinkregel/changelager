<template>
    <app-layout title="Dashboard">
        <template #header>
            <h2 class="font-semibold text-xl text-slate-800 dark:text-slate-100 leading-tight">
                Repositories
            </h2>
        </template>

        <div class="max-w-xl w-full mx-auto">
            <div class="mt-8 bg-white dark:bg-slate-700 dark:text-slate-200 overflow-hidden shadow sm:rounded-lg">
                <div class="mt-5 md:mt-0 md:col-span-2">
                    <div class="shadow sm:rounded-md sm:overflow-hidden">
                        <div class="px-4 py-5 space-y-4 sm:p-6">
                            <div v-if="form.name" class="col-span-6">
                                <label for="name" class="block font-medium">Name</label>
                                <input v-model="form.name" @input="onNameChange" placeholder="finance.git" type="text" name="repo-address"  class="dark:bg-slate-800 dark:border-slate-600 mt-2 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-slate-300 rounded-md" />
                            </div>

                            <div v-if="form.slug" class="col-span-6">
                                <label for="slug" class="block font-medium">Slug</label>
                                <input v-model="form.slug" placeholder="finance.git" type="text" name="repo-address" disabled class="opacity-75 dark:bg-slate-800 dark:border-slate-600 mt-2 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-slate-300 rounded-md" />
                            </div>

                            <div class="col-span-6">
                                <label for="repo-address" class="block font-medium">Repository URL</label>
                                <input v-model="form.url" @input="onRepoChange" placeholder="https://github.com/austinkregel/finance.git" type="text" name="repo-address" id="repo-address" autocomplete="repo-address" class="dark:bg-slate-800 dark:border-slate-600 mt-2 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-slate-300 rounded-md" />
                            </div>
                            <div class="space-y-2">
                                <div class="flex items-start">
                                    <div class="h-5 flex items-center">
                                        <input v-model="form.is_public" id="is_public" name="is_public" type="checkbox" class="focus:ring-indigo-500 h-4 w-4 text-indigo-600 dark:text-indigo-200 border-slate-300 rounded" />
                                    </div>
                                    <div class="ml-3 text-sm">
                                        <label for="is_public" class="font-medium text-slate-700 dark:text-slate-100">Public Log</label>
                                        <p class="text-slate-500 dark:text-slate-300 flex">Makes the changelog available via <pre class="ml-1 text-orange-400">changed.to</pre>.</p>
                                    </div>
                                </div>
                                
                                <div class="flex items-start">
                                    <div class="h-5 flex items-center">
                                        <input v-model="form.use_v_in_version" id="use_v_in_version" name="use_v_in_version" type="checkbox" class="focus:ring-indigo-500 h-4 w-4 text-indigo-600 dark:text-indigo-200 border-slate-300 rounded" />
                                    </div>
                                    <div class="ml-3 text-sm">
                                        <label for="use_v_in_version" class="font-medium text-slate-700 dark:text-slate-100">Use V in Version Number</label>
                                        <div class="text-slate-500 dark:text-slate-300">This tells Changelager to put a lowercase v at the start of the version number.<p> ex: v10.3.1</p></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        {{ form.errors.tag}}

                        
                        <div class="px-2 py-3 bg-slate-50 dark:bg-slate-600 flex justify-between items-center w-full">
                            <a href="/repositories"
                               class="inline-flex items-center px-4 py-2 bg-orange-400 dark:bg-slate-500 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-orange-700 dark:hover:bg-amber-600 active:bg-orange-900 dark:active:bg-amber-900 focus:outline-none focus:border-orange-900 dark:focus:border-amber-900 focus:ring focus:ring-orange-300 dark:focus:ring-amber-600 disabled:opacity-25 transition"
                            >
                            Cancel
                            </a>

                            <button 
                                :disabled="loading"
                                @click="save" 
                                type="button" 
                                :class="[loading ? 'opacity-50 cursor-disabled' : '']" 
                               class="inline-flex items-center px-4 py-2 bg-orange-400 dark:bg-amber-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-orange-700 dark:hover:bg-amber-600 active:bg-orange-900 dark:active:bg-amber-900 focus:outline-none focus:border-orange-900 dark:focus:border-amber-900 focus:ring focus:ring-orange-300 dark:focus:ring-amber-600 disabled:opacity-25 transition"
                            >
                                <span v-if="!loading">Save Repository</span>
                                <span v-else>Saving Repository</span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="flex justify-center mt-4 sm:items-center sm:justify-between text-slate-500 dark:text-slate-300">
                <div class="text-center text-sm sm:text-left">
                    <div class="flex items-center">
                        <svg fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" viewBox="0 0 24 24" class="-mt-px w-5 h-5 text-red-400 dark:text-red-600">
                            <path d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>
                        </svg>

                        <a target="_blank" href="https://github.com/sponsors/austinkregel" class="ml-1 underline">
                            Sponsor
                        </a>
                    </div>
                </div>

                <div class="ml-4 text-center text-sm sm:text-right sm:ml-0">
                    Changelager {{$attrs.version}}
                </div>
            </div>
        </div>
    </app-layout>
</template>
<script>
    import { defineComponent, ref } from 'vue'
    import { Head, Link } from '@inertiajs/inertia-vue3';
    import AppLayout from '@/Layouts/AppLayout.vue'

    export default defineComponent({
        components: {
            Head,
            Link,
            AppLayout,
        },

        setup() {
            const form = ref({
                name: '',
                slug: '',
                url: '',
                is_public: false,
                use_v_in_version: false,
                errors: {},
            });
            return {
                form,
                loading: ref(false),
                hasAccess: ref(false),
            }
        },

        methods: {
            onRepoChange() {
                this.form.name = this.form.url.split('/').reverse()[0];
                this.form.slug = this.form.name.replace('.', '-');
            },
            onNameChange() {
                this.form.slug = this.form.name.replace(/[\W_]+/g, '-');
            },
            async testAccess() {
                await this.cloneRepo();
            },
            cloneRepo() {
                return new Promise ((resolve , reject) => {
                    this.loading = true;
                    axios.post('/api/clone', {
                        url: this.form.repoAddress,
                    })
                    .then(({ data }) => {
                        this.hasAccess = true;
                        this.errors = [];
                        resolve();
                    })
                    .catch(reject)
                    .finally(() => {
                        this.loading = false;
                    })
                });
            },

            save() {
                return new Promise((resolve, reject) => {
                    axios.post('/api/repositories', this.form)
                    .then(({ data }) => {
                        window.location = '/repositories/' + data.id;

                        resolve(data);
                    })
                    .catch(reject)
                });
            }
        }
    })
</script>
