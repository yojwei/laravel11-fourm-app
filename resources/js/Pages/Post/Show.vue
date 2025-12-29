<script setup>
import { computed } from 'vue';
import AppLayout from '@/Layouts/AppLayout.vue';
import { DateTime } from "luxon"
import Container from '@/Components/Container.vue';

const props = defineProps({
    post: Object,
})

const formattedDate = computed(() => {
    return DateTime.fromISO(props.post.created_at).toRelative({ locale: 'en' });
});

const lines = computed(() => {
    return props.post.body ? props.post.body.split(/\r?\n/) : [];
});

</script>

<template>
    <AppLayout :title="post.title">
        <Container>
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                <h1 class="text-3xl font-bold px-6 pt-4">{{ post.title }}</h1>
                <span class="block px-6 pb-4 text-gray-500 text-right">{{ formattedDate }}
                    <span class="font-bold text-gray-700">by {{ post.user.name }}</span>
                </span>

                <hr />

                <article class="px-6 py-6">
                    <div v-for="(line, index) in lines" :key="index" class="indent-4 min-h-[1.5em] text-base/6">
                        {{ line }}
                    </div>
                </article>
            </div>
        </Container>
    </AppLayout>
</template>
