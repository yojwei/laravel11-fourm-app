<script setup>
import { relativeDate } from "@/Utilities/date.js";

defineProps(['comment']);
const emit = defineEmits(['delete', 'edit']);
</script>

<template>
    <div class="sm:flex">
        <div class="mb-4 flex-shrink-0 sm:mb-0 sm:mr-4">
            <img :src="comment.user.profile_photo_url" class="h-10 w-10 rounded-full" />
        </div>
        <div class="flex-1">
            <div v-html="comment.html" class="prose prose-sm max-w-none"></div>
            <span class="first-letter:uppercase block pt-1 text-xs text-gray-600">By {{ comment.user.name }} {{
                relativeDate(comment.created_at) }} | {{ comment.likes_count }} likes</span>
            <div class="mt-1 empty:hidden flex justify-end space-x-2">
                <form @submit.prevent="$emit('edit', comment.id)" v-if="comment.can?.update">
                    <button class="bg-indigo-500 text-white px-2 py-1 rounded">Edit</button>
                </form>
                <form @submit.prevent="$emit('delete', comment.id)" v-if="comment.can?.delete">
                    <button class="bg-red-500 text-white px-2 py-1 rounded">Delete</button>
                </form>
            </div>
        </div>
    </div>
</template>
