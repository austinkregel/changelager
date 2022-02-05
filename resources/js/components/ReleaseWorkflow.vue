<template>
    <div class="space-y-4">
        <div v-if="hasAccess && !form.body" class="space-y-4">
            <div class="space-y-2">
                <label>Based off branch</label>
                <select v-model="form.hash" class="block appearance-none w-full bg-slate-200 border border-slate-200 text-slate-800 dark:bg-slate-600 dark:border-slate-500 dark:text-slate-100 py-3 px-4 pr-8 rounded-lg">
                    <option v-for="branch in branches" :key="branch" :value="branch.hash">{{ branch.name }}</option>
                </select>
            </div>
        </div>
        <div v-else-if="hasAccess && shouldDisplayLogs" class="">
            <div class="space-y-2 text-white">
                <label>Release type</label>
                <select v-model="form.tag" class="block appearance-none w-full bg-slate-200 border border-slate-200 text-slate-800 dark:bg-slate-600 dark:border-slate-500 dark:text-slate-100 py-3 px-4 pr-8 rounded-lg">
                    <option v-for="tag in releases" :key="tag" :value="tag.value">{{ tag.name }}</option>
                </select>
                <div class="text-red-500 dark:text-red-400 ml-4 text-sm" v-if="form.errors.tag">
                    {{ form.errors.tag}}
                </div>
            </div>
            <textarea v-model="form.body" cols="30" rows="10" class="mt-2 w-full dark:bg-slate-600 border border-blue-lighter p-2 rounded-lg"></textarea>
            <div class="prose dark:prose-invert mt-2 tracking-tight leading-tight" v-html="body"></div>
        </div>

        <div v-if="errors.length > 0" class="w-full py-2 text-red-500 dark:text-red-400 flex flex-col">
            <div v-for="error in errors" :key="error">{{ error }}</div>
        </div>

        <div v-if="tags[0] && hasAccess && logs && logs.length === 0" class="mt-4 w-full dark:bg-slate-600 border border-slate-200 dark:border-slate-500 p-2 rounded-lg">
            No changes have been detected since the last release {{ tags[0].tag }} on {{ tags[0].date }}.
        </div>

        <div class="flex w-full justify-between mt-4">
            
            <button 
                @click="resetApplication"
                :disabled="!hasAccess"
                :class="[!hasAccess ? 'opacity-50 cursor-disabled' : '']" 
                type="button" 
                class="inline-flex items-center px-4 py-2 bg-white dark:bg-slate-500 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-orange-700 dark:hover:bg-amber-600 active:bg-orange-900 dark:active:bg-amber-900 focus:outline-none focus:border-orange-900 dark:focus:border-amber-900 focus:ring focus:ring-orange-300 dark:focus:ring-amber-600 disabled:opacity-25 transition"
            >
                Cancel
            </button>


            <button 
                v-if="!hasAccess"
                :disabled="loading"
                @click="testAccess" 
                type="button" 
                :class="[loading ? 'opacity-50 cursor-disabled' : '']" 
                class="inline-flex items-center px-4 py-2 bg-orange-400 dark:bg-amber-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-orange-700 dark:hover:bg-amber-600 active:bg-orange-900 dark:active:bg-amber-900 focus:outline-none focus:border-orange-900 dark:focus:border-amber-900 focus:ring focus:ring-orange-300 dark:focus:ring-amber-600 disabled:opacity-25 transition"
            >
                <span v-if="!loading">Test Access</span>
                <span v-else class="flex items-center gap-2">
                    Testing access
                    <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                </span>
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
                <span v-else class="flex items-center gap-2">
                    Fetching logs
                    <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                </span>
            </button>
            <button 
                v-if="hasAccess && form.body"
                :disabled="loading || !shouldDisplayLogs"
                @click="createTagAndRelease" 
                type="button" 
                :class="[loading ? 'opacity-50 cursor-disabled' : '']" 
                class="inline-flex items-center px-4 py-2 bg-orange-400 dark:bg-amber-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-orange-700 dark:hover:bg-amber-600 active:bg-orange-900 dark:active:bg-amber-900 focus:outline-none focus:border-orange-900 dark:focus:border-amber-900 focus:ring focus:ring-orange-300 dark:focus:ring-amber-600 disabled:opacity-25 transition"
            >
                <span v-if="!loading">Create release {{form.tag}}</span>
                <span v-else class="flex items-center gap-2">
                    Creating release {{form.tag}} 
                    <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                </span>
            </button>
        </div>            
    </div>
</template>

<script>
import { defineComponent, ref } from 'vue'
import semver from 'semver';
import { marked } from 'marked';

export default defineComponent({
    props: {
        repository: {
            type: Object,
            required: false,
            default: () => ({}),
        },
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
            logs: ref(null),
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
            repositories: ref([]),
            errors: ref([]),
        }
    },

    computed: {
        releases() {
            let { tag } = this.tags[0];

            console.log(this.tags)
            // this.form.tag = (this.repository.use_v_in_version ? 'v':'') + semver.inc(tag, 'patch');
            return this.types.map(type => {
                let value = this.version(semver.inc(tag.replace('v', ''), type, 'alpha'))
                return {
                    value,
                    name: value + ' ' + type.charAt(0).toUpperCase() + type.slice(1) + ' release'
                }
            });
        },
        tag() {
            return this.semverInc(this.form.type);
        },
        body() {
            return marked.parse(this.form.body, {
                sanitized: true,
            });
        },
        shouldDisplayLogs() {
            return this.logs && this.logs.length > 0;
        }
    },

    methods: {
        semverInc(type){
            return this.version(semver.inc(this.form.tag, type))
        },
        version(number) {
            const prefix = (this.repository.use_v_in_version && !number?.toLowerCase()?.startsWith('v') ? 'v':'')

            return prefix + number;
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
                    url: this.form.url,
                })
                .then(({ data }) => {
                    this.tags = data;
                    this.form.tag = data[0].tag
                    this.hasAccess = true;
                    this.errors = [];
                    resolve();
                })
                .catch((e) => {
                    this.errors = [e.response.data.message];
                    reject();
                })
                .finally(() => {
                    this.loading = false;
                })
            })
        },
        fetchBranches() {
            return new Promise ((resolve , reject) => {
                this.loading = true;
                axios.post('/api/fetch-branches', {
                    url: this.form.url,
                })
                .then(({ data }) => {
                    this.branches = data;
                    this.hasAccess = true;
                    this.errors = [];
                    resolve()
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
                    url: this.form.url,
                })
                .then(({ data }) => {
                    this.hasAccess = true;
                    this.errors = [];
                    resolve();
                })
                .catch((error) => {
                    this.errors = [error.response.data.message]
                })
                .finally(() => {
                    this.loading = false;
                })
            });
        },
        createTagAndRelease() {
            this.loading = true;
            axios.post('/api/create-tag', {
                url: this.form.url,
                release_version: this.form.tag,
                ref: this.form.hash,
                body: this.form.body,
            })
            .then(async () => {
                await this.fetchTags();
                this.errors = [];
                window.location.replace('/repositories/' + this?.repository?.id);
            })
            .catch((error) => {
                this.form.errors = {
                    tag: error.response.data.message,
                };
            })
            .finally(() => {
                this.loading = false;
            })
        },
        fetchLogs() {
            return new Promise ((resolve , reject) => {
                this.loading = true;
                axios.post('/api/fetch-logs', {
                    url: this.form.url,
                    ref: this.form.hash,
                    release_version: this.tags[0].tag,
                })
                .then(({ data }) => {
                    this.logs = data;
                    this.form.body = "## Release notes\n\n" + data.map(commit => ' * ' + commit.description.trim()).join("\n") + "\n\n" + 'A release from [Change Lager](https://changelager.app)'
                    this.hasAccess = true;
                    this.errors = [];
                    resolve();
                })
                .catch((e) => {
                    if (e?.response?.data?.errors) {
                        Object.keys(e.response.data.errors).forEach(key => {
                            this.errors = this.errors.concat(e.response.data.errors[key]);
                        });
                    } else {
                        this.errors = [e.message];
                    }
                    reject();
                })
                .finally(() => {
                    this.loading = false;
                })
            });
        },
        resetApplication() {
            this.form = {
                url: '',
                tag: '0.0.1',
                name: '',
                hash: '',
                body: '',
                errors: {},
            };
            this.tags = [];
            this.branches = [];
            this.logs = null;
            this.hasAccess = false;
            this.form.url = this?.repository?.url;
        }
    },
    mounted() {
        this.form.url = this?.repository?.url;
    },
})
</script>