<script setup>
import ApplicationLogo from '@/Components/ApplicationLogo.vue';
import { Link } from '@inertiajs/vue3';
</script>

<template>
    <div class="min-h-screen w-full flex flex-col justify-center items-center">
        <!-- <div>
            <Link href="/">
                <ApplicationLogo class="w-20 h-20 fill-current text-gray-500" />
            </Link>
        </div> -->

        <div class=" w-full min-h-screen bg-zinc-100 text-neutral-900 dark:bg-neutral-900 dark:text-zinc-100">
            <slot />
        </div>
    </div>
</template>
