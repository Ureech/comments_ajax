

CREATE TABLE `cms_comments_ctype_rating` (
  `id` varchar(11) NOT NULL,
  `ctype` varchar(32) NOT NULL,
  `total_votes` int(11) NOT NULL DEFAULT '0',
  `total_value` int(11) NOT NULL DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


CREATE TABLE `cms_comments_likes_log` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` int(11) UNSIGNED DEFAULT NULL COMMENT 'Кто лайкнул',
  `comment_id` int(11) DEFAULT NULL,
  `author_name` varchar(100) DEFAULT NULL COMMENT 'Имя кто лайкнул(гостя)',
  `ip` varbinary(16) DEFAULT NULL COMMENT 'ip-адрес проголосовавшего'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Лайки';


CREATE TABLE `cms_comments_rating_log` (
  `id` int(11) DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL,
  `target_subject` varchar(32) DEFAULT NULL,
   `ip` varbinary(16) DEFAULT NULL COMMENT 'ip-адрес проголосовавшего'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


ALTER TABLE `cms_comments_ctype_rating`
  ADD PRIMARY KEY (`id`);


ALTER TABLE `cms_comments_likes_log`
  ADD PRIMARY KEY (`id`);


ALTER TABLE `cms_comments_likes_log`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

ALTER TABLE `cms_content_types` ADD `count_comm` INT(5) NOT NULL DEFAULT '10';
ALTER TABLE `cms_content_types` ADD `is_rating_newcomm` TINYINT(1) NOT NULL DEFAULT '0';
ALTER TABLE `cms_content_types` ADD `is_score_newcomm` TINYINT(1) NOT NULL DEFAULT '0';
ALTER TABLE `cms_content_types` ADD `is_likes_newcomm` TINYINT(1) NOT NULL DEFAULT '0';

COMMIT;

