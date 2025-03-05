import { shallowMount, VueWrapper } from '@vue/test-utils'

import DraftBanner from '@/Components/DraftBanner.vue'
import ProgressType from '@/enums/progressType.ts';
import Presentation from '@/interfaces/presentation.ts';
import QueryParams from '@/interfaces/queryParams.ts';
import PresentationPage from '@/Pages/Presentation.vue'
import dataStore from '@/store/dataStore.ts'
import slideStore from '@/store/slideStore.ts'

afterEach(() => {
    slideStore.reset();
});

const mountWrapper = (isPublished: boolean = true) : VueWrapper<any> => {
    const params: QueryParams = {
        index: 5,
        loop: 10,
        progress: ProgressType.Label,
    };

    const presentation: Presentation = {
        id: '1',
        content: '1\n\n2\n\n3',
        slide_delimiter: '(\n\n|\r\n)',
        is_published: isPublished,
    };

    return shallowMount(PresentationPage, {
        props: {
            ...params,
            presentation,
        }
    });
};

test('mounts the query params in the slide store', async () => {
    const wrapper = mountWrapper();

    expect(slideStore.index).toBe(5);
    expect(slideStore.loop).toBe(10);
    expect(slideStore.progress).toBe(ProgressType.Label);
});

test('parses the presentation content in the data store', async () => {
    const wrapper = mountWrapper();

    expect(dataStore.data).toStrictEqual([
        '<p>1</p>',
        '<p>2</p>',
        '<p>3</p>',
    ]);
});

test('does not show the draft banner for published presentations', async () => {
    const wrapper = mountWrapper();

    expect(wrapper.vm.isDraft).toBeFalsy();
});

test('shows the draft banner for draft presentations', async () => {
    const wrapper = mountWrapper(false);

    expect(wrapper.vm.isDraft).toBeTruthy();
});
