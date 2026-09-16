<script setup>
import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome'
import { library } from '@fortawesome/fontawesome-svg-core'
import { faBookOpen, faWandMagicSparkles } from '@fortawesome/free-solid-svg-icons'

library.add(faBookOpen, faWandMagicSparkles)
</script>

<template>
    <div class="sbfa-auth-shell">
        <div class="sbfa-auth-card">
            <div class="sbfa-auth-visual">
                <a :href="route('home')" class="sbfa-auth-brand">
                    <span class="sbfa-auth-brand-mark">
                        <FontAwesomeIcon :icon="['fas', 'book-open']" />
                    </span>
                    <span class="sbfa-auth-brand-name">
                        Story Book Forge AI
                        <span>Ai storybook studio</span>
                    </span>
                </a>

                <div class="sbfa-auth-visual-book" aria-hidden="true">
                    <div class="sbfa-auth-visual-page is-back">
                        <span class="sbfa-auth-page-line"></span>
                        <span class="sbfa-auth-page-line short"></span>
                    </div>
                    <div class="sbfa-auth-visual-page">
                        <FontAwesomeIcon :icon="['fas', 'wand-magic-sparkles']" class="sbfa-auth-visual-title" style="font-size: 1.4rem" />
                        <span class="sbfa-auth-page-line"></span>
                        <span class="sbfa-auth-page-line"></span>
                        <span class="sbfa-auth-page-line short"></span>
                    </div>
                    <div class="sbfa-auth-visual-page is-back">
                        <span class="sbfa-auth-page-line"></span>
                        <span class="sbfa-auth-page-line short"></span>
                    </div>
                </div>

                <div class="sbfa-auth-visual-copy">
                    <p class="sbfa-auth-visual-title">
                        <FontAwesomeIcon :icon="['fas', 'wand-magic-sparkles']" />
                        <slot name="visual-title" />
                    </p>
                    <p class="sbfa-auth-visual-sub">
                        <slot name="visual-sub" />
                    </p>
                </div>
            </div>

            <div class="sbfa-auth-form-col">
                <slot />
            </div>
        </div>
    </div>
</template>

<style>
.sbfa-input,
.sbfa-auth-title,
.sbfa-auth-subtitle,
.sbfa-auth-link,
.sbfa-auth-footer-text,
.sbfa-error,
.sbfa-field-label,
.sbfa-auth-visual-title,
.sbfa-auth-visual-sub,
.sbfa-auth-brand-name,
.sbfa-auth-primary {
    transition: color var(--transition), border-color var(--transition), box-shadow var(--transition), background var(--transition);
}

.sbfa-auth-shell {
    min-height: calc(100vh - 16rem);
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 2rem 1rem;
}

.sbfa-auth-card {
    display: grid;
    grid-template-columns: 1fr 1.1fr;
    width: 100%;
    max-width: 60rem;
    min-height: 34rem;
    border-radius: 24px;
    overflow: hidden;
    background: var(--surface);
    border: 1px solid var(--border);
    box-shadow: var(--shadow-lg);
    animation: sbfa-auth-in 0.35s ease-out;
}

@keyframes sbfa-auth-in {
    from {
        opacity: 0;
        transform: translateY(10px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.sbfa-auth-visual {
    position: relative;
    display: flex;
    flex-direction: column;
    justify-content: space-between;
    padding: 2.5rem 2rem;
    background: var(--header-bg);
    color: var(--text-inverse);
    overflow: hidden;
}

.sbfa-auth-visual::before {
    content: '';
    position: absolute;
    top: -6rem;
    right: -6rem;
    width: 16rem;
    height: 16rem;
    border-radius: 50%;
    background: radial-gradient(circle, rgb(199 154 59 / 20%) 0%, rgb(199 154 59 / 0%) 70%);
}

.sbfa-auth-visual::after {
    content: '';
    position: absolute;
    bottom: -8rem;
    left: -6rem;
    width: 18rem;
    height: 18rem;
    border-radius: 50%;
    background: radial-gradient(circle, rgb(109 91 208 / 35%) 0%, rgb(109 91 208 / 0%) 70%);
}

.sbfa-auth-brand {
    position: relative;
    z-index: 1;
    display: inline-flex;
    align-items: center;
    gap: 0.75rem;
    text-decoration: none;
}

.sbfa-auth-brand-mark {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 2.5rem;
    height: 2.5rem;
    border-radius: 12px;
    color: #fff;
    background: linear-gradient(135deg, var(--accent) 0%, var(--violet) 100%);
    box-shadow: 0 6px 16px rgb(0 0 0 / 25%);
}

.sbfa-auth-brand-name {
    font-size: 0.95rem;
    font-weight: 700;
    line-height: 1.1;
    color: #fff;
    letter-spacing: -0.01em;
}

.sbfa-auth-brand-name span {
    display: block;
    font-size: 0.72rem;
    font-weight: 600;
    color: var(--header-text-muted);
}

.sbfa-auth-visual-book {
    position: relative;
    z-index: 1;
    display: flex;
    gap: 0.6rem;
    align-items: flex-end;
    margin: 1.5rem 0;
}

.sbfa-auth-visual-page {
    flex: 1;
    height: 10rem;
    border-radius: 10px;
    background: linear-gradient(180deg, rgb(255 255 255 / 92%) 0%, rgb(247 238 217 / 80%) 100%);
    box-shadow: 0 14px 34px rgb(0 0 0 / 25%);
    display: flex;
    flex-direction: column;
    padding: 0.75rem;
    gap: 0.45rem;
}

.sbfa-auth-visual-page.is-back {
    height: 8.5rem;
    opacity: 0.9;
}

.sbfa-auth-page-line {
    height: 5px;
    border-radius: 999px;
    background: linear-gradient(90deg, #E9DFC8 0%, #F3EAD6 100%);
}

.sbfa-auth-page-line.short {
    width: 60%;
}

.sbfa-auth-visual-copy {
    position: relative;
    z-index: 1;
}

.sbfa-auth-visual-title {
    display: flex;
    align-items: center;
    gap: 0.6rem;
    font-size: 1.25rem;
    font-weight: 700;
    color: #fff;
    letter-spacing: -0.01em;
}

.sbfa-auth-visual-title svg {
    color: var(--gold);
}

.sbfa-auth-visual-sub {
    margin-top: 0.5rem;
    font-size: 0.875rem;
    line-height: 1.6;
    color: var(--header-text-muted);
}

.sbfa-auth-form-col {
    display: flex;
    flex-direction: column;
    justify-content: center;
    padding: 2.5rem 2.5rem;
}

.sbfa-auth-header {
    margin-bottom: 1.75rem;
}

.sbfa-auth-title {
    font-size: 1.5rem;
    font-weight: 700;
    letter-spacing: -0.02em;
    color: var(--heading);
}

.sbfa-auth-subtitle {
    margin-top: 0.5rem;
    font-size: 0.875rem;
    line-height: 1.55;
    color: var(--text-muted);
}

.sbfa-auth-form {
    display: flex;
    flex-direction: column;
    gap: 1.25rem;
}

.sbfa-field {
    display: flex;
    flex-direction: column;
    gap: 0.4rem;
}

.sbfa-field-label {
    font-size: 0.8125rem;
    font-weight: 500;
    color: var(--text);
}

.sbfa-input {
    width: 100%;
    height: 2.85rem;
    padding: 0 0.9rem;
    font-size: 0.9rem;
    color: var(--text);
    background: var(--surface);
    border: 1px solid var(--border);
    border-radius: var(--radius-sm);
    outline: none;
    transition: border-color var(--transition), box-shadow var(--transition);
}

.sbfa-input::placeholder {
    color: var(--text-muted);
    opacity: 0.6;
}

.sbfa-input:focus {
    border-color: var(--border-focus);
    box-shadow: var(--focus-ring);
}

.sbfa-input.has-error {
    border-color: var(--danger);
}

.sbfa-input.has-error:focus {
    border-color: var(--danger);
    box-shadow: 0 0 0 3px rgb(220 76 100 / 15%);
}

.sbfa-input-wrap {
    position: relative;
}

.sbfa-input-password {
    padding-right: 2.85rem;
}

.sbfa-input-toggle {
    position: absolute;
    right: 0.75rem;
    top: 50%;
    transform: translateY(-50%);
    color: var(--text-muted);
    padding: 0.3rem;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    transition: color var(--transition);
}

.sbfa-input-toggle:hover {
    color: var(--text);
}

.sbfa-error {
    font-size: 0.8125rem;
    color: var(--danger);
    line-height: 1.4;
}

.sbfa-auth-primary {
    width: 100%;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 0.55rem;
    height: 3rem;
    font-size: 0.9rem;
    font-weight: 600;
    color: #fff;
    background: linear-gradient(135deg, var(--accent) 0%, var(--violet) 100%);
    border: none;
    border-radius: var(--radius-sm);
    cursor: pointer;
    box-shadow: 0 8px 20px rgb(79 70 229 / 28%);
    transition: transform var(--transition), box-shadow var(--transition), background var(--transition);
}

.sbfa-auth-primary:hover:not(:disabled) {
    transform: translateY(-1px);
    box-shadow: 0 12px 26px rgb(79 70 229 / 36%);
}

.sbfa-auth-primary:active:not(:disabled) {
    background: var(--accent-active);
}

.sbfa-auth-primary:focus-visible {
    outline: 0;
    box-shadow: var(--focus-ring);
}

.sbfa-auth-primary:disabled {
    opacity: 0.55;
    cursor: not-allowed;
}

.sbfa-auth-footer {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 1rem;
    margin-top: 1.5rem;
    padding-top: 1.25rem;
    border-top: 1px solid var(--border);
}

.sbfa-auth-footer.is-center {
    justify-content: center;
}

.sbfa-auth-footer-text {
    font-size: 0.8125rem;
    color: var(--text-muted);
}

.sbfa-auth-link {
    font-size: 0.8125rem;
    font-weight: 600;
    color: var(--accent);
    text-decoration: none;
    transition: color var(--transition);
}

.sbfa-auth-link:hover {
    color: var(--accent-hover);
}

@media (max-width: 768px) {
    .sbfa-auth-card {
        grid-template-columns: 1fr;
        min-height: 0;
        max-width: 28rem;
    }

    .sbfa-auth-visual {
        display: none;
    }

    .sbfa-auth-form-col {
        padding: 2rem 1.5rem;
    }
}

@media (max-width: 480px) {
    .sbfa-auth-shell {
        padding: 1.5rem 0.75rem;
    }

    .sbfa-auth-form-col {
        padding: 1.75rem 1.25rem;
    }

    .sbfa-auth-title {
        font-size: 1.3rem;
    }

    .sbfa-auth-footer {
        flex-direction: column;
        align-items: flex-start;
        gap: 0.6rem;
    }

    .sbfa-auth-footer.is-center {
        align-items: center;
    }
}

@media (prefers-reduced-motion: reduce) {
    .sbfa-auth-card {
        animation: none;
    }

    .sbfa-auth-primary {
        transition: none;
    }
}
</style>
