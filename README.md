== todooList ==
# ToDoList - Améliorez une application existante de ToDo & Co
Project 8 - ToDo & Co a start up company needs to improve his website.
## Technologies
<ul>
 <li>PHP 7.2.5</li>
 <li>Symfony LTS 5.4</li> 
 <li>MySQL 5.7.34</li> 
</ul>

## Audit
Initial project was in Symfony 3.1 - 
First Codacy Analysis
[![Codacy Badge](https://app.codacy.com/project/badge/Grade/122f6f0fb36647db8e3af715cd17d9e4)](https://www.codacy.com/gh/Fossette7/todolist/dashboard?utm_source=github.com&amp;utm_medium=referral&amp;utm_content=Fossette7/todolist&amp;utm_campaign=Badge_Grade)<hr>
## Migration
Migrate and fix deprecations from Symfony 3.1 to Symfony 3.3 then 3.4
https://symfonycasts.com/screencast/symfony4-upgrade/sf34-deprecations
and from 3.4 to 4.0 until Symfony LTS 5.4 a Symfony Long-Term Support Release, a maintained Symfony version.
## Installation

#### step1: **Copy the link** on GitHub and **clone it** on your local repository
https://github.com/saro0h/projet8-TodoList

#### step 2 **Clone** the repository to your local path. Use command `git clone`
inside your directory:  `cd my-project`

#### step 3 **Open** your **terminal** and **run**: `composer install`In server MySQL

#### step 4 **Database configuration**
**Open file** `.env` and write your configuration **username** and **password**

> DATABASE_URL: `DATABASE_URL="mysql://db_user:db_password@127.0.0.1:3306/db_name?serverVersion=5.7.34&charset=utf8"`
#### step 4 **Create database** with: `php bin/console doctrine:database:create` 

#### step 5 **Create table on database with: `php bin/console doctrine:schema:update -f`

#### step 6 **Run the migration**: `php bin/console doctrine:migrations:migrate`

#### step 7 **Run** the server : `symfony server:start`
<hr>

### TEST - PHP Unit Test
**Load the test fixture** with :  `php bin/console doctrine:fixtures:load`

**Run tests** with: `vendor/bin/phpunit`
<hr>


