# smart-httpflood
Golgang smart http flood


This script supports HTTP Version, Cookie and Post Data.

Installation:

apt install snapd

snap install go --classic

go build

Link for compiled: https://anonfiles.com/HcO577T7u6/StresserUS


Usage:


./StresserUS version=<version> host=<host> domain=<host header> limit=<rs-ip> time=<time> list=<proxies.txt> threads=<threads> mode=GET/POST cookie=<ddos=true> data=<post=true>


Eexample:


./StresserUS version=2 host=https://pizza-delivery.com limit=64 time=120 list=proxy.txt threads=1000 mode=GET
