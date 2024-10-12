package structs

type SellixPayload struct {
	Title        string      `json:"title"`
	Email        string      `json:"email"`
	Value        float64     `json:"value"`
	Currency     string      `json:"currency"`
	Quantity     int         `json:"quantity"`
	Whitelabel   bool        `json:"white_label"`
	CustomFields CustomField `json:"custom_fields"`
	Webhook      string      `json:"webhook"`
	ReturnURL    string      `json:"return_url"`
}

type CustomField struct {
	TelegramID int64  `json:"telegram_id"`
	Plan       string `json:"plan"`
}

type SellixResponse struct {
	Status int `json:"status"`
	Data   struct {
		URL    string `json:"url"`
		Uniqid string `json:"uniqid"`
	} `json:"data"`
	Message string `json:"message"`
	Log     any    `json:"log"`
	Error   any    `json:"error"`
	Env     string `json:"env"`
}

type SellixWebhook struct {
	Event string `json:"event"`
	Data  struct {
		ID                         int     `json:"id"`
		Uniqid                     string  `json:"uniqid"`
		RecurringBillingID         any     `json:"recurring_billing_id"`
		Type                       string  `json:"type"`
		Subtype                    any     `json:"subtype"`
		Total                      float64 `json:"total"`
		TotalDisplay               int     `json:"total_display"`
		ProductVariants            any     `json:"product_variants"`
		ExchangeRate               float64 `json:"exchange_rate"`
		CryptoExchangeRate         int     `json:"crypto_exchange_rate"`
		Currency                   string  `json:"currency"`
		ShopID                     int     `json:"shop_id"`
		ShopImageName              string  `json:"shop_image_name"`
		ShopImageStorage           string  `json:"shop_image_storage"`
		ShopCloudflareImageID      string  `json:"shop_cloudflare_image_id"`
		Name                       string  `json:"name"`
		CustomerEmail              string  `json:"customer_email"`
		AffiliateRevenueCustomerID any     `json:"affiliate_revenue_customer_id"`
		PaypalEmailDelivery        bool    `json:"paypal_email_delivery"`
		ProductID                  any     `json:"product_id"`
		ProductTitle               string  `json:"product_title"`
		ProductType                string  `json:"product_type"`
		SubscriptionID             any     `json:"subscription_id"`
		SubscriptionTime           any     `json:"subscription_time"`
		Gateway                    string  `json:"gateway"`
		Blockchain                 any     `json:"blockchain"`
		PaypalApm                  any     `json:"paypal_apm"`
		StripeApm                  any     `json:"stripe_apm"`
		PaypalEmail                any     `json:"paypal_email"`
		PaypalOrderID              any     `json:"paypal_order_id"`
		PaypalPayerEmail           any     `json:"paypal_payer_email"`
		PaypalFee                  int     `json:"paypal_fee"`
		PaypalSubscriptionID       any     `json:"paypal_subscription_id"`
		PaypalSubscriptionLink     any     `json:"paypal_subscription_link"`
		LexOrderID                 any     `json:"lex_order_id"`
		LexPaymentMethod           any     `json:"lex_payment_method"`
		PaydashPaymentID           any     `json:"paydash_paymentID"`
		VirtualPaymentsID          any     `json:"virtual_payments_id"`
		StripeClientSecret         string  `json:"stripe_client_secret"`
		StripePriceID              any     `json:"stripe_price_id"`
		SkrillEmail                any     `json:"skrill_email"`
		SkrillSid                  any     `json:"skrill_sid"`
		SkrillLink                 any     `json:"skrill_link"`
		PerfectmoneyID             any     `json:"perfectmoney_id"`
		BinanceInvoiceID           any     `json:"binance_invoice_id"`
		BinanceQrcode              any     `json:"binance_qrcode"`
		BinanceCheckoutURL         any     `json:"binance_checkout_url"`
		CryptoAddress              any     `json:"crypto_address"`
		CryptoAmount               int     `json:"crypto_amount"`
		CryptoReceived             int     `json:"crypto_received"`
		CryptoURI                  any     `json:"crypto_uri"`
		CryptoConfirmationsNeeded  int     `json:"crypto_confirmations_needed"`
		CryptoScheduledPayout      bool    `json:"crypto_scheduled_payout"`
		CryptoPayout               int     `json:"crypto_payout"`
		FeeBilled                  bool    `json:"fee_billed"`
		BillInfo                   any     `json:"bill_info"`
		CashappQrcode              any     `json:"cashapp_qrcode"`
		CashappNote                any     `json:"cashapp_note"`
		CashappCashtag             any     `json:"cashapp_cashtag"`
		Country                    string  `json:"country"`
		Location                   string  `json:"location"`
		IP                         any     `json:"ip"`
		IsVpnOrProxy               bool    `json:"is_vpn_or_proxy"`
		UserAgent                  any     `json:"user_agent"`
		Quantity                   int     `json:"quantity"`
		CouponID                   any     `json:"coupon_id"`
		CustomFields               struct {
			TelegramID int64  `json:"telegram_id"`
			Plan       string `json:"plan"`
		} `json:"custom_fields"`
		DeveloperInvoice   bool   `json:"developer_invoice"`
		DeveloperTitle     string `json:"developer_title"`
		DeveloperWebhook   any    `json:"developer_webhook"`
		DeveloperReturnURL string `json:"developer_return_url"`
		Status             string `json:"status"`
		StatusDetails      any    `json:"status_details"`
		VoidDetails        any    `json:"void_details"`
		Discount           int    `json:"discount"`
		FeePercentage      int    `json:"fee_percentage"`
		FeeBreakdown       string `json:"fee_breakdown"`
		DiscountBreakdown  struct {
			Log struct {
				Coupon struct {
					Total         float64 `json:"total"`
					Coupon        int     `json:"coupon"`
					TotalDisplay  int     `json:"total_display"`
					CouponDisplay int     `json:"coupon_display"`
				} `json:"coupon"`
				DevInvoice struct {
					Total        float64 `json:"total"`
					Currency     string  `json:"currency"`
					DevValue     float64 `json:"dev_value"`
					TotalDisplay int     `json:"total_display"`
				} `json:"dev_invoice"`
				BundleDiscount []any `json:"bundle_discount"`
				VolumeDiscount struct {
					Total                 float64 `json:"total"`
					TotalDisplay          int     `json:"total_display"`
					VolumeDiscount        int     `json:"volume_discount"`
					VolumeDiscountDisplay int     `json:"volume_discount_display"`
				} `json:"volume_discount"`
			} `json:"log"`
			Tax struct {
				Percentage string `json:"percentage"`
			} `json:"tax"`
			Addons []any `json:"addons"`
			Coupon []any `json:"coupon"`
			TaxLog struct {
				Vat                 string  `json:"vat"`
				Type                string  `json:"type"`
				VatTotal            int     `json:"vat_total"`
				TotalPreVat         float64 `json:"total_pre_vat"`
				TotalWithVat        float64 `json:"total_with_vat"`
				VatPercentage       string  `json:"vat_percentage"`
				TotalPreVatDisplay  int     `json:"total_pre_vat_display"`
				TotalWithVatDisplay int     `json:"total_with_vat_display"`
			} `json:"tax_log"`
			Currencies struct {
				Default string `json:"default"`
				Display string `json:"display"`
			} `json:"currencies"`
			GatewayFee      []any `json:"gateway_fee"`
			PriceDiscount   []any `json:"price_discount"`
			BundleDiscounts []any `json:"bundle_discounts"`
			VolumeDiscounts []any `json:"volume_discounts"`
		} `json:"discount_breakdown"`
		DayValue      int    `json:"day_value"`
		Day           string `json:"day"`
		Month         string `json:"month"`
		Year          int    `json:"year"`
		ProductAddons any    `json:"product_addons"`
		BundleConfig  any    `json:"bundle_config"`
		CreatedAt     int    `json:"created_at"`
		UpdatedAt     int    `json:"updated_at"`
		UpdatedBy     int    `json:"updated_by"`
		IPInfo        any    `json:"ip_info"`
		ServiceText   any    `json:"service_text"`
		Webhooks      []struct {
			Uniqid       string `json:"uniqid"`
			URL          string `json:"url"`
			Event        string `json:"event"`
			Retries      int    `json:"retries"`
			ResponseCode int    `json:"response_code"`
			CreatedAt    int    `json:"created_at"`
		} `json:"webhooks"`
		PaypalDispute    any   `json:"paypal_dispute"`
		ProductDownloads []any `json:"product_downloads"`
		StatusHistory    []struct {
			ID        int    `json:"id"`
			InvoiceID string `json:"invoice_id"`
			Status    string `json:"status"`
			Details   string `json:"details"`
			CreatedAt int    `json:"created_at"`
		} `json:"status_history"`
		CryptoTransactions   []any    `json:"crypto_transactions"`
		StripeUserID         string   `json:"stripe_user_id"`
		StripePublishableKey string   `json:"stripe_publishable_key"`
		Products             []any    `json:"products"`
		GatewaysAvailable    []string `json:"gateways_available"`
		CountryRegulations   string   `json:"country_regulations"`
		AvailableStripeApm   []struct {
			ID   string `json:"id"`
			Name string `json:"name"`
		} `json:"available_stripe_apm"`
		ShopPaymentGatewaysFees      []any  `json:"shop_payment_gateways_fees"`
		ShopPaypalCreditCard         bool   `json:"shop_paypal_credit_card"`
		ShopForcePaypalEmailDelivery bool   `json:"shop_force_paypal_email_delivery"`
		OriginalDeveloperReturnURL   string `json:"original_developer_return_url"`
	} `json:"data"`
}
