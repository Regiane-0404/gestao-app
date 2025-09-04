<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import { Head, Link } from '@inertiajs/vue3'

// Recebemos os artigos que o controlador enviou
const props = defineProps({
    artigos: Object,
    filters: Object,
})

// Função para formatar o preço como moeda
const formatCurrency = (value) => {
    return new Intl.NumberFormat('pt-PT', {
        style: 'currency',
        currency: 'EUR',
    }).format(value)
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
                        <!-- (Espaço para a barra de pesquisa futura) -->
                        <div></div>

                        <!-- Botão Adicionar Artigo -->
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
                                    {{ artigo.iva?.taxa || 'N/A' }}%
                                </td>
                                <td class="py-3 px-4 text-right">
                                    <!-- (Espaço para o menu de ações futuro) -->
                                </td>
                            </tr>
                        </tbody>
                    </table>

                    <div
                        v-if="artigos.links.length > 3"
                        class="mt-6 flex justify-center"
                    >
                        <!-- (Paginação virá aqui) -->
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
