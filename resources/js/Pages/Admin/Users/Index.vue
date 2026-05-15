<script setup>
import { ref } from 'vue';
import { Head, Link, router } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import Modal from '@/Components/Modal.vue';
import DangerButton from '@/Components/DangerButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';

const props = defineProps({
    users: Array,
    message: String,
});

const showDeleteModal = ref(false);
const userToDelete = ref(null);

function formatDate(iso) {
    if (!iso) return 'Sin fecha';

    return new Date(iso).toLocaleDateString('es-CO', {
        day: '2-digit',
        month: 'short',
        year: 'numeric',
    });
}

function formatDateTime(iso) {
    if (!iso) return 'Sin reservas';

    const date = new Date(iso);

    return date.toLocaleDateString('es-CO', { day: '2-digit', month: 'short' }) + ' ' +
        date.toLocaleTimeString('es-CO', { hour: '2-digit', minute: '2-digit' });
}

function formatMoney(value) {
    return '$' + Number(value || 0).toLocaleString('es-CO');
}

function openDeleteModal(user) {
    userToDelete.value = user;
    showDeleteModal.value = true;
}

function deleteUser() {
    router.delete(route('admin.users.destroy', userToDelete.value.id), {
        onSuccess: () => {
            showDeleteModal.value = false;
            userToDelete.value = null;
        },
    });
}

function quickAction(action, user) {
    router.post(route(`admin.users.${action}`, user.id), {}, {
        preserveScroll: true,
    });
}

function statusLabel(user) {
    if (user.is_blocked) return 'Bloqueado';
    if (user.is_suspended) return 'Suspendido';
    if (user.is_admin) return 'Administrador';
    return 'Activo';
}

function statusTone(user) {
    if (user.is_blocked) return 'bg-rose-500/15 text-rose-200';
    if (user.is_suspended) return 'bg-amber-500/15 text-amber-200';
    if (user.is_admin) return 'bg-violet-500/15 text-violet-200';
    return 'bg-emerald-500/15 text-emerald-200';
}
</script>

<template>
    <Head title="Usuarios" />

    <AppLayout title="Usuarios">
        <template #header>
            <div class="flex items-center justify-between gap-3">
                <div>
                    <p class="text-xs font-semibold uppercase tracking-[0.3em] text-cyan-300">Administración</p>
                    <h2 class="mt-2 text-xl font-semibold text-slate-100">Usuarios del sistema</h2>
                    <p class="mt-1 text-sm text-slate-400">Gestiona estado, acceso y datos principales de cada cuenta.</p>
                </div>
                <Link
                    :href="route('admin.dashboard')"
                    class="rounded-xl border border-white/10 bg-white/5 px-4 py-2 text-sm font-semibold text-slate-200 transition hover:border-violet-400/30 hover:bg-violet-500/10 hover:text-white"
                >
                    Volver al dashboard
                </Link>
            </div>
        </template>

        <div class="px-4 py-8 sm:px-6 lg:px-8">
            <div class="mx-auto max-w-7xl space-y-6">
                <div v-if="message" class="rounded-2xl border border-emerald-400/20 bg-emerald-500/10 px-4 py-3 text-sm text-emerald-100">
                    {{ message }}
                </div>

                <div class="overflow-hidden rounded-3xl border border-white/10 bg-slate-900/85 shadow-2xl shadow-violet-950/20 ring-1 ring-white/5">
                    <div class="overflow-x-auto">
                        <table class="min-w-full text-sm">
                            <thead class="border-b border-white/10 bg-slate-800/80 text-slate-300">
                                <tr>
                                    <th class="px-5 py-3 text-left font-semibold">Usuario</th>
                                    <th class="px-5 py-3 text-left font-semibold">Contacto</th>
                                    <th class="px-5 py-3 text-left font-semibold">Estado</th>
                                    <th class="px-5 py-3 text-left font-semibold">Actividad</th>
                                    <th class="px-5 py-3 text-left font-semibold">Última reserva</th>
                                    <th class="px-5 py-3 text-right font-semibold">Acciones</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-white/5">
                                <tr v-for="user in users" :key="user.id" class="align-top transition hover:bg-white/5">
                                    <td class="px-5 py-4">
                                        <div class="flex items-center gap-3">
                                            <img :src="user.profile_photo_url" :alt="user.name" class="h-11 w-11 rounded-2xl border border-white/10 object-cover" />
                                            <div>
                                                <div class="font-semibold text-white">{{ user.name }}</div>
                                                <div class="text-xs text-slate-400">Registrado {{ formatDate(user.created_at) }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-5 py-4 text-slate-300">
                                        <div>{{ user.email }}</div>
                                        <div class="mt-1 text-xs" :class="user.email_verified_at ? 'text-emerald-300' : 'text-amber-300'">
                                            {{ user.email_verified_at ? 'Correo verificado' : 'Pendiente de verificación' }}
                                        </div>
                                    </td>
                                    <td class="px-5 py-4">
                                        <div class="flex flex-wrap gap-2">
                                            <span class="rounded-full px-2.5 py-1 text-xs font-semibold" :class="statusTone(user)">{{ statusLabel(user) }}</span>
                                            <span v-if="user.is_blocked" class="rounded-full bg-rose-500/15 px-2.5 py-1 text-xs font-semibold text-rose-200">Acceso bloqueado</span>
                                            <span v-if="user.is_suspended" class="rounded-full bg-amber-500/15 px-2.5 py-1 text-xs font-semibold text-amber-200">Reservas suspendidas</span>
                                        </div>
                                    </td>
                                    <td class="px-5 py-4 text-slate-300">
                                        <div>{{ user.reservations_count }} reservas</div>
                                        <div class="mt-1 text-xs text-slate-400">{{ user.confirmed_reservations_count }} confirmadas · {{ formatMoney(user.confirmed_spend) }}</div>
                                    </td>
                                    <td class="px-5 py-4 text-slate-300">
                                        {{ user.last_reservation_at ? formatDateTime(user.last_reservation_at) : 'Sin reservas' }}
                                    </td>
                                    <td class="px-5 py-4">
                                        <div class="flex flex-wrap justify-end gap-2">
                                            <Link
                                                :href="route('admin.users.edit', user.id)"
                                                class="rounded-lg border border-violet-400/30 px-2.5 py-1 text-xs font-semibold text-violet-200 transition hover:border-violet-400/60 hover:bg-violet-500/10"
                                            >
                                                Editar
                                            </Link>
                                            <button
                                                v-if="!user.is_blocked"
                                                type="button"
                                                @click="quickAction('block', user)"
                                                class="rounded-lg border border-rose-400/30 px-2.5 py-1 text-xs font-semibold text-rose-200 transition hover:border-rose-400/60 hover:bg-rose-500/10"
                                            >
                                                Bloquear
                                            </button>
                                            <button
                                                v-else
                                                type="button"
                                                @click="quickAction('unblock', user)"
                                                class="rounded-lg border border-emerald-400/30 px-2.5 py-1 text-xs font-semibold text-emerald-200 transition hover:border-emerald-400/60 hover:bg-emerald-500/10"
                                            >
                                                Desbloquear
                                            </button>
                                            <button
                                                v-if="!user.is_suspended"
                                                type="button"
                                                @click="quickAction('suspend', user)"
                                                class="rounded-lg border border-amber-400/30 px-2.5 py-1 text-xs font-semibold text-amber-200 transition hover:border-amber-400/60 hover:bg-amber-500/10"
                                            >
                                                Suspender
                                            </button>
                                            <button
                                                v-else
                                                type="button"
                                                @click="quickAction('unsuspend', user)"
                                                class="rounded-lg border border-cyan-400/30 px-2.5 py-1 text-xs font-semibold text-cyan-200 transition hover:border-cyan-400/60 hover:bg-cyan-500/10"
                                            >
                                                Reactivar
                                            </button>
                                            <button
                                                type="button"
                                                @click="openDeleteModal(user)"
                                                class="rounded-lg border border-white/10 px-2.5 py-1 text-xs font-semibold text-slate-200 transition hover:border-rose-400/50 hover:bg-rose-500/10 hover:text-rose-100"
                                            >
                                                Eliminar
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                <tr v-if="users.length === 0">
                                    <td colspan="6" class="px-5 py-12 text-center text-slate-400">No hay usuarios registrados.</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <Modal :show="showDeleteModal" @close="showDeleteModal = false">
            <div class="p-6">
                <h3 class="mb-2 text-lg font-bold text-white">¿Eliminar usuario?</h3>
                <p class="mb-6 text-sm text-slate-300">
                    Vas a eliminar <strong>{{ userToDelete?.name }}</strong>. Las reservas conservarán el historial con los datos guardados en la reserva.
                </p>
                <div class="flex justify-end gap-3">
                    <SecondaryButton @click="showDeleteModal = false">Cancelar</SecondaryButton>
                    <DangerButton @click="deleteUser">Eliminar</DangerButton>
                </div>
            </div>
        </Modal>
    </AppLayout>
</template>