from files.logging.log import log, error

def login(user, password):
    for i in open("assets/storage/db.txt", "r").read().splitlines():
        if user and password in i:
            return i.split(" ")
    return False