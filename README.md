![](./public/assets/img/fav.png)Karma-Shop
=========

<h4 align="center">A Karma-Shop is a simple shop using Symfony (REST API) and React (from [free template](https://colorlib.com/wp/template/karma/). )</h4>


![karma-shop](/public/assets/img/github/homepage-view.png)

## Notice
This project was created for training my skills. Please don't use this project for production. 

## How To Use

To clone and run this application, you'll need [Git](https://git-scm.com) and [yarn](https://yarnpkg.com/) installed on your computer. From your command line:

```bash
# Clone this repository
$ git clone https://github.com/Glancu/Karma-Shop karma-shop

# Go into the repository
$ cd karma-shop

# .env from .env.dist
Create .env file from .env.dist and update DATABASE_URL variable
$ cp .env.dist .env

# docker
If u have docker, you can use it for this project. Go to docker directory and run
$ docker-compose up -d

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

# Edit frontend
$ yarn watch

# Create another admin account (for admin panel)
$ php bin/console app:create-admin-account

# Update .css from .scss files (in frontend folder)
$ cd frontend
$ sass public/assets/scss/main.scss public/assets/css/main.css
```

## Links

### Docker
If you use docker, you can run [localhost:8600](http://localhost:8600) for website and [localhost:8601](http://localhost:8601) for phpmyadmin (login and password **root**).

### API Docs
If you want check api links, you can go **/api/doc**.

### Admin panel
Go to **/admin** for visit admin panel. \
Login: **admin**
Password: **admin1**

## License

MIT
