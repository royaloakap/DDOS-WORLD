package sellix

import (
	"api/core/models"
	"io"
	"net/http"
	"strings"
)

var (
	Manager = NewClient(models.Config.Autobuy.Key)
)

type Client struct {
	client         *http.Client
	Authentication string
}

func NewClient(authentication string) *Client {
	return &Client{
		client:         http.DefaultClient,
		Authentication: "Bearer " + authentication,
	}
}

func (c *Client) CreateRequest(method, data, query string) (*http.Request, error) {
	r, err := http.NewRequest(method, RootURL+query, func() io.Reader {
		if strings.ToLower(method) == "post" {
			return strings.NewReader(data)
		}
		return nil
	}())
	if err != nil {
		return nil, err
	}
	r.Header.Add("Content-Type", "application/json")
	r.Header.Add("Authorization", c.Authentication)
	return r, nil
}
