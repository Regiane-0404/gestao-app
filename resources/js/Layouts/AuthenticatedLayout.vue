<script setup>
import Sidebar from '@/Components/Sidebar.vue'
import { Head, Link, usePage } from '@inertiajs/vue3'
import Dropdown from '@/Components/Dropdown.vue'
import DropdownLink from '@/Components/DropdownLink.vue'
import { ref, watch } from 'vue' // <-- ESTA LINHA ESTAVA EM FALTA OU INCOMPLETA

const page = usePage()

const showFlash = ref(false)
const flashMessage = ref('')
const flashKey = ref(0)

watch(
    () => page.props.flash,
    (flash) => {
        if (flash && flash.success) {
            flashMessage.value = flash.success
            showFlash.value = true
            flashKey.value += 1

            setTimeout(() => {
                showFlash.value = false
            }, 4000)
        }
    },
    { deep: true }
)
</script>

<template>
    <div class="flex min-h-screen bg-gray-100">
        <Sidebar />

        <div class="flex-1 flex flex-col relative">
            <!-- --- INÍCIO DA CORREÇÃO --- -->
            <!-- Usamos :key para garantir que a transição é reiniciada a cada nova mensagem -->
            <Transition
                enter-active-class="transition ease-out duration-300"
                enter-from-class="transform opacity-0 translate-x-full"
                enter-to-class="transform opacity-100 translate-x-0"
                leave-active-class="transition ease-in duration-300"
                leave-from-class="transform opacity-100 translate-x-0"
                leave-to-class="transform opacity-0 translate-x-full"
            >
                <div
                    v-if="showFlash"
                    :key="flashKey"
                    class="absolute top-5 right-5 z-50 bg-green-500 text-white text-sm font-medium px-4 py-2 rounded-md shadow-lg"
                >
                    {{ flashMessage }}
                </div>
            </Transition>
            <!-- --- FIM DA CORREÇÃO --- -->

            <header
                class="bg-white shadow-sm flex items-center justify-between px-6 py-4"
            >
                <div v-if="$slots.header">
                    <slot name="header" />
                </div>

                <div class="flex items-center">
                    <Dropdown align="right" width="48">
                        <template #trigger>
                            <span class="inline-flex rounded-md">
                                <button
                                    type="button"
                                    class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 bg-white hover:text-gray-700 focus:outline-none transition ease-in-out duration-150"
                                >
                                    {{ $page.props.auth.user.name }}
                                    <svg
                                        class="ms-2 -me-0.5 h-4 w-4"
                                        xmlns="http://www.w3.org/2000/svg"
                                        viewBox="0 0 20 20"
                                        fill="currentColor"
                                    >
                                        <path
                                            fill-rule="evenodd"
                                            d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                                            clip-rule="evenodd"
                                        />
                                    </svg>
                                </button>
                            </span>
                        </template>

                        <template #content>
                            <DropdownLink :href="route('profile.edit')">
                                Perfil
                            </DropdownLink>
                            <DropdownLink
                                :href="route('logout')"
                                method="post"
                                as="button"
                            >
                                Log Out
                            </DropdownLink>
                        </template>
                    </Dropdown>
                </div>
            </header>

            <main class="flex-1 p-6">
                <slot />
            </main>
        </div>
    </div>
</template>
