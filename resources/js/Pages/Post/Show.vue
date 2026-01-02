<script setup>
import { computed } from 'vue';
import AppLayout from '@/Layouts/AppLayout.vue';
import Container from '@/Components/Container.vue';
import Pagination from '@/Components/Pagination.vue';
import { relativeDate } from "@/Utilities/date.js";
import Comment from '@/Components/Comment.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextArea from '@/Components/TextArea.vue';
import { useForm, router } from '@inertiajs/vue3';

const props = defineProps({
    post: Object,
    comments: Object
})

const formattedDate = (date) => { return relativeDate(date); };

const lines = computed(() => {
    return props.post.body ? props.post.body.split(/\r?\n/) : [];
});

const commentForm = useForm({
    body: ''
});

const submitComment = () => {
    commentForm.post(route('posts.comments.store', props.post.id), {
        onSuccess: () => {
            commentForm.reset();
        },
        preserveScroll: true
    });
};

const deleteComment = (commentId) => router.delete(route('comments.destroy', {
    'comment': commentId,
    'page': props.comments.meta.current_page
}), {
    preserveScroll: true
});

const editComment = (commentId) => router.get(route('comments.edit', commentId), {
    'page': props.comments.meta.current_page
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

                    <form v-if="$page.props.auth.user" @submit.prevent="submitComment">
                        <div class="mt-4">
                            <InputLabel for="body" class="sr-only">Comment</InputLabel>
                            <TextArea v-model="commentForm.body" rows="4" class="w-full border rounded-md p-2 mt-1"
                                required />
                        </div>
                        <PrimaryButton type="submit" class="mt-2 mb-4" :disabled="commentForm.processing">Post Comment
                        </PrimaryButton>
                    </form>

                    <ul class="divide-y">
                        <li v-for="comment in comments.data" :key="comment.id" class="py-2 hover:bg-gray-100">
                            <Comment :comment="comment" @delete="deleteComment" @edit="editComment" />
                        </li>
                    </ul>
                    <Pagination :meta="comments.meta" :only="['comments']" class="mt-6 flex justify-center" />
                </div>
            </div>
        </Container>
    </AppLayout>
</template>
