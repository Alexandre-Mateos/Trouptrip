export const apiEndpoints = {
    login: '/login_check',
    users: '/users',
    verifyEmail: '/verify_email',
    forgotPassword: '/forgot_password',
    resetPassword: '/reset_password',
    userMe: '/me',
    trips: '/trips',
    groupItemCollection: (tripId: number) => `/trips/${tripId}/group_items`,
    groupItems: '/group_items',
    assignmentCollection: (tripId: number) => `/trips/${tripId}/assignments`,
    assignments: '/assignments',
    personalItemCollection: (tripId: number) => `/trips/${tripId}/personal_items`
} as const