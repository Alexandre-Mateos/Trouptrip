<script lang="ts">
import {defineComponent} from 'vue'

export default defineComponent({
  name: "BaseNav",
  inheritAttrs: false,
  props: {
    links: {
      type: Array as PropType<{ to: string, label: string }[]>,
      required: true,
    }
  }
})
</script>

<template>
  <nav>
    <ul v-bind="$attrs">
      <li v-for="link in links" :key="link.to" class="rounded-md">
        <NuxtLink :to="link.to">
          {{ link.label }}
        </NuxtLink>
      </li>
      <slot></slot>
    </ul>
  </nav>
</template>

<style scoped>
li:hover{
  background-color: var(--color-accent);
  color: var(--color-bg);
}
</style>