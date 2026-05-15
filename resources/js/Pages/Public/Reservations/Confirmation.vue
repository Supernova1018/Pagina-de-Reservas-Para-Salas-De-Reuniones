<script setup>
import { computed } from 'vue';
import { Head, Link, router, usePage } from '@inertiajs/vue3';
import ReservationRatingForm from '@/Components/ReservationRatingForm.vue';

const props = defineProps({
    reservation: Object,
});

function formatDate(iso) {
    return new Date(iso).toLocaleDateString('es-CO', {
        weekday: 'long', year: 'numeric', month: 'long', day: 'numeric',
    });
}

function formatTime(iso) {
    return new Date(iso).toLocaleTimeString('es-CO', { hour: '2-digit', minute: '2-digit' });
}

const statusColors = {
    confirmed: 'bg-emerald-100/20 text-emerald-300 border-emerald-400/30',
    rejected:  'bg-rose-100/20 text-rose-300 border-rose-400/30',
    cancelled: 'bg-gray-100/20 text-gray-300 border-gray-400/30',
    completed: 'bg-violet-100/20 text-violet-300 border-violet-400/30',
};

const page = usePage();

const currentUser = computed(() => page.props.auth?.user || null);

const canCancel = computed(() => {
    if (!currentUser.value) {
        return false;
    }

    return reservation.status === 'confirmed'
        && (reservation.user_id === currentUser.value.id || reservation.user_email === currentUser.value.email);
});

function cancelReservation() {
    if (!confirm('¿Cancelar esta reserva?')) {
        return;
    }

    router.post(route('public.reservations.cancel', reservation.slug));
}
</script>

<template>
    <Head title="Reserva confirmada" />

    <div class="min-h-screen bg-gradient-to-br from-slate-900 to-slate-800 flex items-center justify-center px-4 py-12">
        <div class="bg-slate-900 rounded-2xl shadow-2xl border border-violet-700/30 max-w-lg w-full overflow-hidden">

            <!-- Banner -->
            <div class="bg-gradient-to-r from-violet-600/50 to-purple-600/50 px-6 py-8 text-center text-slate-100">
                <div class="text-5xl mb-3">✓</div>
                <h1 class="text-2xl font-bold text-slate-100">¡Reserva confirmada!</h1>
                <p class="text-violet-200 text-sm mt-2">Tu solicitud está lista y disponible.</p>
            </div>

            <div class="h-48 bg-cover bg-center" :style="reservation.space.image ? { backgroundImage: `url(${reservation.space.image})` } : undefined"></div>

            <div class="p-6 space-y-5">
                <!-- Status badge - only show if not pending -->
                <div v-if="reservation.status !== 'pending'" class="flex items-center justify-center">
                    <span
                        class="text-sm font-bold px-4 py-1.5 rounded-full border"
                        :class="statusColors[reservation.status] || 'bg-slate-800/50 text-slate-300'"
                    >
                        {{ reservation.status_label }}
                    </span>
                </div>

                <div class="rounded-xl border border-violet-700/30 bg-violet-600/10 p-4">
                    <h2 class="text-sm font-bold text-slate-100">Detalles de la reserva</h2>
                    <p class="mt-1 text-xs text-slate-400">Información principal de tu reserva confirmada.</p>
                </div>

                <!-- Reservation details -->
                <div class="bg-slate-800/50 rounded-xl border border-violet-700/20 divide-y divide-violet-700/10">
                    <div class="px-4 py-3 flex justify-between text-sm">
                        <span class="text-slate-400">Sala</span>
                        <span class="font-semibold text-slate-100">{{ reservation.space.name }}</span>
                    </div>
                    <div class="px-4 py-3 flex justify-between text-sm">
                        <span class="text-slate-400">Fecha</span>
                        <span class="font-semibold text-slate-100 text-right">{{ formatDate(reservation.start_time) }}</span>
                    </div>
                    <div class="px-4 py-3 flex justify-between text-sm">
                        <span class="text-slate-400">Horario</span>
                        <span class="font-semibold text-slate-100">
                            {{ formatTime(reservation.start_time) }} – {{ formatTime(reservation.end_time) }}
                        </span>
                    </div>
                    <div v-if="reservation.space.location" class="px-4 py-3 flex justify-between text-sm">
                        <span class="text-slate-400">Ubicación</span>
                        <span class="font-semibold text-slate-100 text-right">{{ reservation.space.location }}</span>
                    </div>
                    <div class="px-4 py-3 flex justify-between text-sm">
                        <span class="text-slate-400">Solicitante</span>
                        <span class="font-semibold text-slate-100">{{ reservation.user_name }}</span>
                    </div>
                    <div class="px-4 py-3 flex justify-between text-sm">
                        <span class="text-slate-400">Correo</span>
                        <span class="font-semibold text-slate-100">{{ reservation.user_email }}</span>
                    </div>
                    <div v-if="reservation.user_phone" class="px-4 py-3 flex justify-between text-sm">
                        <span class="text-slate-400">Teléfono</span>
                        <span class="font-semibold text-slate-100">{{ reservation.user_phone }}</span>
                    </div>
                    <div v-if="reservation.organization" class="px-4 py-3 flex justify-between text-sm">
                        <span class="text-slate-400">Organización</span>
                        <span class="font-semibold text-slate-100 text-right">{{ reservation.organization }}</span>
                    </div>
                    <div v-if="reservation.notes" class="px-4 py-3 flex justify-between text-sm">
                        <span class="text-slate-400">Notas</span>
                        <span class="font-semibold text-slate-100 text-right">{{ reservation.notes }}</span>
                    </div>
                    <div v-if="reservation.total_price > 0" class="px-4 py-3 flex justify-between text-sm">
                        <span class="text-slate-400">Total estimado</span>
                        <span class="font-bold text-violet-300">${{ Number(reservation.total_price).toLocaleString('es-CO') }}</span>
                    </div>
                </div>

                <!-- Rating form - show only if reservation is completed -->
                <ReservationRatingForm v-if="reservation.status === 'completed'" :reservation-slug="reservation.slug" />

                <div v-if="canCancel" class="rounded-xl border border-rose-400/20 bg-rose-500/10 p-4 text-sm text-rose-100">
                    <div class="font-semibold">Puedes cancelar esta reserva si ya no la necesitas.</div>
                    <button @click="cancelReservation" class="mt-3 inline-flex rounded-lg border border-rose-400/30 px-4 py-2 font-semibold text-rose-100 transition hover:bg-rose-500/20">
                        Cancelar reserva
                    </button>
                </div>

                <div class="flex gap-3">
                    <Link
                        href="/"
                        class="flex-1 text-center bg-violet-600 hover:bg-violet-700 text-white font-semibold py-2.5 rounded-lg text-sm transition"
                    >
                        Ver más salas
                    </Link>
                    <Link
                        v-if="currentUser"
                        :href="route('public.reservations.mine')"
                        class="flex-1 text-center border border-violet-700/30 text-slate-200 hover:border-violet-500/50 font-medium py-2.5 rounded-lg text-sm transition"
                    >
                        Mis reservas
                    </Link>
                    <Link
                        :href="'/spaces/' + reservation.space.slug"
                        class="flex-1 text-center border border-violet-700/30 text-slate-200 hover:border-violet-500/50 font-medium py-2.5 rounded-lg text-sm transition"
                    >
                        Volver a la sala
                    </Link>
                </div>
            </div>
        </div>
    </div>
</template>