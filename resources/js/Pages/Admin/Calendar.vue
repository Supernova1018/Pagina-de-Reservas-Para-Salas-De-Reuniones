<script setup>
import { computed } from 'vue';
import { Head, Link } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';

const props = defineProps({
    spaces: Array,
    selectedSpace: String,
    weekStart: String,
    weekOffset: Number,
    calendar: Array,
});

const selectedSpaceData = computed(() => props.spaces.find((space) => space.slug === props.selectedSpace));

function toQuery(spaceSlug, weekOffset) {
    const params = new URLSearchParams();
    if (spaceSlug) params.set('space', spaceSlug);
    params.set('week', String(weekOffset));
    return `/admin/calendar?${params.toString()}`;
}

function formatSlotRange(slot) {
    const start = new Date(slot.start);
    const end = new Date(slot.end);
    return `${start.toLocaleTimeString('es-CO', { hour: '2-digit', minute: '2-digit' })} - ${end.toLocaleTimeString('es-CO', { hour: '2-digit', minute: '2-digit' })}`;
}

const slotStyles = {
    available: 'border-emerald-400/30 bg-emerald-500/10 text-emerald-100',
    
    confirmed: 'border-violet-400/30 bg-violet-500/15 text-violet-100',
    rejected: 'border-red-400/30 bg-red-500/10 text-red-100',
    cancelled: 'border-slate-600 bg-slate-800 text-slate-300',
    blocked: 'border-fuchsia-400/30 bg-fuchsia-500/15 text-fuchsia-100',
};
</script>

<template>
    <Head title="Disponibilidad" />

    <AppLayout title="Disponibilidad de salas">
        <template #header>
            <div class="space-y-4">
                <div class="flex flex-wrap items-start justify-between gap-4">
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-[0.3em] text-violet-300">Dashboard de disponibilidad</p>
                        <h2 class="mt-2 text-2xl font-bold text-slate-50">Calendario semanal de salas</h2>
                        <p class="mt-2 max-w-3xl text-sm text-slate-400">Vista oscura con acentos púrpura para revisar disponibilidad, reservas activas y bloqueos por sala.</p>
                    </div>

                    <div class="rounded-2xl border border-white/10 bg-white/5 px-4 py-3 text-right">
                        <div class="text-xs uppercase tracking-wide text-slate-400">Semana</div>
                        <div class="mt-1 text-sm font-semibold text-slate-100">Desde {{ weekStart }}</div>
                    </div>
                </div>

                <div class="flex flex-wrap gap-2">
                    <Link :href="toQuery(selectedSpace, weekOffset - 1)" class="rounded-full border border-white/10 bg-white/5 px-4 py-2 text-sm font-semibold text-slate-200 transition hover:border-violet-400/50 hover:bg-violet-500/10 hover:text-white">
                        ← Semana anterior
                    </Link>
                    <Link :href="toQuery(selectedSpace, 0)" class="rounded-full border border-white/10 bg-white/5 px-4 py-2 text-sm font-semibold text-slate-200 transition hover:border-violet-400/50 hover:bg-violet-500/10 hover:text-white">
                        Hoy
                    </Link>
                    <Link :href="toQuery(selectedSpace, weekOffset + 1)" class="rounded-full border border-white/10 bg-white/5 px-4 py-2 text-sm font-semibold text-slate-200 transition hover:border-violet-400/50 hover:bg-violet-500/10 hover:text-white">
                        Semana siguiente →
                    </Link>
                </div>
            </div>
        </template>

        <div class="px-4 py-8 sm:px-6 lg:px-8">
            <div class="mx-auto max-w-7xl space-y-6">
                <div class="grid gap-4 lg:grid-cols-3">
                    <div class="rounded-3xl border border-white/10 bg-slate-900/80 p-5 shadow-2xl shadow-violet-950/20 ring-1 ring-white/5">
                        <div class="text-xs uppercase tracking-[0.25em] text-slate-400">Sala activa</div>
                        <div class="mt-2 text-lg font-bold text-white">{{ selectedSpaceData?.name || 'Sin sala seleccionada' }}</div>
                        <div class="mt-1 text-sm text-slate-400">{{ selectedSpaceData?.slug || 'Selecciona una sala para ver la agenda' }}</div>
                    </div>

                    <div class="rounded-3xl border border-white/10 bg-slate-900/80 p-5 shadow-2xl shadow-violet-950/20 ring-1 ring-white/5">
                        <div class="text-xs uppercase tracking-[0.25em] text-slate-400">Leyenda</div>
                        <div class="mt-3 flex flex-wrap gap-2 text-xs font-semibold">
                            <span class="rounded-full border border-emerald-400/20 bg-emerald-500/10 px-3 py-1 text-emerald-100">Libre</span>
                            <span class="rounded-full border border-violet-400/20 bg-violet-500/15 px-3 py-1 text-violet-100">Confirmada</span>
                            <span class="rounded-full border border-slate-600 bg-slate-800 px-3 py-1 text-slate-300">Cancelada</span>
                            <span class="rounded-full border border-fuchsia-400/20 bg-fuchsia-500/15 px-3 py-1 text-fuchsia-100">Bloqueada</span>
                        </div>
                    </div>

                    <div class="rounded-3xl border border-white/10 bg-slate-900/80 p-5 shadow-2xl shadow-violet-950/20 ring-1 ring-white/5">
                        <div class="text-xs uppercase tracking-[0.25em] text-slate-400">Acción rápida</div>
                        <Link href="/admin/reservations" class="mt-3 inline-flex rounded-xl border border-violet-400/30 bg-violet-500/10 px-4 py-2 text-sm font-semibold text-violet-100 transition hover:bg-violet-500/20">
                            Revisar reservas
                        </Link>
                    </div>
                </div>

                <div class="grid gap-4 lg:grid-cols-3">
                    <div class="lg:col-span-1 rounded-3xl border border-white/10 bg-slate-900/85 p-5 shadow-2xl shadow-violet-950/20 ring-1 ring-white/5">
                        <div class="flex items-center justify-between gap-3">
                            <div>
                                <h3 class="text-lg font-bold text-white">Salas</h3>
                                <p class="mt-1 text-sm text-slate-400">Cambia de sala para revisar otro calendario.</p>
                            </div>
                            <span class="rounded-full bg-violet-500/15 px-3 py-1 text-xs font-semibold text-violet-200">{{ spaces.length }}</span>
                        </div>

                        <div class="mt-4 space-y-2">
                            <Link
                                v-for="space in spaces"
                                :key="space.slug"
                                :href="toQuery(space.slug, weekOffset)"
                                class="block rounded-2xl border px-4 py-3 transition"
                                :class="space.slug === selectedSpace
                                    ? 'border-violet-400/40 bg-violet-500/15 text-white shadow-lg shadow-violet-950/20'
                                    : 'border-white/10 bg-white/5 text-slate-300 hover:border-violet-400/30 hover:bg-violet-500/10 hover:text-white'"
                            >
                                <div class="font-semibold">{{ space.name }}</div>
                                <div class="text-xs text-slate-400">{{ space.slug }}</div>
                            </Link>
                        </div>
                    </div>

                    <div class="lg:col-span-2 rounded-3xl border border-white/10 bg-slate-900/85 p-5 shadow-2xl shadow-violet-950/20 ring-1 ring-white/5">
                        <div class="flex items-center justify-between gap-4">
                            <div>
                                <h3 class="text-lg font-bold text-white">Semana actual</h3>
                                <p class="mt-1 text-sm text-slate-400">Bloques por día con colores según estado.</p>
                            </div>
                            <div class="rounded-full border border-white/10 bg-white/5 px-3 py-1 text-xs font-semibold text-slate-300">
                                Semana {{ weekOffset >= 0 ? '+' : '' }}{{ weekOffset }}
                            </div>
                        </div>

                        <div v-if="calendar.length === 0" class="mt-6 rounded-2xl border border-dashed border-white/10 bg-white/5 p-10 text-center text-slate-400">
                            Selecciona una sala activa con disponibilidad configurada.
                        </div>

                        <div v-else class="mt-6 grid gap-4">
                            <section v-for="day in calendar" :key="day.date" class="rounded-3xl border border-white/10 bg-slate-900/85 p-4">
                                <div class="flex flex-wrap items-center justify-between gap-3 border-b border-white/5 pb-3">
                                    <div>
                                        <div class="text-sm font-semibold uppercase tracking-[0.2em] text-violet-200">{{ day.dayName }}</div>
                                        <div class="text-lg font-bold text-white">{{ day.dayNumber }} / {{ day.date }}</div>
                                    </div>
                                    <span class="rounded-full px-3 py-1 text-xs font-semibold" :class="day.available ? 'bg-emerald-500/15 text-emerald-200' : 'bg-slate-700 text-slate-300'">
                                        {{ day.available ? 'Disponible' : 'Sin configuración' }}
                                    </span>
                                </div>

                                <div class="mt-4 flex flex-wrap gap-2">
                                    <template v-if="day.slots.length">
                                        <div
                                            v-for="slot in day.slots"
                                            :key="slot.start"
                                            class="rounded-2xl border px-3 py-2 text-xs font-semibold transition"
                                            :class="slotStyles[slot.type] || slotStyles.available"
                                        >
                                            <div>{{ formatSlotRange(slot) }}</div>
                                            <div class="mt-1 text-[11px] font-medium opacity-80">{{ slot.type }}</div>
                                        </div>
                                    </template>
                                    <div v-else class="text-sm text-slate-400">Sin bloques configurados para este día.</div>
                                </div>
                            </section>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>