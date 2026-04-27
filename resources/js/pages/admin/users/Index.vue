<script setup lang="ts">
import { Head, Link, router, usePage } from '@inertiajs/vue3';
import { computed, reactive } from 'vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import admin from '@/routes/admin';

type UserItem = {
    id: number;
    name: string;
    slug: string;
    email: string;
    role: string | null;
    role_label: string | null;
    is_active: boolean;
    locale: string | null;
    deleted_at: string | null;
};

const props = defineProps<{
    users: {
        data: UserItem[];
        meta?: Record<string, unknown>;
        links?: Record<string, unknown>;
    };
    roles: { value: string; label: string }[];
    filters: {
        q?: string | null;
        role?: string | null;
        active?: string;
        trashed?: string;
    };
}>();

defineOptions({
    layout: {
        breadcrumbs: [
            { title: 'Admin', href: admin.index() },
            { title: 'Users', href: admin.users.index() },
        ],
    },
});

const page = usePage();
const items = computed(() => (page.props as any).users?.data ?? []);

const filters = reactive({
    q: props.filters?.q ?? '',
    role: props.filters?.role ?? '',
    active: props.filters?.active ?? 'all',
    trashed: props.filters?.trashed ?? 'with',
});

const applyFilters = () => {
    router.get(
        admin.users.index().url,
        { ...filters },
        { preserveState: true, preserveScroll: true, replace: true },
    );
};
</script>

<template>
    <Head title="Users" />

    <div class="flex flex-col gap-4 p-4">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-lg font-semibold">Users</h1>
                <p class="text-sm text-muted-foreground">
                    Manage users (admin only).
                </p>
            </div>

            <Button as-child>
                <Link :href="admin.users.create()">New user</Link>
            </Button>
        </div>

        <div class="rounded-xl border border-sidebar-border/70 p-4 dark:border-sidebar-border">
            <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
                <div class="grid gap-2">
                    <Label for="q">Search</Label>
                    <Input
                        id="q"
                        v-model="filters.q"
                        placeholder="Name, email or username"
                        @keydown.enter.prevent="applyFilters"
                    />
                </div>

                <div class="grid gap-2">
                    <Label for="role">Role</Label>
                    <select
                        id="role"
                        v-model="filters.role"
                        class="h-10 w-full rounded-md border bg-background px-3 text-sm"
                        @change="applyFilters"
                    >
                        <option value="">All</option>
                        <option v-for="r in roles" :key="r.value" :value="r.value">
                            {{ r.label }}
                        </option>
                    </select>
                </div>

                <div class="grid gap-2">
                    <Label for="active">Active</Label>
                    <select
                        id="active"
                        v-model="filters.active"
                        class="h-10 w-full rounded-md border bg-background px-3 text-sm"
                        @change="applyFilters"
                    >
                        <option value="all">All</option>
                        <option value="1">Active</option>
                        <option value="0">Inactive</option>
                    </select>
                </div>

                <div class="grid gap-2">
                    <Label for="trashed">Deleted</Label>
                    <select
                        id="trashed"
                        v-model="filters.trashed"
                        class="h-10 w-full rounded-md border bg-background px-3 text-sm"
                        @change="applyFilters"
                    >
                        <option value="with">With deleted</option>
                        <option value="without">Without deleted</option>
                        <option value="only">Only deleted</option>
                    </select>
                </div>
            </div>

            <div class="mt-4 flex items-center gap-2">
                <Button variant="secondary" @click="applyFilters">Apply</Button>
                <Button
                    variant="outline"
                    @click="
                        () => {
                            filters.q = '';
                            filters.role = '';
                            filters.active = 'all';
                            filters.trashed = 'with';
                            applyFilters();
                        }
                    "
                >
                    Clear
                </Button>
            </div>
        </div>

        <div class="overflow-hidden rounded-xl border border-sidebar-border/70 dark:border-sidebar-border">
            <table class="w-full text-sm">
                <thead class="bg-muted/40 text-left">
                    <tr>
                        <th class="p-3 font-medium">Name</th>
                        <th class="p-3 font-medium">Email</th>
                        <th class="p-3 font-medium">Role</th>
                        <th class="p-3 font-medium">Active</th>
                        <th class="p-3 font-medium">Status</th>
                        <th class="p-3 font-medium"></th>
                    </tr>
                </thead>
                <tbody>
                    <tr
                        v-for="user in items"
                        :key="user.id"
                        class="border-t border-sidebar-border/70 dark:border-sidebar-border"
                    >
                        <td class="p-3">
                            <div class="font-medium">{{ user.name }}</div>
                            <div class="text-xs text-muted-foreground">
                                @{{ user.slug }}
                            </div>
                        </td>
                        <td class="p-3">{{ user.email }}</td>
                        <td class="p-3">{{ user.role_label ?? '-' }}</td>
                        <td class="p-3">
                            <span
                                class="rounded-md border px-2 py-1 text-xs"
                                :class="user.is_active ? 'border-green-600/30 text-green-700 dark:text-green-400' : 'border-red-600/30 text-red-700 dark:text-red-400'"
                            >
                                {{ user.is_active ? 'Yes' : 'No' }}
                            </span>
                        </td>
                        <td class="p-3">
                            <span
                                v-if="user.deleted_at"
                                class="rounded-md border border-amber-600/30 px-2 py-1 text-xs text-amber-700 dark:text-amber-400"
                            >
                                Deleted
                            </span>
                            <span
                                v-else
                                class="rounded-md border border-neutral-600/20 px-2 py-1 text-xs text-muted-foreground"
                            >
                                Active
                            </span>
                        </td>
                        <td class="p-3 text-right">
                            <Button as-child variant="outline" size="sm">
                                <Link :href="admin.users.edit({ user: user.id })"
                                    >Edit</Link
                                >
                            </Button>
                        </td>
                    </tr>

                    <tr v-if="items.length === 0">
                        <td class="p-6 text-center text-muted-foreground" colspan="6">
                            No users found.
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</template>

