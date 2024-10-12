from urllib.parse import urlparse
import random,string,time
from typing import List, Union

class HumanBytes:
    METRIC_LABELS: List[str] = ["B", "kB", "MB", "GB", "TB", "PB", "EB", "ZB", "YB","RB","QB"]
    BINARY_LABELS: List[str] = ["B", "KiB", "MiB", "GiB", "TiB", "PiB", "EiB", "ZiB", "YiB"]
    PRECISION_OFFSETS: List[float] = [0.5, 0.05, 0.005, 0.0005, 0.00005]
    PRECISION_FORMATS: List[str] = ["{}{:.0f} {}", "{}{:.1f} {}", "{}{:.2f} {}", "{}{:.3f} {}", "{}{:.4f} {}", "{}{:.5f} {}"]
    @staticmethod
    def format(num: Union[int, float], metric: bool=False, precision: int=1) -> str:
        assert isinstance(num, (int, float))
        assert isinstance(metric, bool)
        assert isinstance(precision, int) and precision >= 0 and precision <= 3
        unit_labels = HumanBytes.METRIC_LABELS if metric else HumanBytes.BINARY_LABELS
        last_label = unit_labels[-1]
        unit_step = 1000 if metric else 1024
        unit_step_thresh = unit_step - HumanBytes.PRECISION_OFFSETS[precision]
        is_negative = num < 0
        if is_negative:
            num = abs(num)
        for unit in unit_labels:
            if num < unit_step_thresh:
                break
            if unit != last_label:
                num /= unit_step
        return HumanBytes.PRECISION_FORMATS[precision].format("-" if is_negative else "", num, unit)

def gen_ips():
    return f'{random.randint(0,255)}.{random.randint(0,255)}.{random.randint(0,255)}.{random.randint(0,255)}'

def get_target(url2):
    url = url2.rstrip()
    target = {}
    parsed_url = urlparse(url)
    target['uri'] = parsed_url.path or '/'
    target['host'] = parsed_url.netloc
    target['scheme'] = parsed_url.scheme
    target['port'] = parsed_url.port or ("443" if target['scheme'] == "https" else "80")
    target['normal'] = url2
    return target

def gen_ips():
    return f'{random.randint(0,255)}.{random.randint(0,255)}.{random.randint(0,255)}.{random.randint(0,255)}'

def gen_id():
    letter = 'abcdefghijklmnopqrstuvwxyz0123456789'
    id_8 = ''
    for _ in range(8):
     id_8 += random.choice((letter))
    id_4v1 = ''
    for _ in range(4):
     id_4v1 += random.choice((letter))
    id_4v2 = ''
    for _ in range(4):
     id_4v2 += random.choice((letter))
    id_4v3 = ''
    for _ in range(4):
     id_4v3 += random.choice((letter))
    id_12 = ''
    for _ in range(12):
     id_12 += random.choice((letter))
    return f'{id_8}-{id_4v1}-{id_4v2}-{id_4v3}-{id_12}'

def generate_url_path(num):
    data = "".join(random.sample(string.printable, int(num)))
    return data

def random_useragent():
 USER = ['Cloudflare-SSLDetector','Mozilla/5.0 (compatible;Cloudflare-Healthchecks/1.0; https://www.cloudflare.com/; healthcheck-id: e645d1e65d3b18b4)','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_13_6) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/67.0.3396.99 Safari/537.36 Cloudf,lareDiagnostics/1.0','Cloudflare Custom Hostname Verification','Mozilla/5.0 (compatible; Cloudflare-AMP/3.0; +https://amp.cloudflare.com/doc/fetcher.html) AppleWebKit/574.34','Mozilla/5.0 (Linux; Android 6.0.1; Nexus 5X Build/MMB29P) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/41.0.2272.96 Mobile Safari/537.36 (compatible; Cloudflare-AMPHTML)','Mozilla/5.0 (compatible; Cloudflare-AMP/1.0; +https://amp.cloudflare.com/doc/fetcher.html) AppleWebKit/534.34','Mozilla/5.0 (compatible; CloudFlare-AlwaysOnline/1.0;+http://www.cloudflare.com/always-online)','Mozilla/5.0 (compatible; CloudFlare-AlwaysOnline/1.0; +http://www.cloudflare.com/always-online) AppleWebKit/534.34','Mozilla/5.0 (compatible; CloudFlare-AlwaysOnline/1.0; +http://www.cloudflare.com/always-online)','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_10_1) AppleWebKit/600.2.5 (KHTML, like Gecko) Version/8.0.2 Safari/600.2.5 (Amazonbot/0.1)','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_10_1) AppleWebKit/600.2.5 (KHTML, like Gecko) Version/8.0.2 Safari/600.2.5 (Amazonbot/0.1; +https://developer.amazon.com/support/amazonbot)','Mozilla/5.0 AppleWebKit/537.36 (KHTML, like Gecko; compatible; bingbot/2.0; +http://www.bing.com/bingbot.htm) Chrome/W.X.Y.Z Safari/537.36','Mozilla/5.0 (Linux; Android 6.0.1; Nexus 5X Build/MMB29P) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/W.X.Y.Z Mobile Safari/537.36 (compatible; bingbot/2.0; +http://www.bing.com/bingbot.htm)','Mozilla/5.0 (Linux; Android 6.0.1; Nexus 5X Build/MMB29P) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/104.0.5112.101 Mobile Safari/537.36 (compatible; Googlebot/2.1; +http://www.google.com/bot.html)','Mozilla/5.0 (Linux; Android 6.0.1; Nexus 5X Build/MMB29P) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/94.0.4606.81 Mobile Safari/537.36 (compatible; Googlebot/2.1; +http://www.google.com/bot.html)','Mozilla/5.0 (Linux; Android 6.0.1; Nexus 5X Build/MMB29P) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/94.0.4606.71 Mobile Safari/537.36 (compatible; Googlebot/2.1; +http://www.google.com/bot.html)','Mozilla/5.0 (Linux; Android 6.0.1; Nexus 5X Build/MMB29P) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/94.0.4606.61 Mobile Safari/537.36 (compatible; Googlebot/2.1; +http://www.google.com/bot.html)','Mozilla/5.0 (Linux; Android 6.0.1; Nexus 5X Build/MMB29P) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/94.0.4606.54 Mobile Safari/537.36 (compatible; Googlebot/2.1; +http://www.google.com/bot.html)','Mozilla/5.0 (Linux; Android 6.0.1; Nexus 5X Build/MMB29P) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/93.0.4577.89 Mobile Safari/537.36 (compatible; Google-AMPHTML)','Mozilla/5.0 (Linux; Android 6.0.1; Nexus 5X Build/MMB29P) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/93.0.4577.83 Mobile Safari/537.36 (compatible; Googlebot/2.1; +http://www.google.com/bot.html)','Mozilla/5.0 (Linux; Android 6.0.1; Nexus 5X Build/MMB29P) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/93.0.4577.82 Mobile Safari/537.36 (compatible; Googlebot/2.1; +http://www.google.com/bot.html)','Mozilla/5.0 (Linux; Android 6.0.1; Nexus 5X Build/MMB29P) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/93.0.4577.79 Mobile Safari/537.36 (compatible; Googlebot/2.1; +http://www.google.com/bot.html)','Mozilla/5.0 (Linux; Android 6.0.1; Nexus 5X Build/MMB29P) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/92.0.4515.119 Mobile Safari/537.36 (compatible; Googlebot/2.1; +http://www.google.com/bot.html)','Mozilla/5.0 (Linux; Android 6.0.1; Nexus 5X Build/MMB29P) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/92.0.4515.119 Mobile Safari/537.36 (compatible; Google-AMPHTML)','Mozilla/5.0 (Linux; Android 6.0.1; Nexus 5X Build/MMB29P) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/92.0.4500.0 Mobile Safari/537.36 (compatible; Googlebot/2.1; +http://www.google.com/bot.html)','Mozilla/5.0 (Linux; Android 6.0.1; Nexus 5X Build/MMB29P) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.90 Mobile Safari/537.36 (compatible; Googlebot/2.1; +http://www.google.com/bot.html),gzip(gfe)','Mozilla/5.0 (Linux; Android 6.0.1; Nexus 5X Build/MMB29P) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.90 Mobile Safari/537.36 (compatible; Googlebot/2.1; +http://www.google.com/bot.html)','Mozilla/5.0 (Linux; Android 6.0.1; Nexus 5X Build/MMB29P) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.90 Mobile Safari/537.36 (compatible; Google-AMPHTML)','Mozilla/5.0 (Linux; Android 6.0.1; Nexus 5X Build/MMB29P) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.89 Mobile Safari/537.36 (compatible; Googlebot/2.1; +http://www.google.com/bot.html)','Mozilla/5.0 (Linux; Android 6.0.1; Nexus 5X Build/MMB29P) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.89 Mobile Safari/537.36 (compatible; Google-AMPHTML)','Mozilla/5.0 (Linux; Android 6.0.1; Nexus 5X Build/MMB29P) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.114 Mobile Safari/537.36 (compatible; Googlebot/2.1; +http://www.google.com/bot.html)','Mozilla/5.0 (Linux; Android 6.0.1; Nexus 5X Build/MMB29P) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4470.0 Mobile Safari/537.36 (compatible; Googlebot/2.1; +http://www.google.com/bot.html)','Mozilla/5.0 (compatible; Googlebot/2.1; +http://www.google.com/bot.html)','Googlebot/2.1 (+http://www.google.com/bot.html)','Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/110.0.5481.177 Safari/537.36 DatadogSynthetics','DatadogSynthetics','Datadog/Synthetics']
 return random.choice((USER))