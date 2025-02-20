# PHP API Project

## Introduction

Welcome to my project !

## ✨ Features

* **Book Management**: Add, delete, update, and view available books.
* **Author Management**: Add, delete, update, and view authors.
* **User Management**: Add, delete, update, and view users.
* **Loan Tracking**: Record book loans and returns by users.
* **JWT Authentication**: Secure access to admin features with a simple login system.
* **API Documentation**: Provide a usage guide for the API with request examples.

## 📚 API Documentation

The complete API documentation is available in the 

[API_DOCUMENTATION.md](API_DOCUMENTATION.md)

## 🚀 Installation

### Prerequisites

* [Docker](https://docs.docker.com/get-docker/)
* [Docker Compose](https://docs.docker.com/compose/install/)
* [Composer](https://getcomposer.org/download/)

### Installing Docker

Follow the instructions on the official [Docker](https://docs.docker.com/get-docker/) website to install Docker on your machine.

### Installing Docker Compose

Follow the instructions on the official [Docker Compose](https://docs.docker.com/compose/install/) website to install Docker Compose.

### Installing Composer

Follow the instructions on the official [Composer](https://getcomposer.org/download/) website to install Composer.

### Configuration

1. Create a `.env` file at the root of the project, with the same content as the .env.sample
2. Add the database and JWT configuration values
3. Open the app folder and run the command `composer install` in it

### Starting the Project

To start the project, run the command:
```sh
docker compose up -d
```
 In the project folder.

The application will be available at [http://localhost](http://localhost).

### Accessing phpMyAdmin

To access phpMyAdmin:
```sh
http://localhost:8090
```
Use the database connection information defined in the 

.env

 file.

## 🛠️ Available Commands

### Stopping the Project

To stop the project, run the following command:
```sh
docker compose down
```

### Viewing Logs

To view the logs of the services, you can use the following command:
```sh
docker compose logs -f
```
This will show the logs of all the services. Press `Ctrl + C` to exit the logs.

## 🔧 Technologies

* PHP 8.0
* MySQL
* Docker
* Docker Compose
* Composer
* JWT for authentication
* Nginx

## License

This project is open-source software licensed under the [MIT license](https://opensource.org/licenses/MIT).

## Conclusion

You can modify the project as you want, remember that it's a school project and PHP is not my main language so enjoy it as you can !

P.S. If you encounter any issues, remember: "It's not a bug, it's a feature!"