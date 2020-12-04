UPDATE data
SET md5 = :file_hash
	, track_artist = :track_artist
	, track_title = :track_title
	, track_album = :track_album
	, track_genre = :track_genre
	, track_composer = :track_composer
	, track_date = :track_date
	, track_key = :track_key
	, track_bpm = :track_bpm
	, track_comment = :track_comment
	, codec_name = :codec_name
	, codec_long_name = :codec_long_name
	, codec_type = :codec_type
WHERE filename = :filename;