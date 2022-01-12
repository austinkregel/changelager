<template>
    <app-layout title="Dashboard">
        <template #header>
            <h2 class="font-semibold text-xl text-slate-800 dark:text-slate-100 leading-tight">
                Dashboard
            </h2>
        </template>

        <div class="max-w-xl w-full mx-auto">
            <div class="mt-8 bg-white dark:bg-slate-700 dark:text-slate-200 overflow-hidden shadow sm:rounded-lg">
                <div class="mt-5 md:mt-0 md:col-span-2">
                    <div class="shadow sm:rounded-md sm:overflow-hidden">
                        <div v-if="!hasAccess" class="px-4 py-5 space-y-4 sm:p-6">
                            <div class="col-span-6">
                                <div class="col-span-6 sm:col-span-3">
                                    <label for="country" class="block text-sm font-medium text-slate-700 dark:text-slate-300">Repository</label>
                                    <select id="country" name="country" autocomplete="country-name" class="mt-1 block w-full bg-white dark:bg-slate-600 border border-slate-300 dark:border-slate-500 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-orange-500 focus:border-orange-500 sm:text-sm">
                                        <option>United States</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div v-else-if="hasAccess && tags?.length === 0">
                            
                            <div>
                                <label>Based off tag</label>
                                <select v-model="form.tag" class="block appearance-none w-full bg-slate-200 border border-slate-200 text-slate-800 dark:bg-slate-600 dark:border-slate-500 dark:text-slate-100 py-3 px-4 pr-8 rounded-lg">
                                    <option v-for="tag in releases" :key="tag" :value="tag">{{ tag }}</option>
                                </select>
                            </div>

                        </div>
                        <div v-else-if="hasAccess && !form.body" class="px-4 py-5 space-y-4 sm:p-6">
                            <div class="space-y-2">
                                <label>Based off branch</label>
                                <select v-model="form.hash" class="block appearance-none w-full bg-slate-200 border border-slate-200 text-slate-800 dark:bg-slate-600 dark:border-slate-500 dark:text-slate-100 py-3 px-4 pr-8 rounded-lg">
                                    <option v-for="branch in branches" :key="branch" :value="branch.hash">{{ branch.name }}</option>
                                </select>
                            </div>
                        </div>
                        <div v-else class="p-4">

                            <div class="space-y-2 text-white">
                                <label>Release Type</label>
                                <select v-model="form.type" class="block appearance-none w-full bg-slate-200 border border-slate-200 text-slate-800 dark:bg-slate-600 dark:border-slate-500 dark:text-slate-100 py-3 px-4 pr-8 rounded-lg">
                                    <option v-for="type in types" :key="type" :value="type">{{ type }} ({{semverInc(type)}})</option>
                                </select>
                            </div>

                            <textarea v-model="form.body" cols="30" rows="10" class="mt-4 w-full dark:bg-slate-600 border border-blue-lighter p-2 rounded-lg"></textarea>
                            <div class="prose dark:prose-invert mt-2 tracking-tight leading-tight" v-html="body"></div>
                        </div>
                        {{ form.errors.tag}}

                        <div class="px-2 py-3 bg-slate-50 dark:bg-slate-600 text-right">
                            <button 
                                v-if="!hasAccess"
                                :disabled="loading"
                                @click="testAccess" 
                                type="button" 
                                :class="[loading ? 'opacity-50 cursor-disabled' : '']" 
                               class="inline-flex items-center px-4 py-2 bg-orange-400 dark:bg-amber-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-orange-700 dark:hover:bg-amber-600 active:bg-orange-900 dark:active:bg-amber-900 focus:outline-none focus:border-orange-900 dark:focus:border-amber-900 focus:ring focus:ring-orange-300 dark:focus:ring-amber-600 disabled:opacity-25 transition"
                            >
                                <span v-if="!loading">Test Access</span>
                                <span v-else>Testing access</span>
                            </button>
                            
                            <button 
                                v-if="hasAccess && !form.body"
                                :disabled="loading"
                                @click="fetchLogs" 
                                type="button" 
                                :class="[loading ? 'opacity-50 cursor-disabled' : '']" 
                                class="inline-flex items-center px-4 py-2 bg-orange-400 dark:bg-amber-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-orange-700 dark:hover:bg-amber-600 active:bg-orange-900 dark:active:bg-amber-900 focus:outline-none focus:border-orange-900 dark:focus:border-amber-900 focus:ring focus:ring-orange-300 dark:focus:ring-amber-600 disabled:opacity-25 transition"
                            >
                                <span v-if="!loading">Fetch log</span>
                                <span v-else>Fetching logs</span>
                            </button>
                            <button 
                                v-if="hasAccess && form.body"
                                :disabled="loading"
                                @click="createTagAndRelease" 
                                type="button" 
                                :class="[loading ? 'opacity-50 cursor-disabled' : '']" 
                                class="inline-flex items-center px-4 py-2 bg-orange-400 dark:bg-amber-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-orange-700 dark:hover:bg-amber-600 active:bg-orange-900 dark:active:bg-amber-900 focus:outline-none focus:border-orange-900 dark:focus:border-amber-900 focus:ring focus:ring-orange-300 dark:focus:ring-amber-600 disabled:opacity-25 transition"
                            >
                                <span v-if="!loading">Create release {{tag}}</span>
                                <span v-else>Creating release {{tag}}</span>
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
                    Changelager v3.0.0
                </div>
            </div>
        </div>
    </app-layout>
</template>
<script>
    import { defineComponent, ref } from 'vue'
    import { Head, Link } from '@inertiajs/inertia-vue3';
    import semver from 'semver';
    import { marked } from 'marked';
    import AppLayout from '@/Layouts/AppLayout.vue'

    export default defineComponent({
        components: {
            Head,
            Link,
            AppLayout,
        },

        props: {
            canLogin: Boolean,
            canRegister: Boolean,
            laravelVersion: String,
            phpVersion: String,
        },

        setup() {
            const form = ref({
                url: '',
                tag: '0.0.1',
                name: '',
                hash: '',
                body: '',
                errors: {},
            });
            return {
                form,
                loading: ref(false),
                tags: ref([]),
                branches: ref([]),
                logs: ref([]),
                hasAccess: ref(false),
                types: ref([
                    'major',
                    'premajor',
                    'minor',
                    'preminor',
                    'patch',
                    'prepatch',
                    'prerelease',
                ]),
                initialReleases: [
                    {
                        value: '0.0.1',
                    },
                    {
                        value: '0.1.0',
                    },
                    {
                        value: '1.0.0',
                    },
                ],
                repositories: ref([]),
            }
        },

        computed: {
            releases() {
                let release = _.first(this.tags);
                if (!release) {
                    return this.initialReleases
                }
                this.form.tag = semver.inc(release.tag, 'patch');
                return this.types.map(type => {
                    let value = semver.inc(release.tag, type, 'alpha')
                    return {
                        value,
                        name: value + ' ' + type.charAt(0).toUpperCase() + type.slice(1) + ' release'
                    }
                });
            },
            tag() {
                return semver.inc(this.form.tag, this.form.type);
            },
            body() {
                return marked.parse(this.form.body, {
                    sanitized: true,
                });
            }
        },

        methods: {
            semverInc(type){
                return semver.inc(this.form.tag, type);
            },
            async testAccess() {
                await this.cloneRepo();
                this.fetchTags();
                this.fetchBranches();
            },
            fetchTags() {
                return new Promise ((resolve , reject) => {
                    this.loading = true;
                    axios.post('/api/fetch-tags', {
                        url: this.form.repoAddress,
                    })
                    .then(({ data }) => {
                        this.tags = data;
                        this.form.tag = data[data.length - 1]
                        this.hasAccess = true;
                        resolve();
                    })
                    .catch(reject)
                    .finally(() => {
                        this.loading = false;
                    })
                })
            },
            
            fetchBranches() {
                return new Promise ((resolve , reject) => {
                    this.loading = true;
                    axios.post('/api/fetch-branches', {
                        url: this.form.repoAddress,
                    })
                    .then(({ data }) => {
                        this.branches = data;
                        this.hasAccess = true;
                        resolve()
                    })
                    .catch(reject)
                    .finally(() => {
                        this.loading = false;
                    })
                });
            },
            
            fetchLogs() {
                return new Promise ((resolve , reject) => {
                    this.loading = true;
                    axios.post('/api/fetch-logs', {
                        url: this.form.repoAddress,
                        ref: this.form.hash,
                        release_version: this.tags[this.tags.length - 1],
                    })
                    .then(({ data }) => {
                        this.logs = data;
                        this.form.body = "## Release notes\n\n" + data.map(commit => ' * ' + commit.description.trim()).join("\n") + "\n\n" + 'A release from [Change Lager](https://changelager.app)'
                        this.hasAccess = true;
                        resolve();
                    })
                    .catch(reject)
                    .finally(() => {
                        this.loading = false;
                    })
                });
            },

            cloneRepo() {
                return new Promise ((resolve , reject) => {
                    this.loading = true;
                    axios.post('/api/clone', {
                        url: this.form.repoAddress,
                    })
                    .then(({ data }) => {
                        this.hasAccess = true;
                        resolve();
                    })
                    .catch(reject)
                    .finally(() => {
                        this.loading = false;
                    })
                });
            },

            createTagAndRelease() {
                return new Promise ((resolve , reject) => {
                    this.loading = true;
                    axios.post('/api/create-tag', {
                        url: this.form.repoAddress,
                        release_version: this.tag,
                        ref: this.form.hash,
                        body: this.form.body,
                    })
                    .then(resolve)
                    .catch((error) => {
                        this.form.errors = {
                            tag: error.response.data.message,
                        };
                        reject();
                    })
                    .finally(() => {
                        this.loading = false;
                    })
                });
            },

            fetchRepositories() {
                return new Promise ((resolve , reject) => {
                    this.loading = true;
                    axios.get('/api/repositories')
                    .then(({ data }) => {
                        this.repositories = data;
                        resolve();
                    })
                    .catch(reject)
                    .finally(() => {
                        this.loading = false;
                    })
                });
            }
        },
        mounted() {
            this.fetchRepositories();
        },
    })
</script>
