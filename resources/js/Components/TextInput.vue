<script setup>
import { onMounted, ref } from 'vue';

defineProps({
    modelValue: {
        type: String,
        required: true,
    },
});

defineEmits(['update:modelValue']);

const input = ref(null);

onMounted(() => {
    if (input.value.hasAttribute('autofocus')) {
        input.value.focus();
    }
});

defineExpose({ focus: () => input.value.focus() });
</script>

<template>
    <input
        class=" bg-transparent border-neutral-900 dark:border-zinc-100 placeholder:text-neutral-500 dark:placeholder:text-zinc-500 focus:border-emerald-600 focus:ring-emerald-600 dark:focus:border-emerald-400 dark:focus:ring-emerald-400 rounded-md text-sm"
        :value="modelValue"
        @input="$emit('update:modelValue', $event.target.value)"
        ref="input"
    />
</template>
