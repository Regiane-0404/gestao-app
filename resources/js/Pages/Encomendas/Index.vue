<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import { Head, Link, router } from '@inertiajs/vue3'
import { Badge } from '@/Components/ui/badge'
import Button from '@/Components/ui/button/Button.vue'
// --- INÍCIO DA CORREÇÃO ---
import { MoreVertical, Trash2 } from 'lucide-vue-next'
import {
    DropdownMenu,
    DropdownMenuContent,
    DropdownMenuItem,
    DropdownMenuSeparator,
    DropdownMenuTrigger,
} from '@/Components/ui/dropdown-menu'
// --- FIM DA CORREÇÃO ---

const props = defineProps({
    encomendas: Object,
})

const formatCurrency = (value) => {
    return new Intl.NumberFormat('pt-PT', {
        style: 'currency',
        currency: 'EUR',
    }).format(value)
}

const formatDate = (dateString) => {
    if (!dateString) return 'N/A'
    const date = new Date(dateString)
    return date.toLocaleDateString('pt-PT', {
        year: 'numeric',
        month: '2-digit',
        day: '2-digit',
    })
}

// --- INÍCIO DA CORREÇÃO ---
const showEncomenda = (encomendaId) => {
    router.get(route('encomendas.show', encomendaId))
}

const confirmDelete = (encomendaId) => {
    if (confirm('Tem a certeza que deseja eliminar esta encomenda?')) {
        router.delete(route('encomendas.destroy', encomendaId), {
            preserveScroll: true,
        })
    }
}
// --- FIM DA CORREÇÃO ---
</script>

<template>
    <Head title="Encomendas" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-bold text-2xl text-gray-800 leading-tight">
                Lista de Encomendas
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div
                    class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6"
                >
                    <div class="flex justify-end mb-6">
                        <!-- Botão para criar encomenda diretamente virá aqui no futuro -->
                    </div>

                    <table class="min-w-full bg-white">
                        <thead class="text-left text-gray-600">
                            <tr>
                                <th
                                    class="py-3 px-4 font-medium border-b border-gray-200"
                                >
                                    Número
                                </th>
                                <th
                                    class="py-3 px-4 font-medium border-b border-gray-200"
                                >
                                    Data
                                </th>
                                <th
                                    class="py-3 px-4 font-medium border-b border-gray-200"
                                >
                                    Cliente
                                </th>
                                <th
                                    class="py-3 px-4 font-medium border-b border-gray-200"
                                >
                                    Valor Total
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
                            <tr v-if="encomendas.data.length === 0">
                                <td
                                    colspan="6"
                                    class="text-center py-6 text-gray-500"
                                >
                                    Nenhuma encomenda encontrada.
                                </td>
                            </tr>
                            <tr
                                v-for="encomenda in encomendas.data"
                                :key="encomenda.id"
                                class="hover:bg-gray-50 border-b border-gray-200"
                            >
                                <td class="py-3 px-4 font-mono text-sm">
                                    #{{ encomenda.id }}
                                </td>
                                <td class="py-3 px-4">
                                    {{ formatDate(encomenda.data_encomenda) }}
                                </td>
                                <td class="py-3 px-4">
                                    {{ encomenda.cliente?.nome || 'N/A' }}
                                </td>
                                <td class="py-3 px-4">
                                    {{ formatCurrency(encomenda.valor_total) }}
                                </td>
                                <td class="py-3 px-4">
                                    <Badge
                                        :variant="
                                            encomenda.estado === 'rascunho'
                                                ? 'outline'
                                                : 'default'
                                        "
                                    >
                                        {{ encomenda.estado }}
                                    </Badge>
                                </td>
                                <!-- --- INÍCIO DA CORREÇÃO --- -->
                                <td class="py-3 px-4 text-right">
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
                                                @select="
                                                    showEncomenda(encomenda.id)
                                                "
                                                class="cursor-pointer"
                                            >
                                                Ver Detalhes
                                            </DropdownMenuItem>
                                            <DropdownMenuSeparator />
                                            <DropdownMenuItem
                                                @select="
                                                    confirmDelete(encomenda.id)
                                                "
                                                class="cursor-pointer text-red-600 focus:text-red-600 focus:bg-red-50"
                                            >
                                                <Trash2 class="w-4 h-4 mr-2" />
                                                Eliminar
                                            </DropdownMenuItem>
                                        </DropdownMenuContent>
                                    </DropdownMenu>
                                </td>
                                <!-- --- FIM DA CORREÇÃO --- -->
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
