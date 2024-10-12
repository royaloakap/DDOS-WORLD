package middleware

import (
	"fmt"
	"os"
	"time"
)


func Log_Timestamp(LogFile string, Message ...interface{}) (int, error) {
	f, err := os.OpenFile(LogFile, os.O_WRONLY|os.O_CREATE|os.O_APPEND, 0644)
	if err != nil {
		return len(Message), err
	}   

	defer f.Close()

	TimeStamp := time.Now().Format("2006-01-02 15:04:05")

	return f.Write([]byte(TimeStamp+fmt.Sprint(Message...)+"\r\n"))
}

func Log_String(LogFile string, Message ...interface{}) (int, error) {
	f, err := os.OpenFile(LogFile, os.O_WRONLY|os.O_CREATE|os.O_APPEND, 0644)
	if err != nil {
		return len(Message), err
	}   

	defer f.Close()



	return f.Write([]byte(fmt.Sprint(Message...)+"\r\n"))
} 