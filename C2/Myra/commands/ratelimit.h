#pragma once

#define BUCKET_WARN 10
#define BUCKET_RESET 7

struct ratelimit_t
{
	int warnings;
	int count;
}ratelimit[MAX_CONNS];

int ratelimit_init(const int fd);
