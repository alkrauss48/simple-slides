# Frontend build
FROM node:18-alpine as node-builder
WORKDIR /app

COPY package.json package-lock.json ./
RUN npm ci

COPY . .
ARG NODE_ENV=production
RUN npm run build

# Final Image
FROM nginx:stable-alpine
WORKDIR /var/www/html

COPY ./docker/nginx/nginx.conf /etc/nginx
COPY . .
COPY --from=node-builder --chown=82:82 /app/public public
