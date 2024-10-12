const chalk = require('chalk')

module.exports = {
    banner: function () {
        console.log(chalk.hex('#ffffff')(" "))
        console.log(chalk.hex('#ffffff')(" ______         _______ _______ _______"))
        console.log(chalk.hex('#ffffff')(" |_____] |      |_____| |______    |   "))
        console.log(chalk.hex('#ffffff')(" |_____] |_____ |     | ______|    |   "))
        console.log(chalk.hex('#ffffff')("                 a strongest multi-thread http flood."))
        console.log(chalk.hex('#ffffff')(" "))
    },
    args: function () {
        console.log(chalk.hex('#ffffff')(" "))
        console.log(chalk.hex('#ffffff')(" Parameters"))
        console.log(chalk.hex('#ffffff')(" -u,  --url <link>, target url."))
        console.log(chalk.hex('#ffffff')(" -m,  --method <GET, POST>,  attack method."))
        console.log(chalk.hex('#ffffff')(" -t,  --time <length>, attack time."))
        console.log(chalk.hex('#ffffff')(" -r,  --reqs <length>, req per proxy connection."))
        console.log(chalk.hex('#ffffff')(" -th, --threads <length>, threads per attack."))
        console.log(chalk.hex('#ffffff')(" "))
    },
    info: function (message) {
        console.log(` ${chalk.hex('#ffffff')("[ ")} ${chalk.hex('#0084ff')("INFO")} ${chalk.hex('#ffffff')(` ] - ${message}`)}`)
    },
    success: function (message) {
        console.log(` ${chalk.hex('#ffffff')("[ ")} ${chalk.hex('#00cf15')("SUCCESS")} ${chalk.hex('#ffffff')(` ] - ${message}`)}`)
    },
    error: function (message) {
        console.log(` ${chalk.hex('#ffffff')("[ ")} ${chalk.hex('#cf0000')("ERROR")} ${chalk.hex('#ffffff')(` ] - ${message}`)}`)
    },
    warn: function (message) {
        console.log(` ${chalk.hex('#ffffff')("[ ")} ${chalk.hex('#fff700')("WARNING")} ${chalk.hex('#ffffff')(` ] - ${message}`)}`)
    }
};