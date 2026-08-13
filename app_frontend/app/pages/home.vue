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

useHead({
  title: 'Mon espace',
  meta: [
    {
      name: 'description',
      content: 'Consultez vos prochaines escapades, vos invitations en attente et votre calendrier de voyage TroupTrip.'
    }
  ]
});

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
  <div class="flex flex-col gap-6 w-full py-2">
    <a
        href="#main-header"
        class="sr-only focus:not-sr-only focus:absolute focus:top-2 focus:left-2 focus:z-50 focus:p-3 focus:bg-white focus:text-black focus:rounded focus:shadow-lg"
    >
      Aller au menu principal
    </a>

    <header class="text-center space-y-1">
      <h1 class="text-3xl font-bold">Mon espace</h1>
      <p v-if="userStore.user" class="text-xl text-gray-600">
        Bonjour {{ userStore.user.firstname }} !
      </p>
    </header>

    <p v-if="error" role="alert" class="error-message text-red-800 font-medium text-center">
      {{ error }}
    </p>

    <section
        class="p-4 bg-trouptrip-accent-100 border border-trouptrip-accent-200 rounded-md flex flex-col gap-3"
    >
      <h2 class="font-semibold text-center">Mon récapitulatif</h2>

      <div>
        <p v-if="invitationCount > 0" class="font-semibold text-lg">
          Vous avez {{ invitationCount }} invitation(s) en attente !
        </p>
        <div v-else class="space-y-1">
          <p class="font-medium">Vous n'avez aucune invitation pour l'instant !</p>
          <p class="text-sm text-gray-600">
            Et si c'était vous qui preniez les devants en organisant la prochaine escapade ?
          </p>
        </div>
      </div>

      <hr v-if="nextTrip" class="border-trouptrip-accent-200/50" />

      <div v-if="nextTrip">
        <p class="font-medium">Plus que {{ daysLeft }} jours avant votre prochain départ :
          <span class="font-semibold">{{ nextTrip.title }}</span>
        </p>
      </div>
    </section>

    <section class="space-y-3">
      <h2 class="font-semibold text-center">Mon agenda</h2>

      <ClientOnly>
        <ScheduleXCalendar class="calendar" v-if="calendarApp" :calendar-app="calendarApp"/>
      </ClientOnly>
    </section>

  </div>
</template>

<style scoped>
.calendar {
  width: 100%;
  height: 800px;
  max-height: 90vh;
}
</style>