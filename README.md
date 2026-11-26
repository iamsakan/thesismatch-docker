# ThesIS — Odkrij zanimivo diplomsko/seminarsko nalogo

### Avtor: Anel Šakanović, 63240310
Ime izhaja iz besede angleške besede za diplomsko delo "Thesis". 
Črki IS sta naglašeni na enako foro kot je to pri StudIS-u. IS pri StudIS-u, predvidevam, da nakazuje na "Informacijski sistem".

Aplikacijo sem že napolnil z določenimi podatki, kakor bi vam olajšal testiranje.

## Namestitev (Docker)
1. `copy .env.example .env` in vpiši gesla
2. `docker compose up -d --build`
3. Odpri http://localhost:8080

Aplikacija (`app`) in baza (`db`) tečeta v ločenih vsebnikih v omrežju `backend`.
Aplikacija se z bazo poveže prek internega docker DNS (`DB_HOST=db`).
Baza nima odprtih portov navzven.

## Testni uporabniki

### NOTE
*Vsi uporabniški računi imajo enako geslo zaradi preprostosti testiranja in sicer: Password123!.*

### Admin
- Email: admin@thesis.si
- Geslo: Password123!

### Profesor
- Email: david.jelenc@thesis.si
- Geslo: Password123!

### Profesor 2
- Email: ales.smrdel@thesis.si
- Geslo: Password123!

### Študent
- Email: anel.sakanovic@thesis.si
- Geslo: Password123!

## Opombe
- Profesorski računi, po registraciji, zahtevajo potrditev admina, preden se lahko prijavijo
- Admin panel dostopen na `/thesismatch/admin.php`

## TLDR - Kaj lahko testirate:

### Kot anonimni uporabnik
- Brskaj po temah na `/thesismatch/topics.php`
- Išči teme po ključnih besedah (live search z Ajax) ali področju

### Kot študent (anel.sakanovic@thesis.si)
- Oddaj interes za temo
- Komentiraj temo
- Poglej svoje prijave

### Kot profesor (david.jelenc@thesis.si)
- Objavi novo temo
- Preglej prijave študentov
- Sprejmi ali zavrni prijavo
- Izbriši temo

### Kot admin (admin@thesis.si)
- Potrdi profesorski račun
- Izbriši uporabnika ali temo
- Dodaj novo področje