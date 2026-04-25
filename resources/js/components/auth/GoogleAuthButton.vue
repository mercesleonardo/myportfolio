<script setup lang="ts">
import { Button } from '@/components/ui/button';
import auth from '@/routes/auth';

withDefaults(
    defineProps<{
        tabindex?: number;
        label?: string;
        class?: string;
    }>(),
    {
        tabindex: 0,
        label: 'Continue with Google',
        class: '',
    },
);

const redirectToGoogle = () => {
    window.location.href = auth.google.redirect().url;
};
</script>

<template>
    <Button
        type="button"
        variant="outline"
        :class="['w-full gap-2', $props.class]"
        :tabindex="tabindex"
        @click="redirectToGoogle"
        data-test="google-auth-button"
    >
        <svg aria-hidden="true" viewBox="0 0 48 48" class="h-4 w-4">
            <path
                fill="#FFC107"
                d="M43.611 20.083H42V20H24v8h11.303C33.654 32.657 29.221 36 24 36c-6.627 0-12-5.373-12-12s5.373-12 12-12c3.059 0 5.842 1.154 7.957 3.043l5.657-5.657C34.016 6.053 29.268 4 24 4 12.955 4 4 12.955 4 24s8.955 20 20 20 20-8.955 20-20c0-1.341-.138-2.651-.389-3.917z"
            />
            <path
                fill="#FF3D00"
                d="M6.306 14.691l6.571 4.819C14.655 16.108 19.01 12 24 12c3.059 0 5.842 1.154 7.957 3.043l5.657-5.657C34.016 6.053 29.268 4 24 4c-7.682 0-14.353 4.342-17.694 10.691z"
            />
            <path
                fill="#4CAF50"
                d="M24 44c5.166 0 9.86-1.977 13.409-5.191l-6.19-5.238C29.211 35.091 26.715 36 24 36c-5.198 0-9.615-3.317-11.27-7.946l-6.52 5.025C9.523 39.556 16.227 44 24 44z"
            />
            <path
                fill="#1976D2"
                d="M43.611 20.083H42V20H24v8h11.303a12.07 12.07 0 0 1-4.084 5.571l.003-.002 6.19 5.238C36.971 39.205 44 34 44 24c0-1.341-.138-2.651-.389-3.917z"
            />
        </svg>
        {{ label }}
    </Button>
</template>

