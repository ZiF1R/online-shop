FROM node

WORKDIR /app

COPY . .

RUN yarn

EXPOSE 5173

CMD ["yarn", "dev"]