export default defineNuxtRouteMiddleware(async (to, from) => {
    const userStore = useUserStore();
    const publicRoutes = ['/login', '/register', '/verify-email', '/forgot-password', '/reset-password', '/', '/privacy-policy', '/legal-notice'];

    if (publicRoutes.includes(to.path)) {
        return;
    }
    await userStore.fetchUser();
    if (!userStore.user) {
        return navigateTo('/login');
    }
});