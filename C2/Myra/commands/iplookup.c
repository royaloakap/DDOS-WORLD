#include <sys/epoll.h>
#include <sys/socket.h>
#include <sys/select.h>
#include <string.h>
#include <stdio.h>
#include <stdlib.h>
#include <errno.h>
#include <unistd.h>
#include <fcntl.h>
#include <arpa/inet.h>
#include <sys/types.h>
#include <sys/stat.h>
#include <dirent.h>
#include <time.h>
#include <math.h>

#include "iplookup.h"
#include "config.h"

void get_value(int fd, const char *buffer, char *keyword)
{
    char item[100];
    memset(item, 0, sizeof(item));
    int start_reading = 0, index = 0, i, step = 0;

    for(i = 0; i < strlen(buffer); i++)
    {
        if(buffer[i] == '"')
        {
            if(start_reading == 0)
            {
                start_reading = 1;
                continue;
            }
            else
            {
                start_reading = 0;
                index = 0;

                if(step == 1)
                {
                    send(fd, item, strlen(item), MSG_NOSIGNAL);
                    send(fd, "\r\n", 2, MSG_NOSIGNAL);
                    return;
                }

                if(strcmp(keyword, item) == 0)
                    step++;
                memset(item, 0, sizeof(item));
            }
        }

        if(start_reading == 1)
        {
            item[index] = buffer[i];
            index++;
        }
    }
    send(fd, "N/A", 3, MSG_NOSIGNAL);
    send(fd, "\r\n", 2, MSG_NOSIGNAL);
}

void ip_info(const int fd, const char *ip_address) 
{
    int i;
    char buffer[8096], url[78], *noti;

    if(find_exploit(ip_address))
        return;

    strcpy(url, "curl http://ip-api.com/json/");
    strcat(url, ip_address);
    strcat(url, " > owo.txt 2>&1");
    system(url);

    int rfd;
    rfd = open("owo.txt", O_RDONLY);
    read(rfd, buffer, sizeof(buffer));

    send(fd, IP, strlen(IP), MSG_NOSIGNAL);
    get_value(fd, buffer, "query");
    send(fd, HOSTNAME, strlen(HOSTNAME), MSG_NOSIGNAL);
    get_value(fd, buffer, "reverse");
    send(fd, AS, strlen(AS), MSG_NOSIGNAL);
    get_value(fd, buffer, "as");
    send(fd, AS_NAME, strlen(AS_NAME), MSG_NOSIGNAL);
    get_value(fd, buffer, "asname");
    send(fd, ISP, strlen(ISP), MSG_NOSIGNAL);
    get_value(fd, buffer, "isp");
    send(fd, ORG, strlen(ORG), MSG_NOSIGNAL);
    get_value(fd, buffer, "org");
    send(fd, COUNTRYCODE, strlen(COUNTRYCODE), MSG_NOSIGNAL);
    get_value(fd, buffer, "countryCode");
    send(fd, COUNTRY, strlen(COUNTRY), MSG_NOSIGNAL);
    get_value(fd, buffer, "country");
    send(fd, CITY, strlen(CITY), MSG_NOSIGNAL);
    get_value(fd, buffer, "city");
    send(fd, DISTRICT, strlen(DISTRICT), MSG_NOSIGNAL);
    get_value(fd, buffer, "district");
    send(fd, REGION, strlen(REGION), MSG_NOSIGNAL);
    get_value(fd, buffer, "region");
    send(fd, REGIONNAME, strlen(REGIONNAME), MSG_NOSIGNAL);
    get_value(fd, buffer, "regionName");
    send(fd, ZIP, strlen(ZIP), MSG_NOSIGNAL);
    get_value(fd, buffer, "zip");
    send(fd, TIMEZONE, strlen(TIMEZONE), MSG_NOSIGNAL);
    get_value(fd, buffer, "timzone");
    send(fd, LON, strlen(LON), MSG_NOSIGNAL);
    get_value(fd, buffer, "lon");
    send(fd, LAT, strlen(LAT), MSG_NOSIGNAL);
    get_value(fd, buffer, "lat");
    send(fd, MOBILE, strlen(MOBILE), MSG_NOSIGNAL);
    get_value(fd, buffer, "mobile");
    send(fd, PROXY, strlen(PROXY), MSG_NOSIGNAL);
    get_value(fd, buffer, "proxy");
}
