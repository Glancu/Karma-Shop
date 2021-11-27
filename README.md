![](./public/assets/img/fav.png)Karma-Shop
=========

<h4 align="center">A Karma-Shop is a simple shop using Symfony (REST API) and React (from [free template](https://colorlib.com/wp/template/karma/). )</h4>


![karma-shop](/public/assets/img/github/homepage-view.png)

## Notice
This project was created for training my skills. Please don't use this project for production. 

## How To Use

To clone and run this application, you'll need [Git](https://git-scm.com), [yarn](https://yarnpkg.com/) and [Redis](https://redis.io/) installed on your computer. **If u wanna use docker, you don't need this few packages installed on your computer.**

```bash
# Clone this repository
$ git clone https://github.com/Glancu/Karma-Shop karma-shop

# Go into the repository
$ cd karma-shop

# .env from .env.dist
Create .env file from .env.dist and update DATABASE_URL variable
$ cp .env.dist .env

# Install packages for frontend
$ cd frontend/
$ yarn install

# Install packages to build view
$ yarn install

# Build view
$ yarn build

# Install symfony dependencies
$ composer install

# Create database
$ php bin/console doctrine:database:create

# Generate database structure
$ php bin/console doctrine:schema:update --force

# Load fixtures
$ php bin/console doctrine:fixtures:load

# Run tests
$ php ./vendor/bin/phpunit
```

## Symfony Messenger

For this project I used symfony messenger for mails. If you want automatic consume messages, you can use [supervisor](http://supervisord.org/). In config/messenger-worker.conf you have configuration file for supervisor.

If you don't want use supervisor, you can run this command and consume messages from command lines.
```bash
$ php bin/console messenger:consume
```

## Additional commands
```bash
# Remove expired jwt tokens
$ php bin/console gesdinet:jwt:clear

# Edit frontend live
$ yarn watch

# Create another admin account (for admin panel)
$ php bin/console app:create-admin-account

# Update .css from .scss files (in frontend folder)
$ cd frontend
$ sass public/assets/scss/main.scss public/assets/css/main.css
```

## Links

### Docker
If you want to use docker, you can run [localhost:8600](http://localhost:8600) for website and [localhost:8601](http://localhost:8601) for phpmyadmin (login and password **root**). But you must run this command, to run containers
```bash
$ cd docker/
$ docker-compose up -d
```

### API Docs
If you want check api links, you can go **/api/doc**.

### Admin panel
Go to **/admin** for visit admin panel. \
Login: **admin**
Password: **admin1**

## License

MIT
