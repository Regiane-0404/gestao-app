<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';

// O controller envia a prop 'clientes', nós recebemo-la aqui
defineProps({
    clientes: Array,
});
</script>

<template>
    <Head title="Clientes" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Lista de Clientes</h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <!-- Botão para adicionar novo cliente (funcionalidade futura) -->
                    <div class="flex justify-end mb-4">
                        <button class="bg-blue-500 text-white px-4 py-2 rounded-md hover:bg-blue-600">
                            Adicionar Cliente
                        </button>
                    </div>

                    <!-- Tabela Simples para mostrar os dados -->
                    <table class="min-w-full bg-white">
                        <thead>
                            <tr>
                                <th class="py-2 px-4 border-b">Nome</th>
                                <th class="py-2 px-4 border-b">NIF</th>
                                <th class="py-2 px-4 border-b">Email</th>
                                <th class="py-2 px-4 border-b">Telefone</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-if="clientes.length === 0">
                                <td colspan="4" class="text-center py-4">Nenhum cliente encontrado.</td>
                            </tr>
                            <tr v-for="cliente in clientes" :key="cliente.id">
                                <td class="py-2 px-4 border-b">{{ cliente.nome }}</td>
                                <td class="py-2 px-4 border-b">{{ cliente.nif }}</td>
                                <td class="py-2 px-4 border-b">{{ cliente.email }}</td>
                                <td class="py-2 px-4 border-b">{{ cliente.telemovel }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>