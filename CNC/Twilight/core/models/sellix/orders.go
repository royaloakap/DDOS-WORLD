package sellix

import "encoding/json"

type OrdersResp struct {
	Status int `json:"status"`
	Data   struct {
		Orders []struct {
			Uniqid  string `json:"uniqid"`
			Product struct {
				Currency string `json:"currency"`
				Price    int    `json:"price"`
			} `json:"product"`
		} `json:"orders"`
	} `json:"data"`
}

func (c *Client) GetOrders() (*OrdersResp, error) {
	r, err := c.CreateRequest("GET", "", RootURL+Orders)
	if err != nil {
		return nil, err
	}
	resp, err := c.client.Do(r)
	if err != nil {
		return nil, err
	}
	v := new(OrdersResp)
	json.NewDecoder(resp.Body).Decode(&v)
	return v, nil
}
