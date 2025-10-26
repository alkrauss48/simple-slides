import { mount, VueWrapper } from '@vue/test-utils'
import { nextTick } from 'vue'

import SettingsMenu from '@/Components/SettingsMenu.vue'
import { VisualMode } from '@/enums/visualMode.ts'
import { getVisualMode, setVisualMode } from '@/utils/handleVisualMode.ts'
import slideStore from '@/store/slideStore.ts'

vi.mock('@inertiajs/vue3', async () => {
    return {
        Link: {
            name: 'Link',
            template: '<a @click="handleClick" :href="href"><slot /></a>',
            props: ['href', 'onBefore'],
            methods: {
                handleClick(this: any, e: Event) {
                    this.$emit('click', e);
                    if (this.onBefore) {
                        this.onBefore();
                    }
                }
            }
        },
        router: {
            visit: vi.fn()
        }
    }
})

beforeEach(() => {
    slideStore.reset();
    localStorage.clear();
});

afterEach(() => {
    slideStore.reset();
    localStorage.clear();
    vi.clearAllMocks();
});

const mountWrapper = (auth?: any) : VueWrapper<any> => {
    return mount(SettingsMenu, {
        props: {
            auth
        }
    });
};

describe('SettingsMenu - Menu Toggle', () => {
    test('menu is closed by default', () => {
        const wrapper = mountWrapper();

        expect(wrapper.vm.isOpen).toBe(false);
    });

    test('clicking cog icon opens the menu', async () => {
        const wrapper = mountWrapper();

        await wrapper.find('button[aria-label="Settings Menu"]').trigger('click');

        expect(wrapper.vm.isOpen).toBe(true);
    });

    test('clicking cog icon twice toggles menu open and closed', async () => {
        const wrapper = mountWrapper();
        const button = wrapper.find('button[aria-label="Settings Menu"]');

        await button.trigger('click');
        expect(wrapper.vm.isOpen).toBe(true);

        await button.trigger('click');
        expect(wrapper.vm.isOpen).toBe(false);
    });
});

describe('SettingsMenu - Navigation Links', () => {
    test('shows Home link', async () => {
        const wrapper = mountWrapper();
        await wrapper.find('button[aria-label="Settings Menu"]').trigger('click');

        const homeLink = wrapper.find('a[href="/"]');
        expect(homeLink.exists()).toBe(true);
        expect(homeLink.text()).toBe('Home');
    });

    test('shows Login and Sign Up links when not authenticated', async () => {
        const wrapper = mountWrapper();
        await wrapper.find('button[aria-label="Settings Menu"]').trigger('click');

        const loginLink = wrapper.find('a[href="/admin"]');
        const signUpLink = wrapper.find('a[href="/admin/register"]');

        expect(loginLink.exists()).toBe(true);
        expect(loginLink.text()).toBe('Login');
        expect(signUpLink.exists()).toBe(true);
        expect(signUpLink.text()).toBe('Sign Up');
    });

    test('shows Dashboard link when authenticated', async () => {
        const wrapper = mountWrapper({ user: { id: 1, name: 'Test User' } });
        await wrapper.find('button[aria-label="Settings Menu"]').trigger('click');

        const dashboardLink = wrapper.find('a[href="/admin"]');
        const signUpLink = wrapper.find('a[href="/admin/register"]');

        expect(dashboardLink.exists()).toBe(true);
        expect(dashboardLink.text()).toBe('Dashboard');
        expect(signUpLink.exists()).toBe(false);
    });
});

describe('SettingsMenu - Dark Mode', () => {
    test('initializes with current visual mode', () => {
        setVisualMode(VisualMode.Dark);
        const wrapper = mountWrapper();

        expect(wrapper.vm.darkMode).toBe(true);

        setVisualMode(VisualMode.Light);
        const wrapper2 = mountWrapper();

        expect(wrapper2.vm.darkMode).toBe(false);
    });

    test('toggling dark mode updates visual mode', async () => {
        setVisualMode(VisualMode.Light);
        const wrapper = mountWrapper();
        await wrapper.find('button[aria-label="Settings Menu"]').trigger('click');

        wrapper.vm.darkMode = true;
        await nextTick();

        expect(getVisualMode()).toBe(VisualMode.Dark);

        wrapper.vm.darkMode = false;
        await nextTick();

        expect(getVisualMode()).toBe(VisualMode.Light);
    });

    test('dark mode button does not close menu when clicked', async () => {
        const wrapper = mountWrapper();
        await wrapper.find('button[aria-label="Settings Menu"]').trigger('click');
        await nextTick();

        expect(wrapper.vm.isOpen).toBe(true);

        // Find the dark mode button
        const darkModeButton = wrapper.find('button[aria-label="Enable Dark Mode"]');
        await darkModeButton.trigger('click');
        await nextTick();

        // Menu should still be open after clicking dark mode
        expect(wrapper.vm.isOpen).toBe(true);
    });
});

describe('SettingsMenu - Loop Interval', () => {
    test('loop interval field initializes with slideStore.loop value', () => {
        slideStore.loop = 5;
        const wrapper = mountWrapper();

        expect(wrapper.vm.loopInterval).toBe(5);
    });

    test('changing loop interval updates slideStore', async () => {
        const wrapper = mountWrapper();
        await wrapper.find('button[aria-label="Settings Menu"]').trigger('click');

        wrapper.vm.loopInterval = 5;
        await nextTick();

        expect(slideStore.loop).toBe(5);
    });

    test('loop interval field exists in menu', async () => {
        const wrapper = mountWrapper();
        await wrapper.find('button[aria-label="Settings Menu"]').trigger('click');

        const loopInput = wrapper.find('#loop-interval');
        expect(loopInput.exists()).toBe(true);
        expect(loopInput.attributes('type')).toBe('number');
    });
});

describe('SettingsMenu - Other Settings Link', () => {
    test('Other Settings link exists', async () => {
        const wrapper = mountWrapper();
        await wrapper.find('button[aria-label="Settings Menu"]').trigger('click');

        const settingsLink = wrapper.find('a[href="/settings"]');
        expect(settingsLink.exists()).toBe(true);
        expect(settingsLink.text()).toBe('Other Settings');
    });

    test('clicking Other Settings saves current URL to localStorage', async () => {
        const wrapper = mountWrapper();

        // Mock location
        delete (window as any).location;
        window.location = {
            pathname: '/presentation/123',
            search: '?index=5',
            href: 'http://example.com/presentation/123?index=5'
        } as any;

        await wrapper.find('button[aria-label="Settings Menu"]').trigger('click');

        // Trigger the onBefore handler directly
        wrapper.vm.onBeforeSettingsVisit();

        expect(localStorage.getItem('appCurrentUrl')).toBe('/presentation/123?index=5');
    });
});

describe('SettingsMenu - Keyboard Support', () => {
    test('pressing Escape closes the menu', async () => {
        const wrapper = mountWrapper();

        // Open menu
        await wrapper.find('button[aria-label="Settings Menu"]').trigger('click');
        await nextTick();
        expect(wrapper.vm.isOpen).toBe(true);

        // Wait for event listener to be attached (setTimeout in component)
        await new Promise(resolve => setTimeout(resolve, 10));

        // Press Escape
        const event = new KeyboardEvent('keydown', { key: 'Escape' });
        document.dispatchEvent(event);
        await nextTick();

        expect(wrapper.vm.isOpen).toBe(false);
    });

    test('Escape key listener is only active when menu is open', async () => {
        const wrapper = mountWrapper();

        // Try pressing Escape when menu is closed
        const event = new KeyboardEvent('keydown', { key: 'Escape' });
        document.dispatchEvent(event);
        await nextTick();

        // Should have no effect (menu already closed)
        expect(wrapper.vm.isOpen).toBe(false);
    });
});

describe('SettingsMenu - Click Outside', () => {
    test('clicking outside the menu closes it', async () => {
        const wrapper = mountWrapper();

        // Open menu
        await wrapper.find('button[aria-label="Settings Menu"]').trigger('click');
        await nextTick();
        expect(wrapper.vm.isOpen).toBe(true);

        // Wait for event listener to be attached
        await new Promise(resolve => setTimeout(resolve, 10));

        // Click outside
        const clickEvent = new MouseEvent('click', {
            bubbles: true,
            cancelable: true
        });
        document.body.dispatchEvent(clickEvent);
        await nextTick();

        expect(wrapper.vm.isOpen).toBe(false);
    });

    test('clicking inside the menu does not close it', async () => {
        const wrapper = mountWrapper();

        // Open menu
        await wrapper.find('button[aria-label="Settings Menu"]').trigger('click');
        await nextTick();
        expect(wrapper.vm.isOpen).toBe(true);

        // Wait for event listener to be attached
        await new Promise(resolve => setTimeout(resolve, 10));

        // Click inside menu
        const menu = wrapper.find('.absolute');
        await menu.trigger('click');
        await nextTick();

        expect(wrapper.vm.isOpen).toBe(true);
    });
});

describe('SettingsMenu - Cleanup', () => {
    test('event listeners are removed when menu closes', async () => {
        const wrapper = mountWrapper();
        const removeEventListenerSpy = vi.spyOn(document, 'removeEventListener');

        // Open menu
        await wrapper.find('button[aria-label="Settings Menu"]').trigger('click');
        await nextTick();

        // Close menu
        wrapper.vm.closeMenu();
        await nextTick();

        expect(removeEventListenerSpy).toHaveBeenCalledWith('click', expect.any(Function));
        expect(removeEventListenerSpy).toHaveBeenCalledWith('keydown', expect.any(Function));
    });
});

