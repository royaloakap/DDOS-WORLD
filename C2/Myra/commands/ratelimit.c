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
#include <mysql.h>
#include <my_global.h>

time_t start;

#include "config.h"
#include "attacks.h"
#include "commands.h"
#include "mysql.h"
#include "ratelimit.h"

int ratelimit_init(const int fd)
{
	int i;


	if(time(0) >= start + BUCKET_RESET)
	{
		for(i = 0; i < MAX_CONNS; i++)
			ratelimit[i].count = 0;

		start = time(0);
	}

	ratelimit[fd].count++;

	if(ratelimit[fd].count > BUCKET_WARN)
	{
		send(fd, "You are getting ratelimited.\r\n", 
			strlen("You are getting ratelimited.\r\n"), MSG_NOSIGNAL);

		ratelimit[fd].warnings++;
		return 0;
	}

	return 1;
}

void ratelimit_close_connections(const int epfd)
{
	int i;


	for(i = 0; i < MAX_CONNS; i++)
	{
		if(user[i].login_count > 0)
		{
			if(time(0) >= user[i].login_count + MAX_LOGIN_TIME)
			{
				send(user[i].fd, "\r\nPlease login quicker.\r\n", 
					strlen("\r\nPlease login quicker.\r\n"), MSG_NOSIGNAL);

				if(time(0) >= user[i].login_count + MAX_LOGIN_TIME + 3)
				{
					struct epoll_event event;
					event.data.fd = user[i].fd;
					event.events = EPOLLIN;

					user[i].login_count = 0;
					epoll_ctl(epfd, EPOLL_CTL_DEL, user[i].fd, &event);
					close(user[i].fd);
				}
			}
		}
	}
}
