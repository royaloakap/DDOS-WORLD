package term

import (
    "io"
    "sync"
)

type Term struct {
    Io    io.ReadWriter
    Mutex sync.Mutex
}

func (T *Term) Write(data []byte) {
    T.Io.Write(data)
}

func New(Io io.ReadWriter) *Term {
    return &Term{
        Io: Io,
    }
}

func (T *Term) ReadLine(prompt string) (string, error) {
    return T.readline(prompt, false)
}

func (T *Term) ReadPassword(prompt string) (string, error) {
    return T.readline(prompt, true)
}

func (T *Term) readline(prompt string, password bool) (string, error) {
    T.Mutex.Lock()
    defer T.Mutex.Unlock()
    var ln []byte
    index := 0
    for {
        var buffer = make([]byte, 1) // 1 byte per character
        _, err := T.Io.Read(buffer)
        if err != nil {
            return "", err
        }
        switch buffer[0] {
        case 27:
            esc := make([]byte, 2)
            _, err := T.Io.Read(esc)
            if err != nil {
                return "", err
            }
            break
        case 13: // carriage return `\r`
            break
        case 0x03: // ctrl + c
            T.Write([]byte("^C\r\n"))
            T.Write([]byte(prompt))
            return "", nil
        case 10, 9: // enter key
            T.Write([]byte("\r\n"))
            return string(ln), nil
        case 127:
            if len(ln) == 0 {
                break
            }
            index--
            T.Write([]byte{127}) // write a backspace
            ln = ln[:len(ln)-1]
            break
        default:
            if password {
                T.Write([]byte("*"))
            } else {
                T.Write(buffer)
            }
            ln = append(ln, buffer...)
            index++
        }
    }
}