<script setup>
import { ref } from 'vue';
import { Head, Link } from '@inertiajs/vue3';

const props = defineProps({
    space:     Object,
    nextSlots: Array,
});

const dayNames = ['Dom', 'Lun', 'Mar', 'Mié', 'Jue', 'Vie', 'Sáb'];

function formatPrice(price) {
    if (!price || parseFloat(price) === 0) return 'Gratuita';
    return '$' + Number(price).toLocaleString('es-CO') + '/hora';
}

function formatDate(iso) {
    const d = new Date(iso);
    return d.toLocaleDateString('es-CO', { weekday: 'short', day: 'numeric', month: 'short' });
}

function formatTime(iso) {
    const d = new Date(iso);
    return d.toLocaleTimeString('es-CO', { hour: '2-digit', minute: '2-digit' });
}

function reserveUrl(slot) {
    return `/reservations/new?space=${props.space.slug}&start=${encodeURIComponent(slot.start)}`;
}

const availDays = props.space.availabilities
    ? [...props.space.availabilities].sort((a, b) => a.day_of_week - b.day_of_week)
    : [];
</script>

<template>
    <Head :title="space.name" />

    <div class="min-h-screen bg-slate-900 text-slate-200">

        <!-- Nav bar -->
        <nav class="border-b border-violet-700/20 bg-slate-900/40 px-4 py-3 backdrop-blur">
            <div class="max-w-6xl mx-auto flex items-center gap-3">
                <Link href="/" class="text-sm text-slate-300 hover:text-white">← Todas las salas</Link>
                <span class="text-slate-500">/</span>
                <span class="text-sm font-medium text-slate-100">{{ space.name }}</span>
            </div>
        </nav>

        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-8 grid grid-cols-1 lg:grid-cols-3 gap-8">

            <!-- Left: Space detail -->
            <div class="lg:col-span-2 space-y-6">

                <!-- Header card -->
                <div class="overflow-hidden rounded-3xl border border-white/10 bg-slate-900/85 shadow-2xl shadow-violet-950/20 ring-1 ring-white/5">
                    <div class="h-64 bg-cover bg-center" :style="space.image ? { backgroundImage: `url(${space.image})` } : undefined"></div>
                    <div
                        class="h-4"
                        :class="space.type === 'university' ? 'bg-gradient-to-r from-violet-500 to-fuchsia-500' : 'bg-gradient-to-r from-emerald-500 to-teal-500'"
                    ></div>
                    <div class="p-6">
                        <div class="flex items-start justify-between flex-wrap gap-3">
                            <div>
                                <span
                                    class="inline-block text-xs font-bold px-2 py-0.5 rounded-full mb-2"
                                    :class="space.type === 'university' ? 'bg-violet-500/15 text-violet-200' : 'bg-emerald-500/15 text-emerald-200'"
                                >
                                    {{ space.type === 'university' ? 'Universitaria' : 'Corporativa' }}
                                </span>
                                <h1 class="text-2xl font-bold text-white">{{ space.name }}</h1>
                                <p v-if="space.location" class="mt-1 text-sm text-slate-400">{{ space.location }}</p>
                            </div>
                            <div class="text-right">
                                <div class="text-2xl font-bold text-violet-200">{{ formatPrice(space.price_per_hour) }}</div>
                                <div class="text-xs text-slate-400">por hora</div>
                            </div>
                        </div>

                        <div class="mt-4 flex gap-6 border-t border-white/10 pt-4 text-sm text-slate-300">
                            <span>Capacidad: <strong>{{ space.capacity }} personas</strong></span>
                        </div>
                    </div>
                </div>

                <!-- Description -->
                <div class="rounded-3xl border border-white/10 bg-slate-900/85 p-6 shadow-sm">
                    <h2 class="mb-3 text-lg font-bold text-white">Descripción</h2>
                    <p class="whitespace-pre-line leading-relaxed text-slate-300">{{ space.description }}</p>
                </div>

                <!-- Rules -->
                <div v-if="space.rules" class="rounded-3xl border border-amber-400/20 bg-amber-500/10 p-6">
                    <h2 class="mb-3 text-lg font-bold text-amber-100">Reglas de uso</h2>
                    <p class="whitespace-pre-line text-sm leading-relaxed text-amber-100/90">{{ space.rules }}</p>
                </div>

                <!-- Weekly availability -->
                <div class="rounded-3xl border border-white/10 bg-slate-900/85 p-6 shadow-sm">
                    <h2 class="mb-4 text-lg font-bold text-white">Horario semanal</h2>
                    <div v-if="availDays.length" class="grid grid-cols-2 sm:grid-cols-3 gap-3">
                        <div
                            v-for="avail in availDays"
                            :key="avail.id"
                            class="rounded-lg border border-white/10 bg-slate-800/60 p-3"
                        >
                            <div class="text-sm font-semibold text-slate-200">{{ dayNames[avail.day_of_week] }}</div>
                            <div class="mt-1 font-mono text-sm text-violet-200">
                                {{ avail.start_time }} – {{ avail.end_time }}
                            </div>
                        </div>
                    </div>
                    <p v-else class="text-sm text-slate-400">Sin disponibilidad configurada.</p>
                </div>
            </div>

            <!-- Right: Next available slots -->
            <aside class="space-y-4">
                <div class="sticky top-6 rounded-3xl border border-white/10 bg-slate-900/85 p-5 shadow-sm">
                    <h2 class="mb-4 text-lg font-bold text-white">Próximos horarios</h2>

                    <div v-if="nextSlots.length === 0" class="py-8 text-center text-sm text-slate-400">
                        Sin horarios disponibles en los próximos 30 días.
                    </div>

                    <div v-else class="space-y-2 max-h-[65vh] overflow-y-auto pr-1">
                        <div
                            v-for="slot in nextSlots"
                            :key="slot.start"
                            class="group rounded-lg border border-white/10 p-3 transition hover:border-violet-400/30 hover:bg-violet-500/10"
                        >
                            <div class="mb-1 text-xs font-medium text-slate-400">{{ formatDate(slot.start) }}</div>
                            <div class="text-sm font-semibold text-slate-100">
                                {{ formatTime(slot.start) }} – {{ formatTime(slot.end) }}
                            </div>
                            <Link
                                :href="reserveUrl(slot)"
                                class="mt-2 block w-full rounded bg-violet-600 px-3 py-1.5 text-center text-xs font-bold text-white transition hover:bg-violet-700"
                            >
                                Reservar este horario
                            </Link>
                        </div>
                    </div>

                    <Link
                        :href="`/reservations/new?space=${space.slug}`"
                        class="mt-4 block w-full rounded-lg bg-slate-800 px-4 py-2.5 text-center text-sm font-semibold text-white transition hover:bg-violet-700"
                    >
                        Reserva personalizada
                    </Link>
                </div>
            </aside>
        </div>
    </div>
</template>