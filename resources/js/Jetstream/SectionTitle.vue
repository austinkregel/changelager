<template>
    <div class="md:col-span-1 flex justify-between">
        <div class="px-4 sm:px-0">
            <h3 class="text-lg font-medium text-slate-900 dark:text-slate-50">
                <slot name="title"></slot>
            </h3>

            <p class="mt-1 text-sm text-slate-600 dark:text-slate-300">
                <slot name="description"></slot>
            </p>
        </div>

        <div class="px-4 sm:px-0">
            <slot name="aside"></slot>
        </div>
    </div>
</template>
