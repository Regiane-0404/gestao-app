<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import { Head, Link, useForm } from '@inertiajs/vue3'
import { Button } from '@/Components/ui/button'
import { Input } from '@/Components/ui/input'
import { Label } from '@/Components/ui/label'
import { Checkbox } from '@/Components/ui/checkbox'

const props = defineProps({
    entidade: Object,
    sourceRoute: String,
})

// A inicialização do formulário é feita de forma mais direta agora
const form = useForm({
    nome: props.entidade?.nome || '',
    nif: props.entidade?.nif || '',
    nic: props.entidade?.nic || '',
    morada: props.entidade?.morada || '',
    codigo_postal: props.entidade?.codigo_postal || '',
    localidade: props.entidade?.localidade || '',
    pais: props.entidade?.pais || '',
    telemovel: props.entidade?.telemovel || '',
    email: props.entidade?.email || '',
    is_cliente: !!props.entidade?.is_cliente,
    is_fornecedor: !!props.entidade?.is_fornecedor,
})

const submit = () => {
    const updateRouteName = props.sourceRoute.replace('.index', '.update')
    const paramName = props.sourceRoute.includes('clientes')
        ? 'cliente'
        : 'fornecedor'

    // O form.put envia todos os dados do formulário automaticamente.
    // Não precisamos de construir um payload manual.
    form.put(route(updateRouteName, { [paramName]: props.entidade.id }))
}
</script>
<template>
    <Head :title="`Editar Entidade: ${form.nome || ''}`" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Editar Entidade: {{ form.nome || '...' }}
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
                            <!-- Nome (sem alterações) -->
                            <div class="md:col-span-2">
                                <Label for="nome">Nome</Label>
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

                            <!-- NIF e NIC lado a lado -->
                            <div>
                                <Label for="nif">NIF</Label>
                                <!-- INÍCIO DA ALTERAÇÃO -->
                                <Input
                                    id="nif"
                                    v-model="form.nif"
                                    type="text"
                                    class="mt-1 block w-full bg-gray-100 cursor-not-allowed"
                                    readonly
                                />
                                <!-- FIM DA ALTERAÇÃO -->
                                <div
                                    v-if="form.errors.nif"
                                    class="text-sm text-red-600 mt-1"
                                >
                                    {{ form.errors.nif }}
                                </div>
                            </div>
                            <div>
                                <Label for="nic">NIC / NIPC</Label>
                                <!-- INÍCIO DA ALTERAÇÃO -->
                                <Input
                                    id="nic"
                                    v-model="form.nic"
                                    type="text"
                                    class="mt-1 block w-full bg-gray-100 cursor-not-allowed"
                                    readonly
                                />
                                <!-- FIM DA ALTERAÇÃO -->
                                <div
                                    v-if="form.errors.nic"
                                    class="text-sm text-red-600 mt-1"
                                >
                                    {{ form.errors.nic }}
                                </div>
                            </div>

                            <!-- Resto do formulário (sem alterações) -->
                            <div class="md:col-span-2">
                                <Label for="morada">Morada</Label>
                                <Input
                                    id="morada"
                                    v-model="form.morada"
                                    type="text"
                                    class="mt-1 block w-full"
                                />
                            </div>
                            <div>
                                <Label for="codigo_postal">Código Postal</Label>
                                <Input
                                    id="codigo_postal"
                                    v-model="form.codigo_postal"
                                    type="text"
                                    class="mt-1 block w-full"
                                />
                            </div>
                            <div>
                                <Label for="localidade">Localidade</Label>
                                <Input
                                    id="localidade"
                                    v-model="form.localidade"
                                    type="text"
                                    class="mt-1 block w-full"
                                />
                            </div>
                            <div>
                                <Label for="pais">País</Label>
                                <Input
                                    id="pais"
                                    v-model="form.pais"
                                    type="text"
                                    class="mt-1 block w-full"
                                />
                            </div>
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
                            <div class="md:col-span-2">
                                <Label>Tipo de Entidade</Label>
                                <div class="flex items-center space-x-6 mt-2">
                                    <div class="flex items-center space-x-2">
                                        <Checkbox
                                            id="is_cliente"
                                            v-model:checked="form.is_cliente"
                                        />
                                        <label
                                            for="is_cliente"
                                            class="text-sm font-medium leading-none cursor-pointer"
                                            >É Cliente</label
                                        >
                                    </div>
                                    <div class="flex items-center space-x-2">
                                        <Checkbox
                                            id="is_fornecedor"
                                            v-model:checked="form.is_fornecedor"
                                        />
                                        <label
                                            for="is_fornecedor"
                                            class="text-sm font-medium leading-none cursor-pointer"
                                            >É Fornecedor</label
                                        >
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div
                            class="flex items-center justify-end mt-8 border-t pt-6"
                        >
                            <Link
                                :href="route(sourceRoute)"
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
