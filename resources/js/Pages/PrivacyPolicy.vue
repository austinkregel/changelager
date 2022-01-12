<template>
    <Head title="Privacy Policy" />

    <div class="font-sans text-slate-900 dark:text-slate-100 antialiased">
        <div class="pt-4 bg-slate-100 dark:bg-slate-900 ">
            <div class="min-h-screen flex flex-col items-center pt-6 sm:pt-0">
                <div>
                    <jet-authentication-card-logo />
                </div>

                <div v-html="policy" class="w-full sm:max-w-2xl mt-6 p-6 bg-white dark:bg-slate-700 shadow-md overflow-hidden sm:rounded-lg prose dark:prose-invert">
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import { defineComponent } from 'vue'
import { Head } from '@inertiajs/inertia-vue3';
import JetAuthenticationCardLogo from '@/Jetstream/AuthenticationCardLogo.vue'

export default defineComponent({
    props: ['policy'],

    components: {
        Head,
        JetAuthenticationCardLogo,
    },
})
</script>
