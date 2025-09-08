<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import { Head, Link, useForm } from '@inertiajs/vue3'
import { Button } from '@/Components/ui/button'
import { Input } from '@/Components/ui/input'
import { Label } from '@/Components/ui/label'
import { Checkbox } from '@/Components/ui/checkbox'
import { computed } from 'vue'

const props = defineProps({
    role: Object,
    permissions: Array, // Todas as permissões disponíveis (ex: ['ver_clientes', ...])
    rolePermissions: Array, // As permissões que este role já tem (ex: ['ver_clientes'])
})

const form = useForm({
    name: props.role.name,
    permissions: props.rolePermissions, // Inicia o form com as permissões já selecionadas
})

// Agrupa as permissões por módulo para a exibição
const groupedPermissions = computed(() => {
    return props.permissions.reduce((groups, permission) => {
        const module = permission.split('_').pop() // Pega a última parte (ex: 'clientes')
        if (!groups[module]) {
            groups[module] = []
        }
        groups[module].push(permission)
        return groups
    }, {})
})

const submit = () => {
    form.put(route('gestao.roles.update', props.role.id))
}
</script>

<template>
    <Head :title="`Editar Grupo: ${form.name}`" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-bold text-2xl text-gray-800 leading-tight">
                Editar Grupo de Permissões
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
                <div
                    class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6"
                >
                    <form @submit.prevent="submit">
                        <!-- Nome do Grupo -->
                        <div>
                            <Label for="name">Nome do Grupo</Label>
                            <Input
                                id="name"
                                v-model="form.name"
                                type="text"
                                class="mt-1 block w-full"
                                required
                            />
                            <div
                                v-if="form.errors.name"
                                class="text-sm text-red-600 mt-1"
                            >
                                {{ form.errors.name }}
                            </div>
                        </div>

                        <!-- Grelha de Permissões -->
                        <div class="mt-8">
                            <h3
                                class="text-lg font-semibold text-gray-800 mb-4"
                            >
                                Permissões Associadas
                            </h3>
                            <div class="space-y-6">
                                <div
                                    v-for="(
                                        modulePermissions, module
                                    ) in groupedPermissions"
                                    :key="module"
                                    class="border rounded-lg p-4"
                                >
                                    <h4 class="font-semibold capitalize mb-3">
                                        {{ module.replace('_', ' ') }}
                                    </h4>
                                    <div
                                        class="grid grid-cols-2 md:grid-cols-4 gap-4"
                                    >
                                        <div
                                            v-for="permission in modulePermissions"
                                            :key="permission"
                                            class="flex items-center space-x-2"
                                        >
                                            <Checkbox
                                                :id="permission"
                                                :value="permission"
                                                v-model:checked="
                                                    form.permissions
                                                "
                                            />
                                            <label
                                                :for="permission"
                                                class="text-sm font-medium leading-none cursor-pointer"
                                            >
                                                {{ permission.split('_')[0] }}
                                                <!-- Mostra apenas a ação (ex: 'ver') -->
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div
                                v-if="form.errors.permissions"
                                class="text-sm text-red-600 mt-1"
                            >
                                {{ form.errors.permissions }}
                            </div>
                        </div>

                        <!-- Botões de Ação -->
                        <div
                            class="flex items-center justify-end mt-8 border-t pt-6"
                        >
                            <Link
                                :href="route('gestao.roles.index')"
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
