import ProgressType from '@/enums/progressType.ts';
import QueryParams from '@/interfaces/queryParams.ts';
import slideStore from '@/store/slideStore.ts'
import { processQueryParams } from '@/utils/handleQueryParams.ts'

afterEach(() => {
    slideStore.reset();
});

test('sets default query param values in the slide store', async () => {
    const params: QueryParams = { };

    processQueryParams(params);

    expect(slideStore.index).toBe(0);
    expect(slideStore.loop).toBe(0);
    expect(slideStore.progress).toBe(ProgressType.Bar);
});

test('sets correct query param values in the slide store', async () => {
    const params: QueryParams = {
        index: 5,
        loop: 10,
        progress: ProgressType.Label,
    };

    processQueryParams(params);

    expect(slideStore.index).toBe(5);
    expect(slideStore.loop).toBe(10);
    expect(slideStore.progress).toBe(ProgressType.Label);
});
