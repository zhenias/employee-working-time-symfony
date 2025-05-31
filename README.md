# Przepracowane godziny pracy przez pracowników.

## Endpoint - POST: `/api/employees`
1. Dodanie użytkownika.
2. Sprawdza czy isnieje ten użytkownik - jeśli tak - zwraca stosowny komunikat.

## Endopoint - POST: `/api/workTime`.
1. Dodaje czas pracy pracownika.
2. Jeśli posiada w ciągu 12 godzin więcej niż 8/12 godzin - zwraca błąd.

## Endpoint - GET: `/api/workTime/dailySummary`.
1. Podsumowanie dzienne dla pracownika.

## Endpoint - GET: `/api/workTime/monthlySummary`.
1. Podsumowanie miesięczne dla pracownika.
