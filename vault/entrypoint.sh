#!/bin/dumb-init /bin/sh
set -e

if [ -z "$CONSUL_ADVERTISE_ADDR" ]; then
  CONSUL_ADVERTISE_ADDR=172.16.2.10
fi

/usr/local/bin/consul.sh agent -node vault_consul -ui -client 0.0.0.0 -advertise $CONSUL_ADVERTISE_ADDR -retry-join consul1 &
/usr/local/bin/vault.sh $@
