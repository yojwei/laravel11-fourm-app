<script setup>
import Container from '@/Components/Container.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextArea from '@/Components/TextArea.vue';
import AppLayout from '@/Layouts/AppLayout.vue';
import { Head, router, useForm } from '@inertiajs/vue3';

const props = defineProps({
    comment: Object,
    page: Number,
});

const commentForm = useForm({
    body: props.comment.body,
});

const updateComment = () => {
    commentForm.put(route('comments.update', props.comment.id), {
        onSuccess: () => {
            router.get(route('posts.show', {
                'post': props.comment.post_id,
                'page': props.page
            }));
        },
        preserveScroll: true,
    });
};
</script>

<template>
    <AppLayout>

        <Head title="Edit Comment" />
        <Container>
            <form @submit.prevent="updateComment">
                <TextArea rows="4" class="w-full border rounded-md p-2 mt-1" v-model="commentForm.body"></TextArea>
                <PrimaryButton type="submit" class="mt-2 mb-4" :disabled="commentForm.processing">Update
                    Comment
                </PrimaryButton>
            </form>
        </Container>
    </AppLayout>
</template>
