<script setup>
import { computed } from 'vue';
import { Head, Link } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';

const props = defineProps({
    stats:              Object,
    recentReservations: Array,
    upcomingToday:      Array,
    topSpaces:          Array,
});

function formatTime(iso) {
    return new Date(iso).toLocaleTimeString('es-CO', { hour: '2-digit', minute: '2-digit' });
}

function formatDateTime(iso) {
    const d = new Date(iso);
    return d.toLocaleDateString('es-CO', { day: '2-digit', month: 'short' }) + ' ' +
           d.toLocaleTimeString('es-CO', { hour: '2-digit', minute: '2-digit' });
}

const statusClasses = {
    confirmed: 'bg-emerald-100/20 text-emerald-300',
    rejected:  'bg-rose-100/20 text-rose-300',
    cancelled: 'bg-slate-100/20 text-slate-300',
    completed: 'bg-violet-100/20 text-violet-300',
};

const statusLabels = {
    confirmed: 'Confirmada',
    rejected:  'Rechazada',
    cancelled: 'Cancelada',
    completed: 'Finalizada',
};

const availabilityBlocks = computed(() => [
    { title: 'Salas activas', value: props.stats.active_spaces, hint: 'Disponibles para reservar ahora', tone: 'from-violet-500/20 to-fuchsia-500/20 text-violet-100' },
    { title: 'Confirmadas hoy', value: props.stats.today_count, hint: 'Agenda del día en curso', tone: 'from-cyan-500/20 to-sky-500/20 text-cyan-100' },
    { title: 'Ingresos totales', value: '$' + Number(props.stats.revenue_total || 0).toLocaleString('es-CO'), hint: 'Suma de confirmadas y finalizadas', tone: 'from-emerald-500/20 to-teal-500/20 text-emerald-100' },
    { title: 'Valoraciones', value: props.stats.ratings_count, hint: 'Reseñas posteriores al uso', tone: 'from-amber-500/20 to-orange-500/20 text-amber-100' },
]);
</script>

<template>
    <Head title="Dashboard" />
    <AppLayout title="Dashboard">
        <template #header>
            <div class="flex flex-wrap items-end justify-between gap-4">
                <div>
                    <p class="text-xs font-semibold uppercase tracking-[0.3em] text-violet-300">Panel de gestión</p>
                    <h2 class="mt-2 text-3xl font-bold text-slate-50">Disponibilidad y reservas</h2>
                    <p class="mt-2 max-w-2xl text-sm text-slate-400">Resumen oscuro con acentos púrpura para revisar disponibilidad, reservas del día y accesos rápidos.</p>
                </div>

                <Link href="/admin/calendar" class="rounded-2xl border border-violet-400/30 bg-violet-500/10 px-4 py-2.5 text-sm font-semibold text-violet-100 transition hover:bg-violet-500/20">
                    Ver calendario de disponibilidad
                </Link>
            </div>
        </template>

        <div class="px-4 py-8 sm:px-6 lg:px-8">
            <div class="mx-auto max-w-7xl space-y-8">

                <div class="grid gap-4 md:grid-cols-3">
                    <div v-for="block in availabilityBlocks" :key="block.title" class="rounded-3xl border border-white/10 bg-slate-900/80 p-5 shadow-2xl shadow-violet-950/20 ring-1 ring-white/5">
                        <div class="inline-flex rounded-full bg-gradient-to-r px-3 py-1 text-xs font-semibold" :class="block.tone">{{ block.title }}</div>
                        <div class="mt-4 text-3xl font-bold text-white">{{ block.value }}</div>
                        <div class="mt-2 text-sm text-slate-400">{{ block.hint }}</div>
                    </div>
                </div>

                <!-- Stats grid -->
                <div class="grid grid-cols-2 gap-4 lg:grid-cols-3">
                    <div class="rounded-3xl border border-white/10 bg-slate-900/80 p-5 shadow-2xl shadow-violet-950/20 ring-1 ring-white/5">
                        <div class="text-3xl font-bold text-emerald-300">{{ stats.confirmed_count }}</div>
                        <div class="mt-1 text-sm text-slate-400">Confirmadas</div>
                    </div>
                    <div class="rounded-3xl border border-white/10 bg-slate-900/80 p-5 shadow-2xl shadow-violet-950/20 ring-1 ring-white/5">
                        <div class="text-3xl font-bold text-cyan-300">{{ stats.today_count }}</div>
                        <div class="mt-1 text-sm text-slate-400">Hoy</div>
                    </div>
                    <div class="rounded-3xl border border-white/10 bg-slate-900/80 p-5 shadow-2xl shadow-violet-950/20 ring-1 ring-white/5">
                        <div class="text-3xl font-bold text-slate-100">{{ stats.this_month_count }}</div>
                        <div class="mt-1 text-sm text-slate-400">Este mes</div>
                    </div>
                    <div class="rounded-3xl border border-white/10 bg-slate-900/80 p-5 shadow-2xl shadow-violet-950/20 ring-1 ring-white/5">
                        <div class="text-3xl font-bold text-violet-300">{{ stats.active_spaces }}</div>
                        <div class="mt-1 text-sm text-slate-400">Salas activas</div>
                    </div>
                    <div class="rounded-3xl border border-white/10 bg-slate-900/80 p-5 shadow-2xl shadow-violet-950/20 ring-1 ring-white/5">
                        <div class="text-3xl font-bold text-slate-100">{{ stats.total_spaces }}</div>
                        <div class="mt-1 text-sm text-slate-400">Salas totales</div>
                    </div>
                    <div class="rounded-3xl border border-white/10 bg-slate-900/80 p-5 shadow-2xl shadow-violet-950/20 ring-1 ring-white/5">
                        <div class="text-3xl font-bold text-amber-300">{{ stats.cancelled_count }}</div>
                        <div class="mt-1 text-sm text-slate-400">Canceladas</div>
                    </div>
                </div>

                <!-- Quick actions -->
                <div class="flex flex-wrap gap-3">
                    <Link
                        href="/admin/spaces/create"
                        class="rounded-2xl border border-violet-400/30 bg-violet-500/15 px-4 py-2.5 text-sm font-semibold text-violet-100 transition hover:bg-violet-500/25"
                    >
                        + Nueva sala
                    </Link>
                    <Link
                        href="/admin/calendar"
                        class="rounded-2xl border border-cyan-400/30 bg-cyan-500/15 px-4 py-2.5 text-sm font-semibold text-cyan-100 transition hover:bg-cyan-500/25"
                    >
                        Ver calendario
                    </Link>
                    <Link
                        href="/admin/blocked-slots"
                        class="rounded-2xl border border-white/10 bg-white/5 px-4 py-2.5 text-sm font-semibold text-slate-200 transition hover:bg-white/10"
                    >
                        Gestionar bloqueos
                    </Link>
                </div>

                <div class="grid grid-cols-1 gap-8 lg:grid-cols-2">

                    <!-- Upcoming today -->
                    <div class="rounded-3xl border border-white/10 bg-slate-900/85 p-6 shadow-2xl shadow-violet-950/20 ring-1 ring-white/5">
                        <h3 class="mb-4 text-lg font-bold text-white">Reservas de hoy</h3>
                        <div v-if="upcomingToday.length === 0" class="py-6 text-center text-sm text-slate-400">
                            No hay reservas programadas para hoy.
                        </div>
                        <div v-else class="space-y-3">
                            <div
                                v-for="r in upcomingToday"
                                :key="r.slug"
                                class="flex items-center justify-between rounded-2xl border border-white/10 px-3 py-3 text-sm transition hover:bg-white/5"
                            >
                                <div>
                                    <div class="font-semibold text-slate-50">{{ r.user_name }}</div>
                                    <div class="text-slate-400">{{ r.space_name }}</div>
                                </div>
                                <div class="text-right">
                                    <div class="font-mono text-slate-200">{{ formatTime(r.start_time) }}</div>
                                    <span
                                        class="rounded-full px-2 py-0.5 text-xs font-semibold"
                                        :class="statusClasses[r.status]"
                                    >{{ statusLabels[r.status] }}</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Recent reservations -->
                    <div class="rounded-3xl border border-white/10 bg-slate-900/85 p-6 shadow-2xl shadow-violet-950/20 ring-1 ring-white/5">
                        <h3 class="mb-4 text-lg font-bold text-white">Últimas solicitudes</h3>
                        <div class="space-y-3">
                            <div
                                v-for="r in recentReservations"
                                :key="r.slug"
                                class="flex items-center justify-between text-sm"
                            >
                                <div class="flex-1 min-w-0">
                                    <div class="truncate font-medium text-slate-50">{{ r.user_name }}</div>
                                    <div class="truncate text-xs text-slate-400">{{ r.space_name }} · {{ formatDateTime(r.start_time) }}</div>
                                </div>
                                <div class="flex items-center gap-2 ml-2 flex-shrink-0">
                                    <span
                                        class="rounded-full px-2 py-0.5 text-xs font-semibold"
                                        :class="statusClasses[r.status]"
                                    >{{ statusLabels[r.status] }}</span>
                                    <Link
                                        :href="'/admin/reservations/' + r.slug"
                                        class="text-xs text-violet-200 hover:text-white hover:underline"
                                    >Ver</Link>
                                </div>
                            </div>
                        </div>
                        <Link
                            href="/admin/reservations"
                            class="mt-4 block text-center text-xs text-violet-200 hover:text-white hover:underline"
                        >
                            Ver todas las reservas →
                        </Link>
                    </div>
                </div>

                <div class="rounded-3xl border border-white/10 bg-slate-900/85 p-6 shadow-2xl shadow-violet-950/20 ring-1 ring-white/5">
                    <div class="flex items-center justify-between gap-4">
                        <div>
                            <p class="text-xs font-semibold uppercase tracking-[0.3em] text-violet-300">Ranking</p>
                            <h3 class="mt-2 text-xl font-bold text-white">Salas más reservadas</h3>
                        </div>
                        <div class="rounded-2xl border border-white/10 bg-white/5 px-4 py-2 text-sm text-slate-300">
                            Top por reservas confirmadas
                        </div>
                    </div>

                    <div class="mt-5 grid gap-4 md:grid-cols-2 xl:grid-cols-3">
                        <article v-for="space in topSpaces" :key="space.id" class="overflow-hidden rounded-2xl border border-white/10 bg-slate-950/50">
                            <div class="h-28 bg-cover bg-center" :style="space.image ? { backgroundImage: `url(${space.image})` } : undefined"></div>
                            <div class="p-4">
                                <div class="flex items-start justify-between gap-3">
                                    <div>
                                        <h4 class="font-semibold text-white">{{ space.name }}</h4>
                                        <p class="text-xs text-slate-400">{{ space.location || 'Sin ubicación' }}</p>
                                    </div>
                                    <span class="rounded-full bg-violet-500/15 px-2 py-1 text-xs font-semibold text-violet-200">{{ space.confirmed_reservations_count }}</span>
                                </div>
                                <div class="mt-3 flex items-center justify-between text-xs text-slate-400">
                                    <span>{{ space.capacity }} personas</span>
                                    <span>${{ Number(space.price_per_hour || 0).toLocaleString('es-CO') }}/h</span>
                                </div>
                            </div>
                        </article>
                    </div>
                </div>

                <div class="rounded-3xl border border-violet-400/20 bg-gradient-to-r from-violet-500/10 via-fuchsia-500/10 to-cyan-500/10 p-6 shadow-2xl shadow-violet-950/20 ring-1 ring-white/5">
                    <div class="flex flex-wrap items-start justify-between gap-4">
                        <div>
                            <p class="text-xs font-semibold uppercase tracking-[0.3em] text-violet-200">Sugerencia</p>
                            <h3 class="mt-2 text-xl font-bold text-white">Predicción de demanda por franjas</h3>
                            <p class="mt-2 max-w-3xl text-sm text-slate-300">Podrías añadir una vista que marque franjas con alta probabilidad de ocupación según histórico, para sugerir horarios alternos antes de que el usuario reserve.</p>
                        </div>
                        <div class="rounded-2xl border border-white/10 bg-slate-950/60 px-4 py-3 text-sm text-slate-200">
                            Sugerencia: ranking de horarios + alerta de sobrecarga
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>