<script setup>
import { computed } from 'vue';
import { Head, Link } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';

const props = defineProps({
    stats:              Object,
    recentReservations: Array,
    upcomingToday:      Array,
    topSpaces:          Array,
    topUsers:           Array,
    registeredUsers:    Array,
    timeSlots:          Array,
});

function formatTime(iso) {
    return new Date(iso).toLocaleTimeString('es-CO', { hour: '2-digit', minute: '2-digit' });
}

function formatDateTime(iso) {
    const d = new Date(iso);
    return d.toLocaleDateString('es-CO', { day: '2-digit', month: 'short' }) + ' ' +
           d.toLocaleTimeString('es-CO', { hour: '2-digit', minute: '2-digit' });
}

function formatDate(iso) {
    const d = new Date(iso);
    return d.toLocaleDateString('es-CO', { day: '2-digit', month: 'short', year: 'numeric' });
}

function formatMoney(value) {
    return '$' + Number(value || 0).toLocaleString('es-CO');
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

const kpiCards = computed(() => [
    { title: 'Salas activas', value: props.stats.active_spaces, hint: 'Disponibles para reservar ahora', tone: 'from-violet-500/20 to-fuchsia-500/20 text-violet-100' },
    { title: 'Usuarios activos', value: props.stats.active_users, hint: `${props.stats.total_users} cuentas registradas`, tone: 'from-cyan-500/20 to-sky-500/20 text-cyan-100' },
    { title: 'Confirmadas', value: props.stats.confirmed_count, hint: 'Reservas históricas confirmadas', tone: 'from-emerald-500/20 to-teal-500/20 text-emerald-100' },
    { title: 'Ingresos totales', value: formatMoney(props.stats.revenue_total), hint: 'Confirmadas y finalizadas', tone: 'from-amber-500/20 to-orange-500/20 text-amber-100' },
    { title: 'Ticket promedio', value: formatMoney(props.stats.average_ticket), hint: 'Promedio por reserva', tone: 'from-fuchsia-500/20 to-pink-500/20 text-fuchsia-100' },
    { title: 'Canceladas', value: props.stats.cancelled_count, hint: 'Solicitudes anuladas', tone: 'from-rose-500/20 to-red-500/20 text-rose-100' },
]);

const topSpacePeak = computed(() => Math.max(1, ...props.topSpaces.map((space) => Number(space.confirmed_reservations_count || 0))));
const topUserPeak = computed(() => Math.max(1, ...props.topUsers.map((user) => Number(user.confirmed_reservations_count || 0))));
</script>

<template>
    <Head title="Dashboard" />
    <AppLayout title="Dashboard">
        <template #header>
            <div class="flex flex-wrap items-end justify-between gap-4">
                <div>
                    <p class="text-xs font-semibold uppercase tracking-[0.3em] text-violet-300">Panel de gestión</p>
                    <h2 class="mt-2 text-3xl font-bold text-slate-50">Disponibilidad, demanda y reservas</h2>
                    <p class="mt-2 max-w-2xl text-sm text-slate-400">Resumen oscuro con métricas reales, pronóstico por franjas y rankings ampliados para salas y usuarios.</p>
                    <div class="mt-4 flex flex-wrap gap-2 text-xs font-semibold">
                        <span class="rounded-full border border-violet-400/20 bg-violet-500/10 px-3 py-1 text-violet-100">Pico: {{ stats.peak_slot_label }}</span>
                        <span class="rounded-full border border-cyan-400/20 bg-cyan-500/10 px-3 py-1 text-cyan-100">Usuarios activos: {{ stats.active_users }}</span>
                        <span class="rounded-full border border-emerald-400/20 bg-emerald-500/10 px-3 py-1 text-emerald-100">Valoraciones: {{ stats.ratings_count }}</span>
                    </div>
                </div>

                <Link href="/admin/calendar" class="rounded-2xl border border-violet-400/30 bg-violet-500/10 px-4 py-2.5 text-sm font-semibold text-violet-100 transition hover:bg-violet-500/20">
                    Ver calendario de disponibilidad
                </Link>
            </div>
        </template>

        <div class="px-4 py-8 sm:px-6 lg:px-8">
            <div class="mx-auto max-w-7xl space-y-8">

                <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-3">
                    <div v-for="card in kpiCards" :key="card.title" class="rounded-3xl border border-white/10 bg-slate-900/80 p-5 shadow-2xl shadow-violet-950/20 ring-1 ring-white/5">
                        <div class="inline-flex rounded-full bg-gradient-to-r px-3 py-1 text-xs font-semibold" :class="card.tone">{{ card.title }}</div>
                        <div class="mt-4 text-3xl font-bold text-white">{{ card.value }}</div>
                        <div class="mt-2 text-sm text-slate-400">{{ card.hint }}</div>
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
                    <section class="rounded-3xl border border-white/10 bg-slate-900/85 p-6 shadow-2xl shadow-violet-950/20 ring-1 ring-white/5">
                        <div class="flex flex-wrap items-start justify-between gap-4">
                            <div>
                                <p class="text-xs font-semibold uppercase tracking-[0.3em] text-violet-300">Demanda histórica</p>
                                <h3 class="mt-2 text-lg font-bold text-white">Franjas con mayor ocupación</h3>
                                <p class="mt-2 max-w-xl text-sm text-slate-400">Basado en reservas confirmadas y finalizadas de los últimos 90 días.</p>
                            </div>
                            <div class="rounded-2xl border border-white/10 bg-white/5 px-4 py-3 text-right text-sm text-slate-300">
                                <div class="text-xs uppercase tracking-wide text-slate-400">Pico</div>
                                <div class="mt-1 font-semibold text-slate-100">{{ stats.peak_slot_label }}</div>
                            </div>
                        </div>

                        <div v-if="timeSlots.length === 0" class="mt-6 rounded-2xl border border-dashed border-white/10 bg-white/5 p-6 text-center text-sm text-slate-400">
                            Todavía no hay suficientes reservas para generar un pronóstico.
                        </div>

                        <div v-else class="mt-6 space-y-4">
                            <div v-for="slot in timeSlots" :key="slot.label" class="rounded-2xl border border-white/10 bg-white/5 p-4">
                                <div class="flex items-center justify-between gap-3 text-sm">
                                    <div class="font-semibold text-white">{{ slot.label }}</div>
                                    <div class="text-slate-400">{{ slot.count }} reservas · {{ slot.level }}</div>
                                </div>
                                <div class="mt-2 h-2 overflow-hidden rounded-full bg-white/5">
                                    <div class="h-full rounded-full bg-gradient-to-r from-violet-500 via-fuchsia-500 to-cyan-400 transition-all" :style="{ width: `${slot.fill}%` }"></div>
                                </div>
                                <div class="mt-2 text-xs text-slate-400">{{ slot.hint }}</div>
                            </div>
                        </div>
                    </section>

                    <section class="rounded-3xl border border-white/10 bg-slate-900/85 p-6 shadow-2xl shadow-violet-950/20 ring-1 ring-white/5">
                        <div class="flex flex-wrap items-start justify-between gap-4">
                            <div>
                                <p class="text-xs font-semibold uppercase tracking-[0.3em] text-cyan-300">Usuarios</p>
                                <h3 class="mt-2 text-lg font-bold text-white">Usuarios que más reservan</h3>
                                <p class="mt-2 max-w-xl text-sm text-slate-400">Ranking ampliado con gasto confirmado y nivel de actividad.</p>
                            </div>
                            <div class="rounded-2xl border border-white/10 bg-white/5 px-4 py-3 text-right text-sm text-slate-300">
                                <div class="text-xs uppercase tracking-wide text-slate-400">Promedio</div>
                                <div class="mt-1 font-semibold text-slate-100">{{ stats.reservations_per_active_user }} reservas</div>
                            </div>
                        </div>

                        <div v-if="topUsers.length === 0" class="mt-6 rounded-2xl border border-dashed border-white/10 bg-white/5 p-6 text-center text-sm text-slate-400">
                            Todavía no hay usuarios con reservas confirmadas.
                        </div>

                        <div v-else class="mt-6 space-y-4">
                            <article v-for="user in topUsers" :key="user.id" class="rounded-2xl border border-white/10 bg-white/5 p-4">
                                <div class="flex items-start justify-between gap-3">
                                    <div>
                                        <h4 class="font-semibold text-white">{{ user.name }}</h4>
                                        <p class="text-xs text-slate-400">{{ user.email }}</p>
                                    </div>
                                    <span class="rounded-full bg-cyan-500/15 px-2 py-1 text-xs font-semibold text-cyan-200">{{ user.confirmed_reservations_count }} reservas</span>
                                </div>
                                <div class="mt-3 h-2 overflow-hidden rounded-full bg-white/5">
                                    <div class="h-full rounded-full bg-gradient-to-r from-cyan-500 to-violet-500 transition-all" :style="{ width: `${(Number(user.confirmed_reservations_count || 0) / topUserPeak) * 100}%` }"></div>
                                </div>
                                <div class="mt-2 flex items-center justify-between text-xs text-slate-400">
                                    <span>Gasto confirmado: {{ formatMoney(user.confirmed_spend) }}</span>
                                    <span>Actividad alta</span>
                                </div>
                            </article>
                        </div>
                    </section>
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

                <section class="rounded-3xl border border-white/10 bg-slate-900/85 p-6 shadow-2xl shadow-violet-950/20 ring-1 ring-white/5">
                    <div class="flex flex-wrap items-start justify-between gap-4">
                        <div>
                            <p class="text-xs font-semibold uppercase tracking-[0.3em] text-cyan-300">Usuarios registrados</p>
                            <h3 class="mt-2 text-xl font-bold text-white">Información detallada de usuarios</h3>
                            <p class="mt-2 max-w-2xl text-sm text-slate-400">Consulta correo, rol, verificación, actividad y última reserva de los usuarios más recientes.</p>
                        </div>
                        <div class="flex flex-col items-end gap-3">
                            <div class="rounded-2xl border border-white/10 bg-white/5 px-4 py-3 text-right text-sm text-slate-300">
                                <div class="text-xs uppercase tracking-wide text-slate-400">Total registrados</div>
                                <div class="mt-1 font-semibold text-slate-100">{{ stats.total_users }}</div>
                            </div>
                            <Link href="/admin/users" class="rounded-2xl border border-cyan-400/30 bg-cyan-500/10 px-4 py-2 text-sm font-semibold text-cyan-100 transition hover:bg-cyan-500/20">
                                Gestionar usuarios
                            </Link>
                        </div>
                    </div>

                    <div v-if="registeredUsers.length === 0" class="mt-6 rounded-2xl border border-dashed border-white/10 bg-white/5 p-6 text-center text-sm text-slate-400">
                        Todavía no hay usuarios registrados para mostrar.
                    </div>

                    <div v-else class="mt-6 overflow-x-auto">
                        <table class="min-w-full border-separate border-spacing-y-3 text-left">
                            <thead>
                                <tr class="text-xs uppercase tracking-[0.25em] text-slate-400">
                                    <th class="px-4 pb-2 font-semibold">Usuario</th>
                                    <th class="px-4 pb-2 font-semibold">Contacto</th>
                                    <th class="px-4 pb-2 font-semibold">Rol</th>
                                    <th class="px-4 pb-2 font-semibold">Actividad</th>
                                    <th class="px-4 pb-2 font-semibold">Última reserva</th>
                                    <th class="px-4 pb-2 font-semibold">Ingresos</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr
                                    v-for="user in registeredUsers"
                                    :key="user.id"
                                    class="rounded-2xl bg-white/5 align-top transition hover:bg-white/10"
                                >
                                    <td class="px-4 py-4">
                                        <div class="flex items-center gap-3">
                                            <img
                                                :src="user.profile_photo_url"
                                                :alt="user.name"
                                                class="h-11 w-11 rounded-2xl border border-white/10 object-cover"
                                            >
                                            <div>
                                                <div class="font-semibold text-white">{{ user.name }}</div>
                                                <div class="text-xs text-slate-400">Registrado el {{ formatDate(user.created_at) }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-4 py-4 text-sm text-slate-300">
                                        <div>{{ user.email }}</div>
                                        <div class="mt-1 text-xs" :class="user.email_verified_at ? 'text-emerald-300' : 'text-amber-300'">
                                            {{ user.email_verified_at ? 'Correo verificado' : 'Pendiente de verificación' }}
                                        </div>
                                    </td>
                                    <td class="px-4 py-4">
                                        <span
                                            class="rounded-full px-3 py-1 text-xs font-semibold"
                                            :class="user.is_blocked ? 'bg-rose-500/15 text-rose-200' : user.is_suspended ? 'bg-amber-500/15 text-amber-200' : user.is_admin ? 'bg-violet-500/15 text-violet-200' : 'bg-white/10 text-slate-200'"
                                        >
                                            {{ user.is_blocked ? 'Bloqueado' : user.is_suspended ? 'Suspendido' : user.is_admin ? 'Administrador' : 'Usuario' }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-4 text-sm text-slate-300">
                                        <div>{{ user.reservations_count }} reservas totales</div>
                                        <div class="mt-1 text-xs text-slate-400">{{ user.confirmed_reservations_count }} confirmadas</div>
                                    </td>
                                    <td class="px-4 py-4 text-sm text-slate-300">
                                        {{ user.last_reservation_at ? formatDateTime(user.last_reservation_at) : 'Sin reservas' }}
                                    </td>
                                    <td class="px-4 py-4 text-sm text-slate-300">
                                        {{ formatMoney(user.confirmed_spend) }}
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </section>

                <div class="rounded-3xl border border-white/10 bg-slate-900/85 p-6 shadow-2xl shadow-violet-950/20 ring-1 ring-white/5">
                    <div class="flex items-center justify-between gap-4">
                        <div>
                            <p class="text-xs font-semibold uppercase tracking-[0.3em] text-violet-300">Ranking</p>
                            <h3 class="mt-2 text-xl font-bold text-white">Salas más reservadas</h3>
                        </div>
                        <div class="rounded-2xl border border-white/10 bg-white/5 px-4 py-2 text-sm text-slate-300">
                            Incluye reservas y gasto confirmado
                        </div>
                    </div>

                    <div v-if="topSpaces.length === 0" class="mt-6 rounded-2xl border border-dashed border-white/10 bg-white/5 p-6 text-center text-sm text-slate-400">
                        Todavía no hay salas con reservas confirmadas.
                    </div>

                    <div v-else class="mt-5 grid gap-4 md:grid-cols-2 xl:grid-cols-3">
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

                                <div class="mt-3 h-2 overflow-hidden rounded-full bg-white/5">
                                    <div class="h-full rounded-full bg-gradient-to-r from-violet-500 to-cyan-400 transition-all" :style="{ width: `${(Number(space.confirmed_reservations_count || 0) / topSpacePeak) * 100}%` }"></div>
                                </div>

                                <div class="mt-3 flex items-center justify-between text-xs text-slate-400">
                                    <span>{{ space.capacity }} personas</span>
                                    <span>{{ formatMoney(space.confirmed_revenue) }}</span>
                                </div>
                                <div class="mt-1 text-xs text-slate-500">{{ formatMoney(space.price_per_hour) }}/h</div>
                            </div>
                        </article>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>