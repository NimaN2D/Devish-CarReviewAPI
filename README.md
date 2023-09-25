# Devish assessment task

Welcome to Devish task! This is the development branch of our Symfony project.

## Introduction

This is the development version of our project. It includes all the features and entities, such as Car, Review, User, Model, and Manufacture.

## Prerequisites

### Running with Docker (Recommended)

To run the project with Docker, you don't need to have PHP, Symfony, or PostgreSQL installed locally. Docker encapsulates the entire environment.

1. Make sure you have Docker installed on your computer.

2. Clone the project from our repository:

   ```shell
   git clone git@github.com:NimaN2D/Devish-CarReviewAPI.git
    ```
3. Go into the project's folder:

   ```shell
   cd Devish-CarReviewAPI
   ```
4. Build the project:

   ```shell
   docker-compose build
   ```
4. Run the project:

   ```shell
   docker-compose up -d
   ```
5. Install the project's dependencies:

   ```shell
   docker compose exec php composer install
   ```

4. Create the necessary database tables:
   ```shell
   docker compose exec php php bin/console doctrine:migrations:migrate
   ```
   
5. You can now access the project at http://localhost:9090 in your web browser.

### Running Locally (For Local Development)

If you plan to work on the project outside of Docker for development purposes or debugging, you'll need the following dependencies:

- PHP 8.x
- Symfony 6.x
- PostgreSQL

Here's how to get started:

1. Clone the project from our repository:

   ```shell
   git clone git@github.com:NimaN2D/Devish-CarReviewAPI.git
    ```
2. Go into the project's folder:

   ```shell
   cd Devish-CarReviewAPI
   ```
3. Install the project's dependencies:

   ```shell
   composer install
   ```
4. Set up a PostgreSQL database and ensure it connects to the project.
5. Create the necessary database tables:

   ```shell
   php bin/console doctrine:migrations:migrate
   ```
6. You can now open the project in your web browser at http://localhost:8000.

## How to use
The API documentation is available at http://localhost:8000/api.

## Docker
The project is Dockerized, making it easy to deploy and manage in a containerized environment. Docker eliminates the need for local installations of PHP, Symfony, or PostgreSQL.

## Local Development
If you prefer to work on the project outside of Docker for development purposes or debugging, ensure you have the necessary dependencies installed locally.

That's it! You're now ready to explore and use our project.
