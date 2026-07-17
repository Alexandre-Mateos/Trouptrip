export const apiEndpoints = {
    login: '/login_check',
    users: '/users',
    verifyEmail: '/verify_email',
    forgotPassword: '/forgot_password',
    resetPassword: '/reset_password',
    userMe: '/me',
    trips: '/trips',
    groupItemsCollection: (tripId: number) => `/trips/${tripId}/group_items`
} as const