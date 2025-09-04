<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import { Head, Link, useForm } from '@inertiajs/vue3'
import { Button } from '@/Components/ui/button'
import { Input } from '@/Components/ui/input'
import { Label } from '@/Components/ui/label'
import { Textarea } from '@/Components/ui/textarea'
import { Checkbox } from '@/Components/ui/checkbox'
import {
    Select,
    SelectContent,
    SelectItem,
    SelectTrigger,
    SelectValue,
} from '@/Components/ui/select'

// Recebemos o contacto a editar e as listas de entidades e funções
const props = defineProps({
    contacto: Object,
    entidades: Array,
    funcoes: Array,
})

// Iniciamos o formulário com os dados existentes do contacto
const form = useForm({
    entidade_id: props.contacto.entidade_id,
    contacto_funcao_id: props.contacto.contacto_funcao_id,
    nome: props.contacto.nome,
    apelido: props.contacto.apelido,
    telemovel: props.contacto.telemovel,
    email: props.contacto.email,
    consentimento_rgpd: !!props.contacto.consentimento_rgpd, // Garante que é booleano
    observacoes: props.contacto.observacoes,
    estado: props.contacto.estado,
})

const submit = () => {
    // Usamos o método 'put' para atualizar o registo
    form.put(route('contactos.update', props.contacto.id))
}
</script>

<template>
    <Head :title="`Editar Contacto: ${form.nome}`" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-bold text-2xl text-gray-800 leading-tight">
                Editar Contacto
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
                            <!-- Entidade (Dropdown) -->
                            <div class="md:col-span-2">
                                <Label for="entidade_id"
                                    >Entidade (Obrigatório)</Label
                                >
                                <Select v-model="form.entidade_id">
                                    <SelectTrigger>
                                        <SelectValue
                                            placeholder="Selecione uma entidade..."
                                        />
                                    </SelectTrigger>
                                    <SelectContent>
                                        <SelectItem
                                            v-for="entidade in entidades"
                                            :key="entidade.id"
                                            :value="entidade.id"
                                        >
                                            {{ entidade.nome }}
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

                            <!-- Nome e Apelido -->
                            <div>
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
                            <div>
                                <Label for="apelido">Apelido</Label>
                                <Input
                                    id="apelido"
                                    v-model="form.apelido"
                                    type="text"
                                    class="mt-1 block w-full"
                                />
                            </div>

                            <!-- Função (Dropdown) -->
                            <div>
                                <Label for="contacto_funcao_id">Função</Label>
                                <Select v-model="form.contacto_funcao_id">
                                    <SelectTrigger>
                                        <SelectValue
                                            placeholder="Selecione uma função..."
                                        />
                                    </SelectTrigger>
                                    <SelectContent>
                                        <SelectItem
                                            v-for="funcao in funcoes"
                                            :key="funcao.id"
                                            :value="funcao.id"
                                        >
                                            {{ funcao.nome }}
                                        </SelectItem>
                                    </SelectContent>
                                </Select>
                            </div>

                            <!-- Telemóvel e Email -->
                            <div>
                                <Label for="telemovel">Telemóvel</Label>
                                <Input
                                    id="telemovel"
                                    v-model="form.telemovel"
                                    type="text"
                                    class="mt-1 block w-full"
                                />
                            </div>
                            <div class="md:col-span-2">
                                <Label for="email">Email</Label>
                                <Input
                                    id="email"
                                    v-model="form.email"
                                    type="email"
                                    class="mt-1 block w-full"
                                />
                                <div
                                    v-if="form.errors.email"
                                    class="text-sm text-red-600 mt-1"
                                >
                                    {{ form.errors.email }}
                                </div>
                            </div>

                            <!-- Observações -->
                            <div class="md:col-span-2">
                                <Label for="observacoes">Observações</Label>
                                <Textarea
                                    id="observacoes"
                                    v-model="form.observacoes"
                                    class="mt-1 block w-full"
                                />
                            </div>

                            <!-- Consentimento RGPD -->
                            <div
                                class="md:col-span-2 flex items-center space-x-2"
                            >
                                <Checkbox
                                    id="consentimento_rgpd"
                                    v-model:checked="form.consentimento_rgpd"
                                />
                                <label
                                    for="consentimento_rgpd"
                                    class="text-sm font-medium leading-none"
                                >
                                    Consentimento RGPD
                                </label>
                            </div>
                        </div>

                        <!-- Botões de Ação -->
                        <div
                            class="flex items-center justify-end mt-8 border-t pt-6"
                        >
                            <Link
                                :href="route('contactos.index')"
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
