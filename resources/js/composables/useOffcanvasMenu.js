import { ref } from 'vue'

const isOffcanvasOpen = ref(false)
const isMobile = ref(window.innerWidth < 768)

let initialized = false

export function useOffcanvasMenu() {
    const openOffcanvasMenu = () => {
        isOffcanvasOpen.value = true
    }

    const closeOffcanvasMenu = () => {
        isOffcanvasOpen.value = false
    }

    const toggleOffcanvasMenu = () => {
        isOffcanvasOpen.value = !isOffcanvasOpen.value
    }

    if (!initialized) {
        initialized = true

        isOffcanvasOpen.value = !isMobile.value

        const mobileQuery = window.matchMedia('(max-width: 767px)')

        const handleBreakpointChange = (event) => {
            isMobile.value = event.matches
            isOffcanvasOpen.value = !event.matches
        }

        if (typeof mobileQuery.addEventListener === 'function') {
            mobileQuery.addEventListener('change', handleBreakpointChange)
        } else {
            mobileQuery.addListener(handleBreakpointChange)
        }
    }

    return {
        isOffcanvasOpen,
        isMobile,
        openOffcanvasMenu,
        closeOffcanvasMenu,
        toggleOffcanvasMenu,
    }
}