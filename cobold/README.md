## Installation

1. Clone the repository

```sh
$ git clone git@github.com:gothinkster/laravel-realworld-example-app.git
```

2. Switch to the repo folder

```sh
cd cobold
```

3. Install all the dependencies using composer

```sh
composer install
```
4. Copy the example env file and make the required configuration changes in the .env file

```sh
cp .env.example .env
```
5. Generate a new application key

```sh
php artisan key:generate
```
6. Run the database migrations (Create database and Set the database connection in .env before migrating)

```sh
php artisan migrate
```
7. Start the local development server

```sh
php artisan serve
```
You can now access the server at http://localhost:8000