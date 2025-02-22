Listing Scrapper
========================

Config
------

Make a copy of `.env` as `.env.local` and set your own values.

Installation
------------

Clone this repository and start container:

```bash
docker-compose up --build -d
```

Run composer install:

```bash
docker/composer install
```

Scrapper is running automatically on container start.

Go to http://localhost:35000/ to see all the results manually.

`DISCORD_WEBHOOK_PING` will be pinged with script uptime.

Errors and found listings will be sent to `DISCORD_WEBHOOK`.