<script setup lang="ts">
import { Form, Head, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';
import ProfessionalProfileController from '@/actions/App/Http/Controllers/ProfessionalProfileController';
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { edit } from '@/routes/professional-profile';

type Profile = {
    id: number;
    professional_title: string | null;
    bio: string | null;
    avatar_url: string | null;
    resume_url: string | null;
};

type Props = {
    profile: { data: Profile } | null;
};

const props = defineProps<Props>();

defineOptions({
    layout: {
        breadcrumbs: [
            {
                title: 'Professional profile',
                href: edit(),
            },
        ],
    },
});

const page = usePage();
const user = computed(() => (page.props as any).auth.user);
const profile = computed(() => props.profile?.data ?? null);
</script>

<template>
    <Head title="Professional profile" />

    <div class="mx-auto w-full max-w-3xl space-y-6 p-4">
        <div>
            <h1 class="text-lg font-semibold">Professional profile</h1>
            <p class="text-sm text-muted-foreground">Update your public information.</p>
        </div>

        <Card>
            <CardHeader>
                <CardTitle>Details</CardTitle>
                <CardDescription>
                    Avatar (image) and resume (PDF) are stored on the public disk.
                </CardDescription>
            </CardHeader>

            <CardContent>
                <Form
                    :action="ProfessionalProfileController.update().url"
                    method="patch"
                    enctype="multipart/form-data"
                    v-slot="{ errors, processing }"
                    class="grid gap-6"
                >
                    <div class="grid gap-2">
                        <Label for="professional_title">Professional title</Label>
                        <Input
                            id="professional_title"
                            name="professional_title"
                            :default-value="String(profile?.professional_title ?? '')"
                            required
                            placeholder="Full-stack developer"
                        />
                        <InputError :message="errors.professional_title" />
                    </div>

                    <div class="grid gap-2">
                        <Label for="bio">Bio</Label>
                        <textarea
                            id="bio"
                            name="bio"
                            rows="6"
                            required
                            class="mt-1 w-full rounded-md border border-input bg-background px-3 py-2 text-sm shadow-sm focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring disabled:cursor-not-allowed disabled:opacity-50"
                            :value="String(profile?.bio ?? '')"
                            placeholder="Tell a bit about your experience..."
                        />
                        <InputError :message="errors.bio" />
                    </div>

                    <div class="grid gap-2">
                        <Label for="avatar">Avatar (image)</Label>
                        <Input id="avatar" name="avatar" type="file" accept="image/*" />
                        <InputError :message="errors.avatar" />
                        <p v-if="profile?.avatar_url" class="text-sm text-muted-foreground">
                            Avatar atual:
                            <a
                                :href="profile.avatar_url"
                                target="_blank"
                                rel="noreferrer"
                                class="underline underline-offset-4"
                                >Open</a
                            >
                        </p>
                    </div>

                    <div class="grid gap-2">
                        <Label for="resume">Resume (PDF)</Label>
                        <Input id="resume" name="resume" type="file" accept="application/pdf" />
                        <InputError :message="errors.resume" />
                        <p v-if="profile?.resume_url" class="text-sm text-muted-foreground">
                            Current resume:
                            <a
                                :href="profile.resume_url"
                                target="_blank"
                                rel="noreferrer"
                                class="underline underline-offset-4"
                                >Open</a
                            >
                        </p>
                    </div>

                    <div class="flex items-center gap-4">
                        <Button type="submit" :disabled="processing">Save</Button>
                        <p class="text-sm text-muted-foreground">Signed in as {{ user.email }}</p>
                    </div>
                </Form>
            </CardContent>
        </Card>
    </div>
</template>
