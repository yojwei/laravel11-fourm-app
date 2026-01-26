<script setup>
import { computed, ref } from 'vue';
import { useForm, router } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import Container from '@/Components/Container.vue';
import Comment from '@/Components/Comment.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import TextArea from '@/Components/TextArea.vue';
import Pagination from '@/Components/Pagination.vue';
import { relativeDate } from '@/Utilities/date.js';
import { useConfirm } from '@/Utilities/Composables/useConfirm';
import MarkdownEditor from '@/Components/MarkdownEditor.vue';
import PageHeading from '@/Components/PageHeading.vue';

// ============================================================================
// Props - 從父組件接收的屬性
// ============================================================================
const props = defineProps({
    post: Object,      // 貼文物件：包含標題、內容、作者、創建時間等
    comments: Object,  // 留言物件：包含分頁留言資料
});

// ============================================================================
// Utilities - 實用函數
// ============================================================================
// 將日期格式化為相對時間（例如：1小時前、2天前）
const formattedDate = (date) => relativeDate(date);

// 追蹤當前正在編輯的留言ID
const commentIdBeingEdited = ref(null);

// ============================================================================
// Form - 表單狀態管理
// ============================================================================
// 留言表單的反應式表單物件，使用 Inertia 提供的 useForm
const commentForm = useForm({
    body: '', // 留言內容
});

// 根據編輯的留言ID找到對應的留言物件
const commentBedingEdit = computed(() => {
    return props.comments.data.find(c => c.id === commentIdBeingEdited.value);
});

const commentTextAreaRef = ref(null);

// ============================================================================
// Methods - 方法
// ============================================================================
// 提交新留言到伺服器
const submitComment = () => {
    commentForm.post(route('posts.comments.store', props.post.id), {
        onSuccess: () => {
            commentForm.reset(); // 成功後清空表單
        },
        preserveScroll: true, // 提交後保持頁面捲動位置
    });
};

const { confirmation } = useConfirm();

// 刪除指定的留言
const deleteComment = async (commentId) => {
    if (!await confirmation('確定刪除這則留言?')) {
        return;
    }


    router.delete(route('comments.destroy', {
        comment: commentId,
        page: props.comments.meta.current_page, // 保持在同一頁
    }), {
        preserveScroll: true,
    });
};

// 進入編輯模式：載入要編輯的留言內容到表單
const editComment = (commentId) => {
    commentIdBeingEdited.value = commentId;            // 設定編輯的留言ID
    commentForm.body = commentBedingEdit.value?.body;  // 載入該留言的內容
    commentTextAreaRef.value?.focus();               // 聚焦到留言輸入框
};

// 更新編輯的留言內容到伺服器
const updateComment = async () => {
    if (!await confirmation('希望更新這則留言?')) {
        commentTextAreaRef.value?.focus();
        return;
    }

    commentForm.put(route('comments.update', {
        comment: commentIdBeingEdited.value,
        page: props.comments.meta.current_page,
    }), {
        onSuccess: cancelEditComment, // 成功後取消編輯狀態
        preserveScroll: true,
    });
};

// 取消編輯：清空編輯狀態和表單
const cancelEditComment = () => {
    commentIdBeingEdited.value = null; // 清空編輯的留言ID
    commentForm.reset();               // 清空表單內容
};
</script>

<template>
    <AppLayout :title="post.title">
        <Container>
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                <!-- 貼文標題和元資訊區域 -->
                <div>
                    <!-- 貼文標題 -->
                    <PageHeading>{{ post.title }}</PageHeading>
                    <!-- 貼文發布時間和作者 -->
                    <span class="block px-6 pb-4 text-gray-500 text-right">
                        {{ formattedDate(post.created_at) }}
                        <span class="font-bold text-gray-700">by {{ post.user.name }}</span>
                    </span>
                </div>

                <hr />

                <!-- 貼文內容區域 -->
                <article class="px-6 py-6 prose prose-sm max-w-none" v-html="post.html">
                </article>

                <!-- 留言區域 -->
                <div class="px-6 pb-6">
                    <!-- 留言標題 -->
                    <h2 class="text-lg font-bold mb-4">Comments</h2>

                    <!-- 留言表單（僅登入用戶可見） -->
                    <form v-if="$page.props.auth.user"
                        @submit.prevent="() => commentBedingEdit ? updateComment() : submitComment()" class="mb-6">
                        <!-- 留言輸入框 -->
                        <div class="mt-4">
                            <InputLabel for="body" class="sr-only">Comment</InputLabel>
                            <MarkdownEditor ref="commentTextAreaRef" v-model="commentForm.body"
                                editorClass="min-h-[150px]" class="w-full border rounded-md p-2 mt-1"
                                placeholder="輸入您的留言..." required />
                        </div>
                        <!-- 提交和取消按鈕 -->
                        <div class="mt-2">
                            <!-- 動態按鈕文本：編輯時顯示「更新留言」，否則顯示「新增留言」 -->
                            <PrimaryButton type="submit" :disabled="commentForm.processing" class="mr-2">
                                {{ commentIdBeingEdited ? 'Update Comment' : 'Add Comment' }}
                            </PrimaryButton>
                            <!-- 編輯時顯示取消按鈕 -->
                            <SecondaryButton v-if="commentIdBeingEdited" type="button" @click="cancelEditComment">
                                Cancel
                            </SecondaryButton>
                        </div>
                    </form>

                    <!-- 留言列表 -->
                    <ul class="divide-y">
                        <!-- 遍歷所有留言 -->
                        <li v-for="comment in comments.data" :key="comment.id" class="py-2 hover:bg-gray-100">
                            <!-- 留言組件：展示單個留言，支援刪除和編輯操作 -->
                            <Comment :comment="comment" @delete="deleteComment" @edit="editComment" />
                        </li>
                    </ul>

                    <!-- 分頁控件 -->
                    <Pagination :meta="comments.meta" :only="['comments']" class="mt-6 flex justify-center" />
                </div>
            </div>
        </Container>
    </AppLayout>
</template>
