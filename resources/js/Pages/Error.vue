<script setup>
import { Head, Link } from '@inertiajs/vue3'

const props = defineProps({
    status: Number,
    message: String,
})

const title = {
    503: '503: Serviço Indisponível',
    500: '500: Erro de Servidor',
    404: '404: Página Não Encontrada',
    403: '403: Acesso Negado',
}[props.status]

const description = {
    503: 'O serviço está temporariamente em manutenção. Por favor, tente novamente mais tarde.',
    500: 'Ocorreu um erro no servidor.',
    404: 'A página que procura não foi encontrada.',
    403: props.message || 'Você não tem permissão para aceder a esta página.',
}[props.status]
</script>

<template>
    <Head :title="title" />
    <div class="flex items-center justify-center min-h-screen bg-gray-100 px-4">
        <div class="text-center w-full max-w-lg">
            <h1 class="text-6xl font-bold text-gray-800">{{ status }}</h1>
            <p class="mt-4 text-xl font-semibold text-gray-700">{{ title }}</p>
            <p class="mt-2 text-base text-gray-500">{{ description }}</p>
            <div class="mt-8">
                <!-- --- INÍCIO DA CORREÇÃO --- -->
                <!-- Usando uma tag <a> normal com classes do Tailwind para o estilo -->
                <a
                    :href="route('dashboard')"
                    class="inline-flex items-center justify-center whitespace-nowrap rounded-md text-sm font-medium ring-offset-background transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:pointer-events-none disabled:opacity-50 bg-primary text-primary-foreground hover:bg-primary/90 h-10 px-4 py-2"
                >
                    Voltar ao Dashboard
                </a>
                <!-- --- FIM DA CORREÇÃO --- -->
            </div>
        </div>
    </div>
</template>
