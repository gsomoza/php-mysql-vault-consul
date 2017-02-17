# Usage

1. Build Containers
2. Initialize & unseal Vault
3. Check services are registered & healthy
4. Configure the PHP App

## Build Containers

Use the helper script (bash) `./compose`:

`./compose up`

or you can manually run the following:

`docker-compose -f docker-compose.yml -f vault.yml -f consul.yml up -d`

## Initialize & Unseal Vault

```bash
bin/vault init
bin/vault unseal # 3 times
```

Then write a secret (e.g. mysql password):

```bash
bin/vault auth # enter root token
bin/vault write secret/app/mysql/password value=eureka1
```

## Check Services are Registered & Ready
The `vault.yml` file exposes Vault's Consul client to the host machine. You can
access the Consul UI by browsing to "http://127.0.0.1:8500"

## Configure the PHP App

1. Install dependencies using Composer. You can do this on the host if you're
using PHP 7.0 or greater. Otherwise you'll have to login to the container:
`./compose exec app bash`
2. Copy `.dist` files inside `app/src/config/autoload` and adjust if necessary.
3. Then create a policy in Vault for the app:
```bash
cat ./app/vault/acl.hcl | bin/vault policy-write app -
```

4. And a token with that policy:
```bash
bin/vault token-generate -policy=app -format=json > app/src/data/vault_token.json
```

Now you should be able to browse to http://localhost:8080/ and see Vault in
action!
