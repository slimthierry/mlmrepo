-- ALTER DATABASE WITH NEW MODIFICATIONS

ALTER TABLE user_infos DROP COLUMN organisation_id;
ALTER TABLE user_infos ADD COLUMN organisation_name VARCHAR(145) NULL DEFAULT NULL;
ALTER TABLE user_infos ADD INDEX user_infos_organisation(organisation_name);
ALTER TABLE user_infos DROP FOREIGN KEY user_infos_parent_id_foreign;
ALTER TABLE user_infos DROP COLUMN parent_id;
ALTER TABLE organisations MODIFY COLUMN name VARCHAR(145) NOT NULL;

ALTER TABLE agencies DROP COLUMN address;
