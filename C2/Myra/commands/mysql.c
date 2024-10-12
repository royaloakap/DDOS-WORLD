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

#include "mysql.h"
#include "config.h"
#include "commands.h"

void start_database(MYSQL *conn)
{
	int index = 0;

	if(mysql_query(conn, "SELECT * from users"))
	{
		puts("Please start your mysql server.");
		exit(0);
	}
	MYSQL_RES *res = mysql_store_result(conn);

	int num_fields = mysql_num_fields(res);

	MYSQL_ROW row;

	while((row = mysql_fetch_row(res)))
	{
		strcpy(db[index].user, row[1]);
		strcpy(db[index].pass, row[2]);
		strcpy(db[index].type, row[3]);
		db[index].seed = rand() % 10000;
		epoll_get_plan(db[index].type, index);
		index++;
	}
}

char *escape_string(char *string)
{
	int i, index = 0, _reall = 0;

	char *return_str = calloc(strlen(string), sizeof(char));
	for(i = 0; i < strlen(string); i++)
	{
		return_str[index] = string[i];
		if(string[i] == '\\')
		{
			return_str[index + 1] = '\\';
			index++;
			_reall++;
			return_str = realloc(return_str, strlen(string) + _reall);
		}
		index++;
	}
	return return_str;
}

int check_blacklisted(MYSQL *conn, const char *buffer)
{
	int index = 0;

	if(mysql_query(conn, "SELECT * from blacklisted"))
	{
		puts("Please start your mysql server.");
		exit(0);
	}
	MYSQL_RES *res = mysql_store_result(conn);

	int num_fields = mysql_num_fields(res);

	MYSQL_ROW row;

	while((row = mysql_fetch_row(res)))
	{
		if(strstr(buffer, row[1]))
			return 1;
	}
	return 0;
}

int check_info(MYSQL *conn, char *username, char *password)
{
	int index = 0;

	if(mysql_query(conn, "SELECT * from users"))
	{
		puts("Please start your mysql server.");
		exit(0);
	}
	MYSQL_RES *res = mysql_store_result(conn);

	int num_fields = mysql_num_fields(res);

	MYSQL_ROW row;
	_encrypt(password);

	while((row = mysql_fetch_row(res)))
	{
		if(strcmp(username, row[1]) == 0 && strcmp(encstr, row[2]) == 0)
		{
			if(decide_expiry_date(row[4]))
				return 696969;
			return index;
		}
		index++;
	}
	return -1;
}

int get_user(MYSQL *conn, const int fd)
{
	char *buffer;

	if(mysql_query(conn, "SELECT * from users"))
	{
		puts("Please start your mysql server.");
		exit(0);
	}
	MYSQL_RES *res = mysql_store_result(conn);

	MYSQL_ROW row;
	int num_fields = mysql_num_fields(res);

	while((row = mysql_fetch_row(res)))
	{
		if(strcmp(user[fd].name, row[1]) == 0)
		{
			asprintf(&buffer, "Concurrents: %d\r\nUsername: %s\r\nPlan: %s\r\nCooldown: %d\r\nExpiry Date: %s\r\n",
				user[fd].concurrents, user[fd].name, row[3], user[fd].cooldown, row[4]);

			send(fd, buffer, strlen(buffer), MSG_NOSIGNAL);
			free(buffer);
			return;
		}
	}
	return -1;
}
