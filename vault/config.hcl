backend "consul" {
  address = "127.0.0.1:8500"
  redirect_addr = "http://172.16.2.10:8200"
  path = "vault"
}
listener "tcp" {
  address = "0.0.0.0:8200"
  tls_disable = 1
}
disable_mlock = true
