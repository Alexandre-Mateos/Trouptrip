<script setup lang="ts">
import { onMounted, shallowRef } from 'vue'
import { ScheduleXCalendar } from '@schedule-x/vue'
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

// Le shallowRef est indispensable pour que le v-if du template s'active
const calendarApp = shallowRef<any>(null)

onMounted(async () => {
  await tripsStore.fetchTrips()

  calendarApp.value = createCalendar({
    selectedDate: Temporal.PlainDate.from('2023-12-19'),
    views: [
      createViewDay(),
      createViewWeekAgenda(),
      createViewWeek(),
      createViewMonthGrid(),
      createViewMonthAgenda(),
    ],
    events: [
      {
        id: 1,
        title: 'Event 1',
        start: Temporal.PlainDate.from('2023-12-19'),
        end: Temporal.PlainDate.from('2023-12-19'),
      },
      {
        id: 2,
        title: 'Event 2',
        start: Temporal.ZonedDateTime.from('2023-12-20T12:00:00+09:00[Asia/Tokyo]'),
        end: Temporal.ZonedDateTime.from('2023-12-20T13:00:00+09:00[Asia/Tokyo]'),
      },
    ],
  })
})
</script>

<template>
  <p v-if="userStore.user">Bonjour {{userStore.user.firstname}}</p>
  <ul>
    <li v-for="(trip, index) in tripsStore.tripsList" :key="index">
      {{ trip.title }}
    </li>
  </ul>

  <div class="calendar-wrapper">
    <ClientOnly>
      <ScheduleXCalendar v-if="calendarApp" :calendar-app="calendarApp" />
    </ClientOnly>
  </div>
</template>

<style scoped>

</style>