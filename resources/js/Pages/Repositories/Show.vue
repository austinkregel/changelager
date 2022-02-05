<template>
    <app-layout title="Dashboard">
        <template #header>
            <div class="flex justify-between items-center">
                <h2 v-if="repository" class="font-semibold text-xl text-slate-800 dark:text-slate-100 leading-tight">
                    {{ repository.name}}
                </h2>
            </div>
        </template>

        <div class="max-w-xl w-full mx-auto space-y-4 pt-4">
            <div class="w-full flex justify-between">
                <a href="/repositories" class="flex flex-wrap items-center gap-2">
                    <svg class="w-6 h-6" data-darkreader-inline-stroke="" fill="none" stroke="currentColor" style="--darkreader-inline-stroke:currentColor;" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                    Go Back
                </a>

                <button @click="confirmDelete = true" class="flex items-center gap-2 text-red-500">
                    Delete this
                    <svg class="w-6 h-6" data-darkreader-inline-stroke="" fill="none" stroke="currentColor" style="--darkreader-inline-stroke:currentColor;" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                </button>

            </div>
            <div class="bg-white dark:bg-slate-700 dark:text-slate-200 overflow-hidden shadow sm:rounded-lg">
                <div class="mt-5 md:mt-0 md:col-span-2" v-if="repository">
                    <div class="shadow sm:rounded-md sm:overflow-hidden p-4 space-y-2">
                        <div class="my-2 font-bold text-2xl">{{ repository.name }}</div>

                        <div class="flex items-center gap-2">
                            <toggle-switch v-model="repository.is_public" />

                            <div>Is publically available</div>
                        </div>
                        <div class="flex items-center gap-2 ">
                            <toggle-switch v-model="repository.use_v_in_version" />

                            <div>Use V in the version number</div>
                        </div>
                        <div class="flex flex-col  gap-2" v-if="$attrs.user?.current_team_id === repository.team_id">
                            <jet-label value="Deploy key with write access" />
                            <div class="text-xs break-all w-full border border-slate-400 bg-blue-50 dark:border-slate-500 dark:bg-slate-600 dark:text-slate-50  rounded-lg px-2 py-2">{{repository.public_key}}</div>
                        </div>

                        <release-workflow :repository="repository" />
                        <div v-if="logs.length > 0">
                            {{ logs }}
                        </div>
                    </div>
                </div>
                <div v-else>
                    Loading...
                </div>
            </div>

            <div class="flex flex-col gap-4">
                <div v-for="release in repository.releases" :key="'release-key-' + release.version" class="bg-white dark:bg-slate-700 rounded p-4">
                    <h2 class="text-2xl leading-6 font-medium text-slate-900 dark:text-slate-200">{{ release.version }}</h2>
                    <h6 class="text-sm leading-6 font-medium text-slate-900 dark:text-slate-300">on {{ formatDate(release.updated_at) }}</h6>

                    <div class="mt-2 max-w-xl text-sm text-slate-500">
                        <div class="prose dark:prose-invert mt-2 tracking-tight leading-tight" v-html="marked.parse(release.notes, { sanitized: true, })"></div>
                    </div>

                    <div class="text-xs mt-2 text-slate-800 dark:text-slate-200 tracking-wide">Released hash: {{ release.hash.substr(0, 16) }}</div>
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

            <Modal
            :show="confirmDelete"
            maxWidth="xl"
            :closeable="true"
            > 
                    <div class="inline-block align-bottom rounded-lg px-4 text-left overflow-hidden transform transition-all sm:align-middle sm:w-full sm:p-6">
                        <div class="hidden sm:block absolute top-0 right-0 pt-4 pr-4">
                            <button type="button" class="rounded-md text-slate-400 hover:text-slate-500 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500" @click="open = false">
                                <span class="sr-only">Close</span>
                                <svg class="w-6 h-6" data-darkreader-inline-stroke="" fill="none" stroke="currentColor" style="--darkreader-inline-stroke:currentColor;" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                            </button>
                        </div>
                        <div class="sm:flex sm:items-start">
                            <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-red-100 sm:mx-0 sm:h-10 sm:w-10">
                                <svg class="w-6 h-6 text-red-600" data-darkreader-inline-stroke="" fill="none" stroke="currentColor" style="--darkreader-inline-stroke:currentColor;" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                            </div>
                            <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
                                <DialogTitle as="h3" class="text-lg leading-6 font-medium text-slate-900 dark:text-slate-50"> Deactivate Repository </DialogTitle>
                                <div class="mt-2">
                                    <p class="text-sm text-slate-500 dark:text-slate-300">Are you sure you want to deactivate your account? This repository, all of its tags, and the deploy key will be removed from our servers forever. This action cannot be undone.</p>
                                </div>
                            </div>
                        </div>
                        <div class="mt-5 sm:mt-4 sm:flex sm:flex-row-reverse">
                            <button @click="destroy" type="button" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-red-600 text-base font-medium text-white hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 sm:ml-3 sm:w-auto sm:text-sm">Deactivate</button>
                            <SecondaryButton @click="confirmDelete = false" type="button" >Cancel</SecondaryButton>
                        </div>
                    </div>                
            </Modal>
        </div>
    </app-layout>
</template>
<script>
    import { defineComponent, ref } from 'vue'
    import { Head, Link } from '@inertiajs/inertia-vue3';
    import AppLayout from '@/Layouts/AppLayout.vue'
    import JetNavLink from '@/Jetstream/NavLink.vue'
    import JetLabel from '@/Jetstream/Label.vue'
    import JetButton from '@/Jetstream/Button.vue'
    import SecondaryButton from '@/Jetstream/SecondaryButton.vue'
    import ToggleSwitch from '@/components/ToggleSwitch.vue'
    import ReleaseWorkflow from '@/components/ReleaseWorkflow.vue'
    import Modal from '@/Jetstream/Modal.vue'
    import { marked } from 'marked';
    import dayjs from 'dayjs';

    export default defineComponent({
        components: {
            Head,
            Link,
            AppLayout,
            JetNavLink,
            ToggleSwitch,
            JetLabel,
            JetButton,
            ReleaseWorkflow,
            Modal,
            SecondaryButton
        },

        setup() {
            return {
                logs: ref([]),
                enabled: ref(false),
                form: ref({
                    url: '',
                    hash: '',
                    tag: '',
                }),
                tags: ref([]),
                confirmDelete: ref(false),
                marked,
                dayjs
            }
        },

        methods: {
            fetchLogs() {
                if (!this.tag) {
                    return;
                }

                return new Promise ((resolve , reject) => {
                    this.loading = true;
                    axios.post('/api/fetch-logs', {
                        url: this.form.url,
                        ref: this.form.hash,
                        release_version: this.tag,
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
            toggleUseVInVersion() {
                axios.put('/api/repositories/' + this.repository.id, {
                    use_v_in_version: this.repository.use_v_in_version,
                }).then(({ data }) => {

                })
                .catch(error => {
                    console.log(error);
                });
            },
            togglePublic() {
                axios.put('/api/repositories/' + this.repository.id, {
                    is_public: this.repository.is_public,
                }).then(({ data }) => {

                })
                .catch(error => {
                    console.log(error);
                });
            },
            destroy() {
                axios.delete('/api/repositories/' + this.repository.id)
                .then(() => {
                    window.location.replace('/repositories');
                })
                .catch(error => {
                    console.log(error);
                });
            },
            formatDate(date) {
                return dayjs(date).format('MMM DD, YYYY hh:mm A');
            },
        },
        computed: {
            repository() {
                return this.$attrs.repository;
            },
        },
        watch: {
            'repository.use_v_in_version': {
                immediate: true,
                handler(value, oldValue) {
                    if (oldValue === undefined) {
                        return;
                    }
                     this.toggleUseVInVersion();
                }
            },
            'repository.is_public': {
                immediate: true,
                handler(value, oldValue) {
                    if (oldValue === undefined) {
                        return;
                    }

                    this.togglePublic();
                }
            },
        },
        mounted() {
            this.fetchLogs();
        }
    })
</script>
