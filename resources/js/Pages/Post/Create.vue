<script setup>
import { useForm } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import Container from '@/Components/Container.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from "@/Components/TextInput.vue";
import InputError from '@/Components/InputError.vue';
import MarkdownEditor from '@/Components/MarkdownEditor.vue';
import { isInProduction } from '@/Utilities/environment.js';
import PageHeading from '@/Components/PageHeading.vue';


// ============================================================================
// Form - 表單狀態管理
// ============================================================================
// 留言表單的反應式表單物件，使用 Inertia 提供的 useForm
const postForm = useForm({
    title: '', // 貼文標題
    body: '', // 留言內容
});


// ============================================================================
// Methods - 方法
// ============================================================================
// 提交新留言到伺服器
const submitPost = () => postForm.post(route('posts.store'));

const autofill = async () => {
    if (isInProduction()) {
        return;
    }

    await axios.get(route('local.post-content'))
        .then(response => {
            postForm.title = response.data.title;
            postForm.body = response.data.body;
        });
}

</script>

<template>
    <AppLayout title="create a post">
        <Container>
            <PageHeading>Create a Post</PageHeading>
            <form @submit.prevent="submitPost">
                <div class="mt-2">
                    <InputLabel for="title" class="sr-only" />
                    <TextInput id="title" v-model="postForm.title" type="text" class="block w-full"
                        placeholder="Post Title" />
                    <InputError :message="postForm.errors.title" class="mt-2" />
                </div>
                <div class="mt-2">
                    <InputLabel for="body" class="sr-only" />
                    <MarkdownEditor v-model="postForm.body" placeholder="寫點什麼...">
                        <template #toolbar="{ editor }">
                            <li v-if="!isInProduction()">
                                <button @click="autofill" type="button" class="px-3 py-2" title="autofull"> <i
                                        class="ri-article-line"></i>
                                </button>
                            </li>
                        </template>
                    </MarkdownEditor>
                    <InputError :message="postForm.errors.body" class="mt-2" />
                </div>
                <div>
                    <PrimaryButton class="mt-4" :disabled="postForm.processing" type="submit">
                        Create Post
                    </PrimaryButton>
                </div>
            </form>
        </Container>
    </AppLayout>
</template>
