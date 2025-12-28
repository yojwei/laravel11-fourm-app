<template>
    <div class="flex items-center justify-between border-t border-white/10 px-4 py-3 sm:px-6">
        <div class="flex flex-1 justify-between sm:hidden">
            <a href="#"
                class="relative inline-flex items-center rounded-md border border-white/10 bg-white/5 px-4 py-2 text-sm font-medium text-gray-200 hover:bg-white/10">Previous</a>
            <a href="#"
                class="relative ml-3 inline-flex items-center rounded-md border border-white/10 bg-white/5 px-4 py-2 text-sm font-medium text-gray-200 hover:bg-white/10">Next</a>
        </div>
        <div class="hidden sm:flex sm:flex-1 sm:items-center sm:justify-between">
            <div>
                <p class="">
                    Showing
                    {{ ' ' }}
                    <span class="font-medium">{{ meta.from }}</span>
                    {{ ' ' }}
                    to
                    {{ ' ' }}
                    <span class="font-medium">{{ meta.to }}</span>
                    {{ ' ' }}
                    of
                    {{ ' ' }}
                    <span class="font-medium">{{ meta.total }}</span>
                    {{ ' ' }}
                    results
                </p>
            </div>
            <div>
                <nav class="isolate inline-flex -space-x-px rounded-md" aria-label="Pagination">
                    <!-- Current: "z-10 text-white focus-visible:outline-2 focus-visible:outline-offset-2 bg-indigo-500 focus-visible:outline-indigo-500", Default: "inset-ring focus:outline-offset-0 text-gray-200 inset-ring-gray-700 hover:bg-white/5" -->
                    <a v-for="link in meta.links" :href="link.url" v-html="link.label"
                        class="relative z-10 inline-flex items-center first-of-type:rounded-l-md last-of-type:rounded-r-md bg-indigo-500 px-3 py-2 text-sm font-semibold"
                        :class="{
                            'bg-indigo-500 text-white focus:z-20 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-500': link.active,
                            'inset-ring focus:outline-offset-0 inset-ring-gray-700 hover:bg-white/5': !link.active
                        }"></a>
                </nav>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ChevronLeftIcon, ChevronRightIcon } from '@heroicons/vue/20/solid'

defineProps({
    meta: Object,
})

const items = [
    { id: 1, title: 'Back End Developer', department: 'Engineering', type: 'Full-time', location: 'Remote' },
    { id: 2, title: 'Front End Developer', department: 'Engineering', type: 'Full-time', location: 'Remote' },
    { id: 3, title: 'User Interface Designer', department: 'Design', type: 'Full-time', location: 'Remote' },
]
</script>
