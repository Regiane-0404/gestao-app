<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import { Head, Link, router } from '@inertiajs/vue3'
import Button from '@/Components/ui/button/Button.vue'
import { MoreVertical, Trash2, Undo2 } from 'lucide-vue-next'
import {
    DropdownMenu,
    DropdownMenuContent,
    DropdownMenuItem,
    DropdownMenuSeparator,
    DropdownMenuTrigger,
} from '@/Components/ui/dropdown-menu'
import { Badge } from '@/Components/ui/badge'

const props = defineProps({
    funcoes: Object,
    funcoesArquivadas: Array,
})

const editFuncao = (funcaoId) => {
    router.get(route('configuracoes.contactos.funcoes.edit', funcaoId))
}

const confirmDelete = (funcaoId) => {
    if (confirm('Tem a certeza que deseja arquivar esta função?')) {
        router.delete(
            route('configuracoes.contactos.funcoes.destroy', funcaoId),
            {
                preserveScroll: true,
            }
        )
    }
}

const restoreFuncao = (funcaoId) => {
    if (confirm('Tem a certeza que deseja restaurar esta função?')) {
        router.post(
            route('configuracoes.contactos.funcoes.restore', funcaoId),
            {},
            {
                preserveScroll: true,
            }
        )
    }
}
</script>

<template>
    <Head title="Funções de Contacto" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-bold text-2xl text-gray-800 leading-tight">
                Configurações - Funções de Contacto
            </h2>
        </template>
        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <!-- Tabela de Funções Ativas -->
                <div
                    class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6"
                >
                    <div class="flex justify-between items-center mb-6">
                        <h3 class="text-lg font-semibold text-gray-700">
                            Funções Ativas
                        </h3>
                        <Link
                            :href="
                                route('configuracoes.contactos.funcoes.create')
                            "
                        >
                            <Button>Adicionar Função</Button>
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
                                    Estado
                                </th>
                                <th
                                    class="py-3 px-4 font-medium border-b border-gray-200 text-right"
                                >
                                    Ações
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-if="funcoes.data.length === 0">
                                <td
                                    colspan="3"
                                    class="text-center py-6 text-gray-500"
                                >
                                    Nenhuma função encontrada.
                                </td>
                            </tr>
                            <tr
                                v-for="funcao in funcoes.data"
                                :key="funcao.id"
                                class="hover:bg-gray-50 border-b border-gray-200"
                            >
                                <td class="py-3 px-4">{{ funcao.nome }}</td>
                                <td class="py-3 px-4">
                                    <Badge
                                        :variant="
                                            funcao.estado === 'ativo'
                                                ? 'default'
                                                : 'destructive'
                                        "
                                    >
                                        {{ funcao.estado }}
                                    </Badge>
                                </td>
                                <td class="py-3 px-4 text-right">
                                    <DropdownMenu>
                                        <DropdownMenuTrigger as-child>
                                            <Button
                                                variant="ghost"
                                                class="w-8 h-8 p-0"
                                                ><MoreVertical class="w-4 h-4"
                                            /></Button>
                                        </DropdownMenuTrigger>
                                        <DropdownMenuContent align="end">
                                            <DropdownMenuItem
                                                @select="editFuncao(funcao.id)"
                                                class="cursor-pointer"
                                                >Editar</DropdownMenuItem
                                            >
                                            <DropdownMenuSeparator />
                                            <DropdownMenuItem
                                                @select="
                                                    confirmDelete(funcao.id)
                                                "
                                                class="cursor-pointer text-red-600 focus:text-red-600"
                                            >
                                                <Trash2 class="w-4 h-4 mr-2" />
                                                Arquivar
                                            </DropdownMenuItem>
                                        </DropdownMenuContent>
                                    </DropdownMenu>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Tabela de Funções Arquivadas -->
                <div
                    v-if="funcoesArquivadas && funcoesArquivadas.length > 0"
                    class="mt-12"
                >
                    <h3 class="text-lg font-semibold text-gray-700 mb-4">
                        Funções Arquivadas
                    </h3>
                    <div
                        class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6"
                    >
                        <table class="min-w-full bg-white">
                            <thead class="text-left text-gray-600">
                                <tr>
                                    <th
                                        class="py-3 px-4 font-medium border-b border-gray-200"
                                    >
                                        Nome
                                    </th>
                                    <th
                                        class="py-3 px-4 font-medium border-b border-gray-200 text-right"
                                    >
                                        Ações
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr
                                    v-for="funcao in funcoesArquivadas"
                                    :key="funcao.id"
                                    class="hover:bg-gray-50 border-b border-gray-200"
                                >
                                    <td class="py-3 px-4">{{ funcao.nome }}</td>
                                    <td class="py-3 px-4 text-right">
                                        <Button
                                            @click="restoreFuncao(funcao.id)"
                                            variant="outline"
                                        >
                                            <Undo2 class="w-4 h-4 mr-2" />
                                            Restaurar
                                        </Button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
