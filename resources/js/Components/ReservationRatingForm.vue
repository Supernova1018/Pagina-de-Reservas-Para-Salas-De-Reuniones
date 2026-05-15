<script setup>
import { ref } from 'vue';
import { router } from '@inertiajs/vue3';

const props = defineProps({
    reservationSlug: String,
});

const form = ref({
    service_satisfaction: 0,
    room_meets_expectations: 0,
    comment: '',
});

const isSubmitting = ref(false);

const submitRating = () => {
    if (form.value.service_satisfaction === 0 || form.value.room_meets_expectations === 0) {
        alert('Por favor califica ambas preguntas');
        return;
    }

    isSubmitting.value = true;

    router.post(
        route('public.reservations.rating.store', props.reservationSlug),
        form.value,
        {
            onFinish: () => {
                isSubmitting.value = false;
            },
        }
    );
};
</script>

<template>
    <div class="rounded-2xl border border-violet-700/30 bg-violet-600/10 p-6 space-y-6">
        <div>
            <h3 class="text-lg font-bold text-slate-100">Cuéntanos tu experiencia</h3>
            <p class="mt-1 text-sm text-slate-400">Tu retroalimentación nos ayuda a mejorar constantemente.</p>
        </div>

        <!-- Question 1: Service satisfaction -->
        <div class="space-y-3">
            <label class="block text-sm font-semibold text-slate-200">
                ¿El servicio de reserva fue satisfactorio?
            </label>
            <div class="flex gap-2">
                <button
                    v-for="star in 5"
                    :key="star"
                    type="button"
                    @click="form.service_satisfaction = star"
                    :class="[
                        'text-3xl transition',
                        star <= form.service_satisfaction ? 'text-yellow-300 scale-110' : 'text-slate-600 hover:text-yellow-200',
                    ]"
                >
                    ★
                </button>
            </div>
        </div>

        <!-- Question 2: Room expectations -->
        <div class="space-y-3">
            <label class="block text-sm font-semibold text-slate-200">
                ¿La sala cumplió tus expectativas?
            </label>
            <div class="flex gap-2">
                <button
                    v-for="star in 5"
                    :key="star"
                    type="button"
                    @click="form.room_meets_expectations = star"
                    :class="[
                        'text-3xl transition',
                        star <= form.room_meets_expectations ? 'text-purple-300 scale-110' : 'text-slate-600 hover:text-purple-200',
                    ]"
                >
                    ★
                </button>
            </div>
        </div>

        <!-- Optional comment -->
        <div class="space-y-2">
            <label class="block text-sm font-semibold text-slate-200">
                Comentario (opcional)
            </label>
            <textarea
                v-model="form.comment"
                rows="3"
                placeholder="Cuéntanos qué mejoraría tu próxima experiencia..."
                class="w-full rounded-xl border border-violet-700/30 bg-slate-800/50 px-3 py-2.5 text-sm text-slate-100 placeholder-slate-500 focus:border-violet-500 focus:ring-2 focus:ring-violet-500/20"
            />
        </div>

        <!-- Submit button -->
        <div class="pt-4 border-t border-violet-700/20">
            <button
                @click="submitRating"
                :disabled="isSubmitting || form.service_satisfaction === 0 || form.room_meets_expectations === 0"
                class="w-full rounded-xl bg-violet-600 px-4 py-2.5 font-semibold text-white transition hover:bg-violet-700 disabled:cursor-not-allowed disabled:opacity-60"
            >
                {{ isSubmitting ? '💾 Enviando...' : '✨ Enviar calificación' }}
            </button>
        </div>
    </div>
</template>
