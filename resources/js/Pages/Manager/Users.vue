<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import InputError from '@/Components/InputError.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue'
import { Head, useForm, router } from '@inertiajs/vue3';
import { nextTick, computed, ref } from 'vue';


router.reload({ only: ['users'] })


const props = defineProps({
    'users': {
        type: Object,
    },
});

const openCreateU = ref(false)

const createUClass = computed(() =>
    openCreateU.value ? ' absolute w-full lg:w-1/2 min-h-full bg-zinc-100 dark:bg-neutral-900 top-0 right-0 rounded-md transition-all duration-300 ease-in-out p-4' : ' absolute w-full h-full lg:w-1/2 bg-zinc-100 dark:bg-neutral-900 top-0 -right-full overflow-y-scroll rounded-md transition-all duration-300 ease-in-out p-4'
);

const createUserForm = useForm({
    name: '',
    email: '',
    privileges: 'agent',
    enable_get_leads: '1',
});

const submit = () => {
    createUserForm.post(route('users.create'), {
        onFinish: () => openCreateU.value = false,
    });
};

/* Edit user */
const nameInput = ref(null);
const modal = ref(false);
const title = ref('');
const operation = ref(1);
const id = ref('');

const editclass = computed(() => modal.value ? 'fixed w-full h-full bg-neutral-700 top-0 right-0 left-0 z-[100] flex items-center justify-center bg-clip-padding backdrop-filter backdrop-blur-sm bg-opacity-10 z-20 transition-all duration-300 ease-in-out' : 'fixed w-full h-full bg-neutral-700 top-0 right-0 left-0 -z-20 bg-clip-padding backdrop-filter backdrop-blur-sm bg-opacity-10 -z-20  transition-all duration-300 ease-in-out flex items-center justify-center')

const form = useForm({
    name:'', email:'', privileges:'', enable_get_lead:'', password:'',
});


const openModal = (op,name,email,privileges,enable_get_lead,password,user) =>{
    modal.value = true;
    nextTick( () => nameInput.value.focus());
    operation.value = op;
    id.value = user;
    if(op == 1){
        title.value = 'Create user';
    }
    else{
        title.value = 'Editar usuario';
        form.name=name;
        form.email=email;
        form.privileges=privileges;
        form.password=password;
        form.enable_get_lead=enable_get_lead;
    }
}
const closeModal = () =>{
    modal.value = false;
    form.reset();
}

const save = () =>{
    if(operation.value == 1){
        form.post(route('employees.store'),{
            onSuccess: () => {ok('Usuario creado')}
        });
    }
    else{
        form.put(route('users.edit',id.value),{
            onSuccess: () => {ok('Usuario actualizado')}
        });
    }
}

const ok = (msj) =>{
    form.reset();
    closeModal();
    //Swal.fire({title:msj,icon:'success'});
}
/* End edit user */


</script>

<template>

<Head title="Usuarios"/>

<AuthenticatedLayout>

    <div class=" relative overflow-x-hidden w-full h-full ">
        <div class="flex gap-4 flex-wrap items-center justify-between">
            <div class=" flex flex-col gap-1">
                <h1 class=" text-xl font-bold">USUARIOS</h1>
                <p class=" text-sm">Crea y administra los valores de los usuarios</p>
            </div>

            <button @click="openCreateU = !openCreateU" class=" rounded-md text-xs  font-bold flex items-center justify-center fill-neutral-900 dark:fill-zinc-100 gap-2 py-2 px-4 dark:bg-neutral-900 bg-zinc-100">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" ><path d="M19 11h-6V5h-2v6H5v2h6v6h2v-6h6z"></path></svg>
                <span>Agregar Usuario</span>
            </button>
        </div>

        <div class=" grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 my-5 gap-5">

            <div v-for="user in props.users" class=" w-full bg-zinc-100 dark:bg-neutral-900 rounded-md p-4 ">
                <div class="flex w-full items-center justify-between">
                    <div class=" text-lg font-bold"><span>{{ user.name }}</span></div>
                    <div class=" text-xs font-semibold "><span>{{ user.key }}</span></div>
                </div>

                <div class=" text-sm my-4 flex gap-2 items-center fill-neutral-900 dark:fill-zinc-100">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24"><path d="M20 4H4c-1.103 0-2 .897-2 2v12c0 1.103.897 2 2 2h16c1.103 0 2-.897 2-2V6c0-1.103-.897-2-2-2zm0 2v.511l-8 6.223-8-6.222V6h16zM4 18V9.044l7.386 5.745a.994.994 0 0 0 1.228 0L20 9.044 20.002 18H4z"></path></svg>
                    <span>{{ user.email }}</span>
                </div>

                <div class=" text-xs font-bold my-2 flex gap-2 items-center justify-end fill-neutral-900 dark:fill-zinc-100">
                    <div class="w-2 h-2 rounded-full bg-emerald-500 dark:bg-emerald-400"></div>
                    <span>{{ user.privileges == 'MANAGER' ? 'Administrador' : 'Vendedor' }}</span>
                </div>

                <div class=" w-full flex items-center justify-end mt-4">
                    <PrimaryButton @click="openModal(2,user.name,user.email,user.privileges,user.enable_get_lead,user.password,user.id)" >
                        EDITAR USUARIO
                    </PrimaryButton>
                </div>

            </div>
        </div>


        <!-- Section Create New User -->
        <div :class="createUClass">

            <div class=" w-full fill-neutral-900 dark:fill-zinc-100 ">
                <svg @click="openCreateU = !openCreateU" class="cursor-pointer" xmlns="http://www.w3.org/2000/svg" width="30" height="30" viewBox="0 0 24 24" ><path d="m16.192 6.344-4.243 4.242-4.242-4.242-1.414 1.414L10.535 12l-4.242 4.242 1.414 1.414 4.242-4.242 4.243 4.242 1.414-1.414L13.364 12l4.242-4.242z"></path></svg>
            </div>


            <div class=" w-full flex flex-col items-center justify-center my-10">

                <form @submit.prevent="submit" class="">
                    <div>
                        <InputLabel for="name" value="Nombre y Apellido" />

                        <TextInput
                            id="name"
                            type="text"
                            class="mt-1 block w-full"
                            v-model=" createUserForm.name"
                            placeholder="Saul Goodman"
                            required
                        />

                        <InputError class="mt-2" :message="createUserForm.errors.email" />
                    </div>

                    <div class="mt-4">
                        <InputLabel for="email" value="Correo Electronico" />
                        <TextInput
                            id="email"
                            type="email"
                            class="mt-1 block w-full"
                            v-model=" createUserForm.email"
                            placeholder="saulgoodman@bettercallsaul.com"
                            required
                        />
                        <InputError class="mt-2" :message=" createUserForm.errors.password" />
                    </div>

                    <div class="mt-4">
                        <InputLabel for="privileges" value="Privilegios"/>

                        <select class=" mt-1 w-full bg-transparent border-neutral-900 dark:border-zinc-100 placeholder:text-neutral-500 dark:placeholder:text-zinc-500 focus:border-emerald-600 focus:ring-emerald-600 dark:focus:border-emerald-400 dark:focus:ring-emerald-400 rounded-md text-sm" id="privileges" v-model="createUserForm.privileges">

                            <option class="bg-zinc-100 dark:bg-neutral-900 " :selected="createUserForm.privileges === 'agent'" value="agent">Vendedor</option>
                            <option class="bg-zinc-100 dark:bg-neutral-900 " value="manager">Administrador</option>
                        </select>
                    </div>

                    <div class="mt-4">
                        <InputLabel for="get_lead" value="Habilitado para recibir leads"/>

                        <select class=" mt-1 w-full bg-transparent border-neutral-900 dark:border-zinc-100 placeholder:text-neutral-500 dark:placeholder:text-zinc-500 focus:border-emerald-600 focus:ring-emerald-600 dark:focus:border-emerald-400 dark:focus:ring-emerald-400 rounded-md text-sm" id="get_lead" v-model="createUserForm.enable_get_leads">

                            <option class="bg-zinc-100 dark:bg-neutral-900 " :selected="createUserForm.privileges === '1'" value="1">Si</option>
                            <option class="bg-zinc-100 dark:bg-neutral-900 " value="0">No</option>
                        </select>
                    </div>

                    <div class=" w-full flex items-center justify-end mt-4">
                        <PrimaryButton :class="{ 'opacity-25':createUserForm.processing }" :disabled="createUserForm.processing" >
                            CREAR USUARIO
                        </PrimaryButton>
                    </div>

                    <div class="text-xs my-10">
                        <p>La contraseña de un nuevo usuario sera: <span class=" font-semibold dark:text-emerald-500 text-emerald-900 underline decoration-2 decoration-emerald-900 dark:decoration-emerald-500"> Neuuni123</span> la cual se puede cambiar desde el usuario creado o algun administrador.</p>
                    </div>

                </form>
            </div>

        </div>


        <!-- Modal for edit user -->
        <div :class="editclass" :show="modal" @close="closeModal">
            <div class=" bg-zinc-100 dark:bg-neutral-900 px-8 py-4 rounded-md">
                <div class=" flex flex-wrap gap-5 items-center justify-between">

                    <div class=" w-full fill-neutral-900 dark:fill-zinc-100 flex justify-end">
                        <svg @click="closeModal" class="cursor-pointer" xmlns="http://www.w3.org/2000/svg" width="30" height="30" viewBox="0 0 24 24" ><path d="m16.192 6.344-4.243 4.242-4.242-4.242-1.414 1.414L10.535 12l-4.242 4.242 1.414 1.414 4.242-4.242 4.243 4.242 1.414-1.414L13.364 12l4.242-4.242z"></path></svg>
                    </div>

                    <div class=" min-w-[250px]">
                        <div>
                            <img src="/assets/img/edituser.png" class=" object-contain w-52 mx-20 my-2" alt=""/>
                        </div>
                    </div>
                    <div>
                        <h2 class="p-3 text-lg font.medium text-hray-900"><strong>{{ title }}</strong></h2>

                        <div class="flex flex-row flex-wrap">
                            <div class="p-3 mt-1">
                                <InputLabel for="name" value="Nombre y apellido:"></InputLabel>

                                <TextInput id="name" ref="nameInput"
                                v-model="form.name" type="text" class="mt-1 block w-full"
                                placeholder="Nombre"></TextInput>

                                <InputError :message="form.errors.name" class="mt-2"></InputError>
                            </div>

                            <div class="p-3 mt-1">
                                <InputLabel for="privileges" value="Privilegios:"></InputLabel>

                                <select class=" mt-1 w-full bg-transparent border-neutral-900 dark:border-zinc-100 placeholder:text-neutral-500 dark:placeholder:text-zinc-500 focus:border-emerald-600 focus:ring-emerald-600 dark:focus:border-emerald-400 dark:focus:ring-emerald-400 rounded-md text-sm" id="privileges" v-model="form.privileges" >
                                    <option class="bg-zinc-100 dark:bg-neutral-900 " value="AGENT">Vendedor</option>
                                    <option class="bg-zinc-100 dark:bg-neutral-900 " value="MANAGER">Administrador</option>
                                </select>
                            </div>
                        </div>

                        <div class="flex flex-row flex-wrap">
                            <div class="p-3 mt-1">
                                <InputLabel for="email" value="Correo electrónico:"></InputLabel>

                                <TextInput id="email"
                                v-model="form.email" type="text" class="mt-1 block w-full"
                                placeholder="Correo electrónico"></TextInput>

                                <InputError :message="form.errors.email" class="mt-2"></InputError>
                            </div>

                            <div class="p-3 mt-1">
                                <InputLabel for="password" value="Nueva contraseña:"></InputLabel>

                                <TextInput id="password"
                                v-model="form.password" type="password" class="mt-1 block w-full"
                                placeholder="Contraseña"></TextInput>

                                <InputError :message="form.errors.password" class="mt-2"></InputError>
                            </div>
                        </div>


                        <div class="p-3">
                            <InputLabel for="enable_get_lead" value="Habilitado para recibir leads:"></InputLabel>

                            <select class=" mt-1 w-full bg-transparent border-neutral-900 dark:border-zinc-100 placeholder:text-neutral-500 dark:placeholder:text-zinc-500 focus:border-emerald-600 focus:ring-emerald-600 dark:focus:border-emerald-400 dark:focus:ring-emerald-400 rounded-md text-sm" id="enable_get_leads" v-model="form.enable_get_lead" >
                                <option class="bg-zinc-100 dark:bg-neutral-900 " value="1">Sí</option>
                                <option class="bg-zinc-100 dark:bg-neutral-900 " value="0">No</option>
                            </select>
                        </div>

                        <div class="flex flex-row flex-wrap justify-end">

                            <div class="p-3">
                                <PrimaryButton :disabled="form.processing" @click="save">
                                    <i class="fa-solid fa-save"></i> Actualizar
                                </PrimaryButton>
                            </div>
                            <div class="p-3">
                                <SecondaryButton class="ml-3" :disabled="form.processing"
                                @click="closeModal">
                                    Cancelar
                                </SecondaryButton>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>

    </div>


</AuthenticatedLayout>


</template>
