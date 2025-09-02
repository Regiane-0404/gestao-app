<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import { Head, Link, router } from '@inertiajs/vue3'
import { ref, watch } from 'vue'
import { Input } from '@/Components/ui/input'
import { Button } from '@/Components/ui/button'

const props = defineProps({
    entidades: Object,
    filters: Object,
    pageTitle: String, // Prop para o título dinâmico
    routeName: String, // Prop para a rota dinâmica
})

const search = ref(props.filters.search)

// A função watch agora usa a rota dinâmica para a pesquisa
let searchTimeout = null
watch(search, (newValue) => {
    clearTimeout(searchTimeout)
    searchTimeout = setTimeout(() => {
        router.get(
            route(props.routeName), // <-- ALTERAÇÃO IMPORTANTE AQUI
            { search: newValue },
            {
                preserveState: true,
                replace: true,
            }
        )
    }, 300)
})
</script>

<template>
    <!-- O título da aba do navegador agora é dinâmico -->
    <Head :title="pageTitle.replace('Lista de ', '')" />

    <AuthenticatedLayout>
        <!-- O título da página agora é dinâmico -->
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ pageTitle }}
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div
                    class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6"
                >
                    <div class="mb-6">
                        <Input
                            v-model="search"
                            type="text"
                            placeholder="Pesquisar por nome, NIF ou NIC..."
                            class="w-full md:w-1/2 placeholder:text-gray-400"
                        />
                    </div>

                    <table class="min-w-full bg-white">
                        <thead class="text-left">
                            <tr>
                                <th class="py-2 px-4 border-b">Nome</th>
                                <th class="py-2 px-4 border-b">
                                    Nº Contribuinte
                                </th>
                                <th class="py-2 px-4 border-b">Email</th>
                                <th class="py-2 px-4 border-b">Telefone</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-if="entidades.data.length === 0">
                                <td colspan="4" class="text-center py-4">
                                    Nenhuma entidade encontrada.
                                </td>
                            </tr>
                            <tr
                                v-for="entidade in entidades.data"
                                :key="entidade.id"
                                class="hover:bg-gray-50"
                            >
                                <td class="py-2 px-4 border-b">
                                    {{ entidade.nome }}
                                </td>
                                <td class="py-2 px-4 border-b">
                                    {{ entidade.nif || entidade.nic }}
                                </td>
                                <td class="py-2 px-4 border-b">
                                    {{ entidade.email }}
                                </td>
                                <td class="py-2 px-4 border-b">
                                    {{ entidade.telemovel }}
                                </td>
                            </tr>
                        </tbody>
                    </table>

                    <div
                        class="mt-6 flex flex-col md:flex-row justify-between items-center space-y-4 md:space-y-0"
                    >
                        <div
                            v-if="entidades.links.length > 3"
                            class="flex items-center space-x-1"
                        >
                            <template
                                v-for="(link, key) in entidades.links"
                                :key="key"
                            >
                                <Link
                                    :href="link.url"
                                    v-html="link.label"
                                    class="px-3 py-2 text-sm leading-4 border rounded"
                                    :class="{
                                        'bg-primary text-primary-foreground':
                                            link.active,
                                        'hover:bg-gray-100': !link.active,
                                        'text-gray-400 cursor-not-allowed':
                                            !link.url,
                                    }"
                                    :disabled="!link.url"
                                />
                            </template>
                        </div>

                        <!-- O botão agora é dinâmico -->
                        <Link
                            :href="
                                route(routeName.replace('.index', '.create'))
                            "
                        >
                            <Button>
                                Adicionar
                                {{
                                    routeName.includes('clientes')
                                        ? 'Cliente'
                                        : 'Fornecedor'
                                }}
                            </Button>
                        </Link>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
