# Drupal 8 Install

We will be using Lando to run a Drupal 8 install on Docker! 

## Pre-reqs

We will be installing
1. Homebrew for Mac (Optional)
2. Docker
3. Lando

### Homebrew (Optional)

For Mac, [Homebrew](https://brew.sh/) makes the installation easy.

```/usr/bin/ruby -e "$(curl -fsSL https://raw.githubusercontent.com/Homebrew/install/master/install)"```

### Docker (Required)

Download Docker for your machine from the correct link in Docker Store. Find the links for Windows and Mac when you scroll down on the Get Started page here: [https://www.docker.com/get-started](https://www.docker.com/get-started)

### Lando (Required)

Then we will follow this tutorial -- [https://www.jeffgeerling.com/blog/2018/getting-started-lando-testing-fresh-drupal-8-umami-site](https://www.jeffgeerling.com/blog/2018/getting-started-lando-testing-fresh-drupal-8-umami-site).

1. Install Docker for Mac / Docker for Windows / Docker CE (if it's not already installed).
2. Install Lando (on Mac, `brew cask install lando`, otherwise, [download the .dmg, .exe., .deb., or .rpm](https://docs.devwithlando.io/installation/installing.html)).
3. You'll need a Drupal codebase, so go somewhere on your computer and use Git to clone it: `git clone --branch 8.6.x https://git.drupal.org/project/drupal.git lando-d8`
4. Change into the Drupal directory: `cd lando-d8`
5. Run `lando init`, `answering drupal8`, `.`, and `Lando D8` (the terminal wizard changes this to `lando-d-8` as seen below).
![Database Setup](/images/lando_init.png)
6. Run lando start, and wait while all the Docker containers are set up.
7. Run lando composer install (this will use Composer/PHP inside the Docker container to build Drupal's Composer dependencies).
8. Go to the site's URL in your web browser, and complete the Drupal install wizard with these options:
    Database host: database 
    Database name, username, password: drupal8
![Database Setup](/images/database_setup.png)

*Make note, on the Drupal 8 install, the database host name field is under 'Advanced Options' during the database configuration section. Ensure that instead of `localhost`, you use the name `database` for field `host`.*
