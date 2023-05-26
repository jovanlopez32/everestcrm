<script setup>

import Checkbox from '@/Components/Checkbox.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, useForm, usePage, router} from '@inertiajs/vue3';
import axios from 'axios';
import { ref, computed, watch } from 'vue';


const props = defineProps({
    'all_leads' : {
        type: Object,
    },
});

const searchForm = useForm({
    name: '',
    phone: '',
    //add
    date_from:'',
    date_to:'',
    //end add
    college_degree: 'Todas las carreras',
    status: 'Todos los estados',
    'from': 'Todos los canales',
});

const submitSearch = () => {
    searchForm.post(route('leads.search'), {
        onFinish: () => console.log('Datos buscados con exito!'),
    });

};

/* Variables del modal */
const openModal = ref(false);
const classModal = computed(() =>
    openModal.value ? 'fixed w-full h-screen top-0 right-0 z-50 flex items-center justify-center bg-gray-400 bg-clip-padding backdrop-filter backdrop-blur-sm bg-opacity-10 ' : 'fixed w-full h-screen top-0 right-0 z-50 hidden items-center justify-center'
);

const notes = ref([]);
const lead_selected = ref(Object);
const lead_time_accepted = ref('');

const followUpForm = useForm({
    id_lead_follow: '',
    name_follow: '',
    college_degree_follow: '',
    phone_follow: '',
    email_follow: '',
    status_follow: '',
    new_note: '',
});

const submitFollowUp = () => {
    followUpForm.post(route('leads.follow_up', { preserveState: true }, { preserveScroll: true }), {
        onFinish: () => {
            axios.post('/getnotes', { idlead: followUpForm.id_lead_follow }).then(response => {
                followUpForm.new_note = '';
                notes.value = response.data;
            });
        }
    });
};

function modalLead(lead){

    axios.post('/getnotes', { idlead: lead.id }).then(response => {
        console.log(response.data);
        notes.value = response.data;
        /* Dividimos el campo de aceptado para poder diferenciar entre dias, minutos y segundos */
        const part = lead.accepted_time.split(', ');
        lead_time_accepted.value = part;
        /* Asignamos el lead dinamicamente */
        lead_selected.value = lead;

        /* Asiganamos los campos del formulario */
        followUpForm.id_lead_follow = lead.id;
        followUpForm.name_follow = lead.name;
        followUpForm.college_degree_follow = lead.college_degree;
        followUpForm.phone_follow = lead.phone;
        followUpForm.email_follow = lead.email;
        followUpForm.status_follow = lead.status;

        openModal.value = true;
    });

}

function deleteNote(idnote){
    axios.post('/deletenote', { idnote: idnote }).then(response => {
        notes.value = response.data;
        router.reload( { only: ['all_leads'] }, { preserveState: true }, { preserveScroll: true });
    });
}

function enrolledLead(leadid){
    axios.post('/enrolledlead', { idlead: leadid}).then(response => {
       lead_selected.value = response.data;
       router.reload( { only: ['all_leads'] }, { preserveState: true }, { preserveScroll: true });
    });
}

const cleanFilters = () => {
    router.get('panelleads');
};

</script>

<template>

    <Head title="Panel Leads" />

    <AuthenticatedLayout class="relative">

        <div :class="classModal" >

            <div class="flex flex-wrap-reverse bg-zinc-100 dark:bg-neutral-900 gap-5 p-4 rounded-md">
                <!-- Notes -->
                <div class="w-full lg:w-fit">

                    <h1 class="font-semibold mb-2">Notas</h1>
                    <div class=" flex flex-col h-72 overflow-y-scroll scrollbar ">
                        <div v-for="(note, index) in notes" class=" w-full lg:w-64 pb-4 rounded-md text-xs px-2 my-2 bg-zinc-200 dark:bg-neutral-700">
                            <div class="w-full flex items-center justify-end my-1">
                                <button v-show="index != 0" @click="deleteNote(note.id)">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24"  class=" fill-red-400 hover:fill-red-500"><path d="m16.192 6.344-4.243 4.242-4.242-4.242-1.414 1.414L10.535 12l-4.242 4.242 1.414 1.414 4.242-4.242 4.243 4.242 1.414-1.414L13.364 12l4.242-4.242z"></path></svg>
                                </button>
                            </div>

                            <p class=" font-semibold">{{ note.text }}</p>
                        </div>
                    </div>

                </div>
                <!-- Lead Info -->
                <div class="text-sm">
                    <div class="w-full flex justify-end items-center fill-neutral-900 dark:fill-zinc-100">
                        <svg @click="openModal = !openModal" class="cursor-pointer" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" ><path d="m16.192 6.344-4.243 4.242-4.242-4.242-1.414 1.414L10.535 12l-4.242 4.242 1.414 1.414 4.242-4.242 4.243 4.242 1.414-1.414L13.364 12l4.242-4.242z"></path></svg>
                    </div>

                    <form @submit.prevent="submitFollowUp">
                        <div>
                            <div class="flex flex-col gap-y-2">

                                <h1 class="my-2 font-semibold underline decoration-2 decoration-emerald-500" v-show="lead_selected.enrolled == true">Felicidades {{ followUpForm.name_follow }} esta inscrito! </h1>

                                <input v-model="followUpForm.name_follow" type="text" class="text-xl font-bold bg-transparent p-0 border-transparent border-none focus:ring-0">
                            <!-- <h2 class="font-semibold max-w-[300px]">{{ lead_selected.college_degree }}</h2> -->
                                <select v-model="followUpForm.college_degree_follow" class=" mt-2 mb-4  text-xs border-transparent py-0 pl-0 bg-transparent focus:ring-0 focus:border-transparent cursor-pointer">
                                    <optgroup label="Licenciaturas">
                                        <option :selected="lead_selected.college_degree" value="Licenciatura en Administración de Negocios">Licenciatura en Administración de Negocios</option>
                                        <option :selected="followUpForm.college_degree_follow == lead_selected.college_degree" value="Licenciatura en Derecho">Licenciatura en Derecho</option>
                                        <option :selected="followUpForm.college_degree_follow == lead_selected.college_degree" value="Ingeniería Industrial">Ingeniería Industrial</option>
                                        <option :selected="followUpForm.college_degree_follow == lead_selected.college_degree" value="Licenciatura en Mercadotecnia y Diseño Digital">Licenciatura en Mercadotecnia y Diseño Digital</option>
                                    </optgroup>
                                    <optgroup label="Maestrias">
                                        <option :selected="followUpForm.college_degree_follow == lead_selected.college_degree" value="Maestría en administración y dirección empresarial">Maestría en administración y dirección empresarial</option>
                                        <option :selected="followUpForm.college_degree_follow == lead_selected.college_degree" value="Maestría en dirección de empresas industriales">Maestría en dirección de empresas industriales</option>
                                        <option :selected="followUpForm.college_degree_follow == lead_selected.college_degree" value="Maestría en docencia y dirección de instituciones educativas">Maestría en docencia y dirección de instituciones educativas</option>
                                        <option :selected="followUpForm.college_degree_follow == lead_selected.college_degree" value="Maestría en sistema penal acusatorio y adversaria">Maestría en sistema penal acusatorio y adversarial</option>
                                    </optgroup>
                                    <optgroup label="Doctorado">
                                        <option :selected="followUpForm.college_degree_follow == lead_selected.college_degree" value="Doctorado en pedagogía e investigación educativa">Doctorado en pedagogía e investigación educativa</option>
                                    </optgroup>
                                </select>
                            </div>
                            <div class="text-xs flex gap-5 my-2">

                                <input type="text" class=" text-xs bg-transparent p-0 w-fit border-transparent border-none focus:ring-0"  v-model="followUpForm.phone_follow">

                                <input type="text" class=" text-xs bg-transparent p-0 w-fit border-transparent border-none focus:ring-0"  v-model="followUpForm.email_follow">
                            </div>
                        </div>

                        <select v-show="lead_selected.enrolled == false" v-model="followUpForm.status_follow" class=" mt-2 mb-4  text-xs border-transparent py-0 pl-0 bg-transparent focus:ring-0 focus:border-transparent" name="" id="">
                            <option class="bg-zinc-100 dark:bg-neutral-900 " value="En Seguimiento">En Seguimiento</option>
                        <option class="bg-zinc-100 dark:bg-neutral-900" value="Por Contactar">Por Contactar</option>
                        <option class="bg-zinc-100 dark:bg-neutral-900" value="Promesa de Pago">Promesa de Pago</option>
                        <option class="bg-zinc-100 dark:bg-neutral-900" value="En Pausa">En Pausa</option>
                        <option class="bg-zinc-100 dark:bg-neutral-900" value="Descartado">Descartado</option>
                        <option class="bg-zinc-100 dark:bg-neutral-900" value="Duplicidad">Duplicidad</option>
                        <option class="bg-zinc-100 dark:bg-neutral-900" value="Legado">Legado</option>
                        <option class="bg-zinc-100 dark:bg-neutral-900 " value="Inscrito">Inscrito</option>
                        </select>



                        <textarea v-model="followUpForm.new_note" placeholder="Agregar Nota" rows="7" class="w-full bg-transparent border-neutral-900 dark:border-zinc-100 placeholder:text-neutral-500 dark:placeholder:text-zinc-500 focus:border-emerald-600 focus:ring-emerald-600 dark:focus:border-emerald-400 dark:focus:ring-emerald-400 rounded-md text-xs"></textarea>

                        <div class="w-full flex items-center justify-end gap-3 my-4">
                            <div v-show="lead_selected.has_card == true">
                               <button :disabled="lead_selected.enrolled == true" @click.prevent="enrolledLead(lead_selected.id)" class="text-xs rounded-md bg-cyan-500 px-4 py-2 text-neutral-900 font-semibold disabled:bg-gray-500">INSCRIBIR</button>
                            </div>

                            <Link class=" text-xs rounded-md bg-yellow-400 px-4 py-2 text-neutral-900 font-semibold " href="/crearFicha" method="get" :data="{ id_lead: lead_selected.id }" as="button" type="button">{{lead_selected.has_card == true ? 'MODIFICAR FICHA DE PAGO' : 'CREAR FICHA DE PAGO'}}</Link>

                            <button class=" text-xs rounded-md bg-emerald-500 px-4 py-2 text-neutral-900 font-semibold " >
                                GUARDAR
                            </button>
                        </div>
                    </form>

                    <div class="flex flex-wrap items-center justify-end text-xs gap-x-10 gap-y-4 my-2">
                        <p>Veces que pidio informes: {{ lead_selected.count }}</p>
                        <p>Tiempo de aceptacion: <span class=" underline decoration-emerald-600">{{ lead_time_accepted[0] == 0 ? '' : lead_time_accepted[0] + ' d'}} {{ lead_time_accepted[1] == 0 ? '' : lead_time_accepted[1] + ' min' }} {{ lead_time_accepted[2] == 0 ? '' : lead_time_accepted[2] + ' seg' }}</span></p>
                    </div>
                </div>

            </div>
        </div>

        <form @submit.prevent="submitSearch" class=" mb-5">
            <div class=" flex flex-wrap-reverse gap-y-2 items-center gap-x-4">
                <input id="namephone" class="bg-transparent rounded-md p-0 px-3 py-1 text-xs placeholder:text-neutral-500 border-neutral-900 border-2 focus:ring-0 focus:border-emerald-500 dark:border-zinc-200 dark:placeholder:text-zinc-400" type="text" placeholder="Buscar por nombre" v-model="searchForm.name" >

                <input id="namephone" class="bg-transparent rounded-md p-0 px-3 py-1 text-xs placeholder:text-neutral-500 border-neutral-900 border-2 focus:ring-0 focus:border-emerald-500 dark:border-zinc-200 dark:placeholder:text-zinc-400" type="text" placeholder="Buscar por telefono" v-model="searchForm.phone" >

                <button class=" px-4 py-1 rounded-md bg-neutral-900 fill-zinc-100 hover:bg-emerald-600 dark:hover:bg-emerald-500 transition-all duration-200">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24"><path d="M7 11h10v2H7zM4 7h16v2H4zm6 8h4v2h-4z"></path></svg>
                </button>
            </div>

            <div class="flex flex-wrap gap-5 my-4">
                <div>
                    <select v-model="searchForm.college_degree" id="" class=" dark:border-zinc-300 w-full md:w-fit py-1 text-xs bg-transparent rounded-md border-2 border-neutral-900 focus:ring-0 focus:border-emerald-500">
                        <option selected value="Todas las carreras" class="bg-zinc-100 dark:bg-neutral-900 ">Todas las carreras</option>
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
                    </select>
                </div>
                <div>
                    <select v-model="searchForm.status" id="" class="dark:border-zinc-300 py-1 text-xs bg-transparent rounded-md border-2 border-neutral-900 focus:ring-0 focus:border-emerald-500">
                        <option class="bg-zinc-100 dark:bg-neutral-900 " selected value="Todos los estados">Todos los estados</option>
                        <option class="bg-zinc-100 dark:bg-neutral-900 " value="Nuevo">Nuevo</option>
                        <option class="bg-zinc-100 dark:bg-neutral-900 " value="En Seguimiento">En Seguimiento</option>
                        <option class="bg-zinc-100 dark:bg-neutral-900" value="Por Contactar">Por Contactar</option>
                        <option class="bg-zinc-100 dark:bg-neutral-900" value="Promesa de Pago">Promesa de Pago</option>
                        <option class="bg-zinc-100 dark:bg-neutral-900" value="En Pausa">En Pausa</option>
                        <option class="bg-zinc-100 dark:bg-neutral-900" value="Descartado">Descartado</option>
                        <option class="bg-zinc-100 dark:bg-neutral-900" value="Duplicidad">Duplicidad</option>
                        <option class="bg-zinc-100 dark:bg-neutral-900" value="Legado">Legado</option>
                        <option class="bg-zinc-100 dark:bg-neutral-900 " value="Inscrito">Inscrito</option>
                    </select>
                </div>
                <div>
                    <select v-model="searchForm.from" name="" id="" class="dark:border-zinc-300 py-1 text-xs bg-transparent rounded-md border-2 border-neutral-900 focus:ring-0 focus:border-emerald-500">
                        <option class="bg-zinc-100 dark:bg-neutral-900 " selected value="Todos los canales">Todos los canales</option>
                        <option class="bg-zinc-100 dark:bg-neutral-900 " value="Ingresado Manualmente">Ingresado Manualmente</option>
                        <option class="bg-zinc-100 dark:bg-neutral-900 " value="Facebook Form">Facebook Form</option>
                        <option class="bg-zinc-100 dark:bg-neutral-900 " value="Pagina Web">Pagina Web</option>
                    </select>
                </div>
            </div>

            <!-- Fechas -->
            <div class=" flex flex-wrap-reverse gap-y-2 items-center gap-x-4">
                <label class="text-sm">Fecha desde:</label>
                <input id="date_from" class="bg-transparent rounded-md p-0 px-3 py-1 text-xs placeholder:text-neutral-500 border-neutral-900 border-2 focus:ring-0 focus:border-emerald-500 dark:border-zinc-200 dark:placeholder:text-zinc-400" type="date" placeholder="Buscar por nombre" v-model="searchForm.date_from" >

                <label class="text-sm">Fecha hasta:</label>
                <input id="date_to" class="bg-transparent rounded-md p-0 px-3 py-1 text-xs placeholder:text-neutral-500 border-neutral-900 border-2 focus:ring-0 focus:border-emerald-500 dark:border-zinc-200 dark:placeholder:text-zinc-400" type="date" placeholder="Buscar por telefono" v-model="searchForm.date_to" >

            </div>

        </form>

        <div class="mb-3">
            <SecondaryButton @click="cleanFilters">
                <label>Limpiar filtros </label><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"><path d="M12 16c1.671 0 3-1.331 3-3s-1.329-3-3-3-3 1.331-3 3 1.329 3 3 3z"></path><path d="M20.817 11.186a8.94 8.94 0 0 0-1.355-3.219 9.053 9.053 0 0 0-2.43-2.43 8.95 8.95 0 0 0-3.219-1.355 9.028 9.028 0 0 0-1.838-.18V2L8 5l3.975 3V6.002c.484-.002.968.044 1.435.14a6.961 6.961 0 0 1 2.502 1.053 7.005 7.005 0 0 1 1.892 1.892A6.967 6.967 0 0 1 19 13a7.032 7.032 0 0 1-.55 2.725 7.11 7.11 0 0 1-.644 1.188 7.2 7.2 0 0 1-.858 1.039 7.028 7.028 0 0 1-3.536 1.907 7.13 7.13 0 0 1-2.822 0 6.961 6.961 0 0 1-2.503-1.054 7.002 7.002 0 0 1-1.89-1.89A6.996 6.996 0 0 1 5 13H3a9.02 9.02 0 0 0 1.539 5.034 9.096 9.096 0 0 0 2.428 2.428A8.95 8.95 0 0 0 12 22a9.09 9.09 0 0 0 1.814-.183 9.014 9.014 0 0 0 3.218-1.355 8.886 8.886 0 0 0 1.331-1.099 9.228 9.228 0 0 0 1.1-1.332A8.952 8.952 0 0 0 21 13a9.09 9.09 0 0 0-.183-1.814z"></path></svg>
            </SecondaryButton>
        </div>

        <div v-if="all_leads.data.length == 0">
            <div class=" w-full h-full flex flex-wrap items-center gap-5 my-10">
                <img src="/assets/img/not-leads.png" class=" object-contain w-60" alt="No existen leads.">
                <div>
                    <h1 class="text-2xl font-extrabold">Ups!</h1>
                    <p class="font-semibold my-2">No se encuentran datos.</p>
                </div>
            </div>
        </div>
        <div @click="modalLead(lead)" v-for="(lead, index) in all_leads.data" class="py-2 grid grid-cols-2 lg:grid-cols-5 text-xs border-b border-black hover:cursor-pointer dark:border-zinc-100" :class="{'border-t': index == 0}">

            <!-- Datos de contacto -->
            <div class=" truncate flex flex-col gap-1">
                <p class="font-bold text-base">{{ lead.name }}</p>
                <p><strong>Lead ID: {{ lead.id }}</strong></p>
                <p>{{ lead.email }}</p>
                <p>{{ lead.phone }}</p>
                <p>{{ new Date(lead.created_at).toLocaleDateString("en-US", { day: "2-digit", month: "2-digit", year: "numeric",  hour: "2-digit",minute: "2-digit", }) }}</p>
            </div>
            <!-- Estado -->
            <div class="trucate flex flex-col items-center justify-center">
                <p class="text-base font-semibold px-4 py-1 rounded-md" :class="{ 'bg-yellow-500' : lead.status === 'En Seguimiento', 'bg-blue-500' : lead.status === 'Por Contactar' }">{{ lead.status }}</p>
            </div>
            <!-- Carrera de interes -->
            <div class=" hidden lg:flex items-center justify-center ">
                <p class="text-sm">{{ lead.college_degree }}</p>
            </div>
            <!-- Canal y campana -->
            <div class=" hidden lg:flex flex-col items-center justify-center ">
                <div>
                    <p class="text-sm">{{ lead.from }}</p>
                    <p>{{ lead.campaing }}</p>
                </div>
            </div>
            <div class=" hidden lg:flex flex-col items-center justify-center ">
                <p>{{ lead.notes == [] ? lead.notes.slice(-1)[0].text : 'No hay notas aun' }}</p>
            </div>
        </div>

        <div class=" justify-self-end flex items-center justify-around border border-neutral-900 dark:border-zinc-200 rounded-md my-5 p-0">
            <template v-for="(link, index) in all_leads.links">
                <div class="flex grow items-center justify-center p-0">
                    <div v-if="link.url == null" v-show="true"
                        class="w-full text-center border font-bold border-neutral-900 dark:border-zinc-200 p-1"
                        :class="{ ' text-neutral-600 cursor-not-allowed':link.url == null}"
                        v-html="link.label ">
                    </div>

                    <Link v-else
                    class="w-full text-center border font-bold border-neutral-900 dark:border-zinc-200 p-1 hover:bg-emerald-500 hover:text-zinc-100 transition-all duration-300 ease-in-out"
                        :class="{ 'bg-emerald-600   text-zinc-100': link.active }"
                        :href="link.url"
                        v-html="link.label"
                    />
                </div>
            </template>
        </div>


    </AuthenticatedLayout>

</template>
