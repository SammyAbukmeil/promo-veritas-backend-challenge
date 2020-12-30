# Promo Veritas Competition System

This system exposes an API with various endpoints to:
- Create Promotions for clients
- Accept Entrants for Promotions and draw winners

# Installation

- `composer install`
- `php -S localhost:8000 -t public`
- Copy `.env.example` to `.env` 
- Create `promo-veritas-promotions` MySQL DB and set in `.env` file
- `php artisan key:generate`

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
