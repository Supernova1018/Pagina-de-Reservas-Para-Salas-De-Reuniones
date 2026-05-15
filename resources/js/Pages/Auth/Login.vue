<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';
import Checkbox from '@/Components/Checkbox.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';

defineProps({
    canResetPassword: Boolean,
    status: String,
});

const form = useForm({
    email: '',
    password: '',
    remember: false,
});

const submit = () => {
    form.transform(data => ({
        ...data,
        remember: form.remember ? 'on' : '',
    })).post(route('login'), {
        onFinish: () => form.reset('password'),
    });
};
</script>

<template>
    <Head title="Acceso" />

    <div class="min-h-screen bg-[#07111f] text-white relative overflow-hidden">
        <div class="absolute inset-0 bg-[radial-gradient(circle_at_top_left,_rgba(56,189,248,0.28),_transparent_35%),radial-gradient(circle_at_bottom_right,_rgba(34,197,94,0.18),_transparent_30%),linear-gradient(135deg,_#07111f_0%,_#0f172a_55%,_#111827_100%)]"></div>
        <div class="absolute inset-0 opacity-20 bg-[linear-gradient(rgba(255,255,255,0.08)_1px,transparent_1px),linear-gradient(90deg,rgba(255,255,255,0.08)_1px,transparent_1px)] bg-[size:48px_48px]"></div>

        <div class="relative min-h-screen flex">
            <section class="hidden lg:flex lg:w-1/2 xl:w-5/12 flex-col justify-between p-10 xl:p-14 border-r border-white/10">
                <div>
                    <div class="inline-flex items-center gap-3 rounded-full border border-cyan-400/30 bg-white/5 px-4 py-2 text-xs font-semibold uppercase tracking-[0.28em] text-cyan-200">
                        Reservas Salas
                    </div>
                    <h1 class="mt-8 max-w-xl text-5xl xl:text-6xl font-black leading-[0.95] tracking-tight text-white">
                        Gestiona espacios, reservas y acceso con una sola sesión.
                    </h1>
                    <p class="mt-6 max-w-lg text-base xl:text-lg leading-8 text-slate-300">
                        Entra con tus credenciales, valida Sanctum y administra el flujo completo desde el panel y la API.
                    </p>
                </div>

                <div class="grid grid-cols-2 gap-4 max-w-xl">
                    <div class="rounded-2xl border border-white/10 bg-white/6 backdrop-blur-md p-4">
                        <div class="text-2xl font-bold text-cyan-300">CRUD</div>
                        <div class="mt-1 text-sm text-slate-300">Espacios y reservas vía API</div>
                    </div>
                    <div class="rounded-2xl border border-white/10 bg-white/6 backdrop-blur-md p-4">
                        <div class="text-2xl font-bold text-emerald-300">Sanctum</div>
                        <div class="mt-1 text-sm text-slate-300">Sesión web y token listos</div>
                    </div>
                </div>
            </section>

            <section class="w-full lg:w-1/2 xl:w-7/12 flex items-center justify-center px-4 sm:px-8 py-10">
                <div class="w-full max-w-md rounded-3xl border border-white/10 bg-slate-950/70 backdrop-blur-xl shadow-[0_30px_100px_rgba(0,0,0,0.45)] p-8 sm:p-10">
                    <div class="mb-8 lg:hidden">
                        <div class="text-xs uppercase tracking-[0.3em] text-cyan-200">Reservas Salas</div>
                        <h2 class="mt-2 text-3xl font-black text-white">Inicia sesión</h2>
                        <p class="mt-2 text-sm text-slate-400">Acceso web compatible con Sanctum.</p>
                    </div>

                    <div v-if="status" class="mb-4 rounded-xl border border-emerald-400/20 bg-emerald-400/10 px-4 py-3 text-sm text-emerald-200">
                        {{ status }}
                    </div>

                    <form @submit.prevent="submit" class="space-y-5">
                        <div>
                            <InputLabel for="email" value="Correo electrónico" class="text-slate-200" />
                            <TextInput
                                id="email"
                                v-model="form.email"
                                type="email"
                                class="mt-1 block w-full rounded-2xl border-white/10 bg-white/5 text-white placeholder:text-slate-500 focus:border-cyan-400 focus:ring-cyan-400"
                                required
                                autofocus
                                autocomplete="username"
                                placeholder="tu@correo.com"
                            />
                            <InputError class="mt-2" :message="form.errors.email" />
                        </div>

                        <div>
                            <InputLabel for="password" value="Contraseña" class="text-slate-200" />
                            <TextInput
                                id="password"
                                v-model="form.password"
                                type="password"
                                class="mt-1 block w-full rounded-2xl border-white/10 bg-white/5 text-white placeholder:text-slate-500 focus:border-cyan-400 focus:ring-cyan-400"
                                required
                                autocomplete="current-password"
                                placeholder="••••••••"
                            />
                            <InputError class="mt-2" :message="form.errors.password" />
                        </div>

                        <div class="flex items-center justify-between gap-4">
                            <label class="flex items-center">
                                <Checkbox v-model:checked="form.remember" name="remember" />
                                <span class="ms-2 text-sm text-slate-300">Recordarme</span>
                            </label>

                            <Link
                                v-if="canResetPassword"
                                :href="route('password.request')"
                                class="text-sm font-medium text-cyan-300 hover:text-cyan-200"
                            >
                                ¿Olvidaste tu contraseña?
                            </Link>
                        </div>

                        <PrimaryButton
                            class="w-full justify-center rounded-2xl bg-cyan-500 px-4 py-3 text-sm font-semibold text-slate-950 hover:bg-cyan-400"
                            :class="{ 'opacity-25': form.processing }"
                            :disabled="form.processing"
                        >
                            {{ form.processing ? 'Ingresando...' : 'Entrar al panel' }}
                        </PrimaryButton>
                    </form>
                </div>
            </section>
        </div>
    </div>
</template>
