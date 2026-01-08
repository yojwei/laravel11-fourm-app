<script setup>
import Container from '@/Components/Container.vue';
import Pagination from '@/Components/Pagination.vue';
import AppLayout from '@/Layouts/AppLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
import { relativeDate } from "@/Utilities/date.js";

defineProps({
    'posts': Object,
});

const formattedDate = (date) => { return relativeDate(date); };
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
                    <li v-for="post in posts.data" :key="post.id" class="px-4 py-2 hover:bg-gray-100">
                        <Link :href="post.routes.show" class="flex justify-between items-center">
                            <span class="font-bold text-lg">{{ post.title }}</span>
                            <span class="flex flex-col justify-end items-end text-right">
                                <span class="text-gray-500 text-sm">{{ formattedDate(post.created_at) }}</span>
                                <span class="font-bold text-gray-700">by {{ post.user.name }}</span>
                            </span>
                        </Link>
                    </li>
                </ul>
                <Pagination :meta="posts.meta" :only="['posts']" class="mt-6 flex justify-center" />
            </div>
        </Container>
    </AppLayout>
</template>
