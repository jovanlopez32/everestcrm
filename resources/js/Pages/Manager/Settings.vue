<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, useForm, usePage, router} from '@inertiajs/vue3';

const props = defineProps({
    'status': {
        type: Array,
    },
});

const statusForm = useForm({
    text: '',
    color: '',
});

const submitStatus = () => {
    statusForm.post(route('manager.store.status'), {
        onFinish: () => {
            statusForm.text = '';
        }
    });
};
</script>


<template>

<AuthenticatedLayout class="relative">


    <Head title="Configuracion" />


    <h1 class=" font-bold text-xl">Configuracion</h1>
    <p class=" text-sm">En esta seccion podras modificar las etiquetas de los estados y los canales de adquisicion</p>



<<<<<<< HEAD
    <div class="w-full ">
        <h2>Lista de estados</h2>
        <p>Los estados predefinidos son <span>Nuevo</span> <span>Por Contactar</span> <span>En Seguimiento</span></p>

        <div>
            <p>Esto es otra seccion de prueba</p>
        </div>
    </div>
=======
    <div class="w-full my-5 flex justify-between items-center flex-wrap">

        <div>
            <h2 class="text-xl font-bold">Lista de estados</h2>
            <p class=" text-sm">Los estados predefinidos son:</p>
            <div class="flex flex-wrap gap-3 text-xs font-semibold dark:text-neutral-800 my-3">
                <div class=" px-4 rounded-md bg-emerald-500 py-1">Nuevo</div>
                <div class=" px-4 rounded-md bg-emerald-500 py-1">Por Contactar</div>
                <div class=" px-4 rounded-md bg-emerald-500 py-1">En Seguimiento</div>
            </div>
        </div>

        <div>
            <form @submit.prevent="submitStatus"  class=" flex gap-5">
                <input v-model="statusForm.text" class="px-2 py-1 bg-transparent rounded-md border-neutral-900 dark:border-zinc-100 focus:ring-emerald-500 focus:border-emerald-500 text-sm" type="text">
                <button class="rounded-full bg-emerald-500 w-7 h-7 flex items-center justify-center">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" ><path d="M19 11h-6V5h-2v6H5v2h6v6h2v-6h6z"></path></svg>
                </button>
            </form>
        </div>
>>>>>>> main


    </div>

    <div>
       <div v-for="status in props.status" class="flex">
            <button></button><p>{{ status.text }}</p>
       </div>
    </div>


</AuthenticatedLayout>

</template>
