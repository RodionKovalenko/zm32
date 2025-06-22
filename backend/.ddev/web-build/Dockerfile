ARG BASE_IMAGE
FROM $BASE_IMAGE

# Set timezone
ENV TZ=Europe/Berlin

RUN apt-get update && apt-get install -y tzdata \
    && ln -snf /usr/share/zoneinfo/Europe/Berlin /etc/localtime \
    && echo "Europe/Berlin" > /etc/timezone \
    && dpkg-reconfigure -f noninteractive tzdata \
    && apt-get clean