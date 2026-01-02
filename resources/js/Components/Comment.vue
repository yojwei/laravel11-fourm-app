<script setup>
import { relativeDate } from "@/Utilities/date.js";
import { computed } from 'vue';
import { router, usePage } from '@inertiajs/vue3';

const props = defineProps(['comment']);

const page = usePage();

const deleteComment = () => router.delete(route('comments.destroy', props.comment.id), {
    preserveScroll: true
});
</script>

<template>
    <div class="sm:flex">
        <div class="mb-4 flex-shrink-0 sm:mb-0 sm:mr-4">
            <img :src="comment.user.profile_photo_url" class="h-10 w-10 rounded-full" />
        </div>
        <div>
            <p class="mt-1">{{ comment.body }}</p>
            <span class="first-letter:uppercase block pt-1 text-xs text-gray-600">By {{ comment.user.name }} {{
                relativeDate(comment.created_at) }} ago</span>
            <div class="block mt-1">
                <form @submit.prevent="deleteComment" v-if="comment.can?.delete">
                    <button class="bg-red-500 text-white px-2 py-1 rounded">Delete</button>
                </form>
            </div>
        </div>
    </div>
</template>
