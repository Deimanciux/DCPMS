## Introduction

Application for managing dental clinic registrations, health records. Created on Symfony framework,
MySQL database.

## Specifications

- php 8.1.2
- symfony 6.0.4
- mysql 8.0.28
- yarn 1.22.17
- node 17.5.0
- Bootstrap 4.6.0

## Installation

- clone project from git repository

- run docker containers
  `docker-compose up -d --build`

- install dependencies
  `docker exec -it php8-container bash`
  `composer install`

- install yarn
  `docker-compose run --rm node-service yarn install`

- install assets
  `docker-compose run --rm node-service yarn encore dev`

- create database and run migrations
  `docker exec -it php8-container bash`
  `php bin/console doctrine:database:create`
  `php bin/console doctrine:migrations:migrate`

## Usage

Go to the url: http://localhost:8080

