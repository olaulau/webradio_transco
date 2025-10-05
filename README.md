# webradio transco
on-demand webradio transcoding platform


## features
- HTTP multimedia relay : serv multiple client with only one dl of the stream
- on-demand : no bw / cpu used if there is no viewer
- on-the-fly transcode : you can change container / codec / bitrate of the stream


## status
working great, specially with ogg/vorbis output.


## requirements
- linux OS
- PHP webserver
  - sqlite
- cvlc (CLI vlc, usually namer vlc-nox)
- 

## installation
install debian / ubuntu package vlc-nox  
if you're on ubuntu 12.04, see [instruction to compile VLC](vlc_compile.md)   
```git clone https://github.com/olaulau/webradio_transco```  
modify and rename ```includes/config.inc.dist.php```  
(default admin password is ```admin```, you can generate a new one with this page : ```/admin/auth/passwd.php```  
the first time you go to ```index.php```, it will create the database and a test Stream.  
