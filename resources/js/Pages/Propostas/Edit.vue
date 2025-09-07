<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import { Head, Link, router } from '@inertiajs/vue3'
import { Badge } from '@/Components/ui/badge'
import Button from '@/Components/ui/button/Button.vue'
import { Input } from '@/Components/ui/input'
import { ref, watch } from 'vue'
import debounce from 'lodash.debounce'
import axios from 'axios'
import { Trash2 } from 'lucide-vue-next'

const props = defineProps({
    proposta: Object,
})

const searchTerm = ref('')
const searchResults = ref([])
const isSearching = ref(false)
const isSearchFocused = ref(false) // Novo estado para controlar o foco

// Função para pesquisar artigos
const searchArtigos = async () => {
    if (searchTerm.value.length < 2) {
        searchResults.value = []
        return
    }
    isSearching.value = true
    try {
        const response = await axios.get(
            route('configuracoes.artigos.search', { term: searchTerm.value })
        )
        searchResults.value = response.data
    } catch (error) {
        console.error('Erro ao pesquisar artigos:', error)
        searchResults.value = []
    } finally {
        isSearching.value = false
    }
}

watch(searchTerm, debounce(searchArtigos, 300))

// Adicionar artigo à proposta
const addArtigoToProposta = (artigo) => {
    router.post(
        route('propostas.linhas.store', props.proposta.id),
        { artigo_id: artigo.id },
        {
            preserveScroll: true,
            onSuccess: () => {
                searchTerm.value = ''
                searchResults.value = []
                isSearchFocused.value = false // Esconde a lista após o sucesso
            },
        }
    )
}

// --- CORREÇÃO ---
// Remover linha da proposta (deve estar fora da função acima)
const removeLinha = (linhaId) => {
    if (confirm('Tem a certeza que deseja remover este artigo da proposta?')) {
        router.delete(route('propostas.linhas.destroy', linhaId), {
            preserveScroll: true,
        })
    }
}

// Funções de foco da pesquisa
const handleSearchFocus = () => {
    isSearchFocused.value = true
}

const handleSearchBlur = () => {
    setTimeout(() => {
        isSearchFocused.value = false
    }, 200)
}
</script>

<template>
    <Head :title="`Editar Proposta #${proposta.id}`" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-bold text-2xl text-gray-800 leading-tight">
                Editar Proposta #{{ proposta.id }}
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white shadow-sm sm:rounded-lg p-6 space-y-6">
                    <!-- Resumo da proposta -->
                    <div
                        class="grid grid-cols-1 md:grid-cols-3 gap-6 border-b pb-6"
                    >
                        <div>
                            <h3 class="text-sm font-medium text-gray-500">
                                Cliente
                            </h3>
                            <p class="text-lg font-semibold text-gray-900">
                                {{ proposta.cliente?.nome || 'N/A' }}
                            </p>
                        </div>
                        <div>
                            <h3 class="text-sm font-medium text-gray-500">
                                Validade
                            </h3>
                            <p class="text-lg font-semibold text-gray-900">
                                {{
                                    new Date(
                                        proposta.validade
                                    ).toLocaleDateString('pt-PT')
                                }}
                            </p>
                        </div>
                        <div>
                            <h3 class="text-sm font-medium text-gray-500">
                                Estado
                            </h3>
                            <Badge
                                :variant="
                                    proposta.estado === 'rascunho'
                                        ? 'outline'
                                        : 'default'
                                "
                            >
                                {{ proposta.estado }}
                            </Badge>
                        </div>
                    </div>

                    <!-- Área para artigos -->
                    <div class="pt-4">
                        <h3 class="text-xl font-semibold text-gray-800 mb-4">
                            Artigos da Proposta
                        </h3>

                        <!-- Pesquisa -->
                        <div class="relative max-w-md mb-4">
                            <Input
                                v-model="searchTerm"
                                type="text"
                                placeholder="Pesquisar por referência ou nome do artigo..."
                                class="w-full"
                                @focus="handleSearchFocus"
                                @blur="handleSearchBlur"
                            />
                            <!-- Lista de Resultados da Pesquisa -->
                            <div
                                v-if="isSearchFocused && searchTerm.length > 0"
                                class="absolute z-10 w-full bg-white border rounded-md mt-1 shadow-lg"
                            >
                                <div
                                    v-if="isSearching"
                                    class="p-2 text-sm text-gray-500"
                                >
                                    A pesquisar...
                                </div>
                                <div v-else-if="searchResults.length > 0">
                                    <ul>
                                        <li
                                            v-for="artigo in searchResults"
                                            :key="artigo.id"
                                            @click="addArtigoToProposta(artigo)"
                                            class="p-2 hover:bg-gray-100 cursor-pointer text-sm"
                                        >
                                            <span class="font-bold">{{
                                                artigo.referencia
                                            }}</span>
                                            - {{ artigo.nome }}
                                        </li>
                                    </ul>
                                </div>
                                <div v-else class="p-2 text-sm text-gray-500">
                                    Nenhum artigo encontrado.
                                </div>
                            </div>
                        </div>

                        <!-- Tabela de Linhas Adicionadas -->
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
                                        <th class="p-2 font-medium">IVA (%)</th>
                                        <th class="p-2 font-medium">Total</th>
                                        <th class="p-2 font-medium">Ações</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y">
                                    <tr v-if="proposta.linhas.length === 0">
                                        <td
                                            colspan="7"
                                            class="p-4 text-center text-gray-500"
                                        >
                                            Nenhum artigo adicionado a esta
                                            proposta.
                                        </td>
                                    </tr>
                                    <tr
                                        v-for="linha in proposta.linhas"
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
                                            {{ linha.preco_unitario }}€
                                        </td>
                                        <td class="p-2 text-sm">
                                            {{ parseInt(linha.taxa_iva) }}%
                                        </td>
                                        <td class="p-2 text-sm font-semibold">
                                            {{
                                                (
                                                    linha.quantidade *
                                                    linha.preco_unitario *
                                                    (1 + linha.taxa_iva / 100)
                                                ).toFixed(2)
                                            }}€
                                        </td>
                                        <td class="p-2 text-center">
                                            <Button
                                                @click="removeLinha(linha.id)"
                                                variant="ghost"
                                                size="icon"
                                                class="h-8 w-8 text-red-500 hover:text-red-600"
                                            >
                                                <Trash2 class="h-4 w-4" />
                                            </Button>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <div class="flex justify-end pt-6 border-t">
                        <Link :href="route('propostas.index')">
                            <Button variant="outline">Voltar à Lista</Button>
                        </Link>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
