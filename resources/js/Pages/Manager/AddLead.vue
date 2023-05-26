<script setup>

import Checkbox from '@/Components/Checkbox.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, useForm, usePage, router} from '@inertiajs/vue3';
import axios from 'axios';
import { ref, computed, watch } from 'vue';


const props = defineProps({
    'users': {
        type: Object,
    },
});


const leadForm = useForm({
    name: '',
    college_degree: 'Licenciatura en Administración de Negocios',
    phone: '',
    email: '',
    from: 'Facebook Form',
    user: 'Seguir Algoritmo',
});


const submitLead = () => {
    leadForm.post(route('manager.store.leads'), {
        onFinish: () => console.log('Lead agregado exitosamente!'),
    });
};


</script>


<template>

    <Head title="Agregar leads" />
<AuthenticatedLayout>



<div class="w-full h-full flex items-center justify-center">

    <form @submit.prevent="submitLead" class=" mb-5">
        <div class=" flex flex-col gap-5 items-start justify-center gap-x-4">

            <div class="flex gap-5 flex-wrap">
                <input id="name" class="bg-transparent rounded-md p-0 px-3 py-1 text-sm placeholder:text-neutral-500 border-neutral-900 border-2 focus:ring-0 focus:border-emerald-500 dark:border-zinc-200 dark:placeholder:text-zinc-400" type="text" placeholder="Nombre " v-model="leadForm.name" >

                <input id="phone" class="bg-transparent rounded-md p-0 px-3 py-1 text-sm placeholder:text-neutral-500 border-neutral-900 border-2 focus:ring-0 focus:border-emerald-500 dark:border-zinc-200 dark:placeholder:text-zinc-400" type="tel" placeholder="Numero celular" v-model="leadForm.phone" >
            </div>

            <div>
                <input id="email" class=" w-full bg-transparent rounded-md p-0 px-3 py-1 text-sm placeholder:text-neutral-500 border-neutral-900 border-2 focus:ring-0 focus:border-emerald-500 dark:border-zinc-200 dark:placeholder:text-zinc-400" type="email" placeholder="Correo Electronico" v-model="leadForm.email" >
            </div>

            <div>
                <select v-model="leadForm.college_degree" id="" class=" dark:border-zinc-300 w-full md:w-fit py-1 text-sm bg-transparent rounded-md border-2 border-neutral-900 focus:ring-0 focus:border-emerald-500">
                    <optgroup label="Licenciaturas" class="bg-zinc-100 dark:bg-neutral-900 ">
                        <option value="Licenciatura en Administración de Negocios">Licenciatura en Administración de Negocios</option>
                        <option value="Licenciatura en Derecho">Licenciatura en Derecho</option>
                        <option value="Ingeniería Industrial">Ingeniería Industrial</option>
                        <option value="Licenciatura en Mercadotecnia y Diseño Digital">Licenciatura en Mercadotecnia y Diseño Digital</option>
                    </optgroup>
                    <optgroup label="Maestrias" class="bg-zinc-100 dark:bg-neutral-900 ">
                        <option value="Maestría en administración y dirección empresarial">Maestría en administración y dirección empresarial</option>
                        <option value="Maestría en dirección de empresas industriales">Maestría en dirección de empresas industriales</option>
                        <option value="Maestría en docencia y dirección de instituciones educativas">Maestría en docencia y dirección de instituciones educativas</option>
                        <option value="Maestría en sistema penal acusatorio y adversaria">Maestría en sistema penal acusatorio y adversarial</option>
                    </optgroup>
                    <optgroup label="Doctorado" class="bg-zinc-100 dark:bg-neutral-900 ">
                        <option value="Doctorado en pedagogía e investigación educativa">Doctorado en pedagogía e investigación educativa</option>
                    </optgroup>
                    <optgroup label="Otro" class="bg-zinc-100 dark:bg-neutral-900 ">
                        <option value="Preguntar Carrera">Preguntar Carrera</option>
                    </optgroup>
                </select>
            </div>

            <div class="flex gap-5 flex-wrap">
                <select v-model="leadForm.from" name="" id="" class="dark:border-zinc-300 py-1 text-sm bg-transparent rounded-md border-2 border-neutral-900 focus:ring-0 focus:border-emerald-500">
                    <option class="bg-zinc-100 dark:bg-neutral-900 " value="Facebook Form">Facebook Form</option>
                    <option class="bg-zinc-100 dark:bg-neutral-900 " value="Pagina Web">Pagina Web</option>
                    <option class="bg-zinc-100 dark:bg-neutral-900 " value="Pagina Web">WhatsApp</option>
                </select>

                <select v-model="leadForm.user" id="" class="dark:border-zinc-300 py-1 text-sm bg-transparent rounded-md border-2 border-neutral-900 focus:ring-0 focus:border-emerald-500">
                    <option class="bg-zinc-100 dark:bg-neutral-900 " selected value="Seguir Algoritmo">Seguir Algoritmo</option>
                    <option v-for="user in props.users" :value="user.id">{{ user.name }}</option>
                </select>
            </div>



            <button class=" px-4 py-1 rounded-md border-2 border-emerald-600 text-sm fill-zinc-100 hover:bg-emerald-600 dark:hover:bg-emerald-500 transition-all duration-200">
                Agregar Nuevo Lead
            </button>
        </div>

    </form>




</div>





</AuthenticatedLayout>

</template>
