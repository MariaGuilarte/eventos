<script setup>
import { computed, onMounted, ref, watch } from 'vue'
import AppLayout from './../../Layouts/AppLayout.vue'
import { Link, useForm } from '@inertiajs/vue3'

import FullCalendar from '@fullcalendar/vue3'
import esLocale from '@fullcalendar/core/locales/es';
import timeGridPlugin from '@fullcalendar/timegrid'
import dayGridPlugin from '@fullcalendar/daygrid'
import listPlugin from '@fullcalendar/list'
import interactionPlugin from '@fullcalendar/interaction'

import DialogModal from './../../Components/DialogModal.vue'
import TextInput from '@/Components/TextInput.vue'
import PrimaryButton from '@/Components/PrimaryButton.vue'
import SecondaryButton from '@/Components/SecondaryButton.vue'
import InputLabel from '@/Components/InputLabel.vue'
import InputError from '@/Components/InputError.vue'
import DangerButton from '@/Components/DangerButton.vue';

const deleting = ref(false)
const props    = defineProps(['events'])
const dialog   = ref(false)
const form     = useForm({ title: '', doctor_name: '', start: null, end: null, id: null })

const calendarOptions = ref({
    locale: esLocale,
    events: [...props.events],
    plugins: [dayGridPlugin, timeGridPlugin, listPlugin, interactionPlugin],
    initialView: 'timeGridWeek',
    headerToolbar: {
        title: 'dayGridMonth,timeGridWeek,timeGridDay,list',
        end: 'dayGridMonth,timeGridWeek,timeGridDay,list',
    },
    selectable: true,
    slotMinTime: '06:00:00',
    slotMaxTime: '21:00:00',
    slotDuration: '00:20:00',
    select: showForm,
    eventClick: eventClick,
})

watch(() => props.events, (newValue, oldValue) => {
    calendarOptions.value.events = [...newValue]
})

function eventClick(arg) {
    setEvent(arg.event)
    dialog.value = true
}
function showForm(arg) {
    setEvent(arg)
    dialog.value = true
}

function setEvent(arg) {
    if (arg.id) {
        let event = props.events.find(needle => arg.id == needle.id)
        form.doctor_name = event.doctor_name
        form.id = event.id
    }
    form.start = arg.startStr
    form.end = arg.endStr
    form.title = arg.title

    form.startHour = String(arg.start.getHours()).padStart(2, '0') + ':' + String(arg.start.getMinutes()).padStart(2, '0')
    form.endHour = String(arg.end.getHours()).padStart(2, '0') + ':' + String(arg.end.getMinutes()).padStart(2, '0')

}

function createEvent(arg) {
    if (form.id) {
        form.put(`/events/${form.id}`, {
            onSuccess: response => { form.reset(); dialog.value = false }
        })
        return
    }
    form.post('/events', {
        onSuccess: response => { form.reset(); dialog.value = false }
    })
}

function deleteEvent(){
    form.delete(`/events/${form.id}`, {
        onSuccess: response => { form.reset(); dialog.value = false; deleting.value = false }
    })
}
</script>
<template>
    <AppLayout title="Registrar evento">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 pt-3">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-3">
                <FullCalendar :options="calendarOptions" />
            </div>
        </div>

        <DialogModal :max-width="'xl'" :show="dialog" :closeable="true">
            <template v-slot:title>
                <div class="flex">
                    <template v-if="!deleting">
                        {{ form.id ? 'Actualizar' : 'Registrar' }} Evento
                    </template>
                    <template v-else>
                        <div class="text-center">
                            Eliminar evento
                        </div>
                    </template>
                    <div class="ms-auto">
                        <SecondaryButton @click="()=>{dialog=false; form.reset()}">&times;</SecondaryButton>
                    </div>
                </div>
            </template>
            <template v-slot:content>
                <div v-if="!deleting">
                    <div class="block w-full">
                        <InputLabel for="name" value="Nombre del evento" />
                        <TextInput id="eventName" v-model="form.title" type="text" class="mt-1 block w-full" required
                            autofocus />
                        <InputError class="mt-2" :message="form.errors.title" />
                    </div>

                    <div class="mt-4 block w-full">
                        <InputLabel for="doctorName" value="Nombre del médico" />
                        <TextInput id="doctorName" v-model="form.doctor_name" type="text" class="mt-1 block w-full"
                            required />
                        <InputError class="mt-2" :message="form.errors.doctor_name" />
                    </div>

                    <div class="flex">
                        <div class="mt-4 block w-full me-2">
                            <InputLabel for="startsAt" value="Hora de inicio" />
                            <TextInput id="startsAt" :value="form.startHour" type="text" class="mt-1 block w-full"
                                required />
                            <InputError class="mt-2" :message="form.errors.start" />
                        </div>
                        <div class="mt-4 block w-full ms-2">
                            <InputLabel for="endsAt" value="Hora de fin" />
                            <TextInput id="endsAt" :value="form.endHour" type="text" class="mt-1 block w-full" required />
                            <InputError class="mt-2" :message="form.errors.end" />
                        </div>
                    </div>

                    <div class="flex items-center justify-end mt-4">
                        <DangerButton @click="deleting=true" v-if="form.id">
                            Eliminar
                        </DangerButton>

                        <PrimaryButton @click="createEvent" class="ms-4" :class="{ 'opacity-25': form.processing }"
                            :disabled="form.processing">
                            {{ form.id ? 'Actualizar' : 'Registrar' }}
                        </PrimaryButton>
                    </div>
                </div>
                <div v-else>
                    <p class="color-grey text-center mt-3">¿Seguro que desea eliminar el evento?</p>
                    <div class="flex justify-center mt-3">
                        <SecondaryButton class="me-3" @click="deleting=null">Cancelar</SecondaryButton>
                        <DangerButton @click="deleteEvent">Sí, eliminar</DangerButton>
                    </div>
                </div>
            </template>
        </DialogModal>
    </AppLayout>
</template>
