import time
from PIL import Image, ImageSequence
import files.networking.sender as client
ESC = "\u001b"
CSI = "["
SGR_END = "m"
CHA_END = "G"
did=f"{ESC}[?25h{ESC}[?0c"
CUP_END = "H"
ED_END = "J"
RESET_CURSOR = ESC + CSI + "1;1" + CUP_END
HIDE_CURSOR = ESC + CSI + "?25l"
RESET_DISPLAY = ESC + CSI + "0" + SGR_END + ESC + CSI + "2" + ED_END
class args:
    height=24
    width=80
def get_rgb_escape(r: float, g: float, b: float) -> str: return "\u001b[48;2;{};{};{}m".format(r, g, b) + "\u001b[38;2;{};{};{}m".format(r, g, b)
def get_rgb_escape_ascii(r: float, g: float, b: float) -> str: return "\u001b[38;2;{};{};{}m".format(r, g, b)
def brightness(r: float, g: float, b: float) -> float: return 0.2126 * r + 0.7152 * g + 0.0722 * b
def image_to_text(image: Image, palette=None) -> str:
    if palette is None: palette = {64: ".",128: "▓",192: "▒",256: "░",}
    text = ""
    for y in range(image.size[1]):
        for x in range(image.size[0]):
            escape = get_rgb_escape(*image.getpixel((x, y)))
            character = next(palette[k] for k in palette.keys() if brightness(*image.getpixel((x, y))) < k)
            text += escape + character
        text += ESC + CSI + "0" + SGR_END + "\r\n"
    return text
def image_to_text_dots(image: Image, palette=None) -> str:
    if palette is None: palette = {64: "▣",128: '▥',192: "▤",256: '▦',}
    text = ""
    for y in range(image.size[1]):
        for x in range(image.size[0]):
            escape = get_rgb_escape_ascii(*image.getpixel((x, y)))
            character = next(palette[k] for k in palette.keys() if brightness(*image.getpixel((x, y))) < k)
            text += escape+character
        text += ESC + CSI + "0" + SGR_END + "\r\n"
    return text
def image_to_text_ascii(image: Image, palette=None) -> str:
    if palette is None: palette = {64: ",",128: ".",192: ";",256: ":",}
    text = ""
    for y in range(image.size[1]):
        for x in range(image.size[0]):
            escape = get_rgb_escape_ascii(*image.getpixel((x, y)))
            character = next(palette[k] for k in palette.keys() if brightness(*image.getpixel((x, y))) < k)
            text += escape+character
        text += ESC + CSI + "0" + SGR_END + "\r\n"
    return text
def giftoolv3(gif,sock):
        image = Image.open(gif)
        frames = [frame.copy().convert("RGB") for frame in ImageSequence.Iterator(image)]
        frames = [frame.resize((frame.size[0] if args.width is None else args.width, frame.size[1] if args.height is None else args.height)) for frame in frames]
        text = HIDE_CURSOR + "\n"
        looped=0
        for index, frame in enumerate(frames):
            image_text = image_to_text(frame)
            text += RESET_CURSOR + image_text
        text += RESET_DISPLAY + did
        for i in text.splitlines():
            #client.send(sock, "\033[2J\033[1;1H",False)
            client.send(sock,i)
            time.sleep(0.0001)
        client.send(sock, "\033[2J\033[1;1H", False)
def giftool_dots(gif,sock):
        image = Image.open(gif)
        frames = [frame.copy().convert("RGB") for frame in ImageSequence.Iterator(image)]
        frames = [frame.resize((frame.size[0] if args.width is None else args.width, frame.size[1] if args.height is None else args.height)) for frame in frames]
        text = HIDE_CURSOR + "\n"
        looped=0
        for index, frame in enumerate(frames):
            image_text = image_to_text_dots(frame)
            text += RESET_CURSOR + image_text
        text += RESET_DISPLAY + did
        for i in text.splitlines():
            #client.send(sock, "\033[2J\033[1;1H",False)
            client.send(sock,i)
            time.sleep(0.0001)
        client.send(sock, "\033[2J\033[1;1H", False)
def giftool_ascii(gif,sock):
        image = Image.open(gif)
        frames = [frame.copy().convert("RGB") for frame in ImageSequence.Iterator(image)]
        frames = [frame.resize((frame.size[0] if args.width is None else args.width, frame.size[1] if args.height is None else args.height)) for frame in frames]
        text = HIDE_CURSOR + "\n"
        looped=0
        for index, frame in enumerate(frames):
            image_text = image_to_text_ascii(frame)
            text += RESET_CURSOR + image_text
        text += RESET_DISPLAY + did
        for i in text.splitlines():
            #client.send(sock, "\033[2J\033[1;1H",False)
            client.send(sock,i)
            time.sleep(0.0001)
        client.send(sock, "\033[2J\033[1;1H", False)