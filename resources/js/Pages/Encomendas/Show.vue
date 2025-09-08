<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import { Head, Link } from '@inertiajs/vue3'
import { Badge } from '@/Components/ui/badge'
import Button from '@/Components/ui/button/Button.vue'

const props = defineProps({
    encomenda: Object,
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
</script>

<template>
    <Head :title="`Detalhes da Encomenda #${encomenda.id}`" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-bold text-2xl text-gray-800 leading-tight">
                Detalhes da Encomenda #{{ encomenda.id }}
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white shadow-sm sm:rounded-lg p-6 space-y-6">
                    <!-- Resumo da encomenda -->
                    <div
                        class="grid grid-cols-1 md:grid-cols-4 gap-6 border-b pb-6"
                    >
                        <div>
                            <h3 class="text-sm font-medium text-gray-500">
                                Cliente
                            </h3>
                            <p class="text-lg font-semibold text-gray-900">
                                {{ encomenda.cliente?.nome || 'N/A' }}
                            </p>
                        </div>
                        <div v-if="encomenda.proposta">
                            <h3 class="text-sm font-medium text-gray-500">
                                Origem da Proposta
                            </h3>
                            <Link
                                :href="
                                    route(
                                        'propostas.edit',
                                        encomenda.proposta.id
                                    )
                                "
                                class="text-lg font-semibold text-blue-600 hover:underline"
                            >
                                #{{ encomenda.proposta.id }}
                            </Link>
                        </div>
                        <div>
                            <h3 class="text-sm font-medium text-gray-500">
                                Data da Encomenda
                            </h3>
                            <p class="text-lg font-semibold text-gray-900">
                                {{ formatDate(encomenda.data_encomenda) }}
                            </p>
                        </div>
                        <div>
                            <h3 class="text-sm font-medium text-gray-500">
                                Estado
                            </h3>
                            <Badge
                                :variant="
                                    encomenda.estado === 'rascunho'
                                        ? 'outline'
                                        : 'default'
                                "
                                >{{ encomenda.estado }}</Badge
                            >
                        </div>
                    </div>

                    <!-- Área para artigos -->
                    <div class="pt-4">
                        <h3 class="text-xl font-semibold text-gray-800 mb-3">
                            Artigos da Encomenda
                        </h3>
                        <div class="border rounded-lg overflow-hidden">
                            <table class="min-w-full">
                                <thead
                                    class="bg-gray-50 text-left text-sm text-gray-600"
                                >
                                    <tr>
                                        <th class="p-2 font-medium">
                                            Referência
                                        </th>
                                        <th class="p-2 font-medium">
                                            Descrição
                                        </th>
                                        <th class="p-2 font-medium">Qtd.</th>
                                        <th class="p-2 font-medium">
                                            Preço Unit.
                                        </th>
                                        <th class="p-2 font-medium">Total</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y">
                                    <tr
                                        v-for="linha in encomenda.linhas"
                                        :key="linha.id"
                                    >
                                        <td class="p-2 font-mono text-xs">
                                            {{ linha.referencia }}
                                        </td>
                                        <td class="p-2 text-sm">
                                            {{ linha.descricao }}
                                        </td>
                                        <td class="p-2 text-sm">
                                            {{ linha.quantidade }}
                                        </td>
                                        <td class="p-2 text-sm">
                                            {{
                                                formatCurrency(
                                                    linha.preco_unitario
                                                )
                                            }}
                                        </td>
                                        <td class="p-2 text-sm font-semibold">
                                            {{
                                                formatCurrency(
                                                    linha.quantidade *
                                                        linha.preco_unitario *
                                                        (1 +
                                                            linha.taxa_iva /
                                                                100)
                                                )
                                            }}
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <div class="flex justify-end pt-6 border-t space-x-2">
                        <Link
                            :href="
                                route('propostas.edit', encomenda.proposta.id)
                            "
                            v-if="encomenda.proposta"
                        >
                            <Button variant="outline">Voltar à Proposta</Button>
                        </Link>
                        <Link :href="route('encomendas.index')">
                            <Button variant="outline">Voltar à Lista</Button>
                        </Link>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
