/**
 * Vue 3 composable 用於管理確認對話框
 * 提供全域狀態和函數來顯示/隱藏確認模態框
 * 並通過 Promise 處理用戶的確認/取消操作
 */
import { ref, readonly } from "vue";

// 全域狀態用於確認對話框
// 此狀態在所有使用此 composable 的組件間共享
// 全域狀態用於確認對話框
// 此狀態在所有使用此 composable 的組件間共享
const globalState = ref({
    show: false,      // 控制對話框可見性
    title: "",        // 對話框標題文字
    message: "",      // 對話框訊息文字
    resolver: null,   // Promise 解析函數
})

/**
 * 顯示帶有指定訊息和標題的確認對話框
 * @param {string} message - 要在對話框中顯示的訊息
 * @param {string} title - 對話框的標題（預設為"請確認"）
 * @returns {Promise<boolean>} - 如果確認則解析為 true，如果取消則為 false
 */
function confirmation(message, title = "請確認") {
    globalState.value.show = true;
    globalState.value.message = message;
    globalState.value.title = title;

    return new Promise((resolve) => {
        globalState.value.resolver = resolve;
    });
}

/**
 * 處理確認操作 - 以 true 解析 Promise 並重置模態框
 */
function confirm() {
    if (globalState.value.resolver) {
        globalState.value.resolver(true);
    }
    resetModal();
}

/**
 * 處理取消操作 - 以 false 解析 Promise 並重置模態框
 */
function cancel() {
    if (globalState.value.resolver) {
        globalState.value.resolver(false);
    }
    resetModal();
}

/**
 * 將模態框狀態重置為初始值
 */
function resetModal() {
    globalState.value.show = false;
    globalState.value.message = "";
    globalState.value.title = "";
    globalState.value.resolver = null;
}

/**
 * Vue composable，提供確認對話框功能
 * @returns {Object} - 包含狀態和控制函數的物件
 * @property {Object} state - 對話框的唯讀響應式狀態
 * @property {Function} confirmation - 用於顯示確認對話框的函數
 * @property {Function} confirm - 用於處理確認操作的函數
 * @property {Function} cancel - 用於處理取消操作的函數
 */
export function useConfirm() {
    resetModal();
    return {
        state: readonly(globalState.value),
        confirmation,
        confirm,
        cancel,
    };
}
