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

const props = defineProps({
    roles: Array,
})

const form = useForm({
    name: '',
    email: '',
    password: '',
    password_confirmation: '',
    role: null,
})

const submit = () => {
    form.post(route('gestao.utilizadores.store'))
}
</script>

<template>
    <Head title="Adicionar Utilizador" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-bold text-2xl text-gray-800 leading-tight">
                Adicionar Novo Utilizador
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <form @submit.prevent="submit">
                        <div class="space-y-4">
                            <div>
                                <Label for="name">Nome</Label>
                                <Input id="name" v-model="form.name" type="text" class="mt-1 block w-full" required />
                                <div v-if="form.errors.name" class="text-sm text-red-600 mt-1">{{ form.errors.name }}</div>
                            </div>

                            <div>
                                <Label for="email">Email</Label>
                                <Input id="email" v-model="form.email" type="email" class="mt-1 block w-full" required />
                                <div v-if="form.errors.email" class="text-sm text-red-600 mt-1">{{ form.errors.email }}</div>
                            </div>

                             <div>
                                <Label for="role">Grupo de Permissões</Label>
                                <Select v-model="form.role">
                                    <SelectTrigger>
                                        <SelectValue placeholder="Selecione um grupo..." />
                                    </SelectTrigger>
                                    <SelectContent>
                                        <SelectItem v-for="role in roles" :key="role" :value="role">
                                            {{ role }}
                                        </SelectItem>
                                    </SelectContent>
                                </Select>
                                <div v-if="form.errors.role" class="text-sm text-red-600 mt-1">{{ form.errors.role }}</div>
                            </div>

                            <div>
                                <Label for="password">Password</Label>
                                <Input id="password" v-model="form.password" type="password" class="mt-1 block w-full" required />
                                <div v-if="form.errors.password" class="text-sm text-red-600 mt-1">{{ form.errors.password }}</div>
                            </div>

                            <div>
                                <Label for="password_confirmation">Confirmar Password</Label>
                                <Input id="password_confirmation" v-model="form.password_confirmation" type="password" class="mt-1 block w-full" required />
                            </div>
                        </div>

                        <div class="flex items-center justify-end mt-6">
                            <Link :href="route('gestao.utilizadores.index')" class="text-sm text-gray-600 hover:text-gray-900 mr-4">
                                Cancelar
                            </Link>
                            <Button type="submit" :disabled="form.processing">
                                Guardar Utilizador
                            </Button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>