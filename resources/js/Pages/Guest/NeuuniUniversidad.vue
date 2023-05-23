<script setup>
import { Head, useForm } from '@inertiajs/vue3';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import InputError from '@/Components/InputError.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import NeuuniLogo from '@/Components/NeuuniLogo.vue';
console.log('Hola bienvenido a Neuuni Universidad!');

const createLeadForm = useForm({
    name: '',
    email: '',
    phone: '',
    college_degree: 'Licenciatura en Administración de Negocios',
});

const submit = () => {
    createLeadForm.post(route('send.data'), {
        onFinish: () => console.log('Datos enviados correctamente.'),
    });
};


</script>

<template>

    <Head title="Neuuni Universidad" />

    <div class=" w-full min-h-screen text-neutral-900 dark:text-zinc-100 bg-zinc-100 dark:bg-neutral-800 grid grid-cols-1 md:grid-cols-2">

        <div class=" w-full flex flex-col gap-5 items-center justify-center bg-blue-800">
            <NeuuniLogo class=" fill-zinc-50 w-14"/>
            <p class=" font-bold text-zinc-50">Neuuni Universidad</p>
        </div>

        <div class=" w-full flex items-center justify-center p-10">
            <form @submit.prevent="submit" class="">
                <div>
                    <InputLabel for="name" value="Nombre y apellido *" />

                    <TextInput
                        id="name"
                        type="text"
                        class="mt-1 block w-full"
                        v-model=" createLeadForm.name"
                        placeholder="Jorge Lopez"
                        required
                    />
                </div>

                <div class="mt-4">
                    <InputLabel for="email" value="Correo Electronico" />
                    <TextInput
                        id="email"
                        type="email"
                        class="mt-1 block w-full"
                        v-model=" createLeadForm.email"
                        placeholder="jorge_lopez@gmail.com"
                    />
                </div>

                <div class="mt-4">
                    <InputLabel for="phone" value="Telefono *" />
                    <TextInput
                        id="phone"
                        type="tel"
                        class="mt-1 block w-full"
                        v-model=" createLeadForm.phone"
                        placeholder="5390674598"
                        required
                    />
                </div>



                <div class="mt-4">
                    <InputLabel for="college_degree" value="Programa de Interes"/>

                    <select class=" mt-1 w-full bg-transparent border-neutral-900 dark:border-zinc-100 placeholder:text-neutral-500 dark:placeholder:text-zinc-500 focus:border-emerald-600 focus:ring-emerald-600 dark:focus:border-emerald-400 dark:focus:ring-emerald-400 rounded-md text-sm" id="college_degree" v-model="createLeadForm.college_degree">

                        <optgroup label="Licenciaturas" class="bg-zinc-100 dark:bg-neutral-900 ">
                            <option class="bg-zinc-100 dark:bg-neutral-900 " :selected="createLeadForm.college_degree === 'Licenciatura en Administración de Negocios'" value="Licenciatura en Administración de Negocios">Licenciatura en Administración de Negocios</option>
                            <option class="bg-zinc-100 dark:bg-neutral-900 " value="Licenciatura en Derecho">Licenciatura en Derecho</option>
                            <option class="bg-zinc-100 dark:bg-neutral-900 " value="Ingeniería Industrial">Ingeniería Industrial</option>
                            <option class="bg-zinc-100 dark:bg-neutral-900 " value="Licenciatura en Mercadotecnia y Diseño Digital">Licenciatura en Mercadotecnia y Diseño Digital</option>
                        </optgroup>

                        <optgroup label="Maestrias" class="bg-zinc-100 dark:bg-neutral-900 ">
                            <option class="bg-zinc-100 dark:bg-neutral-900 " value="Maestría en administración y dirección empresarial">Maestría en administración y dirección empresarial</option>
                            <option class="bg-zinc-100 dark:bg-neutral-900 " value="Maestría en dirección de empresas industriales">Maestría en dirección de empresas industriales</option>
                            <option class="bg-zinc-100 dark:bg-neutral-900 " value="Maestría en docencia y dirección de instituciones educativas">Maestría en docencia y dirección de instituciones educativas</option>
                            <option class="bg-zinc-100 dark:bg-neutral-900 " value="Maestría en sistema penal acusatorio y adversaria">Maestría en sistema penal acusatorio y adversarial</option>
                        </optgroup>

                        <optgroup label="Doctorado" class="bg-zinc-100 dark:bg-neutral-900 ">
                            <option class="bg-zinc-100 dark:bg-neutral-900 " value="Doctorado en pedagogía e investigación educativa">Doctorado en pedagogía e investigación educativa</option>
                        </optgroup>
                    </select>
                </div>


                <div class=" w-full flex items-center justify-end mt-4">
                    <button  class="inline-flex items-center px-4 py-2 bg-blue-600 text-zinc-100 border border-transparent rounded-md font-semibold text-xs uppercase tracking-widest hover:bg-blue-800 hover:dark:bg-blue-500 transition ease-in-out duration-150" :class="{ 'opacity-25':createLeadForm.processing }" :disabled="createLeadForm.processing" >
                        PEDIR INFORMES
                    </button>
                </div>

            </form>
        </div>

    </div>

</template>
