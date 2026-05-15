<script setup>
import { ref } from 'vue';
import { Head, Link, router, useForm } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import Modal from '@/Components/Modal.vue';
import DangerButton from '@/Components/DangerButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';

const props = defineProps({
    spaces:  Array,
    message: String,
});

const showDeleteModal = ref(false);
const spaceToDelete   = ref(null);
const deleteForm      = useForm({});

const confirmDelete = (space) => {
    spaceToDelete.value = space;
    showDeleteModal.value = true;
};

const deleteSpace = () => {
    deleteForm.delete(route('admin.spaces.destroy', spaceToDelete.value.slug), {
        onSuccess: () => { showDeleteModal.value = false; },
    });
};

function formatPrice(price) {
    if (!price || parseFloat(price) === 0) return 'Gratis';
    return '$' + Number(price).toLocaleString('es-CO') + '/h';
}
</script>

<template>
    <Head title="Gestión de Salas" />
    <AppLayout title="Salas">
        <template #header>
            <div class="flex items-center justify-between gap-3">
                <h2 class="font-semibold text-xl text-slate-100 leading-tight">Salas de reuniones</h2>
                <Link
                    :href="route('admin.spaces.create')"
                    class="rounded-xl border border-violet-400/30 bg-violet-500/10 px-4 py-2 text-sm font-semibold text-violet-100 transition hover:bg-violet-500/20"
                >
                    Nueva sala
                </Link>
            </div>
        </template>

        <div class="px-4 py-8 sm:px-6 lg:px-8">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

                <!-- Flash -->
                <div v-if="message" class="mb-6 rounded-2xl border border-emerald-400/20 bg-emerald-500/10 px-4 py-3 text-sm text-emerald-100">
                    {{ message }}
                </div>

                <!-- Table -->
                <div class="overflow-hidden rounded-3xl border border-white/10 bg-slate-900/85 shadow-2xl shadow-violet-950/20 ring-1 ring-white/5">
                    <table class="w-full text-sm">
                        <thead class="border-b border-white/10 bg-slate-800/80">
                            <tr>
                                <th class="text-left px-5 py-3 font-semibold text-slate-300">Sala</th>
                                <th class="text-left px-5 py-3 font-semibold text-slate-300 hidden md:table-cell">Tipo</th>
                                <th class="text-left px-5 py-3 font-semibold text-slate-300 hidden lg:table-cell">Cap.</th>
                                <th class="text-left px-5 py-3 font-semibold text-slate-300 hidden lg:table-cell">Precio</th>
                                <th class="text-left px-5 py-3 font-semibold text-slate-300 hidden xl:table-cell">Reservas</th>
                                <th class="text-left px-5 py-3 font-semibold text-slate-300">Estado</th>
                                <th class="px-5 py-3"></th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-white/5">
                            <tr v-for="space in spaces" :key="space.id" class="transition hover:bg-white/5">
                                <td class="px-5 py-4">
                                    <div class="flex items-center gap-3">
                                        <div class="h-12 w-16 overflow-hidden rounded-xl border border-white/10 bg-slate-800/60">
                                            <img v-if="space.image" :src="space.image" :alt="space.name" class="h-full w-full object-cover" />
                                        </div>
                                        <div>
                                            <div class="font-semibold text-white">{{ space.name }}</div>
                                            <div class="text-xs font-mono text-slate-400">{{ space.slug }}</div>
                                            <div v-if="space.location" class="mt-0.5 text-xs text-slate-500">{{ space.location }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-5 py-4 hidden md:table-cell">
                                    <span
                                        class="text-xs font-bold px-2 py-0.5 rounded-full"
                                        :class="space.type === 'university' ? 'bg-violet-500/15 text-violet-200' : 'bg-emerald-500/15 text-emerald-200'"
                                    >
                                        {{ space.type === 'university' ? 'Universitaria' : 'Corporativa' }}
                                    </span>
                                </td>
                                <td class="px-5 py-4 hidden lg:table-cell text-slate-300">{{ space.capacity }}</td>
                                <td class="px-5 py-4 hidden lg:table-cell font-medium text-slate-200">{{ formatPrice(space.price_per_hour) }}</td>
                                <td class="px-5 py-4 hidden xl:table-cell text-slate-300">{{ space.reservations_count }}</td>
                                <td class="px-5 py-4">
                                    <span
                                        class="text-xs font-bold px-2 py-1 rounded-full"
                                        :class="space.is_active ? 'bg-emerald-500/15 text-emerald-200' : 'bg-rose-500/15 text-rose-200'"
                                    >
                                        {{ space.is_active ? 'Activa' : 'Inactiva' }}
                                    </span>
                                </td>
                                <td class="px-5 py-4">
                                    <div class="flex items-center gap-2 justify-end">
                                        <Link
                                            :href="route('admin.spaces.edit', space.slug)"
                                            class="rounded-lg border border-violet-400/30 px-2 py-1 text-xs font-semibold text-violet-200 transition hover:border-violet-400/60 hover:bg-violet-500/10"
                                        >Editar</Link>
                                        <button
                                            @click="confirmDelete(space)"
                                            class="rounded-lg border border-rose-400/30 px-2 py-1 text-xs font-semibold text-rose-200 transition hover:border-rose-400/60 hover:bg-rose-500/10"
                                        >Eliminar</button>
                                    </div>
                                </td>
                            </tr>
                            <tr v-if="spaces.length === 0">
                                <td colspan="7" class="py-12 text-center text-slate-400">No hay salas registradas.</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Delete modal -->
        <Modal :show="showDeleteModal" @close="showDeleteModal = false">
            <div class="p-6">
                <h3 class="mb-2 text-lg font-bold text-white">¿Eliminar sala?</h3>
                <p class="mb-6 text-sm text-slate-300">
                    Vas a eliminar <strong>{{ spaceToDelete?.name }}</strong>. Esta acción no se puede deshacer.
                </p>
                <div class="flex gap-3 justify-end">
                    <SecondaryButton @click="showDeleteModal = false">Cancelar</SecondaryButton>
                    <DangerButton @click="deleteSpace" :disabled="deleteForm.processing">Eliminar</DangerButton>
                </div>
            </div>
        </Modal>
    </AppLayout>
</template>