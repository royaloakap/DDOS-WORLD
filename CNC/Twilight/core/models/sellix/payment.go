package sellix

import (
	"encoding/json"
	"errors"
	"fmt"
	"net"
	"net/http"
	"reflect"
	"strings"
)

type Payment struct {
	*PaymentReq
}

type PaymentReq struct {
	Title         string      `json:"title"`
	ProductID     interface{} `json:"product_id"`
	Gateway       interface{} `json:"gateway"`
	Value         float64     `json:"value"`
	Currency      string      `json:"currency"`
	Quantity      int         `json:"quantity"`
	CouponCode    string      `json:"coupon_code"`
	Confirmations int         `json:"confirmations"`
	Email         string      `json:"email"`
	FraudShield   struct {
		IP           string `json:"ip"`
		UserAgent    string `json:"user_agent"`
		UserLanguage string `json:"user_language"`
	} `json:"fraud_shield"`
	Webhook    string `json:"webhook"`
	WhiteLabel bool   `json:"white_label"`
	ReturnURL  string `json:"return_url"`
}

type PaymentResp struct {
	Resp   *http.Response
	Status int `json:"status"`
	Data   struct {
		Invoice struct {
			ID                        int         `json:"id"`
			Uniqid                    string      `json:"uniqid"`
			Total                     float64     `json:"total"`
			TotalDisplay              float64     `json:"total_display"`
			Currency                  string      `json:"currency"`
			ExchangeRate              int         `json:"exchange_rate"`
			CryptoExchangeRate        float64     `json:"crypto_exchange_rate"`
			ShopID                    int         `json:"shop_id"`
			Name                      string      `json:"name"`
			CustomerEmail             string      `json:"customer_email"`
			ProductID                 string      `json:"product_id"`
			ProductType               string      `json:"product_type"`
			ProductPrice              float64     `json:"product_price"`
			FileAttachmentUniqid      interface{} `json:"file_attachment_uniqid"`
			Gateway                   string      `json:"gateway"`
			PaypalEmail               interface{} `json:"paypal_email"`
			PaypalOrderID             interface{} `json:"paypal_order_id"`
			PaypalPayerEmail          interface{} `json:"paypal_payer_email"`
			SkrillEmail               interface{} `json:"skrill_email"`
			SkrillSid                 interface{} `json:"skrill_sid"`
			SkrillLink                interface{} `json:"skrill_link"`
			PerfectmoneyID            interface{} `json:"perfectmoney_id"`
			CryptoAddress             string      `json:"crypto_address"`
			CryptoAmount              float64     `json:"crypto_amount"`
			CryptoReceived            int         `json:"crypto_received"`
			CryptoURI                 string      `json:"crypto_uri"`
			CryptoConfirmationsNeeded int         `json:"crypto_confirmations_needed"`
			Country                   string      `json:"country"`
			Location                  string      `json:"location"`
			IP                        string      `json:"ip"`
			IsVpnOrProxy              bool        `json:"is_vpn_or_proxy"`
			UserAgent                 string      `json:"user_agent"`
			Quantity                  int         `json:"quantity"`
			CouponID                  interface{} `json:"coupon_id"`
			CustomFields              struct {
				DemoUsername string `json:"Demo Username"`
			} `json:"custom_fields"`
			DeveloperInvoice        bool          `json:"developer_invoice"`
			DeveloperTitle          string        `json:"developer_title"`
			DeveloperWebhook        string        `json:"developer_webhook"`
			DeveloperReturnURL      string        `json:"developer_return_url"`
			Status                  string        `json:"status"`
			Discount                int           `json:"discount"`
			FeeFixed                int           `json:"fee_fixed"`
			FeePercentage           int           `json:"fee_percentage"`
			DayValue                int           `json:"day_value"`
			Day                     string        `json:"day"`
			Month                   string        `json:"month"`
			Year                    int           `json:"year"`
			CreatedAt               int           `json:"created_at"`
			UpdatedAt               int           `json:"updated_at"`
			UpdatedBy               int           `json:"updated_by"`
			Serials                 []interface{} `json:"serials"`
			File                    interface{}   `json:"file"`
			Webhooks                []interface{} `json:"webhooks"`
			CryptoPayout            bool          `json:"crypto_payout"`
			CryptoPayoutTransaction interface{}   `json:"crypto_payout_transaction"`
			CryptoTransactions      []interface{} `json:"crypto_transactions"`
			Product                 struct {
				Title        string  `json:"title"`
				PriceDisplay float64 `json:"price_display"`
				Currency     string  `json:"currency"`
			} `json:"product"`
			TotalConversions struct {
				USD float64 `json:"USD"`
				EUR float64 `json:"EUR"`
				GBP float64 `json:"GBP"`
				JPY float64 `json:"JPY"`
				AUD float64 `json:"AUD"`
				CAD float64 `json:"CAD"`
				CHF float64 `json:"CHF"`
				CNY float64 `json:"CNY"`
				SEK float64 `json:"SEK"`
				NZD float64 `json:"NZD"`
				PLN float64 `json:"PLN"`
			} `json:"total_conversions"`
			Theme string `json:"theme"`
		} `json:"invoice"`
	} `json:"data"`
	Message interface{} `json:"message"`
	Log     interface{} `json:"log"`
	Error   interface{} `json:"error"`
	Env     string      `json:"env"`
}

func (c *Client) NewPayment(amount int, fiat, title, email, gateway, coupon string, req *http.Request) *Payment {
	return &Payment{
		PaymentReq: &PaymentReq{
			Title:         "Sellix Payment",
			Gateway:       gateway,
			Value:         float64(amount),
			Currency:      fiat,
			CouponCode:    coupon,
			Confirmations: 2,
			Webhook:       "https://test.twilight.lol/api/payments/webhook",
			FraudShield: func() struct {
				IP           string "json:\"ip\""
				UserAgent    string "json:\"user_agent\""
				UserLanguage string "json:\"user_language\""
			} {
				return struct {
					IP           string "json:\"ip\""
					UserAgent    string "json:\"user_agent\""
					UserLanguage string "json:\"user_language\""
				}{
					IP:           KeyByRealIP(req),
					UserAgent:    req.UserAgent(),
					UserLanguage: strings.Split(req.Header.Get("Accept-Language"), ",")[0],
				}

			}(),
			Email:      email,
			WhiteLabel: true,
		},
	}
}

func (c *Client) CreatePayment(payment *Payment) (*PaymentResp, error) {
	body, err := json.Marshal(payment.PaymentReq)
	if err != nil {
		return nil, err
	}
	fmt.Println(string(body))
	req, err := c.CreateRequest("POST", string(body), Payments)
	if err != nil {
		return nil, err
	}
	resp, err := c.client.Do(req)
	if err != nil {
		return nil, err
	}
	var pResp *PaymentResp
	json.NewDecoder(resp.Body).Decode(&pResp)
	pResp.Resp = resp
	fmt.Println(pResp)
	if pResp.Status != 200 {
		if reflect.ValueOf(pResp.Error).Kind() != reflect.String {
			if reflect.ValueOf(pResp.Error).IsNil() {
				return nil, errors.New("unknown error occured")
			}
		} else {
			return nil, errors.New(pResp.Error.(string))
		}
	}
	return pResp, nil
}

func KeyByRealIP(r *http.Request) string {
	var ip string

	if tcip := r.Header.Get("True-Client-IP"); tcip != "" {
		ip = tcip
	} else if xrip := r.Header.Get("X-Real-IP"); xrip != "" {
		ip = xrip
	} else if xff := r.Header.Get("X-Forwarded-For"); xff != "" {
		i := strings.Index(xff, ", ")
		if i == -1 {
			i = len(xff)
		}
		ip = xff[:i]
	} else if ccip := r.Header.Get("CF-Connecting-IP"); ccip != "" {
		ip = ccip
	} else {
		var err error
		ip, _, err = net.SplitHostPort(r.RemoteAddr)
		if err != nil {
			ip = r.RemoteAddr
		}
	}

	return ip
}
