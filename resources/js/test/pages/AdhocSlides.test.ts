import { shallowMount, VueWrapper } from '@vue/test-utils'


import ProgressType from '@/enums/progressType.ts';
import QueryParams from '@/interfaces/queryParams.ts';
import { MOCK_INSTRUCTIONS_URL, MOCK_WINDOWS_INSTRUCTIONS_URL } from '@/mocks/handlers.ts';
import { server } from '@/mocks/browser.ts';
import AdhocSlidesPage from '@/Pages/AdhocSlides.vue'
import dataStore from '@/store/dataStore.ts'
import slideStore from '@/store/slideStore.ts'

afterEach(() => {
    dataStore.reset()
    slideStore.reset();
    localStorage.clear()
});

const mountWrapper = (encodedSlides: string | undefined) : VueWrapper<any> => {
    const params: QueryParams = {
        index: 5,
        loop: 10,
        progress: ProgressType.Label,
    };

    return shallowMount(AdhocSlidesPage, {
        props: {
            ...params,
            encodedSlides,
        }
    });
};

describe('fetching data', () => {
    // Start server before all tests
    beforeAll(() => server.listen({ onUnhandledRequest: 'error' }))

    //  Close server after all tests
    afterAll(() => server.close())

    // Reset handlers after each test `important for test isolation`
    afterEach(() => server.resetHandlers())

    test('mounts the query params in the slide store', async () => {
        const wrapper = mountWrapper(btoa(MOCK_INSTRUCTIONS_URL));

        expect(slideStore.index).toBe(5);
        expect(slideStore.loop).toBe(10);
        expect(slideStore.progress).toBe(ProgressType.Label);
    });

    test('sets data in the data store', async () => {
        const wrapper = mountWrapper(btoa(MOCK_INSTRUCTIONS_URL));

        expect(dataStore.data.length).toBe(3);
        expect(dataStore.data[0]).toBe('<p>Welcome to</p>');
        expect(dataStore.data[1]).toBe('<h1>Simple Slides</h1>');
        expect(dataStore.data[2]).toBe('<p><a href="https://example.com">https://example.com</a></p>');
    });
});
