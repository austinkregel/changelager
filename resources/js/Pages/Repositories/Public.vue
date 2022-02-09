<template>
    <app-layout :title="repository?.name">
        <template #header>
            <div v-if="repository" class="flex justify-between items-center">
                <div>
                    <h2 class="font-semibold text-3xl text-slate-800 dark:text-slate-100 leading-tight">
                        {{ repository.name}}
                    </h2>
                    
                    <div class="text-base text-slate-200">
                        {{ repository.releases.filter(release => release.version === repository.last_released_version)[0]?.hash?.substr(0, 16)}}
                    </div>
                </div>
                <div class="text-right">
                    <div class="flex items-center text-xl gap-2" v-if="repository.last_released_version">
                        <span class="leading-tight font-semibold tracking-wide text-slate-600 dark:text-slate-200">{{ repository.last_released_version}}</span>
                        <span class="uppercase text-slate-100 dark:text-slate-300" >Latest version</span>
                    </div>
                    <div v-if="repository.last_released_at" class="text-base text-slate-200">
                        {{ repository.last_released_at}}
                    </div>
                </div>
            </div>
        </template>

        <div class="max-w-xl w-full mx-auto space-y-4 pt-4">
            <div class="flex flex-col gap-4">
                <div v-for="release in repository.releases" :key="'release-key-' + release.version" class="bg-white dark:bg-slate-700 rounded p-4">
                    <h2 class="text-2xl leading-6 font-medium text-slate-900 dark:text-slate-200">{{ release.version }}</h2>
                    <h6 class="text-sm leading-6 font-medium text-slate-900 dark:text-slate-300">on {{ formatDate(release.released_at) }}</h6>

                    <div class="mt-2 max-w-xl text-sm text-slate-500">
                        <div class="prose dark:prose-invert mt-2 tracking-tight leading-tight" v-html="marked.parse(release?.notes ?? '', { sanitized: true, })"></div>
                    </div>

                    <div class="text-xs mt-2 text-slate-800 dark:text-slate-200 tracking-wide">Released hash: {{ release.hash.substr(0, 16) }}</div>
                </div>

                <div v-if="!repository.releases.length" class="space-y-4 text-center text-slate-700 dark:text-slate-200 bg-white dark:bg-slate-700 p-4 mt-2 mb-8 rounded">
                    <p class="max-w-lg mx-auto tracking-wide leading-loose">No releases yet. If you're the author, you can create a new release by clicking the button below.</p>
                    <jet-button @click="window.location.replace(route('repositories:id', { repository: repository?.id }))">
                        Create a release
                    </jet-button>
                </div>

            </div>

            <div class="flex justify-center pb-4 sm:items-center sm:justify-between text-slate-500 dark:text-slate-300">
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
    import AppLayout from '@/Layouts/GuestLayout.vue'
    import JetNavLink from '@/Jetstream/NavLink.vue'
    import JetLabel from '@/Jetstream/Label.vue'
    import JetButton from '@/Jetstream/Button.vue'
    import ToggleSwitch from '@/components/ToggleSwitch.vue'
    import ReleaseWorkflow from '@/components/ReleaseWorkflow.vue'
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
        },

        setup() {
            return {
                logs: ref([]),
                enabled: ref(false),
                tags: ref([]),
                marked,
                dayjs,
                window
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
            formatDate(date) {
                return dayjs(date).format('MMM DD, YYYY hh:mm A');
            },
        },
        computed: {
            repository() {
                return this.$attrs.repository;
            },
        },
        mounted() {
            this.fetchLogs();
        }
    })
</script>
