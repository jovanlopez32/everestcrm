<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, useForm, usePage, router} from '@inertiajs/vue3';
import DangerButton from '@/Components/DangerButton.vue';

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

const deleteForm = useForm({
    id: '',
    phone: '',
});

const submitLead = () => {
    deleteForm.post(route('lead.destroy'), {
        onFinish: () => {
            deleteForm.id = '';
            deleteForm.phone = '';
        }
    });
};

</script>


<template>

<AuthenticatedLayout class="relative">


    <Head title="Configuracion" />


    <h1 class=" font-bold text-xl">Configuracion</h1>
    <p class=" text-sm">En esta seccion podras modificar las etiquetas de los estados y los canales de adquisicion</p>



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
    </div>

    <div>
       <div v-for="status in props.status" class="flex">
            <button></button><p>{{ status.text }}</p>
       </div>
    </div>

    <div class="mt-4">
        <h1 class=" font-bold text-xl">Eliminar leads</h1>
        <p class=" text-sm">En esta sección podrás eliminar un lead ingresando el ID y número de teléfono.</p>
    </div>

    <div class="w-full my-5 flex justify-between items-center flex-wrap">
    
        <div>
            <form @submit.prevent="submitLead" class=" flex gap-5">
                <input v-model="deleteForm.id" class="px-2 py-1 bg-transparent rounded-md border-neutral-900 dark:border-zinc-100 focus:ring-emerald-500 focus:border-emerald-500 text-sm" type="text" placeholder="ID del lead" required>
                <input v-model="deleteForm.phone" class="px-2 py-1 bg-transparent rounded-md border-neutral-900 dark:border-zinc-100 focus:ring-emerald-500 focus:border-emerald-500 text-sm" type="text" placeholder="Número de teléfono" required>
                <DangerButton>
                Eliminar
                </DangerButton>
            </form>
        </div>
    </div>

    <!-- <button onclick="myFacebookLogin()">Login with Facebook</button> -->

    
        


</AuthenticatedLayout>

</template>
