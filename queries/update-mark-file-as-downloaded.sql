UPDATE data
SET downloaded = true, downloaded_datetime = :datetime
WHERE filename = :filename;