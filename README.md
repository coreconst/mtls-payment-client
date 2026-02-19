# mTLS Payment Client

> This is a test/demo library for educational purposes. It demonstrates how to implement mutual TLS (mTLS) connections and HMAC payload signing in PHP.

---

## Requirements

- Docker
- Docker Compose

---

## Setup

**1. Clone the repository**

**2. Copy the environment file and fill in your values:**

```bash
cp .env.example .env
```

**3. Place your client certificate and private key into the `certs/` directory:**

```
certs/
├── badssl.com-client.pem
└── badssl.com-client-key.pem
```

> You can download test certificates from [badssl.com/download](https://badssl.com/download/). The passphrase for the private key is `badssl.com`.

**4. Build and start the container:**

```bash
docker compose up -d
```

**5. Install Composer dependencies:**

```bash
docker compose exec app composer install
```

---

## Running Tests

**Run all tests:**

```bash
docker compose exec app ./vendor/bin/phpunit
```

**Run only unit tests:**

```bash
docker compose exec app ./vendor/bin/phpunit --testsuite Unit
```

**Run only integration tests:**

```bash
docker compose exec app ./vendor/bin/phpunit --testsuite Integration
```

> Integration tests require valid certificate paths and `.env` values to be configured before running.