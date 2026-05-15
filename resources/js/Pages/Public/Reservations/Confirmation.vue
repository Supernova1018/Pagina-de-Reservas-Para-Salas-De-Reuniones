<script setup>
import { computed } from 'vue';
import { Head, Link, router, usePage } from '@inertiajs/vue3';
import ReservationRatingForm from '@/Components/ReservationRatingForm.vue';

const props = defineProps({
    reservation: Object,
});

const reservation = computed(() => props.reservation);

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
    if (!currentUser.value || !reservation.value) {
        return false;
    }

    return reservation.value.status === 'confirmed'
        && (reservation.value.user_id === currentUser.value.id || reservation.value.user_email === currentUser.value.email);
});

function cancelReservation() {
    if (!confirm('¿Cancelar esta reserva?')) {
        return;
    }

    router.post(route('public.reservations.cancel', reservation.value.slug));
}
</script>

<template>
    <Head title="Reserva confirmada" />

    <div class="min-h-screen bg-gradient-to-br from-slate-900 to-slate-800 px-4 py-6 sm:py-8">
        <div class="mx-auto w-full max-w-2xl overflow-hidden rounded-3xl border border-violet-700/30 bg-slate-900 shadow-2xl">
            <div class="relative h-40 overflow-hidden border-b border-white/10 bg-cover bg-center sm:h-48" :style="reservation.space.image ? { backgroundImage: `url(${reservation.space.image})` } : undefined">
                <div class="absolute inset-0 bg-gradient-to-b from-slate-900/20 via-slate-900/40 to-slate-900/80"></div>
                <div class="absolute inset-0 flex flex-col items-center justify-center p-6 text-center">
                    <div class="text-5xl mb-2">✓</div>
                    <h1 class="text-2xl font-bold text-white">¡Reserva confirmada!</h1>
                    <p class="mt-2 text-sm text-violet-200">{{ reservation.space.name }}</p>
                </div>
            </div>

            <div class="space-y-4 p-4 sm:p-6">
                <div class="flex items-center justify-between gap-3">
                    <div>
                        <h2 class="text-sm font-bold text-slate-100">Detalles de tu reserva</h2>
                    </div>
                    <div v-if="reservation.status !== 'pending'" class="flex items-center justify-center">
                        <span
                            class="rounded-full border px-3 py-1 text-xs font-bold sm:px-4 sm:py-1.5 sm:text-sm whitespace-nowrap"
                            :class="statusColors[reservation.status] || 'bg-slate-800/50 text-slate-300'"
                        >
                            {{ reservation.status_label }}
                        </span>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-2 rounded-xl border border-violet-700/20 bg-slate-800/50 p-3 sm:gap-3 sm:p-4">
                    <div class="rounded-lg border border-white/5 bg-slate-900/30 px-3 py-2 text-sm">
                        <div class="text-[10px] uppercase tracking-wider text-slate-400">Tipo</div>
                        <div class="mt-1 font-semibold text-slate-100 text-xs">{{ reservation.space.type_label }}</div>
                    </div>
                    <div class="rounded-lg border border-white/5 bg-slate-900/30 px-3 py-2 text-sm">
                        <div class="text-[10px] uppercase tracking-wider text-slate-400">Fecha</div>
                        <div class="mt-1 font-semibold text-slate-100 text-xs">{{ formatDate(reservation.start_time) }}</div>
                    </div>
                    <div class="rounded-lg border border-white/5 bg-slate-900/30 px-3 py-2 text-sm col-span-2 sm:col-span-1">
                        <div class="text-[10px] uppercase tracking-wider text-slate-400">Horario</div>
                        <div class="mt-1 font-semibold text-slate-100 text-xs">{{ formatTime(reservation.start_time) }} – {{ formatTime(reservation.end_time) }}</div>
                    </div>
                    <div v-if="reservation.space.location" class="rounded-lg border border-white/5 bg-slate-900/30 px-3 py-2 text-sm col-span-2">
                        <div class="text-[10px] uppercase tracking-wider text-slate-400">Ubicación</div>
                        <div class="mt-1 font-semibold text-slate-100 text-xs">{{ reservation.space.location }}</div>
                    </div>
                    <div class="rounded-lg border border-white/5 bg-slate-900/30 px-3 py-2 text-sm">
                        <div class="text-[10px] uppercase tracking-wider text-slate-400">Solicitante</div>
                        <div class="mt-1 font-semibold text-slate-100 text-xs">{{ reservation.user_name }}</div>
                    </div>
                    <div class="rounded-lg border border-white/5 bg-slate-900/30 px-3 py-2 text-sm">
                        <div class="text-[10px] uppercase tracking-wider text-slate-400">Correo</div>
                        <div class="mt-1 font-semibold text-slate-100 text-xs truncate">{{ reservation.user_email }}</div>
                    </div>
                    <div v-if="reservation.user_phone" class="rounded-lg border border-white/5 bg-slate-900/30 px-3 py-2 text-sm col-span-2 sm:col-span-1">
                        <div class="text-[10px] uppercase tracking-wider text-slate-400">Teléfono</div>
                        <div class="mt-1 font-semibold text-slate-100 text-xs">{{ reservation.user_phone }}</div>
                    </div>
                    <div v-if="reservation.organization" class="rounded-lg border border-white/5 bg-slate-900/30 px-3 py-2 text-sm col-span-2 sm:col-span-1">
                        <div class="text-[10px] uppercase tracking-wider text-slate-400">Organización</div>
                        <div class="mt-1 font-semibold text-slate-100 text-xs truncate">{{ reservation.organization }}</div>
                    </div>
                    <div v-if="reservation.notes" class="rounded-lg border border-white/5 bg-slate-900/30 px-3 py-2 text-sm col-span-2">
                        <div class="text-[10px] uppercase tracking-wider text-slate-400">Notas</div>
                        <div class="mt-1 font-semibold text-slate-100 text-xs line-clamp-2">{{ reservation.notes }}</div>
                    </div>
                    <div v-if="reservation.total_price > 0" class="rounded-lg border border-white/5 bg-slate-900/30 px-3 py-2 text-sm col-span-2">
                        <div class="text-[10px] uppercase tracking-wider text-slate-400">Total estimado</div>
                        <div class="mt-1 font-bold text-violet-300 text-lg">${{ Number(reservation.total_price).toLocaleString('es-CO') }}</div>
                    </div>
                </div>

                <ReservationRatingForm v-if="reservation.status === 'completed'" :reservation-slug="reservation.slug" />

                <div v-if="canCancel" class="rounded-xl border border-rose-400/20 bg-rose-500/10 p-3 text-sm text-rose-100 sm:p-4">
                    <div class="font-semibold">Puedes cancelar esta reserva si ya no la necesitas.</div>
                    <button @click="cancelReservation" class="mt-3 inline-flex rounded-lg border border-rose-400/30 px-3 py-2 text-sm font-semibold text-rose-100 transition hover:bg-rose-500/20">
                        Cancelar reserva
                    </button>
                </div>

                <div class="flex flex-col gap-3 sm:flex-row">
                    <Link
                        href="/"
                        class="flex-1 rounded-lg bg-violet-600 py-2.5 text-center text-sm font-semibold text-white transition hover:bg-violet-700"
                    >
                        Ver más salas
                    </Link>
                    <Link
                        v-if="currentUser"
                        :href="route('public.reservations.mine')"
                        class="flex-1 rounded-lg border border-violet-700/30 py-2.5 text-center text-sm font-medium text-slate-200 transition hover:border-violet-500/50"
                    >
                        Mis reservas
                    </Link>
                    <Link
                        :href="'/spaces/' + reservation.space.slug"
                        class="flex-1 rounded-lg border border-violet-700/30 py-2.5 text-center text-sm font-medium text-slate-200 transition hover:border-violet-500/50"
                    >
                        Volver a la sala
                    </Link>
                </div>
            </div>
        </div>
    </div>
</template>