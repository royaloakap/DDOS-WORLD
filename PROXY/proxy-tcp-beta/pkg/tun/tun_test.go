package tun

import (
	"testing"

	"github.com/stretchr/testify/require"
)

func TestParseLink(t *testing.T) {
	res, err := parseLink("user@host.example.com:22")
	require.NoError(t, err)
	require.Equal(t, link{
		username: "user",
		host:     "host.example.com",
		port:     22,
	}, res)
}
