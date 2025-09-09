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
    ivas: Object,
    ivasArquivadas: Array,
})

const editIva = (ivaId) => {
    router.get(route('configuracoes.ivas.edit', ivaId))
}

const confirmDelete = (ivaId) => {
    if (confirm('Tem a certeza que deseja arquivar esta taxa de IVA?')) {
        router.delete(route('configuracoes.ivas.destroy', ivaId), {
            preserveScroll: true,
        })
    }
}

const restoreIva = (ivaId) => {
    if (confirm('Tem a certeza que deseja restaurar esta taxa de IVA?')) {
        router.post(
            route('configuracoes.ivas.restore', ivaId),
            {},
            {
                preserveScroll: true,
            }
        )
    }
}
</script>

<template>
    <Head title="Taxas de IVA" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-bold text-2xl text-gray-800 leading-tight">
                Configurações - Taxas de IVA
            </h2>
        </template>
        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <!-- --- INÍCIO DA CORREÇÃO: Tabela de IVA's Ativos --- -->
                <div
                    class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6"
                >
                    <div class="flex justify-between items-center mb-6">
                        <h3 class="text-lg font-semibold text-gray-700">
                            Taxas Ativas
                        </h3>
                        <Link :href="route('configuracoes.ivas.create')">
                            <Button>Adicionar Taxa de IVA</Button>
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
                                    Taxa
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
                            <tr v-if="ivas.data.length === 0">
                                <td
                                    colspan="4"
                                    class="text-center py-6 text-gray-500"
                                >
                                    Nenhuma taxa de IVA encontrada.
                                </td>
                            </tr>
                            <tr
                                v-for="iva in ivas.data"
                                :key="iva.id"
                                class="hover:bg-gray-50 border-b border-gray-200"
                            >
                                <td class="py-3 px-4">{{ iva.nome }}</td>
                                <td class="py-3 px-4">
                                    {{ parseInt(iva.taxa) }}%
                                </td>
                                <td class="py-3 px-4">
                                    <Badge
                                        :variant="
                                            iva.estado === 'ativo'
                                                ? 'default'
                                                : 'destructive'
                                        "
                                    >
                                        {{ iva.estado }}
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
                                                @select="editIva(iva.id)"
                                                class="cursor-pointer"
                                                >Editar</DropdownMenuItem
                                            >
                                            <DropdownMenuSeparator />
                                            <DropdownMenuItem
                                                @select="confirmDelete(iva.id)"
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
                <!-- --- FIM DA CORREÇÃO --- -->

                <!-- Tabela de Itens Arquivados -->
                <div
                    v-if="ivasArquivadas && ivasArquivadas.length > 0"
                    class="mt-12"
                >
                    <h3 class="text-lg font-semibold text-gray-700 mb-4">
                        Taxas de IVA Arquivadas
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
                                        class="py-3 px-4 font-medium border-b border-gray-200"
                                    >
                                        Taxa
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
                                    v-for="iva in ivasArquivadas"
                                    :key="iva.id"
                                    class="hover:bg-gray-50 border-b border-gray-200"
                                >
                                    <td class="py-3 px-4">{{ iva.nome }}</td>
                                    <td class="py-3 px-4">
                                        {{ parseInt(iva.taxa) }}%
                                    </td>
                                    <td class="py-3 px-4 text-right">
                                        <Button
                                            @click="restoreIva(iva.id)"
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
