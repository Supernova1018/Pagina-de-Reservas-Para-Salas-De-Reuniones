<script setup>
import { computed } from 'vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';

const props = defineProps({
    space: Object,
});

const isEdit = !!props.space;

const days = [
    { value: 1, label: 'Lunes' },
    { value: 2, label: 'Martes' },
    { value: 3, label: 'Miércoles' },
    { value: 4, label: 'Jueves' },
    { value: 5, label: 'Viernes' },
    { value: 6, label: 'Sábado' },
    { value: 0, label: 'Domingo' },
];

const buildAvailabilities = () => days.map((day) => {
    const existing = props.space?.availabilities?.find((item) => item.day_of_week === day.value);

    return {
        day_of_week: day.value,
        label: day.label,
        enabled: !!existing,
        start_time: existing?.start_time || '08:00',
        end_time: existing?.end_time || '18:00',
    };
});

const form = useForm({
    name: props.space?.name || '',
    slug: props.space?.slug || '',
    type: props.space?.type || 'corporate',
    capacity: props.space?.capacity || '',
    description: props.space?.description || '',
    rules: props.space?.rules || '',
    price_per_hour: props.space?.price_per_hour || '0',
    is_active: props.space?.is_active ?? true,
    location: props.space?.location || '',
    image: props.space?.image || '',
    availabilities: buildAvailabilities(),
});

const activeDaysCount = computed(() => form.availabilities.filter((item) => item.enabled).length);

const totalAvailabilityHours = computed(() => form.availabilities.reduce((total, item) => {
    if (!item.enabled || !item.start_time || !item.end_time) {
        return total;
    }

    const start = new Date(`2000-01-01T${item.start_time}:00`);
    const end = new Date(`2000-01-01T${item.end_time}:00`);

    return total + Math.max((end - start) / 1000 / 3600, 0);
}, 0));

const tabs = [
    { label: 'Salas', href: route('admin.spaces.index'), active: false },
    { label: isEdit ? 'Editar sala' : 'Crear sala', href: route('admin.spaces.create'), active: true },
    { label: 'Nueva reserva', href: route('public.reservations.create'), active: false },
];

const imagePreview = computed(() => form.image || props.space?.image || '');

function onNameInput() {
    if (!isEdit) {
        form.slug = form.name
            .toLowerCase()
            .normalize('NFD').replace(/[\u0300-\u036f]/g, '')
            .replace(/[^a-z0-9\s-]/g, '')
            .trim()
            .replace(/\s+/g, '-');
    }
}

function submit() {
    if (isEdit) {
        form.put(route('admin.spaces.update', props.space.slug));
    } else {
        form.post(route('admin.spaces.store'));
    }
}

function formatMoney(value) {
    const amount = Number(value || 0);
    return amount === 0 ? 'Gratis' : '$' + amount.toLocaleString('es-CO');
}
</script>

<template>
    <Head :title="isEdit ? 'Editar sala' : 'Nueva sala'" />

    <AppLayout :title="isEdit ? 'Editar sala' : 'Nueva sala'">
        <template #header>
            <div class="space-y-4">
                <div class="flex items-center gap-3 text-sm">
                    <Link :href="route('admin.spaces.index')" class="text-slate-400 hover:text-violet-300">← Salas</Link>
                    <span class="text-slate-500">/</span>
                    <h2 class="font-semibold text-xl text-slate-100">{{ isEdit ? 'Editar sala' : 'Nueva sala' }}</h2>
                </div>

                <div class="flex flex-wrap gap-2">
                    <Link
                        v-for="tab in tabs"
                        :key="tab.label"
                        :href="tab.href"
                        class="rounded-full border px-4 py-2 text-sm font-semibold transition"
                        :class="tab.active
                            ? 'border-violet-400/40 bg-violet-500/15 text-white shadow-sm'
                            : 'border-white/10 bg-slate-900/85 text-slate-200 hover:border-violet-400/30 hover:text-white'"
                    >
                        {{ tab.label }}
                    </Link>
                </div>
            </div>
        </template>

        <div class="py-8">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <div class="grid grid-cols-1 gap-6 xl:grid-cols-3">
                    <form class="space-y-6 xl:col-span-2" @submit.prevent="submit">
                        <div class="rounded-2xl border border-white/10 bg-slate-900/85 p-6 shadow-sm sm:p-8">
                            <div class="mb-6 flex items-start justify-between gap-4">
                                <div>
                                    <h3 class="text-xl font-bold text-white">Información básica</h3>
                                    <p class="mt-1 text-sm text-slate-400">Define la sala, su precio y los datos visibles para el panel y el público.</p>
                                </div>
                                <span class="rounded-full bg-violet-500/15 px-3 py-1 text-xs font-semibold text-violet-200">
                                    {{ isEdit ? 'Modo edición' : 'Modo creación' }}
                                </span>
                            </div>

                            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                                <div class="sm:col-span-2">
                                    <label class="mb-1 block text-sm font-semibold text-slate-200">Nombre <span class="text-rose-400">*</span></label>
                                    <input v-model="form.name" @input="onNameInput" type="text" class="w-full rounded-2xl border border-white/10 bg-slate-800/70 px-3 py-2.5 text-sm text-slate-100 placeholder-slate-400 focus:border-violet-500 focus:ring-2 focus:ring-violet-500/20" placeholder="Sala Ejecutiva Boardroom" />
                                    <p v-if="form.errors.name" class="mt-1 text-xs text-rose-400">{{ form.errors.name }}</p>
                                </div>

                                <div>
                                    <label class="mb-1 block text-sm font-semibold text-slate-200">Slug (URL)</label>
                                    <input v-model="form.slug" type="text" class="w-full rounded-2xl border border-white/10 bg-slate-800/70 px-3 py-2.5 font-mono text-sm text-slate-100 placeholder-slate-400 focus:border-violet-500 focus:ring-2 focus:ring-violet-500/20" placeholder="sala-ejecutiva-boardroom" />
                                    <p v-if="form.errors.slug" class="mt-1 text-xs text-rose-400">{{ form.errors.slug }}</p>
                                </div>

                                <div>
                                    <label class="mb-1 block text-sm font-semibold text-slate-200">Tipo <span class="text-rose-400">*</span></label>
                                    <select v-model="form.type" class="w-full rounded-2xl border border-white/10 bg-slate-800/70 px-3 py-2.5 text-sm text-slate-100 focus:border-violet-500 focus:ring-2 focus:ring-violet-500/20">
                                        <option value="corporate">Corporativa</option>
                                        <option value="university">Universitaria</option>
                                    </select>
                                </div>

                                <div>
                                    <label class="mb-1 block text-sm font-semibold text-slate-200">Capacidad (personas) <span class="text-rose-400">*</span></label>
                                    <input v-model="form.capacity" type="number" min="1" class="w-full rounded-2xl border border-white/10 bg-slate-800/70 px-3 py-2.5 text-sm text-slate-100 focus:border-violet-500 focus:ring-2 focus:ring-violet-500/20" />
                                    <p v-if="form.errors.capacity" class="mt-1 text-xs text-rose-400">{{ form.errors.capacity }}</p>
                                </div>

                                <div>
                                    <label class="mb-1 block text-sm font-semibold text-slate-200">Precio por hora (COP)</label>
                                    <input v-model="form.price_per_hour" type="number" min="0" step="1000" class="w-full rounded-2xl border border-white/10 bg-slate-800/70 px-3 py-2.5 text-sm text-slate-100 focus:border-violet-500 focus:ring-2 focus:ring-violet-500/20" />
                                </div>

                                <div class="sm:col-span-2">
                                    <label class="mb-1 block text-sm font-semibold text-slate-200">Ubicación</label>
                                    <input v-model="form.location" type="text" class="w-full rounded-2xl border border-white/10 bg-slate-800/70 px-3 py-2.5 text-sm text-slate-100 focus:border-violet-500 focus:ring-2 focus:ring-violet-500/20" placeholder="Piso 3, Edificio A" />
                                </div>

                                <div class="sm:col-span-2">
                                    <label class="mb-1 block text-sm font-semibold text-slate-200">Imagen de portada</label>
                                    <input v-model="form.image" type="text" class="w-full rounded-2xl border border-white/10 bg-slate-800/70 px-3 py-2.5 text-sm text-slate-100 placeholder-slate-400 focus:border-violet-500 focus:ring-2 focus:ring-violet-500/20" placeholder="/images/spaces/space-01.svg" />
                                    <p class="mt-1 text-xs text-slate-500">Puedes usar una ruta local como <span class="font-mono">/images/spaces/space-01.svg</span> o una URL pública.</p>
                                </div>

                                <div class="sm:col-span-2">
                                    <label class="mb-1 block text-sm font-semibold text-slate-200">Descripción <span class="text-rose-400">*</span></label>
                                    <textarea v-model="form.description" rows="4" class="w-full rounded-2xl border border-white/10 bg-slate-800/70 px-3 py-2.5 text-sm text-slate-100 focus:border-violet-500 focus:ring-2 focus:ring-violet-500/20"></textarea>
                                    <p v-if="form.errors.description" class="mt-1 text-xs text-rose-400">{{ form.errors.description }}</p>
                                </div>

                                <div class="sm:col-span-2">
                                    <label class="mb-1 block text-sm font-semibold text-slate-200">Reglas de uso</label>
                                    <textarea v-model="form.rules" rows="3" class="w-full rounded-2xl border border-white/10 bg-slate-800/70 px-3 py-2.5 text-sm text-slate-100 focus:border-violet-500 focus:ring-2 focus:ring-violet-500/20"></textarea>
                                </div>

                                <div class="sm:col-span-2 flex items-center gap-3 rounded-xl border border-white/10 bg-violet-500/10 px-4 py-3">
                                    <input id="is_active" v-model="form.is_active" type="checkbox" class="h-4 w-4 rounded border-violet-400 text-violet-600 focus:ring-violet-500" />
                                    <label for="is_active" class="text-sm font-medium text-slate-200">Sala activa y visible</label>
                                </div>
                            </div>
                        </div>

                        <div class="rounded-2xl border border-white/10 bg-slate-900/85 p-6 shadow-sm sm:p-8">
                            <div class="mb-4 flex items-start justify-between gap-4">
                                <div>
                                    <h3 class="text-xl font-bold text-white">Disponibilidad semanal</h3>
                                    <p class="mt-1 text-sm text-slate-400">Activa sólo los días que se pueden reservar y define un rango horario válido.</p>
                                </div>
                                <div class="text-right">
                                    <div class="text-sm font-semibold text-slate-100">{{ activeDaysCount }} días activos</div>
                                    <div class="text-xs text-slate-400">{{ totalAvailabilityHours.toFixed(1) }} horas totales</div>
                                </div>
                            </div>

                            <div class="space-y-3">
                                <div
                                    v-for="avail in form.availabilities"
                                    :key="avail.day_of_week"
                                    class="rounded-xl border px-4 py-3"
                                    :class="avail.enabled ? 'border-violet-400/30 bg-violet-500/10' : 'border-white/10 bg-slate-800/60'"
                                >
                                    <div class="flex flex-col gap-3 lg:flex-row lg:items-center lg:justify-between">
                                        <div class="flex items-center gap-3">
                                            <input :id="'day-' + avail.day_of_week" v-model="avail.enabled" type="checkbox" class="h-4 w-4 rounded border-slate-300 text-blue-600" />
                                            <label :for="'day-' + avail.day_of_week" class="cursor-pointer text-sm font-semibold text-slate-200">{{ avail.label }}</label>
                                        </div>

                                        <template v-if="avail.enabled">
                                            <div class="flex flex-wrap items-center gap-3">
                                                <input v-model="avail.start_time" type="time" class="rounded-lg border border-white/10 bg-slate-800/70 px-2 py-1.5 font-mono text-sm text-slate-100 focus:border-violet-500 focus:ring-2 focus:ring-violet-500/20" />
                                                <span class="text-sm text-slate-400">hasta</span>
                                                <input v-model="avail.end_time" type="time" class="rounded-lg border border-white/10 bg-slate-800/70 px-2 py-1.5 font-mono text-sm text-slate-100 focus:border-violet-500 focus:ring-2 focus:ring-violet-500/20" />
                                            </div>
                                        </template>
                                        <span v-else class="text-sm italic text-slate-500">No disponible</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="flex flex-col gap-3 sm:flex-row">
                            <button type="submit" :disabled="form.processing" class="inline-flex flex-1 items-center justify-center rounded-xl bg-violet-600 px-5 py-3 text-sm font-semibold text-white transition hover:bg-violet-700 disabled:cursor-not-allowed disabled:opacity-60">
                                {{ form.processing ? 'Guardando...' : (isEdit ? 'Guardar cambios' : 'Crear sala') }}
                            </button>
                            <Link :href="route('admin.spaces.index')" class="inline-flex items-center justify-center rounded-xl border border-white/10 px-5 py-3 text-sm font-medium text-slate-200 transition hover:border-violet-400/30 hover:text-white">
                                Cancelar
                            </Link>
                        </div>
                    </form>

                    <aside class="space-y-6 xl:sticky xl:top-6 h-fit">
                        <div class="rounded-2xl border border-white/10 bg-slate-900/85 p-6 shadow-sm">
                            <h4 class="text-lg font-bold text-white">Vista previa</h4>
                            <p class="mt-1 text-sm text-slate-400">Comprueba cómo quedará la sala antes de guardar.</p>

                            <div class="mt-5 overflow-hidden rounded-2xl border border-white/10 bg-slate-800/60">
                                <div class="aspect-[16/10] bg-cover bg-center" :style="imagePreview ? { backgroundImage: `url(${imagePreview})` } : undefined"></div>
                            </div>

                            <div class="mt-5 space-y-4 rounded-2xl bg-slate-800/60 p-4">
                                <div>
                                    <div class="text-xs uppercase tracking-wide text-slate-400">Nombre</div>
                                    <div class="mt-1 text-sm font-semibold text-slate-100">{{ form.name || 'Sin nombre todavía' }}</div>
                                </div>
                                <div>
                                    <div class="text-xs uppercase tracking-wide text-slate-400">Precio</div>
                                    <div class="mt-1 text-sm font-semibold text-slate-100">{{ formatMoney(form.price_per_hour) }}/hora</div>
                                </div>
                                <div>
                                    <div class="text-xs uppercase tracking-wide text-slate-400">Capacidad</div>
                                    <div class="mt-1 text-sm font-semibold text-slate-100">{{ form.capacity || '0' }} personas</div>
                                </div>
                                <div>
                                    <div class="text-xs uppercase tracking-wide text-slate-400">Ubicación</div>
                                    <div class="mt-1 text-sm font-semibold text-slate-100">{{ form.location || 'Sin ubicación' }}</div>
                                </div>
                                <div>
                                    <div class="text-xs uppercase tracking-wide text-slate-400">Disponibilidad</div>
                                    <div class="mt-1 text-sm font-semibold text-slate-100">{{ activeDaysCount }} días activos</div>
                                </div>
                            </div>

                            <div class="mt-5 rounded-2xl border border-violet-400/20 bg-violet-500/10 px-4 py-3 text-sm text-violet-100">
                                <p class="font-semibold">Validaciones incluidas</p>
                                <p class="mt-1 text-violet-100/80">Slug único, capacidad mínima, descripción obligatoria y franjas horarias consistentes.</p>
                            </div>
                        </div>

                        <div class="rounded-2xl border border-white/10 bg-gradient-to-br from-slate-900 to-slate-800 p-6 text-white shadow-sm">
                            <h4 class="text-lg font-bold">Accesos rápidos</h4>
                            <p class="mt-1 text-sm text-slate-300">Salta entre el listado, la creación de salas y la reserva.</p>

                            <div class="mt-5 space-y-3">
                                <Link :href="route('admin.spaces.index')" class="block rounded-xl border border-white/10 bg-white/5 px-4 py-3 text-sm font-semibold text-white transition hover:bg-white/10">
                                    Ver listado de salas
                                </Link>
                                <Link :href="route('public.reservations.create')" class="block rounded-xl border border-white/10 bg-white/5 px-4 py-3 text-sm font-semibold text-white transition hover:bg-white/10">
                                    Ir a crear reserva
                                </Link>
                            </div>
                        </div>
                    </aside>
                </div>
            </div>
        </div>
    </AppLayout>
</template>