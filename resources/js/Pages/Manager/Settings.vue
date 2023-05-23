<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, useForm, usePage, router} from '@inertiajs/vue3';

</script>


<template>

<AuthenticatedLayout class="relative">


    <Head title="Configuracion" />


    <h1 class=" font-bold text-xl">Configuracion</h1>
    <p class=" text-sm">En esta seccion podras modificar las etiquetas de los estados y los canales de adquisicion</p>



    <div class="w-full ">
        <h2>Lista de estados</h2>
        <p>Los estados predefinidos son <span>Nuevo</span> <span>Por Contactar</span> <span>En Seguimiento</span></p>
    </div>




</AuthenticatedLayout>

</template>
