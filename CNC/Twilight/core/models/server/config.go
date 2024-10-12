package server

type Config struct {
	Addr      string
	Secure    bool
	Cert, Key string
}
