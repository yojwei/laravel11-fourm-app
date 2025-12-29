<script setup>
import Container from '@/Components/Container.vue';
import Pagination from '@/Components/Pagination.vue';
import AppLayout from '@/Layouts/AppLayout.vue';
import { Head } from '@inertiajs/vue3';

defineProps({
    'posts': Object,
});
</script>

<template>
    <AppLayout title="Posts">

        <Head title="Posts" />
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tigh">
                Posts
            </h2>
        </template>
        <Container>
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                <ul class="divide-y">
                    <li v-for="post in posts.data" :key="post.id" class="px-2 py-4">
                        <span class="font-bold text-lg">{{ post.title }}</span>
                    </li>
                </ul>
                <Pagination :meta="posts.meta" class="mt-6 flex justify-center" />
            </div>
        </Container>
    </AppLayout>
</template>
