<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import { Head, Link, useForm } from '@inertiajs/vue3'
import Button from '@/Components/ui/button/Button.vue'
import { Input } from '@/Components/ui/input'
import { Label } from '@/Components/ui/label'
import {
    Select,
    SelectContent,
    SelectItem,
    SelectTrigger,
    SelectValue,
} from '@/Components/ui/select'

const form = useForm({
    nome: '',
    taxa: 0.0,
    estado: 'ativo',
})

const submit = () => form.post(route('configuracoes.ivas.store'))
</script>

<template>
    <Head title="Adicionar Taxa de IVA" />
    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-bold text-2xl text-gray-800 leading-tight">
                Adicionar Nova Taxa de IVA
            </h2>
        </template>
        <div class="py-12">
            <div class="max-w-xl mx-auto sm:px-6 lg:px-8">
                <div
                    class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6"
                >
                    <form @submit.prevent="submit">
                        <div class="space-y-4">
                            <div>
                                <Label for="nome">Nome (Ex: Taxa Normal)</Label>
                                <Input
                                    id="nome"
                                    v-model="form.nome"
                                    type="text"
                                    class="mt-1 block w-full"
                                    required
                                />
                                <div
                                    v-if="form.errors.nome"
                                    class="text-sm text-red-600 mt-1"
                                >
                                    {{ form.errors.nome }}
                                </div>
                            </div>
                            <div>
                                <Label for="taxa">Taxa (%)</Label>
                                <Input
                                    id="taxa"
                                    v-model="form.taxa"
                                    type="number"
                                    step="0.01"
                                    min="0"
                                    class="mt-1 block w-full"
                                    required
                                />
                                <div
                                    v-if="form.errors.taxa"
                                    class="text-sm text-red-600 mt-1"
                                >
                                    {{ form.errors.taxa }}
                                </div>
                            </div>
                            <div>
                                <Label for="estado">Estado</Label>
                                <Select v-model="form.estado">
                                    <SelectTrigger>
                                        <SelectValue
                                            placeholder="Selecione um estado..."
                                        />
                                    </SelectTrigger>
                                    <SelectContent>
                                        <SelectItem value="ativo"
                                            >Ativo</SelectItem
                                        >
                                        <SelectItem value="inativo"
                                            >Inativo</SelectItem
                                        >
                                    </SelectContent>
                                </Select>
                                <div
                                    v-if="form.errors.estado"
                                    class="text-sm text-red-600 mt-1"
                                >
                                    {{ form.errors.estado }}
                                </div>
                            </div>
                        </div>
                        <div class="flex items-center justify-end mt-6">
                            <Link
                                :href="route('configuracoes.ivas.index')"
                                class="text-sm text-gray-600 hover:text-gray-900 mr-4"
                            >
                                Cancelar
                            </Link>
                            <Button type="submit" :disabled="form.processing">
                                Guardar
                            </Button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
