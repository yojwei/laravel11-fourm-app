<script setup>
import { computed } from 'vue';
import AppLayout from '@/Layouts/AppLayout.vue';
import { DateTime } from "luxon"

const props = defineProps({
    post: Object,
})

const formattedDate = computed(() => {
    return DateTime.fromISO(props.post.created_at).toRelative({ locale: 'en' });
});

</script>

<template>
    <AppLayout :title="post.title">
        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <h1 class="text-xl font-bold">{{ post.title }}</h1>
                <span>{{ formattedDate }} by {{ post.user.name }}</span>
                <article>{{ post.body }}</article>
            </div>
        </div>
    </AppLayout>
</template>
