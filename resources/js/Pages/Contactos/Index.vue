<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import { Head, Link, router } from '@inertiajs/vue3'
import { MoreVertical, Trash2 } from 'lucide-vue-next'
import { Button } from '@/Components/ui/button'
import {
    DropdownMenu,
    DropdownMenuContent,
    DropdownMenuItem,
    DropdownMenuSeparator,
    DropdownMenuTrigger,
} from '@/Components/ui/dropdown-menu'

const props = defineProps({
    contactos: Object,
    filters: Object,
})

// Funções para Editar e Eliminar (a serem implementadas nos próximos passos)
const editContacto = (contactoId) => {
    router.get(route('contactos.edit', contactoId))
}

const confirmDelete = (contactoId) => {
    if (confirm('Tem a certeza que deseja eliminar este contacto?')) {
        router.delete(route('contactos.destroy', contactoId), {
            preserveScroll: true,
        })
    }
}
</script>

<template>
    <Head title="Contactos" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-bold text-2xl text-gray-800 leading-tight">
                Lista de Contactos
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div
                    class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6"
                >
                    <div class="flex justify-between items-center mb-6">
                        <!-- (Barra de pesquisa a ser adicionada) -->
                        <div></div>
                        <Link :href="route('contactos.create')">
                            <Button>Adicionar Contacto</Button>
                        </Link>
                    </div>

                    <table class="min-w-full bg-white">
                        <thead class="text-left text-gray-600">
                            <tr>
                                <th
                                    class="py-3 px-4 font-medium border-b border-gray-200"
                                >
                                    Nome
                                </th>
                                <th
                                    class="py-3 px-4 font-medium border-b border-gray-200"
                                >
                                    Função
                                </th>
                                <th
                                    class="py-3 px-4 font-medium border-b border-gray-200"
                                >
                                    Entidade
                                </th>
                                <th
                                    class="py-3 px-4 font-medium border-b border-gray-200"
                                >
                                    Telemóvel
                                </th>
                                <th
                                    class="py-3 px-4 font-medium border-b border-gray-200"
                                >
                                    Email
                                </th>
                                <th
                                    class="py-3 px-4 font-medium border-b border-gray-200 text-right"
                                >
                                    Ações
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-if="contactos.data.length === 0">
                                <td
                                    colspan="6"
                                    class="text-center py-6 text-gray-500"
                                >
                                    Nenhum contacto encontrado.
                                </td>
                            </tr>
                            <tr
                                v-for="contacto in contactos.data"
                                :key="contacto.id"
                                class="hover:bg-gray-50 border-b border-gray-200"
                            >
                                <td class="py-3 px-4">
                                    {{ contacto.nome }} {{ contacto.apelido }}
                                </td>
                                <td class="py-3 px-4">
                                    {{ contacto.funcao?.nome || 'N/A' }}
                                </td>
                                <td class="py-3 px-4">
                                    {{ contacto.entidade?.nome || 'N/A' }}
                                </td>
                                <td class="py-3 px-4">
                                    {{ contacto.telemovel }}
                                </td>
                                <td class="py-3 px-4">{{ contacto.email }}</td>
                                <!-- INÍCIO DA ALTERAÇÃO -->
                                <td class="py-3 px-4 text-right">
                                    <DropdownMenu>
                                        <DropdownMenuTrigger as-child>
                                            <Button
                                                variant="ghost"
                                                class="w-8 h-8 p-0"
                                            >
                                                <span class="sr-only"
                                                    >Abrir menu</span
                                                >
                                                <MoreVertical class="w-4 h-4" />
                                            </Button>
                                        </DropdownMenuTrigger>
                                        <DropdownMenuContent align="end">
                                            <DropdownMenuItem
                                                @select="
                                                    editContacto(contacto.id)
                                                "
                                                class="cursor-pointer"
                                            >
                                                Editar
                                            </DropdownMenuItem>
                                            <DropdownMenuSeparator />
                                            <DropdownMenuItem
                                                @select="
                                                    confirmDelete(contacto.id)
                                                "
                                                class="cursor-pointer text-red-600 focus:text-red-600 focus:bg-red-50"
                                            >
                                                <Trash2 class="w-4 h-4 mr-2" />
                                                Eliminar
                                            </DropdownMenuItem>
                                        </DropdownMenuContent>
                                    </DropdownMenu>
                                </td>
                                <!-- FIM DA ALTERAÇÃO -->
                            </tr>
                        </tbody>
                    </table>

                    <!-- (Paginação a ser adicionada) -->
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
