<script setup>
import { Link } from '@inertiajs/vue3'
import ApplicationLogo from '@/Components/ApplicationLogo.vue'
import NavLink from '@/Components/NavLink.vue'
import {
    Accordion,
    AccordionContent,
    AccordionItem,
    AccordionTrigger,
} from '@/Components/ui/accordion'

import {
    Users,
    Building,
    FileText,
    Calendar,
    Truck,
    Briefcase,
    Settings,
    UserCog,
    BookUser,
    Wallet,
    FileArchive,
    Landmark,
    ShieldCheck,
    ScrollText,
    Building2,
    Package,
} from 'lucide-vue-next'
</script>

<template>
    <aside class="w-64 flex-shrink-0 bg-gray-800 text-white flex flex-col">
        <!-- Logo -->
        <div
            class="h-16 flex items-center justify-center border-b border-gray-700"
        >
            <Link :href="route('dashboard')">
                <ApplicationLogo class="block h-9 w-auto" />
            </Link>
        </div>

        <!-- Menu Principal -->
        <nav class="flex-grow p-4 space-y-2">
            <NavLink
                :href="route('clientes.index')"
                :active="route().current('clientes.*')"
            >
                <Users class="h-5 w-5 mr-3" />
                Clientes
            </NavLink>

            <NavLink
                :href="route('fornecedores.index')"
                :active="route().current('fornecedores.*')"
            >
                <Building class="h-5 w-5 mr-3" />
                Fornecedores
            </NavLink>

            <NavLink
                :href="route('contactos.index')"
                :active="route().current('contactos.*')"
            >
                <BookUser class="h-5 w-5 mr-3" />
                Contactos
            </NavLink>

            <!-- --- INÍCIO DA CORREÇÃO --- -->
            <Link
                :href="route('propostas.index')"
                class="flex items-center px-4 py-2 rounded text-sm font-medium"
                :class="{
                    'bg-gray-900 text-white': route().current('propostas.*'),
                    'text-gray-300 hover:bg-gray-700 hover:text-white':
                        !route().current('propostas.*'),
                }"
            >
                <FileText class="h-5 w-5 mr-3" />
                Propostas
            </Link>
            <!-- --- FIM DA CORREÇÃO --- -->

            <NavLink href="#">
                <Calendar class="h-5 w-5 mr-3" />
                Calendário
            </NavLink>

            <!-- Menu Financeiro -->
            <Accordion type="single" collapsible>
                <AccordionItem value="financeiro" class="border-none">
                    <AccordionTrigger
                        class="text-white hover:no-underline hover:bg-gray-700 rounded-md"
                    >
                        <div class="flex items-center">
                            <Wallet class="h-5 w-5 mr-3" />
                            Financeiro
                        </div>
                    </AccordionTrigger>
                    <AccordionContent class="pl-8 space-y-2 pt-2">
                        <NavLink href="#">
                            <Landmark class="h-4 w-4 mr-2" />
                            Contas Bancárias
                        </NavLink>
                        <NavLink href="#">
                            <FileArchive class="h-4 w-4 mr-2" />
                            Faturas Fornecedores
                        </NavLink>
                    </AccordionContent>
                </AccordionItem>
            </Accordion>

            <!-- Menu Configurações e Acessos -->
            <div class="pt-8">
                <Accordion
                    type="single"
                    collapsible
                    :default-value="
                        route().current('configuracoes.*')
                            ? 'configuracoes'
                            : ''
                    "
                >
                    <AccordionItem value="gestao-acessos" class="border-none">
                        <AccordionTrigger
                            class="text-white hover:no-underline hover:bg-gray-700 rounded-md"
                        >
                            <div class="flex items-center">
                                <UserCog class="h-5 w-5 mr-3" />
                                Gestão de Acessos
                            </div>
                        </AccordionTrigger>
                        <AccordionContent class="pl-8 space-y-2 pt-2">
                            <NavLink href="#">
                                <Users class="h-4 w-4 mr-2" />
                                Utilizadores
                            </NavLink>
                            <NavLink href="#">
                                <ShieldCheck class="h-4 w-4 mr-2" />
                                Permissões
                            </NavLink>
                        </AccordionContent>
                    </AccordionItem>

                    <AccordionItem value="configuracoes" class="border-none">
                        <AccordionTrigger
                            class="text-white hover:no-underline hover:bg-gray-700 rounded-md"
                        >
                            <div class="flex items-center">
                                <Settings class="h-5 w-5 mr-3" />
                                Configurações
                            </div>
                        </AccordionTrigger>

                        <AccordionContent class="pl-8 space-y-2 pt-2">
                            <NavLink
                                :href="route('configuracoes.artigos.index')"
                                :active="
                                    route().current('configuracoes.artigos.*')
                                "
                            >
                                <Package class="h-4 w-4 mr-2" />
                                Artigos
                            </NavLink>
                            <NavLink href="#">
                                <Building2 class="h-4 w-4 mr-2" />
                                Empresa
                            </NavLink>
                            <NavLink href="#">
                                <ScrollText class="h-4 w-4 mr-2" />
                                Logs
                            </NavLink>
                        </AccordionContent>
                    </AccordionItem>
                </Accordion>
            </div>
        </nav>
    </aside>
</template>
