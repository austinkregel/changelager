<template>
    <app-layout title="Repositories">
        <template #header>
            <div class="flex justify-between items-center">
                <h2 class="font-semibold text-xl text-slate-800 dark:text-slate-100 leading-tight">
                    Repositories
                </h2>

                <jet-nav-link href="/repositories/new" class="py-1 px-2 border border-orange-500 text-orange-500 dark:text-amber-400 rounded">
                    Add Repository
                </jet-nav-link>
            </div>
        </template>

        <div class="max-w-xl w-full mx-auto">
            <div class="mt-8 bg-white dark:bg-slate-700 dark:text-slate-200 overflow-hidden shadow sm:rounded-lg">
                <div class="mt-5 md:mt-0 md:col-span-2">
                    <div v-if="$attrs.repositories.length !== 0" class="shadow sm:rounded-md sm:overflow-hidden divide-y divide-slate-200 dark:divide-slate-600">
                        <repository-item :repositories="$attrs.repositories" />
                    </div>
                    <div v-else>
                        <div class="p-4 text-center shadow bg-white dark:bg-slate-700">
                            <p class="text-slate-100">
                                You don't have any repositories yet.
                            </p>
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
    import AppLayout from '@/Layouts/AppLayout.vue'
    import JetNavLink from '@/Jetstream/NavLink.vue'
    import RepositoryItem from '@/components/RepositoryIndexItem.vue'

    export default defineComponent({
        components: {
            Head,
            Link,
            AppLayout,
            JetNavLink,
            RepositoryItem
        },

        setup() {
            return {
            }
        },
    })
</script>
