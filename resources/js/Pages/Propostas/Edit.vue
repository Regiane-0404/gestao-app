<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import { Head, Link } from '@inertiajs/vue3'
import { Badge } from '@/Components/ui/badge'
import Button from '@/Components/ui/button/Button.vue' // Usando o caminho direto e correto

const props = defineProps({
    proposta: Object,
})
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
                                >{{ proposta.estado }}</Badge
                            >
                        </div>
                    </div>

                    <!-- Área para artigos -->
                    <div class="pt-4">
                        <h3 class="text-xl font-semibold text-gray-800 mb-3">
                            Artigos da Proposta
                        </h3>
                        <div
                            class="border rounded-lg p-8 text-center bg-gray-50"
                        >
                            <p class="text-gray-500 text-sm">
                                (Aqui iremos adicionar o componente para
                                pesquisar e adicionar artigos)
                            </p>
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
