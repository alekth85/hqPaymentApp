# hqPaymentApp

This is a test application that utilizes BrainTreePayments and Paypal payment SDKs.

There is no installation, no sql schema files (due to the fact it's using sqllite3 as database backend), no anything.
The reason sqllite is used is because I wanted to make it easier for you to set this up. So no database db/user creation, import of initial schema etc.
Schema is programmatically set from class.Database.php file. For your convenience.

I normally use MySQL or PostgreSQL myself, and sqllite for really small projects that don't require large RDBMS, such as this.

Setup instructions:

( These instructions assume your root for the app will be in /var/www/html/hq, and once downloaded, the app will be /var/www/html/hq/hqPaymentApp )

1. mkdir -p /var/www/html/hq
2. cd /var/www/html/hq
3. git clone https://github.com/alekth85/hqPaymentApp.git
4. cd hqPaymentApp
5. php /path/to/composer.phar install (this will install all things needed for the app to work, and obviously you'll need composer. https://getcomposer.org/download/ )
6. edit includes/config.php (only APP_DIR and APP_URL are needed to change)
7. Install php sqllite package, ubuntu/debian: apt-get install php7.0-sqlite3
8. Make sure db/hq.db is correct permissions. For the sake of this test, chmod -R 777 db/ should be fine. Note: Chmod to 777 is bad, every time. Same as laziness.

Optional:
9. Run the tests
hq@dev001(/var/www/html/hq/hqPaymentApp/tests)# php ../vendor/phpunit/phpunit/phpunit .
PHPUnit 4.8.27 by Sebastian Bergmann and contributors.

............

Time: 85 ms, Memory: 4.00MB

OK (12 tests, 12 assertions)
hq@dev001(/var/www/html/hq/hqPaymentApp/tests)#

This is the output you want to see.

10. Open the project: http://128.199.109.30/hq/hqPaymentApp/      --> this link actually exist, and is live (Oct 19, 2016)

After opening, the form is filled with predefined values for your convenience. You can change it anyway you like. Notice that when testing amex, you have to change the number
It's on the link I provided there.
Also, this current number is generated on my paypal sandbox account. I'm not sure what numbers will work (as in: your test numbers) and what will not... that's up to paypal,
i haven't tested with other paypal accounts.

11. Click go

If everything is fine, you should see:
"Payment verified. Click Here to see list of payments -"

Clicking on "Here" will show you your payment:
Var dump for result number 2 
"array(11) { ["id"]=> int(2) ["full_name"]=> string(10) "John Smith" ["cctype"]=> string(4) "visa" ["ccnumber"]=> string(16) "4996083535460392" ["ccexpire"]=> string(7) "12/2017" ["cccvv"]=> string(0) "" ["price"]=> float(12) ["currency"]=> string(3) "USD" ["gateway"]=> string(6) "paypal" ["verified"]=> int(1) ["payid"]=> string(28) "PAY-2DW60965MA404942HLADULCI" } "

It's a var_dump of the DB .. it's not formatted, but you can see it works.

Note: From all the data gateway gives back to me, i'm only entering one data into the table, "payid" field. This is because this is a test project, not a production app.
It's a proof of concept, that is, not a production app.

# Troubleshooting

If you having trouble running this app, check the logs of course. It will say what's wrong in there.
Most probable cause of having a problem is some prerequisites are not installed (such as php-sqllite package), wrong permissions or wrong path configuration.
Logs will help you clear the problem.
