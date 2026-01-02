<script setup>
import { relativeDate } from "@/Utilities/date.js";

const props = defineProps(['comment']);
const emit = defineEmits(['delete']);
</script>

<template>
    <div class="sm:flex">
        <div class="mb-4 flex-shrink-0 sm:mb-0 sm:mr-4">
            <img :src="comment.user.profile_photo_url" class="h-10 w-10 rounded-full" />
        </div>
        <div class="flex-1">
            <p class="mt-1">{{ comment.body }}</p>
            <span class="first-letter:uppercase block pt-1 text-xs text-gray-600">By {{ comment.user.name }} {{
                relativeDate(comment.created_at) }}</span>
            <div class="mt-1 text-right empty:hidden">
                <form @submit.prevent="$emit('delete', comment.id)" v-if="comment.can?.delete">
                    <button class="bg-red-500 text-white px-2 py-1 rounded">Delete</button>
                </form>
            </div>
        </div>
    </div>
</template>
