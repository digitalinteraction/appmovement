App Movement
============
App Movement is an open source collaborative mobile application design platform

License
----------------
Licenced under the Apache License v2.0:

http://www.apache.org/licenses/LICENSE-2.0

Software included in the App Movement distribution
----------------
CakePHP (MIT) : [https://github.com/cakephp/cakephp](https://github.com/cakephp/cakephp)

Bootstrap (MIT) by Twitter, Inc. : [https://github.com/twbs/bootstrap](https://github.com/twbs/bootstrap)

HashIds (MIT) by @ivanakimov : [https://github.com/ivanakimov/hashids.php](https://github.com/ivanakimov/hashids.php)

Amazon S3 PHP class by Donovan Schönknecht : [https://github.com/tpyo/amazon-s3-php-class](https://github.com/tpyo/amazon-s3-php-class)

imageColor (MIT) by Blaine Schmeisser : [https://github.com/blainesch/imageColor](https://github.com/blainesch/imageColor)

JQuery (MIT) by The JQuery Foundation : [https://github.com/jquery/jquery](https://github.com/jquery/jquery)

JQuery fancyBox (Creative Commons Attribution-NonCommercial 3.0) by Jānis Skarnelis : [https://github.com/fancyapps/fancyBox](https://github.com/fancyapps/fancyBox)

JQuery File Upload Plugin (MIT) by Sebastian Tschan : [https://github.com/blueimp/jQuery-File-Upload](https://github.com/blueimp/jQuery-File-Upload)

JQuery jscolor (GNU Lesser General Public License) by Jan Odvarko : [http://jscolor.com](http://jscolor.com)

Bootstrap-tour (Apache License, Version 2.0) by Ulrich Sossou : [https://github.com/sorich87/](https://github.com/sorich87/)

Chartjs (MIT) by Nick Downie : [https://github.com/nnnick/Chart.js](https://github.com/nnnick/Chart.js)

imagesLoaded (MIT) by Oliver Caldwell : [https://github.com/Olical/EventEmitter](https://github.com/Olical/EventEmitter)

JQuery timeago (MIT) by Ryan McGeary : [https://github.com/rmm5t/jquery-timeago](https://github.com/rmm5t/jquery-timeago)

jQuery UI Tag-it (MIT) by Levy Carneiro Jr. : [https://github.com/aehlke/tag-it](https://github.com/aehlke/tag-it)

sprintf.js (3-clause BSD license) by Alexandru Marasteanu : [https://github.com/alexei/sprintf.js](https://github.com/alexei/sprintf.js)

Modernizr (MIT & BSD) by Modernizr : [https://github.com/Modernizr/Modernizr](https://github.com/Modernizr/Modernizr)

Masonry (MIT) by David DeSandro : [https://github.com/desandro/masonry](https://github.com/desandro/masonry)

KineticJS (MIT) by Eric Rowell : [https://github.com/ericdrowell/KineticJS](https://github.com/ericdrowell/KineticJS)

Very simple jQuery color picker (MIT) by Tanguy Krotoff : [https://github.com/tkrotoff/jquery-simplecolorpicker](https://github.com/tkrotoff/jquery-simplecolorpicker)

jquery.counterup.js (GPL v2) by Benjamin Intal : [https://github.com/cmincarelli/jquery.counterup](https://github.com/cmincarelli/jquery.counterup)

apiDoc (MIT) by Peter Rottmann : [https://github.com/apidoc/apidoc](https://github.com/apidoc/apidoc)


Get Started
============

Configuration
----------------
To get started it is important to include the following configuration files in 

bootstrap.php
- Add the folowing
 - URL_SALT
 - SHARE_LINK_SALT
 - AWS_ACCESS_KEY
 - AWS_SECRET_KEY
 - GEOLOCATION_API_KEY
 - FOURSQUARE_CLIENT_ID
 - FOURSQUARE_CLIENT_SECRET
 - FOURSQUARE_VERSION
 - GOOGLE_RECAPTCHA_SECRET
 - COOKIE_KEY
 - SHORT_URL_PATH

core.php
- Add the following
 - SECURITY_SALT
 - CIPHER_SEED

database.php
- Add MySQL connection configuration for your environment

email.php
- Add sendgrid credentials

Database
----------------
App Movement uses MySQL and has a primaray database for the platform and secondary databases for each generated application.

The database schema is included for the main database - 'app/Config/Schema/schema.php'

The schema can be different for each type of app, the geolocation template has 5 tables; likes, photos, reports, reviews and venues.