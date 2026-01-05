<script setup>
import { watchEffect, ref, nextTick } from 'vue';
import ConfirmationModal from './ConfirmationModal.vue';
import PrimaryButton from './PrimaryButton.vue';
import SecondaryButton from './SecondaryButton.vue';
import { useConfirm } from '@/Utilities/Composables/useConfirm';

const { state, confirm, cancel } = useConfirm();
const cancelButtonRef = ref(null);

watchEffect(async () => {
    if (state.show) {
        // 等待下一個 DOM 更新週期，確保模態框已經顯示
        await nextTick();
        // 將焦點設置到取消按鈕上
        cancelButtonRef.value?.$el.focus();
    }
});

</script>

<template>
    <ConfirmationModal :show="state.show">
        <template #title>
            {{ state.title }}
        </template>

        <template #content>
            {{ state.message }}
        </template>

        <template #footer>
            <SecondaryButton ref="cancelButtonRef" @click="cancel">Cancel</SecondaryButton>
            <PrimaryButton class="ml-2" @click="confirm">Confirm</PrimaryButton>
        </template>
    </ConfirmationModal>
</template>
