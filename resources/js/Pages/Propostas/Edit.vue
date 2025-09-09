<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import { Head, Link, router, useForm, usePage } from '@inertiajs/vue3'
import { Badge } from '@/Components/ui/badge'
import { Button } from '@/Components/ui/button'
import { Input } from '@/Components/ui/input'
import { Label } from '@/Components/ui/label'
import { Textarea } from '@/Components/ui/textarea'
import { ref, watch, computed } from 'vue'
import debounce from 'lodash.debounce'
import axios from 'axios'
import { Trash2 } from 'lucide-vue-next'

const props = defineProps({
    proposta: Object,
    encomendaExistenteId: Number, // Recebe o ID da encomenda, se existir
})
// --- ADICIONE ESTE BLOCO ---
const page = usePage()
const userPermissions = computed(() => page.props.auth?.permissions || [])
const hasPermission = (permission) => {
    return userPermissions.value.includes(permission)
}
// --- FIM DO BLOCO ---

// Formulário para o cabeçalho (apenas para a validade e estado)
const formProposta = useForm({
    estado: props.proposta.estado,
    validade: new Date(props.proposta.validade).toISOString().split('T')[0],
})

const isFechado = computed(() => props.proposta.estado === 'fechado')

// Lógica de totais
const totais = computed(() => {
    const subtotal = props.proposta.linhas.reduce(
        (acc, l) => acc + l.quantidade * l.preco_unitario,
        0
    )
    const totalIva = props.proposta.linhas.reduce(
        (acc, l) => acc + l.quantidade * l.preco_unitario * (l.taxa_iva / 100),
        0
    )
    return { subtotal, totalIva, totalGeral: subtotal + totalIva }
})

// Lógica de pesquisa e gestão de linhas
const searchTerm = ref('')
const searchResults = ref([])
const isSearching = ref(false)
const isSearchFocused = ref(false)

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
    } finally {
        isSearching.value = false
    }
}
watch(searchTerm, debounce(searchArtigos, 300))

const addArtigoToProposta = (artigo) => {
    router.post(
        route('propostas.linhas.store', props.proposta.id),
        { artigo_id: artigo.id },
        {
            preserveScroll: true,
            onSuccess: () => {
                searchTerm.value = ''
                searchResults.value = []
                isSearchFocused.value = false
            },
        }
    )
}

const removeLinha = (linhaId) => {
    if (confirm('Tem a certeza que deseja remover este artigo da proposta?')) {
        router.delete(route('propostas.linhas.destroy', linhaId), {
            preserveScroll: true,
        })
    }
}

const updateQuantidade = (linha) => {
    if (linha.quantidade && linha.quantidade > 0) {
        router.patch(
            route('propostas.linhas.update', linha.id),
            { quantidade: linha.quantidade },
            { preserveScroll: true }
        )
    } else {
        router.reload({ only: ['proposta'] })
    }
}

// Função de submissão do cabeçalho
const submitProposta = () => {
    formProposta.put(route('propostas.update', props.proposta.id), {
        preserveScroll: true,
    })
}

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
                    <!-- Resumo da proposta com Validade editável -->
                    <div
                        class="grid grid-cols-1 md:grid-cols-3 gap-6 border-b pb-6 items-end"
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
                            <Label for="validade">Validade</Label>
                            <Input
                                id="validade"
                                type="date"
                                v-model="formProposta.validade"
                                :disabled="isFechado"
                                class="mt-1"
                            />
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

                        <div v-if="!isFechado" class="relative max-w-md mb-4">
                            <Input
                                v-model="searchTerm"
                                type="text"
                                placeholder="Pesquisar por referência ou nome do artigo..."
                                class="w-full"
                                @focus="handleSearchFocus"
                                @blur="handleSearchBlur"
                            />
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
                                        <th class="p-2 font-medium w-24">
                                            Qtd.
                                        </th>
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
                                        <td class="p-2">
                                            <Input
                                                type="number"
                                                v-model="linha.quantidade"
                                                @blur="updateQuantidade(linha)"
                                                @keydown.enter.prevent="
                                                    updateQuantidade(linha)
                                                "
                                                class="w-20 h-8 text-sm"
                                                step="1"
                                                min="1"
                                                :disabled="isFechado"
                                            />
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
                                                v-if="!isFechado"
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

                    <!-- Bloco de Totais e Botões de Ação -->
                    <div class="flex justify-between items-start pt-6 border-t">
                        <div class="flex items-center space-x-2">
                            <Link :href="route('propostas.index')">
                                <Button variant="outline"
                                    >Voltar à Lista</Button
                                >
                            </Link>

                            <!-- --- INÍCIO DA CORREÇÃO --- -->
                            <template v-if="isFechado">
                                <a :href="route('propostas.pdf', proposta.id)">
                                    <Button>Download PDF</Button>
                                </a>

                                <!-- --- INÍCIO DA CORREÇÃO --- -->
                                <Link
                                    v-if="encomendaExistenteId"
                                    :href="
                                        route(
                                            'encomendas.show',
                                            encomendaExistenteId
                                        )
                                    "
                                >
                                    <Button variant="outline"
                                        >Ver Encomenda Gerada</Button
                                    >
                                </Link>
                                <Link
                                    v-if="
                                        !encomendaExistenteId &&
                                        hasPermission('converter_propostas')
                                    "
                                    :href="
                                        route(
                                            'propostas.converter',
                                            proposta.id
                                        )
                                    "
                                    method="post"
                                    as="button"
                                >
                                    <Button>Converter em Encomenda</Button>
                                </Link>
                                <!-- --- FIM DA CORREÇÃO --- -->
                            </template>

                            <template v-else>
                                <Button
                                    @click="submitProposta"
                                    :disabled="formProposta.processing"
                                >
                                    Guardar Rascunho
                                </Button>
                                <Button
                                    @click="
                                        () => {
                                            formProposta.estado = 'fechado'
                                            submitProposta()
                                        }
                                    "
                                    :disabled="
                                        formProposta.processing ||
                                        proposta.linhas.length === 0
                                    "
                                >
                                    Finalizar e Fechar Proposta
                                </Button>
                            </template>
                            <!-- --- FIM DA CORREÇÃO --- -->
                        </div>

                        <!-- Tabela de Totais -->
                        <div class="w-1/3">
                            <div class="flex justify-between text-sm">
                                <span class="text-gray-600">Subtotal:</span>
                                <span class="font-medium"
                                    >{{ totais.subtotal.toFixed(2) }}€</span
                                >
                            </div>
                            <div class="flex justify-between text-sm mt-1">
                                <span class="text-gray-600">Total IVA:</span>
                                <span class="font-medium"
                                    >{{ totais.totalIva.toFixed(2) }}€</span
                                >
                            </div>
                            <div
                                class="flex justify-between text-lg mt-2 font-bold border-t pt-2"
                            >
                                <span>Total:</span>
                                <span>{{ totais.totalGeral.toFixed(2) }}€</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
