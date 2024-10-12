package paymentsapi

import (
	"api/core/database"
	"api/core/models/sellix"
	"api/core/models/server"
	"bytes"
	"crypto/hmac"
	"crypto/sha512"
	"encoding/json"
	"fmt"
	"io"
	"log"
	"net/http"
)

type WebhookBody struct {
	Event string `json:"event"`
	Data  struct {
		ID                         int           `json:"id"`
		Uniqid                     string        `json:"uniqid"`
		RecurringBillingID         interface{}   `json:"recurring_billing_id"`
		PayoutConfiguration        interface{}   `json:"payout_configuration"`
		Type                       string        `json:"type"`
		Subtype                    interface{}   `json:"subtype"`
		Origin                     interface{}   `json:"origin"`
		Total                      int           `json:"total"`
		TotalDisplay               int           `json:"total_display"`
		ProductVariants            interface{}   `json:"product_variants"`
		ExchangeRate               int           `json:"exchange_rate"`
		CryptoExchangeRate         float64       `json:"crypto_exchange_rate"`
		Currency                   string        `json:"currency"`
		ShopID                     int           `json:"shop_id"`
		ShopImageName              interface{}   `json:"shop_image_name"`
		ShopImageStorage           interface{}   `json:"shop_image_storage"`
		ShopCloudflareImageID      interface{}   `json:"shop_cloudflare_image_id"`
		Name                       interface{}   `json:"name"`
		CustomerEmail              string        `json:"customer_email"`
		CustomerID                 interface{}   `json:"customer_id"`
		AffiliateRevenueCustomerID interface{}   `json:"affiliate_revenue_customer_id"`
		PaypalEmailDelivery        bool          `json:"paypal_email_delivery"`
		ProductID                  string        `json:"product_id"`
		ProductTitle               string        `json:"product_title"`
		ProductType                string        `json:"product_type"`
		SubscriptionID             interface{}   `json:"subscription_id"`
		SubscriptionTime           interface{}   `json:"subscription_time"`
		Gateway                    interface{}   `json:"gateway"`
		Blockchain                 interface{}   `json:"blockchain"`
		PaypalApm                  interface{}   `json:"paypal_apm"`
		StripeApm                  interface{}   `json:"stripe_apm"`
		PaypalEmail                interface{}   `json:"paypal_email"`
		PaypalOrderID              interface{}   `json:"paypal_order_id"`
		PaypalPayerEmail           interface{}   `json:"paypal_payer_email"`
		PaypalFee                  int           `json:"paypal_fee"`
		PaypalSubscriptionID       interface{}   `json:"paypal_subscription_id"`
		PaypalSubscriptionLink     interface{}   `json:"paypal_subscription_link"`
		LexOrderID                 interface{}   `json:"lex_order_id"`
		LexPaymentMethod           interface{}   `json:"lex_payment_method"`
		PaydashPaymentID           interface{}   `json:"paydash_paymentID"`
		VirtualPaymentsID          interface{}   `json:"virtual_payments_id"`
		StripeClientSecret         interface{}   `json:"stripe_client_secret"`
		StripePriceID              interface{}   `json:"stripe_price_id"`
		SkrillEmail                interface{}   `json:"skrill_email"`
		SkrillSid                  interface{}   `json:"skrill_sid"`
		SkrillLink                 interface{}   `json:"skrill_link"`
		PerfectmoneyID             interface{}   `json:"perfectmoney_id"`
		BinanceInvoiceID           interface{}   `json:"binance_invoice_id"`
		BinanceQrcode              interface{}   `json:"binance_qrcode"`
		BinanceCheckoutURL         interface{}   `json:"binance_checkout_url"`
		CryptoAddress              interface{}   `json:"crypto_address"`
		CryptoAmount               int           `json:"crypto_amount"`
		CryptoReceived             float64       `json:"crypto_received"`
		CryptoURI                  interface{}   `json:"crypto_uri"`
		CryptoConfirmationsNeeded  int           `json:"crypto_confirmations_needed"`
		CryptoScheduledPayout      bool          `json:"crypto_scheduled_payout"`
		CryptoPayout               int           `json:"crypto_payout"`
		FeeBilled                  bool          `json:"fee_billed"`
		BillInfo                   interface{}   `json:"bill_info"`
		CashappQrcode              interface{}   `json:"cashapp_qrcode"`
		CashappNote                interface{}   `json:"cashapp_note"`
		CashappCashtag             interface{}   `json:"cashapp_cashtag"`
		Country                    interface{}   `json:"country"`
		Location                   string        `json:"location"`
		IP                         interface{}   `json:"ip"`
		IsVpnOrProxy               bool          `json:"is_vpn_or_proxy"`
		UserAgent                  interface{}   `json:"user_agent"`
		Quantity                   int           `json:"quantity"`
		CouponID                   interface{}   `json:"coupon_id"`
		CustomFields               interface{}   `json:"custom_fields"`
		DeveloperInvoice           bool          `json:"developer_invoice"`
		DeveloperTitle             interface{}   `json:"developer_title"`
		DeveloperWebhook           interface{}   `json:"developer_webhook"`
		DeveloperReturnURL         interface{}   `json:"developer_return_url"`
		Status                     string        `json:"status"`
		StatusDetails              interface{}   `json:"status_details"`
		VoidDetails                interface{}   `json:"void_details"`
		Discount                   int           `json:"discount"`
		FeePercentage              int           `json:"fee_percentage"`
		FeeBreakdown               interface{}   `json:"fee_breakdown"`
		DiscountBreakdown          interface{}   `json:"discount_breakdown"`
		DayValue                   int           `json:"day_value"`
		Day                        string        `json:"day"`
		Month                      string        `json:"month"`
		Year                       int           `json:"year"`
		ProductAddons              interface{}   `json:"product_addons"`
		BundleConfig               interface{}   `json:"bundle_config"`
		CreatedAt                  int           `json:"created_at"`
		UpdatedAt                  int           `json:"updated_at"`
		UpdatedBy                  int           `json:"updated_by"`
		ApprovedAddress            interface{}   `json:"approved_address"`
		Serials                    []interface{} `json:"serials"`
		LockedSerials              []interface{} `json:"locked_serials"`
		IPInfo                     interface{}   `json:"ip_info"`
		Webhooks                   []interface{} `json:"webhooks"`
		PaypalDispute              interface{}   `json:"paypal_dispute"`
		ProductDownloads           []interface{} `json:"product_downloads"`
		PaymentLinkID              interface{}   `json:"payment_link_id"`
		License                    bool          `json:"license"`
		StatusHistory              []struct {
			ID        int    `json:"id"`
			InvoiceID string `json:"invoice_id"`
			Status    string `json:"status"`
			Details   string `json:"details"`
			CreatedAt int    `json:"created_at"`
		} `json:"status_history"`
		AmlWallets         []interface{} `json:"aml_wallets"`
		CryptoTransactions []interface{} `json:"crypto_transactions"`
		Product            struct {
			Uniqid                  string      `json:"uniqid"`
			Title                   string      `json:"title"`
			RedirectLink            interface{} `json:"redirect_link"`
			Description             string      `json:"description"`
			PriceDisplay            int         `json:"price_display"`
			Currency                string      `json:"currency"`
			ImageName               interface{} `json:"image_name"`
			ImageStorage            interface{} `json:"image_storage"`
			PayWhatYouWant          int         `json:"pay_what_you_want"`
			AffiliateRevenuePercent int         `json:"affiliate_revenue_percent"`
			CloudflareImageID       interface{} `json:"cloudflare_image_id"`
			LabelSingular           interface{} `json:"label_singular"`
			LabelPlural             interface{} `json:"label_plural"`
			Feedback                struct {
				Total    int           `json:"total"`
				Positive int           `json:"positive"`
				Neutral  int           `json:"neutral"`
				Negative int           `json:"negative"`
				List     []interface{} `json:"list"`
			} `json:"feedback"`
			AverageScore              interface{}   `json:"average_score"`
			ID                        int           `json:"id"`
			ShopID                    int           `json:"shop_id"`
			Price                     int           `json:"price"`
			QuantityMin               int           `json:"quantity_min"`
			QuantityMax               int           `json:"quantity_max"`
			QuantityWarning           int           `json:"quantity_warning"`
			Gateways                  []interface{} `json:"gateways"`
			CryptoConfirmationsNeeded int           `json:"crypto_confirmations_needed"`
			MaxRiskLevel              int           `json:"max_risk_level"`
			BlockVpnProxies           bool          `json:"block_vpn_proxies"`
			Private                   bool          `json:"private"`
			Stock                     int           `json:"stock"`
			Unlisted                  bool          `json:"unlisted"`
			SortPriority              int           `json:"sort_priority"`
			CreatedAt                 int           `json:"created_at"`
			UpdatedAt                 int           `json:"updated_at"`
			UpdatedBy                 int           `json:"updated_by"`
		} `json:"product"`
		TotalConversions struct {
			CAD    int    `json:"CAD"`
			HKD    int    `json:"HKD"`
			ISK    int    `json:"ISK"`
			PHP    int    `json:"PHP"`
			DKK    int    `json:"DKK"`
			HUF    int    `json:"HUF"`
			CZK    int    `json:"CZK"`
			GBP    int    `json:"GBP"`
			RON    int    `json:"RON"`
			SEK    int    `json:"SEK"`
			IDR    int    `json:"IDR"`
			INR    int    `json:"INR"`
			BRL    int    `json:"BRL"`
			RUB    int    `json:"RUB"`
			HRK    int    `json:"HRK"`
			JPY    int    `json:"JPY"`
			THB    int    `json:"THB"`
			CHF    int    `json:"CHF"`
			EUR    int    `json:"EUR"`
			MYR    int    `json:"MYR"`
			BGN    int    `json:"BGN"`
			TRY    int    `json:"TRY"`
			CNY    int    `json:"CNY"`
			NOK    int    `json:"NOK"`
			NZD    int    `json:"NZD"`
			ZAR    int    `json:"ZAR"`
			USD    string `json:"USD"`
			MXN    int    `json:"MXN"`
			SGD    int    `json:"SGD"`
			AUD    int    `json:"AUD"`
			ILS    int    `json:"ILS"`
			KRW    int    `json:"KRW"`
			PLN    int    `json:"PLN"`
			Crypto struct {
				BTC        string `json:"BTC"`
				DOGE       string `json:"DOGE"`
				BNB        string `json:"BNB"`
				ETH        string `json:"ETH"`
				LTC        string `json:"LTC"`
				BCH        string `json:"BCH"`
				NANO       string `json:"NANO"`
				XMR        string `json:"XMR"`
				SOL        string `json:"SOL"`
				XRP        string `json:"XRP"`
				CRO        string `json:"CRO"`
				USDC       string `json:"USDC"`
				USDCNATIVE string `json:"USDC_NATIVE"`
				USDT       string `json:"USDT"`
				TRX        string `json:"TRX"`
				CCD        string `json:"CCD"`
				MATIC      string `json:"MATIC"`
				APE        string `json:"APE"`
				PEPE       string `json:"PEPE"`
				DAI        string `json:"DAI"`
				WETH       string `json:"WETH"`
				SHIB       string `json:"SHIB"`
			} `json:"crypto"`
		} `json:"total_conversions"`
		Theme      string      `json:"theme"`
		DarkMode   interface{} `json:"dark_mode"`
		CryptoMode interface{} `json:"crypto_mode"`
		Products   []struct {
			Uniqid                  string      `json:"uniqid"`
			Title                   string      `json:"title"`
			RedirectLink            interface{} `json:"redirect_link"`
			Description             string      `json:"description"`
			PriceDisplay            string      `json:"price_display"`
			Currency                string      `json:"currency"`
			ImageName               interface{} `json:"image_name"`
			ImageStorage            interface{} `json:"image_storage"`
			PayWhatYouWant          int         `json:"pay_what_you_want"`
			AffiliateRevenuePercent int         `json:"affiliate_revenue_percent"`
			CloudflareImageID       interface{} `json:"cloudflare_image_id"`
			LabelSingular           interface{} `json:"label_singular"`
			LabelPlural             interface{} `json:"label_plural"`
			Feedback                struct {
				Total    int           `json:"total"`
				Positive int           `json:"positive"`
				Neutral  int           `json:"neutral"`
				Negative int           `json:"negative"`
				List     []interface{} `json:"list"`
			} `json:"feedback"`
			AverageScore interface{} `json:"average_score"`
		} `json:"products"`
		GatewaysAvailable            []string      `json:"gateways_available"`
		ShopPaymentGatewaysFees      []interface{} `json:"shop_payment_gateways_fees"`
		ShopPaypalCreditCard         bool          `json:"shop_paypal_credit_card"`
		ShopForcePaypalEmailDelivery bool          `json:"shop_force_paypal_email_delivery"`
		ShopWalletconnectID          interface{}   `json:"shop_walletconnect_id"`
		RatesSnapshot                interface{}   `json:"rates_snapshot"`
		VoidTimes                    []struct {
			Gateways []string `json:"gateways"`
			Conf     struct {
				Void       int         `json:"void"`
				WaitPeriod interface{} `json:"wait_period"`
			} `json:"conf"`
		} `json:"void_times"`
	} `json:"data"`
}

var (
	Secret = "Wo0rSk55FAyujGUM416Pvxs9QhZU2g85"
	ips    = []string{"99.81.24.41", "162.158.38.60", "162.158.38.61"}
)

func init() {
	Route.NewSub(server.NewRoute("/webhook", func(w http.ResponseWriter, r *http.Request) {
		ip := sellix.KeyByRealIP(r)
		if ip != "99.81.24.41" {
			fmt.Println(r.RemoteAddr)
			w.WriteHeader(403)
			w.Write([]byte("Unathorized!"))
			return
		}
		if r.Method != "POST" {
			return
		}
		body, _ := io.ReadAll(r.Body)
		fmt.Println(string(body))
		signature := r.Header.Get("x-sellix-unescaped-signature")
		hmac := hmac.New(sha512.New, []byte(Secret))
		if !bytes.Equal(hmac.Sum(body), []byte(signature)) {
			log.Println("invalid hmac signature!")
		}
		var data *WebhookBody
		json.Unmarshal(body, &data)
		fmt.Println(data)
		switch data.Event {
		case "order:created":
			switch data.Data.Status {
			case "PARTIAL":
				sale, err := database.Container.GetSaleByUniq(data.Data.Uniqid)
				if err != nil {
					return
				}
				sale.Status = "partially_paid"
				sale.Recieved = data.Data.CryptoReceived
				user, err := database.Container.GetUserByID(sale.Parent)
				if err != nil {
					return
				}
				user.Balance += int(data.Data.CryptoReceived * data.Data.CryptoExchangeRate)
				database.Container.UpdateUser(user)
				database.Container.UpdateSale(sale)
			}
		case "order:paid":
			sale, err := database.Container.GetSaleByUniq(data.Data.Uniqid)
			if err != nil {
				return
			}
			sale.Status = "finished"
			sale.Recieved = data.Data.CryptoReceived
			user, err := database.Container.GetUserByID(sale.Parent)
			if err != nil {
				return
			}
			user.Balance += sale.Amount
			database.Container.UpdateUser(user)
			database.Container.UpdateSale(sale)
		case "order:partial":
			sale, err := database.Container.GetSaleByUniq(data.Data.Uniqid)
			if err != nil {
				return
			}
			sale.Status = "partially_paid"
			sale.Recieved = data.Data.CryptoReceived
			if err := database.Container.UpdateSale(sale); err != nil {
				log.Println(err)
			}
		case "order:cancelled":
			sale, err := database.Container.GetSaleByUniq(data.Data.Uniqid)
			if err != nil {
				return
			}
			sale.Status = "expired"
			sale.Recieved = data.Data.CryptoReceived
			database.Container.UpdateSale(sale)
		}
	}))
}
