import { reactive } from 'vue'

import ProgressType from '@/enums/progressType.ts';
import dataStore from './dataStore.ts'

const slideStore = reactive({
  index: 0,
  loop: 0,
  progress: ProgressType.Bar,

  getNewIndex(count: number) : number {
    if (slideStore.index + count < 0) {
      return 0;
    }

    if (slideStore.index + count >= dataStore.data.length) {
      return dataStore.data.length - 1;
    }

    return slideStore.index + count;
  },

  increment(count: number) : void {
    const newIndex = this.getNewIndex(count);

    if (slideStore.index === newIndex) {
      return;
    }

    slideStore.index = newIndex;
  },

  isEnd() : boolean {
      return this.index === dataStore.data.length - 1;
  },

  canLoop() : boolean {
      if (this.loop < 2) {
          return false;
      }

      return true;
  },

  reset() {
    this.index = 0;
    this.progress = ProgressType.Bar;
  },
});

export default slideStore;
