import { mount, VueWrapper } from '@vue/test-utils'
import { nextTick } from 'vue'
import { vi, beforeEach, afterEach, describe, test, expect } from 'vitest'

import Profile from '@/Pages/Profile.vue'
import type { PublicUser, PresentationListItem, PaginatedData } from '@/types'

// Mock Inertia router - use globalThis to store mocks (works with hoisting)
vi.mock('@inertiajs/vue3', () => {
    const mockRouterGet = vi.fn()
    const mockRouterVisit = vi.fn()

    // Store mocks in globalThis so they're accessible in tests
    ;(globalThis as any).__mockRouterGet = mockRouterGet
    ;(globalThis as any).__mockRouterVisit = mockRouterVisit

    return {
        router: {
            get: mockRouterGet,
            visit: mockRouterVisit,
        },
        Link: {
            name: 'Link',
            template: '<a :href="href"><slot /></a>',
            props: ['href', 'preserveState', 'preserveScroll'],
        },
        Head: {
            name: 'Head',
            template: '<div><slot /></div>',
            props: ['title'],
        },
    }
})

// Access the mocks from globalThis
// Note: vi.mock is hoisted, so the factory runs first and populates globalThis
const getMockRouterGet = () => (globalThis as any).__mockRouterGet as ReturnType<typeof vi.fn>
const getMockRouterVisit = () => (globalThis as any).__mockRouterVisit as ReturnType<typeof vi.fn>

// Create convenience references that will be used in tests
let mockRouterGet: ReturnType<typeof vi.fn>
let mockRouterVisit: ReturnType<typeof vi.fn>

// Mock route helper
const mockRoute = vi.fn((name: string, params?: Record<string, any>) => {
    if (name === 'profile.show') {
        return `/users/${params?.user || 'testuser'}`
    }
    if (name === 'presentations.show') {
        return `/users/${params?.user || 'testuser'}/${params?.slug || 'test-slug'}`
    }
    return `/${name}`
})

global.route = mockRoute

// Mock AppHead component
vi.mock('@/Components/AppHead.vue', () => ({
    default: {
        name: 'AppHead',
        template: '<div></div>',
    },
}))

describe('Profile.vue', () => {
    let wrapper: VueWrapper<any>

    const mockUser: PublicUser = {
        id: 1,
        name: 'John Doe',
        username: 'johndoe',
    }

    const mockPresentations: PresentationListItem[] = [
        {
            id: 1,
            title: 'First Presentation',
            slug: 'first-presentation',
            description: 'This is the first presentation description',
            updated_at: '2024-01-15 10:00:00',
        },
        {
            id: 2,
            title: 'Second Presentation',
            slug: 'second-presentation',
            description: 'This is the second presentation description',
            updated_at: '2024-01-14 09:00:00',
        },
        {
            id: 3,
            title: 'Third Presentation',
            slug: 'third-presentation',
            description: null,
            updated_at: '2024-01-13 08:00:00',
        },
    ]

    const mockPaginatedData: PaginatedData<PresentationListItem> = {
        data: mockPresentations,
        current_page: 1,
        first_page_url: '/users/johndoe?page=1',
        from: 1,
        last_page: 1,
        last_page_url: '/users/johndoe?page=1',
        links: [
            { url: null, label: '&laquo; Previous', active: false },
            { url: '/users/johndoe?page=1', label: '1', active: true },
            { url: null, label: 'Next &raquo;', active: false },
        ],
        next_page_url: null,
        path: '/users/johndoe',
        per_page: 15,
        prev_page_url: null,
        to: 3,
        total: 3,
    }

    beforeEach(() => {
        // Initialize mock references from globalThis
        mockRouterGet = getMockRouterGet()
        mockRouterVisit = getMockRouterVisit()
        vi.clearAllMocks()
        vi.useFakeTimers()
    })

    afterEach(() => {
        vi.useRealTimers()
        wrapper?.unmount()
    })

    const mountWrapper = (props: {
        user: PublicUser
        presentations: PaginatedData<PresentationListItem>
        search?: string
        auth?: any
    }): VueWrapper<any> => {
        return mount(Profile, {
            props: {
                user: props.user,
                presentations: props.presentations,
                search: props.search || '',
                auth: props.auth,
            },
        })
    }

    describe('Rendering', () => {
        test('renders user name and username correctly', () => {
            wrapper = mountWrapper({
                user: mockUser,
                presentations: mockPaginatedData,
            })

            expect(wrapper.text()).toContain("John Doe's Presentations")
            expect(wrapper.text()).toContain('@johndoe')
        })

        test('renders all presentations', () => {
            wrapper = mountWrapper({
                user: mockUser,
                presentations: mockPaginatedData,
            })

            expect(wrapper.text()).toContain('First Presentation')
            expect(wrapper.text()).toContain('This is the first presentation description')
            expect(wrapper.text()).toContain('Second Presentation')
            expect(wrapper.text()).toContain('Third Presentation')
        })

        test('renders presentation without description', () => {
            wrapper = mountWrapper({
                user: mockUser,
                presentations: mockPaginatedData,
            })

            const thirdPresentation = wrapper.findAll('button').find((btn) =>
                btn.text().includes('Third Presentation')
            )
            expect(thirdPresentation).toBeTruthy()
        })

        test('renders updated_at for each presentation', () => {
            wrapper = mountWrapper({
                user: mockUser,
                presentations: mockPaginatedData,
            })

            expect(wrapper.text()).toContain('Updated 2024-01-15 10:00:00')
            expect(wrapper.text()).toContain('Updated 2024-01-14 09:00:00')
        })

        test('renders search input with correct placeholder', () => {
            wrapper = mountWrapper({
                user: mockUser,
                presentations: mockPaginatedData,
            })

            const searchInput = wrapper.find('#search')
            expect(searchInput.exists()).toBe(true)
            expect(searchInput.attributes('placeholder')).toBe('Search by title or description...')
        })

        test('initializes search input with search prop value', () => {
            wrapper = mountWrapper({
                user: mockUser,
                presentations: mockPaginatedData,
                search: 'test query',
            })

            const searchInput = wrapper.find('#search')
            expect((searchInput.element as HTMLInputElement).value).toBe('test query')
        })
    })

    describe('Search functionality', () => {
        test('debounces search input changes', async () => {
            wrapper = mountWrapper({
                user: mockUser,
                presentations: mockPaginatedData,
            })

            const searchInput = wrapper.find('#search')
            await searchInput.setValue('test')

            // Should not call router.get immediately
            expect(mockRouterGet).not.toHaveBeenCalled()

            // Fast-forward 300ms
            vi.advanceTimersByTime(300)

            await nextTick()

            // Now it should be called
            expect(mockRouterGet).toHaveBeenCalledTimes(1)
            expect(mockRouterGet).toHaveBeenCalledWith(
                '/users/johndoe',
                { search: 'test' },
                {
                    preserveState: true,
                    preserveScroll: true,
                    only: ['presentations', 'search'],
                }
            )
        })

        test('cancels previous search timeout when typing quickly', async () => {
            wrapper = mountWrapper({
                user: mockUser,
                presentations: mockPaginatedData,
            })

            const searchInput = wrapper.find('#search')

            await searchInput.setValue('t')
            vi.advanceTimersByTime(200)

            await searchInput.setValue('te')
            vi.advanceTimersByTime(200)

            await searchInput.setValue('test')
            vi.advanceTimersByTime(300)

            await nextTick()

            // Should only be called once with the final value
            expect(mockRouterGet).toHaveBeenCalledTimes(1)
            expect(mockRouterGet).toHaveBeenCalledWith(
                '/users/johndoe',
                { search: 'test' },
                expect.any(Object)
            )
        })

        test('uses correct route helper for search', async () => {
            wrapper = mountWrapper({
                user: mockUser,
                presentations: mockPaginatedData,
            })

            const searchInput = wrapper.find('#search')
            await searchInput.setValue('query')
            vi.advanceTimersByTime(300)

            await nextTick()

            expect(mockRoute).toHaveBeenCalledWith('profile.show', { user: 'johndoe' })
        })
    })

    describe('Pagination', () => {
        test('renders pagination links when last_page > 1', () => {
            const paginatedDataWithPages: PaginatedData<PresentationListItem> = {
                ...mockPaginatedData,
                last_page: 3,
                links: [
                    { url: null, label: '&laquo; Previous', active: false },
                    { url: '/users/johndoe?page=1', label: '1', active: true },
                    { url: '/users/johndoe?page=2', label: '2', active: false },
                    { url: '/users/johndoe?page=3', label: '3', active: false },
                    { url: '/users/johndoe?page=2', label: 'Next &raquo;', active: false },
                ],
            }

            wrapper = mountWrapper({
                user: mockUser,
                presentations: paginatedDataWithPages,
            })

            const paginationLinks = wrapper.findAll('a[href*="page"]')
            expect(paginationLinks.length).toBeGreaterThan(0)
        })

        test('does not render pagination when last_page is 1', () => {
            wrapper = mountWrapper({
                user: mockUser,
                presentations: mockPaginatedData,
            })

            const paginationSection = wrapper.find('.flex.justify-center.items-center.space-x-2')
            expect(paginationSection.exists()).toBe(false)
        })

        test('renders pagination links with correct hrefs', () => {
            const paginatedDataWithPages: PaginatedData<PresentationListItem> = {
                ...mockPaginatedData,
                last_page: 2,
                links: [
                    { url: null, label: '&laquo; Previous', active: false },
                    { url: '/users/johndoe?page=1', label: '1', active: true },
                    { url: '/users/johndoe?page=2', label: '2', active: false },
                    { url: '/users/johndoe?page=2', label: 'Next &raquo;', active: false },
                ],
            }

            wrapper = mountWrapper({
                user: mockUser,
                presentations: paginatedDataWithPages,
            })

            const links = wrapper.findAll('a')
            const pageLinks = links.filter((link) => link.attributes('href')?.includes('page'))
            expect(pageLinks.length).toBeGreaterThan(0)
        })
    })

    describe('Empty states', () => {
        test('renders empty state when no presentations', () => {
            const emptyPaginatedData: PaginatedData<PresentationListItem> = {
                ...mockPaginatedData,
                data: [],
                total: 0,
            }

            wrapper = mountWrapper({
                user: mockUser,
                presentations: emptyPaginatedData,
            })

            expect(wrapper.text()).toContain('No presentations yet')
            expect(wrapper.text()).toContain("This user hasn't published any presentations yet.")
        })

        test('renders search empty state when search has no results', () => {
            const emptyPaginatedData: PaginatedData<PresentationListItem> = {
                ...mockPaginatedData,
                data: [],
                total: 0,
            }

            wrapper = mountWrapper({
                user: mockUser,
                presentations: emptyPaginatedData,
                search: 'nonexistent',
            })

            expect(wrapper.text()).toContain('No presentations found')
            expect(wrapper.text()).toContain('Try adjusting your search terms.')
        })
    })

    describe('Navigation', () => {
        test('calls router.visit when clicking on a presentation', async () => {
            wrapper = mountWrapper({
                user: mockUser,
                presentations: mockPaginatedData,
            })

            const firstPresentationButton = wrapper.findAll('button').find((btn) =>
                btn.text().includes('First Presentation')
            )

            expect(firstPresentationButton).toBeTruthy()
            await firstPresentationButton!.trigger('click')

            expect(mockRouterVisit).toHaveBeenCalledTimes(1)
            expect(mockRoute).toHaveBeenCalledWith('presentations.show', {
                user: 'johndoe',
                slug: 'first-presentation',
            })
        })

        test('uses correct username from props when navigating', async () => {
            const differentUser: PublicUser = {
                id: 2,
                name: 'Jane Smith',
                username: 'janesmith',
            }

            wrapper = mountWrapper({
                user: differentUser,
                presentations: mockPaginatedData,
            })

            const firstPresentationButton = wrapper.findAll('button').find((btn) =>
                btn.text().includes('First Presentation')
            )

            await firstPresentationButton!.trigger('click')

            expect(mockRoute).toHaveBeenCalledWith('presentations.show', {
                user: 'janesmith',
                slug: 'first-presentation',
            })
        })
    })

    describe('Header navigation', () => {
        test('renders Home link', () => {
            wrapper = mountWrapper({
                user: mockUser,
                presentations: mockPaginatedData,
            })

            const homeLink = wrapper.findAll('a').find((link) => link.text().includes('Home'))
            expect(homeLink?.attributes('href')).toBe('/')
        })

        test('renders Dashboard link when authenticated', () => {
            wrapper = mountWrapper({
                user: mockUser,
                presentations: mockPaginatedData,
                auth: { user: { id: 1, name: 'John Doe' } },
            })

            const dashboardLink = wrapper.findAll('a').find((link) => link.text().includes('Dashboard'))
            expect(dashboardLink?.attributes('href')).toBe('/admin')
        })

        test('renders Login link when not authenticated', () => {
            wrapper = mountWrapper({
                user: mockUser,
                presentations: mockPaginatedData,
            })

            const loginLink = wrapper.findAll('a').find((link) => link.text().includes('Login'))
            expect(loginLink?.attributes('href')).toBe('/admin')
        })
    })

    describe('Accessibility', () => {
        test('has proper label for search input', () => {
            wrapper = mountWrapper({
                user: mockUser,
                presentations: mockPaginatedData,
            })

            const label = wrapper.find('label[for="search"]')
            expect(label.exists()).toBe(true)
            expect(label.text()).toBe('Search Presentations')
        })

        test('search input has correct id matching label', () => {
            wrapper = mountWrapper({
                user: mockUser,
                presentations: mockPaginatedData,
            })

            const searchInput = wrapper.find('#search')
            expect(searchInput.exists()).toBe(true)
        })
    })
})

