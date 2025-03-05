import ProgressType from '../enums/progressType.ts';

interface QueryParams {
    index?: number,
    loop?: number,
    progress?: ProgressType,
}

export default QueryParams;
