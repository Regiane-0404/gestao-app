<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import { Head, Link, useForm } from '@inertiajs/vue3'
import { Label } from '@/Components/ui/label'
import {
    Select,
    SelectContent,
    SelectItem,
    SelectTrigger,
    SelectValue,
} from '@/Components/ui/select'
import { Input } from '@/Components/ui/input'
import { ref } from 'vue'

const props = defineProps({
    clientes: Array,
})

const defaultValidityDate = () => {
    const date = new Date()
    date.setDate(date.getDate() + 30)
    return date.toISOString().split('T')[0]
}

const form = useForm({
    entidade_id: null,
    validade: defaultValidityDate(),
})

// A função submit continua a mesma, ela contém a lógica do Inertia
const submit = () => {
    form.post(route('propostas.store'))
}
</script>

<template>
    <Head title="Criar Proposta" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-bold text-2xl text-gray-800 leading-tight">
                Criar Nova Proposta
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
                <div
                    class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6"
                >
                    <!-- --- INÍCIO DA ALTERAÇÃO --- -->
                    <!-- Removida a tag <form> e o evento @submit.prevent -->
                    <div>
                        <!-- --- FIM DA ALTERAÇÃO --- -->
                        <p class="text-sm text-gray-600 mb-6">
                            Passo 1 de 2: Selecione o cliente e a validade. Após
                            guardar, poderá adicionar os artigos.
                        </p>
                        <div
                            class="grid grid-cols-1 md:grid-cols-2 gap-x-8 gap-y-6"
                        >
                            <div class="md:col-span-1">
                                <Label for="entidade_id"
                                    >Cliente (Obrigatório)</Label
                                >
                                <Select v-model="form.entidade_id">
                                    <SelectTrigger>
                                        <SelectValue
                                            placeholder="Selecione um cliente..."
                                        />
                                    </SelectTrigger>
                                    <SelectContent>
                                        <SelectItem
                                            v-for="cliente in clientes"
                                            :key="cliente.id"
                                            :value="cliente.id"
                                        >
                                            {{ cliente.nome }}
                                        </SelectItem>
                                    </SelectContent>
                                </Select>
                                <div
                                    v-if="form.errors.entidade_id"
                                    class="text-sm text-red-600 mt-1"
                                >
                                    {{ form.errors.entidade_id }}
                                </div>
                            </div>

                            <div class="md:col-span-1">
                                <Label for="validade">Validade</Label>
                                <Input
                                    id="validade"
                                    v-model="form.validade"
                                    type="date"
                                    class="mt-1 block w-full"
                                />
                                <div
                                    v-if="form.errors.validade"
                                    class="text-sm text-red-600 mt-1"
                                >
                                    {{ form.errors.validade }}
                                </div>
                            </div>
                        </div>

                        <div
                            class="flex items-center justify-end mt-8 border-t pt-6 space-x-4"
                        >
                            <Link
                                :href="route('propostas.index')"
                                class="text-sm font-medium text-gray-600 hover:text-gray-900"
                            >
                                Cancelar
                            </Link>

                            <!-- --- INÍCIO DA ALTERAÇÃO --- -->
                            <!-- Removido 'type="submit"' e adicionado o evento @click="submit" -->
                            <button
                                @click="submit"
                                :disabled="form.processing"
                                class="inline-flex items-center justify-center whitespace-nowrap rounded-md text-sm font-medium ring-offset-background transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:pointer-events-none disabled:opacity-50 bg-primary text-primary-foreground hover:bg-primary/90 h-10 px-4 py-2"
                                :class="{ 'opacity-50': form.processing }"
                            >
                                {{
                                    form.processing
                                        ? 'A criar...'
                                        : 'Continuar e Adicionar Artigos'
                                }}
                            </button>
                            <!-- --- FIM DA ALTERAÇÃO --- -->
                        </div>
                    </div>
                    <!-- Fecho do <div> que substituiu a <form> -->
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
