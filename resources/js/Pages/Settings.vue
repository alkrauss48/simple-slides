<script setup lang="ts">
import { ref, watch } from 'vue'
import { Link, router } from '@inertiajs/vue3'

import AppHead from '@/Components/AppHead.vue';
import GithubIcon from '@/Components/icons/GithubIcon.vue';
import MoonFillIcon from '@/Components/icons/MoonFillIcon.vue';
import MoonStrokeIcon from '@/Components/icons/MoonStrokeIcon.vue';
import YoutubeIcon from '@/Components/icons/YoutubeIcon.vue';
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
                  hover:bg-gray-100 dark:hover:bg-gray-800 text-center
                  focus:bg-gray-100 dark:focus:bg-gray-800"
                  >S</Link>
              <a
                  href="/admin"
                  class="hover:underline focus:underline ml-4 md:ml-8"
                  >{{ props.auth?.user ? 'Dashboard' : 'Login' }}</a>
              <Link
                  v-if="!props.auth?.user"
                  href="/register"
                  class="hover:underline focus:underline ml-8"
                  >Sign Up</Link>
          </div>
          <div class="flex items-center">
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
            Enter the URL to your markdown file below.
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
    <nav class="flex items-center fixed bottom-6 right-6">
        <a
            href="https://github.com/alkrauss48/simple-slides"
            target="_blank"
            class="hover:underline focus:underline mr-8"
        >Privacy Policy</a>
        <a
            href="https://www.youtube.com/playlist?list=PLWXp2X5PBDOkzYGV3xd0zviD6xR8OoiFR"
            target="_blank"
            title="Github repo"
            class="
            text-5xl mr-8
            hover:text-gray-400 focus:text-gray-400
            "
        ><YoutubeIcon /></a>
        <a
            href="https://github.com/alkrauss48/simple-slides"
            target="_blank"
            title="Github repo"
            class="
            text-5xl mr-8
            hover:text-gray-400 focus:text-gray-400
            "
        ><GithubIcon /></a>
        <button
            v-if="!darkMode"
            id="darkMode"
            title="Toggle Dark Mode"
            @click="darkMode = true;"
            class="
            text-5xl
            hover:text-gray-400 focus:text-gray-400
            "
        ><MoonStrokeIcon /></button>
        <button
            v-if="darkMode"
            id="lightMode"
            title="Toggle Dark Mode"
            @click="darkMode = false;"
            class="
            text-5xl
            hover:text-gray-400 focus:text-gray-400
            "
        ><MoonFillIcon /></button>
    </nav>
  </main>
</template>
