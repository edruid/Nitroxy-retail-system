This code was written for another project and depends rather heavily on that codebase, but I leave the code here since it should be rather easy to integrate it in another project

I den här mappen placeras migrations
Migrations hanteras av scripten update_database.php och create_migration.php.

För att skapa en ny migration kör man 
./create_migration.php namn_på_migrationen

Då skapas en tom migration med ett namn i stil med 20110821231945_namn_på_migrationen.sql.
Namnet på filen (inklusive datumet) är versionen och måste vara unikt. Filerna ska vara i utf-8 om man tänkte använda annat än ascii.

I migrationsfilen skriver man sin sql patch och sedan kör man ./update_database.php varpå alla migrationer som inte har körts körs.
Går något snett under en migration rullas den tillbaka och inga fler migrationer körs.

Man kan även ange filformat till ./create_migration.php efter namnet:
./create_migration.php namn_på_migrationen sql
eller
./create_migration.php namn_på_migrationen php

Använder man .php får man en php-fil som parsas som vanligt med php (kom ihåg <?).
För att köra sql kan man använda hjälpmetoden run_sql(query) som returnerar affected_rows.
Hela vanliga miljön är inladdad. Skriver man en php-migration bör man tänka på att vara verbos. run_query skriver inte ut något själv.

Alla körda migrationer lagras i tabellen schema_migrations

migration_sql(query): Kör frågan och skriver ut den

Som policy SKA man inte ändra i en migration man redan har skapat. Gjorde du fel så skapa en ny migration.

Vill man åt globala variabler i sin migration måste man använda global.
