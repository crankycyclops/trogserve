# Trogserve

A Laravel + Vue.js frontend for the [trogdor-pp](https://github.com/crankycyclops/trogdor-pp "trogdor-pp") text adventure game engine, which allows for the easy creation, configuration, and management of a browser-accessible text-based [MUD](https://en.wikipedia.org/wiki/MUD "MUD") server.

**WARNING:** this project is not yet in a useable state. Currently, only the following features are implemented:

1. A partially implemented REST API (accessible to admins only) for creating, querying, updating, and deleting games and players. The implemented endpoints are as follows:
 - GET /admin/api/info: for returning global information and statistics about the configured instance of trogdord
 - GET /admin/api/games: for returning a list of all currently existing games
 - POST /admin/api/games: for creating new games
 - GET /admin/api/games/{id}: for returning data about a specific game
 - DELETE /admin/api/games/{id}: for deleting a specific game
 - GET /admin/api/games/{id}/start: for starting a specific game's clock
 - GET /admin/api/games/{id}/stop: for stopping a specific game's clock
 - GET /admin/api/games/{id}/meta: for returning meta data associated with a game
 - POST /admin/api/games/{id}/meta: for setting meta data associated with a game
 - GET /admin/api/definitions: for returning a list of all game definition files available to our configured instance of trogdord
2. A partially implemented admin panel that currently displays some statistics about the configured trogdord instance and that allows for the creation, modification, and deletion of individual games.

This list will be updated as I complete more features.

## Dependencies

Laravel 7, PHP 7.2+, composer, npm, Vue.js, Vuetify, [trogdord](https://github.com/crankycyclops/trogdor-pp "trogdord"), and the [trogdord PHP extension](https://github.com/crankycyclops/trogdor-pp/tree/php7-module/src/php7/trogdord "trogdord PHP extension").

The PHP extension has been tested on and is known to build against PHP 7.2, 7.3, and 7.4.

## Installation

1. Make sure you've built, installed, and configured the trogdor-pp library, trogdord daemon, and trogdord PHP extension. You can find more information about those dependencies here: https://github.com/crankycyclops/trogdor-pp
2. `cd webroot && git clone git@github.com:crankycyclops/trogserve.git`
3. `cp webroot/.env.example webroot/.env` and configure according to your environment
4. `composer install`
5. `node install`
6. `php artisan migrate`
7. `php artisan passport:keys && php artisan passport:client --password`
8. make prod to build frontend (or make dev if not a production server)
9. To test your installation, fire up the Laravel development server: `php artisan serve`

## Configuration

Before logging into the admin, you'll need to see your database with a default username and password. Do so with the following command: `php artisan db:seed`. You can then login with the default username `admin` and the default password `password`.

You'll also need to point your configuration over to a running trogdord instance. You can find the relevant config options in `config/trogdord.php`.

Finally, to login, browse to: http://your.domain/admin and authenticate using the default username and password above.

## Exploring the code

If you're looking for the interesting backend bits that exist so far (or if you're a hiring manager who wants to see what my Laravel code looks like), I suggest you examine the following files:

For the backend API (available to admins only):

- app/Http/Controllers/AdminApiController.php
- routes/adminApi.php

For frontend session-based authentication (headless API access is also supported via Laravel Passport and token-based authentication):

- app/Http/Controllers/Auth/AdminLoginController.php
- app/Models/Admin.php
- config/auth.php
- resources/views/admin/login.blade.php

For the Vue.js-based admin interface:

- app/Http/Controllers/AdminController.php
- resources/views/admin/index.blade.php
- everything under resources/js/admin
- everything under resources/css/admin

And finally, for the build process:

- webpack.mix.js, webpack.app.mix.js, webpack.adminauth.mix.js, and webpack.admin.mix.js (interesting because I had to do a little magic to separate everything out into their own separate bundles)
- Makefile (you'll notice that in order to separate Vuetify from the main bundle, I had to resort to some Makefile magic that you might find interesting—or which you might find to be ugly, depending on your disposition.)

The default page that you'll see if you fire up http://your.domain/ is just the default Laravel page and isn't very interesting yet; all my active development right now is geared toward implementing the admin, as well as going back and fixing/enhancing trogdord to support the extra features that I need to eventually make this a viable platform.

If you've never seen the innards of a PHP extension before, you might also find the following link interesting (written in C++17): https://github.com/crankycyclops/trogdor-pp/tree/php7-module/src/php7/trogdord

## License

This project is licensed under the [GPL 3.0](https://www.gnu.org/licenses/gpl-3.0.en.html "GPL 3.0").

## Credits

James Colannino (Author)

This entire project, as well as its trogdor-pp, trogdord, and PHP extension dependencies, is my own personal passion project and has been built from the ground up. I loved text adventure games growing up and always wanted to write one. As a child, my lack of programming ability frustrated me. Now, as an adult CS major, this is a bit of personal wish fulfillment :)