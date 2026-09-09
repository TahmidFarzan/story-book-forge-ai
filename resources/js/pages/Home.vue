<script setup>
import { Head } from '@inertiajs/vue3'
import Layout from "@/pages/layouts/PublicLayout.vue"

import { library } from '@fortawesome/fontawesome-svg-core'
import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome'

import {
    faArrowRight,
    faBook,
    faBookOpen,
    faFeather,
    faFilm,
    faHeart,
    faImage,
    faLightbulb,
    faMicrophone,
    faPalette,
    faPenNib,
    faRocket,
    faSpellCheck,
    faStar,
    faUser,
    faWandMagicSparkles,
} from '@fortawesome/free-solid-svg-icons'

library.add(
    faArrowRight,
    faBook,
    faBookOpen,
    faFeather,
    faFilm,
    faHeart,
    faImage,
    faLightbulb,
    faMicrophone,
    faPalette,
    faPenNib,
    faRocket,
    faSpellCheck,
    faStar,
    faUser,
    faWandMagicSparkles,
)

defineOptions({
    layout: Layout
})

const pipeline = [
    { label: 'Idea', icon: ['fas', 'lightbulb'] },
    { label: 'Story', icon: ['fas', 'feather'] },
    { label: 'Characters', icon: ['fas', 'user'] },
    { label: 'Illustrations', icon: ['fas', 'palette'] },
    { label: 'Storybook', icon: ['fas', 'book-open'] },
]

const steps = [
    {
        number: '01',
        title: 'Imagine',
        text: 'Give the AI your story idea — a world, a character, or just a spark of a scene.',
        icon: ['fas', 'lightbulb'],
    },
    {
        number: '02',
        title: 'Create',
        text: 'AI develops the story, characters, and narrative arc around your idea.',
        icon: ['fas', 'feather'],
    },
    {
        number: '03',
        title: 'Illustrate',
        text: 'Each scene is transformed into a rich, consistent illustration.',
        icon: ['fas', 'palette'],
    },
    {
        number: '04',
        title: 'Narrate',
        text: 'Warm narration is generated for every page of your storybook.',
        icon: ['fas', 'microphone'],
    },
    {
        number: '05',
        title: 'Storybook',
        text: 'Everything comes together into a complete, illustrated storybook.',
        icon: ['fas', 'book-open'],
    },
]

const features = [
    {
        icon: ['fas', 'spell-check'],
        title: 'AI Story Generation',
        text: 'Transform a simple idea into a structured story with a beginning, middle, and end.',
    },
    {
        icon: ['fas', 'user'],
        title: 'Character Creation',
        text: 'Build memorable characters with consistent, cohesive visual identities.',
    },
    {
        icon: ['fas', 'image'],
        title: 'Illustrated Pages',
        text: 'Generate beautiful illustrations tailored to every story scene.',
    },
    {
        icon: ['fas', 'microphone'],
        title: 'Narration',
        text: 'Create natural narration that brings each and every page to life.',
    },
    {
        icon: ['fas', 'film'],
        title: 'Page-by-Page Storytelling',
        text: 'Craft structured pages with story text, scenes, narration, and prompts.',
    },
    {
        icon: ['fas', 'book'],
        title: 'Complete Storybook',
        text: 'Combine all your generated content into one polished story experience.',
    },
]

const whyPoints = [
    {
        icon: ['fas', 'wand-magic-sparkles'],
        title: 'From a single idea',
        text: 'No templates, no starting from scratch. Just describe your story and watch it take shape.',
    },
    {
        icon: ['fas', 'heart'],
        title: 'Crafted with you in mind',
        text: 'Stories, characters, and illustrations are generated around your unique idea.',
    },
    {
        icon: ['fas', 'rocket'],
        title: 'Ready-to-enjoy storybooks',
        text: 'A complete, narrated, illustrated storybook — assembled for you to read and share.',
    },
]

const loginUrl = route('login')
const seeHowUrl = '#how-it-works'
</script>

<template>
    <Head title="Story Book Forge AI — Turn Your Ideas Into Storybooks" />
    <section class="sbfa-hero">
        <div class="sbfa-hero-glow" aria-hidden="true"></div>

        <div class="mx-auto grid max-w-7xl gap-12 px-4 pb-20 pt-14 sm:px-6 lg:grid-cols-2 lg:items-center lg:gap-10 lg:pb-28 lg:pt-20">
            <div>
                <p class="sbfa-eyebrow">
                    <span class="sbfa-eyebrow-dot" aria-hidden="true"></span>
                    AI-Powered Illustrated Storybooks
                </p>

                <h1 class="sbfa-hero-title">
                    Turn Your Ideas Into
                    <span class="sbfa-hero-title-accent">Storybooks</span>
                </h1>

                <p class="sbfa-hero-subtitle">
                    Create beautiful AI-powered storybooks with stories, characters, narration, and illustrations — all from a single idea.
                </p>

                <div class="sbfa-hero-actions">
                    <a :href="loginUrl" class="sbfa-btn sbfa-btn-primary">
                        Create a Storybook
                        <FontAwesomeIcon :icon="['fas', 'arrow-right']" class="text-sm" />
                    </a>

                    <a :href="seeHowUrl" class="sbfa-btn sbfa-btn-ghost">
                        See How It Works
                    </a>
                </div>

                <ul class="sbfa-hero-stats">
                    <li>
                        <span class="sbfa-stat-value">Idea</span>
                        <span class="sbfa-stat-label">to</span>
                    </li>
                    <li>
                        <span class="sbfa-stat-value">Storybook</span>
                        <span class="sbfa-stat-label">in minutes</span>
                    </li>
                </ul>
            </div>

            <div class="sbfa-hero-scene" aria-hidden="true">
                <div class="sbfa-scene-halo"></div>

                <div class="sbfa-openbook">
                    <div class="sbfa-book-page sbfa-book-page-left">
                        <div class="sbfa-page-illustration">
                            <FontAwesomeIcon :icon="['fas', 'wand-magic-sparkles']" class="sbfa-page-illus-icon" />
                        </div>
                        <div class="sbfa-page-line"></div>
                        <div class="sbfa-page-line short"></div>
                        <div class="sbfa-page-line short"></div>
                    </div>

                    <div class="sbfa-book-spine" aria-hidden="true"></div>

                    <div class="sbfa-book-page sbfa-book-page-right">
                        <span class="sbfa-page-caption">Once upon a time...</span>
                        <div class="sbfa-page-line"></div>
                        <div class="sbfa-page-line"></div>
                        <div class="sbfa-page-line short"></div>
                    </div>
                </div>

                <span class="sbfa-float-chip chip-a">Characters</span>
                <span class="sbfa-float-chip chip-b">Narration</span>

                <div class="sbfa-pipeline">
                    <template v-for="(item, index) in pipeline" :key="item.label">
                        <div class="sbfa-pipeline-step">
                            <span class="sbfa-pipeline-icon"><FontAwesomeIcon :icon="item.icon" /></span>
                            <span class="sbfa-pipeline-label">{{ item.label }}</span>
                        </div>
                        <FontAwesomeIcon
                            v-if="index < pipeline.length - 1"
                            :icon="['fas', 'arrow-right']"
                            class="sbfa-pipeline-arrow"
                        />
                    </template>
                </div>
            </div>
        </div>
    </section>
    <section id="how-it-works" class="sbfa-section">
        <div class="mx-auto max-w-7xl px-4 sm:px-6">
            <div class="sbfa-section-head">
                <p class="sbfa-section-kicker">How It Works</p>
                <h2 class="sbfa-section-title">From idea to illustrated storybook</h2>
                <p class="sbfa-section-sub">
                    Five simple steps carry your idea through the whole creative journey.
                </p>
            </div>

            <div class="sbfa-steps">
                <article v-for="step in steps" :key="step.number" class="sbfa-step-card">
                    <div class="sbfa-step-top">
                        <span class="sbfa-step-icon"><FontAwesomeIcon :icon="step.icon" /></span>
                        <span class="sbfa-step-number">{{ step.number }}</span>
                    </div>
                    <h3 class="sbfa-step-title">{{ step.title }}</h3>
                    <p class="sbfa-step-text">{{ step.text }}</p>
                </article>
            </div>
        </div>
    </section>
    <section id="features" class="sbfa-section sbfa-section-soft">
        <div class="mx-auto max-w-7xl px-4 sm:px-6">
            <div class="sbfa-section-head">
                <p class="sbfa-section-kicker">Features</p>
                <h2 class="sbfa-section-title">Everything your story needs</h2>
                <p class="sbfa-section-sub">
                    A complete toolkit for generating stories, characters, narration, and illustrations.
                </p>
            </div>

            <div class="sbfa-features">
                <article v-for="feature in features" :key="feature.title" class="sbfa-feature-card">
                    <div class="sbfa-feature-icon"><FontAwesomeIcon :icon="feature.icon" /></div>
                    <h3 class="sbfa-feature-title">{{ feature.title }}</h3>
                    <p class="sbfa-feature-text">{{ feature.text }}</p>
                </article>
            </div>
        </div>
    </section>
    <section id="preview" class="sbfa-section">
        <div class="mx-auto max-w-7xl px-4 sm:px-6">
            <div class="sbfa-section-head">
                <p class="sbfa-section-kicker">Preview</p>
                <h2 class="sbfa-section-title">A glimpse inside your storybook</h2>
                <p class="sbfa-section-sub">
                    Every page is an illustrated scene with its own text and narration.
                </p>
            </div>

            <div class="sbfa-preview-wrap">
                <div class="sbfa-preview-book">
                    <div class="sbfa-preview-page sbfa-preview-page-left">
                        <span class="sbfa-preview-page-num">01</span>
                        <div class="sbfa-preview-illus">
                            <FontAwesomeIcon :icon="['fas', 'wand-magic-sparkles']" class="sbfa-preview-illus-icon" />
                            <span class="sbfa-preview-illus-sun" aria-hidden="true"></span>
                        </div>
                        <span class="sbfa-preview-caption">The little fox and the lost star</span>
                    </div>

                    <div class="sbfa-preview-spine" aria-hidden="true"></div>

                    <div class="sbfa-preview-page sbfa-preview-page-right">
                        <span class="sbfa-preview-page-num">02</span>
                        <span class="sbfa-preview-title">A quiet beginning</span>
                        <p class="sbfa-preview-text">
                            Once upon a time, deep in the amber woods, a little fox wished upon a star that had tumbled from the night sky...
                        </p>
                        <div class="sbfa-preview-deco-line" aria-hidden="true"></div>
                    </div>
                </div>

                <p class="sbfa-preview-note">
                    <FontAwesomeIcon :icon="['fas', 'star']" class="sbfa-preview-note-icon" />
                    Your own stories are generated around your idea, character by character and page by page.
                </p>
            </div>
        </div>
    </section>
    <section class="sbfa-section sbfa-section-soft">
        <div class="mx-auto max-w-7xl px-4 sm:px-6">
            <div class="sbfa-section-head">
                <p class="sbfa-section-kicker">Why Story Book Forge AI</p>
                <h2 class="sbfa-section-title">Made to bring your imagination to life</h2>
            </div>

            <div class="sbfa-why">
                <article v-for="point in whyPoints" :key="point.title" class="sbfa-why-card">
                    <div class="sbfa-why-icon"><FontAwesomeIcon :icon="point.icon" /></div>
                    <h3 class="sbfa-why-title">{{ point.title }}</h3>
                    <p class="sbfa-why-text">{{ point.text }}</p>
                </article>
            </div>
        </div>
    </section>
    <section class="sbfa-section">
        <div class="mx-auto max-w-7xl px-4 sm:px-6">
            <div class="sbfa-cta">
                <div class="sbfa-cta-glow" aria-hidden="true"></div>
                <span class="sbfa-cta-icon"><FontAwesomeIcon :icon="['fas', 'pen-nib']" /></span>
                <h2 class="sbfa-cta-title">Ready to write your next story?</h2>
                <p class="sbfa-cta-text">
                    Give us your idea and let AI craft it into a beautiful, illustrated storybook you can read and share.
                </p>
                <a :href="loginUrl" class="sbfa-btn sbfa-btn-light">
                    Create a Storybook
                    <FontAwesomeIcon :icon="['fas', 'arrow-right']" class="text-sm" />
                </a>
            </div>
        </div>
    </section>
</template>

<style scoped>
.sbfa-section {
    padding-top: 4.5rem;
    padding-bottom: 4.5rem;
}

.sbfa-section-soft {
    background: var(--story-book-forge-ai-surface-muted);
    border-block: 1px solid var(--story-book-forge-ai-border);
}

.sbfa-section-head {
    max-width: 42rem;
    margin: 0 auto 3rem;
    text-align: center;
}

.sbfa-section-kicker {
    font-size: 0.8125rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.08em;
    color: var(--story-book-forge-ai-accent);
    margin-bottom: 0.75rem;
}

.sbfa-section-title {
    font-size: clamp(1.75rem, 3.5vw, 2.5rem);
    font-weight: 700;
    letter-spacing: -0.02em;
    line-height: 1.15;
    color: var(--story-book-forge-ai-heading);
}

.sbfa-section-sub {
    margin-top: 1rem;
    font-size: 1.05rem;
    line-height: 1.6;
    color: var(--story-book-forge-ai-text-muted);
}
.sbfa-btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 0.6rem;
    padding: 0.85rem 1.5rem;
    border-radius: 999px;
    font-size: 0.95rem;
    font-weight: 600;
    text-decoration: none;
    white-space: nowrap;
    transition: all var(--story-book-forge-ai-transition);
}

.sbfa-btn-primary {
    color: #fff;
    background: linear-gradient(135deg, var(--story-book-forge-ai-accent) 0%, var(--story-book-forge-ai-violet) 100%);
    box-shadow: 0 8px 24px rgb(79 70 229 / 30%);
}

.sbfa-btn-primary:hover {
    transform: translateY(-2px);
    box-shadow: 0 12px 30px rgb(79 70 229 / 38%);
}

.sbfa-btn-ghost {
    color: var(--story-book-forge-ai-text);
    background: var(--story-book-forge-ai-surface);
    border: 1px solid var(--story-book-forge-ai-border);
    box-shadow: 0 2px 8px rgb(43 38 51 / 4%);
}

.sbfa-btn-ghost:hover {
    border-color: var(--story-book-forge-ai-border-focus);
    color: var(--story-book-forge-ai-accent);
    transform: translateY(-2px);
}

.sbfa-btn-light {
    color: var(--story-book-forge-ai-heading);
    background: #fff;
    box-shadow: 0 8px 24px rgb(43 38 51 / 18%);
}

.sbfa-btn-light:hover {
    transform: translateY(-2px);
    box-shadow: 0 12px 30px rgb(43 38 51 / 24%);
}
.sbfa-hero {
    position: relative;
    overflow: hidden;
    background: var(--story-book-forge-ai-background);
}

.sbfa-hero-glow {
    position: absolute;
    top: -12rem;
    right: -10rem;
    width: 36rem;
    height: 36rem;
    border-radius: 50%;
    background: radial-gradient(circle, rgb(199 154 59 / 18%) 0%, rgb(199 154 59 / 0%) 70%);
    pointer-events: none;
}

.sbfa-eyebrow {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.4rem 0.9rem;
    border-radius: 999px;
    background: var(--story-book-forge-ai-gold-soft);
    border: 1px solid rgb(199 154 59 / 35%);
    font-size: 0.8125rem;
    font-weight: 600;
    color: var(--story-book-forge-ai-gold);
    letter-spacing: 0.01em;
}

.sbfa-eyebrow-dot {
    width: 0.5rem;
    height: 0.5rem;
    border-radius: 50%;
    background: var(--story-book-forge-ai-gold);
}

.sbfa-hero-title {
    margin-top: 1.25rem;
    font-size: clamp(2.4rem, 5.5vw, 3.9rem);
    font-weight: 800;
    letter-spacing: -0.03em;
    line-height: 1.05;
    color: var(--story-book-forge-ai-heading);
}

.sbfa-hero-title-accent {
    color: var(--story-book-forge-ai-accent);
    position: relative;
    white-space: nowrap;
}

.sbfa-hero-title-accent::after {
    content: '';
    position: absolute;
    left: 0;
    right: 0;
    bottom: 0.06em;
    height: 0.28em;
    border-radius: 999px;
    background: rgb(199 154 59 / 30%);
    z-index: -1;
}

.sbfa-hero-subtitle {
    margin-top: 1.5rem;
    max-width: 33rem;
    font-size: clamp(1.05rem, 1.8vw, 1.2rem);
    line-height: 1.65;
    color: var(--story-book-forge-ai-text-muted);
}

.sbfa-hero-actions {
    display: flex;
    flex-wrap: wrap;
    gap: 0.75rem;
    margin-top: 2rem;
}

.sbfa-hero-stats {
    display: flex;
    gap: 2rem;
    margin-top: 2.75rem;
    padding-top: 1.75rem;
    border-top: 1px solid var(--story-book-forge-ai-border);
    list-style: none;
}

.sbfa-stat-value {
    display: block;
    font-size: 1.5rem;
    font-weight: 800;
    letter-spacing: -0.02em;
    color: var(--story-book-forge-ai-heading);
}

.sbfa-stat-label {
    display: block;
    margin-top: 0.15rem;
    font-size: 0.8125rem;
    color: var(--story-book-forge-ai-text-muted);
}

.sbfa-hero-scene {
    position: relative;
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 1.75rem;
}

.sbfa-scene-halo {
    position: absolute;
    inset: 10% 5%;
    border-radius: 50%;
    background: radial-gradient(circle, rgb(79 70 229 / 10%) 0%, rgb(199 154 59 / 8%) 55%, rgb(79 70 229 / 0%) 75%);
    filter: blur(10px);
    pointer-events: none;
}

.sbfa-openbook {
    position: relative;
    display: flex;
    width: min(100%, 30rem);
    height: clamp(15rem, 34vw, 20rem);
    filter: drop-shadow(0 24px 40px rgb(43 38 51 / 22%));
    transform-style: preserve-3d;
    animation: sbfa-book-float 6s ease-in-out infinite;
}

@keyframes sbfa-book-float {
    0%, 100% { transform: translateY(0) rotateX(0); }
    50% { transform: translateY(-8px) rotateX(1.5deg); }
}

.sbfa-book-page {
    position: relative;
    width: 50%;
    height: 100%;
    background: linear-gradient(180deg, #FFFDF6 0%, #FBF6E8 100%);
    border: 1px solid #EFE7D3;
    padding: 1.25rem 1.5rem;
    overflow: hidden;
}

.sbfa-book-page-left {
    border-radius: 14px 6px 6px 14px;
    box-shadow: inset -8px 0 18px rgb(199 154 59 / 10%);
}

.sbfa-book-page-right {
    border-radius: 6px 14px 14px 6px;
    box-shadow: inset 8px 0 18px rgb(199 154 59 / 10%);
}

.sbfa-book-spine {
    width: 0.9rem;
    flex-shrink: 0;
    background: linear-gradient(180deg, #4C3F73 0%, #6D5BD0 100%);
    box-shadow: inset -3px 0 6px rgb(0 0 0 / 15%), inset 3px 0 6px rgb(255 255 255 / 12%);
    position: relative;
    z-index: 2;
}

.sbfa-page-illustration {
    display: flex;
    align-items: center;
    justify-content: center;
    height: 45%;
    border-radius: 10px;
    background: linear-gradient(135deg, var(--story-book-forge-ai-accent-soft) 0%, var(--story-book-forge-ai-gold-soft) 100%);
    color: var(--story-book-forge-ai-accent);
    margin-bottom: 0.9rem;
}

.sbfa-page-illus-icon {
    font-size: 2.2rem;
}

.sbfa-page-line {
    height: 6px;
    border-radius: 999px;
    background: linear-gradient(90deg, #E9DFC8 0%, #F3EAD6 100%);
    margin-bottom: 0.5rem;
}

.sbfa-page-line.short {
    width: 70%;
}

.sbfa-page-caption {
    display: block;
    font-family: Georgia, 'Times New Roman', serif;
    font-style: italic;
    font-size: 0.95rem;
    color: var(--story-book-forge-ai-text-muted);
    margin-bottom: 0.9rem;
}

.sbfa-float-chip {
    position: absolute;
    padding: 0.5rem 0.85rem;
    border-radius: 999px;
    font-size: 0.8125rem;
    font-weight: 700;
    box-shadow: 0 10px 24px rgb(43 38 51 / 18%);
    animation: sbfa-float 5s ease-in-out infinite;
}

.chip-a {
    top: 2%;
    left: -2%;
    color: #fff;
    background: linear-gradient(135deg, var(--story-book-forge-ai-accent), var(--story-book-forge-ai-violet));
}

.chip-b {
    bottom: 6%;
    right: -2%;
    color: var(--story-book-forge-ai-heading);
    background: linear-gradient(135deg, var(--story-book-forge-ai-gold), #E7C77B);
    animation-delay: 1.4s;
}

@keyframes sbfa-float {
    0%, 100% { transform: translateY(0); }
    50% { transform: translateY(-6px); }
}

.sbfa-pipeline {
    position: relative;
    z-index: 2;
    display: flex;
    align-items: center;
    flex-wrap: wrap;
    justify-content: center;
    gap: 0.5rem;
    width: 100%;
    max-width: 30rem;
    padding: 0.9rem 1.1rem;
    border-radius: 16px;
    background: var(--story-book-forge-ai-surface);
    border: 1px solid var(--story-book-forge-ai-border);
    box-shadow: var(--story-book-forge-ai-shadow);
}

.sbfa-pipeline-step {
    display: inline-flex;
    align-items: center;
    gap: 0.4rem;
}

.sbfa-pipeline-icon {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 1.7rem;
    height: 1.7rem;
    border-radius: 8px;
    font-size: 0.8rem;
    color: var(--story-book-forge-ai-accent);
    background: var(--story-book-forge-ai-accent-soft);
}

.sbfa-pipeline-label {
    font-size: 0.8125rem;
    font-weight: 600;
    color: var(--story-book-forge-ai-text);
}

.sbfa-pipeline-arrow {
    font-size: 0.7rem;
    color: var(--story-book-forge-ai-text-muted);
}
.sbfa-steps {
    display: grid;
    grid-template-columns: repeat(5, 1fr);
    gap: 1.25rem;
}

.sbfa-step-card {
    padding: 1.75rem 1.25rem;
    border-radius: var(--story-book-forge-ai-radius);
    background: var(--story-book-forge-ai-surface);
    border: 1px solid var(--story-book-forge-ai-border);
    box-shadow: var(--story-book-forge-ai-shadow);
    transition: transform var(--story-book-forge-ai-transition), box-shadow var(--story-book-forge-ai-transition);
}

.sbfa-step-card:hover {
    transform: translateY(-4px);
    box-shadow: var(--story-book-forge-ai-shadow-lg);
}

.sbfa-step-top {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 1.1rem;
}

.sbfa-step-icon {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 3rem;
    height: 3rem;
    border-radius: 12px;
    font-size: 1.25rem;
    color: #fff;
    background: linear-gradient(135deg, var(--story-book-forge-ai-accent) 0%, var(--story-book-forge-ai-violet) 100%);
}

.sbfa-step-number {
    font-size: 1rem;
    font-weight: 800;
    letter-spacing: -0.02em;
    color: var(--story-book-forge-ai-gold);
}

.sbfa-step-title {
    font-size: 1.1rem;
    font-weight: 700;
    letter-spacing: -0.01em;
    color: var(--story-book-forge-ai-heading);
}

.sbfa-step-text {
    margin-top: 0.5rem;
    font-size: 0.9rem;
    line-height: 1.55;
    color: var(--story-book-forge-ai-text-muted);
}
.sbfa-features {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 1.5rem;
}

.sbfa-feature-card {
    padding: 2rem 1.75rem;
    border-radius: var(--story-book-forge-ai-radius);
    background: var(--story-book-forge-ai-surface);
    border: 1px solid var(--story-book-forge-ai-border);
    box-shadow: var(--story-book-forge-ai-shadow);
    transition: transform var(--story-book-forge-ai-transition), box-shadow var(--story-book-forge-ai-transition);
}

.sbfa-feature-card:hover {
    transform: translateY(-4px);
    box-shadow: var(--story-book-forge-ai-shadow-lg);
}

.sbfa-feature-icon {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 3.25rem;
    height: 3.25rem;
    border-radius: 14px;
    font-size: 1.3rem;
    color: var(--story-book-forge-ai-accent);
    background: var(--story-book-forge-ai-accent-soft);
    margin-bottom: 1.25rem;
}

.sbfa-feature-title {
    font-size: 1.15rem;
    font-weight: 700;
    letter-spacing: -0.01em;
    color: var(--story-book-forge-ai-heading);
}

.sbfa-feature-text {
    margin-top: 0.6rem;
    font-size: 0.95rem;
    line-height: 1.6;
    color: var(--story-book-forge-ai-text-muted);
}
.sbfa-preview-wrap {
    max-width: 56rem;
    margin: 0 auto;
    display: flex;
    flex-direction: column;
    align-items: center;
}

.sbfa-preview-book {
    display: flex;
    width: 100%;
    height: clamp(20rem, 42vw, 27rem);
    filter: drop-shadow(0 28px 48px rgb(43 38 51 / 24%));
    border-radius: 6px;
    overflow: hidden;
}

.sbfa-preview-page {
    display: flex;
    flex-direction: column;
    width: 50%;
    padding: 1.75rem 2rem;
    background: linear-gradient(180deg, #FFFDF6 0%, #FCF6E8 100%);
    border: 1px solid #EFE7D3;
    position: relative;
}

.sbfa-preview-page-left {
    border-radius: 16px 4px 4px 16px;
    box-shadow: inset -10px 0 22px rgb(199 154 59 / 12%);
}

.sbfa-preview-page-right {
    border-radius: 4px 16px 16px 4px;
    box-shadow: inset 10px 0 22px rgb(199 154 59 / 12%);
}

.sbfa-preview-spine {
    width: 1.1rem;
    flex-shrink: 0;
    background: linear-gradient(180deg, #4C3F73 0%, #6D5BD0 100%);
    box-shadow: inset -3px 0 6px rgb(0 0 0 / 18%), inset 3px 0 6px rgb(255 255 255 / 12%);
}

.sbfa-preview-page-num {
    position: absolute;
    bottom: 1.1rem;
    font-size: 0.8rem;
    font-weight: 700;
    color: var(--story-book-forge-ai-gold);
    letter-spacing: 0.08em;
}

.sbfa-preview-page-left .sbfa-preview-page-num { left: 1.75rem; }
.sbfa-preview-page-right .sbfa-preview-page-num { right: 1.75rem; }

.sbfa-preview-illus {
    position: relative;
    flex: 1;
    border-radius: 14px;
    background: linear-gradient(135deg, var(--story-book-forge-ai-accent-soft) 0%, var(--story-book-forge-ai-gold-soft) 100%);
    overflow: hidden;
    display: flex;
    align-items: center;
    justify-content: center;
}

.sbfa-preview-illus-icon {
    font-size: 3.5rem;
    color: var(--story-book-forge-ai-violet);
    position: relative;
    z-index: 1;
}

.sbfa-preview-illus-sun {
    position: absolute;
    top: -2rem;
    right: -2rem;
    width: 7rem;
    height: 7rem;
    border-radius: 50%;
    background: radial-gradient(circle, rgb(199 154 59 / 30%) 0%, rgb(199 154 59 / 0%) 70%);
}

.sbfa-preview-caption {
    display: block;
    margin-top: 1rem;
    font-family: Georgia, 'Times New Roman', serif;
    font-style: italic;
    font-size: clamp(0.85rem, 1.6vw, 1.05rem);
    color: var(--story-book-forge-ai-text-muted);
    text-align: center;
}

.sbfa-preview-title {
    font-family: Georgia, 'Times New Roman', serif;
    font-size: clamp(1.1rem, 2.2vw, 1.5rem);
    font-weight: 700;
    color: var(--story-book-forge-ai-heading);
    letter-spacing: -0.01em;
}

.sbfa-preview-text {
    margin-top: 1rem;
    font-size: clamp(0.9rem, 1.7vw, 1.05rem);
    line-height: 1.7;
    color: var(--story-book-forge-ai-text);
    font-family: Georgia, 'Times New Roman', serif;
}

.sbfa-preview-deco-line {
    margin-top: auto;
    height: 1px;
    background: linear-gradient(90deg, var(--story-book-forge-ai-gold-soft), var(--story-book-forge-ai-gold));
    width: 60%;
}

.sbfa-preview-note {
    display: flex;
    align-items: center;
    gap: 0.6rem;
    margin-top: 2rem;
    max-width: 34rem;
    text-align: center;
    justify-content: center;
    font-size: 0.95rem;
    line-height: 1.6;
    color: var(--story-book-forge-ai-text-muted);
}

.sbfa-preview-note-icon {
    color: var(--story-book-forge-ai-gold);
    flex-shrink: 0;
}
.sbfa-why {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 1.5rem;
}

.sbfa-why-card {
    text-align: center;
    padding: 2.5rem 2rem;
    border-radius: var(--story-book-forge-ai-radius);
    background: var(--story-book-forge-ai-surface);
    border: 1px solid var(--story-book-forge-ai-border);
    box-shadow: var(--story-book-forge-ai-shadow);
}

.sbfa-why-icon {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 3.5rem;
    height: 3.5rem;
    border-radius: 50%;
    font-size: 1.4rem;
    color: #fff;
    background: linear-gradient(135deg, var(--story-book-forge-ai-gold) 0%, #E7C77B 100%);
    box-shadow: 0 8px 20px rgb(199 154 59 / 30%);
}

.sbfa-why-title {
    margin-top: 1.25rem;
    font-size: 1.2rem;
    font-weight: 700;
    letter-spacing: -0.01em;
    color: var(--story-book-forge-ai-heading);
}

.sbfa-why-text {
    margin-top: 0.6rem;
    font-size: 0.95rem;
    line-height: 1.6;
    color: var(--story-book-forge-ai-text-muted);
}
.sbfa-cta {
    position: relative;
    overflow: hidden;
    text-align: center;
    padding: 4rem 2rem;
    border-radius: 24px;
    background: var(--story-book-forge-ai-header-bg);
    color: var(--story-book-forge-ai-text-inverse);
    box-shadow: var(--story-book-forge-ai-shadow-lg);
}

.sbfa-cta-glow {
    position: absolute;
    top: -6rem;
    left: 50%;
    transform: translateX(-50%);
    width: 30rem;
    height: 18rem;
    border-radius: 50%;
    background: radial-gradient(circle, rgb(199 154 59 / 18%) 0%, rgb(79 70 229 / 12%) 45%, rgb(79 70 229 / 0%) 70%);
    pointer-events: none;
}

.sbfa-cta-icon {
    position: relative;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 3.25rem;
    height: 3.25rem;
    border-radius: 50%;
    font-size: 1.3rem;
    color: var(--story-book-forge-ai-heading);
    background: linear-gradient(135deg, var(--story-book-forge-ai-gold) 0%, #E7C77B 100%);
    margin-bottom: 1.5rem;
}

.sbfa-cta-title {
    position: relative;
    font-size: clamp(1.7rem, 4vw, 2.5rem);
    font-weight: 800;
    letter-spacing: -0.02em;
    color: #fff;
}

.sbfa-cta-text {
    position: relative;
    margin: 1rem auto 0;
    max-width: 34rem;
    font-size: 1.05rem;
    line-height: 1.6;
    color: var(--story-book-forge-ai-header-text-muted);
}

.sbfa-cta .sbfa-btn-light {
    position: relative;
    margin-top: 2rem;
}
@media (max-width: 1024px) {
    .sbfa-steps {
        grid-template-columns: repeat(3, 1fr);
    }

    .sbfa-step-card:nth-child(4),
    .sbfa-step-card:nth-child(5) {
        grid-column: span 1;
    }
}

@media (max-width: 900px) {
    .sbfa-features,
    .sbfa-why {
        grid-template-columns: repeat(2, 1fr);
    }

    .sbfa-hero-stats {
        gap: 1.5rem;
    }
}

@media (max-width: 768px) {
    .sbfa-section {
        padding-top: 3.5rem;
        padding-bottom: 3.5rem;
    }

    .sbfa-section-head {
        margin-bottom: 2.25rem;
    }

    .sbfa-hero {
        padding-bottom: 1rem;
    }

    .sbfa-hero-scene {
        margin-top: 0.5rem;
    }

    .sbfa-pipeline {
        flex-wrap: wrap;
    }
}

@media (max-width: 640px) {
    .sbfa-steps {
        grid-template-columns: 1fr;
    }

    .sbfa-features,
    .sbfa-why {
        grid-template-columns: 1fr;
    }

    .sbfa-hero-stats {
        flex-direction: column;
        gap: 1rem;
        align-items: flex-start;
    }

    .sbfa-hero-actions .sbfa-btn {
        width: 100%;
    }

    .sbfa-preview-book {
        height: auto;
        min-height: 22rem;
    }

    .sbfa-preview-page {
        padding: 1.25rem 1.25rem;
    }

    .sbfa-preview-text {
        font-size: 0.85rem;
    }

    .sbfa-cta {
        padding: 3rem 1.25rem;
    }
}
@media (prefers-reduced-motion: reduce) {
    .sbfa-openbook,
    .sbfa-float-chip {
        animation: none;
    }

    .sbfa-btn,
    .sbfa-step-card,
    .sbfa-feature-card {
        transition: none;
    }
}
</style>
