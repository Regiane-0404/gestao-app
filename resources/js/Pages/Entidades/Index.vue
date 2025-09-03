<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import { Head, Link, router } from '@inertiajs/vue3'
import { ref, watch } from 'vue'
import { MoreHorizontal } from 'lucide-vue-next'
import { Input } from '@/Components/ui/input'
import { Button } from '@/Components/ui/button'
import {
    DropdownMenu,
    DropdownMenuContent,
    DropdownMenuItem,
    DropdownMenuLabel,
    DropdownMenuSeparator,
    DropdownMenuTrigger,
} from '@/Components/ui/dropdown-menu'

const props = defineProps({
    entidades: Object,
    filters: Object,
    pageTitle: String,
    sourceRoute: String, // <-- Corrigido para sourceRoute
})

const search = ref(props.filters.search)

let searchTimeout = null
watch(search, (newValue) => {
    clearTimeout(searchTimeout)
    searchTimeout = setTimeout(() => {
        router.get(
            route(props.sourceRoute), // <-- Corrigido para sourceRoute
            { search: newValue },
            {
                preserveState: true,
                replace: true,
            }
        )
    }, 300)
})

const editEntidade = (entidadeId) => {
    const editRouteName = props.sourceRoute.replace('.index', '.edit') // <-- Corrigido para sourceRoute
    const paramName = props.sourceRoute.includes('clientes') // <-- Corrigido para sourceRoute
        ? 'cliente'
        : 'fornecedor'
    router.get(route(editRouteName, { [paramName]: entidadeId }))
}
</script>

<template>
    <Head :title="pageTitle.replace('Lista de ', '')" />

    <AuthenticatedLayout>
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
                    <div class="flex justify-between items-center mb-6">
                        <Input
                            v-model="search"
                            type="text"
                            placeholder="Pesquisar por nome, NIF ou NIC..."
                            class="w-full md:w-1/2 placeholder:text-gray-400"
                        />
                        <Link
                            :href="
                                route(sourceRoute.replace('.index', '.create'))
                            "
                        >
                            <!-- Corrigido para sourceRoute -->
                            <Button>
                                Adicionar
                                {{
                                    sourceRoute.includes('clientes')
                                        ? 'Cliente'
                                        : 'Fornecedor'
                                }}
                                <!-- Corrigido para sourceRoute -->
                            </Button>
                        </Link>
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
                                <th class="py-2 px-4 border-b text-right">
                                    Ações
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-if="entidades.data.length === 0">
                                <td colspan="5" class="text-center py-4">
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
                                <td class="py-2 px-4 border-b text-right">
                                    <DropdownMenu>
                                        <DropdownMenuTrigger as-child>
                                            <Button
                                                variant="ghost"
                                                class="w-8 h-8 p-0"
                                            >
                                                <span class="sr-only"
                                                    >Abrir menu</span
                                                >
                                                <MoreHorizontal
                                                    class="w-4 h-4"
                                                />
                                            </Button>
                                        </DropdownMenuTrigger>
                                        <DropdownMenuContent align="end">
                                            <DropdownMenuLabel
                                                >Ações</DropdownMenuLabel
                                            >
                                            <DropdownMenuSeparator />
                                            <DropdownMenuItem
                                                @select="
                                                    editEntidade(entidade.id)
                                                "
                                                class="cursor-pointer"
                                            >
                                                Editar
                                            </DropdownMenuItem>
                                        </DropdownMenuContent>
                                    </DropdownMenu>
                                </td>
                            </tr>
                        </tbody>
                    </table>

                    <div
                        v-if="entidades.links.length > 3"
                        class="mt-6 flex justify-center"
                    >
                        <div class="flex items-center space-x-1">
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
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
