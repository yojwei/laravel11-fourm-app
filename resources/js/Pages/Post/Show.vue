<script setup>
import { computed } from 'vue';
import AppLayout from '@/Layouts/AppLayout.vue';
import Container from '@/Components/Container.vue';
import Pagination from '@/Components/Pagination.vue';
import { relativeDate } from "@/Utilities/date.js";
import Comment from '@/Components/Comment.vue';

const props = defineProps({
    post: Object,
    comments: Object
})

const formattedDate = (date) => { return relativeDate(date); };

const lines = computed(() => {
    return props.post.body ? props.post.body.split(/\r?\n/) : [];
});

</script>

<template>
    <AppLayout :title="post.title">
        <Container>
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                <h1 class="text-3xl font-bold px-6 pt-4">{{ post.title }}</h1>
                <span class="block px-6 pb-4 text-gray-500 text-right">{{ formattedDate(post.created_at) }}
                    <span class="font-bold text-gray-700">by {{ post.user.name }}</span>
                </span>

                <hr />

                <article class="px-6 py-6">
                    <div v-for="(line, index) in lines" :key="index" class="indent-4 min-h-[1.5em] text-base/6">
                        {{ line }}
                    </div>
                </article>

                <div class="px-6 pb-6">
                    <h2 class="text-lg font-bold">Comments</h2>

                    <ul class="divide-y">
                        <li v-for="comment in comments.data" :key="comment.id" class="py-2 hover:bg-gray-100">
                            <Comment :comment="comment" />
                        </li>
                    </ul>
                    <Pagination :meta="comments.meta" :only="['comments']" class="mt-6 flex justify-center" />
                </div>
            </div>
        </Container>
    </AppLayout>
</template>
