CREATE TABLE `posts`
(
    `id`            INT                NOT NULL AUTO_INCREMENT,
    `title`         TEXT               NOT NULL,
    `content`       TEXT               NOT NULL,
    `authorId`      INT                NOT NULL,
    `createdDate`   DATETIME           NOT NULL,
    `publishedDate` DATETIME           NULL,
    `deleted`       ENUM ('Yes', 'No') NOT NULL DEFAULT 'No',
    PRIMARY KEY (`id`)
);

CREATE TABLE `users`
(
    `id`          INT          NOT NULL AUTO_INCREMENT,
    `displayName` VARCHAR(45)  NOT NULL,
    `userName`    VARCHAR(45)  NOT NULL,
    `email`       VARCHAR(320) NOT NULL,
    `password`    VARCHAR(200) NOT NULL,
    PRIMARY KEY (`id`)
);

CREATE TABLE `comments`
(
    `id`            INT      NOT NULL AUTO_INCREMENT,
    `comment`       TEXT     NOT NULL,
    `authorId`      INT      NOT NULL,
    `publishedDate` DATETIME NOT NULL,
    PRIMARY KEY (`id`)
);

create index comments_authorId_index
    on comments (authorId);

alter table comments
    add constraint comments_users_id_fk
        foreign key (authorId) references users (id);

create index posts_authorId_index
    on posts (authorId);

alter table posts
    add constraint posts_users_id_fk
        foreign key (authorId) references users (id);

