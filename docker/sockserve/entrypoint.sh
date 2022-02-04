#!/bin/bash

# Make sure trogdord starts before we attempt to connect to it.
# A little hacky. Oh well.
sleep 3

# Right now, config.env is only read from the current directory. I should
# fix fhis, but for now, changing the working directory works.
cd /sockserve
node index