# SNOwTRICK

P6 OpenClassRoom Project

[![Codacy Badge](https://app.codacy.com/project/badge/Grade/f03f0dffe632446393f5a47e4521f12d)](https://www.codacy.com/gh/Urza45/snowtrick/dashboard?utm_source=github.com&amp;utm_medium=referral&amp;utm_content=Urza45/snowtrick&amp;utm_campaign=Badge_Grade)

## Context

Jimmy Sweat is an ambitious entrepreneur with a passion for snowboarding. Its objective is the creation of a collaborative site to make this sport known to the general public and help in learning figures (tricks).

He wants to capitalize on content provided by Internet users in order to develop rich content that arouses the interest of site users. Subsequently, Jimmy wants to develop a business of connecting with snowboard brands thanks to the traffic that the content will have generated.

For this project, we will focus on the technical creation of the site for Jimmy.

## Description of need

You are responsible for developing the site that meets Jimmy's needs. You must therefore implement the following functionalities:

    - A directory of snowboard tricks. You can take inspiration from the list of figures on Wikipedia. 
        Just integrate 10 figures, the rest will be entered by Internet users;
    - Figure management (creation, modification, consultation);
    - A space for discussion common to all figures.

To implement these features, you need to create the following pages:

    - The home page where the list of figures will appear;
    - The page for creating a new figure;
    - The edit page of a figure;
    - The presentation page of a figure (containing the common discussion space around a figure).

## Pre-requisites

The present project was developed with:

- PHP 7.4.9 (cli) (built: Aug  4 2020 11:52:41)

- MySQL  5.7.31 Community Server (GPL)

- Bootstrap v5.0

- JQuery 3.4.1

- SASS

## Installation

1.Clone Repository on your web server :

    ```text
    git clone git@github.com:Urza45/snowtrick.git
    ```

2.Configure BDD connect on `.env` file

3.Configure SMTP connect on `.env` file

4.Install dependencies, in a command prompt:

    ```text
    composer install
    ```

then

    ```text
    npm install
    ```

(Node js and Composer must be installed on your server)

5.Create database, in a command prompt:

    ```text
    php bin/console doctrine:database:create
    ```

6.Migrate tables on database, in a command prompt:

    ```text
    php bin/console doctrine:migrations:migrate
    ```

7.Load fixtures into the database, in a command prompt:

    ```text
    php bin/console doctrine:fixtures:load
    ```

(You need fixtures to have one administrator account and some others parameters in database)

8.Change parameters `mail_from` on `config/services.yaml`

9.Rebuild js script and css, in a command prompt:

    ```text
    npm run build
    ```

10.Account fixtures:
      - Role : Admin
        - PSeudo : admin
        - Email  : mon@email.fr
        - Pass   : aToo_422
