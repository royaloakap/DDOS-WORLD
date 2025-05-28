

./Royal-Ascii-Art gif -f pic/sharingan.gif -W 93 -H 29 -d 200 -C 10 --seq Royal --loop 4 -m bgm/fight.mp3 > out.txt : The most realistic for a gif to ANSI

./Royal-Ascii-Art completion Usage : Generates an auto-completion script for your shell.

./Royal-Ascii-Art gif [options] : Plays GIFs in your terminal by converting them to ASCII or ANSI art.

./Royal-Ascii-Art image [options] : Converts an image to ASCII or ANSI art and displays it in the terminal.

./Royal-Ascii-Art help : ./Royal-Ascii-Art help

Image options
-a, --ascii : Generate ASCII or ANSI art.
-b, --blockMode : Use "block" mode for more detailed rendering.
-C, --contrast float : Adjust the contrast of the image (-100 to 100).
-f, --filename string : Specify the input image file (default is demo.gif).
-H, --height int : Set the resized height (default is 100).
-s, --seq string : Set the ASCII characters used to create the image (default is "01").
-S, --sigma float : Apply sharpening to improve details.
-W, --width int : Set the resized width (default is 100)

Generate an ANSI image with adjusted contrast: ./Royal-Ascii-Art image -f pic/messi.png -W 150 -H 60 -C 20 -S 10 -a > out.txt

Generate an image with a custom text sequence: ./Royal-Ascii-Art image -f pic/messi.png -W 150 -H 60 -s ROYAL > out.txt

Subcommand: gif
Reads a GIF in the terminal and converts it to animated ASCII or ANSI art.

Options for gif
-a, --ascii : Generates an ASCII or ANSI rendering.
-b, --blockMode : Uses "block" mode for detailed rendering.
-C, --contrast float : Adjusts the contrast of the GIF (-100 to 100).
-d, --duration int : Sets the duration of each frame (in ms, default 200).
-f, --filename string : Specifies the input GIF file (default demo.gif).
-H, --height int : Sets the resized height (default 100).
-L, --loop int : Sets the number of loops (default 1).
-m, --music string : Plays an audio file in the background.
-s, --seq string : Sets the ASCII characters used (default "01").
-S, --sigma float : Applies sharpening to improve details.
-W, --width int : Sets the resized width (default 100)

Play a GIF with smooth animation and audio: ./Royal-Ascii-Art gif -f pic/sharingan.gif -W 250 -H 60 -d 100 -a -m bgm/NadaNaruto.mp3 -C 30 -S 30 --loop 10 > out.txt

Play a GIF with block mode and custom text sequence: ./Royal-Ascii-Art gif -f pic/brother_fight.gif -W 150 -H 35 -d 200 -b -C 10 --seq BROTHER --loop 4 -m bgm/fight.mp3 > out.txt