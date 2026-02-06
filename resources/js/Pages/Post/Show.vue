<script setup>
import { computed, ref } from 'vue';
import { useForm, router, Link, Head } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import Container from '@/Components/Container.vue';
import Comment from '@/Components/Comment.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
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
// ---------------------------------------------------------------------------
// 提交留言：submitComment
// - 使用 Inertia 的 POST 請求將留言送到後端
// - 成功時清空表單欄位（避免殘留舊內容）
// - 使用 `preserveScroll: true` 避免頁面跳動，改善 UX
// ---------------------------------------------------------------------------
const submitComment = () => {
    commentForm.post(route('posts.comments.store', props.post.id), {
        onSuccess: () => {
            commentForm.reset();
        },
        preserveScroll: true,
    });
};

const { confirmation } = useConfirm();

// ---------------------------------------------------------------------------
// 刪除留言：deleteComment
// 流程：
// 1. 顯示確認對話框（使用 useConfirm），若使用者取消則中止
// 2. 計算要傳給刪除路由的 page 參數：
//    - 若當前頁面上仍有多於 1 則留言，則保留目前頁碼（不改變分頁）
//    - 否則刪除後可能造成本頁為空，回到前一頁（最小為 1）
// 3. 使用 Inertia 的 `router.delete` 呼叫刪除 API，並加上 `preserveScroll` 選項
// ---------------------------------------------------------------------------
const deleteComment = async (commentId) => {
    if (!await confirmation('確定刪除這則留言?')) {
        return;
    }

    // props.comments.data 為目前頁的留言陣列，props.comments.meta.current_page 為頁碼
    const targetPage = props.comments.data.length > 1
        ? props.comments.meta.current_page
        : Math.max(props.comments.meta.current_page - 1, 1);

    router.delete(route('comments.destroy', {
        comment: commentId,
        page: targetPage,
    }), {
        // 保持滾動位置，避免頁面跳動
        preserveScroll: true,
    });
};

// ---------------------------------------------------------------------------
// 編輯留言：editComment
// - 設定要編輯的留言 ID，將該留言內容載入表單，並將游標聚焦到輸入框
// ---------------------------------------------------------------------------
const editComment = (commentId) => {
    commentIdBeingEdited.value = commentId;
    commentForm.body = commentBedingEdit.value?.body;
    commentTextAreaRef.value?.focus();
};

// ---------------------------------------------------------------------------
// 更新留言：updateComment
// - 顯示確認對話，使用者確認後以 PUT 更新指定留言
// - 成功後呼叫 cancelEditComment 恢復狀態
// - 使用 preserveScroll 保持使用者視窗位置
// ---------------------------------------------------------------------------
const updateComment = async () => {
    if (!await confirmation('希望更新這則留言?')) {
        commentTextAreaRef.value?.focus();
        return;
    }

    commentForm.put(route('comments.update', {
        comment: commentIdBeingEdited.value,
        page: props.comments.meta.current_page,
    }), {
        onSuccess: cancelEditComment,
        preserveScroll: true,
    });
};

// ---------------------------------------------------------------------------
// 取消編輯：cancelEditComment
// - 清除編輯 ID 並重置表單，恢復新增留言模式
// ---------------------------------------------------------------------------
const cancelEditComment = () => {
    commentIdBeingEdited.value = null;
    commentForm.reset();
};
</script>

<template>

    <Head>
        <link rel="canonical" :href="post?.routes.show" />
    </Head>
    <AppLayout :title="post.title">
        <Container>
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                <!-- 貼文標題和元資訊區域 -->
                <div>

                    <!-- 貼文標題 -->
                    <PageHeading>{{ post.title }}
                        <Link class="badge badge-red text-2xl" :href="route('posts.index', { topic: post.topic.slug })">
                            {{
                                post.topic.name }}
                        </Link>
                    </PageHeading>
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
                                editorClass="!min-h-[150px]" class="w-full border rounded-md p-2 mt-1"
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
