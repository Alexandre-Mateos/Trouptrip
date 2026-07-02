<script setup lang="ts">
import {onMounted, shallowRef} from 'vue'
import {ScheduleXCalendar} from '@schedule-x/vue'
import {
  createCalendar,
  createViewDay,
  createViewWeekAgenda,
  createViewMonthAgenda,
  createViewMonthGrid,
  createViewWeek,
} from '@schedule-x/calendar'

const tripsStore = useTripsStore()
const userStore = useUserStore()

import '@schedule-x/theme-default/dist/index.css'
import 'temporal-polyfill/global'

const calendarApp = shallowRef<any>(null)

onMounted(async () => {
  await tripsStore.fetchTrips();

  const monthGrid = createViewMonthGrid()

  calendarApp.value = createCalendar({
    selectedDate: Temporal.Now.plainDateISO(),
    views: [
      createViewMonthGrid(),
      createViewMonthAgenda(),
      createViewWeek(),
      createViewWeekAgenda()
    ],
    defaultView: monthGrid.name,
    events: tripsStore.calendarDatas
  })
})
</script>

<template>
  <p v-if="userStore.user">Bonjour {{ userStore.user.firstname }}</p>

  <div>
    <ClientOnly>
      <ScheduleXCalendar class="calendar" v-if="calendarApp" :calendar-app="calendarApp"/>
    </ClientOnly>
  </div>
</template>

<style scoped>
.calendar {
  width: 100%;
  height: 800px;
  max-height: 90vh;
}
</style>