path "database/creds/readonly" {
  policy = "read"
}

path "sys/renew" {
  policy = "write"
}

path "auth/token/lookup-self" {
  policy = "read"
}
