<template>
    <Link href="/" class="flex items-center gap-4 text-5xl text-orange-400 dark:text-amber-600 uppercase dark:text-slate-200" style="font-family: 'Permanent Marker', sans-serif;">
        <jet-application-mark class="w-16 h-16 w-auto" />
        Changelager
    </Link>
</template>

<script>
    import { defineComponent } from 'vue'
    import { Link } from '@inertiajs/inertia-vue3';
    import JetApplicationMark from '@/Jetstream/ApplicationMark.vue'

    export default defineComponent({
        components: {
            JetApplicationMark,
        },
    })
</script>
