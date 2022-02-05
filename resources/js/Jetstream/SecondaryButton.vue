<template>
    <button :type="type" class="inline-flex items-center px-4 py-2 bg-white dark:bg-slate-500 border border-slate-300 dark:border-slate-600 rounded-md font-semibold text-xs text-slate-700 dark:text-slate-200 uppercase tracking-widest shadow-sm hover:text-slate-500 dark:hover:text-slate-300 focus:outline-none focus:border-blue-300 focus:ring focus:ring-blue-200 active:text-slate-800 active:bg-slate-50 dark:active:bg-slate-600 disabled:opacity-25 transition">
        <slot></slot>
    </button>
</template>

<script>
    import { defineComponent } from 'vue'

    export default defineComponent({
        props: {
            type: {
                type: String,
                default: 'button',
            },
        }
    })
</script>
