<template>
    <div v-for="repository in repositories" :key="repository.id+'repository'" class="flex flex-wrap justify-between w-full px-4 py-2">
        <div class="text-2xl underline cursor-pointer flex flex-wrap items-center gap-2">
            <svg v-if="repository.is_public" class="w-6 h-6" data-darkreader-inline-stroke="" fill="none" stroke="currentColor" style="--darkreader-inline-stroke:currentColor;" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            <svg v-else class="w-6 h-6" data-darkreader-inline-stroke="" fill="none" stroke="currentColor" style="--darkreader-inline-stroke:currentColor;" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"></path></svg>

            <a :href="'/repositories/'+repository.id">{{ repository.name }}</a>
        </div>

        <div class="flex flex-col text-right">
            <div>{{ repository.last_released_at ?? 'Feb 2, 2022 18:39' }}</div>
            <div>{{ repository.last_released_version ?? 'v0.29.1' }}</div>
        </div>
    </div>

</template>

<script>
import { defineComponent } from 'vue'

export default defineComponent({
    props: [ 'repositories' ,],
    setup() {
        
    },
})
</script>
