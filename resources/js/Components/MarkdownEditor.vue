<script setup>
import { Markdown } from '@tiptap/markdown';
import StarterKit from '@tiptap/starter-kit';
import { EditorContent, useEditor } from '@tiptap/vue-3';
import { defineProps, defineEmits, watch } from 'vue';


const props = defineProps({
    modelValue: '',
});

const emit = defineEmits(['update:modelValue']);

const editor = useEditor({
    extensions: [
        StarterKit,
        Markdown
    ],
    editorProps: {
        attributes: {
            class: 'min-h-[512px] prose prose-sm max-w-none py-1.5 px-3',
        },
    },
    onUpdate: () => emit('update:modelValue', editor.value?.getHTML()),
});

watch(() => props.modelValue, (newValue) => {
    if (editor.value && newValue !== editor.value?.getHTML()) {
        editor.value.commands.setContent(newValue || '');
    }
}, { immediate: true });
</script>

<template>
    <div
        class="bg-white rounded-md border-0 shadow-sm ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-inset focus:ring-indigo-600">
        <EditorContent :editor="editor" />
    </div>
</template>
