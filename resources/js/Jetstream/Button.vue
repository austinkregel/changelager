<template>
    <button :type="type" class="inline-flex items-center px-4 py-2 bg-orange-400 dark:bg-amber-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-orange-700 dark:hover:bg-amber-600 active:bg-orange-900 dark:active:bg-amber-900 focus:outline-none focus:border-orange-900 dark:focus:border-amber-900 focus:ring focus:ring-orange-300 dark:focus:ring-amber-600 disabled:opacity-25 transition">
        <slot></slot>
    </button>
</template>

<script>
    import { defineComponent } from 'vue'

    export default defineComponent({
        props: {
            type: {
                type: String,
                default: 'submit',
            },
        }
    })
</script>
