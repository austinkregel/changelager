<template>
    <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 bg-slate-100 dark:bg-slate-900">
        <div>
            <slot name="logo" />
        </div>

        <div class="w-full sm:max-w-md mt-6 px-6 py-4 bg-white dark:bg-slate-800 shadow-md overflow-hidden sm:rounded-lg">
            <slot />
        </div>

        <div class="text-right mt-4 w-full sm:max-w-md">
            <slot name="registerLink" />
        </div>
    </div>
</template>
