<script setup>
import Pagination from '@/Components/Pagination.vue';
import AppLayout from '@/Layouts/AppLayout.vue';

defineProps({
    'posts': Object,
});
</script>

<template>
    <AppLayout title="Posts">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Posts
            </h2>
        </template>
        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                    <h1 class="text-2xl font-bold mb-4">Posts</h1>
                    <ul class="divide-y">
                        <li v-for="post in posts.data" :key="post.id" class="px-2 py-4">
                            <span class="font-bold text-lg">{{ post.title }}</span>
                        </li>
                    </ul>
                    <Pagination :meta="posts.meta" class="mt-6 flex justify-center" />
                </div>
            </div>
        </div>
    </AppLayout>
</template>
