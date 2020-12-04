/*
	SQLite Datatypes
	https://www.sqlite.org/datatype3.html

	No "id" column will be specified. Instead, we'll rely upon a combination of the track UUID and SQLite's
	automatic "rowid"
	http://www.sqlitetutorial.net/sqlite-autoincrement/
*/

CREATE TABLE IF NOT EXISTS data
(
	s3_bucket varchar(63) -- https://docs.aws.amazon.com/AmazonS3/latest/dev/BucketRestrictions.html
	, s3_timestamp datetime
	, s3_size bigint
	, filename varchar(255)
	, downloaded boolean NULL
	, downloaded_datetime datetime NULL
	, md5 varchar(32) NULL
	, track_artist varchar(1000) NULL -- https://stackoverflow.com/questions/6109532/what-is-the-maximum-size-limit-of-varchar-data-type-in-sqlite
	, track_title varchar(1000) NULL
	, track_album varchar(1000) NULL
	, track_genre varchar(255) NULL
	, track_composer varchar(255) NULL
	, track_date int NULL
	, track_key varchar(8) NULL
	, track_bpm int NULL
	, track_comment varchar(8000) NULL
	, codec_name varchar(1000) NULL
	, codec_long_name varchar(1000) NULL
	, codec_type varchar(1000) NULL
);

/*
	Create Indexes on the table for performance reasons.
	https://medium.com/@JasonWyatt/squeezing-performance-from-sqlite-indexes-indexes-c4e175f3c346
*/
CREATE INDEX filenames ON data(filename);
CREATE INDEX hashes ON data(md5);