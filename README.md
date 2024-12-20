# Dep Free PHP

----------------
[![License](https://img.shields.io/github/license/CamKem/dep-free-php)](https://github.com/CamKem/dep-free-php/blob/master/LICENSE)
[![GitHub issues](https://img.shields.io/github/issues/CamKem/dep-free-php)](https://github.com/CamKem/dep-free-php/issues)
[![GitHub last commit](https://img.shields.io/github/last-commit/CamKem/dep-free-php)](https://github.com/CamKem/dep-free-php/commits/master)
----------------

## Overview

This project is a dependency-free PHP framework that leverages all the features of PHP to create a fully functional MVC architecture. It encapsulates a wide range of features within its classes, providing a comprehensive solution for web application development.

## Features

- **Authentication**: Secure user authentication mechanisms.
- **Authorization**: Role-based access control (RBAC) for fine-grained permissions.
- **Mailing**: Integration with mailing services for sending emails.
- **Templating**: Efficient templating engine for rendering views.
- **IoC Container**: Inversion of Control container for managing dependencies.
- **DI**: Dependency Injection for better code modularity and testing.
- **Environment Management**: Easy management of environment configurations.
- **Service Providers**: Extendable service providers for adding functionality.
- **Session Handling**: Robust session management.
- **Data Validation**: Comprehensive data validation mechanisms.
- **Exception Handling**: Graceful handling of exceptions.
- **Slugging**: URL-friendly slugs for resources.
- **Query Builder**: Fluent query builder for database interactions.
- **Database Connection**: Easy database connection management.
- **FileSystem**: File system operations and management.
- **HTTP Request/Response Handling**: Handling of inbound and outbound HTTP requests and responses.
- **Data Collections**: Advanced data collection utilities.
- **Caching**: Efficient caching mechanisms.
- **Middleware**: Middleware support for request processing.
- **User Account Control**: Features like cookies (remember me) and password reset tokens.
- **And More**: Additional features to support modern web application development.

## Getting Started

To get started with this framework, follow these steps:

1. **Clone the repository**:
    ```sh
    git clone https://github.com/CamKem/dep-free-php.git project_name
    ```
   
2. **Create a mysql database**:
    ```sql
    CREATE DATABASE project_name;
    ```

3. **Copy the environment file**:
    ```sh
    cp .env.example .env
    ```
   
4. **Update the environment file**:
    Update the `.env` file with your database credentials, mailer settings, and other configurations.
    ```dotenv
    APP_NAME=DepFreePHP
   
    DB_USER=root
    DB_PASSWORD=
    DB_NAME=you_database_name
    
    MAILER_HOST=smtp.mailtrap.io
    MAILER_PORT=2525
    MAILER_USERNAME=your_username
    MAILER_PASSWORD=your_password
   
    # Other configurations...
    ```
5. **Star a PHP server to run the application**:
    ```sh
    php -S localhost:8000 -t public
    ```

## Documentation

In the future, we will provide detailed documentation on how to use the features of this framework. Stay tuned!

## Contributing

We welcome contributions! Please read our [contributing guidelines](./CONTRIBUTING.md) before submitting a pull request.

## License

This project is licensed under the MIT Licence. See the [LICENCE](./LICENCE) file for more information.