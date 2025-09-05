<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import { Head, Link, router } from '@inertiajs/vue3'
import { ref, watch } from 'vue'
import { MoreVertical, Search, Trash2 } from 'lucide-vue-next'
import { Input } from '@/Components/ui/input'

// --- INÍCIO DA CORREÇÃO ---
// Esta é a única linha que precisa de ser diferente do seu código do Git
import Button from '@/Components/ui/button/Button.vue'
// --- FIM DA CORREÇÃO ---

import {
    DropdownMenu,
    DropdownMenuContent,
    DropdownMenuItem,
    DropdownMenuSeparator,
    DropdownMenuTrigger,
} from '@/Components/ui/dropdown-menu'

const props = defineProps({
    entidades: Object,
    filters: Object,
    pageTitle: String,
    sourceRoute: String,
})

const search = ref(props.filters.search)

let searchTimeout = null
watch(search, (newValue) => {
    clearTimeout(searchTimeout)
    searchTimeout = setTimeout(() => {
        router.get(
            route(props.sourceRoute),
            { search: newValue },
            {
                preserveState: true,
                replace: true,
            }
        )
    }, 300)
})

const editEntidade = (entidadeId) => {
    const editRouteName = props.sourceRoute.replace('.index', '.edit')
    const paramName = props.sourceRoute.includes('clientes')
        ? 'cliente'
        : 'fornecedor'
    router.get(route(editRouteName, { [paramName]: entidadeId }))
}

const confirmDelete = (entidadeId) => {
    if (
        confirm(
            'Tem a certeza que deseja eliminar esta entidade? Esta ação não pode ser revertida.'
        )
    ) {
        const destroyRouteName = props.sourceRoute.replace('.index', '.destroy')
        const paramName = props.sourceRoute.includes('clientes')
            ? 'cliente'
            : 'fornecedor'

        router.delete(route(destroyRouteName, { [paramName]: entidadeId }), {
            preserveScroll: true,
        })
    }
}
</script>

<template>
    <Head :title="pageTitle.replace('Lista de ', '')" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-bold text-2xl text-gray-800 leading-tight">
                {{ pageTitle }}
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div
                    class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6"
                >
                    <div class="flex justify-between items-center mb-6">
                        <div class="relative w-full max-w-sm">
                            <Search
                                class="absolute left-2.5 top-2.5 h-4 w-4 text-gray-400"
                            />
                            <Input
                                v-model="search"
                                type="text"
                                placeholder="Pesquisar por nome, NIF ou NIC..."
                                class="pl-9 w-full placeholder:text-gray-400"
                            />
                        </div>

                        <Link
                            :href="
                                route(sourceRoute.replace('.index', '.create'))
                            "
                        >
                            <Button>
                                Adicionar
                                {{
                                    sourceRoute.includes('clientes')
                                        ? 'Cliente'
                                        : 'Fornecedor'
                                }}
                            </Button>
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
                                    Nº Contribuinte
                                </th>
                                <th
                                    class="py-3 px-4 font-medium border-b border-gray-200"
                                >
                                    Email
                                </th>
                                <th
                                    class="py-3 px-4 font-medium border-b border-gray-200"
                                >
                                    Telefone
                                </th>
                                <th
                                    class="py-3 px-4 font-medium border-b border-gray-200 text-right"
                                >
                                    Ações
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-if="entidades.data.length === 0">
                                <td
                                    colspan="5"
                                    class="text-center py-6 text-gray-500"
                                >
                                    Nenhuma entidade encontrada.
                                </td>
                            </tr>
                            <tr
                                v-for="entidade in entidades.data"
                                :key="entidade.id"
                                class="hover:bg-gray-50 border-b border-gray-200"
                            >
                                <td class="py-3 px-4">{{ entidade.nome }}</td>
                                <td class="py-3 px-4">
                                    {{ entidade.nif || entidade.nic }}
                                </td>
                                <td class="py-3 px-4">{{ entidade.email }}</td>
                                <td class="py-3 px-4">
                                    {{ entidade.telemovel }}
                                </td>
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
                                                    editEntidade(entidade.id)
                                                "
                                                class="cursor-pointer"
                                            >
                                                Editar
                                            </DropdownMenuItem>
                                            <DropdownMenuSeparator />
                                            <DropdownMenuItem
                                                @select="
                                                    confirmDelete(entidade.id)
                                                "
                                                class="cursor-pointer text-red-600 focus:text-red-600 focus:bg-red-50"
                                            >
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
