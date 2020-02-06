Steps to deploy (turn this into a Make target, like 'ENV="prod" make deploy'):

1. Checkout from Github
2. cp .env.example .env and setup appropriately
3. composer install
4. node install
5. Setup .env file
6. php artisan migrate
7. php artisan passport:keys
8. php artisan passport:client --password
9. make prod to build frontend (or dev if not a production server)
