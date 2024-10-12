import colorama
import time
import re
import os
from files.termfx.gif.gif import giftool_ascii, giftool_dots, giftoolv3
def _str_to_tuple(string: str) -> tuple:
  x = string.split(',' if ',' in string else '/')
  return (x[0], x[1], x[2])
def _tfx_fade(start: str,
              end: str,
              text: str,
              ignore: bool = False,
              text_color: str = "\033[0m") -> str:
  start = _str_to_tuple(start)
  end = _str_to_tuple(end)
  return _fade(start, end, text, ignore, text_color)
def _fade(start: tuple,
          end: tuple,
          text: str,
          ignore_alpha: bool = False,
          text_color: str = "\033[0m") -> str:
  result = ""
  changer = int((int(end[0]) - int(start[0])) / len(text))
  changeg = int((int(end[1]) - int(start[1])) / len(text))
  changeb = int((int(end[2]) - int(start[2])) / len(text))
  r, g, b = int(start[0]), int(start[1]), int(start[2])
  for letter in text:
    if letter == "\n":
      pass
    if ignore_alpha:
      if letter.isalpha() or letter.isnumeric():
        result += text_color + letter
        r += changer
        g += changeg
        b += changeb
        continue
    result += "\x1b[40;38;2;%s;%s;%sm%s\033[0m" % (r, g, b, letter)
    r += changer
    g += changeg
    b += changeb
  return result
def parse_banner(banner, client, username="", cons="", expiry="", ip='', nl=""):
    tgx = {"username": username, "cons": cons, "expiry": expiry, "ip": ip, "nl": nl}
    banner = re.sub(r'<fade:(.*?)>(.*?)<fade:(.*?)>',lambda m: _tfx_fade(m.group(1), m.group(3), m.group(2)),banner)
    banner = re.sub(r'<ansigif>(.*?)</ansigif>',lambda m: giftoolv3(m.group(1), client),banner)
    banner = re.sub(r'<8bitgif>(.*?)</8bitgif>',lambda m: giftool_dots(m.group(1), client),banner)
    banner = re.sub(r'<asciigif>(.*?)</asciigif>',lambda m: giftool_ascii(m.group(1), client),banner)
    return banner
