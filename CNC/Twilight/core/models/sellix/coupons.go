package sellix

type Coupon struct {
	Code                        string   `json:"code"`
	DiscountValue               int      `json:"discount_value"`
	MaxUses                     int      `json:"max_uses"`
	ProductsBound               []string `json:"products_bound"`
	DiscountType                string   `json:"discount_type"`
	DiscountOrderType           string   `json:"discount_order_type"`
	DisabledWithVolumeDiscounts bool     `json:"disabled_with_volume_discounts"`
	AllRecurringBillInvoices    bool     `json:"all_recurring_bill_invoices"`
}
