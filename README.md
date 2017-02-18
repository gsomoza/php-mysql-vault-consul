# Usage

1. Build Containers
2. Initialize & unseal Vault
3. Check services are registered & healthy
4. Configure Vault
5. Configure the PHP App

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

## Check All Services are Registered & Ready
The `vault.yml` file exposes Vault's Consul client to the host machine. You can
access the Consul UI by browsing to "http://127.0.0.1:8500"

### Let Vault Create MySQL Secrets Dynamically
Next, we will mount the `mysql` secrets backend and configure it to create
users for our database dynamically.

First use the mysql `root` user to create another user that has GRANT
privileges on the `myapp` database and can connect to it from the `vault`
container. The mysql `root` password cab be found towards the top of the log
output of the `mysql` container.

Then we will show Vault how to use this user:
```bash
bin/vault mount mysql
bin/vault write mysql/config/connection \
  connection_url="admin:adminpass@tcp(mysql:3306)"
bin/vault write mysql/config/lease lease=1h lease_max=24h
bin/vault write mysql/roles/readonly sql=@/vault/config/mysql/readonly.sql
# I recommend checking that file out to understand what's going on
```

You should now be able to ask Vault for access to the "myapp" database by
reading from the role we just created:

```bash
bin/vault read mysql/roles/readonly
```

Vault should print a username and password, that you (or an app) can use to
make `SELECT` queries on the `myapp` database.


## Configure the PHP App

1. Install dependencies using Composer. You can do this on the host if you're
using PHP 7.0 or greater. Otherwise you'll have to login to the container:
`./compose exec app bash`
2. Copy `.dist` files inside `app/src/config/autoload` and adjust if necessary.
3. Then create a policy in Vault for the app:
```bash
bin/vault policy-write app -< ./app/vault/acl.hcl
```

4. And a token with that policy:
```bash
bin/vault token-create -policy=app -format=json > app/src/data/vault_token.json
```

Now you should be able to browse to http://localhost:8080/ and see Vault in
action!

1. The application will request a "readonly" role to Vault
2. It will use that to query the `myapp` table for some info.
3. If all works, you should see a green success message in the homepage.

## TODOs
1. Cache & automatically renew leases
2. Use an Auth Backend (probably App Role) to avoid having to create and use `vault_token.json`
3. Create helper scripts that will automate the Vault setup
