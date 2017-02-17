path "secret/app/*" {
  policy = "read"
}

path "auth/token/lookup-self" {
  policy = "read"
}
