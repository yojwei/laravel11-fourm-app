<script setup>
import Container from '@/Components/Container.vue';
import Pagination from '@/Components/Pagination.vue';
import AppLayout from '@/Layouts/AppLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { relativeDate } from "@/Utilities/date.js";
import PageHeading from '@/Components/PageHeading.vue'
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';

defineProps({
    'posts': Object,
    'selectedTopic': Object,
    'topics': Array,
});

const formattedDate = (date) => { return relativeDate(date); };

const searchForm = useForm({
    search: '',
});

const search = () => {
    searchForm.get(route('posts.index', { search: searchForm.search }), {
        preserveState: true,
        preserveScroll: true,
    });
};
</script>

<template>
    <AppLayout title="Posts">

        <Head title="Posts" />
        <Container>
            <div class="ml-2">
                <PageHeading>{{ selectedTopic ? selectedTopic.name : 'All Posts' }}</PageHeading>
                <p class="text-sm text-gray-600">{{ selectedTopic?.description }}</p>

                <menu class="flex flex-wrap gap-2 my-4">
                    <li>
                        <Link :href="route('posts.index')" class="badge text-xl"
                            :class="[!selectedTopic ? 'badge-red' : 'badge-blue']"> All Posts </Link>
                    </li>
                    <li v-for="topic in topics" :key="topic.id">
                        <Link class="badge text-xl"
                            :class="[selectedTopic?.id == topic.id ? 'badge-red' : 'badge-blue']"
                            :href="route('posts.index', { topic: topic.slug })">{{
                                topic.name }}
                        </Link>
                    </li>
                </menu>
            </div>
            <form class="flex gap-2" @submit.prevent="search">
                <div class="w-full my-2">
                    <InputLabel>Search</InputLabel>
                    <div class="flex space-x-2">
                        <TextInput v-model="searchForm.search" placeholder="Search posts..." class="w-full" />
                        <SecondaryButton type="submit">Search</SecondaryButton>
                    </div>
                </div>
            </form>
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                <ul class="divide-y">
                    <li v-for="post in posts.data" :key="post.id" class="px-4 py-2">
                        <Link :href="post.routes.show" class="flex justify-between items-center">
                            <span class="font-bold text-lg">{{ post.title }}</span>
                            <span class="flex flex-col justify-end items-end text-right">
                                <span class="text-gray-500 text-sm">{{ formattedDate(post.created_at) }}</span>
                                <span class="font-bold text-gray-700">by {{ post.user.name }}</span>
                            </span>
                        </Link>
                        <Link class="badge badge-red" :href="route('posts.index', { topic: post.topic.slug })">{{
                            post.topic.name }}
                        </Link>
                    </li>
                </ul>
                <Pagination :meta="posts.meta" :only="['posts']" class="mt-6 flex justify-center" />
            </div>
        </Container>
    </AppLayout>
</template>
