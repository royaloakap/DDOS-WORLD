#pragma once

struct attack_t
{
	char name[30];
	char type[30];
	char data[100];
	char extradata[100];
	int len;
	int port;
}atk[MAX_CONNS];

void cnc_add_attack(char *name, char *_atk, int port, int len, char *data, char *extradata);
void cnc_add_attacks(void);
