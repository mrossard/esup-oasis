# Build
FROM node:22.11 AS build

## Copy App Code
COPY . /app

WORKDIR /app

## Install dependencies
RUN yarn install

## Build App
RUN yarn build


FROM nginx:latest

# Copy the build files to the container
COPY --from=build /app/build/ /usr/share/nginx/html

WORKDIR /usr/share/nginx/html

# Copy the ngnix.conf to the container
COPY docker/nginx.conf /etc/nginx/conf.d/default.conf

# Install npx (pour pouvoir utiliser react-inject-env dans l'entrypoint)
RUN curl -sL https://deb.nodesource.com/setup_lts.x | bash - && apt-get install -y nodejs

# Clean up
RUN apt-get clean && rm -rf /var/lib/apt/lists/*

# Copy the docker-entrypoint.sh to the container
COPY --link --chmod=755 docker/docker-entrypoint.sh /usr/local/bin/docker-entrypoint
RUN chmod 755 /usr/local/bin/docker-entrypoint

EXPOSE 80

ENTRYPOINT ["/usr/local/bin/docker-entrypoint"]