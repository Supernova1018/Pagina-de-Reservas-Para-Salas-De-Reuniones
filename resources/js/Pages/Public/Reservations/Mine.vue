<script setup>
import { computed } from 'vue';
import { Head, Link, router } from '@inertiajs/vue3';

const props = defineProps({
    reservations: Array,
    message: String,
});

function formatDateTime(iso) {
    const date = new Date(iso);
    return date.toLocaleDateString('es-CO', { day: '2-digit', month: 'short' }) + ' ' + date.toLocaleTimeString('es-CO', { hour: '2-digit', minute: '2-digit' });
}

function formatMoney(value) {
    return '$' + Number(value || 0).toLocaleString('es-CO');
}

const metrics = computed(() => [
    { title: 'Reservas totales', value: props.reservations.length },
    { title: 'Cancelables', value: props.reservations.filter((item) => item.can_cancel).length },
    { title: 'Canceladas', value: props.reservations.filter((item) => item.status === 'cancelled').length },
]);

function cancelReservation(slug) {
    if (!confirm('¿Cancelar esta reserva?')) {
        return;
    }

    router.post(route('public.reservations.cancel', slug));
}
</script>

<template>
    <Head title="Mis reservas" />

    <div class="min-h-screen bg-slate-900 text-slate-200">
        <header class="border-b border-violet-700/20 bg-slate-900/40 backdrop-blur">
            <div class="mx-auto max-w-7xl px-4 py-6 sm:px-6 lg:px-8">
                <div class="flex flex-wrap items-center justify-between gap-4">
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-[0.3em] text-violet-300">Reservas del usuario</p>
                        <h1 class="mt-2 text-3xl font-bold text-white">Mis reservas</h1>
                        <p class="mt-2 max-w-2xl text-sm text-slate-400">Aquí puedes revisar tus reservas activas y cancelar las que aún estén confirmadas.</p>
                    </div>

                    <div class="flex flex-wrap gap-2">
                        <Link href="/" class="rounded-xl border border-white/10 bg-white/5 px-4 py-2 text-sm font-semibold text-slate-200 transition hover:border-violet-400/30 hover:bg-violet-500/10 hover:text-white">
                            Ver salas
                        </Link>
                        <Link href="/reservations/new" class="rounded-xl bg-violet-600 px-4 py-2 text-sm font-semibold text-white transition hover:bg-violet-700">
                            Nueva reserva
                        </Link>
                    </div>
                </div>
            </div>
        </header>

        <main class="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8">
            <div v-if="message" class="mb-6 rounded-2xl border border-emerald-400/20 bg-emerald-500/10 px-4 py-3 text-sm text-emerald-100">
                {{ message }}
            </div>

            <div class="grid gap-4 md:grid-cols-3">
                <div v-for="metric in metrics" :key="metric.title" class="rounded-3xl border border-white/10 bg-slate-900/85 p-5 shadow-2xl shadow-violet-950/20 ring-1 ring-white/5">
                    <div class="text-xs uppercase tracking-[0.25em] text-slate-400">{{ metric.title }}</div>
                    <div class="mt-2 text-3xl font-bold text-white">{{ metric.value }}</div>
                </div>
            </div>

            <div class="mt-8 space-y-4">
                <article v-for="reservation in reservations" :key="reservation.slug" class="overflow-hidden rounded-3xl border border-white/10 bg-slate-900/85 shadow-2xl shadow-violet-950/20 ring-1 ring-white/5">
                    <div class="grid gap-0 lg:grid-cols-[300px_1fr]">
                        <div class="relative min-h-56 overflow-hidden border-b border-white/10 bg-slate-950/40 lg:min-h-full lg:border-b-0 lg:border-r">
                            <img v-if="reservation.space.image" :src="reservation.space.image" :alt="reservation.space.name" class="h-full w-full object-contain p-5" />
                            <div v-else class="flex h-full items-center justify-center px-6 py-10 text-center text-sm text-slate-400">
                                Sin imagen disponible para esta sala.
                            </div>
                        </div>

                        <div class="p-5 sm:p-6">
                            <div class="flex flex-wrap items-start justify-between gap-4">
                                <div>
                                    <div class="inline-flex rounded-full px-3 py-1 text-xs font-semibold" :class="reservation.status === 'confirmed' ? 'bg-emerald-500/15 text-emerald-200' : reservation.status === 'cancelled' ? 'bg-slate-500/15 text-slate-200' : 'bg-violet-500/15 text-violet-200'">
                                        {{ reservation.status_label }}
                                    </div>
                                    <h2 class="mt-3 text-2xl font-bold text-white">{{ reservation.space.name }}</h2>
                                    <p class="mt-1 text-sm text-slate-400">{{ reservation.space.location || 'Sin ubicación' }}</p>
                                </div>

                                <div class="text-right">
                                    <div class="text-sm text-slate-400">Total</div>
                                    <div class="text-xl font-bold text-violet-200">{{ formatMoney(reservation.total_price) }}</div>
                                </div>
                            </div>

                            <div class="mt-5 grid gap-3 sm:grid-cols-2 xl:grid-cols-4">
                                <div class="rounded-2xl border border-white/10 bg-white/5 p-4">
                                    <div class="text-xs uppercase tracking-[0.25em] text-slate-400">Inicio</div>
                                    <div class="mt-1 text-sm font-semibold text-slate-100">{{ formatDateTime(reservation.start_time) }}</div>
                                </div>
                                <div class="rounded-2xl border border-white/10 bg-white/5 p-4">
                                    <div class="text-xs uppercase tracking-[0.25em] text-slate-400">Fin</div>
                                    <div class="mt-1 text-sm font-semibold text-slate-100">{{ formatDateTime(reservation.end_time) }}</div>
                                </div>
                                <div class="rounded-2xl border border-white/10 bg-white/5 p-4">
                                    <div class="text-xs uppercase tracking-[0.25em] text-slate-400">Correo</div>
                                    <div class="mt-1 truncate text-sm font-semibold text-slate-100">{{ reservation.user_email }}</div>
                                </div>
                                <div class="rounded-2xl border border-white/10 bg-white/5 p-4">
                                    <div class="text-xs uppercase tracking-[0.25em] text-slate-400">Slug</div>
                                    <div class="mt-1 font-mono text-sm font-semibold text-slate-100">{{ reservation.slug }}</div>
                                </div>
                            </div>

                            <div class="mt-5 flex flex-wrap gap-3">
                                <Link :href="'/spaces/' + reservation.space.slug" class="rounded-xl border border-white/10 bg-white/5 px-4 py-2 text-sm font-semibold text-slate-200 transition hover:border-violet-400/30 hover:bg-violet-500/10 hover:text-white">
                                    Ver sala
                                </Link>
                                <button
                                    v-if="reservation.can_cancel"
                                    @click="cancelReservation(reservation.slug)"
                                    class="rounded-xl border border-rose-400/30 px-4 py-2 text-sm font-semibold text-rose-100 transition hover:bg-rose-500/20"
                                >
                                    Cancelar reserva
                                </button>
                                <span v-else class="rounded-xl border border-white/10 bg-white/5 px-4 py-2 text-sm font-semibold text-slate-400">
                                    No cancelable
                                </span>
                            </div>
                        </div>
                    </div>
                </article>

                <div v-if="reservations.length === 0" class="rounded-3xl border border-dashed border-white/10 bg-slate-900/85 p-10 text-center text-slate-400">
                    Todavía no tienes reservas registradas.
                </div>
            </div>
        </main>
    </div>
</template>
