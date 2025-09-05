<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import { Head, Link, router } from '@inertiajs/vue3'
import { MoreVertical, Trash2 } from 'lucide-vue-next'
import Button from '@/Components/ui/button/Button.vue'
import {
    DropdownMenu,
    DropdownMenuContent,
    DropdownMenuItem,
    DropdownMenuSeparator,
    DropdownMenuTrigger,
} from '@/Components/ui/dropdown-menu'

const props = defineProps({
    artigos: Object,
    filters: Object,
})

const formatCurrency = (value) => {
    return new Intl.NumberFormat('pt-PT', {
        style: 'currency',
        currency: 'EUR',
    }).format(value)
}

const editArtigo = (artigoId) => {
    router.get(route('configuracoes.artigos.edit', artigoId))
}

const confirmDelete = (artigoId) => {
    if (confirm('Tem a certeza que deseja eliminar este artigo?')) {
        router.delete(route('configuracoes.artigos.destroy', artigoId), {
            preserveScroll: true,
        })
    }
}
</script>

<template>
    <Head title="Artigos" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-bold text-2xl text-gray-800 leading-tight">
                Configurações - Artigos
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div
                    class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6"
                >
                    <div class="flex justify-between items-center mb-6">
                        <div></div>
                        <Link :href="route('configuracoes.artigos.create')">
                            <Button>Adicionar Artigo</Button>
                        </Link>
                    </div>

                    <table class="min-w-full bg-white">
                        <thead class="text-left text-gray-600">
                            <tr>
                                <th
                                    class="py-3 px-4 font-medium border-b border-gray-200"
                                >
                                    Referência
                                </th>
                                <th
                                    class="py-3 px-4 font-medium border-b border-gray-200"
                                >
                                    Nome
                                </th>
                                <th
                                    class="py-3 px-4 font-medium border-b border-gray-200"
                                >
                                    Preço
                                </th>
                                <th
                                    class="py-3 px-4 font-medium border-b border-gray-200"
                                >
                                    IVA
                                </th>
                                <th
                                    class="py-3 px-4 font-medium border-b border-gray-200 text-right"
                                >
                                    Ações
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-if="artigos.data.length === 0">
                                <td
                                    colspan="5"
                                    class="text-center py-6 text-gray-500"
                                >
                                    Nenhum artigo encontrado.
                                </td>
                            </tr>
                            <tr
                                v-for="artigo in artigos.data"
                                :key="artigo.id"
                                class="hover:bg-gray-50 border-b border-gray-200"
                            >
                                <td class="py-3 px-4 font-mono text-sm">
                                    {{ artigo.referencia }}
                                </td>
                                <td class="py-3 px-4">{{ artigo.nome }}</td>
                                <td class="py-3 px-4">
                                    {{ formatCurrency(artigo.preco) }}
                                </td>
                                <td class="py-3 px-4">
                                    {{
                                        artigo.iva
                                            ? `${parseInt(artigo.iva.taxa)}%`
                                            : 'N/A'
                                    }}
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
                                                @select="editArtigo(artigo.id)"
                                                class="cursor-pointer"
                                            >
                                                Editar
                                            </DropdownMenuItem>
                                            <DropdownMenuSeparator />
                                            <DropdownMenuItem
                                                @select="
                                                    confirmDelete(artigo.id)
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
