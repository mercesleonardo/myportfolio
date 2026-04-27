<script setup lang="ts">
import { Form, Head, Link } from '@inertiajs/vue3';
import { ref } from 'vue';
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Checkbox } from '@/components/ui/checkbox';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import admin from '@/routes/admin';

defineProps<{
    roles: { value: string; label: string }[];
}>();

defineOptions({
    layout: {
        breadcrumbs: [
            { title: 'Admin', href: admin.index() },
            { title: 'Users', href: admin.users.index() },
            { title: 'Create', href: admin.users.create() },
        ],
    },
});

const isActive = ref(false);
</script>

<template>
    <Head title="Create user" />

    <div class="mx-auto w-full max-w-3xl space-y-6 p-4">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="text-lg font-semibold">Create user</h1>
                <p class="text-sm text-muted-foreground">
                    Create a new user account (admin only).
                </p>
            </div>

            <Button as-child variant="outline">
                <Link :href="admin.users.index()">Back</Link>
            </Button>
        </div>

        <Card>
            <CardHeader>
                <CardTitle>User details</CardTitle>
                <CardDescription>
                    Username must be letters only (a-z), unique, and not reserved.
                </CardDescription>
            </CardHeader>

            <CardContent>
                <Form
                    :action="admin.users.store().url"
                    method="post"
                    v-slot="{ errors, processing }"
                    class="grid gap-6"
                >
                    <div class="grid gap-2">
                        <Label for="name">Name</Label>
                        <Input id="name" name="name" required autocomplete="name" />
                        <InputError :message="errors.name" />
                    </div>

                    <div class="grid gap-2">
                        <Label for="slug">Username</Label>
                        <Input id="slug" name="slug" required autocomplete="username" />
                        <InputError :message="errors.slug" />
                    </div>

                    <div class="grid gap-2">
                        <Label for="email">Email</Label>
                        <Input id="email" name="email" type="email" required autocomplete="email" />
                        <InputError :message="errors.email" />
                    </div>

                    <div class="grid gap-2 sm:grid-cols-2 sm:gap-4">
                        <div class="grid gap-2">
                            <Label for="password">Password</Label>
                            <Input
                                id="password"
                                name="password"
                                type="password"
                                required
                                autocomplete="new-password"
                            />
                            <InputError :message="errors.password" />
                        </div>

                        <div class="grid gap-2">
                            <Label for="password_confirmation">Confirm password</Label>
                            <Input
                                id="password_confirmation"
                                name="password_confirmation"
                                type="password"
                                required
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
                        <Button type="submit" :disabled="processing">Create user</Button>
                    </div>
                </Form>
            </CardContent>
        </Card>
    </div>
</template>

