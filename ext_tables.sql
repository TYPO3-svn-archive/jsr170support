#
# Table structure for table 'tx_jsr170support_repo'
#
CREATE TABLE tx_jsr170support_repo (
	uid int(11) unsigned DEFAULT '0' NOT NULL auto_increment,
	pid int(11) unsigned DEFAULT '0' NOT NULL,
	tstamp int(11) unsigned DEFAULT '0' NOT NULL,
	crdate int(11) unsigned DEFAULT '0' NOT NULL,
	cruser_id int(11) unsigned DEFAULT '0' NOT NULL,
	deleted tinyint(4) unsigned DEFAULT '0' NOT NULL,
	hidden tinyint(4) unsigned DEFAULT '0' NOT NULL,
	title tinytext NOT NULL,
	description text NOT NULL,
	connectconfig text NOT NULL,
	
	PRIMARY KEY (uid),
	KEY parent (pid)
);