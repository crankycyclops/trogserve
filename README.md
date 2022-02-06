# Trogserve

A Laravel + Vue.js frontend for the [trogdor-pp](https://github.com/crankycyclops/trogdor-pp "trogdor-pp") text adventure game engine, which allows for the easy creation, configuration, and management of a browser-accessible text-based [MUD](https://en.wikipedia.org/wiki/MUD "MUD") server.


The following features have been implemented so far:

1. A partially implemented admin REST API for creating, querying, updating, and deleting games and players. The implemented endpoints are as follows:
 - GET /admin/api/config: returns non-sensitive settings set in trogdord.ini
 - GET /admin/api/info: returns global information and statistics about the configured instance of trogdord
 - GET /admin/api/definitions: for returning a list of all game definition files available to our configured instance of trogdord
 - POST /admin/api/dump: dumps trogdord's state to disk
 - POST /admin/api/restore: restores trogdord's state from disk
 - GET /admin/api/games: returns a list of all currently existing games
 - POST /admin/api/games: creates a new game
 - GET /admin/api/games/{id}: returns data about a specific game
 - DELETE /admin/api/games/{id}: deletes a specific game
 - GET /admin/api/games/{id}/start: starts a game's clock
 - GET /admin/api/games/{id}/stop: stops a game's clock
 - GET /admin/api/games/{id}/meta: returns meta data associated with a game
 - POST /admin/api/games/{id}/meta: sets meta data associated with a game
 - GET /admin/api/games/{id}/players: returns a list of all players in a game
 - POST /admin/api/games/{id}/players: creates a new player in a game
 - DELETE /admin/api/games/{id}/players/{name}: removes a player from a game
 - GET /admin/api/games/{id}/dump: dumps a game to disk
 - GET /admin/api/dumps: returns a list of all dumped games
 - GET /admin/api/dumps/{id}: returns the details of a dumped game
 - DELETE /admin/api/dumps/{id}: deletes a dumped game
 - POST /admin/api/dumps/{id}/restore: restores a dumped game
 - GET /admin/api/dumps/{id}/slots: returns a list of slots (versions) for a game dump
 - GET /admin/api/dumps/{id}/slots/{slot}: returns details of a specific game dump slot
 - DELETE /admin/api/dumps/{id}/slots/{slot}: deletes a specific game dump slot (deletes the whole dump history if it's the only slot left)
 - POST /admin/api/dumps/{id}/slots/{slot}/restore: restores a game from a specific dump slot
2. A partially implemented admin panel that currently displays some statistics about the configured trogdord instance and that allows for the creation, modification, and deletion of individual games and players.
3. A public REST API for listing and interacting with available games:
 - GET /api/games: for returning a list of all running and publicly available games.
 - GET /api/games/{id}: return the details of a running and publicly accessible game.
4. Basic game play on the front end as well as a homepage describing the project and its use.

This list will be updated as I complete more features.

## Dependencies

Laravel 8, Laravel Passport 10, PHP 7.4+, composer, npm, Vue.js, Vuetify, [trogdord](https://github.com/crankycyclops/trogdor-pp "trogdord") 0.60.1+, the [trogdord npm module](https://github.com/crankycyclops/trogdor-pp/tree/master/src/nodejs/trogdord "trogdord npm module") 0.5.0+, and the [trogdord PHP extension](https://github.com/crankycyclops/trogdor-pp/tree/master/src/trogdord "trogdord PHP extension") 0.3.0+.

The PHP extension has been tested with and is known to build against PHP 7.2, 7.3, 7.4, 8.0, and 8.1.

## Installation with Docker

1. `git clone git@github.com:crankycyclops/trogserve.git && cd trogserve`
2. `cd docker`
3. `DOCKER_UID=<your uid on the host> docker-compose up`. If you don't execute the container with the same UID as you on the host, you're going to have issues.
4. `docker container ls` and look for the container using the `crankycyclops/php8.1-fpm-trogdord-ext-npm` image.
5. `docker exec -it <container id> /bin/bash`

The next steps are executed inside the container:

6. `source /etc/profile`. This file doesn't automatically get executed when you attach to the container and you need to incldue it before you can run npm.
7. `cd /var/www/trogserve`
8. `composer install`
9. `npm install --cache /tmp && cd sockserve && npm install --cache /tmp && cd ..`. If you don't execute these commands with the `--cache /tmp` argument, you're going to get an error about a directory owned by root. I'm eventually going to fix this, but for now, this kludge works.
10. `php artisan migrate && php artisan db:seed`
11. `php artisan passport:keys`
12. `php artisan passport:client --password --provider admins`. Accept the default and press enter.
13. `make dev`. You can also type `make prod` if you want smaller bundles, but you won't have as much information if things go wrong.

At this point, you can exit the container and test your environment by browsing to `http://localhost:8080`. You should also be able to browse to `http://localhost:8080/admin` and login with username `admin` and default password `password`. At some point (certainly before I make a Docker environment that's considered safe for production), I'm going to simplify/automate a lot of these steps.

Note that the `docker-compose.yml` file that currently comes with Trogserve is intended for a development environment. It shouldn't be considered a stable or secure application. Also, note that it bind-mounts the web root to the host's cloned repository so that changes made on the host filesystem will be immediately visible to the container.

## Installation Without Docker

1. Make sure you've built, installed, and configured the trogdor-pp library, trogdord daemon, and trogdord PHP extension. Furthermore, make sure that the `trogdord` daemon is running in the background and listening on `localhost`. You can find more information about those dependencies here: https://github.com/crankycyclops/trogdor-pp
2. `cd webroot && git clone git@github.com:crankycyclops/trogserve.git`
3. `cp webroot/.env.example webroot/.env` and configure according to your environment
4. `composer install`
5. `npm install && cd webroot/sockserve && npm install && cd ..`
6. `php artisan migrate`
7. `php artisan passport:keys && php artisan passport:client --password --provider admins`
8. `make prod` to build frontend (or `make dev` if not a production server)
9. To test your installation, fire up the Laravel development server: `php artisan serve`

These instructions do not currently cover the installation of a real HTTP server.

## Configuration

Before logging into the admin, you'll need to seed your database with a default username and password. Do so with the following command: `php artisan db:seed`. You can then login with the default username `admin` and the default password `password`.

You'll also need to point your configuration over to a running trogdord instance. You can find the relevant config options in `config/trogdord.php`.

Finally, to login, browse to: http://your.domain/admin and authenticate using the default username and password above.

## Exploring the code

If you're looking for the interesting backend bits that exist so far (or if you're a hiring manager who wants to see what my code looks like when I'm working with Laravel and Vue.js), I suggest you examine the following files:

For the backend API (available to admins only):

- app/Http/Controllers/AdminApiController.php
- routes/adminApi.php

For frontend session-based authentication (headless API access is also supported via Laravel Passport and token-based authentication):

- app/Http/Controllers/Auth/AdminLoginController.php
- app/Models/Admin.php
- routes/admin.php
- config/auth.php
- resources/views/admin/login.blade.php

For the Vue.js-based admin interface:

- app/Http/Controllers/AdminController.php
- resources/views/admin/index.blade.php
- everything under resources/js/admin
- everything under resources/css/admin

For the Node.js websocket server used to send and receive messages between the browser and trogdord:

- sockserve/index.js

For the frontend API and SPA used to retrieve information about and play games:

- app/Http/Controllers/ApiController.php
- app/routes/publicApi.php
- app/Http/Controllers/FrontendController.php
- app/routes/public.php
- resources/views/frontend/app.blade.php
- Everything under resources/js/app

And finally, for the build process:

- webpack.mix.js, webpack.app.mix.js, webpack.adminauth.mix.js, and webpack.admin.mix.js: interesting because I had to do a little magic to separate everything out into their own separate bundles
- Makefile: you'll notice that in order to separate Vuetify from the main bundle, I had to resort to some Makefile magic that you might find interesting—or that you might find ugly, depending on your disposition :)

If you've never seen the innards of a PHP extension before, you might also find the following link to my sister project interesting (written in C++17): https://github.com/crankycyclops/trogdor-pp/tree/master/src/php7/trogdord

## License

This project is licensed under the [GPL 3.0](https://www.gnu.org/licenses/gpl-3.0.en.html "GPL 3.0").

## Credits

James Colannino (Author)

This entire repo, as well as its trogdor-pp, trogdord, and PHP extension dependencies, is my own personal passion project built from the ground up. Growing up, I loved text adventure games and wanted to write one, but as a child, my lack of programming ability always frustrated me. Now, as an adult CS major, this is a bit of over-the-top wish fulfillment on my part.

Enjoy!
