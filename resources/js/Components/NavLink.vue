<script setup>
import { computed } from 'vue';
import { Link } from '@inertiajs/vue3';

const props = defineProps({
    href: {
        type: String,
        required: true,
    },
    active: {
        type: Boolean,
    },
});

const classes = computed(() =>
    props.active
        ? 'w-full bg-zinc-200 dark:bg-neutral-800 flex items-center justify-start rounded-md px-4 py-2 text-sm fill-emerald-700 dark:fill-emerald-300 text-emerald-700 dark:text-emerald-300 font-medium gap-2 mb-5 tracking-wider transition duration-150 ease-in-out'
        : 'w-full bg-zinc-200 dark:bg-neutral-800 flex items-center justify-start rounded-md px-4 py-2 text-sm fill-neutral-800 dark:fill-zinc-100 text-neutral-800 dark:text-zinc-200 font-medium gap-2 mb-5 tracking-wider hover:text-emerald-700 hover:fill-emerald-700 dark:hover:text-emerald-300 dark:hover:fill-emerald-300 transition duration-150 ease-in-out'
);
</script>

<template>
    <Link :href="href" :class="classes">
        <slot />
    </Link>
</template>
