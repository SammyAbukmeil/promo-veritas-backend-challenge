# Promo Veritas Competition System

This system exposes an API with various routes to:
- Create Promotions for clients
- Accept Entrants for Promotions and draw winners

Changes for production:
- Replace PHP mail() with dedicated mail service (e.g. MailGun) to avoid mail issues (Junkmail etc)