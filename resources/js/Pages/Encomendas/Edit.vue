<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import { Head, Link } from '@inertiajs/vue3'
import { Badge } from '@/Components/ui/badge'
import Button from '@/Components/ui/button/Button.vue'
import { ref } from 'vue'

// Props recebidas da página Inertia
const props = defineProps({
    encomenda: Object,
})

// Função utilitária para formatar data
const formatDate = (date) => {
    return new Date(date).toLocaleDateString('pt-PT', {
        year: 'numeric',
        month: '2-digit',
        day: '2-digit',
    })
}

// Estado local
const encomenda = ref(props.encomenda)
</script>

<template>
    <Head title="Editar Encomenda" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Editar Encomenda #{{ encomenda.id }}
            </h2>
        </template>

        <div class="py-8">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
                <!-- Resumo da encomenda -->
                <div
                    class="grid grid-cols-1 md:grid-cols-4 gap-6 border-b pb-6"
                >
                    <!-- Alterado para 4 colunas -->
                    <div>
                        <h3 class="text-sm font-medium text-gray-500">
                            Cliente
                        </h3>
                        <p class="text-lg font-semibold text-gray-900">
                            {{ encomenda.cliente?.nome || 'N/A' }}
                        </p>
                    </div>

                    <!-- --- INÍCIO DA CORREÇÃO --- -->
                    <div v-if="encomenda.proposta_id">
                        <h3 class="text-sm font-medium text-gray-500">
                            Origem da Proposta
                        </h3>
                        <Link
                            :href="
                                route('propostas.edit', encomenda.proposta_id)
                            "
                            class="text-lg font-semibold text-blue-600 hover:underline"
                        >
                            #{{ encomenda.proposta_id }}
                        </Link>
                    </div>
                    <!-- --- FIM DA CORREÇÃO --- -->

                    <div>
                        <h3 class="text-sm font-medium text-gray-500">Data</h3>
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
                        >
                            {{ encomenda.estado }}
                        </Badge>
                    </div>
                </div>

                <!-- Itens da encomenda -->
                <div>
                    <h3 class="text-lg font-medium text-gray-900 mb-4">
                        Itens
                    </h3>
                    <div
                        v-if="encomenda.itens && encomenda.itens.length > 0"
                        class="space-y-4"
                    >
                        <div
                            v-for="item in encomenda.itens"
                            :key="item.id"
                            class="flex justify-between border-b pb-2"
                        >
                            <span>{{ item.nome }}</span>
                            <span
                                >{{ item.quantidade }} x
                                {{ item.preco }} €</span
                            >
                        </div>
                    </div>
                    <p v-else class="text-gray-500">Nenhum item adicionado.</p>
                </div>

                <!-- Total -->
                <div class="flex justify-end">
                    <h3 class="text-xl font-bold">
                        Total: {{ encomenda.total }} €
                    </h3>
                </div>

                <!-- Ações -->
                <div class="flex justify-end space-x-4">
                    <Button variant="outline">Cancelar</Button>
                    <Button>Guardar Alterações</Button>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
