<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import InputError from '@/Components/InputError.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import { Head, useForm, usePage} from '@inertiajs/vue3';
import {ref, computed} from 'vue';



</script>

<template>
    <Head title="Panel principal" />

    <AuthenticatedLayout>

        <div class=" w-full h-full flex items-center justify-center ">

            <div>
                <img src="/assets/img/home.png" class=" w-36" alt="">
            </div>

            <div>
                <h1 class=" text-2xl font-bold">Hola {{ $page.props.auth.user.name }}!</h1>
                <h1 class=" text-sm font-semibold underline decoration-2 decoration-emerald-500">Es genial verte de nuevo.</h1>
            </div>

        </div>

        <!-- <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Dashboard</h2>
        </template> -->



        <!-- <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900">{{ leadsAll }}</div>
                </div>
            </div>
        </div> -->
    </AuthenticatedLayout>
</template>
