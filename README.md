# Promo Veritas Competition System

This system exposes an API with various endpoints to:
- Create Promotions for clients
- Accept Entrants for Promotions and draw winners

# Installation

- `composer install`
- `php -S localhost:8000 -t public`
- Copy `.env.example` to `.env` 
- Create `promo-veritas-promotions` MySQL DB and set in `.env` file
- Get a 32 length key from [here](http://www.unit-conversion.info/texttools/random-string-generator/) and add it to `APP_KEY` in `.env`
- `php artisan migrate`

# Interact with app

- Import Postman collection 
  - [Collection JSON file](https://gist.github.com/SammyAbukmeil/7cca86c26fa6057d8d06a90cdc814bb3)
  - Save the JSON file to your computer, in postman, click import (top left) and select the file

- Main requests
  - Create Promotion - Chance
  - Create entrant - Chance
  - Create Promotion - Winning Moment
  - Create entrant - Winning Moment

# Notes

Changes for production:
- Replace PHP mail() with dedicated mail service (e.g. MailGun) to avoid mail issues (junk mail etc)
- Check whether an email has already been entered before accepting an entrant (proved to be challenging with hashed values)

Devops plan:
- Host on EC2 and RDS
- Setup CloudWatch monitoring dashboard + alarms
- To scale up
    - Using auto-scaling group / load balancer

Time taken on challenge:
- 6 hours
