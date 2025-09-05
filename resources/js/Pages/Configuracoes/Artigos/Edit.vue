<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import { Head, Link, useForm } from '@inertiajs/vue3'
import Button from '@/Components/ui/button/Button.vue'
import { Input } from '@/Components/ui/input'
import { Label } from '@/Components/ui/label'
import { Textarea } from '@/Components/ui/textarea'
import {
    Select,
    SelectContent,
    SelectItem,
    SelectTrigger,
    SelectValue,
} from '@/Components/ui/select'

const props = defineProps({
    artigo: Object,
    ivas: Array,
})

const form = useForm({
    referencia: props.artigo.referencia,
    nome: props.artigo.nome,
    descricao: props.artigo.descricao,
    preco: props.artigo.preco,
    iva_id: props.artigo.iva_id,
    observacoes: props.artigo.observacoes,
    estado: props.artigo.estado,
})

const submit = () => {
    form.put(route('configuracoes.artigos.update', props.artigo.id))
}
</script>

<template>
    <Head :title="`Editar Artigo: ${form.referencia}`" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-bold text-2xl text-gray-800 leading-tight">
                Editar Artigo
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
                <div
                    class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6"
                >
                    <form @submit.prevent="submit">
                        <div
                            class="grid grid-cols-1 md:grid-cols-2 gap-x-8 gap-y-6"
                        >
                            <div>
                                <Label for="referencia">Referência</Label>
                                <Input
                                    id="referencia"
                                    v-model="form.referencia"
                                    type="text"
                                    class="mt-1 block w-full bg-gray-100 cursor-not-allowed"
                                    readonly
                                />
                            </div>

                            <div class="md:col-span-2">
                                <Label for="nome">Nome (Obrigatório)</Label>
                                <Input
                                    id="nome"
                                    v-model="form.nome"
                                    type="text"
                                    class="mt-1 block w-full"
                                />
                                <div
                                    v-if="form.errors.nome"
                                    class="text-sm text-red-600 mt-1"
                                >
                                    {{ form.errors.nome }}
                                </div>
                            </div>

                            <div class="md:col-span-2">
                                <Label for="descricao">Descrição</Label>
                                <Textarea
                                    id="descricao"
                                    v-model="form.descricao"
                                    class="mt-1 block w-full"
                                />
                            </div>

                            <div>
                                <Label for="preco">Preço (sem IVA)</Label>
                                <Input
                                    id="preco"
                                    v-model="form.preco"
                                    type="number"
                                    step="0.01"
                                    class="mt-1 block w-full"
                                />
                                <div
                                    v-if="form.errors.preco"
                                    class="text-sm text-red-600 mt-1"
                                >
                                    {{ form.errors.preco }}
                                </div>
                            </div>

                            <div>
                                <Label for="iva_id"
                                    >Taxa de IVA (Obrigatório)</Label
                                >
                                <Select v-model="form.iva_id">
                                    <SelectTrigger>
                                        <SelectValue
                                            placeholder="Selecione uma taxa..."
                                        />
                                    </SelectTrigger>
                                    <SelectContent>
                                        <SelectItem
                                            v-for="iva in ivas"
                                            :key="iva.id"
                                            :value="iva.id"
                                        >
                                            {{ iva.nome }} ({{
                                                parseInt(iva.taxa)
                                            }}%)
                                        </SelectItem>
                                    </SelectContent>
                                </Select>
                                <div
                                    v-if="form.errors.iva_id"
                                    class="text-sm text-red-600 mt-1"
                                >
                                    {{ form.errors.iva_id }}
                                </div>
                            </div>

                            <div class="md:col-span-2">
                                <Label for="observacoes">Observações</Label>
                                <Textarea
                                    id="observacoes"
                                    v-model="form.observacoes"
                                    class="mt-1 block w-full"
                                />
                            </div>
                        </div>

                        <div
                            class="flex items-center justify-end mt-8 border-t pt-6"
                        >
                            <Link
                                :href="route('configuracoes.artigos.index')"
                                class="text-sm text-gray-600 hover:text-gray-900 mr-4"
                            >
                                Cancelar
                            </Link>
                            <Button type="submit" :disabled="form.processing">
                                {{
                                    form.processing
                                        ? 'A Guardar...'
                                        : 'Guardar Alterações'
                                }}
                            </Button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
