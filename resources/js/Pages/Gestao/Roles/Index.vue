<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import { Head, Link, router } from '@inertiajs/vue3'
import Button from '@/Components/ui/button/Button.vue'
// --- INÍCIO DA ALTERAÇÃO ---
import { MoreVertical, Trash2 } from 'lucide-vue-next'
import {
    DropdownMenu,
    DropdownMenuContent,
    DropdownMenuItem,
    DropdownMenuSeparator,
    DropdownMenuTrigger,
} from '@/Components/ui/dropdown-menu'
// --- FIM DA ALTERAÇÃO ---

const props = defineProps({
    roles: Object,
})

// --- INÍCIO DA ALTERAÇÃO ---
const editRole = (roleId) => {
    router.get(route('gestao.roles.edit', roleId))
}

const confirmDelete = (roleId) => {
    if (
        confirm(
            'Tem a certeza que deseja eliminar este grupo? Todos os utilizadores associados perderão estas permissões.'
        )
    ) {
        router.delete(route('gestao.roles.destroy', roleId), {
            preserveScroll: true,
        })
    }
}
// --- FIM DA ALTERAÇÃO ---
</script>

<template>
    <Head title="Grupos de Permissões" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-bold text-2xl text-gray-800 leading-tight">
                Gestão de Acessos - Grupos de Permissões
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div
                    class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6"
                >
                    <div class="flex justify-end mb-6">
                        <Link :href="route('gestao.roles.create')">
                            <Button>Adicionar Grupo</Button>
                        </Link>
                    </div>

                    <table class="min-w-full bg-white">
                        <thead class="text-left text-gray-600">
                            <tr>
                                <th
                                    class="py-3 px-4 font-medium border-b border-gray-200"
                                >
                                    Nome do Grupo
                                </th>
                                <th
                                    class="py-3 px-4 font-medium border-b border-gray-200"
                                >
                                    Utilizadores Associados
                                </th>
                                <th
                                    class="py-3 px-4 font-medium border-b border-gray-200 text-right"
                                >
                                    Ações
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-if="roles.data.length === 0">
                                <td
                                    colspan="3"
                                    class="text-center py-6 text-gray-500"
                                >
                                    Nenhum grupo de permissões encontrado.
                                </td>
                            </tr>
                            <tr
                                v-for="role in roles.data"
                                :key="role.id"
                                class="hover:bg-gray-50 border-b border-gray-200"
                            >
                                <td class="py-3 px-4">{{ role.name }}</td>
                                <td class="py-3 px-4">
                                    {{ role.users_count }}
                                </td>
                                <td class="py-3 px-4 text-right">
                                    <!-- --- INÍCIO DA ALTERAÇÃO --- -->
                                    <DropdownMenu>
                                        <DropdownMenuTrigger as-child>
                                            <Button
                                                variant="ghost"
                                                class="w-8 h-8 p-0"
                                            >
                                                <MoreVertical class="w-4 h-4" />
                                            </Button>
                                        </DropdownMenuTrigger>
                                        <DropdownMenuContent align="end">
                                            <DropdownMenuItem
                                                @select="editRole(role.id)"
                                                class="cursor-pointer"
                                            >
                                                Editar
                                            </DropdownMenuItem>
                                            <DropdownMenuSeparator />
                                            <DropdownMenuItem
                                                @select="confirmDelete(role.id)"
                                                class="cursor-pointer text-red-600 focus:text-red-600 focus:bg-red-50"
                                            >
                                                <Trash2 class="w-4 h-4 mr-2" />
                                                Eliminar
                                            </DropdownMenuItem>
                                        </DropdownMenuContent>
                                    </DropdownMenu>
                                    <!-- --- FIM DA ALTERAÇÃO --- -->
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
