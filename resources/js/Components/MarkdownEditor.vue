<script setup>
import { Markdown } from '@tiptap/markdown';
import StarterKit from '@tiptap/starter-kit';
import { EditorContent, useEditor } from '@tiptap/vue-3';
import { watch } from 'vue';
import 'remixicon/fonts/remixicon.css'
import Link from '@tiptap/extension-link';
import CodeBlock from '@tiptap/extension-code-block'


const props = defineProps({
    modelValue: '',
});

const emit = defineEmits(['update:modelValue']);

const editor = useEditor({
    extensions: [
        StarterKit,
        Markdown,
        Link,
        CodeBlock
    ],
    editorProps: {
        attributes: {
            class: 'min-h-[512px] prose prose-sm max-w-none py-1.5 px-3',
        },
    },
    onUpdate: () => emit('update:modelValue', editor.value?.getMarkdown()),
});

watch(() => props.modelValue, (newValue) => {
    if (editor.value && newValue !== editor.value?.getMarkdown()) {
        editor.value.commands.setContent(newValue || '', { contentType: 'markdown' });
    }
}, { immediate: true });


const promptUserForHref = () => {
    if (editor.value?.isActive('link')) {
        return editor.value?.chain().unsetLink().run();
    }

    const href = prompt('Enter the URL');

    if (!href) {
        return editor.value?.chain().focus().run();
    }

    return editor.value?.chain().focus().setLink({ href }).run();
};
</script>

<template>
    <div
        class="bg-white rounded-md border-0 shadow-sm ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-inset focus:ring-indigo-600">
        <menu class="flex divide-x border-b" v-if="editor">
            <li>
                <button @click="() => editor.chain().focus().toggleBold().run()" type="button"
                    class="px-3 py-2 rounded-tl-md" title="Bold" :class="[
                        editor.isActive('bold') ? 'bg-indigo-500 text-white' : 'hover:bg-gray-200'
                    ]">
                    <i class="ri-bold"></i>
                </button>
            </li>
            <li>
                <button @click="() => editor.chain().focus().toggleItalic().run()" type="button" class="px-3 py-2"
                    title="Italic" :class="[
                        editor.isActive('italic') ? 'bg-indigo-500 text-white' : 'hover:bg-gray-200'
                    ]">
                    <i class="ri-italic"></i>
                </button>
            </li>
            <li>
                <button @click="() => editor.chain().focus().toggleStrike().run()" type="button" class="px-3 py-2"
                    title="strike" :class="[
                        editor.isActive('strike') ? 'bg-indigo-500 text-white' : 'hover:bg-gray-200'
                    ]">
                    <i class="ri-strikethrough"></i>
                </button>
            </li>
            <li>
                <button @click="() => editor.chain().focus().toggleBlockquote().run()" type="button" class="px-3 py-2"
                    :class="[editor.isActive('blockquote') ? 'bg-indigo-500 text-white' : 'hover:bg-gray-200']"
                    title="Blockquote">
                    <i class="ri-double-quotes-l"></i>
                </button>
            </li>
            <li>
                <button @click="() => editor.chain().focus().toggleBulletList().run()" type="button" class="px-3 py-2"
                    :class="[editor.isActive('bulletList') ? 'bg-indigo-500 text-white' : 'hover:bg-gray-200']"
                    title="Bullet list">
                    <i class="ri-list-unordered"></i>
                </button>
            </li>
            <li>
                <button @click="() => editor.chain().focus().toggleOrderedList().run()" type="button" class="px-3 py-2"
                    :class="[editor.isActive('orderedList') ? 'bg-indigo-500 text-white' : 'hover:bg-gray-200']"
                    title="Numeric list">
                    <i class="ri-list-ordered"></i>
                </button>
            </li>
            <li>
                <button @click="() => editor.chain().focus().toggleHeading({ level: 2 }).run()" type="button"
                    class="px-3 py-2"
                    :class="[editor.isActive('heading', { level: 2 }) ? 'bg-indigo-500 text-white' : 'hover:bg-gray-200']"
                    title="Heading 1">
                    <i class="ri-h-1"></i>
                </button>
            </li>
            <li>
                <button @click="() => editor.chain().focus().toggleHeading({ level: 3 }).run()" type="button"
                    class="px-3 py-2"
                    :class="[editor.isActive('heading', { level: 3 }) ? 'bg-indigo-500 text-white' : 'hover:bg-gray-200']"
                    title="Heading 2">
                    <i class="ri-h-2"></i>
                </button>
            </li>
            <li>
                <button @click="() => editor.chain().focus().toggleHeading({ level: 4 }).run()" type="button"
                    class="px-3 py-2"
                    :class="[editor.isActive('heading', { level: 4 }) ? 'bg-indigo-500 text-white' : 'hover:bg-gray-200']"
                    title="Heading 3">
                    <i class="ri-h-3"></i>
                </button>
            </li>
            <li>
                <button @click="promptUserForHref" type="button" class="px-3 py-2"
                    :class="[editor.isActive('link') ? 'bg-indigo-500 text-white' : 'hover:bg-gray-200']"
                    title="Add link">
                    <i class="ri-link"></i>
                </button>
            </li>
            <li>
                <button @click="() => editor.chain().focus().toggleCodeBlock().run()" type="button" class="px-3 py-2"
                    :class="[editor.isActive('codeBlock') ? 'bg-indigo-500 text-white' : 'hover:bg-gray-200']"
                    title="Code Block">
                    <i class="ri-code-line"></i>
                </button>
            </li>
        </menu>
        <EditorContent :editor="editor" />
    </div>
</template>
