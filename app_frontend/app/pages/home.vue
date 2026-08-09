<script setup lang="ts">
import {onMounted, shallowRef} from 'vue'
import {ScheduleXCalendar} from '@schedule-x/vue'
import {
  createCalendar,
  createViewWeekAgenda,
  createViewMonthAgenda,
  createViewMonthGrid,
  createViewWeek,
} from '@schedule-x/calendar'

const tripsStore = useTripsStore();
const userStore = useUserStore();
const participationStore = useParticipationStore()

import '@schedule-x/theme-default/dist/index.css'
import 'temporal-polyfill/global'

const calendarApp = shallowRef<any>(null);
const error = ref('');

const invitationCount = computed(() => {
  return participationStore.invitations.size;
});

const nextTrip = computed(() => {
  return tripsStore.getUpcomingTrip;
})

const daysLeft = computed(() => {
  if (!nextTrip.value?.startDate) return null;

  const today = Temporal.Now.plainDateISO();
  const tripDate = Temporal.PlainDate.from(nextTrip.value.startDate);

  return today.until(tripDate, { largestUnit: 'day' }).days;
})

onMounted(async () => {

  try {
    await participationStore.fetchInvitations()
  } catch (e) {
    error.value = 'Impossible d\'afficher la liste des invitations pour le moment.'
  }

  await tripsStore.fetchTrips();

  const monthGrid = createViewMonthGrid();

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
  <div class="flex flex-col gap-3">

    <a
        href="#main-header"
        class="sr-only focus:not-sr-only focus:absolute focus:top-2 focus:left-2 focus:z-50 focus:p-3 focus:bg-white focus:text-black focus:rounded focus:shadow-lg"
    >
      Aller au menu principal
    </a>

    <p v-if="userStore.user" class="text-2xl text-center">Bonjour {{ userStore.user.firstname }}</p>

    <p v-if="error" class="error-message">{{ error }}</p>

    <div class="p-4 bg-surface-primary-trouptrip border border-trouptrip-accent-200 rounded-md flex flex-col gap-1">
      <p v-if="invitationCount > 0"> Vous avez {{invitationCount}} invitation(s) en attente !</p>
      <div v-else>
        <p>Vous n'avez aucune invitation pour l'instant !</p>
        <p>Et si c'était vous qui preniez les devants en organisant la prochaine escapade ?</p>
      </div>

      <div v-if="nextTrip">
        <p>Plus que {{daysLeft}} jours avant ton prochain départ: {{nextTrip.title}}</p>
      </div>
    </div>


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