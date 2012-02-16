# Nitroxy retail system
A Point of Sale (POS) system run in a browser. It handles a barcodescanner to sell stuffs.

## State of the project
The project is in active use but should be considerd an unstable alpha.

## Features
* Sell things with a barcode scanner
* Type the name of a product to not need the scanner
* Track deliveries
* Track stock
* Contains a (very) limited book keeping system

## Requirements
### Server
* php >= 5.3
* php-gd (for bar code generation and statistics display)
* php-curl (used by login service which might want to be replaced)
* php-mysql
* MySQL
* gnu-barcode (deb package barcode)

The project requires a webserver, it has been developed and tested with apache
webserver but try a different one and it might work.

### Client
This project has only been tested to work with firefox and chromium but other browsers should not be a problem.

# Setting up the system
## Installation
1. Place the content of the project in a folder
2. Create an empty mysql database for the project. If the database name is not "nitroxy\_retail" grant.sql needs to be updated accordingly.
3. Create a db user. If the name of the user is not "nitroxy\_retail", grant.sql needs to be updated accordingly.
4. Copy db\_settings/nitroxy\_retail.php to db\_settings/nitroxy\_retail.local.php and edit the settings to your configuration.
5. Run (in order) 
   1. source nitroxy\_retail.sql
   2. source data.sql
   3. source grant.sql
6. Compile genbarcode for your architecture (can be ignored if you don't want barcodes for your products)
   1. make -C lib/src/genbarcode-0.4
   2. mkdir lib/bin
   3. mv lib/src/genbarcode-0.4/genbarcode lib/bin
7. Point your webserver to the public directory
8. Unless you are from the society Proxxi you want to do something about what is done in contrellers/Session.php in the authenticate part so as to not authenticate against Proxxis system...

## Adding products to the system
1. Log in to the system
2. Navigate to Lager > Kategorier
3. Use the form to add categories to the system (Candy, Soda, etc)
4. Next go to Lager > Ny leverans and create a delivery (Se seperate delivery instructions)
