ifeq ($(VERSION),)
VERSION := $(shell git tag -l --sort=-version:refname | head -n 1 | cut -c 2-)
endif

LDFLAGS := "-X github.com/borud/tun/pkg/global.Version=$(VERSION)"

all: lint vet test build

build: tun

tun:
	@cd cmd/$@ && go build  -ldflags=$(LDFLAGS) -o ../../bin/$@

release: test vet
	@cd cmd/tun && GOOS=linux GOARCH=amd64 go build -ldflags=$(LDFLAGS) -o ../../bin/tun
	@cd bin && zip tun.amd64-linux.zip tun && rm tun

	@cd cmd/tun && GOOS=darwin GOARCH=amd64 go build -ldflags=$(LDFLAGS) -o ../../bin/tun
	@cd bin && zip tun.amd64-macos.zip tun && rm tun
	
	@cd cmd/tun && GOOS=darwin GOARCH=arm64 go build -ldflags=$(LDFLAGS) -o ../../bin/tun
	@cd bin && zip tun.arm64-macos.zip tun && rm tun

	@cd cmd/tun && GOOS=windows GOARCH=amd64 go build -ldflags=$(LDFLAGS) -o ../../bin/tun.exe
	@cd bin && zip tun.amd64-win.zip tun.exe && rm tun.exe

	@cd cmd/tun && GOOS=linux GOARCH=arm GOARM=5 go build -ldflags=$(LDFLAGS) -o ../../bin/tun
	@cd bin && zip tun.arm5-rpi-linux.zip tun && rm tun

test:
	@go test ./...

vet:
	@go vet ./...

lint:
	@revive ./...

clean:
	@rm -rf bin
