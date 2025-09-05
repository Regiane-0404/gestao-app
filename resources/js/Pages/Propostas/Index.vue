<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import { Head, Link } from '@inertiajs/vue3'
import { Badge } from '@/Components/ui/badge'
import Button from '@/Components/ui/button/Button.vue'

const props = defineProps({
    propostas: Object,
    filters: Object,
})

// Função para formatar o preço como moeda
const formatCurrency = (value) => {
    return new Intl.NumberFormat('pt-PT', {
        style: 'currency',
        currency: 'EUR',
    }).format(value)
}

// Função para formatar a data
const formatDate = (dateString) => {
    if (!dateString) return 'N/A'
    const date = new Date(dateString)
    return date.toLocaleDateString('pt-PT', {
        year: 'numeric',
        month: '2-digit',
        day: '2-digit',
    })
}
</script>

<template>
    <Head title="Propostas" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-bold text-2xl text-gray-800 leading-tight">
                Lista de Propostas
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div
                    class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6"
                >
                    <div class="flex justify-between items-center mb-6">
                        <!-- (Espaço para a barra de pesquisa futura) -->
                        <div></div>

                        <Link :href="route('propostas.create')">
                            <Button>Adicionar Proposta</Button>
                        </Link>
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
                                    Validade
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
                            <tr v-if="propostas.data.length === 0">
                                <td
                                    colspan="7"
                                    class="text-center py-6 text-gray-500"
                                >
                                    Nenhuma proposta encontrada.
                                </td>
                            </tr>
                            <tr
                                v-for="proposta in propostas.data"
                                :key="proposta.id"
                                class="hover:bg-gray-50 border-b border-gray-200"
                            >
                                <td class="py-3 px-4 font-mono text-sm">
                                    #{{ proposta.id }}
                                </td>
                                <td class="py-3 px-4">
                                    {{ formatDate(proposta.data_proposta) }}
                                </td>
                                <td class="py-3 px-4">
                                    {{ proposta.cliente?.nome || 'N/A' }}
                                </td>
                                <td class="py-3 px-4">
                                    {{ formatDate(proposta.validade) }}
                                </td>
                                <td class="py-3 px-4">
                                    {{ formatCurrency(proposta.valor_total) }}
                                </td>
                                <td class="py-3 px-4">
                                    <Badge
                                        :variant="
                                            proposta.estado === 'rascunho'
                                                ? 'outline'
                                                : 'default'
                                        "
                                    >
                                        {{ proposta.estado }}
                                    </Badge>
                                </td>
                                <td class="py-3 px-4 text-right">
                                    <!-- (Espaço para o menu de ações futuro) -->
                                </td>
                            </tr>
                        </tbody>
                    </table>

                    <div
                        v-if="propostas.links.length > 3"
                        class="mt-6 flex justify-center"
                    >
                        <!-- (Paginação virá aqui) -->
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
