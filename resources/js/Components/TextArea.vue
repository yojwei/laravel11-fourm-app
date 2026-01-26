<script setup>
import { onMounted, ref, useAttrs } from 'vue';

const model = defineModel();
const input = ref(null);
const attrs = useAttrs();

onMounted(() => {
    if (input.value && input.value.hasAttribute('autofocus')) {
        input.value.focus();
    }
});

defineExpose({ focus: () => input.value && input.value.focus() });
</script>

<template>
    <textarea ref="input" v-bind="attrs"
        :class="['border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm', attrs.class]"
        v-model="model"></textarea>
</template>
