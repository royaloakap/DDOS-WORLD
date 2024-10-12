package utils

import (
    "encoding/json"
    "net/http"
)

type GeoIPDetails struct {
    City     string `json:"city"`
    Country  string `json:"country"`
    Hostname string `json:"isp"`
}

func GetIPDetails(ip string) (*GeoIPDetails, error) {
    // Make a GET request to the IP geolocation API
    resp, err := http.Get("http://ip-api.com/json/" + ip)
    if err != nil {
        return nil, err
    }
    defer resp.Body.Close()

    // Decode the JSON response
    var details GeoIPDetails
    err = json.NewDecoder(resp.Body).Decode(&details)
    if err != nil {
        return nil, err
    }

    return &details, nil
}