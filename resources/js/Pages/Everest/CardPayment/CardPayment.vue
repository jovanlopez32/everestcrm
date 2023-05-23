<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, useForm, usePage, router} from '@inertiajs/vue3';
import { ref, computed, watch } from 'vue';

/* import pdfFonts from "pdfmake/build/vfs_fonts"; */
import pdfMake from "pdfmake/build/pdfmake";

import {neuuni_logo} from '@/Img64/NeuuniUniversidad';
import {santander_logo} from '@/Img64/Santander';
import {arrow} from '@/Img64/ArrowLeft';
import {oxxo_logo} from '@/Img64/Oxxo';
import {neuuni_iso} from '@/Img64/NeuuniIso';

const page = usePage();

pdfMake.fonts = {
    Roboto: {
        normal: 'https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.66/fonts/Roboto/Roboto-Regular.ttf',
        bold: 'https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.66/fonts/Roboto/Roboto-Medium.ttf',
        italics: 'https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.66/fonts/Roboto/Roboto-Italic.ttf',
        bolditalics: 'https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.66/fonts/Roboto/Roboto-MediumItalic.ttf'
   },
};

/* pdfMake.vfs = pdfFonts.pdfMake.vfs; */

const props = defineProps({
    'lead': {
        type: Object,
    },
});

const months = [
  'Enero',
  'Febrero',
  'Marzo',
  'Abril',
  'Mayo',
  'Junio',
  'Julio',
  'Agosto',
  'Septiembre',
  'Octubre',
  'Noviembre',
  'Diciembre'
];

const boot_cycle = [
  'C1' + new Date().getFullYear(),
  'C2' + new Date().getFullYear(),
  'C3' + new Date().getFullYear(),
];
//Licenciaturas e ingenierias $1150
const lice_promotion = [
  'Ninguno', //1150
  'Descuento Principal', //1050
  'Descuento Especial', //950
  'Descuento Convenio', //950
];

//Maestria $2247    -   Doctorado $2132
const maest_promotion = [
    'Ninguno',
    '10%',
    '20%',
    '30%',
];

const promotion = ref([]);

const price = ref(0);

const detect_program = props.lead.college_degree.split(" ");

if(detect_program[0] == 'Licenciatura' || detect_program[0] == "Ingeniería"){
    price.value = 1150;
    promotion.value = lice_promotion;
};

if(detect_program[0] == 'Maestría' || detect_program[0] == "Doctorado"){
    detect_program[0] == 'Maestría' ? price.value = 2247 : price.value = 2132;
    promotion.value = maest_promotion;
};

const date = new Date().toLocaleDateString();

const cardpaymentForm = useForm({
    fullname: props.lead.name,
    month: 'Enero',
    boot_cycle: 'C1' + new Date().getFullYear(),
    date: date,
    promotion: 'Ninguno',
    note_one: '',
    note_two: '',
    total: 0,
    id_lead: props.lead.id,
});

/* Variable de descuento */
const desc = ref(0);

const totalPrice = computed(() => {

    switch (cardpaymentForm.promotion) {
        case 'Ninguno':
            desc.value = 0;
            cardpaymentForm.total = price.value;
            return price.value;
            break;
        case 'Descuento Principal':
            desc.value = 100;
            cardpaymentForm.total = price.value - desc.value;
            return price.value - desc.value;
            break;
        case 'Descuento Especial':
            desc.value = 200;
            cardpaymentForm.total = price.value - desc.value;
            return price.value - desc.value;
            break;
        case 'Descuento Convenio':
            desc.value = 200;
            cardpaymentForm.total = price.value - desc.value;
            return price.value - desc.value;
            break;
        case '10%':
            desc.value = (10 * price.value)/100;
            cardpaymentForm.total = Math.round(price.value - desc.value);
            return Math.ceil(price.value - desc.value);
            break;
        case '20%':
            desc.value = (20 * price.value)/100;
            cardpaymentForm.total = Math.round(price.value - desc.value);
            return Math.ceil(price.value - desc.value);
            break;
        case '30%':
            desc.value = (30 * price.value)/100;
            cardpaymentForm.total = Math.round(price.value - desc.value);
            return Math.ceil(price.value - desc.value);
            break;
        default:
            break;
    }
});


const submitPDF = () => {

    var dd = {
        pageSize: 'LETTER',
        pageMargins: [ 40, 50, 40, 50 ],
        content: [
            {
                table: {
                    widths: ['*', '*'],
                    body: [
                        [{text: 'FICHA DE PAGO', bold: true, fontSize:20, color: '#171717', alignment: 'left'}, {image: 'neuuni_logo', fit: [110,110], rowSpan: 3, alignment: 'right'}],
                        [{text: 'Ficha No. 2890', fontSize: 10, color: '#171717', alignment: 'left'}],
                        [{text: 'Fecha de emision: ' + new Date().toLocaleDateString('es-MX'), fontSize: 10, color: '#171717', alignment: 'left'}]
                    ]
                },
                layout: 'noBorders',
            },
            {
                style: 'tableBank',
                table: {
                    widths: ['*','*'],
                    body: [
                        [{text: cardpaymentForm.fullname.toString(), bold: true, fontSize: 17, color: '#171717', alignment:'left'}, {text: '$ ' + new Intl.NumberFormat('en-IN').format(totalPrice.value), fontSize: 25, color: '#171717', alignment:'right'}],
                        [{text: props.lead.college_degree.toString(), fontSize: 10, color: '#171717', alignment:'left'}, {text: 'Total a pagar', fontSize: 10, color: '#0148ba', alignment:'right'}],
                        [{text: 'Modalidad Online', fontSize: 10, color: '#171717', alignment:'left'}, {image: 'arrow_left', fit: [25, 25], rowSpan: 2, alignment: 'right'}],
                        [{text: 'Inicio de ciclo: ' + cardpaymentForm.month.toString() + ' ' + cardpaymentForm.boot_cycle.toString(), fontSize: 10, color: '#171717', alignment:'left'}, {}],
                    ]

                },
                layout: 'noBorders',
            },
            {
                table: {
                    widths: ['*'],
                    body: [
                        [{text: 'Fecha limite de pago: ' + cardpaymentForm.date, fontSize: 10, color: '#0148ba', alignment:'right'}]
                    ]
                },
                layout: 'noBorders',
            },
            {
                style: 'tableBank',
                table: {
                    widths: ['*','*','*','*'],
                    body: [
                        [{border: [false, true, false, true], text: 'Descripcion', fontSize: 10, color: '#171717', alignment: 'left'},
                        {border: [false, true, false, true], text: 'Precio', fontSize: 10, color: '#171717', alignment: 'left'},
                        {border: [false, true, false, true], text: 'Descuento', fontSize: 10, color: '#171717', alignment: 'left'},
                        {border: [false, true, false, true], text: 'Total', fontSize: 10, color: '#171717', alignment: 'left'}],

                        [{border: [false, false, false, false], text: 'Pago de mensualidad', fontSize: 10, color: '#171717', alignment: 'left', margin: [0, 10, 0, 10]},
                        {border: [false, false, false, false], text: '$ ' + new Intl.NumberFormat('en-IN').format(price.value), fontSize: 10, color: '#171717', alignment: 'left', margin: [0, 10, 0, 10]},
                        {border: [false, false, false, false], text: '$ ' + new Intl.NumberFormat('en-IN').format(desc.value), fontSize: 10, color: '#171717', alignment: 'left', margin: [0, 10, 0, 10]},
                        {border: [false, false, false, false], text: '$ ' + new Intl.NumberFormat('en-IN').format(totalPrice.value), fontSize: 10, color: '#171717', alignment: 'left', margin: [0, 10, 0, 10]}],

                        [{border: [false, true, false, true], text: ''},
                        {border: [false, true, false, true], text: ''},
                        {border: [false, true, false, true], text: 'Total', fontSize: 10, color: '#171717', alignment: 'left'},
                        {border: [false, true, false, true], text: '$ ' + new Intl.NumberFormat('en-IN').format(totalPrice.value), fontSize: 10, color: '#0148ba', alignment: 'left'}],
                    ]
                },
            },
            {
                style: 'tableBank',
                table: {
                    widths: ['*'],
                    body: [
                        [{text: 'Para finalizar el proceso, es necesario que realice el pago correspondiente por medio de una transferencia bancaria.', fontSize: 8, color: '#0148ba', alignment: 'left'}]
                    ]
                },
                layout: 'noBorders',
            },

            {
                style: 'tableBank',
                table: {
                    heights: ['*', 10],
                    widths: ['*', '*', '*'],
                    body: [
                        [{image: 'santander_logo', fit: [100,100], colSpan: 3, alignment: 'left'}, {}, {}],
                        [{text: '\nSucursal: Santander', fontSize: 10, color: '#171717', alignment: 'left'}, {text: '\nCuenta: 65507635075', fontSize: 10, color: '#171717', alignment: 'left'}, {text: '\nCLABE: 014700655076350755', fontSize: 10, color: '#171717', alignment: 'left'}]

                    ]
                },
                layout: 'noBorders',
            },

            {
                style: 'tableBank',
                table: {
                    heights: ['*', 10],
                    widths: ['*', 200, '*'],
                    body: [
                        [{image: 'santander_logo', fit: [100,100], alignment: 'left'}, {image: 'oxxo_logo', fit: [40,40], alignment: 'left'}, {}],
                        [{text: '\nPago en Oxxo | Cuenta: Santander', fontSize: 10, color: '#171717', alignment: 'left'}, {text: '\nNúmero de tarjeta: 5579 0890 0412 6978', fontSize: 10, color: '#171717', alignment: 'left'}, {text: '\nA nombre de: Nevile Online S.C.', fontSize: 10, color: '#171717', alignment: 'left'}]

                    ]
                },
                layout: 'noBorders',
            },
            {
                style: 'tableTitleNotes',
                table: {
                    widths: ['*'],
                    body: [
                        [{text: 'Notas Importantes', fontSize: 15, color: '#171717', alignment: 'left'}]
                    ]
                },
                layout: 'noBorders',
            },
            {
                style: 'tableFooter',
                table: {
                    heights: [15, 15, 15, 15, 5],
                    widths: ['*', '*'],
                    body: [
                        [{text: 'Los costos pueden estar sujetos a cambios en cualquier momento.', fontSize: 8, color: '#171717', alignment: 'left'}, {text: 'Una vez realizado el pago, no habrá devolución.', fontSize: 8, color: '#171717', alignment: 'left'}],
                        [{text: 'Esta ficha es PERSONAL, verifica que tu NOMBRE y tu CARRERA sean los correctos', fontSize: 8, color: '#171717', alignment: 'left'}, {text: 'Para cualquier aclaración es indispensable que presente su comprobante de pago.', fontSize: 8, color: '#171717', alignment: 'left'}],
                        [{text: 'Esta ficha es INTRANSFERIBLE, no se puede prestar, ni cambiar el monto, ni el orden del pago.', fontSize: 8, color: '#171717', alignment: 'left'}, {text: cardpaymentForm.note_one.toString(), fontSize: 8, color: '#0148ba', alignment: 'left'}],
                        [{text: 'Esta ficha de pago es válida hasta el ' + cardpaymentForm.date.toString(), fontSize: 8, color: '#171717', alignment: 'left'}, {text: cardpaymentForm.note_two.toString(), fontSize: 8, color: '#0148ba', alignment: 'left'}],
                        [{text: 'Si requiere factura, favor de solicitarla el mismo día de pago.', fontSize: 8, color: '#171717', alignment: 'left'}, {}]
                    ]

                },
                layout: 'noBorders',
            },
            {
                style: 'tableBank',
                table: {
                    widths: ['*', '*', '*', '*'],
                    body: [
                        [{image: 'neuuni_iso', fit: [30, 30],fillColor: '#171717', alignment: 'center', margin: [0, 20, 0, 20]}, {text: 'NEVILE ONLINE, S.C. NOL1812269IA AV. SALVADOR NAVA MARTINEZ 2906 TANGAMANGA C.P. 78269 SAN LUIS POTOSI, SAN LUIS POTOSI', fontSize: 7, fillColor: '#171717', color: '#fafafa', alignment: 'left', margin: [0, 12, 0, 10]}, {text: 'Ficha emitida por: \n' + page.props.auth.user.name, fontSize: 8, fillColor: '#171717', color: '#fafafa', alignment: 'center', margin: [0, 25, 0, 20]}, { qr: 'https://unineuuni.edu.mx/',fillColor: '#171717', foreground: '#fafafa', background: '#171717', alignment: 'center', fit: '50', margin: [0, 10, 0, 10] }]
                    ]
                },
                layout: 'noBorders',
            }
        ],
        styles: {
            tableTitleNotes: {
                bold: true,
                margin: [0, 10, 0, 10]
            },
            tableBank: {
                margin: [0, 10, 0, 10]
            },
            tableFooter: {
                margin: [0, 10, 0, 10]
            }
        },
        images: {
            'neuuni_logo' : neuuni_logo,
            'santander_logo' : santander_logo,
            'arrow_left': arrow,
            'oxxo_logo': oxxo_logo,
            'neuuni_iso': neuuni_iso,
        }
    };

    pdfMake.createPdf(dd).open();

    cardpaymentForm.post(route('card.store'), {
        onFinish: () => {
            console.log('Ficha de pago guardada con exito!');
        }
    });
};



</script>

<template>

<Head title="Crear Fichas" />

<AuthenticatedLayout>

    <div class="w-full h-full grid grid-cols-1 lg:grid-cols-2">

        <div class="w-full h-full rounded-md bg-zinc-100 dark:bg-neutral-900 flex items-center justify-center ">

            <form @submit.prevent="submitPDF">

                <div class="flex flex-col gap-1 my-2">
                    <label class="font-medium" for="fullname">Nombre Completo</label>
                    <input id="fullname" type="text" v-model="cardpaymentForm.fullname" :placeholder="props.lead.name" class=" rounded-md border-0 bg-zinc-200 dark:bg-neutral-800 focus:ring-emerald-600 focus:border-emerald-600 text-sm border-neutral-900 dark:border-zinc-100" required autofocus>
                </div>

                <div class="flex flex-wrap lg:gap-10 my-3">
                    <div class="flex flex-col gap-1 grow">
                        <label class="font-medium" for="month_card">Mes de inicio</label>
                        <select required v-model="cardpaymentForm.month" id="month_card" class="rounded-md border-0 bg-zinc-200 dark:bg-neutral-800 focus:ring-emerald-600 focus:border-emerald-600 text-sm border-neutral-900 dark:border-zinc-100 ">
                            <option class="dark:bg-neutral-800 bg-zinc-100" :selected="cardpaymentForm == 'Enero'" v-for="month in months"  :value="month">
                                {{ month }}
                            </option>
                        </select>
                    </div>

                    <div class="flex flex-col gap-1 grow">
                        <label class="font-medium" for="cycle_card">Cilco de inicio</label>
                        <select v-model="cardpaymentForm.boot_cycle" id="cycle_card" class="rounded-md border-0 bg-zinc-200 dark:bg-neutral-800 focus:ring-emerald-600 focus:border-emerald-600 text-sm border-neutral-900 dark:border-zinc-100 ">
                            <option class="dark:bg-neutral-800 bg-zinc-100" :selected="cardpaymentForm.boot_cycle === 'C1' + new Date().getFullYear()" v-for="cycle in boot_cycle"  :value="cycle">
                                {{ cycle }}
                            </option>
                        </select>
                    </div>
                </div>

                <div class="flex flex-col gap-1 my-2 ">
                    <label class="font-medium" for="date">Fecha limite de pago</label>
                    <input required id="date" type="date" v-model="cardpaymentForm.date" class=" rounded-md border-0 bg-zinc-200 dark:bg-neutral-800 focus:ring-emerald-600 focus:border-emerald-600 text-sm border-neutral-900 dark:border-zinc-100" >
                </div>

                <div class="flex flex-col gap-1 my-2">
                    <label class="font-medium" for="promotion">Descuento</label>
                    <select v-model="cardpaymentForm.promotion" id="promotion" class=" rounded-md border-0 bg-zinc-200 dark:bg-neutral-800 focus:ring-emerald-600 focus:border-emerald-600 text-sm border-neutral-900 dark:border-zinc-100">
                        <option class="dark:bg-neutral-800 bg-zinc-100" :selected="cardpaymentForm.promotion == 'Ninguno'" v-for="prom in promotion"  :value="prom">
                            {{ prom }}
                        </option>
                    </select>
                </div>

                <div class="flex flex-wrap lg:gap-10 my-2">
                    <div class="flex flex-col gap-1">
                        <label class="font-medium" for="noteone">Nota Uno</label>
                        <input id="noteone" type="text" v-model="cardpaymentForm.note_one" class=" rounded-md border-0 bg-zinc-200 dark:bg-neutral-800 focus:ring-emerald-600 focus:border-emerald-600 text-sm border-neutral-900 dark:border-zinc-100" >
                    </div>

                    <div class="flex flex-col gap-1">
                        <label class="font-medium" for="notetwo">Nota Dos</label>
                        <input id="noteone" type="text" v-model="cardpaymentForm.note_two" class=" rounded-md border-0 bg-zinc-200 dark:bg-neutral-800 focus:ring-emerald-600 focus:border-emerald-600 text-sm" >
                    </div>
                </div>

                <button class="bg-emerald-600 py-2 rounded-md w-full font-semibold text-zinc-200 my-5 dark:text-neutral-800">Crear Ficha</button>

            </form>

            <!-- <form @submit.prevent="submitPDF" class="w-full flex flex-col justify-end bg-black items-center">





                <div class="flex flex-col gap-5  my-3">
                    <div class="flex flex-col">
                        <label for="boot_cycle_card">Nota uno</label>
                        <input placeholder="Terminos y condiciones" v-model="cardpaymentForm.note_one" id="boot_cycle_card" type="text" class="  bg-transparent rounded-md px-2 py-1 focus:ring-0 focus:border-emerald-600 my-2 w-full md:w-fit">
                    </div>

                    <div class="flex flex-col">
                        <label for="boot_cycle_card">Nota dos</label>
                        <input placeholder="Terminos y condiciones" v-model="cardpaymentForm.note_two" id="boot_cycle_card" type="text" class="  bg-transparent rounded-md px-2 py-1 focus:ring-0 focus:border-emerald-600 my-2 w-full md:w-fit">
                    </div>
                </div>

                <div class="w-full">
                    <button class="px-4 bg-emerald-600 py-2 rounded-md w-full">Crear Ficha</button>
                </div>

            </form> -->

        </div>

        <div class="w-full h-full hidden lg:flex items-center justify-center">

            <div>

                <div class=" my-4 font-extrabold">
                    {{ cardpaymentForm.boot_cycle }}
                </div>

                <div class="flex gap-10 font-semibold">
                    <p>Precio:  $ {{ new Intl.NumberFormat('en-IN').format(price) }}</p>
                    <p>Descuento: $ {{ new Intl.NumberFormat('en-IN').format(desc) }}</p>
                    <p class=" underline decoration-2 decoration-emerald-600">Total: $ {{ new Intl.NumberFormat('en-IN').format(totalPrice) }}</p>
                </div>

                <div class="my-5 flex flex-col gap-1">
                    <p class=" text-2xl font-bold">{{ cardpaymentForm.fullname }}</p>
                    <p class="font-medium">{{ props.lead.college_degree }}</p>
                    <p class="text-sm">Fecha limite de pago: <span class=" underline decoration-emerald-600">{{ cardpaymentForm.date }}</span></p>
                    <p class="text-sm">Mes de inicio: <span class=" ">{{ cardpaymentForm.month }}</span></p>
                </div>


                <div class="flex flex-col gap-2">
                    <div class="my-2">
                        <p>Nota 1: </p>
                        <p class="min-h-[26px] my-1">{{ cardpaymentForm.note_one == '' ? 'Inserte un nota opcional' : cardpaymentForm.note_one }}</p>
                    </div>
                    <div>
                        <p>Nota 2: </p>
                        <p class="min-h-[26px] my-1">{{ cardpaymentForm.note_two == '' ? 'Inserte un nota opcional' : cardpaymentForm.note_two }}</p>
                    </div>
                </div>
            </div>

        </div>

    </div>


</AuthenticatedLayout>

</template>
