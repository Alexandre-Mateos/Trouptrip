<script lang="ts">
import {defineComponent} from 'vue'
import LogoutButton from "~/components/LogoutButton.vue";
import {useDisplayStore} from "~/stores/display";

export default defineComponent({
  name: "Header",
  components: {LogoutButton},
  computed: {
    headerNav() {
      return useDisplayStore().headerNav;
    },
    userStore(){
      return useUserStore();
    }
  },
})
</script>

<template>
  <UHeader
      mode="slideover"
      :toggle="{
        variant: 'ghost',
        class: 'rounded-md text-trouptrip-title hover:bg-trouptrip-accent-500 hover:text-white'
      }"
      :ui="{
        root: 'bg-trouptrip-accent-100 border-none shadow-md',
        content: 'text-trouptrip-title w-72 max-w-xs',
        header: 'border-b-0'
      }"
  >
    <template #title>
      <NuxtLink to="/" class="flex flex-row items-center gap-2">
        <img
            src="/trouptrip_logo_no_background.png"
            alt=""
            class="h-10 w-auto"
        />
        <span class="trouptrip font-bold text-lg">Trouptrip</span>
      </NuxtLink>
    </template>

    <UNavigationMenu
        :items="headerNav"
        variant="link"
        :ui="{
          root: 'w-auto',
          list: 'flex flex-row gap-x-6',
          item: 'shrink-0',
          link: 'text-md whitespace-nowrap text-trouptrip-title hover:text-trouptrip-accent-500 hover:underline hover:underline-offset-4 transform hover:-translate-y-1 transition-all duration-200'
        }"
    />

    <template #right>
      <div v-if="userStore.user" class="hidden lg:block">
        <LogoutButton class="text-md text-trouptrip-title hover:text-trouptrip-accent-500 hover:underline hover:underline-offset-4 transform hover:-translate-y-1 transition-all duration-200"/>
      </div>
    </template>

    <template #body>
      <UNavigationMenu
          :items="headerNav"
          orientation="vertical"
          variant="link"
          class="-mx-2.5"
          :ui="{
            root: 'w-full border-none',
            link: 'text-trouptrip-title hover:text-trouptrip-accent-500 hover:underline hover:underline-offset-4 transition-all text-md'
          }"
      />
      <div v-if="userStore.user" class="mt-4 pt-2 border-solid border-t border-trouptrip-neutral-500">
        <LogoutButton class="text-trouptrip-title hover:text-trouptrip-accent-500 hover:underline hover:underline-offset-4 transition-all text-md"/>
      </div>
    </template>
  </UHeader>
</template>

<style scoped>
.trouptrip{
  color: var(--color-trouptrip-title);
}
</style>