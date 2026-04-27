<script setup lang="ts">
import { Form, Head, Link, router, setLayoutProps } from '@inertiajs/vue3';
import { ref, watchEffect } from 'vue';
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Checkbox } from '@/components/ui/checkbox';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import admin from '@/routes/admin';

type UserItem = {
    id: number;
    name: string;
    slug: string;
    email: string;
    role: string | null;
    role_label?: string | null;
    is_active: boolean;
    locale: string | null;
    deleted_at: string | null;
};

const props = defineProps<{
    user: { data: UserItem };
    roles: { value: string; label: string }[];
}>();

const isActive = ref(Boolean(props.user.data.is_active));
const roleValue = ref<string>(props.user.data.role ?? props.roles[0]?.value ?? '');

watchEffect(() => {
    setLayoutProps({
        breadcrumbs: [
            { title: 'Admin', href: admin.index() },
            { title: 'Users', href: admin.users.index() },
            { title: 'Edit', href: admin.users.edit({ user: props.user.data.id }) },
        ],
    });
});

const destroy = () => {
    if (!confirm('Delete this user?')) {
        return;
    }

    router.delete(admin.users.destroy({ user: props.user.data.id }).url);
};

const restore = () => {
    router.post(admin.users.restore({ user: props.user.data.id }).url);
};
</script>

<template>
    <Head title="Edit user" />

    <div class="mx-auto w-full max-w-3xl space-y-6 p-4">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="text-lg font-semibold">Edit user</h1>
                <p class="text-sm text-muted-foreground">
                    Update user details.
                </p>
            </div>

            <div class="flex gap-2">
                <Button as-child variant="outline">
                    <Link :href="admin.users.index()">Back</Link>
                </Button>
                <Button v-if="user.data.deleted_at" variant="secondary" @click="restore">
                    Restore
                </Button>
                <Button v-else variant="destructive" @click="destroy">Delete</Button>
            </div>
        </div>

        <Card>
            <CardHeader>
                <CardTitle>User details</CardTitle>
                <CardDescription>
                    Manage user info and status.
                </CardDescription>
            </CardHeader>

            <CardContent>
                <Form
                    :action="admin.users.update({ user: user.data.id }).url"
                    method="patch"
                    v-slot="{ errors, processing }"
                    class="grid gap-6"
                >
                    <div class="grid gap-2">
                        <Label for="name">Name</Label>
                        <Input
                            id="name"
                            name="name"
                            :default-value="user.data.name"
                            required
                        />
                        <InputError :message="errors.name" />
                    </div>

                    <div class="grid gap-2">
                        <Label for="slug">Username</Label>
                        <Input
                            id="slug"
                            name="slug"
                            :default-value="user.data.slug"
                            required
                        />
                        <InputError :message="errors.slug" />
                    </div>

                    <div class="grid gap-2">
                        <Label for="email">Email</Label>
                        <Input
                            id="email"
                            name="email"
                            type="email"
                            :default-value="user.data.email"
                            required
                        />
                        <InputError :message="errors.email" />
                    </div>

                    <div class="grid gap-2 sm:grid-cols-2 sm:gap-4">
                        <div class="grid gap-2">
                            <Label for="password">Password (optional)</Label>
                            <Input
                                id="password"
                                name="password"
                                type="password"
                                autocomplete="new-password"
                            />
                            <InputError :message="errors.password" />
                        </div>

                        <div class="grid gap-2">
                            <Label for="password_confirmation"
                                >Confirm password</Label
                            >
                            <Input
                                id="password_confirmation"
                                name="password_confirmation"
                                type="password"
                                autocomplete="new-password"
                            />
                        </div>
                    </div>

                    <div class="grid gap-2 sm:grid-cols-2 sm:gap-4">
                        <div class="grid gap-2">
                            <Label for="role">Role</Label>
                            <select
                                id="role"
                                name="role"
                                class="h-10 w-full rounded-md border bg-background px-3 text-sm"
                                v-model="roleValue"
                            >
                                <option
                                    v-for="r in roles"
                                    :key="r.value"
                                    :value="r.value"
                                >
                                    {{ r.label }}
                                </option>
                            </select>
                            <InputError :message="errors.role" />
                        </div>

                        <div class="grid gap-2">
                            <Label>Active</Label>
                            <div class="flex items-center gap-3">
                                <input
                                    type="hidden"
                                    name="is_active"
                                    :value="isActive ? 1 : 0"
                                />
                                <Checkbox
                                    id="is_active"
                                    v-model="isActive"
                                />
                                <Label for="is_active" class="font-normal">
                                    {{ isActive ? 'Active' : 'Inactive' }}
                                </Label>
                            </div>
                            <InputError :message="errors.is_active" />
                        </div>
                    </div>

                    <div class="flex items-center gap-3">
                        <Button type="submit" :disabled="processing">Save changes</Button>
                    </div>
                </Form>
            </CardContent>
        </Card>
    </div>
</template>

