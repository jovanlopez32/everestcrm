<script setup>
import { ref, computed } from 'vue';
import ApplicationLogo from '@/Components/ApplicationLogo.vue';
import Dropdown from '@/Components/Dropdown.vue';
import DropdownLink from '@/Components/DropdownLink.vue';
import NavLink from '@/Components/NavLink.vue';
import Push from 'push.js'
import ResponsiveNavLink from '@/Components/ResponsiveNavLink.vue';
import { Link, router } from '@inertiajs/vue3';

const showingNavigationDropdown = ref(false);

/* Image for the notification */
const imgNotification = '/assets/img/bg-notification-new-lead2.png';

/* Pusher config */
//Pusher.logToConsole = true;
var pusher = new Pusher('24fa5859e2996586a30f', {
    cluster: 'mt1'
});
var channel = pusher.subscribe('getData-channel');

/* Send Notification with the PushJs library */
channel.bind('get-lead', function(data) {

    Push.create("Nuevo Lead", {
        body: data.data[0].college_degree + ': ' + data.data[0].name,
        icon: imgNotification,
        timeout: 5000,
        onClick: function () {
            window.focus();
            this.close();
        }
    });
});

const logOut = () => {
    router.post('logout');
};


const openNav = ref(false);

const classNav = computed(() =>
    openNav.value ? 'fixed min-h-screen w-60 flex flex-col justify-between translate-x-0 md:translate-x-0 bg-zinc-100 dark:bg-neutral-900 px-4 z-50 pt-5 pb-10 transition-all duration-200' : 'fixed min-h-screen w-60 flex flex-col justify-between -translate-x-full md:translate-x-0 bg-zinc-100 dark:bg-neutral-900 px-4 z-50 pt-5 pb-10 transition-all duration-200'
);


</script>

<template>

<div class=" relative flex bg-zinc-200 dark:bg-neutral-800 w-full text-neutral-900 dark:text-zinc-100">

    <div id="sidebar" :class="classNav">

        <div>

            <div class=" w-full flex items-center justify-center gap-x-3 pb-5">
                <ApplicationLogo class=" w-6 h-6 fill-neutral-900 dark:fill-zinc-100 "/>
                <!-- <h1 class=" text-2xl font-normal tracking-widest">everest</h1> -->
            </div>

            <NavLink :href="route('dashboard')" :active="route().current('dashboard')">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" ><path d="M4 13h6a1 1 0 0 0 1-1V4a1 1 0 0 0-1-1H4a1 1 0 0 0-1 1v8a1 1 0 0 0 1 1zm-1 7a1 1 0 0 0 1 1h6a1 1 0 0 0 1-1v-4a1 1 0 0 0-1-1H4a1 1 0 0 0-1 1v4zm10 0a1 1 0 0 0 1 1h6a1 1 0 0 0 1-1v-7a1 1 0 0 0-1-1h-6a1 1 0 0 0-1 1v7zm1-10h6a1 1 0 0 0 1-1V4a1 1 0 0 0-1-1h-6a1 1 0 0 0-1 1v5a1 1 0 0 0 1 1z"></path></svg>
                <span>Panel principal</span>
            </NavLink>

            <NavLink :href="route('leads.home')" :active="route().current('leads.home')">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24"><path d="M18 2H6c-1.103 0-2 .897-2 2v17a1 1 0 0 0 1 1h14a1 1 0 0 0 1-1V4c0-1.103-.897-2-2-2zm0 18H6V4h12v16z"></path><path d="M8 6h3v2H8zm5 0h3v2h-3zm-5 4h3v2H8zm5 .031h3V12h-3zM8 14h3v2H8zm5 0h3v2h-3z"></path></svg>
                <span>Nuevos Leads</span>
            </NavLink>

            <NavLink :href="route('leads.panel')" :active="route().current('leads.panel')">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24"><path d="M19 2H9c-1.103 0-2 .897-2 2v6H5c-1.103 0-2 .897-2 2v9a1 1 0 0 0 1 1h16a1 1 0 0 0 1-1V4c0-1.103-.897-2-2-2zM5 12h6v8H5v-8zm14 8h-6v-8c0-1.103-.897-2-2-2H9V4h10v16z"></path><path d="M11 6h2v2h-2zm4 0h2v2h-2zm0 4.031h2V12h-2zM15 14h2v2h-2zm-8 .001h2v2H7z"></path></svg>
                <span>Todos tus Leads</span>
            </NavLink>

            <NavLink v-show="$page.props.auth.user.privileges == 'MANAGER'" :href="route('manager.add.leads')" :active="route().current('manager.add.leads')">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" ><path d="M4.5 8.552c0 1.995 1.505 3.5 3.5 3.5s3.5-1.505 3.5-3.5-1.505-3.5-3.5-3.5-3.5 1.505-3.5 3.5zM19 8h-2v3h-3v2h3v3h2v-3h3v-2h-3zM4 19h10v-1c0-2.757-2.243-5-5-5H7c-2.757 0-5 2.243-5 5v1h2z"></path></svg>
                <span>Agregar Leads</span>
            </NavLink>

            <NavLink v-show="$page.props.auth.user.privileges == 'MANAGER'" :href="route('manager.listleads')" :active="route().current('manager.listleads')">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24"><path d="M19 2H9c-1.103 0-2 .897-2 2v6H5c-1.103 0-2 .897-2 2v9a1 1 0 0 0 1 1h16a1 1 0 0 0 1-1V4c0-1.103-.897-2-2-2zM5 12h6v8H5v-8zm14 8h-6v-8c0-1.103-.897-2-2-2H9V4h10v16z"></path><path d="M11 6h2v2h-2zm4 0h2v2h-2zm0 4.031h2V12h-2zM15 14h2v2h-2zm-8 .001h2v2H7z"></path></svg>
                <span>Listado de Leads</span>
            </NavLink>


            <NavLink v-show="$page.props.auth.user.privileges == 'MANAGER'" :href="route('users.view')" :active="route().current('users.view')">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24"><path d="M12 2C6.579 2 2 6.579 2 12s4.579 10 10 10 10-4.579 10-10S17.421 2 12 2zm0 5c1.727 0 3 1.272 3 3s-1.273 3-3 3c-1.726 0-3-1.272-3-3s1.274-3 3-3zm-5.106 9.772c.897-1.32 2.393-2.2 4.106-2.2h2c1.714 0 3.209.88 4.106 2.2C15.828 18.14 14.015 19 12 19s-3.828-.86-5.106-2.228z"></path></svg>
                <span>Usuarios</span>
            </NavLink>

            <NavLink v-show="$page.props.auth.user.privileges == 'MANAGER'" :href="route('manager.settings')" :active="route().current('manager.settings')">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24"><path d="m2.344 15.271 2 3.46a1 1 0 0 0 1.366.365l1.396-.806c.58.457 1.221.832 1.895 1.112V21a1 1 0 0 0 1 1h4a1 1 0 0 0 1-1v-1.598a8.094 8.094 0 0 0 1.895-1.112l1.396.806c.477.275 1.091.11 1.366-.365l2-3.46a1.004 1.004 0 0 0-.365-1.366l-1.372-.793a7.683 7.683 0 0 0-.002-2.224l1.372-.793c.476-.275.641-.89.365-1.366l-2-3.46a1 1 0 0 0-1.366-.365l-1.396.806A8.034 8.034 0 0 0 15 4.598V3a1 1 0 0 0-1-1h-4a1 1 0 0 0-1 1v1.598A8.094 8.094 0 0 0 7.105 5.71L5.71 4.904a.999.999 0 0 0-1.366.365l-2 3.46a1.004 1.004 0 0 0 .365 1.366l1.372.793a7.683 7.683 0 0 0 0 2.224l-1.372.793c-.476.275-.641.89-.365 1.366zM12 8c2.206 0 4 1.794 4 4s-1.794 4-4 4-4-1.794-4-4 1.794-4 4-4z"></path></svg>
                <span>Configuracion</span>
            </NavLink>

            <!-- <NavLink :href="route('logout')" method="post" as="button">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"><path d="M19.002 3h-14c-1.103 0-2 .897-2 2v4h2V5h14v14h-14v-4h-2v4c0 1.103.897 2 2 2h14c1.103 0 2-.897 2-2V5c0-1.103-.898-2-2-2z"></path><path d="m11 16 5-4-5-4v3.001H3v2h8z"></path></svg>
                <span>Cerrar sesión</span>
            </NavLink> -->
        </div>

        <div class=" self-end w-full border dark:border-zinc-100 border-neutral-900 rounded-md py-3 flex items-center justify-around ">
            <div class="w-8 h-8 rounded-full bg-neutral-900 dark:bg-zinc-200 flex items-center justify-center">
                <span class="text-zinc-100 dark:text-neutral-900 text-xs font-semibold">{{ $page.props.auth.user.name.slice(0, 1) }}</span>
            </div>
            <div>
                <h1 class="text-sm font-semibold">{{ $page.props.auth.user.name }}</h1>
            </div>
            <div class="fill-neutral-900 dark:fill-zinc-100 hover:fill-emerald-600 cursor-pointer transition-all duration-200" @click="logOut">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" ><path d="M16 13v-2H7V8l-5 4 5 4v-3z"></path><path d="M20 3h-9c-1.103 0-2 .897-2 2v4h2V5h9v14h-9v-4H9v4c0 1.103.897 2 2 2h9c1.103 0 2-.897 2-2V5c0-1.103-.897-2-2-2z"></path></svg>
            </div>
        </div>




    </div>

    <div @click="openNav = !openNav" class="fixed cursor-pointer bg-neutral-900 right-4 bottom-4 w-16 h-16 rounded-full flex items-center justify-center fill-zinc-100 z-50 opacity-100 lg:opacity-0 transition-all duration-200">
        <svg xmlns="http://www.w3.org/2000/svg" width="30" height="30" viewBox="0 0 24 24" ><path d="M4 6h16v2H4zm0 5h16v2H4zm0 5h16v2H4z"></path></svg>
    </div>


    <main class=" relative ml-0 md:ml-60 min-h-screen w-full p-5 md:py-10 md:px-5">

        <slot />
    </main>

</div>

</template>
