#pragma once

#include <stdbool.h>

#define IP "\e[38;5;79m║ \e[38;5;230mIP\e[38;5;204m............\e[38;5;230m: \e[38;5;79m"
#define HOSTNAME "\e[38;5;79m║ \e[38;5;230mHostname\e[38;5;204m......\e[38;5;230m: \e[38;5;79m"
#define AS "\e[38;5;79m║ \e[38;5;230mAS\e[38;5;204m............\e[38;5;230m: \e[38;5;79m"
#define AS_NAME "\e[38;5;79m║ \e[38;5;230mAS Name\e[38;5;204m.......\e[38;5;230m: \e[38;5;79m"
#define ISP "\e[38;5;79m║ \e[38;5;230mISP\e[38;5;204m...........\e[38;5;230m: \e[38;5;79m"
#define ORG "\e[38;5;79m║ \e[38;5;230mOrganisation\e[38;5;204m..\e[38;5;230m: \e[38;5;79m"
#define COUNTRYCODE "\e[38;5;79m║ \e[38;5;230mCountry Code\e[38;5;204m..\e[38;5;230m: \e[38;5;79m"
#define COUNTRY "\e[38;5;79m║ \e[38;5;230mCountry\e[38;5;204m.......\e[38;5;230m: \e[38;5;79m"
#define CITY "\e[38;5;79m║ \e[38;5;230mCity\e[38;5;204m..........\e[38;5;230m: \e[38;5;79m"
#define DISTRICT "\e[38;5;79m║ \e[38;5;230mDistrict\e[38;5;204m......\e[38;5;230m: \e[38;5;79m"
#define REGION "\e[38;5;79m║ \e[38;5;230mRegion\e[38;5;204m........\e[38;5;230m: \e[38;5;79m"
#define REGIONNAME "\e[38;5;79m║ \e[38;5;230mRegion Name\e[38;5;204m...\e[38;5;230m: \e[38;5;79m"
#define CURRENCY "\e[38;5;79m║ \e[38;5;230mCurrency\e[38;5;204m......\e[38;5;230m: \e[38;5;79m"
#define ZIP "\e[38;5;79m║ \e[38;5;230mZip\e[38;5;204m...........\e[38;5;230m: \e[38;5;79m"
#define TIMEZONE "\e[38;5;79m║ \e[38;5;230mTime Zone\e[38;5;204m.....\e[38;5;230m: \e[38;5;79m"
#define LON "\e[38;5;79m║ \e[38;5;230mLongitude\e[38;5;204m.....\e[38;5;230m: \e[38;5;79m"
#define LAT "\e[38;5;79m║ \e[38;5;230mLatitude\e[38;5;204m......\e[38;5;230m: \e[38;5;79m"
#define MOBILE "\e[38;5;79m║ \e[38;5;230mMobile\e[38;5;204m........\e[38;5;230m: \e[38;5;79m"
#define PROXY "\e[38;5;79m║ \e[38;5;230mProxy\e[38;5;204m.........\e[38;5;230m: \e[38;5;79m"

void ip_info(const int fd, const char *ip_address);
void get_value(int fd, const char *buffer, char *keyword);
