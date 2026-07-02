export function useApiErrors() {
    const formatErrors = (error: any): Record<string, string[]> => {
        const formattedErrors: Record<string, string[]> = {};

        if (error?.data?.violations && Array.isArray(error.data.violations)) {
            for (const violation of error.data.violations) {
                const key = violation.propertyPath;
                const message = violation.message;

                if (!formattedErrors[key]) {
                    formattedErrors[key] = [];
                }
                formattedErrors[key].push(message);
            }
        } else {
            formattedErrors['unexpected'] = ["Une erreur inattendue est survenue. Veuillez réessayer."];
        }
        return formattedErrors;
    };

    return { formatErrors };
}