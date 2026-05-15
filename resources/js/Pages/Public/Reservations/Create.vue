<script setup>
import { computed } from 'vue';
import { Head, Link, router, useForm, usePage } from '@inertiajs/vue3';

const props = defineProps({
    space: Object,
    spaces: Array,
    startTime: String,
    endTime: String,
});

const form = useForm({
    space_slug: props.space?.slug || '',
    start_time: props.startTime ? toLocalInput(props.startTime) : '',
    end_time: props.endTime ? toLocalInput(props.endTime) : '',
    user_name: '',
    user_email: '',
    user_phone: '',
    organization: '',
    notes: '',
});

const tabs = [
    { label: 'Ver salas', href: route('home'), active: false },
    { label: 'Nueva reserva', href: route('public.reservations.create'), active: true },
];

const selectedSpace = computed(() => props.spaces.find((space) => space.slug === form.space_slug) || props.space);

const reservationHours = computed(() => {
    if (!form.start_time || !form.end_time) return null;

    const start = new Date(form.start_time);
    const end = new Date(form.end_time);
    const diff = (end - start) / 1000 / 3600;

    return diff > 0 ? diff : null;
});

const estimatedPrice = computed(() => {
    if (!form.start_time || !form.end_time || !selectedSpace.value) return null;

    const start = new Date(form.start_time);
    const end = new Date(form.end_time);
    const hours = (end - start) / 1000 / 3600;

    if (hours <= 0) return null;

    const price = hours * parseFloat(selectedSpace.value.price_per_hour || 0);
    return price > 0 ? '$' + price.toLocaleString('es-CO', { minimumFractionDigits: 0 }) : 'Gratis';
});

const reservationTemplate = computed(() => {
    if (!selectedSpace.value) {
        return 'Selecciona una sala para ver el resumen de la reserva.';
    }

    return [
        `Reserva solicitada para ${selectedSpace.value.name}`,
        form.start_time ? `Inicio: ${form.start_time}` : null,
        form.end_time ? `Fin: ${form.end_time}` : null,
        form.user_name ? `Solicitante: ${form.user_name}` : null,
        form.user_email ? `Correo: ${form.user_email}` : null,
        form.organization ? `Organización: ${form.organization}` : null,
        form.notes ? `Notas: ${form.notes}` : null,
    ].filter(Boolean).join('\n');
});

const page = usePage();
const currentUser = computed(() => page.props.auth?.user ?? null);
const isAdmin = computed(() => currentUser.value?.is_admin === true);

function logout() {
    router.post(route('logout'));
}

function toLocalInput(iso) {
    const date = new Date(iso);
    const pad = (value) => String(value).padStart(2, '0');

    return `${date.getFullYear()}-${pad(date.getMonth() + 1)}-${pad(date.getDate())}T${pad(date.getHours())}:${pad(date.getMinutes())}`;
}

function submit() {
    form.post('/reservations');
}
</script>

<template>
    <Head title="Nueva reserva" />

    <div class="min-h-screen bg-slate-900 text-slate-200">
        <header class="border-b border-violet-700/20 bg-slate-900/40 backdrop-blur">
            <div class="mx-auto flex max-w-7xl flex-col gap-4 px-4 py-5 sm:px-6 lg:px-8">
                <div class="flex flex-wrap items-center justify-between gap-3">
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-[0.28em] text-slate-400">Reservas</p>
                        <h1 class="text-2xl font-bold text-white">Solicitar nueva reserva</h1>
                    </div>
                    <div class="flex flex-wrap items-center gap-2">
                        <div v-if="currentUser" class="inline-flex items-center gap-2 rounded-full border border-violet-400/20 bg-violet-500/10 px-3 py-2 text-sm text-violet-100">
                            <span class="text-xs font-semibold uppercase tracking-[0.2em] text-violet-200/80">Sesión</span>
                            <span class="max-w-[180px] truncate font-semibold">{{ currentUser.name }}</span>
                        </div>

                        <Link v-if="currentUser && isAdmin" :href="route('admin.dashboard')" class="inline-flex items-center rounded-full border border-white/10 px-4 py-2 text-sm font-semibold text-slate-200 transition hover:border-violet-400/30 hover:text-white">
                            Panel
                        </Link>
                        <Link v-else-if="currentUser" :href="route('public.reservations.mine')" class="inline-flex items-center rounded-full border border-white/10 px-4 py-2 text-sm font-semibold text-slate-200 transition hover:border-violet-400/30 hover:text-white">
                            Mis reservas
                        </Link>
                        <button v-if="currentUser" @click="logout" class="inline-flex items-center rounded-full border border-white/10 px-4 py-2 text-sm font-semibold text-slate-200 transition hover:border-rose-400/30 hover:text-rose-100">
                            Cerrar sesión
                        </button>

                        <Link href="/" class="inline-flex items-center rounded-full border border-white/10 px-4 py-2 text-sm font-semibold text-slate-200 transition hover:border-violet-400/30 hover:text-white">
                            ← Ver salas
                        </Link>
                    </div>
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
        </header>

        <div class="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 gap-6 xl:grid-cols-3">
                <form class="space-y-6 xl:col-span-2" @submit.prevent="submit">
                    <div class="rounded-3xl border border-white/10 bg-slate-900/85 p-6 shadow-sm sm:p-8">
                        <div class="flex items-start justify-between gap-4">
                            <div>
                                <h2 class="text-2xl font-bold text-white">Solicitar reserva</h2>
                                <p class="mt-2 text-sm text-slate-400">Completa el formulario. Revisaremos la disponibilidad antes de confirmar tu reserva.</p>
                            </div>
                                <div class="hidden rounded-full bg-slate-800/60 px-3 py-1 text-xs font-semibold text-slate-200 sm:block">Control de horario</div>
                        </div>

                        <div v-if="Object.keys(form.errors).length" class="mt-6 rounded-2xl border border-rose-400/20 bg-rose-500/10 px-4 py-3 text-sm text-rose-100">
                            Revisa los campos marcados. Hay errores que deben corregirse antes de guardar.
                        </div>

                        <div class="mt-6 space-y-6">
                            <div>
                                <label class="mb-1 block text-sm font-semibold text-slate-200">Sala <span class="text-rose-400">*</span></label>
                                <select
                                    v-model="form.space_slug"
                                    :disabled="!!space"
                                    class="w-full rounded-2xl border border-white/10 bg-slate-800/70 px-3 py-2.5 text-sm text-slate-100 focus:border-violet-500 focus:ring-2 focus:ring-violet-500/20 disabled:bg-slate-800/40"
                                >
                                    <option value="">— Selecciona una sala —</option>
                                    <option v-for="item in spaces" :key="item.slug" :value="item.slug">{{ item.name }}</option>
                                </select>
                                <p v-if="form.errors.space_slug" class="mt-1 text-xs text-red-500">{{ form.errors.space_slug }}</p>
                            </div>

                            <div v-if="selectedSpace" class="rounded-2xl border border-violet-400/20 bg-violet-500/10 px-4 py-4 text-sm text-violet-100">
                                <div class="flex flex-wrap items-center justify-between gap-2">
                                    <div>
                                        <div class="font-semibold">{{ selectedSpace.name }}</div>
                                        <div v-if="selectedSpace.location" class="text-violet-100/80">{{ selectedSpace.location }}</div>
                                    </div>
                                    <div class="flex flex-wrap gap-2 text-xs font-semibold">
                                        <span class="rounded-full bg-white/10 px-3 py-1">{{ selectedSpace.capacity }} personas</span>
                                        <span class="rounded-full bg-white/10 px-3 py-1">{{ selectedSpace.type_label || 'Sala disponible' }}</span>
                                    </div>
                                </div>
                            </div>

                            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                                <div>
                                    <label class="mb-1 block text-sm font-semibold text-slate-200">Inicio <span class="text-rose-400">*</span></label>
                                    <input v-model="form.start_time" type="datetime-local" class="w-full rounded-2xl border border-white/10 bg-slate-800/70 px-3 py-2.5 text-sm text-slate-100 focus:border-violet-500 focus:ring-2 focus:ring-violet-500/20" />
                                    <p v-if="form.errors.start_time" class="mt-1 text-xs text-red-500">{{ form.errors.start_time }}</p>
                                </div>
                                <div>
                                    <label class="mb-1 block text-sm font-semibold text-slate-200">Fin <span class="text-rose-400">*</span></label>
                                    <input v-model="form.end_time" type="datetime-local" class="w-full rounded-2xl border border-white/10 bg-slate-800/70 px-3 py-2.5 text-sm text-slate-100 focus:border-violet-500 focus:ring-2 focus:ring-violet-500/20" />
                                    <p v-if="form.errors.end_time" class="mt-1 text-xs text-red-500">{{ form.errors.end_time }}</p>
                                </div>
                            </div>

                            <div v-if="estimatedPrice" class="rounded-2xl border border-emerald-400/20 bg-emerald-500/10 px-4 py-3 text-sm text-emerald-100">
                                Precio estimado: <strong>{{ estimatedPrice }}</strong>
                                <span v-if="reservationHours" class="ml-2 text-emerald-700/80">({{ reservationHours.toFixed(1) }} horas)</span>
                            </div>

                            <hr class="border-white/10" />

                            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                                <div>
                                    <label class="mb-1 block text-sm font-semibold text-slate-200">Nombre completo <span class="text-rose-400">*</span></label>
                                    <input v-model="form.user_name" type="text" placeholder="Juan Pérez" class="w-full rounded-2xl border border-white/10 bg-slate-800/70 px-3 py-2.5 text-sm text-slate-100 placeholder-slate-500 focus:border-violet-500 focus:ring-2 focus:ring-violet-500/20" />
                                    <p v-if="form.errors.user_name" class="mt-1 text-xs text-red-500">{{ form.errors.user_name }}</p>
                                </div>
                                <div>
                                    <label class="mb-1 block text-sm font-semibold text-slate-200">Correo electrónico <span class="text-rose-400">*</span></label>
                                    <input v-model="form.user_email" type="email" placeholder="juan@correo.com" class="w-full rounded-2xl border border-white/10 bg-slate-800/70 px-3 py-2.5 text-sm text-slate-100 placeholder-slate-500 focus:border-violet-500 focus:ring-2 focus:ring-violet-500/20" />
                                    <p v-if="form.errors.user_email" class="mt-1 text-xs text-red-500">{{ form.errors.user_email }}</p>
                                </div>
                                <div>
                                    <label class="mb-1 block text-sm font-semibold text-slate-200">Teléfono</label>
                                    <input v-model="form.user_phone" type="tel" placeholder="+57 300 000 0000" class="w-full rounded-2xl border border-white/10 bg-slate-800/70 px-3 py-2.5 text-sm text-slate-100 placeholder-slate-500 focus:border-violet-500 focus:ring-2 focus:ring-violet-500/20" />
                                </div>
                                <div>
                                    <label class="mb-1 block text-sm font-semibold text-slate-200">Organización / Universidad</label>
                                    <input v-model="form.organization" type="text" placeholder="Empresa o institución" class="w-full rounded-2xl border border-white/10 bg-slate-800/70 px-3 py-2.5 text-sm text-slate-100 placeholder-slate-500 focus:border-violet-500 focus:ring-2 focus:ring-violet-500/20" />
                                </div>
                            </div>

                            <div>
                                <label class="mb-1 block text-sm font-semibold text-slate-200">Notas adicionales</label>
                                <textarea v-model="form.notes" rows="3" placeholder="¿Necesitas equipo especial, configuración particular...?" class="w-full rounded-2xl border border-white/10 bg-slate-800/70 px-3 py-2.5 text-sm text-slate-100 placeholder-slate-500 focus:border-violet-500 focus:ring-2 focus:ring-violet-500/20"></textarea>
                            </div>

                            <div class="flex flex-col gap-3 pt-2 sm:flex-row">
                                <button type="submit" :disabled="form.processing" class="inline-flex flex-1 items-center justify-center rounded-2xl bg-violet-600 px-5 py-3 text-sm font-semibold text-white transition hover:bg-violet-700 disabled:cursor-not-allowed disabled:opacity-60">
                                    {{ form.processing ? 'Enviando...' : 'Solicitar reserva' }}
                                </button>
                                <Link
                                    :href="space ? '/spaces/' + space.slug : '/'"
                                    class="inline-flex items-center justify-center rounded-2xl border border-white/10 px-5 py-3 text-sm font-medium text-slate-200 transition hover:border-violet-400/30 hover:text-white"
                                >
                                    Cancelar
                                </Link>
                            </div>
                        </div>
                    </div>
                </form>

                <aside class="space-y-6 xl:sticky xl:top-6 h-fit">
                    <div class="rounded-3xl border border-white/10 bg-slate-900/85 p-6 shadow-sm">
                        <h3 class="text-lg font-bold text-white">Resumen de la reserva</h3>
                        <div class="mt-4 space-y-3 rounded-2xl bg-slate-800/60 p-4 text-sm text-slate-300">
                            <div>
                                <div class="text-xs uppercase tracking-wide text-slate-400">Sala</div>
                                <div class="mt-1 font-semibold text-slate-100">{{ selectedSpace ? selectedSpace.name : 'Selecciona una sala' }}</div>
                            </div>
                            <div>
                                <div class="text-xs uppercase tracking-wide text-slate-400">Horario</div>
                                <div class="mt-1 font-semibold text-slate-100">
                                    {{ form.start_time || '—' }}
                                    <span class="text-slate-400">→</span>
                                    {{ form.end_time || '—' }}
                                </div>
                            </div>
                            <div>
                                <div class="text-xs uppercase tracking-wide text-slate-400">Estado inicial</div>
                                <div class="mt-1 inline-flex rounded-full bg-emerald-500/10 px-3 py-1 text-xs font-semibold text-emerald-100">Libre</div>
                            </div>
                            <div>
                                <div class="text-xs uppercase tracking-wide text-slate-400">Canal de confirmación</div>
                                <div class="mt-1 text-slate-300">Correo electrónico automático.</div>
                            </div>
                        </div>

                        <div class="mt-4 rounded-2xl border border-violet-400/20 bg-violet-500/10 px-4 py-3 text-sm text-violet-100">
                            Revisamos solapamientos, capacidad y duración mínima antes de guardar.
                        </div>
                    </div>

                    <div class="rounded-3xl border border-white/10 bg-gradient-to-br from-slate-900 to-slate-800 p-6 text-white shadow-sm">
                        <h3 class="text-lg font-bold">Plantilla de confirmación</h3>
                        <p class="mt-1 text-sm text-slate-300">Esta es la base del correo que se envía automáticamente al reservar.</p>
                        <pre class="mt-4 whitespace-pre-wrap rounded-2xl border border-white/10 bg-white/5 p-4 text-xs leading-6 text-slate-200">{{ reservationTemplate }}</pre>
                    </div>
                </aside>
            </div>
        </div>
    </div>
</template>