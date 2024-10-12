const { Worker } = require('worker_threads')
const log = require('./logger')

const LEADING_ZEROES = 4

module.exports = {
    CreateThread: function (filename) {
        const worker = new Worker(filename, { env: { LEADING_ZEROES } })

        worker.on('error', function (error) {
            log.error(error)
        })

        worker.on('exit', function (code) {
            log.success(`Thread finished with code ${code}.`)
        })

        log.info(`Thread initialized!`)
    }
};