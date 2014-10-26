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
7. Set up apache2
   1. create a new site in `/etc/apache2/sites_available/100-nitroxy-retail-system.conf` (debianistic systems)
      This is what I have in my test config, update as needed with ssl cert etc.
		```
			NameVirtualHost *:80

			<VirtualHost *:80>
				ServerName retail.eric.druid.se
				ServerAlias retail

				DocumentRoot /path/to/root/Nitroxy-retail-system/public
				<Directory /path/to/root/Nitroxy-retail-system/public>
					Options Indexes FollowSymLinks MultiViews
					AllowOverride All
					Order allow,deny
					Require all granted # apache2 version >= 2.4
				</Directory>

				ErrorLog ${APACHE_LOG_DIR}/nitroxy_retail_error.log
				LogLevel warn
				CustomLog ${APACHE_LOG_DIR}/nitroxy_retail_access.log combined
			</VirtualHost>
		```

  2. Enable the site with `sudo a2ensite 100-nitroxy-retail-system.conf`
  3. Enable apache2 module rewrite `sudo a2enmod rewrite`
  4. Make sure `/etc/php5/apache2/php.ini` has `short_open_tag = On` and have a look at other settings as desired.
  5. Restart apache `sudo service apache2 restart`
8. Unless you are from the society Proxxi you want to do something about what is done in classes/User.php in the external\_login method so as to not authenticate against Proxxis system...

## Adding products to the system
1. Log in to the system
2. Navigate to Lager > Kategorier
3. Use the form to add categories to the system (Candy, Soda, etc)
4. Next go to Lager > Ny leverans and create a delivery (Se seperate delivery instructions)
