export function getFormatedDate(rawDate: string){
    const userTimeZone = Intl.DateTimeFormat().resolvedOptions().timeZone;

    const date = Temporal.Instant.from(rawDate)
        .toZonedDateTimeISO(userTimeZone)
        .toPlainDate();
    const dateFormatter = new Intl.DateTimeFormat('fr-FR', { dateStyle: 'long' });
    return dateFormatter.format(date);
}