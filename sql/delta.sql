
--- 2012-05-25
--- Required for PDO implementation of io\creat\chassis\session\settings class.
ALTER TABLE `core_settings` ADD UNIQUE `core_settings_index` ( `scope`, `id`, `ns`, `key` )