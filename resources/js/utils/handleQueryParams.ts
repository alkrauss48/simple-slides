import ProgressType from '@/enums/progressType.ts';
import QueryParams from '@/interfaces/queryParams.ts';
import slideStore from '@/store/slideStore.ts'

export const processQueryParams = (params: QueryParams): void =>  {
    slideStore.index = params.index ?? 0;
    slideStore.loop = params.loop ?? 0;
    slideStore.progress = params.progress ?? ProgressType.Bar;
};
