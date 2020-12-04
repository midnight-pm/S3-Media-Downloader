# S3 Media Downloader

This was designed to help me with retrieving media (MP3, MP4, M4A, and M4V) files from an S3 Bucket used by DJs. It leverages a combination of the AWS-CLI client and FFmpeg to obtain the bucket contents, retrieve them, and process them from there.

The bucket in question was and remains prone to having duplicates of files, albeit with different tags and filenames. As such, ffmpeg is used to hash the bitstream of each audio and video file in order to reduce the occurrence of duplicates. As each file is retrieved and processed, its hash is stored in an SQL DB (along with relevant tag information) and retained there for future comparison. Accordingly, if any files are retrieved in the future and match against the same hash, they will be discarded.
Likewise, a record of each file that has already been retrieved will be stored in the database. To that end, if a file is already present when the next bucket listing is retrieved, it will be ignored.

----

Requirements:

 * [PHP](https://www.php.net/) 7.3 (or newer)
 * [AWS CLI](https://aws.amazon.com/cli/)
 * [ffmpeg](https://ffmpeg.org/) (and ffprobe)

This supports depedency checking, and will check for their installation. If they are not present, then the script will exit and inform accordingly. This was developed in a Linux environment (specifically, Fedora Linux); and while it has not been tested under Windows nor macOS, it was designed with cross-platform compatibility in mind. As such, while it does rely on external libraries, if they are not discoverable via `$PATH` (or, in the context of Windows, `where`), then their locations will need to be specified in [res/config.inc.php](res/config.inc.php).

Additionally, this makes use of [RollingCurlX](http://github.com/marcushat/RollingCurlX) in order to facilitate usage of cURL Multi. Relevant configuration options can also be found in [res/config.inc.php](res/config.inc.php). Further configuration options - including the specification of the bucket name, are present in the same file. Review all available configuration options for more information.

----

Notes:

 * In direct correlation to the size of the files that will be retrieved, and the more threads that are used, PHP may exhaust the set memory limit. Configuring the parameter `memory_limit` in `php.ini` accordingly may be necessary. It should be expected that the default parameter of 128M will be exhausted almost immediately if downloading video.
 * The more (virtual or physical) memory available when downloading, the better.
 * The [hashing process](https://ffmpeg.org/doxygen/3.3/group__lavu__hash.html) - particularly when scanning video - is very processor intensive. However, unlike downloads (which are asynchronous by way of [curl_multi_*()](https://www.php.net/manual/en/function.curl-multi-init.php)), processing of downloads (and hashing) is limited to one file at a time.
 * Files are stored and then processed afterwards. During the processing phase, files will be checked for their hash and any existing metadata. This will be used to rename files, and then further sort files into different directories based on their genre. If no genre is found, then the file will be left at the root of the specified storage directory.

In the event of an error with either the download, processing of a given file, or any other such error, PHP will issue a warning level event. All other general status updates will be issued by way of PHP issuing a notice level event.

In order to maximize portability, SQLite3 was utilized as the RDMBS of choice. As such, the database itself can be placed anywhere on the filesystem that the user can write to. As such, the database can also be queried using the standard SQLite3 client (or any other compatible client), and can be used in any another application given a bit of development effort.

----

Standard Faire

This is provided with no warranty nor a gaurantee of any kind.