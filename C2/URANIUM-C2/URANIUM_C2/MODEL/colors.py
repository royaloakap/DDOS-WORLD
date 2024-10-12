def color_char(color,char='\x1b',mode='fg'):
    c2 = char
    if mode == 'fg':
     c2 += '[38;5;'
    else:
     c2 += '[48;5;'
    c2 += f'{int(color)}m'
    return c2