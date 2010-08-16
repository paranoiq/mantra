<?php

$translations = array(
    // head
    'Mantra - MongoDB administration tool' => 'Mantra - administrační nástroj pro MongoDB',
    
    // common
    'Language:' => 'Jazyk:',
    'help:' => 'manuál:',
    '(select database)' => '(vybrat databázi)',
    'select' => 'vypsat',
    'Protection timeout expired. Pleas, try again.' => 'Vypršela bezpečnostní lhůta. Prosím odešlete dotaz znovu.',
    
    // help
    'Manual' => 'Manuál',
    'Databases' => 'Databáze',
    'Processes' => 'Procesy',
    'Diagnostics' => 'Diagnostika',
    'Optimization' => 'Optimalizace',
    'Configuration' => 'Konfigurace',
    'Collections' => 'Kolekce',
    'Updating' => 'Editace',
    'Removing' => 'Mazání',
    'Inserting' => 'Vkládání',
    
    // home
    'Select database' => 'Vybrat databázi',
    'PHP Mongo extension' => 'rozšíření PHP Mongo',
    'Database' => 'Databáze',
    'Objects' => 'Objektů',
    'Data size' => 'Objem dat',
    'File size' => 'Úložiště',
    'Drop database' => 'Smazat databázi',
    'Repair database' => 'Opravit databázi',
    'Backup original files' => 'Zálohovat původní soubory',
    'Preserve cloned files' => 'Zachovat naklonované soubory',
    "Database '%' was dropped." => "Databáze '%' byla odstraněna.",
    "Database '%' was repaired." => "Databáze '%' byla opravena.",
    
    // create-db
    'Create database' => 'Vytvořit databázi',
    'create database' => 'vytvořit databázi',
    'Name' => 'jméno',
    'Database name must be filled.' => 'Jméno databáze musí být vyplněno.',
    "Database name includes an invalid character. Allowed characters are numbers, letters and following symbols: !#%&\'()+-,;>=<@[]^_`{}~"
        => "Jméno databáze obsahuje nepovolený znak. Povoleny jsou čísla, písmena a následující symboly: !#%&\'()+-,;>=<@[]^_`{}~",
    "Database '%' was created." => "Databáze '%' byla vytvořena.",
    
    // processes
    'Process list' => 'Seznam procesů',
    'Process id' => 'Id procesu',
    'Time' => 'Čas',
    'Active' => 'Aktivní',
    'Lock' => 'Zámek',
    'Waiting' => 'Čeká',
    'Operation' => 'Operace',
    'Namespace' => 'Jmenný prostor',
    'Query' => 'Dotaz',
    'Client' => 'Klient',
    'Kill process' => 'Ukončit proces',
    "Process '%' was killed." => "Proces '%' byl ukončen.",
    
    // status
    'Server status' => 'Stav serveru',
    'server status' => 'stav serveru',
    'Server dignostic screen' => 'Diagnostická stránka serveru',
    'running as:' => 'spuštěn jako:',
    'configuration:' => 'konfigurace:',
    'Shutdown server' => 'Vypnout server',
    'Server was shut down.' => 'Server byl vypnut.',
    
    // usage
    'Collection usage' => 'Využití kolekcí',
    'collection usage' => 'využití kolekcí',
    'time [s]' => 'čas [s]',
    'queries' => 'dotazy',
    
    // database
    'Database:' => 'Databáze:',
    'Collection' => 'Kolekce',
    'Capped' => 'Fixní',
    'yes' => 'ano',
    'Objects' => 'Objekty',
    'Data size' => 'Objem dat',
    'Max. objects' => 'Max. objektů',
    'Initial size' => 'Počáteční velikost',
    'Empty collection' => 'Vyprázdnit kolekci',
    'Drop collection' => 'Smazat kolekci',
    'Drop indexes' => 'Smazat indexy',
    'Reindex' => 'Přeindexovat',
    'Validate indexes' => 'Zkontrolovat indexy',
    'Validate data' => 'Zkontrolovat data',
    'Database statistics' => 'Statistiky databáze',
    "Collection '%' was validated." => "Kolekce '%' byla zkontrolována.",
    "Collection '%' was emptied." => "Kolekce '%' byla vyprázněna.",
    "Collection '%' was dropped." => "Kolekce '%' byla smazána.",
    "Indexes on collection '%' was dropped." => "Indexy na kolekci '%' byly smazány.",
    "Collection '%' was reindexed." => "Kolekce '%' byla přeindexována.",
    
    // command
    'Run command' => 'Spustit příkaz',
    'run command' => 'spustit příkaz',
    'List commands' => 'Seznam příkazů',
    'Command (JSON)' => 'Příkaz (JSON)',
    'Command must be filled.' => 'Příkaz musí být vyplněn.',
    'commands' => 'příkazy',
    'Name' => 'Jméno',
    'on admin database only' => 'pouze na databázi admin',
    'allowed on slave' => 'povoleno na slave serveru',
    'read lock' => 'zámek pro čtení',
    'write lock' => 'zámek pro zápis',
    'Help' => 'Nápověda',
    "A = on 'admin' database only, S = allowed on slave, R = read lock, W = write lock" 
        => "A = pouze na databázi 'admin', S = povoleno na slave serveru, R = zámek pro čtení, W = zámek pro zápis",
    
    // create-coll
    'Create collection' => 'Vytvořit kolekci',
    'create collection' => 'vytvořit kolekci',
    'Name' => 'Jméno',
    'Collection name must be filled.' => 'Musí být vyplněno jméno kolekce.',
    'Collection name is too long.' => 'Jméno kolekce je příliš dlouhé.',
    "Collection name includes an invalid character. Allowed are all ASCII characters except controls, space, \", $ and DEL." 
        => "Jméno kolekce obsahuje nepovolený znak. Povoleny jsou všechny znaky ASCII kromě kontrolních, mezery, \", $ a DEL.",
    'Size must be a positive number.' => 'Velikost musí být kladné číslo.',
    'Initial size [MB]' => 'Počáteční velikost [MB]',
    "No index on field '_id'" => "Bez indexu na poli '_id'",
    'Capped (fixed size)' => 'Fixní velikost (capped)',
    'Maximum elements' => 'Maximum záznamů',
    'Maximum elements must be a positive number.' => 'Maximum záznamů musí být kladné číslo.',
    'Collection name must be filled.' => 'Jméno kolekce musí být vyplněno.',
    "Collection '%' was created." => "Kolekce '%' byla vytvořena.",
    'Capped collections' => 'Fixní kolekce',
    
    // collection
    'Collection:' => 'Kolekce:',
    'Indexes' => 'Indexy',
    'Keys' => 'Klíče',
    'Size' => 'Velikost',
    'Drop index' => 'Smazat index',
    'Collection statistics' => 'Statistiky kolekce',
    'Query' => 'Dotaz',
    'Time [s]' => 'Čas [s]',
    'Count' => 'Počet',
    "Index '%' on collection '%' was dropped." => "Index '%' na kolekci '%' byl smazán.",
    
    // select
    'Select' => 'Vybrat',
    'Select items:' => 'Vypsat záznamy:',
    'Query (JSON)' => 'Dotaz (JSON)',
    'Order' => 'Seřadit',
    'Key name include an invalid character. All characters except controls, space, dolar and dor are allowed.'
        => 'Jméno klíče obsahuje nepovolený znak. Povoleny jsou všechny znaky kromě kontrolních, mezery, dolaru a tečky.',
    'descending' => 'sestupně',
    'Limit' => 'Limit',
    'Limit must be a positive number.' => 'Limit musí být kladné číslo.',
    'Action' => 'Akce',
    'Select' => 'Vypsat',
    'Page:' => 'Strana:',
    'Edit' => 'Upravit',
    'Id' => 'Id',
    'Data' => 'Data',
    'compact view' => 'kompaktní zobrazení',
    'edit' => 'upravit',
    '(% items)' => '(% záznamů)',
    'all matching items' => 'všechny vyhovující záznamy',
    'Update' => 'Upravit',
    'Clone' => 'Klonovat',
    'Delete' => 'Smazat',
    'save' => 'uložit',
    'open' => 'otevřít',
    'zip' => 'zip',
    'Export' => 'Exportovat',
    "% items deleted from collection '%'." => "Smazáno % záznamů z kolekce '%'.",
    'Querying' => 'Dotazy',
    'Extended JSON' => 'Rozšířený JSON',
    
    // #operators
    'Query operators:' => 'Operátory dotazu:',
    'Field operator:' => 'Operátor výběru:',
    'Update operators:' => 'Operátory změn:',
    
    // update
    'Update' => 'Upravit',
    'update' => 'upravit',
    'Update items:' => 'Upravit záznamy:',
    'Query (JSON)' => 'Dotaz (JSON)',
    'Query must be specified.' => 'Musí být uveden dotaz.',
    'Changes (JSON)' => 'Změny (JSON)',
    'When updating, you must specify the changes.' => 'Při updatu musí být vyplněno pole změn.',
    'Delete matching items' => 'Smazat vyhovující záznamy',
    'Insert if no match found' => 'Vložit, pokud není nic nalezeno',
    'Update/delete just one item' => 'Upravit/smazat pouze jeden záznam',
    'Update' => 'Upravit',
    
    // insert
    'Insert' => 'Vložit',
    'insert' => 'vložit',
    'Insert item:' => 'Vložit záznam:',
    'Object (JSON)' => 'Objekt (JSON)',
    'Object must be filled.' => 'Objekt musí být vyplněn',
    'Insert' => 'Vložit',
    "A new item was inserted into collection '%'." => "Nový záznam byl vložen do kolekce '%'.",
    
    // import
    'Import' => 'Importovat',
    'import' => 'importovat',
    'Import file:' => 'Načíst soubor:',
    'Import file' => 'Načíst soubor',
    'File cannot be larger than % MB.' => 'Soubor nesmí být větší než % MB.',
    'File must be either JSON or CSV.' => 'Soubor musí být ve formátu JSON nebo CSV.',
    'Select file on server …' => 'Vybrat soubor na serveru …',
    '… or upload file' => '… nebo uploadovat soubor',
    'Type' => 'Typ',
    'File was not found.' => 'Soubor nebyl nalezen.',
    'File cannot be read from.' => 'Ze souboru nelze číst.',
    'Error when receiving file.' => 'Došlo kchybě při příjmu souboru.',
    'File was succesfully loaded and % items created.' => 'Soubor byl úspěšně načten a bylo založeno % položek.',
    
    // rename-coll
    'Rename collection' => 'Přejmenovat kolekci',
    
    // create-index
    'Create index' => 'Přidat index',
    
    // 
    '' => '',
    '' => '',
    '' => '',
    '' => '',
    '' => '',
    '' => '',
    '' => '',
    '' => '',
    '' => '',
    '' => '',
    '' => '',
    '' => '',
    
    
);
