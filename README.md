# Introduction
This project is based on PHP Symfony2 Framework, uses Mysql as the database storage and Redis as the cache server, PSR-4 as the code standard. Also some other libs or tools are involved: Boostrap and jquery, bower, composer, SASS.  
Demo: https://booster.mmyyabb.com
# Directory Structure
  - ***app/config***: config files dir.
  - ***app/resources***: services, routings or other php related resources 
  - ***app/src***: main source files
  - ***bin***: command entry file
  - ***database-migration***: database migration
  - ***res/cache***: cache dir
  - ***res/logs***: logs dir
  - ***res/styles***: SCSS files dir
  - ***res/template***: twig template files dir
  - ***webroot***: web root dir

# Database:
 - Tables: fundraisers, reviews.
 - Foreign key/constraint: fundraiser_id_constraint. I use this constraint to avoid invalid data inserting to table reviews.
 - Trigger: update_rating.  When a new review is created, the rating of that fundraiser is updated automatically. 

# Implementation
 - All form has the validation on both front end and back end. All pages are user-friendly, users will see either an error message or success message when they interact with the pages.
 - Users request rate is implemented with Redis in order to avoid potential attacks. Users are limit to send no more than 3 posts per 30 seconds. 
 - Email addresses with proper format is considered as an unique user.
 - Unit tests are implements for util class, controllers, entity, helper class.

# Alternative for email verification
In this version, I just check the format for email validation. If we need to verify whether an email address is used by someone but not just the one with the right format. Those are steps: 
1. Add new columns flag, token for table reviews.
2. Once users submit the forms, we insert a new record into table reviews and set reviews.flag = 0, reviews.token = a random string 
3. Create another token with reviews.token, reviews.email_address and send users an email including a link with this new token.
4. Users open their mailbox and click the link.
5. We process this token and compare with the ones in table reviews: token=reviews.token and review.email=email address
6. If a matched record is found, we update review.flag and consider users review is valid.

### Installation
1.Add write rule & ENV

For apache
```sh
RewriteCond %{DOCUMENT_ROOT}%{REQUEST_URI} !-f
RewriteRule ^(.*)$ /index.php$1 [L]
......
SetEnv BOOSTER_ENV booster_dev
```
For nginx
```sh
location ~ \.php$|/index.php/|^/status$ {
fastcgi_pass   unix:/var/run/php/php5.6-fpm.sock;
fastcgi_index  index.php;
fastcgi_param  SCRIPT_FILENAME  $document_root$fastcgi_script_name;
fastcgi_param  BOOSTER_ENV booster_prod;
include        fastcgi_params;
fastcgi_read_timeout 600;
}
```
2.Copy the code to local server
```
$ git clone https://github.com/sunceenjoy/booster
# Enter into project dir
$ cd booster
```

3.Import migration.sql into database
```sh
# Change username to your db username
$ mysql -u username -p < ./database-migration/migration.sql
```
4.Set parameters for databse, redis
```sh
$ vim ./app/config/prod/app.ini
```
5.Set up cache, logs privileges
```sh
$ mkdir -m 777 ./res/cache
$ mkdir -m 777 ./res/logs
```

6.Install php dependences.
```sh
$ composer install
```

 # Run unit test:

```sh
#Test files are located in the same directories as its function classes.
$  phpunit -c phpunit.xml.dist
```
