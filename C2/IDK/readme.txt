this source is leaked by reflect (Thanks to him)

how to build/setup/compile (tutorial by gnu)
move all files to vps expect "HellSing" (alredy compiled)
cd /root/
apt install wget -y
apt install screen -y
apt install mysql-server -y

yum install wget mysql-server screen -y

edit Authority\config.json file and replace with your mysql details
you can change port in Authority\main.go on line 43 and 52
------------------------------------------------------------
wget https://dl.google.com/go/go1.16.4.linux-amd64.tar.gz
------------------------------------------------------------
tar -xvf go1.16.4.linux-amd64.tar.gz 
------------------------------------------------------------
mv go /usr/local 
------------------------------------------------------------
export GOROOT=/usr/local/go 
export GOPATH=$HOME/Apps/app1 
export PATH=$GOPATH/bin:$GOROOT/bin:$PATH 
------------------------------------------------------------
go build main.go
------------------------------------------------------------
(dont forget to join my discord server for more leaks)
https://discord.gg/cKcTKSMn2b
