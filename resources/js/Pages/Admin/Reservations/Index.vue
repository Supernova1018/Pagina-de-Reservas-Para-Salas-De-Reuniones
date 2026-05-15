<script setup>
import { ref } from 'vue';
import { Head, Link, router } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';

const props = defineProps({
    reservations: Object,
    spaces: Array,
    filters: Object,
    message: String,
});

const statusFilter = ref(props.filters?.status || '');
const spaceFilter = ref(props.filters?.space || '');
const dateFilter = ref(props.filters?.date || '');

const statusClasses = {
    confirmed: 'bg-emerald-700/10 text-emerald-200',
    rejected: 'bg-red-700/10 text-red-200',
    cancelled: 'bg-slate-700/10 text-slate-200',
    completed: 'bg-blue-700/10 text-blue-200',
};

const statusLabels = {
    confirmed: 'Confirmada',
    rejected: 'Rechazada',
    cancelled: 'Cancelada',
    completed: 'Finalizada',
};

const tabs = [
    { label: 'Salas', href: route('admin.spaces.index'), active: false },
    { label: 'Crear sala', href: route('admin.spaces.create'), active: false },
    { label: 'Reservas', href: route('admin.reservations.index'), active: true },
    { label: 'Nueva reserva', href: route('public.reservations.create'), active: false },
];

function applyFilters() {
    router.get(route('admin.reservations.index'), {
        status: statusFilter.value || undefined,
        space: spaceFilter.value || undefined,
        date: dateFilter.value || undefined,
    }, {
        preserveState: true,
        preserveScroll: true,
        replace: true,
    });
}

function clearFilters() {
    statusFilter.value = '';
    spaceFilter.value = '';
    dateFilter.value = '';
    router.get(route('admin.reservations.index'));
}

function formatDateTime(iso) {
    const date = new Date(iso);
    return date.toLocaleDateString('es-CO', { day: '2-digit', month: 'short' }) + ' ' + date.toLocaleTimeString('es-CO', { hour: '2-digit', minute: '2-digit' });
}

function formatMoney(value) {
    return '$' + Number(value || 0).toLocaleString('es-CO');
}
</script>

<template>
    <Head title="Reservas" />

    <AppLayout title="Reservas">
        <template #header>
            <div class="space-y-4">
                <div class="flex items-center justify-between gap-3">
                    <h2 class="text-xl font-semibold leading-tight text-white">Reservas</h2>
                    <Link :href="route('public.reservations.create')" class="rounded-xl border border-violet-400/30 bg-violet-500/10 px-4 py-2 text-sm font-semibold text-violet-100 transition hover:bg-violet-500/20">
                        Nueva reserva
                    </Link>
                </div>

                <div class="flex flex-wrap gap-2">
                    <Link v-for="tab in tabs" :key="tab.label" :href="tab.href" class="rounded-full border px-4 py-2 text-sm font-semibold transition" :class="tab.active ? 'border-blue-600 bg-blue-600 text-white' : 'border-slate-200 bg-white text-slate-600 hover:border-blue-300 hover:text-blue-700'">
                        {{ tab.label }}
                    </Link>
                </div>
            </div>
        </template>

        <div class="py-8">
            <div class="mx-auto max-w-7xl space-y-6 px-4 sm:px-6 lg:px-8">
                <div v-if="message" class="rounded-2xl border border-emerald-400/20 bg-emerald-500/10 px-4 py-3 text-sm text-emerald-100">
                    {{ message }}
                </div>

                <div class="rounded-2xl border border-white/10 bg-slate-900/85 p-4 shadow-sm">
                    <div class="grid grid-cols-1 gap-4 lg:grid-cols-4">
                        <div>
                            <label class="mb-1 block text-xs font-semibold uppercase tracking-wide text-slate-400">Estado</label>
                            <select v-model="statusFilter" class="w-full rounded-xl border border-white/10 bg-slate-800/70 px-3 py-2.5 text-sm text-slate-100 focus:border-violet-500 focus:ring-2 focus:ring-violet-500/20">
                                <option value="">Todos</option>
                                <option value="confirmed">Confirmada</option>
                                <option value="rejected">Rechazada</option>
                                <option value="cancelled">Cancelada</option>
                                <option value="completed">Finalizada</option>
                            </select>
                        </div>
                        <div>
                            <label class="mb-1 block text-xs font-semibold uppercase tracking-wide text-slate-400">Sala</label>
                            <select v-model="spaceFilter" class="w-full rounded-xl border border-white/10 bg-slate-800/70 px-3 py-2.5 text-sm text-slate-100 focus:border-violet-500 focus:ring-2 focus:ring-violet-500/20">
                                <option value="">Todas</option>
                                <option v-for="space in spaces" :key="space.slug" :value="space.slug">{{ space.name }}</option>
                            </select>
                        </div>
                        <div>
                            <label class="mb-1 block text-xs font-semibold uppercase tracking-wide text-slate-400">Fecha</label>
                            <input v-model="dateFilter" type="date" class="w-full rounded-xl border border-white/10 bg-slate-800/70 px-3 py-2.5 text-sm text-slate-100 focus:border-violet-500 focus:ring-2 focus:ring-violet-500/20" />
                        </div>
                        <div class="flex items-end gap-3">
                            <button @click="applyFilters" class="flex-1 rounded-xl bg-violet-600 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-violet-700">
                                Filtrar
                            </button>
                            <button v-if="statusFilter || spaceFilter || dateFilter" @click="clearFilters" class="rounded-xl border border-white/10 px-4 py-2.5 text-sm font-semibold text-slate-200 transition hover:border-violet-400/30 hover:text-white">
                                Limpiar
                            </button>
                        </div>
                    </div>
                </div>

                <div class="overflow-hidden rounded-2xl border border-white/10 bg-slate-900/85 shadow-sm">
                    <table class="min-w-full text-sm">
                        <thead class="bg-slate-800 text-left text-xs uppercase tracking-wide text-slate-400">
                            <tr>
                                <th class="px-5 py-3">Solicitante</th>
                                <th class="px-5 py-3">Sala</th>
                                <th class="px-5 py-3">Horario</th>
                                <th class="px-5 py-3">Estado</th>
                                <th class="px-5 py-3">Total</th>
                                <th class="px-5 py-3 text-right">Acciones</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-white/5">
                            <tr v-for="reservation in reservations.data" :key="reservation.slug" class="hover:bg-white/5">
                                <td class="px-5 py-4">
                                    <div class="font-semibold text-white">{{ reservation.user_name }}</div>
                                    <div class="text-xs text-slate-400">{{ reservation.user_email }}</div>
                                    <div v-if="reservation.organization" class="text-xs text-slate-500">{{ reservation.organization }}</div>
                                </td>
                                <td class="px-5 py-4">
                                    <div class="font-semibold text-slate-100">{{ reservation.space?.name }}</div>
                                    <div class="text-xs text-slate-500">{{ reservation.slug }}</div>
                                </td>
                                <td class="px-5 py-4 text-slate-300">
                                    <div class="font-medium">{{ formatDateTime(reservation.start_time) }}</div>
                                    <div class="text-xs text-slate-500">→ {{ formatDateTime(reservation.end_time) }}</div>
                                </td>
                                <td class="px-5 py-4">
                                    <span class="rounded-full px-3 py-1 text-xs font-semibold" :class="statusClasses[reservation.status] || 'bg-slate-700/10 text-slate-200'">
                                        {{ statusLabels[reservation.status] || reservation.status_label }}
                                    </span>
                                </td>
                                <td class="px-5 py-4 font-semibold text-slate-200">{{ formatMoney(reservation.total_price) }}</td>
                                <td class="px-5 py-4">
                                    <div class="flex flex-wrap justify-end gap-2">
                                        <Link :href="route('admin.reservations.show', reservation.slug)" class="rounded-lg border border-violet-400/30 px-3 py-1.5 text-xs font-semibold text-violet-200 transition hover:border-violet-400/60 hover:bg-violet-500/10">
                                            Ver
                                        </Link>
                                        <button v-if="reservation.status === 'confirmed'" @click="router.post(route('admin.reservations.cancel', reservation.slug))" class="rounded-lg border border-white/10 px-3 py-1.5 text-xs font-semibold text-slate-200 transition hover:border-white/20">
                                            Cancelar
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            <tr v-if="reservations.data.length === 0">
                                <td colspan="6" class="px-5 py-16 text-center text-slate-400">No hay reservas registradas con esos filtros.</td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div v-if="reservations.links && reservations.links.length > 3" class="flex flex-wrap gap-2">
                    <Link
                        v-for="link in reservations.links"
                        :key="link.label"
                        :href="link.url || ''"
                        v-html="link.label"
                        class="rounded-lg border px-3 py-2 text-sm transition"
                        :class="link.active ? 'border-blue-600 bg-blue-600 text-white' : 'border-slate-200 bg-white text-slate-600 hover:border-blue-400 hover:text-blue-700'"
                        :disabled="!link.url"
                    />
                </div>
            </div>
        </div>
    </AppLayout>
</template>
