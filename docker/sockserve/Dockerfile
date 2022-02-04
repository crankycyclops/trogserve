# This docker file defines a container for the web socket server portion of the
# trogserve project.

# To build, be sure to specify a node version: docker build --build-arg NODE_VER=16 -t crankycyclops/trogserve-websocket:master .

ARG NODE_VER=16
FROM node:${NODE_VER}

# Pre-configure environment and install build dependencies
ENV DEBIAN_FRONTEND="noninteractive" TZ="UTC"
RUN apt-get update
RUN apt-get dist-upgrade -y

# TODO: when I have releases, wget a specific release tarball instead
RUN apt-get install -y wget unzip
WORKDIR /
RUN wget https://github.com/crankycyclops/trogserve/archive/refs/heads/master.zip
RUN unzip master.zip
RUN mv /trogserve-master/sockserve /
RUN rm -rf /trogserve-master
RUN apt-get remove -y wget unzip

# Install node dependencies
WORKDIR /sockserve
RUN npm install

# Create a non-root user to execute sockserve
RUN groupadd -r trogserve && useradd -r -g trogserve -d /home/trogserve -s /sbin/nologin -c "Docker image user" trogserve

USER trogserve
WORKDIR /sockserve

ENTRYPOINT ["node", "index"]
