# SNOwTRICK

P6 OpenClassRoom Project

[![Codacy Badge](https://app.codacy.com/project/badge/Grade/f03f0dffe632446393f5a47e4521f12d)](https://www.codacy.com/gh/Urza45/snowtrick/dashboard?utm_source=github.com&amp;utm_medium=referral&amp;utm_content=Urza45/snowtrick&amp;utm_campaign=Badge_Grade)

## Pre-requisites

The present project was developed with:

- PHP 7.4.9 (cli) (built: Aug  4 2020 11:52:41)

- MySQL  5.7.31 Community Server (GPL)

- Bootstrap v5.0

- JQuery 3.4.1

- SASS

## Installation

1. Clone Repository on your web server : `git clone git@github.com:Urza45/snowtrick.git`

2. Configure BDD connect on `.env` file

3. Configure SMTP connect on `.env` file

4. Install dependencies, in a command prompt: `composer install` then `npm install`
    (Node js and Composer must be installed on your server)

5. Create database, in a command prompt: `php bin/console doctrine:database:create`

6. Migrate tables on database, in a command prompt: `php bin/console doctrine:migrations:migrate`

7. Load fixtures into the database, in a command prompt: `php bin/console doctrine:fixtures:load`
    (You need fixtures to have one administrator account and some others parameters in database)

8. Change parameters `mail_from` on `config/services.yaml`

9. Account fixtures:
      - Role : Admin
        - PSeudo : admin
        - Email  : mon@email.fr
        - Pass   : aToo_422
