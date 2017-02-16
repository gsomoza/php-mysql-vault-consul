# Usage

1. Build Containers
2. Initialize & unseal Vault
3. Check services are registered & healthy

## Build Containers

Use the helper script (bash) `./compose`:

`./compose up`

or you can manually run the following:

`docker-compose -f docker-compose.yml -f vault.yml -f consul.yml up -d`

## Initialize & Unseal Vault

```bash
vault init
vault unseal # 3 times
```

## Check Services are Registered & Ready
The `vault.yml` file exposes Vault's Consul client to the host machine. You can
access the Consul UI by browsing to "http://127.0.0.1:8500"
