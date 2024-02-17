<script setup lang="ts">
import { ref, watch } from 'vue'
import { Link, router } from '@inertiajs/vue3'

import AppHead from '@/Components/AppHead.vue';
import GithubIcon from '@/Components/icons/GithubIcon.vue';
import { VisualMode, isDarkMode } from '@/enums/visualMode.ts';
import { getVisualMode, setVisualMode } from '@/utils/handleVisualMode.ts';

const visualMode = getVisualMode();

const slidesUrl = ref<string>(localStorage.getItem('slidesUrl') ?? '');
const darkMode = ref<boolean>(isDarkMode(visualMode));

const props = defineProps<{
    auth?: any,
}>();

const back = () => {
    const url = localStorage.getItem('appCurrentUrl') ?? '/';

    router.visit(url);
};

const go = () => {
    if (!slidesUrl.value) {
        return;
    }

    router.visit(`/${btoa(slidesUrl.value)}`);
};

watch(darkMode, async (newValue: boolean) => {
  const newMode = newValue ? VisualMode.Dark : VisualMode.Light;

  setVisualMode(newMode);
});
</script>

<template>
  <main>
      <AppHead />
      <div class="fixed py-4 px-8 w-full bg-gray-200 dark:bg-gray-800 flex
          justify-between border-b-1 border-gray-300">
          <div class="flex items-center">
              <Link
                  href="/"
                  class="w-10 h-10 text-4xl font-bold bg-white dark:bg-gray-900
                  hover:bg-gray-100 dark:hover:bg-gray-800 text-center"
                  >S</Link>
              <a
                  href="/admin"
                  class="hover:underline ml-4 md:ml-8"
                  >{{ props.auth?.user ? 'Dashboard' : 'Login' }}</a>
              <Link
                  v-if="!props.auth?.user"
                  href="/register"
                  class="hover:underline ml-8 hidden md:inline"
                  >Sign Up</Link>
          </div>
          <div class="flex items-center">
              <div class="mr-4 md:mr-8 flex items-center">
                  <input type="checkbox" id="darkMode" v-model="darkMode">
                  <label class="ml-4 font-bold" for="darkMode">Dark Mode?</label>
              </div>
              <button
                  id="close"
                  @click="back()"
                  class="text-3xl"
                  >✕</button>
          </div>
      </div>
    <form
      @submit.prevent="go()"
      class="h-[100dvh] flex flex-col justify-center items-center"
      action=""
      method="post"
    >
        <h2 class="w-[30rem] max-w-full mb-4 font-bold text-3xl text-center">Try out Simple Slides</h2>
        <label for="slidesUrl" class="w-64 m-4">
            Enter the URL to your markdown file below, and then click present.
        </label>
        <textarea
          id="slidesUrl"
          class="w-64 p-2 my-4 border-solid border-2 border-black text-black"
          name="slidesUrl"
          v-model="slidesUrl"
          rows="4"
          required
          placeholder="https://example.com/your-presentation.md"
          ></textarea>
      <button
        class="button"
        type="submit"
      >Present</button>
        <p class="w-64 mt-8">
            <small>Psst:
              <Link
                  href="/register"
                  class="underline hover:no-underline"
                  >Creating a free account</Link>
                 lets you control the URL of your
                presentation, so it'll look better when sharing.</small></p>
    </form>
    <a
      href="https://github.com/alkrauss48/simple-slides"
      target="_blank"
      title="Github repo"
      class="
        fixed bottom-6 right-6
        text-5xl text-gray-300/50
        hover:text-gray-300 focus:text-gray-300
      "
      ><GithubIcon /></a>
  </main>
</template>
