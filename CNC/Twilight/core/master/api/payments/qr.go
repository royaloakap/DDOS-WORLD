package paymentsapi

import (
	"encoding/base64"

	"rsc.io/qr"
)

type generateBtcQRParam struct {
	Coin    string
	Address string
	Amount  string
	Label   string
	Message string
}

// GenerateBtcQR is Generate QR code based on generateBtcQRParam
func GenerateBtcQR(params *generateBtcQRParam) (string, error) {

	uri := params.Coin + ":" + params.Address + "?amount=" + params.Amount

	qrCode, err := qr.Encode(uri, qr.H)
	if err != nil {
		return "", err
	}

	b64 := base64.StdEncoding.EncodeToString(qrCode.PNG())

	return b64, nil
}
