<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import { Head, Link, router } from '@inertiajs/vue3'
import Button from '@/Components/ui/button/Button.vue'
import { MoreVertical, Trash2 } from 'lucide-vue-next'
import {
    DropdownMenu,
    DropdownMenuContent,
    DropdownMenuItem,
    DropdownMenuSeparator,
    DropdownMenuTrigger,
} from '@/Components/ui/dropdown-menu'

const props = defineProps({
    users: Object,
})

const editUser = (userId) => {
    router.get(route('gestao.utilizadores.edit', userId))
}

const confirmDelete = (userId) => {
    if (confirm('Tem a certeza que deseja eliminar este utilizador?')) {
        router.delete(route('gestao.utilizadores.destroy', userId), {
            preserveScroll: true,
        })
    }
}
</script>

<template>
    <Head title="Utilizadores" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-bold text-2xl text-gray-800 leading-tight">
                Gestão de Acessos - Utilizadores
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <div class="flex justify-end mb-6">
                        <Link :href="route('gestao.utilizadores.create')">
                            <Button>Adicionar Utilizador</Button>
                        </Link>
                    </div>

                    <table class="min-w-full bg-white">
                        <thead class="text-left text-gray-600">
                            <tr>
                                <th class="py-3 px-4 font-medium border-b border-gray-200">Nome</th>
                                <th class="py-3 px-4 font-medium border-b border-gray-200">Email</th>
                                <th class="py-3 px-4 font-medium border-b border-gray-200">Grupo de Permissões</th>
                                <th class="py-3 px-4 font-medium border-b border-gray-200 text-right">Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-if="users.data.length === 0">
                                <td colspan="4" class="text-center py-6 text-gray-500">
                                    Nenhum utilizador encontrado.
                                </td>
                            </tr>
                            <tr v-for="user in users.data" :key="user.id" class="hover:bg-gray-50 border-b border-gray-200">
                                <td class="py-3 px-4">{{ user.name }}</td>
                                <td class="py-3 px-4">{{ user.email }}</td>
                                <td class="py-3 px-4 capitalize">{{ user.roles[0]?.name || 'N/A' }}</td>
                                <td class="py-3 px-4 text-right">
                                    <DropdownMenu>
                                        <DropdownMenuTrigger as-child>
                                            <Button variant="ghost" class="w-8 h-8 p-0">
                                                <MoreVertical class="w-4 h-4" />
                                            </Button>
                                        </DropdownMenuTrigger>
                                        <DropdownMenuContent align="end">
                                            <DropdownMenuItem @select="editUser(user.id)" class="cursor-pointer">
                                                Editar
                                            </DropdownMenuItem>
                                            <DropdownMenuSeparator />
                                            <DropdownMenuItem @select="confirmDelete(user.id)" class="cursor-pointer text-red-600 focus:text-red-600 focus:bg-red-50">
                                                <Trash2 class="w-4 h-4 mr-2" />
                                                Eliminar
                                            </DropdownMenuItem>
                                        </DropdownMenuContent>
                                    </DropdownMenu>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>