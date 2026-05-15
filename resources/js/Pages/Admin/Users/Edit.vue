<script setup>
import { computed } from 'vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';

const props = defineProps({
    user: Object,
    message: String,
});

const form = useForm({
    name: props.user?.name || '',
    email: props.user?.email || '',
    password: '',
    password_confirmation: '',
    is_admin: props.user?.is_admin ?? false,
    is_blocked: props.user?.is_blocked ?? false,
    is_suspended: props.user?.is_suspended ?? false,
    email_verified: !!props.user?.email_verified_at,
});

const statusLabel = computed(() => {
    if (form.is_blocked) return 'Bloqueado';
    if (form.is_suspended) return 'Suspendido';
    if (form.is_admin) return 'Administrador';
    return 'Activo';
});

const statusTone = computed(() => {
    if (form.is_blocked) return 'bg-rose-500/15 text-rose-200';
    if (form.is_suspended) return 'bg-amber-500/15 text-amber-200';
    if (form.is_admin) return 'bg-violet-500/15 text-violet-200';
    return 'bg-emerald-500/15 text-emerald-200';
});

function formatDate(iso) {
    if (!iso) return 'Sin fecha';

    return new Date(iso).toLocaleDateString('es-CO', {
        day: '2-digit',
        month: 'short',
        year: 'numeric',
    });
}

function submit() {
    form.put(route('admin.users.update', props.user.id));
}
</script>

<template>
    <Head :title="`Editar usuario: ${user.name}`" />

    <AppLayout :title="`Editar usuario: ${user.name}`">
        <template #header>
            <div class="space-y-4">
                <div class="flex items-center gap-3 text-sm">
                    <Link :href="route('admin.users.index')" class="text-slate-400 hover:text-violet-300">← Usuarios</Link>
                    <span class="text-slate-500">/</span>
                    <h2 class="font-semibold text-xl text-slate-100">Editar usuario</h2>
                </div>
                <p class="max-w-3xl text-sm text-slate-400">Actualiza datos del perfil, estado de acceso y permisos administrativos.</p>
            </div>
        </template>

        <div class="px-4 py-8 sm:px-6 lg:px-8">
            <div class="mx-auto grid max-w-7xl grid-cols-1 gap-6 xl:grid-cols-3">
                <form class="space-y-6 xl:col-span-2" @submit.prevent="submit">
                    <div class="rounded-3xl border border-white/10 bg-slate-900/85 p-6 shadow-2xl shadow-violet-950/20 ring-1 ring-white/5 sm:p-8">
                        <div class="mb-6 flex items-start justify-between gap-4">
                            <div>
                                <h3 class="text-xl font-bold text-white">Información del usuario</h3>
                                <p class="mt-1 text-sm text-slate-400">Edita nombre, correo y contraseña si lo necesitas.</p>
                            </div>
                            <span class="rounded-full px-3 py-1 text-xs font-semibold" :class="statusTone">{{ statusLabel }}</span>
                        </div>

                        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                            <div class="sm:col-span-2">
                                <label class="mb-1 block text-sm font-semibold text-slate-200">Nombre</label>
                                <input v-model="form.name" type="text" class="w-full rounded-2xl border border-white/10 bg-slate-800/70 px-3 py-2.5 text-sm text-slate-100 focus:border-violet-500 focus:ring-2 focus:ring-violet-500/20" />
                                <p v-if="form.errors.name" class="mt-1 text-xs text-rose-400">{{ form.errors.name }}</p>
                            </div>

                            <div class="sm:col-span-2">
                                <label class="mb-1 block text-sm font-semibold text-slate-200">Correo electrónico</label>
                                <input v-model="form.email" type="email" class="w-full rounded-2xl border border-white/10 bg-slate-800/70 px-3 py-2.5 text-sm text-slate-100 focus:border-violet-500 focus:ring-2 focus:ring-violet-500/20" />
                                <p v-if="form.errors.email" class="mt-1 text-xs text-rose-400">{{ form.errors.email }}</p>
                            </div>

                            <div>
                                <label class="mb-1 block text-sm font-semibold text-slate-200">Nueva contraseña</label>
                                <input v-model="form.password" type="password" class="w-full rounded-2xl border border-white/10 bg-slate-800/70 px-3 py-2.5 text-sm text-slate-100 focus:border-violet-500 focus:ring-2 focus:ring-violet-500/20" />
                                <p class="mt-1 text-xs text-slate-500">Déjalo vacío si no quieres cambiarla.</p>
                                <p v-if="form.errors.password" class="mt-1 text-xs text-rose-400">{{ form.errors.password }}</p>
                            </div>

                            <div>
                                <label class="mb-1 block text-sm font-semibold text-slate-200">Confirmar contraseña</label>
                                <input v-model="form.password_confirmation" type="password" class="w-full rounded-2xl border border-white/10 bg-slate-800/70 px-3 py-2.5 text-sm text-slate-100 focus:border-violet-500 focus:ring-2 focus:ring-violet-500/20" />
                            </div>

                            <div class="sm:col-span-2 grid gap-3 md:grid-cols-2">
                                <label class="flex items-center gap-3 rounded-2xl border border-white/10 bg-white/5 px-4 py-3">
                                    <input v-model="form.email_verified" type="checkbox" class="h-4 w-4 rounded border-violet-400 text-violet-600 focus:ring-violet-500" />
                                    <span class="text-sm font-medium text-slate-200">Correo verificado</span>
                                </label>

                                <label class="flex items-center gap-3 rounded-2xl border border-white/10 bg-white/5 px-4 py-3">
                                    <input v-model="form.is_admin" type="checkbox" class="h-4 w-4 rounded border-violet-400 text-violet-600 focus:ring-violet-500" />
                                    <span class="text-sm font-medium text-slate-200">Administrador</span>
                                </label>

                                <label class="flex items-center gap-3 rounded-2xl border border-white/10 bg-white/5 px-4 py-3">
                                    <input v-model="form.is_blocked" type="checkbox" class="h-4 w-4 rounded border-violet-400 text-violet-600 focus:ring-violet-500" />
                                    <span class="text-sm font-medium text-slate-200">Bloquear acceso</span>
                                </label>

                                <label class="flex items-center gap-3 rounded-2xl border border-white/10 bg-white/5 px-4 py-3">
                                    <input v-model="form.is_suspended" type="checkbox" class="h-4 w-4 rounded border-violet-400 text-violet-600 focus:ring-violet-500" />
                                    <span class="text-sm font-medium text-slate-200">Suspender reservas</span>
                                </label>
                            </div>
                        </div>
                    </div>

                    <div class="flex flex-col gap-3 sm:flex-row">
                        <button type="submit" :disabled="form.processing" class="inline-flex flex-1 items-center justify-center rounded-2xl bg-violet-600 px-5 py-3 text-sm font-semibold text-white transition hover:bg-violet-700 disabled:cursor-not-allowed disabled:opacity-60">
                            {{ form.processing ? 'Guardando...' : 'Guardar cambios' }}
                        </button>
                        <Link :href="route('admin.users.index')" class="inline-flex items-center justify-center rounded-2xl border border-white/10 px-5 py-3 text-sm font-medium text-slate-200 transition hover:border-violet-400/30 hover:text-white">
                            Cancelar
                        </Link>
                    </div>
                </form>

                <aside class="space-y-6 xl:sticky xl:top-6 h-fit">
                    <div class="rounded-3xl border border-white/10 bg-slate-900/85 p-6 shadow-2xl shadow-violet-950/20 ring-1 ring-white/5">
                        <div class="flex items-center gap-4">
                            <img :src="user.profile_photo_url" :alt="user.name" class="h-16 w-16 rounded-2xl border border-white/10 object-cover" />
                            <div>
                                <h4 class="text-lg font-bold text-white">{{ user.name }}</h4>
                                <p class="text-sm text-slate-400">{{ user.email }}</p>
                            </div>
                        </div>

                        <div class="mt-5 space-y-3 rounded-2xl bg-white/5 p-4 text-sm text-slate-300">
                            <div class="flex items-center justify-between gap-4">
                                <span class="text-slate-400">Registrado</span>
                                <span>{{ formatDate(user.created_at) }}</span>
                            </div>
                            <div class="flex items-center justify-between gap-4">
                                <span class="text-slate-400">Reservas totales</span>
                                <span>{{ user.reservations_count }}</span>
                            </div>
                            <div class="flex items-center justify-between gap-4">
                                <span class="text-slate-400">Confirmadas</span>
                                <span>{{ user.confirmed_reservations_count }}</span>
                            </div>
                            <div class="flex items-center justify-between gap-4">
                                <span class="text-slate-400">Gasto confirmado</span>
                                <span>{{ '$' + Number(user.confirmed_spend || 0).toLocaleString('es-CO') }}</span>
                            </div>
                            <div class="flex items-center justify-between gap-4">
                                <span class="text-slate-400">Última reserva</span>
                                <span>{{ user.last_reservation_at ? formatDate(user.last_reservation_at) : 'Sin reservas' }}</span>
                            </div>
                        </div>
                    </div>

                    <div class="rounded-3xl border border-violet-400/20 bg-violet-500/10 p-6 text-sm text-violet-100">
                        <h4 class="text-base font-bold text-white">Consejo operativo</h4>
                        <p class="mt-2 text-violet-100/80">Si bloqueas al usuario, no podrá iniciar sesión. Si lo suspendes, seguirá entrando pero no podrá crear ni cancelar reservas.</p>
                    </div>
                </aside>
            </div>
        </div>
    </AppLayout>
</template>