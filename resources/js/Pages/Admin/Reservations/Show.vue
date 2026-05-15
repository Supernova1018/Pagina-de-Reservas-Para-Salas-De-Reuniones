<script setup>
import { Head, Link, router } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';

const props = defineProps({
    reservation: Object,
});

const tabs = [
    { label: 'Salas', href: route('admin.spaces.index') },
    { label: 'Crear sala', href: route('admin.spaces.create') },
    { label: 'Reservas', href: route('admin.reservations.index') },
    { label: 'Nueva reserva', href: route('public.reservations.create') },
];

const statusClasses = {
    confirmed: 'bg-emerald-700/10 text-emerald-200 border-emerald-700/10',
    rejected: 'bg-red-700/10 text-red-200 border-red-700/10',
    cancelled: 'bg-slate-700/10 text-slate-200 border-slate-700/10',
    completed: 'bg-blue-700/10 text-blue-200 border-blue-700/10',
};

function formatDate(iso) {
    return new Date(iso).toLocaleDateString('es-CO', {
        weekday: 'long', year: 'numeric', month: 'long', day: 'numeric',
    });
}

function formatTime(iso) {
    return new Date(iso).toLocaleTimeString('es-CO', { hour: '2-digit', minute: '2-digit' });
}

function confirmReservation() {
    router.post(route('admin.reservations.accept', props.reservation.slug));
}

function rejectReservation() {
    router.post(route('admin.reservations.reject', props.reservation.slug));
}

function cancelReservation() {
    router.post(route('admin.reservations.cancel', props.reservation.slug));
}
</script>

<template>
    <Head :title="'Reserva ' + reservation.slug" />

    <AppLayout title="Reserva">
        <template #header>
            <div class="space-y-4">
                    <div class="flex items-center gap-3 text-sm">
                    <Link :href="route('admin.reservations.index')" class="text-slate-300 hover:text-white">← Reservas</Link>
                    <span class="text-slate-500">/</span>
                    <h2 class="font-semibold text-xl text-white">Reserva {{ reservation.slug }}</h2>
                </div>

                <div class="flex flex-wrap gap-2">
                    <Link
                        v-for="tab in tabs"
                        :key="tab.label"
                        :href="tab.href"
                        class="rounded-full border border-white/10 bg-slate-900/85 px-4 py-2 text-sm font-semibold text-slate-200 transition hover:border-violet-400/30 hover:bg-violet-500/10"
                    >
                        {{ tab.label }}
                    </Link>
                </div>
            </div>
        </template>

        <div class="py-8">
            <div class="mx-auto grid max-w-7xl grid-cols-1 gap-6 px-4 sm:px-6 lg:px-8 xl:grid-cols-3">
                <div class="space-y-6 xl:col-span-2">
                    <div class="rounded-3xl border border-white/10 bg-slate-900/85 p-6 shadow-sm sm:p-8">
                        <div class="flex flex-wrap items-start justify-between gap-4">
                            <div>
                                <p class="text-xs font-semibold uppercase tracking-[0.28em] text-slate-400">Detalle de reserva</p>
                                <h1 class="mt-2 text-2xl font-bold text-white">{{ reservation.user_name }}</h1>
                                <p class="mt-1 text-sm text-slate-400">{{ reservation.user_email }}</p>
                            </div>
                            <span class="rounded-full border px-4 py-1.5 text-sm font-semibold" :class="statusClasses[reservation.status] || 'bg-slate-700/10 text-slate-200 border-slate-700/10'">
                                {{ reservation.status_label }}
                            </span>
                        </div>

                        <div class="mt-6 grid grid-cols-1 gap-4 rounded-2xl bg-slate-900/80 p-4 text-sm text-slate-300 sm:grid-cols-2">
                            <div>
                                <div class="text-xs uppercase tracking-wide text-slate-400">Sala</div>
                                <div class="mt-1 font-semibold text-white">{{ reservation.space.name }}</div>
                            </div>
                            <div>
                                <div class="text-xs uppercase tracking-wide text-slate-400">Horario</div>
                                <div class="mt-1 font-semibold text-white">{{ formatDate(reservation.start_time) }}</div>
                                <div class="text-slate-400">{{ formatTime(reservation.start_time) }} - {{ formatTime(reservation.end_time) }}</div>
                            </div>
                            <div v-if="reservation.organization">
                                <div class="text-xs uppercase tracking-wide text-slate-400">Organización</div>
                                <div class="mt-1 font-semibold text-white">{{ reservation.organization }}</div>
                            </div>
                            <div v-if="reservation.user_phone">
                                <div class="text-xs uppercase tracking-wide text-slate-400">Teléfono</div>
                                <div class="mt-1 font-semibold text-white">{{ reservation.user_phone }}</div>
                            </div>
                            <div class="sm:col-span-2">
                                <div class="text-xs uppercase tracking-wide text-slate-400">Notas</div>
                                <div class="mt-1 whitespace-pre-line text-slate-300">{{ reservation.notes || 'Sin notas adicionales.' }}</div>
                            </div>
                        </div>
                    </div>

                    <div class="rounded-3xl border border-white/10 bg-slate-900/85 p-6 shadow-sm sm:p-8">
                        <h2 class="text-lg font-bold text-white">Resumen económico</h2>
                        <div class="mt-4 flex items-center justify-between rounded-2xl bg-slate-800/70 px-4 py-3 text-sm text-slate-200">
                            <span>Total estimado</span>
                            <span class="text-lg font-bold">${{ Number(reservation.total_price).toLocaleString('es-CO') }}</span>
                        </div>
                        <p class="mt-3 text-sm text-slate-400">La reserva puede pasar por estados de confirmación, rechazo, cancelación o finalización.</p>
                    </div>
                </div>

                <aside class="space-y-6 xl:sticky xl:top-6 h-fit">
                    <div class="rounded-3xl border border-white/10 bg-slate-900/85 p-6 shadow-sm">
                        <h3 class="text-lg font-bold text-white">Acciones rápidas</h3>
                        <p class="mt-1 text-sm text-slate-400">Las acciones cambian el estado y disparan la notificación correspondiente.</p>

                        <div class="mt-5 space-y-3">
                            <button v-if="reservation.status === 'confirmed'" @click="cancelReservation" class="w-full rounded-xl border border-white/10 px-4 py-3 text-sm font-semibold text-slate-200 transition hover:border-white/20">
                                Cancelar reserva
                            </button>
                        </div>
                    </div>

                    <div class="rounded-3xl border border-white/10 bg-gradient-to-br from-slate-900 to-slate-800 p-6 text-white shadow-sm">
                        <h3 class="text-lg font-bold">Navegación</h3>
                        <div class="mt-4 space-y-3">
                            <Link :href="route('admin.reservations.index')" class="block rounded-xl border border-white/10 bg-white/5 px-4 py-3 text-sm font-semibold text-white transition hover:bg-white/10">
                                Volver al listado
                            </Link>
                            <Link :href="route('public.reservations.create')" class="block rounded-xl border border-white/10 bg-white/5 px-4 py-3 text-sm font-semibold text-white transition hover:bg-white/10">
                                Crear otra reserva
                            </Link>
                        </div>
                    </div>
                </aside>
            </div>
        </div>
    </AppLayout>
</template>
