<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import InputError from '@/Components/InputError.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import { Head, useForm, usePage} from '@inertiajs/vue3';
import { ref, computed, reactive, watch} from 'vue';
import ClipboardJS from 'clipboard'

const props = defineProps({
    'my_new_leads' : {
        type: Object,
    },
});
new ClipboardJS('.cpy-btn');
/* With this we get the props of auth */
const page = usePage();

const leads = ref(props.my_new_leads);
const newleads = computed(() => leads.value);

Pusher.logToConsole = false;
var pusher = new Pusher('24fa5859e2996586a30f', {
    cluster: 'mt1'
});
var channel = pusher.subscribe('getData-channel');
channel.bind('get-lead', function(data) {
    /* leads.value = data.data; */

    leads.value.length = 0;

    data.data.forEach(element => {
        if(element.user_id == page.props.auth.user.id){
            leads.value.push(element);
        }
    });
});

watch(() => props.my_new_leads, () => {
    leads.value = props.my_new_leads;
});

/* Modal create lead */
const openCreateL = ref(false);
/* Create the lead class for the modal */
const createLClass = computed(() =>
    openCreateL.value ? ' absolute w-full lg:w-1/2 min-h-full bg-zinc-100 dark:bg-neutral-900 top-0 right-0 rounded-md transition-all duration-300 ease-in-out p-4' : ' absolute w-full h-full lg:w-1/2 bg-zinc-100 dark:bg-neutral-900 top-0 -right-full overflow-y-scroll rounded-md transition-all duration-300 ease-in-out p-4'
);

/* Modal notes */
const openNewNt = ref(false);

const newNtClass = computed(() => openNewNt.value ? 'fixed w-full h-full bg-neutral-700 top-0 right-0 left-0 z-[100] flex items-center justify-center bg-clip-padding backdrop-filter backdrop-blur-sm bg-opacity-10 z-20 transition-all duration-300 ease-in-out' : 'fixed w-full h-full bg-neutral-700 top-0 right-0 left-0 -z-20 bg-clip-padding backdrop-filter backdrop-blur-sm bg-opacity-10 -z-20  transition-all duration-300 ease-in-out flex items-center justify-center')

const nameNote = ref('');
const phoneNote = ref('');
const emailNote = ref('');
const college_degreeNote = ref('');
let idleadNote = ref(0);

function createNote(name, college_degree, idlead, phone, email){
    nameNote.value = name;
    college_degreeNote.value = college_degree;
    idleadNote.value = idlead;
    phoneNote.value = phone;
    emailNote.value = email;
    openNewNt.value = true;
}

/* Form new lead */
const createLeadForm = useForm({
    name: '',
    email: '',
    phone: '',
    college_degree: 'Licenciatura en Administración de Negocios',
    userid: page.props.auth.user.id,
});
const submit = () => {
    createLeadForm.post(route('leads.create'), {
        onFinish: () => openCreateL.value = false,
    });
};

/* Form notes */
const createNoteForm = useForm({
    notetext: '',
    status: 'En Seguimiento',
    idlead: idleadNote.value,
});

watch(() => idleadNote.value, () => {
   createNoteForm.idlead = idleadNote.value;
});

const submitNote = () => {
    createNoteForm.post(route('notes.create'), {
        onFinish: () => openNewNt.value = false,
    });
};


</script>

<template>

    <Head title="Nuevos Leads" />

    <AuthenticatedLayout>

        <div class="relative overflow-x-hidden w-full h-full ">
            <div class=" w-full flex items-center justify-between flex-wrap gap-5">
                <div>
                    <small>{{ $page['props']['messageLeadRepeat'] }}</small>
                    <h1 class=" text-2xl font-semibold">Nuevos Leads ({{ newleads.length }})</h1>
                    <p class=" my-1">En esta seccion apareceran los leads que han requerido informes en los últimos días.</p>
                </div>

                <button @click="openCreateL = !openCreateL" class=" rounded-md text-xs  font-bold flex items-center justify-center fill-neutral-900 dark:fill-zinc-100 gap-2 py-2 px-4 dark:bg-neutral-900 bg-zinc-100">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" ><path d="M19 11h-6V5h-2v6H5v2h6v6h2v-6h6z"></path></svg>
                    <span>Agregar nuevo lead</span>
                </button>
            </div>

            <div class="flex flex-row-reverse justify-end flex-wrap-reverse gap-5 my-5">

                <div v-if="newleads.length == 0">
                    <div class=" flex flex-wrap gap-4 items-center justify-center my-5">
                        <img src="/assets/img/not-new-lead.png" class="w-48 object-contain" alt="No hay nuevos leads.">
                        <h1 class=" text-lg font-semibold mx-4"><span class=" font-bold">Bien hecho!</span> <br> <span class=" underline decoration-emerald-500 decoration-2">No hay nuevos leads</span> por el momento.</h1>
                    </div>
                </div>

                <div v-for="lead in newleads" class="py-3 px-6 bg-zinc-100 dark:bg-neutral-900 rounded-md flex flex-col gap-5 overflow-hidden w-full md:w-fit">

                    <div>
                        <div class=" flex gap-10 items-center justify-between">
                            <span class=" font-semibold">{{ lead.name }}</span>
                            <span class=" text-xs underline">{{ new Date(lead.created_at).toLocaleDateString("en-US", { day: "2-digit", month: "2-digit", year: "numeric",  hour: "2-digit",minute: "2-digit", }) }}</span>
                        </div>

                        <div class="my-4">
                            <span class=" font-extrabold text-xl">{{ lead.college_degree }}</span>
                        </div>

                        <div class="my-2 flex items-center flex-wrap justify-between gap-4">
                            <div class=" text-sm">{{ lead.email }}</div>
                            <div class=" text-xs font-bold py-1 text-neutral-800 dark:text-zinc-200 px-2 rounded-md border-2 dark:border-emerald-500 border-emerald-600 tracking-wider">{{ lead.phone }}</div>
                        </div>

                        <div class=" my-4 flex gap-4 flex-wrap items-center font-semibold">
                            <span class=" text-xs">{{ lead.from }}</span>
                            <span class=" text-xs">{{ lead.campaing }}</span>
                            <span class=" text-xs text-emerald-700 font-bold tracking-wider">{{ lead.count > 1 ? 'PIDIO INFORMES NUEVAMENTE' : lead.status.toUpperCase() }}</span>
                        </div>

                        <div class=" w-full flex ">
                            <button @click="createNote(lead.name, lead.college_degree, lead.id, lead.phone, lead.email)" class="w-full bg-emerald-500 px-4 py-2 rounded-md text-sm font-bold text-zinc-50 dark:text-neutral-800 my-2"> Aceptar </button>
                        </div>
                    </div>

                </div>

            </div>

            <!-- New lead modal -->
            <div :class="createLClass">

                <div class="relative w-full fill-neutral-900 dark:fill-zinc-100 ">
                    <svg @click="openCreateL = !openCreateL" class="cursor-pointer" xmlns="http://www.w3.org/2000/svg" width="30" height="30" viewBox="0 0 24 24" ><path d="m16.192 6.344-4.243 4.242-4.242-4.242-1.414 1.414L10.535 12l-4.242 4.242 1.414 1.414 4.242-4.242 4.243 4.242 1.414-1.414L13.364 12l4.242-4.242z"></path></svg>
                    <small class=" text-emerald-900 absolute top-0 right-0 font-bold text-xs underline decoration-2"></small>
                </div>

                <div class=" w-full flex flex-col items-center justify-center my-10">

                    <div class=" w-full flex items-center justify-center">
                        <img src="/assets/img/new-lead.png" class=" w-40 mb-5 object-contain " alt="Nuevo lead.">
                    </div>



                    <form @submit.prevent="submit" class="">
                        <div>
                            <InputLabel for="name" value="Nombre y apellido *" />

                            <TextInput
                                id="name"
                                type="text"
                                class="mt-1 block w-full"
                                v-model=" createLeadForm.name"
                                placeholder="Walter H. White"
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
                                placeholder="wwhartwell@breakingbad.com"
                            />
                        </div>

                        <div class="mt-4">
                            <InputLabel for="phone" value="Telefono *" />
                            <TextInput
                                id="phone"
                                type="tel"
                                class="mt-1 block w-full"
                                v-model=" createLeadForm.phone"
                                placeholder="23128395766"
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
                            <PrimaryButton :class="{ 'opacity-25':createLeadForm.processing }" :disabled="createLeadForm.processing" >
                                CREAR LEAD
                            </PrimaryButton>
                        </div>

                    </form>
                </div>

            </div>

            <!-- Modal Notas -->
            <div :class="newNtClass">

                <div class=" bg-zinc-100 dark:bg-neutral-900 px-8 py-4 rounded-md">

                    <div class=" w-full fill-neutral-900 dark:fill-zinc-100 flex justify-end ">
                        <svg @click="openNewNt = !openNewNt" class="cursor-pointer" xmlns="http://www.w3.org/2000/svg" width="30" height="30" viewBox="0 0 24 24" ><path d="m16.192 6.344-4.243 4.242-4.242-4.242-1.414 1.414L10.535 12l-4.242 4.242 1.414 1.414 4.242-4.242 4.243 4.242 1.414-1.414L13.364 12l4.242-4.242z"></path></svg>
                    </div>

                    <div class=" flex flex-wrap gap-5 items-center justify-between">
                        <div class=" min-w-[250px]">
                            <div>
                                <img src="/assets/img/notes1.png" class=" object-contain w-32 my-2" alt="">
                            </div>

                            <div>
                                <h1 class=" text-lg font-bold">{{ nameNote }}</h1>
                                <h2 class="text-sm">{{ college_degreeNote }}</h2>

                                <div class="flex items-center justify-start flex-wrap text-xs gap-5 my-2">
                                    <input id="phone_lead" type="hidden" :value="phoneNote">
                                    <button class=" cpy-btn hover:text-emerald-600 hover:underline" data-clipboard-target="#phone_lead"> {{ phoneNote }}</button>

                                    <input id="email_lead" type="hidden" :value="emailNote">
                                    <button class=" cpy-btn hover:text-emerald-600 hover:underline" data-clipboard-target="#email_lead">{{ emailNote }}</button>

                                </div>
                                <small class=" text-[7px] font-bold">Selecciona el numero o el correo para copiarlo en tu portapapeles</small>
                            </div>
                        </div>

                        <div >
                            <h1 class=" text-2xl font-bold">Notas</h1>
                            <p class=" text-xs">Crea una nota para dar tu primer siguimiento.</p>

                            <form @submit.prevent="submitNote" class=" mt-4">

                                <div>
                                    <div class="flex gap-5 my-4">
                                        <div class="flex gap-2 items-center justify-center">
                                            <input class="rounded-full text-emerald-500" id="a" type="radio" v-model="createNoteForm.status" value = "En Seguimiento" />
                                            <label class="cursor-pointer text-sm" for="a">En Seguimiento</label>
                                        </div>
                                        <div class="flex gap-2 items-center justify-center">
                                            <input class="rounded-full text-emerald-500" id="b" type="radio" v-model="createNoteForm.status" value = "Por Contactar" />
                                            <label class="cursor-pointer text-sm" for="b">Por Contactar</label>
                                        </div>
                                    </div>
                                </div>

                                <div>

                                    <textarea v-model="createNoteForm.notetext" required class="bg-transparent border-neutral-900 dark:border-zinc-100 placeholder:text-neutral-500 dark:placeholder:text-zinc-500 focus:border-emerald-600 focus:ring-emerald-600 dark:focus:border-emerald-400 dark:focus:ring-emerald-400 rounded-md text-sm" id="notetext" cols="30" rows="10">

                                    </textarea>

                                </div>


                                <div class=" w-full flex items-center justify-end mt-4">
                                    <PrimaryButton :class="{ 'opacity-25':createNoteForm.processing }" :disabled="createNoteForm.processing" >
                                        AGREGAR
                                    </PrimaryButton>
                                </div>

                            </form>


                        </div>
                    </div>

                </div>

            </div>

        </div>



    </AuthenticatedLayout>


</template>
