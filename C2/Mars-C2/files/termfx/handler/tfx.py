import re, os
from files.termfx.handler.fading import parse_banner
from colored import fg, attr
class Reader():
    def __init__(self):
        self.variables = {}
        self.functions = {}
        self.anchors = { "start": "<<", "end": ">>" }
        self.target = None 
        self.user = None
        self.cons = None
        self.expiry = None
        self.maxtime = None
        self.nl = "\n"
        self.ip = None
        self.register_function("color", self._colored)
        self.register_function("fade", self._tfx_fade)
    def _tfx_fade(self, start:str, end:str, text:str, ignore:bool=False, text_color:str="\033[0m") -> str:
        start = self._str_to_tuple(start)
        end = self._str_to_tuple(end)
        return self._fade(start, end, text, ignore, text_color)
    def _colored(self, hex: str):
        try: return fg(hex)
        except Exception: return attr(hex)
    def _fade(self, start:tuple, end:tuple, text:str, ignore_alpha:bool=False, text_color:str="\033[0m") -> str:
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
                    r += changer; g += changeg; b += changeb 
                    continue
            result += "\x1b[40;38;2;%s;%s;%sm%s\033[0m" % (r, g, b, letter) 
            r += changer; g += changeg; b += changeb 
        return result
    def _str_to_tuple(self, string:str) -> tuple:
        x = string.split(',' if ',' in string else '/')
        return (x[0], x[1], x[2])
    def unregister_variable(self, name:str) -> None:
        if name in self.variables.keys():
            del self.variables[name]
        else:
            raise Exception(f"A variable with the name {name} does not exist.")
    def register_variable(self, name:str, value:str) -> None:
        if name in self.variables.keys():
            raise Exception(f"A variable with the name {name} already exists.")
        self.variables[name] = value
    def register_function(self, name:str, func:any):
        if name in self.functions.keys():
            raise Exception(f"A function with the name {name} already exists.")
        self.functions[name] = func
    def register_dict(self, data:dict) -> None:
        for name, value in data.items():
            self.register_variable(name, value)
    def unregister_dict(self, data:dict) -> None:
        for name, value in data.items():
            self.unregister_variable(name, value)
    def stripper(self, string:str) -> str:
        for x in ['"', "'", '''"""''', """'''"""]:
            string=string.replace(x, "")
        return string
    def execute_realtime(self, username:str, file:str, func:any) -> None:
        path = (f'data/assets/{file}' + ('.tfx' if not file.endswith('.tfx') else '')) if not 'data/assets/commands' in file else file
        if not os.path.isfile(path): return 
        with open(path, encoding="utf-8") as f:
            for line in f.read().split("\n"):
                try: func(self.execute(line))
                except: pass
    def execute(self, string:str) -> str:
        output = string
        for line in re.findall(fr"(\<\<(.*?)\>\>)", string):
            value = line[1]
            if value.startswith("$"): 
                name = self.variables.get(value.replace("$",""))
                if name is None: 
                    continue
                output = output.replace(f"<<{value}>>", str(name))
            elif "(" in line[1] and ")" in line[1]: 
                arguments = value.split("(")[1].split(")")[0]
                arglist = arguments.split(",") if len(arguments.split(",")) > 1 else [arguments]
                arguments = [int(x) if x.isdigit() else float(x) if re.match(r"^-?\d+(?:\.\d+)$", x) else self.stripper(x) for x in arglist]
                getfunc = self.functions.get(value.split("(")[0])
                if getfunc is None: 
                    continue
                if arglist[0] == "": func_output = getfunc()
                else: func_output = getfunc(*arguments)
                output = output.replace(line[0], "" if func_output is None else str(func_output))
        output = parse_banner(output, self.target, self.user, self.cons, self.expiry, self.ip, self.nl)
        return output
        